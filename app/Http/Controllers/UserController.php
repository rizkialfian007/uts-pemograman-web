<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => false,
            'message' => "harap login terlebih dahulu"
        ], 401);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    }


    public function logout()
    {
        auth('api')->logout();
    }

    public function loginGoogle(Request $request)
    {
        $access_token = $request->bearerToken();
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

            $userdata = $response->json();

            if (isset($userdata['error'])) {
                return response()->json([
                    'status' => false, 'message' => 'invalid token (get new access token in postman)'
                ]);
            }

            $user = User::where('email', $userdata['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name'      => $userdata['name'],
                    'email'     => $userdata['email'],
                    'password'  => bcrypt('password'),
                ]);
            }
            $token = auth()->guard('api')->login($user);
            return response()->json([
                'success' => true,
                'user'    => $user,
                'token'   => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 'message' => $e->getMessage()
            ]);
        }
    }
}
