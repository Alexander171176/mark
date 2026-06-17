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
    /** Страница с универсальными графиками */
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
            $entityKey = $config['default_entity'] ?? array_key_first($entities);
        }

        $entity = $entities[$entityKey];
        $availableMetrics = $this->availableMetrics($entity, $metrics);

        $metricKey = (string) $request->query(
            'metric',
            array_key_first($availableMetrics)
        );

        if (!isset($availableMetrics[$metricKey])) {
            $metricKey = array_key_first($availableMetrics);
        }

        $metric = $availableMetrics[$metricKey];
        $locale = app()->getLocale();

        return Inertia::render('Admin/Statistics/Charts/Index', [
            'entities' => $this->entityOptions($entities),
            'metrics' => $this->metricOptions($availableMetrics),
            'filters' => [
                'entity' => $entityKey,
                'metric' => $metricKey,
            ],
            'chart' => [
                'title' => $entity['label'] . ' — ' . $metric['label'],
                'entity' => $entityKey,
                'metric' => $metricKey,
                'data' => $this->chartData($entity, $metricKey, $metric, $locale),
            ],
        ]);
    }

    /** Список сущностей для select */
    private function entityOptions(array $entities): array
    {
        return collect($entities)
            ->map(fn ($entity, $key) => [
                'value' => $key,
                'label' => $entity['label'] ?? $key,
            ])
            ->values()
            ->all();
    }

    /** Список метрик для select */
    private function metricOptions(array $metrics): array
    {
        return collect($metrics)
            ->map(fn ($metric, $key) => [
                'value' => $key,
                'label' => $metric['label'] ?? $key,
            ])
            ->values()
            ->all();
    }

    /** Доступные метрики для выбранной сущности */
    private function availableMetrics(array $entity, array $metrics): array
    {
        return collect($metrics)
            ->filter(function ($metric, $key) use ($entity) {
                return match ($key) {
                    'views' => (bool) ($entity['has_views'] ?? false),
                    'likes' => (bool) ($entity['has_likes'] ?? false),
                    'activity' => (bool) ($entity['has_activity'] ?? false),
                    'created', 'updated' => true,
                    default => true,
                };
            })
            ->all();
    }

    /** Получить данные графика */
    private function chartData(array $entity, string $metricKey, array $metric, string $locale): array
    {
        return match ($metric['type']) {
            'boolean' => $this->booleanData($entity, $metric),
            'date_count' => $this->dateCountData($entity, $metric),
            'count_relation' => $this->relationCountData($entity, $metric, $locale),
            default => $this->fieldTopData($entity, $metricKey, $metric, $locale),
        };
    }

    /** Топ по числовому полю: views */
    private function fieldTopData(array $entity, string $metricKey, array $metric, string $locale): array
    {
        $table = $entity['table'];
        $field = $metric['field'];

        $query = DB::table($table)
            ->leftJoin($entity['title_table'] . ' as tr', function ($join) use ($table, $entity, $locale) {
                $join->on('tr.' . $entity['title_foreign_key'], '=', $table . '.id')
                    ->where('tr.locale', '=', $locale);
            })
            ->select([
                $table . '.id',
                DB::raw("COALESCE(tr.{$entity['title_column']}, CONCAT('ID: ', {$table}.id)) as label"),
                DB::raw("COALESCE({$table}.{$field}, 0) as value"),
            ])
            ->orderBy($table . '.id')
            ->limit(100);

        return $query->get()->map(fn ($item) => [
            'id' => $item->id,
            'label' => $item->label,
            'value' => (int) $item->value,
        ])->all();
    }

    /** Количество лайков через relation withCount */
    private function relationCountData(array $entity, array $metric, string $locale): array
    {
        /** @var class-string $model */
        $model = $entity['model'];
        $relation = $entity['likes_relation'] ?? 'likes';

        /** @var Builder $query */
        $query = $model::query()
            ->with(['translations'])
            ->withCount($relation)
            ->orderBy('id')
            ->limit(100);

        return $query->get()->map(function ($item) use ($entity, $relation, $locale) {
            $translation = $item->translations
                ? $item->translations->firstWhere('locale', $locale)
                : null;

            $label = $translation?->{$entity['title_column']}
                ?: 'ID: ' . $item->id;

            return [
                'id' => $item->id,
                'label' => $label,
                'value' => (int) ($item->{$relation . '_count'} ?? 0),
            ];
        })->all();
    }

    /** Активные / неактивные */
    private function booleanData(array $entity, array $metric): array
    {
        $table = $entity['table'];
        $field = $metric['field'];

        $rows = DB::table($table)
            ->select([
                DB::raw("CASE WHEN {$field} = 1 THEN 'Активные' ELSE 'Неактивные' END as label"),
                DB::raw('COUNT(*) as value'),
            ])
            ->groupBy($field)
            ->orderByDesc('value')
            ->get();

        return $rows->map(fn ($item) => [
            'label' => $item->label,
            'value' => (int) $item->value,
        ])->all();
    }

    /** Количество записей по датам */
    private function dateCountData(array $entity, array $metric): array
    {
        $table = $entity['table'];
        $field = $metric['field'];

        $rows = DB::table($table)
            ->select([
                DB::raw("DATE({$field}) as label"),
                DB::raw('COUNT(*) as value'),
            ])
            ->whereNotNull($field)
            ->groupBy(DB::raw("DATE({$field})"))
            ->orderBy('label')
            ->limit(60)
            ->get();

        return $rows->map(fn ($item) => [
            'label' => $item->label,
            'value' => (int) $item->value,
        ])->all();
    }
}
