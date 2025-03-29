<?php

namespace App\Observers;

use App\Mail\UserVerficationMail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Log;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            $otp = mt_rand(100000, 999999);
            $user->update([
                'otp' => $otp,
                'otp_created_at' => Carbon::now(),
            ]);
            $toEmail = $user->email;
            $createdOtp = $otp;
            $subject = 'Email Otp verfiaction';
            Mail::to($toEmail)->send(new UserVerficationMail($createdOtp, $subject));
        } catch (Exception $ex) {
            Log::error('Error sending OTP email: ' . $ex->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $ex
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
     
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
