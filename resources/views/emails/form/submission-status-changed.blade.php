
@extends('emails.layouts.base')

@section('title', 'PulsarCMS — изменение статуса заявки')

@section('content')

    {{-- Заголовок уведомления --}}
    <h2 style="
        margin: 0 0 20px;
        font-size: 22px;
        line-height: 1.4;
        color: #0f172a;
    ">
        Статус заявки изменён
    </h2>

    <p style="
        margin: 0 0 20px;
        font-size: 14px;
        line-height: 1.7;
        color: #334155;
    ">
        В PulsarCMS изменился статус заявки.
        Ниже представлена информация об изменении.
    </p>

    {{-- Информация о заявке --}}
    <table
        role="presentation"
        cellpadding="0"
        cellspacing="0"
        border="0"
        width="100%"
        style="
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-collapse: collapse;
        "
    >
        <tbody>
        <tr>
            <td style="
                    padding: 20px;
                    font-size: 14px;
                    line-height: 1.7;
                    color: #334155;
                ">

                {{-- Номер заявки --}}
                <p style="margin: 0 0 12px;">
                    <strong>Номер заявки:</strong>
                    #{{ $submission->id }}
                </p>

                {{-- Название формы --}}
                <p style="margin: 0 0 12px;">
                    <strong>Форма:</strong>
                    {{ $submission->form?->title ?? ('#' . $submission->form_id) }}
                </p>

                {{-- Предыдущий статус --}}
                <p style="margin: 0 0 12px;">
                    <strong>Предыдущий статус:</strong>
                    <span style="color: #64748b;">
                            {{ $oldStatus }}
                        </span>
                </p>

                {{-- Новый статус --}}
                <p style="margin: 0 0 12px;">
                    <strong>Новый статус:</strong>
                    <strong style="color: #0f766e;">
                        {{ $newStatus }}
                    </strong>
                </p>

                {{-- Инициатор изменения --}}
                @if (!empty($changedBy))
                    <p style="margin: 0 0 12px;">
                        <strong>Изменил:</strong>
                        {{ $changedBy->name }}
                    </p>
                @endif

                {{-- Источник изменения --}}
                <p style="margin: 0 0 12px;">
                    <strong>Источник изменения:</strong>
                    {{ $source }}
                </p>

                {{-- Комментарий к изменению --}}
                @if (!empty($comment))
                    <p style="margin: 0 0 8px;">
                        <strong>Комментарий:</strong>
                    </p>

                    <p style="
                            margin: 0;
                            white-space: pre-wrap;
                            color: #475569;
                        ">{{ $comment }}</p>
                @endif

            </td>
        </tr>
        </tbody>
    </table>

    {{-- Инструкция для получателя --}}
    <p style="
        margin: 22px 0 0;
        font-size: 14px;
        line-height: 1.7;
        color: #334155;
    ">
        Откройте административную панель PulsarCMS,
        чтобы просмотреть заявку и историю изменения статусов.
    </p>

@stop
