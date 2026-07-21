<?php
$title = "Booking Form Builder";
$activeTab = "form-builder";
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

    <!-- Add Custom Field Form -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Booking Form Builder</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Add custom fields to collect extra information from clients when booking.</p>
        </div>

        <form action="<?= APP_URL ?>/form-builder" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Field Label</label>
                    <input type="text" name="label" required placeholder="e.g. Phone Number, Company Name"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Field Type</label>
                    <select name="field_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                        <option value="text">Text Input</option>
                        <option value="textarea">Textarea (Multi-line)</option>
                        <option value="phone">Phone Input</option>
                        <option value="number">Number Input</option>
                        <option value="select">Dropdown Select</option>
                        <option value="radio">Radio Buttons</option>
                        <option value="checkbox">Checkboxes</option>
                        <option value="yes_no">Yes / No Switch</option>
                        <option value="url">Website URL</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Placeholder</label>
                    <input type="text" name="placeholder" placeholder="Optional placeholder text..."
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Options (Comma Separated for Select)</label>
                    <input type="text" name="options_raw" placeholder="Option 1, Option 2, Option 3"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center gap-6 pt-1">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1" class="w-5 h-5 accent-black rounded">
                    <span class="text-xs font-bold text-slate-800">Required Field</span>
                </label>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-black hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md transition-all">
                    Add Field
                </button>
            </div>
        </form>
    </div>

    <!-- Active Form Fields List -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 space-y-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-950 tracking-tight">Active Form Fields</h2>

        <div class="space-y-3">
            <!-- Default mandatory fields -->
            <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold bg-slate-200 text-slate-800 px-2 py-0.5 rounded uppercase">Default</span>
                    <span class="font-bold text-slate-900 text-sm ml-2">Full Name</span>
                    <span class="text-xs text-slate-500 ml-2">(Text input, Required)</span>
                </div>
            </div>
            <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold bg-slate-200 text-slate-800 px-2 py-0.5 rounded uppercase">Default</span>
                    <span class="font-bold text-slate-900 text-sm ml-2">Email Address</span>
                    <span class="text-xs text-slate-500 ml-2">(Email input, Required)</span>
                </div>
            </div>

            <!-- Custom fields -->
            <?php if (empty($fields)): ?>
                <p class="text-xs text-slate-400 py-4 text-center">No custom fields added yet.</p>
            <?php else: ?>
                <?php foreach ($fields as $f): ?>
                    <div class="p-4 bg-white border border-slate-200 rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($f['label']) ?></span>
                            <span class="text-xs text-slate-500 ml-2">(<?= ucfirst($f['field_type']) ?><?= $f['is_required'] ? ', Required' : '' ?>)</span>
                        </div>

                        <form method="POST" action="<?= APP_URL ?>/form-builder/delete/<?= $f['id'] ?>" onsubmit="return confirm('Delete this field?')">
                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
                                Delete
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
