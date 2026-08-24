<?php

namespace App\Models\Admin\School\SchoolAssignment;

use App\Models\Admin\School\SchoolAssignmentSubmission\SchoolAssignmentSubmission;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolAssignment extends Model
{
    use HasFactory;

    protected $table = 'school_assignments';

    protected $fillable = [
        'school_course_id',
        'school_module_id',
        'school_lesson_id',
        'school_instructor_profile_id',
        'slug',
        'sort',
        'activity',
        'left',
        'main',
        'right',
        'published_at',
        'status',
        'visibility',
        'attempts_limit',
        'grading_type',
        'max_score',
        'due_at',
    ];

    protected $casts = [
        'school_course_id' => 'integer',
        'school_module_id' => 'integer',
        'school_lesson_id' => 'integer',
        'school_instructor_profile_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',
        'published_at' => 'datetime',
        'attempts_limit' => 'integer',
        'max_score' => 'integer',
        'due_at' => 'datetime',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolAssignmentTranslation::class, 'school_assignment_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolAssignmentTranslation::class, 'school_assignment_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Курс задания */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Модуль задания */
    public function module(): BelongsTo
    {
        return $this->belongsTo(SchoolModule::class, 'school_module_id');
    }

    /** Урок задания */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(SchoolLesson::class, 'school_lesson_id');
    }

    /** Преподаватель */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(SchoolInstructorProfile::class, 'school_instructor_profile_id');
    }

    /** Сдачи студентов */
    public function submissions(): HasMany
    {
        return $this->hasMany(SchoolAssignmentSubmission::class, 'school_assignment_id');
    }

    /** Изображения задания */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolAssignmentImage::class,
            'school_assignment_has_images',
            'school_assignment_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_assignment_has_images.order', 'asc');
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Опубликованные */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /** Сортировка */
    public function scopeSorted(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Алиас сортировки */
    public function scopeOrdered(Builder $q): Builder
    {
        return $this->scopeSorted($q);
    }

    /** Главный блок */
    public function scopeMain(Builder $q): Builder
    {
        return $q->where('main', true);
    }

    /** Левая колонка */
    public function scopeLeft(Builder $q): Builder
    {
        return $q->where('left', true);
    }

    /** Правая колонка */
    public function scopeRight(Builder $q): Builder
    {
        return $q->where('right', true);
    }

    /** По курсу */
    public function scopeForCourse(Builder $q, int $courseId): Builder
    {
        return $q->where('school_course_id', $courseId);
    }

    /** По модулю */
    public function scopeForModule(Builder $q, int $moduleId): Builder
    {
        return $q->where('school_module_id', $moduleId);
    }

    /** По уроку */
    public function scopeForLesson(Builder $q, int $lessonId): Builder
    {
        return $q->where('school_lesson_id', $lessonId);
    }

    /** Ближайший дедлайн */
    public function scopeDueSoon(Builder $q, int $days = 7): Builder
    {
        return $q
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDays($days)]);
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q->with([
            'translations' => fn ($query) => $query->where('locale', $locale),
        ]);
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->published()
            ->whereHas('translations', fn ($qq) => $qq->where('locale', $locale))
            ->withLocale($locale);
    }

    /** Поиск */
    public function scopeSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        $words = collect(
            preg_split(
                '/[\s:#№,"\'«»(){}\[\].!?\/\\\\|]+/u',
                $term
            )
        )
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words, $locale) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word, $locale) {
                    $query
                        /**
                         * Основные поля задания.
                         */
                        ->where(
                            'school_assignments.slug',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.id',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.sort',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.school_course_id',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.school_module_id',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.school_lesson_id',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.school_instructor_profile_id',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.status',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.visibility',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.grading_type',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.max_score',
                            'like',
                            "%{$word}%"
                        )
                        ->orWhere(
                            'school_assignments.attempts_limit',
                            'like',
                            "%{$word}%"
                        )

                        /**
                         * Перевод задания.
                         */
                        ->orWhereHas(
                            'translations',
                            function (Builder $qq) use ($word, $locale) {
                                $qq
                                    ->where('locale', $locale)
                                    ->where(function (Builder $sub) use ($word) {
                                        $sub
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'subtitle',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'description',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'instructions',
                                                'like',
                                                "%{$word}%"
                                            );
                                    });
                            }
                        )

                        /**
                         * Курс.
                         *
                         * slug лежит в school_courses,
                         * title/short/... — в translations.
                         */
                        ->orWhereHas(
                            'course',
                            function (Builder $qq) use ($word, $locale) {
                                $qq
                                    ->where(
                                        'school_courses.slug',
                                        'like',
                                        "%{$word}%"
                                    )
                                    ->orWhereHas(
                                        'translations',
                                        function (Builder $translationQuery) use ($word, $locale) {
                                            $translationQuery
                                                ->where('locale', $locale)
                                                ->where(function (Builder $sub) use ($word) {
                                                    $sub
                                                        ->where(
                                                            'title',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'subtitle',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'short',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'description',
                                                            'like',
                                                            "%{$word}%"
                                                        );
                                                });
                                        }
                                    );
                            }
                        )

                        /**
                         * Модуль.
                         */
                        ->orWhereHas(
                            'module',
                            function (Builder $qq) use ($word, $locale) {
                                $qq
                                    ->where(
                                        'school_modules.slug',
                                        'like',
                                        "%{$word}%"
                                    )
                                    ->orWhereHas(
                                        'translations',
                                        function (Builder $translationQuery) use ($word, $locale) {
                                            $translationQuery
                                                ->where('locale', $locale)
                                                ->where(function (Builder $sub) use ($word) {
                                                    $sub
                                                        ->where(
                                                            'title',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'subtitle',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'short',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'description',
                                                            'like',
                                                            "%{$word}%"
                                                        );
                                                });
                                        }
                                    );
                            }
                        )

                        /**
                         * Урок.
                         */
                        ->orWhereHas(
                            'lesson',
                            function (Builder $qq) use ($word, $locale) {
                                $qq
                                    ->where(
                                        'school_lessons.slug',
                                        'like',
                                        "%{$word}%"
                                    )
                                    ->orWhereHas(
                                        'translations',
                                        function (Builder $translationQuery) use ($word, $locale) {
                                            $translationQuery
                                                ->where('locale', $locale)
                                                ->where(function (Builder $sub) use ($word) {
                                                    $sub
                                                        ->where(
                                                            'title',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'subtitle',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'short',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'description',
                                                            'like',
                                                            "%{$word}%"
                                                        );
                                                });
                                        }
                                    );
                            }
                        )

                        /**
                         * Инструктор.
                         */
                        ->orWhereHas(
                            'instructor.translations',
                            function (Builder $qq) use ($word, $locale) {
                                $qq
                                    ->where('locale', $locale)
                                    ->where(
                                        'title',
                                        'like',
                                        "%{$word}%"
                                    );
                            }
                        )

                        /**
                         * Пользователь инструктора.
                         */
                        ->orWhereHas(
                            'instructor.user',
                            function (Builder $qq) use ($word) {
                                $qq
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$word}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$word}%"
                                    );
                            }
                        );
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(
        Builder $q,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' =>
            $q->orderBy(
                'school_assignments.id',
                'asc'
            ),

            'idDesc' =>
            $q->orderBy(
                'school_assignments.id',
                'desc'
            ),

            'sortAsc' =>
            $q
                ->orderBy(
                    'school_assignments.sort',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'sortDesc' =>
            $q
                ->orderBy(
                    'school_assignments.sort',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'slugAsc' =>
            $q
                ->orderBy(
                    'school_assignments.slug',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'slugDesc' =>
            $q
                ->orderBy(
                    'school_assignments.slug',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Название задания.
             */
            'titleAsc' =>
            $q
                ->leftJoin(
                    'school_assignment_translations as sat_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sat_sort.school_assignment_id',
                                '=',
                                'school_assignments.id'
                            )
                            ->where(
                                'sat_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'sat_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'titleDesc' =>
            $q
                ->leftJoin(
                    'school_assignment_translations as sat_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sat_sort.school_assignment_id',
                                '=',
                                'school_assignments.id'
                            )
                            ->where(
                                'sat_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'sat_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Курс — ID.
             */
            'courseAsc' =>
            $q
                ->orderBy(
                    'school_assignments.school_course_id',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'courseDesc' =>
            $q
                ->orderBy(
                    'school_assignments.school_course_id',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Курс — название.
             *
             * FK таблицы переводов:
             * school_course_id.
             */
            'courseTitleAsc' =>
            $q
                ->leftJoin(
                    'school_course_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sct_sort.school_course_id',
                                '=',
                                'school_assignments.school_course_id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'sct_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'courseTitleDesc' =>
            $q
                ->leftJoin(
                    'school_course_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sct_sort.school_course_id',
                                '=',
                                'school_assignments.school_course_id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'sct_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Модуль — ID.
             */
            'moduleAsc' =>
            $q
                ->orderBy(
                    'school_assignments.school_module_id',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'moduleDesc' =>
            $q
                ->orderBy(
                    'school_assignments.school_module_id',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Модуль — название.
             *
             * FK:
             * school_module_id.
             */
            'moduleTitleAsc' =>
            $q
                ->leftJoin(
                    'school_module_translations as smt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'smt_sort.school_module_id',
                                '=',
                                'school_assignments.school_module_id'
                            )
                            ->where(
                                'smt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'smt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'moduleTitleDesc' =>
            $q
                ->leftJoin(
                    'school_module_translations as smt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'smt_sort.school_module_id',
                                '=',
                                'school_assignments.school_module_id'
                            )
                            ->where(
                                'smt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'smt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Урок — ID.
             */
            'lessonAsc' =>
            $q
                ->orderBy(
                    'school_assignments.school_lesson_id',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'lessonDesc' =>
            $q
                ->orderBy(
                    'school_assignments.school_lesson_id',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Урок — название.
             *
             * FK:
             * school_lesson_id.
             */
            'lessonTitleAsc' =>
            $q
                ->leftJoin(
                    'school_lesson_translations as slt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'slt_sort.school_lesson_id',
                                '=',
                                'school_assignments.school_lesson_id'
                            )
                            ->where(
                                'slt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'slt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'lessonTitleDesc' =>
            $q
                ->leftJoin(
                    'school_lesson_translations as slt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'slt_sort.school_lesson_id',
                                '=',
                                'school_assignments.school_lesson_id'
                            )
                            ->where(
                                'slt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'slt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Инструктор — ID.
             */
            'instructorAsc' =>
            $q
                ->orderBy(
                    'school_assignments.school_instructor_profile_id',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'instructorDesc' =>
            $q
                ->orderBy(
                    'school_assignments.school_instructor_profile_id',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Инструктор — имя профиля.
             *
             * FK:
             * school_instructor_profile_id.
             */
            'instructorTitleAsc' =>
            $q
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sipt_sort.school_instructor_profile_id',
                                '=',
                                'school_assignments.school_instructor_profile_id'
                            )
                            ->where(
                                'sipt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'sipt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'instructorTitleDesc' =>
            $q
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sipt_sort.school_instructor_profile_id',
                                '=',
                                'school_assignments.school_instructor_profile_id'
                            )
                            ->where(
                                'sipt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_assignments.*'
                )
                ->orderBy(
                    'sipt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Простые поля.
             */
            'statusAsc' =>
            $q
                ->orderBy(
                    'school_assignments.status',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'statusDesc' =>
            $q
                ->orderBy(
                    'school_assignments.status',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'visibilityAsc' =>
            $q
                ->orderBy(
                    'school_assignments.visibility',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'visibilityDesc' =>
            $q
                ->orderBy(
                    'school_assignments.visibility',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'gradingTypeAsc' =>
            $q
                ->orderBy(
                    'school_assignments.grading_type',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'gradingTypeDesc' =>
            $q
                ->orderBy(
                    'school_assignments.grading_type',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'attemptsLimitAsc' =>
            $q
                ->orderBy(
                    'school_assignments.attempts_limit',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'attemptsLimitDesc' =>
            $q
                ->orderBy(
                    'school_assignments.attempts_limit',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'maxScoreAsc' =>
            $q
                ->orderBy(
                    'school_assignments.max_score',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'maxScoreDesc' =>
            $q
                ->orderBy(
                    'school_assignments.max_score',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Counts уже добавляет indexQuery().
             *
             * Поэтому второй withCount()
             * здесь не нужен.
             */
            'submissionsAsc' =>
            $q
                ->orderBy(
                    'submissions_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'submissionsDesc' =>
            $q
                ->orderBy(
                    'submissions_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'imagesAsc' =>
            $q
                ->orderBy(
                    'images_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'imagesDesc' =>
            $q
                ->orderBy(
                    'images_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Даты.
             */
            'publishedAtAsc',
            'dateAsc' =>
            $q
                ->orderBy(
                    'school_assignments.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'publishedAtDesc',
            'dateDesc' =>
            $q
                ->orderBy(
                    'school_assignments.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'dueAtAsc' =>
            $q
                ->orderBy(
                    'school_assignments.due_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'dueAtDesc' =>
            $q
                ->orderBy(
                    'school_assignments.due_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'createdAtAsc' =>
            $q
                ->orderBy(
                    'school_assignments.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'createdAtDesc' =>
            $q
                ->orderBy(
                    'school_assignments.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'updatedAtAsc' =>
            $q
                ->orderBy(
                    'school_assignments.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'updatedAtDesc' =>
            $q
                ->orderBy(
                    'school_assignments.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Активность.
             */
            'activityAsc' =>
            $q
                ->orderBy(
                    'school_assignments.activity',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'activityDesc' =>
            $q
                ->orderBy(
                    'school_assignments.activity',
                    'desc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'activity' =>
            $q
                ->where(
                    'school_assignments.activity',
                    true
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'inactive' =>
            $q
                ->where(
                    'school_assignments.activity',
                    false
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            /**
             * Позиции.
             */
            'left' =>
            $q
                ->where(
                    'school_assignments.left',
                    true
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'noLeft' =>
            $q
                ->where(
                    'school_assignments.left',
                    false
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'main' =>
            $q
                ->where(
                    'school_assignments.main',
                    true
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'noMain' =>
            $q
                ->where(
                    'school_assignments.main',
                    false
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'right' =>
            $q
                ->where(
                    'school_assignments.right',
                    true
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            'noRight' =>
            $q
                ->where(
                    'school_assignments.right',
                    false
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),

            default =>
            $q
                ->orderBy(
                    'school_assignments.sort',
                    'asc'
                )
                ->orderByDesc(
                    'school_assignments.id'
                ),
        };
    }

    /* ======================== Accessors ======================== */

    /** Просрочено ли задание */
    public function getIsOverdueAttribute(): bool
    {
        return !is_null($this->due_at) && $this->due_at->isPast();
    }

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolAssignmentImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_assignment_has_images.order', 'asc')
            ->first();
    }
}
