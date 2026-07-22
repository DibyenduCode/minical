<?php
$title = "Manage Users - Super Admin";
$adminTab = "users";
require_once TEMPLATES_DIR . '/admin/layout/header.php';
?>

<div class="max-w-7xl mx-auto space-y-8">
    <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- User Management Section -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 space-y-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Registered Platform Users</h1>
                <p class="text-slate-500 text-xs font-medium mt-1">Manage user accounts, custom white-label domains, and administrative permissions.</p>
            </div>
            <span class="text-xs text-slate-500 font-bold bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200"><?= count($users) ?> Total Accounts</span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">User Details</th>
                        <th class="px-6 py-4">Booking Link</th>
                        <th class="px-6 py-4">Custom Branded Domain</th>
                        <th class="px-6 py-4">Bookings</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Subscription Plan</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($u['name']) ?></p>
                                <p class="text-xs text-slate-500"><?= htmlspecialchars($u['email']) ?></p>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($u['username']) ?>" target="_blank" class="hover:underline text-black font-semibold">
                                    /u/<?= htmlspecialchars($u['username']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <?php if (!empty($u['custom_domain'])): ?>
                                    <a href="http://<?= htmlspecialchars($u['custom_domain']) ?>" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:underline">
                                        <span>🌐 <?= htmlspecialchars($u['custom_domain']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 font-medium italic">None configured</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-800"><?= $u['total_bookings'] ?> bookings</td>
                            <td class="px-6 py-4 uppercase text-[11px] font-bold text-slate-700"><?= $u['role'] ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $u['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                                    <?= ucfirst($u['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ((int)$u['id'] !== (int)$admin['id']): ?>
                                    <form action="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>/update-plan" method="POST" class="inline-block">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        <select name="plan" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-black">
                                            <?php foreach ($plans as $p): ?>
                                                <option value="<?= htmlspecialchars($p['slug']) ?>" <?= ($u['plan'] ?? 'free') === $p['slug'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($p['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Unlimited (SA)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 flex items-center justify-end gap-2">
                                <?php if ((int)$u['id'] !== (int)$admin['id']): ?>
                                    <form method="POST" action="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>/toggle" class="inline-block">
                                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors <?= $u['status'] === 'active' ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' ?>">
                                            <?= $u['status'] === 'active' ? 'Disable' : 'Enable' ?>
                                        </button>
                                    </form>

                                    <form id="delete-user-form-<?= $u['id'] ?>" method="POST" action="<?= APP_URL ?>/admin/users/<?= $u['id'] ?>/delete" class="inline-block">
                                        <button type="button" onclick="confirmDeleteUser(<?= $u['id'] ?>)" class="text-xs text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
                                            Delete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 font-semibold italic">Super Admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 Deletion Confirmation script -->
<script>
    function confirmDeleteUser(userId) {
        Swal.fire({
            title: 'Delete User?',
            text: "Are you sure you want to permanently delete this user account? All their data (events, bookings, settings) will be destroyed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Red 600
            cancelButtonColor: '#64748b',  // Slate 500
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-3xl border border-slate-200 shadow-xl font-sans text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-user-form-' + userId).submit();
            }
        });
    }
</script>

<?php require_once TEMPLATES_DIR . '/admin/layout/footer.php'; ?>
