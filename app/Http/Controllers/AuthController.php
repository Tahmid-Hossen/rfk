<?php

namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('user');
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return ResponseHelper::success('Login successful', [
                'token' => $token,  // Return only the token
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames(), // This should return the roles the user has
                    'permissions' => $user->getAllPermissions()->pluck('name'), // Ensure you're using getAllPermissions() instead of getPermissionNames() if needed
                ]
            ]);
        }
        return ResponseHelper::error('Invalid credentials', [], 401);
    }


    public function refreshToken(Request $request)
    {
        $validated=$request->validate([
            'access_token' => 'required|string',
        ]);
        $token = $request->access_token;

        try {
            // Verify and decode the token
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

            if (!$tokenModel || !$tokenModel->tokenable) {
                return ResponseHelper::error('Invalid token', [],401);
            }
            $user = $tokenModel->tokenable;
            if (!$user instanceof User) {
                return ResponseHelper::error('Invalid token owner', [], 401);
            }
            $expiresAt = now()->addHour(24);
            $tokenModel->expires_at = $expiresAt;
            $tokenModel->save();
            return ResponseHelper::success('Success', []);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Token error: ' . $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ResponseHelper::success('Logout successful', []);
    }

    public function user()
    {
        return auth()->user();
    }
}
