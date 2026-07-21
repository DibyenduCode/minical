<?php
$title = "Super Admin Dashboard";
$adminTab = "overview";
require_once TEMPLATES_DIR . '/admin/layout/header.php';
?>

<div class="max-w-7xl mx-auto space-y-8">
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header Banner -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-black text-white text-[10px] font-extrabold rounded-full uppercase tracking-wider mb-2">
                Super Admin Control Panel
            </div>
            <h1 class="text-3xl font-extrabold text-slate-950 tracking-tight">Platform Overview</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">High-level platform metrics, active user accounts, and custom white-label domains.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= APP_URL ?>/admin/users" class="px-5 py-2.5 bg-black hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                Manage Users
            </a>
            <a href="<?= APP_URL ?>/admin/settings" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-200 transition-all">
                System Settings
            </a>
        </div>
    </div>

    <!-- Platform Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Registered Users</span>
            <p class="text-3xl font-extrabold text-slate-950 mt-3"><?= $usersCount ?></p>
        </div>
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Platform Bookings</span>
            <p class="text-3xl font-extrabold text-slate-950 mt-3"><?= $bookingsCount ?></p>
        </div>
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Event Types</span>
            <p class="text-3xl font-extrabold text-slate-950 mt-3"><?= $eventsCount ?></p>
        </div>
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Revenue Processed</span>
            <p class="text-3xl font-extrabold text-emerald-700 mt-3">$<?= number_format($revenueTotal, 2) ?></p>
        </div>
    </div>

    <!-- Quick User Overview -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-950 tracking-tight">Recent User Registrations</h2>
            <a href="<?= APP_URL ?>/admin/users" class="text-xs font-bold text-black hover:underline">View All Users →</a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Username</th>
                        <th class="px-6 py-3.5">Custom Domain</th>
                        <th class="px-6 py-3.5">Role</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5">
                                <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($u['name']) ?></p>
                                <p class="text-xs text-slate-500"><?= htmlspecialchars($u['email']) ?></p>
                            </td>
                            <td class="px-6 py-3.5 font-mono text-xs text-slate-600">/u/<?= htmlspecialchars($u['username']) ?></td>
                            <td class="px-6 py-3.5">
                                <?php if (!empty($u['custom_domain'])): ?>
                                    <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-200">
                                        🌐 <?= htmlspecialchars($u['custom_domain']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">None</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3.5 uppercase text-[11px] font-bold text-slate-700"><?= $u['role'] ?></td>
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $u['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                                    <?= ucfirst($u['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/admin/layout/footer.php'; ?>
