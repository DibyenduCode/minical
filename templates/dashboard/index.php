<?php
$title = "Bookings";
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

    <!-- Bookings Table Section -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 space-y-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-slate-950 tracking-tight">Scheduled Appointments</h2>

            <!-- Filters & Search -->
            <form method="GET" action="<?= APP_URL ?>/dashboard" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search name or email..."
                       class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-black">

                <select name="filter" onchange="this.form.submit()"
                        class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="">All Statuses</option>
                    <option value="today" <?= ($filter === 'today') ? 'selected' : '' ?>>Today</option>
                    <option value="upcoming" <?= ($filter === 'upcoming') ? 'selected' : '' ?>>Upcoming</option>
                    <option value="completed" <?= ($filter === 'completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= ($filter === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs font-medium">
                                No appointments found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($b['customer_name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars($b['customer_email']) ?></p>
                                        <?php if (!empty($b['responses'])): ?>
                                            <button type="button" onclick="toggleResponses(<?= $b['id'] ?>)" 
                                                    class="inline-flex items-center gap-1.5 mt-1.5 text-[10px] font-extrabold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-900 border border-indigo-100 px-2.5 py-1 rounded-lg w-max transition-colors cursor-pointer select-none">
                                                <span>💬 View Form Answers</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 text-xs">
                                    <?= htmlspecialchars($b['event_name']) ?>
                                    <?php if ($b['is_paid']): ?>
                                        <span class="ml-2 text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-bold">$<?= number_format($b['price'], 2) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-slate-900 font-semibold text-xs"><?= date('M j, Y', strtotime($b['booking_date'])) ?></p>
                                    <p class="text-[11px] text-slate-500"><?= date('h:i A', strtotime($b['start_time'])) ?> - <?= date('h:i A', strtotime($b['end_time'])) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $statusClasses = [
                                        'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                        'pending'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                    ];
                                    $class = $statusClasses[$b['status']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $class ?>">
                                        <?= ucfirst($b['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <?php if ($b['status'] !== 'cancelled' && $b['status'] !== 'completed' && !empty($b['meeting_link'])): ?>
                                        <a href="<?= htmlspecialchars($b['meeting_link']) ?>" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-xl shadow-sm transition-all">
                                            <span>📹 Join Meet</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($b['status'] === 'confirmed' || $b['status'] === 'pending' || $b['status'] === 'paid'): ?>
                                        <form id="complete-form-<?= $b['id'] ?>" method="POST" action="<?= APP_URL ?>/dashboard/complete" class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <button type="button" onclick="confirmComplete(<?= $b['id'] ?>)" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200/60">
                                                Complete
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($b['status'] !== 'cancelled' && $b['status'] !== 'completed'): ?>
                                        <form id="cancel-form-<?= $b['id'] ?>" method="POST" action="<?= APP_URL ?>/dashboard/cancel" class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <button type="button" onclick="confirmCancel(<?= $b['id'] ?>)" class="text-xs text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
                                                Cancel
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Premium Collapsible Form Field Responses Row -->
                            <?php if (!empty($b['responses'])): ?>
                                <tr id="responses-row-<?= $b['id'] ?>" class="bg-slate-50/50 hidden border-t border-b border-slate-100/80">
                                    <td colspan="5" class="px-4 sm:px-8 py-4">
                                        <div class="w-full max-w-lg bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-sm">📋</span>
                                                    <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Custom Form Responses</span>
                                                </div>
                                                <button type="button" onclick="toggleResponses(<?= $b['id'] ?>)" 
                                                        class="text-[10px] font-extrabold text-slate-400 hover:text-slate-600 transition-colors">
                                                    ✕ Close
                                                </button>
                                            </div>
                                            
                                            <!-- Stacked Vertical Question List -->
                                            <div class="space-y-3.5">
                                                <?php foreach ($b['responses'] as $resp): ?>
                                                    <div class="space-y-1 text-left">
                                                        <span class="block text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">
                                                            <?= htmlspecialchars($resp['field_label']) ?>
                                                        </span>
                                                        <div class="bg-slate-50 border border-slate-200/60 px-4 py-2.5 rounded-2xl text-slate-800 text-xs font-semibold leading-relaxed text-left whitespace-pre-wrap">
                                                            <?= htmlspecialchars($resp['value']) ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JS for Collapsible responses, SweetAlert2 Confirmations, and Chart.js -->
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

    function toggleResponses(bookingId) {
        const row = document.getElementById('responses-row-' + bookingId);
        if (row) {
            row.classList.toggle('hidden');
        }
    }

    function confirmComplete(bookingId) {
        Swal.fire({
            title: 'Mark as Completed?',
            text: "Are you sure you want to mark this consultation session as completed?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669', // Emerald 600
            cancelButtonColor: '#64748b',  // Slate 500
            confirmButtonText: 'Yes, complete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-3xl border border-slate-200 shadow-xl font-sans text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('complete-form-' + bookingId).submit();
            }
        });
    }

    function confirmCancel(bookingId) {
        Swal.fire({
            title: 'Cancel Appointment?',
            text: "Are you sure you want to cancel this booking? This will also remove the event from Google Calendar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Red 600
            cancelButtonColor: '#64748b',  // Slate 500
            confirmButtonText: 'Yes, cancel booking!',
            cancelButtonText: 'No, keep it',
            customClass: {
                popup: 'rounded-3xl border border-slate-200 shadow-xl font-sans text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + bookingId).submit();
            }
        });
    }
</script>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
