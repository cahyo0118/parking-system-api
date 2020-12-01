<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $validators = Validator::make($request->all(), [
            "username" => "required",
            "password" => "required",
        ]);

        if ($validators->fails()) {
            return response()->json([
                'success' => false,
                'body' => $validators->errors(),
                'message' => 'Failed to login',
            ], 400);
        }


        $user = User::where('email', $request->username)
            ->first();

        if (empty($user) || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to login, user not found',
            ], 400);
        }

        /*Register to Auth Service Provider*/
        Auth::guard('api')->login($user);
        $auth = auth('api')->user();

        $token = auth('api')->login($auth);

        return response()->json([
            'success' => true,
            'body' => [
                'credential' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ],
                'user' => $user,
            ],
            'message' => 'Successfully logged in',
        ]);
    }
}
