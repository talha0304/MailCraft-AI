<?php

namespace App\Observers;

use App\Mail\UserVerficationMail;
use App\Models\Company;
use Carbon\Carbon;
use Exception;
use Log;
use Mail;

class ComapanyObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        try {
            $otp = mt_rand(100000, 999999);
            $company->update([
                'otp' => $otp,
                'otp_created_at' => Carbon::now(),
            ]);
            $toEmail = $company->email;
            $createdOtp = $otp;
            $subject = 'Email Otp verfiaction';
            Mail::to($toEmail)->send(new UserVerficationMail($createdOtp, $subject));
        } catch (Exception $ex) {
            Log::error('Error sending OTP email: ' . $ex->getMessage(), [
                'user_id' => $company->id,
                'email' => $company->email,
                'exception' => $ex
            ]);
        }
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        //
    }
}
