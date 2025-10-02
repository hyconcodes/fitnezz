<?php

use Livewire\Volt\Component;
use App\Models\ClassRegistration;
use App\Models\FitnessClass;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

new class extends Component {
    use WithPagination;

    public $classId;
    
    #[Validate('nullable|string|max:100')]
    public $search = '';
    
    public $perPage = 10;

    // Modal state
    public $showModal = false;
    public $modalRegistrationId;
    
    #[Validate('required|integer|min:0|max:100')]
    public $modalProgress = 0;
    
    #[Validate('nullable|string|max:500')]
    public $modalComment = '';
    
    #[Validate('nullable|string|max:1000')]
    public $modalWorkoutDiet = '';

    public function mount($classID)
    {
        $this->classId = $classID;
        
        // Verify class exists
        FitnessClass::findOrFail($classID);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function getParticipantsProperty()
    {
        $query = ClassRegistration::with(['student:id,name,email'])
            ->where('class_id', $this->classId);

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->whereHas('student', function($sq) use ($searchTerm) {
                $sq->where('name', 'like', $searchTerm);
            });
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function openModal($registrationId)
    {
        $this->resetValidation();
        
        $registration = ClassRegistration::where('id', $registrationId)
            ->where('class_id', $this->classId)
            ->firstOrFail();

        $this->modalRegistrationId = $registration->id;
        $this->modalProgress = $registration->progress ?? 0;
        $this->modalComment = $registration->comment ?? '';
        $this->modalWorkoutDiet = $registration->workoutdiet ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset([
            'showModal', 
            'modalRegistrationId', 
            'modalProgress', 
            'modalComment', 
            'modalWorkoutDiet'
        ]);
        $this->resetValidation();
    }

    public function saveParticipantDetails()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                ClassRegistration::where('id', $this->modalRegistrationId)
                    ->where('class_id', $this->classId)
                    ->update([
                        'progress' => $this->modalProgress,
                        'comment' => $this->modalComment,
                        'workoutdiet' => $this->modalWorkoutDiet,
                        'updated_at' => now()
                    ]);
            });

            session()->flash('message', 'Participant details updated successfully! 🎯');
            $this->closeModal();
            
        } catch (\Exception $e) {
            \Log::error('Failed to update participant details: ' . $e->getMessage());
            session()->flash('error', 'Failed to update details. Please try again.');
        }
    }
}; ?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Class Participants</h2>
            </div>
            <flux:button 
                href="{{ route('trainer.classes') }}" 
                variant="outline" 
                class="!text-emerald-600 dark:!text-emerald-400 hover:!text-emerald-800 dark:hover:!text-emerald-300"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Classes
            </flux:button>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
        <div class="flex items-center p-4 mb-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg border border-green-200 dark:border-green-800" role="alert">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="flex items-center p-4 mb-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg border border-red-200 dark:border-red-800" role="alert">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Search & Filters -->
        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by student name..."
                    class="w-full"
                >
                    <x-slot name="iconTrailing">
                        <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </x-slot>
                </flux:input>
            </div>
            <div>
                <flux:select wire:model.live="perPage" class="w-full">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </flux:select>
            </div>
        </div>

        <!-- Participants Table -->
        <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Student
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Progress
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($this->participants as $participant)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                                            <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                {{ strtoupper(substr($participant->student->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $participant->student->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $participant->student->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                                        <div 
                                            class="bg-emerald-600 dark:bg-emerald-500 h-2 rounded-full transition-all duration-300" 
                                            style="width: {{ $participant->progress ?? 0 }}%"
                                        ></div>
                                    </div>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100 min-w-[3rem]">
                                        {{ $participant->progress ?? 0 }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <flux:button 
                                    variant="outline" 
                                    size="sm" 
                                    wire:click="openModal({{ $participant->id }})"
                                    class="!text-emerald-600 dark:!text-emerald-400 hover:!text-emerald-800 dark:hover:!text-emerald-300"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Update
                                </flux:button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-zinc-400 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                                        @if($this->search)
                                            No participants found matching "{{ $this->search }}"
                                        @else
                                            No participants enrolled in this class yet.
                                        @endif
                                    </p>
                                    @if($this->search)
                                    <button 
                                        wire:click="$set('search', '')" 
                                        class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm"
                                    >
                                        Clear search
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($this->participants->hasPages())
        <div class="mt-4">
            {{ $this->participants->links() }}
        </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-zinc-500/75 dark:bg-zinc-900/90 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                    Update Participant Details
                </h3>
                <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-500 dark:text-zinc-500 dark:hover:text-zinc-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="saveParticipantDetails" class="space-y-6">
                <!-- Progress -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Progress
                    </label>
                    <div class="flex items-center space-x-4">
                        <input 
                            type="range" 
                            min="0" 
                            max="100" 
                            step="1"
                            wire:model.live="modalProgress"
                            class="flex-1 h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer dark:bg-zinc-700 accent-emerald-600"
                        >
                        <div class="flex items-center justify-center min-w-[4rem] px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 rounded-md">
                            <span class="text-base font-semibold text-emerald-700 dark:text-emerald-400">
                                {{ $modalProgress }}%
                            </span>
                        </div>
                    </div>
                    @error('modalProgress') 
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Comment
                        <span class="text-zinc-400 text-xs font-normal">(Optional)</span>
                    </label>
                    <flux:textarea
                        wire:model="modalComment"
                        rows="3"
                        maxlength="500"
                        class="mt-1 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100"
                        placeholder="Add feedback or notes about the student's performance..."
                    ></flux:textarea>
                    <div class="flex justify-between mt-1">
                        @error('modalComment') 
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                        @else
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ strlen($modalComment ?? '') }}/500 characters
                        </p>
                        @enderror
                    </div>
                </div>

                <!-- Workout & Diet -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Workout & Diet Plan
                        <span class="text-zinc-400 text-xs font-normal">(Optional)</span>
                    </label>
                    <flux:textarea
                        wire:model="modalWorkoutDiet"
                        rows="5"
                        maxlength="1000"
                        class="mt-1 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100 font-mono text-sm"
                        placeholder="Example:&#10;Monday: Upper body strength (3 sets x 12 reps)&#10;Diet: High protein, 2500 cal/day&#10;..."
                    ></flux:textarea>
                    <div class="flex justify-between mt-1">
                        @error('modalWorkoutDiet') 
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                        @else
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ strlen($modalWorkoutDiet ?? '') }}/1000 characters
                        </p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button 
                        type="button"
                        wire:click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600"
                    >
                        Cancel
                    </flux:button>
                    <flux:button 
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 dark:bg-emerald-500 rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600"
                    >
                        <span wire:loading.remove wire:target="saveParticipantDetails">
                            Save Changes
                        </span>
                        <span wire:loading wire:target="saveParticipantDetails" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>