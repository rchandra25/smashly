<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Match Approvals') }}
        </h2>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No pending matches') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('All matches have been reviewed.') }}</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($matches as $match)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-6">
                                            <!-- Players & Score -->
                                            <div class="text-center">
                                                <div class="flex items-center space-x-4">
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $match->player1->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $match->player1->current_elo }} Elo</p>
                                                    </div>
                                                    <div class="text-xl font-bold text-gray-900">
                                                        {{ $match->player1_score }} - {{ $match->player2_score }}
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $match->player2->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $match->player2->current_elo }} Elo</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Meta -->
                                            <div class="text-sm text-gray-500">
                                                <p>{{ __('Submitted by') }} {{ $match->submittedBy->name }}</p>
                                                <p>{{ $match->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('matches.show', $match) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                {{ __('View') }}
                                            </a>

                                            <form action="{{ route('matches.approve', $match) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-green-700">
                                                    {{ __('Approve') }}
                                                </button>
                                            </form>

                                            <button type="button" onclick="toggleRejectForm({{ $match->id }})" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-red-700">
                                                {{ __('Reject') }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Reject Form (hidden by default) -->
                                    <div id="reject-form-{{ $match->id }}" class="hidden mt-4 pt-4 border-t">
                                        <form action="{{ route('matches.reject', $match) }}" method="POST" class="flex items-end space-x-4">
                                            @csrf
                                            <div class="flex-1">
                                                <label for="rejection_reason_{{ $match->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                    {{ __('Rejection Reason (optional)') }}
                                                </label>
                                                <input type="text" name="rejection_reason" id="rejection_reason_{{ $match->id }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    placeholder="{{ __('Enter reason for rejection...') }}">
                                            </div>
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                                                {{ __('Confirm Reject') }}
                                            </button>
                                            <button type="button" onclick="toggleRejectForm({{ $match->id }})" class="text-gray-500 hover:text-gray-700 text-sm">
                                                {{ __('Cancel') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
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

    <script>
        function toggleRejectForm(matchId) {
            const form = document.getElementById('reject-form-' + matchId);
            form.classList.toggle('hidden');
        }
    </script>
</x-app-layout>
