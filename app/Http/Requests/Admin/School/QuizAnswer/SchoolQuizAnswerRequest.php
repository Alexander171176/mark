<?php

namespace App\Http\Requests\Admin\School\QuizAnswer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolQuizAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['school_quiz_id', 'school_quiz_question_id', 'weight', 'sort'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['is_correct', 'activity'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if ($this->has('translations') && is_array($this->input('translations'))) {
            $data['translations'] = collect($this->input('translations'))
                ->map(function ($translation) {
                    if (isset($translation['locale']) && is_string($translation['locale'])) {
                        $translation['locale'] = mb_strtolower(trim($translation['locale']));
                    }

                    return $translation;
                })
                ->toArray();
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
            'school_quiz_id' => [
                'required',
                'integer',
                Rule::exists('school_quizzes', 'id'),
            ],

            'school_quiz_question_id' => [
                'required',
                'integer',
                Rule::exists('school_quiz_questions', 'id'),
            ],

            'is_correct' => ['nullable', 'boolean'],
            'weight' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['nullable', 'boolean'],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.locale' => ['required', 'string', 'max:10', 'distinct'],
            'translations.*.text' => ['required', 'string'],
            'translations.*.explanation' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_quiz_id.required' => 'Не указан квиз, к которому относится ответ.',
            'school_quiz_id.integer' => 'Идентификатор квиза должен быть числом.',
            'school_quiz_id.exists' => 'Указанный квиз не найден.',

            'school_quiz_question_id.required' => 'Не указан вопрос, к которому относится ответ.',
            'school_quiz_question_id.integer' => 'Идентификатор вопроса должен быть числом.',
            'school_quiz_question_id.exists' => 'Указанный вопрос не найден.',

            'is_correct.boolean' => 'Поле «Правильный ответ» должно быть булевым значением.',

            'weight.integer' => 'Вес ответа должен быть целым числом.',
            'weight.min' => 'Вес ответа не может быть меньше 0.',
            'weight.max' => 'Вес ответа не может быть больше 100.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min' => 'Позиция не может быть отрицательной.',

            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'translations.required' => 'Необходимо передать переводы ответа.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Добавьте хотя бы один перевод.',

            'translations.*.locale.required' => 'Укажите локаль перевода.',
            'translations.*.locale.string' => 'Локаль должна быть строкой.',
            'translations.*.locale.max' => 'Локаль не должна превышать 10 символов.',
            'translations.*.locale.distinct' => 'Локали переводов не должны повторяться.',

            'translations.*.text.required' => 'Заполните текст варианта ответа.',
            'translations.*.text.string' => 'Текст ответа должен быть строкой.',

            'translations.*.explanation.string' => 'Пояснение должно быть текстом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_quiz_id' => 'Квиз',
            'school_quiz_question_id' => 'Вопрос квиза',
            'is_correct' => 'Правильный ответ',
            'weight' => 'Вес ответа',
            'sort' => 'Позиция',
            'activity' => 'Активность',
            'translations' => 'Переводы',
        ];
    }
}
