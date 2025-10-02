<?php

use Livewire\Volt\Component;
use App\Models\FitnessClass;
use App\Models\ClassRegistration;

new class extends Component {
    public $classID;
    public $progress = 0; // 0-100
    public $comment;
    public $workoutdiet;
    public $classTitle;

    public function mount($classID)
    {
        $this->classID = $classID;
        $registration = ClassRegistration::where('class_id', $classID)
            ->where('student_id', auth()->id())
            ->first();
        if ($registration) {
            $this->progress = (int) $registration->progress ?? 0;
            $this->comment = $registration->comment ?? '';
            $this->workoutdiet = $registration->workoutdiet ?? '';
        }

        $fitnessClass = FitnessClass::find($classID);
        $this->classTitle = $fitnessClass ? $fitnessClass->title : 'Unknown Class';
    }
}; ?>

<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition">
                ← Back
            </a>
            <div class="text-center flex-1">
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $classTitle }} – Your Progress</h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-400">Track your journey in this fitness class</p>
            </div>
            <div class="w-20"></div>
        </div>

        <!-- Progress Bar Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Class Progress</h2>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">0%</span>
                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $progress }}%</span>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">100%</span>
            </div>
            <div class="mt-2 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5">
                <div
                    class="h-2.5 rounded-full transition-all duration-300
                        @if($progress < 10) bg-gradient-to-r from-pink-400 to-pink-600
                        @elseif($progress < 20) bg-gradient-to-r from-rose-400 to-rose-600
                        @elseif($progress < 30) bg-gradient-to-r from-orange-400 to-orange-600
                        @elseif($progress < 40) bg-gradient-to-r from-amber-400 to-amber-600
                        @elseif($progress < 50) bg-gradient-to-r from-yellow-400 to-yellow-600
                        @elseif($progress < 60) bg-gradient-to-r from-lime-400 to-lime-600
                        @elseif($progress < 70) bg-gradient-to-r from-green-400 to-green-600
                        @elseif($progress < 80) bg-gradient-to-r from-teal-400 to-teal-600
                        @elseif($progress < 90) bg-gradient-to-r from-cyan-400 to-cyan-600
                        @else bg-gradient-to-r from-indigo-400 to-indigo-600
                        @endif"
                    style="width: {{ $progress }}%;"
                ></div>
            </div>
            <div class="mt-3 text-center text-sm
                @if($progress < 10) text-pink-600 dark:text-pink-400
                @elseif($progress < 20) text-rose-600 dark:text-rose-400
                @elseif($progress < 30) text-orange-600 dark:text-orange-400
                @elseif($progress < 40) text-amber-600 dark:text-amber-400
                @elseif($progress < 50) text-yellow-600 dark:text-yellow-400
                @elseif($progress < 60) text-lime-600 dark:text-lime-400
                @elseif($progress < 70) text-green-600 dark:text-green-400
                @elseif($progress < 80) text-teal-600 dark:text-teal-400
                @elseif($progress < 90) text-cyan-600 dark:text-cyan-400
                @else text-indigo-600 dark:text-indigo-400
                @endif">
                @if($progress < 10)
                    Fresh start – every step counts!
                @elseif($progress < 20)
                    Gaining traction – stay consistent!
                @elseif($progress < 30)
                    Building momentum – keep pushing!
                @elseif($progress < 40)
                    Rising energy – you're on fire!
                @elseif($progress < 50)
                    Halfway milestone – celebrate progress!
                @elseif($progress < 60)
                    Beyond halfway – strength grows!
                @elseif($progress < 70)
                    Surging forward – almost there!
                @elseif($progress < 80)
                    Closing in – final stretch ahead!
                @elseif($progress < 90)
                    Nearly complete – finish strong!
                @else
                    Victory lap – you did it!
                @endif
            </div>
        </div>

        <!-- Notes Card (read-only) -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Notes & Plan</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Trainer Comments</label>
                    <div class="w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 px-3 py-2 min-h-[5rem]">
                        {{ $comment ?: 'No comments yet.' }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Workout & Diet Plan</label>
                    <div class="w-full rounded-md border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 px-3 py-2 min-h-[7rem]">
                        {{ $workoutdiet ?: 'No plan provided yet.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
