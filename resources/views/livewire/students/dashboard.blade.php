<?php

use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    public $membership;

    public function mount()
    {
        $this->membership = auth()->user()->memberships()->latest()->first();
    }

    public function isActive()
    {
        if (!$this->membership) {
            return false;
        }
        return Carbon::parse($this->membership->end_date)->isFuture();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        Student Dashboard
    </h1>
    @if (session()->has('error'))
        <div
            class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div
            class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    <div class="grid auto-rows-min gap-6 md:grid-cols-3">
        <div
            class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Membership Status</h2>
                    <div class="flex items-center">
                        @if ($this->isActive())
                            <span
                                class="px-3 py-1 text-sm text-green-700 bg-green-100 rounded-full dark:bg-green-800 dark:text-green-100">
                                Active
                            </span>
                        @else
                            <span
                                class="px-3 py-1 text-sm text-red-700 bg-red-100 rounded-full dark:bg-red-800 dark:text-red-100">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                @if ($membership)
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Start Date: {{ Carbon::parse($membership->start_date)->format('M d, Y') }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            End Date: {{ Carbon::parse($membership->end_date)->format('M d, Y') }}
                        </p>
                    </div>
                @endif

                @if (!$this->isActive())
                    <a href="{{ route('student.deposit') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Renew Membership
                    </a>
                @endif
            </div>
        </div>

        <div
            class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
            <div class="flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">My Schedule</h2>
            </div>
        </div>

        <div
            class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
            <div class="flex items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Progress</h2>
            </div>
        </div>
    </div>

    <div
        class="relative flex-1 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Activity Timeline</h2>
    </div>
</div>
