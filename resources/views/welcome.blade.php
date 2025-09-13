<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fitnezz - BOUESTI Gym Management System</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#fefce8',
                            100: '#fef3c7',
                            500: '#eab308',
                            600: '#ca8a04',
                            700: '#a16207',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-yellow-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">F</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">Fitnezz</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 font-medium transition-colors duration-200">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
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
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    Welcome to
                    <span class="bg-gradient-to-r from-blue-600 to-yellow-600 bg-clip-text text-transparent">
                        Fitnezz
                    </span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                    BOUESTI's comprehensive gym management system designed for students, trainers, and administrators
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                            Join as Student
                        </a>
                        <a href="{{ route('login') }}" 
                           class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-200">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Background decoration -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200 dark:bg-blue-800 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-yellow-200 dark:bg-yellow-800 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-blue-300 dark:bg-blue-700 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-4000"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Everything you need for gym management
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Comprehensive features designed specifically for BOUESTI's fitness community
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Student Features -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-8 rounded-xl border border-blue-200 dark:border-blue-700">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">For Students</h3>
                    <ul class="text-gray-600 dark:text-gray-300 space-y-2">
                        <li>• Class booking & scheduling</li>
                        <li>• Membership management</li>
                        <li>• Payment tracking</li>
                        <li>• Progress monitoring</li>
                    </ul>
                </div>

                <!-- Trainer Features -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 p-8 rounded-xl border border-yellow-200 dark:border-yellow-700">
                    <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">For Trainers</h3>
                    <ul class="text-gray-600 dark:text-gray-300 space-y-2">
                        <li>• Class management</li>
                        <li>• Student tracking</li>
                        <li>• Equipment monitoring</li>
                        <li>• Attendance records</li>
                    </ul>
                </div>

                <!-- Admin Features -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-8 rounded-xl border border-green-200 dark:border-green-700">
                    <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">For Admins</h3>
                    <ul class="text-gray-600 dark:text-gray-300 space-y-2">
                        <li>• User management</li>
                        <li>• Payment processing</li>
                        <li>• Reports & analytics</li>
                        <li>• System configuration</li>
                    </ul>
                </div>

                <!-- Equipment Features -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-8 rounded-xl border border-purple-200 dark:border-purple-700">
                    <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Equipment</h3>
                    <ul class="text-gray-600 dark:text-gray-300 space-y-2">
                        <li>• Equipment tracking</li>
                        <li>• Maintenance scheduling</li>
                        <li>• Status monitoring</li>
                        <li>• Usage analytics</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- BOUESTI Integration Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-yellow-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Built for BOUESTI Students
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Seamlessly integrated with BOUESTI's email system. Students can register using their official BOUESTI email addresses for instant access to all gym facilities and services.
            </p>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-8 max-w-2xl mx-auto">
                <h3 class="text-2xl font-semibold text-white mb-4">Email Format Requirements</h3>
                <div class="grid md:grid-cols-2 gap-6 text-left">
                    <div class="bg-white/20 rounded-lg p-4">
                        <h4 class="font-semibold text-white mb-2">Students</h4>
                        <p class="text-blue-100 text-sm">firstname.matricno@bouesti.edu.ng</p>
                        <p class="text-blue-200 text-xs mt-1">Example: john.1234@bouesti.edu.ng</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-4">
                        <h4 class="font-semibold text-white mb-2">Staff</h4>
                        <p class="text-blue-100 text-sm">firstname.lastname@bouesti.edu.ng</p>
                        <p class="text-blue-200 text-xs mt-1">Example: john.doe@bouesti.edu.ng</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">
                Ready to get started?
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                Join the BOUESTI fitness community and start your fitness journey today
            </p>
            @guest
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                        Register Now
                    </a>
                    <a href="{{ route('login') }}" 
                       class="border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all duration-200">
                        Sign In
                    </a>
                </div>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-yellow-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">F</span>
                    </div>
                    <span class="text-2xl font-bold">Fitnezz</span>
                </div>
                <p class="text-gray-400 mb-4">
                    BOUESTI Gym Management System
                </p>
                <p class="text-gray-500 text-sm">
                    © {{ date('Y') }} BOUESTI. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <style>
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>
</html>
