<?php
$title = "Event Types";
$activeTab = "event";
require_once TEMPLATES_DIR . '/layout/header.php';
?>

<div class="max-w-7xl mx-auto space-y-8">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Event Types</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Create and configure your consultation services, pricing, and scheduling rules.</p>
        </div>
        <div>
            <?php
            $maxEvents = isset($planDetails['max_events']) ? (int)$planDetails['max_events'] : -1;
            $hasReachedLimit = ($maxEvents !== -1 && count($events) >= $maxEvents);
            ?>
            <?php if ($hasReachedLimit): ?>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3.5 py-2.5 rounded-xl">
                        ⚠️ Plan Limit: <?= $maxEvents ?> Event Type<?= $maxEvents > 1 ? 's' : '' ?> max
                    </span>
                    <a href="<?= APP_URL ?>/profile" class="px-4 py-2.5 bg-black hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                        Upgrade Plan
                    </a>
                </div>
            <?php else: ?>
                <button type="button" onclick="document.getElementById('new-event-form').classList.toggle('hidden')"
                        class="px-5 py-3 bg-black hover:bg-slate-800 text-white text-xs font-bold rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <span class="text-sm">+</span> New Event Type
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Event Type Form Drawer -->
    <div id="new-event-form" class="hidden bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
        <h2 class="text-lg font-bold text-slate-950 tracking-tight">Create a New Consultation Service</h2>

        <form action="<?= APP_URL ?>/event/create" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Service Title</label>
                    <input type="text" name="name" placeholder="e.g. 1:1 Business Strategy Session" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">URL Slug</label>
                    <input type="text" name="slug" placeholder="e.g. strategy-session" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Duration</label>
                    <select name="duration_minutes"
                            class="w-full px-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="15">15 Mins</option>
                        <option value="30" selected>30 Mins</option>
                        <option value="45">45 Mins</option>
                        <option value="60">60 Mins</option>
                        <option value="90">90 Mins</option>
                        <option value="120">120 Mins</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Buffer Time (Padding)</label>
                    <select name="buffer_minutes"
                            class="w-full px-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="0" selected>No Buffer</option>
                        <option value="10">10 Mins</option>
                        <option value="15">15 Mins</option>
                        <option value="20">20 Mins</option>
                        <option value="30">30 Mins</option>
                        <option value="45">45 Mins</option>
                        <option value="60">60 Mins (1h)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Booking Window (Advance Booking)</label>
                    <select name="booking_window_days"
                            class="w-full px-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="7">7 Days Advance</option>
                        <option value="14">14 Days Advance</option>
                        <option value="30" selected>30 Days Advance</option>
                        <option value="60">60 Days Advance</option>
                        <option value="90">90 Days Advance</option>
                        <option value="180">180 Days Advance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Meeting Location</label>
                    <select name="location_type"
                            class="w-full px-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="online">Online (Google Meet Video Room)</option>
                        <option value="phone">Phone Call</option>
                        <option value="in_person">In Person</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                <textarea name="description" placeholder="A brief description of what this consultation covers..." rows="3"
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black"></textarea>
            </div>

            <!-- Pricing & Payments -->
            <div class="border-t border-slate-100 pt-4 space-y-4">
                <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-950">Require Payment</h4>
                        <p class="text-slate-500 text-[10px] font-medium mt-0.5">Prompt clients to complete payment before booking.</p>
                    </div>
                    <input type="checkbox" name="is_paid" value="1" class="w-5 h-5 accent-black rounded">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Consultation Price</label>
                        <input type="number" step="0.01" name="price" value="0.00"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Currency</label>
                        <select name="currency"
                                class="w-full px-3 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="USD" selected>USD ($)</option>
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
                <?php $winDays = (int)($ev['booking_window_days'] ?? 30); ?>
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
                            <h3 class="text-lg font-extrabold text-slate-950 tracking-tight flex items-center gap-2">
                                <span><?= htmlspecialchars($ev['name']) ?></span>
                                <?php if ($ev['status'] !== 'active'): ?>
                                    <span class="text-[9px] bg-red-100 text-red-800 px-1.5 py-0.5 rounded uppercase font-bold">Inactive</span>
                                <?php endif; ?>
                            </h3>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">/u/<?= htmlspecialchars($user['username']) ?>/<?= htmlspecialchars($ev['slug']) ?></p>
                        </div>

                        <?php if (!empty($ev['description'])): ?>
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed"><?= htmlspecialchars($ev['description']) ?></p>
                        <?php endif; ?>

                        <div class="pt-2 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
                            <span class="px-2 py-0.5 bg-slate-100 rounded text-[11px] font-bold text-slate-700">📅 <?= $winDays ?> Days Advance</span>
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
                                <button type="button" onclick="openEditModal(<?= $ev['id'] ?>)"
                                        class="text-xs font-semibold text-slate-800 hover:text-black bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl border border-slate-200 transition-colors">
                                    Edit
                                </button>

                                <form id="delete-event-form-<?= $ev['id'] ?>" method="POST" action="<?= APP_URL ?>/event/delete/<?= $ev['id'] ?>">
                                    <button type="button" onclick="confirmDeleteEvent(<?= $ev['id'] ?>)" class="text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-xl border border-red-200 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Modal for Editing Event Type -->
                <div id="edit-modal-<?= $ev['id'] ?>" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden transition-all duration-200 transform scale-95">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Edit Consultation Service</h3>
                            <button type="button" onclick="closeEditModal(<?= $ev['id'] ?>)" class="text-slate-400 hover:text-slate-600 font-extrabold text-sm">✕</button>
                        </div>
                        <form action="<?= APP_URL ?>/event/update/<?= $ev['id'] ?>" method="POST" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto text-left">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Title</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($ev['name']) ?>" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">URL Slug</label>
                                <input type="text" name="slug" value="<?= htmlspecialchars($ev['slug']) ?>" required class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Duration</label>
                                    <select name="duration_minutes" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                        <?php foreach ([15, 30, 45, 60, 90, 120] as $d): ?>
                                            <option value="<?= $d ?>" <?= $ev['duration_minutes'] == $d ? 'selected' : '' ?>><?= $d ?> Mins</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Buffer Time</label>
                                    <select name="buffer_minutes" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                        <?php foreach ([0, 10, 15, 20, 30, 45, 60] as $bM): ?>
                                            <option value="<?= $bM ?>" <?= ($ev['buffer_minutes'] ?? 0) == $bM ? 'selected' : '' ?>><?= $bM == 0 ? 'No Buffer' : $bM . ' Mins' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Advance Booking</label>
                                    <select name="booking_window_days" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                        <?php foreach ([7, 14, 30, 60, 90, 180] as $w): ?>
                                            <option value="<?= $w ?>" <?= $winDays == $w ? 'selected' : '' ?>><?= $w ?> Days</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Location</label>
                                <select name="location_type" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                    <option value="online" <?= $ev['location_type'] === 'online' ? 'selected' : '' ?>>Online</option>
                                    <option value="phone" <?= $ev['location_type'] === 'phone' ? 'selected' : '' ?>>Phone</option>
                                    <option value="in_person" <?= $ev['location_type'] === 'in_person' ? 'selected' : '' ?>>In Person</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Description</label>
                                <textarea name="description" rows="2" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg"><?= htmlspecialchars($ev['description'] ?? '') ?></textarea>
                            </div>

                            <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200/60">
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

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-600 mb-1">Status</label>
                                <select name="status" class="w-full px-2.5 py-2 text-xs bg-white border border-slate-200 rounded-lg">
                                    <option value="active" <?= $ev['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $ev['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" onclick="closeEditModal(<?= $ev['id'] ?>)" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-black hover:bg-slate-800 rounded-xl shadow-md transition-all">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- SweetAlert2 Deletion Confirmation script and Modal controls -->
<script>
    function confirmDeleteEvent(eventId) {
        Swal.fire({
            title: 'Delete Event Type?',
            text: "Are you sure you want to delete this consultation service? This action cannot be undone.",
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
                document.getElementById('delete-event-form-' + eventId).submit();
            }
        });
    }

    function openEditModal(id) {
        const modal = document.getElementById('edit-modal-' + id);
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const inner = modal.querySelector('.transform');
                if (inner) {
                    inner.classList.remove('scale-95');
                    inner.classList.add('scale-100');
                }
            }, 10);
        }
    }

    function closeEditModal(id) {
        const modal = document.getElementById('edit-modal-' + id);
        if (modal) {
            const inner = modal.querySelector('.transform');
            if (inner) {
                inner.classList.remove('scale-100');
                inner.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }
    }
</script>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
