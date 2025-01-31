<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

new #[Layout('layouts.auth')] class extends Component {
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => ['required', 'string', 'email:strict,dns,spoof', 'max:255', 'exists:users,email'],
        'password' => ['required', 'string', 'min:5', 'max:64'],
    ];

    protected $messages = [
        'email.required' => 'The email field is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.max' => 'The email may not be greater than 255 characters.',
        'email.exists' => 'The provided email does not exist in our records.',

        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 5 characters long.',
        'password.max' => 'The password may not be greater than 64 characters.',
    ];

    public function login()
    {
        try {
            $this->validate();
            if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                return redirect()
                    ->route('email.gen')
                    ->with('notify', [
                        'type' => 'success',
                        'message' => 'Login successful.',
                    ]);
            }
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'The provided email or password is incorrect.',
            ]);
        } catch (ValidationException $validationException) {
            throw $validationException;
        } catch (Exception $ex) {
            Log::error($ex);
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'Something went wrong. Please try again later.',
            ]);
        }
    }
};
?>
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Notification Section -->
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

    <div class="max-w-md w-full space-y-8">
     <!-- Logo -->
     <div class="text-center">
        <!-- Logo SVG -->
        <div class="flex justify-center">
            <svg class="w-20 h-20 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1
            class="mt-4 text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-500 animate-gradient-x hover:animate-pulse transition-all duration-300">
            MailCraft
        </h1>
        <p class="mt-2 text-sm text-gray-400 font-medium">
            Modern Email Solutions for the Digital Age
        </p>
    </div>

        <!-- Glassmorphism Card for Form -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-700/30 p-8">
            <h2 class="text-2xl font-bold text-white text-center">
                Welcome Back
            </h2>
            <p class="mt-2 text-sm text-gray-400 text-center">
                Sign in to continue to your account
            </p>

            <!-- Modern Form -->
            <form class="mt-6 space-y-6" wire:submit.prevent="login">
                <div class="space-y-5">
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <div class="relative">
                            <input id="email" name="email" type="email" wire:model="email" required
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                placeholder="Email address">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <!-- Validation Error for Email -->
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" wire:model="password" required
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                placeholder="Password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                        </div>
                        <!-- Validation Error for Password -->
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Forgot Password Link -->
                <div class="flex items-center justify-end">
                    <a href="#"
                        class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                        Forgot your password?
                    </a>
                </div>

                <!-- Sign In Button -->
                <div>
                    <button type="submit"
                        class="w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all transform hover:scale-105 active:scale-95">
                        Sign in
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-400">
            <p>Don't have an account?
                <a href="{{ route('create.account') }}" wire:navigate
                    class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                    Create Account
                </a>
            </p>
        </div>
    </div>
</div>
