<?php
$title = "Event Types";
$activeTab = "event";
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
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Event Settings</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Configure meeting title, duration, location, and pricing details.</p>
        </div>

        <form action="<?= APP_URL ?>/event" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Event Title</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($event['name'] ?? '30 Minute Meeting') ?>" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">URL Slug</label>
                    <input type="text" name="slug" value="<?= htmlspecialchars($event['slug'] ?? '30-min-meeting') ?>" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Duration (Minutes)</label>
                    <select name="duration_minutes" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                        <?php
                        $durations = [15, 30, 45, 60, 90, 120];
                        $currentDur = (int)($event['duration_minutes'] ?? 30);
                        foreach ($durations as $d) {
                            $selected = ($d === $currentDur) ? 'selected' : '';
                            echo "<option value='{$d}' {$selected}>{$d} Minutes</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location Type</label>
                    <select name="location_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                        <option value="online" <?= (($event['location_type'] ?? '') === 'online') ? 'selected' : '' ?>>Google Meet / Zoom (Online)</option>
                        <option value="phone" <?= (($event['location_type'] ?? '') === 'phone') ? 'selected' : '' ?>>Phone Call</option>
                        <option value="in_person" <?= (($event['location_type'] ?? '') === 'in_person') ? 'selected' : '' ?>>In Person</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Event Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            </div>

            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Require Payment for Booking</h3>
                        <p class="text-slate-500 text-xs mt-0.5">Collect upfront payment via Stripe or Razorpay before confirming appointments.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_paid" value="1" <?= !empty($event['is_paid']) ? 'checked' : '' ?> class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Price Amount</label>
                        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($event['price'] ?? '0.00') ?>"
                               class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Currency</label>
                        <select name="currency" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="INR">INR (₹)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md transition-all">
                    Save Event Settings
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
