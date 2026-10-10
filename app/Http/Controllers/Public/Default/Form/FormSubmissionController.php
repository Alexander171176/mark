<?php

namespace App\Http\Controllers\Public\Default\Form;

use App\Http\Controllers\Controller;
use App\Models\Admin\Form\Form\Form;
use App\Services\Public\Form\FormSubmissionProtectionService;
use App\Services\Public\Form\FormSubmissionService;
use App\Services\Public\Form\FormSubmissionValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FormSubmissionController extends Controller
{
    public function __construct(
        private readonly FormSubmissionValidationService $validationService,
        private readonly FormSubmissionService $submissionService,
        private readonly FormSubmissionProtectionService $protectionService
    ) {
    }

    /**
     * Отправка публичной формы.
     * @throws ValidationException|Throwable
     */
    public function store(Request $request, string $formCode): JsonResponse
    {
        // Загружаем опубликованную публичную форму.
        $form = Form::query()
            ->forPublic()
            ->byCode($formCode)
            ->with([
                'translations',
                'activeFields.translations',
                'activeFields.activeOptions.translations',
            ])
            ->firstOrFail();

        // Проверяем необходимость авторизации.
        if ($form->auth_required && !$request->user()) {
            return response()->json([
                'message' => 'Для отправки этой формы необходимо авторизоваться.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // HTTP 429 не учитываем как ошибочную попытку.
        $this->protectionService->checkFailedAttempts($request, $form);

        try {
            // Honeypot и минимальное время заполнения.
            $this->protectionService->checkBeforeValidation($request, $form);

            // Валидация динамических полей.
            $validated = $this->validationService->validate($request, $form);

            // CAPTCHA проверяется внутри защищённой операции.
            $submission = $this->protectionService->executeProtected(
                $request,
                $form,
                fn () => $this->submissionService->create($form, $validated, $request)
            );
        } catch (ValidationException $e) {
            // Только ошибки проверок увеличивают счётчик ошибок.
            $this->protectionService->recordFailedAttempt($request, $form);
            throw $e;
        }

        return response()->json([
            'message' => $form->loadedTranslationOrFallback()?->success_message
                ?: 'Форма успешно отправлена.',
            'submission' => [
                'id' => $submission->id,
                'status' => $submission->status,
                'submitted_at' => $submission->submitted_at?->toISOString(),
            ],
        ], Response::HTTP_CREATED);
    }
}
