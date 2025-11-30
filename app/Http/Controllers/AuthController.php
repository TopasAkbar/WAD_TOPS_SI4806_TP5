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
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }


        /**
         * =========2===========
         * Buat user baru dan generate token API, atur masa berlaku token 1 jam
         */
         $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Token Sanctum dengan masa berlaku 1 jam (60 minutes)
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(60))->plainTextToken;


        /**
         * =========3===========
         * Kembalikan response sukses dengan data $user dan $token
         */
         return response()->json([
            'message' => 'Registrasi berhasil',
            'user'    => $user,
            'token'   => $token
        ], 201);
    }


    public function login(Request $request)
    {
        /**
         * =========4===========
         * Validasi data login yang masuk
         */
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();



        /**
         * =========5===========
         * Generate token API untuk user yang terautentikasi
         * Atur token agar expired dalam 1 jam
         */
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(60))->plainTextToken;

        /**
         * =========6===========
         * Kembalikan response sukses dengan data $user dan $token
         */
        return response()->json([
            'message' => 'Login Successful',
            'user'    => $user,
            'token'   => $token
        ], 200);
    }


    public function logout(Request $request)
    {
        /**
         * =========7===========
         * Invalidate token yang digunakan untuk autentikasi request saat ini
         */
        $request->user()->currentAccessToken()->delete();


        /**
         * =========8===========
         * Kembalikan response sukses
         */
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }
}
