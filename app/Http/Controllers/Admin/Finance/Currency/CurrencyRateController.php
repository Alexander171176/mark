<?php

namespace App\Http\Controllers\Admin\Finance\Currency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\Currency\BulkCurrencyRatesRequest;
use App\Http\Requests\Admin\Finance\Currency\CurrencyRateRequest;
use App\Http\Resources\Admin\Finance\Currency\CurrencyRateResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Finance\Currency\CurrencyRate;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CurrencyRateController extends Controller
{
    private function createRateHistory(
        int $baseId,
        int $quoteId,
        string|float $rate,
        ?string $provider,
        bool $isManual,
        Carbon $fetchedAt
    ): CurrencyRate {
        $attempts = 0;
        $max = 5;

        while (true) {
            try {
                return CurrencyRate::create([
                    'base_currency_id'  => $baseId,
                    'quote_currency_id' => $quoteId,
                    'rate'              => $rate,
                    'provider'          => $provider,
                    'is_manual'         => $isManual,
                    'fetched_at'        => $fetchedAt->toDateTimeString(),
                ]);
            } catch (QueryException $e) {
                $isDuplicate = (int)($e->errorInfo[1] ?? 0) === 1062;

                if (!$isDuplicate || $attempts >= $max) {
                    throw $e;
                }

                $attempts++;
                $fetchedAt = $fetchedAt->copy()->addSeconds($attempts);
            }
        }
    }

    public function index(Currency $currency): Response
    {
        // последние курсы по каждой quote для выбранной base
        $latest = CurrencyRate::query()
            ->where('base_currency_id', $currency->id)
            ->with(['quote:id,code,name'])
            ->latestFirst()
            ->get()
            ->unique('quote_currency_id')
            ->values();

        $currencies = Currency::query()
            ->orderBy('code')
            ->get(['id','code','name']);

        return Inertia::render('Admin/Finance/CurrencyRates/Index', [
            'currency'   => $currency->only(['id','code','name']),
            'rates'      => CurrencyRateResource::collection($latest),
            'currencies' => $currencies,
        ]);
    }

    public function store(CurrencyRateRequest $request, Currency $currency): RedirectResponse
    {
        $data = $request->validated();

        try {
            // base строго из route
            $baseId = (int) $currency->id;

            $rate = $this->createRateHistory(
                $baseId,
                (int)$data['quote_currency_id'],
                (string)$data['rate'],
                $data['provider'] ?? 'manual',
                (bool)$data['is_manual'],
                Carbon::parse($data['fetched_at'])
            );

            return back()->with('success', 'Курс добавлен в историю.');
        } catch (\Throwable $e) {
            Log::error('Store rate failed: '.$e->getMessage(), ['data' => $data]);
            return back()->with('error', 'Ошибка сохранения курса.');
        }
    }

    /**
     * В режиме "история" update = создать новую запись, а не перетирать старую.
     */
    public function update(CurrencyRateRequest $request, Currency $currency, CurrencyRate $rate): RedirectResponse
    {
        if ((int)$rate->base_currency_id !== (int)$currency->id) {
            return back()->with('error', 'Курс не принадлежит указанной валюте.');
        }

        $data = $request->validated();

        try {
            $this->createRateHistory(
                (int)$currency->id,
                (int)$data['quote_currency_id'],
                (string)$data['rate'],
                $data['provider'] ?? 'manual',
                (bool)$data['is_manual'],
                Carbon::parse($data['fetched_at'])
            );

            return back()->with('success', 'Добавлена новая запись курса (история).');
        } catch (\Throwable $e) {
            Log::error('Update (history-create) rate failed: '.$e->getMessage(), ['data' => $data, 'rate_id' => $rate->id]);
            return back()->with('error', 'Ошибка обновления курса.');
        }
    }

    public function destroy(Currency $currency, CurrencyRate $rate): RedirectResponse
    {
        if ((int)$rate->base_currency_id !== (int)$currency->id) {
            return back()->with('error', 'Курс не принадлежит указанной валюте.');
        }

        try {
            $rate->delete();
            return back()->with('success', 'Историческая запись курса удалена.');
        } catch (\Throwable $e) {
            Log::error('Delete rate failed: '.$e->getMessage());
            return back()->with('error', 'Ошибка удаления курса.');
        }
    }

    /**
     * Пакетная вставка в историю.
     * ВАЖНО: НЕ upsert по паре — только create (история).
     */
    public function bulkUpsert(BulkCurrencyRatesRequest $request, Currency $currency): RedirectResponse
    {
        $payload = $request->validated();

        try {
            DB::transaction(function () use ($payload, $currency) {
                foreach ($payload['rates'] as $row) {
                    // base строго из route
                    $baseId = (int) $currency->id;

                    $this->createRateHistory(
                        $baseId,
                        (int)$row['quote_currency_id'],
                        (string)$row['rate'],
                        $row['provider'] ?? 'manual',
                        (bool)$row['is_manual'],
                        Carbon::parse($row['fetched_at'])
                    );
                }
            });

            return back()->with('success', 'Курсы добавлены в историю пакетно.');
        } catch (\Throwable $e) {
            Log::error('Bulk insert rates failed: '.$e->getMessage(), ['payload' => $payload]);
            return back()->with('error', 'Ошибка пакетного обновления курсов.');
        }
    }

    public function refresh(Currency $currency): RedirectResponse
    {
        try {
            return back()->with('success', 'Курсы обновлены с провайдера.');
        } catch (Throwable $e) {
            Log::error('Refresh rates failed: '.$e->getMessage());
            return back()->with('error', 'Не удалось обновить курсы.');
        }
    }
}
