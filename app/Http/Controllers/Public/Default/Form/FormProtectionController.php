<?php

namespace App\Http\Controllers\Public\Default\Form;

use App\Http\Controllers\Controller;
use App\Models\Admin\Form\Form\Form;
use App\Services\Public\Form\FormSubmissionProtectionService;
use Illuminate\Http\JsonResponse;

class FormProtectionController extends Controller
{
    public function __construct(
        private readonly FormSubmissionProtectionService $protectionService
    ) {
    }

    /**
     * Инициализация защиты публичной формы.
     */
    public function show(string $formCode): JsonResponse
    {
        $form = Form::query()
            ->forPublic()
            ->byCode($formCode)
            ->firstOrFail();

        if (!$form->spam_protection) {
            return response()->json([
                'token' => null,
                'expires_in' => 0,
            ]);
        }

        return response()
            ->json(
                $this->protectionService->issueToken($form)
            )
            ->header('Cache-Control', 'no-store, private');
    }
}
