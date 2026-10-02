<?php

namespace App\Http\Controllers\Public\Default\Form;

use App\Http\Controllers\Controller;
use App\Models\Admin\Form\Form\Form;
use App\Services\Public\Form\FormSubmissionService;
use App\Services\Public\Form\FormSubmissionValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicFormSubmissionController extends Controller
{
    public function __construct(
        private readonly FormSubmissionValidationService $validationService,
        private readonly FormSubmissionService $submissionService
    ) {
    }

    /**
     * Отправка публичной формы.
     */
    public function store(
        Request $request,
        string $formCode
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | Публичная форма
        |--------------------------------------------------------------------------
        |
        | Получаем только активную и опубликованную форму.
        |
        | Вместе с формой заранее загружаем активные поля,
        | их переводы и активные варианты выбора.
        |
        */

        $form = Form::query()
            ->forPublic()
            ->byCode($formCode)
            ->with([
                'activeFields.translations',
                'activeFields.activeOptions.translations',
            ])
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Только для авторизованных пользователей
        |--------------------------------------------------------------------------
        */

        if (
            $form->auth_required
            && !$request->user()
        ) {
            return response()->json(
                [
                    'message' =>
                        'Для отправки этой формы необходимо авторизоваться.',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Динамическая валидация
        |--------------------------------------------------------------------------
        |
        | Правила строятся по актуальной конфигурации
        | полей формы из базы данных.
        |
        */

        $validated = $this->validationService->validate(
            $request,
            $form
        );

        /*
        |--------------------------------------------------------------------------
        | Создание заявки
        |--------------------------------------------------------------------------
        |
        | FormSubmissionService сохраняет:
        |
        | - основную заявку;
        | - snapshot значений полей;
        | - загруженные файлы;
        | - первоначальную историю статуса.
        |
        */

        $submission = $this->submissionService->create(
            $form,
            $validated,
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Успешный ответ
        |--------------------------------------------------------------------------
        */

        return response()->json(
            [
                'message' =>
                    $form
                        ->loadedTranslationOrFallback()
                        ?->success_message
                        ?: 'Форма успешно отправлена.',

                'submission' => [
                    'id' => $submission->id,
                    'status' => $submission->status,
                    'submitted_at' =>
                        $submission->submitted_at?->toISOString(),
                ],
            ],
            Response::HTTP_CREATED
        );
    }
}
