<?php
$title = "Dashboard";
$activeTab = "dashboard";
require_once TEMPLATES_DIR . '/layout/header.php';
?>

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto space-y-8">
    <!-- Flash Messages -->
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Bookings & Overview</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">See your upcoming appointments, revenue metrics, and booking status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($user['username']) ?>" target="_blank"
               class="px-4 py-2.5 bg-black hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Copy Booking Link
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Today's Bookings</span>
                <div class="p-2.5 bg-slate-100 text-slate-900 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-950 mt-4"><?= $stats['today'] ?></p>
        </div>

        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Upcoming Bookings</span>
                <div class="p-2.5 bg-slate-100 text-slate-900 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-950 mt-4"><?= $stats['upcoming'] ?></p>
        </div>

        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Revenue</span>
                <div class="p-2.5 bg-emerald-50 text-emerald-700 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-950 mt-4">$<?= number_format($stats['revenue'], 2) ?></p>
        </div>
    </div>

    <!-- Analytics Report Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Panel -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-1">Status Distribution</h2>
                <p class="text-slate-500 text-xs font-medium">Visual proportion of appointment statuses.</p>
            </div>
            <div class="py-6 flex justify-center items-center">
                <div class="relative w-44 h-44">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <div class="text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Appointments: <?= $stats['total'] ?></span>
            </div>
        </div>

        <!-- Details Overview List -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-1">Status Overview Report</h2>
                <p class="text-slate-500 text-xs font-medium">Live breakdown and comparative percentage stats.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-2">
                <?php
                $breakdown = $stats['status_breakdown'] ?? [];
                $total = array_sum($breakdown);

                $statusLabels = [
                    'confirmed' => ['label' => 'Confirmed', 'color' => 'bg-emerald-500', 'bar' => 'bg-emerald-500'],
                    'completed' => ['label' => 'Completed', 'color' => 'bg-blue-500', 'bar' => 'bg-blue-500'],
                    'cancelled' => ['label' => 'Cancelled', 'color' => 'bg-red-500', 'bar' => 'bg-red-500'],
                    'pending'   => ['label' => 'Pending', 'color' => 'bg-amber-500', 'bar' => 'bg-amber-500'],
                    'awaiting_payment' => ['label' => 'Awaiting Payment', 'color' => 'bg-slate-400', 'bar' => 'bg-slate-400']
                ];
                ?>
                <?php foreach ($statusLabels as $statusKey => $cfg): ?>
                    <?php
                    $count = $breakdown[$statusKey] ?? 0;
                    $pct = $total > 0 ? round(($count / $total) * 100) : 0;
                    ?>
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col justify-between gap-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full <?= $cfg['color'] ?>"></span>
                                <span class="text-xs font-bold text-slate-700"><?= $cfg['label'] ?></span>
                            </div>
                            <span class="text-xs font-extrabold text-slate-900"><?= $count ?></span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="h-full <?= $cfg['bar'] ?>" style="width: <?= $pct ?>%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-semibold"><?= $pct ?>% of total bookings</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- JS for Chart.js -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('statusChart').getContext('2d');
        const data = {
            labels: ['Confirmed', 'Completed', 'Cancelled', 'Pending', 'Awaiting Payment'],
            datasets: [{
                data: [
                    <?= (int)($breakdown['confirmed'] ?? 0) ?>,
                    <?= (int)($breakdown['completed'] ?? 0) ?>,
                    <?= (int)($breakdown['cancelled'] ?? 0) ?>,
                    <?= (int)($breakdown['pending'] ?? 0) ?>,
                    <?= (int)($breakdown['awaiting_payment'] ?? 0) ?>
                ],
                backgroundColor: [
                    '#10b981', // Emerald 500
                    '#3b82f6', // Blue 500
                    '#ef4444', // Red 500
                    '#f59e0b', // Amber 500
                    '#94a3b8'  // Slate 400
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        };
        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '75%'
            }
        };
        new Chart(ctx, config);
    });
</script>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
