<?php
$title = "Promo Codes - MiniCal";
$activeTab = "promos";
require_once TEMPLATES_DIR . '/layout/header.php';
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

    <!-- Create Promo Code -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight font-sans">My Promo Codes</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Create promotional discount coupons to offer clients who book your consultations.</p>
        </div>

        <form action="<?= APP_URL ?>/promo-codes" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 font-sans">Promo Code</label>
                    <input type="text" name="code" required placeholder="OFFER15"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 font-sans">Discount Type</label>
                    <select name="discount_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all font-semibold">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount ($)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 font-sans">Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" required placeholder="15.00"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 font-sans">Max Uses (Optional)</label>
                    <input type="number" name="max_uses" placeholder="50"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 font-sans">Expiration Date (Optional)</label>
                    <input type="date" name="expires_at"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all font-semibold">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                    Create Promo Code
                </button>
            </div>
        </form>
    </div>

    <!-- Active Promo Codes List -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-slate-950 tracking-tight font-sans">Your Coupons</h2>
                <p class="text-slate-500 text-xs font-medium mt-1">List of active coupon offers and discount rates.</p>
            </div>
            <span class="text-xs text-slate-500 font-bold bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200"><?= count($promoCodes) ?> Coupons</span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200 font-sans">
                    <tr>
                        <th class="px-6 py-4">Promo Code</th>
                        <th class="px-6 py-4">Discount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Usage Stats</th>
                        <th class="px-6 py-4">Expires At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php if (empty($promoCodes)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400 italic">No coupons created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($promoCodes as $pc): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 font-mono text-sm font-bold text-slate-900">
                                    <?= htmlspecialchars($pc['code']) ?>
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-indigo-700 font-sans">
                                    <?php if ($pc['discount_type'] === 'percentage'): ?>
                                        <?= (float)$pc['discount_value'] ?>% Off
                                    <?php else: ?>
                                        $<?= number_format($pc['discount_value'], 2) ?> Off
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $isExpired = !empty($pc['expires_at']) && strtotime($pc['expires_at']) < time();
                                    $isLimitReached = !empty($pc['max_uses']) && $pc['used_count'] >= $pc['max_uses'];
                                    
                                    if ($isExpired): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-red-50 text-red-600 border border-red-200">Expired</span>
                                    <?php elseif ($isLimitReached): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-50 text-amber-600 border border-amber-200">Limit Reached</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700 font-sans">
                                    <?= $pc['used_count'] ?> / <?= $pc['max_uses'] ?? '∞' ?> uses
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-500 font-sans">
                                    <?= !empty($pc['expires_at']) ? date('F j, Y', strtotime($pc['expires_at'])) : 'Never' ?>
                                </td>
                                <td class="px-6 py-4 flex items-center justify-end">
                                    <form id="delete-promo-form-<?= $pc['id'] ?>" method="POST" action="<?= APP_URL ?>/promo-codes/delete/<?= $pc['id'] ?>" class="inline-block">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        <button type="button" onclick="confirmDeletePromo(<?= $pc['id'] ?>)" class="text-xs text-red-600 hover:text-red-700 font-bold px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60 font-sans">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 Delete script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeletePromo(promoId) {
        Swal.fire({
            title: 'Delete Coupon?',
            text: "Are you sure you want to delete this promo code? New booking clients won't be able to apply it.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Red 600
            cancelButtonColor: '#64748b',  // Slate 500
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-3xl border border-slate-200 shadow-xl font-sans text-left'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-promo-form-' + promoId).submit();
            }
        });
    }
</script>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
