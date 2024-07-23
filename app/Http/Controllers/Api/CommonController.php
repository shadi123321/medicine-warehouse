<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    // LOGOUT API
    public function logout()
    {
        auth()->user()->Tokens()->delete();
        return response()->json([
            "status" => 1,
            "message" => __("messages.Logged Out Successfully")
        ]);
    }
}
