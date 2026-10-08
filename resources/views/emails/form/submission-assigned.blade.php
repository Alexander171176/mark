
@extends('emails.layouts.base')

@section('title', 'PulsarCMS — назначена заявка')

@section('content')

    <h2 style="
        margin: 0 0 20px;
        font-size: 22px;
        line-height: 1.4;
        color: #0f172a;
    ">
        Вам назначена заявка
    </h2>

    <p style="margin: 0 0 12px;">
        Здравствуйте, {{ $assignedUser->name }}!
    </p>

    <p style="margin: 0 0 20px;">
        Вы назначены ответственным сотрудником
        за обработку заявки.
    </p>

    <table
        role="presentation"
        cellpadding="0"
        cellspacing="0"
        border="0"
        width="100%"
        style="
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        "
    >
        <tr>
            <td style="padding: 20px;">

                <p style="margin: 0 0 12px;">
                    <strong>Номер заявки:</strong>
                    #{{ $submission->id }}
                </p>

                <p style="margin: 0 0 12px;">
                    <strong>Форма:</strong>
                    {{ $submission->form?->title ?? ('#' . $submission->form_id) }}
                </p>

                <p style="margin: 0 0 12px;">
                    <strong>Дата поступления:</strong>
                    {{ $submission->created_at?->format('d.m.Y H:i') }}
                </p>

                @if($assignedBy)
                    <p style="margin: 0;">
                        <strong>Назначил:</strong>
                        {{ $assignedBy->name }}
                    </p>
                @endif

            </td>
        </tr>
    </table>

    <p style="margin: 22px 0 0;">
        Откройте административную панель PulsarCMS,
        чтобы ознакомиться с заявкой и приступить
        к её обработке.
    </p>

@endsection
