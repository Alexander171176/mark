<?php

namespace App\Http\Controllers\Admin\School\QuizAttemptItem;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\QuizAttemptItem\QuizAttemptItemRequest;
use App\Http\Resources\Admin\School\QuizAttemptItem\QuizAttemptItemResource;
use App\Models\Admin\School\QuizAttemptItem\QuizAttemptItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Админ-контроллер (Review-only) для ответов в попытках квиза.
 *
 * Ресурс:
 *  admin.quizAttemptItems.*
 *
 * Доп. экшены:
 *  admin.actions.quizAttemptItems.bulkDestroy
 *  admin.actions.quizAttemptItems.bulkUpdateCorrect
 */
class QuizAttemptItemController extends Controller
{
    /**
     * Список ответов.
     *
     * GET /admin/quiz-attempt-items
     */
    public function index(): Response
    {
        $adminCountItems = (int) config('site_settings.AdminCountQuizAttemptItems', 20);
        $adminSortItems  = (string) config('site_settings.AdminSortQuizAttemptItems', 'idDesc');

        $items = collect();
        $itemsCount = 0;

        try {
            $items = QuizAttemptItem::query()
                ->with([
                    'attempt:id,quiz_id,user_id,status,attempt_number',
                    'attempt.quiz:id,title,slug',
                    'attempt.user:id,name,email',

                    'question:id,question_text,question_type,points,quiz_id',

                    'selectedAnswer:id,text,is_correct',
                    // Если в модели есть relation на multiple-answers (pivot/JSON) — добавь сюда
                    // 'selectedAnswers:id,text,is_correct',
                ])
                ->orderByDesc('id')
                ->get();

            $itemsCount = $items->count();
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки QuizAttemptItem (admin)', [
                'exception' => $e,
            ]);

            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/QuizAttemptItems/Index', [
            'items'           => QuizAttemptItemResource::collection($items)->resolve(),
            'itemsCount'      => $itemsCount,
            'adminCountItems' => $adminCountItems,
            'adminSortItems'  => $adminSortItems,
        ]);
    }

    /**
     * show в админке — редирект на edit.
     */
    public function show(QuizAttemptItem $quizAttemptItem): RedirectResponse
    {
        return redirect()->route('admin.quizAttemptItems.edit', $quizAttemptItem);
    }

    /**
     * Форма review-редактирования.
     *
     * GET /admin/quiz-attempt-items/{quizAttemptItem}/edit
     */
    public function edit(QuizAttemptItem $quizAttemptItem): Response
    {
        // Грузим всё, что нужно для "контекста" (readonly) и review-полей
        $quizAttemptItem->load([
            'attempt:id,quiz_id,user_id,status,attempt_number',
            'attempt.quiz:id,title,slug',
            'attempt.user:id,name,email',

            // ✅ важно: answers
            'question:id,question_text,question_type,points,quiz_id',
            'question.answers:id,quiz_question_id,text,is_correct',

            'selectedAnswer:id,text,is_correct',
        ]);


        // В режиме review мы НЕ даём менять attempt/question/answers,
        // поэтому списки attempts/questions/answers НЕ нужны.
        // Показываем в Edit.vue только контекст текущей записи.

        return Inertia::render('Admin/School/QuizAttemptItems/Edit', [
            'item' => (new QuizAttemptItemResource($quizAttemptItem))->resolve(),
        ]);
    }

    /**
     * Обновление (Review-only):
     * - is_correct (ТОЛЬКО для open_text)
     * - score
     * - reviewer_comment
     *
     * PUT /admin/quiz-attempt-items/{quizAttemptItem}
     */
    public function update(QuizAttemptItemRequest $request, QuizAttemptItem $quizAttemptItem): RedirectResponse
    {
        // Реквест сам в режиме review запрещает все "student answer" поля.
        $validated = $request->validated();

        try {
            // Подгрузим question, чтобы понимать тип и points
            $quizAttemptItem->loadMissing(['question:id,question_type,points']);

            $questionType = (string) ($quizAttemptItem->question->question_type ?? '');
            $isOpenText = str_contains(strtolower($questionType), 'open')
                || str_contains(strtolower($questionType), 'text');

            // Белый список review-полей
            $data = collect($validated)->only([
                'score',
                'reviewer_comment',
                'is_correct',
            ])->toArray();

            // 1) is_correct разрешаем менять только для open_text
            if (!$isOpenText) {
                unset($data['is_correct']);
            }

            // 2) score не должен превышать max (question.points), если points есть
            if (array_key_exists('score', $data)) {
                $max = $quizAttemptItem->question->points ?? null;
                if (is_numeric($max)) {
                    $data['score'] = min((int) $data['score'], (int) $max);
                }
            }

            $quizAttemptItem->fill($data);
            $quizAttemptItem->save();

            Log::info('QuizAttemptItem обновлён (review/admin)', [
                'item_id' => $quizAttemptItem->id,
                'is_open_text' => $isOpenText,
            ]);

            return redirect()
                ->route('admin.quizAttemptItems.index')
                ->with('success', __('admin/controllers.updated_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка обновления QuizAttemptItem ID {$quizAttemptItem->id}", [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => __('admin/controllers.updated_error'),
                ]);
        }
    }

    /**
     * Удаление одного ответа.
     *
     * DELETE /admin/quiz-attempt-items/{quizAttemptItem}
     */
    public function destroy(QuizAttemptItem $quizAttemptItem): RedirectResponse
    {
        try {
            $quizAttemptItem->delete();

            Log::info('QuizAttemptItem удалён (admin)', [
                'item_id' => $quizAttemptItem->id,
            ]);

            return redirect()
                ->route('admin.quizAttemptItems.index')
                ->with('success', __('admin/controllers.deleted_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка удаления QuizAttemptItem ID {$quizAttemptItem->id}", [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление.
     *
     * DELETE /admin/actions/quiz-attempt-items/bulk-delete
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:quiz_attempt_items,id'],
        ]);

        $ids   = $validated['ids'];
        $count = count($ids);

        try {
            DB::beginTransaction();

            QuizAttemptItem::whereIn('id', $ids)->delete();

            DB::commit();

            Log::info('Массовое удаление QuizAttemptItem (admin)', [
                'ids' => $ids,
            ]);

            return redirect()
                ->route('admin.quizAttemptItems.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', [
                    'count' => $count,
                ]));
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Ошибка массового удаления QuizAttemptItem (admin)', [
                'ids'       => $ids,
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Массовое обновление correct.
     *
     * ВАЖНО (вариант 1):
     * - массово менять is_correct имеет смысл ТОЛЬКО для open_text,
     *   иначе это сломает логику авто-проверки choice-вопросов.
     *
     * PUT /admin/actions/quiz-attempt-items/bulk-correct
     */
    public function bulkUpdateCorrect(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'        => ['required', 'array'],
            'ids.*'      => ['integer', 'exists:quiz_attempt_items,id'],
            'is_correct' => ['required', 'boolean'],
        ]);

        $ids       = $validated['ids'];
        $isCorrect = (bool) $validated['is_correct'];

        if (empty($ids)) {
            return back()->with('warning', __('admin/controllers.bulk_updated_activity_no_selection'));
        }

        try {
            // Обновляем ТОЛЬКО open_text
            $updated = QuizAttemptItem::query()
                ->whereIn('id', $ids)
                ->whereHas('question', function ($q) {
                    $q->whereIn('question_type', ['open_text', 'open', 'text']); // если у тебя строго open_text — оставь только его
                })
                ->update(['is_correct' => $isCorrect]);

            Log::info('bulkUpdateCorrect QuizAttemptItem (open_text only)', [
                'ids'        => $ids,
                'is_correct' => $isCorrect,
                'updated'    => $updated,
            ]);

            return back()->with('success', __('admin/controllers.updated_success'));
        } catch (Throwable $e) {
            Log::error('Ошибка bulkUpdateCorrect QuizAttemptItem', [
                'ids'       => $ids,
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.updated_error'));
        }
    }
}
