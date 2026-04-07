<?php
$u = $_SESSION['current_user'];
$r = $_SESSION['restriction'];
$fullName = $u['lastName'] . ', ' . $u['firstName'] . ' ' . substr($u['middleName'], 0, 1) . '.';
$initials = strtoupper(substr($u['firstName'], 0, 1) . substr($u['lastName'], 0, 1));
$role = ucfirst($r['role'] ?? 'User');
$roleColor = ($r['role'] === 'admin') ? '#ef4444' : '#3b82f6';
$birthday = !empty($u['birthday']) ? date('F j, Y', strtotime($u['birthday'])) : '—';

// Resolve existing profile photo (any extension)
$bioId = $u['biometrics_id'];
$photoDir = __DIR__ . '/../../public/assets/photos/';
$photoUrl = null;
foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
    if (file_exists($photoDir . $bioId . '.' . $ext)) {
        $photoUrl = './public/assets/photos/' . $bioId . '.' . $ext . '?t=' . filemtime($photoDir . $bioId . '.' . $ext);
        break;
    }
}
?>

<div class="flex flex-col items-center justify-start w-full h-full gap-6 page-content">

    <!-- Page Header -->
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-start w-full h-auto">
            <h1 class="text-3xl font-bold text-orange-500">Settings</h1>
        </div>
        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php echo date('l, F j, Y') ?>
            </p>
            <div class="border border-zinc-500 dark:border-zinc-400 rounded-full h-1 w-1"></div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Preferences &amp; Configuration
            </p>
        </div>
    </div>

    <!-- ===== Info Grid ===== -->
    <div class="grid grid-cols-2 w-full h-auto gap-5">

        <!-- Personal Information Card -->
        <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-3xl p-6 gap-4">
            <div class="flex flex-row items-center gap-3 pb-2 border-b border-white/10">
                <div class="flex items-center justify-center h-9 w-9 rounded-full bg-orange-500/15">
                    <i class="fa-solid fa-id-card text-orange-500"></i>
                </div>
                <p class="text-white text-lg font-semibold">Personal Information</p>
            </div>
            <div class="flex flex-col gap-3">

                <!-- Profile Header: Avatar + Name/Badges -->
                <div class="flex flex-row items-center gap-4 pb-3 border-b border-white/10">
                    <!-- Hidden file input -->
                    <input type="file" id="photo-input" accept="image/jpeg,image/png,image/webp,image/gif"
                        class="hidden" />

                    <!-- Avatar wrapper -->
                    <div class="relative flex-shrink-0 group cursor-pointer" id="avatar-wrapper"
                        title="Click to change photo">
                        <!-- Avatar circle -->
                        <div id="avatar-display"
                            class="h-20 w-20 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg overflow-hidden transition-all duration-300"
                            style="background: linear-gradient(135deg, #f97316, #ea580c);">
                            <?php if ($photoUrl): ?>
                                <img id="avatar-img" src="<?php echo htmlspecialchars($photoUrl); ?>" alt="Profile Photo"
                                    class="w-full h-full object-cover" />
                            <?php else: ?>
                                <span id="avatar-initials"><?php echo $initials; ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Hover overlay -->
                        <div class="absolute inset-0 rounded-full bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                    flex flex-col items-center justify-center gap-1 pointer-events-none">
                            <i class="fa-solid fa-camera text-white text-lg"></i>
                            <span class="text-white text-[10px] font-medium">Change</span>
                        </div>

                        <!-- Upload progress ring -->
                        <svg id="upload-ring" class="absolute inset-0 h-20 w-20 hidden" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="36" fill="none" stroke="#f97316" stroke-width="4"
                                stroke-dasharray="226.19" stroke-dashoffset="226.19" stroke-linecap="round"
                                transform="rotate(-90 40 40)" id="upload-ring-circle"
                                style="transition:stroke-dashoffset 0.3s ease;" />
                        </svg>
                    </div>

                    <!-- Name + badges -->
                    <div class="flex flex-col gap-1 flex-1 min-w-0">
                        <p class="text-white font-bold text-lg leading-tight truncate">
                            <?php echo htmlspecialchars($fullName); ?></p>
                        <p class="text-zinc-400 text-sm"><?php echo htmlspecialchars($u['position'] ?? '—'); ?>
                            &nbsp;·&nbsp; <?php echo htmlspecialchars($u['department'] ?? '—'); ?></p>
                        <div class="flex flex-row items-center gap-2 mt-1 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                style="background-color:<?php echo $roleColor; ?>22; color:<?php echo $roleColor; ?>; border:1px solid <?php echo $roleColor; ?>55;">
                                <?php echo $role; ?>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-zinc-600 text-zinc-300">
                                ID: <?php echo htmlspecialchars($u['employee_id'] ?? '—'); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Full Name -->
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-400 text-xs uppercase tracking-wide">Full Name</label>
                    <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                        <p class="text-white text-sm font-medium">
                            <?php echo htmlspecialchars($u['firstName'] . ' ' . $u['middleName'] . ' ' . $u['lastName']); ?>
                        </p>
                    </div>
                </div>

                <!-- Gender -->
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-400 text-xs uppercase tracking-wide">Gender</label>
                    <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                        <p class="text-white text-sm"><?php echo htmlspecialchars($u['gender'] ?? '—'); ?></p>
                    </div>
                </div>

                <!-- Birthday -->
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-400 text-xs uppercase tracking-wide">Birthday</label>
                    <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                        <p class="text-white text-sm"><?php echo htmlspecialchars($birthday); ?></p>
                    </div>
                </div>

                <!-- Contact Number -->
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-400 text-xs uppercase tracking-wide">Contact Number</label>
                    <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                        <p class="text-white text-sm"><?php echo htmlspecialchars($u['contactNumber'] ?? '—'); ?></p>
                    </div>
                </div>

                <!-- Address -->
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-400 text-xs uppercase tracking-wide">Address</label>
                    <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                        <p class="text-white text-sm"><?php echo htmlspecialchars($u['address'] ?? '—'); ?></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column -->
        <div class="flex flex-col w-full gap-3">

            <!-- Work / Account Information Card -->
            <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-3xl p-6 gap-4">
                <div class="flex flex-row items-center gap-3 pb-2 border-b border-white/10">
                    <div class="flex items-center justify-center h-9 w-9 rounded-full bg-blue-500/15">
                        <i class="fa-solid fa-briefcase text-blue-400"></i>
                    </div>
                    <p class="text-white text-lg font-semibold">Work &amp; Account</p>
                </div>
                <div class="flex flex-col gap-3">

                    <!-- Department -->
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-400 text-xs uppercase tracking-wide">Department</label>
                        <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                            <p class="text-white text-sm"><?php echo htmlspecialchars($u['department'] ?? '—'); ?></p>
                        </div>
                    </div>

                    <!-- Position -->
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-400 text-xs uppercase tracking-wide">Position</label>
                        <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                            <p class="text-white text-sm"><?php echo htmlspecialchars($u['position'] ?? '—'); ?></p>
                        </div>
                    </div>

                    <!-- School -->
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-400 text-xs uppercase tracking-wide">School / Institution</label>
                        <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                            <p class="text-white text-sm"><?php echo htmlspecialchars($u['school'] ?? '—'); ?></p>
                        </div>
                    </div>

                    <!-- Employee ID & Biometrics ID side by side -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-zinc-400 text-xs uppercase tracking-wide">Employee ID</label>
                            <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                                <p class="text-white text-sm font-mono">
                                    <?php echo htmlspecialchars($u['employee_id'] ?? '—'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-zinc-400 text-xs uppercase tracking-wide">Biometrics ID</label>
                            <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                                <p class="text-white text-sm font-mono">
                                    <?php echo htmlspecialchars($u['biometrics_id'] ?? '—'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Gmail -->
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-400 text-xs uppercase tracking-wide">Gmail</label>
                        <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3 flex items-center gap-2">
                            <i class="fa-brands fa-google text-red-400 text-xs"></i>
                            <p class="text-white text-sm"><?php echo htmlspecialchars($u['gmail'] ?? '—'); ?></p>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-400 text-xs uppercase tracking-wide">System Role</label>
                        <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                            <span class="text-sm font-semibold" style="color:<?php echo $roleColor; ?>">
                                <?php echo $role; ?>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Notification Preferences Card (compact) -->
            <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-3xl p-6 gap-4">
                <div class="flex flex-row items-center gap-3 pb-2 border-b border-white/10">
                    <div class="flex items-center justify-center h-9 w-9 rounded-full bg-yellow-500/15">
                        <i class="fa-solid fa-bell text-yellow-400"></i>
                    </div>
                    <p class="text-white text-lg font-semibold">Notifications</p>
                </div>
                <div class="flex flex-col gap-3">
                    <?php
                    $toggles = [
                        ['label' => 'Email Notifications', 'desc' => 'Receive daily attendance summaries via email', 'on' => false],
                        ['label' => 'Late Alerts', 'desc' => 'Get notified when marked as late', 'on' => true],
                        ['label' => 'Journal Reminders', 'desc' => 'Remind me to submit my daily journal', 'on' => true],
                        ['label' => 'Browser Notifications', 'desc' => 'Show desktop push notifications', 'on' => false],
                    ];
                    foreach ($toggles as $t):
                        $active = $t['on'];
                        ?>
                        <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-3">
                            <div class="flex flex-col gap-0.5">
                                <p class="text-white text-sm font-medium"><?php echo $t['label']; ?></p>
                                <p class="text-zinc-400 text-xs"><?php echo $t['desc']; ?></p>
                            </div>
                            <button class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer flex-shrink-0 ml-3
                            <?php echo $active ? 'bg-orange-500' : 'bg-zinc-500 dark:bg-zinc-600'; ?>">
                                <span class="absolute h-4 w-4 rounded-full bg-white shadow-md transition-transform
                                <?php echo $active ? 'translate-x-6' : 'translate-x-1'; ?>">
                                </span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    (function () {
        const wrapper = document.getElementById('avatar-wrapper');
        const input = document.getElementById('photo-input');
        const display = document.getElementById('avatar-display');
        const ring = document.getElementById('upload-ring');
        const ringCirc = document.getElementById('upload-ring-circle');
        const circumference = 276.46;

        // Open file picker when avatar is clicked
        wrapper.addEventListener('click', () => input.click());

        input.addEventListener('change', async () => {
            const file = input.files[0];
            if (!file) return;

            // --- Client-side validation ---
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Only JPEG, PNG, WebP, and GIF images are allowed.', background: '#27272a', color: '#fff' });
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Maximum allowed size is 5 MB.', background: '#27272a', color: '#fff' });
                input.value = '';
                return;
            }

            // --- Instant local preview ---
            const reader = new FileReader();
            reader.onload = e => {
                display.style.background = 'none';
                // Replace inner content with <img>
                display.innerHTML = `<img id="avatar-img" src="${e.target.result}" alt="Profile Photo" class="w-full h-full object-cover" />`;
            };
            reader.readAsDataURL(file);

            // --- Show progress ring ---
            ring.classList.remove('hidden');
            setProgress(0);

            // --- Upload via XHR (for progress events) ---
            const formData = new FormData();
            formData.append('photo', file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../server/api/upload_photo.php', true);

            xhr.upload.addEventListener('progress', e => {
                if (e.lengthComputable) setProgress(e.loaded / e.total);
            });

            xhr.addEventListener('load', () => {
                ring.classList.add('hidden');
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.success) {
                        // Update the img src with the server-returned URL (cache-busted)
                        const img = document.getElementById('avatar-img');
                        if (img) img.src = resp.photo_url;

                        Swal.fire({
                            icon: 'success',
                            title: 'Photo Updated!',
                            text: 'Your profile photo has been saved.',
                            background: '#27272a',
                            color: '#fff',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Upload Failed', text: resp.message, background: '#27272a', color: '#fff' });
                        // Revert to initials if upload truly failed
                        display.style.background = 'linear-gradient(135deg, #f97316, #ea580c)';
                        display.innerHTML = `<span id="avatar-initials"><?php echo $initials; ?></span>`;
                    }
                } catch {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Unexpected server response.', background: '#27272a', color: '#fff' });
                }
                input.value = '';
            });

            xhr.addEventListener('error', () => {
                ring.classList.add('hidden');
                Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not reach the server. Please try again.', background: '#27272a', color: '#fff' });
                input.value = '';
            });

            xhr.send(formData);
        });

        function setProgress(ratio) {
            const offset = circumference * (1 - ratio);
            ringCirc.style.strokeDashoffset = offset;
        }
    })();
</script>