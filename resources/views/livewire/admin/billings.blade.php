<?php

use Livewire\Volt\Component;
use App\Models\Payment;
use App\Models\Membership;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $payments;
    public $memberships;
    public $editingPayment = null;
    public $confirmingPaymentDeletion = false;
    public $search = '';
    
    public function mount() {
        $this->refreshData();
    }

    public function refreshData() {
        $query = Payment::with('user')
            ->when($this->search, function($q) {
                $q->whereHas('user', function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('reference', 'like', '%' . $this->search . '%');
            });
            
        $this->payments = $query->latest()->paginate(10);
        $this->memberships = Membership::with('user')->latest()->get();
    }

    public function editPayment($paymentId) {
        $this->editingPayment = Payment::find($paymentId);
    }

    public function updatePayment() {
        $this->validate([
            'editingPayment.status' => 'required|in:pending,paid,failed',
            'editingPayment.amount' => 'required|numeric',
        ]);

        $this->editingPayment->save();
        $this->editingPayment = null;
        session()->flash('success', 'Payment updated successfully.');
    }

    public function confirmPaymentDeletion($paymentId) {
        $this->confirmingPaymentDeletion = $paymentId;
    }

    public function deletePayment() {
        $payment = Payment::find($this->confirmingPaymentDeletion);
        $payment->delete();
        
        $this->confirmingPaymentDeletion = false;
        session()->flash('success', 'Payment deleted successfully.');
        $this->refreshData();
    }

    public function updatedSearch() {
        $this->refreshData();
    }
}; ?>

<div class="max-w-7xl mx-auto p-6 bg-white dark:bg-zinc-800 rounded-lg shadow-md">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">Payment Management</h2>
        <div class="mt-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search payments..." 
                   class="w-full px-4 py-2 border rounded-lg dark:bg-zinc-700 dark:border-zinc-600">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Payment Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($payments as $payment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $payment->user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">₦{{ number_format($payment->amount) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $payment->reference }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y H:i') : 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="editPayment({{ $payment->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                        <button wire:click="confirmPaymentDeletion({{ $payment->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>

    @if($editingPayment)
    <div class="fixed inset-0 bg-zinc-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-medium mb-4">Edit Payment</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Status</label>
                    <select wire:model="editingPayment.status" class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Amount</label>
                    <input type="number" wire:model="editingPayment.amount" class="mt-1 block w-full rounded-md border-zinc-300 dark:border-zinc-700">
                </div>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('editingPayment', null)" class="px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700 rounded-md">Cancel</button>
                    <button wire:click="updatePayment" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($confirmingPaymentDeletion)
    <div class="fixed inset-0 bg-zinc-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-medium mb-4">Confirm Deletion</h3>
            <p class="mb-4">Are you sure you want to delete this payment? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button wire:click="$set('confirmingPaymentDeletion', false)" class="px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700 rounded-md">Cancel</button>
                <button wire:click="deletePayment" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">Delete</button>
            </div>
        </div>
    </div>
    @endif
</div>
