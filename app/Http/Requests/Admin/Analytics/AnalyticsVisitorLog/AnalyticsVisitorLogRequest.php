<?php

namespace App\Http\Requests\Admin\Analytics\AnalyticsVisitorLog;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AnalyticsVisitorLogRequest extends FormRequest
{
    /**
     * Разрешаем автоматический сбор аналитики.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Валидация данных, которые приходят от frontend tracker.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'visitor_uuid' => ['nullable', 'uuid'],

            'method' => ['nullable', 'string', 'max:10'],
            'url' => ['nullable', 'string', 'max:1000'],
            'page_title' => ['nullable', 'string', 'max:500'],
            'route_name' => ['nullable', 'string', 'max:255'],

            'module' => ['nullable', 'string', 'max:50'],
            'entity_type' => ['nullable', 'string', 'max:100'],
            'entity_id' => ['nullable', 'integer', 'min:1'],
            'event_type' => ['nullable', 'string', 'max:50'],

            'request_type' => ['nullable', 'string', 'max:30'],

            'screen_width' => ['nullable', 'integer', 'min:0'],
            'screen_height' => ['nullable', 'integer', 'min:0'],
            'browser_language' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:100'],

            'referer' => ['nullable', 'string', 'max:1000'],

            'time_on_page' => ['nullable', 'integer', 'min:0'],
            'scroll_depth' => ['nullable', 'integer', 'min:0', 'max:100'],
            'clicks_count' => ['nullable', 'integer', 'min:0'],

            'locale' => ['nullable', 'string', 'max:10'],
        ];
    }
}
