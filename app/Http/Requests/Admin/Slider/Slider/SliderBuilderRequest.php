<?php

namespace App\Http\Requests\Admin\Slider\Slider;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Валидация единого конструктора. Наследует проверку полей SliderRequest.
 * Вложенные коллекции: отсутствие — оставить без изменений, [] — удалить все.
 * Изображения: ID существующего или UploadedFile нового Desktop.
 */
class SliderBuilderRequest extends SliderRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules += [
            'slides' => ['sometimes', 'array', 'max:200'],
            'slides.*' => ['required', 'array:id,activity,sort,is_main,status,published_at,show_from_at,show_to_at,background_color,text_color,accent_color,overlay_color,overlay_opacity,image_position,content_position,animation_type,animation_duration,animation_delay,animation_stagger,animation_once,animation_settings,settings,translations,actions,advantages,images'],
            'slides.*.id' => ['sometimes', 'nullable', 'integer', 'min:1', Rule::exists('slider_slides', 'id')],
            'slides.*.activity' => ['sometimes', 'boolean'],
            'slides.*.sort' => ['sometimes', 'integer', 'min:0'],
            'slides.*.is_main' => ['sometimes', 'boolean'],
            'slides.*.status' => ['sometimes', Rule::in(['draft','published','archived'])],
            'slides.*.published_at' => ['nullable', 'date'],
            'slides.*.show_from_at' => ['nullable', 'date'],
            'slides.*.show_to_at' => ['nullable', 'date'],
            'slides.*.background_color' => ['nullable', 'string', 'max:30'],
            'slides.*.text_color' => ['nullable', 'string', 'max:30'],
            'slides.*.accent_color' => ['nullable', 'string', 'max:30'],
            'slides.*.overlay_color' => ['nullable', 'string', 'max:30'],
            'slides.*.overlay_opacity' => ['nullable', 'integer', 'between:0,100'],
            'slides.*.image_position' => ['sometimes', Rule::in(['object-center','object-top','object-bottom','object-left','object-right','object-left-top','object-left-bottom','object-right-top','object-right-bottom'])],
            'slides.*.content_position' => ['sometimes', Rule::in(['left','center','right'])],
            'slides.*.animation_type' => ['sometimes', Rule::in(['none','fade','fade-up','fade-down','fade-left','fade-right','zoom-in','zoom-out','slide-up','slide-down','slide-left','slide-right'])],
            'slides.*.animation_duration' => ['sometimes', 'integer', 'min:0'],
            'slides.*.animation_delay' => ['sometimes', 'integer', 'min:0'],
            'slides.*.animation_stagger' => ['sometimes', 'integer', 'min:0'],
            'slides.*.animation_once' => ['sometimes', 'boolean'],
            'slides.*.animation_settings' => ['nullable', 'array'],
            'slides.*.settings' => ['nullable', 'array'],
            'slides.*.translations' => ['sometimes', 'array'],
            'slides.*.translations.*' => ['required', 'array:label,title,accent,description'],
            'slides.*.translations.*.label' => ['nullable', 'string', 'max:255'],
            'slides.*.translations.*.title' => ['nullable', 'string', 'max:255'],
            'slides.*.translations.*.accent' => ['nullable', 'string', 'max:255'],
            'slides.*.translations.*.description' => ['nullable', 'string'],
            'slides.*.images' => ['sometimes', 'array', 'max:30'],
            'slides.*.images.*' => ['required', 'array:id,file,alt,caption'],
            'slides.*.images.*.id' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                Rule::exists('slider_slide_images', 'id'),
            ],
            'slides.*.images.*.file' => [
                'sometimes',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:10240',
            ],
            'slides.*.images.*.alt' => ['nullable', 'string', 'max:255'],
            'slides.*.images.*.caption' => ['nullable', 'string', 'max:255'],
            'slides.*.actions' => ['sometimes', 'array', 'max:30'],
            'slides.*.actions.*' => ['required', 'array:id,sort,activity,is_primary,action_type,action_value,route_params,target,style,icon,icon_position,css_class,settings,translations'],
            'slides.*.actions.*.id' => ['sometimes', 'nullable', 'integer', 'min:1', Rule::exists('slider_slide_actions', 'id')],
            'slides.*.actions.*.sort' => ['sometimes', 'integer', 'min:0'],
            'slides.*.actions.*.activity' => ['sometimes', 'boolean'],
            'slides.*.actions.*.is_primary' => ['sometimes', 'boolean'],
            'slides.*.actions.*.action_type' => ['required', Rule::in(['url','route','form','anchor'])],
            'slides.*.actions.*.action_value' => ['required', 'string', 'max:2048'],
            'slides.*.actions.*.route_params' => ['nullable', 'array'],
            'slides.*.actions.*.target' => ['sometimes', Rule::in(['_self','_blank'])],
            'slides.*.actions.*.style' => ['nullable', Rule::in(['primary','secondary','outline','ghost','link','danger','success','warning'])],
            'slides.*.actions.*.icon' => ['nullable', 'string', 'max:100'],
            'slides.*.actions.*.icon_position' => ['sometimes', Rule::in(['left','right'])],
            'slides.*.actions.*.css_class' => ['nullable', 'string', 'max:255'],
            'slides.*.actions.*.settings' => ['nullable', 'array'],
            'slides.*.actions.*.translations' => ['sometimes', 'array'],
            'slides.*.actions.*.translations.*' => ['required', 'array:label,title,aria_label'],
            'slides.*.actions.*.translations.*.label' => ['nullable', 'string', 'max:255'],
            'slides.*.actions.*.translations.*.title' => ['nullable', 'string', 'max:255'],
            'slides.*.actions.*.translations.*.aria_label' => ['nullable', 'string', 'max:255'],
            'slides.*.advantages' => ['sometimes', 'array', 'max:50'],
            'slides.*.advantages.*' => ['required', 'array:id,sort,activity,icon_type,icon,icon_color,style,settings,translations'],
            'slides.*.advantages.*.id' => ['sometimes', 'nullable', 'integer', 'min:1', Rule::exists('slider_slide_advantages', 'id')],
            'slides.*.advantages.*.sort' => ['sometimes', 'integer', 'min:0'],
            'slides.*.advantages.*.activity' => ['sometimes', 'boolean'],
            'slides.*.advantages.*.icon_type' => ['required', Rule::in(['lucide','heroicon','class','none'])],
            'slides.*.advantages.*.icon' => ['nullable', 'string', 'max:100'],
            'slides.*.advantages.*.icon_color' => ['nullable', 'string', 'max:30'],
            'slides.*.advantages.*.style' => ['nullable', Rule::in(['default','card','compact','inline','minimal','outlined'])],
            'slides.*.advantages.*.settings' => ['nullable', 'array'],
            'slides.*.advantages.*.translations' => ['sometimes', 'array'],
            'slides.*.advantages.*.translations.*' => ['required', 'array:title,text'],
            'slides.*.advantages.*.translations.*.title' => ['nullable', 'string', 'max:255'],
            'slides.*.advantages.*.translations.*.text' => ['nullable', 'string'],
        ];
        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);
        $validator->after(function (Validator $validator): void {
            $slides = $this->input('slides');
            if (! is_array($slides)) return;
            $locales = $this->availableLocales();
            $checkTranslations = function ($translations, string $path) use ($validator, $locales): void {
                if (! is_array($translations)) return;
                foreach (array_keys($translations) as $locale) {
                    if (! is_string($locale) || ! in_array($locale, $locales, true)) {
                        $validator->errors()->add("$path.$locale", 'Язык отсутствует в конфигурации приложения.');
                    }
                }
            };
            $uniqueIds = function ($items, string $path) use ($validator): void {
                if (! is_array($items)) return;
                $seen = [];
                foreach ($items as $index => $item) {
                    if (! is_array($item) || empty($item['id'])) continue;
                    $id = (string) $item['id'];
                    if (isset($seen[$id])) $validator->errors()->add("$path.$index.id", 'Повторяющийся ID.');
                    $seen[$id] = true;
                }
            };
            $uniqueIds($slides, 'slides');
            foreach ($slides as $i => $slide) {
                if (! is_array($slide)) continue;
                if (isset($slide['show_from_at'], $slide['show_to_at']) && strtotime((string)$slide['show_to_at']) < strtotime((string)$slide['show_from_at'])) {
                    $validator->errors()->add("slides.$i.show_to_at", 'Дата окончания раньше даты начала.');
                }
                $checkTranslations($slide['translations'] ?? null, "slides.$i.translations");
                foreach (['actions','advantages','images'] as $relation) {
                    $items = $slide[$relation] ?? null;
                    $uniqueIds($items, "slides.$i.$relation");
                    if (! is_array($items)) continue;
                    foreach ($items as $j => $item) {
                        if (! is_array($item)) {
                            continue;
                        }

                        // Проверяем, что изображение имеет либо ID, либо новый файл.
                        if ($relation === 'images') {
                            $hasId = !empty($item['id']);

                            $hasFile = $this->hasFile(
                                "slides.$i.images.$j.file"
                            );

                            if ($hasId === $hasFile) {
                                $validator->errors()->add(
                                    "slides.$i.images.$j",
                                    'Укажите либо существующее изображение, либо новый файл.'
                                );
                            }
                        }

                        // Частично заполненный перевод кнопки обязан иметь label.
                        // Полностью пустые переводы разрешены: сервис их пропустит.
                        if ($relation === 'actions' && is_array($item['translations'] ?? null)) {
                            foreach ($item['translations'] as $code => $translation) {
                                if (! is_array($translation)) continue;
                                $hasContent = collect(['label', 'title', 'aria_label'])
                                    ->contains(fn ($field) => filled($translation[$field] ?? null));
                                if ($hasContent && blank($translation['label'] ?? null)) {
                                    $validator->errors()->add(
                                        "slides.$i.actions.$j.translations.$code.label",
                                        "Укажите название кнопки для языка [$code]."
                                    );
                                }
                            }
                        }

                        // Проверка переводов вложенных элементов.
                        $checkTranslations(
                            $item['translations'] ?? null,
                            "slides.$i.$relation.$j.translations"
                        );

                        // Проверка обязательной иконки преимущества.
                        if (
                            $relation === 'advantages'
                            && in_array(
                                $item['icon_type'] ?? null,
                                ['lucide', 'heroicon', 'class'],
                                true
                            )
                            && blank($item['icon'] ?? null)
                        ) {
                            $validator->errors()->add(
                                "slides.$i.advantages.$j.icon",
                                'Для выбранного типа требуется иконка.'
                            );
                        }
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return parent::messages() + [
                'slides.array' => 'Слайды должны быть массивом.',
                'slides.*.images.*.id.required_without' => 'Укажите ID существующего изображения либо загрузите файл.',
                'slides.*.actions.*.action_value.required' => 'Укажите адрес или значение действия.',
            ];
    }
}
