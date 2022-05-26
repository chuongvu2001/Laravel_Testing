<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\TokenRepository;

class LoginController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validate_data = [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];

        $validator = Validator::make($request->only(['email', 'password']), $validate_data);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('validation.accepted'),
                'errors' => $validator->errors()
            ], 422);
        }

        $auth = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => __('auth.401')
            ], 401);
        }

        $token = Auth::user()->createToken('Laravel Personal Access Client')->accessToken->token;

        return response()->json([
            'success' => true,
            'token_type' => 'Bearer',
            'message' => __('auth.success'),
            'user' => Auth::user(),
            'access_token' => $token
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        dd(\auth()->user());
        $accessToken = Auth::user()->token();
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();

        return response()->json([
            'success' => true,
            'message' => 'User logout successfully.'
        ], 200);
    }
}
