<?php

namespace App\Http\Controllers\Admin\Form\FormField;

use App\Http\Controllers\Admin\Form\BaseFormAdminController;
use App\Http\Requests\Admin\Form\FormField\FormFieldRequest;
use App\Http\Resources\Admin\Form\Form\FormSharedResource;
use App\Http\Resources\Admin\Form\FormField\FormFieldResource;
use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormField\FormField;
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

class FormFieldController extends BaseFormAdminController
{
    use HasFormTranslationsTrait;
    use HasFormActivityTrait;
    use HasFormSortingTrait;
    /**
     * Основная модель контроллера.
     */
    protected string $modelClass = FormField::class;

    /**
     * Название сущности для сообщений.
     */
    protected string $entityLabel = 'полей формы';

    /**
     * У form_fields нет собственного user_id.
     *
     * Владелец определяется через:
     *
     * form_fields.form_id
     *      -> forms.user_id
     */
    protected bool $ownerScoped = false;

    /**
     * Поля переводов поля формы.
     */
    protected array $translationFields = [
        'label',
        'placeholder',
        'description',
    ];

    /**
     * Список полей форм.
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
            'adminFormFieldsPerPage',
            12
        );

        $defaultSort = $settings->string(
            'adminFormFieldsDefaultSort',
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
            'adminFormFieldsProcessingMode',
            'auto'
        );

        /*
        |--------------------------------------------------------------------------
        | Определение режима обработки
        |--------------------------------------------------------------------------
        */

