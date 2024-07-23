<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function createWarehouse(Request $request)
    {
        $manager_id = auth()->user()->id;

        $request->validate([
            "name" => 'required|string|unique:warehouses',
            "location" => 'required|string',
        ]);
        $wareHouse = new Warehouse();
        $wareHouse->manager_id = $manager_id;
        $wareHouse->name = $request->name;
        $wareHouse->location = $request->location;
        $wareHouse->save();

        return response()->json([
            'status' => 1,
            'message' => 'warehouse created successfully',
        ], 200);
    }
}
