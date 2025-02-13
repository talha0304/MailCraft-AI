<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\language;

new #[Layout('layouts.app')] class extends Component {
    public $languageId;
    public $language;
    public $selectedLanguage;

    public function mount($id)
    {
        $this->languageId = $id;
        $this->selectedLanguage = Language::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$this->selectedLanguage) {
            return redirect()->route('show.lang')->with('notify', [
                'type' => 'error',
                'message' => 'Language not found!',
            ]);
        }
        $this->language = $this->selectedLanguage->language;
    }
    public function updateLang()
    {
        try {
            language::where('id', $this->languageId)
                ->where('user_id', auth()->user()->id)
                ->update([
                    'language' => $this->language,
                ]);
            return redirect()
                ->route('show.lang')
                ->with('notify', [
                    'type' => 'success',
                    'message' => 'Language Updated Succesfully ',
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
    <!-- Main Content -->
    <div class="w-full max-w-2xl mx-auto p-8 bg-gray-800 rounded-xl shadow-2xl">
        <!-- Heading -->
        <h2 class="text-3xl font-bold text-center mb-8 text-white">Update Languages</h2>


        <!-- Back Button -->
        <button wire:navigate href="{{ route('show.lang') }}"
            class="flex items-center space-x-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg mb-6 transition-all duration-300 transform hover:scale-105 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back</span>
        </button>


        <!-- Modern Add Language Form -->
        <!-- Modern Add Language Form -->
        <form class="flex flex-col space-y-6" wire:submit.prevent="updateLang()">
            <!-- Input Field -->
            <input type="text" name="language" placeholder="Enter new language..." wire:model="language"
                class="bg-gray-700 text-white rounded-lg px-5 py-3 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-gray-400 transition-all duration-200"
                required />

            <!-- Submit Button -->
            <button type="submit"
                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Update Language</span>
            </button>
        </form>
    </div>
</div>
