<?php

namespace App\Http\Requests\Admin\School\ProgressRecord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgressRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('status')) {
            $merge['status'] = trim((string) $this->input('status'));
        }

        // нормализация чисел
        foreach (['progress_percent','time_spent_seconds'] as $k) {
            if ($this->filled($k)) {
                $merge[$k] = (int) $this->input($k);
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $currentId = $this->route('progress_record')?->id ?? null;

        return [
            // владельцы/связи
            'user_id'            => ['required','integer','exists:users,id'],
            'enrollment_id'      => ['nullable','integer','exists:enrollments,id'],
            'course_id'          => ['nullable','integer','exists:courses,id'],
            'module_id'          => ['nullable','integer','exists:modules,id'],
            'lesson_id'          => [
                'nullable','integer','exists:lessons,id',
                // уникальность пары (user_id, lesson_id), если lesson_id задан
                Rule::unique('progress_records', 'lesson_id')
                    ->where(fn($q) => $q->where('user_id', $this->input('user_id')))
                    ->ignore($currentId),
            ],

            // прогресс/состояние
            'status'             => ['sometimes','string', Rule::in(['in_progress','completed','skipped','locked'])],
            'progress_percent'   => ['sometimes','integer','between:0,100'],
            'time_spent_seconds' => ['sometimes','integer','min:0'],

            // тайминги
            'last_viewed_at'     => ['nullable','date'],
            'completed_at'       => ['nullable','date'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'           => 'Не указан пользователь.',
            'user_id.exists'             => 'Пользователь не найден.',
            'enrollment_id.exists'       => 'Указанное зачисление не найдено.',
            'course_id.exists'           => 'Курс не найден.',
            'module_id.exists'           => 'Модуль не найден.',
            'lesson_id.exists'           => 'Урок не найден.',
            'lesson_id.unique'           => 'Прогресс по этому уроку уже зафиксирован для данного пользователя.',
            'status.in'                  => 'Недопустимый статус. Разрешены: in_progress, completed, skipped, locked.',
            'progress_percent.between'   => 'Процент прогресса должен быть в диапазоне 0–100.',
            'time_spent_seconds.min'     => 'time_spent_seconds не может быть отрицательным.',
            'last_viewed_at.date'        => 'Поле last_viewed_at должно быть корректной датой.',
            'completed_at.date'          => 'Поле completed_at должно быть корректной датой.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id'            => 'пользователь',
            'enrollment_id'      => 'зачисление',
            'course_id'          => 'курс',
            'module_id'          => 'модуль',
            'lesson_id'          => 'урок',
            'status'             => 'статус',
            'progress_percent'   => 'процент прогресса',
            'time_spent_seconds' => 'затраченное время (сек)',
            'last_viewed_at'     => 'последняя активность',
            'completed_at'       => 'время завершения',
        ];
    }
}
