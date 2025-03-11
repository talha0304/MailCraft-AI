<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Template;

new #[Layout('layouts.app')] class extends Component {

    public  $templateId;
    public  $template;
    public string $name;
    public string $category;
    public ?string $subject = null;
    public string $content;

    public function mount($id): void
    {
        $this->template = Template::where('user_id', auth()->id())->findOrFail($id);
        $this->name = $this->template->name;
        $this->category = $this->template->category;
        $this->subject = $this->template->subject;
        $this->content = $this->template->content;
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:WORK,PERSONAL,MARKETING',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string|min:50',
        ], [
            'name.required' => 'Template name is required',
            'content.min' => 'Content should be at least 50 characters',
            'category.in' => 'Invalid category selected',
        ]);

        try {
            $this->template->update($validated);
            return redirect()->route('show.template')->with('notify', [
                'type' => 'success',
                'message' => 'Template updated successfully',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Template update failed: ' . $e->getMessage());
            return back()->with('notify', [
                'type' => 'error',
                'message' => 'Failed to update template. Please try again.',
            ]);
        }
    }
};
?>

<div class="min-h-screen bg-gradient-to-br from-gray-900 via-indigo-900 to-purple-900">
    <div class="max-w-5xl mx-auto p-6 relative">
        <!-- Animated background elements -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute w-72 h-72 bg-purple-500/20 blur-[100px] -top-20 -left-20 animate-pulse"></div>
            <div class="absolute w-72 h-72 bg-indigo-500/20 blur-[100px] -bottom-20 -right-20 animate-pulse"></div>
        </div>

        @if (session('notify'))
            <div class="mb-6 p-4 rounded-xl bg-white/5 backdrop-blur-lg border border-white/10 shadow-xl">
                <div class="flex items-center gap-3 text-sm font-medium text-purple-300">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    {{ session('notify.message') }}
                </div>
            </div>
        @endif

        <form wire:submit.prevent="save" class="relative bg-gradient-to-br from-gray-800/50 to-indigo-900/20 backdrop-blur-2xl rounded-2xl shadow-2xl border border-white/10 overflow-hidden">
            <!-- Glowing border effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-blue-500/20 opacity-30 pointer-events-none"></div>

            <div class="p-8 space-y-8">
                <!-- Header Section -->
                <div class="space-y-6">
                    <input wire:model="name" type="text"
                           class="w-full text-4xl font-bold bg-transparent border-none focus:ring-0 p-0 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-300 placeholder:text-gray-500"
                           placeholder="Untitled Template">
                    @error('name')<p class="mt-1 text-sm text-red-400 font-medium">{{ $message }}</p>@enderror

                    <select wire:model="category"
                            class="w-full px-5 py-3 rounded-xl bg-gray-700/50 backdrop-blur-sm border border-white/10 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 text-gray-200 transition-all duration-300 hover:bg-gray-700/70">
                        <option value="WORK" class="bg-gray-800">🚀 Work</option>
                        <option value="PERSONAL" class="bg-gray-800">🌟 Personal</option>
                        <option value="MARKETING" class="bg-gray-800">📈 Marketing</option>
                    </select>
                    @error('category')<p class="mt-1 text-sm text-red-400 font-medium">{{ $message }}</p>@enderror
                </div>

                <!-- Content Section -->
                <div class="space-y-8">
                    <input wire:model="subject" type="text"
                           class="w-full px-5 py-3 rounded-xl bg-gray-700/50 backdrop-blur-sm border border-white/10 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 text-gray-200 placeholder-gray-500 transition-all duration-300"
                           placeholder="✉️ Email Subject (Optional)">
                    @error('subject')<p class="mt-1 text-sm text-red-400 font-medium">{{ $message }}</p>@enderror

                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 opacity-30 rounded-xl transition-opacity duration-300 group-hover:opacity-50"></div>
                        <textarea wire:model.live="content" rows="12"
                                  class="w-full p-6 rounded-xl bg-gray-700/50 backdrop-blur-sm border border-white/10 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 text-gray-200 placeholder-gray-500 resize-none transition-all duration-300 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-white/20 hover:scrollbar-thumb-white/30"></textarea>
                        @error('content')<p class="mt-1 text-sm text-red-400 font-medium">{{ $message }}</p>@enderror
                        
                        <!-- Floating character count -->
                        <div class="absolute bottom-4 right-4 px-3 py-1 rounded-full bg-gray-800/80 backdrop-blur-sm text-sm font-medium text-blue-400">
                            {{ strlen($content) }} chars
                        </div>
                    </div>
                </div>

                <!-- Holographic Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-5 rounded-xl bg-gray-700/30 backdrop-blur-sm border border-white/10 hover:border-blue-400/30 transition-all duration-300">
                        <p class="text-sm text-gray-400 mb-1">Last Updated</p>
                        <p class="text-xl font-semibold text-purple-300">
                            {{ $template->updated_at->format('M j, Y') }}
                        </p>
                        <p class="text-sm text-gray-400">{{ $template->updated_at->format('g:i A') }}</p>
                    </div>
                    
                    <div class="p-5 rounded-xl bg-gray-700/30 backdrop-blur-sm border border-white/10 hover:border-blue-400/30 transition-all duration-300">
                        <p class="text-sm text-gray-400 mb-1">Template Health</p>
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-full bg-gray-600 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-400 to-cyan-400 w-3/4"></div>
                            </div>
                            <span class="text-cyan-400 text-sm font-medium">84%</span>
                        </div>
                    </div>
                    
                    <div class="p-5 rounded-xl bg-gray-700/30 backdrop-blur-sm border border-white/10 hover:border-blue-400/30 transition-all duration-300">
                        <p class="text-sm text-gray-400 mb-1">Version Control</p>
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <p class="text-purple-300 font-semibold">v2.1.5</p>
                                <p class="text-xs text-gray-400">Latest stable</p>
                            </div>
                            <div class="p-2 rounded-lg bg-green-500/10 border border-green-500/20">
                                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl font-bold text-white hover:from-blue-400 hover:to-purple-400 transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-blue-500/20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent opacity-20"></div>
                    <span class="relative flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Save Evolution
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>