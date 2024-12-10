<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="News Aggregator API documentation"
 * )
 */

class AuthController extends Controller
{

     /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="User Registration",
     *     description="Register a new user and return an authentication token.",
     *     operationId="register",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data",
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="StrongPass123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your-api-token-here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {
        // Validation rules
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|max:64|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ], [
            'password.regex' => 'The password must be at least 8 characters long, contain both uppercase and lowercase letters, a number, and a special character.'
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }
    
        // Check if email already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'User already exists with this email',
            ], 409); // 409 Conflict status
        }
    
        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'token' => $user->createToken('auth_token')->plainTextToken,
            ],
        ], 201);
    }
}
