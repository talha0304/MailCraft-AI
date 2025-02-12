<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\language;

new #[Layout('layouts.app')] class extends Component {
    public $userLanguages;
    public function mount()
    {
        $this->userLanguages = Language::where('user_id', '=', request()->user()->id)->get();
    }
}; ?>
<div class="min-h-screen flex flex-col bg-gray-900">
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
    <div class="flex-1 p-6 mt-20">
        <!-- Add Language Button -->
        <div class="mb-8 flex justify-start">
            <button
                class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium px-6 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <a href="{{ route('add.lang') }}">Add Language</a>
            </button>
        </div>

        <!-- Language List Container -->
        <div class="space-y-6">
            @foreach ($userLanguages as $language)
                <!-- Language Card -->
                <div
                    class="group relative bg-gray-800/50 backdrop-blur-md p-5 rounded-2xl hover:bg-gray-750/60 transition-all duration-300 border-l-4 border-blue-500 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <!-- Language Name -->
                        <span class="text-gray-200 font-semibold text-lg tracking-wide truncate">
                            {{ $language->language }}
                        </span>
                        <!-- Action Buttons -->
                        <div
                            class="flex items-center space-x-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <!-- Edit Link -->
                            <a href="/edit"
                                class="p-2 hover:bg-green-900/20 rounded-xl transition-all duration-200 transform hover:scale-110"
                                title="Edit">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>

                            <!-- Delete Link -->
                            <a href="/delete"
                                class="p-2 hover:bg-red-900/20 rounded-xl transition-all duration-200 transform hover:scale-110"
                                title="Delete">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
