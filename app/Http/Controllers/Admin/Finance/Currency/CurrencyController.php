<?php

namespace App\Http\Controllers\Admin\Finance\Currency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\Currency\CurrencyActivityRequest;
use App\Http\Requests\Admin\Finance\Currency\CurrencyInlineRateRequest;
use App\Http\Requests\Admin\Finance\Currency\CurrencyRequest;
use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Finance\Currency\CurrencyRate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CurrencyController extends Controller
{

    private function isDuplicateKey(QueryException $e): bool
    {
        // SQLSTATE:
        // - MySQL duplicate: 23000 (integrity constraint violation)
        // - PostgreSQL unique violation: 23505
        $sqlState = (string)($e->errorInfo[0] ?? '');
        if ($sqlState === '23505') return true; // PostgreSQL unique_violation
        if ($sqlState === '23000') {
            // MySQL duplicate key often has driver error code 1062
            $driverCode = (int)($e->errorInfo[1] ?? 0);
            if ($driverCode === 1062) return true;
        }

        // fallback by message (последний шанс, чтобы не зависеть от драйвера)
        $msg = strtolower($e->getMessage());
        return str_contains($msg, 'duplicate') || str_contains($msg, 'unique');
    }

    /**
     * Историческая запись курса.
     * Если попали на UNIQUE(base,quote,fetched_at) — двигаем fetched_at на +N секунд и повторяем.
     */
    private function createRateHistory(
        int $baseId,
        int $quoteId,
        string|float $rate,
        ?string $provider,
        bool $isManual,
        Carbon $fetchedAt
    ): void {
        $attempts = 0;
        $max = 7;

        while (true) {
            try {
                CurrencyRate::create([
                    'base_currency_id'  => $baseId,
                    'quote_currency_id' => $quoteId,
                    'rate'              => (string) $rate,
                    'provider'          => $provider,
                    'is_manual'         => $isManual,
                    'fetched_at'        => $fetchedAt, // ✅ Carbon
                ]);
                return;
            } catch (QueryException $e) {
                if (!$this->isDuplicateKey($e) || $attempts >= $max) {
                    throw $e;
                }

                $attempts++;
                $fetchedAt = $fetchedAt->copy()->addSeconds($attempts);
            }
        }
    }

    private function decodeSepToken(?string $token): string
    {
        return match ($token) {
            'space'      => ' ',
            'nbsp'       => "\u{00A0}",
            'thinspace'  => "\u{2009}",
            'comma'      => ',',
            'dot'        => '.',
            'apostrophe' => "'",
            default      => (string) $token,
        };
    }

    public function index(): Response
    {
        $adminCountCurrencies = (int) config('site_settings.AdminCountCurrencies', 15);
        $adminSortCurrencies  = (string) config('site_settings.AdminSortCurrencies', 'idAsc');

        try {
            $currencies = Currency::query()->ordered()->get();
            $currenciesCount = $currencies->count();

            /** @var Currency|null $base */
            $base = Currency::query()->default()->first();

            // latest rates map for base: quote_id => CurrencyRate
            $ratesMap = [];
            if ($base) {
                $latest = CurrencyRate::query()
                    ->where('base_currency_id', $base->id)
                    ->latestFirst()
                    ->get()
                    ->unique('quote_currency_id');

                foreach ($latest as $rate) {
                    $ratesMap[$rate->quote_currency_id] = $rate;
                }
            }

            $currenciesArray = CurrencyResource::collection($currencies)->resolve();

            $currenciesArray = array_map(function (array $row) use ($ratesMap, $base) {
                $row['rate_vs_default'] = null;
                $row['rate_provider']   = null;
                $row['rate_at']         = null;

                if ($base && (int)$row['id'] === (int)$base->id) {
                    $row['rate_vs_default'] = 1.0;
                    $row['rate_provider']   = 'system';
                    $row['rate_at']         = null;
                } elseif ($base && isset($ratesMap[$row['id']])) {
                    $rate = $ratesMap[$row['id']];
                    $row['rate_vs_default'] = (float) $rate->rate;
                    $row['rate_provider']   = $rate->provider;
                    $row['rate_at'] = $rate->fetched_at?->toISOString();
                }

                return $row;
            }, $currenciesArray);

        } catch (\Throwable $e) {
            Log::error("Ошибка загрузки валют: " . $e->getMessage());
            $currenciesArray = [];
            $currenciesCount = 0;
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/Finance/Currencies/Index', [
            'currencies'           => $currenciesArray,
            'currenciesCount'      => $currenciesCount,
            'adminCountCurrencies' => $adminCountCurrencies,
            'adminSortCurrencies'  => $adminSortCurrencies,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Finance/Currencies/Create');
    }

    public function store(CurrencyRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $data['thousands_sep'] = $this->decodeSepToken($data['thousands_sep'] ?? 'space');
            $data['decimal_sep']   = $this->decodeSepToken($data['decimal_sep'] ?? 'dot');

            $data['sort'] = $data['sort'] ?? (int) (Currency::max('sort') + 1);

            Currency::create($data);

            session()->flash('success', __('admin/controllers.created_successfully'));
            return redirect()->route('admin.currencies.index');
        } catch (\Throwable $e) {
            Log::error("Ошибка создания валюты [store]: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['general' => __('admin/controllers.store_error')])->withInput();
        }
    }

    public function edit(Currency $currency): Response
    {
        return Inertia::render('Admin/Finance/Currencies/Edit', [
            'currency' => new CurrencyResource($currency),
        ]);
    }

    public function update(CurrencyRequest $request, Currency $currency): RedirectResponse
    {
        try {
            $data = $request->validated();

            $data['thousands_sep'] = $this->decodeSepToken($data['thousands_sep'] ?? 'space');
            $data['decimal_sep']   = $this->decodeSepToken($data['decimal_sep'] ?? 'dot');

            $currency->update($data);

            session()->flash('success', __('admin/controllers.updated_successfully'));
            return redirect()->route('admin.currencies.index');
        } catch (\Throwable $e) {
            Log::error("Ошибка обновления валюты [update]: {$e->getMessage()}");
            return back()->withErrors(['general' => __('admin/controllers.update_error')])->withInput();
        }
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        try {
            $currency->delete();
            session()->flash('success', __('admin/controllers.deleted_successfully'));
            return back();
        } catch (Throwable $e) {
            Log::error("Ошибка удаления валюты [destroy]: {$e->getMessage()}");
            return back()->withErrors(['general' => __('admin/controllers.delete_error')]);
        }
    }

    public function updateActivity(CurrencyActivityRequest $request, Currency $currency): RedirectResponse
    {
        try {
            $currency->activity = (bool) $request->boolean('activity');
            $currency->save();

            return back()->with('success', __('admin/controllers.updated_successfully'));
        } catch (Throwable $e) {
            Log::error("Ошибка обновления активности [updateActivity]: {$e->getMessage()}");
            return back()->withErrors(['activity' => __('admin/controllers.update_error')]);
        }
    }

    public function bulkUpdateActivity(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids'      => ['required','array','min:1'],
            'ids.*'    => ['integer','exists:currencies,id'],
            'activity' => ['required','boolean'],
        ]);

        try {
            Currency::whereIn('id', $data['ids'])->update(['activity' => $data['activity']]);
            return back()->with('success', __('admin/controllers.updated_successfully'));
        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления активности [bulkUpdateActivity]: {$e->getMessage()}");
            return back()->withErrors(['general' => __('admin/controllers.update_error')]);
        }
    }

    public function updateSortBulk(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'currencies'           => ['required','array','min:1'],
            'currencies.*.id'      => ['required','integer','exists:currencies,id'],
            'currencies.*.sort'    => ['required','integer'],
        ]);

        try {
            DB::transaction(function () use ($payload) {
                foreach ($payload['currencies'] as $row) {
                    Currency::whereKey($row['id'])->update(['sort' => (int) $row['sort']]);
                }
            });

            return back()->with('success', __('admin/controllers.updated_successfully'));
        } catch (Throwable $e) {
            Log::error("Ошибка bulk-сортировки валют [updateSortBulk]: {$e->getMessage()}");
            return back()->withErrors(['currencies' => __('admin/controllers.update_error')]);
        }
    }

    public function updateSort(Request $request, Currency $currency): RedirectResponse
    {
        $data = $request->validate([
            'sort' => ['required','integer'],
        ]);

        try {
            $currency->update(['sort' => (int) $data['sort']]);
            return back()->with('success', __('admin/controllers.updated_successfully'));
        } catch (Throwable $e) {
            Log::error("Ошибка обновления сортировки [updateSort]: {$e->getMessage()}");
            return back()->withErrors(['sort' => __('admin/controllers.update_error')]);
        }
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids'   => ['required','array','min:1'],
            'ids.*' => ['integer','exists:currencies,id'],
        ]);

        try {
            Currency::whereIn('id', $data['ids'])->delete();
            return back()->with('success', __('admin/controllers.deleted_successfully'));
        } catch (Throwable $e) {
            Log::error("Ошибка массового удаления валют [bulkDestroy]: {$e->getMessage()}");
            return back()->withErrors(['general' => __('admin/controllers.delete_error')]);
        }
    }

    /**
     * Назначить валюту основной (история не ломается).
     * Пересчёт кросс-курсов — создаёт новые записи.
     */
    public function setDefault(Currency $currency): RedirectResponse
    {
        try {
            DB::transaction(function () use ($currency) {
                /** @var Currency|null $oldBase */
                $oldBase = Currency::query()->default()->first();

                Currency::where('is_default', true)->update(['is_default' => false]);
                $currency->update([
                    'is_default'     => true,
                    'set_default_at' => Carbon::now(),
                    'activity'       => true,
                ]);

                $now = Carbon::now();

                // Если была старая база и она отличается — пересчитываем кросс-курсы
                if ($oldBase && $oldBase->id !== $currency->id) {
                    $this->rebuildCrossRatesHistory($oldBase->id, $currency->id, $now);
                }

                // base->base = 1.0 (историческая запись)
                $this->createRateHistory(
                    $currency->id,
                    $currency->id,
                    '1.0',
                    'system',
                    false,
                    $now
                );
            });

            return back()->with('success', __('admin/controllers.updated_successfully'));
        } catch (\Throwable $e) {
            Log::error("Ошибка назначения основной валюты [setDefault]: {$e->getMessage()}");
            return back()->withErrors(['general' => __('admin/controllers.update_error')]);
        }
    }

    /**
     * Пересобрать кросс-курсы (история): создаём новые записи под новой базой.
     */
    private function rebuildCrossRatesHistory(int $oldBaseId, int $newBaseId, Carbon $now): void
    {
        /** @var Collection|CurrencyRate[] $latest */
        $latest = CurrencyRate::query()
            ->where('base_currency_id', $oldBaseId)
            ->latestFirst()
            ->get()
            ->unique('quote_currency_id');

        $a2b = $latest->firstWhere('quote_currency_id', $newBaseId);
        if (!$a2b || (float)$a2b->rate <= 0) {
            Log::warning("Кросс-курсы не пересчитаны: нет валидного курса A({$oldBaseId})->B({$newBaseId}).");
            return;
        }

        foreach ($latest as $row) {
            $quoteId = (int) $row->quote_currency_id;

            if ($quoteId === $newBaseId) continue;

            // rate(B->X) = rate(A->X) / rate(A->B)
            $newRate = (float)$row->rate / (float)$a2b->rate;

            if (!is_finite($newRate) || $newRate <= 0) continue;

            $this->createRateHistory(
                $newBaseId,
                $quoteId,
                (string)$newRate,
                'cross',
                false,
                $now
            );
        }
    }

    /**
     * История: ручной “снимок” курса base→currency
     */
    public function updateRate(CurrencyInlineRateRequest $request, Currency $currency): RedirectResponse
    {
        try {
            $base = Currency::query()->default()->first();
            if (!$base) return back()->withErrors(['general' => 'Не задана базовая валюта.']);
            if ($base->id === $currency->id) return back()->withErrors(['general' => 'Для базовой валюты курс всегда 1.']);
            if (!$currency->activity) return back()->withErrors(['general' => 'Нельзя обновлять курс неактивной валюты.']);

            DB::transaction(function () use ($request, $base, $currency) {
                $now = Carbon::now();

                $this->createRateHistory(
                    $base->id,
                    $currency->id,
                    (string)$request->input('rate'),
                    (string)($request->input('provider') ?: 'manual'),
                    true,
                    $now
                );
            });

            return back()->with('success', 'Курс добавлен в историю.');
        } catch (\Throwable $e) {
            Log::error('updateRate error: '.$e->getMessage());
            return back()->withErrors(['general' => 'Ошибка обновления курса.']);
        }
    }

    /**
     * CBR provider: https://www.cbr-xml-daily.ru/daily_json.js
     * Возвращает нормализованные rates (QUOTE per 1 BASE).
     */
    private function fetchFromCBR(string $baseCode, array $allowedUpper): array
    {
        try {
            $resp = Http::withHeaders([
                'User-Agent' => 'PulsarCMS/1.0 (CurrencyUpdater)',
                'Accept'     => 'application/json',
            ])
                ->timeout(12)
                ->retry(2, 300)
                ->get('https://www.cbr-xml-daily.ru/daily_json.js');

            if (!$resp->ok()) return ['ok' => false, 'rates' => [], 'error' => 'HTTP '.$resp->status()];

            $json = $resp->json();
            if (!is_array($json)) return ['ok' => false, 'rates' => [], 'error' => 'invalid_payload'];

            $rub_per = ['RUB' => 1.0];
            if (isset($json['Valute']) && is_array($json['Valute'])) {
                foreach ($json['Valute'] as $code => $row) {
                    if (!isset($row['Value'])) continue;
                    $value   = (float) $row['Value'];
                    $nominal = max(1.0, (float) ($row['Nominal'] ?? 1));
                    $rub_per[strtoupper($code)] = $value / $nominal;
                }
            }

            $base = strtoupper($baseCode);
            if (!isset($rub_per[$base])) {
                return ['ok' => false, 'rates' => [], 'error' => "base_not_supported: {$base}"];
            }

            $out = [];
            foreach ($allowedUpper as $code) {
                $codeU = strtoupper($code);
                if ($codeU === $base) { $out[$codeU] = 1.0; continue; }
                if (!isset($rub_per[$codeU])) continue;

                // quote per 1 base
                $rate = $rub_per[$base] / $rub_per[$codeU];

                if (is_finite($rate) && $rate > 0) $out[$codeU] = (float) $rate;
            }

            return ['ok' => !empty($out), 'rates' => $out, 'error' => empty($out) ? 'empty_after_filter' : null];

        } catch (\Throwable $e) {
            return ['ok' => false, 'rates' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * История: обновить курсы с CBR для выбранной базовой валюты.
     */
    public function refreshRates(Currency $currency): RedirectResponse
    {
        try {
            $all = Currency::query()
                ->active()
                ->ordered()
                ->get(['id','code']);

            if ($all->isEmpty()) {
                return back()->withErrors(['general' => 'Нет активных валют для обновления.']);
            }

            $baseCode     = strtoupper($currency->code);
            $allowedUpper = $all->pluck('code')->map(fn($c)=>strtoupper($c))->values()->all();

            $parsed = $this->fetchFromCBR($baseCode, $allowedUpper);
            if (!$parsed['ok']) {
                Log::warning('refreshRates CBR failed', [
                    'error' => $parsed['error'] ?? null,
                    'base'  => $baseCode,
                ]);
                return back()->withErrors(['general' => 'ЦБ РФ не вернул корректные курсы.']);
            }

            $rates    = $parsed['rates'];
            $now      = Carbon::now();
            $baseId   = $currency->id;

            DB::transaction(function () use ($rates, $all, $baseId, $now) {
                // base->base = 1.0 (история)
                $this->createRateHistory($baseId, $baseId, '1.0', 'system', false, $now);

                foreach ($all as $quote) {
                    if ((int)$quote->id === (int)$baseId) continue;

                    $code = strtoupper($quote->code);
                    if (!array_key_exists($code, $rates)) continue;

                    $this->createRateHistory(
                        $baseId,
                        (int)$quote->id,
                        (string)$rates[$code],
                        'cbr.ru',
                        false,
                        $now
                    );
                }
            });

            return back()->with('success', __('admin/controllers.updated_successfully'));
        } catch (\Throwable $e) {
            Log::error("Ошибка refreshRates (CBR): ".$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['general' => 'Не удалось обновить курсы (ЦБ РФ).']);
        }
    }
}
