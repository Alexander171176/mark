<?php

namespace App\Http\Requests\Admin\School\QuizQuestion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Включи Policy при необходимости. Пока разрешаем.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ints = ['quiz_id','sort','points'];
        $merge = [];

        foreach ($ints as $key) {
            if ($this->filled($key)) {
                $merge[$key] = (int) $this->input($key);
            }
        }

        if ($this->has('activity')) {
            $merge['activity'] = filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'quiz_id'       => ['required','integer','exists:quizzes,id'],

            'sort'          => ['sometimes','integer','min:0'],
            'question_type' => ['required','string', Rule::in(['single_choice','multiple_choice','true_false','open_text'])],

            'question_text' => ['required','string'],
            'explanation'   => ['nullable','string'],

            'points'        => ['required','integer','min:0'], // по миграции default=1, но 0 допустим для «неоцениваемых»
            'meta'          => ['nullable','array'],

            'activity'     => ['sometimes','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'quiz_id.required' => 'Не указан квиз, к которому относится вопрос.',
            'quiz_id.integer'  => 'Идентификатор квиза должен быть числом.',
            'quiz_id.exists'   => 'Указанный квиз не найден.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min'     => 'Позиция не может быть отрицательной.',

            'question_type.required' => 'Укажите тип вопроса.',
            'question_type.in'       => 'Недопустимый тип вопроса. Разрешено: single_choice, multiple_choice, true_false, open_text.',

            'question_text.required' => 'Заполните текст вопроса.',
            'question_text.string'   => 'Текст вопроса должен быть строкой.',

            'explanation.string'     => 'Объяснение должно быть текстом.',

            'points.required' => 'Укажите количество баллов за вопрос.',
            'points.integer'  => 'Баллы должны быть целым числом.',
            'points.min'      => 'Баллы не могут быть меньше :min.',

            'meta.array'      => 'Поле meta должно быть объектом (JSON).',

            'activity.boolean'=> 'Поле публикации должно быть булевым значением.',
        ];
    }

    public function attributes(): array
    {
        return [
            'quiz_id'       => 'Квиз',
            'sort'          => 'Позиция',
            'question_type' => 'Тип вопроса',
            'question_text' => 'Текст вопроса',
            'explanation'   => 'Объяснение',
            'points'        => 'Баллы',
            'meta'          => 'Метаданные',
            'activity'      => 'Публикация',
        ];
    }
}
