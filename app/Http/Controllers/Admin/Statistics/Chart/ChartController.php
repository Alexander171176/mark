<?php

namespace App\Http\Controllers\Admin\Statistics\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ChartController extends Controller
{
    /**
     * Количество элементов графика на одной странице.
     */
    private const PER_PAGE = 20;

    /**
     * Страница с универсальными графиками.
     */
    public function index(Request $request): Response
    {
        $config = config('admin_charts');

        $entities = $config['entities'] ?? [];
        $metrics = $config['metrics'] ?? [];

        $entityKey = (string) $request->query(
            'entity',
            $config['default_entity'] ?? array_key_first($entities)
        );

        if (!isset($entities[$entityKey])) {
            $entityKey =
                $config['default_entity'] ??
                array_key_first($entities);
        }

        $entity = $entities[$entityKey];

        $availableMetrics = $this->availableMetrics(
            $entity,
            $metrics
        );

        $metricKey = (string) $request->query(
            'metric',
            array_key_first($availableMetrics)
        );

        if (!isset($availableMetrics[$metricKey])) {
            $metricKey = array_key_first(
                $availableMetrics
            );
        }

        $metric = $availableMetrics[$metricKey];

        $locale = app()->getLocale();

        $page = max(
            1,
            (int) $request->query(
                'page',
                1
            )
        );

        $chartResult = $this->chartData(
            $entity,
            $metricKey,
            $metric,
            $locale,
            $page,
            self::PER_PAGE
        );

        return Inertia::render(
            'Admin/Statistics/Charts/Index',
            [
                'entities' =>
                    $this->entityOptions(
                        $entities
                    ),

                'metrics' =>
                    $this->metricOptions(
                        $availableMetrics
                    ),

                'filters' => [
                    'entity' => $entityKey,
                    'metric' => $metricKey,
                    'page' => $page,
                ],

                'chart' => [
                    'entity_label_key' =>
                        $entity['label_key'] ??
                        $entityKey,

                    'metric_label_key' =>
                        $metric['label_key'] ??
                        $metricKey,

                    'entity' => $entityKey,
                    'metric' => $metricKey,

                    'data' =>
                        $chartResult['data'],

                    'pagination' =>
                        $chartResult['pagination'],
                ],
            ]
        );
    }

    /**
     * Список сущностей для select.
     */
    private function entityOptions(
        array $entities
    ): array {
        return collect($entities)
            ->map(
                fn ($entity, $key) => [
                    'value' => $key,
                    'label_key' =>
                        $entity['label_key'] ??
                        $key,
                ]
            )
            ->values()
            ->all();
    }

    /**
     * Список метрик для select.
     */
    private function metricOptions(
        array $metrics
    ): array {
        return collect($metrics)
            ->map(
                fn ($metric, $key) => [
                    'value' => $key,
                    'label_key' =>
                        $metric['label_key'] ??
                        $key,
                ]
            )
            ->values()
            ->all();
    }

    /**
     * Доступные метрики
     * для выбранной сущности.
     */
    private function availableMetrics(
        array $entity,
        array $metrics
    ): array {
        return collect($metrics)
            ->filter(
                function (
                    $metric,
                    $key
                ) use ($entity) {
                    return match ($key) {
                        'views' =>
                        (bool) (
                            $entity['has_views'] ??
                            false
                        ),

                        'likes' =>
                        (bool) (
                            $entity['has_likes'] ??
                            false
                        ),

                        'activity' =>
                        (bool) (
                            $entity['has_activity'] ??
                            false
                        ),

                        'created',
                        'updated' => true,

                        default => true,
                    };
                }
            )
            ->all();
    }

    /**
     * Получить данные графика.
     */
    private function chartData(
        array $entity,
        string $metricKey,
        array $metric,
        string $locale,
        int $page,
        int $perPage
    ): array {
        return match ($metric['type']) {
            'boolean' => [
                'data' =>
                    $this->booleanData(
                        $entity,
                        $metric
                    ),

                'pagination' => null,
            ],

            'date_count' =>
            $this->dateCountData(
                $entity,
                $metric,
                $page,
                $perPage
            ),

            'count_relation' =>
            $this->relationCountData(
                $entity,
                $metric,
                $locale,
                $page,
                $perPage
            ),

            default =>
            $this->fieldTopData(
                $entity,
                $metricKey,
                $metric,
                $locale,
                $page,
                $perPage
            ),
        };
    }

