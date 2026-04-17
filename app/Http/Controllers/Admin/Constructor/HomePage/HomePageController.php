<?php

namespace App\Http\Controllers\Admin\Constructor\HomePage;

use App\Http\Controllers\Controller;
use App\Models\Admin\Constructor\HomePage\Component\ComponentSection;
use App\Models\Admin\Constructor\HomePage\Demo\DemoSection;
use App\Models\Admin\Constructor\HomePage\Developer\DeveloperSection;
use App\Models\Admin\Constructor\HomePage\Feature\FeatureSection;
use App\Models\Admin\Constructor\HomePage\Hero\HeroSection;
use App\Models\Admin\Constructor\HomePage\Quality\QualitySection;
use App\Models\Admin\Constructor\HomePage\Quickstart\QuickstartSection;
use App\Models\Admin\Constructor\HomePage\Reason\ReasonSection;
use App\Models\Admin\Constructor\HomePage\Wave\WaveSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class HomePageController extends Controller
{
    public function index(): Response
    {
        $locale = app()->getLocale();

        $hero = HeroSection::where('locale', $locale)->firstOrCreate(
            ['locale' => $locale],
            ['subtitle' => null, 'title' => null, 'sort' => 0, 'activity' => true]
        );

        $defs = [
            ['type' => 'wave',       'class' => WaveSection::class,      'defaultSort' => 10],
            ['type' => 'feature',    'class' => FeatureSection::class,   'defaultSort' => 20],
            ['type' => 'developer',  'class' => DeveloperSection::class, 'defaultSort' => 30],
            ['type' => 'quickstart', 'class' => QuickstartSection::class,'defaultSort' => 40],
            ['type' => 'demo',       'class' => DemoSection::class,      'defaultSort' => 50],
            ['type' => 'quality',    'class' => QualitySection::class,   'defaultSort' => 60],
            ['type' => 'component',  'class' => ComponentSection::class, 'defaultSort' => 70],
            ['type' => 'reason',     'class' => ReasonSection::class,    'defaultSort' => 80],
        ];

        $blocks = [];
        foreach ($defs as $d) {
            $m = $d['class']::where('locale', $locale)->firstOrCreate(
                ['locale' => $locale],
                ['sort' => $d['defaultSort'], 'activity' => true]
            );
            $blocks[] = [
                'type'     => $d['type'],
                'id'       => $m->id,
                'sort'     => (int)($m->sort ?? $d['defaultSort']),
                'activity' => (bool)($m->activity ?? true),   // <-- добавили
            ];
        }

        usort($blocks, fn ($a, $b) => $a['sort'] <=> $b['sort']);

        return Inertia::render('Admin/Constructor/HomePage/Index', [
            'heroSectionId' => $hero->id,
            'blocks'        => $blocks,
        ]);
    }

    public function updateSort(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'blocks'        => ['required','array'],
            'blocks.*.id'   => ['required','integer'],
            'blocks.*.type' => ['required','string','in:wave,feature,developer,quickstart,demo,quality,component,reason'],
            'blocks.*.sort' => ['required','integer','min:0'],
        ]);

        $locale = app()->getLocale();

        $map = [
            'wave'       => WaveSection::class,
            'feature'    => FeatureSection::class,
            'developer'  => DeveloperSection::class,
            'quickstart' => QuickstartSection::class,
            'demo'       => DemoSection::class,
            'quality'    => QualitySection::class,
            'component'  => ComponentSection::class,
            'reason'     => ReasonSection::class,
        ];

        try {
            DB::transaction(function () use ($validated, $locale, $map) {
                foreach ($validated['blocks'] as $b) {
                    $model = $map[$b['type']] ?? null;
                    if (!$model) {
                        throw new \InvalidArgumentException("Unknown section type: {$b['type']}");
                    }

                    $model::where('id', $b['id'])
                        ->where('locale', $locale)
                        ->update(['sort' => $b['sort']]);
                }
            });

            Log::info('HomePage: sort updated', ['count' => count($validated['blocks'])]);

            return back()->with('success', 'Порядок секций сохранён');
        } catch (Throwable $e) {
            Log::error('HomePage: updateSort failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'Не удалось сохранить порядок секций']);
        }
    }

    public function updateActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id'       => ['required', 'integer'],
            'type'     => ['required', 'string', Rule::in([
                'wave','feature','developer','quickstart','demo','quality','component','reason'
            ])],
            'activity' => ['required', 'boolean'],
        ]);

        $locale = app()->getLocale();

        $map = [
            'wave'       => WaveSection::class,
            'feature'    => FeatureSection::class,
            'developer'  => DeveloperSection::class,
            'quickstart' => QuickstartSection::class,
            'demo'       => DemoSection::class,
            'quality'    => QualitySection::class,
            'component'  => ComponentSection::class,
            'reason'     => ReasonSection::class,
        ];

        try {
            $model = $map[$validated['type']] ?? null;
            if (!$model) {
                return back()->withErrors(['general' => 'Unknown section type.']);
            }

            $row = $model::where('id', $validated['id'])
                ->where('locale', $locale)
                ->firstOrFail();

            $row->activity = (bool)$validated['activity'];
            $row->save();

            $msg = $row->activity ? 'Секция активирована' : 'Секция деактивирована';
            Log::info("HomePage section [{$validated['type']} #{$row->id}] activity => {$row->activity}");

            return back()->with('success', $msg);
        } catch (Throwable $e) {
            Log::error("HomePage section activity update error: ".$e->getMessage());
            return back()->withErrors(['general' => 'Не удалось обновить активность секции.']);
        }
    }
}
