<?php

use Livewire\Volt\Component;
use App\Models\FitnessClass;
use App\Models\ClassRegistration;
use App\Models\Membership;
use Carbon\Carbon;

new class extends Component {
    public $search = '';
    public $activeTab = 'upcoming'; // upcoming | ongoing

    public function getMembershipProperty()
    {
        return auth()->user()->memberships()->latest()->first();
    }

    public function isMembershipActive()
    {
        $membership = $this->membership;
        return $membership && Carbon::parse($membership->end_date)->isFuture();
    }

    // Upcoming classes: created by this trainer and scheduled in the future
    public function getUpcomingClassesProperty()
    {
        return FitnessClass::with('registrations.student')
            ->where('trainer_id', auth()->id())
            ->where('schedule_time', '>', now())
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('schedule_time')
            ->limit(4)
            ->get();
    }

    // Ongoing classes: created by this trainer and happening now (within 1 hour window)
    public function getOngoingClassesProperty()
    {
        return FitnessClass::with('registrations.student')
            ->where('trainer_id', auth()->id())
            ->whereBetween('schedule_time', [now()->subHour(), now()->addHour()])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('schedule_time')
            ->limit(4)
            ->get();
    }

    // Dynamic property to return the correct list based on activeTab
    public function getClassesProperty()
    {
        return $this->activeTab === 'upcoming'
            ? $this->upcomingClasses
            : $this->ongoingClasses;
    }

    public function getStatsProperty()
    {
        $trainerId = auth()->id();
        return [
            'totalClasses' => FitnessClass::where('trainer_id', $trainerId)->count(),
            'completedClasses' => FitnessClass::where('trainer_id', $trainerId)
                ->where('schedule_time', '<', now())
                ->count(),
            'upcomingClasses' => FitnessClass::where('trainer_id', $trainerId)
                ->where('schedule_time', '>', now())
                ->count(),
            'totalStudents' => ClassRegistration::whereHas('fitnessClass', fn($q) => $q->where('trainer_id', $trainerId))
                ->distinct('student_id')
                ->count('student_id'),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Trainer Dashboard
        </h1>
        <a href="{{ route('trainer.classes') }}"
           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
            Create Class
        </a>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Classes -->
        <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Classes</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $this->stats['totalClasses'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Classes -->
        <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $this->stats['completedClasses'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Upcoming Classes -->
        <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Upcoming</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $this->stats['upcomingClasses'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Students -->
        <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Students</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $this->stats['totalStudents'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- My Classes Section -->
    <div class="flex-1 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4 md:mb-0">My Classes</h2>
            <div class="flex items-center gap-4">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search classes..."
                       class="px-3 py-2 rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <div class="inline-flex rounded-lg bg-neutral-100 dark:bg-neutral-700 p-1 space-x-1">
                    <button wire:click="$set('activeTab', 'upcoming')"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition
                                {{ $activeTab === 'upcoming' ? 'bg-white dark:bg-neutral-800 text-emerald-600 dark:text-emerald-400 shadow' : 'text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-neutral-100' }}">
                        Upcoming
                    </button>
                    <button wire:click="$set('activeTab', 'ongoing')"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition
                                {{ $activeTab === 'ongoing' ? 'bg-white dark:bg-neutral-800 text-emerald-600 dark:text-emerald-400 shadow' : 'text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-neutral-100' }}">
                        Ongoing
                    </button>
                </div>
            </div>
        </div>

        <!-- Classes Horizontal List -->
        <div class="space-y-3">
            @forelse ($this->classes as $class)
                <div class="p-4 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900 hover:shadow transition-shadow flex items-center justify-between gap-4">
                    <!-- Left: Title & Description -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-gray-800 dark:text-white truncate">{{ $class->title }}</h3>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-800 dark:text-emerald-100">
                                {{ $activeTab === 'upcoming' ? 'Upcoming' : 'Ongoing' }}
                            </span>
                        </div>
                        {{-- <p class="text-sm text-gray-600 dark:text-gray-300 truncate">
                            {{ $class->description ?? 'No description' }}
                        </p> --}}
                    </div>

                    <!-- Middle: Schedule & Registrations -->
                    <div class="hidden md:flex md:flex-col md:items-start md:gap-1 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ Carbon::parse($class->schedule_time)->format('D, M d • g:i A') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Registrations: {{ $class->registrations->count() }}
                        </div>
                    </div>

                    <!-- Right: Action Button -->
                    <div>
                        <a href="{{ route('trainer.classes.participants', $class->id) }}"
                           class="px-3 py-1.5 text-xs rounded-md bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                            Check Participants
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-sm">
                        @if ($activeTab === 'upcoming')
                            No upcoming classes found. Create a class to see it here!
                        @else
                            No ongoing classes right now.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        {{-- <div class="mt-4">
            {{ $this->classes->links() }}
        </div> --}}
    </div>
</div>
