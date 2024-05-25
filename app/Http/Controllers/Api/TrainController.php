<?php

namespace App\Http\Controllers\Api;

use App\Models\train;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class trainController extends Controller
{
    public function index()
    {
        $trains = train::all();
        return response()->json($trains);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:trains,name',
            'capacity' => 'required|integer|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $train = train::create($request->only('name', 'capacity'));
        return response()->json($train, 201);
    }

    public function show($id)
    {
        $train = train::findOrFail($id);
        return response()->json($train);
    }

    public function update(Request $request, $id)
    {
        $train = train::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:trains,name,' . $train->id,
            'capacity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $train->update($request->only('name', 'capacity'));
        return response()->json($train);
    }

    public function destroy($id)
    {
        $train = train::findOrFail($id);
        $train->delete();
        return response()->json(['message' => 'train deleted successfully']);
    }
}
