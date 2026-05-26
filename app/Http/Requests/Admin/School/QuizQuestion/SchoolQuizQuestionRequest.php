<?php

namespace App\Http\Requests\Admin\School\QuizQuestion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['school_quiz_id', 'sort', 'points'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->has('activity')) {
            $data['activity'] = filter_var(
                $this->input('activity'),
                FILTER_VALIDATE_BOOL,
                FILTER_NULL_ON_FAILURE
            );
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
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
        $question = $this->route('schoolQuizQuestion')
            ?? $this->route('quizQuestion')
            ?? $this->route('school_quiz_question')
            ?? $this->route('id');

        // Зарезервировано для update-правил, если появится уникальность
        $questionId = is_object($question)
            ? $question->id
            : ($question ? (int) $question : null);

        return [
            'school_quiz_id' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'integer',
                Rule::exists('school_quizzes', 'id'),
            ],

            'sort' => ['nullable', 'integer', 'min:0'],

            'question_type' => [
                'required',
                'string',
                Rule::in(['single_choice', 'multiple_choice', 'true_false', 'open_text']),
            ],

            'points' => ['required', 'integer', 'min:0', 'max:65535'],
            'meta' => ['nullable', 'array'],
            'activity' => ['required', 'boolean'],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.question_text' => ['required', 'string'],
            'translations.*.explanation' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_quiz_id.required' => 'Не указан квиз, к которому относится вопрос.',
            'school_quiz_id.integer' => 'Идентификатор квиза должен быть числом.',
            'school_quiz_id.exists' => 'Указанный квиз не найден.',

            'sort.integer' => 'Позиция должна быть целым числом.',
            'sort.min' => 'Позиция не может быть отрицательной.',

            'question_type.required' => 'Укажите тип вопроса.',
            'question_type.in' => 'Недопустимый тип вопроса. Разрешено: single_choice, multiple_choice, true_false, open_text.',

            'points.required' => 'Укажите количество баллов за вопрос.',
            'points.integer' => 'Баллы должны быть целым числом.',
            'points.min' => 'Баллы не могут быть меньше 0.',
            'points.max' => 'Баллы не могут быть больше 65535.',

            'meta.array' => 'Поле meta должно быть JSON-объектом.',

            'activity.required' => 'Укажите активность вопроса.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'translations.required' => 'Необходимо передать переводы вопроса.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Добавьте хотя бы один перевод.',

            'translations.*.locale.required' => 'Укажите локаль перевода.',
            'translations.*.locale.string' => 'Локаль должна быть строкой.',
            'translations.*.locale.max' => 'Локаль не должна превышать 10 символов.',
            'translations.*.locale.distinct' => 'Локали переводов не должны повторяться.',

            'translations.*.question_text.required' => 'Заполните текст вопроса.',
            'translations.*.question_text.string' => 'Текст вопроса должен быть строкой.',

            'translations.*.explanation.string' => 'Объяснение должно быть текстом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'school_quiz_id' => 'Квиз',
            'sort' => 'Позиция',
            'question_type' => 'Тип вопроса',
            'points' => 'Баллы',
            'meta' => 'Метаданные',
            'activity' => 'Активность',
            'translations' => 'Переводы',
        ];
    }
}
