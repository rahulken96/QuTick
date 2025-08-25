<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterStoreRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*=====================
       Profile
    ==========================*/
    public function myProfile()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'status'  => true,
                'data'    => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /*=====================
       Auth
    ==========================*/
    public function register(RegisterStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = new User();
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();
            return response()->json([
                'status'  => true,
                'data'  => [
                    'token' => $token,
                    'user'  => new UserResource($user),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'Login Accepted',
                'data'  => [
                    'token' => $token,
                    'user'  => $user,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Logout Success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
