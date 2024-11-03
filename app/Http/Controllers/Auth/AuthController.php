<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * [POST]
     * /register
     */
    public function register(Request $request)
    {
        $data = $request->all();

        // return response()->json([
        //     'data' => $data
        // ]);

        $validator = Validator::make($data, [
            'username' => 'required|string',
            'password' => 'required|min:6'
        ]);

        try {
            //code...
            if ($validator->validate()) {
                $user = User::create([
                    'name' => $request->username,
                    'password' => Hash::make($request->password)
                ]);

                if ($user) {
                    return response()->json([
                        'status' => 200,
                        'success' => true,
                        'data' => $user
                    ]);
                } else {
                    return response()->json([
                        'status' => 400,
                        'success' => false,
                        'errors' => "Failed to register new user"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'errors' => $th->getMessage()
            ]);
        }
    }

    /**
     * [POST]
     * /login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'     => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to login", $validator->errors(), 400);
        }

        $credentials = $request->only('username', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return $this->error_json("Bad Credentials", false, 400);
        }


        return $this->success_json("Successfully Login", [
            'user'    => auth()->guard('api')->user(),
            'token'   => $token
        ]);
    }

    /**
     * [POST]
     * /logout
     */
    public function logout(Request $request)
    {
        //remove token
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        if ($removeToken) {
            //return response JSON
            return $this->success_json("Successfully logout", true);
        }
    }

    public function refresh(Request $request)
    {
        try {
            // Refresh the token
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            // Return the new token in the response
            return $this->success_json("Successfully refresh token", [
                'token' => $newToken
            ]);
        } catch (TokenExpiredException $e) {
            // Token expired, return 401 with a specific message
            return response()->json(['error' => 'Token has expired and cannot be refreshed. Please log in again.'], 401);
        } catch (TokenInvalidException $e) {
            // Token invalid, return 401 with a specific message
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            // Token is missing or another error occurred
            return response()->json(['error' => 'Token is missing or could not be refreshed.'], 401);
        }
    }
}
