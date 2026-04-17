<?php

namespace Database\Seeders;

use App\Models\Admin\School\QaMessage\QaMessage;
use App\Models\Admin\School\QaThread\QaThread;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Random\RandomException;

class QaMessageSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('qa_threads') || !Schema::hasTable('qa_messages') || !Schema::hasTable('users')) {
            $this->command?->warn('Нет таблиц qa_threads/qa_messages/users — пропускаю QaMessageSeeder.');
            return;
        }

        $threads = QaThread::query()->get(['id','user_id','status','is_locked','created_at','last_activity_at','last_reply_at','replies_count']);
        if ($threads->isEmpty()) {
            $this->command?->warn('Темы Q&A отсутствуют — сперва запусти QaThreadSeeder.');
            return;
        }

        $users = User::query()->get(['id','name']);
        if ($users->isEmpty()) {
            $this->command?->warn('Пользователи не найдены — пропускаю QaMessageSeeder.');
            return;
        }

        $faker   = fake();
        $created = 0; $updatedThreads = 0;

        foreach ($threads as $thread) {
            // 0) Удалим только ранее посеянные сообщения (не трогаем «живые»)
            QaMessage::query()
                ->where('thread_id', $thread->id)
                ->where('meta->seeded', true)
                ->delete();

            // Закрытые темы — меньше сообщений и без ответов иногда
            $targetCount = $thread->is_locked || $thread->status !== 'open'
                ? $faker->numberBetween(1, 3)
                : $faker->numberBetween(3, 4);

            // 1) Подберём участников: автор темы + 1–4 случайных других пользователей
            $participantIds = collect([$thread->user_id])->merge(
                $users->pluck('id')->reject(fn ($id) => $id === $thread->user_id)->shuffle()->take($faker->numberBetween(1, 4))
            )->unique()->values();

            // 2) Сгенерируем «временную шкалу» сообщений в пределах от created_at до «сейчас»
            $start = $thread->created_at?->copy() ?? now()->subDays(60);
            $end   = now();
            $timeStamps = $this->spreadTimestamps($start, $end, $targetCount);

            // 3) Создаём сообщения, часть будет ответами на предыдущие
            $allMessages = collect(); // Collection<QaMessage>
            $possibleParents = collect(); // кандидаты для ответов

            for ($i = 0; $i < $targetCount; $i++) {
                $ts = $timeStamps[$i];

                $isReply = $i > 0 && $faker->boolean(45) && $possibleParents->isNotEmpty();
                $parent  = $isReply ? $possibleParents->random() : null;

                $body = $faker->paragraphs($faker->numberBetween(1, 2), true);
                if ($faker->boolean(25)) {
                    $body .= ' ' . $faker->sentence();
                }

                $message = QaMessage::query()->create([
                    'thread_id'     => $thread->id,
                    'user_id'       => $participantIds->random(),
                    'parent_id'     => $parent?->id,
                    'body'          => $body,
                    'is_private'    => $faker->boolean(8) && !$thread->is_locked ? true : false,
                    'is_pinned'     => $faker->boolean(5) && !$isReply, // обычно пинят корни
                    'replies_count' => 0,
                    'edited_at'     => $faker->boolean(10) ? $ts->copy()->addMinutes($faker->numberBetween(5, 90)) : null,
                    'meta'          => [
                        'seeded'   => true,
                        'seed_run' => now()->toDateTimeString(),
                    ],
                    'created_at'    => $ts,
                    'updated_at'    => $ts,
                ]);

                $created++;
                $allMessages->push($message);

                // Родителем для будущих ответов может быть корень или сообщение без глубокого вложения
                // (чтобы дерево не уходило слишком глубоко)
                if (!$parent || $faker->boolean(60)) {
                    $possibleParents->push($message);
                }

                // Если это был ответ — увеличим счётчик у родителя
                if ($parent) {
                    QaMessage::query()->whereKey($parent->id)->increment('replies_count');
                    // актуализируем в локальной коллекции
                    $idx = $allMessages->search(fn ($m) => $m->id === $parent->id);
                    if ($idx !== false) {
                        $parentModel = $allMessages[$idx];
                        $parentModel->replies_count = (int)$parentModel->replies_count + 1;
                        $allMessages[$idx] = $parentModel;
                    }
                }
            }

            // 4) Пересчёт итогов темы: количество ответов, last_reply_at, last_activity_at
            if ($allMessages->isNotEmpty()) {
                $last = $allMessages->sortBy('created_at')->last();
                $repliesTotal = $allMessages->count();

                $thread->replies_count    = $repliesTotal;
                $thread->last_reply_at    = $last?->created_at;
                $thread->last_activity_at = $last?->created_at;
                $thread->save();
                $updatedThreads++;
            }
        }

        $this->command?->info("QA messages seeded: created {$created}; threads updated: {$updatedThreads}.");
    }

    /**
     * Равномерно (с шумом) распределяет N временных меток между start..end.
     * @return Collection<Carbon>
     * @throws RandomException
     */
    private function spreadTimestamps($start, $end, int $n): Collection
    {
        $n = max(1, $n);
        $totalSeconds = max(1, $end->diffInSeconds($start));

        $points = [];
        for ($i = 1; $i <= $n; $i++) {
            // равномерная доля с небольшим шумом
            $ratio = $i / ($n + 1);
            $offset = (int) round($ratio * $totalSeconds);
            $jitter = random_int(-3600, 3600); // +/- 1 час шума
            $sec = max(0, min($totalSeconds, $offset + $jitter));
            $points[] = $start->copy()->addSeconds($sec);
        }
        sort($points);
        return collect($points);
    }
}
