<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
new #[Layout('layouts.auth')] class extends Component {
    //
};
?>

<div class="min-h-screen bg-gray-900 text-white">
    <!-- Hero Section -->
    <section class="container mx-auto px-4 py-20 text-center">
        <div class="max-w-4xl mx-auto">
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
            <h1 class="text-5xl md:text-6xl font-bold mb-6 mt-2">
                Transform Your Email
                <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                    Communication
                </span>
            </h1>
            <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                AI-powered email solutions that help you write better, faster, and more effective communications.
            </p>
            <div class="flex justify-center gap-4 mb-16">
                <a href="{{ route('create.account') }}" wire:navigate
                    class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold transition-colors">
                    Get Started Free
                </a>
                <a href="#features"
                    class="px-8 py-4 border border-gray-700 hover:border-indigo-500 rounded-lg font-semibold transition-colors">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-gray-800/50 py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-16">Powerful Features</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature Cards -->
                <div class="p-6 bg-gray-900/50 rounded-xl border border-gray-700">
                    <div class="text-indigo-400 text-3xl mb-4">🤖</div>
                    <h3 class="text-xl font-semibold mb-2">AI-Powered Writing</h3>
                    <p class="text-gray-400">Generate perfect emails using advanced AI models trained on professional
                        communication.</p>
                </div>

                <div class="p-6 bg-gray-900/50 rounded-xl border border-gray-700">
                    <div class="text-indigo-400 text-3xl mb-4">⚡</div>
                    <h3 class="text-xl font-semibold mb-2">Instant Responses</h3>
                    <p class="text-gray-400">Quickly reply to messages with context-aware suggestions and templates.</p>
                </div>

                <div class="p-6 bg-gray-900/50 rounded-xl border border-gray-700">
                    <div class="text-indigo-400 text-3xl mb-4">🎯</div>
                    <h3 class="text-xl font-semibold mb-2">Tone Adjustment</h3>
                    <p class="text-gray-400">Adapt your message tone from casual to formal with a single click.</p>
                </div>

                <div class="p-6 bg-gray-900/50 rounded-xl border border-gray-700">
                    <div class="text-indigo-400 text-3xl mb-4">🔒</div>
                    <h3 class="text-xl font-semibold mb-2">Secure & Private</h3>
                    <p class="text-gray-400">Enterprise-grade security with end-to-end encryption for all your
                        communications.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-4xl font-bold mb-6">Ready to Transform Your Email?</h2>
                <p class="text-gray-300 mb-8">Join thousands of professionals already improving their communication with
                    MailCraft</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('create.account') }}" wire:navigate
                        class="px-8 py-4 bg-purple-600 hover:bg-purple-700 rounded-lg font-semibold transition-colors">
                        Start Free 
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
