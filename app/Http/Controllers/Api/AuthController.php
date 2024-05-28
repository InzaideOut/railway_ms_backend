<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //

    public function register(Request $request){

        // Validate request data
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]
        // ,[
        //     'name.required' => 'Your name is required.',
        //     'email.unique' => 'An account with this email address already exists.',

        // ]
         );

        // Create user
        $user = \App\Models\User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'phone' => $validated['phone'],
            'password' =>  Hash::make($validated['password']),
            'email' => $validated['email'],
            'role' => 'User',
        ]);

        // Return response
        return response()->json(['message' => 'Registration successful'], 201);

    }


    public function login(Request $request){
         // Validate request data
         $credentials = $request->only('email', 'password');

         if (!Auth::attempt($credentials)) {
             throw ValidationException::withMessages([
                 'email' => ['The provided credentials do not match our records.'],
             ]);
         }
 
         // Generate token
         $token = $request->user()->createToken('railway-token')->plainTextToken;
 
         // Return response
         return response()->json(['access_token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
