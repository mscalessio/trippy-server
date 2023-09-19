<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'numeric', 'min:10'],
        ]);

        // find or create a user model
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'Could not process a user with that phone number.'
            ], 401);
        }

        // send the user a one-time use code
        // $user->notify(new LoginNeedsVerification());
        // for testing purposes, we'll just return the code
        $user->update([
            'login_code' => 123456,
        ]);

        return response()->json([
            'message' => 'Text message notification sent.'
        ]);
    }

    public function verify(Request $request)
    {
        // validate the request
        $request->validate([
            'phone' => ['required', 'numeric', 'min:10'],
            'login_code' => ['required', 'numeric', 'between:111111,999999'],
        ]);

        // find the user
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        // check the login code
        // if it matches, return back an auth token
        if($user) {
            $user->update([
                'login_code' => null,
            ]);

            return $user->createToken($request->login_code)->plainTextToken;
        }

        // if it doesn't match, return back an error message
        return response()->json([
            'message' => 'Invalid verification code.'
        ], 401);
    }
}
