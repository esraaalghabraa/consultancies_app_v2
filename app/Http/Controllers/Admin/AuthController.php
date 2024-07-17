<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AuthenticationException;
use App\Exceptions\EmailVerificationException;
use App\Exceptions\OTPException;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AuthService;
use App\Services\AuthServiceFactory;
use App\Services\OTPService;
use App\Services\OTPServiceFactory;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ResponseTrait;
    protected AuthService $authService;
    protected OTPService $otpService;

    public function __construct()
    {
        $this->authService = AuthServiceFactory::create(Admin::Admin_GUARD, Admin::class);
        $this->otpService = OTPServiceFactory::create();

        // Applying middleware to specific methods
        $this->middleware(['auth:sanctum', 'abilities:admin,access'])->only(['resetPassword','logout','addFollowUser']);
        $this->middleware(['auth:sanctum', 'abilities:admin,refresh'])->only('refreshToken');
    }
    public function login(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => ['string', 'exists:admins,email,deleted_at,NULL'],
            'password' => ['required', 'string', 'min:6', 'max:20']
        ]);
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());

        try {
            // Attempt to log in the admin
        $admin = $this->authService->login($request->only('email', 'password'));
        } catch (AuthenticationException $e) {
            return $this->failedResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->failedResponse('An unexpected error occurred');
        }
        // Send OTP code to the user
        return $this->sendCode($admin);
    }

    public function sendCode(Admin $admin): JsonResponse
    {
        try {
            $this->otpService->sendCode($admin);
        } catch (OTPException|EmailVerificationException $e) {
            return $this->failedResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->failedResponse('An unexpected error occurred');
        }

        return $this->successResponse();
    }

    public function getUser($admin): JsonResponse
    {
        $admin->token = $admin->createToken('accessToken', ['admin', 'access'], now()->addDays(6))->plainTextToken;
        $admin->refresh_token = $admin->createToken('refreshToken', ['admin', 'refresh'], now()->addDays(12))->plainTextToken;

        // Return success response with user details
        return $this->successResponse($admin);
    }

    public function reSendCode(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:admins,email,deleted_at,NULL'],
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors()->first());
        }

        // Find the user by email and resend the OTP code
        $admin = Admin::where('email', $request->email)->first();
        return $this->sendCode($admin);
    }

    public function verifiedEmail(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'exists:admins,email,deleted_at,NULL'],
            'otp' => ['required', 'numeric']
        ]);
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());

        // Find the admin by email and verify the OTP code
        $admin = Admin::where('email', $request->email)->first();
        try {
            $this->otpService->verifyCode($admin, $request->otp);
        } catch (OTPException $e) {
            return $this->failedResponse($e->getMessage());
        }
        if (!$admin->markEmailAsVerified()) {
            $admin->markEmailAsVerified();
        }
        return $this->getUser($admin);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:6', 'max:20']
        ]);
        if ($validator->fails()) {
            return $this->failedResponse($validator->errors()->first());
        }

        // Reset the user's password
        $this->authService->resetPassword(Auth::user(), $request->password);
        return $this->successResponse();
    }
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            // Delete old tokens and create new ones
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('accessToken', ['admin', 'access'], now()->addDay())->plainTextToken;
            $r_token = $request->user()->createToken('refreshToken', ['admin', 'refresh'], now()->addDays(6))->plainTextToken;
            return $this->successResponse(['token' => $token, 'refresh_token' => $r_token]);
        } catch (\Exception $e) {
            return $this->failedResponse('Server failure : ' . $e, 500);
        }
    }
    public function logout(): JsonResponse
    {
        // Delete the current access token of the user
        Auth::user()->currentAccessToken()->delete();
        return $this->successResponse();
    }

}
