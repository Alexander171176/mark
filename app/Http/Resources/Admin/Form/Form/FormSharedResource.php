<?php

namespace App\Http\Resources\Admin\Form\Form;

use App\Http\Resources\Admin\System\User\UserSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSharedResource extends JsonResource
{
    /**
     * Компактное представление формы.
     *
     * Основное назначение:
     * - Admin Index;
     * - select/справочные списки;
     * - связанные сущности;
     * - заявки формы.
     *
     * Resource читает только заранее загруженные
     * relations/counts и не должен создавать
     * дополнительные SQL-запросы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslationOrFallback();

        return [
            /** Основные данные */
            'id' => (int) $this->id,

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            'code' => $this->code,
            'status' => $this->status,

            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,

            /** Владелец */
            'owner' => new UserSharedResource(
                $this->whenLoaded('user')
            ),

            /** Перевод текущей локали с fallback */
            'translation' => $translation
                ? new FormTranslationResource($translation)
                : null,

            /** Счётчики */
            'fields_count' => $this->whenCounted('fields'),
            'submissions_count' => $this->whenCounted('submissions'),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
