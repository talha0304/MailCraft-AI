<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\GroqApiService;
use App\Models\Language;

new #[Layout('layouts.app')] class extends Component {
    public $name = '';
    public $category = '';
    public $subject = '';
    public $prompt = '';
    public $content = '';
    public $language = '';
    public $showPreview = false;
    public $isGenerating = false;
    public $userLaguage;

    public function mount()
    {
        $this->userLaguage = auth()->user()->languages()->get();
    }

    public function generateEmail(GroqApiService $apiService)
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'prompt' => 'required|string',
        ]);

        $this->isGenerating = true;
        $this->showPreview = false;

        try {
            $promptText = "Create a professionally formatted email template on the topic of '{$this->category} - {$this->subject}'.\nDetails: {$this->prompt} and make thisi email template in {$this->language} language.";
            $response = $apiService->generateEmail($promptText);

            if (isset($response['error'])) {
                $this->addError('api', 'API request failed: ' . $response['message']);
            } else {
                $this->content = $response;
                $this->showPreview = true;
            }
        } finally {
            $this->isGenerating = false;
        }
    }

    public function saveTemplate()
    {
        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|in:marketing,onboarding,newsletter,transactional',
                'content' => 'required|string',
                'language' => 'required|string',
            ]);
            // Save to database
            auth()
                ->user()
                ->emailTemplates()
                ->create([
                    'name' => $this->name,
                    'category' => $this->category,
                    'content' => $this->content,
                    'language' => $this->language,
                    'user_id' => auth()->user()->id,
                ]);
            $this->reset();
            return redirect()
                ->route('show.template')
                ->with('notify', [
                    'type' => 'success',
                    'message' => 'Template created successful',
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

<div x-data="{ showTemplateForm: true, previewTemplate: $wire.entangle('showPreview') }"
    class="min-h-screen bg-gray-900 flex flex-col items-center py-8 text-gray-100 relative overflow-hidden">
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
                </div>
            </div>
        </div>
    @endif

    <!-- Background gradient effects -->
    <div
        class="absolute -top-1/3 left-1/2 w-[800px] h-[800px] bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full blur-3xl -translate-x-1/2">
    </div>

    <div class="w-full max-w-4xl px-4 relative z-10">
        <!-- Header -->
        <div class="text-center mb-12 space-y-4">
            <h1
                class="text-5xl font-bold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
                Template Generator
            </h1>
            <p class="mt-3 text-gray-400 font-light">Craft and manage email templates with AI assistance</p>
        </div>

        <!-- Toggle Button -->
        <div class="flex justify-center mb-8">
            <button @click="showTemplateForm = !showTemplateForm"
                class="bg-gradient-to-r from-cyan-600/60 to-purple-600/60 backdrop-blur-lg hover:from-cyan-500/50 hover:to-purple-500/50 
                       text-white px-8 py-3 rounded-2xl font-medium transition-all duration-300 flex items-center gap-3
                       border border-white/10 hover:border-white/20 shadow-[0_0_30px_rgba(112,144,176,0.1)]">
                <span x-text="showTemplateForm ? 'Hide Template Form' : 'Show Template Form'"></span>
                <!-- Icons remain same -->
            </button>
        </div>

        <!-- Template Creation Form -->
        <div x-show="showTemplateForm" x-transition
            class="w-full bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 mb-8">
            <form wire:submit.prevent="saveTemplate" class="space-y-6 p-6">
                <div class="space-y-6">
                    <!-- Template Name -->
                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                        </div>
                        <div class="relative">
                            <input type="text" wire:model="name" placeholder="Template Name"
                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent placeholder-gray-500 transition-all duration-300">
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                        </div>
                        <div class="relative">
                            <select wire:model="category"
                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none transition-all duration-300">
                                <option value="" disabled selected>Select Category</option>
                                <option value="marketing">Marketing</option>
                                <option value="onboarding">Onboarding</option>
                                <option value="newsletter">Newsletter</option>
                                <option value="transactional">Transactional</option>
                            </select>
                            <!-- Dropdown icon -->
                        </div>
                    </div>
                    <!-- Language -->
                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                        </div>
                        <div class="relative">
                            <select wire:model="language"
                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none transition-all duration-300">
                                <option value="" disabled selected>Select Language</option>
                                @foreach ($userLaguage as $lang)
                                    <option value="{{ $lang->language }}">{{ $lang->language }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <!-- Email Subject -->
                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                        </div>
                        <div class="relative">
                            <input type="text" wire:model="subject" placeholder="Email Subject"
                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent placeholder-gray-500 transition-all duration-300">
                        </div>
                    </div>

                    <!-- Generation Prompt -->
                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                        </div>
                        <div class="relative">
                            <textarea wire:model="prompt" rows="3" placeholder="Describe the email content you want to generate..."
                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent placeholder-gray-500 transition-all duration-300 resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <div class="pt-2">
                        <button type="button" wire:click="generateEmail" wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 
                                   text-white px-8 py-4 rounded-xl font-medium transition-all duration-300 
                                   flex items-center justify-center space-x-3 relative overflow-hidden group">
                            <span class="relative z-10 flex items-center gap-2">
                                <span wire:loading.remove>Generate Email Content</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </span>
                        </button>
                        @error('api')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Generated Content -->
                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                        </div>
                        <div class="relative">
                            <textarea wire:model="content" rows="6" placeholder="Generated email content will appear here..."
                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent placeholder-gray-500 transition-all duration-300 resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 text-white px-8 py-4 rounded-xl font-medium transition-all duration-300">
                            Save Template
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
