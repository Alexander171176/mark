<?php

namespace App\Services\Admin\Notification;

use App\Services\SiteSettings\AdminSettingsService;
use InvalidArgumentException;

class EmailSettingsService
{
    /**
     * Поддерживаемые события Email Notifications.
     *
     * В дальнейшем добавим события Marketplace,
     * School, Blog и CRM.
     */
    private const EVENT_SETTINGS = [
        'form_submission_created'
        => 'adminEmailFormSubmissionCreatedEnabled',

        'form_submission_assigned'
        => 'adminEmailFormSubmissionAssignedEnabled',

        'form_submission_status_changed'
        => 'adminEmailFormSubmissionStatusChangedEnabled',

        'form_submission_confirmation'
        => 'adminEmailFormSubmissionConfirmationEnabled',
    ];

    /**
     * Разрешённые почтовые транспорты
     * для управления через Setting.
     */
    private const ALLOWED_MAILERS = [
        'smtp',
        'log',
        'array',
    ];

    public function __construct(
        private readonly AdminSettingsService $settings
    ) {
    }

    /**
     * Глобальное разрешение Email Notifications.
     */
    public function enabled(): bool
    {
        return $this->settings->bool(
            'adminEmailNotificationsEnabled',
            false
        );
    }

    /**
     * Проверить разрешение конкретного события.
     *
     * Неизвестные события запрещаются по умолчанию.
     */
    public function eventEnabled(string $event): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $option = self::EVENT_SETTINGS[$event] ?? null;

        if ($option === null) {
            return false;
        }

        return $this->settings->bool($option, false);
    }

    /**
     * Получить список зарегистрированных событий
     * и их текущих разрешений.
     */
    public function events(): array
    {
        $result = [];

        foreach (self::EVENT_SETTINGS as $event => $option) {
            $result[$event] = $this->eventEnabled($event);
        }

        return $result;
    }

    /**
     * Почтовый транспорт.
     *
     * При некорректном значении запрещаем отправку
     * через непредусмотренный транспорт.
     */
    public function mailer(): string
    {
        $mailer = strtolower(trim(
            $this->settings->string(
                'adminEmailMailer',
                'smtp'
            )
        ));

        if (!in_array($mailer, self::ALLOWED_MAILERS, true)) {
            throw new InvalidArgumentException(
                'Недопустимый почтовый транспорт PulsarCMS.'
            );
        }

        return $mailer;
    }

    /**
     * SMTP-хост.
     */
    public function smtpHost(): string
    {
        return trim(
            $this->settings->string(
                'adminEmailSmtpHost',
                ''
            )
        );
    }

    /**
     * SMTP-порт.
     */
    public function smtpPort(): int
    {
        $port = $this->settings->int(
            'adminEmailSmtpPort',
            587
        );

        if ($port < 1 || $port > 65535) {
            throw new InvalidArgumentException(
                'Недопустимый SMTP-порт PulsarCMS.'
            );
        }

        return $port;
    }

    /**
     * Режим шифрования SMTP.
     *
     * none -> null
     * tls  -> tls
     * ssl  -> ssl
     */
    public function smtpEncryption(): ?string
    {
        $encryption = strtolower(trim(
            $this->settings->string(
                'adminEmailSmtpEncryption',
                'none'
            )
        ));

        return match ($encryption) {
            'none', '' => null,
            'tls', 'ssl' => $encryption,
            default => throw new InvalidArgumentException(
                'Недопустимый режим шифрования SMTP.'
            ),
        };
    }

    /**
     * SMTP-логин.
     */
    public function smtpUsername(): ?string
    {
        $username = trim(
            $this->settings->string(
                'adminEmailSmtpUsername',
                ''
            )
        );

        return $username !== '' ? $username : null;
    }

    /**
     * Адрес отправителя.
     */
    public function fromAddress(): string
    {
        $address = trim(
            $this->settings->string(
                'adminEmailFromAddress',
                ''
            )
        );

        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'Некорректный Email отправителя PulsarCMS.'
            );
        }

        return $address;
    }

    /**
     * Имя отправителя.
     */
    public function fromName(): string
    {
        return trim(
            $this->settings->string(
                'adminEmailFromName',
                'PulsarCMS'
            )
        );
    }

    /**
     * Язык писем по умолчанию.
     */
    public function defaultLocale(): string
    {
        $locale = strtolower(trim(
            $this->settings->string(
                'adminEmailNotificationsDefaultLocale',
                'ru'
            )
        ));

        return in_array($locale, ['ru', 'kk', 'en'], true)
            ? $locale
            : 'ru';
    }

    /**
     * Разрешена ли отправка через очередь.
     */
    public function queueEnabled(): bool
    {
        return $this->settings->bool(
            'adminEmailNotificationsQueueEnabled',
            false
        );
    }

    /**
     * Имя очереди Email Notifications.
     */
    public function queueName(): string
    {
        $name = trim(
            $this->settings->string(
                'adminEmailNotificationsQueueName',
                'emails'
            )
        );

        return preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $name)
            ? $name
            : 'emails';
    }

    /**
     * Параметры отправителя.
     */
    public function sender(): array
    {
        return [
            'address' => $this->fromAddress(),
            'name' => $this->fromName(),
        ];
    }

    /**
     * Подготовить конфигурацию SMTP.
     *
     * Важно: пароль здесь пока не используется.
     * Его защищённое хранение реализуем отдельно.
     */
    public function smtpConfig(): array
    {
        return [
            'transport' => 'smtp',
            'host' => $this->smtpHost(),
            'port' => $this->smtpPort(),
            'encryption' => $this->smtpEncryption(),
            'username' => $this->smtpUsername(),
            'password' => null,
            'timeout' => 10,
        ];
    }

    /**
     * Получить безопасную сводку настроек.
     *
     * Пароли и другие секреты не возвращаются.
     */
    public function summary(): array
    {
        return [
            'enabled' => $this->enabled(),
            'mailer' => $this->mailer(),
            'sender' => $this->sender(),
            'locale' => $this->defaultLocale(),
            'queue_enabled' => $this->queueEnabled(),
            'queue_name' => $this->queueName(),
            'events' => $this->events(),
        ];
    }
}
