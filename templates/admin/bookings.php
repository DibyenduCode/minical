<?php
$title = "Global Appointments - Super Admin";
$adminTab = "bookings";
require_once TEMPLATES_DIR . '/admin/layout/header.php';
?>

<div class="max-w-7xl mx-auto space-y-8">
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 space-y-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Global Appointments Log</h1>
                <p class="text-slate-500 text-xs font-medium mt-1">Platform-wide overview of all customer appointments across all hosts.</p>
            </div>
            <span class="text-xs text-slate-500 font-bold bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200"><?= count($allBookings) ?> Total Bookings</span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Host Account</th>
                        <th class="px-6 py-4">Event Type</th>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($allBookings)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400 font-medium">No appointments recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($allBookings as $b): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($b['customer_name']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($b['customer_email']) ?></p>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 text-xs">
                                    <?= htmlspecialchars($b['host_name']) ?> <span class="text-slate-400">(/u/<?= htmlspecialchars($b['host_username']) ?>)</span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 text-xs">
                                    <?= htmlspecialchars($b['event_name']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-slate-900 font-semibold text-xs"><?= date('M j, Y', strtotime($b['booking_date'])) ?></p>
                                    <p class="text-[11px] text-slate-500"><?= date('h:i A', strtotime($b['start_time'])) ?> - <?= date('h:i A', strtotime($b['end_time'])) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border bg-slate-100 text-slate-800 border-slate-200 uppercase">
                                        <?= ucfirst($b['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/admin/layout/footer.php'; ?>
