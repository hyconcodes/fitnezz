<?php

use Livewire\Volt\Component;
use App\Models\FitnessClass;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $title;
    public $description;
    public $schedule_time;
    public $capacity;
    public $status = 'active';
    public $editMode = false;
    public $classId;
    public $showModal = false;
    public $viewModal = false;
    public $viewingClass;

    // Search & Filter
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $rules = [
        'title' => 'required|min:3|max:100',
        'description' => 'nullable|string|max:500',
        'schedule_time' => 'required|date|after:now',
        'capacity' => 'required|integer|min:1|max:100',
        'status' => 'required|in:active,cancelled,completed'
    ];

    protected $messages = [
        'title.required' => 'Please enter the class title! 😊',
        'title.min' => 'Title must be at least 3 characters long! 📏',
        'title.max' => 'Title cannot exceed 100 characters! 📏',
        'schedule_time.required' => 'Please select a schedule time! 📅',
        'schedule_time.date' => 'Please enter a valid date! 📅',
        'schedule_time.after' => 'Schedule time must be in the future! ⏰',
        'capacity.required' => 'Please enter the class capacity! 👥',
        'capacity.integer' => 'Capacity must be a number! 🔢',
        'capacity.min' => 'Capacity must be at least 1! 👤',
        'capacity.max' => 'Capacity cannot exceed 100! 👥',
        'status.required' => 'Please select a status! 🔄',
        'status.in' => 'Invalid status selected! ❌'
    ];

    public function mount() {
        $this->resetPage(); // Ensure we start on page 1
    }

    public function updatingSearch() {
        $this->resetPage();
    }

    public function updatingStatusFilter() {
        $this->resetPage();
    }

    public function getClassesProperty() {
        return FitnessClass::where('trainer_id', auth()->id())
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);
    }

    public function create() {
        $this->status = 'active';
        $this->validate();

        try {
            DB::transaction(function () {
                FitnessClass::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'trainer_id' => auth()->id(),
                    'schedule_time' => $this->schedule_time,
                    'capacity' => $this->capacity,
                    'status' => $this->status
                ]);
            });

            $this->reset(['title', 'description', 'schedule_time', 'capacity', 'status', 'showModal']);
            session()->flash('message', '🎉 New class created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', '😕 Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit($id) {
        try {
            $this->editMode = true;
            $this->classId = $id;
            $class = FitnessClass::where('trainer_id', auth()->id())->findOrFail($id);
            
            $this->title = $class->title;
            $this->description = $class->description;
            $this->schedule_time = $class->schedule_time->format('Y-m-d\TH:i');
            $this->capacity = $class->capacity;
            $this->status = $class->status;
            $this->showModal = true;
        } catch (\Exception $e) {
            session()->flash('error', '😮 Error while editing: ' . $e->getMessage());
            $this->cancelEdit();
        }
    }

    public function update() {
        $this->validate();

        try {
            DB::transaction(function () {
                $class = FitnessClass::where('trainer_id', auth()->id())->findOrFail($this->classId);
                $class->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'schedule_time' => $this->schedule_time,
                    'capacity' => $this->capacity,
                    'status' => $this->status
                ]);
            });

            $this->reset(['title', 'description', 'schedule_time', 'capacity', 'status', 'editMode', 'classId', 'showModal']);
            session()->flash('message', '✨ Class updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', '😬 Update failed: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $class = FitnessClass::where('trainer_id', auth()->id())->findOrFail($id);
            $class->delete();
            session()->flash('message', '🗑️ Class deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', '😱 Deletion failed: ' . $e->getMessage());
        }
    }

    public function cancelEdit() {
        $this->reset(['title', 'description', 'schedule_time', 'capacity', 'status', 'editMode', 'classId', 'showModal']);
    }

    public function viewClass($id) {
        try {
            $this->viewingClass = FitnessClass::where('trainer_id', auth()->id())->findOrFail($id);
            $this->viewModal = true;
        } catch (\Exception $e) {
            session()->flash('error', '😮 Error while viewing: ' . $e->getMessage());
        }
    }

    public function closeView() {
        $this->viewModal = false;
        $this->viewingClass = null;
    }
}; ?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h2 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">My Classes</h2>
            </div>
            <flux:button wire:click="$set('showModal', true)" class="inline-flex items-center px-4 py-2 rounded-lg shadow-sm text-sm font-medium !text-white !bg-emerald-600 hover:!bg-emerald-700 dark:!bg-emerald-500 dark:hover:!bg-emerald-600 transition-colors duration-200">
                <p>Create Class</p>
            </flux:button>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
        <div class="flex items-center p-4 mb-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded-lg" role="alert">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
        @endif
        @if (session()->has('error'))
        <div class="flex items-center p-4 mb-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-lg" role="alert">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Search & Filters -->
        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search by title..."
                {{-- icon="search" --}}
                class="w-full"
            />
            <flux:select wire:model.live="statusFilter" placeholder="All statuses" class="w-full">
                <option value="">All statuses</option>
                <option value="active">Active</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
            </flux:select>
            <flux:select wire:model.live="perPage" class="w-full">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </flux:select>
        </div>

        <!-- Class Modal -->
        @if($showModal)
        <div class="fixed inset-0 bg-zinc-500/75 dark:bg-zinc-900/90 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $editMode ? 'Edit Class' : 'Create New Class' }}
                    </h3>
                    <button wire:click="cancelEdit" class="text-zinc-400 hover:text-zinc-500 dark:text-zinc-500 dark:hover:text-zinc-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="{{ $editMode ? 'update' : 'create' }}" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Title *</label>
                        <flux:input type="text" wire:model="title" class="mt-1 block w-full rounded-lg dark:bg-zinc-700 dark:text-zinc-100" />
                        @error('title') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</label>
                        <flux:textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100"></flux:textarea>
                        @error('description') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Schedule Time *</label>
                            <flux:input type="datetime-local" wire:model="schedule_time" class="mt-1 block w-full rounded-lg dark:bg-zinc-700 dark:text-zinc-100" />
                            @error('schedule_time') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Capacity *</label>
                            <flux:input type="number" wire:model="capacity" min="1" max="100" class="mt-1 block w-full rounded-lg dark:bg-zinc-700 dark:text-zinc-100" />
                            @error('capacity') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Show status field only when editing -->
                    @if($editMode)
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status *</label>
                        <flux:select wire:model="status" class="mt-1 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100">
                            <option value="active">Active</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </flux:select>
                        @error('status') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div class="flex justify-end space-x-3 mt-6">
                        <flux:button type="button" wire:click="cancelEdit" class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 dark:bg-emerald-500 rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600">
                            {{ $editMode ? 'Update' : 'Create' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- View Class Modal -->
        @if($viewModal && $viewingClass)
        <div class="fixed inset-0 bg-zinc-500/75 dark:bg-zinc-900/90 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Class Details</h3>
                    <button wire:click="closeView" class="text-zinc-400 hover:text-zinc-500 dark:text-zinc-500 dark:hover:text-zinc-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Title</label>
                        <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $viewingClass->title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</label>
                        <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $viewingClass->description ?? 'No description provided' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Schedule Time</label>
                            <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $viewingClass->schedule_time->format('M d, Y g:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Capacity</label>
                            <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $viewingClass->capacity }} participants</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                        <span class="inline-flex px-2.5 py-1.5 text-xs font-medium rounded-full
                            @if($viewingClass->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                            @elseif($viewingClass->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                            {{ ucfirst($viewingClass->status) }}
                        </span>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <flux:button wire:click="closeView" class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600">
                            Close
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Classes Horizontal Cards -->
        <div class="space-y-4">
            @forelse ($this->classes as $class)
            <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1 cursor-pointer" wire:click="viewClass({{ $class->id }})">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $class->title }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $class->schedule_time->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center space-x-6">
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                <span class="font-medium">Capacity:</span> {{ $class->capacity }} participants
                            </div>
                            <div>
                                <span class="px-2.5 py-1.5 text-xs font-medium rounded-full
                                    @if($class->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($class->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 @endif">
                                    {{ ucfirst($class->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-2">
                        <flux:button wire:click="edit({{ $class->id }})" class="!text-emerald-600 dark:!text-emerald-400 hover:!text-emerald-800 dark:hover:!text-emerald-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </flux:button>
                        <!-- Check Participants Button -->
                        <flux:button href="{{ route('trainer.classes.participants', $class->id) }}" variant="outline" class="!text-emerald-600 dark:!text-emerald-400 hover:!text-emerald-800 dark:hover:!text-emerald-300">
                            Check Participants
                        </flux:button>
                        <flux:button x-data="" @click.prevent="if(confirm('Are you sure you want to delete this class?')) { $wire.delete({{ $class->id }}) }" class="!text-red-600 dark:!text-red-400 hover:!text-red-800 dark:hover:!text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </flux:button>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-6 text-center">
                    <svg class="w-12 h-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-zinc-500 dark:text-zinc-400">No classes found. Create your first class to get started!</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $this->classes->links() }}
        </div>
    </div>
</div>
