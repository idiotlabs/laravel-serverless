<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgiuijaRestaurant;
use Illuminate\Http\Request;

class AgiuijaController extends Controller
{
    public function index()
    {
        return response()->json(
            AgiuijaRestaurant::orderBy('createdAt', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'address'        => 'required|string|max:255',
            'lat'            => 'required|numeric',
            'lng'            => 'required|numeric',
            'hasHighChair'   => 'boolean',
            'hasBowlAndSpoon'=> 'boolean',
            'notes'          => 'nullable|string',
        ]);

        $restaurant = AgiuijaRestaurant::create($data);

        return response()->json($restaurant, 201);
    }

    public function destroy($id)
    {
        AgiuijaRestaurant::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, $id)
    {
        $restaurant = AgiuijaRestaurant::findOrFail($id);
        $restaurant->update($request->only([
            'name', 'address', 'lat', 'lng',
            'hasHighChair', 'hasBowlAndSpoon', 'notes',
        ]));
        return response()->json($restaurant);
    }
}
