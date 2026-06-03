<?php

namespace App\Http\Resources\Admin\School\SchoolBookmark;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolBookmarkSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'bookmarkable_type' => $this->bookmarkable_type,
            'bookmarkable_id' => $this->bookmarkable_id,

            'is_favorite' => (bool) $this->is_favorite,
            'folder' => $this->folder,
            'position' => (int) $this->position,
        ];
    }
}
