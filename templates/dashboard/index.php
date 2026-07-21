<?php
$title = "Bookings";
$activeTab = "dashboard";
require_once TEMPLATES_DIR . '/layout/header.php';
?>

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
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($b['customer_name']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($b['customer_email']) ?></p>
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
                                <td class="px-6 py-4 text-right">
                                    <?php if ($b['status'] !== 'cancelled'): ?>
                                        <form method="POST" action="<?= APP_URL ?>/dashboard/cancel" onsubmit="return confirm('Are you sure you want to cancel this booking?')" class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
                                                Cancel
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
