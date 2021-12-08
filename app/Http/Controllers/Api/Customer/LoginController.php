<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        //validate
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials email and password
        $credentials = $request->only('email', 'password');

        //check, if email or passowrd incorrect
        if (!$token = auth()->guard('api_customer')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password anda salah',

            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user(),
            'token' => $token
        ], 200);
    }

    public function getCustomer()
    {
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user(),
        ], 200);
    }

    public function refreshToken(Request $request)
    {
        //refresh token
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        //set customer with new token
        $user = JWTAuth::setToken($refreshToken)->toUser();

        //set headers authorization with bearer and new token
        $request->headers->set('Authorization', 'Bearer' . $refreshToken);

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $refreshToken,
        ], 200);
    }

    public function logout()
    {
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true
        ], 200);
    }
}
