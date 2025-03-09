<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Template;

new #[Layout('layouts.app')] class extends Component {
    public $saveTemplate;
    public $search = '';
    public $category = '';

    public function mount()
    {
        $this->saveTemplate = auth()->user()->emailTemplates()->get();
    }

    public function deleteTemplate($id)
    {
        try {
            Template::where('id', '=', $id)->delete();
            $this->saveTemplate = auth()->user()->emailTemplates()->get();
        } catch (Exception $ex) {
            Log::error($ex);
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'Something went wrong. Please try again later.',
            ]);
        }
    }

    public function templateFilterByCategory($word)
    {
        if (!empty($word)) {
            $this->saveTemplate = auth()->user()->emailTemplates()->where('category', '=', $word)->get();
        } else {
            $this->saveTemplate = auth()->user()->emailTemplates()->get();
        }
    }

    public function searchTemplates()
    {
        if (!empty($this->search)) {
            $this->saveTemplate = auth()
                ->user()
                ->emailTemplates()
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('content', 'like', '%' . $this->search . '%')
                ->get();
        } else {
            $this->saveTemplate = auth()->user()->emailTemplates()->get();
        }
    }
};
?>

<div class="min-h-screen bg-gray-900 flex flex-col items-center py-8 text-gray-100 relative overflow-hidden">
    <!-- Notification Section -->
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

    <!-- Main Content -->
    <div class="w-full max-w-6xl px-4 relative z-10">
        <!-- Header -->
        <div class="text-center mb-12 space-y-4">
            <h1
                class="text-5xl font-bold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
                MailCraft
            </h1>
            <p class="mt-3 text-gray-400 font-light">AI-powered email template Generator</p>
        </div>

        <!-- Buttons -->
        <div wire:navigate href="{{ route('gen.template') }}" class="flex justify-center gap-4 mb-8">
            <button
                class="bg-gradient-to-r from-cyan-600/60 to-purple-600/60 backdrop-blur-lg text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-300 border border-white/10 hover:border-white/20 hover:scale-105">
                ▲ Create Template
            </button>
            <button wire:navigate href="{{ route('dashboard') }}"
                class="bg-gradient-to-r from-purple-600/60 to-cyan-600/60 backdrop-blur-lg text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-300 border border-white/10 hover:border-white/20 hover:scale-105">
                ▲ Back to Dashboard
            </button>
        </div>

        <!-- Templates Section -->
        <div class="mt-8">
            <div class="mb-6 flex items-center justify-between">
                <h2
                    class="text-2xl font-semibold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
                    Your Templates
                </h2>
                <div class="flex gap-3">
                    <!-- Search Input -->
                    <input type="text" placeholder="Search templates..." wire:model="search"
                        wire:keydown.enter="searchTemplates"
                        class="px-4 py-2 bg-gray-800/50 border border-white/10 rounded-xl text-sm w-64 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-300 hover:bg-gray-800/70">
                    <!-- Category Dropdown -->
                    <select wire:model="category" wire:change="templateFilterByCategory($event.target.value)"
                        class="px-4 py-2 bg-gray-800/50 border border-white/10 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 hover:bg-gray-800/70">
                        <option value="">All Categories</option>
                        <option value="marketing">Marketing</option>
                        <option value="onboarding">Onboarding</option>
                        <option value="newsletter">Newsletter</option>
                        <option value="transactional">Transactional</option>
                    </select>
                </div>
            </div>

            <!-- Template Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($saveTemplate as $template)
                    <div
                        class="group relative bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-white/10 transition-all duration-300 hover:shadow-xl hover:border-cyan-400/30">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-lg text-cyan-400">{{ $template->name }}</h3>
                                <span class="text-sm text-purple-400">{{ $template->category }}</span>
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <!-- Edit Button -->
                                <button
                                wire:navigate
                                href="{{ route('update.template', ['id' => $template->id]) }}"
                                class="p-1.5 hover:bg-gray-700/50 rounded-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:bg-gray-700/50 flex items-center justify-center"
                                aria-label="Edit template"
                            >
                                <svg 
                                    class="w-5 h-5 text-gray-400 hover:text-cyan-400 transition-colors duration-200" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path 
                                        stroke-linecap="round" 
                                        stroke-linejoin="round" 
                                        stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536l-7.072 7.072-4.242.707.707-4.242 7.072-7.072zM12 15h.01"
                                    />
                                </svg>
                            </button>
                                <!-- View Button -->
                                <button wire:navigate href="{{ route('preview.template', ['id' => $template->id]) }}"
                                    class="p-1.5 hover:bg-gray-700/50 rounded-lg transition-all duration-200 hover:scale-110">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                    </svg>
                                </button>
                                <!-- Delete Button -->
                                <button wire:click="deleteTemplate({{ $template->id }})"
                                    class="p-1.5 hover:bg-red-500/20 rounded-lg transition-all duration-200 hover:scale-110">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="text-sm text-gray-400 line-clamp-3 mb-4">
                            {{ Str::limit($template->content, 100) }}
                        </div>
                        <div class="text-xs text-gray-500">Created {{ $template->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 5s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</div>
