<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\language;
use App\Models\GenratedEmail;
use App\Models\Template; // Add this model for template counts
use Carbon\Carbon;

new #[Layout('layouts.app')] class extends Component {
    public $languageCount;
    public $sentEmailCount;
    public $currentYear;
    public $emailCounts = [];
    public $templateCounts = [];
    public $GeratedTemplateCounts;

    public function mount()
    {
        // Get the current year
        $this->currentYear = Carbon::now()->year;

        // Count the number of languages for the current user
        $this->languageCount = language::where('user_id', '=', request()->user()->id)->count();

        // Count the total number of sent emails for the current user
        $this->sentEmailCount = GenratedEmail::where('user_id', '=', request()->user()->id)
            ->where('status', '=', 'sent')
            ->count();

        // Count the total number of GeratedTemplate for the current user
        $this->GeratedTemplateCounts = $templateCount = Template::where('user_id', request()->user()->id)->count();

        // Calculate the number of emails sent for each month of the current year
        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($this->currentYear, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::create($this->currentYear, $month, 1)->endOfMonth();

            // Email counts
            $emailCount = GenratedEmail::where('user_id', request()->user()->id)
                ->where('status', 'sent')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            $this->emailCounts[] = $emailCount;

            // Template counts
            $templateCount = Template::where('user_id', request()->user()->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            $this->templateCounts[] = $templateCount;
        }
    }
};
?>

<div class="gradient-bg text-gray-100 min-h-screen p-8">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0f172a, #1e293b);
        }

        .cyber-glow {
            position: relative;
            overflow: hidden;
        }

        .cyber-glow::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    rgba(99, 102, 241, 0.2) 0%,
                    rgba(16, 185, 129, 0.2) 50%,
                    rgba(99, 102, 241, 0.2) 100%);
            animation: rotate 6s linear infinite;
            z-index: 0;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .cyber-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .cyber-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }

        .holographic-effect {
            position: relative;
        }

        .holographic-effect::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(120deg,
                    rgba(255, 255, 255, 0.05) 0%,
                    rgba(255, 255, 255, 0.01) 50%,
                    rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .neon-text {
            text-shadow: 0 0 8px rgba(99, 102, 241, 0.4);
        }

        .dashboard-title {
            background: linear-gradient(45deg, #6366f1, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        .chart-container {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 1.5rem;
        }

        .stats-icon {
            background: linear-gradient(45deg, #6366f1, #3b82f6);
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
        }
    </style>

    <!-- Flash Messages -->
    @if (session('notify'))
        <!-- Keep your existing flash message style -->
    @endif

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-4xl font-bold dashboard-title">DASHBOARD</h1>
            <div class="flex items-center space-x-4">
                <div class="cyber-card px-4 py-2 hover:bg-gray-800/20">
                    <span class="text-lg font-medium">📅 {{ now()->format('F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="cyber-card p-6 cyber-glow">
                <div class="flex items-center justify-between holographic-effect">
                    <div class="z-10">
                        <p class="text-gray-400 mb-1">Emails Sent</p>
                        <p class="text-3xl font-bold neon-text">{{ $sentEmailCount }}</p>
                    </div>
                    <div class="stats-icon rounded-full p-3 transform hover:rotate-12 transition-transform">
                        <i class="fas fa-paper-plane text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-1 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full"></div>
                </div>
            </div>
            <a href="{{ route('show.lang') }}"wire:navigate>
                <div class="cyber-card p-6 cyber-glow">
                    <div class="flex items-center justify-between holographic-effect">
                        <div class="z-10">
                            <p class="text-gray-400 mb-1">Active language</p>
                            <p class="text-3xl font-bold neon-text">{{ $languageCount }}</p>
                        </div>
                        <div class="stats-icon rounded-full p-3 transform hover:rotate-12 transition-transform">
                            <i class="fas fa-language text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="h-1 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full"></div>
                    </div>
                </div>
            </a>
            <a href="{{ route('show.template') }}" wire:navigate>
                <div class="cyber-card p-6 cyber-glow">
                    <div class="flex items-center justify-between holographic-effect">
                        <div class="z-10">
                            <p class="text-gray-400 mb-1">Generated Template</p>
                            <p class="text-3xl font-bold neon-text">{{ $GeratedTemplateCounts }}</p>
                        </div>
                        <div class="stats-icon rounded-full p-3 transform hover:rotate-12 transition-transform">
                            <i class="fas fa-clipboard text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="h-1 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full"></div>
                    </div>
                </div>
            </a>
            <a href="{{ route('email.gen') }}">
                <div class="cyber-card p-6 cyber-glow">
                    <div class="flex items-center justify-between holographic-effect">
                        <div class="z-10">
                            <p class="text-gray-400 mb-1">Generated Emails</p>
                            <p class="text-3xl font-bold neon-text">0</p>
                        </div>
                        <div class="stats-icon rounded-full p-3 transform hover:rotate-12 transition-transform">
                            <i class="fas fa-envelope text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="h-1 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full"></div>
                    </div>
                </div>
            </a>
            <!-- Repeat similar structure for other stats cards -->
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Email Chart -->
            <div class="cyber-card p-6">
                <h2 class="text-xl font-semibold mb-4 neon-text">Email Analytics</h2>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="emailChart"></canvas>
                </div>
            </div>

            <!-- Template Chart -->
            <div class="cyber-card p-6">
                <h2 class="text-xl font-semibold mb-4 neon-text">Template Analytics</h2>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="templateChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced Chart Config
        const chartConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.4,
                    borderWidth: 3
                },
                point: {
                    radius: 5,
                    hoverRadius: 8
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        };

        // Email Chart
        new Chart(document.getElementById('emailChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Emails Sent',
                    data: @json($emailCounts),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1'
                }]
            },
            options: chartConfig
        });

        // Template Chart
        new Chart(document.getElementById('templateChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Templates Generated',
                    data: @json($templateCounts),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981'
                }]
            },
            options: chartConfig
        });
    </script>
</div>
