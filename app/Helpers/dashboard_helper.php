<?php

use CodeIgniter\I18n\Time;
use App\Models\ScheduleModel;
use App\Models\CommercialModel;
use App\Models\ClientModel;
use App\Models\ProgramModel;

if (!function_exists('get_greeting')) {
    function get_greeting(string $name = 'User'): string
    {
        $hour = Time::now()->getHour();

        $greetings = [];

        if ($hour >= 5 && $hour < 12) {
            $greetings = [
                "Good morning, $name! Time to rise and shine ☀️",
                "Top of the morning to you, $name! 🍳☕",
                "Wakey wakey, $name! Let’s seize the day!",
                "Morning, $name! Hope you’ve had your coffee already ☕️",
                "Hey $name, another beautiful day awaits! 🌼",
                "Rise and grind, $name! The early bird gets the worm 🐦",
                "Good morning, $name! Don’t forget to smile today 😊",
                "A fresh new morning, $name! Let’s make it count!",
                "Good morning, $name! May your day be as bright as your smile!",
            ];
        } elseif ($hour >= 12 && $hour < 17) {
            $greetings = [
                "Good afternoon, $name! Keep grinding 💪",
                "Hope you're having a productive afternoon, $name!",
                "Still awake after lunch, $name? 😴",
                "Hey $name, don't let the post-lunch sleepiness win!",
                "$name, you're crushing it this afternoon! 🚀",
                "Good afternoon, $name! Time to power through!",
                "Keep pushing, $name! The day’s almost won!",
                "Afternoon, $name! Remember to hydrate 💧",
                "Hey $name, your hard work is paying off!",
            ];
        } elseif ($hour >= 17 && $hour < 21) {
            $greetings = [
                "Good evening, $name! Time to wind down 🌇",
                "Hey $name, how was your day?",
                "Evening vibes with style, $name 🌙",
                "$name, it’s almost Netflix o’clock! 🍿",
                "Time to relax, $name! You’ve earned it ✨",
                "Good evening, $name! Reflect and recharge.",
                "Evening, $name! A perfect time to unwind.",
                "Hey $name, hope you had a fantastic day!",
                "Chill out, $name! Tomorrow’s a new adventure.",
            ];
        } else {
            $greetings = [
                "Burning the midnight oil, $name? 🌙",
                "It’s late, $name! Don’t forget to rest 😴",
                "Hello night owl $name 🦉",
                "What’s cooking this late, $name?",
                "$name, remember: sleep is important, even for heroes 🛌",
                "Late night hustle, $name? Keep it up but rest well!",
                "Good night, $name! Dream big!",
                "The stars are out, $name. Time to recharge!",
                "$name, even legends need their beauty sleep!",
            ];
        }

        // Pick a random message
        return $greetings[array_rand($greetings)];
    }

    if (!function_exists('get_total_counts')) {
        /**
         * Get total counts for schedules, commercials, clients, and programs.
         *
         * @return array Associative array with counts
         */
        function get_total_counts(): array
        {
            $scheduleModel = new ScheduleModel();
            $commercialModel = new CommercialModel();
            $clientModel = new ClientModel();
            $programModel = new ProgramModel();

            return [
                'schedules' => $scheduleModel->countAllResults(false),   // false disables reset of query builder
                'commercials' => $commercialModel->countAllResults(false),
                'clients' => $clientModel->countAllResults(false),
                'programs' => $programModel->countAllResults(false),
            ];
        }
    }
}
