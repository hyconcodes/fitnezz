<?php

use Livewire\Volt\Component;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    
    public $equipments;
    public $name;
    public $status = 'available';
    public $maintenanceSchedule;
    public $lastServicedAt;
    public $notes;
    public $picture;
    public $editMode = false;
    public $equipmentId;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|min:3|max:100',
        'status' => 'required|in:available,in-use,under-maintenance',
        'maintenanceSchedule' => 'nullable|date|after_or_equal:today',
        'lastServicedAt' => 'nullable|date|before_or_equal:today',
        'notes' => 'nullable|string|max:500',
        'picture' => 'nullable|image|max:2048'
    ];

    protected $messages = [
        'name.required' => 'Please enter the equipment name! 😊',
        'status.required' => 'Please select the equipment status! 🔄',
        'maintenanceSchedule.date' => 'Please enter a valid date! 📅',
        'maintenanceSchedule.after_or_equal' => 'Maintenance date must be today or in the future! ⏱️',
        'lastServicedAt.date' => 'Please enter a valid date! 📅',
        'lastServicedAt.before_or_equal' => 'Last serviced date cannot be in the future! ⏱️',
        'picture.image' => 'The file must be an image! 🖼️',
        'picture.max' => 'The image size should not exceed 2MB! 📏'
    ];

    public function mount() {
        $this->loadEquipment();
    }

    public function loadEquipment() {
        $this->equipments = Equipment::latest()->get();
    }

    public function create() {
        if(!auth()->user()->can('create.equipment')) {
            session()->flash('error', '🚫 You don\'t have permission to add equipment.');
            return;
        }

        $this->validate();

        try {
            DB::transaction(function () {
                $equipment = Equipment::create([
                    'name' => $this->name,
                    'status' => $this->status,
                    'maintenance_schedule' => $this->maintenanceSchedule,
                    'last_serviced_at' => $this->lastServicedAt,
                    'notes' => $this->notes,
                    'picture' => $this->picture ? $this->picture->store('equipment', 'public') : null
                ]);
            });

            $this->reset(['name', 'status', 'maintenanceSchedule', 'lastServicedAt', 'notes', 'picture', 'showModal']);
            $this->loadEquipment();
            session()->flash('message', '🎉 New equipment added successfully!');
        } catch (\Exception $e) {
            session()->flash('error', '😕 Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit($id) {
        if(!auth()->user()->can('edit.equipment')) {
            session()->flash('error', '🚫 You don\'t have edit permissions.');
            return;
        }

        try {
            $this->editMode = true;
            $this->equipmentId = $id;
            $equipment = Equipment::findOrFail($id);
            
            $this->name = $equipment->name;
            $this->status = $equipment->status;
            $this->maintenanceSchedule = $equipment->maintenance_schedule?->format('Y-m-d');
            $this->lastServicedAt = $equipment->last_serviced_at?->format('Y-m-d');
            $this->notes = $equipment->notes;
            $this->showModal = true;
        } catch (\Exception $e) {
            session()->flash('error', '😮 Error while editing: ' . $e->getMessage());
            $this->cancelEdit();
        }
    }

    public function update() {
        if(!auth()->user()->can('edit.equipment')) {
            session()->flash('error', '🚫 You can\'t modify this record.');
            return;
        }

        $this->validate();

        try {
            DB::transaction(function () {
                $equipment = Equipment::findOrFail($this->equipmentId);
                $updateData = [
                    'name' => $this->name,
                    'status' => $this->status,
                    'maintenance_schedule' => $this->maintenanceSchedule,
                    'last_serviced_at' => $this->lastServicedAt,
                    'notes' => $this->notes
                ];

                if ($this->picture) {
                    // Delete old picture if exists
                    if ($equipment->picture) {
                        Storage::disk('public')->delete($equipment->picture);
                    }
                    $updateData['picture'] = $this->picture->store('equipment', 'public');
                }

                $equipment->update($updateData);
            });

            $this->reset(['name', 'status', 'maintenanceSchedule', 'lastServicedAt', 'notes', 'picture', 'editMode', 'equipmentId', 'showModal']);
            $this->loadEquipment();
            session()->flash('message', '✨ Equipment updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', '😬 Update failed: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status) {
        if(!auth()->user()->can('maintain.equipment')) {
            session()->flash('error', '🚫 You don\'t have permission to update status.');
            return;
        }

        try {
            $equipment = Equipment::findOrFail($id);
            $equipment->update(['status' => $status]);
            $this->loadEquipment();
            session()->flash('message', '🔄 Equipment status updated!');
        } catch (\Exception $e) {
            session()->flash('error', '😮 Status update failed: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        if(!auth()->user()->can('delete.equipment')) {
            session()->flash('error', '🚫 You can\'t delete records.');
            return;
        }

        try {
            $equipment = Equipment::findOrFail($id);
            
            // Delete picture if exists
            if ($equipment->picture) {
                Storage::disk('public')->delete($equipment->picture);
            }
            
            $equipment->delete();
            $this->loadEquipment();
            session()->flash('message', '🗑️ Equipment deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', '😱 Deletion failed: ' . $e->getMessage());
        }
    }

    public function cancelEdit() {
        $this->reset(['name', 'status', 'maintenanceSchedule', 'lastServicedAt', 'notes', 'picture', 'editMode', 'equipmentId', 'showModal']);
    }
};
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h2 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Equipment Management</h2>
            </div>
            @can('create.equipment')
            <flux:button wire:click="$set('showModal', true)" class="inline-flex items-center px-4 py-2 rounded-lg shadow-sm text-sm font-medium !text-white !bg-green-600 hover:!bg-green-700 dark:!bg-green-500 dark:hover:!bg-green-600 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p>Add Equipment</p>
            </flux:button>
            @endcan
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

        <!-- Equipment Modal -->
        @if($showModal)
        <div class="fixed inset-0 bg-zinc-500/75 dark:bg-zinc-900/90 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $editMode ? 'Edit Equipment' : 'Add New Equipment' }}
                    </h3>
                    <button wire:click="cancelEdit" class="text-zinc-400 hover:text-zinc-500 dark:text-zinc-500 dark:hover:text-zinc-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="{{ $editMode ? 'update' : 'create' }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name *</label>
                            <flux:input type="text" wire:model="name" class="mt-1 block w-full rounded-lg dark:bg-zinc-700 dark:text-zinc-100" />
                            @error('name') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status *</label>
                            <select wire:model="status" class="mt-1 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100">
                                <option value="available">Available</option>
                                <option value="in-use">In Use</option>
                                <option value="under-maintenance">Under Maintenance</option>
                            </select>
                            @error('status') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Maintenance Schedule</label>
                            <flux:input type="date" wire:model="maintenanceSchedule" class="mt-1 block w-full rounded-lg dark:bg-zinc-700 dark:text-zinc-100" />
                            @error('maintenanceSchedule') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Last Serviced On</label>
                            <flux:input type="date" wire:model="lastServicedAt" class="mt-1 block w-full rounded-lg dark:bg-zinc-700 dark:text-zinc-100" />
                            @error('lastServicedAt') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes</label>
                            <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100"></textarea>
                            @error('notes') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Picture</label>
                            <input type="file" wire:model="picture" class="mt-1 block w-full text-sm text-zinc-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-medium
                                file:bg-green-50 file:text-green-700
                                dark:file:bg-green-900 dark:file:text-green-300
                                hover:file:bg-green-100 dark:hover:file:bg-green-800">
                            @error('picture') <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 mt-6">
                        <flux:button type="button" wire:click="cancelEdit" class="px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-700 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-600">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 dark:bg-green-500 rounded-lg hover:bg-green-700 dark:hover:bg-green-600">
                            {{ $editMode ? 'Update' : 'Create' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Equipment Table -->
        <div class="bg-white dark:bg-zinc-800 shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Maintenance Date</th>
                            <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Last Serviced</th>
                            <th scope="col" class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Notes</th>
                            @canany(['edit.equipment', 'delete.equipment'])
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($equipments as $equipment)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($equipment->picture)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $equipment->picture) }}" alt="{{ $equipment->name }}">
                                    </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $equipment->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(auth()->user()->can('maintain.equipment'))
                                <select wire:change="updateStatus({{ $equipment->id }}, $event.target.value)" 
                                    class="text-xs font-medium px-2.5 py-1.5 rounded-full border-0
                                        @if($equipment->status === 'available') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($equipment->status === 'in-use') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                    <option value="available" {{ $equipment->status === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="in-use" {{ $equipment->status === 'in-use' ? 'selected' : '' }}>In Use</option>
                                    <option value="under-maintenance" {{ $equipment->status === 'under-maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @else
                                <span class="px-2.5 py-1.5 text-xs font-medium rounded-full
                                    @if($equipment->status === 'available') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($equipment->status === 'in-use') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                    {{ ucfirst($equipment->status) }}
                                </span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $equipment->maintenance_schedule ? \Carbon\Carbon::parse($equipment->maintenance_schedule)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $equipment->last_serviced_at ? \Carbon\Carbon::parse($equipment->last_serviced_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="hidden lg:table-cell px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400">
                                @if(strlen($equipment->notes) > 50)
                                    <span x-data="{ showFull: false }">
                                        <span x-show="!showFull">{{ Str::limit($equipment->notes, 50) }}</span>
                                        <span x-show="showFull">{{ $equipment->notes }}</span>
                                        <button @click="showFull = !showFull" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-xs ml-1">
                                            <span x-text="showFull ? 'Show less' : '...more'"></span>
                                        </button>
                                    </span>
                                @else
                                    {{ $equipment->notes ?? 'N/A' }}
                                @endif
                            </td>
                            @canany(['edit.equipment', 'delete.equipment'])
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @can('edit.equipment')
                                    <flux:button wire:click="edit({{ $equipment->id }})" class="p-1 !text-green-600 dark:!text-green-400 hover:!text-green-800 dark:hover:!text-green-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </flux:button>
                                    @endcan
                                    @can('delete.equipment')
                                    <flux:button x-data="" @click.prevent="if(confirm('Are you sure you want to delete this equipment?')) { $wire.delete({{ $equipment->id }}) }" class="p-1 !text-red-600 dark:!text-red-400 hover:!text-red-800 dark:hover:!text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </flux:button>
                                    @endcan
                                </div>
                            </td>
                            @endcanany
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center py-6">
                                    <svg class="w-12 h-12 text-zinc-400 dark:text-zinc-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p>No equipment found. Add your first equipment to get started!</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>