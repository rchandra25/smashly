<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $session->title }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($session->status === 'upcoming') bg-blue-100 text-blue-800
                @elseif($session->status === 'in_progress') bg-green-100 text-green-800
                @elseif($session->status === 'completed') bg-gray-100 text-gray-800
                @else bg-red-100 text-red-800
                @endif">
                {{ ucfirst(str_replace('_', ' ', $session->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Session Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Info Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Session Details') }}</h3>

                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Date') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $session->session_date->format('l, M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Time') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                        @if($session->end_time)
                                            - {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}
                                        @endif
                                    </dd>
                                </div>
                                @if($session->location)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Location') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $session->location }}</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Organizer') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $session->organizer->name }}</dd>
                                </div>
                            </dl>

                            @if($session->description)
                                <div class="mt-4 pt-4 border-t">
                                    <p class="text-sm text-gray-600">{{ $session->description }}</p>
                                </div>
                            @endif

                            <!-- Join/Leave Button -->
                            <div class="mt-6 pt-4 border-t">
                                @if($session->isUpcoming())
                                    @if($session->hasPlayer(auth()->user()))
                                        <form action="{{ route('sessions.leave', $session) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                                {{ __('Leave Session') }}
                                            </button>
                                        </form>
                                    @elseif(!$session->isFull())
                                        <form action="{{ route('sessions.join', $session) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                                {{ __('Join Session') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-500">{{ __('Session is full') }}</span>
                                    @endif
                                @endif
                            </div>

                            <!-- Status Controls (Organizer Only) -->
                            @can('updateStatus', $session)
                                <div class="mt-4 pt-4 border-t">
                                    <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Update Status') }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(['upcoming', 'in_progress', 'completed', 'cancelled'] as $status)
                                            @if($status !== $session->status)
                                                <form action="{{ route('sessions.updateStatus', $session) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $status }}">
                                                    <button type="submit" class="px-3 py-1 text-xs font-medium rounded-md
                                                        @if($status === 'in_progress') bg-green-100 text-green-800 hover:bg-green-200
                                                        @elseif($status === 'completed') bg-gray-100 text-gray-800 hover:bg-gray-200
                                                        @elseif($status === 'cancelled') bg-red-100 text-red-800 hover:bg-red-200
                                                        @else bg-blue-100 text-blue-800 hover:bg-blue-200
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>

                    <!-- Suggested Pairings -->
                    @if(!empty($suggestedPairings))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Suggested Pairings') }}</h3>
                                <p class="text-sm text-gray-500 mb-4">{{ __('Based on Elo ratings for balanced matches') }}</p>

                                <div class="space-y-3">
                                    @foreach($suggestedPairings as $pairing)
                                        <div class="border rounded-lg p-4 bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="text-center">
                                                        <p class="font-medium text-gray-900">{{ $pairing['player1']->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $pairing['player1']->current_elo }} Elo</p>
                                                    </div>
                                                    <span class="text-gray-400">vs</span>
                                                    <div class="text-center">
                                                        <p class="font-medium text-gray-900">{{ $pairing['player2']->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $pairing['player2']->current_elo }} Elo</p>
                                                    </div>
                                                </div>
                                                <div class="text-right text-sm">
                                                    <p class="text-gray-500">
                                                        {{ $pairing['player1_win_probability'] }}% - {{ $pairing['player2_win_probability'] }}%
                                                    </p>
                                                    <p class="text-xs text-gray-400">
                                                        {{ $pairing['elo_difference'] }} Elo diff
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('matches.create', ['opponent_id' => $pairing['player2']->id]) }}"
                                                    class="text-xs text-indigo-600 hover:text-indigo-800">
                                                    {{ __('Submit match result') }} &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Session Matches -->
                    @if($session->matches->isNotEmpty())
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Session Matches') }}</h3>

                                <div class="space-y-3">
                                    @foreach($session->matches as $match)
                                        <a href="{{ route('matches.show', $match) }}" class="block">
                                            <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-4">
                                                        <span class="font-medium text-gray-900">{{ $match->player1->name }}</span>
                                                        <span class="text-lg font-bold">{{ $match->player1_score }} - {{ $match->player2_score }}</span>
                                                        <span class="font-medium text-gray-900">{{ $match->player2->name }}</span>
                                                    </div>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                        @if($match->status === 'approved') bg-green-100 text-green-800
                                                        @elseif($match->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ ucfirst($match->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Players Sidebar -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                {{ __('Players') }}
                                <span class="text-sm font-normal text-gray-500">
                                    ({{ $session->players->count() }}{{ $session->max_players ? '/' . $session->max_players : '' }})
                                </span>
                            </h3>

                            @if($session->players->isEmpty())
                                <p class="text-sm text-gray-500">{{ __('No players yet') }}</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($session->players->sortByDesc('current_elo') as $player)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-medium text-indigo-600">
                                                        {{ strtoupper(substr($player->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $player->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $player->current_elo }} Elo</p>
                                                </div>
                                            </div>
                                            @if($player->pivot->status === 'checked_in')
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="mt-6">
                <a href="{{ route('sessions.index') }}" class="text-indigo-600 hover:text-indigo-800">
                    &larr; {{ __('Back to Sessions') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
