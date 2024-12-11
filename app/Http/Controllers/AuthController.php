<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', Rule::unique('users')->where('deleted', false)],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
        ], [
            'password.regex' => 'The password must be at least 8 characters long, contain both uppercase and lowercase letters, a number, and a special character.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            if ($existingUser->deleted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This account has been deactivated. Please contact support to reactivate your account.',
                ], 403);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'User already exists with this email.',
            ], 409);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'token' => $user->createToken('auth_token')->plainTextToken,
            ],
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     description="Authenticate the user and return an authentication token.",
     *     operationId="login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User login data",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="StrongPass123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your-api-token-here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials.")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Validate the input
        $validator = validator($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Attempt login
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Check if the user is marked as deleted
            if ($user->deleted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Account is deactivated. Please contact support.',
                ], 403);
            }

            // Generate token if login is successful and the user is not deleted
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => [
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ],
            ]);
        }

        // Invalid credentials
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="User Logout",
     *     description="Logout the user by invalidating their token.",
     *     operationId="logout",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, user not authenticated"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Check if the user is authenticated
        $user = $request->user();

        // If the user is not authenticated, return an error response
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized, user not authenticated',
            ], 401);
        }

        // Check if the user has any active tokens
        if ($user->tokens()->count() > 0) {
            // Revoke all tokens for the user
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully',
            ]);
        }

        // If there are no active tokens, return a message indicating no active sessions
        return response()->json([
            'status' => 'error',
            'message' => 'No active sessions found to log out.',
        ], 400);
    }

    /**
     * @OA\Post(
     *     path="/api/password-reset",
     *     summary="Password Reset",
     *     description="Send a password reset link to the user.",
     *     operationId="password-reset",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email for password reset",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid email address"
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset link sent successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send password reset link.',
        ], 400);
    }

    /**
     * @OA\Put(
     *     path="/api/delete-user",
     *     summary="Delete User",
     *     description="Delete a user by setting 'deleted' flag to true.",
     *     operationId="delete-user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email for deletion",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User marked as deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function deleteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        // $user->update(['deleted' => true]);
        $user->deleted = true;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User marked as deleted successfully',
        ]);
    }
}