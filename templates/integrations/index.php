<?php
$title = "Integrations & Apps";
$activeTab = "integrations";
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
    <div class="flex items-center justify-between bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-black text-white text-[10px] font-extrabold rounded-full uppercase tracking-wider mb-2">
                Apps & Ecosystem
            </div>
            <h1 class="text-3xl font-extrabold text-slate-950 tracking-tight">Integrations</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">Connect your calendars, video conferencing tools, and payment gateways.</p>
        </div>
        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200">
            <?= $isGoogleConnected ? '2 Active Apps' : '0 Apps Connected' ?>
        </span>
    </div>

    <!-- Integrations Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Google Calendar Integration Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center text-xl">
                            📅
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-950 text-base">Google Calendar</h3>
                            <p class="text-xs text-slate-500 font-medium">Calendar Synchronization</p>
                        </div>
                    </div>

                    <?php if ($isGoogleConnected): ?>
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Connected ✅
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full text-xs font-semibold">
                            Not Connected
                        </span>
                    <?php endif; ?>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Automatically sync all new bookings with your Google Calendar and read busy times to avoid double bookings.
                </p>

                <?php if ($isGoogleConnected): ?>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Connected Account:</span>
                            <span class="font-bold text-slate-900"><?= htmlspecialchars($googleAccount['google_email']) ?></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Selected Calendar:</span>
                            <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                <?= htmlspecialchars($googleAccount['calendar_id'] ?? 'primary') ?>
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Last Sync Status:</span>
                            <span class="text-emerald-700 font-semibold">✓ Active & Auto-Refreshed</span>
                        </div>

                        <?php if (!empty($calendars)): ?>
                            <form action="<?= APP_URL ?>/integrations/google/select-calendar" method="POST" class="pt-2 border-t border-slate-200/80 space-y-2">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <label class="block text-[10px] font-bold uppercase text-slate-600">Switch Target Calendar</label>
                                <div class="flex gap-2">
                                    <select name="calendar_id" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold">
                                        <?php foreach ($calendars as $cal): ?>
                                            <option value="<?= htmlspecialchars($cal['id']) ?>" <?= ($googleAccount['calendar_id'] ?? 'primary') === $cal['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cal['summary']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="px-3 py-1.5 bg-black text-white text-xs font-bold rounded-lg shadow-sm">
                                        Save
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <?php if ($isGoogleConnected): ?>
                    <form action="<?= APP_URL ?>/integrations/google/disconnect" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs rounded-xl border border-red-200 transition-colors">
                            Disconnect
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/integrations/google/connect" class="w-full py-3 bg-black hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all text-center">
                        Connect Google Calendar
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Google Meet Integration Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-xl">
                            📹
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-950 text-base">Google Meet</h3>
                            <p class="text-xs text-slate-500 font-medium">Video Conferencing</p>
                        </div>
                    </div>
                    <?php if ($isGoogleConnected): ?>
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active & Ready ✅
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full text-xs font-semibold">
                            Requires Google Auth
                        </span>
                    <?php endif; ?>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Automatically generate unique Google Meet video call links for all online consultation bookings.
                </p>

                <?php if ($isGoogleConnected): ?>
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs space-y-1">
                        <p class="font-bold text-emerald-900">✓ Auto-Provisioning Active</p>
                        <p class="text-[11px] text-emerald-800">Every new online appointment generates a dedicated Google Meet room automatically.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <?php if ($isGoogleConnected): ?>
                    <button disabled class="w-full py-2.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl cursor-default">
                        Bundled with Google OAuth
                    </button>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/integrations/google/connect" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl border border-slate-200 transition-colors block text-center">
                        Connect Google Account
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Outlook Calendar Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6 opacity-75">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center text-xl">
                            📫
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-950 text-base">Outlook Calendar</h3>
                            <p class="text-xs text-slate-500 font-medium">Microsoft 365</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 border border-slate-200 rounded-full text-xs font-bold">
                        Coming Soon
                    </span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Connect your Microsoft Outlook & Office 365 calendar to sync events and check busy times.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl cursor-not-allowed">
                    Coming Soon
                </button>
            </div>
        </div>

        <!-- Zoom Video Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6 opacity-75">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-center text-xl">
                            🎥
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-950 text-base">Zoom Video</h3>
                            <p class="text-xs text-slate-500 font-medium">Video Conferencing</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 border border-slate-200 rounded-full text-xs font-bold">
                        Coming Soon
                    </span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Auto-generate dynamic Zoom meeting links and attach them to client booking emails.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl cursor-not-allowed">
                    Coming Soon
                </button>
            </div>
        </div>

        <!-- Stripe Payment Gateway Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-50 border border-purple-200 rounded-2xl flex items-center justify-center text-xl">
                            💳
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-950 text-base">Stripe</h3>
                            <p class="text-xs text-slate-500 font-medium">Payment Processing</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold">
                        Active Mode
                    </span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Collect paid appointment fees upfront via Credit Cards & Apple Pay with Stripe.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-500">Configured in System Settings</span>
            </div>
        </div>

        <!-- Razorpay Payment Gateway Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center text-xl">
                            ⚡
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-950 text-base">Razorpay</h3>
                            <p class="text-xs text-slate-500 font-medium">UPI & NetBanking</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold">
                        Active Mode
                    </span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Accept UPI, Google Pay, PhonePe, and NetBanking payments for consultation sessions.
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-500">Configured in System Settings</span>
            </div>
        </div>

    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
