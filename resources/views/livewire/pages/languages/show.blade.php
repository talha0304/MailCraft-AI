<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\language;

new #[Layout('layouts.app')] class extends Component {
    public $userLanguages;
    public function mount()
    {
        $this->userLanguages = Language::where('user_id', '=', request()->user()->id)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function deleteLang($id)
    {
        try {
            Language::where('id', $id)->delete();
            $this->userLanguages = Language::where('user_id', '=', request()->user()->id)->orderBy('id', 'desc')->get();
        } catch (Exception $ex) {
            Log::error($ex);
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'Something went wrong. Please try again later.',
            ]);
        }
    }
}; ?>
<div class="min-h-screen flex flex-col bg-gray-900 overflow-hidden">
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

    <!-- Main Content -->
    <div class="flex-1 p-6 ">
        <!-- Add Language Button -->
        <div
            class="mb-8 flex items-center justify-between px-6 py-8 bg-gray-800/30 backdrop-blur-lg rounded-2xl border border-gray-700/50 shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-1">
            <!-- Add Language Button -->
            <button wire:navigate href="{{ route('add.lang') }}"
                class="flex items-center space-x-3 bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white font-semibold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-pink-600 opacity-0 hover:opacity-20 transition-opacity duration-300"></div>
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-lg">Add Language</span>
            </button>

            <!-- Languages Heading -->
            <h2
                class="text-5xl font-bold text-center text-white bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent animate-pulse">
                Languages
            </h2>

            <!-- Dashboard Button -->
            <button onclick="window.location.href='{{ route('dashboard') }}'"
                class="flex items-center space-x-3 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white font-semibold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-green-700 to-teal-700 opacity-0 hover:opacity-20 transition-opacity duration-300"></div>
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-lg">Dashboard</span>
            </button>
        </div>

        <!-- Language List Container -->
        <div class="space-y-6">
            @foreach ($userLanguages as $language)
                <!-- Language Card -->
                <div
                    class="group relative bg-gray-800/30 backdrop-blur-lg p-6 rounded-2xl hover:bg-gray-750/40 transition-all duration-500 border-l-4 border-blue-500 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 overflow-hidden">
                    <!-- Hover Glow Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>

                    <div class="flex items-center justify-between relative z-10">
                        <!-- Language Name -->
                        <span class="text-gray-200 font-semibold text-xl tracking-wide truncate neon-text">
                            {{ $language->language }}
                        </span>

                        <!-- Action Buttons -->
                        <div
                            class="flex items-center space-x-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <!-- Edit Link -->
                            <button onclick="window.location.href='{{ route('update.lang', $language->id) }}'"
                                class="p-2 hover:bg-green-900/20 rounded-xl transition-all duration-200 transform hover:scale-110"
                                title="Edit">
                                <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>

                            <!-- Delete Link -->
                            <button wire:click="deleteLang({{ $language->id }})"
                                class="p-2 hover:bg-red-900/20 rounded-xl transition-all duration-200 transform hover:scale-110"
                                title="Delete">
                                <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <style>
        .neon-text {
            text-shadow: 0 0 8px rgba(99, 102, 241, 0.4), 0 0 16px rgba(99, 102, 241, 0.3);
        }
    
        .animate-pulse {
            animation: pulse 2s infinite;
        }
    
        @keyframes pulse {
            0% { opacity: 0.8; }
            50% { opacity: 1; }
            100% { opacity: 0.8; }
        }
    </style>
</div>
