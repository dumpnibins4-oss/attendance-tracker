<?php
include_once(__DIR__ . '/../../../server/db/conn.php');
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

<div class="flex flex-col items-center justify-start w-full h-auto gap-5 page-content overflow-y-auto pb-20">
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
    <div class="grid grid-cols-2 w-full h-auto gap-5">
        <!-- Left Column -->
        <div class="flex flex-col w-full gap-5">
            <!-- Personal Information Card -->
            <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
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
                            <div id="avatar-display"
                                class="h-20 w-20 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg overflow-hidden transition-all duration-300"
                                style="background: linear-gradient(135deg, #f97316, #ea580c);">
                                <?php if ($photoUrl): ?>
                                    <img id="avatar-img" src="<?php echo htmlspecialchars($photoUrl); ?>"
                                        alt="Profile Photo" class="user-avatar-img w-full h-full object-cover" />
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
                                <?php echo htmlspecialchars($fullName); ?>
                            </p>
                            <p class="text-zinc-400 text-sm">
                                <?php echo htmlspecialchars($u['position'] ?? '—'); ?> &nbsp;·&nbsp;
                                <?php echo htmlspecialchars($u['department'] ?? '—'); ?>
                            </p>
                            <div class="flex flex-row items-center gap-2 mt-1 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                    style="background-color:<?php echo $roleColor; ?>22; color:<?php echo $roleColor; ?>; border:1px solid <?php echo $roleColor; ?>55;">
                                    <?php echo $role; ?>
                                </span>
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-zinc-600 text-zinc-300">
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

                    <!-- Contact & Gmail Details -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-zinc-400 text-xs uppercase tracking-wide">Contact Number</label>
                            <input type="text" id="input-contactNumber"
                                class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-orange-500 transition-all font-mono"
                                value="<?php echo htmlspecialchars($u['contactNumber'] ?? ''); ?>" placeholder="—" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-zinc-400 text-xs uppercase tracking-wide">Gmail Address</label>
                            <div
                                class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3 flex items-center gap-2 focus-within:ring-1 focus-within:ring-orange-500 transition-all h-full">
                                <i class="fa-brands fa-google text-red-400 text-xs"></i>
                                <input type="email" id="input-gmail"
                                    class="bg-transparent text-white text-sm outline-none w-full"
                                    value="<?php echo htmlspecialchars($u['gmail'] ?? ''); ?>" placeholder="—" />
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-400 text-xs uppercase tracking-wide">Address</label>
                        <div class="bg-zinc-600 dark:bg-zinc-700 rounded-xl px-4 py-3">
                            <p class="text-white text-sm"><?php echo htmlspecialchars($u['address'] ?? '—'); ?></p>
                        </div>
                    </div>

                    <!-- Update Button for Personal Info -->
                    <div class="flex justify-end mt-2">
                        <button id="btn-update-personal"
                            class="px-5 py-2 rounded-xl bg-orange-500 text-white text-sm font-semibold hover:bg-orange-600 transition-all shadow-md active:scale-95 cursor-pointer">
                            Update
                        </button>
                    </div>
                </div>
            </div>
            <!-- Appearance & Theme Card (full width) -->
            <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
                <div class="flex flex-row items-center gap-3 pb-2 border-b border-white/10">
                    <div class="flex items-center justify-center h-9 w-9 rounded-full bg-violet-500/15">
                        <i class="fa-solid fa-palette text-violet-400"></i>
                    </div>
                    <p class="text-white text-lg font-semibold">Appearance &amp; Theme</p>
                </div>
                <div class="flex flex-col gap-3">
                    <p class="text-zinc-400 text-xs uppercase tracking-wide">Accent Color</p>
                    <div class="flex flex-row items-center gap-4">
                        <button
                            class="color-picker-btn h-10 w-10 rounded-full bg-orange-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer ring-2 ring-offset-2 ring-offset-zinc-500 dark:ring-offset-zinc-800 ring-white"
                            data-color="#f97316" data-hover="#ea580c" title="Orange (Default)"></button>
                        <button
                            class="color-picker-btn h-10 w-10 rounded-full bg-blue-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent"
                            data-color="#3b82f6" data-hover="#2563eb" title="Blue"></button>
                        <button
                            class="color-picker-btn h-10 w-10 rounded-full bg-emerald-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent"
                            data-color="#10b981" data-hover="#059669" title="Emerald"></button>
                        <button
                            class="color-picker-btn h-10 w-10 rounded-full bg-rose-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent"
                            data-color="#f43f5e" data-hover="#e11d48" title="Rose"></button>
                        <button
                            class="color-picker-btn h-10 w-10 rounded-full bg-violet-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent"
                            data-color="#8b5cf6" data-hover="#7c3aed" title="Violet"></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Column -->
        <div class="flex flex-col w-full gap-5">
            <!-- Work & Account Card -->
            <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
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
                    <!-- Employee ID & Biometrics ID -->
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
                    <!-- System Role -->
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
            <!-- Notifications Card -->
            <?php
            $toggles = [
                ['label' => 'Late Alerts', 'desc' => 'Get notified when marked as late', 'on' => true, 'icon' => 'fa-clock'],
                ['label' => 'Journal Reminders', 'desc' => 'Remind me to submit my daily journal', 'on' => true, 'icon' => 'fa-book-open'],
                ['label' => 'Browser Notifications', 'desc' => 'Show desktop push notifications', 'on' => false, 'icon' => 'fa-desktop'],
            ];

            try {
                $notifUserId = $_SESSION['current_user']['id'];
                
                foreach ($toggles as &$t) {
                    $label = $t['label'];
                    // Get or create type
                    $stmt = $conn->prepare("SELECT id FROM att_track_notif_type WHERE notif_type = ?");
                    $stmt->execute([$label]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$row) {
                        $ins = $conn->prepare("INSERT INTO att_track_notif_type (notif_type) VALUES (?)");
                        $ins->execute([$label]);
                        $stmt->execute([$label]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    $t['id'] = $row['id'];
                    
                    // Get or create setting
                    $stmt2 = $conn->prepare("SELECT is_on FROM att_track_notif_setting WHERE notif_type_id = ? AND user_id = ?");
                    $stmt2->execute([$t['id'], $notifUserId]);
                    $setRow = $stmt2->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$setRow) {
                        $def = $t['on'] ? 1 : 0;
                        $ins2 = $conn->prepare("INSERT INTO att_track_notif_setting (notif_type_id, is_on, user_id) VALUES (?, ?, ?)");
                        $ins2->execute([$t['id'], $def, $notifUserId]);
                        $t['active'] = (bool)$def;
                    } else {
                        $t['active'] = (bool)$setRow['is_on'];
                    }
                }
                unset($t);
            } catch (Exception $e) {
                // If DB fails, fallback to UI only
                foreach ($toggles as &$t) {
                    $t['id'] = 0;
                    $t['active'] = $t['on'];
                }
                unset($t);
            }
            ?>
            <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
                <div class="flex flex-row items-center gap-3 pb-2 border-b border-white/10">
                    <div class="flex items-center justify-center h-9 w-9 rounded-full bg-yellow-500/15">
                        <i class="fa-solid fa-bell text-yellow-400"></i>
                    </div>
                    <p class="text-white text-lg font-semibold">Notifications</p>
                </div>
                <div class="flex flex-col gap-3" id="notif-toggle-list">
                    <?php foreach ($toggles as $t): 
                        $active = $t['active'];
                        $icon   = $t['icon'];
                    ?>
                        <div class="notif-toggle-row flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-3 transition-all duration-200"
                             data-setting-id="<?php echo (int)$t['id']; ?>">
                            <div class="flex flex-row items-center gap-3">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full <?php echo $active ? 'bg-yellow-400/15' : 'bg-zinc-500/50 dark:bg-zinc-600/50'; ?> transition-colors duration-300 notif-icon-bg">
                                    <i class="fa-solid <?php echo $icon; ?> text-xs <?php echo $active ? 'text-yellow-400' : 'text-zinc-400'; ?> transition-colors duration-300 notif-icon"></i>
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <p class="text-white text-sm font-medium"><?php echo htmlspecialchars($t['label']); ?></p>
                                    <p class="text-zinc-400 text-xs"><?php echo htmlspecialchars($t['desc']); ?></p>
                                </div>
                            </div>
                            <button type="button"
                                id="notif-btn-<?php echo (int)$t['id']; ?>"
                                class="notif-toggle-btn relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-300 cursor-pointer flex-shrink-0 ml-3 <?php echo $active ? 'bg-yellow-400' : 'bg-zinc-500 dark:bg-zinc-600'; ?>"
                                data-active="<?php echo $active ? '1' : '0'; ?>"
                                title="<?php echo $active ? 'Turn off' : 'Turn on'; ?>"
                                aria-pressed="<?php echo $active ? 'true' : 'false'; ?>">
                                <span class="absolute h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-300 <?php echo $active ? 'translate-x-6' : 'translate-x-1'; ?> notif-knob">
                                </span>
                                <!-- Saving spinner overlay -->
                                <span class="notif-saving absolute inset-0 flex items-center justify-center hidden">
                                    <i class="fa-solid fa-circle-notch fa-spin text-white text-[10px]"></i>
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

        wrapper.addEventListener('click', () => input.click());

        input.addEventListener('change', async () => {
            const file = input.files[0];
            if (!file) return;

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

            // Instant local preview
            const reader = new FileReader();
            reader.onload = e => {
                display.style.background = 'none';
                display.innerHTML = `<img id="avatar-img" src="${e.target.result}" alt="Profile Photo" class="user-avatar-img w-full h-full object-cover" />`;
            };
            reader.readAsDataURL(file);

            ring.classList.remove('hidden');
            setProgress(0);

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
                        // Update ALL instances of the user avatar in the UI
                        const allAvatars = document.querySelectorAll('.user-avatar-img');
                        allAvatars.forEach(img => {
                            img.src = resp.photo_url;
                        });

                        // If the top bar was showing initials, replace them with an <img>
                        const topBar = document.getElementById('topbar-avatar');
                        if (topBar && !topBar.querySelector('img')) {
                            topBar.innerHTML = `<img src="${resp.photo_url}" alt="Avatar" class="user-avatar-img w-full h-full object-cover" />`;
                        }

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
            ringCirc.style.strokeDashoffset = circumference * (1 - ratio);
        }

        // --- Update User Information ---
        async function updateUserInfo(data) {
            try {
                const response = await fetch('../server/api/update_user_info.php', {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: { 'Content-Type': 'application/json' }
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Successfully',
                        text: result.message,
                        background: '#27272a',
                        color: '#fff',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(result.message);
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: err.message,
                    background: '#27272a',
                    color: '#fff'
                });
            }
        }

        document.getElementById('btn-update-personal')?.addEventListener('click', () => {
            const contactNumber = document.getElementById('input-contactNumber').value;
            const gmail = document.getElementById('input-gmail').value;
            updateUserInfo({ contactNumber, gmail });
        });

        // --- Notification Toggles ---
        document.querySelectorAll('.notif-toggle-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const row        = btn.closest('.notif-toggle-row');
                const settingId  = parseInt(row.dataset.settingId, 10);
                const isActive   = btn.dataset.active === '1';
                const newState   = !isActive;

                const knob    = btn.querySelector('.notif-knob');
                const saving  = btn.querySelector('.notif-saving');
                const iconBg  = row.querySelector('.notif-icon-bg');
                const icon    = row.querySelector('.notif-icon');

                // Optimistic UI update
                btn.dataset.active = newState ? '1' : '0';
                btn.setAttribute('aria-pressed', newState ? 'true' : 'false');
                btn.title = newState ? 'Turn off' : 'Turn on';

                if (newState) {
                    btn.classList.replace('bg-zinc-500', 'bg-yellow-400');
                    btn.classList.remove('dark:bg-zinc-600');
                    knob.classList.replace('translate-x-1', 'translate-x-6');
                    iconBg?.classList.replace('bg-zinc-500/50', 'bg-yellow-400/15');
                    iconBg?.classList.remove('dark:bg-zinc-600/50');
                    icon?.classList.replace('text-zinc-400', 'text-yellow-400');
                } else {
                    btn.classList.replace('bg-yellow-400', 'bg-zinc-500');
                    btn.classList.add('dark:bg-zinc-600');
                    knob.classList.replace('translate-x-6', 'translate-x-1');
                    iconBg?.classList.replace('bg-yellow-400/15', 'bg-zinc-500/50');
                    iconBg?.classList.add('dark:bg-zinc-600/50');
                    icon?.classList.replace('text-yellow-400', 'text-zinc-400');
                }

                // Show spinner
                btn.disabled = true;
                saving?.classList.remove('hidden');

                try {
                    const res = await fetch('../server/api/update_notif_settings.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ setting_id: settingId, is_on: newState })
                    });
                    const data = await res.json();

                    if (!data.success) throw new Error(data.message || 'Failed to save');

                    // Brief success flash on the row
                    row.classList.add('ring-1', 'ring-yellow-400/40');
                    setTimeout(() => row.classList.remove('ring-1', 'ring-yellow-400/40'), 800);

                } catch (err) {
                    // Revert on failure
                    btn.dataset.active = isActive ? '1' : '0';
                    if (isActive) {
                        btn.classList.replace('bg-zinc-500', 'bg-yellow-400');
                        btn.classList.remove('dark:bg-zinc-600');
                        knob.classList.replace('translate-x-1', 'translate-x-6');
                        iconBg?.classList.replace('bg-zinc-500/50', 'bg-yellow-400/15');
                        iconBg?.classList.remove('dark:bg-zinc-600/50');
                        icon?.classList.replace('text-zinc-400', 'text-yellow-400');
                    } else {
                        btn.classList.replace('bg-yellow-400', 'bg-zinc-500');
                        btn.classList.add('dark:bg-zinc-600');
                        knob.classList.replace('translate-x-6', 'translate-x-1');
                        iconBg?.classList.replace('bg-yellow-400/15', 'bg-zinc-500/50');
                        iconBg?.classList.add('dark:bg-zinc-600/50');
                        icon?.classList.replace('text-yellow-400', 'text-zinc-400');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: err.message,
                        background: '#27272a',
                        color: '#fff',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } finally {
                    saving?.classList.add('hidden');
                    btn.disabled = false;
                }
            });
        });
    })();
</script>