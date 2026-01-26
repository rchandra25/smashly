<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rankings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Period Filter -->
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-sm text-gray-500">{{ __('Show players active in:') }}</span>
                            <div class="flex rounded-md shadow-sm">
                                <a href="{{ route('rankings.index') }}"
                                    class="px-4 py-2 text-sm font-medium rounded-l-md border
                                        {{ !$period ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                    {{ __('All Time') }}
                                </a>
                                <a href="{{ route('rankings.index', ['period' => 'month']) }}"
                                    class="px-4 py-2 text-sm font-medium border-t border-b
                                        {{ $period === 'month' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                    {{ __('This Month') }}
                                </a>
                                <a href="{{ route('rankings.index', ['period' => 'week']) }}"
                                    class="px-4 py-2 text-sm font-medium rounded-r-md border
                                        {{ $period === 'week' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                    {{ __('This Week') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rankings Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($rankings->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No players found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($period)
                                    {{ __('No players have played matches in this period.') }}
                                @else
                                    {{ __('No players have registered yet.') }}
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Rank') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Player') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Elo') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('W-L') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Win Rate') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rankings as $player)
                                        <tr class="{{ $player->id === auth()->id() ? 'bg-indigo-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($player->rank === 1)
                                                        <span class="text-2xl">🥇</span>
                                                    @elseif($player->rank === 2)
                                                        <span class="text-2xl">🥈</span>
                                                    @elseif($player->rank === 3)
                                                        <span class="text-2xl">🥉</span>
                                                    @else
                                                        <span class="text-lg font-bold text-gray-500">#{{ $player->rank }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                        <span class="text-sm font-bold text-indigo-600">
                                                            {{ strtoupper(substr($player->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $player->name }}
                                                            @if($player->id === auth()->id())
                                                                <span class="text-xs text-indigo-600">(You)</span>
                                                            @endif
                                                        </div>
                                                        @if($player->isOrganizer())
                                                            <span class="text-xs text-purple-600">Organizer</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-lg font-bold text-gray-900">{{ $player->current_elo }}</div>
                                                @php
                                                    $latestChange = $player->getLatestEloChange();
                                                @endphp
                                                @if($latestChange !== null && $latestChange !== 0)
                                                    <div class="text-xs {{ $latestChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $latestChange >= 0 ? '+' : '' }}{{ $latestChange }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                <span class="text-green-600">{{ $player->getWinCount() }}</span>
                                                <span class="text-gray-400">-</span>
                                                <span class="text-red-600">{{ $player->getLossCount() }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    $winRate = $player->getWinRate();
                                                @endphp
                                                <div class="flex items-center justify-center">
                                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $winRate }}%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-900">{{ $winRate }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Your Ranking Info -->
            @if($rankings->isNotEmpty())
                @php
                    $userRanking = $rankings->firstWhere('id', auth()->id());
                @endphp
                @if($userRanking)
                    <div class="mt-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-800">{{ __('Your Ranking') }}</p>
                                <p class="text-2xl font-bold text-indigo-900">#{{ $userRanking->rank }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-indigo-800">{{ __('Your Elo') }}</p>
                                <p class="text-2xl font-bold text-indigo-900">{{ $userRanking->current_elo }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
