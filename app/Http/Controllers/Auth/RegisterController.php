<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller {

    protected $jwt;

    public function __construct(JWTAuth $jwt) {
        $this->jwt = $jwt;
    }

    public function register(Request $request) {
        $validator = Validator::make($request->only('name', 'email', 'password', 'password_confirmation'), [
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:4|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => 'Please fix these errors', 'errors' => $validator->errors()], 500);
        }

        try {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $token = $this->jwt->attempt($request->only($user->email, $user->password));


            // $token = auth('api')->tokenById($user->id);

            return response()->json([
                        'success' => 1,
                        'message' => 'User Registration success!',
                        'access_token' => $token,
                        'user' => $user
            ]);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['success' => 0, 'message' => 'User Registration Failed!'], 409);
        }
    }

}
