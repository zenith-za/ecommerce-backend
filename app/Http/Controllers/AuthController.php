<?php

namespace App\Http\Controllers;

use App\Applications\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->middleware('auth:api')->except(['login', 'register']);
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        Log::info('AuthController@login: Attempting login', ['email' => $request->email]);

        if (!$token = $this->authService->attemptLogin($credentials)) {
            Log::error('AuthController@login: Login failed - invalid credentials');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('AuthController@login: Login successful', ['user_id' => Auth::id()]);

        return response()->json([
            'token' => $token,
            'user' => Auth::user(),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->authService->register($validated);

        // Automatically login the user after registration
        $token = auth()->guard('api')->login($user);

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
    
    public function logout()
    {
        Log::info('AuthController@logout: User logging out', ['user_id' => Auth::id()]);

        try {
            auth()->guard('api')->logout();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            Log::error('AuthController@logout: Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Logout failed'], 500);
        }
    }

    public function refresh()
    {
        Log::info('AuthController@refresh: Token refresh requested', ['user_id' => Auth::id()]);

        try {
            $token = auth()->guard('api')->refresh();
            return response()->json([
                'token' => $token,
                'user' => Auth::user(),
            ]);
        } catch (\Exception $e) {
            Log::error('AuthController@refresh: Token refresh failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Token refresh failed'], 401);
        }
    }
    
    public function profile()
    {
        return response()->json(Auth::user());
    }
    
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email',
            'billing_address' => 'nullable|string',
        ]);
        
        $user = $this->authService->updateProfile(Auth::id(), $validated);
        
        return response()->json($user);
    }

    public function checkAuth(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'isAuthenticated' => true
        ]);
    }
}