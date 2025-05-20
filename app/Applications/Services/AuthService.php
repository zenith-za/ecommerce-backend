<?php

namespace App\Applications\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthService
{
    public function attemptLogin(array $credentials): ?string
    {
        try {
            Log::info('AuthService@attemptLogin: Attempting JWT login');
            
            // Attempt to get JWT token directly 
            if ($token = auth()->guard('api')->attempt($credentials)) {
                Log::info('AuthService@attemptLogin: JWT login successful');
                return $token;
            }
            
            Log::error('AuthService@attemptLogin: JWT login failed - invalid credentials');
            return null;
        } catch (Exception $e) {
            Log::error('AuthService@attemptLogin: JWT login failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function register(array $data): User
    {
        try {
            Log::info('AuthService@register: Creating new user', ['email' => $data['email']]);
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'billing_address' => $data['billing_address'] ?? null,
                'role' => 'customer',
            ]);
            
            Log::info('AuthService@register: User created successfully', ['user_id' => $user->id]);
            
            return $user;
        } catch (Exception $e) {
            Log::error('AuthService@register: User creation failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateProfile($userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'billing_address' => $data['billing_address'] ?? null,
        ]);

        return $user;
    }
}