<?php
$title = "Pricing Plans - Super Admin";
$adminTab = "plans";
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

    <!-- Add New Plan Card -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Pricing Plans Management</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Create and customize pricing tiers displayed dynamically on the landing page.</p>
        </div>

        <form action="<?= APP_URL ?>/admin/plans" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Plan Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Teams, Enterprise"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Price *</label>
                    <input type="text" name="price" required placeholder="e.g. Free, $12, $28, Custom"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Billing Cycle</label>
                    <input type="text" name="billing_cycle" placeholder="e.g. per month / user, annual"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Badge Tag</label>
                    <input type="text" name="badge" placeholder="e.g. Most Popular, 14 day free trial"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Button Text</label>
                    <input type="text" name="button_text" value="Try for free" placeholder="e.g. Use for free, Talk to sales"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Display Order</label>
                    <input type="number" name="display_order" value="1"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Plan Description</label>
                <textarea name="description" rows="2" placeholder="Brief summary of who this plan is designed for..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Features Checklist (One feature per line)</label>
                <textarea name="features_raw" rows="4" placeholder="1 user account&#10;Unlimited booking links&#10;Google Calendar sync&#10;Accept payments"
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all"></textarea>
            </div>

            <div class="flex items-center gap-6 pt-1">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="w-5 h-5 accent-black rounded">
                    <span class="text-xs font-bold text-slate-800">Highlight as Featured Dark Card (Cal.com style)</span>
                </label>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                    Create Pricing Plan
                </button>
            </div>
        </form>
    </div>

    <!-- Active Plans List Grid -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-950 tracking-tight">Current Landing Page Tiers</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($plans as $p): ?>
                <?php $features = !empty($p['features']) ? json_decode($p['features'], true) : []; ?>
                <div class="rounded-3xl p-6 border transition-all flex flex-col justify-between <?= !empty($p['is_featured']) ? 'bg-slate-950 text-white border-slate-900 shadow-xl' : 'bg-slate-50/80 text-slate-900 border-slate-200' ?>">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider <?= !empty($p['is_featured']) ? 'text-indigo-400 bg-indigo-500/20 px-3 py-1 rounded-full' : 'text-slate-600 bg-slate-200 px-3 py-1 rounded-full' ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </span>
                            <?php if (!empty($p['badge'])): ?>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-500 bg-amber-500/10 px-2 py-0.5 rounded">
                                    <?= htmlspecialchars($p['badge']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <span class="text-3xl font-extrabold"><?= htmlspecialchars($p['price']) ?></span>
                            <span class="text-xs text-slate-400">/ <?= htmlspecialchars($p['billing_cycle']) ?></span>
                        </div>

                        <p class="text-xs <?= !empty($p['is_featured']) ? 'text-slate-300' : 'text-slate-500' ?>"><?= htmlspecialchars($p['description']) ?></p>

                        <ul class="space-y-2 text-xs border-t <?= !empty($p['is_featured']) ? 'border-slate-800' : 'border-slate-200' ?> pt-4">
                            <?php foreach ($features as $f): ?>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 <?= !empty($p['is_featured']) ? 'text-indigo-400' : 'text-slate-700' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span><?= htmlspecialchars($f) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="pt-6">
                        <form id="delete-plan-form-<?= $p['id'] ?>" method="POST" action="<?= APP_URL ?>/admin/plans/delete/<?= $p['id'] ?>">
                            <button type="button" onclick="confirmDeletePlan(<?= $p['id'] ?>)" class="w-full py-2 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 transition-colors">
                                Delete Plan
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- SweetAlert2 Deletion Confirmation script -->
<script>
    function confirmDeletePlan(planId) {
        Swal.fire({
            title: 'Delete Plan Tier?',
            text: "Are you sure you want to delete this pricing plan? This action cannot be undone and it will be removed from the landing page.",
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
                document.getElementById('delete-plan-form-' + planId).submit();
            }
        });
    }
</script>

<?php require_once TEMPLATES_DIR . '/admin/layout/footer.php'; ?>
