<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Match Result') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('matches.store') }}" id="matchForm">
                        @csrf

                        <!-- Opponent Selection -->
                        <div class="mb-6">
                            <label for="opponent_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Opponent') }}
                            </label>
                            <select name="opponent_id" id="opponent_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Select opponent...') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        data-elo="{{ $user->current_elo }}"
                                        {{ ($selectedOpponent && $selectedOpponent->id === $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->current_elo }} Elo)
                                    </option>
                                @endforeach
                            </select>
                            @error('opponent_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Win Probability Display -->
                        <div id="probabilityDisplay" class="mb-6 p-4 bg-gray-50 rounded-lg {{ !$selectedOpponent ? 'hidden' : '' }}">
                            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Expected Win Probability') }}</p>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>{{ __('You') }}</span>
                                        <span id="yourProbability">{{ $expectedProbability ? round($expectedProbability * 100) : 50 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div id="probabilityBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                            style="width: {{ $expectedProbability ? round($expectedProbability * 100) : 50 }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-600 mt-1">
                                        <span id="opponentName">{{ $selectedOpponent ? $selectedOpponent->name : 'Opponent' }}</span>
                                        <span id="opponentProbability">{{ $expectedProbability ? round((1 - $expectedProbability) * 100) : 50 }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Score Input -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="player1_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Your Score') }}
                                </label>
                                <input type="number" name="player1_score" id="player1_score"
                                    min="0" max="30" required
                                    value="{{ old('player1_score') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-2xl py-4">
                                @error('player1_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="player2_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Opponent Score') }}
                                </label>
                                <input type="number" name="player2_score" id="player2_score"
                                    min="0" max="30" required
                                    value="{{ old('player2_score') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-2xl py-4">
                                @error('player2_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Score validation message -->
                        <div id="scoreValidation" class="mb-6 hidden">
                            <p class="text-sm text-red-600"></p>
                        </div>

                        <!-- Badminton scoring rules hint -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>{{ __('Badminton scoring:') }}</strong>
                                {{ __('Games to 21 points, win by 2. At 20-20, play continues until one player leads by 2 (max 30-29).') }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('matches.index') }}" class="text-gray-600 hover:text-gray-800">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Submit Match') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const opponentSelect = document.getElementById('opponent_id');
            const probabilityDisplay = document.getElementById('probabilityDisplay');
            const yourProbability = document.getElementById('yourProbability');
            const opponentProbability = document.getElementById('opponentProbability');
            const opponentName = document.getElementById('opponentName');
            const probabilityBar = document.getElementById('probabilityBar');
            const yourElo = {{ auth()->user()->current_elo }};

            opponentSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const opponentElo = parseInt(selectedOption.dataset.elo);
                    const expected = 1 / (1 + Math.pow(10, (opponentElo - yourElo) / 400));
                    const yourPercent = Math.round(expected * 100);
                    const opponentPercent = 100 - yourPercent;

                    yourProbability.textContent = yourPercent + '%';
                    opponentProbability.textContent = opponentPercent + '%';
                    opponentName.textContent = selectedOption.text.split(' (')[0];
                    probabilityBar.style.width = yourPercent + '%';
                    probabilityDisplay.classList.remove('hidden');
                } else {
                    probabilityDisplay.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
