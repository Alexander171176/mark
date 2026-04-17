<?php

namespace App\Http\Resources\Admin\System\Redirect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RedirectResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'from_path'      => $this->from_path,
            'to_url'         => $this->to_url,
            'code'           => $this->code,
            'preserve_query' => $this->preserve_query,
            'locale'         => $this->locale,
            'activity'       => $this->activity,
            'hits'           => $this->hits,
            'last_used_at'   => optional($this->last_used_at)?->toIso8601String(),
            'notes'          => $this->notes,
            'meta'           => $this->meta,

            // Удобное производное поле
            'is_permanent'   => in_array((int)$this->code, [301, 308], true),

            'created_at'     => optional($this->created_at)?->toIso8601String(),
            'updated_at'     => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'     => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
