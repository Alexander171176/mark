<?php

namespace App\Http\Requests\Admin\Finance\Currency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class BulkCurrencyRatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $routeCurrency = $this->route('currency');
        $baseId = is_object($routeCurrency)
            ? (int) data_get($routeCurrency, 'id', 0)
            : (int) ($routeCurrency ?? 0);

        $inputRates = $this->input('rates', []);
        $normalized = [];

        $now = now()->toDateTimeString();

        if (is_array($inputRates)) {
            foreach ($inputRates as $item) {
                $rawRate = trim((string) ($item['rate'] ?? ''));
                $rate = $rawRate === '' ? null : str_replace(',', '.', $rawRate);

                $provider = $item['provider'] ?? null;
                $provider = is_string($provider) && trim($provider) !== '' ? trim($provider) : null;

                $isManual = filter_var($item['is_manual'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($isManual === null) $isManual = true;

                $rawFetchedAt = $item['fetched_at'] ?? null;
                if ($rawFetchedAt === null || $rawFetchedAt === '') {
                    $fetchedAt = $now;
                } else {
                    try {
                        $fetchedAt = Carbon::parse((string) $rawFetchedAt)->toDateTimeString();
                    } catch (\Throwable $e) {
                        $fetchedAt = $now;
                    }
                }

                $normalized[] = [
                    'base_currency_id'  => $baseId,
                    'quote_currency_id' => (int) ($item['quote_currency_id'] ?? 0),
                    'rate'              => $rate, // строка
                    'provider'          => $provider,
                    'is_manual'         => $isManual,
                    'fetched_at'        => $fetchedAt,
                ];
            }
        }

        $this->merge(['rates' => $normalized]);
    }

    public function rules(): array
    {
        return [
            'rates'                     => ['required','array','min:1'],

            'rates.*.base_currency_id'  => ['required','integer','exists:currencies,id'],
            'rates.*.quote_currency_id' => ['required','integer','exists:currencies,id'],

            'rates.*.rate'              => ['required','numeric','gt:0','decimal:0,18'],
            'rates.*.provider'          => ['nullable','string','max:64'],
            'rates.*.is_manual'         => ['required','boolean'],
            'rates.*.fetched_at'        => ['required','date'],
        ];
    }
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $rates = $this->input('rates', []);
            foreach ($rates as $i => $row) {
                if ((int)($row['quote_currency_id'] ?? 0) === (int)($row['base_currency_id'] ?? 0)) {
                    $v->errors()->add("rates.$i.quote_currency_id", 'Базовая и котируемая валюты должны отличаться.');
                }
            }
        });
    }
}

