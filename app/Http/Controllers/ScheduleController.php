<?php

namespace App\Http\Controllers;

use App\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'type' => 'required|string|in:TRAIN,BUS',
            'line' => 'required|integer|gte:0',
            'from_place_id' => 'required|integer|exists:places,id',
            'to_place_id' => 'required|integer|exists:places,id',
            'departure_time' => 'required|date_format:H:i:s',
            'arrival_time' => 'required|date_format:H:i:s|after:departure_time',
            'distance' => 'required|integer|gte:0',
            'speed' => 'required|integer|gte:0',
            'status' => 'string|in:AVAILABLE,UNAVAILABLE',
        ]);

        if ($validator->fails())
            return response()->json(['message' => 'Data cannot be processed'], 422);

        Schedule::create($request->all());

        return response()->json(['message' => 'create success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Schedule $schedule
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response()->json(['message' => 'delete success'], 200);
    }
}
