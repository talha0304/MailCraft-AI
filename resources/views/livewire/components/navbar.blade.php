<?php
use Livewire\Volt\Component;

new class extends Component {
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('notify', [
            'type' => 'success',
            'message' => 'Logged out successfully!',
        ]);
    }
}; ?>

<div>
    <nav class="bg-gradient-to-r from-gray-900 to-gray-800 border-b border-gray-700 shadow-xl">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo with modern gradient -->
                <div class="flex-shrink-0 flex items-center space-x-3">
                    <i class="fas fa-envelope-open-text text-2xl bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent"></i>
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent tracking-tight">
                        MailCraft
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <!-- User Dropdown -->
                    <div class="relative group" x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl py-2 transform origin-top transition-all duration-200 scale-95 group-hover:scale-100 opacity-0 group-hover:opacity-100"
                            x-show="open"
                            x-cloak
                            style="display: none;">
                            <a href="#" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                <i class="fas fa-user-circle mr-2"></i>Profile
                            </a>
                            <a href="#" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <a href="#" wire:click="logout" wire:navigate class="block px-4 py-3 text-red-400 hover:bg-gray-700 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="isOpen = !isOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div class="md:hidden bg-gray-800/95 backdrop-blur-sm" x-show="isOpen" x-cloak style="display: none;">
            <div class="px-4 py-3 space-y-2">
                <div class="pt-4 border-t border-gray-700">
                    <a href="#" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-user-circle mr-3"></i>Profile
                    </a>
                    <a href="#" class="block px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-cog mr-3"></i>Settings
                    </a>
                    <a href="#" wire:click="logout" wire:navigate class="block px-4 py-3 text-red-400 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-3"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>