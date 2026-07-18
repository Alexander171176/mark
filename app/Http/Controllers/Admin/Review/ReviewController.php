<?php

namespace App\Http\Controllers\Admin\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Resources\Admin\Review\ReviewResource;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Review\Review;
use App\Models\Admin\Review\ReviewImage;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер управления универсальными отзывами
 * в административной панели.
 *
 * Администратор не создаёт и не редактирует
 * пользовательское содержимое отзывов.
 *
 * Доступные административные действия:
 * - просмотр списка и подробной информации;
 * - поиск, сортировка и пагинация;
 * - frontend / auto / server режимы обработки;
 * - переключение активности;
 * - массовое переключение активности;
 * - модерация;
 * - одиночное и массовое удаление;
 * - автоматический пересчёт рейтинга связанной сущности.
 */
class ReviewController extends Controller
{
    /**
     * Базовый запрос отзывов.
     */
    private function baseQuery(): Builder
    {
        return Review::query();
    }

    /**
     * Список отзывов.
     */
    public function index(Request $request): Response
    {
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminReviewsPerPage',
            10
        );

        $defaultSort = $settings->string(
            'adminReviewsDefaultSort',
            'idDesc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query('search', '')
        );

        $processingMode = $settings->string(
            'adminReviewsProcessingMode',
            'frontend'
        );

