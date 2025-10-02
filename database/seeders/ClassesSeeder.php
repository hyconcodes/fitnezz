<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FitnessClass;
use Carbon\Carbon;
class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $classes = [
            ['title' => 'Morning HIIT Blast', 'description' => 'High-intensity interval training to kick-start your day with energy.'],
            ['title' => 'Yoga Flow', 'description' => 'Vinyasa yoga session focused on flexibility, balance, and mindfulness.'],
            ['title' => 'Strength & Conditioning', 'description' => 'Full-body strength workout using free weights and machines.'],
            ['title' => 'Spin & Burn', 'description' => 'Indoor cycling class set to upbeat music for maximum calorie burn.'],
            ['title' => 'Boxing Bootcamp', 'description' => 'Cardio boxing drills combined with core and strength exercises.'],
            ['title' => 'Pilates Core', 'description' => 'Low-impact core strengthening and posture improvement exercises.'],
            ['title' => 'Zumba Party', 'description' => 'Dance fitness class with Latin-inspired moves and infectious rhythms.'],
            ['title' => 'Barre Sculpt', 'description' => 'Ballet-inspired workout targeting legs, glutes, and core.'],
            ['title' => 'Kettlebell Power', 'description' => 'Dynamic kettlebell movements to build strength and endurance.'],
            ['title' => 'TRX Suspension', 'description' => 'Body-weight exercises using suspension straps for stability and strength.'],
            ['title' => 'Evening Stretch', 'description' => 'Gentle stretching session to release tension and improve flexibility.'],
            ['title' => 'Booty Builder', 'description' => 'Focused glute and lower-body workout to lift and tone.'],
            ['title' => 'Abs & Core Attack', 'description' => 'Intense core circuit to sculpt strong abdominal muscles.'],
            ['title' => 'Rowing Intervals', 'description' => 'High-energy rowing machine intervals for cardio and power.'],
            ['title' => 'Mobility & Recovery', 'description' => 'Foam rolling and mobility drills to aid muscle recovery.'],
            ['title' => 'Cardio Dance', 'description' => 'Fun dance routines that keep your heart rate up and spirits high.'],
            ['title' => 'Power Lifting', 'description' => 'Technique-focused session on squats, deadlifts, and presses.'],
            ['title' => 'Senior Fit', 'description' => 'Low-impact class designed for older adults to improve balance and strength.'],
            ['title' => 'Kickboxing Cardio', 'description' => 'Non-contact kickboxing combinations for cardio and coordination.'],
            ['title' => 'Mindful Meditation', 'description' => 'Guided meditation and breathing exercises to reduce stress.'],
            ['title' => 'Athletic Performance', 'description' => 'Sport-specific drills to boost speed, agility, and power.'],
            ['title' => 'Circuit Express', 'description' => '30-minute full-body circuit for busy schedules.'],
            ['title' => 'Flex & Stretch', 'description' => 'Deep stretching and flexibility training for all levels.'],
            ['title' => 'Core & More', 'description' => 'Core-focused workout with added cardio bursts.'],
            ['title' => 'Sunset Yoga', 'description' => 'Relaxing evening yoga to unwind and restore balance.'],
            ['title' => 'Battle Ropes', 'description' => 'High-intensity rope training for total-body conditioning.'],
            ['title' => 'Functional Fitness', 'description' => 'Exercises that mimic daily movements to improve everyday strength.'],
            ['title' => 'Jump Rope HIIT', 'description' => 'Fast-paced jump rope intervals for cardio and coordination.'],
            ['title' => 'Lower Body Burn', 'description' => 'Targeted leg and glute workout to build strength and shape.'],
            ['title' => 'Upper Body Pump', 'description' => 'Focused arms, chest, and back session for muscle definition.'],
        ];

        foreach ($classes as $index => $class) {
            FitnessClass::create([
                'title' => $class['title'],
                'description' => $class['description'],
                'trainer_id' => 2,
                'schedule_time' => $now->copy()->addDays($index + 1),
                'capacity' => rand(10, 30),
                'status' => 'active',
            ]);
        }
    }
}
