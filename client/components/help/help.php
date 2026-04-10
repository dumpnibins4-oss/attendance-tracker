<div class="flex flex-col items-center justify-start w-full h-full gap-5 page-content">
    <!-- Page Header -->
    <div class="flex flex-col items-start justify-start w-full h-auto">
        <div class="flex flex-row items-center justify-start w-full h-auto">
            <h1 class="text-3xl font-bold text-orange-500">Help & Support</h1>
        </div>
        <div class="flex flex-row items-center justify-start w-full h-auto gap-2">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                <?php echo date('l, F j, Y') ?>
            </p>
            <div class="border border-zinc-500 dark:border-zinc-400 rounded-full h-1 w-1"></div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Frequently Asked Questions
            </p>
        </div>
    </div>

    <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
        <div class="flex flex-row items-center gap-3">
            <i class="fa-solid fa-circle-question text-white text-xl"></i>
            <p class="text-white text-xl font-medium">FAQs</p>
        </div>

        <?php
        $faqs = [
            ['q' => 'How do I time in?', 'a' => 'Click the "Time In" button on your Dashboard under Quick Actions. You must be connected to the office network.'],
            ['q' => 'What if I forget to time out?', 'a' => 'Please contact your supervisor or an administrator to manually adjust your record. Unresolved entries may count as incomplete.'],
            ['q' => 'How are late arrivals calculated?', 'a' => 'Any time-in after 8:00 AM is marked as late. Accumulated tardiness may affect your attendance rating.'],
            ['q' => 'How do I view my attendance history?', 'a' => 'Navigate to the Attendance page using the calendar icon in the sidebar to view your detailed log.'],
            ['q' => 'Who do I contact for technical issues?', 'a' => 'Reach out to the IT department via email at IT.Dev@la-rose-noire.com or dial 8022 or visit the IT Help Desk.'],
        ];
        ?>

        <div class="flex flex-col gap-3">
            <?php foreach ($faqs as $index => $faq): ?>
                <details class="group bg-zinc-600 dark:bg-zinc-700 rounded-2xl overflow-hidden">
                    <summary
                        class="flex flex-row items-center justify-between w-full px-5 py-4 cursor-pointer list-none text-white font-medium text-sm hover:bg-zinc-500/50 dark:hover:bg-zinc-600/50 transition-colors">
                        <span><?php echo $faq['q'] ?></span>
                        <i
                            class="fa-solid fa-chevron-down text-zinc-400 text-xs transition-transform group-open:rotate-180"></i>
                    </summary>
                    <div class="px-5 pb-4 text-zinc-300 text-sm leading-relaxed">
                        <?php echo $faq['a'] ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Contact Card -->
    <div class="flex flex-col w-full h-auto bg-zinc-500 dark:bg-zinc-800 rounded-4xl p-6 gap-4">
        <div class="flex flex-row items-center gap-3">
            <i class="fa-solid fa-headset text-white text-xl"></i>
            <p class="text-white text-xl font-medium">Contact IT Support</p>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div
                class="flex flex-col items-center justify-center bg-zinc-600 dark:bg-zinc-700 rounded-2xl p-5 gap-3 hover:scale-105 transition-all cursor-pointer">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-orange-500/20">
                    <i class="fa-solid fa-envelope text-orange-400 text-lg"></i>
                </div>
                <p class="text-white text-sm font-medium">Email</p>
                <p class="text-zinc-400 text-xs">IT.Dev@la-rose-noire.com</p>
            </div>
            <div
                class="flex flex-col items-center justify-center bg-zinc-600 dark:bg-zinc-700 rounded-2xl p-5 gap-3 hover:scale-105 transition-all cursor-pointer">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-500/20">
                    <i class="fa-solid fa-phone text-blue-400 text-lg"></i>
                </div>
                <p class="text-white text-sm font-medium">Phone</p>
                <p class="text-zinc-400 text-xs">8022</p>
            </div>
            <div
                class="flex flex-col items-center justify-center bg-zinc-600 dark:bg-zinc-700 rounded-2xl p-5 gap-3 hover:scale-105 transition-all cursor-pointer">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-500/20">
                    <i class="fa-solid fa-location-dot text-green-400 text-lg"></i>
                </div>
                <p class="text-white text-sm font-medium">IT Help Desk</p>
                <p class="text-zinc-400 text-xs">General Office </p>
            </div>
        </div>
    </div>
</div>