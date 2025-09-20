<?php

namespace App\Http\Controllers;

use App\Models\FitnessClass;
use App\Models\ClassRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            $classes = FitnessClass::with('trainer')->latest()->get();
        } elseif ($user->isTrainer()) {
            $classes = $user->trainerClasses()->with('trainer')->latest()->get();
        } else {
            // For students, show only active classes they're not registered in
            $registeredClassIds = $user->classRegistrations()->pluck('class_id');
            $classes = FitnessClass::where('status', 'active')
                ->whereNotIn('id', $registeredClassIds)
                ->with('trainer')
                ->latest()
                ->get();
        }

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $this->authorize('create', FitnessClass::class);
        
        $trainers = $this->getTrainers();
        return view('classes.create', compact('trainers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', FitnessClass::class);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trainer_id' => 'required|exists:users,id',
            'schedule_time' => 'required|date|after:now',
            'capacity' => 'required|integer|min:1',
        ]);

        $class = FitnessClass::create($validated);
        
        return redirect()->route('classes.show', $class)
            ->with('success', 'Class created successfully.');
    }

    public function show(FitnessClass $class)
    {
        $class->load('trainer', 'registrations.student');
        $isRegistered = false;
        $registration = null;
        
        if (auth()->user()->isStudent()) {
            $registration = $class->registrations()
                ->where('student_id', auth()->id())
                ->first();
            $isRegistered = $registration !== null;
        }
        
        return view('classes.show', compact('class', 'isRegistered', 'registration'));
    }

    public function edit(FitnessClass $class)
    {
        $this->authorize('update', $class);
        
        $trainers = $this->getTrainers();
        return view('classes.edit', compact('class', 'trainers'));
    }

    public function update(Request $request, FitnessClass $class)
    {
        $this->authorize('update', $class);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trainer_id' => 'required|exists:users,id',
            'schedule_time' => 'required|date|after:now',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,cancelled,completed',
        ]);

        $class->update($validated);
        
        return redirect()->route('classes.show', $class)
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(FitnessClass $class)
    {
        $this->authorize('delete', $class);
        
        $class->delete();
        
        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    public function register(FitnessClass $class)
    {
        $this->authorize('register', $class);
        
        if ($class->isFull()) {
            return back()->with('error', 'This class is already full.');
        }
        
        ClassRegistration::create([
            'class_id' => $class->id,
            'student_id' => auth()->id(),
            'status' => 'booked',
        ]);
        
        return back()->with('success', 'Successfully registered for the class.');
    }
    
    public function cancelRegistration(FitnessClass $class)
    {
        $registration = $class->registrations()
            ->where('student_id', auth()->id())
            ->firstOrFail();
            
        $registration->delete();
        
        return back()->with('success', 'Registration cancelled successfully.');
    }
    
    public function updateAttendance(Request $request, ClassRegistration $registration)
    {
        $this->authorize('updateAttendance', $registration);
        
        $validated = $request->validate([
            'status' => 'required|in:attended,absent',
        ]);
        
        $registration->update($validated);
        
        return back()->with('success', 'Attendance updated successfully.');
    }
    
    protected function getTrainers()
    {
        return \App\Models\User::role('trainer')->get();
    }
}
