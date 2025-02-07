<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <!-- Scrollable List of Languages -->
    <div class="mt-6 max-h-60 overflow-y-auto bg-gray-900 rounded-lg p-4 ">
        <ul class="space-y-2">
            @foreach ($userLanguages as $languages)
                <li class="bg-gray-700 px-4 py-3 rounded-lg flex justify-between items-center">
                    <span>{{ $languages->language}}</span>
                    <div class="flex space-x-3">
                        <button class="text-green-700 hover:text-green-600">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="text-red-500 hover:text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
