<?php
$title = "Profile & Settings";
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

    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Profile & Settings</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Manage your public avatar details, timezone, and intro bio.</p>
        </div>

        <form action="<?= APP_URL ?>/profile" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username (Public URL)</label>
                    <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled
                           class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-400 text-sm cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled
                           class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-400 text-sm cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" placeholder="+1 (555) 000-0000"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Timezone</label>
                <select name="timezone" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                    <?php
                    $timezones = DateTimeZone::listIdentifiers();
                    $currentTimezone = $profile['timezone'] ?? 'UTC';
                    foreach ($timezones as $tz) {
                        $selected = ($tz === $currentTimezone) ? 'selected' : '';
                        echo "<option value='{$tz}' {$selected}>{$tz}</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bio / Introduction</label>
                <textarea name="bio" rows="4" placeholder="Brief intro description shown on your public booking page..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md transition-all">
                    Save Profile
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
