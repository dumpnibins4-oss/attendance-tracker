<?php
    // --- PLACEHOLDER DATA (replace with DB queries later) ---
    $attendanceRecords = [
        ['date' => '2026-04-07', 'day' => 'Monday',    'timeIn' => '08:02 AM', 'timeOut' => '05:00 PM', 'status' => 'present', 'hours' => '8.97'],
        ['date' => '2026-04-04', 'day' => 'Friday',    'timeIn' => '08:00 AM', 'timeOut' => '05:00 PM', 'status' => 'present', 'hours' => '9.00'],
        ['date' => '2026-04-03', 'day' => 'Thursday',  'timeIn' => '08:15 AM', 'timeOut' => '05:00 PM', 'status' => 'late',    'hours' => '8.75'],
        ['date' => '2026-04-02', 'day' => 'Wednesday', 'timeIn' => '08:00 AM', 'timeOut' => '05:00 PM', 'status' => 'present', 'hours' => '9.00'],
        ['date' => '2026-04-01', 'day' => 'Tuesday',   'timeIn' => null,       'timeOut' => null,       'status' => 'absent',  'hours' => '0.00'],
        ['date' => '2026-03-31', 'day' => 'Monday',    'timeIn' => '08:00 AM', 'timeOut' => '05:00 PM', 'status' => 'present', 'hours' => '9.00'],
    ];

    $statusBadge = [
        'present' => 'bg-green-500/20 text-green-400',
        'late'    => 'bg-yellow-500/20 text-yellow-400',
        'absent'  => 'bg-red-500/20 text-red-400',
    ];

    $statusIcon = [
        'present' => 'fa-circle-check',
        'late'    => 'fa-clock',
        'absent'  => 'fa-circle-xmark',
    ];
?>

<div class="flex flex-col items-center justify-start w-full h-full gap-5 page-content">
    <!-- Page Header -->
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-between w-full h-auto">
            <h1 class="text-3xl font-bold text-orange-500">Attendance</h1>
            <div class="flex flex-row items-center gap-3">
                <button class="flex items-center gap-2 px-4 py-2 rounded-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium transition-all hover:scale-105 cursor-pointer shadow-lg shadow-orange-500/20">
                    <i class="fa-solid fa-file-export"></i>
                    Export
                </button>
            </div>
        </div>
        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php echo date('l, F j, Y') ?>
            </p>
            <div class="border border-zinc-500 dark:border-zinc-400 rounded-full h-1 w-1"></div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Attendance Records
            </p>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
        <div class="flex flex-row items-center justify-between w-full">
            <div class="flex flex-row items-center gap-3">
                <i class="fa-solid fa-calendar-days text-white text-xl"></i>
                <p class="text-white text-xl font-medium">Attendance Log</p>
            </div>
            <!-- Filter Controls -->
            <div class="flex flex-row items-center gap-3">
                <div class="flex items-center gap-2 bg-zinc-600 dark:bg-zinc-700 rounded-full px-4 py-2">
                    <i class="fa-solid fa-search text-zinc-400 text-sm"></i>
                    <input type="text" placeholder="Search date..." class="bg-transparent text-white text-sm outline-none placeholder-zinc-400 w-32" />
                </div>
                <select class="bg-zinc-600 dark:bg-zinc-700 text-white text-sm rounded-full px-4 py-2 outline-none cursor-pointer">
                    <option>This Week</option>
                    <option>This Month</option>
                    <option>All Time</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="w-full overflow-hidden rounded-2xl">
            <table class="w-full">
                <thead>
                    <tr class="bg-zinc-600 dark:bg-zinc-900/50">
                        <th class="text-left text-zinc-300 text-xs font-medium uppercase tracking-wider px-5 py-3">Date</th>
                        <th class="text-left text-zinc-300 text-xs font-medium uppercase tracking-wider px-5 py-3">Day</th>
                        <th class="text-left text-zinc-300 text-xs font-medium uppercase tracking-wider px-5 py-3">Time In</th>
                        <th class="text-left text-zinc-300 text-xs font-medium uppercase tracking-wider px-5 py-3">Time Out</th>
                        <th class="text-left text-zinc-300 text-xs font-medium uppercase tracking-wider px-5 py-3">Status</th>
                        <th class="text-left text-zinc-300 text-xs font-medium uppercase tracking-wider px-5 py-3">Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $index => $record): ?>
                    <tr class="border-b border-zinc-600/30 hover:bg-zinc-600/30 dark:hover:bg-zinc-700/30 transition-colors">
                        <td class="px-5 py-4 text-white text-sm font-medium"><?php echo date('M j, Y', strtotime($record['date'])) ?></td>
                        <td class="px-5 py-4 text-zinc-300 text-sm"><?php echo $record['day'] ?></td>
                        <td class="px-5 py-4 text-zinc-300 text-sm"><?php echo $record['timeIn'] ?? '—' ?></td>
                        <td class="px-5 py-4 text-zinc-300 text-sm"><?php echo $record['timeOut'] ?? '—' ?></td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium <?php echo $statusBadge[$record['status']] ?>">
                                <i class="fa-solid <?php echo $statusIcon[$record['status']] ?> text-[10px]"></i>
                                <?php echo ucfirst($record['status']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-zinc-300 text-sm"><?php echo $record['hours'] ?> hrs</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
