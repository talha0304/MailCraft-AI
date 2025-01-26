<nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo -->
        <a href="#" class="text-white text-xl font-semibold">MailCraft</a>

        <!-- Menu (hidden on small screens) -->
        <ul class="hidden md:flex space-x-6">
            <li><a href="" wire:navigate class="text-white hover:text-gray-300">Home</a></li>
        </ul>

        <!-- Mobile Menu Button -->
        <button id="menuButton" class="md:hidden text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Dropdown Menu -->
    <ul id="mobileMenu" class="hidden flex-col space-y-2 mt-2 bg-gray-700 p-4 rounded-md md:hidden">
        <li><a href="#" class="text-white hover:text-gray-300">Home</a></li>
        <!-- Add more items here if needed -->
    </ul>
</nav>