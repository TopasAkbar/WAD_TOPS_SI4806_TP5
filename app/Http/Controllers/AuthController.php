<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        /**
         * ==========1===========
         * Validasi data registrasi yang masuk
         */
        $validator = Validator::make($request -> all(), 
        [
            'name' => 'required|string|max:225',
            'email' => 'required|string|email|unique:users|max:225',
            'password' => 'required|string|min:8'
           ]);

        if ($validator->fails()){
            return response()-> json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors() 
            ], 422);
        }


        /**
         * =========2===========
         * Buat user baru dan generate token API, atur masa berlaku token 1 jam
         */
        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password)

        ]);

        $token = $user->createToken('api-token', ['*'], now()-> addHour())->plainTextToken;



        /**
         * =========3===========
         * Kembalikan response sukses dengan data $user dan $token
         */
        return response([
            'message' => 'Registration Successful',
            'user' => $user,
            'token' => $token
            
            
        ], 201);

  }  


    public function login(Request $request)
    {
        /**
         * =========4===========
         * Validasi data login yang masuk
         */
        if (!auth::attempt($request->only('email', 'password')))
        {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }


        /**
         * =========5===========
         * Generate token API untuk user yang terautentikasi
         * Atur token agar expired dalam 1 jam
         */
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        /**
         * =========6===========
         * Kembalikan response sukses dengan data $user dan $token
         */
        return response()->json([
            'message' => 'login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
            ], 200);

    }

    public function logout(Request $request)
    {
        /**
         * =========7===========
         * Invalidate token yang digunakan untuk autentikasi request saat ini
         */
       

        /**
         * =========8===========
         * Kembalikan response sukses
         */
         $request->user()->currentAccessToken()->delete();

        return response()->json(
            [
                'message' => 'Susccessfully logged out'
            ], 200
        );

    }
}
