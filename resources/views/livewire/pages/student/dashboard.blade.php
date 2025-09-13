<?php

use App\Models\ClassModel;
use App\Models\Membership;
use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function with()
    {
        return [
            'membership' => auth()->user()->membership,
            'upcomingClasses' => ClassModel::where('schedule_time', '>', now())
                ->where('status', 'scheduled')
                ->orderBy('schedule_time')
                ->limit(5)
                ->get(),
            'recentPayments' => auth()->user()->payments()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-zinc-900 dark:text-zinc-100">
                    <h1 class="text-2xl font-bold mb-6">Student Dashboard</h1>
                    
                    <!-- Membership Status -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Membership Status</h2>
                        @if($membership)
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    Status: <span class="font-semibold">{{ ucfirst($membership->status) }}</span>
                                </p>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    Valid until: {{ $membership->end_date->format('M d, Y') }}
                                </p>
                            </div>
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    No active membership. Please contact admin to activate your membership.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Upcoming Classes -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Upcoming Classes</h2>
                        @if($upcomingClasses->count() > 0)
                            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                @foreach($upcomingClasses as $class)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h3 class="font-semibold">{{ $class->title }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $class->schedule_time->format('M d, Y g:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Trainer: {{ $class->trainer->name }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Capacity: {{ $class->students->count() }}/{{ $class->capacity }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No upcoming classes scheduled.</p>
                        @endif
                    </div>

                    <!-- Recent Payments -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Recent Payments</h2>
                        @if($recentPayments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($recentPayments as $payment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    ₦{{ number_format($payment->amount, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($payment->status === 'completed') bg-green-100 text-green-800
                                                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $payment->created_at->format('M d, Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No payment history found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
