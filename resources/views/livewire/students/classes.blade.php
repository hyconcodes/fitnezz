<?php

use Livewire\Volt\Component;
use App\Models\FitnessClass;
use App\Models\ClassRegistration;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    // Student-facing: remove create/edit/delete logic
    public $search = '';
    public $statusFilter = 'active'; // Default to active classes only
    public $perPage = 10;
    public $viewModal = false;
    public $viewingClass;
    public $activeTab = 'available'; // available | registered
    public $dateFilter = ''; // Y-m-d or empty for all

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    // Only show active & future classes to students
    public function getClassesProperty()
    {
        if ($this->activeTab === 'registered') {
            return FitnessClass::with('trainer')->whereHas('registrations', fn($q) => $q->where('class_id', '!=', null)->where('student_id', auth()->id()))->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))->when($this->dateFilter, fn($q) => $q->whereDate('schedule_time', $this->dateFilter))->latest('schedule_time')->paginate($this->perPage);
        }

        // available tab
        return FitnessClass::with('trainer')->where('status', 'active')->where('schedule_time', '>', now())->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))->when($this->dateFilter, fn($q) => $q->whereDate('schedule_time', $this->dateFilter))->latest('schedule_time')->paginate($this->perPage);
    }

    // Helper to check if user is registered
    public function isRegistered($classId)
    {
        return ClassRegistration::where('class_id', $classId)
            ->where('student_id', auth()->id())
            ->exists();
    }

    // Helper to check if membership is still valid
    public function hasValidMembership()
    {
        return Membership::where('user_id', auth()->id())
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->exists();
    }

    public function viewClass($id)
    {
        try {
            $this->viewingClass = FitnessClass::with('trainer')->findOrFail($id);
            $this->viewModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Class not found.');
        }
    }

    public function closeView()
    {
        $this->viewModal = false;
        $this->viewingClass = null;
    }

    // Enroll the authenticated student
    public function enroll($classId)
    {
        try {
            // Check membership validity first
            if (!$this->hasValidMembership()) {
                session()->flash('error', 'Your membership has expired. Please renew to enroll in classes.');
                return;
            }

            $class = FitnessClass::where('status', 'active')->where('schedule_time', '>', now())->findOrFail($classId);

            // Check capacity
            $currentRegistrations = ClassRegistration::where('class_id', $classId)->count();
            if ($currentRegistrations >= $class->capacity) {
                session()->flash('error', 'This class is already full.');
                return;
            }

            // Check already registered
            $already = ClassRegistration::where('class_id', $classId)
                ->where('student_id', auth()->id())
                ->exists();
            if ($already) {
                session()->flash('info', 'You are already registered in this class.');
                return;
            }

            DB::transaction(function () use ($class) {
                ClassRegistration::create([
                    'class_id' => $class->id,
                    'student_id' => auth()->id(),
                    'status' => 'booked',
                ]);
            });

            session()->flash('message', 'Successfully registered! See you in class 💪');
        } catch (\Exception $e) {
            session()->flash('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    // Redirect to progress tracking
    // public function trackProgress($classId) {
    //     return redirect()->route('student.progress', ['class' => $classId]);
    // }
}; ?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-3">
                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">Fitness Classes</h1>
            </div>
            <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                @if ($activeTab === 'registered')
                    View and track your enrolled classes
                @else
                    Join any class that fits your schedule and start your fitness journey today!
                @endif
            </p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="flex items-center p-4 mb-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded-lg"
                role="alert">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="flex items-center p-4 mb-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-lg"
                role="alert">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if (session()->has('info'))
            <div class="flex items-center p-4 mb-4 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-200 rounded-lg"
                role="alert">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Tabs -->
        <div class="mb-6 flex justify-center">
            <div class="inline-flex rounded-lg bg-zinc-100 dark:bg-zinc-700 p-1 space-x-1">
                <button wire:click="$set('activeTab', 'available')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition
                        {{ $activeTab === 'available' ? 'bg-white dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400 shadow' : 'text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                    Available Classes
                </button>
                <button wire:click="$set('activeTab', 'registered')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition
                        {{ $activeTab === 'registered' ? 'bg-white dark:bg-zinc-800 text-emerald-600 dark:text-emerald-400 shadow' : 'text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100' }}">
                    Registered Classes
                </button>
            </div>
        </div>

        <!-- Search & Date Filter -->
        <div class="mb-6 flex flex-col md:flex-row gap-4 max-w-4xl mx-auto">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search classes by title..."
                class="w-full" />
            <flux:input type="date" wire:model.live="dateFilter" placeholder="Filter by date"
                class="w-full md:w-auto" />
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($this->classes as $class)
                <div
                    class="bg-white dark:bg-zinc-800 rounded-xl shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 cursor-pointer" wire:click="viewClass({{ $class->id }})">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $class->title }}
                                </h3>
                                {{-- <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $class->description ?? 'No description' }}</p> --}}
                            </div>
                            <span
                                class="inline-flex px-2.5 py-1.5 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Active
                            </span>
                        </div>

                        <div class="mt-4 space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $class->schedule_time->format('D, M d • g:i A') }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ $class->capacity }} spots total
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Trainer: {{ $class->trainer->name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <flux:button wire:click="viewClass({{ $class->id }})" variant="outline" size="xs">
                                View Details
                            </flux:button>
                            @if ($this->isRegistered($class->id))
                                <flux:button href="{{ route('student.progress', $class->id) }}"
                                    size="xs" class="!bg-blue-600 !text-white hover:!bg-blue-700">
                                    Track Progress
                                </flux:button>
                            @else
                                <flux:button wire:click="enroll({{ $class->id }})" size="xs"
                                    class="!bg-emerald-600 !text-white hover:!bg-emerald-700">
                                    Enroll
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-zinc-500 dark:text-zinc-400">
                        @if ($activeTab === 'registered')
                            You haven't registered for any classes yet.
                        @else
                            No upcoming active classes found. Check back later!
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $this->classes->links() }}
        </div>

        <!-- View Class Modal -->
        @if ($viewModal && $viewingClass)
            <div class="fixed inset-0 bg-zinc-500/75 dark:bg-zinc-900/90 flex items-center justify-center p-4 z-50">
                <div
                    class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $viewingClass->title }}
                        </h3>
                        <button wire:click="closeView"
                            class="text-zinc-400 hover:text-zinc-500 dark:text-zinc-500 dark:hover:text-zinc-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</label>
                            <p class="mt-1 text-zinc-900 dark:text-zinc-100">
                                {{ $viewingClass->description ?? 'No description provided' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Schedule</label>
                                <p class="mt-1 text-zinc-900 dark:text-zinc-100">
                                    {{ $viewingClass->schedule_time->format('l, M d, Y • g:i A') }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Capacity</label>
                                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $viewingClass->capacity }}
                                    participants</p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Trainer</label>
                                <p class="mt-1 text-zinc-900 dark:text-zinc-100">
                                    {{ $viewingClass->trainer->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                                <span
                                    class="inline-flex px-2.5 py-1.5 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Active
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <flux:button wire:click="closeView" variant="outline">
                                Close
                            </flux:button>
                            @if ($this->isRegistered($viewingClass->id))
                                <flux:button wire:click="trackProgress({{ $viewingClass->id }})"
                                    class="!bg-blue-600 !text-white hover:!bg-blue-700">
                                    Track Progress
                                </flux:button>
                            @else
                                <flux:button wire:click="enroll({{ $viewingClass->id }})"
                                    class="!bg-emerald-600 !text-white hover:!bg-emerald-700">
                                    Enroll Now
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
