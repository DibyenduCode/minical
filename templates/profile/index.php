<?php
$title = "Profile Settings";
$activeTab = "profile";
require_once TEMPLATES_DIR . '/layout/header.php';
?>

<div class="max-w-4xl mx-auto space-y-8">
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

    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Profile & Branding Settings</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Manage your account details, timezone, and custom white-label domain.</p>
        </div>

        <form action="<?= APP_URL ?>/profile" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                           class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-400 text-sm cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="+1 (555) 000-0000"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Timezone</label>
                    <select name="timezone" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <?php
                        $tzs = ['UTC', 'America/New_York', 'America/Los_Angeles', 'Europe/London', 'Asia/Kolkata', 'Asia/Dhaka', 'Asia/Tokyo'];
                        $currentTz = $profile['timezone'] ?? 'UTC';
                        foreach ($tzs as $t) {
                            $selected = ($t === $currentTz) ? 'selected' : '';
                            echo "<option value='{$t}' {$selected}>{$t}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Custom Branded Domain Section (Cal.com style) -->
            <div class="p-6 bg-slate-50 border border-slate-200/90 rounded-2xl space-y-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span>🌐 Custom Branded Domain (Cal.com Style White-Label)</span>
                    </h3>
                    <p class="text-slate-500 text-xs mt-1">Serve your booking page on your own custom domain or subdomain (e.g., <code class="font-bold text-slate-800">booking.dibyendu.in</code>).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Your Custom Domain / Subdomain</label>
                    <input type="text" name="custom_domain" value="<?= htmlspecialchars($profile['custom_domain'] ?? '') ?>" placeholder="booking.dibyendu.in"
                           class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-2 text-xs text-slate-600">
                    <p class="font-bold text-slate-900">DNS Setup Instructions for your domain registrar:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-600 font-mono text-[11px]">
                        <li>Record Type: <strong class="text-black">CNAME</strong></li>
                        <li>Name / Host: <strong class="text-black">booking</strong> (or your subdomain name)</li>
                        <li>Target / Value: <strong class="text-black">xyz.com</strong> (or your main platform domain)</li>
                    </ul>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bio / Description</label>
                <textarea name="bio" rows="3" placeholder="Tell clients about yourself..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md transition-all">
                    Save Profile Settings
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
