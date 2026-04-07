<?php
    session_start();
    if (!isset($_SESSION['current_user'])) {
        header("Location: ./auth/login.php");
        exit();
    }

    // --- Routing ---
    $validPages = ['dashboard', 'attendance', 'security', 'help', 'settings'];
    $currentPage = isset($_GET['page']) && in_array($_GET['page'], $validPages)
        ? $_GET['page']
        : 'dashboard';

    // --- AJAX partial request: return only the component ---
    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        include_once("./components/{$currentPage}/{$currentPage}.php");
        exit();
    }

    // --- Page titles ---
    $pageTitles = [
        'dashboard'  => 'Attendance Tracker | Dashboard',
        'attendance' => 'Attendance Tracker | Attendance',
        'security'   => 'Attendance Tracker | Security',
        'help'       => 'Attendance Tracker | Help & Support',
        'settings'   => 'Attendance Tracker | Settings',
    ];

    // --- Fetch Attendance Stats for Top Bar ---
    include_once(__DIR__ . '/../server/db/conn.php');
    $userId = $_SESSION['current_user']['id'] ?? 0;
    
    $stmtStats = $conn->prepare("
        SELECT 
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as presents,
            SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as lates,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absents
        FROM att_track_attendance
        WHERE user_id = ?
    ");
    $stmtStats->execute([$userId]);
    $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

    $totalPresents = (int)($stats['presents'] ?? 0);
    $totalLates    = (int)($stats['lates'] ?? 0);
    $totalAbsents  = (int)($stats['absents'] ?? 0);
    $totalDays     = $totalPresents + $totalLates + $totalAbsents;

    // Calculate On Time %
    // Assuming 'present' means on time. If they have 0 days, default to 100%.
    $onTimePercent = $totalDays > 0 ? round(($totalPresents / $totalDays) * 100, 1) : 100;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="./css/styles.css" />
        <title><?= $pageTitles[$currentPage] ?></title>
        <style type="text/tailwindcss">
            @import "tailwindcss";
            @theme {
                --color-mainp: #E8EAEC;
                --color-login: #F5F2EA;
                --color-dark-primary: #1C1C1C;
                --color-accent: var(--accent-color);
                --color-accent-hover: var(--accent-color-hover);
            }
            @custom-variant dark (&:where(.dark, .dark *));
        </style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
        <script>
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            document.documentElement.classList.toggle(
                "dark",
                localStorage.theme === "dark" ||
                    (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches),
            );

            // Set accent color
            const accentBase = localStorage.accent || '#f97316'; // Default orange-500
            const accentHover = localStorage.accentHover || '#ea580c'; // Default orange-600
            document.documentElement.style.setProperty('--accent-color', accentBase);
            document.documentElement.style.setProperty('--accent-color-hover', accentHover);
        </script>
    </head>
    <body>
        <div class="flex flex-row items-start justify-start w-full h-screen bg-mainp dark:bg-[var(--color-dark-primary)] transition-colors duration-300">
            <!-- Navigation Bar -->
            <div id="navigation-bar" class="w-auto h-screen flex flex-col items-center justify-between p-5">
                <div class="w-auto h-full flex flex-col items-center justify-center bg-white dark:bg-zinc-800 px-5 py-8 rounded-4xl gap-5">
                    <div class="flex items-center justify-center h-10 w-10">
                        <img src="./public/assets/images/logo.png" alt="logo" class="h-auto w-10" />
                    </div>
                    <hr class="w-full border border-white/20 rounded-full" />

                    <!-- Navigation Buttons -->
                    <div class="flex flex-col items-center justify-between flex-1">
                        <div class="flex flex-col items-center justify-start w-full h-auto gap-3">
                            <!-- Dashboard -->
                            <button data-page="dashboard" title="Dashboard" class="nav-btn flex items-center justify-center h-12 w-12 rounded-full hover:scale-105 cursor-pointer transition-all <?= $currentPage === 'dashboard' ? 'bg-accent shadow-lg shadow-accent/30' : 'bg-white dark:bg-zinc-600 hover:bg-white/70 dark:hover:bg-zinc-500' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#E8EAEC"><path d="M520-600v-240h320v240H520ZM120-440v-400h320v400H120Zm400 320v-400h320v400H520Zm-400 0v-240h320v240H120Zm80-400h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z" class="nav-icon <?= $currentPage !== 'dashboard' ? 'invert-100 dark:invert-0' : '' ?>"/></svg>
                            </button>
                            <!-- Attendance -->
                            <button data-page="attendance" title="Attendance" class="nav-btn flex items-center justify-center h-12 w-12 rounded-full hover:scale-105 cursor-pointer transition-all <?= $currentPage === 'attendance' ? 'bg-accent shadow-lg shadow-accent/30' : 'bg-white dark:bg-zinc-600 hover:bg-white/70 dark:hover:bg-zinc-500' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#E8EAEC"><path d="M438-226 296-368l58-58 84 84 168-168 58 58-226 226ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z" class="nav-icon <?= $currentPage !== 'attendance' ? 'invert-100 dark:invert-0' : '' ?>"/></svg>
                            </button>
                            <!-- Security -->
                            <button data-page="security" title="Security" class="nav-btn flex items-center justify-center h-12 w-12 rounded-full hover:scale-105 cursor-pointer transition-all <?= $currentPage === 'security' ? 'bg-accent shadow-lg shadow-accent/30' : 'bg-white dark:bg-zinc-600 hover:bg-white/70 dark:hover:bg-zinc-500' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#E8EAEC"><path d="M722.5-297.5Q740-315 740-340t-17.5-42.5Q705-400 680-400t-42.5 17.5Q620-365 620-340t17.5 42.5Q655-280 680-280t42.5-17.5ZM680-160q31 0 57-14.5t42-38.5q-22-13-47-20t-52-7q-27 0-52 7t-47 20q16 24 42 38.5t57 14.5ZM480-80q-139-35-229.5-159.5T160-516v-244l320-120 320 120v227q-19-8-39-14.5t-41-9.5v-147l-240-90-240 90v188q0 47 12.5 94t35 89.5Q310-290 342-254t71 60q11 32 29 61t41 52q-1 0-1.5.5t-1.5.5Zm200 0q-83 0-141.5-58.5T480-280q0-83 58.5-141.5T680-480q83 0 141.5 58.5T880-280q0 83-58.5 141.5T680-80ZM480-494Z" class="nav-icon <?= $currentPage !== 'security' ? 'invert-100 dark:invert-0' : '' ?>"/></svg>
                            </button>
                        </div>
                        <div class="flex flex-col items-center justify-start w-full h-auto gap-5">
                            <div class="flex flex-col items-center justify-start w-full h-auto gap-3">
                                <!-- Help -->
                                <button data-page="help" title="Help & Support" class="nav-btn flex items-center justify-center h-12 w-12 rounded-full hover:scale-105 cursor-pointer transition-all <?= $currentPage === 'help' ? 'bg-accent shadow-lg shadow-accent/30' : 'bg-white dark:bg-zinc-600 hover:bg-white/70 dark:hover:bg-zinc-500' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#E8EAEC"><path d="M513.5-254.5Q528-269 528-290t-14.5-35.5Q499-340 478-340t-35.5 14.5Q428-311 428-290t14.5 35.5Q457-240 478-240t35.5-14.5ZM442-394h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" class="nav-icon <?= $currentPage !== 'help' ? 'invert-100 dark:invert-0' : '' ?>"/></svg>
                                </button>
                                <!-- Settings -->
                                <button data-page="settings" title="Settings" class="nav-btn flex items-center justify-center h-12 w-12 rounded-full hover:scale-105 cursor-pointer transition-all <?= $currentPage === 'settings' ? 'bg-accent shadow-lg shadow-accent/30' : 'bg-white dark:bg-zinc-600 hover:bg-white/70 dark:hover:bg-zinc-500' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#E8EAEC"><path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z" class="nav-icon <?= $currentPage !== 'settings' ? 'invert-100 dark:invert-0' : '' ?>"/></svg>
                                </button>
                            </div>
                            <hr class="w-full border border-white/20 rounded-full" />
                            <div class="flex flex-col items-center justify-start w-full h-auto">
                                <button id="logout-btn" class="flex items-center justify-center h-12 w-12 group rounded-full bg-white dark:bg-zinc-600 hover:bg-red-500 hover:text-white hover:scale-105 transition-all cursor-pointer ">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-lg text-zinc-500 dark:text-zinc-400 group-hover:text-white transition-colors duration-300"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Navigation Bar -->

            <div class="flex flex-col items-center justify-start w-full h-full pt-5 px-5 gap-5">
                <!-- Top Bar -->
                <div class="flex flex-row items-start justify-between w-full h-20">
                    <div class="flex flex-row items-start justify-start gap-3 h-full w-auto">
                        <div class="flex flex-row items-center justify-center w-auto h-10 bg-zinc-500 dark:bg-zinc-800 rounded-full px-3 py-1 gap-2">
                            <i class="fa-solid fa-check text-green-500"></i>
                            <span class="dark:text-white/50 text-white/90">On Time:</span>
                            <span class="dark:text-white text-white/90"><?php echo $onTimePercent; ?>%</span>
                        </div>
                        <div class="flex flex-row items-center justify-center w-auto h-10 bg-zinc-500 dark:bg-zinc-800 rounded-full px-3 py-1 gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
                            <span class="dark:text-white/50 text-white/90">Late/s:</span>
                            <span class="dark:text-white text-white/90"><?php echo $totalLates; ?></span>
                        </div>
                        <div class="flex flex-row items-center justify-center w-auto h-10 bg-zinc-500 dark:bg-zinc-800 rounded-full px-3 py-1 gap-2">
                            <i class="fa-solid fa-skull text-red-500"></i>
                            <span class="dark:text-white/50 text-white/90">Absent/s:</span>
                            <span class="dark:text-white text-white/90"><?php echo $totalAbsents; ?></span>
                        </div>
                    </div>
                    <div class="flex flex-row items-center justify-start gap-3 h-full w-auto">
                        <div class="flex items-center justify-center w-auto h-10 bg-zinc-800 rounded-full px-3 py-1 gap-2 shadow-md">
                            <span class="<?= $_SESSION['restriction']['role'] === 'admin' ? 'text-red-500' : ($_SESSION['restriction']['role'] == 'user' ? 'text-blue-500' : 'text-white/90') ?>"><?php echo ucfirst($_SESSION['restriction']['role']); ?></span>
                        </div>
                        <button class="flex items-center justify-center h-10 w-12 rounded-full bg-zinc-500 dark:bg-zinc-800 hover:bg-zinc-700 transition-all duration-300 cursor-pointer">
                            <i class="fa-regular fa-bell text-white"></i>
                        </button>
                        <div class="h-10 w-0 border border-zinc-300 dark:border-zinc-600 rounded-full"></div>
                        <div class="flex flex-row items-center justify-start gap-2 h-10 w-auto py-1">
                            <div class="h-10 w-10 rounded-full bg-zinc-500 dark:bg-zinc-800"></div>
                            <div class="flex flex-col items-start justify-between w-auto h-10">
                                <span class="text-zinc-700 dark:text-zinc-200 font-medium text-md transition-colors"><?php echo $_SESSION['current_user']['lastName'] . ', ' . $_SESSION['current_user']['firstName'] . ' ' . substr($_SESSION['current_user']['middleName'], 0, 1) . '.'; ?></span>
                                <div class="flex flex-row items-center justify-start w-auto h-auto gap-2">
                                    <span class="text-zinc-500 dark:text-zinc-200 text-xs transition-colors"><?php echo $_SESSION['current_user']['department']; ?></span>
                                    <span class="text-zinc-500 dark:text-zinc-200 text-xs transition-colors">|</span>
                                    <span class="text-zinc-500 dark:text-zinc-200 text-xs transition-colors"><?php echo ucfirst(str_replace('_', ' ', $_SESSION['current_user']['position'])); ?></span>
                                </div>
                            </div>
                            <!-- Darkmode switch -->
                            <button id="dark-mode-toggle" class="relative inline-flex h-9 w-16 items-center rounded-full bg-gradient-to-r from-cyan-400 to-blue-400 dark:from-indigo-800 dark:to-zinc-900 transition-all duration-500 ml-4 shadow-inner hover:scale-105 active:scale-95 focus:outline-none cursor-pointer group">
                                <!-- Background Icons -->
                                <div class="absolute inset-0 w-full flex items-center justify-between px-2.5 pointer-events-none">
                                    <i class="fa-solid fa-sun text-white/90 text-[10px] drop-shadow-sm"></i>
                                    <i class="fa-solid fa-moon text-white/90 text-[10px] drop-shadow-sm"></i>
                                </div>
                                <!-- Sliding Knob -->
                                <span class="absolute left-1 top-1 flex h-7 w-7 items-center justify-center rounded-full bg-white shadow-[0_2px_5px_rgba(0,0,0,0.3)] transition-transform duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] dark:translate-x-7">
                                    <i class="fa-solid fa-sun text-amber-500 text-xs absolute transition-opacity duration-300 dark:opacity-0 opacity-100"></i>
                                    <i class="fa-solid fa-moon text-indigo-600 text-xs absolute transition-opacity duration-300 dark:opacity-100 opacity-0"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- End of Top Bar -->

                <!-- Main Content -->
                <div id="main-content" class="flex flex-col items-center justify-start w-full h-full pb-5">
                    <?php include_once("./components/{$currentPage}/{$currentPage}.php"); ?>
                </div>
                <!-- End of Main Content -->
            </div>
        </div>
    </body>
    <script>
        // --- Dark Mode Toggle ---
        const toggleBtn = document.getElementById('dark-mode-toggle');
        toggleBtn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
        });

        // --- SPA-style AJAX Routing ---
        const navButtons = document.querySelectorAll('.nav-btn');
        const mainContent = document.getElementById('main-content');
        const activeClasses = ['bg-accent', 'shadow-lg', 'shadow-accent/30'];
        const inactiveClassesDark = ['dark:bg-zinc-600', 'dark:hover:bg-zinc-500', 'z-100'];
        const inactiveClassesLight = ['bg-white', 'hover:bg-white/70', 'z-100'];
        const allInactive = [...inactiveClassesDark, ...inactiveClassesLight];

        function setActiveButton(targetPage) {
            navButtons.forEach(btn => {
                const page = btn.getAttribute('data-page');
                const icon = btn.querySelector('.nav-icon');
                if (page === targetPage) {
                    btn.classList.remove(...allInactive);
                    btn.classList.add(...activeClasses);
                    if (icon) {
                        icon.classList.remove('invert-100', 'dark:invert-0');
                    }
                } else {
                    btn.classList.remove(...activeClasses);
                    btn.classList.add(...allInactive);
                    if (icon) {
                        icon.classList.add('invert-100', 'dark:invert-0');
                    }
                }
            });
        }

        async function navigateTo(page) {
            // Fade out current content
            mainContent.style.opacity = '0';
            mainContent.style.transform = 'translateY(8px)';

            // Update active state immediately for responsive feel
            setActiveButton(page);

            try {
                const response = await fetch(`./index.php?page=${page}&ajax=1`);
                if (!response.ok) throw new Error('Failed to load page');
                const html = await response.text();

                // Wait for fade-out transition
                await new Promise(r => setTimeout(r, 200));

                // Swap content
                mainContent.innerHTML = html;

                // Execute any <script> tags in the loaded content (innerHTML doesn't run them)
                mainContent.querySelectorAll('script').forEach(oldScript => {
                    const newScript = document.createElement('script');
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent;
                    }
                    oldScript.replaceWith(newScript);
                });
                setTimeout(() => {
                    if (typeof window.initTrackers === 'function') {
                        window.initTrackers();
                    }
                }, 50);

                // Update browser URL & title
                const titles = {
                    dashboard:  'Attendance Tracker | Dashboard',
                    attendance: 'Attendance Tracker | Attendance',
                    security:   'Attendance Tracker | Security',
                    help:       'Attendance Tracker | Help & Support',
                    settings:   'Attendance Tracker | Settings',
                };
                history.pushState({ page }, '', `?page=${page}`);
                document.title = titles[page] || 'Attendance Tracker';

                // Fade in new content
                requestAnimationFrame(() => {
                    mainContent.style.opacity = '1';
                    mainContent.style.transform = 'translateY(0)';
                    updateColorPickerUI();
                });
            } catch (err) {
                console.error(err);
                mainContent.innerHTML = `
                    <div class="flex flex-col items-center justify-center w-full h-full gap-3">
                        <i class="fa-solid fa-circle-exclamation text-red-500 text-4xl"></i>
                        <p class="text-zinc-400 text-lg">Failed to load page. Please try again.</p>
                    </div>
                `;
                mainContent.style.opacity = '1';
                mainContent.style.transform = 'translateY(0)';
            }
        }

        // --- Accent Color Management ---
        document.body.addEventListener('click', (e) => {
            const colorBtn = e.target.closest('.color-picker-btn');
            if (colorBtn) {
                const base = colorBtn.getAttribute('data-color');
                const hover = colorBtn.getAttribute('data-hover');
                
                // Update CSS variables
                document.documentElement.style.setProperty('--accent-color', base);
                document.documentElement.style.setProperty('--accent-color-hover', hover);

                // Persist
                localStorage.setItem('accent', base);
                localStorage.setItem('accentHover', hover);

                // Update UI selection rings
                document.querySelectorAll('.color-picker-btn').forEach(btn => {
                    btn.classList.remove('ring-2', 'ring-offset-2', 'ring-offset-zinc-500', 'dark:ring-offset-zinc-800', 'ring-white');
                    btn.classList.add('border-2', 'border-transparent');
                });
                colorBtn.classList.add('ring-2', 'ring-offset-2', 'ring-offset-zinc-500', 'dark:ring-offset-zinc-800', 'ring-white');
                colorBtn.classList.remove('border-2', 'border-transparent');
            }
        });

        // Initialize active state of color buttons if settings page is loaded
        function updateColorPickerUI() {
            const currentAccent = localStorage.accent || '#f97316';
            document.querySelectorAll('.color-picker-btn').forEach(btn => {
                if(btn.getAttribute('data-color') === currentAccent) {
                    btn.classList.add('ring-2', 'ring-offset-2', 'ring-offset-zinc-500', 'dark:ring-offset-zinc-800', 'ring-white');
                    btn.classList.remove('border-2', 'border-transparent');
                } else {
                    btn.classList.remove('ring-2', 'ring-offset-2', 'ring-offset-zinc-500', 'dark:ring-offset-zinc-800', 'ring-white');
                    btn.classList.add('border-2', 'border-transparent');
                }
            });
        }
        
        // Initial setup for the currently open page
        document.addEventListener('DOMContentLoaded', updateColorPickerUI);

        // Attach click handlers to nav buttons
        navButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const page = btn.getAttribute('data-page');
                navigateTo(page);
            });
        });

        // Handle browser back/forward
        window.addEventListener('popstate', (e) => {
            const page = e.state?.page || 'dashboard';
            navigateTo(page);
        });

        // Set initial history state
        history.replaceState({ page: '<?= $currentPage ?>' }, '', window.location.href);

        // --- Logout ---
        document.getElementById('logout-btn').addEventListener('click', () => {
            Swal.fire({
                title: 'Logout?',
                text: 'Are you sure you want to sign out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#3f3f46',
                confirmButtonText: 'Yes, logout',
                background: '#27272a',
                color: '#fff',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = './auth/logout.php';
                }
            });
        });
    </script>
</html>
