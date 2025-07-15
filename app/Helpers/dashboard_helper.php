<?php

use CodeIgniter\I18n\Time;

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
            ];
        } elseif ($hour >= 12 && $hour < 17) {
            $greetings = [
                "Good afternoon, $name! Keep grinding 💪",
                "Hope you're having a productive afternoon, $name!",
                "Still awake after lunch, $name? 😴",
                "Hey $name, don't let the post-lunch sleepiness win!",
                "$name, you're crushing it this afternoon! 🚀",
            ];
        } elseif ($hour >= 17 && $hour < 21) {
            $greetings = [
                "Good evening, $name! Time to wind down 🌇",
                "Hey $name, how was your day?",
                "Evening vibes with style, $name 🌙",
                "$name, it’s almost Netflix o’clock! 🍿",
                "Time to relax, $name! You’ve earned it ✨",
            ];
        } else {
            $greetings = [
                "Burning the midnight oil, $name? 🌙",
                "It’s late, $name! Don’t forget to rest 😴",
                "Hello night owl $name 🦉",
                "What’s cooking this late, $name?",
                "$name, remember: sleep is important, even for heroes 🛌",
            ];
        }

        // Pick a random message
        return $greetings[array_rand($greetings)];
    }
}
