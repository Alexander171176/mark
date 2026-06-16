<?php

namespace App\Http\Controllers\Admin\System\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\System\Setting\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

// Используем ресурсы для JSON ответа, если он нужен в таком виде
// Для streamDownload
// Для кэширования локали
// Для обработки ошибок генерации

class ReportController extends Controller
{
    // Время кэширования локали (в секундах)
    private const LOCALE_CACHE_TTL = 3600;

    /** Отображает страницу отчетов или отдает JSON с данными. */
    public function index(Request $request): JsonResponse|InertiaResponse
    {
        $tables = $this->getDatabaseTables();

        $selectedTable = $request->query('table', $tables[0] ?? null);

        if (!$selectedTable) {
            return Inertia::render('Admin/System/Reports/Index', [
                'tables' => [],
                'selectedTable' => null,
                'columns' => [],
                'items' => [],
            ]);
        }

        $selectedTable = $this->validateTable($selectedTable);

        $columns = Schema::getColumnListing($selectedTable);

        $items = DB::table($selectedTable)
            ->limit(500)
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'tables' => $tables,
                'selectedTable' => $selectedTable,
                'columns' => $columns,
                'data' => $items,
            ]);
        }

        return Inertia::render('Admin/System/Reports/Index', [
            'tables' => $tables,
            'selectedTable' => $selectedTable,
            'columns' => $columns,
            'items' => $items,
        ]);
    }

    /** Экспорт отчёта в выбранном формате. */
    public function download(Request $request): StreamedResponse
    {
        // TODO: Проверка прав 'download-reports'
        // $this->authorize('download-reports');

        // Валидация входных данных
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['rubrics', 'articles'])],
            'format' => ['required', 'string', Rule::in(['csv', 'xls', 'pdf', 'zip'])],
            // TODO: Добавить валидацию других фильтров, если они есть (даты, локаль и т.д.)
            // 'locale' => ['sometimes', 'string', Rule::in(['ru', 'en', 'kz'])],
        ]);

        $type = $validated['type'];
        $format = $validated['format'];
        $locale = $request->query('locale', $this->getCurrentLocale()); // Берем из запроса или текущую

        Log::info("Запрошен экспорт отчета",
            ['type' => $type, 'format' => $format, 'locale' => $locale]);

        try {
            // Получаем данные (загружаем ВСЕ для экспорта)
            $query = $this->getBaseReportQuery($type, $locale);
            // Для больших отчетов рассмотрите ->cursor() или ->chunk() вместо ->get()
            $data = $query->get();

            if ($data->isEmpty()) {
                // TODO: Вернуть сообщение пользователю, что данных для экспорта нет
                // Возможно, редирект назад с ошибкой?
                return response()
                    ->streamDownload(function () { echo "Нет данных для экспорта."; }, "empty_report.txt");
            }

            $filename = "report_{$type}_{$locale}_" . now()->format('YmdHis') . ".{$format}";
            $content = $this->generateReportContent($data, $format, $type); // Генерация контента

            // Установка правильных заголовков
            $headers = $this->getDownloadHeaders($format);

            return Response::streamDownload(function () use ($content) {
                echo $content;
            }, $filename, $headers);

        } catch (InvalidArgumentException $e) {
            Log::warning("Неподдерживаемый формат отчета запрошен: " . $format);
            abort(400, $e->getMessage()); // Возвращаем ошибку клиенту
        } catch (Throwable $e) {
            Log::error("Ошибка при генерации или загрузке отчета: "
                . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // TODO: Вернуть пользователю сообщение об ошибке генерации отчета
            abort(500, 'Ошибка при генерации отчета.');
        }
    }

    /** Формирует базовый запрос для получения данных отчета. */
    private function getBaseReportQuery(string $type, string $locale): Builder
    {
        return match ($type) {
            'rubrics' => BlogRubric::query()
                ->with([
                    'translations' => fn ($q) => $q->where('locale', $locale),
                    'articles' => fn ($q) => $q
                        ->where('activity', true)
                        ->whereHas('translations', fn ($tq) => $tq->where('locale', $locale))
                        ->with([
                            'translations' => fn ($tq) => $tq->where('locale', $locale),
                        ])
                        ->withCount('comments'),
                ])
                ->whereHas('translations', fn ($q) => $q->where('locale', $locale))
                ->orderBy('sort')
                ->orderByDesc('id'),

            'articles' => BlogArticle::query()
                ->with([
                    'translations' => fn ($q) => $q->where('locale', $locale),
                    'rubrics.translations' => fn ($q) => $q->where('locale', $locale),
                    'tags.translations' => fn ($q) => $q->where('locale', $locale),
                    'images',
                ])
                ->withCount([
                    'comments',
                    'likes',
                ])
                ->where('activity', true)
                ->whereHas('translations', fn ($q) => $q->where('locale', $locale))
                ->orderBy('sort')
                ->orderByDesc('id'),

            'page' => BlogArticle::query()
                ->with([
                    'translations' => fn ($q) => $q->where('locale', $locale),
                ])
                ->whereHas('translations', fn ($q) => $q->where('locale', $locale)),

            default => throw new InvalidArgumentException('Неподдерживаемый тип отчета: ' . $type),
        };
    }

    /** Генерирует контент отчета в указанном формате. */
    private function generateReportContent($data, string $format, string $type): string
    {
        if ($data->isEmpty()) return '';

        switch ($format) {
            case 'csv':
                return $this->generateCsv($data, $type);
            case 'xls':
                // TODO: Реализовать генерацию XLS
                Log::warning("Генерация XLS не реализована для отчета типа {$type}");
                return "XLS generation not implemented yet."; // Заглушка
            case 'pdf':
                // TODO: Реализовать генерацию PDF
                Log::warning("Генерация PDF не реализована для отчета типа {$type}");
                return "PDF generation not implemented yet."; // Заглушка
            case 'zip':
                // TODO: Реализовать генерацию ZIP
                Log::warning("Генерация ZIP не реализована для отчета типа {$type}");
                return "ZIP generation not implemented yet."; // Заглушка
            default:
                // Исключение уже было выброшено в download() или getBaseReportQuery()
                return '';
        }
    }

    /** Генерирует CSV контент. */
    private function generateCsv($data, string $type): string
    {
        // TODO: Улучшить генерацию CSV - выбирать конкретные колонки, разворачивать связи
        $temp = fopen('php://temp', 'r+');
        if ($data->isNotEmpty()) {
            // Получаем заголовки из атрибутов первой модели + имена связей (если нужно)
            $firstItem = $data->first()->toArray(); // Используем toArray ресурса или модели
            // Убираем сложные связи из заголовков CSV по умолчанию
            $headers = array_keys(collect($firstItem)->filter(fn($value) => !is_array($value))->all());
            fputcsv($temp, $headers, ';'); // Используем ';' как разделитель

            foreach ($data as $item) {
                // Преобразуем объект в массив
                $itemArray = $item->toArray();
                // Оставляем только скалярные значения для CSV
                $row = collect($itemArray)->filter(fn($value) => !is_array($value))->all();
                // Приводим булевы значения к 1/0 или Да/Нет
                foreach ($row as $key => &$value) {
                    if (is_bool($value)) {
                        $value = $value ? '1' : '0';
                    }
                }
                fputcsv($temp, $row, ';');
            }
        }
        rewind($temp);
        $csvContent = stream_get_contents($temp);
        fclose($temp);
        // Убедимся, что контент в правильной кодировке (например, Windows-1251 для Excel)
        return mb_convert_encoding($csvContent, 'Windows-1251', 'UTF-8');
        // Или return $csvContent; если UTF-8 достаточно
    }

    /** Возвращает заголовки для скачивания файла. */
    private function getDownloadHeaders(string $format): array
    {
        $contentType = match ($format) {
            'csv' => 'text/csv; charset=Windows-1251', // Добавляем charset
            'xls' => 'application/vnd.ms-excel',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            default => 'application/octet-stream',
        };
        return ['Content-Type' => $contentType];
    }

    /** Получает текущую локаль из настроек (с кэшированием). */
    private function getCurrentLocale(): string
    {
        return Cache::remember('setting_locale', self::LOCALE_CACHE_TTL, function () {
            return Setting::where('option', 'locale')->value('value') ?? config('app.fallback_locale', 'ru');
        });
    }

    private function getDatabaseTables(): array
    {
        $database = config('database.connections.mysql.database');

        return collect(DB::select('SHOW TABLES'))
            ->map(function ($row) use ($database) {
                $key = "Tables_in_{$database}";
                return $row->$key;
            })
            ->filter(fn ($table) => !str_starts_with($table, 'migrations'))
            ->values()
            ->toArray();
    }

    private function validateTable(string $table): string
    {
        $tables = $this->getDatabaseTables();

        if (!in_array($table, $tables, true)) {
            abort(404, 'Таблица не найдена.');
        }

        return $table;
    }
}
