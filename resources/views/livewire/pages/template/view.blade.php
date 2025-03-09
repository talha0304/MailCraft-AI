<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Template;

new #[Layout('layouts.app')] class extends Component {
    public $templateId;
    public $template;

    public function mount($id)
    {
        $this->templateId = $id ;
        $this->template = Template::where('user_id','=',auth()->user()->id)->findOrFail($this->templateId);
    }

   
};
?>

<div class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <!-- Main Card Container -->
    <div class="w-full max-w-4xl h-[85vh] bg-gray-800/60 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 relative overflow-hidden transition-all duration-300 hover:shadow-cyan-500/20">
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-purple-500/5 pointer-events-none"></div>
        
        <div class="p-8 space-y-8 relative h-full flex flex-col">
            <!-- Header Section -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-700">
                <div>
                    <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-300">
                        Template Preview
                    </h1>
                    <p class="text-sm text-gray-400 mt-1">Created {{ $this->template->created_at->diffForHumans() }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 text-sm font-medium">
                    {{ Str::upper($this->template->category ?? 'DEFAULT') }}
                </span>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto 
                scrollbar-thin 
                scrollbar-track-transparent 
                scrollbar-thumb-gray-700 
                hover:scrollbar-thumb-cyan-500/80 
                scrollbar-thumb-rounded-full
                scrollbar-track-rounded-full
                pr-3">
                
                <div class="prose prose-invert max-w-none space-y-6">
                    <div class="group relative p-6 rounded-xl bg-gray-700/10 border border-gray-700/50 transition-all duration-300 hover:border-cyan-400/30">
                        <!-- Action Buttons -->
                        <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="copyContent" class="p-2 hover:bg-gray-700/30 rounded-lg" title="Copy content">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                            <button wire:click="generateEmail" class="p-2 hover:bg-gray-700/30 rounded-lg" title="Generate email">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                        
                        <pre class="whitespace-pre-wrap font-[450] leading-relaxed text-gray-200/95 tracking-wide">
                            {{ $this->template->content }}
                        </pre>
                    </div>
                    
                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="p-4 rounded-lg bg-gray-700/10 border border-gray-700/30">
                            <p class="text-gray-400">Characters</p>
                            <p class="text-cyan-400 font-medium">{{ strlen($this->template->content) }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-700/10 border border-gray-700/30">
                            <p class="text-gray-400">Last Updated</p>
                            <p class="text-purple-400 font-medium">{{ $this->template->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>