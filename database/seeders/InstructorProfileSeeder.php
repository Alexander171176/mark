<?php

namespace Database\Seeders;

use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstructorProfileSeeder extends Seeder
{
    /**
     * Локали для посева. Можно расширять в будущем.
     *
     * @var string[]
     */
    protected array $locales = ['ru', 'en', 'kk'];

    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->locales as $locale) {
                $this->seedLocale($locale);
            }
        });
    }

    /**
     * Посев набора инструкторов для одной локали.
     */
    protected function seedLocale(string $locale): void
    {
        $faker   = $this->fakerForLocale($locale);
        $records = $this->datasetFor($locale);

        foreach (array_values($records) as $index => $row) {
            // 1) Пользователь (технический, если не существует)
            $user = $this->firstOrCreateUser($row['user_email'] ?? null, $row['user_name'] ?? null, $locale, $index);

            // 2) Слагаем уникальный slug внутри локали
            $slug = $this->makeSlug($locale, $row['slug'] ?? null, $row['title'] ?? $user->name);

            // 3) SEO и прочие поля
            $meta = $this->seoFor($faker);

            /** @var InstructorProfile $profile */
            $profile = InstructorProfile::withTrashed()->firstOrNew([
                'locale' => $locale,
                'slug'   => $slug,
            ]);

            $profile->fill([
                'sort'             => $row['sort']             ?? $index,
                'activity'         => $row['activity']         ?? true,
                'user_id'          => $user->id,
                'locale'           => $locale,
                'title'            => $row['title']            ?? $this->titleFor($locale, $index),
                'short'            => $row['short']            ?? $faker->sentence(10),
                'bio'              => $row['bio']              ?? $faker->paragraphs(3, true),
                'slug'             => $slug,
                'experience_years' => $row['experience_years'] ?? $faker->numberBetween(1, 15),
                'hourly_rate'      => $row['hourly_rate']      ?? $faker->randomFloat(2, 15, 120),
                'rating_count'     => $row['rating_count']     ?? $faker->numberBetween(5, 250),
                'rating_avg'       => $row['rating_avg']       ?? $faker->randomFloat(2, 4.2, 4.9),
                'social_links'     => $row['social_links']     ?? $this->socialLinks($faker),
                'views'            => $row['views']            ?? $faker->numberBetween(0, 5000),
                'meta_title'       => $row['meta_title']       ?? $meta['title'],
                'meta_keywords'    => $row['meta_keywords']    ?? $meta['keywords'],
                'meta_desc'        => $row['meta_desc']        ?? $meta['desc'],
            ]);

            if ($profile->exists && $profile->trashed()) {
                $profile->restore();
            }
            $profile->save();

            // 4) Медиа (НЕ обязательно): если файлы существуют — прикрепим avatar/cover
            $this->attachMediaIfExists($profile, $row['avatar'] ?? null, $row['cover'] ?? null);

            // 5) Пример работы с галереей (через InstructorProfileImage)
            // Закомментировано — раскомментируйте и добавьте файлы при необходимости.
            //
            // $this->attachGallery($profile, [
            //     ['file' => 'gallery-1.png', 'alt' => 'Gallery 1', 'order' => 0],
            //     ['file' => 'gallery-2.png', 'alt' => 'Gallery 2', 'order' => 1],
            // ]);
        }
    }

    /**
     * Демонстрационный датасет на локаль.
     * Можно расширять или подменять содержимое без изменения общей логики.
     */
    protected function datasetFor(string $locale): array
    {
        // Нейтральные записи (переводы заголовков — ниже).
        return [
            [
                'user_email'       => "instructor_{$locale}_1@example.com",
                'user_name'        => $this->nameFor($locale, 1),
                'title'            => $this->titleFor($locale, 1),
                'short'            => null,
                'bio'              => null,
                'slug'             => null, // сгенерируется от title
                'experience_years' => 8,
                'hourly_rate'      => 45.00,
                'rating_avg'       => 4.7,
                'rating_count'     => 120,
                'activity'         => true,
                'avatar'           => $this->asset("instructors/{$locale}/avatar-1.jpg"),
                'cover'            => $this->asset("instructors/{$locale}/cover-1.jpg"),
            ],
            [
                'user_email'       => "instructor_{$locale}_2@example.com",
                'user_name'        => $this->nameFor($locale, 2),
                'title'            => $this->titleFor($locale, 2),
                'experience_years' => 5,
                'hourly_rate'      => 35.00,
                'rating_avg'       => 4.5,
                'rating_count'     => 65,
                'activity'         => true,
                'avatar'           => $this->asset("instructors/{$locale}/avatar-2.jpg"),
                'cover'            => $this->asset("instructors/{$locale}/cover-2.jpg"),
            ],
            [
                'user_email'       => "instructor_{$locale}_3@example.com",
                'user_name'        => $this->nameFor($locale, 3),
                'title'            => $this->titleFor($locale, 3),
                'experience_years' => 12,
                'hourly_rate'      => 60.00,
                'rating_avg'       => 4.8,
                'rating_count'     => 200,
                'activity'         => true,
                'avatar'           => $this->asset("instructors/{$locale}/avatar-3.jpg"),
                'cover'            => $this->asset("instructors/{$locale}/cover-3.jpg"),
            ],
        ];
    }

    /* ===================== helpers ===================== */

    protected function fakerForLocale(string $locale)
    {
        return match ($locale) {
            'ru' => Faker::create('ru_RU'),
            'en' => Faker::create('en_US'),
            'kk' => Faker::create('kk_KZ'), // при отсутствии полного словаря вернёт англ. fallback
            default => Faker::create(),
        };
    }

    protected function seoFor($faker): array
    {
        return [
            'title'    => $faker->sentence(6),
            'keywords' => implode(', ', $faker->words(6)),
            'desc'     => $faker->text(160),
        ];
    }

    protected function firstOrCreateUser(?string $email, ?string $name, string $locale, int $index): User
    {
        $email = $email ?: "instructor_{$locale}_" . ($index + 1) . '@example.com';
        $name  = $name  ?: $this->nameFor($locale, $index + 1);

        /** @var User $user */
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => bcrypt('password'), // dev-значение
                'email_verified_at' => now(),
            ]
        );

        // здесь можно повесить роль/флаг инструктора, если используется Spatie Roles etc.
        return $user;
    }

    /**
     * Генерация slug, уникального в пределах локали.
     */
    protected function makeSlug(string $locale, ?string $slug, string $title): string
    {
        $base = Str::slug($slug ?: $title);

        if (!InstructorProfile::where('locale', $locale)->where('slug', $base)->exists()) {
            return $base;
        }

        $i = 2;
        while (InstructorProfile::where('locale', $locale)->where('slug', "{$base}-{$i}")->exists()) {
            $i++;
        }
        return "{$base}-{$i}";
    }

    /**
     * Подбор “человеческих” имён под локаль — просто для демо.
     */
    protected function nameFor(string $locale, int $n): string
    {
        return match ($locale) {
            'ru' => match ($n) {
                1 => 'Иван Петров',
                2 => 'Мария Смирнова',
                default => 'Алексей Иванов',
            },
            'kk' => match ($n) {
                1 => 'Ерлан Сеитов',
                2 => 'Айым Мукушева',
                default => 'Нұрлан Төлеуов',
            },
            default => match ($n) {
                1 => 'John Carter',
                2 => 'Emma Wilson',
                default => 'Alex Morgan',
            },
        };
    }

    /**
     * Заголовок/позиционирование под локаль.
     */
    protected function titleFor(string $locale, int $n): string
    {
        return match ($locale) {
            'ru' => match ($n) {
                1 => 'Senior PHP / Laravel Инженер',
                2 => 'Frontend Разработчик (Vue.js)',
                default => 'Data Engineer / DevOps',
            },
            'kk' => match ($n) {
                1 => 'Senior PHP / Laravel инженер',
                2 => 'Frontend әзірлеуші (Vue.js)',
                default => 'Деректер инженері / DevOps',
            },
            default => match ($n) {
                1 => 'Senior PHP / Laravel Engineer',
                2 => 'Frontend Developer (Vue.js)',
                default => 'Data Engineer / DevOps',
            },
        };
    }

    /**
     * Соцсети (примеры).
     */
    protected function socialLinks($faker): array
    {
        return [
            'github'   => 'https://github.com/' . Str::slug($faker->userName()),
            'linkedin' => 'https://www.linkedin.com/in/' . Str::slug($faker->userName()),
            'telegram' => 'https://t.me/' . Str::slug($faker->userName()),
        ];
    }

    /**
     * Абсолютный путь к ассетам для аватара/ковера в resources/images/...
     */
    protected function asset(string $rel): string
    {
        return resource_path('images/' . ltrim($rel, '/'));
    }

    protected function attachMediaIfExists(InstructorProfile $profile, ?string $avatarPath, ?string $coverPath): void
    {
        // avatar
        if ($avatarPath && File::exists($avatarPath)) {
            $profile->clearMediaCollection(InstructorProfile::MEDIA_AVATAR);
            $profile->addMedia($avatarPath)
                ->preservingOriginal()
                ->toMediaCollection(InstructorProfile::MEDIA_AVATAR);
        }

        // cover
        if ($coverPath && File::exists($coverPath)) {
            $profile->clearMediaCollection(InstructorProfile::MEDIA_COVER);
            $profile->addMedia($coverPath)
                ->preservingOriginal()
                ->toMediaCollection(InstructorProfile::MEDIA_COVER);
        }
    }

    // Пример подключения галереи через pivot (раскомментировать при наличии файлов)
    // protected function attachGallery(InstructorProfile $profile, array $items): void
    // {
    //     foreach ($items as $i => $it) {
    //         $img = \App\Models\Admin\InstructorProfile\InstructorProfileImage::create([
    //             'order'   => $it['order'] ?? $i,
    //             'alt'     => $it['alt'] ?? '',
    //             'caption' => $it['caption'] ?? '',
    //         ]);
    //         $full = $this->asset('instructors/gallery/' . $it['file']);
    //         if (File::exists($full)) {
    //             $img->addMedia($full)->preservingOriginal()
    //                 ->toMediaCollection(\App\Models\Admin\InstructorProfile\InstructorProfileImage::MEDIA_COLLECTION);
    //         }
    //         $profile->images()->syncWithoutDetaching([$img->id => ['order' => $img->order]]);
    //     }
    // }
}
