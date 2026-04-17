<?php

namespace App\Http\Requests\Admin\Blog\Comment;

use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\Blog\Video\Video;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreating = $this->isMethod('post');

        // route('comment') может быть моделью или id
        $routeComment = $this->route('comment');
        $commentId = is_object($routeComment) ? ($routeComment->id ?? null) : $routeComment;

        // Разрешённые типы commentable
        $commentableTypes = [
            Article::class,
            Video::class,
        ];

        return [
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],

            // На CREATE обязательно, на UPDATE обычно не меняем
            'commentable_type' => [
                Rule::requiredIf(fn () => $isCreating),
                'string',
                Rule::in($commentableTypes),
            ],

            'commentable_id' => [
                Rule::requiredIf(fn () => $isCreating),
                'integer',

                // Проверяем существование ID в зависимости от типа
                function ($attribute, $value, $fail) use ($isCreating) {
                    // На update commentable_* обычно не присылаем — если нет, пропускаем
                    $type = $this->input('commentable_type');

                    if (! $type || ! $value) {
                        if ($isCreating) {
                            $fail('Не указана связанная сущность комментария.');
                        }
                        return;
                    }

                    $table = match ($type) {
                        Article::class => 'articles',
                        Video::class   => 'videos',
                        default        => null,
                    };

                    if (! $table) {
                        $fail('Недопустимый тип связанной сущности.');
                        return;
                    }

                    $exists = DB::table($table)->where('id', (int) $value)->exists();
                    if (! $exists) {
                        $fail('Связанная сущность не найдена.');
                    }
                },
            ],

            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id'),
                // не даём выбрать себя родителем
                Rule::when($commentId, ['not_in:' . $commentId]),

                // parent должен быть из того же commentable (очень желательно)
                function ($attribute, $value, $fail) {
                    if (! $value) return;

                    $type = $this->input('commentable_type');
                    $cid  = $this->input('commentable_id');

                    // Если commentable не пришёл (например update только текста) —
                    // тогда не навязываем проверку “одной сущности”.
                    if (! $type || ! $cid) return;

                    $parent = Comment::query()->find($value);
                    if (! $parent) return;

                    if ($parent->commentable_type !== $type || (int)$parent->commentable_id !== (int)$cid) {
                        $fail('Родительский комментарий должен относиться к той же сущности (commentable).');
                    }
                },
            ],

            'content' => ['required', 'string', 'max:65535'],

            'activity' => ['required', 'boolean'],

            // ВАЖНО: approved НЕТ -> moderation_status
            'moderation_status' => ['nullable', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
            // moderated_by / moderated_at не принимаем с фронта
        ];
    }

    public function messages(): array
    {
        return Lang::get('admin/requests');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);

        // parent_id: пустое -> null
        if ($this->input('parent_id') === '' || $this->input('parent_id') === 0 || $this->input('parent_id') === '0') {
            $this->merge(['parent_id' => null]);
        }

        // Нормализуем moderation_status (если вдруг пришёл строкой)
        if ($this->has('moderation_status') && $this->input('moderation_status') !== null) {
            $this->merge([
                'moderation_status' => is_numeric($this->input('moderation_status'))
                    ? (int) $this->input('moderation_status')
                    : $this->input('moderation_status'),
            ]);
        }
    }
}
