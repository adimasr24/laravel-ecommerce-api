<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{  
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request) {

        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required',
        ]);
        
        // response error validasi
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // get email and password from input
        $credentials = $request->only('email', 'password');

        // check if email and password don't match
        if(!$token = auth()->guard('api_admin')->attempt($credentials)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Email or password is incorrect'
            ], 401);
        }

        // response login success with generating token
        return response()->json([
            'success'   => true,
            'user'      => auth()->guard('api_admin')->user(),
            'token'     => $token
        ], 200);

    }
    
    /**
     * getUser
     *
     * @return void
     */
    public function getUser() {
        return response()->json([
            'success'   => true,
            'user'      => auth()->guard('api_admin')->user()
        ], 200);
    }
    
    /**
     * refreshToken
     *
     * @param  mixed $request
     * @return void
     */
    public function refreshToken(Request $request) {
        // refresh token
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        // set user with new token
        $user = JWTAuth::setToken($refreshToken)->toUser();

        // set header authorization with bearer type and new token
        $request->headers->set('Authorization', 'Bearer '.$refreshToken);

        // response user data with new token
        return response()->json([
            'success'   => true,
            'user'      => $user,
            'token'     => $refreshToken,
        ], 200);
    }

    public function logout() {
        // remove token JWT
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        // response success logout
        return response()->json([
            'success'   => true,
        ], 200);

    }
}
