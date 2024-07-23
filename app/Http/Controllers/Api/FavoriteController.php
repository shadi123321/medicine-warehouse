<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Favorites
    public function add_to_favorites($medicine_id)
    {
        $pharmacist = auth()->user();
        $favorites = Favorite::get();
        foreach ($favorites as $item) {
            if ($item->pharmacist_id == $pharmacist->id && $item->medicine_id == $medicine_id) {
                return response()->json([
                    "status" => 0,
                    "message" => 'The medicine already in your favorites list',
                ]);
            }
        }
        $favorite = Favorite::create([
            "pharmacist_id" => $pharmacist->id,
            "medicine_id" => $medicine_id,
        ]);
        return response()->json([
            "status" => 1,
            "message" => "medicine Added Successfully To Your Favorites List",
            "data" => $favorite
        ], 200);
    }

    public function remove_from_favorites($medicine_id)
    {
        $pharmacist = auth()->user();
        $favorite = Favorite::where('pharmacist_id', $pharmacist->id)->where('medicine_id', $medicine_id)->first();
        if (!isset($favorite)) {
            return response()->json([
                'status' => 0,
                'message' => 'there is no medicine with this id to remove'
            ]);
        }
        Favorite::where('pharmacist_id', $pharmacist->id)->where('medicine_id', $medicine_id)->forceDelete();
        return response()->json([
            'status' => 1,
            'message' => 'Medicine Removed from The Favorites List'
        ]);
    }

    public function get_favorites()
    {
        $pharmacist = auth()->user();
        $favorites = Favorite::where('pharmacist_id', $pharmacist->id)->get();
        return response()->json([
            'status' => 1,
            'data' => $favorites
        ]);
    }
}
