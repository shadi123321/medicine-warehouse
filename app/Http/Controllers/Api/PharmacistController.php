<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pharmacist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//1.1
class PharmacistController extends Controller
{
    // PHARMACIST REGESTER
    public function pharmacist_register(Request $request)
    {
        // validation
        $request->validate([
            "name" => 'required|alpha_dash',
            "address" => 'required',
            "phone" => 'required|unique:pharmacists|regex:/^\+?\d{10,13}$/',
            "password" => 'required|confirmed',
        ]);
        // create pharmacist
        $pharmacist = new Pharmacist();
        $pharmacist->name = $request->name;
        $pharmacist->address = $request->address;
        $pharmacist->phone = $request->phone;
        $pharmacist->password = Hash::make($request->password);
        $pharmacist->save();
        $token = $pharmacist->createToken("token")->plainTextToken;
        return response()->json([
            "status" => 1,
            "messege" => __("messages.Pharmacist Created Sccessfully"),
            "token" => $token,
            "data" => $pharmacist
        ]);
    }


    // LOGIN API
    //1.2
    public function pharmacist_login(Request $request)
    {
        // validation
        $request->validate([
            "phone" => "required|regex:/^\+?\d{10,13}$/",
            "password" => "required"
        ]);
        // check user
        $pharmacist = Pharmacist::where("phone", $request->phone)->first();
        if (isset($pharmacist)) {
            if (Hash::check($request->password, $pharmacist->password)) {
                // create token
                $token = $pharmacist->createToken("pharmacist_token")->plainTextToken;
                // send response
                return response()->json([
                    "status" => 1,
                    "message" => __("messages.Pharmacist Loged In Succesfully"),
                    "token" => $token,
                    "data" => $pharmacist
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "messege" => __("messages.password didn't match")
                ]);
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => __("messages.Not Found")
            ]);
        }
    }
}
