<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verify']]);
    }

    /**
     * login
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = request(['user_name', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        if (!$user->is_verified) {
            auth()->logout();
            return response()->json(['error' => 'Tài khoản của bạn chưa được xác minh.'], 403);
        }

        return $this->respondWithToken($token);
    }

    /**
     * logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    
    /**
     * register
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function register(AuthRequest $request): JsonResponse
    {
        $validateData = $request->validated();

        $user = User::create(array_merge($validateData, [
            'password' => bcrypt($validateData['password']),
            'verification_code' => Str::random(6),
        ]));

        SendVerificationEmail::dispatch($user);
        return response()->json($user);
    }
    
    /**
     * verify
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $user = User::where('verification_code', $request->input('code'))->first();
        if ($user) {
            $user->is_verified = true;
            $user->email_verified_at = now();
            $user->save();

            return response()->json(['message' => 'Xác minh email thành công']);
        }
        return response()->json(['message' => 'Mã Xác minh không hợp lệ']);
    }

    /**
     * respondWithToken
     *
     * @param  mixed $token
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
    
    /**
     * profile
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        $profile = auth()->user();

        return response()->json($profile);
    }
}
