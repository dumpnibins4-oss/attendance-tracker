<?php
    // --- PLACEHOLDER DATA (replace with DB queries later) ---
    $hoursRendered   = 312;
    $hoursRequired   = 600;
    $timeIn          = '08:02 AM';
    $timeOut         = null;
    $hasJournalToday = false;
    $weeklyStatus    = ['present', 'present', 'late', 'present', 'absent', 'none'];
    $percent = min(100, round($hoursRendered / $hoursRequired * 100));
    $remaining = $hoursRequired - $hoursRendered;
    $colors = [
        'present' => 'bg-green-500',
        'absent'  => 'bg-red-500',
        'late'    => 'bg-yellow-400',
        'none'    => 'bg-zinc-600',
    ];
    $days = ['M','T','W','Th','F','S'];
?>

<div class="flex flex-col items-center justify-start w-full h-full gap-5">
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-start w-full h-auto">
            <h1 class="text-3xl font-bold text-orange-500">Dashboard</h1>
        </div>
        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php echo date('l, F j, Y') ?>
            </p>
            <div class="border border-zinc-500 dark:border-zinc-400 rounded-full h-1 w-1"></div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Attendance Overview
            </p>
        </div>
    </div>
    <div class="grid grid-cols-2 w-full h-auto gap-5">
        <!-- Quick Actions -->
        <div class="flex flex-col items-center justify-between w-full h-40 bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-3">
            <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                <i class="fa-solid fa-computer-mouse text-white text-xl"></i>
                <p class="text-white text-xl font-medium">Quick Actions</p>
            </div>
            <div class="grid grid-cols-3 items-center justify-start w-full h-auto gap-3">
                <button class="flex flex-row items-center justify-start h-15 w-full rounded-3xl p-3 bg-zinc-700 gap-2 hover:bg-zinc-600 hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    <div class="flex flex-row items-center justify-center h-10 w-10 rounded-lg bg-zinc-600">
                        <i class="fa-solid fa-plus text-zinc-200 text-xl"></i>
                    </div>
                    <p class="text-zinc-200 text-md font-medium">Time In</p>
                </button>
                <button class="flex flex-row items-center justify-start h-15 w-full rounded-3xl p-3 bg-zinc-700 gap-2 hover:bg-zinc-600 hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    <div class="flex flex-row items-center justify-center h-10 w-10 rounded-lg bg-zinc-600">
                        <i class="fa-solid fa-file-lines text-zinc-200 text-xl"></i>
                    </div>
                    <p class="text-zinc-200 text-md font-medium">Generate Report</p>
                </button>
                <button class="flex flex-row items-center justify-start h-15 w-full rounded-3xl p-3 bg-zinc-700 gap-2 hover:bg-zinc-600 hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    <div class="flex flex-row items-center justify-center h-10 w-10 rounded-lg bg-zinc-600">
                        <i class="fa-solid fa-bullhorn text-zinc-200 text-xl"></i>
                    </div>
                    <p class="text-zinc-200 text-md font-medium">Create Announcement</p>
                </button>
            </div>
        </div>
        <!-- End of Quick Actions -->

        <!-- OJT Progress Card -->
        <div class="flex flex-col items-start justify-between w-full h-40 bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-3">
            <!-- Header -->
            <div class="flex flex-row items-center justify-between w-full">
                <div class="flex flex-row items-center gap-3">
                    <i class="fa-solid fa-clock text-white text-xl"></i>
                    <p class="text-white text-xl font-medium">OJT Progress</p>
                </div>
                <?php if (!$hasJournalToday): ?>
                <span class="text-xs bg-orange-500 text-white px-3 py-1 rounded-full font-medium animate-pulse">
                    Journal pending
                </span>
                <?php endif; ?>
            </div>

            <!-- Body -->
            <div class="flex flex-row items-center justify-between w-full flex-1 gap-6">
                <!-- Hours + progress bar -->
                <div class="flex flex-col justify-center gap-1.5">
                    <p class="text-3xl font-bold text-white leading-none">
                        <?php echo $hoursRendered ?>
                        <span class="text-sm font-normal text-zinc-300">/ <?php echo $hoursRequired ?> hrs</span>
                    </p>
                    <div class="w-44 h-2 bg-zinc-600 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500 rounded-full" style="width: <?php echo $percent ?>%"></div>
                    </div>
                    <p class="text-xs text-zinc-300"><?php echo $remaining ?> hrs remaining &mdash; <?php echo $percent ?>% done</p>
                </div>

                <!-- Divider -->
                <div class="self-stretch w-px bg-zinc-600"></div>

                <!-- Time-in/out + weekly pills -->
                <div class="flex flex-col justify-center gap-2">
                    <!-- Today -->
                    <div class="flex flex-row items-center gap-3">
                        <div class="flex flex-row items-center gap-1.5">
                            <i class="fa-solid fa-right-to-bracket text-zinc-300 text-xs"></i>
                            <p class="text-white text-sm font-medium"><?php echo $timeIn ?? '—' ?></p>
                        </div>
                        <div class="flex flex-row items-center gap-1.5">
                            <i class="fa-solid fa-right-from-bracket text-zinc-300 text-xs"></i>
                            <p class="text-white text-sm font-medium"><?php echo $timeOut ?? '—' ?></p>
                        </div>
                    </div>
                    <!-- Weekly pills -->
                    <div class="flex flex-row gap-1.5">
                        <?php foreach ($days as $i => $day): ?>
                        <?php $s = $weeklyStatus[$i] ?? 'none'; ?>
                        <div class="flex flex-col items-center gap-1">
                            <div class="h-2 w-6 rounded-full <?php echo $colors[$s] ?>"></div>
                            <p class="text-zinc-400 text-xs"><?php echo $day ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of OJT Progress Card -->
    </div>
</div>