<?php

namespace App\Http\Requests\Admin\Finance\Currency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CurrencyRateRequest extends FormRequest
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

        $merge = [
            // фиксируем base из route
            'base_currency_id' => $baseId,
        ];

        if ($this->has('quote_currency_id')) {
            $merge['quote_currency_id'] = (int) $this->input('quote_currency_id');
        }

        if ($this->has('rate')) {
            $raw = trim((string) $this->input('rate'));
            $merge['rate'] = $raw === '' ? null : str_replace(',', '.', $raw);
        }

        if ($this->has('provider')) {
            $provider = trim((string) $this->input('provider'));
            $merge['provider'] = $provider !== '' ? $provider : null;
        }

        // is_manual по умолчанию true для ручных форм
        $isManual = $this->input('is_manual', true);
        $merge['is_manual'] = filter_var($isManual, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($merge['is_manual'] === null) $merge['is_manual'] = true;

        // история: момент обязателен, если не передали — ставим now()
        $rawFetchedAt = $this->input('fetched_at');
        if ($rawFetchedAt === null || $rawFetchedAt === '') {
            $merge['fetched_at'] = now()->toDateTimeString();
        } else {
            try {
                $merge['fetched_at'] = Carbon::parse((string) $rawFetchedAt)->toDateTimeString();
            } catch (\Throwable $e) {
                $merge['fetched_at'] = now()->toDateTimeString();
            }
        }

        $this->merge($merge);
    }

    public function rules(): array
    {
        return [
            'base_currency_id'  => ['required','integer','exists:currencies,id'],
            'quote_currency_id' => ['required','integer','exists:currencies,id','different:base_currency_id'],

            'rate'              => ['required','numeric','gt:0','decimal:0,18'],

            'provider'          => ['nullable','string','max:64'],
            'is_manual'         => ['required','boolean'],
            'fetched_at'        => ['required','date'],
        ];
    }
}
