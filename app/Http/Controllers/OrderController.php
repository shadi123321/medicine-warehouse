<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //2.3
    public function makeOrder(Request $request)
    {
        $pharmacist_id =  auth()->user()->id;
        //validation
        $request->validate([
            'medication.*' => 'required|string|max:255',
            'quantity.*' => 'required|integer|min:1',
        ]);

        $order = new Order();
        $order->pharmacist_id = $pharmacist_id;
        $order->save();

        $medications = $request->medication;
        $quantities = $request->quantity;

        if (count($medications) === count($quantities)) {
            for ($i = 0; $i < count($medications); $i++) {
                $medication = $medications[$i];
                $quantity = $quantities[$i];
                $medication_id = Medicine::where('name', $medication)->first()->id;
                $order->medicine()->attach($medication_id, ['quantity' => $quantity]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'there is no quantity input for a certain mediciation',
            ]);
        }
        $data = Order::where('id', $order->id)->first();
        return response()->json([
            'status' => 1,
            'message' => 'the order is created successfuly',
            'data' => $data
        ], 200);
    }


    //2.4
    public function getPharmacistOrders()
    {
        $pharmacist =  auth()->user();
        $orders = Order::where('pharmacist_id', $pharmacist->id)->with('medicine')->get();
        return response()->json([
            "status" => 1,
            "message" => "all orders of the pharmacist $pharmacist->name",
            "data" => $orders,
        ], 200);
    }


    public function getAllOrders()
    {
        $orders = Order::with('medicine')->get();
        return response()->json([
            "status" => 1,
            "message" => "all orders",
            "data" => $orders,
        ], 200);
    }


    //2.5
    public function changeOrderStatus(Request $request)
    {
        $order_id = $request->order_id;
        $status = $request->status;
        $order = Order::find($order_id);
        if (!isset($order)) {
            return response()->json([
                'status' => 0,
                'message' => "invalid order id",
            ]);
        }
        switch ($status) {
            case 'pending':
                $order->status = $status;
                $order->isPaid = false;
                $order->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'status changed successfully',
                    'order status' => $order->status,
                    'isPaid' => $order->isPaid
                ]);
            case 'sending':
                $orders = $order->medicine()->wherePivot('order_id', $order_id)->get();
                $i = 0;
                $medicines = [];
                foreach ($orders as $o) {
                    $medicine = Medicine::find($o->pivot->medicine_id);
                    $medicines[$i] = $medicine->warehouse()->get();
                    $i++;
                }
                for ($m = 0; $m < count($medicines); $m++) {
                    if ($medicines[$m][0]->pivot->availableQuantity < $o->pivot->quantity) {
                        return response()->json([
                            'status' => 0,
                            'message' => "not enough available quantity of the medicine",
                        ]);
                    }
                    $medicines[$m][0]->pivot->availableQuantity -= $o->pivot->quantity;
                    $medicines[$m][0]->pivot->save();
                }
                $order->status = $status;
                $order->isPaid = false;
                $order->save();
                return response()->json([
                    'status' => 1,
                    'message' => "The order status was updated successfully.",
                    'order status' => $order->status,
                    'isPaid' => $order->isPaid
                ], 200);
            case 'done':
                $order->status = $status;
                $order->isPaid = true;
                $order->save();
                return response()->json([
                    'status' => 1,
                    'message' => "The payment status was updated successfully.",
                    'order status' => $order->status,
                    'isPaid' => $order->isPaid
                ], 200);
            default:
                return response()->json([
                    'status' => 0,
                    'message' => 'invalid status'
                ]);
        }
    }
}
