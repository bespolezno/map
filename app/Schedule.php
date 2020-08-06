<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public $timestamps = false;
    protected $fillable = ['type', 'line', 'from_place_id', 'to_place_id', 'departure_time', 'arrival_time', 'distance', 'speed', 'status'];
    protected $casts = [
        'departure_time' => 'datetime:H:i:s',
        'arrival_time' => 'datetime:H:i:s'
    ];

    public function from_place()
    {
        return $this->belongsTo(Place::class, 'from_place_id', 'id');
    }

    public function to_place()
    {
        return $this->belongsTo(Place::class, 'to_place_id', 'id');
    }

    public function cost($time = null)
    {
        $time = $time ?? $this->departure_time;
        return $this->arrival_time->diffInSeconds($time);
    }

    public function travelTime()
    {
        return gmdate('H:i', $this->cost());
    }
}
