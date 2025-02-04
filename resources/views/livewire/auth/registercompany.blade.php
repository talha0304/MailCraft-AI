<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Company;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;

new #[Layout('layouts.auth')] class extends Component {
    use WithFileUploads;
    public $name = '';
    public $email = '';
    public $url = '';
    public $company_logo = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => [
            'required',
            'string',
            'min:3',
            'max:100',
            // 'regex:/^[a-zA-Z0-9_]+$/', // Allows letters, numbers, and underscores
        ],
        'email' => ['required', 'string', 'email:strict,dns,spoof', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', 'unique:companies,email', 'max:255'],
        'url' => ['nullable', 'url', 'max:255'],
        'company_logo' => [
            'nullable',
            'image',
            'mimes:jpeg,png,jpg,gif,svg',
            'max:2048', // 2MB
        ],
        'password' => ['required', 'string', 'min:8', 'max:64', 'confirmed'],
        'password_confirmation' => ['required', 'string'],
    ];

    protected $messages = [
        'name.required' => 'The name field cannot be left empty.',
        'name.min' => 'The name must be at least :min characters long.',
        'name.max' => 'The name must not exceed :max characters.',
        'name.regex' => 'The name can only contain letters, numbers, and underscores.',

        'email.required' => 'The email field cannot be left empty.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'The email is already registered.',
        'email.regex' => 'The email format is invalid.',

        'url.url' => 'Please enter a valid URL.',
        'url.max' => 'The URL must not exceed :max characters.',

        'company_logo.image' => 'The company logo must be an image.',
        'company_logo.mimes' => 'The company logo must be a file of type: jpeg, png, jpg, gif, svg.',
        'company_logo.max' => 'The company logo must not exceed :max kilobytes.',

        'password.required' => 'The password field cannot be left empty.',
        'password.min' => 'The password must be at least :min characters long.',
        'password.max' => 'The password must not exceed :max characters.',
        'password.confirmed' => 'The password confirmation does not match.',

        'password_confirmation.required' => 'The password confirmation field cannot be left empty.',
    ];

    public function registerCompany()
    {
        $this->validate();
        try {
            if ($this->company_logo) {
                $imageName = time() . '_' . uniqid() . '.' . $this->company_logo->getClientOriginalExtension();
                $path = $this->company_logo->storeAs('images', $imageName, 'public');
            }
            
            $company = Company::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' =>  Hash::make($this->password),
                'url' => $this->url,
                'company_logo' => $path,
            ]);
            
            if ($company) {
                return redirect()
                    ->route('email.verification', ['id' => $company->id])
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
}; ?>

<!-- Main Form -->
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Flash Messages (keep existing flash message code) -->
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
                Business Email Solutions
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-700/30 p-8">
            <h2 class="text-2xl font-bold text-white text-center">
                Register Your Company
            </h2>
            <p class="mt-2 text-sm text-gray-400 text-center">
                Create your business account
            </p>

            <!-- Form -->
            <form class="mt-6 space-y-6" wire:submit.prevent="registerCompany" enctype="multipart/form-data">
                <!-- Company Name -->
                <div>
                    <label for="name" class="sr-only">Company Name</label>
                    <input id="name" name="name" type="text" wire:model.defer="name" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Company Name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="sr-only">Business Email</label>
                    <input id="email" name="email" type="email" wire:model.defer="email" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Business Email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Website URL -->
                <div>
                    <label for="url" class="sr-only">Company Website</label>
                    <input id="url" name="url" type="url" wire:model.defer="url" required
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all hover:bg-gray-700/70"
                        placeholder="Company Website URL">
                    @error('url')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Logo Upload -->
                <div x-data="{ logoPreview: null }">
                    <label class="block text-sm font-medium text-gray-400 mb-2">
                        Company Logo
                    </label>
                    <div class="flex items-center gap-4">
                        <!-- Preview -->
                        <div x-show="logoPreview" class="shrink-0">
                            <img :src="logoPreview" class="h-12 w-12 rounded-full object-cover">
                        </div>
                        <!-- Upload Input -->
                        <label class="block w-full">
                            <input type="file" name="company_logo" wire:model.defer="company_logo" accept="image/*"
                                class="hidden"
                                x-on:change="logoPreview = URL.createObjectObject($event.target.files[0])">
                            <div
                                class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/30 rounded-lg 
                                cursor-pointer hover:bg-gray-700/70 transition-colors text-gray-400
                                flex items-center justify-between">
                                <span x-text="company_logo ? company_logo.name : 'Upload Logo'"></span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </label>
                    </div>
                    @error('company_logo')
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
                    Register Company
                </button>
            </form>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-400 mt-6">
                <p>Already registered?
                    <a href="{{ route('login') }}" wire:navigate
                        class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                        Company Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
