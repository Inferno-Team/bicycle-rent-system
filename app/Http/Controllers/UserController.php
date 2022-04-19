<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' =>  'required'
        ]);

        if ($valid->fails())
            return response()->json(['code' => 400, 'message' => 'Bad Request'], 200);

        $user = User::where('email', $request->email)->first();
        if (!isset($user)) {
            return response()->json(['code' => 404, 'message' => 'User not found'], 200);
        }

        if (!Hash::check($request->password, $user->password))
            return response()->json(['code' => 300, 'message' => 'Do not match our records!!'], 200);

        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'code' => 200,
            'token' => $tokenResult,
            'message' => 'good',
            'type' => $user->type
        ], 200);
    }
    public function signUp(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' =>  'required',
            'first_name' =>  'required',
            'last_name' =>  'required',
            'national_id' =>  'required|min:9',
            'phone_number' =>  'required|min:10|max:10',
        ]);
        $user = User::where('email', $request->email)->first();
        if (isset($user)) {
            return response()->json(['status_code' => 400, 'message' => 'this email already in use.'], 200);
        }
        if ($valid->fails())
            return response()->json(['status_code' => 400, 'message' => 'Bad Request'], 200);
        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'national_id' => $request->national_id,
            'type' => 'customer',
        ]);
        if (isset($user)) {
            return response()->json([
                'code' => 200,
                'message' => "user created successfully"
            ], 200);
        } else {
            return response()->json([
                'code' => 300,
                'message' => "user can't be created now."
            ], 200);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'code' => 200,
            'message' => 'token deleted successfully',
        ], 200);
    }
}
