<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $saveTemplate;

    public function mount()
    {
        $this->saveTemplate = auth()->user()->emailTemplates()->get();
    }
}; ?>

<div class="min-h-screen bg-gray-900 flex flex-col items-center py-8 text-gray-100 relative overflow-hidden">

    <div class="w-full max-w-6xl px-4 relative z-10">
        <div class="text-center mb-12 space-y-4">
            <h1
                class="text-5xl font-bold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
                MailCraft
            </h1>
            <p class="mt-3 text-gray-400 font-light">AI-powered email template Genrator </p>
        </div>

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

        <div class="mt-8">
            <div class="mb-6 flex items-center justify-between">
                <h2
                    class="text-2xl font-semibold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
                    Your Templates
                </h2>
                <div class="flex gap-3">
                    <input type="text" placeholder="Search templates..."
                        class="px-4 py-2 bg-gray-800/50 border border-white/10 rounded-xl text-sm w-64 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-300 hover:bg-gray-800/70">
                    <select
                        class="px-4 py-2 bg-gray-800/50 border border-white/10 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 hover:bg-gray-800/70">
                        <option value="">All Categories</option>
                        <option value="marketing">Marketing</option>
                        <option value="onboarding">Onboarding</option>
                        <option value="newsletter">Newsletter</option>
                        <option value="transactional">Transactional</option>
                    </select>
                </div>
            </div>

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
                                <button
                                    class="p-1.5 hover:bg-gray-700/50 rounded-lg transition-all duration-200 hover:scale-110">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 114.95 0 2.5 2.5 0 01-4.95 0M12 15h.01M12 12h.01M12 9h.01M4 19v-8a3 3 0 013-3h4" />
                                    </svg>
                                </button>
                                <button
                                    class="p-1.5 hover:bg-gray-700/50 rounded-lg transition-all duration-200 hover:scale-110">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2 17l6 6 12-12" />
                                    </svg>
                                </button>
                                <button
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
                        <div class="text-xs text-gray-500">Created {{$template->created_at->diffForHumans()}}</div>
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
