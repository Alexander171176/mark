<?php

namespace App\Http\Controllers\Admin\Form\FormSubmission;

use App\Http\Controllers\Admin\Form\BaseFormAdminController;
use App\Http\Requests\Admin\Form\FormSubmission\FormSubmissionRequest;
use App\Http\Resources\Admin\Form\FormSubmission\FormSubmissionResource;
use App\Http\Resources\Admin\Form\FormSubmission\FormSubmissionSharedResource;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FormSubmissionController extends BaseFormAdminController
{
    /**
     * Модель административного раздела.
     */
    protected string $modelClass = FormSubmission::class;

    /**
     * Название сущности для общих
     * административных операций.
     */
    protected string $entityLabel = 'заявку';

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    /**
     * Display a listing of the resource.
     */
    public function index(
        Request $request
    ): Response {
        $locale = $this->resolveLocale(
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Основные параметры списка
        |--------------------------------------------------------------------------
        */

        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $sort = (string) $request->input(
            'sort',
            'submittedAtDesc'
        );

        $perPage = (int) $request->input(
            'per_page',
            20
        );

        $perPage = in_array(
            $perPage,
            [10, 20, 50, 100],
            true
        )
            ? $perPage
            : 20;

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = $this->indexQuery(
            $locale
        );

        /*
        |--------------------------------------------------------------------------
        | Поиск
        |--------------------------------------------------------------------------
        */

        $query->search(
            $search,
            $locale
        );

        /*
        |--------------------------------------------------------------------------
        | Структурный фильтр: форма
        |--------------------------------------------------------------------------
        */

        if ($request->filled('form_id')) {
            $query->forForm(
                (int) $request->input(
                    'form_id'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Фильтр: статус
        |--------------------------------------------------------------------------
        |
        | Пока сохраняем отдельный status-фильтр.
        |
        | Позже Vue сможет использовать как отдельный
        | CRM-фильтр, так и status-токены SortSelect.
        |
        */

        if ($request->filled('status')) {
            $query->where(
                'form_submissions.status',
                (string) $request->input(
                    'status'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Фильтр: источник
        |--------------------------------------------------------------------------
        */

        if ($request->filled('source')) {
            $query->fromSource(
                (string) $request->input(
                    'source'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Фильтр: локаль заявки
        |--------------------------------------------------------------------------
        |
        | Это локаль, на которой была отправлена
        | заявка, а не текущая локаль Admin UI.
        |
        */

        if ($request->filled('locale')) {
            $query->locale(
                (string) $request->input(
                    'locale'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Фильтр: отправитель
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {
            $query->forUser(
                (int) $request->input(
                    'user_id'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Фильтр: ответственный сотрудник
        |--------------------------------------------------------------------------
        |
        | Поддерживаем:
        |
        | assigned_user_id = 15
        | assigned_user_id = unassigned
        |
        */

        if ($request->filled('assigned_user_id')) {
            $assignedUserId = $request->input(
                'assigned_user_id'
            );

            if ($assignedUserId === 'unassigned') {
                $query->unassigned();
            } elseif (is_numeric($assignedUserId)) {
                $query->assignedTo(
                    (int) $assignedUserId
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UTM source
        |--------------------------------------------------------------------------
        */

        if ($request->filled('utm_source')) {
            $query->utmSource(
                (string) $request->input(
                    'utm_source'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UTM campaign
        |--------------------------------------------------------------------------
        */

        if ($request->filled('utm_campaign')) {
            $query->utmCampaign(
                (string) $request->input(
                    'utm_campaign'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Период отправки
        |--------------------------------------------------------------------------
        */

        $query->submittedBetween(
            $request->input(
                'date_from'
            ),
            $request->input(
                'date_to'
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Сортировка / фильтрация по sort-контракту
        |--------------------------------------------------------------------------
        */

        $query->sortByParam(
            $sort,
            $locale
        );

        /*
        |--------------------------------------------------------------------------
        | Пагинация
        |--------------------------------------------------------------------------
        */

        $submissions = $query
            ->paginate(
                $perPage
            )
            ->withQueryString();

        return Inertia::render(
            'Admin/Form/FormSubmission/Index',
            [
                'submissions' =>
                    FormSubmissionSharedResource::collection(
                        $submissions
                    ),

                /*
                 * Параметры общего Admin Index.
                 */
                'search' => $search,
                'sortParam' => $sort,
                'perPage' => $perPage,

                /*
                 * Структурные / CRM-фильтры.
                 */
                'filters' => [
                    'form_id' =>
                        $request->filled('form_id')
                            ? (int) $request->input(
                            'form_id'
                        )
                            : null,

                    'status' =>
                        $request->input(
                            'status'
                        ),

                    'source' =>
                        $request->input(
                            'source'
                        ),

                    'locale' =>
                        $request->input(
                            'locale'
                        ),

                    'user_id' =>
                        $request->filled('user_id')
                            ? (int) $request->input(
                            'user_id'
                        )
                            : null,

                    'assigned_user_id' =>
                        $request->input(
                            'assigned_user_id'
                        ),

                    'utm_source' =>
                        $request->input(
                            'utm_source'
                        ),

                    'utm_campaign' =>
                        $request->input(
                            'utm_campaign'
                        ),

                    'date_from' =>
                        $request->input(
                            'date_from'
                        ),

                    'date_to' =>
                        $request->input(
                            'date_to'
                        ),
                ],

                /*
                 * Справочники.
                 */
                'statuses' => config(
                    'forms.submission_statuses',
                    []
                ),

                'currentLocale' =>
                    $locale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
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
        int $formSubmission
    ): Response {
        $submission = $this->fullSubmissionQuery()
            ->findOrFail(
                $formSubmission
            );

        return Inertia::render(
            'Admin/Form/FormSubmission/Show',
            [
                'submission' =>
                    new FormSubmissionResource(
                        $submission
                    ),

                'statuses' => config(
                    'forms.submission_statuses',
                    []
                ),
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
        int $formSubmission
    ): Response {
        $submission = $this->fullSubmissionQuery()
            ->findOrFail(
                $formSubmission
            );

        return Inertia::render(
            'Admin/Form/FormSubmission/Edit',
            [
                'submission' =>
                    new FormSubmissionResource(
                        $submission
                    ),

                'statuses' => config(
                    'forms.submission_statuses',
                    []
                ),

                /*
                |--------------------------------------------------------------------------
                | Пользователи
                |--------------------------------------------------------------------------
                |
                | Пока передаём компактный список
                | для выбора ответственного сотрудника.
                |
                | Когда CRM получит собственную модель
                | сотрудников / ролей, здесь можно
                | будет ограничить выбор.
                |
                */

                'users' => User::query()
                    ->select([
                        'id',
                        'name',
                        'email',
                    ])
                    ->orderBy(
                        'name'
                    )
                    ->get(),
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
        FormSubmissionRequest $request,
        int $formSubmission
    ): RedirectResponse {
        /*
         * Получаем заявку исключительно
         * через access-controlled query.
         */
        $submission = $this->baseQuery()
            ->findOrFail(
                $formSubmission
            );

        $validated = $request->validated();

        DB::transaction(
            function () use (
                $submission,
                $validated
            ) {
                $oldStatus =
                    $submission->status;

                $newStatus =
                    $validated['status'];

                /*
                |--------------------------------------------------------------------------
                | Основные административные данные
                |--------------------------------------------------------------------------
                */

                $submission->status =
                    $newStatus;

                $submission->assigned_user_id =
                    $validated['assigned_user_id']
                    ?? null;

                /*
                |--------------------------------------------------------------------------
                | Начало обработки
                |--------------------------------------------------------------------------
                |
                | processed_at фиксирует первое начало
                | обработки заявки.
                |
                | Если заявка уже когда-либо была
                | взята в работу, timestamp повторно
                | не изменяется.
                |
                */

                if (
                    $newStatus
                    === FormSubmission::STATUS_PROCESSING
                    && $submission->processed_at === null
                ) {
                    $submission->processed_at = now();
                }

                /*
                |--------------------------------------------------------------------------
                | Завершение заявки
                |--------------------------------------------------------------------------
                |
                | completed_at устанавливаем при первом
                | переходе в completed.
                |
                | Если завершённую заявку возвращают
                | в другой статус, completed_at очищаем.
                |
                */

                if (
                    $newStatus
                    === FormSubmission::STATUS_COMPLETED
                ) {
                    if (
                        $oldStatus
                        !== FormSubmission::STATUS_COMPLETED
                        || $submission->completed_at === null
                    ) {
                        $submission->completed_at = now();
                    }
                } elseif (
                    $oldStatus
                    === FormSubmission::STATUS_COMPLETED
                ) {
                    $submission->completed_at = null;
                }

                $submission->save();
            }
        );

        return redirect()
            ->route(
                'admin.formSubmissions.edit',
                [
                    'formSubmission' =>
                        $submission->id,
                ]
            )
            ->with(
                'success',
                'Заявка успешно обновлена.'
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
        int $formSubmission
    ): RedirectResponse {
        /*
         * Обязательно получаем заявку
         * через owner/access scope.
         */
        $submission = $this->baseQuery()
            ->with([
                'files',
            ])
            ->findOrFail(
                $formSubmission
            );

        /*
        |--------------------------------------------------------------------------
        | Физические файлы
        |--------------------------------------------------------------------------
        |
        | После удаления form_submissions записи
        | form_submission_files будут удалены
        | каскадом БД.
        |
        | Поэтому физические файлы необходимо
        | удалить до удаления заявки.
        |
        */

        foreach ($submission->files as $file) {
            try {
                $deleted =
                    $file->deleteFromDisk();

                if (!$deleted) {
                    return back()->with(
                        'error',
                        sprintf(
                            'Не удалось удалить файл "%s". Заявка не была удалена.',
                            $file->original_name
                        )
                    );
                }
            } catch (Throwable $exception) {
                report(
                    $exception
                );

                return back()->with(
                    'error',
                    sprintf(
                        'Не удалось удалить файл "%s". Заявка не была удалена.',
                        $file->original_name
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Запись заявки
        |--------------------------------------------------------------------------
        |
        | После удаления заявки:
        |
        | form_submission_values
        | form_submission_files
        |
        | удаляются через ON DELETE CASCADE.
        |
        */

        DB::transaction(
            function () use ($submission) {
                $submission->delete();
            }
        );

        return redirect()
            ->route(
                'admin.formSubmissions.index'
            )
            ->with(
                'success',
                'Заявка успешно удалена.'
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
     * FormSubmission не имеет собственного
     * user_id владельца.
     *
     * user_id внутри form_submissions —
     * это отправитель заявки, а НЕ владелец.
     *
     * Поэтому административный доступ
     * проверяем через:
     *
     * submission
     * → form
     * → user_id.
     */
    protected function baseQuery(): Builder
    {
        $query = FormSubmission::query();

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
                function (
                    Builder $formQuery
                ) use ($user) {
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
     * Query для Admin Index.
     *
     * Загружаем только relations/counts,
     * необходимые SharedResource.
     */
    protected function indexQuery(
        string $locale
    ): Builder {
        $locales = $this->resourceLocales(
            $locale
        );

        return $this->baseQuery()
            ->with([
                /*
                 * Родительская форма.
                 */
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

                /*
                 * Авторизованный отправитель.
                 */
                'user:id,name,email,profile_photo_path',

                /*
                 * Ответственный сотрудник.
                 */
                'assignedUser:id,name,email,profile_photo_path',
            ])
            ->withCount([
                'values',
                'files',
            ]);
    }

    /**
     * Полный query для Show / Edit.
     *
     * Resource получает все необходимые
     * relations заранее и сам SQL не выполняет.
     */
    protected function fullSubmissionQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                /*
                |--------------------------------------------------------------------------
                | Форма
                |--------------------------------------------------------------------------
                */

                'form' =>
                    function (
                        Builder $formQuery
                    ) {
                        $formQuery->with([
                            'translations',

                            'user:id,name,email,profile_photo_path',
                        ]);
                    },

                /*
                |--------------------------------------------------------------------------
                | Пользователи
                |--------------------------------------------------------------------------
                */

                'user:id,name,email,profile_photo_path',

                'assignedUser:id,name,email,profile_photo_path',

                /*
                |--------------------------------------------------------------------------
                | Значения полей
                |--------------------------------------------------------------------------
                |
                | В самой записи значения уже хранятся
                | snapshot-поля.
                |
                | Связь field нужна для дополнительного
                | административного контекста, если
                | исходное поле ещё существует.
                |
                */

                'values',

                'values.field' =>
                    function (
                        Builder $fieldQuery
                    ) {
                        $fieldQuery->with([
                            'translations',
                        ]);
                    },

                /*
                |--------------------------------------------------------------------------
                | Файлы
                |--------------------------------------------------------------------------
                */

                'files',

                'files.field' =>
                    function (
                        Builder $fieldQuery
                    ) {
                        $fieldQuery->with([
                            'translations',
                        ]);
                    },
            ])
            ->withCount([
                'values',
                'files',
            ]);
    }
}
