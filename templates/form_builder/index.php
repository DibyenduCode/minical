<?php
$title = "Booking Form Builder";
$activeTab = "form_builder";
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

    <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Custom Booking Form Questions</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Create custom questions for specific consultation services (Personal Counselling, Family Counselling, Child Counselling) or all services.</p>
        </div>

        <form action="<?= APP_URL ?>/form-builder" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Apply to Service / Event *</label>
                    <select name="event_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="">All Services & Consultation Types</option>
                        <?php foreach ($events as $ev): ?>
                            <option value="<?= $ev['id'] ?>"><?= htmlspecialchars($ev['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Question / Field Label *</label>
                    <input type="text" name="label" required placeholder="e.g., What is your main area of concern?"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Field Input Type *</label>
                    <select name="field_type" id="field_type" onchange="toggleOptionsArea()" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="text">Single Line Text</option>
                        <option value="textarea">Multi-line Paragraph</option>
                        <option value="select">Dropdown Select</option>
                        <option value="radio">Radio Buttons (Single Choice)</option>
                        <option value="checkbox">Checkboxes (Multiple Choice)</option>
                        <option value="phone">Phone Number</option>
                        <option value="date">Date Picker</option>
                    </select>
                </div>
            </div>

            <div id="options-area" class="hidden">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Options List (One option per line)</label>
                <textarea name="options_raw" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-black"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Placeholder Text</label>
                    <input type="text" name="placeholder" placeholder="e.g., Please be specific..."
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Help / Instruction Text</label>
                    <input type="text" name="help_text" placeholder="e.g., Visible below the question field"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1" class="w-5 h-5 accent-black rounded">
                    <span class="text-xs font-bold text-slate-800">Mandatory Question (Required to complete booking)</span>
                </label>

                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                    Add Form Question
                </button>
            </div>
        </form>
    </div>

    <!-- Questions Table -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
        <h2 class="text-lg font-bold text-slate-950 tracking-tight">Active Consultation Questions</h2>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Question Label</th>
                        <th class="px-6 py-4">Assigned Consultation</th>
                        <th class="px-6 py-4">Input Type</th>
                        <th class="px-6 py-4">Required</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($fields)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400 font-medium">No custom form questions added yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fields as $f): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900"><?= htmlspecialchars($f['label']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border <?= !empty($f['event_name']) ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                                        <?= htmlspecialchars($f['event_name'] ?? 'All Consultation Types') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 uppercase text-xs font-semibold text-slate-600"><?= htmlspecialchars($f['field_type']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded <?= $f['is_required'] ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500' ?>">
                                        <?= $f['is_required'] ? 'Yes' : 'No' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="<?= APP_URL ?>/form-builder/delete/<?= $f['id'] ?>" onsubmit="return confirm('Delete this question?')">
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
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

<script>
    function toggleOptionsArea() {
        const type = document.getElementById('field_type').value;
        const area = document.getElementById('options-area');
        if (['select', 'radio', 'checkbox'].includes(type)) {
            area.classList.remove('hidden');
        } else {
            area.classList.add('hidden');
        }
    }
</script>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
