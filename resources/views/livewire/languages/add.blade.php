<?php

use Livewire\Volt\Component;

new class extends Component {
    public $language;
    
    public function storelanguage()
    {
        try {
            language::create([
                'user_id' => request()->user()->id,
                'language' => $this->language,
            ]);
            return redirect()
                ->route('dashboard')
                ->with('notify', [
                    'type' => 'success',
                    'message' => 'Language Created Succesfully ',
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

<div>
    <!-- Main Content -->
    <div class="max-w-2xl mx-auto mt-12 p-8 bg-gray-800 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-center mb-6 text-white">Manage Languages</h2>

        <!-- Modern Add Language Form -->
        <form class="flex flex-col items-center space-y-4"wire:submit.prevent="storelanguage">
            <input type="text" name="language" placeholder="Enter new language..." wire:model="language"
                class="bg-gray-700 text-white rounded-lg px-4 py-3 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required />
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Add Language</span>
            </button>
        </form>


        <!-- Scrollable List of Languages -->
        <div class="mt-6 max-h-60 overflow-y-auto bg-gray-900 rounded-lg p-4 ">
            <ul class="space-y-2">
                @foreach ($userLanguages as $languages)
                    <li class="bg-gray-700 px-4 py-3 rounded-lg flex justify-between items-center">
                        <span>{{ $languages->language }}</span>
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

</div>
