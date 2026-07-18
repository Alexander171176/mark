<?php

namespace App\Http\Requests\Admin\Review;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReviewRequest extends FormRequest
{
    /**
     * Разрешение выполнения запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка и нормализация данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        $reply = $this->normalizeNullableText($this->input('reply'));

        $moderationStatus = $this->filled('moderation_status')
            ? (int) $this->input('moderation_status')
            : 0;

        $this->merge([
            /** Полиморфная сущность */
            'reviewable_type' => $this->normalizeNullableString($this->input('reviewable_type')),

            'reviewable_id' => $this->filled('reviewable_id')
                ? (int) $this->input('reviewable_id')
                : null,

            /** Автор */
            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

            /** Оценка и содержимое */
            'rating' => $this->filled('rating')
                ? (int) $this->input('rating')
                : 5,

            'advantages' => $this->normalizeNullableText($this->input('advantages')),
            'disadvantages' => $this->normalizeNullableText($this->input('disadvantages')),
            'comment' => $this->normalizeNullableText($this->input('comment')),

            /** Подтверждённый опыт */
            'verified' => filter_var(
                $this->input('verified', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            /** Ответ */
            'reply' => $reply,

            'replied_by' => filled($reply)
                ? (
                $this->filled('replied_by')
                    ? (int) $this->input('replied_by')
                    : $this->user()?->id
                )
                : null,

            'replied_at' => filled($reply)
                ? (
                $this->filled('replied_at')
                    ? $this->input('replied_at')
                    : now()
                )
                : null,

            /** Модерация */
            'moderation_status' => $moderationStatus,

            'moderated_by' => $moderationStatus !== 0
                ? (
                $this->filled('moderated_by')
                    ? (int) $this->input('moderated_by')
                    : $this->user()?->id
                )
                : null,

            'moderated_at' => $moderationStatus !== 0
                ? (
                $this->filled('moderated_at')
                    ? $this->input('moderated_at')
                    : now()
                )
                : null,

            'moderation_note' => $this->normalizeNullableString(
                $this->input('moderation_note')
            ),

            /** Полезность и активность */
            'likes' => $this->filled('likes')
                ? max(0, (int) $this->input('likes'))
                : 0,

            'activity' => filter_var(
                $this->input('activity', true),
                FILTER_VALIDATE_BOOLEAN
            ),

            /** Удаляемые изображения */
            'deletedImages' => $this->normalizeDeletedImages(
                $this->input('deletedImages', [])
            ),
        ]);
    }

    /**
     * Правила валидации для store и update.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $reviewId = $this->resolveReviewId();

        return [
            /** Полиморфная сущность */
            'reviewable_type' => [
                'required',
                'string',
                'max:100',
                Rule::in(array_keys($this->reviewableTypes())),
            ],

            'reviewable_id' => [
                'required',
                'integer',

                function (string $attribute, mixed $value, \Closure $fail): void {
                    $modelClass = $this->resolveReviewableClass(
                        $this->input('reviewable_type')
                    );

                    if (! $modelClass) {
                        $fail('Указан недопустимый тип сущности отзыва.');

                        return;
                    }

                    if (! $modelClass::query()->whereKey((int) $value)->exists()) {
                        $fail('Связанная сущность отзыва не найдена.');
                    }
                },

                Rule::unique('reviews', 'reviewable_id')
                    ->where(fn ($query) => $query
                        ->where('reviewable_type', $this->input('reviewable_type'))
                        ->where('user_id', $this->input('user_id'))
                    )
                    ->ignore($reviewId),
            ],

            /** Автор */
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],

            /** Оценка */
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            /** Содержимое */
            'advantages' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'disadvantages' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:10000',
            ],

            /** Подтверждённый опыт */
            'verified' => [
                'nullable',
                'boolean',
            ],

            /** Ответ */
            'reply' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'replied_by' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
                'required_with:reply',
            ],

            'replied_at' => [
                'nullable',
                'date',
                'required_with:reply',
            ],

            /** Модерация */
            'moderation_status' => [
                'nullable',
                'integer',
                Rule::in([0, 1, 2]),
            ],

            'moderated_by' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'moderated_at' => [
                'nullable',
                'date',
            ],

            'moderation_note' => [
                'nullable',
                'string',
                'max:500',
            ],

            /** Полезность и активность */
            'likes' => [
                'nullable',
                'integer',
                'min:0',
                'max:4294967295',
            ],

            'activity' => [
                'nullable',
                'boolean',
            ],

            /** Изображения */
            'images' => [
                'nullable',
                'array',
            ],

            'images.*' => [
                'required',
                'array',
            ],

            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('review_images', 'id'),
                Rule::prohibitedIf(fn () => $this->isMethod('POST')),
            ],

            'images.*.order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'images.*.alt' => [
                'nullable',
                'string',
                'max:255',
            ],

            'images.*.caption' => [
                'nullable',
                'string',
                'max:255',
            ],

            'images.*.file' => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            /** Удаляемые изображения */
            'deletedImages' => [
                'sometimes',
                'array',
            ],

            'deletedImages.*' => [
                'integer',
                'distinct',
                Rule::exists('review_images', 'id'),
            ],
        ];
    }

    /**
     * Дополнительные проверки.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateReviewContent($validator);
            $this->validateModerationData($validator);
            $this->validateReplyData($validator);
            $this->validateExistingImages($validator);
        });
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            /** Связанная сущность */
            'reviewable_type.required' => 'Необходимо указать тип сущности отзыва.',
            'reviewable_type.string' => 'Тип сущности отзыва должен быть строкой.',
            'reviewable_type.max' => 'Тип сущности отзыва не должен превышать 100 символов.',
            'reviewable_type.in' => 'Указан недопустимый тип сущности отзыва.',

            'reviewable_id.required' => 'Необходимо выбрать сущность для отзыва.',
            'reviewable_id.integer' => 'Идентификатор связанной сущности должен быть числом.',
            'reviewable_id.unique' => 'Этот пользователь уже оставил отзыв на выбранную сущность.',

            /** Автор */
            'user_id.required' => 'Необходимо указать автора отзыва.',
            'user_id.integer' => 'Идентификатор автора должен быть числом.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            /** Оценка */
            'rating.required' => 'Необходимо указать оценку.',
            'rating.integer' => 'Оценка должна быть целым числом.',
            'rating.between' => 'Оценка должна быть от 1 до 5.',

            /** Содержимое */
            'advantages.string' => 'Достоинства должны быть текстом.',
            'advantages.max' => 'Достоинства не должны превышать 5000 символов.',

            'disadvantages.string' => 'Недостатки должны быть текстом.',
            'disadvantages.max' => 'Недостатки не должны превышать 5000 символов.',

            'comment.string' => 'Комментарий должен быть текстом.',
            'comment.max' => 'Комментарий не должен превышать 10000 символов.',

            /** Подтверждение */
            'verified.boolean' => 'Поле подтверждённого опыта должно быть логическим значением.',

            /** Ответ */
            'reply.string' => 'Ответ должен быть текстом.',
            'reply.max' => 'Ответ не должен превышать 10000 символов.',

            'replied_by.integer' => 'Идентификатор автора ответа должен быть числом.',
            'replied_by.exists' => 'Пользователь, оставивший ответ, не найден.',
            'replied_by.required_with' => 'Необходимо указать автора ответа.',

            'replied_at.date' => 'Дата ответа имеет неверный формат.',
            'replied_at.required_with' => 'Необходимо указать дату ответа.',

            /** Модерация */
            'moderation_status.integer' => 'Статус модерации должен быть числом.',
            'moderation_status.in' => 'Недопустимое значение статуса модерации.',

            'moderated_by.integer' => 'Идентификатор модератора должен быть числом.',
            'moderated_by.exists' => 'Указанный модератор не найден.',

            'moderated_at.date' => 'Дата модерации имеет неверный формат.',

            'moderation_note.string' => 'Комментарий модератора должен быть текстом.',
            'moderation_note.max' => 'Комментарий модератора не должен превышать 500 символов.',

            /** Полезность и активность */
            'likes.integer' => 'Количество отметок полезности должно быть целым числом.',
            'likes.min' => 'Количество отметок полезности не может быть отрицательным.',
            'likes.max' => 'Превышено максимальное количество отметок полезности.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',

            /** Изображения */
            'images.array' => 'Изображения должны быть массивом.',
            'images.*.array' => 'Данные изображения должны быть массивом.',

            'images.*.id.integer' => 'Идентификатор изображения должен быть числом.',
            'images.*.id.exists' => 'Одно из изображений отзыва не найдено.',
            'images.*.id.prohibited_if' => 'При создании отзыва нельзя передавать существующий ID изображения.',

            'images.*.order.integer' => 'Порядок изображения должен быть целым числом.',
            'images.*.order.min' => 'Порядок изображения не может быть меньше 0.',

            'images.*.alt.string' => 'Alt-текст изображения должен быть строкой.',
            'images.*.alt.max' => 'Alt-текст изображения не должен превышать 255 символов.',

            'images.*.caption.string' => 'Подпись изображения должна быть строкой.',
            'images.*.caption.max' => 'Подпись изображения не должна превышать 255 символов.',

            'images.*.file.required_without' => 'Необходимо загрузить файл изображения.',
            'images.*.file.file' => 'Загруженный объект должен быть файлом.',
            'images.*.file.image' => 'Загруженный файл должен быть изображением.',
            'images.*.file.mimes' => 'Допустимые форматы: JPEG, JPG, PNG, GIF, SVG и WEBP.',
            'images.*.file.max' => 'Размер изображения не должен превышать 10 МБ.',

            'deletedImages.array' => 'Удаляемые изображения должны быть массивом.',
            'deletedImages.*.integer' => 'Идентификатор удаляемого изображения должен быть числом.',
            'deletedImages.*.distinct' => 'Удаляемые изображения не должны повторяться.',
            'deletedImages.*.exists' => 'Одно из удаляемых изображений не найдено.',
        ];
    }

    /**
     * Отзыв должен содержать хотя бы одно текстовое поле.
     */
    protected function validateReviewContent(Validator $validator): void
    {
        $hasContent = filled($this->input('advantages'))
            || filled($this->input('disadvantages'))
            || filled($this->input('comment'));

        if (! $hasContent) {
            $validator->errors()->add(
                'comment',
                'Необходимо заполнить текст отзыва, достоинства или недостатки.'
            );
        }
    }

    /**
     * Проверка данных модерации.
     */
    protected function validateModerationData(Validator $validator): void
    {
        $moderationStatus = (int) $this->input('moderation_status', 0);

        if ($moderationStatus === 0) {
            return;
        }

        if (! $this->filled('moderated_by')) {
            $validator->errors()->add(
                'moderated_by',
                'Необходимо указать модератора.'
            );
        }

        if (! $this->filled('moderated_at')) {
            $validator->errors()->add(
                'moderated_at',
                'Необходимо указать дату модерации.'
            );
        }

        if ($moderationStatus === 2 && ! $this->filled('moderation_note')) {
            $validator->errors()->add(
                'moderation_note',
                'При отклонении отзыва необходимо указать причину.'
            );
        }
    }

    /**
     * Проверка согласованности ответа.
     */
    protected function validateReplyData(Validator $validator): void
    {
        if ($this->filled('replied_at') && ! $this->filled('reply')) {
            $validator->errors()->add(
                'reply',
                'Нельзя указать дату ответа без текста ответа.'
            );
        }

        if ($this->filled('replied_by') && ! $this->filled('reply')) {
            $validator->errors()->add(
                'reply',
                'Нельзя указать автора ответа без текста ответа.'
            );
        }
    }

    /**
     * Существующие и удаляемые изображения должны принадлежать
     * редактируемому отзыву.
     */
    protected function validateExistingImages(Validator $validator): void
    {
        $reviewId = $this->resolveReviewId();

        if (! $reviewId) {
            return;
        }

        $imageIds = collect($this->input('images', []))
            ->pluck('id')
            ->merge($this->input('deletedImages', []))
            ->filter(fn (mixed $id) => is_numeric($id))
            ->map(fn (mixed $id) => (int) $id)
            ->unique()
            ->values();

        if ($imageIds->isEmpty()) {
            return;
        }

        $attachedImageIds = DB::table('review_has_images')
            ->where('review_id', $reviewId)
            ->whereIn('review_image_id', $imageIds)
            ->pluck('review_image_id')
            ->map(fn ($id) => (int) $id);

        $invalidIds = $imageIds->diff($attachedImageIds);

        if ($invalidIds->isNotEmpty()) {
            $validator->errors()->add(
                'images',
                'Одно или несколько изображений не принадлежат редактируемому отзыву.'
            );
        }
    }

    /**
     * Получить ID редактируемого отзыва из маршрута.
     */
    protected function resolveReviewId(): ?int
    {
        $routeReview = $this->route('review')
            ?? $this->route('id');

        if (is_object($routeReview) && isset($routeReview->id)) {
            return (int) $routeReview->id;
        }

        return is_numeric($routeReview)
            ? (int) $routeReview
            : null;
    }

    /**
     * Разрешённые полиморфные типы.
     *
     * Ключ хранится в reviews.reviewable_type.
     *
     * @return array<string, class-string<Model>>
     */
    protected function reviewableTypes(): array
    {
        return [
            'market_product' => MarketProduct::class,

            /*
             * Позднее добавим:
             *
             * 'market_bundle' => MarketBundle::class,
             * 'school_course' => SchoolCourse::class,
             */
        ];
    }

    /**
     * Получить класс модели по morph-алиасу.
     *
     * @return class-string<Model>|null
     */
    protected function resolveReviewableClass(?string $type): ?string
    {
        return $this->reviewableTypes()[$type] ?? null;
    }

    /**
     * Нормализация удаляемых изображений.
     */
    protected function normalizeDeletedImages(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn (mixed $id) => is_numeric($id))
            ->map(fn (mixed $id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Нормализация nullable-строки.
     */
    protected function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * Нормализация nullable-текста.
     */
    protected function normalizeNullableText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
