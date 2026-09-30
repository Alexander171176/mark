<?php

namespace App\Http\Controllers\Admin\Form\Form;

use App\Http\Controllers\Admin\Form\BaseFormAdminController;
use App\Http\Requests\Admin\Form\Form\FormRequest;
use App\Http\Resources\Admin\Form\Form\FormResource;
use App\Http\Resources\Admin\Form\Form\FormSharedResource;
use App\Models\Admin\Form\Form\Form;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use App\Traits\Admin\Form\HasFormActivityTrait;
use App\Traits\Admin\Form\HasFormSortingTrait;
use App\Traits\Admin\Form\HasFormTranslationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FormController extends BaseFormAdminController
{
    use HasFormTranslationsTrait;
    use HasFormActivityTrait;
    use HasFormSortingTrait;

    /**
     * Основная модель контроллера.
     */
    protected string $modelClass = Form::class;

    /**
     * Название сущности для сообщений.
     */
    protected string $entityLabel = 'форм';

    /**
     * Формы имеют прямого владельца user_id.
     */
    protected bool $ownerScoped = true;

    /**
     * Поля переводов формы.
     */
    protected array $translationFields = [
        'title',
        'subtitle',
        'description',
        'submit_text',
        'success_message',
        'error_message',
    ];

    /**
     * Список форм.
     *
     * Поддерживаемые режимы обработки:
     * - frontend;
     * - server;
     * - auto.
     */
    public function index(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $settings = app(
            AdminSettingsService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Настройки списка
        |--------------------------------------------------------------------------
        */

        $perPage = $settings->int(
            'adminFormsPerPage',
            12
        );

        $defaultSort = $settings->string(
            'adminFormsDefaultSort',
            'sortAsc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );

        $processingMode = $settings->string(
            'adminFormsProcessingMode',
            'auto'
        );

        /*
        |--------------------------------------------------------------------------
        | Определение режима обработки
        |--------------------------------------------------------------------------
        */

        $formsCount = $this->baseQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $formsCount,
            300
        );

        try {
            $forms = $this->getIndexForms(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Form/Forms/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    /*
                    |--------------------------------------------------------------------------
                    | Настройки
                    |--------------------------------------------------------------------------
                    */

                    'adminFormsPerPage' =>
                        $perPage,

                    'adminFormsDefaultSort' =>
                        $defaultSort,

                    'adminFormsProcessingMode' =>
                        $processingMode,

                    /*
                    |--------------------------------------------------------------------------
                    | Данные
                    |--------------------------------------------------------------------------
                    */

                    'forms' => FormSharedResource::collection(
                        $forms
                    ),

                    'formsCount' =>
                        $formsCount,

                    /*
                    |--------------------------------------------------------------------------
                    | Текущее состояние списка
                    |--------------------------------------------------------------------------
                    */

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    /*
                    |--------------------------------------------------------------------------
                    | Справочники
                    |--------------------------------------------------------------------------
                    */

                    'statuses' => config(
                        'forms.statuses',
                        []
                    ),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка форм: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Form/Forms/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminFormsPerPage' =>
                        $perPage,

                    'adminFormsDefaultSort' =>
                        $defaultSort,

                    'adminFormsProcessingMode' =>
                        $processingMode,

                    'forms' => [],
                    'formsCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'statuses' => config(
                        'forms.statuses',
                        []
                    ),

                    'error' =>
                        'Ошибка загрузки форм.',
                ]
            );
        }
    }

    /**
     * Страница создания формы.
     */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/Form/Forms/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'statuses' => config(
                    'forms.statuses',
                    []
                ),

                'fieldTypes' => config(
                    'forms.field_types',
                    []
                ),

                'fieldWidths' => config(
                    'forms.field_widths',
                    []
                ),

                'defaults' => [
                    'status' =>
                        Form::STATUS_DRAFT,

                    'activity' =>
                        true,

                    'sort' =>
                        100,

                    /*
                    |--------------------------------------------------------------------------
                    | Защита от спама
                    |--------------------------------------------------------------------------
                    */

                    'spam_protection' =>
                        true,

                    'honeypot_enabled' =>
                        true,

                    'min_submit_seconds' => (int) config(
                        'forms.spam.min_submit_seconds',
                        3
                    ),

                    'rate_limit' => (int) config(
                        'forms.spam.rate_limit',
                        5
                    ),

                    'rate_limit_minutes' => (int) config(
                        'forms.spam.rate_limit_minutes',
                        10
                    ),

                    'captcha_enabled' =>
                        false,

                    /*
                    |--------------------------------------------------------------------------
                    | Поведение
                    |--------------------------------------------------------------------------
                    */

                    'auth_required' =>
                        false,

                    /*
                    |--------------------------------------------------------------------------
                    | Дополнительные настройки
                    |--------------------------------------------------------------------------
                    */

                    'settings' =>
                        null,
                ],
            ]
        );
    }

    /**
     * Создание формы.
     */
    public function store(
        FormRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Вложенные данные
        |--------------------------------------------------------------------------
        */

        $translations = $data['translations']
            ?? [];

        $fields = $data['fields']
            ?? [];

        unset(
            $data['translations'],
            $data['fields']
        );

        /*
        |--------------------------------------------------------------------------
        | Владелец
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            /*
             * Обычный пользователь не может
             * создать форму от имени другого пользователя.
             */
            $data['user_id'] =
                $user->id;
        }

        try {
            DB::transaction(
                function () use (
                    &$form,
                    $data,
                    $translations,
                    $fields
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | Автоматическая сортировка
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !isset($data['sort'])
                        || is_null($data['sort'])
                    ) {
                        $maxSort = Form::query()
                            ->max(
                                'forms.sort'
                            );

                        $data['sort'] = is_null(
                            $maxSort
                        )
                            ? 0
                            : $maxSort + 1;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Форма
                    |--------------------------------------------------------------------------
                    */

                    $form = Form::create(
                        $data
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Переводы формы
                    |--------------------------------------------------------------------------
                    */

                    $this->syncTranslations(
                        $form,
                        $translations
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Поля формы
                    |--------------------------------------------------------------------------
                    */

                    $this->createFormFields(
                        $form,
                        $fields
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.forms.index',
                    [
                        'form' => $form->id,
                    ]
                )
                ->with(
                    'success',
                    'Форма успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании формы: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании формы.'
                );
        }
    }

    /**
     * Просмотр формы.
     */
    public function show(
        int $form,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Полный набор данных
        |--------------------------------------------------------------------------
        |
        | Используем baseQuery(), чтобы применялось
        | ограничение владелец / администратор.
        |
        | Show/Edit используют полный Resource,
        | поэтому загружаются все переводы формы,
        | поля формы и варианты полей.
        |
        */

        $form = $this->baseQuery()
            ->with([
                'translations',

                'user:id,name,email,profile_photo_path',

                'fields.translations',

                'fields.options.translations',
            ])
            ->withCount([
                'fields',
                'submissions',
            ])
            ->findOrFail(
                $form
            );

        return Inertia::render(
            'Admin/Form/Form/Show',
            [
                'form' => new FormResource(
                    $form
                ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'statuses' => config(
                    'forms.statuses',
                    []
                ),

                'fieldTypes' => config(
                    'forms.field_types',
                    []
                ),

                'fieldWidths' => config(
                    'forms.field_widths',
                    []
                ),
            ]
        );
    }

    /**
     * Страница редактирования формы.
     */
    public function edit(
        int $form,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Полный набор данных конструктора
        |--------------------------------------------------------------------------
        */

        $form = $this->baseQuery()
            ->with([
                'translations',

                'user:id,name,email,profile_photo_path',

                'fields.translations',

                'fields.options.translations',
            ])
            ->withCount([
                'fields',
                'submissions',
            ])
            ->findOrFail(
                $form
            );

        return Inertia::render(
            'Admin/Form/Forms/Edit',
            [
                'form' => new FormResource(
                    $form
                ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'statuses' => config(
                    'forms.statuses',
                    []
                ),

                'fieldTypes' => config(
                    'forms.field_types',
                    []
                ),

                'fieldWidths' => config(
                    'forms.field_widths',
                    []
                ),
            ]
        );
    }

    /**
     * Обновление формы.
     */
    public function update(
        FormRequest $request,
        int $form
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Проверка доступности формы
        |--------------------------------------------------------------------------
        */

        $form = $this->baseQuery()
            ->findOrFail(
                $form
            );

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Вложенные данные
        |--------------------------------------------------------------------------
        */

        $translations = $data['translations']
            ?? [];

        $fields = $data['fields']
            ?? [];

        unset(
            $data['translations'],
            $data['fields'],
            $data['_method']
        );

        /*
        |--------------------------------------------------------------------------
        | Владелец
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            /*
             * Обычный пользователь не может
             * передать форму другому владельцу.
             */
            $data['user_id'] =
                $user->id;
        }

        try {
            DB::transaction(
                function () use (
                    $form,
                    $data,
                    $translations,
                    $fields
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | Форма
                    |--------------------------------------------------------------------------
                    */

                    $form->update(
                        $data
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Переводы формы
                    |--------------------------------------------------------------------------
                    */

                    $this->syncTranslations(
                        $form,
                        $translations
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Поля формы
                    |--------------------------------------------------------------------------
                    */

                    $this->syncFormFields(
                        $form,
                        $fields
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.forms.index',
                    [
                        'form' => $form->id,
                    ]
                )
                ->with(
                    'success',
                    'Форма успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении формы ID '
                . $form->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении формы.'
                );
        }
    }

    /**
     * Удаление формы.
     */
    public function destroy(
        int $form
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Проверка доступности формы
        |--------------------------------------------------------------------------
        */

        $form = $this->baseQuery()
            ->findOrFail(
                $form
            );

        /*
        |--------------------------------------------------------------------------
        | Форму с заявками физически не удаляем
        |--------------------------------------------------------------------------
        |
        | form_submissions.form_id использует restrictOnDelete().
        |
        | Помимо ограничения базы данных это является
        | бизнес-правилом: форма является частью истории
        | уже полученных заявок.
        |
        */

        if (
            $form->submissions()
                ->exists()
        ) {
            return back()->with(
                'error',
                'Нельзя удалить форму, по которой уже существуют заявки. Переведите её в архив.'
            );
        }

        try {
            DB::transaction(
                function () use ($form) {
                    /*
                    |--------------------------------------------------------------------------
                    | Удаление формы
                    |--------------------------------------------------------------------------
                    |
                    | Переводы, поля и зависимые данные удаляются
                    | согласно cascade-ограничениям миграций.
                    |
                    */

                    $form->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.forms.index'
                )
                ->with(
                    'success',
                    'Форма успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении формы ID '
                . $form->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении формы.'
            );
        }
    }

    /**
     * Массовое удаление форм.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:forms,id',
            ],
        ]);

        $ids = $validated['ids'];

        /*
        |--------------------------------------------------------------------------
        | Проверяем доступность всех выбранных форм
        |--------------------------------------------------------------------------
        */

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'forms.id',
                $ids
            )
            ->pluck(
                'forms.id'
            )
            ->toArray();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            return back()->with(
                'error',
                'Часть форм недоступна для удаления.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Проверяем наличие заявок
        |--------------------------------------------------------------------------
        |
        | Если хотя бы по одной выбранной форме существуют
        | заявки, всю операцию отменяем.
        |
        | Это сохраняет единое правило с destroy().
        |
        */

        $formsWithSubmissions = $this->baseQuery()
            ->whereIn(
                'forms.id',
                $allowedIds
            )
            ->whereHas(
                'submissions'
            )
            ->exists();

        if ($formsWithSubmissions) {
            return back()->with(
                'error',
                'Среди выбранных форм есть формы с заявками. Такие формы нельзя удалить — переведите их в архив.'
            );
        }

        try {
            DB::transaction(
                function () use (
                    $allowedIds
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | Удаление форм
                    |--------------------------------------------------------------------------
                    |
                    | Переводы, поля и остальные зависимые записи,
                    | для которых настроен cascadeOnDelete(),
                    | будут удалены базой данных.
                    |
                    */

                    Form::query()
                        ->whereIn(
                            'forms.id',
                            $allowedIds
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные формы успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy forms: '
                . $e->getMessage(),
                [
                    'ids' =>
                        $allowedIds,

                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении форм.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Form fields helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Создание вложенных полей новой формы.
     */
    private function createFormFields(
        Form $form,
        array $fields
    ): void {
        foreach ($fields as $fieldData) {
            if (!is_array($fieldData)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Вложенные данные поля
            |--------------------------------------------------------------------------
            */

            $translations = $fieldData['translations']
                ?? [];

            unset(
                $fieldData['id'],
                $fieldData['_delete'],
                $fieldData['translations']
            );

            /*
            |--------------------------------------------------------------------------
            | Поле
            |--------------------------------------------------------------------------
            |
            | form_id вручную не передаём.
            | Связь устанавливается через hasMany relation.
            |
            */

            $field = $form
                ->fields()
                ->create(
                    $fieldData
                );

            /*
            |--------------------------------------------------------------------------
            | Переводы поля
            |--------------------------------------------------------------------------
            */

            $this->syncTranslations(
                $field,
                $translations,
                [
                    'label',
                    'placeholder',
                    'description',
                ]
            );
        }
    }

    /**
     * Синхронизация вложенных полей формы.
     *
     * Порядок операций:
     * 1. DELETE;
     * 2. UPDATE;
     * 3. CREATE.
     *
     * Отсутствие существующего поля в массиве
     * не считается командой удаления.
     */
    private function syncFormFields(
        Form $form,
        array $fields
    ): void {
        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        foreach ($fields as $fieldData) {
            if (
                !is_array($fieldData)
                || empty($fieldData['_delete'])
            ) {
                continue;
            }

            $fieldId = isset($fieldData['id'])
            && is_numeric($fieldData['id'])
                ? (int) $fieldData['id']
                : null;

            if ($fieldId === null) {
                continue;
            }

            /*
             * Ищем поле строго внутри текущей формы.
             * Это дополнительная защита persistence-уровня.
             */
            $field = $form
                ->fields()
                ->whereKey($fieldId)
                ->firstOrFail();

            $field->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        foreach ($fields as $fieldData) {
            if (
                !is_array($fieldData)
                || !empty($fieldData['_delete'])
            ) {
                continue;
            }

            $fieldId = isset($fieldData['id'])
            && is_numeric($fieldData['id'])
                ? (int) $fieldData['id']
                : null;

            if ($fieldId === null) {
                continue;
            }

            $translations = $fieldData['translations']
                ?? [];

            unset(
                $fieldData['id'],
                $fieldData['_delete'],
                $fieldData['translations']
            );

            $field = $form
                ->fields()
                ->whereKey($fieldId)
                ->firstOrFail();

            $field->update(
                $fieldData
            );

            $this->syncTranslations(
                $field,
                $translations,
                [
                    'label',
                    'placeholder',
                    'description',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        foreach ($fields as $fieldData) {
            if (
                !is_array($fieldData)
                || !empty($fieldData['_delete'])
            ) {
                continue;
            }

            $fieldId = isset($fieldData['id'])
            && is_numeric($fieldData['id'])
                ? (int) $fieldData['id']
                : null;

            if ($fieldId !== null) {
                continue;
            }

            $translations = $fieldData['translations']
                ?? [];

            unset(
                $fieldData['id'],
                $fieldData['_delete'],
                $fieldData['translations']
            );

            $field = $form
                ->fields()
                ->create(
                    $fieldData
                );

            $this->syncTranslations(
                $field,
                $translations,
                [
                    'label',
                    'placeholder',
                    'description',
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Index helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Базовый запрос Admin Index.
     *
     * Важно:
     * - не загружаем все переводы;
     * - загружаем current + fallback;
     * - FormSharedResource работает только
     *   с уже загруженными relations;
     * - дополнительных SQL-запросов из Resource нет;
     * - владелец загружается только с нужными полями;
     * - counts загружаются заранее.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        $locales = $this->resourceLocales(
            $locale
        );

        return $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->whereIn(
                        'locale',
                        $locales
                    ),

                'user:id,name,email,profile_photo_path',
            ])
            ->withCount([
                'fields',
                'submissions',
            ]);
    }

    /**
     * Получение списка форм для Index.
     *
     * Server:
     * - поиск выполняется в БД;
     * - сортировка выполняется в БД;
     * - используется серверная пагинация.
     *
     * Frontend:
     * - сервер отдаёт весь список;
     * - поиск, сортировка, фильтрация
     *   и пагинация выполняются Vue.
     */
    private function getIndexForms(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery(
            $locale
        );

        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        return $query
            ->ordered()
            ->get();
    }
}
