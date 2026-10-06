<?php

namespace App\Http\Controllers\Admin\Form\FormSubmission;

use App\Http\Controllers\Admin\Form\BaseFormAdminController;
use App\Http\Requests\Admin\Form\FormSubmission\FormSubmissionRequest;
use App\Http\Resources\Admin\Form\FormSubmission\FormSubmissionResource;
use App\Http\Resources\Admin\Form\FormSubmission\FormSubmissionSharedResource;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\Admin\Form\FormSubmissionFile\FormSubmissionFile;
use App\Models\User;
use App\Services\Admin\Form\FormSubmissionAssignmentService;
use App\Services\Admin\Form\FormSubmissionStatusService;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
     * Список заявок.
     *
     * Поддерживает:
     * - frontend;
     * - server;
     * - auto режимы обработки;
     * - поиск;
     * - сортировку;
     * - CRM-фильтры;
     * - серверную пагинацию.
     */
    public function index(Request $request): Response
    {
        $locale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminFormSubmissionsPerPage',
            20
        );

        $defaultSort = $settings->string(
            'adminFormSubmissionsDefaultSort',
            'submittedAtDesc'
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
            'adminFormSubmissionsProcessingMode',
            'auto'
        );

        /**
         * Общее количество доступных пользователю заявок.
         *
         * Используется для определения режима auto.
         *
         * baseQuery() уже учитывает доступ:
         * - admin видит все заявки;
         * - остальные пользователи видят заявки
         *   только принадлежащих им форм.
         */
        $submissionsCount = $this->baseQuery()->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $submissionsCount,
            300
        );

        try {
            $submissions = $this->getIndexSubmissions(
                request: $request,
                locale: $locale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render(
                'Admin/Form/FormSubmissions/Index',
                [
                    'submissions' =>
                        FormSubmissionSharedResource::collection(
                            $submissions
                        ),

                    'submissionsCount' =>
                        $submissionsCount,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminFormSubmissionsProcessingMode' =>
                        $processingMode,

                    'adminFormSubmissionsPerPage' =>
                        $perPage,

                    'adminFormSubmissionsDefaultSort' =>
                        $defaultSort,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' =>
                        $this->indexFilters($request),

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
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки заявок форм для Index: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Form/FormSubmissions/Index',
                [
                    'submissions' => [],

                    'submissionsCount' =>
                        $submissionsCount,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminFormSubmissionsProcessingMode' =>
                        $processingMode,

                    'adminFormSubmissionsPerPage' =>
                        $perPage,

                    'adminFormSubmissionsDefaultSort' =>
                        $defaultSort,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' =>
                        $this->indexFilters($request),

                    'statuses' => config(
                        'forms.submission_statuses',
                        []
                    ),

                    'currentLocale' =>
                        $locale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'error' =>
                        __('admin/controllers.index_error'),
                ]
            );
        }
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
            'Admin/Form/FormSubmissions/Show',
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
            'Admin/Form/FormSubmissions/Edit',
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
        int $formSubmission,
        FormSubmissionAssignmentService $assignmentService,
        FormSubmissionStatusService $statusService
    ): RedirectResponse {
        /**
         * Получаем заявку исключительно
         * через access-controlled query.
         */
        $submission = $this->baseQuery()
            ->findOrFail($formSubmission);

        $validated = $request->validated();

        /**
         * Пользователь, выполняющий
         * административное действие.
         */
        $user = auth()->user();

        /**
         * Новый ответственный сотрудник.
         *
         * null означает снятие назначения.
         */
        $assignedUser = isset($validated['assigned_user_id'])
            ? User::query()->findOrFail(
                $validated['assigned_user_id']
            )
            : null;

        /**
         * Изменение ответственного сотрудника.
         *
         * Сервис самостоятельно:
         * - проверяет фактическое изменение;
         * - сохраняет assigned_user_id;
         * - отправляет событие после транзакции.
         */
        $assignmentService->assign(
            $submission,
            $assignedUser,
            $user,
            'admin'
        );

        /**
         * Изменение статуса.
         *
         * Сервис самостоятельно:
         * - проверяет фактическое изменение;
         * - управляет processed_at;
         * - управляет completed_at;
         * - создаёт историю перехода;
         * - отправляет событие после транзакции.
         */
        $statusService->changeStatus(
            $submission,
            $validated['status'],
            $user,
            'admin'
        );

        return redirect()
            ->route(
                'admin.formSubmissions.index',
                [
                    'formSubmission' => $submission->id,
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
        /**
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
     * Получить заявки для Admin Index.
     *
     * Frontend:
     * - CRM-фильтры выполняются Laravel;
     * - поиск выполняется Vue;
     * - сортировка выполняется Vue;
     * - пагинация выполняется Vue.
     *
     * Server:
     * - CRM-фильтры выполняются Laravel;
     * - поиск выполняется Laravel;
     * - сортировка выполняется Laravel;
     * - пагинация выполняется Laravel.
     */
    protected function getIndexSubmissions(
        Request $request,
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search
    ): Collection|LengthAwarePaginator {
        $query = $this->indexQuery($locale);

        /**
         * Структурные CRM-фильтры применяем
         * независимо от режима обработки.
         */
        $this->applyIndexFilters(
            $query,
            $request
        );

        /**
         * Server mode.
         */
        if ($useServerProcessing) {
            $query->search(
                $search,
                $locale
            );

            $query->sortByParam(
                $sort,
                $locale
            );

            return $query
                ->paginate($perPage)
                ->withQueryString();
        }

        /**
         * Frontend mode.
         *
         * Возвращаем всю отфильтрованную коллекцию.
         * Поиск, сортировку и пагинацию выполнит Vue.
         */
        return $query->get();
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
                /**
                 * Родительская форма.
                 */
                'form' =>
                    function ($formQuery) use ($locales) {
                        $formQuery->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery->whereIn(
                                    'locale',
                                    $locales
                                ),

                            'user:id,name,email,profile_photo_path',
                        ]);
                    },

                /**
                 * Авторизованный отправитель.
                 */
                'user:id,name,email,profile_photo_path',

                /**
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

                'form' => function ($formQuery) {
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

                'values.field' => function ($fieldQuery) {
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

                'files.field' => function ($fieldQuery) {
                    $fieldQuery->with([
                        'translations',
                    ]);
                },

                /*
                |--------------------------------------------------------------------------
                | История статусов
                |--------------------------------------------------------------------------
                */

                'statusHistory.user:id,name,email,profile_photo_path',
            ])
            ->withCount([
                'values',
                'files',
            ]);
    }

    /**
     * Применить CRM-фильтры списка заявок.
     */
    protected function applyIndexFilters(
        Builder $query,
        Request $request
    ): void {
        /**
         * Форма.
         */
        if ($request->filled('form_id')) {
            $query->forForm(
                (int) $request->input('form_id')
            );
        }

        /**
         * Статус.
         */
        if ($request->filled('status')) {
            $query->where(
                'form_submissions.status',
                (string) $request->input('status')
            );
        }

        /**
         * Источник.
         */
        if ($request->filled('source')) {
            $query->fromSource(
                (string) $request->input('source')
            );
        }

        /**
         * Локаль отправленной заявки.
         */
        if ($request->filled('locale')) {
            $query->locale(
                (string) $request->input('locale')
            );
        }

        /**
         * Отправитель.
         */
        if ($request->filled('user_id')) {
            $query->forUser(
                (int) $request->input('user_id')
            );
        }

        /**
         * Ответственный сотрудник.
         *
         * Поддерживает:
         * assigned_user_id = 15
         * assigned_user_id = unassigned
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

        /**
         * UTM source.
         */
        if ($request->filled('utm_source')) {
            $query->utmSource(
                (string) $request->input('utm_source')
            );
        }

        /**
         * UTM campaign.
         */
        if ($request->filled('utm_campaign')) {
            $query->utmCampaign(
                (string) $request->input('utm_campaign')
            );
        }

        /**
         * Период отправки.
         */
        $query->submittedBetween(
            $request->input('date_from'),
            $request->input('date_to')
        );
    }

    /**
     * Текущие CRM-фильтры списка.
     *
     * @return array<string, mixed>
     */
    protected function indexFilters(
        Request $request
    ): array {
        return [
            'form_id' =>
                $request->filled('form_id')
                    ? (int) $request->input('form_id')
                    : null,

            'status' =>
                $request->input('status'),

            'source' =>
                $request->input('source'),

            'locale' =>
                $request->input('locale'),

            'user_id' =>
                $request->filled('user_id')
                    ? (int) $request->input('user_id')
                    : null,

            'assigned_user_id' =>
                $request->input('assigned_user_id'),

            'utm_source' =>
                $request->input('utm_source'),

            'utm_campaign' =>
                $request->input('utm_campaign'),

            'date_from' =>
                $request->input('date_from'),

            'date_to' =>
                $request->input('date_to'),
        ];
    }

    /**
     * Скачать прикреплённый файл заявки.
     */
    public function downloadFile(
        FormSubmission $formSubmission,
        FormSubmissionFile $formSubmissionFile
    ): StreamedResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Проверка доступа к заявке
        |--------------------------------------------------------------------------
        |
        | Используем ту же ownership-логику, что и для остальных
        | административных действий с заявками.
        |
        */

        $submission = $this->baseQuery()
            ->whereKey($formSubmission->id)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Проверка принадлежности файла заявке
        |--------------------------------------------------------------------------
        */

        if (
            (int) $formSubmissionFile->form_submission_id
            !== (int) $submission->id
        ) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Проверка существования файла
        |--------------------------------------------------------------------------
        */

        if (!$formSubmissionFile->existsOnDisk()) {
            abort(
                404,
                'Файл заявки не найден.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Скачивание
        |--------------------------------------------------------------------------
        */

        return Storage::disk(
            $formSubmissionFile->disk
        )->download(
            $formSubmissionFile->path,
            $formSubmissionFile->original_name
        );
    }
}
