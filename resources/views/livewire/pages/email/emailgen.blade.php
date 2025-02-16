<?php
use Livewire\Volt\Component;
use App\Services\GroqApiService;
use Livewire\Attributes\Layout;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

new #[Layout('layouts.app')] class extends Component {
    public string $subject = '';
    public string $content = '';
    public string $generatedEmail = '';
    public string $editableEmail = '';
    public string $email = '';
    public string $cc = '';
    public string $command = '';
    public bool $isGenerating = false;
    public bool $isModifying = false;

    public function generateEmail(GroqApiService $apiService)
    {
        $this->reset('generatedEmail', 'editableEmail');
        $this->validate([
            'email' => 'required|email',
            'cc' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $this->isGenerating = true;

        try {
            $prompt = "Write a professional email with proper formatting about: {$this->subject}\nDetails: {$this->content}";
            $response = $apiService->generateEmail($prompt);

            if (isset($response['error'])) {
                $this->addError('api', 'API request failed: ' . $response['message']);
            } else {
                $this->generatedEmail = $response;
                $this->editableEmail = $response;
                $this->dispatch('email-generated'); // Dispatch event to collapse form
            }
        } finally {
            $this->isGenerating = false;
        }
    }

    public function modifyEmail(GroqApiService $apiService)
    {
        $this->validate(['command' => 'required|string|max:500']);
        $this->isModifying = true;

        try {
            $prompt = "Modify this email according to: {$this->command}\n\nCurrent email:\n{$this->editableEmail}\n\nProvide only the modified email without explanations.";
            $response = $apiService->generateEmail($prompt);

            if (isset($response['error'])) {
                $this->addError('api', 'Modification failed: ' . $response['message']);
            } else {
                $this->editableEmail = $response;
                $this->command = '';
                $this->dispatch('email-modified');
            }
        } finally {
            $this->isModifying = false;
        }
    }

    public function sendMail()
    {
        $this->validate([
            'email' => 'required|email',
            'cc' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $emails = array_map('trim', explode(',', $value));
                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail('One or more CC emails are invalid');
                        }
                    }
                },
            ],
            'subject' => 'required|string|max:255',
            'editableEmail' => 'required|string',
        ]);

        try {
            $mail = Mail::to($this->email)
                ->when(!empty($this->cc), function ($message) {
                    $ccEmails = array_map('trim', explode(',', $this->cc));
                    $message->cc($ccEmails);
                })
                ->send(new SendMail(subject: $this->subject, content: $this->editableEmail));

            // Flash success message
            Session::flash('notify', [
                'type' => 'success',
                'message' => 'Email sent successfully!',
            ]);

            $this->resetExcept(['email', 'cc', 'subject', 'editableEmail']);
        } catch (\Throwable $th) {
            // Flash error message
            Session::flash('notify', [
                'type' => 'error',
                'message' => 'Failed to send email: ' . $th->getMessage(),
            ]);
        }
    }
};
?>
<div>
    <!-- Flash Messages -->
    @if (session('notify'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:leave="transition ease-in duration-200"
             class="fixed top-4 right-4 z-50 max-w-md w-full">
            <div class="px-4 py-3 rounded-lg shadow-xl backdrop-blur-lg bg-gray-800/95 border border-gray-700
                @if (session('notify.type') === 'success') text-emerald-400 @endif
                @if (session('notify.type') === 'error') text-red-400 @endif">
                <div class="flex items-center gap-3">
                    <div class="shrink-0">
                        @if (session('notify.type') === 'success')
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
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
    <div x-data="{ showInputForm: true }" class="min-h-screen bg-gray-900 flex flex-col items-center py-8 text-gray-100">
        <div class="w-full max-w-2xl px-4">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Email Assistant
                </h1>
                <p class="mt-3 text-gray-400">AI-powered email composition made simple</p>
            </div>

            <!-- Toggle Button for Input Form -->
            <div class="flex justify-center mb-4">
                <button @click="showInputForm = !showInputForm"
                    class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 
                           text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                    <span x-text="showInputForm ? 'Hide Input Form' : 'Show Input Form'"></span>
                    <svg x-show="!showInputForm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg x-show="showInputForm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Input Form -->
            <div x-show="showInputForm" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="w-full bg-gray-800 rounded-xl shadow-xl border border-gray-700 mb-8">
                <form wire:submit.prevent="generateEmail" class="space-y-6 p-6">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 gap-5">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Recipient Details</label>
                                    <input type="email" wire:model="email" 
                                           class="w-full px-4 py-2.5 rounded-lg bg-gray-700 border border-gray-600 text-gray-100
                                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                           placeholder="Recipient email">
                                    <input type="text" wire:model="cc" 
                                           class="mt-2 w-full px-4 py-2.5 rounded-lg bg-gray-700 border border-gray-600 text-gray-100
                                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                           placeholder="CC (comma separated)">
                                </div>

                                <input type="text" wire:model="subject" 
                                       class="w-full px-4 py-2.5 rounded-lg bg-gray-700 border border-gray-600 text-gray-100
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                       placeholder="Email subject">

                                <textarea wire:model="content" rows="4"
                                       class="w-full px-4 py-2.5 rounded-lg bg-gray-700 border border-gray-600 text-gray-100
                                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                       placeholder="Describe your email content..."></textarea>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 
                                       text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 
                                       flex items-center justify-center space-x-2 relative overflow-hidden">
                                <span wire:loading.remove class="relative z-10">Generate Email</span>
                                <span wire:loading class="relative z-10">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                <div class="absolute inset-0 bg-white/5 backdrop-blur-sm transition-opacity" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-show="wire:loading"></div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Generated Email Section -->
            @if ($editableEmail)
                <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 mb-8">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-700 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-100">Generated Email</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="$clipboard.write($refs.emailContent.textContent)"
                                    class="text-gray-400 hover:text-blue-400 p-2 rounded-lg hover:bg-gray-700 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <button wire:click="sendMail" wire:loading.attr="disabled"
                                    class="text-gray-400 hover:text-blue-400 p-2 rounded-lg hover:bg-gray-700 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4 text-gray-300">
                            <div class="text-sm space-y-1">
                                <div><span class="font-medium text-gray-400">To:</span> {{ $email }}</div>
                                @if ($cc)<div><span class="font-medium text-gray-400">CC:</span> {{ $cc }}</div>@endif
                                <div><span class="font-medium text-gray-400">Subject:</span> {{ $subject }}</div>
                            </div>
                            
                            <div x-ref="emailContent" class="prose prose-invert max-w-none border-t border-gray-700 pt-4">
                                <div id="editable-email" class="whitespace-pre-line leading-relaxed text-gray-300">
                                    {{ $editableEmail }}
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-700">
                                <form wire:submit.prevent="modifyEmail" class="flex gap-3">
                                    <input type="text" wire:model="command"
                                        class="flex-1 px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-gray-100
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
                                        placeholder="Modification instructions...">
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-all
                                               flex items-center justify-center min-w-[120px] relative overflow-hidden">
                                        <span wire:loading.remove class="relative z-10">Update</span>
                                        <span wire:loading class="relative z-10">
                                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        <div class="absolute inset-0 bg-white/5 backdrop-blur-sm transition-opacity" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-show="wire:loading"></div>
                                    </button>
                                </form>
                                <p class="text-sm text-gray-500 mt-2">
                                    Examples: "Make more formal", "Shorten to 3 paragraphs", "Add call-to-action"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>