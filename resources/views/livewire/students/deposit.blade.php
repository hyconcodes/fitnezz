<?php

use Livewire\Volt\Component;

new class extends Component {
    public $amount;
    public $months = 1;
    public $reference;
    public $membershipStatus = null;
    public $errorMessage = '';
    public $membership = null;

    public function mount()
    {
        $this->calculateAmount();
        
        // Check existing membership status
        $this->membership = \App\Models\Membership::where('user_id', auth()->id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        if ($this->membership) {
            $this->membershipStatus = 'active';
        }
    }

    public function calculateAmount()
    {
        // Base amount is 20,000 for one month
        // Each additional month adds 10,000
        $this->amount = 20000 + (($this->months - 1) * 10000);
    }

    public function updatedMonths()
    {
        $this->calculateAmount();
    }

    public function initiatePayment()
    {
        try {
            if ($this->membershipStatus === 'active') {
                $this->errorMessage = 'You already have an active membership.';
                return;
            }

            $this->reference = 'PAY_' . time() . '_' . auth()->id();
            
            $client = new \GuzzleHttp\Client();
            $response = $client->post(env('PAYSTACK_PAYMENT_URL') . '/transaction/initialize', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'amount' => $this->amount * 100,
                    'email' => auth()->user()->email,
                    'reference' => $this->reference,
                    'callback_url' => route('paystack.callback'),
                    'metadata' => [
                        'user_id' => auth()->id(),
                        'months' => $this->months,
                    ],
                ],
            ]);

            $result = json_decode($response->getBody());
            
            if ($result->status) {
                return redirect($result->data->authorization_url);
            }

            $this->errorMessage = 'Payment initialization failed. Please try again.';
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Payment system error. Please try again later.';
            \Log::error('Payment Error: ' . $e->getMessage());
        }
    }
}; ?>

<div class="max-w-6xl mx-auto p-6 bg-white dark:bg-zinc-800 rounded-lg shadow-md">
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Membership Status Column -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">
                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4l1.465 1.638a2 2 0 01.393 2.098l-.668 1.534a2 2 0 00.393 2.098L15 13M7 13l1.465-1.638a2 2 0 00.393-2.098l-.668-1.534a2 2 0 01.393-2.098L10 4"/>
                    </svg>Membership Status
                </h2>
                <span class="px-4 py-2 rounded-full {{ $membershipStatus === 'active' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-100' }}">
                    {{ $membershipStatus === 'active' ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($membershipStatus === 'active' && $membership)
            <div class="bg-white dark:bg-zinc-700 rounded-lg p-6 shadow-sm space-y-4">
                <div class="flex items-center space-x-2 text-zinc-700 dark:text-zinc-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Start Date: {{ $membership->start_date->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center space-x-2 text-zinc-700 dark:text-zinc-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                    <span>End Date: {{ $membership->end_date->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center space-x-2 text-zinc-700 dark:text-zinc-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-data="{
                        endDate: '{{ $membership->end_date }}',
                        timeLeft: '',
                        updateTimeLeft() {
                            const end = new Date(this.endDate);
                            const now = new Date();
                            const diff = end - now;
                            
                            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                            
                            this.timeLeft = `${days} ${days === 1 ? 'Day' : 'Days'}, ` +
                                          `${hours} ${hours === 1 ? 'Hour' : 'Hours'}, ` +
                                          `${minutes} ${minutes === 1 ? 'Minute' : 'Minutes'}, ` +
                                          `${seconds} ${seconds === 1 ? 'Second' : 'Seconds'} Remaining`;
                        }
                    }" 
                    x-init="updateTimeLeft(); setInterval(() => updateTimeLeft(), 1000)"
                    x-text="timeLeft"
                    ></span>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Column -->
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">
                <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>Membership Payment
            </h2>

            @if($membershipStatus === 'active')
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>You already have an active membership.
                </div>
            @else
                <div class="space-y-6">
                    <div>
                        <label class="block text-zinc-700 dark:text-zinc-300 text-sm font-bold mb-2">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>Select Membership Duration
                        </label>
                        <select wire:model="months" class="w-full px-3 py-2 border dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ Str::plural('Month', $i) }} - ₦{{ number_format(20000 + (($i - 1) * 10000)) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <p class="text-zinc-700 dark:text-zinc-300 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Total Amount: ₦{{ number_format($amount) }}
                        </p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                            </svg>
                            Membership duration: {{ $months }} {{ Str::plural('month', $months) }}
                        </p>
                    </div>

                    @if($errorMessage)
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>{{ $errorMessage }}
                        </div>
                    @endif

                    <button 
                        wire:click="initiatePayment"
                        class="w-full bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline flex items-center justify-center"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>Make Payment
                        </span>
                        <span wire:loading>
                            <svg class="w-5 h-5 inline-block mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>Processing...
                        </span>
                    </button>

                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-4 text-center flex items-center justify-center">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>Secured by Paystack
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
