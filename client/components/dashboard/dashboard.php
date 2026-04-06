

<div class="flex flex-col items-center justify-start w-full h-full gap-5">
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-start w-full h-auto">
            <h1 class="text-3xl font-bold text-orange-500">Dashboard</h1>
        </div>
        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php echo date('l, F j, Y'); ?>
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
                        <i class="fa-solid fa-file-lines text-zinc-200 text-xl"></i>
                    </div>
                    <p class="text-zinc-200 text-md font-medium">Generate Report</p>
                </button>
                <button class="flex flex-row items-center justify-start h-15 w-full rounded-3xl p-3 bg-zinc-700 gap-2 hover:bg-zinc-600 hover:scale-105 hover:shadow-lg cursor-pointer transition-all">
                    <div class="flex flex-row items-center justify-center h-10 w-10 rounded-lg bg-zinc-600">
                        <i class="fa-solid fa-plus text-zinc-200 text-xl"></i>
                    </div>
                    <p class="text-zinc-200 text-md font-medium">Register OJT</p>
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

        <!-- Attendance Overview -->
        <div class="flex flex-col items-center justify-center w-full h-40 bg-zinc-500 dark:bg-zinc-800 rounded-2xl">
            
        </div>
    </div>
</div>