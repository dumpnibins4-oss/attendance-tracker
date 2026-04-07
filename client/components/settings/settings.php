<div class="flex flex-col items-center justify-start w-full h-full gap-5 page-content">
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
                Preferences & Configuration
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 w-full h-auto gap-5">
        <!-- Profile Settings Card -->
        <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
            <div class="flex flex-row items-center gap-3">
                <i class="fa-solid fa-user-pen text-white text-xl"></i>
                <p class="text-white text-xl font-medium">Profile Settings</p>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex flex-row items-center gap-4 bg-zinc-600 dark:bg-zinc-700 rounded-2xl p-4">
                    <div class="h-16 w-16 rounded-full bg-zinc-500 dark:bg-zinc-600 flex items-center justify-center">
                        <i class="fa-solid fa-user text-zinc-400 text-2xl"></i>
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="text-white font-medium"><?php echo $_SESSION['current_user']['lastName'] . ', ' . $_SESSION['current_user']['firstName']; ?></p>
                        <p class="text-zinc-400 text-sm"><?php echo $_SESSION['current_user']['department']; ?></p>
                    </div>
                    <button class="ml-auto px-4 py-2 rounded-full bg-zinc-500 dark:bg-zinc-600 text-white text-sm hover:bg-orange-500 transition-all cursor-pointer">
                        Change Photo
                    </button>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-zinc-300 text-sm">Display Name</label>
                    <input type="text" value="<?php echo $_SESSION['current_user']['firstName'] . ' ' . $_SESSION['current_user']['lastName']; ?>" class="w-full bg-zinc-600 dark:bg-zinc-700 text-white rounded-xl px-4 py-3 outline-none text-sm focus:ring-2 focus:ring-orange-500 transition-all" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-zinc-300 text-sm">Email Address</label>
                    <input type="email" placeholder="your.email@company.com" class="w-full bg-zinc-600 dark:bg-zinc-700 text-white rounded-xl px-4 py-3 outline-none text-sm placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 transition-all" />
                </div>
                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-xl transition-all hover:scale-[1.02] cursor-pointer mt-2 shadow-lg shadow-orange-500/20">
                    Save Changes
                </button>
            </div>
        </div>

        <!-- Notification Preferences Card -->
        <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
            <div class="flex flex-row items-center gap-3">
                <i class="fa-solid fa-bell text-white text-xl"></i>
                <p class="text-white text-xl font-medium">Notifications</p>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Email Notifications</p>
                        <p class="text-zinc-400 text-xs">Receive daily attendance summaries via email</p>
                    </div>
                    <button class="relative inline-flex h-7 w-12 items-center rounded-full bg-zinc-500 dark:bg-zinc-600 transition-colors cursor-pointer">
                        <span class="absolute left-1 h-5 w-5 rounded-full bg-white shadow-md transition-transform"></span>
                    </button>
                </div>
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Late Alerts</p>
                        <p class="text-zinc-400 text-xs">Get notified when marked as late</p>
                    </div>
                    <button class="relative inline-flex h-7 w-12 items-center rounded-full bg-orange-500 transition-colors cursor-pointer">
                        <span class="absolute left-1 h-5 w-5 rounded-full bg-white shadow-md transition-transform translate-x-5"></span>
                    </button>
                </div>
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Journal Reminders</p>
                        <p class="text-zinc-400 text-xs">Remind me to submit my daily journal</p>
                    </div>
                    <button class="relative inline-flex h-7 w-12 items-center rounded-full bg-orange-500 transition-colors cursor-pointer">
                        <span class="absolute left-1 h-5 w-5 rounded-full bg-white shadow-md transition-transform translate-x-5"></span>
                    </button>
                </div>
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Browser Notifications</p>
                        <p class="text-zinc-400 text-xs">Show desktop push notifications</p>
                    </div>
                    <button class="relative inline-flex h-7 w-12 items-center rounded-full bg-zinc-500 dark:bg-zinc-600 transition-colors cursor-pointer">
                        <span class="absolute left-1 h-5 w-5 rounded-full bg-white shadow-md transition-transform"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
