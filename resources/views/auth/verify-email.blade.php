<x-guest-layout>
    <div class="text-center mb-6">
        <!-- Email Icon -->
        <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Verify Your Email') }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ __('We sent a verification link to') }}</p>
        <p class="font-medium text-gray-900">{{ Auth::user()->email }}</p>
    </div>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-600 text-center">
            {{ __('Click the link in your email to verify your account. If you don\'t see it, check your spam folder.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">
                    {{ __('A new verification link has been sent to your email address.') }}
                </p>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center py-3">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">{{ __('or') }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-3 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                {{ __('Sign out and use a different account') }}
            </button>
        </form>
    </div>

    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
            {{ __('Having trouble?') }}
            <a href="#" class="text-indigo-600 hover:text-indigo-500">{{ __('Contact Support') }}</a>
        </p>
    </div>
</x-guest-layout>
