<?php
    // --- Fetch real data from the database ---
    include_once(__DIR__ . '/../../../server/db/conn.php');

    $userId = $_SESSION['current_user']['id'] ?? 0;
    $biometricsId = $_SESSION['current_user']['biometrics_id'] ?? '';

    if ($biometricsId) {
        try {
            $conn->exec("ALTER TABLE att_track_attendance ADD in_location NVARCHAR(255), out_location NVARCHAR(255)");
        } catch (Exception $e) { /* Already exists */ }

        try {
            // Sync logs from biometrics staging table to attendance table
            $syncStmt = $conn->prepare("
                WITH DailyBounds AS (
                    SELECT 
                        CAST(log_datetime AS DATE) as log_date,
                        MIN(CASE WHEN device_name = 'GO Entrance Door Access' THEN log_datetime ELSE NULL END) as time_in,
                        MAX(log_datetime) as time_out
                    FROM [LRNPH_HIK_BIO].[dbo].hik_logs_staging
                    WHERE emp_id = ?
                    GROUP BY CAST(log_datetime AS DATE)
                )
                SELECT 
                    db.log_date,
                    db.time_in,
                    db.time_out,
                    l_in.device_name as in_location,
                    l_out.device_name as out_location
                FROM DailyBounds db
                LEFT JOIN [LRNPH_HIK_BIO].[dbo].hik_logs_staging l_in
                    ON l_in.emp_id = ? AND l_in.log_datetime = db.time_in AND l_in.device_name = 'GO Entrance Door Access'
                LEFT JOIN (
                    SELECT emp_id, log_datetime, MAX(device_name) as device_name
                    FROM [LRNPH_HIK_BIO].[dbo].hik_logs_staging
                    GROUP BY emp_id, log_datetime
                ) l_out ON l_out.emp_id = ? AND l_out.log_datetime = db.time_out
            ");
            $syncStmt->execute([$biometricsId, $biometricsId, $biometricsId]);
            $logs = $syncStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($logs as $log) {
                $timeIn = $log['time_in'];
                $timeOut = $log['time_out'];
                if ($timeIn === $timeOut || !$timeIn) {
                    $timeOut = $timeOut ?: null; // Keep whatever logic makes sense, but ensure if they match, we clear one of them?
                    // Actually, if they only punch out, timeIn is null, timeOut is valid.
                    // If they only punch in, timeIn is valid, timeOut is same as timeIn.
                    if ($timeIn && $timeIn === $timeOut) {
                        $timeOut = null;
                    }
                }
                $dateOnly = $log['log_date'];

                $hours = 0;
                if ($timeIn && $timeOut) {
                    $diff = strtotime($timeOut) - strtotime($timeIn);
                    $hours = round($diff / 3600, 2);
                    if ($hours > 8) {
                        $hours = 8;
                    }
                }

                $status = 'present';
                if ($hours < 8) {
                    $status = 'incomplete';
                }
                if (!$timeIn && !$timeOut) {
                    $status = 'absent';
                }

                $inLoc = $log['in_location'] ?: '--';
                $outLoc = $log['out_location'] ?: '--';
                if (!$timeOut) {
                    $outLoc = '--'; // Only 1 log for the whole day, so exit point should equal '--' (null mapping)
                }

                $checkStmt = $conn->prepare("SELECT id FROM att_track_attendance WHERE user_id = ? AND CAST(time_in AS DATE) = ?");
                $checkStmt->execute([$userId, $dateOnly]);
                $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    $updateStmt = $conn->prepare("UPDATE att_track_attendance SET time_out = ?, hours = ?, status = ?, in_location = ?, out_location = ? WHERE id = ?");
                    $updateStmt->execute([$timeOut, $hours, $status, $inLoc, $outLoc, $existing['id']]);
                } else {
                    $insertStmt = $conn->prepare("INSERT INTO att_track_attendance (user_id, time_in, time_out, status, hours, journal, in_location, out_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertStmt->execute([$userId, $timeIn, $timeOut, $status, $hours, '', $inLoc, $outLoc]);
                }

                // Push locations to att_track_users as user requested
                $updateUser = $conn->prepare("UPDATE att_track_users SET in_location = ?, out_location = ? WHERE id = ?");
                $updateUser->execute([$inLoc, $outLoc, $userId]);
            }
        } catch (Exception $e) {
            // Silently handle error so UI doesn't break
        }
    }

    // Fetch synced attendance records
    $attStmt = $conn->prepare("SELECT * FROM att_track_attendance WHERE user_id = ? ORDER BY time_in DESC");
    $attStmt->execute([$userId]);
    $attendances = $attStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch total accumulated and required hours
    $sumStmt = $conn->prepare("SELECT SUM(hours) as sum_hours FROM att_track_attendance WHERE user_id = ?");
    $sumStmt->execute([$userId]);
    $totalHoursAcc = floatval($sumStmt->fetchColumn() ?: 0);

    // Update the local users table with accurate dynamic accumulation
    $updateAcc = $conn->prepare("UPDATE att_track_users SET accumulated_hours = ? WHERE id = ?");
    $updateAcc->execute([$totalHoursAcc, $userId]);

    $reqStmt = $conn->prepare("SELECT required_hours FROM att_track_users WHERE id = ?");
    $reqStmt->execute([$userId]);
    $requiredHours = floatval($reqStmt->fetchColumn() ?: 400);

    $overallProgress = 0;
    if ($requiredHours > 0) {
        $overallProgress = min(100, round(($totalHoursAcc / $requiredHours) * 100));
    }

    $todayStmt = $conn->prepare("SELECT TOP 1 time_in, time_out, hours FROM att_track_attendance WHERE user_id = ? AND CAST(time_in AS DATE) = CAST(GETDATE() AS DATE) ORDER BY time_in DESC");
    $todayStmt->execute([$userId]);
    $todayRecord = $todayStmt->fetch(PDO::FETCH_ASSOC);
    $todayTimeIn = $todayRecord['time_in'] ?? null;
    $todayTimeOut = $todayRecord['time_out'] ?? null;
    $todayHours = $todayRecord ? floatval($todayRecord['hours']) : 0;

    $accHrs = floor($totalHoursAcc);
    $accMins = round(($totalHoursAcc - $accHrs) * 60);
    if ($accMins >= 60) { $accHrs++; $accMins = 0; }
    $totalHoursAccStr = sprintf("%02d:%02d", $accHrs, $accMins);

    $reqHrs = floor($requiredHours);
    $reqMins = round(($requiredHours - $reqHrs) * 60);
    if ($reqMins >= 60) { $reqHrs++; $reqMins = 0; }
    $requiredHoursStr = sprintf("%02d:%02d", $reqHrs, $reqMins);
?>

    <div class="flex flex-col items-center justify-start w-full h-full gap-5">
        <div class="flex flex-col items-start justify-start w-full h-auto">
            <div class="flex flex-row items-center justify-start w-full h-auto">
                <h1 class="text-3xl font-bold text-accent">Attendance</h1>
            </div>
            <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    <?php echo date('l, F j, Y') ?>
                </p>
                <div class="border border-zinc-500 dark:border-zinc-400 rounded-full h-1 w-1"></div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Track your attendance
                </p>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="grid grid-cols-3 w-full flex-1 min-h-0 gap-5">
            <div class="col-span-2 flex flex-col items-center justify-start w-full h-full gap-5 bg-white dark:bg-zinc-800 rounded-4xl overflow-hidden">
                <div class="flex flex-row items-center justify-start w-full h-auto gap-2 pt-6 px-6">
                    <i class="fa-regular fa-calendar text-xl dark:text-white text-zinc-900"></i>
                    <p class="text-xl font-medium dark:text-white text-zinc-900">Attendance History</p>
                </div>
                <div class="grid grid-cols-2 w-full h-16 px-6">
                    <div class="flex flex-col items-center justify-start">
                        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
                            <i class="fa-regular fa-clock text-md dark:text-white text-zinc-900"></i>
                            <p class="text-md font-medium dark:text-white text-zinc-900">Today's Timer</p>
                        </div>
                        <div class="flex flex-row items-end justify-start w-full flex-1 gap-2">
                            <p id="live-timer" class="text-4xl font-medium text-green-500 font-mono tracking-wider">00:00:00</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-between">
                        <div class="flex flex-row items-center justify-between w-full h-auto gap-2">
                            <div class="flex flex-row items-center gap-2">
                                <i class="fa-solid fa-arrow-trend-up text-md dark:text-white text-zinc-900"></i>
                                <p class="text-md font-medium dark:text-white text-zinc-900">Progress</p>
                            </div>
                            <span class="text-xs font-bold text-zinc-500 dark:text-zinc-400">
                                <?= $totalHoursAccStr ?> / <?= $requiredHoursStr ?> Hrs (<?= $overallProgress ?>%)
                            </span>
                        </div>
                        <div class="flex flex-row items-end justify-start w-full h-8 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden relative mt-1 shadow-inner">
                            <div class="absolute top-0 bottom-0 left-0 bg-accent transition-all duration-1000" style="width: <?= $overallProgress ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-start w-full h-13 bg-zinc-200 dark:bg-zinc-700 rounded-t-xl shrink-0"></div>
                <div class="relative w-full flex-1 min-h-0">
                    <div class="absolute inset-0 p-5 overflow-y-auto thin-scrollbar shadow-[inset_0_20px_15px_-15px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_20px_15px_-15px_rgba(0,0,0,0.4)]">
                        <div class="grid grid-cols-4 content-start gap-3 w-full">
                            <?php if (count($attendances) > 0): ?>
                                <?php foreach ($attendances as $att): ?>
                                    <?php 
                                        $dateObj = new DateTime($att['time_in'] ?? $att['time_out'] ?? 'now');
                                        $dateStr = $dateObj->format('m-d-Y');
                                        
                                        $timeInStr = $att['time_in'] ? (new DateTime($att['time_in']))->format('h:i A') : '--:--';
                                        $timeOutStr = $att['time_out'] ? (new DateTime($att['time_out']))->format('h:i A') : '--:--';
                                        
                                        $statusClass = 'bg-accent/20 text-accent';
                                        $iconClass = 'fa-solid fa-check text-accent';
                                        $statusText = 'Complete';
                                        $cardClass = 'bg-accent/20 border-solid border-accent shadow-accent/20';

                                        if ($att['status'] === 'incomplete' || (!$att['time_out'])) {
                                            $statusClass = 'bg-yellow-500/20 text-yellow-500';
                                            $iconClass = 'fa-solid fa-triangle-exclamation text-yellow-500';
                                            $statusText = 'Incomplete';
                                            $cardClass = 'bg-zinc-200 dark:bg-zinc-700 border-dashed border-zinc-400 dark:border-zinc-500';
                                        } elseif ($att['status'] === 'absent') {
                                            $statusClass = 'bg-red-500/20 text-red-500';
                                            $iconClass = 'fa-solid fa-xmark text-red-500';
                                            $statusText = 'Absent';
                                            $cardClass = 'bg-zinc-200 dark:bg-zinc-700 border-dashed border-zinc-400 dark:border-zinc-500';
                                        }
                                        
                                        $bgClass = explode(' ', $statusClass)[0];
                                        $txtClass = explode(' ', $statusClass)[1];
                                    ?>
                                    <!-- Attendance Card -->
                                    <button class="att-card flex flex-col items-start justify-between w-full h-40 rounded-md border-2 p-5 shadow-md shadow-black/20 cursor-pointer hover:scale-105 transition-all duration-300 hover:shadow-black/40 <?= $cardClass ?>"
                                        data-id="<?= $att['id'] ?>"
                                        data-date="<?= $dateObj->format('l') ?>"
                                        data-date-full="<?= $dateStr ?>"
                                        data-times="<?= $timeInStr . ' - ' . $timeOutStr ?>"
                                        data-status-text="<?= $statusText ?>"
                                        data-status-bg="<?= $bgClass ?>"
                                        data-status-txt="<?= $txtClass ?>"
                                        data-hours="<?= $att['hours'] ?? 0 ?>"
                                        data-in-location="<?= htmlspecialchars($att['in_location'] ?: '--') ?>"
                                        data-out-location="<?= htmlspecialchars($att['out_location'] ?: '--') ?>"
                                        data-journal="<?= htmlspecialchars($att['journal'] ?: 'No journal entry available for this log.') ?>">
                                        
                                        <div class="flex flex-col w-full items-start">
                                            <h2 class="text-xl font-medium dark:text-white text-zinc-900"><?= $dateStr ?></h2>
                                            <p class="text-sm font-medium dark:text-white/60 text-zinc-900"><?= $timeInStr ?> - <?= $timeOutStr ?></p>
                                        </div>
                                        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
                                            <div class="flex items-center justify-center px-2 py-1 rounded-full <?= $bgClass ?>">
                                                <i class="<?= $iconClass ?> text-xl"></i>
                                            </div>
                                            <div class="flex items-center justify-center px-2 py-1 rounded-full <?= $bgClass ?>">
                                                <p class="text-sm font-medium <?= $txtClass ?>"><?= $statusText ?></p>
                                            </div>
                                        </div>
                                    </button>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-zinc-500 dark:text-zinc-400 col-span-4 text-center mt-5">No attendance records found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Attendance Summary -->
            <div class="w-full max-w-2xl bg-white dark:bg-zinc-800 rounded-3xl shadow-sm border border-zinc-100 dark:border-zinc-700 p-6 flex flex-col gap-6">
                <div class="flex flex-row items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-pie text-2xl text-zinc-900 dark:text-white"></i>
                        <h1 class="text-xl font-bold text-zinc-900 dark:text-white">Attendance Summary</h1>
                    </div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        Total: <span id="sum-total-hours" class="text-zinc-900 dark:text-white font-bold">0.00 Hours</span>
                    </p>
                </div>
                <div class="flex flex-col gap-5 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl p-5 border border-zinc-200 dark:border-zinc-700/50">
                    <div class="flex flex-row items-start justify-between w-full">
                        <div class="flex flex-col">
                            <h2 id="sum-day" class="text-xl font-bold text-zinc-900 dark:text-white">Monday</h2>
                            <p id="sum-times" class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-1">08:00 AM - 05:00 PM</p>
                        </div>
                        <div id="sum-badge" class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 text-sm font-bold rounded-full">
                            Complete
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div class="flex flex-col">
                            <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">OJT</h3>
                            <p class="text-base font-medium text-zinc-900 dark:text-white mt-1">
                                <?= $_SESSION['current_user']['lastName'] . ', ' . $_SESSION['current_user']['firstName'] . ' ' . substr($_SESSION['current_user']['middleName'], 0, 1) . '.' ?>
                            </p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Attendance ID</h3>
                            <p id="sum-att-id" class="text-base font-medium text-zinc-900 dark:text-white mt-1">#4</p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Entry Point</h3>
                            <p id="sum-entry-point" class="text-base font-medium text-zinc-900 dark:text-white mt-1">--</p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Exit Point</h3>
                            <p id="sum-exit-point" class="text-base font-medium text-zinc-900 dark:text-white mt-1">--</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 mt-2">
                        <div class="flex justify-between items-end w-full">
                            <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Completion</h3>
                            <span id="sum-percent" class="text-sm font-bold text-zinc-900 dark:text-white">100%</span>
                        </div>
                        <div class="w-full h-3 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div id="sum-bar" class="h-full bg-accent transition-all w-[100%]"></div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 w-full mt-2 bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-200 dark:border-yellow-500/30 p-4 rounded-xl">
                        <div class="flex flex-col gap-1">
                            <h3 class="text-xs font-bold text-yellow-800 dark:text-yellow-500 uppercase tracking-wider">Journal Entry</h3>
                            <p id="sum-journal" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 leading-relaxed break-words whitespace-pre-wrap max-h-32 overflow-y-auto thin-scrollbar pr-2">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.
                            </p>
                        </div>
                        <button id="add-journal-btn" class="w-full py-2.5 bg-accent text-white font-bold rounded-lg cursor-pointer hover:bg-accent-hover hover:shadow-md transition-all active:scale-95">
                            Add Journal Entry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
(() => {
    function initAttendanceCards() {
        const cards = document.querySelectorAll('.att-card');
        
        cards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove active state from all
                cards.forEach(c => {
                    c.classList.remove('bg-accent/20', 'border-accent', 'border-solid');
                    // If not Complete, restore its dashed border style
                    if (c.dataset.statusText !== 'Complete') {
                        c.classList.add('bg-zinc-200', 'dark:bg-zinc-700', 'border-dashed', 'border-zinc-400', 'dark:border-zinc-500');
                    }
                });

                // Add active state to clicked card
                this.classList.remove('bg-zinc-200', 'dark:bg-zinc-700', 'border-dashed', 'border-zinc-400', 'dark:border-zinc-500');
                this.classList.add('bg-accent/20', 'border-accent', 'border-solid');

                // Update Summary Panel
                document.getElementById('sum-day').innerText = this.dataset.date;
                document.getElementById('sum-times').innerText = this.dataset.times;
                
                const badge = document.getElementById('sum-badge');
                badge.innerText = this.dataset.statusText;
                badge.className = `px-3 py-1 text-sm font-bold rounded-full ${this.dataset.statusBg} ${this.dataset.statusTxt}`;

                document.getElementById('sum-att-id').innerText = '#' + this.dataset.id;
                
                const hours = parseFloat(this.dataset.hours || 0);
                const percent = Math.min(100, Math.round((hours / 8) * 100));
                document.getElementById('sum-percent').innerText = percent + '%';
                document.getElementById('sum-bar').style.width = percent + '%';
                
                document.getElementById('sum-total-hours').innerText = parseFloat(this.dataset.hours || 0).toFixed(2) + ' Hours';

                document.getElementById('sum-entry-point').innerText = this.dataset.inLocation;
                document.getElementById('sum-exit-point').innerText = this.dataset.outLocation;

                document.getElementById('sum-journal').innerText = this.dataset.journal;
            });
        });

        // Trigger click on first card initially
        if (cards.length > 0) {
            cards[0].click();
        }
    }

    // Call init if loaded directly or via SPA router
    initAttendanceCards();

    // Setup Live Timer
    window.attendanceData = {
        todayTimeIn: <?= json_encode($todayTimeIn) ?>,
        todayTimeOut: <?= json_encode($todayTimeOut) ?>,
        todayHours: <?= json_encode($todayHours) ?>
    };

    function initLiveTimer() {
        const el = document.getElementById('live-timer');
        if (!el) return;

        if (!window.attendanceData.todayTimeIn) {
            el.innerText = '00:00:00';
            return;
        }

        // Running Live Timer
        const timeInStr = window.attendanceData.todayTimeIn.replace(' ', 'T');
        const timeIn = new Date(timeInStr).getTime();
        
        if (window.liveTimerInterval) clearInterval(window.liveTimerInterval);

        window.liveTimerInterval = setInterval(() => {
            let diffMs = Date.now() - timeIn;
            if (diffMs < 0) diffMs = 0;
            let diffSecs = Math.floor(diffMs / 1000);
            
            // Cap at 8 hours (28800 seconds)
            if (diffSecs > 28800) {
                diffSecs = 28800; // 8 hours tight cap
                clearInterval(window.liveTimerInterval);
            }

            const h = Math.floor(diffSecs / 3600).toString().padStart(2, '0');
            const m = Math.floor((diffSecs % 3600) / 60).toString().padStart(2, '0');
            const s = Math.floor(diffSecs % 60).toString().padStart(2, '0');
            el.innerText = `${h}:${m}:${s}`;
        }, 1000);
    }

    initLiveTimer();

    // Setup Journal Add function
    const btn = document.getElementById('add-journal-btn');
    if (btn) {
        // Prevent multi-bindings if SPA load fires this multiple times
        btn.replaceWith(btn.cloneNode(true)); 
        document.getElementById('add-journal-btn').addEventListener('click', async function() {
            const attIdText = document.getElementById('sum-att-id').innerText;
            const attId = parseInt(attIdText.replace('#', ''));
            if (!attId) {
                Swal.fire('Error', 'Please select an attendance record first.', 'error');
                return;
            }

            const currentJournal = document.getElementById('sum-journal').innerText;
            const isDefault = currentJournal === 'No journal entry available for this log.';
            
            const { value: text } = await Swal.fire({
                title: 'Journal Entry',
                input: 'textarea',
                inputLabel: 'What did you do on this day?',
                inputValue: !isDefault ? currentJournal : '',
                showCancelButton: true
            });

            if (text !== undefined) {
                try {
                    const req = await fetch('http://localhost/attendance-tracker-ojt/server/api/update_journal_api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ att_id: attId, journal: text })
                    });
                    const res = await req.json();
                    if (res.success) {
                        Swal.fire('Saved!', 'Journal entry updated successfully.', 'success');
                        
                        const finalText = text.trim() ? text.trim() : 'No journal entry available for this log.';
                        document.getElementById('sum-journal').innerText = finalText;
                        
                        const activeCard = document.querySelector(`.att-card[data-id="${attId}"]`);
                        if (activeCard) {
                            activeCard.dataset.journal = finalText;
                        }
                    } else {
                        Swal.fire('Error', res.message || 'Failed to save.', 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Network error. See console.', 'error');
                    console.error(e);
                }
            }
        });
    }
})();
</script>
