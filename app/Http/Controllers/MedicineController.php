<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    //1.3
    public function ManagerAddMedicine(Request $request)
    {
        //validate
        $request->validate([
            "category_id" => "required|numeric",
            "name" => "required|string",
            "scientificName" => "required|string",
            "tradeName" => "required|string",
            "manufacturer" => "required|string",
            "medImage" => "required",
            "expirationDate" => "required",
            "price" => "required|numeric",
            "warehouseName" => "required|string",
            "quantity" => "required|numeric",
        ]);
        $med = Medicine::where('name', $request->name)->first();
        if (isset($med)) {
            $medicineWarehouse = MedicineWarehouse::where('medicine_id', $med->id)->first();
            $medicineWarehouse->availableQuantity += $request->quantity;
            $medicineWarehouse->save();
            return response()->json([
                "status" => 1,
                "messege" => "medicine Added Successfully",
                "data" => $med
            ], 200);
        }
        $medicine = new Medicine();
        $medicine->category_id = $request->category_id;
        $medicine->name = $request->name;
        $medicine->scientificName = $request->scientificName;
        $medicine->tradeName = $request->tradeName;
        $medicine->manufacturer = $request->manufacturer;
        $medicine->medImage = 'storage/' . $request->file('medImage')->store('images', 'public');
        $medicine->expirationDate = $request->expirationDate;
        $medicine->price = $request->price;
        $medicine->save();

        $warehouseId = Warehouse::where('name', $request->warehouseName)->first()->id;
        $medicine->warehouse()->attach($warehouseId, ['availableQuantity' => $request->quantity]);

        return response()->json([
            "status" => 1,
            "messege" => "medicine Created Successfully",
            "data" => $medicine
        ], 200);
    }


    //1.4
    public function get_category_medicines($category_id)
    {
        $category = Category::where('id', $category_id)->first();
        if (!isset($category)) {
            return response()->json([
                "status" => 0,
                'message' => "Invalid category id"
            ], 400);
        }
        $category_meds = Category::where('id', $category_id)->with('medicine')->get();
        return response()->json([
            "status" => 1,
            'data' => $category_meds[0],
        ], 200);
    }


    public function get_all_categories()
    {
        $categories_meds = Category::with('medicine')->get();
        return response()->json([
            "status" => 1,
            'data' => $categories_meds,
        ], 200);
    }


    //2.2
    public function get_medicine_info($med_id)
    {
        $medicine = Medicine::where('id', $med_id)->first();
        if (!isset($medicine)) {
            return response()->json([
                "status" => 0,
                'message' => "Invalid medicine id"
            ], 400);
        }
        return response()->json([
            "status" => 1,
            "data" => $medicine,
        ], 200);
    }


    //2.1
    public function search(Request $request)
    {
        $category = Category::query();
        $medicine = Medicine::query();
        $search = $request->search_name;
        if ($search) {
            if ($category->where('categoryName', 'like', '%' . $search . '%')->exists() && $medicine->where('name', 'like', '%' . $search . '%')->exists()) {
                return response()->json([
                    'data' => ['medicines' => $medicine->get(['id', 'name']), 'categories' => $category->get()]
                ]);
            } else if ($category->where('categoryName', 'like', '%' . $search . '%')->exists()) {
                return response()->json([
                    'data' => ['medicines' => [], 'categories' => $category->get()]
                ]);
            } else if ($medicine->where('name', 'like', '%' . $search . '%')->exists()) {
                return response()->json([
                    'data' => ['medicines' => $medicine->get(['id', 'name']), 'consults' => []]
                ]);
            }
        }

        return response()->json([
            'message' => 'There Is No medicine or category With This Name',
            'data' => []
        ], 422);
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'categoryName' => 'required|string|unique:categories',
            'categoryImage' => 'required'
        ]);

        $category = new Category();
        $category->categoryName = $request->categoryName;
        $category->categoryImage = 'storage/' . $request->file('categoryImage')->store('images', 'public');
        $category->save();

        return response()->json([
            "status" => 1,
            "message" => "category created successfully",
            "data" => $category
        ], 200);
    }
}
