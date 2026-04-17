<?php

namespace App\Http\Requests\Admin\Blog\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // route('banner') может быть моделью или id — делаем универсально
        $routeBanner = $this->route('banner');
        $bannerId = is_object($routeBanner) ? ($routeBanner->id ?? null) : $routeBanner;

        // если user_id не приходит (обычно в админке), берём из auth
        $userId = (int) ($this->input('user_id') ?: ($this->user()?->id ?? 0));

        return [
            'sort'     => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'left'  => ['required', 'boolean'],
            'main'  => ['required', 'boolean'],
            'right' => ['required', 'boolean'],

            'locale' => ['required', 'string', 'max:10'],

            // moderation
            'moderation_status' => ['nullable', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
            // moderated_by / moderated_at — НЕ принимаем с фронта

            'title' => [
                'required',
                'string',
                'max:255',

                // В миграции banners нет unique, но логично держать уникальность в рамках владельца+локали.
                // Если не нужно — скажешь, и уберём это правило.
                Rule::unique('banners', 'title')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($bannerId),
            ],

            'link'    => ['nullable', 'string'], // text
            'short'   => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:255'],

            /**
             * ВАЖНО: sections УДАЛЯЕМ — их нет.
             */

            // ---------- images (НЕ МЕНЯЮ ЛОГИКУ, как просил) ----------
            'images' => ['nullable', 'array'],

            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('banner_images', 'id'),
                Rule::prohibitedIf(fn () => $this->isMethod('POST')),
            ],

            'images.*.order'   => ['nullable', 'integer', 'min:0'],
            'images.*.alt'     => ['nullable', 'string', 'max:255'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],

            'images.*.file' => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            'deletedImages'   => ['sometimes', 'array'],
            'deletedImages.*' => ['integer', 'exists:banner_images,id'],
        ];
    }

    public function messages(): array
    {
        return Lang::get('admin/requests');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'left'     => filter_var($this->input('left'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'main'     => filter_var($this->input('main'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'right'    => filter_var($this->input('right'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,

            'locale'   => is_string($this->input('locale')) ? trim($this->input('locale')) : $this->input('locale'),
            'title'    => is_string($this->input('title')) ? trim($this->input('title')) : $this->input('title'),
            'link'     => is_string($this->input('link')) ? trim($this->input('link')) : $this->input('link'),
            'short'    => is_string($this->input('short')) ? trim($this->input('short')) : $this->input('short'),
            'comment'  => is_string($this->input('comment')) ? trim($this->input('comment')) : $this->input('comment'),
        ]);
    }
}
