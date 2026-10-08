<!DOCTYPE html>
<html lang="@yield('lang', 'ru')">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">

    <title>@yield('title', 'Уведомление PulsarCMS')</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    width: 100%;
    background-color: #f1f5f9;
    font-family: Arial, Helvetica, sans-serif;
    color: #334155;
    -webkit-text-size-adjust: 100%;
">

<table
    role="presentation"
    cellpadding="0"
    cellspacing="0"
    border="0"
    width="100%"
    style="background-color: #f1f5f9;"
>
    <tr>
        <td
            align="center"
            style="padding: 32px 12px;"
        >
            <!-- Основной контейнер письма -->
            <table
                role="presentation"
                cellpadding="0"
                cellspacing="0"
                border="0"
                width="100%"
                style="
                    width: 100%;
                    max-width: 600px;
                    background-color: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-collapse: separate;
                    border-spacing: 0;
                "
            >
                <!-- Шапка -->
                <tr>
                    <td>
                        @include('emails.partials.header')
                    </td>
                </tr>

                <!-- Основное содержимое -->
                <tr>
                    <td style="
                        padding: 32px 28px;
                        font-size: 14px;
                        line-height: 1.7;
                        color: #334155;
                    ">
                        @yield('content')
                    </td>
                </tr>

                <!-- Подвал -->
                <tr>
                    <td>
                        @include('emails.partials.footer')
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
