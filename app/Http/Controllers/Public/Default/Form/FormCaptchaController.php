<?php

namespace App\Http\Controllers\Public\Default\Form;

use App\Http\Controllers\Controller;
use App\Models\Admin\Form\Form\Form;
use App\Services\Public\Form\FormCaptchaService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FormCaptchaController extends Controller
{
    public function __construct(
        private readonly FormCaptchaService $captchaService
    ) {
    }

    /**
     * Получить CAPTCHA для публичной формы.
     */
    public function show(string $formCode): JsonResponse
    {
        $form = Form::query()
            ->forPublic()
            ->byCode($formCode)
            ->firstOrFail();

        if (!$form->captcha_enabled) {
            return response()->json(
                [
                    'message' => 'CAPTCHA отключена для этой формы.',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(
            $this->captchaService->generate($form->id)
        )->header('Cache-Control', 'no-store, private');
    }
}
