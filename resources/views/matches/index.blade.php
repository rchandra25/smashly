<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Matches') }}
            </h2>
            <a href="{{ route('matches.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                {{ __('Submit Match') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($matches->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No matches yet') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Get started by submitting your first match result.') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('matches.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    {{ __('Submit Match') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($matches as $match)
                                <a href="{{ route('matches.show', $match) }}" class="block">
                                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="text-center">
                                                    <span class="text-lg font-bold {{ $match->winner_id === auth()->id() ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $match->player1_id === auth()->id() ? $match->player1_score : $match->player2_score }}
                                                    </span>
                                                    <span class="text-gray-400 mx-1">-</span>
                                                    <span class="text-lg font-bold text-gray-600">
                                                        {{ $match->player1_id === auth()->id() ? $match->player2_score : $match->player1_score }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">
                                                        vs {{ $match->getOpponent(auth()->user())->name }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $match->created_at->format('M d, Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                @if($match->isApproved())
                                                    @php
                                                        $eloChange = $match->getPlayerEloChange(auth()->user());
                                                    @endphp
                                                    <span class="text-sm font-medium {{ $eloChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $eloChange >= 0 ? '+' : '' }}{{ $eloChange }}
                                                    </span>
                                                @endif
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($match->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($match->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($match->status === 'rejected') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($match->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $matches->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
