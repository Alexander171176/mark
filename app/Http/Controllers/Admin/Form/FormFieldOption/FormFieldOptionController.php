<?php

namespace App\Http\Controllers\Admin\Form\FormFieldOption;

use App\Http\Controllers\Admin\Form\BaseFormAdminController;
use App\Http\Requests\Admin\Form\FormFieldOption\FormFieldOptionRequest;
use App\Http\Resources\Admin\Form\FormField\FormFieldResource;
use App\Http\Resources\Admin\Form\FormFieldOption\FormFieldOptionResource;
use App\Http\Resources\Admin\Form\FormFieldOption\FormFieldOptionSharedResource;
use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormFieldOption\FormFieldOption;
use App\Traits\Admin\Form\HasFormActivityTrait;
use App\Traits\Admin\Form\HasFormSortingTrait;
use App\Traits\Admin\Form\HasFormTranslationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FormFieldOptionController extends BaseFormAdminController
{

    use HasFormTranslationsTrait;
    use HasFormActivityTrait;
    use HasFormSortingTrait;

    /**
     * Модель, с которой работает базовый
     * административный контроллер форм.
     */
    protected string $modelClass = FormFieldOption::class;

    /**
     * Название сущности для общих
     * административных операций.
     */
    protected string $entityLabel = 'вариант поля';

    /**
     * Вариант поля не имеет собственного user_id.
     *
     * Проверка владельца выполняется через:
     *
     * option
     * → field
     * → form
     * → user_id
     */
    protected bool $ownerScoped = false;

    /**
     * Поля переводов варианта.
     */
    protected array $translationFields = [
        'label',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale(
            $request
        );

        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $sort = (string) $request->input(
            'sort',
            config(
                'settings.adminFormFieldOptionsDefaultSort',
                'sortAsc'
            )
        );

        $perPage = (int) $request->input(
            'per_page',
            config(
                'settings.adminFormFieldOptionsPerPage',
                20
            )
        );

        $perPage = in_array(
            $perPage,
            [10, 20, 50, 100],
            true
        )
            ? $perPage
            : 20;

        $processingMode = (string) config(
            'settings.adminFormFieldOptionsProcessingMode',
            'auto'
        );

        $useServerProcessing = $this->shouldUseServerProcessing(
            $processingMode
        );

        $query = $this->indexQuery(
            $locale
        );

        /*
        |--------------------------------------------------------------------------
        | Структурный фильтр: поле формы
        |--------------------------------------------------------------------------
        */

        if ($request->filled('form_field_id')) {
            $fieldId = (int) $request->input(
                'form_field_id'
            );

            /*
             * Проверяем, что поле доступно
             * текущему пользователю.
             */
            $this->accessibleFieldQuery()
                ->findOrFail(
                    $fieldId
                );

            $query->forField(
                $fieldId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Структурный фильтр: форма
        |--------------------------------------------------------------------------
        |
        | Позволяет получить варианты всех полей
        | конкретной формы.
        |
        */

        if ($request->filled('form_id')) {
            $formId = (int) $request->input(
                'form_id'
            );

            /*
             * Сам scope доступа ниже уже проверяет
             * владельца формы.
             *
             * Дополнительно ограничиваем список
             * конкретной формой.
             */
            $query->whereHas(
                'field',
                fn (Builder $fieldQuery) =>
                $fieldQuery->where(
                    'form_fields.form_id',
                    $formId
                )
            );
        }

        $options = $this->getIndexOptions(
            query: $query,
            useServerProcessing: $useServerProcessing,
            search: $search,
            sort: $sort,
            locale: $locale,
            perPage: $perPage
        );

        return Inertia::render(
            'Admin/Form/FormFieldOption/Index',
            [
                'options' =>
                    FormFieldOptionSharedResource::collection(
                        $options
                    ),

                'optionsCount' => $useServerProcessing
                    ? $options->total()
                    : $options->count(),

                'adminFormFieldOptionsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'adminFormFieldOptionsPerPage' =>
                    $perPage,

                'adminFormFieldOptionsDefaultSort' =>
                    $sort,

                'search' => $search,
                'sortParam' => $sort,

                /*
                 * Структурные фильтры не входят
                 * в единый sort-контракт.
                 */
                'formId' => $request->filled('form_id')
                    ? (int) $request->input('form_id')
                    : null,

                'formFieldId' => $request->filled('form_field_id')
                    ? (int) $request->input('form_field_id')
                    : null,

                'errors' => session(
                    'errors'
                ),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    /**
     * Show the form for creating a new resource.
     */
    public function create(
        Request $request
    ): Response {
        $locale = $this->resolveLocale(
            $request
        );

        $field = null;

        /*
        |--------------------------------------------------------------------------
        | Предварительно выбранное поле
        |--------------------------------------------------------------------------
        |
        | Например:
        |
        | /admin/form-field-options/create?form_field_id=15
        |
        */

        if ($request->filled('form_field_id')) {
            $field = $this->fullFieldQuery()
                ->findOrFail(
                    (int) $request->input(
                        'form_field_id'
                    )
                );
        }

        return Inertia::render(
            'Admin/Form/FormFieldOption/Create',
            [
                'field' => $field
                    ? new FormFieldResource(
                        $field
                    )
                    : null,

                'locales' =>
                    $this->availableLocales(),

                'defaults' => [
                    'form_field_id' =>
                        $field?->id,

                    'value' => null,

                    'activity' => true,
                    'is_default' => false,

                    'sort' => 100,

                    'settings' => null,
                ],
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        FormFieldOptionRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        /*
         * Нельзя создать вариант для поля
         * чужой формы.
         */
        $this->accessibleFieldQuery()
            ->findOrFail(
                (int) $validated['form_field_id']
            );

        $option = DB::transaction(
            function () use ($validated) {
                /*
                |--------------------------------------------------------------------------
                | Default option
                |--------------------------------------------------------------------------
                */

                $this->prepareDefaultOption(
                    (int) $validated['form_field_id'],
                    (bool) $validated['is_default']
                );

                /*
                |--------------------------------------------------------------------------
                | Вариант
                |--------------------------------------------------------------------------
                */

                $option = FormFieldOption::query()
                    ->create(
                        $this->optionData(
                            $validated
                        )
                    );

                /*
                |--------------------------------------------------------------------------
                | Переводы
                |--------------------------------------------------------------------------
                */

                $this->syncTranslations(
                    $option,
                    $validated['translations']
                );

                return $option;
            }
        );

        return redirect()
            ->route(
                'admin.formFieldOptions.edit',
                [
                    'formFieldOption' =>
                        $option->id,
                ]
            )
            ->with(
                'success',
                'Вариант поля успешно создан.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    /**
     * Display the specified resource.
     */
    public function show(
        int $formFieldOption
    ): Response {
        $option = $this->fullOptionQuery()
            ->findOrFail(
                $formFieldOption
            );

        return Inertia::render(
            'Admin/Form/FormFieldOption/Show',
            [
                'option' =>
                    new FormFieldOptionResource(
                        $option
                    ),

                'field' => $option->field
                    ? new FormFieldResource(
                        $option->field
                    )
                    : null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        int $formFieldOption
    ): Response {
        $option = $this->fullOptionQuery()
            ->findOrFail(
                $formFieldOption
            );

        return Inertia::render(
            'Admin/Form/FormFieldOption/Edit',
            [
                'option' =>
                    new FormFieldOptionResource(
                        $option
                    ),

                'field' => $option->field
                    ? new FormFieldResource(
                        $option->field
                    )
                    : null,

                'locales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    /**
     * Update the specified resource in storage.
     */
    public function update(
        FormFieldOptionRequest $request,
        int $formFieldOption
    ): RedirectResponse {
        $option = $this->baseQuery()
            ->findOrFail(
                $formFieldOption
            );

        $validated = $request->validated();

        /*
         * Если вариант переносится в другое поле,
         * новое поле также должно быть доступно
         * текущему пользователю.
         */
        $this->accessibleFieldQuery()
            ->findOrFail(
                (int) $validated['form_field_id']
            );

        DB::transaction(
            function () use (
                $option,
                $validated
            ) {
                /*
                |--------------------------------------------------------------------------
                | Default option
                |--------------------------------------------------------------------------
                */

                $this->prepareDefaultOption(
                    (int) $validated['form_field_id'],
                    (bool) $validated['is_default'],
                    (int) $option->id
                );

                /*
                |--------------------------------------------------------------------------
                | Вариант
                |--------------------------------------------------------------------------
                */

                $option->update(
                    $this->optionData(
                        $validated
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | Переводы
                |--------------------------------------------------------------------------
                */

                $this->syncTranslations(
                    $option,
                    $validated['translations']
                );
            }
        );

        return redirect()
            ->route(
                'admin.formFieldOptions.edit',
                [
                    'formFieldOption' =>
                        $option->id,
                ]
            )
            ->with(
                'success',
                'Вариант поля успешно обновлён.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        int $formFieldOption
    ): RedirectResponse {
        $option = $this->baseQuery()
            ->findOrFail(
                $formFieldOption
            );

        $fieldId = (int) $option
            ->form_field_id;

        DB::transaction(
            function () use ($option) {
                $option->delete();
            }
        );

        return redirect()
            ->route(
                'admin.formFieldOptions.index',
                [
                    'form_field_id' =>
                        $fieldId,
                ]
            )
            ->with(
                'success',
                'Вариант поля успешно удалён.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk destroy
    |--------------------------------------------------------------------------
    */

    /**
     * Массовое удаление вариантов поля.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:form_field_options,id',
            ],
        ]);

        $ids = collect(
            $validated['ids']
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        /*
         * Получаем только доступные текущему
         * пользователю варианты.
         */
        $allowedIds = $this->baseQuery()
            ->whereIn(
                'form_field_options.id',
                $ids
            )
            ->pluck(
                'form_field_options.id'
            )
            ->map(
                fn ($id) => (int) $id
            );

        /*
         * Если хотя бы один ID недоступен,
         * запрещаем всю bulk-операцию.
         */
        if (
            $allowedIds->count()
            !== $ids->count()
        ) {
            abort(403);
        }

        DB::transaction(
            function () use ($allowedIds) {
                FormFieldOption::query()
                    ->whereIn(
                        'id',
                        $allowedIds
                    )
                    ->delete();
            }
        );

        return back()->with(
            'success',
            'Выбранные варианты полей успешно удалены.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
    */

    /**
     * Базовый query с проверкой доступа.
     *
     * FormFieldOption не имеет user_id,
     * поэтому доступ проверяется через:
     *
     * option → field → form → user_id.
     */
    protected function baseQuery(): Builder
    {
        $query = FormFieldOption::query();

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
                'field.form',
                fn (Builder $formQuery) =>
                $formQuery->where(
                    'forms.user_id',
                    $user->id
                )
            );
        }

        return $query;
    }

    /**
     * Query для Admin Index.
     *
     * Загружаются только:
     * - текущая локаль;
     * - fallback локаль.
     *
     * Resource после этого не выполняет SQL.
     */
    protected function indexQuery(
        string $locale
    ): Builder {
        $locales = $this->resourceLocales(
            $locale
        );

        return $this->baseQuery()
            ->with([
                'translations' =>
                    fn (Builder $query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'field' =>
                    function (
                        Builder $fieldQuery
                    ) use ($locales) {
                        $fieldQuery->with([
                            'translations' =>
                                fn (Builder $translationQuery) =>
                                $translationQuery->whereIn(
                                    'locale',
                                    $locales
                                ),

                            'form' =>
                                function (
                                    Builder $formQuery
                                ) use ($locales) {
                                    $formQuery->with([
                                        'translations' =>
                                            fn (
                                                Builder $translationQuery
                                            ) =>
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
                    },
            ]);
    }

    /**
     * Полное представление варианта
     * для Show / Edit.
     */
    protected function fullOptionQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                /*
                 * Для полного Resource нужны
                 * все переводы варианта.
                 */
                'translations',

                /*
                 * Родительское поле.
                 */
                'field' =>
                    function (
                        Builder $fieldQuery
                    ) {
                        $fieldQuery
                            ->with([
                                'translations',

                                'options.translations',

                                'form' =>
                                    function (
                                        Builder $formQuery
                                    ) {
                                        $formQuery->with([
                                            'translations',

                                            'user:id,name,email,profile_photo_path',
                                        ]);
                                    },
                            ])
                            ->withCount([
                                'options',
                                'submissionValues',
                                'submissionFiles',
                            ]);
                    },
            ]);
    }

    /**
     * Полное представление поля
     * для Create.
     *
     * Одновременно выполняется
     * проверка владельца формы.
     */
    protected function fullFieldQuery(): Builder
    {
        return $this->accessibleFieldQuery()
            ->with([
                'translations',

                'options.translations',

                'form' =>
                    function (
                        Builder $formQuery
                    ) {
                        $formQuery->with([
                            'translations',

                            'user:id,name,email,profile_photo_path',
                        ]);
                    },
            ])
            ->withCount([
                'options',
                'submissionValues',
                'submissionFiles',
            ]);
    }

    /**
     * Доступные текущему пользователю поля.
     *
     * FormField также не имеет user_id,
     * поэтому доступ проверяется через форму.
     */
    protected function accessibleFieldQuery(): Builder
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
                fn (Builder $formQuery) =>
                $formQuery->where(
                    'forms.user_id',
                    $user->id
                )
            );
        }

        return $query;
    }

    /**
     * Получение списка вариантов
     * в зависимости от режима обработки.
     */
    protected function getIndexOptions(
        Builder $query,
        bool $useServerProcessing,
        string $search,
        string $sort,
        string $locale,
        int $perPage
    ) {
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
    | Data helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Подготовить основные данные варианта
     * без массива переводов.
     */
    protected function optionData(
        array $validated
    ): array {
        return [
            'form_field_id' =>
                $validated['form_field_id'],

            'value' =>
                $validated['value'],

            'activity' =>
                $validated['activity'],

            'is_default' =>
                $validated['is_default'],

            'sort' =>
                $validated['sort'],

            'settings' =>
                $validated['settings']
                ?? null,
        ];
    }

    /**
     * Подготовить вариант, выбранный
     * значением по умолчанию.
     *
     * Для:
     * - select;
     * - radio;
     *
     * допускается только один default.
     *
     * Для:
     * - checkbox_group;
     *
     * допускается несколько default options.
     */
    protected function prepareDefaultOption(
        int $fieldId,
        bool $isDefault,
        ?int $exceptOptionId = null
    ): void {
        if (!$isDefault) {
            return;
        }

        /*
         * Поле одновременно проверяется
         * на доступ текущему пользователю.
         */
        $field = $this->accessibleFieldQuery()
            ->select([
                'form_fields.id',
                'form_fields.type',
            ])
            ->findOrFail(
                $fieldId
            );

        /*
        |--------------------------------------------------------------------------
        | checkbox_group
        |--------------------------------------------------------------------------
        |
        | Группа checkbox поддерживает несколько
        | выбранных значений по умолчанию.
        |
        */

        if (
            $field->type
            === 'checkbox_group'
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Остальные option-типы
        |--------------------------------------------------------------------------
        |
        | Для select / radio должен существовать
        | максимум один default.
        |
        */

        $query = FormFieldOption::query()
            ->where(
                'form_field_id',
                $fieldId
            )
            ->where(
                'is_default',
                true
            );

        if ($exceptOptionId !== null) {
            $query->where(
                'id',
                '!=',
                $exceptOptionId
            );
        }

        $query->update([
            'is_default' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Processing mode
    |--------------------------------------------------------------------------
    */

    /**
     * Определить, используется ли
     * серверная обработка списка.
     */
    protected function shouldUseServerProcessing(
        string $mode
    ): bool {
        return match ($mode) {
            'server' => true,
            'frontend' => false,

            /*
             * Для auto пока используем server.
             *
             * При необходимости позже можно
             * переключать режим по количеству записей.
             */
            default => true,
        };
    }
}
