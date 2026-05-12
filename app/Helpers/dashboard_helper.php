<?php

use CodeIgniter\I18n\Time;
use App\Models\ScheduleModel;
use App\Models\CommercialModel;
use App\Models\ClientModel;
use App\Models\ProgramModel;
use App\Models\ScheduleItemModel;
use App\Models\ChipModel;

if (!function_exists('get_greeting')) {
    function get_greeting(string $name = 'User'): string
    {
        $hour = Time::now()->getHour();

        $greetings = [];

        if ($hour >= 5 && $hour < 12) {
            $greetings = [
                "Good morning, $name! Time to rise and shine ☀️",
                "Top of the morning to you, $name! 🍳☕",
                "Wakey wakey, $name! Let's seize the day!",
                "Morning, $name! Hope you've had your coffee already ☕️",
                "Hey $name, another beautiful day awaits! 🌼",
                "Rise and grind, $name! The early bird gets the worm 🐦",
                "Good morning, $name! Don't forget to smile today 😊",
                "A fresh new morning, $name! Let's make it count!",
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
                "Keep pushing, $name! The day's almost won!",
                "Afternoon, $name! Remember to hydrate 💧",
                "Hey $name, your hard work is paying off!",
            ];
        } elseif ($hour >= 17 && $hour < 21) {
            $greetings = [
                "Good evening, $name! Time to wind down 🌇",
                "Hey $name, how was your day?",
                "Evening vibes with style, $name 🌙",
                "$name, it's almost Netflix o'clock! 🍿",
                "Time to relax, $name! You've earned it ✨",
                "Good evening, $name! Reflect and recharge.",
                "Evening, $name! A perfect time to unwind.",
                "Hey $name, hope you had a fantastic day!",
                "Chill out, $name! Tomorrow's a new adventure.",
            ];
        } else {
            $greetings = [
                "Burning the midnight oil, $name? 🌙",
                "It's late, $name! Don't forget to rest 😴",
                "Hello night owl $name 🦉",
                "What's cooking this late, $name?",
                "$name, remember: sleep is important, even for heroes 🛌",
                "Late night hustle, $name? Keep it up but rest well!",
                "Good night, $name! Dream big!",
                "The stars are out, $name. Time to recharge!",
                "$name, even legends need their beauty sleep!",
            ];
        }

        return $greetings[array_rand($greetings)];
    }

    if (!function_exists('get_total_counts')) {
        function get_total_counts(): array
        {
            $db = \Config\Database::connect();

            // Scheduling
            $scheduleModel     = new ScheduleModel();
            $scheduleItemModel = new ScheduleItemModel();
            $commercialModel   = new CommercialModel();
            $clientModel       = new ClientModel();
            $programModel      = new ProgramModel();

            $counts = [
                'schedules'              => $scheduleModel->where('deleted_at', null)->countAllResults(false),
                'publishedSchedules'     => $scheduleModel->where('deleted_at', null)->where('published', 1)->countAllResults(false),
                'scheduleItems'          => $scheduleItemModel->where('deleted_at', null)->countAllResults(false),
                'publishedScheduleItems' => $scheduleItemModel->where('deleted_at', null)->where('published', 1)->countAllResults(false),
                'commercials'            => $commercialModel->countAllResults(false),
                'clients'                => $clientModel->countAllResults(false),
                'programs'               => $programModel->countAllResults(false),
            ];

            // Bookings
            $today = date('Y-m-d');
            $counts['bookings_total']   = $db->table('bookings')->countAllResults();
            $counts['bookings_today']   = $db->table('bookings')->where('booking_date', $today)->whereNotIn('status', ['rejected', 'cancelled'])->countAllResults();
            $counts['bookings_pending'] = $db->table('bookings')->where('status', 'pending')->countAllResults();

            // Chips
            $chips = (new ChipModel())->getAllWithCurrentHolder();
            $counts['chips_total']    = count($chips);
            $counts['chips_producers'] = count(array_filter($chips, fn($c) => ($c['to_location'] ?? null) === 'producer'));
            $counts['chips_library']   = count(array_filter($chips, fn($c) => ($c['to_location'] ?? null) === 'library'));
            $counts['chips_digital_unit']  = count(array_filter($chips, fn($c) => ($c['to_location'] ?? null) === 'digital_unit'));

            // Ingest sessions
            $counts['sessions_open']    = $db->table('ingest_sessions')->where('status', 'open')->countAllResults();
            $counts['sessions_partial'] = $db->table('ingest_sessions')->where('status', 'partial')->countAllResults();

            // Chip transactions — headline counts
            $todayDate = date('Y-m-d');
            $weekStart = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $month30   = date('Y-m-d 00:00:00', strtotime('-29 days'));

            $counts['tx_today'] = $db->table('chip_transactions')
                ->where('DATE(created_at)', $todayDate)->countAllResults();
            $counts['tx_week']  = $db->table('chip_transactions')
                ->where('created_at >=', $weekStart)->countAllResults();
            $counts['tx_total'] = $db->table('chip_transactions')->countAllResults();

            $typeRows = $db->table('chip_transactions')
                ->select('transaction_type, COUNT(*) AS cnt')
                ->where('created_at >=', $month30)
                ->groupBy('transaction_type')
                ->get()->getResultArray();

            $counts['tx_by_type_30d'] = ['RECEIVE' => 0, 'TRANSFER' => 0, 'HANDOVER' => 0, 'INGEST' => 0];
            foreach ($typeRows as $r) {
                if (isset($counts['tx_by_type_30d'][$r['transaction_type']])) {
                    $counts['tx_by_type_30d'][$r['transaction_type']] = (int) $r['cnt'];
                }
            }

            // 14-day timeline axis (oldest first)
            $days = [];
            for ($i = 13; $i >= 0; $i--) {
                $days[] = date('Y-m-d', strtotime("-{$i} days"));
            }
            $counts['chart_days']        = $days;
            $counts['chart_day_labels']  = array_map(fn($d) => date('M j', strtotime($d)), $days);

            // Chip transactions per day, by type
            $txDayRows = $db->table('chip_transactions')
                ->select("DATE(created_at) AS d, transaction_type, COUNT(*) AS cnt")
                ->where('created_at >=', $days[0] . ' 00:00:00')
                ->groupBy(['d', 'transaction_type'])
                ->get()->getResultArray();

            $txByDay = [];
            foreach (['RECEIVE', 'TRANSFER', 'HANDOVER', 'INGEST'] as $t) {
                $txByDay[$t] = array_fill_keys($days, 0);
            }
            foreach ($txDayRows as $r) {
                $type = $r['transaction_type'];
                if (isset($txByDay[$type]) && isset($txByDay[$type][$r['d']])) {
                    $txByDay[$type][$r['d']] = (int) $r['cnt'];
                }
            }
            $counts['chart_tx_series'] = [];
            foreach ($txByDay as $type => $series) {
                $counts['chart_tx_series'][$type] = array_values($series);
            }

            // Bookings per day (booking_date)
            $bkRows = $db->table('bookings')
                ->select("booking_date AS d, COUNT(*) AS cnt")
                ->where('booking_date >=', $days[0])
                ->where('booking_date <=', end($days))
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->groupBy('d')
                ->get()->getResultArray();
            $bookingsByDay = array_fill_keys($days, 0);
            foreach ($bkRows as $r) {
                if (isset($bookingsByDay[$r['d']])) $bookingsByDay[$r['d']] = (int) $r['cnt'];
            }
            $counts['chart_bookings'] = array_values($bookingsByDay);

            // Schedule items per day (created_at)
            $schRows = $db->table('schedule_items')
                ->select("DATE(created_at) AS d, COUNT(*) AS cnt")
                ->where('created_at >=', $days[0] . ' 00:00:00')
                ->where('deleted_at', null)
                ->groupBy('d')
                ->get()->getResultArray();
            $schedulesByDay = array_fill_keys($days, 0);
            foreach ($schRows as $r) {
                if (isset($schedulesByDay[$r['d']])) $schedulesByDay[$r['d']] = (int) $r['cnt'];
            }
            $counts['chart_schedule_items'] = array_values($schedulesByDay);

            // Master schedules per day (created_at)
            $schMasterRows = $db->table('schedules')
                ->select("DATE(created_at) AS d, COUNT(*) AS cnt")
                ->where('created_at >=', $days[0] . ' 00:00:00')
                ->where('deleted_at', null)
                ->groupBy('d')
                ->get()->getResultArray();
            $schedulesMasterByDay = array_fill_keys($days, 0);
            foreach ($schMasterRows as $r) {
                if (isset($schedulesMasterByDay[$r['d']])) $schedulesMasterByDay[$r['d']] = (int) $r['cnt'];
            }
            $counts['chart_schedules'] = array_values($schedulesMasterByDay);

            // Commercials per day (created_at)
            $comRows = $db->table('commercials')
                ->select("DATE(created_at) AS d, COUNT(*) AS cnt")
                ->where('created_at >=', $days[0] . ' 00:00:00')
                ->where('deleted_at', null)
                ->groupBy('d')
                ->get()->getResultArray();
            $commercialsByDay = array_fill_keys($days, 0);
            foreach ($comRows as $r) {
                if (isset($commercialsByDay[$r['d']])) $commercialsByDay[$r['d']] = (int) $r['cnt'];
            }
            $counts['chart_commercials'] = array_values($commercialsByDay);

            // Schedule items created in the last 24h AND scheduled for today
            // (Commercial name comes via the parent schedule's commercial FK.)
            $cutoff24h = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $todayDate = date('Y-m-d');

            $recentScheduleItems = $db->table('schedule_items si')
                ->select("si.scd_id AS id, si.sched_date, si.created_at, si.published,
                          s.sched_id  AS schedule_id, s.usched_id AS schedule_ref,
                          c.com_id    AS commercial_id, c.name    AS commercial_name,
                          cl.name     AS client_name,
                          pr.name     AS program_name,
                          sp.name     AS spot_name,
                          CONCAT(u.first_name, ' ', u.last_name) AS added_by_name")
                ->join('schedules s',    's.sched_id   = si.sched_id', 'left')
                ->join('commercials c',  'c.com_id     = s.commercial', 'left')
                ->join('clients cl',     'cl.client_id = c.client',     'left')
                ->join('programs pr',    'pr.prog_id   = s.program',    'left')
                ->join('spots sp',       'sp.spot_id   = si.spot',      'left')
                ->join('users u',        'u.id         = si.added_by',  'left')
                ->where('si.created_at >=', $cutoff24h)
                ->where('si.sched_date',    $todayDate)
                ->where('si.deleted_at', null)
                ->orderBy('si.created_at', 'DESC')
                ->get()->getResultArray();

            $recentAdditions = [];
            foreach ($recentScheduleItems as $r) {
                $recentAdditions[] = [
                    'commercial'    => $r['commercial_name'] ?: '(unlinked)',
                    'commercial_href' => $r['commercial_id'] ? base_url('commercials/edit/' . $r['commercial_id']) : null,
                    'schedule_ref'  => $r['schedule_ref'] ?: ($r['schedule_id'] ? '#' . $r['schedule_id'] : '—'),
                    'schedule_href' => $r['schedule_id'] ? base_url('schedules/edit/' . $r['schedule_id']) : null,
                    'client'        => $r['client_name']  ?: '—',
                    'program'       => $r['program_name'] ?: '—',
                    'spot'          => $r['spot_name']    ?: '—',
                    'sched_date'    => $r['sched_date'],
                    'published'     => (int) $r['published'],
                    'added_by'      => $r['added_by_name'],
                    'created_at'    => $r['created_at'],
                    'href'          => base_url('daily-schedule/' . ($r['sched_date'] ?? '')),
                ];
            }
            $counts['recent_schedule_additions'] = $recentAdditions;

            // Recent chip transactions (last 8) for activity feed
            $counts['recent_transactions'] = $db->table('chip_transactions ct')
                ->select("ct.id, ct.transaction_type, ct.to_location, ct.created_at,
                          fp.name AS from_name, tp.name AS to_name,
                          CONCAT(u.first_name, ' ', u.last_name) AS handler_name,
                          s.id AS session_id, s.title AS session_title,
                          (SELECT COUNT(*) FROM transaction_items ti WHERE ti.transaction_id = ct.id) AS chip_count")
                ->join('participants fp',   'fp.id = ct.from_participant_id', 'left')
                ->join('participants tp',   'tp.id = ct.to_participant_id',   'left')
                ->join('users u',           'u.id  = ct.handled_by',          'left')
                ->join('ingest_sessions s', 's.id  = ct.ingest_session_id',   'left')
                ->orderBy('ct.id', 'DESC')
                ->limit(8)
                ->get()->getResultArray();

            // Chip distribution donut (already calculated above; just compute unassigned)
            $counts['chips_unassigned'] = max(0, $counts['chips_total']
                - $counts['chips_library']
                - $counts['chips_producers']
                - $counts['chips_digital_unit']);

            return $counts;
        }
    }
}
