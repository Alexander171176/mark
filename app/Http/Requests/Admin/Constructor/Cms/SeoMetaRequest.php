<?php

namespace App\Http\Requests\Admin\Constructor\Cms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeoMetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['title','keywords','canonical_url','og_title','og_image_url','og_type','twitter_card','locale'] as $f) {
            if ($this->filled($f)) {
                $merge[$f] = trim((string) $this->input($f));
            }
        }

        if ($this->filled('og_type')) {
            $merge['og_type'] = strtolower((string) $this->input('og_type'));
        }
        if ($this->filled('twitter_card')) {
            $merge['twitter_card'] = strtolower((string) $this->input('twitter_card'));
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string,ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Типичные значения для OG и Twitter Card
        $ogTypes = ['article','website','profile','video','book'];
        $twitterCards = ['summary','summary_large_image','app','player'];

        return [
            // Полиморфная цель (оба nullable, но если один указан — требовать второй)
            'seoable_type'      => ['nullable','string','max:255','required_with:seoable_id'],
            'seoable_id'        => ['nullable','integer','required_with:seoable_type'],

            // Базовое SEO
            'title'             => ['nullable','string','max:255'],
            'description'       => ['nullable','string'],
            'keywords'          => ['nullable','string','max:255'],

            // Индексация/каноникал
            'robots_noindex'    => ['boolean'],
            'robots_nofollow'   => ['boolean'],
            'canonical_url'     => ['nullable','url','max:2048'],

            // OpenGraph
            'og_title'          => ['nullable','string','max:255'],
            'og_description'    => ['nullable','string'],
            'og_image_url'      => ['nullable','url','max:2048'],
            'og_type'           => ['nullable','string','max:50', Rule::in($ogTypes)],

            // Twitter
            'twitter_card'      => ['nullable','string','max:20', Rule::in($twitterCards)],

            // Локаль/флаги
            'locale'            => ['nullable','string','max:10'],
            'activity'         => ['boolean'],

            // Структурированные данные и произвольные мета
            'json_ld'           => ['nullable','array'],
            'meta'              => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'seoable_type'    => 'тип сущности',
            'seoable_id'      => 'ID сущности',
            'title'           => 'SEO‑title',
            'description'     => 'SEO‑description',
            'keywords'        => 'SEO‑keywords',
            'robots_noindex'  => 'robots noindex',
            'robots_nofollow' => 'robots nofollow',
            'canonical_url'   => 'канонический URL',
            'og_title'        => 'OG title',
            'og_description'  => 'OG description',
            'og_image_url'    => 'OG image URL',
            'og_type'         => 'OG type',
            'twitter_card'    => 'Twitter card',
            'locale'          => 'локаль',
            'activity'        => 'флаг активности',
            'json_ld'         => 'JSON‑LD',
            'meta'            => 'метаданные',
        ];
    }
}
