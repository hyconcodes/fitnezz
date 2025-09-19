<?php

use Livewire\Volt\Component;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;

new class extends Component {
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
            <h2 class="text-2xl font-semibold text-blue-900 dark:text-blue-100">Equipment Management</h2>
            @can('create.equipment')
            <flux:button wire:click="$set('showModal', true)" class="inline-flex items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium !text-white !bg-blue-700">
                Add New Equipment
            </flux:button>
            @endcan
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
        <div class="border border-green-400 bg-green-500 text-white px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
        @endif
        @if (session()->has('error'))
        <div class="border border-red-400 bg-red-500 text-white px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Equipment Modal -->
        @if($showModal)
        <div class="fixed inset-0 bg-zinc-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-medium text-zinc-900 mb-4">
                    {{ $editMode ? 'Edit Equipment' : 'Add New Equipment' }}
                </h3>
                <form wire:submit.prevent="{{ $editMode ? 'update' : 'create' }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700">Name *</label>
                            <flux:input type="text" wire:model="name" class="mt-1 block w-full rounded-md" />
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700">Status *</label>
                            <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="available">Available</option>
                                <option value="in-use">In Use</option>
                                <option value="under-maintenance">Under Maintenance</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700">Maintenance Schedule</label>
                            <flux:input type="date" wire:model="maintenanceSchedule" class="mt-1 block w-full rounded-md" />
                            @error('maintenanceSchedule') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700">Last Serviced On</label>
                            <flux:input type="date" wire:model="lastServicedAt" class="mt-1 block w-full rounded-md" />
                            @error('lastServicedAt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700">Notes</label>
                            <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700">Picture</label>
                            <input type="file" wire:model="picture" class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100">
                            @error('picture') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="mt-5 flex justify-end space-x-3">
                        <flux:button type="button" wire:click="cancelEdit" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 rounded-md">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">
                            {{ $editMode ? 'Update' : 'Create' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Equipment Table -->
        <div class="shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Serviced</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            @canany(['edit.equipment', 'delete.equipment'])
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($equipments as $equipment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($equipment->picture)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $equipment->picture) }}" alt="{{ $equipment->name }}">
                                    </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $equipment->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(auth()->user()->can('maintain.equipment'))
                                <select wire:change="updateStatus({{ $equipment->id }}, $event.target.value)" 
                                    class="text-xs font-medium px-2.5 py-0.5 rounded-full 
                                        @if($equipment->status === 'available') bg-green-100 text-green-800
                                        @elseif($equipment->status === 'in-use') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                    <option value="available" {{ $equipment->status === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="in-use" {{ $equipment->status === 'in-use' ? 'selected' : '' }}>In Use</option>
                                    <option value="under-maintenance" {{ $equipment->status === 'under-maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @else
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full 
                                    @if($equipment->status === 'available') bg-green-100 text-green-800
                                    @elseif($equipment->status === 'in-use') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($equipment->status) }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipment->maintenance_schedule ? \Carbon\Carbon::parse($equipment->maintenance_schedule)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipment->last_serviced_at ? \Carbon\Carbon::parse($equipment->last_serviced_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if(strlen($equipment->notes) > 50)
                                    <span x-data="{ showFull: false }">
                                        <span x-show="!showFull">{{ Str::limit($equipment->notes, 50) }}</span>
                                        <span x-show="showFull">{{ $equipment->notes }}</span>
                                        <button @click="showFull = !showFull" class="text-blue-600 hover:text-blue-800 text-xs ml-1">
                                            <span x-text="showFull ? 'Show less' : '...more'"></span>
                                        </button>
                                    </span>
                                @else
                                    {{ $equipment->notes ?? 'N/A' }}
                                @endif
                            </td>
                            @canany(['edit.equipment', 'delete.equipment'])
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @can('edit.equipment')
                                <flux:button wire:click="edit({{ $equipment->id }})" class="!text-blue-600 hover:!text-blue-800 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </flux:button>
                                @endcan
                                @can('delete.equipment')
                                <flux:button x-data="" @click.prevent="if(confirm('Are you sure you want to delete this equipment?')) { $wire.delete({{ $equipment->id }}) }" class="!text-red-600 hover:!text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </flux:button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No equipment found. Add your first equipment to get started!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>