<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RobotController extends Controller
{
    protected string $path = 'robots.txt';

    /**
     * Просмотр и настройка robots.txt.
     *
     * Доступ разрешён только суперпользователю с ID = 1.
     */
    public function index(Request $request): Response
    {
        abort_unless(
            (int) $request->user()?->id === 1,
            403
        );

        $path = public_path($this->path);

        $content = File::exists($path)
            ? File::get($path)
            : '';

        $parsed = $this->parseRobots($content);

        return Inertia::render(
            'Admin/System/RobotEditPage',
            [
                'content' => $content,

                'groups' => $parsed['groups'],

                'sitemaps' => $parsed['sitemaps'],

                'blockAll' => $parsed['block_all'],
            ]
        );
    }

    /**
     * Сохранение robots.txt.
     */
    public function update(Request $request): RedirectResponse
    {
        abort_unless(
            (int) $request->user()?->id === 1,
            403
        );

        $request->validate([
            'editor_mode' => [
                'required',
                Rule::in([
                    'builder',
                    'manual',
                ]),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ручной режим
        |--------------------------------------------------------------------------
        |
        | В ручном режиме содержимое robots.txt сохраняется без автоматической
        | генерации. Это позволяет использовать любые дополнительные директивы.
        |
        */

        if ($request->input('editor_mode') === 'manual') {
            $validated = $request->validate([
                'content' => [
                    'nullable',
                    'string',
                ],
            ]);

            $content = $validated['content'] ?? '';

            $this->writeRobots($content);

            return redirect()
                ->route('admin.robot.index')
                ->with(
                    'success',
                    'Файл robots.txt успешно обновлён.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Режим конструктора
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'block_all' => [
                'required',
                'boolean',
            ],

            'groups' => [
                'required',
                'array',
                'min:1',
            ],

            'groups.*.user_agents' => [
                'required',
                'array',
                'min:1',
            ],

            'groups.*.user_agents.*' => [
                'required',
                'string',
                'max:255',
            ],

            'groups.*.allow' => [
                'nullable',
                'array',
            ],

            'groups.*.allow.*' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'groups.*.disallow' => [
                'nullable',
                'array',
            ],

            'groups.*.disallow.*' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'groups.*.clean_params' => [
                'nullable',
                'array',
            ],

            'groups.*.clean_params.*.params' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'groups.*.clean_params.*.path' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'sitemaps' => [
                'nullable',
                'array',
            ],

            'sitemaps.*' => [
                'nullable',
                'url',
                'max:2048',
            ],
        ]);

        $content = $this->buildRobots(
            $validated
        );

        $this->writeRobots($content);

        return redirect()
            ->route('admin.robot.index')
            ->with(
                'success',
                'Файл robots.txt успешно обновлён.'
            );
    }

    /**
     * Запись robots.txt.
     */
    protected function writeRobots(string $content): void
    {
        /*
         * Приводим конец файла к одному переводу строки.
         */
        $content = rtrim(
                $content,
                "\r\n"
            ) . PHP_EOL;

        $result = File::put(
            public_path($this->path),
            $content
        );

        abort_if(
            $result === false,
            500,
            'Не удалось сохранить файл robots.txt.'
        );
    }

    /**
     * Разбор существующего robots.txt.
     */
    protected function parseRobots(string $content): array
    {
        $groups = [];
        $sitemaps = [];

        $currentGroupIndex = null;

        $lines = preg_split(
            '/\r\n|\r|\n/',
            $content
        );

        foreach ($lines as $line) {
            $line = trim($line);

            if (
                $line === '' ||
                str_starts_with($line, '#')
            ) {
                continue;
            }

            if (!str_contains($line, ':')) {
                continue;
            }

            [$directive, $value] = array_map(
                'trim',
                explode(':', $line, 2)
            );

            $directive = strtolower(
                $directive
            );

            switch ($directive) {
                case 'user-agent':
                    if (
                        $currentGroupIndex === null ||
                        $this->groupHasRules(
                            $groups[$currentGroupIndex]
                        )
                    ) {
                        $groups[] = [
                            'user_agents' => [],
                            'allow' => [],
                            'disallow' => [],
                            'clean_params' => [],
                        ];

                        $currentGroupIndex =
                            array_key_last($groups);
                    }

                    if ($value !== '') {
                        $groups[$currentGroupIndex]['user_agents'][] =
                            $value;
                    }

                    break;

                case 'allow':
                    $this->ensureGroup(
                        $groups,
                        $currentGroupIndex
                    );

                    if ($value !== '') {
                        $groups[$currentGroupIndex]['allow'][] =
                            $value;
                    }

                    break;

                case 'disallow':
                    $this->ensureGroup(
                        $groups,
                        $currentGroupIndex
                    );

                    if ($value !== '') {
                        $groups[$currentGroupIndex]['disallow'][] =
                            $value;
                    }

                    break;

                case 'clean-param':
                    $this->ensureGroup(
                        $groups,
                        $currentGroupIndex
                    );

                    if ($value === '') {
                        break;
                    }

                    $parts = preg_split(
                        '/\s+/',
                        $value,
                        2
                    );

                    $groups[$currentGroupIndex]['clean_params'][] = [
                        'params' => $parts[0] ?? '',
                        'path' => $parts[1] ?? '',
                    ];

                    break;

                case 'sitemap':
                    if ($value !== '') {
                        $sitemaps[] = $value;
                    }

                    break;
            }
        }

        if (empty($groups)) {
            $groups[] = [
                'user_agents' => [
                    '*',
                ],
                'allow' => [],
                'disallow' => [],
                'clean_params' => [],
            ];
        }

        foreach ($groups as &$group) {
            if (empty($group['user_agents'])) {
                $group['user_agents'] = [
                    '*',
                ];
            }

            $group['user_agents'] = array_values(
                array_unique(
                    $group['user_agents']
                )
            );

            $group['allow'] = array_values(
                array_unique(
                    $group['allow']
                )
            );

            $group['disallow'] = array_values(
                array_unique(
                    $group['disallow']
                )
            );
        }

        unset($group);

        $sitemaps = array_values(
            array_unique($sitemaps)
        );

        return [
            'groups' => $groups,

            'sitemaps' => $sitemaps,

            'block_all' => $this->isBlockAll(
                $groups
            ),
        ];
    }

    /**
     * Генерация robots.txt.
     */
    protected function buildRobots(array $data): string
    {
        $lines = [];

        $lines[] = '# robots.txt';
        $lines[] = '# Generated from admin panel';
        $lines[] = '';

        if ($data['block_all']) {
            $lines[] = 'User-agent: *';
            $lines[] = 'Disallow: /';
        } else {
            foreach ($data['groups'] as $group) {
                $userAgents = $this->cleanList(
                    $group['user_agents'] ?? []
                );

                $allow = $this->cleanList(
                    $group['allow'] ?? []
                );

                $disallow = $this->cleanList(
                    $group['disallow'] ?? []
                );

                if (empty($userAgents)) {
                    $userAgents = [
                        '*',
                    ];
                }

                foreach ($userAgents as $userAgent) {
                    $lines[] =
                        'User-agent: ' . $userAgent;
                }

                foreach ($disallow as $path) {
                    $lines[] =
                        'Disallow: ' . $path;
                }

                foreach ($allow as $path) {
                    $lines[] =
                        'Allow: ' . $path;
                }

                foreach (
                    $group['clean_params'] ?? []
                    as $cleanParam
                ) {
                    $params = trim(
                        $cleanParam['params'] ?? ''
                    );

                    $path = trim(
                        $cleanParam['path'] ?? ''
                    );

                    if ($params === '') {
                        continue;
                    }

                    $line =
                        'Clean-param: ' . $params;

                    if ($path !== '') {
                        $line .= ' ' . $path;
                    }

                    $lines[] = $line;
                }

                $lines[] = '';
            }
        }

        $sitemaps = $this->cleanList(
            $data['sitemaps'] ?? []
        );

        if (!empty($sitemaps)) {
            if (
                !empty($lines) &&
                end($lines) !== ''
            ) {
                $lines[] = '';
            }

            foreach ($sitemaps as $sitemap) {
                $lines[] =
                    'Sitemap: ' . $sitemap;
            }
        }

        while (
            !empty($lines) &&
            end($lines) === ''
        ) {
            array_pop($lines);
        }

        return implode(
                PHP_EOL,
                $lines
            ) . PHP_EOL;
    }

    /**
     * Создание группы по умолчанию,
     * если директива обнаружена раньше User-agent.
     */
    protected function ensureGroup(
        array &$groups,
        ?int &$currentGroupIndex
    ): void {
        if ($currentGroupIndex !== null) {
            return;
        }

        $groups[] = [
            'user_agents' => [
                '*',
            ],
            'allow' => [],
            'disallow' => [],
            'clean_params' => [],
        ];

        $currentGroupIndex =
            array_key_last($groups);
    }

    /**
     * Проверка наличия правил внутри группы.
     */
    protected function groupHasRules(
        array $group
    ): bool {
        return
            !empty($group['allow']) ||
            !empty($group['disallow']) ||
            !empty($group['clean_params']);
    }

    /**
     * Определение режима полного запрета.
     */
    protected function isBlockAll(
        array $groups
    ): bool {
        if (count($groups) !== 1) {
            return false;
        }

        $group = $groups[0];

        return
            ($group['user_agents'] ?? []) === ['*'] &&
            ($group['disallow'] ?? []) === ['/'] &&
            empty($group['allow']) &&
            empty($group['clean_params']);
    }

    /**
     * Очистка массива от пустых
     * и повторяющихся значений.
     */
    protected function cleanList(
        array $items
    ): array {
        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($item) =>
                        trim((string) $item),
                        $items
                    ),
                    static fn ($item) =>
                        $item !== ''
                )
            )
        );
    }
}
