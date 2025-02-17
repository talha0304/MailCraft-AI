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
                    <div class="text-sm font-medium">
                        {{ session('notify.message') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div x-data="{ showInputForm: true }"
        class="min-h-screen bg-gray-900 flex flex-col items-center py-8 text-gray-100 relative overflow-hidden">
        <!-- Background gradient effects -->
        <div
            class="absolute -top-1/3 left-1/2 w-[800px] h-[800px] bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full blur-3xl -translate-x-1/2">
        </div>

        <div class="w-full max-w-2xl px-4 relative z-10">
            <!-- Header -->
            <div class="text-center mb-12 space-y-4">
                <h1
                    class="text-5xl font-bold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent animate-gradient">
                    MailCraft
                </h1>
                <p class="mt-3 text-gray-400 font-light"> AI Email Composition</p>
            </div>

            <!-- Toggle Button with Holographic effect -->
            <div class="flex justify-center mb-8">
                <button @click="showInputForm = !showInputForm"
                    class="bg-gradient-to-r from-cyan-600/60 to-purple-600/60 backdrop-blur-lg hover:from-cyan-500/50 hover:to-purple-500/50 
                           text-white px-8 py-3 rounded-2xl font-medium transition-all duration-300 flex items-center gap-3
                           border border-white/10 hover:border-white/20 shadow-[0_0_30px_rgba(112,144,176,0.1)]">
                    <span x-text="showInputForm ? 'Collapse Interface' : 'Expand Interface'"></span>
                    <svg x-show="!showInputForm" class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                    </svg>
                    <svg x-show="showInputForm" class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
            </div>

            <!-- Input Form with Glassmorphism -->
            <div x-show="showInputForm" x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 -translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-10"
                class="w-full bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 mb-8">
                <form wire:submit.prevent="generateEmail" class="space-y-6 p-6">
                    <div class="space-y-6">
                        <!-- Recipient Section -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="space-y-4">
                                    <div class="relative group">
                                        <div
                                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-2xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                                        </div>
                                        <div class="relative space-y-4">
                                            <input type="email" wire:model="email"
                                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100
                                                          focus:ring-2 focus:ring-cyan-500 focus:border-transparent placeholder-gray-500
                                                          transition-all duration-300"
                                                placeholder="Recipient Email  Address">
                                            <input type="text" wire:model="cc"
                                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100
                                                          focus:ring-2 focus:ring-purple-500 focus:border-transparent placeholder-gray-500
                                                          transition-all duration-300"
                                                placeholder=" CC  (comma separated)">
                                        </div>
                                    </div>

                                    <!-- Preferences Dropdown Section -->
                                    <div class="relative group">
                                        <div
                                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                                        </div>
                                        <div class="relative">
                                            <select wire:model="emailTone"
                                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100
                       focus:ring-2 focus:ring-cyan-500 focus:border-transparent appearance-none
                       transition-all duration-300">
                                                <option value="" disabled selected>Select Tone Preference</option>
                                                <option value="professional">Professional</option>
                                                <option value="casual">Casual</option>
                                                <option value="sales">Sales</option>
                                                <option value="technical">Technical</option>
                                            </select>
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Template Dropdown Section -->
                                    <div class="relative group mt-4">
                                        <div
                                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600 to-purple-600 rounded-xl opacity-20 blur transition-all duration-300 group-hover:opacity-40">
                                        </div>
                                        <div class="relative">
                                            <select wire:model="emailTemplate"
                                                class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100
                       focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none
                       transition-all duration-300">
                                                <option value="" disabled selected>Choose Email Template</option>
                                                <option value="formal-invitation">Formal Invitation</option>
                                                <option value="follow-up">Follow-Up</option>
                                                <option value="newsletter">Newsletter</option>
                                                <option value="onboarding">Onboarding</option>
                                            </select>
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="text" wire:model="subject"
                                        class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100
                                                  focus:ring-2 focus:ring-purple-500 focus:border-transparent placeholder-gray-500
                                                  transition-all duration-300"
                                        placeholder="Email Subject">

                                    <textarea wire:model="content" rows="4"
                                        class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100
                                                  focus:ring-2 focus:ring-cyan-500 focus:border-transparent placeholder-gray-500
                                                  transition-all duration-300 resize-none"
                                        placeholder="Description..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Generate Button -->
                        <div class="pt-2">
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 
                                       text-white px-8 py-4 rounded-xl font-medium transition-all duration-300 
                                       flex items-center justify-center space-x-3 relative overflow-hidden group">
                                <span class="relative z-10 flex items-center gap-2">
                                    <span wire:loading.remove>Generate Email</span>
                                    <span wire:loading>
                                        <svg class="animate-spin h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </span>
                                </span>
                                <div
                                    class="absolute inset-0 bg-white/5 backdrop-blur-sm transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                                </div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Generated Email Section -->
            @if ($editableEmail)
                <div class="bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 mb-8">
                    <div class="p-6 space-y-6">
                        <!-- Header Section -->
                        <div class="flex items-center justify-between pb-4 border-b border-white/10">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-cyan-900/30 border border-cyan-400/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3
                                    class="text-xl font-semibold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">
                                    Neural Output
                                </h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="$clipboard.write($refs.emailContent.textContent)"
                                    class="p-2.5 rounded-xl hover:bg-white/5 transition-all duration-300 group relative"
                                    x-tooltip="Copy Transmission">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button wire:click="sendMail" wire:loading.attr="disabled"
                                    class="p-2.5 rounded-xl hover:bg-white/5 transition-all duration-300 group relative"
                                    x-tooltip="Transmit Signal">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Email Content -->
                        <div class="space-y-6 text-gray-300">
                            <div class="text-sm space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-cyan-400 w-16">To:</span>
                                    <span class="font-mono text-gray-300">{{ $email }}</span>
                                </div>
                                @if ($cc)
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-purple-400 w-16">CC:</span>
                                        <span class="font-mono text-gray-300">{{ $cc }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-cyan-400 w-16">Subject:</span>
                                    <span class="font-semibold text-gray-100">{{ $subject }}</span>
                                </div>
                            </div>

                            <!-- Editable Content Area -->
                            <div x-ref="emailContent"
                                class="prose prose-invert max-w-none pt-4 border-t border-white/10">
                                <div id="editable-email"
                                    class="whitespace-pre-line leading-relaxed text-gray-300 font-light">
                                    {{ $editableEmail }}
                                </div>
                            </div>

                            <!-- Modification Interface -->
                            <div class="pt-6 border-t border-white/10">
                                <form wire:submit.prevent="modifyEmail" class="flex gap-4">
                                    <div class="relative flex-1 group">
                                        <div
                                            class="absolute -inset-1 bg-gradient-to-r from-cyan-600/30 to-purple-600/30 rounded-xl blur opacity-20 group-hover:opacity-30 transition-all duration-300">
                                        </div>
                                        <input type="text" wire:model="command"
                                            class="w-full px-5 py-3 bg-gray-900/50 border border-white/10 rounded-xl text-gray-100
                                                   focus:ring-2 focus:ring-purple-500 focus:border-transparent placeholder-gray-500
                                                   transition-all duration-300 relative"
                                            placeholder="Input modification protocol...">
                                    </div>
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="px-6 py-3 bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 
                                               text-white rounded-xl transition-all duration-300 flex items-center justify-center 
                                               min-w-[140px] relative overflow-hidden group">
                                        <span class="relative z-10 flex items-center gap-2">
                                            <span wire:loading.remove>Apply Patch</span>
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
                                        </span>
                                        <div
                                            class="absolute inset-0 bg-white/5 backdrop-blur-sm transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                                        </div>
                                    </button>
                                </form>
                                <p class="text-sm text-gray-500 mt-3 ml-1">
                                    Example protocols: "Activate formal protocol", "Enable concise mode", "Initiate
                                    engagement sequence"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
