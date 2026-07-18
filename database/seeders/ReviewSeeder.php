<?php

namespace Database\Seeders;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Review\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Запуск сидера универсальных отзывов.
     *
     * На текущем этапе создаются только отзывы товаров.
     * Изображения отзывов не создаются.
     */
    public function run(): void
    {
        /** Товары, для которых создаются отзывы */
        $products = MarketProduct::query()
            ->orderBy('id')
            ->get();

        /** Пользователи — авторы отзывов */
        $users = User::query()
            ->orderBy('id')
            ->get();

        if ($products->isEmpty()) {
            $this->command?->warn(
                'ReviewSeeder: товары не найдены.'
            );

            return;
        }

        if ($users->isEmpty()) {
            $this->command?->warn(
                'ReviewSeeder: пользователи не найдены.'
            );

            return;
        }

        /** Первый пользователь используется как модератор и автор ответов */
        $moderator = $users->first();

        /** Шаблоны тестовых отзывов */
        $reviewTemplates = $this->reviewTemplates();

        DB::transaction(function () use (
            $products,
            $users,
            $moderator,
            $reviewTemplates
        ): void {
            foreach ($products as $productIndex => $product) {
                /*
                 * Для каждого товара создаём не более трёх отзывов,
                 * если в базе имеется достаточное количество пользователей.
                 */
                $reviewUsers = $users
                    ->take(min(3, $users->count()))
                    ->values();

                foreach ($reviewUsers as $userIndex => $user) {
                    $templateIndex = (
                            $productIndex + $userIndex
                        ) % count($reviewTemplates);

                    $template = $reviewTemplates[$templateIndex];

                    /** Разные даты создания отзывов */
                    $createdAt = Carbon::now()
                        ->subDays(
                            ($productIndex * 3) + $userIndex + 1
                        )
                        ->setTime(
                            10 + $userIndex,
                            15
                        );

                    $moderationStatus = (int) $template[
                    'moderation_status'
                    ];

                    $hasReply = filled($template['reply']);

                    Review::query()->updateOrCreate(
                        [
                            'reviewable_type' => $product->getMorphClass(),
                            'reviewable_id' => $product->id,
                            'user_id' => $user->id,
                        ],
                        [
                            'rating' => $template['rating'],

                            'advantages' => $template['advantages'],
                            'disadvantages' => $template['disadvantages'],
                            'comment' => $template['comment'],

                            'verified' => $template['verified'],

                            'reply' => $template['reply'],

                            'replied_by' => $hasReply
                                ? $moderator->id
                                : null,

                            'replied_at' => $hasReply
                                ? $createdAt->copy()->addDay()
                                : null,

                            'moderation_status' => $moderationStatus,

                            'moderated_by' => $moderationStatus !== 0
                                ? $moderator->id
                                : null,

                            'moderated_at' => $moderationStatus !== 0
                                ? $createdAt->copy()->addHours(3)
                                : null,

                            'moderation_note' =>
                                $template['moderation_note'],

                            'likes' => $template['likes'],
                            'activity' => $template['activity'],

                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]
                    );
                }
            }

            /** Пересчёт рейтинга каждого товара */
            $products->each(function (
                MarketProduct $product
            ): void {
                $this->recalculateProductRating($product);
            });
        });

        $this->command?->info(
            'Универсальные отзывы товаров успешно созданы.'
        );
    }

    /**
     * Шаблоны тестовых отзывов.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function reviewTemplates(): array
    {
        return [
            [
                'rating' => 5,

                'advantages' =>
                    'Хорошее качество, удобное использование и аккуратная упаковка.',

                'disadvantages' =>
                    'Существенных недостатков не обнаружено.',

                'comment' =>
                    'Товар полностью соответствует описанию. Покупкой доволен, всё работает исправно.',

                'verified' => true,

                'reply' =>
                    'Спасибо за ваш отзыв! Будем рады видеть вас снова.',

                'moderation_status' => 1,
                'moderation_note' => null,

                'likes' => 12,
                'activity' => true,
            ],

            [
                'rating' => 4,

                'advantages' =>
                    'Удобный товар, хорошее соотношение цены и качества.',

                'disadvantages' =>
                    'Упаковка могла быть немного надёжнее.',

                'comment' =>
                    'В целом покупкой доволен. Товар выполняет свои функции и выглядит качественно.',

                'verified' => true,

                'reply' =>
                    'Благодарим за обратную связь. Учтём замечание по упаковке.',

                'moderation_status' => 1,
                'moderation_note' => null,

                'likes' => 7,
                'activity' => true,
            ],

            [
                'rating' => 3,

                'advantages' =>
                    'Товар соответствует основным характеристикам.',

                'disadvantages' =>
                    'Есть небольшие замечания к качеству исполнения.',

                'comment' =>
                    'Средний вариант за свою стоимость. Перед покупкой рекомендую внимательно изучить характеристики.',

                'verified' => false,
                'reply' => null,

                'moderation_status' => 0,
                'moderation_note' => null,

                'likes' => 2,
                'activity' => true,
            ],

            [
                'rating' => 2,

                'advantages' =>
                    'Быстрая доставка.',

                'disadvantages' =>
                    'Товар не полностью соответствовал ожиданиям.',

                'comment' =>
                    'После получения обнаружились недостатки. Ожидал более высокое качество.',

                'verified' => true,

                'reply' =>
                    'Нам жаль, что товар не оправдал ожиданий. Свяжитесь с магазином для решения вопроса.',

                'moderation_status' => 1,
                'moderation_note' => null,

                'likes' => 1,
                'activity' => true,
            ],

            [
                'rating' => 1,

                'advantages' => null,

                'disadvantages' =>
                    'Некорректное описание и недостаточно информации о товаре.',

                'comment' =>
                    'Отзыв оставлен для проверки отклонённого статуса модерации.',

                'verified' => false,
                'reply' => null,

                'moderation_status' => 2,

                'moderation_note' =>
                    'Отзыв отклонён модератором как тестовая запись.',

                'likes' => 0,
                'activity' => false,
            ],
        ];
    }

    /**
     * Пересчёт рейтинга товара по активным
     * и одобренным отзывам.
     */
    protected function recalculateProductRating(
        MarketProduct $product
    ): void {
        $ratingQuery = Review::query()
            ->where(
                'reviewable_type',
                $product->getMorphClass()
            )
            ->where(
                'reviewable_id',
                $product->id
            )
            ->where('activity', true)
            ->where('moderation_status', 1);

        $ratingCount = (clone $ratingQuery)->count();

        $ratingAverage = $ratingCount > 0
            ? round(
                (float) (clone $ratingQuery)->avg('rating'),
                2
            )
            : 0;

        $product->forceFill([
            'rating_avg' => $ratingAverage,
            'rating_count' => $ratingCount,
        ])->saveQuietly();
    }
}