    /**
     * Топ по числовому полю.
     *
     * Например: views.
     */
    private function fieldTopData(
        array $entity,
        string $metricKey,
        array $metric,
        string $locale,
        int $page,
        int $perPage
    ): array {
        $table = $entity['table'];
        $field = $metric['field'];

        $query = DB::table($table)
            ->leftJoin(
                $entity['title_table'] . ' as tr',
                function (
                    $join
                ) use (
                    $table,
                    $entity,
                    $locale
                ) {
                    $join
                        ->on(
                            'tr.' .
                            $entity['title_foreign_key'],
                            '=',
                            $table . '.id'
                        )
                        ->where(
                            'tr.locale',
                            '=',
                            $locale
                        );
                }
            )
            ->select([
                $table . '.id',

                DB::raw(
                    "COALESCE(
                        tr.{$entity['title_column']},
                        CONCAT('ID: ', {$table}.id)
                    ) as label"
                ),

                DB::raw(
                    "COALESCE(
                        {$table}.{$field},
                        0
                    ) as value"
                ),
            ])
            ->orderBy(
                $table . '.id'
            );

        $paginator = $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

        $data = $paginator
            ->getCollection()
            ->map(
                fn ($item) => [
                    'id' => $item->id,
                    'label' => $item->label,
                    'value' =>
                        (int) $item->value,
                ]
            )
            ->values()
            ->all();

        return [
            'data' => $data,

            'pagination' => [
                'current_page' =>
                    $paginator->currentPage(),

                'last_page' =>
                    $paginator->lastPage(),

                'per_page' =>
                    $paginator->perPage(),

                'total' =>
                    $paginator->total(),

                'from' =>
                    $paginator->firstItem(),

                'to' =>
                    $paginator->lastItem(),
            ],
        ];
    }

    /**
     * Количество через relation withCount.
     *
     * Например: likes.
     */
    private function relationCountData(
        array $entity,
        array $metric,
        string $locale,
        int $page,
        int $perPage
    ): array {
        /** @var class-string $model */
        $model = $entity['model'];

        $relation =
            $entity['likes_relation'] ??
            'likes';

        /** @var Builder $query */
        $query = $model::query()
            ->with([
                'translations' =>
                    function ($query) use (
                        $locale
                    ) {
                        $query->where(
                            'locale',
                            $locale
                        );
                    },
            ])
            ->withCount($relation)
            ->orderBy('id');

        $paginator = $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

        $data = $paginator
            ->getCollection()
            ->map(
                function (
                    $item
                ) use (
                    $entity,
                    $relation
                ) {
                    $translation =
                        $item->translations
                            ?->first();

                    $label =
                        $translation
                            ?->{$entity['title_column']}
                            ?: 'ID: ' . $item->id;

                    return [
                        'id' => $item->id,
                        'label' => $label,

                        'value' =>
                            (int) (
                                $item->{
                                $relation .
                                '_count'
                                } ?? 0
                            ),
                    ];
                }
            )
            ->values()
            ->all();

        return [
            'data' => $data,

            'pagination' => [
                'current_page' =>
                    $paginator->currentPage(),

                'last_page' =>
                    $paginator->lastPage(),

                'per_page' =>
                    $paginator->perPage(),

                'total' =>
                    $paginator->total(),

                'from' =>
                    $paginator->firstItem(),

                'to' =>
                    $paginator->lastItem(),
            ],
        ];
    }

    /**
     * Активные / неактивные.
     */
    private function booleanData(
        array $entity,
        array $metric
    ): array {
        $table = $entity['table'];
        $field = $metric['field'];

        $rows = DB::table($table)
            ->select([
                $field,

                DB::raw(
                    'COUNT(*) as value'
                ),
            ])
            ->groupBy($field)
            ->orderByDesc('value')
            ->get();

        return $rows
            ->map(
                function ($item) use ($field) {
                    $active =
                        (bool) $item->{$field};

                    return [
                        'label' =>
                            $active
                                ? 'active'
                                : 'inactive',

                        'translate' => true,

                        'value' =>
                            (int) $item->value,
                    ];
                }
            )
            ->all();
    }

    /**
     * Количество записей по датам.
     *
     * Например: created_at, updated_at.
     */
    private function dateCountData(
        array $entity,
        array $metric,
        int $page,
        int $perPage
    ): array {
        $table = $entity['table'];
        $field = $metric['field'];

        $query = DB::table($table)
            ->select([
                DB::raw(
                    "DATE({$field}) as label"
                ),

                DB::raw(
                    'COUNT(*) as value'
                ),
            ])
            ->whereNotNull($field)
            ->groupBy(
                DB::raw(
                    "DATE({$field})"
                )
            )
            ->orderBy('label');

        $paginator = $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

        $data = $paginator
            ->getCollection()
            ->map(
                fn ($item) => [
                    'label' => $item->label,

                    'value' =>
                        (int) $item->value,
                ]
            )
            ->values()
            ->all();

        return [
            'data' => $data,

            'pagination' => [
                'current_page' =>
                    $paginator->currentPage(),

                'last_page' =>
                    $paginator->lastPage(),

                'per_page' =>
                    $paginator->perPage(),

                'total' =>
                    $paginator->total(),

                'from' =>
                    $paginator->firstItem(),

                'to' =>
                    $paginator->lastItem(),
            ],
        ];
    }
}
