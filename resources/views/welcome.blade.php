<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Fitnezz - Your ultimate fitness companion for students and trainers">

    <title>{{ config('app.name', 'Fitnezz') }} - Fitness Management System</title>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Heroicons -->
    <script src="https://unpkg.com/@heroicons/v2/outline"></script>

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>

<body class="bg-white text-gray-800 min-h-screen font-sans">
    <!-- Navigation -->
    <header class="fixed w-full bg-white/90 backdrop-blur-sm shadow-sm z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <svg class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span
                        class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-400 bg-clip-text text-transparent">Fitnezz</span>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features"
                        class="text-gray-700 hover:text-primary-600 transition-colors duration-200">Features</a>
                    <a href="#how-it-works"
                        class="text-gray-700 hover:text-primary-600 transition-colors duration-200">How It Works</a>
                    <a href="#testimonials"
                        class="text-gray-700 hover:text-primary-600 transition-colors duration-200">Testimonials</a>
                    <a href="#pricing"
                        class="text-gray-700 hover:text-primary-600 transition-colors duration-200">Pricing</a>

                    @auth
                        <a href="{{ url(auth()->user()->hasRole('student') ? '/student/dashboard' : (auth()->user()->hasRole('trainer') ? '/trainer/dashboard' : '/admin/dashboard')) }}"
                            class="px-6 py-2 rounded-full bg-primary-600 text-white font-medium hover:bg-primary-700 transition-colors duration-200">
                            Go to Dashboard
                        </a>
                    @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}"
                                class="text-gray-700 hover:text-primary-600 font-medium transition-colors duration-200">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-6 py-2 rounded-full bg-primary-600 text-white font-medium hover:bg-primary-700 transition-colors duration-200">
                                Get Started
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-700 hover:text-primary-600" id="mobile-menu-button">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="md:hidden hidden bg-white border-t border-gray-100" id="mobile-menu">
                <div class="px-4 py-3 space-y-3">
                    <a href="#features" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Features</a>
                    <a href="#how-it-works" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">How It
                        Works</a>
                    <a href="#testimonials"
                        class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Testimonials</a>
                    <a href="#pricing" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-md">Pricing</a>
                    @guest
                        <div class="pt-2 space-y-2">
                            <a href="{{ route('login') }}"
                                class="block w-full px-4 py-2 text-center text-gray-700 hover:bg-gray-50 rounded-md border border-gray-200">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}"
                                class="block w-full px-4 py-2 text-center text-white bg-primary-600 hover:bg-primary-700 rounded-md">
                                Get Started
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <main class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6" data-aos="fade-up">
                Transform Your Fitness Journey
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-10" data-aos="fade-up" data-aos-delay="100">
                Join thousands of students and trainers in achieving their fitness goals with our comprehensive fitness
                management system.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                @guest
                    <a href="{{ route('register') }}"
                        class="px-8 py-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors duration-200 text-lg">
                        Get Started for Free
                    </a>
                    <a href="#features" class="px-8 py-4 border-2 border-primary-600 text-primary-600 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200 text-lg">
                        Learn More
                    </a>
                @else
                    <a href="{{ url(auth()->user()->hasRole('student') ? '/student/dashboard' : (auth()->user()->hasRole('trainer') ? '/trainer/dashboard' : '/admin/dashboard')) }}"
                       class="px-8 py-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors duration-200 text-lg">
                        Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>

        <div class="mt-16 grid md:grid-cols-2 gap-8 items-center">
            <div class="space-y-6">
                <h2 class="text-3xl font-bold text-gray-900">Welcome to Fitnezz</h2>
                <p class="text-lg text-gray-600">Your comprehensive fitness management solution for students and trainers.</p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <svg class="h-6 w-6 text-green-500 mt-1 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Personalized workout plans</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-6 w-6 text-green-500 mt-1 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Progress tracking and analytics</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-6 w-6 text-green-500 mt-1 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Expert trainer guidance</span>
                    </li>
                </ul>
            </div>
            <div class="mt-8 md:mt-0">
                <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1740&q=80" 
                     alt="Fitness Training" 
                     class="rounded-lg shadow-xl w-full h-auto">
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Amazing Features</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Everything you need to manage your fitness journey in one place</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Personalized Workouts</h3>
                    <p class="text-gray-600">Get customized workout plans tailored to your fitness level and goals.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Progress Tracking</h3>
                    <p class="text-gray-600">Monitor your progress with detailed analytics and performance metrics.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Community Support</h3>
                    <p class="text-gray-600">Connect with trainers and fellow students for motivation and support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Start Your Fitness Journey?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto opacity-90">
                Join thousands of students and trainers who are already achieving their fitness goals with Fitnezz.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        Get Started for Free
                    </a>
                    <a href="#features" class="px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-colors duration-200">
                        Learn More
                    </a>
                @else
                    <a href="{{ url(auth()->user()->hasRole('student') ? '/student/dashboard' : (auth()->user()->hasRole('trainer') ? '/trainer/dashboard' : '/admin/dashboard')) }}" 
                       class="px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="text-2xl font-bold text-white">Fitnezz</span>
                    </div>
                    <p class="text-gray-400 text-sm">Your ultimate fitness companion for students and trainers to achieve their fitness goals together.</p>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Careers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Community</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Webinars</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Cookie Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">GDPR</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm"> 2023 Fitnezz. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.415-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                    </svg>
                </a>
            </div>
        </div>
    </footer>

    <!-- Mobile menu script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                }
            });
            
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true,
                    easing: 'ease-out',
                });
            }
        });
    </script>
</body>
</html>
