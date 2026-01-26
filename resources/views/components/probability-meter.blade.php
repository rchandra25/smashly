@props(['player1Name', 'player2Name', 'player1Probability', 'player2Probability' => null])

@php
    $p1 = round($player1Probability);
    $p2 = $player2Probability !== null ? round($player2Probability) : 100 - $p1;
@endphp

<div {{ $attributes->merge(['class' => 'p-4 bg-gray-50 rounded-lg']) }}>
    <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Win Probability') }}</p>
    <div class="flex items-center space-x-4">
        <div class="flex-1">
            <div class="flex justify-between text-xs text-gray-600 mb-1">
                <span>{{ $player1Name }}</span>
                <span>{{ $p1 }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                    style="width: {{ $p1 }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-600 mt-1">
                <span>{{ $player2Name }}</span>
                <span>{{ $p2 }}%</span>
            </div>
        </div>
    </div>
</div>
