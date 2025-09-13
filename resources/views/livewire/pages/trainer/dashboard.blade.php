<?php

use App\Models\ClassModel;
use App\Models\Equipment;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function with()
    {
        return [
            'myClasses' => ClassModel::where('trainer_id', auth()->id())
                ->where('schedule_time', '>', now())
                ->orderBy('schedule_time')
                ->get(),
            'equipment' => Equipment::where('status', '!=', 'out_of_order')->get(),
            'totalStudents' => ClassModel::where('trainer_id', auth()->id())
                ->withCount('students')
                ->get()
                ->sum('students_count'),
        ];
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-zinc-900 dark:text-zinc-100">
                    <h1 class="text-2xl font-bold mb-6">Trainer Dashboard</h1>
                    
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">My Classes</h3>
                            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $myClasses->count() }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">Total Students</h3>
                            <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $totalStudents }}</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-200">Available Equipment</h3>
                            <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $equipment->count() }}</p>
                        </div>
                    </div>

                    <!-- My Classes -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">My Classes</h2>
                        @if($myClasses->count() > 0)
                            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                @foreach($myClasses as $class)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h3 class="font-semibold">{{ $class->title }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $class->schedule_time->format('M d, Y g:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Students: {{ $class->students->count() }}/{{ $class->capacity }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Status: <span class="font-semibold">{{ ucfirst($class->status) }}</span>
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No classes assigned to you.</p>
                        @endif
                    </div>

                    <!-- Equipment Status -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Equipment Status</h2>
                        @if($equipment->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Maintenance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($equipment as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $item->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($item->status === 'available') bg-green-100 text-green-800
                                                        @elseif($item->status === 'in_use') bg-blue-100 text-blue-800
                                                        @elseif($item->status === 'maintenance') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $item->maintenance_schedule ? $item->maintenance_schedule->format('M d, Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No equipment found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
