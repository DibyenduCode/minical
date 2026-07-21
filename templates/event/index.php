<?php
$title = "Event Types";
$activeTab = "event";
require_once TEMPLATES_DIR . '/layout/header.php';
?>

<div class="max-w-5xl mx-auto space-y-8">
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

    <!-- Header Banner -->
    <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Consultation Services & Event Types</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Create and edit multiple booking services (e.g. Personal Counselling, Family Counselling, Child Counselling).</p>
        </div>
        <button type="button" onclick="document.getElementById('new-event-form').classList.toggle('hidden')" class="px-5 py-2.5 bg-black hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all">
            + New Event Type
        </button>
    </div>

    <!-- Create New Event Form (Hidden by default) -->
    <div id="new-event-form" class="hidden bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-950 tracking-tight">Create New Consultation Service</h2>

        <form action="<?= APP_URL ?>/event" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Event Title *</label>
                    <input type="text" name="name" required placeholder="e.g., Family Counselling Session"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">URL Slug *</label>
                    <input type="text" name="slug" required placeholder="e.g., family-counselling"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Duration (Minutes)</label>
                    <select name="duration_minutes" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="15">15 Minutes</option>
                        <option value="30" selected>30 Minutes</option>
                        <option value="45">45 Minutes</option>
                        <option value="60">60 Minutes</option>
                        <option value="90">90 Minutes</option>
                        <option value="120">120 Minutes</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location Type</label>
                    <select name="location_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="online">Google Meet / Zoom (Online)</option>
                        <option value="phone">Phone Call</option>
                        <option value="in_person">In Person</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Service Description</label>
                <textarea name="description" rows="2" placeholder="Brief summary of what this consultation covers..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black"></textarea>
            </div>

            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Require Payment</h3>
                        <p class="text-slate-500 text-xs mt-0.5">Collect upfront payment via Stripe or Razorpay.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_paid" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Price Amount</label>
                        <input type="number" step="0.01" name="price" value="0.00"
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

            <div class="pt-2 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('new-event-form').classList.add('hidden')" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                    Create Service
                </button>
            </div>
        </form>
    </div>

    <!-- Active Event Types List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($events)): ?>
            <div class="col-span-full bg-white border border-slate-200 rounded-3xl p-12 text-center text-slate-400 font-medium">
                No consultation services created yet. Click "+ New Event Type" above to get started.
            </div>
        <?php else: ?>
            <?php foreach ($events as $ev): ?>
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 bg-slate-100 text-slate-800 rounded-full border border-slate-200">
                                <?= $ev['duration_minutes'] ?> Mins
                            </span>
                            <?php if (!empty($ev['is_paid'])): ?>
                                <span class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    $<?= number_format($ev['price'], 2) ?> <?= htmlspecialchars($ev['currency']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">$0.00 USD</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <h3 class="text-lg font-extrabold text-slate-950 tracking-tight"><?= htmlspecialchars($ev['name']) ?></h3>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">/u/<?= htmlspecialchars($user['username']) ?>/<?= htmlspecialchars($ev['slug']) ?></p>
                        </div>

                        <?php if (!empty($ev['description'])): ?>
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed"><?= htmlspecialchars($ev['description']) ?></p>
                        <?php endif; ?>

                        <div class="pt-2 flex items-center gap-3 text-xs font-semibold text-slate-500">
                            <span>📍 <?= ucfirst($ev['location_type']) ?></span>
                            <span>•</span>
                            <a href="<?= APP_URL ?>/form-builder" class="text-black font-bold hover:underline">Form Questions →</a>
                        </div>
                    </div>

                    <!-- Action Buttons: Preview, Edit, Delete -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($user['username']) ?>?event=<?= $ev['slug'] ?>" target="_blank"
                               class="text-xs font-bold text-black hover:underline flex items-center gap-1">
                                <span>Preview Booking Link</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="document.getElementById('edit-event-form-<?= $ev['id'] ?>').classList.toggle('hidden')"
                                        class="text-xs font-semibold text-slate-800 hover:text-black bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl border border-slate-200 transition-colors">
                                    Edit
                                </button>

                                <form method="POST" action="<?= APP_URL ?>/event/delete/<?= $ev['id'] ?>" onsubmit="return confirm('Delete this consultation event?')">
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-xl border border-red-200 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Inline Edit Form (Hidden by default) -->
                        <div id="edit-event-form-<?= $ev['id'] ?>" class="hidden bg-slate-50 border border-slate-200 rounded-2xl p-4 text-left space-y-4 mt-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Edit Service Details</h4>
                            <form action="<?= APP_URL ?>/event/update/<?= $ev['id'] ?>" method="POST" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Title</label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($ev['name']) ?>" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">URL Slug</label>
                                    <input type="text" name="slug" value="<?= htmlspecialchars($ev['slug']) ?>" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Duration</label>
                                        <select name="duration_minutes" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                            <?php foreach ([15, 30, 45, 60, 90, 120] as $d): ?>
                                                <option value="<?= $d ?>" <?= $ev['duration_minutes'] == $d ? 'selected' : '' ?>><?= $d ?> Mins</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Location</label>
                                        <select name="location_type" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                            <option value="online" <?= $ev['location_type'] === 'online' ? 'selected' : '' ?>>Online</option>
                                            <option value="phone" <?= $ev['location_type'] === 'phone' ? 'selected' : '' ?>>Phone</option>
                                            <option value="in_person" <?= $ev['location_type'] === 'in_person' ? 'selected' : '' ?>>In Person</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Description</label>
                                    <textarea name="description" rows="2" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg"><?= htmlspecialchars($ev['description'] ?? '') ?></textarea>
                                </div>

                                <div class="flex items-center justify-between bg-white p-2.5 rounded-lg border border-slate-200">
                                    <span class="text-xs font-bold text-slate-700">Require Payment</span>
                                    <input type="checkbox" name="is_paid" value="1" <?= !empty($ev['is_paid']) ? 'checked' : '' ?> class="w-4 h-4 accent-black">
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Price</label>
                                        <input type="number" step="0.01" name="price" value="<?= $ev['price'] ?>" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Currency</label>
                                        <select name="currency" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                            <option value="USD" <?= $ev['currency'] === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                            <option value="EUR" <?= $ev['currency'] === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                            <option value="GBP" <?= $ev['currency'] === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                            <option value="INR" <?= $ev['currency'] === 'INR' ? 'selected' : '' ?>>INR (₹)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 pt-1">
                                    <button type="button" onclick="document.getElementById('edit-event-form-<?= $ev['id'] ?>').classList.add('hidden')" class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-slate-200 rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-1.5 text-xs font-bold text-white bg-black hover:bg-slate-800 rounded-lg shadow-sm">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
