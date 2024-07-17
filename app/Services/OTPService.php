<?php

namespace App\Services;

use App\Exceptions\EmailVerificationException;
use App\Exceptions\OTPException;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    /**
     * Send (OTP) to the user's email.
     *
     * @param Model $user
     * @return bool
     * @throws OTPException
     * @throws EmailVerificationException
     */
    public function sendCode(Model $user)
    {
        // Get the current time
        $currentTime = now();

        // Check if the user needs to wait before requesting a new OTP
        if ($user->otp_last_sent_at && $currentTime->lessThan($this->calculateNextAllowedTime($user))) {
            throw new OTPException('You must wait before requesting a new OTP');
        }

        // Generate a random 6-digit OTP
        $otp = mt_rand(100000, 999999);
        $details = ['first_name' => $user->first_name, 'otp' => $otp];

        // Attempt to send the OTP email
        try {
            Mail::to($user->email)->send(new EmailVerification($details));
        } catch (\Exception $exception) {
            // Log the error and throw an exception if the email fails to send
            Log::error('Failed to send OTP email', ['error' => $exception->getMessage()]);
            throw new EmailVerificationException('Failed to send OTP email'.$exception->getMessage());
        }

        // Update the user's OTP and related information
        $user->update([
            'otp' => $otp,
            'otp_last_sent_at' => $currentTime,
            'otp_resend_count' => $user->otp_resend_count + 1,
        ]);

        return true;
    }

    /**
     * Calculate the next allowed time to request an OTP based on the resend count.
     *
     * @param Model $user
     * @return Carbon|null
     */
    private function calculateNextAllowedTime(Model $user): ?Carbon
    {
        // Get the number of times the user has requested an OTP
        $resendCount = $user->otp_resend_count;
        // Get the time the last OTP was sent
        $lastSent = Carbon::create($user->otp_last_sent_at);

        // Determine the next allowed time to request an OTP based on the resend count
        switch ($resendCount) {
            case 1:
                return $lastSent->addMinute();
            case 2:
                return $lastSent->addMinutes(5);
            case 3:
                return $lastSent->addMinutes(15);
            case 4:
                return $lastSent->addMinutes(30);
            case 5:
                return $lastSent->addHour();
            default:
                return $lastSent->addDay();
        }
    }

    /**
     * Verify the provided OTP code for the user.
     *
     * @param Model $user
     * @param int $otp
     * @return bool
     * @throws OTPException
     */
    public function verifyCode(Model $user, int $otp): bool
    {
        // Check if the provided OTP matches the user's OTP and if it's still valid
        if ($user->otp !== $otp || $user->otp_last_sent_at < now()->subMinutes(15)) {
            throw new OTPException('Invalid code');
        }

        // Clear the user's OTP after successful verification
        $user->otp = null;
        $user->save();

        return true;
    }
}
