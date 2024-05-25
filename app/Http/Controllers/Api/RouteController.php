<?php

namespace App\Http\Controllers\Api;

use App\Models\route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class routeController extends Controller
{
    //
    public function index()
    {
        $routes = route::all();
        return response()->json($routes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:routes,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $route = route::create($request->only('name'));
        return response()->json($route, 201);
    }

    public function show($id)
    {
        $route = route::findOrFail($id);
        return response()->json($route);
    }

    public function update(Request $request, $id)
    {
        $route = route::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:routes,name,' . $route->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $route->update($request->only('name'));
        return response()->json($route);
    }

    public function destroy($id)
    {
        $route = route::findOrFail($id);
        $route->delete();
        return response()->json(['message' => 'route deleted successfully']);
    }
}