        $fieldsCount = $this->baseQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $fieldsCount,
            300
        );

        try {
            $fields = $this->getIndexFields(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Form/FormField/Index',
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

                    'adminFormFieldsPerPage' =>
                        $perPage,

                    'adminFormFieldsDefaultSort' =>
                        $defaultSort,

                    'adminFormFieldsProcessingMode' =>
                        $processingMode,

                    /*
                    |--------------------------------------------------------------------------
                    | Данные
                    |--------------------------------------------------------------------------
                    */

                    'fields' => FormFieldSharedResource::collection(
                        $fields
                    ),

                    'fieldsCount' =>
                        $fieldsCount,

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

                    'fieldTypes' => config(
                        'forms.field_types',
                        []
                    ),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка полей форм: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Form/FormField/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminFormFieldsPerPage' =>
                        $perPage,

                    'adminFormFieldsDefaultSort' =>
                        $defaultSort,

                    'adminFormFieldsProcessingMode' =>
                        $processingMode,

                    'fields' => [],
                    'fieldsCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'fieldTypes' => config(
                        'forms.field_types',
                        []
                    ),

                    'error' =>
                        'Ошибка загрузки полей форм.',
                ]
            );
        }
    }

    /**
     * Страница создания поля формы.
     */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $form = null;

        /*
        |--------------------------------------------------------------------------
        | Предварительно выбранная форма
        |--------------------------------------------------------------------------
        |
        | Позволяет открыть создание поля:
        |
        | /admin/form-fields/create?form_id=5
        |
        */

        if ($request->filled('form_id')) {
            $form = $this->formQuery(
                $currentLocale
            )
                ->withCount([
                    'fields',
                    'submissions',
                ])
                ->findOrFail(
                    (int) $request->query(
                        'form_id'
                    )
                );
        }

        return Inertia::render(
            'Admin/Form/FormField/Create',
            [
                'form' => $form
                    ? new FormSharedResource(
                        $form
                    )
                    : null,

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'fieldTypes' => config(
                    'forms.field_types',
                    []
                ),

                'fieldWidths' => config(
                    'forms.field_widths',
                    []
                ),

                'validationRules' => config(
                    'forms.validation_rules',
                    []
                ),

                'fileSettings' => config(
                    'forms.files',
                    []
                ),

                'defaults' => [
                    'form_id' =>
                        $form?->id,

                    'type' =>
                        'text',

                    'activity' =>
                        true,

                    'required' =>
                        false,

                    'readonly' =>
                        false,

                    'disabled' =>
                        false,

                    'sort' =>
                        100,

                    'default_value' =>
                        null,

                    'validation' =>
                        null,

                    'width' =>
                        'full',

                    'settings' =>
                        null,
                ],
            ]
        );
    }

    /**
     * Создание поля формы.
     */
    public function store(
        FormFieldRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Проверяем доступ к родительской форме
        |--------------------------------------------------------------------------
        |
        | Нельзя создать поле в форме другого пользователя,
        | даже если form_id прошёл обычную exists-валидацию.
        |
        */

        $this->accessibleFormsQuery()
            ->findOrFail(
                (int) $validated['form_id']
            );

        try {
            $field = DB::transaction(
                function () use ($validated) {
                    /*
                    |--------------------------------------------------------------------------
                    | Поле
                    |--------------------------------------------------------------------------
                    */

                    $field = FormField::query()
                        ->create(
                            $this->fieldData(
                                $validated
                            )
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | Переводы
                    |--------------------------------------------------------------------------
                    */

                    $this->syncTranslations(
                        $field,
                        $validated['translations']
                    );

                    return $field;
                }
            );

            return redirect()
                ->route(
                    'admin.formFields.edit',
                    [
                        'formField' =>
                            $field->id,
                    ]
                )
                ->with(
                    'success',
                    'Поле формы успешно создано.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании поля формы: '
                . $e->getMessage(),
                [
                    'form_id' =>
                        $validated['form_id'] ?? null,

                    'exception' =>
                        $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании поля формы.'
                );
        }
    }

    /**
     * Просмотр поля формы.
     */
    public function show(
        int $formField,
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
        | Доступ к полю проверяется через baseQuery(),
        | который учитывает владельца родительской формы.
        |
        */

        $formField = $this->fullFieldQuery()
            ->findOrFail(
                $formField
            );

        return Inertia::render(
            'Admin/Form/FormField/Show',
            [
                'field' => new FormFieldResource(
                    $formField
                ),

                'form' => $formField->form
                    ? new FormSharedResource(
                        $formField->form
                    )
                    : null,

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'fieldTypes' => config(
                    'forms.field_types',
                    []
                ),

                'fieldWidths' => config(
                    'forms.field_widths',
                    []
                ),

                'validationRules' => config(
                    'forms.validation_rules',
                    []
                ),

                'fileSettings' => config(
                    'forms.files',
                    []
                ),
            ]
        );
    }

    /**
     * Страница редактирования поля формы.
     */
    public function edit(
        int $formField,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $formField = $this->fullFieldQuery()
            ->findOrFail(
                $formField
            );

        return Inertia::render(
            'Admin/Form/FormField/Edit',
            [
                'field' => new FormFieldResource(
                    $formField
                ),

                'form' => $formField->form
                    ? new FormSharedResource(
                        $formField->form
                    )
                    : null,

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'fieldTypes' => config(
                    'forms.field_types',
                    []
                ),

                'fieldWidths' => config(
                    'forms.field_widths',
                    []
                ),

                'validationRules' => config(
                    'forms.validation_rules',
                    []
                ),

                'fileSettings' => config(
                    'forms.files',
                    []
                ),
            ]
        );
    }

    /**
     * Обновление поля формы.
     */
    public function update(
        FormFieldRequest $request,
        int $formField
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Проверяем доступ к текущему полю
        |--------------------------------------------------------------------------
        */

        $formField = $this->baseQuery()
            ->findOrFail(
                $formField
            );

        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Проверяем доступ к новой родительской форме
        |--------------------------------------------------------------------------
        |
        | form_id может быть изменён при редактировании.
        | Поэтому проверяем не только текущего владельца
        | поля, но и форму, в которую поле переносится.
        |
        */

        $this->accessibleFormsQuery()
            ->findOrFail(
                (int) $validated['form_id']
            );

        try {
            DB::transaction(
                function () use (
                    $formField,
                    $validated
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | Поле
                    |--------------------------------------------------------------------------
                    */

                    $formField->update(
                        $this->fieldData(
                            $validated
                        )
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Переводы
                    |--------------------------------------------------------------------------
                    */

                    $this->syncTranslations(
                        $formField,
                        $validated['translations']
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.formFields.edit',
                    [
                        'formField' =>
                            $formField->id,
                    ]
                )
                ->with(
                    'success',
                    'Поле формы успешно обновлено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении поля формы ID '
                . $formField->id
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
                    'Ошибка при обновлении поля формы.'
                );
        }
    }

    /**
     * Удаление поля формы.
     */
    public function destroy(
        int $formField
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Проверяем доступ к полю
        |--------------------------------------------------------------------------
        */

        $formField = $this->baseQuery()
            ->findOrFail(
                $formField
            );

        $formId = (int) $formField->form_id;

        try {
            DB::transaction(
                function () use ($formField) {
                    /*
                    |--------------------------------------------------------------------------
                    | Удаление поля
                    |--------------------------------------------------------------------------
                    |
                    | Исторические значения и файлы заявок
                    | сохраняются.
                    |
                    | Их field_id становится NULL согласно
                    | внешним ключам БД, а snapshot-поля
                    | field_name / field_type / field_label
                    | продолжают хранить исторические данные.
                    |
                    */

                    $formField->delete();
                }
            );

            return redirect()
                ->route(
                    'admin.formFields.index',
                    [
                        'form_id' =>
                            $formId,
                    ]
                )
                ->with(
                    'success',
                    'Поле формы успешно удалено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении поля формы ID '
                . $formField->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении поля формы.'
            );
        }
    }

    /**
     * Массовое удаление полей форм.
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
                'exists:form_fields,id',
            ],
        ]);

        $ids = $validated['ids'];

        /*
        |--------------------------------------------------------------------------
        | Проверяем доступность всех выбранных полей
        |--------------------------------------------------------------------------
        */

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'form_fields.id',
                $ids
            )
            ->pluck(
                'form_fields.id'
            )
            ->toArray();

        if (
            count($allowedIds)
            !== count($ids)
        ) {
            return back()->with(
                'error',
                'Часть полей формы недоступна для удаления.'
            );
        }

        try {
            DB::transaction(
                function () use ($allowedIds) {
                    /*
                    |--------------------------------------------------------------------------
                    | Массовое удаление
                    |--------------------------------------------------------------------------
                    |
                    | В отличие от Form здесь нет запрета
                    | на удаление при наличии исторических
                    | submission values/files.
                    |
                    | Их field_id становится NULL,
                    | snapshot-данные сохраняются.
                    |
                    */

                    FormField::query()
                        ->whereIn(
                            'form_fields.id',
                            $allowedIds
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные поля формы успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy form fields: '
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
                'Ошибка при массовом удалении полей формы.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Base queries
    |--------------------------------------------------------------------------
    */

    /**
     * Базовый запрос полей форм.
     *
     * FormField не имеет собственного user_id,
     * поэтому доступ обычного пользователя
     * ограничивается владельцем родительской формы.
     *
     * Администратор видит все поля.
     */
    protected function baseQuery(): Builder
    {
        $query = FormField::query();

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            $query->whereHas(
                'form',
                function (Builder $formQuery) use ($user) {
                    $formQuery->where(
                        'forms.user_id',
                        $user->id
                    );
                }
            );
        }

        return $query;
    }

    /**
     * Формы, доступные текущему пользователю.
     *
     * Используется при:
     * - Create;
     * - Store;
     * - Update;
     * - смене form_id.
     */
    private function accessibleFormsQuery(): Builder
    {
        $query = Form::query();

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            $query->where(
                'forms.user_id',
                $user->id
            );
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Index helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Базовый запрос Admin Index.
     *
     * Загружаются:
     * - current + fallback переводы поля;
     * - родительская форма;
     * - current + fallback переводы формы;
     * - владелец формы;
     * - количество вариантов поля.
     *
     * Resource не должен выполнять
     * дополнительные SQL-запросы.
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

                'form' => function ($query) use ($locales) {
                    $query->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn(
                            'locale',
                            $locales
                        ),

                        'user:id,name,email,profile_photo_path',
                    ]);
                },
            ])
            ->withCount([
                'options',
            ]);
    }

    /**
     * Получение списка полей для Index.
     *
     * Server:
     * - поиск выполняется в БД;
     * - сортировка / фильтрация выполняется в БД;
     * - используется серверная пагинация.
     *
     * Frontend:
     * - сервер отдаёт весь список;
     * - поиск, сортировка, фильтрация
     *   и пагинация выполняются Vue.
     */
    private function getIndexFields(
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

    /*
    |--------------------------------------------------------------------------
    | Full resource helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Полный запрос поля формы
     * для Show / Edit.
     */
    private function fullFieldQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                /*
                |--------------------------------------------------------------------------
                | Все переводы поля
                |--------------------------------------------------------------------------
                */

                'translations',

                /*
                |--------------------------------------------------------------------------
                | Родительская форма
                |--------------------------------------------------------------------------
                |
                | FormSharedResource использует уже
                | загруженные translations и user.
                |
                */

                'form.translations',

                'form.user:id,name,email,profile_photo_path',

                /*
                |--------------------------------------------------------------------------
                | Варианты выбора
                |--------------------------------------------------------------------------
                |
                | Полный FormFieldResource отдаёт
                | FormFieldOptionResource.
                |
                */

                'options.translations',
            ])
            ->withCount([
                'options',
                'submissionValues',
                'submissionFiles',
            ]);
    }

    /**
     * Подготовить родительскую форму
     * для FormSharedResource.
     *
     * Используется на Create.
     */
    private function formQuery(
        string $locale
    ): Builder {
        $locales = $this->resourceLocales(
            $locale
        );

        return $this->accessibleFormsQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->whereIn(
                        'locale',
                        $locales
                    ),

                'user:id,name,email,profile_photo_path',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Data helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Подготовить основные данные поля
     * без массива переводов.
     */
    protected function fieldData(
        array $validated
    ): array {
        return [
            'form_id' =>
                $validated['form_id'],

            'name' =>
                $validated['name'],

            'type' =>
                $validated['type'],

            /*
            |--------------------------------------------------------------------------
            | Состояние
            |--------------------------------------------------------------------------
            */

            'activity' =>
                $validated['activity'],

            'required' =>
                $validated['required'],

            'readonly' =>
                $validated['readonly'],

            'disabled' =>
                $validated['disabled'],

            /*
            |--------------------------------------------------------------------------
            | Порядок
            |--------------------------------------------------------------------------
            */

            'sort' =>
                $validated['sort'],

            /*
            |--------------------------------------------------------------------------
            | Значение и валидация
            |--------------------------------------------------------------------------
            */

            'default_value' =>
                $validated['default_value']
                ?? null,

            'validation' =>
                $validated['validation']
                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Отображение
            |--------------------------------------------------------------------------
            */

            'width' =>
                $validated['width'],

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings' =>
                $validated['settings']
                ?? null,
        ];
    }
}
