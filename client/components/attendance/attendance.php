<?php
    // --- Fetch real data from the database ---
    include_once(__DIR__ . '/../../../server/db/conn.php');

    $userId = $_SESSION['current_user']['id'] ?? 0;

    // Get today's record
    $stmtToday = $conn->prepare("
        SELECT id, time_in, time_out, status, hours
        FROM att_track_attendance
        WHERE user_id = ? AND CAST(time_in AS DATE) = CAST(GETDATE() AS DATE)
    ");
    $stmtToday->execute([$userId]);
    $todayRecord = $stmtToday->fetch(PDO::FETCH_ASSOC);

    // Determine button state
    $hasTimedIn  = $todayRecord !== false;
    $hasTimedOut = $hasTimedIn && $todayRecord['time_out'] !== null;
    $timeInISO   = $hasTimedIn ? $todayRecord['time_in'] : null;

    // Get all attendance records for the table
    $stmtAll = $conn->prepare("
        SELECT id, time_in, time_out, status, hours, journal
        FROM att_track_attendance
        WHERE user_id = ?
        ORDER BY time_in DESC
    ");
    $stmtAll->execute([$userId]);
    $attendanceRecords = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

    $statusBadge = [
        'present' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        'late'    => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
        'absent'  => 'bg-red-500/10 text-red-500 border-red-500/20',
    ];

    $statusIcon = [
        'present' => 'fa-check',
        'late'    => 'fa-clock',
        'absent'  => 'fa-xmark',
    ];
?>

<div class="flex flex-col items-center justify-start w-full h-full gap-6 page-content">
    <!-- Page Header -->
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-between w-full h-auto">
            <h1 class="text-3xl font-bold text-accent">Attendance</h1>
            <div class="flex flex-row items-center gap-3">
                <button class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-accent hover:bg-accent-hover text-white text-sm font-medium transition-all hover:scale-105 cursor-pointer shadow-lg shadow-accent/20 border border-white/10">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    Export CSV
                </button>
            </div>
        </div>
        <div class="flex flex-row items-center justify-start w-full h-auto gap-2 mt-1">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php echo date('l, F j, Y') ?>
            </p>
            <div class="border border-zinc-500 dark:border-zinc-400 rounded-full h-1 w-1"></div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Attendance & Shift Records
            </p>
        </div>
    </div>

    <!-- Attendance Table Card -->
    <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800/80 rounded-4xl p-6 gap-6 shadow-xl border border-white/5 backdrop-blur-sm">
        <div class="flex flex-row items-center justify-between w-full">
            <div class="flex flex-row items-center gap-3">
                <div class="h-10 w-10 bg-accent/20 text-accent rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-list-check text-lg"></i>
                </div>
                <p class="text-white text-xl font-semibold">Attendance Log</p>
            </div>
            <!-- Filter Controls & Action Buttons -->
            <div class="flex flex-row items-center gap-3">
                <div class="flex items-center gap-2 bg-zinc-600 dark:bg-zinc-700/50 rounded-full px-4 py-2 border border-white/5 focus-within:ring-2 focus-within:ring-accent transition-all duration-300 shadow-inner">
                    <i class="fa-solid fa-search text-zinc-400 text-sm"></i>
                    <input type="text" placeholder="Search record..." class="bg-transparent text-white text-sm outline-none placeholder-zinc-400/80 w-40" />
                </div>
                <div class="relative flex items-center">
                    <select class="appearance-none bg-zinc-600 dark:bg-zinc-700/50 text-white text-sm rounded-full pl-5 pr-10 py-2 border border-white/5 shadow-inner outline-none hover:border-accent/40 cursor-pointer transition-colors duration-300">
                        <option class="bg-zinc-700 max-w-full">This Week</option>
                        <option class="bg-zinc-700">This Month</option>
                        <option class="bg-zinc-700">All Time</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 text-zinc-400 text-xs pointer-events-none"></i>
                </div>

                <!-- Lunch Break Button -->
                <?php if ($hasTimedIn && !$hasTimedOut): ?>
                <button id="btn-lunch"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium transition-all hover:scale-105 cursor-pointer shadow-lg shadow-yellow-500/20 border border-white/10">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Start Lunch</span>
                </button>
                <?php else: ?>
                <button disabled
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-zinc-600/50 text-zinc-400 text-sm font-medium border border-white/5 cursor-not-allowed opacity-50">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Start Lunch</span>
                </button>
                <?php endif; ?>

                <!-- Time In / Time Out Button -->
                <?php if (!$hasTimedIn): ?>
                    <!-- TIME IN -->
                    <button id="btn-time-action"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-green-500 hover:bg-green-600 text-white text-sm font-medium transition-all hover:scale-105 cursor-pointer shadow-lg shadow-green-500/20 border border-white/10"
                        data-action="time_in">
                        <i class="fa-regular fa-clock"></i>
                        Time In
                    </button>
                <?php elseif (!$hasTimedOut): ?>
                    <!-- TIME OUT -->
                    <button id="btn-time-action"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition-all hover:scale-105 cursor-pointer shadow-lg shadow-red-500/20 border border-white/10"
                        data-action="time_out">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Time Out
                    </button>
                <?php else: ?>
                    <!-- COMPLETED -->
                    <button disabled
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-zinc-600 text-zinc-400 text-sm font-medium border border-white/5 cursor-not-allowed opacity-60">
                        <i class="fa-solid fa-check"></i>
                        Completed
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Table -->
        <div class="w-full rounded-3xl border border-white/5 shadow-inner bg-zinc-600 dark:bg-zinc-900/40">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-zinc-500 dark:bg-zinc-800/80 border-b border-white/10 uppercase tracking-wider text-[10px] font-bold text-zinc-300">
                        <th class="text-left px-6 py-4">Date</th>
                        <th class="text-left px-6 py-4">Day</th>
                        <th class="text-left px-6 py-4">Time In</th>
                        <th class="text-left px-6 py-4">Time Out</th>
                        <th class="text-left px-6 py-4 w-32">Tracker</th>
                        <th class="text-left px-6 py-4">Status</th>
                        <th class="text-center px-6 py-4">Hours</th>
                        <th class="text-center px-6 py-4">Journal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($attendanceRecords)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-zinc-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-inbox text-3xl opacity-40"></i>
                                <p>No attendance records yet. Click <b>Time In</b> to start!</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach ($attendanceRecords as $index => $record):
                        $timeIn  = $record['time_in'] ? new DateTime($record['time_in']) : null;
                        $timeOut = $record['time_out'] ? new DateTime($record['time_out']) : null;
                        $isToday = $timeIn && $timeIn->format('Y-m-d') === date('Y-m-d');
                        $isOngoing = $timeIn && !$timeOut && $isToday;
                        $status = $record['status'] ?? 'absent';
                        $badge = $statusBadge[$status] ?? $statusBadge['absent'];
                        $icon  = $statusIcon[$status] ?? $statusIcon['absent'];
                    ?>
                    <tr class="hover:bg-zinc-500/20 dark:hover:bg-zinc-700/20 transition-all duration-200 group">
                        
                        <!-- Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-white font-medium group-hover:text-accent transition-colors duration-200">
                                <?php echo $timeIn ? $timeIn->format('M j, Y') : '—' ?>
                            </span>
                        </td>
                        
                        <!-- Day -->
                        <td class="px-6 py-4 whitespace-nowrap text-zinc-300 font-medium">
                            <?php echo $timeIn ? $timeIn->format('l') : '—' ?>
                        </td>

                        <!-- Time In -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($timeIn): ?>
                                <span class="bg-white/5 text-zinc-200 px-3 py-1 rounded-md border border-white/10 font-mono text-xs shadow-inner">
                                    <?php echo $timeIn->format('h:i A') ?>
                                </span>
                            <?php else: ?>
                                <span class="text-zinc-500">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Time Out -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($timeOut): ?>
                                <span class="bg-white/5 text-zinc-200 px-3 py-1 rounded-md border border-white/10 font-mono text-xs shadow-inner">
                                    <?php echo $timeOut->format('h:i A') ?>
                                </span>
                            <?php else: ?>
                                <span class="text-zinc-500">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Real-time Tracker -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($isOngoing): ?>
                                <!-- Active Tracker -->
                                <div class="flex flex-row items-center gap-2">
                                    <span class="flex h-2.5 w-2.5 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-accent"></span>
                                    </span>
                                    <span class="text-accent font-mono text-xs font-bold tracker-timer" data-timestamp="<?php echo $timeIn->format('Y-m-d H:i:s') ?>">
                                        00:00:00
                                    </span>
                                </div>
                            <?php elseif ($timeIn && $timeOut): ?>
                                <!-- Completed -->
                                <span class="text-zinc-400 font-mono text-xs"><i class="fa-solid fa-stop text-[10px] mr-1 opacity-50"></i> Done</span>
                            <?php else: ?>
                                <span class="text-zinc-500">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border <?php echo $badge ?>">
                                <i class="fa-solid <?php echo $icon ?> text-[10px]"></i>
                                <?php echo strtoupper($status) ?>
                            </span>
                        </td>

                        <!-- Hours -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if ($isOngoing): ?>
                                <span class="text-zinc-400 italic text-xs">...</span>
                            <?php else: ?>
                                <span class="text-white font-medium badge bg-zinc-700 px-2 py-1 rounded shadow-inner"><?php echo number_format(floatval($record['hours']), 2) ?> <span class="text-zinc-400 text-[10px] uppercase ml-0.5">hrs</span></span>
                            <?php endif; ?>
                        </td>

                        <!-- Journal -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if ($status === 'absent'): ?>
                                <button disabled class="h-8 w-8 rounded-full bg-zinc-500/20 text-zinc-500 border border-white/5 cursor-not-allowed opacity-50">
                                    <i class="fa-solid fa-ban text-[12px]"></i>
                                </button>
                            <?php else: ?>
                                <button onclick='openJournalModal(<?php echo $record['id']; ?>, <?php echo htmlspecialchars(json_encode($record['journal'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>)' class="h-8 w-8 rounded-full bg-accent/10 hover:bg-accent text-accent hover:text-white border border-accent/20 transition-all duration-300 hover:shadow-lg hover:shadow-accent/30 cursor-pointer group-hover:-translate-y-0.5">
                                    <i class="fa-solid <?php echo empty(trim((string)$record['journal'])) ? 'fa-pen-nib' : 'fa-check'; ?> text-[12px]"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if (count($attendanceRecords) > 0): ?>
            <div class="flex flex-row items-center justify-between w-full px-6 py-4 bg-zinc-600/50 dark:bg-zinc-900/50 border-t border-white/5">
                <p class="text-xs text-zinc-400">Showing <span class="text-white font-medium">1</span> to <span class="text-white font-medium"><?php echo min(count($attendanceRecords), 10) ?></span> of <span class="text-white font-medium"><?php echo count($attendanceRecords) ?></span> entries</p>
                <div class="flex items-center gap-1.5">
                    <button class="flex items-center justify-center h-8 w-8 rounded-full bg-white/5 text-zinc-400 hover:text-white hover:bg-accent/80 transition-all border border-white/10 cursor-not-allowed opacity-50" disabled>
                        <i class="fa-solid fa-chevron-left text-xs pr-0.5"></i>
                    </button>
                    <button class="flex items-center justify-center h-8 w-8 rounded-full bg-accent text-white shadow-lg shadow-accent/20 border border-accent/50 font-medium text-xs transition-all cursor-pointer hover:scale-105 active:scale-95">
                        1
                    </button>
                    <button class="flex items-center justify-center h-8 w-8 rounded-full bg-white/5 text-zinc-400 hover:text-white hover:bg-accent hover:shadow-lg hover:shadow-accent/20 transition-all border border-white/10 cursor-pointer hover:scale-105 active:scale-95">
                        <i class="fa-solid fa-chevron-right text-xs pl-0.5"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const API_URL = '../server/api/attendance_api.php';
    const MAX_HOURS = 8;
    const MAX_MS = MAX_HOURS * 3600000;

    // ── Journal Modal Function ──
    window.openJournalModal = function(recordId, currentText = '') {
        Swal.fire({
            title: 'Daily Journal',
            input: 'textarea',
            inputLabel: 'What did you achieve or learn today?',
            inputValue: currentText,
            inputPlaceholder: 'Start typing here...',
            inputAttributes: { 'aria-label': 'Type your journal entry here' },
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
                    const res = await fetch(API_URL, { method: 'POST', body: fd });
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
                    if (typeof navigateTo === "function") navigateTo("attendance");
                    else location.reload();
                });
            }
        });
    }

    // ── Lunch Break State (localStorage) ──
    function getLunchState() {
        const raw = localStorage.getItem('att_lunch');
        if (!raw) return { onLunch: false, lunchStartMs: 0, totalLunchMs: 0 };
        try { return JSON.parse(raw); }
        catch { return { onLunch: false, lunchStartMs: 0, totalLunchMs: 0 }; }
    }

    function setLunchState(state) {
        localStorage.setItem('att_lunch', JSON.stringify(state));
    }

    function clearLunchState() {
        localStorage.removeItem('att_lunch');
    }

    // ── Timer ──
    let timerInterval = null;

    function initTrackers() {
        const trackers = document.querySelectorAll('.tracker-timer');
        if (trackers.length === 0) return;

        trackers.forEach(tracker => {
            const ts = tracker.getAttribute('data-timestamp');
            const timeInDate = new Date(ts);
            if (isNaN(timeInDate.getTime())) return;

            if (timerInterval) clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                const lunch = getLunchState();
                const now = Date.now();

                // If currently on lunch, freeze the displayed time
                let totalLunchMs = lunch.totalLunchMs || 0;
                if (lunch.onLunch && lunch.lunchStartMs) {
                    totalLunchMs += (now - lunch.lunchStartMs);
                }

                let elapsedMs = now - timeInDate.getTime() - totalLunchMs;
                if (elapsedMs < 0) elapsedMs = 0;

                // Cap at 8 hours
                if (elapsedMs > MAX_MS) elapsedMs = MAX_MS;

                const hrs  = Math.floor(elapsedMs / 3600000);
                const mins = Math.floor((elapsedMs % 3600000) / 60000);
                const secs = Math.floor((elapsedMs % 60000) / 1000);

                const fmt = n => n.toString().padStart(2, '0');
                tracker.textContent = `${fmt(hrs)}:${fmt(mins)}:${fmt(secs)}`;

                // Visual warning when approaching 8 hours
                if (elapsedMs >= MAX_MS) {
                    tracker.classList.add('text-red-400');
                    tracker.classList.remove('text-accent');
                }
            }, 1000);
        });
    }

    // ── Lunch Button Logic ──
    const lunchBtn = document.getElementById('btn-lunch');
    if (lunchBtn) {
        // Restore state on load
        const lunch = getLunchState();
        if (lunch.onLunch) {
            lunchBtn.innerHTML = '<i class="fa-solid fa-utensils"></i><span>End Lunch</span>';
            lunchBtn.classList.remove('bg-yellow-500', 'hover:bg-yellow-600', 'shadow-yellow-500/20');
            lunchBtn.classList.add('bg-amber-600', 'hover:bg-amber-700', 'shadow-amber-600/20', 'animate-pulse');
        }

        lunchBtn.addEventListener('click', () => {
            const lunch = getLunchState();

            if (!lunch.onLunch) {
                // START lunch
                setLunchState({
                    onLunch: true,
                    lunchStartMs: Date.now(),
                    totalLunchMs: lunch.totalLunchMs || 0
                });
                lunchBtn.innerHTML = '<i class="fa-solid fa-utensils"></i><span>End Lunch</span>';
                lunchBtn.classList.remove('bg-yellow-500', 'hover:bg-yellow-600', 'shadow-yellow-500/20');
                lunchBtn.classList.add('bg-amber-600', 'hover:bg-amber-700', 'shadow-amber-600/20', 'animate-pulse');

                // Disable time-out during lunch
                const timeBtn = document.getElementById('btn-time-action');
                if (timeBtn) {
                    timeBtn.disabled = true;
                    timeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    timeBtn.classList.remove('hover:scale-105');
                }

                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true })
                    .fire({ icon: 'info', title: 'Lunch break started. Timer paused.' });
            } else {
                // END lunch
                const elapsed = Date.now() - lunch.lunchStartMs;
                setLunchState({
                    onLunch: false,
                    lunchStartMs: 0,
                    totalLunchMs: (lunch.totalLunchMs || 0) + elapsed
                });
                lunchBtn.innerHTML = '<i class="fa-solid fa-utensils"></i><span>Start Lunch</span>';
                lunchBtn.classList.add('bg-yellow-500', 'hover:bg-yellow-600', 'shadow-yellow-500/20');
                lunchBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700', 'shadow-amber-600/20', 'animate-pulse');

                // Re-enable time-out
                const timeBtn = document.getElementById('btn-time-action');
                if (timeBtn) {
                    timeBtn.disabled = false;
                    timeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    timeBtn.classList.add('hover:scale-105');
                }

                const mins = Math.round(elapsed / 60000);
                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true })
                    .fire({ icon: 'success', title: `Lunch break ended. (${mins} min)` });
            }
        });
    }

    // ── Time In / Time Out Button Logic ──
    const timeBtn = document.getElementById('btn-time-action');
    if (timeBtn) {
        // If currently on lunch, disable time-out on load
        const lunchOnLoad = getLunchState();
        if (lunchOnLoad.onLunch && timeBtn.dataset.action === 'time_out') {
            timeBtn.disabled = true;
            timeBtn.classList.add('opacity-50', 'cursor-not-allowed');
            timeBtn.classList.remove('hover:scale-105');
        }

        timeBtn.addEventListener('click', async () => {
            const action = timeBtn.dataset.action;
            if (timeBtn.disabled) return;

            if (action === 'time_in') {
                // Clear any previous lunch state
                clearLunchState();

                try {
                    const fd = new FormData();
                    fd.append('action', 'time_in');
                    const res = await fetch(API_URL, { method: 'POST', body: fd });
                    const data = await res.json();

                    if (data.success) {
                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true })
                            .fire({ icon: 'success', title: data.message })
                            .then(() => {
                                // Reload the attendance page via SPA
                                if (typeof navigateTo === 'function') {
                                    navigateTo('attendance');
                                } else {
                                    location.reload();
                                }
                            });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Oops!', text: data.message });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
                }

            } else if (action === 'time_out') {
                // Confirm before timing out
                const result = await Swal.fire({
                    title: 'Time Out?',
                    text: 'Are you sure you want to time out? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#71717a',
                    confirmButtonText: 'Yes, Time Out',
                    cancelButtonText: 'Cancel'
                });

                if (!result.isConfirmed) return;

                try {
                    // Calculate total lunch minutes from localStorage
                    const lunch = getLunchState();
                    let totalLunchMs = lunch.totalLunchMs || 0;
                    if (lunch.onLunch && lunch.lunchStartMs) {
                        totalLunchMs += (Date.now() - lunch.lunchStartMs);
                    }
                    const lunchMinutes = Math.round(totalLunchMs / 60000);

                    const fd = new FormData();
                    fd.append('action', 'time_out');
                    fd.append('lunchMinutes', lunchMinutes);
                    const res = await fetch(API_URL, { method: 'POST', body: fd });
                    const data = await res.json();

                    if (data.success) {
                        clearLunchState();
                        if (timerInterval) clearInterval(timerInterval);

                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true })
                            .fire({ icon: 'success', title: `${data.message} (${data.hours} hrs logged)` })
                            .then(() => {
                                if (typeof navigateTo === 'function') {
                                    navigateTo('attendance');
                                } else {
                                    location.reload();
                                }
                            });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Oops!', text: data.message });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
                }
            }
        });
    }

    // Initialize timer on page load
    initTrackers();
</script>
