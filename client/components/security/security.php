<div class="flex flex-col items-center justify-start w-full h-full gap-5 page-content">
    <!-- Page Header -->
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-start w-full h-auto">
            <h1 class="text-3xl font-bold text-orange-500">Security</h1>
        </div>
        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php echo date('l, F j, Y') ?>
            </p>
            <div class="border border-zinc-500 dark:border-zinc-400 rounded-full h-1 w-1"></div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Account Security & Activity
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 w-full h-auto gap-5">
        <!-- Change Password Card -->
        <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
            <div class="flex flex-row items-center gap-3">
                <i class="fa-solid fa-lock text-white text-xl"></i>
                <p class="text-white text-xl font-medium">Change Password</p>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-zinc-300 text-sm">Current Password</label>
                    <input type="password" placeholder="Enter current password" class="w-full bg-zinc-600 dark:bg-zinc-700 text-white rounded-xl px-4 py-3 outline-none text-sm placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 transition-all" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-zinc-300 text-sm">New Password</label>
                    <input type="password" placeholder="Enter new password" class="w-full bg-zinc-600 dark:bg-zinc-700 text-white rounded-xl px-4 py-3 outline-none text-sm placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 transition-all" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-zinc-300 text-sm">Confirm Password</label>
                    <input type="password" placeholder="Confirm new password" class="w-full bg-zinc-600 dark:bg-zinc-700 text-white rounded-xl px-4 py-3 outline-none text-sm placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 transition-all" />
                </div>
                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-xl transition-all hover:scale-[1.02] cursor-pointer mt-2 shadow-lg shadow-orange-500/20">
                    Update Password
                </button>
            </div>
        </div>

        <!-- Login Activity Card -->
        <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
            <div class="flex flex-row items-center gap-3">
                <i class="fa-solid fa-shield-halved text-white text-xl"></i>
                <p class="text-white text-xl font-medium">Recent Login Activity</p>
            </div>
            <div class="flex flex-col gap-3">
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Windows • Chrome</p>
                        <p class="text-zinc-400 text-xs">192.168.1.1 • Today, 8:02 AM</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400">Active</span>
                </div>
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Windows • Firefox</p>
                        <p class="text-zinc-400 text-xs">192.168.1.1 • Yesterday, 7:55 AM</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/30 text-zinc-400">Ended</span>
                </div>
                <div class="flex flex-row items-center justify-between bg-zinc-600 dark:bg-zinc-700 rounded-xl p-4">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-white text-sm font-medium">Android • Chrome Mobile</p>
                        <p class="text-zinc-400 text-xs">192.168.0.5 • Apr 4, 2026</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-500/30 text-zinc-400">Ended</span>
                </div>
            </div>
        </div>
    </div>
</div>
