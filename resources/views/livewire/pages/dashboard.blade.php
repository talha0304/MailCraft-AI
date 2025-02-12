<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\language;

new #[Layout('layouts.app')] class extends Component {
    public $languageCount;
    public function mount()
    {
        $this->languageCount = Language::where('user_id', '=', request()->user()->id)->count();
    }
};
?>
<div class="gradient-bg text-gray-100 min-h-screen p-8">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1f2937, #111827);
        }

        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
        }

        .chart-container {
            background: rgba(31, 41, 55, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
        }
    </style>

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

    <!-- Header -->
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Dashboard</h1>
            <div class="flex items-center space-x-4">
                <!-- Add any header buttons or icons here -->
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <a href="" wire:navigate>
                <div class="bg-gray-800 rounded-xl p-6 card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400">Total Emails Sent</p>
                            <p class="text-2xl font-bold">1,234</p>
                        </div>
                        <div class="bg-blue-500 rounded-full p-3">
                            <i class="fas fa-envelope-open-text text-white fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
            <a href="" wire:navigate>
                <div class="bg-gray-800 rounded-xl p-6 card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400">Saved Templates</p>
                            <p class="text-2xl font-bold">56</p>
                        </div>
                        <div class="bg-green-500 rounded-full p-3">
                            <i class="fas fa-file-alt text-white fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{ route('show.lang') }}" wire:navigate>
                <div class="bg-gray-800 rounded-xl p-6 card-hover">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400">Active Languages</p>
                            <p class="text-2xl font-bold">{{ $languageCount }}</p>
                        </div>
                        <div class="bg-purple-500 rounded-full p-3">
                            <i class="fas fa-language text-white fa-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
           <a href="" wire:navigate>
            <div class="bg-gray-800 rounded-xl p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400">Total Generated Emails</p>
                        <p class="text-2xl font-bold">8</p>
                    </div>
                    <div class="bg-purple-500 rounded-full p-3">
                        <i class="fas fa-language text-white fa-lg"></i>
                    </div>
                </div>
            </div>
           </a>
        </div>

        <!-- Email Sent Graph -->
        <div class="bg-gray-800 rounded-xl p-6 card-hover mb-8">
            <h2 class="text-xl font-semibold mb-4">Emails Sent Over Time</h2>
            <div class="chart-container">
                <canvas id="emailChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('emailChart').getContext('2d');
            const emailChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Emails Sent',
                        data: [120, 190, 300, 500, 250, 400, 600],
                        borderColor: 'rgba(99, 102, 241, 1)', // Purple
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                        fill: true,
                        tension: 0.4, // Smooth curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: 'rgba(255, 255, 255, 0.7)',
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>
