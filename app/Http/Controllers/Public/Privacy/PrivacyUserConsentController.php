<?php

namespace App\Http\Controllers\Public\Privacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Privacy\PrivacyUserConsent\PrivacyUserConsentRequest;
use App\Http\Requests\Admin\Privacy\PrivacyUserConsent\PrivacyUserConsentStatusRequest;
use App\Models\Admin\Privacy\PrivacyUserConsent\PrivacyUserConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PrivacyUserConsentController extends Controller
{
    /**
     * Проверка статуса согласия пользователя.
     */
    public function show(PrivacyUserConsentStatusRequest $request): JsonResponse
    {
        $policyVersion = config('privacy.policy_version', 1);

        $consent = PrivacyUserConsent::query()
            ->where('accepted', true)
            ->whereNull('revoked_at')
            ->where('policy_version', $policyVersion)
            ->where(function ($query) use ($request) {
                $query->where('session_id', $request->session()->getId());

                if (Auth::check()) {
                    $query->orWhere('user_id', Auth::id());
                }
            })
            ->latest('accepted_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'accepted' => (bool) $consent,
                'policy_version' => $policyVersion,
            ],
        ]);
    }

    /**
     * Сохранение согласия пользователя.
     */
    public function store(PrivacyUserConsentRequest $request): JsonResponse
    {
        $policyVersion = config('privacy.policy_version', 1);

        $consent = PrivacyUserConsent::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
                'policy_version' => $policyVersion,
            ],
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'locale' => app()->getLocale(),
                'policy_url' => url('/privacy'),
                'policy_hash' => null,
                'accepted' => $request->boolean('accepted'),
                'accepted_at' => now(),
                'revoked_at' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Согласие успешно сохранено.',
            'data' => [
                'id' => $consent->id,
                'accepted' => $consent->accepted,
                'policy_version' => $consent->policy_version,
                'accepted_at' => $consent->accepted_at,
            ],
        ]);
    }
}
