<?php

namespace App\Http\Controllers\Admin\System\Invokable;

use App\Http\Controllers\Controller;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Video\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

// Импорт Log
// Импорт Throwable

class RemoveArticleFromVideoController extends Controller
{
    /**
     * Отсоединяет указанную статью от указанного видео.
     *
     * @param Video $video Модель видео (через Route Model Binding)
     * @param Article $article Модель статьи (через Route Model Binding)
     * @return RedirectResponse
     */
    public function __invoke(Video $video, Article $article): RedirectResponse
    {
        // TODO: Реализовать проверку прав доступа. Примеры:
        // $this->authorize('update', $video); // Может ли редактировать видео?
        // $this->authorize('update', $article); // Может ли редактировать статью?
        // $this->authorize('manage content relationships'); // Специальное разрешение?
//        if (!auth()->user()?->can('manage content')) { // Пример
//            abort(403, 'У вас нет прав для изменения связей видео и статей.');
//        }

        try {
            // Выполняем отсоединение. Выбираем один из вариантов:
            $detached = $article->videos()->detach($video->id);

            if ($detached) {
                Log::info('Статья успешно отсоединена от видео', [
                    'article_id' => $article->id,
                    'video_id' => $video->id,
                    'user_id' => auth()->id()
                ]);
                // Уточненное сообщение
                return back()->with('success', "Видео '{$video->title}' успешно отсоединено от статьи '{$article->title}'.");
            } else {
                Log::warning('Попытался отделить видео от статьи, но взаимосвязи не было.', [
                    'article_id' => $article->id,
                    'video_id' => $video->id,
                    'user_id' => auth()->id()
                ]);
                return back()->with('info', 'Видео уже было отсоединено от этой статьи.');
            }

        } catch (Throwable $e) {
            Log::error("Ошибка при отсоединении видео {$video->id} от статьи {$article->id}: " . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);
            return back()->with('error', 'Произошла ошибка при отсоединении видео от статьи.');
        }
    }
}
