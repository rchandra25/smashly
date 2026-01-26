<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Match Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Match Result Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="flex justify-between items-start mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($match->status === 'approved') bg-green-100 text-green-800
                            @elseif($match->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($match->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($match->status) }}
                        </span>
                        <span class="text-sm text-gray-500">
                            {{ $match->created_at->format('M d, Y \a\t g:i A') }}
                        </span>
                    </div>

                    <!-- Score Display -->
                    <div class="flex items-center justify-center space-x-8 mb-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-{{ $match->winner_id === $match->player1_id ? 'green' : 'gray' }}-100 rounded-full flex items-center justify-center mb-2 mx-auto">
                                <span class="text-xl font-bold text-{{ $match->winner_id === $match->player1_id ? 'green' : 'gray' }}-600">
                                    {{ strtoupper(substr($match->player1->name, 0, 1)) }}
                                </span>
                            </div>
                            <p class="font-medium text-gray-900">{{ $match->player1->name }}</p>
                            <p class="text-sm text-gray-500">{{ $match->player1_elo_before ?? $match->player1->current_elo }} Elo</p>
                            @if($match->isApproved() && $match->player1_elo_change)
                                <span class="text-sm font-medium {{ $match->player1_elo_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $match->player1_elo_change >= 0 ? '+' : '' }}{{ $match->player1_elo_change }}
                                </span>
                            @endif
                        </div>

                        <div class="text-center">
                            <div class="text-4xl font-bold text-gray-900">
                                {{ $match->player1_score }} - {{ $match->player2_score }}
                            </div>
                            @if($match->winner_id === $match->player1_id)
                                <p class="text-sm text-green-600 mt-1">{{ $match->player1->name }} won</p>
                            @else
                                <p class="text-sm text-green-600 mt-1">{{ $match->player2->name }} won</p>
                            @endif
                        </div>

                        <div class="text-center">
                            <div class="w-16 h-16 bg-{{ $match->winner_id === $match->player2_id ? 'green' : 'gray' }}-100 rounded-full flex items-center justify-center mb-2 mx-auto">
                                <span class="text-xl font-bold text-{{ $match->winner_id === $match->player2_id ? 'green' : 'gray' }}-600">
                                    {{ strtoupper(substr($match->player2->name, 0, 1)) }}
                                </span>
                            </div>
                            <p class="font-medium text-gray-900">{{ $match->player2->name }}</p>
                            <p class="text-sm text-gray-500">{{ $match->player2_elo_before ?? $match->player2->current_elo }} Elo</p>
                            @if($match->isApproved() && $match->player2_elo_change)
                                <span class="text-sm font-medium {{ $match->player2_elo_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $match->player2_elo_change >= 0 ? '+' : '' }}{{ $match->player2_elo_change }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($match->gameSession)
                        <div class="text-center text-sm text-gray-500 mb-4">
                            <a href="{{ route('sessions.show', $match->gameSession) }}" class="hover:text-indigo-600">
                                {{ $match->gameSession->title }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Elo Explanation -->
            @if($explanation)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Rating Change Explanation') }}</h3>
                        <div class="prose prose-sm text-gray-600">
                            {{ $explanation }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Match Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Match Information') }}</h3>

                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Submitted By') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $match->submittedBy->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Submitted At') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $match->created_at->format('M d, Y g:i A') }}</dd>
                        </div>

                        @if($match->isApproved())
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Approved By') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $match->approvedBy->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Approved At') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $match->approved_at->format('M d, Y g:i A') }}</dd>
                            </div>

                            @if($match->expected_probability)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Pre-Match Win Probability') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $match->player1->name }}: {{ round($match->expected_probability * 100) }}% |
                                        {{ $match->player2->name }}: {{ round((1 - $match->expected_probability) * 100) }}%
                                    </dd>
                                </div>
                            @endif
                        @endif

                        @if($match->isRejected() && $match->rejection_reason)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Rejection Reason') }}</dt>
                                <dd class="mt-1 text-sm text-red-600">{{ $match->rejection_reason }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('matches.index') }}" class="text-indigo-600 hover:text-indigo-800">
                    &larr; {{ __('Back to Matches') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
