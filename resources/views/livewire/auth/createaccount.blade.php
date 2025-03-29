<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
new #[Layout('layouts.auth')] class extends Component {
    public $username = '';
    public $age = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'username' => [
            'required',
            'string',
            'min:3',
            'max:30',
            'unique:users,username',
        ],
        'email' => ['required', 'string', 'email:strict,dns,spoof', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', 'unique:users,email', 'max:255'],
        'password' => ['required', 'string', 'min:5', 'max:64', 'confirmed'],
        'password_confirmation' => ['required', 'string'],
        'age' => ['required', 'integer', 'min:13', 'max:120'],
    ];

    protected $messages = [
        'username.required' => 'The username field cannot be left empty.',
        'username.min' => 'The username must be at least :min characters long.',
        'username.max' => 'The username must not exceed :max characters.',
        'username.unique' => 'The username is already taken.',
        'username.regex' => 'The username can only contain letters, numbers, and underscores.',
        'email.required' => 'The email field cannot be left empty.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'The email is already registered.',
        'password.required' => 'The password field cannot be left empty.',
        'password.min' => 'The password must be at least :min characters long.',
        'password.max' => 'The password must not exceed :max characters.',
        'password.confirmed' => 'The password confirmation does not match.',
        // 'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'age.required' => 'The age field cannot be left empty.',
        'age.min' => 'You must be at least :min years old to register.',
        'age.max' => 'The age must not exceed :max years.',
    ];

    public function createAccount()
    {
        $this->validate();
        try {
            // Create the user
            $user = User::create([
                'username' => $this->username,
                'age' => $this->age,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            if ($user) {
                return redirect()
                    ->route('email.verification')
                    ->with('notify', [
                        'type' => 'success',
                        'message' => 'Account created successfully!',
                    ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex);
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'Failed to create account: ',
            ]);
        }
    }
};
?>

<!-- Main Form -->
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Flash Messages -->
    @if (session('notify'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:leave="transition ease-in duration-200"
             class="fixed top-4 right-4 z-50 max-w-md w-full">
            <div class="px-4 py-3 rounded-lg shadow-xl backdrop-blur-lg bg-gray-800/95 border border-gray-700
                @if (session('notify.type') === 'success') text-emerald-400 @endif
                @if (session('notify.type') === 'error') text-red-400 @endif">
                <div class="flex items-center gap-3">
                    <div class="shrink-0">
                        @if (session('notify.type') === 'success')
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
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

        <!-- Form Card -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-700/30 p-8">
            <h2 class="text-2xl font-bold text-white text-center">
                Create Your Account
            </h2>
            <p class="mt-2 text-sm text-gray-400 text-center">
                Sign up to get started with MailCraft
            </p>

            <!-- Form -->
            <form class="mt-6 space-y-6" wire:submit.prevent="createAccount">
                <!-- Username -->
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" wire:model.defer="username" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Username">
                    @error('username')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Age -->
                <div>
                    <label for="age" class="sr-only">Age</label>
                    <input id="age" name="age" type="number" wire:model.defer="age" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Age" min="1">
                    @error('age')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" name="email" type="email" wire:model.defer="email" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" wire:model.defer="password" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        wire:model.defer="password_confirmation" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Confirm Password">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all transform hover:scale-105 active:scale-95">
                    Create Account
                </button>
            </form>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-400 mt-6">
                <p>Already have an account?
                    <a href="{{ route('login') }}" wire:navigate
                        class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                        Log in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div> 