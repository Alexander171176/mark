<?php

namespace App\Models\Admin\Form\FormSubmissionValue;

use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmissionValue extends Model
{
    use HasFactory;

    protected $table = 'form_submission_values';

    /*
    |--------------------------------------------------------------------------
    | Mass assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'form_submission_id',
        'form_field_id',

        // Snapshot поля
        'field_name',
        'field_type',
        'field_label',

        // Значения
        'value',
        'value_json',
        'display_value',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'form_submission_id' => 'integer',
        'form_field_id' => 'integer',

        'value_json' => 'array',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Заявка, которой принадлежит значение.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(
            FormSubmission::class,
            'form_submission_id'
        );
    }

    /**
     * Исходное поле конструктора формы.
     *
     * Связь может вернуть null, если поле
     * впоследствии было удалено.
     *
     * Исторические данные при этом сохраняются
     * в snapshot-полях текущей модели.
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
    | Value helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Содержит ли запись структурированное значение.
     */
    public function hasJsonValue(): bool
    {
        return $this->value_json !== null;
    }

    /**
     * Получить машинное значение.
     *
     * Для обычных полей возвращается value.
     * Для структурированных полей возвращается value_json.
     */
    public function getRawValue(): mixed
    {
        if ($this->hasJsonValue()) {
            return $this->value_json;
        }

        return $this->value;
    }

    /**
     * Получить значение для отображения пользователю.
     *
     * Если при отправке был сохранён display_value,
     * используем его.
     *
     * Для структурированного значения возвращаем
     * сохранённый value_json.
     *
     * Иначе возвращаем обычное value.
     */
    public function getDisplayValue(): mixed
    {
        if ($this->display_value !== null) {
            return $this->display_value;
        }

        if ($this->hasJsonValue()) {
            return $this->value_json;
        }

        return $this->value;
    }

    /**
     * Является ли значение множественным.
     */
    public function isMultiple(): bool
    {
        return is_array(
            $this->value_json
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Значения конкретной заявки.
     */
    public function scopeForSubmission(
        Builder $query,
        int $submissionId
    ): Builder {
        return $query->where(
            'form_submission_values.form_submission_id',
            $submissionId
        );
    }

    /**
     * Значения конкретного поля
     * по snapshot field_name.
     */
    public function scopeForFieldName(
        Builder $query,
        string $fieldName
    ): Builder {
        return $query->where(
            'form_submission_values.field_name',
            $fieldName
        );
    }

    /**
     * Значения конкретного поля конструктора.
     */
    public function scopeForField(
        Builder $query,
        int $fieldId
    ): Builder {
        return $query->where(
            'form_submission_values.form_field_id',
            $fieldId
        );
    }

    /**
     * Значения определённого типа поля.
     */
    public function scopeOfType(
        Builder $query,
        string $fieldType
    ): Builder {
        return $query->where(
            'form_submission_values.field_type',
            $fieldType
        );
    }

    /**
     * Стабильный порядок значений заявки.
     *
     * Отдельного sort в таблице значений нет,
     * поэтому используем порядок создания записей.
     */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query->orderBy(
            'form_submission_values.id',
            'asc'
        );
    }
}
