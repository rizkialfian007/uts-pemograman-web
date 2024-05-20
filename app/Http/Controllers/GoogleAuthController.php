<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{


    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }


    public function callback()
    {
        $user = Socialite::driver('google')->user();
        return response()->json([
            'status' => true,
            'data' => $user
        ], 200);
    }
}
