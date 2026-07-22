<?php
$title = "Availability Schedule";
$activeTab = "availability";
require_once TEMPLATES_DIR . '/layout/header.php';

$daysOfWeek = [
    0 => 'Sunday',
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday'
];

$scheduleMap = [];
foreach ($schedule as $row) {
    $scheduleMap[$row['day_of_week']] = $row;
}
?>

<div class="max-w-4xl mx-auto space-y-8">
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Weekly Availability</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Set your working hours for each day to prevent double bookings.</p>
        </div>

        <form action="<?= APP_URL ?>/availability" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="divide-y divide-slate-100 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                <?php foreach ($daysOfWeek as $dayNum => $dayName): ?>
                    <?php
                    $dayData = $scheduleMap[$dayNum] ?? [
                        'is_enabled' => ($dayNum >= 1 && $dayNum <= 5) ? 1 : 0,
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00'
                    ];
                    $isEnabled = (bool)$dayData['is_enabled'];
                    $startTime = substr($dayData['start_time'], 0, 5);
                    $endTime = substr($dayData['end_time'], 0, 5);
                    ?>
                    <div class="p-4 flex flex-col gap-3.5 hover:bg-white transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="days[<?= $dayNum ?>][enabled]" id="enabled-<?= $dayNum ?>" value="1" <?= $isEnabled ? 'checked' : '' ?>
                                       class="w-5 h-5 accent-black rounded cursor-pointer" onchange="toggleDayRow(this, <?= $dayNum ?>)">
                                <label for="enabled-<?= $dayNum ?>" class="font-bold text-slate-900 text-sm w-28 cursor-pointer select-none"><?= $dayName ?></label>
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="time" name="days[<?= $dayNum ?>][start_time]" value="<?= $startTime ?>"
                                       class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-black">
                                <span class="text-slate-400 text-xs font-medium">to</span>
                                <input type="time" name="days[<?= $dayNum ?>][end_time]" value="<?= $endTime ?>"
                                       class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-black">
                            </div>
                        </div>

                        <!-- Daily Break Time Row -->
                        <div id="break-section-<?= $dayNum ?>" class="flex flex-col sm:flex-row sm:items-center gap-3 pl-8 text-xs <?= $isEnabled ? '' : 'hidden' ?>">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="days[<?= $dayNum ?>][break_enabled]" id="break-enabled-<?= $dayNum ?>" value="1" <?= !empty($dayData['break_start_time']) ? 'checked' : '' ?>
                                       class="w-4 h-4 accent-black rounded cursor-pointer" onchange="toggleBreakTimes(this, <?= $dayNum ?>)">
                                <span class="font-bold text-slate-500">Add Break / Lunch Time</span>
                            </label>

                            <div id="break-times-<?= $dayNum ?>" class="flex items-center gap-3 <?= !empty($dayData['break_start_time']) ? '' : 'hidden' ?>">
                                <span class="text-slate-400 font-medium">from</span>
                                <input type="time" name="days[<?= $dayNum ?>][break_start]" value="<?= !empty($dayData['break_start_time']) ? substr($dayData['break_start_time'], 0, 5) : '13:00' ?>"
                                       class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-black">
                                <span class="text-slate-400 font-medium">to</span>
                                <input type="time" name="days[<?= $dayNum ?>][break_end]" value="<?= !empty($dayData['break_end_time']) ? substr($dayData['break_end_time'], 0, 5) : '14:00' ?>"
                                       class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-black">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md transition-all">
                    Save Availability
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JS for Interactive Availability Form toggling -->
<script>
    function toggleDayRow(checkbox, dayNum) {
        const breakSection = document.getElementById('break-section-' + dayNum);
        if (checkbox.checked) {
            breakSection.classList.remove('hidden');
        } else {
            breakSection.classList.add('hidden');
        }
    }

    function toggleBreakTimes(checkbox, dayNum) {
        const breakTimes = document.getElementById('break-times-' + dayNum);
        if (checkbox.checked) {
            breakTimes.classList.remove('hidden');
        } else {
            breakTimes.classList.add('hidden');
        }
    }
</script>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
