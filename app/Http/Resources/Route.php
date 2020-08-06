<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Route extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'line' => $this->line,
            'departure_time' => $this->departure_time->format('H:i:s'),
            'arrival_time' => $this->arrival_time->format('H:i:s'),
            'distance' => $this->distance,
            'speed' => $this->speed,
            'status' => $this->status,
            'from_place' => Place::make($this->from_place),
            'to_place' => Place::make($this->to_place),
            'travel_time' => $this->travelTime()
        ];
    }
}
