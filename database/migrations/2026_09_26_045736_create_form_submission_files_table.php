<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_submission_files', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Связи
            |--------------------------------------------------------------------------
            */

            // Заявка, к которой прикреплён файл.
            //
            // При удалении самой заявки связанные файлы
            // должны быть удалены из БД.
            // Физическое удаление файлов из хранилища
            // будет выполняться моделью / сервисом.
            $table->foreignId('form_submission_id')
                ->constrained('form_submissions')
                ->cascadeOnDelete();

            // Исходное поле конструктора формы.
            //
            // nullable намеренно:
            // поле формы может быть удалено позднее,
            // но информация о файле в старой заявке
            // должна сохраниться.
            $table->foreignId('form_field_id')
                ->nullable()
                ->constrained('form_fields')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Snapshot поля
            |--------------------------------------------------------------------------
            */

            // Системное имя поля на момент отправки.
            //
            // Например:
            // specification
            // attachment
            // drawings
            $table->string('field_name', 100);

            // Название поля на языке отправки формы.
            //
            // Например:
            // "Спецификация"
            // "Прикрепить чертежи"
            $table->string('field_label')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Файл
            |--------------------------------------------------------------------------
            */

            // Оригинальное имя файла,
            // которое было на устройстве пользователя.
            //
            // Например:
            // specification-office.xlsx
            $table->string('original_name');

            // Фактическое имя сохранённого файла.
            //
            // Оно не должно напрямую зависеть
            // от original_name.
            $table->string('file_name');

            // Путь относительно выбранного Laravel-диска.
            //
            // Например:
            // forms/submissions/150/uuid.pdf
            $table->string('path', 1000);

            // Laravel filesystem disk.
            //
            // Например:
            // local
            // public
            // s3
            $table->string('disk', 50)
                ->default('local');

            /*
            |--------------------------------------------------------------------------
            | Метаданные
            |--------------------------------------------------------------------------
            */

            // MIME-тип файла.
            //
            // Например:
            // application/pdf
            // application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
            $table->string('mime_type', 255)
                ->nullable();

            // Расширение файла без точки.
            //
            // Например:
            // pdf
            // docx
            // xlsx
            $table->string('extension', 50)
                ->nullable();

            // Размер файла в байтах
            $table->unsignedBigInteger('size')
                ->nullable();

            // Порядок файла внутри поля
            $table->unsignedInteger('sort')
                ->default(100);

            /*
            |--------------------------------------------------------------------------
            | Дополнительные данные
            |--------------------------------------------------------------------------
            */

            // Расширяемые метаданные файла.
            //
            // Пока может оставаться null.
            // В будущем здесь можно хранить, например:
            // размеры изображения,
            // результаты антивирусной проверки,
            // checksum и другие служебные данные.
            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Индексы
            |--------------------------------------------------------------------------
            */

            // Получение всех файлов заявки
            $table->index([
                'form_submission_id',
                'field_name',
                'sort',
            ]);

            // Получение файлов конкретного поля конструктора
            $table->index('form_field_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submission_files');
    }
};
