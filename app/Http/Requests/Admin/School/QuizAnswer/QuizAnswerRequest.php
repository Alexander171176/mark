<?php

namespace App\Http\Requests\Admin\School\QuizAnswer;

use Illuminate\Foundation\Http\FormRequest;

class QuizAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Подключи Policy при необходимости. Пока разрешаем.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['quiz_id','quiz_question_id','weight','sort'] as $int) {
            if ($this->filled($int)) $merge[$int] = (int) $this->input($int);
        }

        foreach (['is_correct','activity'] as $bool) {
            if ($this->has($bool)) {
                $merge[$bool] = filter_var($this->input($bool), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
        }

        if ($merge) $this->merge($merge);
    }

    public function rules(): array
    {
        return [
            'quiz_id'          => ['required','integer','exists:quizzes,id'],
            'quiz_question_id' => ['required','integer','exists:quiz_questions,id'],

            'text'        => ['required','string'],
            'is_correct'  => ['sometimes','boolean'],
            'weight'      => ['sometimes','integer','min:0','max:100'], // 0..100 для частичного начисления
            'sort'        => ['sometimes','integer','min:0'],
            'explanation' => ['nullable','string'],
            'activity'    => ['sometimes','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'quiz_id.required'          => 'Не указан квиз, к которому относится ответ.',
            'quiz_id.integer'           => 'Идентификатор квиза должен быть числом.',
            'quiz_id.exists'            => 'Указанный квиз не найден.',

            'quiz_question_id.required' => 'Не указан вопрос, к которому относится ответ.',
            'quiz_question_id.integer'  => 'Идентификатор вопроса должен быть числом.',
            'quiz_question_id.exists'   => 'Указанный вопрос не найден.',

            'text.required' => 'Заполните текст варианта ответа.',
            'text.string'   => 'Текст ответа должен быть строкой.',

            'is_correct.boolean' => 'Поле «Правильный ответ» должно быть булевым значением.',

            'weight.integer' => 'Вес ответа должен быть целым числом.',
            'weight.min'     => 'Вес ответа не может быть меньше :min.',
            'weight.max'     => 'Вес ответа не может быть больше :max.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min'     => 'Позиция не может быть отрицательной.',

            'explanation.string' => 'Пояснение должно быть текстом.',

            'activity.boolean' => 'Поле публикации должно быть булевым значением.',
        ];
    }

    public function attributes(): array
    {
        return [
            'quiz_id'          => 'Квиз',
            'quiz_question_id' => 'Вопрос квиза',
            'text'             => 'Текст ответа',
            'is_correct'       => 'Правильный ответ',
            'weight'           => 'Вес ответа',
            'sort'             => 'Позиция',
            'explanation'      => 'Пояснение',
            'activity'         => 'Публикация',
        ];
    }
}
