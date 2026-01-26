<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Elo Rating Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-3xl font-bold text-indigo-600">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-6">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    {{ $user->name }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                @if($user->isOrganizer())
                                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Organizer
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ __('Current Elo Rating') }}</p>
                            <div class="flex items-center justify-end">
                                <span class="text-4xl font-bold text-gray-900">{{ $user->current_elo }}</span>
                                @php
                                    $latestChange = $user->getLatestEloChange();
                                @endphp
                                @if($latestChange !== null && $latestChange !== 0)
                                    <span class="ml-2 text-lg font-medium {{ $latestChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $latestChange >= 0 ? '+' : '' }}{{ $latestChange }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['matches_played'] }}</p>
                        <p class="text-sm text-gray-500">{{ __('Matches Played') }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-3xl font-bold text-green-600">{{ $stats['wins'] }}</p>
                        <p class="text-sm text-gray-500">{{ __('Wins') }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-3xl font-bold text-red-600">{{ $stats['losses'] }}</p>
                        <p class="text-sm text-gray-500">{{ __('Losses') }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-3xl font-bold text-indigo-600">{{ $stats['win_rate'] }}%</p>
                        <p class="text-sm text-gray-500">{{ __('Win Rate') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Elo History Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Elo Progression') }}</h3>
                        <div class="h-64">
                            <canvas id="eloChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Matches -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent Matches') }}</h3>
                            <a href="{{ route('matches.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                {{ __('View all') }} &rarr;
                            </a>
                        </div>
                        @if($recentMatches->isEmpty())
                            <div class="text-center py-8">
                                <p class="text-gray-500">{{ __('No matches yet') }}</p>
                                <a href="{{ route('matches.create') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">
                                    {{ __('Submit your first match') }}
                                </a>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($recentMatches as $match)
                                    <a href="{{ route('matches.show', $match) }}" class="block">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-lg font-bold {{ $match->winner_id === $user->id ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $match->winner_id === $user->id ? 'W' : 'L' }}
                                                </span>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        vs {{ $match->getOpponent($user)->name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $match->getPlayerScore($user) }} - {{ $match->player1_id === $user->id ? $match->player2_score : $match->player1_score }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $eloChange = $match->getPlayerEloChange($user);
                                                @endphp
                                                <span class="text-sm font-medium {{ $eloChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $eloChange >= 0 ? '+' : '' }}{{ $eloChange }}
                                                </span>
                                                <p class="text-xs text-gray-500">
                                                    {{ $match->approved_at->format('M d') }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('matches.create') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ __('Submit Match') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Record a result') }}</p>
                            </div>
                        </a>

                        <a href="{{ route('rankings.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ __('Rankings') }}</p>
                                <p class="text-xs text-gray-500">{{ __('View leaderboard') }}</p>
                            </div>
                        </a>

                        <a href="{{ route('sessions.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ __('Sessions') }}</p>
                                <p class="text-xs text-gray-500">{{ __('Join a session') }}</p>
                            </div>
                        </a>

                        @if($user->isOrganizer())
                            <a href="{{ route('matches.pending') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors relative">
                                <div class="p-2 bg-red-100 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Approvals') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Review matches') }}</p>
                                </div>
                                @if($pendingCount > 0)
                                    <span class="absolute top-2 right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>
                        @else
                            <a href="{{ route('profile.edit') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ __('Profile') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Edit settings') }}</p>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('eloChart').getContext('2d');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Elo Rating',
                    data: chartData.data,
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: 'rgb(79, 70, 229)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
