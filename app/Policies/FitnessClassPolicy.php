<?php

namespace App\Policies;

use App\Models\FitnessClass;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FitnessClassPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // All authenticated users can view classes
    }

    public function view(User $user, FitnessClass $class)
    {
        return true; // All authenticated users can view a class
    }

    public function create(User $user)
    {
        // Only super admins and trainers can create classes
        return $user->isSuperAdmin() || $user->isTrainer();
    }

    public function update(User $user, FitnessClass $class)
    {
        // Super admins can update any class
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // Trainers can only update their own classes
        if ($user->isTrainer()) {
            return $class->trainer_id === $user->id;
        }
        
        return false;
    }

    public function delete(User $user, FitnessClass $class)
    {
        // Only super admins can delete classes
        return $user->isSuperAdmin();
    }
    
    public function register(User $user, FitnessClass $class)
    {
        // Only students can register for classes
        // Cannot register if already registered
        // Cannot register if class is full
        // Cannot register if class is not active
        if (!$user->isStudent()) {
            return false;
        }
        
        // Check if already registered
        $isRegistered = $class->registrations()
            ->where('student_id', $user->id)
            ->exists();
            
        if ($isRegistered) {
            return false;
        }
        
        // Check if class is active and not full
        return $class->status === 'active' && !$class->isFull();
    }
    
    public function updateAttendance(User $user, $registration)
    {
        // Only the trainer of the class can update attendance
        return $user->isSuperAdmin() || 
               ($user->isTrainer() && $registration->fitnessClass->trainer_id === $user->id);
    }
}
