<?php

namespace App\Http\Resources\V1;

use App\Models\Project;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'status'     => $this->status,
            'utility_id' => $this->utility_id,
            'project_id' => $this->project_id
        ];
    }
}
