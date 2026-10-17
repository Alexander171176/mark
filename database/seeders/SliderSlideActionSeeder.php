<?php

namespace Database\Seeders;

use App\Models\Admin\Slider\SliderSlideAction\SliderSlideAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Демонстрационные независимые кнопки для слайдов интернет-магазина.
 *
 * Кнопки не привязываются к слайдам: связи создаются отдельно в CRUD.
 * Повторный запуск не перезаписывает изменения пользователя.
 */
class SliderSlideActionSeeder extends Seeder
{
    public function run(): void
    {
        $created = 0;
        $existing = 0;

        foreach ($this->actions() as $data) {
            DB::transaction(function () use ($data, &$created, &$existing): void {
                // В таблице нет уникального кода кнопки. Для повторного запуска
                // ищем её по типу, значению действия и исходному русскому тексту.
                $action = SliderSlideAction::query()
                    ->where('action_type', $data['action_type'])
                    ->where('action_value', $data['action_value'])
                    ->whereHas('translations', function ($query) use ($data): void {
                        $query->where('locale', 'ru')
                            ->where('label', $data['translations']['ru']['label']);
                    })
                    ->first();

                if ($action !== null) {
                    $existing++;
                    return;
                }

                $translations = $data['translations'];
                unset($data['translations']);

                $action = SliderSlideAction::create($data);

                foreach ($translations as $locale => $fields) {
                    $action->translations()->create([
                        'locale' => $locale,
                        'label' => $fields['label'],
                        'title' => $fields['title'],
                        'aria_label' => $fields['aria_label'],
                    ]);
                }

                $created++;
            });
        }

        $this->command?->info("Кнопки слайдов: создано {$created}, уже существовали {$existing}.");
    }

    /**
     * Семь форм приложения, ссылки на каталог и варианты кнопок из старого SliderSeeder.
     *
     * Формы используют action_type=form и код формы в action_value.
     * Каталог использует action_type=route и существующее имя маршрута.
     * Назначение is_primary и сортировка находятся в pivot, не в этой таблице.
     */
    private function actions(): array
    {
        return [
            // 1–7. Универсальные формы, открываемые в интерфейсе приложения.
            $this->form('consultation', 'Получить консультацию', 'Кеңес алу', 'Get a consultation', 'messages-square', 'primary'),
            $this->form('selection', 'Подобрать оборудование', 'Жабдық таңдау', 'Select equipment', 'sliders-horizontal', 'primary'),
            $this->form('specification', 'Отправить спецификацию', 'Спецификацияны жіберу', 'Send a specification', 'file-up', 'secondary'),
            $this->form('callback', 'Заказать звонок', 'Қоңырауға тапсырыс беру', 'Request a callback', 'phone-call', 'outline'),
            $this->form('supplier', 'Стать поставщиком', 'Жеткізуші болу', 'Become a supplier', 'handshake', 'primary'),
            $this->form('school', 'Записаться на обучение', 'Оқуға жазылу', 'Enroll in a course', 'graduation-cap', 'primary'),
            $this->form('contact', 'Связаться с нами', 'Бізбен байланысу', 'Contact us', 'mail', 'outline'),

            // 8–10. Существующие формулировки перехода в каталог из старого сидера.
            $this->catalog('Каталог оборудования', 'Жабдықтар каталогы', 'Equipment catalog', 'layout-grid', 'primary'),
            $this->catalog('Смотреть оборудование', 'Жабдықтарды көру', 'Browse equipment', 'search', 'primary'),
            $this->catalog('Перейти в каталог', 'Каталогқа өту', 'Go to catalog', 'arrow-right', 'primary'),

            // 11–12. Дополнительные формулировки из старого сидера.
            $this->form('selection', 'Подобрать систему', 'Жүйені таңдау', 'Select a system', 'settings-2', 'secondary'),
            $this->form('specification', 'Загрузить спецификацию', 'Спецификацияны жүктеу', 'Upload a specification', 'upload', 'outline'),

            // 13–18. Универсальные маркетинговые действия для тематических слайдов.
            $this->catalog('Посмотреть новинки', 'Жаңа тауарларды көру', 'Explore new arrivals', 'sparkles', 'secondary'),
            $this->catalog('Выбрать товар', 'Тауар таңдау', 'Choose a product', 'shopping-bag', 'primary'),
            $this->catalog('Открыть ассортимент', 'Тауар түрлерін көру', 'Explore our range', 'package-search', 'outline'),
            $this->form('consultation', 'Задать вопрос', 'Сұрақ қою', 'Ask a question', 'message-circle-question', 'secondary'),
            $this->form('callback', 'Перезвоните мне', 'Маған қайта қоңырау шалыңыз', 'Call me back', 'phone-incoming', 'secondary'),
            $this->form('contact', 'Написать нам', 'Бізге жазу', 'Write to us', 'send', 'secondary'),
        ];
    }

    private function form(
        string $code,
        string $ru,
        string $kk,
        string $en,
        string $icon,
        string $style
    ): array {
        return $this->makeAction('form', $code, $ru, $kk, $en, $icon, $style);
    }

    private function catalog(
        string $ru,
        string $kk,
        string $en,
        string $icon,
        string $style
    ): array {
        return $this->makeAction('route', 'public.marketProducts.index', $ru, $kk, $en, $icon, $style);
    }

    private function makeAction(
        string $type,
        string $value,
        string $ru,
        string $kk,
        string $en,
        string $icon,
        string $style
    ): array {
        return [
            'activity' => true,
            'action_type' => $type,
            'action_value' => $value,
            'route_params' => null,
            'target' => '_self',
            'style' => $style,
            'icon' => $icon,
            'icon_position' => 'left',
            'css_class' => null,
            'settings' => null,
            'translations' => [
                'ru' => $this->translation($ru),
                'kk' => $this->translation($kk),
                'en' => $this->translation($en),
            ],
        ];
    }

    private function translation(string $label): array
    {
        return [
            'label' => $label,
            'title' => $label,
            'aria_label' => $label,
        ];
    }
}
