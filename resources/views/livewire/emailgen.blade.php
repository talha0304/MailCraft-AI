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
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-10"
            class="fixed top-4 right-4 z-50 min-w-[300px]">
            <div
                class="px-6 py-4 rounded-lg shadow-lg bg-white border-l-4 
                @if (session('notify.type') === 'success') border-green-500 text-green-600 @endif
                @if (session('notify.type') === 'error') border-red-500 text-red-600 @endif">
                <div class="flex items-center gap-3">
                    <div class="shrink-0">
                        @if (session('notify.type') === 'success')
                            <!-- Checkmark Icon for Success -->
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                        @if (session('notify.type') === 'error')
                            <!-- Cross Icon for Error -->
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
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
    <div x-data="{ showInputForm: true }" class="min-h-screen bg-gray-900 flex flex-col items-center py-8 text-white">
        <div class="w-full max-w-3xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1
                    class="text-3xl font-bold bg-gradient-to-r from-indigo-400 to-purple-500 bg-clip-text text-transparent">
                    AI Email Assistant
                </h1>
                <p class="mt-2 text-gray-400">Your professional email writing partner</p>
            </div>

            <!-- Toggle Button for Input Form -->
            <div class="flex justify-center mb-4">
                <button @click="showInputForm = !showInputForm"
                    class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg text-sm font-medium transition-all">
                    <span x-text="showInputForm ? 'Hide Input Form' : 'Show Input Form'"></span>
                </button>
            </div>

            <!-- Input Form -->
            <div x-show="showInputForm" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
                class="sticky bottom-0 bg-gray-900/95 backdrop-blur-sm pt-6">
                <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700/50">
                    <form wire:submit.prevent="generateEmail" class="space-y-4 p-4">
                        <!-- Form Fields -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                <input type="email" wire:model="email"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5
                                              focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                              transition duration-200 placeholder-gray-400 text-sm"
                                    placeholder="Recipient email">

                                <input type="text" wire:model="cc"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5
                                              focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                              transition duration-200 placeholder-gray-400 text-sm"
                                    placeholder="CC (comma-separated emails)">

                                <input type="text" wire:model="subject"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5
                                              focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                              transition duration-200 placeholder-gray-400 text-sm"
                                    placeholder="Email subject">
                            </div>

                            <textarea wire:model="content" rows="3"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 
                                             focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                             transition duration-200 placeholder-gray-400 text-sm"
                                placeholder="Describe your email content..."></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-red-400 text-sm">
                                @error('email')
                                    {{ $message }}
                                @enderror
                                @error('cc')
                                    {{ $message }}
                                @enderror
                                @error('subject')
                                    {{ $message }}
                                @enderror
                                @error('content')
                                    {{ $message }}
                                @enderror
                                @error('command')
                                    {{ $message }}
                                @enderror
                                @if ($errors->has('api'))
                                    <div class="text-red-400 text-sm">
                                        ⚠️ {{ $errors->first('api') }}
                                    </div>
                                @endif
                            </div>

                            <button type="submit" wire:loading.attr="disabled"
                                class="bg-indigo-600 hover:bg-indigo-700 px-6 py-2.5 rounded-lg font-medium
                                           transition duration-200 transform hover:scale-[1.02] disabled:opacity-50
                                           flex items-center justify-center space-x-2 text-sm">
                                <span wire:loading.remove>Generate Email</span>
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
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Chat History -->
            <div class="space-y-6 mb-8">
                @if ($editableEmail)
                    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)" x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0" class="flex gap-4">
                        <!-- AI Avatar -->
                        <div class="shrink-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                🤖
                            </div>
                        </div>

                        <!-- Email Bubble -->
                        <div class="flex-1 bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-700/50">
                            <div class="space-y-3 text-gray-300">
                                <!-- Email Content -->
                                <div class="flex items-center space-x-2 text-sm">
                                    <span class="font-medium text-indigo-400">Generated Email</span>
                                    <span class="text-gray-500">•</span>
                                    <button @click="$clipboard.write($refs.emailContent.textContent)"
                                        class="text-indigo-400 hover:text-indigo-300 transition text-xs">
                                        📋 Copy
                                    </button>
                                    <span class="text-gray-500">•</span>
                                    <button wire:click="sendMail" wire:loading.attr="disabled"
                                        class="text-indigo-400 hover:text-indigo-300 transition text-xs"
                                        @if (!$editableEmail) disabled @endif>
                                        📤 Send
                                        <span wire:loading wire:target="sendMail">
                                            <svg class="inline animate-spin h-4 w-4 ml-1"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-sm font-medium text-gray-400">To: {{ $email }}</div>
                                    @if ($cc)
                                        <div class="text-sm font-medium text-gray-400">CC: {{ $cc }}</div>
                                    @endif
                                    <div class="text-sm font-medium text-gray-400">Subject: {{ $subject }}</div>
                                </div>
                                <div x-ref="emailContent" class="prose prose-invert max-w-none text-current">
                                    <div id="editable-email" class="whitespace-pre-line leading-relaxed">
                                        {{ $editableEmail }}
                                    </div>
                                </div>

                                <!-- Modification Interface -->
                                <div class="pt-4 border-t border-gray-700/50">
                                    <form wire:submit.prevent="modifyEmail" class="flex gap-2">
                                        <input type="text" wire:model="command"
                                            class="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-4 py-2
                                                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                                      transition duration-200 placeholder-gray-400 text-sm"
                                            placeholder="Enter modification command (e.g., 'make it more formal', 'shorten to 3 paragraphs')"
                                            x-bind:disabled="$wire.isModifying">

                                        <button type="submit" wire:loading.attr="disabled"
                                            class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg
                                                       transition duration-200 disabled:opacity-50 text-sm
                                                       flex items-center justify-center min-w-[120px]">
                                            <span wire:loading.remove>Apply Changes</span>
                                            <span wire:loading>
                                                <svg class="animate-spin h-5 w-5 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </span>
                                        </button>
                                    </form>
                                    <div class="mt-2 text-xs text-gray-500">
                                        Example commands: "make it shorter", "add call-to-action", "use more formal
                                        tone"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
