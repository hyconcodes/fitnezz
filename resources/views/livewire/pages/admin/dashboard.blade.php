<?php

use App\Models\ClassModel;
use App\Models\Equipment;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function with()
    {
        return [
            'totalStudents' => User::role('student')->count(),
            'totalTrainers' => User::role('trainer')->count(),
            'totalClasses' => ClassModel::count(),
            'totalEquipment' => Equipment::count(),
            'activeMemberships' => Membership::where('status', 'active')->count(),
            'pendingPayments' => Payment::where('status', 'pending')->count(),
            'recentStudents' => User::role('student')->latest()->limit(5)->get(),
            'recentPayments' => Payment::with('user')->latest()->limit(5)->get(),
        ];
    }
}; ?>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-zinc-900 dark:text-zinc-100">
                    <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>
                    
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Total Students</h3>
                            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ $totalStudents }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">Active Memberships</h3>
                            <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ $activeMemberships }}</p>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Total Classes</h3>
                            <p class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">{{ $totalClasses }}</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-200">Equipment</h3>
                            <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ $totalEquipment }}</p>
                        </div>
                    </div>

                    <!-- Recent Students -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Recent Students</h2>
                        @if($recentStudents->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matric No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($recentStudents as $student)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $student->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $student->email }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $student->matric_no ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $student->created_at->format('M d, Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No students found.</p>
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
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($recentPayments as $payment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $payment->user->name }}
                                                </td>
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
