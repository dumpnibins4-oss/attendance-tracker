<div class="flex flex-col items-center justify-start w-full h-full gap-5 page-content">
    <!-- Page Header -->
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-start w-full h-auto">
            <h1 class="text-3xl font-bold text-accent">Settings</h1>
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
                    <button class="ml-auto px-4 py-2 rounded-full bg-zinc-500 dark:bg-zinc-600 text-white text-sm hover:bg-accent transition-all cursor-pointer">
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
                <button class="w-full bg-accent hover:bg-accent-hover text-white font-medium py-3 rounded-xl transition-all hover:scale-[1.02] cursor-pointer mt-2 shadow-lg shadow-accent/20">
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
                    <button class="relative inline-flex h-7 w-12 items-center rounded-full bg-accent transition-colors cursor-pointer">
                        <span class="absolute left-1 h-5 w-5 rounded-full bg-white shadow-md transition-transform translate-x-5"></span>
                    </button>
                </div>
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Journal Reminders</p>
                        <p class="text-zinc-400 text-xs">Remind me to submit my daily journal</p>
                    </div>
                    <button class="relative inline-flex h-7 w-12 items-center rounded-full bg-accent transition-colors cursor-pointer">
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

        <!-- Appearance Card -->
        <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4 col-span-2">
            <div class="flex flex-row items-center gap-3">
                <i class="fa-solid fa-palette text-white text-xl"></i>
                <p class="text-white text-xl font-medium">Appearance & Theme</p>
            </div>
            <div class="flex flex-col gap-3">
                <p class="text-zinc-300 text-sm">Accent Color</p>
                <div class="flex flex-row items-center gap-4">
                    <button class="color-picker-btn h-10 w-10 rounded-full bg-orange-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer ring-2 ring-offset-2 ring-offset-zinc-500 dark:ring-offset-zinc-800 ring-white" data-color="#f97316" data-hover="#ea580c" title="Orange (Default)"></button>
                    <button class="color-picker-btn h-10 w-10 rounded-full bg-blue-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent" data-color="#3b82f6" data-hover="#2563eb" title="Blue"></button>
                    <button class="color-picker-btn h-10 w-10 rounded-full bg-emerald-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent" data-color="#10b981" data-hover="#059669" title="Emerald"></button>
                    <button class="color-picker-btn h-10 w-10 rounded-full bg-rose-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent" data-color="#f43f5e" data-hover="#e11d48" title="Rose"></button>
                    <button class="color-picker-btn h-10 w-10 rounded-full bg-violet-500 shadow-md transition-transform hover:scale-110 active:scale-95 cursor-pointer border-2 border-transparent" data-color="#8b5cf6" data-hover="#7c3aed" title="Violet"></button>
                </div>
            </div>
        </div>
    </div>
</div>
