<?php

namespace App\Models\Admin\Form\FormSubmissionFile;

use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FormSubmissionFile extends Model
{
    use HasFactory;

    protected $table = 'form_submission_files';

    protected $fillable = [
        'form_submission_id',
        'form_field_id',

        // Snapshot поля
        'field_name',
        'field_label',

        // Файл
        'original_name',
        'file_name',
        'path',
        'disk',

        // Метаданные
        'mime_type',
        'extension',
        'size',
        'sort',
        'metadata',
    ];

    protected $casts = [
        'form_submission_id' => 'integer',
        'form_field_id' => 'integer',

        'size' => 'integer',
        'sort' => 'integer',

        'metadata' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Заявка, которой принадлежит файл.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(
            FormSubmission::class,
            'form_submission_id'
        );
    }

    /**
     * Исходное file-поле конструктора.
     *
     * Может вернуть null, если поле формы
     * впоследствии было удалено.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(
            FormField::class,
            'form_field_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | File helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Проверить существование физического файла.
     */
    public function existsOnDisk(): bool
    {
        if (!$this->path || !$this->disk) {
            return false;
        }

        return Storage::disk($this->disk)
            ->exists($this->path);
    }

    /**
     * Удалить физический файл из хранилища.
     */
    public function deleteFromDisk(): bool
    {
        if (!$this->path || !$this->disk) {
            return false;
        }

        if (!$this->existsOnDisk()) {
            return true;
        }

        return Storage::disk($this->disk)
            ->delete($this->path);
    }

    /**
     * Получить отдельное значение metadata.
     *
     * Поддерживается dot notation.
     */
    public function getMetadata(
        string $key,
        mixed $default = null
    ): mixed {
        return data_get(
            $this->metadata ?? [],
            $key,
            $default
        );
    }

    /**
     * Размер файла в килобайтах.
     */
    public function getSizeInKb(): ?float
    {
        if ($this->size === null) {
            return null;
        }

        return round(
            $this->size / 1024,
            2
        );
    }

    /**
     * Размер файла в мегабайтах.
     */
    public function getSizeInMb(): ?float
    {
        if ($this->size === null) {
            return null;
        }

        return round(
            $this->size / 1024 / 1024,
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Файлы конкретного поля по snapshot field_name.
     */
    public function scopeForFieldName(
        Builder $query,
        string $fieldName
    ): Builder {
        return $query->where(
            'field_name',
            $fieldName
        );
    }

    /**
     * Файлы конкретного поля конструктора.
     */
    public function scopeForField(
        Builder $query,
        int $fieldId
    ): Builder {
        return $query->where(
            'form_field_id',
            $fieldId
        );
    }

    /**
     * Файлы определённого расширения.
     */
    public function scopeOfExtension(
        Builder $query,
        string $extension
    ): Builder {
        return $query->where(
            'extension',
            strtolower($extension)
        );
    }

    /**
     * Сортировка файлов.
     */
    public function scopeSorted(Builder $query): Builder
    {
        return $query
            ->orderBy('sort')
            ->orderBy('id');
    }
}
