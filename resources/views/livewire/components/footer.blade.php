<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-gray-700/50 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
                <!-- Logo with gradient -->
                <div class="flex items-center space-x-3">
                    <i class="fas fa-envelope-open-text text-2xl bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent"></i>
                    <a href="#" class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent tracking-tight hover:opacity-80 transition-opacity">
                        MailCraft
                    </a>
                </div>

                <!-- Social Links -->
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors" aria-label="Twitter">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-purple-500 transition-colors" aria-label="GitHub">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-red-500 transition-colors" aria-label="YouTube">
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors" aria-label="LinkedIn">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                </div>

                <!-- Copyright with hover effect -->
                <p class="text-gray-500 text-sm hover:text-gray-300 transition-colors">
                    &copy; 2025 MailCraft. All rights reserved.
                </p>
            </div>

            <!-- Additional Links (optional) -->
            <div class="mt-8 border-t border-gray-700/50 pt-6 text-center md:text-left">
                <div class="flex flex-col md:flex-row justify-center md:justify-start space-y-4 md:space-y-0 md:space-x-8">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">FAQs</a>
                </div>
            </div>
        </div>
    </footer>
</div>
