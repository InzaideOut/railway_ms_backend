<?php

namespace App\Http\Controllers\Api;

use App\Models\path;
use App\Models\route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class pathController extends Controller
{
    
    public function index($routeId)
    {
        $route = route::findOrFail($routeId);
        $paths = $route->paths;
        return response()->json($paths);
    }

    public function store(Request $request, $routeId)
    {
        $validator = Validator::make($request->all(), [
            'departure_city' => 'required|string',
            'arrival_city' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $path = new path();
        $path->route_id = $routeId;
        $path->departure_city = $request->departure_city;
        $path->arrival_city = $request->arrival_city;
        $path->save();

        return response()->json($path, 201);
    }

    public function show($routeId, $pathId)
    {
        $path = path::where('route_id', $routeId)->findOrFail($pathId);
        return response()->json($path);
    }

    public function update(Request $request, $routeId, $pathId)
    {
        $path = path::where('route_id', $routeId)->findOrFail($pathId);

        $validator = Validator::make($request->all(), [
            'departure_city' => 'required|string',
            'arrival_city' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $path->update($request->only('departure_city', 'arrival_city'));
        return response()->json($path);
    }

    public function destroy($routeId, $pathId)
    {
        $path = path::where('route_id', $routeId)->findOrFail($pathId);
        $path->delete();
        return response()->json(['message' => 'path deleted successfully']);
    }
}
