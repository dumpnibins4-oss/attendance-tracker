<?php
    include_once(__DIR__ . '/../../../server/db/conn.php');

    $userId = $_SESSION['current_user']['id'] ?? 0;

    // Fetch user progress
    $stmtUser = $conn->prepare("SELECT accumulated_hours, required_hours FROM att_track_users WHERE id = ?");
    $stmtUser->execute([$userId]);
    $uData = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $hoursRendered = floatval($uData['accumulated_hours'] ?? 0);
    $hoursRequired = floatval($uData['required_hours'] ?? 600);
    $percent = $hoursRequired > 0 ? min(100, round(($hoursRendered / $hoursRequired) * 100)) : 0;
    $remaining = max(0, $hoursRequired - $hoursRendered);

    // Format hours as HH:MM for display
    $renderedH = floor($hoursRendered);
    $renderedM = round(($hoursRendered - $renderedH) * 60);
    $renderedFmt = str_pad($renderedH, 2, '0', STR_PAD_LEFT) . ':' . str_pad($renderedM, 2, '0', STR_PAD_LEFT);

    $remainH = floor($remaining);
    $remainM = round(($remaining - $remainH) * 60);
    $remainFmt = str_pad($remainH, 2, '0', STR_PAD_LEFT) . ':' . str_pad($remainM, 2, '0', STR_PAD_LEFT);

    $requiredH = floor($hoursRequired);
    $requiredM = round(($hoursRequired - $requiredH) * 60);
    $requiredFmt = str_pad($requiredH, 2, '0', STR_PAD_LEFT) . ':' . str_pad($requiredM, 2, '0', STR_PAD_LEFT);

    // Fetch today's record
    $stmtToday = $conn->prepare("
        SELECT id, time_in, time_out, journal 
        FROM att_track_attendance 
        WHERE user_id = ? AND CAST(time_in AS DATE) = CAST(GETDATE() AS DATE)
    ");
    $stmtToday->execute([$userId]);
    $todayRecord = $stmtToday->fetch(PDO::FETCH_ASSOC);

    $timeIn = $todayRecord && $todayRecord['time_in'] ? (new DateTime($todayRecord['time_in']))->format('h:i A') : null;
    $timeOut = $todayRecord && $todayRecord['time_out'] ? (new DateTime($todayRecord['time_out']))->format('h:i A') : null;
    $hasJournalToday = $todayRecord && !empty(trim($todayRecord['journal']));
    $journalText = $todayRecord ? ($todayRecord['journal'] ?? '') : '';
    $todayRecordId = $todayRecord['id'] ?? 0;

    // Fetch weekly status (Mon-Sat for current week)
    $weeklyStatus = ['none', 'none', 'none', 'none', 'none', 'none'];
    $days = ['M','T','W','Th','F','S'];
    
    // Approximation for this week (SQL Server)
    $stmtWeek = $conn->prepare("
        SELECT CAST(time_in AS DATE) as tDate, status
        FROM att_track_attendance
        WHERE user_id = ? AND time_in >= DATEADD(wk, DATEDIFF(wk, 0, GETDATE()), 0)
    ");
    $stmtWeek->execute([$userId]);
    while ($row = $stmtWeek->fetch(PDO::FETCH_ASSOC)) {
        $dayOfWeek = (new DateTime($row['tDate']))->format('N'); // 1 (Mon) to 7 (Sun)
        if ($dayOfWeek >= 1 && $dayOfWeek <= 6) {
            $weeklyStatus[$dayOfWeek - 1] = $row['status'] ?? 'present';
        }
    }

    $colors = [
        'present' => 'bg-green-500',
        'absent'  => 'bg-red-500',
        'late'    => 'bg-yellow-400',
        'none'    => 'bg-zinc-600',
    ];
?>

<div class="flex flex-col items-center justify-start w-full h-full gap-5">
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-start w-full h-auto">
            <h1 class="text-3xl font-bold text-accent">Dashboard</h1>
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
        <div class="flex flex-col items-center justify-between w-full h-40 bg-white dark:bg-zinc-800 rounded-4xl p-6 gap-3">
            <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                <i class="fa-solid fa-computer-mouse text-zinc-900 dark:text-white text-xl"></i>
                <p class="text-zinc-900 dark:text-white text-xl font-medium">Quick Actions</p>
            </div>
            <div class="grid grid-cols-3 items-center justify-start w-full h-auto gap-3">
                <button onclick="navigateTo('attendance')" class="group flex flex-row items-center justify-start h-15 w-full rounded-3xl p-3 dark:bg-zinc-700 bg-zinc-200 gap-2 dark:hover:bg-zinc-600 hover:bg-zinc-300 hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    <div class="flex flex-row items-center justify-center h-10 w-10 rounded-lg dark:bg-zinc-600 bg-zinc-300 group-hover:bg-zinc-400 dark:group-hover:bg-zinc-500 transition-all">
                        <i class="fa-solid fa-plus dark:text-zinc-200 text-zinc-900 text-xl"></i>
                    </div>
                    <p class="dark:text-zinc-200 text-zinc-900 text-md font-medium">Time In</p>
                </button>
                <button onclick="navigateTo('attendance')" class="group flex flex-row items-center justify-start h-15 w-full rounded-3xl p-3 dark:bg-zinc-700 bg-zinc-200 gap-2 dark:hover:bg-zinc-600 hover:bg-zinc-300 hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    <div class="flex flex-row items-center justify-center h-10 w-10 rounded-lg dark:bg-zinc-600 bg-zinc-300 group-hover:bg-zinc-400 dark:group-hover:bg-zinc-500 transition-all">
                        <i class="fa-solid fa-bowl-rice dark:text-zinc-200 text-zinc-900 text-xl"></i>
                    </div>
                    <p class="dark:text-zinc-200 text-zinc-900 text-md font-medium">Start Lunch Break</p>
                </button>
                <button class="group flex flex-row items-center justify-start h-15 w-full rounded-3xl p-3 dark:bg-zinc-700 bg-zinc-200 gap-2 dark:hover:bg-zinc-600 hover:bg-zinc-300 hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    <div class="flex flex-row items-center justify-center h-10 w-10 rounded-lg dark:bg-zinc-600 bg-zinc-300 group-hover:bg-zinc-400 dark:group-hover:bg-zinc-500 transition-all">
                        <i class="fa-solid fa-file-lines dark:text-zinc-200 text-zinc-900 text-xl"></i>
                    </div>
                    <p class="dark:text-zinc-200 text-zinc-900 text-md font-medium">Generate Report</p>
                </button>
            </div>
        </div>
        <!-- End of Quick Actions -->

        <!-- OJT Progress Card -->
        <div class="flex flex-col items-start justify-between w-full h-40 bg-white dark:bg-zinc-800 rounded-4xl p-6 gap-3">
            <!-- Header -->
            <div class="flex flex-row items-center justify-between w-full">
                <div class="flex flex-row items-center gap-3">
                    <i class="fa-solid fa-clock text-zinc-900 dark:text-white text-xl"></i>
                    <p class="text-zinc-900 dark:text-white text-xl font-medium">OJT Progress</p>
                </div>
                <?php if (!$hasJournalToday): ?>
                <span class="text-xs bg-accent text-white px-3 py-1 rounded-full font-medium animate-pulse">
                    Journal pending
                </span>
                <?php endif; ?>
            </div>

            <!-- Body -->
            <div class="flex flex-row items-center justify-between w-full flex-1 gap-6">
                <!-- Hours + progress bar -->
                <div class="flex flex-col justify-center gap-1.5">
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white leading-none font-mono">
                        <?php echo $renderedFmt ?>
                        <span class="text-sm font-normal dark:text-zinc-300 text-zinc-900 font-sans">/ <?php echo $requiredFmt ?> hrs</span>
                    </p>
                    <div class="w-44 h-2 bg-zinc-600 dark:bg-zinc-700 rounded-full overflow-hidden">
                        <div class="h-full bg-accent rounded-full" style="width: <?php echo $percent ?>%"></div>
                    </div>
                    <p class="text-xs dark:text-zinc-300 text-zinc-900"><?php echo $remainFmt ?> hrs remaining &mdash; <?php echo $percent ?>% done</p>
                </div>

                <!-- Divider -->
                <div class="self-stretch w-px bg-zinc-600"></div>

                <!-- Time-in/out + weekly pills -->
                <div class="flex flex-col justify-center gap-2">
                    <!-- Today -->
                    <div class="flex flex-row items-center gap-3">
                        <div class="flex flex-row items-center gap-1.5">
                            <i class="fa-solid fa-right-to-bracket dark:text-zinc-300 text-zinc-900 text-xs"></i>
                            <p class="text-zinc-900 dark:text-white text-sm font-medium"><?php echo $timeIn ?? '—' ?></p>
                        </div>
                        <div class="flex flex-row items-center gap-1.5">
                            <i class="fa-solid fa-right-from-bracket dark:text-zinc-300 text-zinc-900 text-xs"></i>
                            <p class="text-zinc-900 dark:text-white text-sm font-medium"><?php echo $timeOut ?? '—' ?></p>
                        </div>
                    </div>
                    <!-- Weekly pills -->
                    <div class="flex flex-row gap-1.5">
                        <?php foreach ($days as $i => $day): ?>
                        <?php $s = $weeklyStatus[$i] ?? 'none'; ?>
                        <div class="flex flex-col items-center gap-1">
                            <div class="h-2 w-6 rounded-full <?php echo $colors[$s] ?>"></div>
                            <p class="dark:text-zinc-400 text-zinc-900 text-xs"><?php echo $day ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of OJT Progress Card -->
         
        <!-- Attendance Warning Card -->
        <div class="flex flex-col items-center justify-start w-full h-90 bg-white dark:bg-zinc-800 rounded-4xl p-6 gap-5">
            <div class="flex flex-row items-center justify-between w-full h-auto">
                <div class="flex flex-row items-center justify-start flex-1 h-auto gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-zinc-900 dark:text-white text-xl"></i>
                    <p class="text-zinc-900 dark:text-white text-xl font-medium">Tardiness & Absence Record</p>
                </div>
                <button class="text-zinc-900 dark:text-white text-sm font-medium hover:text-accent hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    See All
                </button>
            </div>
            <div class="flex flex-col items-center justify-start w-full h-auto gap-3">
                <div class="flex flex-row items-center justify-between w-full h-20 bg-zinc-200 dark:bg-zinc-700 shadow-md rounded-4xl px-5 py-3 border-l-4 border-red-500">
                    <div class="flex flex-col items-center justify-between">
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <i class="fa-solid fa-clock text-zinc-900 dark:text-white text-lg"></i>
                            <p class="text-zinc-900 dark:text-white text-lg font-medium">Absent</p>
                            <div class="h-4 w-0 border border-zinc-900 dark:border-white/50 rounded-full"></div>
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">2024-01-01</p>
                        </div>
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">You failed to report for duty.</p>
                        </div>
                    </div>
                    <button class="flex items-center justify-center h-10 w-10 rounded-full bg-green-500/20 hover:bg-green-500 hover:scale-105 hover:shadow-lg cursor-pointer transition-all group/button">
                        <i class="fa-solid fa-check text-green-500 text-xl group-hover/button:text-white transition-all"></i>
                    </button>
                </div>
                <div class="flex flex-row items-center justify-between w-full h-20 bg-zinc-200 dark:bg-zinc-700 shadow-md rounded-4xl px-5 py-3 border-l-4 border-yellow-500">
                    <div class="flex flex-col items-center justify-between">
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <i class="fa-solid fa-clock text-zinc-900 dark:text-white text-lg"></i>
                            <p class="text-zinc-900 dark:text-white text-lg font-medium">Tardiness</p>
                            <div class="h-4 w-0 border border-zinc-900 dark:border-white/50 rounded-full"></div>
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">2024-01-01</p>
                        </div>
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">You came back 19 minutes late from your lunch break.</p>
                        </div>
                    </div>
                    <button class="flex items-center justify-center h-10 w-10 rounded-full bg-green-500/20 hover:bg-green-500 hover:scale-105 hover:shadow-lg cursor-pointer transition-all group/button">
                        <i class="fa-solid fa-check text-green-500 text-xl group-hover/button:text-white transition-all"></i>
                    </button>
                </div>
                <div class="flex flex-row items-center justify-between w-full h-20 bg-zinc-200 dark:bg-zinc-700 shadow-md rounded-4xl px-5 py-3 border-l-4 border-yellow-500">
                    <div class="flex flex-col items-center justify-between">
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <i class="fa-solid fa-clock text-zinc-900 dark:text-white text-lg"></i>
                            <p class="text-zinc-900 dark:text-white text-lg font-medium">Tardiness</p>
                            <div class="h-4 w-0 border border-zinc-900 dark:border-white/50 rounded-full"></div>
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">2024-01-01</p>
                        </div>
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">You came back 15 minutes late from your lunch break.</p>
                        </div>
                    </div>
                    <button class="flex items-center justify-center h-10 w-10 rounded-full bg-green-500/20 hover:bg-green-500 hover:scale-105 hover:shadow-lg cursor-pointer transition-all group/button">
                        <i class="fa-solid fa-check text-green-500 text-xl group-hover/button:text-white transition-all"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- End of Attendance Warning Card -->

        <!-- Recent Activities Card -->
        <div class="flex flex-col items-center justify-start w-full h-90 bg-white dark:bg-zinc-800 rounded-4xl p-6 gap-5">
            <div class="flex flex-row items-center justify-between w-full h-auto">
                <div class="flex flex-row items-center justify-start flex-1 h-auto gap-3">
                    <i class="fa-solid fa-person-walking text-zinc-900 dark:text-white text-xl"></i>
                    <p class="text-zinc-900 dark:text-white text-xl font-medium">Recent Activities</p>
                </div>
                <button class="text-zinc-900 dark:text-white text-sm font-medium hover:text-accent hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    See All
                </button>
            </div>
            <div class="flex flex-col items-center justify-start w-full h-auto gap-3">
                <div class="flex flex-row items-center justify-between w-full h-20 bg-zinc-200 dark:bg-zinc-700 shadow-md rounded-4xl px-5 py-3 border-l-4 border-green-500">
                    <div class="flex flex-col items-center justify-between">
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <i class="fa-solid fa-door-open text-zinc-900 dark:text-white text-lg"></i>
                            <p class="text-zinc-900 dark:text-white text-lg font-medium">Time In</p>
                            <div class="h-4 w-0 border border-zinc-900 dark:border-white/50 rounded-full"></div>
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">Morning</p>
                        </div>
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                            <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">7 April, 2026 - 8:00 AM</p>
                        </div>
                    </div>
                    <button class="flex items-center justify-center h-10 w-10 rounded-full bg-green-500/20 hover:bg-green-500 hover:scale-105 hover:shadow-lg cursor-pointer transition-all group/button">
                        <i class="fa-solid fa-check text-green-500 text-xl group-hover/button:text-white transition-all"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- End of Recent Activities Card -->

        <!-- Journal Card -->
        <div class="col-span-2 flex flex-col items-center justify-start w-full flex-1 bg-white dark:bg-zinc-800 rounded-4xl p-6 gap-5">
            <div class="flex flex-row items-center justify-between w-full h-auto">
                <div class="flex flex-row items-center justify-start flex-1 h-auto gap-3">
                    <i class="fa-solid fa-book text-zinc-900 dark:text-white text-xl"></i>
                    <p class="text-zinc-900 dark:text-white text-xl font-medium">Today's Journal</p>
                </div>
            </div>
            <div class="flex flex-col items-center justify-start w-full h-auto gap-3">
                <?php if (!$timeIn): ?>
                    <!-- Not timed in yet -->
                    <div class="flex flex-row items-center justify-between w-full h-20 bg-zinc-200 dark:bg-zinc-700 shadow-md rounded-4xl px-5 py-3 border-l-4 border-zinc-500">
                        <div class="flex flex-col items-center justify-between">
                            <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                                <i class="fa-solid fa-lock text-zinc-900 dark:text-white text-lg"></i>
                                <p class="text-zinc-900 dark:text-white text-lg font-medium">Locked</p>
                                <div class="h-4 w-0 border border-zinc-900 dark:border-white/50 rounded-full"></div>
                                <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">Time In to unlock your journal</p>
                            </div>
                        </div>
                        <button disabled class="flex items-center justify-center px-5 h-10 rounded-full bg-zinc-500/20 text-zinc-500 cursor-not-allowed opacity-50 gap-2">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span class="font-medium text-sm">Write</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="flex flex-row items-center justify-between w-full h-20 bg-zinc-200 dark:bg-zinc-700 shadow-md rounded-4xl px-5 py-3 border-l-4 <?php echo $hasJournalToday ? 'border-green-500' : 'border-yellow-500'; ?>">
                        <div class="flex flex-col items-center justify-between">
                            <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                                <i class="fa-solid <?php echo $hasJournalToday ? 'fa-check text-green-500' : 'fa-file-pen dark:text-white text-zinc-900'; ?> text-lg"></i>
                                <p class="text-zinc-900 dark:text-white text-lg font-medium"><?php echo $hasJournalToday ? 'Entry Saved' : 'Pending Entry'; ?></p>
                                <div class="h-4 w-0 border border-zinc-900 dark:border-white/50 rounded-full"></div>
                                <p class="dark:text-white/70 text-zinc-900 text-sm font-medium">What did you learn today?</p>
                            </div>
                            <div class="flex flex-row items-center justify-start w-full h-auto gap-3">
                                <p class="dark:text-white/70 text-zinc-900 text-sm font-medium truncate w-[400px]">
                                    <?php echo $hasJournalToday ? htmlspecialchars($journalText) : 'You have not submitted a journal entry for today yet.'; ?>
                                </p>
                            </div>
                        </div>
                        <button onclick="openJournalModal(<?php echo $todayRecordId; ?>, <?php echo htmlspecialchars(json_encode($journalText)); ?>)" class="flex items-center justify-center px-5 h-10 rounded-full bg-accent/20 hover:bg-accent hover:scale-105 hover:shadow-lg cursor-pointer transition-all group/button gap-2">
                            <i class="fa-solid <?php echo $hasJournalToday ? 'fa-pen' : 'fa-pen-to-square'; ?> text-accent group-hover/button:text-white transition-all"></i>
                            <span class="text-accent font-medium group-hover/button:text-white transition-all text-sm"><?php echo $hasJournalToday ? 'Edit' : 'Write'; ?></span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- End of Journal Card -->
    </div>
</div>

<script>
    // Journal Modal Function
    function openJournalModal(recordId, currentText = '') {
        Swal.fire({
            title: 'Daily Journal',
            input: 'textarea',
            inputLabel: 'What did you achieve or learn today?',
            inputValue: currentText,
            inputPlaceholder: 'Start typing here...',
            inputAttributes: {
                'aria-label': 'Type your journal entry here'
            },
            showCancelButton: true,
            confirmButtonText: 'Save Entry',
            confirmButtonColor: 'var(--color-accent)',
            showLoaderOnConfirm: true,
            background: document.documentElement.classList.contains('dark') ? '#27272a' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
            preConfirm: async (text) => {
                try {
                    const fd = new FormData();
                    fd.append('action', 'save_journal');
                    fd.append('record_id', recordId);
                    fd.append('journal', text);
                    
                    const res = await fetch('../server/api/attendance_api.php', { method: 'POST', body: fd });
                    if (!res.ok) throw new Error(res.statusText);
                    
                    const data = await res.json();
                    if (!data.success) throw new Error(data.message);
                    return data;
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Your journal entry has been saved.',
                    timer: 1500,
                    showConfirmButton: false,
                    background: document.documentElement.classList.contains('dark') ? '#27272a' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                }).then(() => {
                    if (typeof navigateTo === "function") navigateTo("dashboard");
                    else location.reload();
                });
            }
        });
    }
</script>
