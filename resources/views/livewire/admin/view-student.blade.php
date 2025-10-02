<?php

use Livewire\Volt\Component;
use App\Models\FitnessClass;
use App\Models\User;
use App\Models\ClassRegistration;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $student;

    public function mount($student)
    {
        $this->student = User::findOrFail($student);
    }

    // Helper to check if student has valid membership
    public function hasValidMembership()
    {
        return Membership::where('user_id', $this->student->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->exists();
    }

    // Upcoming classes for this student
    public function getUpcomingClassesProperty()
    {
        return FitnessClass::with('trainer')
            ->whereHas('registrations', fn($q) => $q->where('student_id', $this->student->id))
            ->where('schedule_time', '>', now())
            ->orderBy('schedule_time')
            ->limit(3)
            ->get();
    }

    // Registered classes count
    public function getRegisteredCountProperty()
    {
        return ClassRegistration::where('student_id', $this->student->id)->count();
    }

    // Helper to get registration & progress for a class
    public function getRegistrationFor($classId)
    {
        return ClassRegistration::where('class_id', $classId)
            ->where('student_id', $this->student->id)
            ->first();
    }
}; ?>

<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow p-6 mb-6">
            <div class="flex items-center space-x-6">
                <img class="w-20 h-20 rounded-full object-cover"
                     src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&color=7F9CF5&background=EBF4FF' }}"
                     alt="{{ $student->name }}">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $student->name }}</h2>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ $student->email }}</p>
                    <div class="mt-2 flex items-center space-x-4 text-sm">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $student->phone ?? 'N/A' }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Joined {{ $student->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Membership</div>
                    @if($this->hasValidMembership())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            Expired
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Classes -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Upcoming Classes</h3>
                <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $this->registeredCount }} total registered</span>
            </div>

            @if($this->upcomingClasses->count() > 0)
                <div class="space-y-3">
                    @foreach($this->upcomingClasses as $class)
                        @php
                            $reg = $this->getRegistrationFor($class->id);
                            $progress = $reg ? (int) $reg->progress : 0;
                        @endphp
                        <div class="border dark:border-zinc-700 rounded-lg p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $class->title }}</div>
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $class->schedule_time->format('D, M d • g:i A') }} with {{ $class->trainer->name ?? 'N/A' }}
                                    </div>
                                    <!-- Inline Progress Bar -->
                                    <div class="mt-2">
                                        <div class="flex items-center justify-between mb-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            <span>Progress</span>
                                            <span>{{ $progress }}%</span>
                                        </div>
                                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full transition-all duration-300
                                                @if($progress < 10) bg-gradient-to-r from-pink-400 to-pink-600
                                                @elseif($progress < 20) bg-gradient-to-r from-rose-400 to-rose-600
                                                @elseif($progress < 30) bg-gradient-to-r from-orange-400 to-orange-600
                                                @elseif($progress < 40) bg-gradient-to-r from-amber-400 to-amber-600
                                                @elseif($progress < 50) bg-gradient-to-r from-yellow-400 to-yellow-600
                                                @elseif($progress < 60) bg-gradient-to-r from-lime-400 to-lime-600
                                                @elseif($progress < 70) bg-gradient-to-r from-green-400 to-green-600
                                                @elseif($progress < 80) bg-gradient-to-r from-teal-400 to-teal-600
                                                @elseif($progress < 90) bg-gradient-to-r from-cyan-400 to-cyan-600
                                                @else bg-gradient-to-r from-indigo-400 to-indigo-600
                                                @endif"
                                                style="width: {{ $progress }}%;"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    No upcoming classes scheduled
                </div>
            @endif
        </div>
    </div>
</div>
