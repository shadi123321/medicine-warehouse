<?php

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ManagerController;
use App\Http\Controllers\Api\PharmacistController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WarehouseController;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// WITHOUT AUTHENTICATION //
Route::middleware('setapplang')->prefix('{locale}')->group(function () {
    //- PharmacistController
    Route::post("pharmaist_register", [PharmacistController::class, "pharmacist_register"]);
    Route::post("pharmacist_login", [PharmacistController::class, "pharmacist_login"]);
    //- ManagerController
    Route::post("manager_register", [ManagerController::class, "manager_register"]);
    Route::post("manager_login", [ManagerController::class, "manager_login"]);
});

Route::post("search", [MedicineController::class, "search"]);
//this is the logout without the prefix of the language
Route::middleware(['auth:sanctum'])->group(function () {
    //- CommonController
    Route::get("{locale}/logout", [CommonController::class, "logout"])->middleware('setapplang');
    //- MedicineController
    Route::post("ManagerAddMedicine", [MedicineController::class, "ManagerAddMedicine"]);
    Route::get("get_category_medicines/{category_id}", [MedicineController::class, "get_category_medicines"]);
    Route::get("get_all_categories", [MedicineController::class, "get_all_categories"]);
    Route::get("get_medicine_info/{med_id}", [MedicineController::class, "get_medicine_info"]);
    Route::post("createCategory", [MedicineController::class, "createCategory"]);
    //- OrderController
    Route::post("makeOrder", [OrderController::class, "makeOrder"]);
    Route::get("getPharmacistOrders", [OrderController::class, "getPharmacistOrders"]);
    Route::get("getAllOrders", [OrderController::class, "getAllOrders"]);
    Route::post("changeOrderStatus", [OrderController::class, "changeOrderStatus"]);
    //- WarehouseController
    Route::post("createWarehouse", [WarehouseController::class, "createWarehouse"]);
    //- FavoritesController
    Route::post('add_to_favorites/{medicine_id}', [FavoriteController::class, 'add_to_favorites']);
    Route::delete('remove_from_favorites/{medicine_id}', [FavoriteController::class, 'remove_from_favorites']);
    Route::get('get_favorites', [FavoriteController::class, 'get_favorites']);
});














// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //     return $request->user();
    // });

    // USING AUTHENTICATION //
    // Route::middleware(['auth:sanctum', 'SetAppLang'])->prefix('{local}')->group(function () {
    //     //- CommonController
    //     Route::get("logout", [CommonController::class, "logout"]);
    // });
