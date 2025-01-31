<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\UserVerficationMail;

new #[Layout('layouts.auth')] class extends Component {
    public $userId;
    public $inputOtp;

    protected $rules = [
        'otp' => ['required', 'string', 'min:6', 'max:6', 'exists:otps,otp'],
    ];
    protected $messages = [
        'otp.required' => 'The OTP field is required.',
        'otp.string' => 'The OTP must be valid.',
        'otp.min' => 'The OTP must be exactly 6 characters long.',
        'otp.max' => 'The OTP must be exactly 6 characters long.',
        'otp.exists' => 'The entered OTP is invalid or has expired.',
    ];
    public function mount($id)
    {
        $this->userId = $id;
    }

    public function verifyOtp()
    {
        $user = User::findOrFail($this->userId);
        if (now()->diffInMinutes($user->otp_created_at) > 10 || $user->otp !== $this->inputOtp) {
            return back()
                ->with('notify', [
                    'type' => 'error',
                    'message' => 'OTP expired or invalid. Please request a new one.',
                ]);
        }
        $user->otp = null;
        $user->otp_created_at = null;
        $user->is_verfied = 1;
        $user->save();

        return redirect()
            ->route('login')
            ->with('notify', [
                'type' => 'success',
                'message' => 'OTP verified successfully. Please Login ',
            ]);
    }

    public function resendOtp()
    {
        $user = User::findOrFail($this->userId);
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
            ->route('email.verification', ['id' => $user->id])
            ->with('notify', [
                'type' => 'success',
                'message' => 'OTP Send Successfully. Please Check Your Email ',
            ]);
    }
}; ?>
<!-- Flash Messages -->

<div class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    @if (session('notify'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-10"
            class="fixed top-4 right-4 z-50 min-w-[300px]">
            <div
                class="px-6 py-4 rounded-lg shadow-lg bg-white border-l-4 
                 @if (session('notify.type') === 'success') border-green-500 text-green-600 @endif
                 @if (session('notify.type') === 'error') border-red-500 text-red-600 @endif">
                <div class="flex items-center gap-3">
                    <div class="shrink-0">
                        @if (session('notify.type') === 'success')
                            <!-- Checkmark Icon for Success -->
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                        @if (session('notify.type') === 'error')
                            <!-- Cross Icon for Error -->
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
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
    <div class="w-full max-w-md bg-gray-800 rounded-lg shadow-2xl p-8">
        <!-- Header -->
        <h1 class="text-3xl font-bold text-white mb-6 text-center">Email Verification</h1>

        <!-- Message indicating OTP has been sent -->
        <div class="mb-6 text-gray-300 text-center">
            An OTP has been sent to your email address. Please check your inbox and enter the OTP below.
        </div>

        <!-- OTP Input Field -->
        <div class="mb-6">
            <label for="otp" class="block text-sm font-medium text-gray-400 mb-2">Enter OTP</label>
            <input type="text" id="otp" placeholder="Enter your OTP" name="otp" wire:model.defer="inputOtp"
                required
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
        </div>

        <!-- Verify OTP Button -->
        <button type="submit" wire:click.prevent="verifyOtp"
            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-all">
            Verify OTP
        </button>
        <!-- Verify OTP Button -->
        <button type="submit" wire:click.prevent="resendOtp"
            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white mt-3 py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-all">
            Re-Send OTP
        </button>
    </div>
</div>
