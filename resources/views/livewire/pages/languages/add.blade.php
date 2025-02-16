<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\language;

new #[Layout('layouts.app')] class extends Component {
    public $language;

    public function storelanguage()
    {
        try {
            language::create([
                'user_id' => request()->user()->id,
                'language' => $this->language,
            ]);
            return redirect()
                ->route('show.lang')
                ->with('notify', [
                    'type' => 'success',
                    'message' => 'Language Created Succesfully ',
                ]);
        } catch (Exception $ex) {
            Log::error($ex);
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'Something went wrong. Please try again later.',
            ]);
        }
    }
}; ?>

<div class="min-h-screen bg-gray-900 flex flex-col items-center justify-center p-6">
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
    <div class="w-full max-w-2xl mx-auto p-8 bg-gray-800/40 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-700/30 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-0">
            <div class="absolute w-96 h-96 -top-48 -left-48 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute w-96 h-96 -bottom-48 -right-48 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>
    
        <!-- Heading -->
        <h2 class="text-4xl font-bold text-center mb-8 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent relative z-10 neon-title">
            Manage Languages
        </h2>
    
        <!-- Back Button -->
        <button wire:navigate href="{{ route('show.lang') }}"
            class="flex items-center space-x-2 bg-gray-700/40 backdrop-blur-sm hover:bg-gray-700/60 text-white px-5 py-2.5 rounded-xl mb-6 transition-all duration-300 transform hover:scale-[1.02] active:scale-95 border border-gray-600/30 hover:border-gray-500/50 relative z-10">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-medium">Back to List</span>
        </button>
    
        <!-- Update Language Form -->
        <form class="flex flex-col space-y-6 relative z-10" wire:submit.prevent="storelanguage()">
            <!-- Input Field -->
            <div class="relative group">
                <input type="text" name="language" placeholder="Enter new language..." wire:model="language"
                    class="w-full bg-gray-700/40 backdrop-blur-sm text-white rounded-xl px-6 py-4 pr-12 border-2 border-gray-600/30 
                           focus:border-blue-500/50 focus:ring-0 placeholder-gray-400 transition-all duration-300
                           hover:border-gray-500/50 focus:shadow-[0_0_20px_-3px_rgba(99,102,241,0.3)]" 
                    required />
                
                <!-- Animated Border -->
                <div class="absolute inset-0 rounded-xl pointer-events-none border-2 border-transparent 
                            group-hover:border-blue-500/20 transition-all duration-500"></div>
            </div>
    
            <!-- Submit Button -->
            <button type="submit"
                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 
                       text-white px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] 
                       active:scale-95 border border-blue-400/30 hover:border-blue-300/50 relative overflow-hidden
                       shadow-[0_0_20px_-5px_rgba(99,102,241,0.3)] hover:shadow-[0_0_30px_-5px_rgba(99,102,241,0.4)]">
                
                <!-- Hover Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Button Content -->
                <div class="relative flex items-center justify-center gap-3">
                    <i class="fas fa-plus text-lg text-blue-300"></i>
                    <span class="font-semibold text-lg tracking-wide">Add Language</span>
                </div>
            </button>
        </form>
    </div>
    
    <style>
        .neon-title {
            text-shadow: 0 0 15px rgba(96, 165, 250, 0.4), 
                         0 0 30px rgba(139, 92, 246, 0.3);
            animation: neon-pulse 3s ease-in-out infinite;
        }
    
        @keyframes neon-pulse {
            0%, 100% { opacity: 0.95; }
            50% { opacity: 1; }
        }
    
        .animate-pulse {
            animation: background-pulse 8s ease-in-out infinite;
        }
    
        @keyframes background-pulse {
            0%, 100% { transform: scale(1); opacity: 0.1; }
            50% { transform: scale(1.05); opacity: 0.15; }
        }
    </style>
</div>
