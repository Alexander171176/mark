<?php

namespace App\Services\Public\Form;

use App\Models\Admin\Form\Form\Form;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormSubmissionProtectionService
{
    /**
     * Время жизни токена формы — 30 минут.
     */
    private const TOKEN_TTL_SECONDS = 1800;

    /**
     * Ограничения настроек формы.
     */
    private const MAX_RATE_LIMIT = 100;
    private const MAX_RATE_WINDOW_MINUTES = 1440;
    private const MAX_MIN_SECONDS = 300;

    /**
     * Лимит ошибочных попыток.
     */
    private const MAX_FAILED_ATTEMPTS = 10;
    private const FAILED_WINDOW_MINUTES = 10;

    /**
     * Параметры блокировок.
     */
    private const LOCK_SECONDS = 120;
    private const LOCK_WAIT_SECONDS = 5;

    /**
     * Отдельное хранилище Redis.
     */
    private const CACHE_STORE = 'form_protection';

    public function __construct(
        private readonly FormCaptchaService $captchaService
    ) {
    }

    /**
     * Получить специализированное хранилище.
     */
    private function cache(): Repository
    {
        return Cache::store(self::CACHE_STORE);
    }

    /**
     * Независимый RateLimiter.
     */
    private function limiter(): RateLimiter
    {
        return new RateLimiter($this->cache());
    }

    /**
     * Создать токен открытия формы.
     */
    public function issueToken(Form $form): array
    {
        $token = Str::random(64);

        $this->cache()->put(
            $this->tokenKey($token),
            [
                'form_id' => (int) $form->id,
                'started_at' => now()->timestamp,
            ],
            self::TOKEN_TTL_SECONDS
        );

        return [
            'token' => $token,
            'expires_in' => self::TOKEN_TTL_SECONDS,
        ];
    }

    /**
     * Проверить лимит ошибочных попыток.
     */
    public function checkFailedAttempts(
        Request $request,
        Form $form
    ): void {
        if (!$form->spam_protection) {
            return;
        }

        $key = $this->failedRateLimitKey($request, $form);
        $limiter = $this->limiter();

        if ($limiter->tooManyAttempts(
            $key,
            self::MAX_FAILED_ATTEMPTS
        )) {
            $this->rejectTooManyRequests(
                'Превышен лимит ошибочных попыток.',
                $limiter->availableIn($key)
            );
        }
    }

    /**
     * Учесть ошибочную попытку.
     *
     * Не затрагивает счётчик успешных отправок.
     */
    public function recordFailedAttempt(
        Request $request,
        Form $form
    ): void {
        if (!$form->spam_protection) {
            return;
        }

        $this->limiter()->hit(
            $this->failedRateLimitKey($request, $form),
            self::FAILED_WINDOW_MINUTES * 60
        );
    }

    /**
     * Предварительная проверка формы.
     *
     * Выполняется до валидации динамических полей.
     */
    public function checkBeforeValidation(
        Request $request,
        Form $form
    ): void {
        if ($form->honeypot_enabled) {
            $this->checkHoneypot($request);
        }

        if ($form->spam_protection) {
            $this->checkSubmissionTime($request, $form);
        }
    }

    /**
     * Проверить Honeypot.
     */
    private function checkHoneypot(Request $request): void
    {
        $value = $request->input('_form_website');

        if (
            (!is_null($value) && !is_string($value))
            || (is_string($value) && trim($value) !== '')
        ) {
            throw ValidationException::withMessages([
                '_form_website' => [
                    'Не удалось проверить отправку формы.',
                ],
            ]);
        }
    }

    /**
     * Проверить CAPTCHA.
     *
     * Вызывать внутри executeProtected(),
     * поскольку CAPTCHA может быть одноразовой.
     */
    public function checkCaptcha(
        Request $request,
        Form $form
    ): void {
        if (!$form->captcha_enabled) {
            return;
        }

        $token = $request->input('_captcha_token');
        $answer = $request->input('_captcha_answer');

        if (
            !is_string($token)
            || !is_string($answer)
            || trim($token) === ''
            || trim($answer) === ''
        ) {
            throw ValidationException::withMessages([
                '_captcha_answer' => [
                    'Введите код с изображения.',
                ],
            ]);
        }

        if (!$this->captchaService->verify(
            (int) $form->id,
            $token,
            $answer
        )) {
            throw ValidationException::withMessages([
                '_captcha_answer' => [
                    'Неверный или устаревший код. Обновите CAPTCHA.',
                ],
            ]);
        }
    }

    /**
     * Проверить успешную квоту.
     *
     * Настройки:
     * rate_limit
     * rate_limit_minutes
     */
    public function checkRateLimit(
        Request $request,
        Form $form
    ): void {
        if (!$form->spam_protection) {
            return;
        }

        $limit = min(
            self::MAX_RATE_LIMIT,
            max(1, (int) ($form->rate_limit ?: 5))
        );

        $key = $this->rateLimitKey($request, $form);
        $limiter = $this->limiter();

        if ($limiter->tooManyAttempts($key, $limit)) {
            $this->rejectTooManyRequests(
                'Превышен лимит отправок формы.',
                $limiter->availableIn($key)
            );
        }
    }

    /**
     * Проверить токен и время заполнения.
     */
    private function checkSubmissionTime(
        Request $request,
        Form $form
    ): void {
        $token = $request->input('_form_token');

        if (!$this->isValidToken($token)) {
            throw ValidationException::withMessages([
                '_form' => [
                    'Сессия формы недействительна. Откройте форму заново.',
                ],
            ]);
        }

        $data = $this->cache()->get(
            $this->tokenKey($token)
        );

        if (
            !is_array($data)
            || (int) ($data['form_id'] ?? 0) !== (int) $form->id
        ) {
            throw ValidationException::withMessages([
                '_form' => [
                    'Срок действия формы истёк или она уже отправлена.',
                ],
            ]);
        }

        $startedAt = (int) ($data['started_at'] ?? 0);
        $elapsed = now()->timestamp - $startedAt;

        if ($startedAt <= 0 || $elapsed < 0) {
            throw ValidationException::withMessages([
                '_form' => [
                    'Не удалось проверить время заполнения формы.',
                ],
            ]);
        }

        $minimum = min(
            self::MAX_MIN_SECONDS,
            max(0, (int) $form->min_submit_seconds)
        );

        if ($elapsed < $minimum) {
            $remaining = $minimum - $elapsed;

            throw ValidationException::withMessages([
                '_form' => [
                    "Форма отправлена слишком быстро. "
                    . "Повторите через {$remaining} сек.",
                ],
            ]);
        }
    }

    /**
     * Выполнить сохранение под блокировками.
     *
     * 1. Блокировка токена.
     * 2. Блокировка квоты отправителя.
     * 3. Повторная проверка токена.
     * 4. Проверка успешной квоты.
     * 5. Проверка CAPTCHA.
     * 6. Сохранение заявки.
     * 7. Учёт успешной отправки.
     * 8. Погашение токена.
     * @throws ValidationException
     */
    public function executeProtected(
        Request $request,
        Form $form,
        Closure $callback
    ): mixed {
        /*
         * При отключённой антиспам-защите
         * токен и лимиты не обязательны.
         *
         * CAPTCHA при этом продолжает работать.
         */
        if (!$form->spam_protection) {
            $this->checkCaptcha($request, $form);

            return $callback();
        }

        $token = $request->input('_form_token');

        if (!$this->isValidToken($token)) {
            throw ValidationException::withMessages([
                '_form' => [
                    'Сессия формы недействительна.',
                ],
            ]);
        }

        $tokenLock = $this->cache()->lock(
            $this->tokenLockKey($token),
            self::LOCK_SECONDS
        );

        try {
            return $tokenLock->block(
                self::LOCK_WAIT_SECONDS,
                function () use ($request, $form, $callback) {

                    $rateLock = $this->cache()->lock(
                        $this->rateLockKey($request, $form),
                        self::LOCK_SECONDS
                    );

                    return $rateLock->block(
                        self::LOCK_WAIT_SECONDS,
                        function () use ($request, $form, $callback) {

                            // Повторно проверяем токен.
                            $this->checkSubmissionTime(
                                $request,
                                $form
                            );

                            // Проверяем успешную квоту.
                            $this->checkRateLimit(
                                $request,
                                $form
                            );

                            // Проверяем CAPTCHA.
                            $this->checkCaptcha(
                                $request,
                                $form
                            );

                            // Сохраняем заявку.
                            $submission = $callback();

                            // Учитываем успешную отправку.
                            $this->recordSuccessfulSubmission(
                                $request,
                                $form
                            );

                            // Погашаем использованный токен.
                            $this->consumeToken(
                                $request,
                                $form
                            );

                            return $submission;
                        }
                    );
                }
            );
        } catch (LockTimeoutException $e) {
            $this->rejectTooManyRequests(
                'Форма обрабатывается. Повторите попытку позже.',
                self::LOCK_WAIT_SECONDS
            );
        }
    }

    /**
     * Зарегистрировать успешную отправку.
     */
    public function recordSuccessfulSubmission(
        Request $request,
        Form $form
    ): void {
        if (!$form->spam_protection) {
            return;
        }

        $minutes = min(
            self::MAX_RATE_WINDOW_MINUTES,
            max(1, (int) ($form->rate_limit_minutes ?: 10))
        );

        $this->limiter()->hit(
            $this->rateLimitKey($request, $form),
            $minutes * 60
        );
    }

    /**
     * Погасить одноразовый токен.
     */
    public function consumeToken(
        Request $request,
        Form $form
    ): void {
        if (!$form->spam_protection) {
            return;
        }

        $token = $request->input('_form_token');

        if ($this->isValidToken($token)) {
            $this->cache()->forget(
                $this->tokenKey($token)
            );
        }
    }

    /**
     * Проверить формат токена.
     */
    private function isValidToken(mixed $token): bool
    {
        return is_string($token)
            && preg_match('/^[A-Za-z0-9]{64}$/D', $token) === 1;
    }

    /**
     * Ответ при превышении ограничений.
     */
    private function rejectTooManyRequests(
        string $message,
        int $seconds
    ): never {
        $seconds = max(1, $seconds);

        throw new HttpResponseException(
            response()->json([
                'message' => $message,
                'retry_after' => $seconds,
                'errors' => [
                    '_form' => [
                        "{$message} Повторите через {$seconds} сек.",
                    ],
                ],
            ], 429)->header(
                'Retry-After',
                (string) $seconds
            )
        );
    }

    /**
     * Ключ токена.
     */
    private function tokenKey(string $token): string
    {
        return 'form_submission_token:'
            . hash('sha256', $token);
    }

    /**
     * Ключ блокировки токена.
     */
    private function tokenLockKey(string $token): string
    {
        return 'form_token_lock:'
            . hash('sha256', $token);
    }

    /**
     * Ключ успешной квоты.
     */
    private function rateLimitKey(
        Request $request,
        Form $form
    ): string {
        return 'form_submit_rate:'
            . $form->id
            . ':'
            . $this->clientIdentifier($request);
    }

    /**
     * Ключ ошибочных попыток.
     */
    private function failedRateLimitKey(
        Request $request,
        Form $form
    ): string {
        return 'form_submit_failed:'
            . $form->id
            . ':'
            . $this->clientIdentifier($request);
    }

    /**
     * Ключ блокировки успешной квоты.
     */
    private function rateLockKey(
        Request $request,
        Form $form
    ): string {
        return 'form_submit_lock:'
            . $form->id
            . ':'
            . $this->clientIdentifier($request);
    }

    /**
     * Идентификатор отправителя.
     *
     * Авторизованный пользователь — ID.
     * Гость — хеш IP.
     */
    private function clientIdentifier(Request $request): string
    {
        if ($request->user()) {
            return 'user:'
                . $request->user()->getAuthIdentifier();
        }

        return 'ip:'
            . hash('sha256', (string) $request->ip());
    }
}
