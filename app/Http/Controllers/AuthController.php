<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthenticationException;
use App\Exceptions\EmailVerificationException;
use App\Exceptions\OTPException;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OTPService;
use App\Services\RoleService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ResponseTrait;

    protected AuthService $authService;
    protected OTPService $otpService;
    protected RoleService $roleService;

    public function __construct(AuthService $authService, OTPService $otpService, RoleService $roleService)
    {
        $this->authService = $authService;
        $this->otpService = $otpService;
        $this->roleService = $roleService;

        // Applying middleware to specific methods
        $this->middleware(['auth:sanctum', 'abilities:user,access'])->only(['resetPassword','logout']);
        $this->middleware(['auth:sanctum', 'ability:user,refresh'])->only('refreshToken');
    }
    // Method for registering a new user
    public function register(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'min:3', 'max:20'],
            'last_name' => ['required', 'string', 'min:3', 'max:20'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:20'],
            'is_expert' => ['required', 'boolean']
        ]);
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());

        // Register the user and assign a role
        $user = $this->authService->register($request->all());
        $role = $request->is_expert ? User::EXPERT_ROLE : User::CUSTOMER_ROLE;
        $this->roleService->assignRole($user, $role);

        // Send OTP code to the user
        return $this->sendCode($user);
    }


    // Method for sending OTP code
    public function sendCode(User $user): JsonResponse
    {
        try {
            $this->otpService->sendCode($user);
        } catch (OTPException|EmailVerificationException $e) {
            return $this->failedResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->failedResponse('An unexpected error occurred');
        }

        return $this->successResponse();
    }


    // Method for logging in a user
    public function login(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => ['string', 'exists:users,email,deleted_at,NULL'],
            'password' => ['required', 'string', 'min:6', 'max:20']
        ]);
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());

        try {
            // Attempt to log in the user
            $user = $this->authService->login($request->only('email', 'password'));
        } catch (AuthenticationException $e) {
            return $this->failedResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->failedResponse('An unexpected error occurred');
        }

        // Return the logged-in user details
        return $this->getUser($user);
    }

    // Method for getting user details
    public function getUser($user): JsonResponse
    {
        // Attach role and generate tokens for the user
        $user->role = $user->roles[0]['name'];
        Arr::forget($user, 'roles');
        $user->token = $user->createToken('accessToken', ['user', 'access'], now()->addDays(6))->plainTextToken;
        $user->refresh_token = $user->createToken('refreshToken', ['user', 'refresh'], now()->addDays(12))->plainTextToken;

        // Return success response with user details
        return $this->successResponse($user);
    }

    // Method for resending OTP code
    public function reSendCode(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:users,email,deleted_at,NULL'],
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors()->first());
        }

        // Find the user by email and resend the OTP code
        $user = User::where('email', $request->email)->first();
        return $this->sendCode($user);
    }

    // Method for verifying email with OTP code
    public function verifiedEmail(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'exists:users,email,deleted_at,NULL'],
            'otp' => ['required', 'numeric']
        ]);
        if ($validator->fails())
            return $this->failedResponse($validator->errors()->first());

        // Find the user by email and verify the OTP code
        $user = User::where('email', $request->email)->first();
        try {
            $this->otpService->verifyCode($user, $request->otp);
        } catch (OTPException $e) {
            return $this->failedResponse($e->getMessage());
        }
        if (!$user->markEmailAsVerified()) {
            $user->markEmailAsVerified();
        }
        return $this->getUser($user);
    }

    // Method for resetting the password
    public function resetPassword(Request $request)
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

    public function refreshToken(Request $request)
    {
        try {
            // Delete old tokens and create new ones
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('accessToken', ['user', 'access'], now()->addDay())->plainTextToken;
            $r_token = $request->user()->createToken('refreshToken', ['user', 'refresh'], now()->addDays(6))->plainTextToken;
            return $this->successResponse(['token' => $token, 'refresh_token' => $r_token]);
        } catch (\Exception $e) {
            return $this->failedResponse('Server failure : ' . $e, 500);
        }
    }

    // Method for logging out the user
    public function logout(): JsonResponse
    {
        // Delete the current access token of the user
        Auth::user()->currentAccessToken()->delete();
        return $this->successResponse();
    }

}
