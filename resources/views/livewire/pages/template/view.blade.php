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

<!-- Full-page Container -->
<div class="min-h-screen bg-gray-900 flex flex-col items-center justify-center p-4">
    <!-- Floating Container with Animated Gradient Border -->
    <div
        class="w-full max-w-4xl h-[90vh] bg-gray-800/40 backdrop-blur-3xl rounded-3xl shadow-2xl 
                  border border-white/10 relative overflow-hidden
                  transform transition-all duration-500 hover:scale-[1.02] group
                  before:absolute before:inset-0 before:animate-gradient-pulse before:bg-gradient-to-r 
                  before:from-cyan-500/20 before:via-purple-500/20 before:to-cyan-500/20 before:-z-10">

        <div class="p-8 space-y-8 relative h-full flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between pb-6 border-b border-white/10">
                <h3
                    class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-purple-400 
                              bg-clip-text text-transparent animate-text-gradient">
                    TEMPLATE PREVIEW
                </h3>
            </div>

            <!-- Content Area with Static Data -->
            <div
                class="prose prose-invert max-w-none flex-1 overflow-y-auto scrollbar-thin 
                           scrollbar-track-gray-800/50 scrollbar-thumb-cyan-500/70 pr-4">
                <div
                    class="whitespace-pre-line leading-relaxed text-gray-200/90 font-light
                              bg-gradient-to-br from-white/5 to-transparent p-6 rounded-xl">
                    <h2 class="text-xl font-semibold text-cyan-300">Welcome to the Template Preview</h2>
                    <p>This is a static preview of your template. Below is some sample content:</p>

                    <ul class="list-disc list-inside text-gray-300">
                        <li>Feature 1: Fully responsive design</li>
                        <li>Feature 2: Modern UI with animated transitions</li>
                        <li>Feature 3: Customizable and easy to use</li>
                    </ul>

                    <p>Thank you for trying out this template. Feel free to modify it as needed.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-6 border-t border-white/10 text-sm text-gray-400">
                <p>© 2023 Your Company. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
