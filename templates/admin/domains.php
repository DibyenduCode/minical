<?php
$title = "Custom Domains - Super Admin";
$adminTab = "domains";
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
    <div class="flex items-center justify-between bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-black text-white text-[10px] font-extrabold rounded-full uppercase tracking-wider mb-2">
                White-Label Domain Manager
            </div>
            <h1 class="text-3xl font-extrabold text-slate-950 tracking-tight">Configured Custom Branded Domains</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Overview of which registered users have connected white-label custom domains (Cal.com CNAME feature).</p>
        </div>
        <span class="text-xs text-slate-700 font-bold bg-slate-100 px-4 py-2 rounded-xl border border-slate-200"><?= count($domainUsers) ?> Domains Active</span>
    </div>

    <!-- Active Custom Domains Table -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 space-y-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-950 tracking-tight">User Domain Mappings</h2>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">User Details</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Configured Custom Domain</th>
                        <th class="px-6 py-4">CNAME Target</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($domainUsers)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400 font-medium">
                                No custom branded domains configured yet by users. Users can enter their domain in Profile Settings (<code class="font-bold text-black">/profile</code>).
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($domainUsers as $du): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($du['name']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($du['email']) ?></p>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">/u/<?= htmlspecialchars($du['username']) ?></td>
                                <td class="px-6 py-4 font-semibold text-slate-900 text-xs">
                                    <a href="http://<?= htmlspecialchars($du['custom_domain']) ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 font-mono font-bold rounded-full border border-indigo-200 hover:underline">
                                        🌐 <?= htmlspecialchars($du['custom_domain']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">CNAME ➔ xyz.com</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="http://<?= htmlspecialchars($du['custom_domain']) ?>" target="_blank" class="text-xs font-bold text-black hover:underline inline-flex items-center gap-1">
                                        <span>Visit Branded Page</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Registered Users Reference Table -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 space-y-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-950 tracking-tight">All Platform Users Domain Status</h2>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Username</th>
                        <th class="px-6 py-3.5">Custom Branded Domain</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($allUsers as $au): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5">
                                <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($au['name']) ?></p>
                                <p class="text-xs text-slate-500"><?= htmlspecialchars($au['email']) ?></p>
                            </td>
                            <td class="px-6 py-3.5 font-mono text-xs text-slate-600">/u/<?= htmlspecialchars($au['username']) ?></td>
                            <td class="px-6 py-3.5">
                                <?php if (!empty($au['custom_domain'])): ?>
                                    <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-200 font-mono">
                                        🌐 <?= htmlspecialchars($au['custom_domain']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">None configured</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $au['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
                                    <?= ucfirst($au['status']) ?>
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
