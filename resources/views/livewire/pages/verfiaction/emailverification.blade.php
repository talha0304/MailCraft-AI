<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Security_log;
use Carbon\Carbon;
use App\Mail\UserVerficationMail;

new #[Layout('layouts.auth')] class extends Component {
    public $inputOtp;
    public $resendEmail;

    protected $rules = [
        'otp' => ['required', 'string', 'min:6', 'max:6', 'exists:otps,otp'],
    ];
    protected $messages = [
        'otp.required' => 'The OTP field is required.',
        'otp.string' => 'The OTP must be valid.',
        'otp.min' => 'The OTP must be exactly 6 characters long.',
        'otp.max' => 'The OTP must be exactly 6 characters long.',
        'otp.exists' => 'The entered OTP is invalid or has expired.',
        'email' => ['required', 'string', 'email:strict,dns,spoof', 'max:255', 'exists:users,email'],
    ];

    public function verifyOtp()
    {
        try {
            $user = User::where('otp', '=', $this->inputOtp)->first();

            if (!$user || ($user && (now()->diffInMinutes(optional($user)->otp_created_at) > 10 || $user->otp !== $this->inputOtp))) {
                return back()->with('notify', [
                    'type' => 'error',
                    'message' => 'OTP expired or invalid. Please request a new one.',
                ]);
            }
            $user->otp = null;
            $user->otp_created_at = null;
            $user->is_verfied = 1;
            $user->save();

            Auth::login($user);

            Security_log::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => request()->ip(),
            ]);

            return redirect()
                ->route('dashboard')
                ->with('notify', [
                    'type' => 'success',
                    'message' => 'OTP verified successfully. Login Successfully.',
                ]);
        } catch (Exception $ex) {
            Log::error('Error verifying OTP: ' . $ex->getMessage(), [
                'exception' => $ex,
            ]);
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'An error occurred while verifying OTP. Please try again.',
            ]);
        }
    }

    public function resendOtp()
    {
        $user = User::where('email', $this->resendEmail)->first();
        if (!$user) {
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'Please Enter Valid Email Address.',
            ]);
        }
        $otp = mt_rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_created_at' => Carbon::now(),
        ]);
        $toEmail = $user->email;
        $createdOtp = $otp;
        $subject = 'Resend Otp';
        Mail::to($toEmail)->send(new UserVerficationMail($createdOtp, $subject));
        return redirect()
            ->route('email.verification')
            ->with('notify', [
                'type' => 'success',
                'message' => 'OTP Send Successfully. Please Check Your Email ',
            ]);
    }
}; ?>
<div class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <!-- Flash Messages -->
    @if (session('notify'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200"
            class="fixed top-4 right-4 z-50 max-w-md w-full">
            <div
                class="px-4 py-3 rounded-lg shadow-xl backdrop-blur-lg bg-gray-800/95 border border-gray-700
            @if (session('notify.type') === 'success') text-emerald-400 @endif
            @if (session('notify.type') === 'error') text-red-400 @endif">
                <div class="flex items-center gap-3">
                    <div class="shrink-0">
                        @if (session('notify.type') === 'success')
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                    <div class="text-sm font-medium">
                        {{ session('notify.message') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="w-full max-w-md bg-gray-800 rounded-lg shadow-2xl p-8" x-data="{ showResendOtp: false }">
        <!-- Header -->
        <h1 class="text-3xl font-bold text-white mb-6 text-center">Email Verification</h1>

        <!-- OTP Message -->
        <div class="mb-6 text-gray-300 text-center">
            An OTP has been sent to your email address. Please check your inbox and enter the OTP below.
        </div>

        <!-- OTP Form -->
        <form wire:submit.prevent="verifyOtp">
            <!-- OTP Input -->
            <div class="mb-6">
                <label for="otp" class="block text-sm font-medium text-gray-400 mb-2">Enter OTP</label>
                <input type="text" id="otp" placeholder="Enter your OTP" wire:model.defer="inputOtp" required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <!-- Verify Button -->
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-all">
                Verify OTP
            </button>
        </form>

        <!-- Resend OTP Section -->
        <div class="mt-4">
            <button type="button" @click="showResendOtp = !showResendOtp"
                class="w-full text-blue-500 hover:text-blue-400 focus:outline-none transition-all">
                Resend OTP
            </button>

            <div x-show="showResendOtp" x-collapse class="mt-4 space-y-4">
                <!-- Email Input -->
                <div>
                    <form wire:submit.prevent="">
                        <label for="resend-email" class="block text-sm font-medium text-gray-400 mb-2">
                            Enter your email
                        </label>
                        <input type="email" id="resend-email" placeholder="Enter your email"
                            wire:model.defer="resendEmail"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </form>
                </div>

                <!-- Resend Button -->
                <button type="button" wire:click.prevent="resendOtp"
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-all">
                    Resend OTP
                </button>
            </div>
        </div>
    </div>
</div>
