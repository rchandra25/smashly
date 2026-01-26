@props(['match', 'currentUser' => null])

@php
    $user = $currentUser ?? auth()->user();
    $isInvolved = $match->involvesPlayer($user);
    $isWinner = $match->winner_id === $user->id;
    $opponent = $isInvolved ? $match->getOpponent($user) : null;
    $eloChange = $isInvolved ? $match->getPlayerEloChange($user) : null;
@endphp

<div {{ $attributes->merge(['class' => 'border rounded-lg p-4 hover:bg-gray-50 transition-colors']) }}>
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            @if($isInvolved)
                <span class="text-lg font-bold {{ $isWinner ? 'text-green-600' : 'text-red-600' }}">
                    {{ $isWinner ? 'W' : 'L' }}
                </span>
            @endif

            <div class="text-center">
                <div class="flex items-center space-x-2">
                    <span class="font-medium text-gray-900">{{ $match->player1->name }}</span>
                    <span class="text-xl font-bold text-gray-900">
                        {{ $match->player1_score }} - {{ $match->player2_score }}
                    </span>
                    <span class="font-medium text-gray-900">{{ $match->player2->name }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $match->created_at->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            @if($match->isApproved() && $eloChange !== null)
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
