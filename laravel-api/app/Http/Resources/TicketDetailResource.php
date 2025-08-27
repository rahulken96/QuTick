<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user'          => new UserResource($this->user),
            'title'         => $this->title,
            'code'          => $this->code,
            'description'   => $this->description,
            'status'        => [
                'number'    => $this->status,
                'name'      => match ($this->status) {
                    1 => 'In Progress',
                    2 => 'Resolved',
                    3 => 'Rejected',
                    default => 'Open',
                },
            ],
            'priority'      => [
                'number'    => $this->priority,
                'name'      => match ($this->priority) {
                    1 => 'Medium/Netral',
                    2 => 'High/Urgent',
                    default => 'Low',
                },
            ],
            'created_at'    => formatCreatedForHumans($this->created_at),
            'updated_at'    => formatCreatedForHumans($this->updated_at),
            'completed_at'  => formatCreatedForHumans($this->completed_at),
        ];
    }
}
