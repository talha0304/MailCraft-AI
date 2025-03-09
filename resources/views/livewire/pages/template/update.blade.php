<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Template;

new #[Layout('layouts.app')] class extends Component {
    public $templateId;
    public $template;
    public $title;
    public $category;
    public $subject;
    public $content;

    public function mount($id)
    {
        $this->templateId = $id;
        $this->template = Template::where('user_id', auth()->id())->findOrFail($this->templateId);
        
        // Initialize form fields
        $this->title = $this->template->title;
        $this->category = $this->template->category;
        $this->subject = $this->template->subject;
        $this->content = $this->template->content;
    }

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);
        $this->template->update($validated);
    }
};
?>

<div class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="w-full max-w-4xl h-[85vh] bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 relative overflow-hidden transition-all duration-300 hover:shadow-cyan-500/20">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-purple-500/5 pointer-events-none"></div>
        
        <form wire:submit.prevent="save" class="p-8 space-y-8 relative h-full flex flex-col">
            <!-- Header Section -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-700">
                <div class="flex-1 pr-4">
                    <input 
                        wire:model="title"
                        type="text" 
                        class="text-3xl font-bold bg-transparent border-b border-transparent focus:border-cyan-400 focus:outline-none text-cyan-400 w-full"
                        placeholder="Template Title"
                    >
                    @error('title')<span class="text-red-400 text-sm">{{ $message }}</span>@enderror
                </div>
                
                <div class="flex flex-col items-end gap-3">
                    <select 
                        wire:model="category"
                        class="px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 text-sm font-medium border border-transparent focus:border-cyan-400/50 outline-none"
                    >
                        <option value="WORK">Work</option>
                        <option value="PERSONAL">Personal</option>
                        <option value="MARKETING">Marketing</option>
                    </select>
                    @error('category')<span class="text-red-400 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-700 hover:scrollbar-thumb-cyan-500/80 pr-3">
                <div class="space-y-6">
                    <!-- Subject Input -->
                    <div class="group relative">
                        <input 
                            wire:model="subject"
                            type="text" 
                            class="w-full bg-gray-700/10 border border-gray-700/50 rounded-lg px-4 py-3 text-gray-200 focus:border-cyan-400/30 focus:outline-none"
                            placeholder="Email Subject"
                        >
                        @error('subject')<span class="text-red-400 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <!-- Content Editor -->
                    <div class="group relative p-6 rounded-xl bg-gray-700/10 border border-gray-700/50 transition-all duration-300 hover:border-cyan-400/30">
                        <textarea 
                            wire:model.live="content"
                            rows="12"
                            class="w-full bg-transparent text-gray-200/95 tracking-wide focus:outline-none resize-none"
                            placeholder="Write your template content here..."
                        ></textarea>
                        @error('content')<span class="text-red-400 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="p-4 rounded-lg bg-gray-700/10 border border-gray-700/30">
                            <p class="text-gray-400">Characters</p>
                            <p class="text-cyan-400 font-medium">{{ strlen($this->content) }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-700/10 border border-gray-700/30">
                            <p class="text-gray-400">Last Updated</p>
                            <p class="text-purple-400 font-medium">{{ $template->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-700/10 border border-gray-700/30">
                            <p class="text-gray-400">Status</p>
                            <p class="text-green-400 font-medium">Saved</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="border-t border-gray-700 pt-6">
                <button type="submit" class="w-full py-3 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>