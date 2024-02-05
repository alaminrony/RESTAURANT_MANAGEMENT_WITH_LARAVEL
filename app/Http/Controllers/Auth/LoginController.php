<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class LoginController extends Controller {

    protected $jwt;

    public function __construct(JWTAuth $jwt) {
        $this->jwt = $jwt;
    }

    public function login(Request $request) {

        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->only('email', 'password'), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Please fix these errors', 'errors' => $validator->errors()], 500);
        }

        try {

            $token = $this->jwt->attempt($request->only('email', 'password'));
            if (!$token) {
                return response()->json(['success' => 0, 'message' => 'user not found'], 404);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['success' => 0, 'message' => 'token expired'], 500);
        } catch (TokenInvalidException $e) {

            return response()->json(['success' => 0, 'message' => 'token invalid'], 500);
        } catch (JWTException $e) {

            return response()->json(['success' => 0, 'message' => 'unknown error'], 500);
        }

        // if everything ok
        $user = Auth::user();

        return response()->json(['success' => 1, 'access_token' => $token, 'user' => $user]);
    }

    public function userDetails() {
        $user = Auth::user();

        return response()->json([
                    'user' => $user
        ]);
    }

    public function logout() {


        Auth::logout();

        return response()->json([
                    'success' => 1,
                    'message' => 'Signed out successfully!'
        ]);
    }

    public function checkLogin() {
        if (Auth::user()) {
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0]);
    }

}
