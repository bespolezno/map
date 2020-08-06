<?php

namespace App\Http\Controllers;

use App\Place;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

require_once 'Poi.php';

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Place::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'required|image',
            'description' => 'string',
        ]);

        if ($validator->fails())
            return response()->json(['message' => 'Data cannot be processed'], 422);

        $path = $request->file('image')->storePublicly('place_images');
        $filename = explode('/', $path)[1];

        Place::create($request->all() +
            ['image_path' => $filename] +
            (new \PoiFactory())->calculate($request->all(['latitude', 'longitude']))
        );
        return response()->json(['message' => 'create success'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Place $place
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Place $place)
    {
        return response()->json($place, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Place $place
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Place $place)
    {
        $validator = validator($request->all(), [
            'name' => 'string|max:100',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'image' => 'image',
            'description' => 'string',
        ]);

        if ($validator->fails())
            return response()->json(['message' => 'Data cannot be updated'], 422);

        $data = $request->all();

        if ($request->has('image')) {
            $path = $request->file('image')->storePublicly('place_images');
            $filename = explode('/', $path)[1];
            $data['image_path'] = $filename;
        }

        if ($request->has(['latitude', 'longitude'])) {
            $coords = (new \PoiFactory())->calculate($place->toArray() + $data);
            $data = array_merge($data, $coords);
        }

        $place->update($data);
        return response()->json(['message' => 'update success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Place $place
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Place $place)
    {
        $place->delete();
        return response()->json(['message' => 'delete success'], 200);
    }
}
