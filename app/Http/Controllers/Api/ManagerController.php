<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    // manager REGESTER
    public function manager_register(Request $request)
    { {
            // validation
            $request->validate([
                "name" => 'required|alpha_dash',
                "email" => 'required|unique:managers|email',
                "password" => 'required|confirmed',
            ]);
            // create manager
            $manager = new Manager();
            $manager->name = $request->name;
            $manager->email = $request->email;
            $manager->password = Hash::make($request->password);
            $manager->save();
            $token = $manager->createToken("token")->plainTextToken;
            return response()->json([
                "status" => 1,
                "messege" => __("messages.manager Created Sccessfully"),
                "token" => $token,
                "data" => $manager
            ]);
        }
    }


    // LOGIN API
    public function manager_login(Request $request)
    {
        // validation
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        // check user
        $manager = Manager::where("email", $request->email)->first();
        if (isset($manager)) {
            if (Hash::check($request->password, $manager->password)) {
                // create token
                $token = $manager->createToken("manager_token")->plainTextToken;
                // send response
                return response()->json([
                    "status" => 1,
                    "message" => __("messages.manager Loged In Succesfully"),
                    "token" => $token,
                    "data" => $manager
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
