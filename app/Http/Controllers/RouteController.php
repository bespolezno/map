<?php

namespace App\Http\Controllers;

use App\Http\Resources\Route;
use App\Place;
use App\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RouteController extends Controller
{

    public function search(Place $from_place, Place $to_place, $departure_time = null)
    {
        if (empty($departure_time))
            $departure_time = gmdate('H:i', now()->secondsSinceMidnight());
        $schedules = $from_place->getSchedules($departure_time);

        do {
            $schedules = $schedules
                ->map(function ($el) use ($departure_time) {
                    return $el->to_place->getSchedules($el->arrival_time, $el->visited, $el);
                })
                ->flatten(1)
                ->merge($schedules);
        } while ($schedules->where('to_place', $to_place)->unique(['path'])->count() < 5);

        $data = $schedules
            ->where('to_place', $to_place)
            ->sortBy('cost')
            ->unique(['path'])
            ->values();

        return response()->json([
            'number_of_history' => \App\Route::where(['hash' => implode(',', $data[0]['visited'])])
                    ->first()
                    ->number ?? 0,
            'schedules' => $data
                ->map(function ($el) {
                    return Route::collection(
                        collect($el->path)->map(function ($item) {
                            return Schedule::with(['from_place', 'to_place'])->find($item);
                        })
                    );
                })
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'from_place_id' => 'required|integer|exists:places,id',
            'to_place_id' => 'required|integer|exists:places,id',
            'schedule_id' => 'required|array',
            'schedule_id.*' => 'exists:schedules,id'
        ]);
        if ($validator->fails())
            return response()->json(['message' => 'Data cannot be processed'], 422);

        $route = \App\Route::firstOrCreate(
                ['hash' => implode(',', $request->schedule_id)],
                $request->all()
            );
        $route->increment('number');

        return response()->json(['message' => 'create success'], 200);
    }
}


