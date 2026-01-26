<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Smashly') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            <!-- Navigation -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="/" class="flex items-center">
                                <x-application-logo class="w-10 h-10 text-indigo-600" />
                                <span class="ml-3 text-xl font-bold text-gray-900">{{ config('app.name', 'Smashly') }}</span>
                            </a>
                        </div>

                        <div class="flex items-center space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                                        Log in
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            Get Started
                                        </a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main>
                <div class="relative overflow-hidden">
                    <div class="max-w-7xl mx-auto">
                        <div class="relative z-10 pb-8 sm:pb-16 md:pb-20 lg:pb-28 xl:pb-32">
                            <div class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                                <div class="text-center lg:text-left">
                                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                        <span class="block">Welcome to</span>
                                        <span class="block text-indigo-600">{{ config('app.name', 'Smashly') }}</span>
                                    </h1>
                                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                        Your all-in-one platform for managing your projects and tasks. Get started today and experience the difference.
                                    </p>
                                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start gap-4">
                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition-colors">
                                                Create Account
                                            </a>
                                        @endif
                                        @if (Route::has('login'))
                                            <a href="{{ route('login') }}" class="mt-3 sm:mt-0 w-full flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10 transition-colors">
                                                Sign In
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="py-16 bg-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center">
                            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Features</h2>
                            <p class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                                Everything you need
                            </p>
                            <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                                Powerful tools to help you succeed
                            </p>
                        </div>

                        <div class="mt-16">
                            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                                <!-- Feature 1 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 rounded-lg px-6 pb-8">
                                        <div class="-mt-6">
                                            <div class="inline-flex items-center justify-center p-3 bg-indigo-600 rounded-lg shadow-lg">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Secure Authentication</h3>
                                            <p class="mt-5 text-base text-gray-500">
                                                Email verification ensures your account stays secure. We protect your data with industry-standard encryption.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 2 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 rounded-lg px-6 pb-8">
                                        <div class="-mt-6">
                                            <div class="inline-flex items-center justify-center p-3 bg-indigo-600 rounded-lg shadow-lg">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Lightning Fast</h3>
                                            <p class="mt-5 text-base text-gray-500">
                                                Built with performance in mind. Experience blazing fast load times and responsive interactions.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Feature 3 -->
                                <div class="pt-6">
                                    <div class="flow-root bg-gray-50 rounded-lg px-6 pb-8">
                                        <div class="-mt-6">
                                            <div class="inline-flex items-center justify-center p-3 bg-indigo-600 rounded-lg shadow-lg">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="mt-8 text-lg font-medium text-gray-900 tracking-tight">Always Available</h3>
                                            <p class="mt-5 text-base text-gray-500">
                                                Access your dashboard from anywhere, anytime. We ensure 99.9% uptime for your peace of mind.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="bg-indigo-600">
                    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
                        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                            <span class="block">Ready to get started?</span>
                            <span class="block text-indigo-200">Create your account today.</span>
                        </h2>
                        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0 gap-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-lg text-indigo-600 bg-white hover:bg-indigo-50 transition-colors">
                                    Get started
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-white text-base font-medium rounded-lg text-white hover:bg-indigo-700 transition-colors">
                                    Sign in
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-gray-500 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Smashly') }}. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