        $reviewsCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $reviewsCount,
                300
            );

        try {
            $reviews = $this->getIndexReviews(
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render('Admin/Reviews/Index', [
                'reviews' => ReviewResource::collection($reviews),
                'reviewsCount' => $reviewsCount,

                'useServerProcessing' => $useServerProcessing,

                'adminReviewsProcessingMode' => $processingMode,
                'adminReviewsPerPage' => $perPage,
                'adminReviewsDefaultSort' => $defaultSort,

                'sortParam' => $sortParam,
                'search' => $search,

                'currentLocale' => app()->getLocale(),
                'availableLocales' => config(
                    'app.available_locales',
                    ['ru']
                ),

                'isAdmin' => $this->isAdmin(),
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка отзывов: '.$e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render('Admin/Reviews/Index', [
                'reviews' => [],
                'reviewsCount' => 0,

                'useServerProcessing' => $useServerProcessing,

                'adminReviewsProcessingMode' => $processingMode,
                'adminReviewsPerPage' => $perPage,
                'adminReviewsDefaultSort' => $defaultSort,

                'sortParam' => $sortParam,
                'search' => $search,

                'currentLocale' => app()->getLocale(),
                'availableLocales' => config(
                    'app.available_locales',
                    ['ru']
                ),

                'isAdmin' => $this->isAdmin(),

                'error' => __('admin/controllers.index_error'),
            ]);
        }
    }

    /**
     * Удаление отзыва вместе с его изображениями.
     */
    public function destroy(
        Review $review
    ): RedirectResponse {
        $this->ensureAdmin();

        $reviewableType = $review->reviewable_type;
        $reviewableId = (int) $review->reviewable_id;

        try {
            DB::transaction(function () use (
                $review,
                $reviewableType,
                $reviewableId
            ): void {
                $this->deleteReviewWithImages($review);

                $this->recalculateReviewableRating(
                    $reviewableType,
                    $reviewableId
                );
            });

            Log::info('Отзыв удалён.', [
                'review_id' => $review->id,
            ]);

            return redirect()
                ->route('admin.reviews.index')
                ->with(
                    'success',
                    __('admin/controllers.deleted_success')
                );
        } catch (Throwable $e) {
            Log::error(
                "Ошибка удаления отзыва ID {$review->id}: "
                .$e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                __('admin/controllers.deleted_error')
            );
        }
    }

    /**
     * Массовое удаление отзывов.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse|JsonResponse {
        $this->ensureAdmin();

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
                Rule::exists('reviews', 'id'),
            ],
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                $reviews = Review::query()
                    ->whereIn('id', $validated['ids'])
                    ->with('images')
                    ->get();

                $reviewables = $this->reviewableKeys($reviews);

                foreach ($reviews as $review) {
                    $this->deleteReviewWithImages($review);
                }

                $this->recalculateReviewableRatings($reviewables);
            });

            Log::info('Отзывы удалены массово.', [
                'ids' => $validated['ids'],
            ]);

            $message = __(
                'admin/controllers.bulk_deleted_success'
            );

            return $request->expectsJson()
                ? response()->json([
                    'success' => true,
                    'message' => $message,
                    'reload' => true,
                ])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления отзывов: '
                .$e->getMessage(),
                [
                    'ids' => $validated['ids'],
                    'exception' => $e,
                ]
            );

            $message = __(
                'admin/controllers.bulk_deleted_error'
            );

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Переключение активности одного отзыва.
     */
    public function updateActivity(
        UpdateActivityRequest $request,
        Review $review
    ): JsonResponse {
        $this->ensureAdmin();

        $validated = $request->validated();

        try {
            DB::transaction(function () use (
                $review,
                $validated
            ): void {
                $review->update([
                    'activity' => (bool) $validated['activity'],
                ]);

                $this->recalculateReviewableRating(
                    $review->reviewable_type,
                    (int) $review->reviewable_id
                );
            });

            Log::info('Обновлена активность отзыва.', [
                'review_id' => $review->id,
                'activity' => $review->activity,
            ]);

            return response()->json([
                'success' => true,
                'activity' => (bool) $review->activity,

                'message' => $review->activity
                    ? __('admin/controllers.activated_success')
                    : __('admin/controllers.deactivated_success'),
            ]);
        } catch (Throwable $e) {
            Log::error(
                "Ошибка updateActivity отзыва ID {$review->id}: "
                .$e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    __('admin/controllers.activity_updated_error'),
            ], 500);
        }
    }

    /**
     * Массовое переключение активности отзывов.
     */
    public function bulkUpdateActivity(
        Request $request
    ): RedirectResponse|JsonResponse {
        $this->ensureAdmin();

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
                Rule::exists('reviews', 'id'),
            ],

            'activity' => [
                'required',
                'boolean',
            ],
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                $reviews = Review::query()
                    ->whereIn('id', $validated['ids'])
                    ->get([
                        'id',
                        'reviewable_type',
                        'reviewable_id',
                    ]);

                $reviewables = $this->reviewableKeys($reviews);

                Review::query()
                    ->whereIn('id', $validated['ids'])
                    ->update([
                        'activity' =>
                            (bool) $validated['activity'],
                    ]);

                $this->recalculateReviewableRatings($reviewables);
            });

            Log::info('Массово обновлена активность отзывов.', [
                'ids' => $validated['ids'],
                'activity' => (bool) $validated['activity'],
            ]);

            $message = __(
                'admin/controllers.bulk_activity_updated_success'
            );

            return $request->expectsJson()
                ? response()->json([
                    'success' => true,
                    'message' => $message,
                ])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkUpdateActivity отзывов: '
                .$e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message = __(
                'admin/controllers.bulk_activity_updated_error'
            );

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Модерация одного отзыва.
     */
    public function approve(
        Request $request,
        Review $review
    ): RedirectResponse|JsonResponse {
        $this->ensureAdmin();

        $validated = $request->validate([
            'moderation_status' => [
                'required',
                'integer',
                Rule::in([0, 1, 2]),
            ],

            'moderation_note' => [
                'nullable',
                'string',
                'max:500',

                Rule::requiredIf(
                    fn () => (int) $request->input(
                            'moderation_status'
                        ) === 2
                ),
            ],
        ]);

        $user = auth()->user();

        try {
            DB::transaction(function () use (
                $review,
                $validated,
                $user
            ): void {
                $moderationStatus = (int) $validated[
                'moderation_status'
                ];

                $review->update([
                    'moderation_status' => $moderationStatus,

                    'moderation_note' =>
                        $validated['moderation_note'] ?? null,

                    'moderated_by' => $moderationStatus === 0
                        ? null
                        : $user->id,

                    'moderated_at' => $moderationStatus === 0
                        ? null
                        : now(),
                ]);

                $this->recalculateReviewableRating(
                    $review->reviewable_type,
                    (int) $review->reviewable_id
                );
            });

            $message = __(
                'admin/controllers.updated_success'
            );

            if ($request->expectsJson()) {
                $this->loadReviewRelations($review);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'review' => new ReviewResource($review),
                ]);
            }

            return back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error(
                "Ошибка модерации отзыва ID {$review->id}: "
                .$e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message = __(
                'admin/controllers.updated_error'
            );

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Базовый запрос списка отзывов со связями.
     */
    private function indexReviewsQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'author:id,name,email,profile_photo_path',
                'replier:id,name,email,profile_photo_path',
                'moderator:id,name,email,profile_photo_path',
                'images.media',

                'reviewable' => function (
                    MorphTo $morphTo
                ): void {
                    $morphTo->morphWith(
                        $this->reviewableMorphWith()
                    );
                },
            ])
            ->withCount('images');
    }

    /**
     * Получение списка по выбранному режиму обработки.
     */
    private function getIndexReviews(
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexReviewsQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search)
                ->sortByParam($sort)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort)
            ->get();
    }

    /**
     * Загрузка отношений одного отзыва.
     */
    private function loadReviewRelations(
        Review $review
    ): void {
        $review->load([
            'author:id,name,email,profile_photo_path',
            'replier:id,name,email,profile_photo_path',
            'moderator:id,name,email,profile_photo_path',
            'images.media',

            'reviewable' => function (
                MorphTo $morphTo
            ): void {
                $morphTo->morphWith(
                    $this->reviewableMorphWith()
                );
            },
        ])->loadCount('images');
    }

    /**
     * Связи, загружаемые для разных reviewable-типов.
     *
     * Позже сюда добавятся комплекты и курсы.
     *
     * @return array<class-string<Model>, array<int, string>>
     */
    private function reviewableMorphWith(): array
    {
        return [
            MarketProduct::class => [
                'translations',
            ],

            /*
             * Позже:
             *
             * MarketBundle::class => [
             *     'translations',
             * ],
             *
             * SchoolCourse::class => [
             *     'translations',
             * ],
             */
        ];
    }

    /**
     * Удаление отзыва и неиспользуемых изображений.
     */
    private function deleteReviewWithImages(
        Review $review
    ): void {
        $imageIds = $review->images()
            ->pluck('review_images.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $review->images()->detach();
        $review->delete();

        if ($imageIds === []) {
            return;
        }

        $unusedImages = ReviewImage::query()
            ->whereIn('id', $imageIds)
            ->whereDoesntHave('reviews')
            ->get();

        foreach ($unusedImages as $image) {
            /*
             * ReviewImage наследуется от BaseImage.
             * При удалении модели Spatie MediaLibrary удалит
             * связанные файлы коллекции.
             */
            $image->delete();
        }
    }

    /**
     * Уникальные пары reviewable_type + reviewable_id.
     *
     * @return Collection<int, array{
     *     type: string,
     *     id: int
     * }>
     */
    private function reviewableKeys(
        Collection $reviews
    ): Collection {
        return $reviews
            ->map(fn (Review $review) => [
                'type' => (string) $review->reviewable_type,
                'id' => (int) $review->reviewable_id,
            ])
            ->unique(
                fn (array $item) =>
                    $item['type'].'#'.$item['id']
            )
            ->values();
    }

    /**
     * Пересчёт рейтинга нескольких сущностей.
     */
    private function recalculateReviewableRatings(
        Collection $reviewables
    ): void {
        foreach ($reviewables as $reviewable) {
            $this->recalculateReviewableRating(
                $reviewable['type'],
                $reviewable['id']
            );
        }
    }

    /**
     * Пересчёт среднего рейтинга полиморфной сущности.
     *
     * В расчёт входят только активные и одобренные отзывы.
     * Если у связанной модели нет rating_avg и rating_count,
     * пересчёт безопасно пропускается.
     */
    private function recalculateReviewableRating(
        string $reviewableType,
        int $reviewableId
    ): void {
        $reviewable = $this->resolveReviewable(
            $reviewableType,
            $reviewableId
        );

        if (! $reviewable) {
            return;
        }

        if (
            ! Schema::hasColumns(
                $reviewable->getTable(),
                [
                    'rating_avg',
                    'rating_count',
                ]
            )
        ) {
            return;
        }

        $ratingQuery = Review::query()
            ->where('reviewable_type', $reviewableType)
            ->where('reviewable_id', $reviewableId)
            ->where('activity', true)
            ->where('moderation_status', 1);

        $ratingCount = (clone $ratingQuery)->count();

        $ratingAverage = $ratingCount > 0
            ? round(
                (float) (clone $ratingQuery)->avg('rating'),
                2
            )
            : 0;

        $reviewable->forceFill([
            'rating_avg' => $ratingAverage,
            'rating_count' => $ratingCount,
        ])->saveQuietly();
    }

    /**
     * Получение полиморфной модели по типу и ID.
     */
    private function resolveReviewable(
        string $reviewableType,
        int $reviewableId
    ): ?Model {
        $modelClass = match ($reviewableType) {
            'market_product' => MarketProduct::class,

            /*
             * Позже:
             *
             * 'market_bundle' => MarketBundle::class,
             * 'school_course' => SchoolCourse::class,
             */

            default => null,
        };

        if (! $modelClass) {
            return null;
        }

        return $modelClass::query()->find($reviewableId);
    }

    /**
     * Проверка административной роли.
     */
    private function ensureAdmin(): void
    {
        abort_unless($this->isAdmin(), 403);
    }

    /**
     * Пользователь является администратором.
     */
    private function isAdmin(): bool
    {
        $user = auth()->user();

        return (bool) (
            $user
            && method_exists($user, 'hasRole')
            && $user->hasRole('admin')
        );
    }
}
