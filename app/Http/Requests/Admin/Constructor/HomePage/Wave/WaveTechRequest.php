<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Wave;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class WaveTechRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
        ]);

        // Приводим альтернативные имена к единым ключам
        if ($this->hasFile('light') && !$this->hasFile('image_light')) {
            $this->files->set('image_light', $this->file('light'));
        }
        if ($this->hasFile('dark') && !$this->hasFile('image_dark')) {
            $this->files->set('image_dark', $this->file('dark'));
        }
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('tech');

        // Общее правило SVG: допускаем разные MIME, но делаем доп. проверку содержимого
        $svgRule = [
            'nullable',           // ← ключевое: позволяем отсутствовать
            'file',
            'max:4096',           // ~4 МБ
            'mimes:svg',          // по расширению
            'mimetypes:image/svg+xml,application/svg+xml,application/xml,text/xml,text/plain,application/octet-stream',
            $this->svgSafe(),     // безопасная проверка содержимого
        ];

        return [
            'wave_section_id' => $isUpdate
                ? ['sometimes', 'integer', 'exists:wave_sections,id']
                : ['required',  'integer', 'exists:wave_sections,id'],

            'title'       => ['required','string','max:255'],
            'subtitle'    => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'alt'         => ['nullable','string','max:255'],

            'sort'        => ['sometimes','integer','min:0'],
            'activity'    => ['sometimes','boolean'],

            // Файлы (поддерживаем оба набора ключей)
            'image_light' => $svgRule,
            'image_dark'  => $svgRule,
            'light'       => $svgRule,
            'dark'        => $svgRule,
        ];
    }

    /**
     * Доп. валидация содержимого SVG (безопасно для null/не-UploadedFile).
     */
    protected function svgSafe(): Closure
    {
        return function (string $attribute, $value, Closure $fail) {
            // Если файла нет или это не UploadedFile — ничего не проверяем
            if (!$value instanceof UploadedFile) {
                return;
            }

            if (!$value->isValid()) {
                $fail('Файл повреждён или не загружен полностью.');
                return;
            }

            $path = $value->getRealPath();
            if (!$path || !is_file($path)) {
                // На всякий случай молча выходим: другие правила уже проверили «file»
                return;
            }

            // Читаем только «шапку», чтобы не грузить большой файл целиком
            $head = @file_get_contents($path, false, null, 0, 2048);
            if ($head === false) {
                $fail('Не удалось прочитать файл.');
                return;
            }

            $headLower = strtolower($head);

            // Минимальная проверка, что это действительно SVG
            if (!str_contains($headLower, '<svg')) {
                $fail('Файл не похож на корректный SVG.');
                return;
            }

            // Базовая санитация: запретим потенциально опасные конструкции
            if (str_contains($headLower, '<script')
                || str_contains($headLower, '<iframe')
                || str_contains($headLower, 'onload=')
                || str_contains($headLower, 'onerror=')
            ) {
                $fail('SVG не должен содержать скрипты или опасные атрибуты.');
            }
        };
    }

    public function attributes(): array
    {
        return [
            'wave_section_id' => 'секция',
            'title'           => 'заголовок',
            'subtitle'        => 'подзаголовок',
            'description'     => 'описание',
            'alt'             => 'alt-текст',
            'sort'            => 'сортировка',
            'activity'        => 'активность',
            'image_light'     => 'SVG (светлая тема)',
            'image_dark'      => 'SVG (тёмная тема)',
            'light'           => 'SVG (светлая тема)',
            'dark'            => 'SVG (тёмная тема)',
        ];
    }

    public function messages(): array
    {
        return [
            'wave_section_id.required' => 'Не выбрана «:attribute».',
            'wave_section_id.integer'  => 'Поле «:attribute» должно быть числом.',
            'wave_section_id.exists'   => 'Выбранная «:attribute» не найдена.',

            'title.required' => 'Укажите «:attribute».',
            'title.string'   => 'Поле «:attribute» должно быть строкой.',
            'title.max'      => 'В «:attribute» не более :max символов.',

            'subtitle.string' => 'Поле «:attribute» должно быть строкой.',
            'subtitle.max'    => 'В «:attribute» не более :max символов.',

            'description.string' => 'Поле «:attribute» должно быть строкой.',

            'alt.string' => 'Поле «:attribute» должно быть строкой.',
            'alt.max'    => 'В «:attribute» не более :max символов.',

            'sort.integer' => 'Поле «:attribute» должно быть целым числом.',
            'sort.min'     => 'Поле «:attribute» не может быть меньше :min.',

            'activity.boolean' => 'Поле «:attribute» должно быть булевым значением.',

            // SVG-файлы
            'image_light.file'      => 'Загрузите файл в поле «:attribute».',
            'image_light.mimes'     => 'Поле «:attribute» принимает только файлы с расширением .svg.',
            'image_light.mimetypes' => 'Поле «:attribute» должно быть SVG (image/svg+xml).',
            'image_light.max'       => 'Размер «:attribute» не должен превышать :max КБ.',

            'image_dark.file'       => 'Загрузите файл в поле «:attribute».',
            'image_dark.mimes'      => 'Поле «:attribute» принимает только файлы с расширением .svg.',
            'image_dark.mimetypes'  => 'Поле «:attribute» должно быть SVG (image/svg+xml).',
            'image_dark.max'        => 'Размер «:attribute» не должен превышать :max КБ.',

            'light.file'            => 'Загрузите файл в поле «:attribute».',
            'light.mimes'           => 'Поле «:attribute» принимает только файлы с расширением .svg.',
            'light.mimetypes'       => 'Поле «:attribute» должно быть SVG (image/svg+xml).',
            'light.max'             => 'Размер «:attribute» не должен превышать :max КБ.',

            'dark.file'             => 'Загрузите файл в поле «:attribute».',
            'dark.mimes'            => 'Поле «:attribute» принимает только файлы с расширением .svg.',
            'dark.mimetypes'        => 'Поле «:attribute» должно быть SVG (image/svg+xml).',
            'dark.max'              => 'Размер «:attribute» не должен превышать :max КБ.',
        ];
    }
}
