<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'latitude', 'longitude', 'x', 'y', 'image_path', 'description'];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'from_place_id', 'id');
    }

    public function getSchedules($time, $visited = [], $parent = null)
    {
        if (!$time instanceof Carbon)
            $time = Carbon::create($time);
        return $this
            ->schedules()
            ->with(['to_place'])
            ->where('departure_time', '>=', $time->toTimeString())
            ->whereNotIn('to_place_id', $visited)
            ->get()
            ->map(function ($el) use ($time, $parent, $visited) {
                $el['visited'] = array_merge($visited, [$el['from_place_id']]);
                if (!empty($parent)) {
                    $prevCost = $parent->cost;
                    $el['path'] = array_merge($parent->path, [$el['id']]);
                    $el['from_place_id'] = $parent->from_place_id;
                } else {
                    $el['path'] = [$el['id']];
                }
                $el['cost'] = $el->cost($time) + ($prevCost ?? 0);
                return $el;
            })
            ->groupBy('to_place_id')
            ->map(function ($el) {
                return collect($el)->sortBy('cost')->first();
            })
            ->values();
    }
}
