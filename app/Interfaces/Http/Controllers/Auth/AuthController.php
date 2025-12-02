<?php

namespace App\Interfaces\Http\Controllers\Auth;

use App\Application\Auth\DTO\LoginUserDTO;
use App\Application\Auth\DTO\ResetPasswordDTO;
use App\Application\Auth\UseCases\LoginUser;
use App\Application\Auth\UseCases\RequestPasswordReset;
use App\Application\Auth\UseCases\ResetPassword;
use App\Interfaces\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints"
 * )
 */

class AuthController extends Controller
{
    public function __construct(
        private LoginUser $loginUser,
        private ResetPassword $resetPassword,
        private RequestPasswordReset $requestResetPassword,
    ) {}

    /**
     * @OA\Post(
     *     path="/v1/login",
     *     tags={"Auth"},
     *     summary="Login and get API token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful, token returned"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $input = new LoginUserDTO(
            email: $data['email'],
            password: $data['password'],
        );

        $result = $this->loginUser->execute($input);

        return response()->json($result->toArray(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/v1/logout",
     *     tags={"Auth"},
     *     summary="Logout (revoke current token)",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // delete current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/v1/forgotPassword",
     *     tags={"Auth"},
     *     summary="Request password reset email",
     *     description="Send a password reset link to the user's email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reset link sent"),
     *     @OA\Response(response=400, description="Unable to send reset link"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $this->requestResetPassword->execute($data['email']);

        return response()->json([
            'message' => 'Password reset link sent to email.',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/v1/resetPassword",
     *     tags={"Auth"},
     *     summary="Reset password using token",
     *     description="Resets the user's password using the token received by email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","token","password","password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="password", type="string", format="password", minLength=6),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=6)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password reset successfully"),
     *     @OA\Response(response=400, description="Reset failed or invalid token"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */

    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'email'=> 'required|email',
            'token'=> 'required',
            'password'=> 'required|string|min:6|confirmed',
        ]);

        $input = new ResetPasswordDTO(
            email: $data['email'],
            token: $data['token'],
            password: $data['password'],
            passwordConfirmation: $request->input('password_confirmation'),
        );

        $this->resetPassword->execute($input);

        return response()->json([
            'message' => 'Password reset successfully.',
        ], Response::HTTP_OK);
    }
}
