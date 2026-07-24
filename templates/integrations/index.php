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
                    <?php if (($dbUser['plan'] ?? 'free') === 'free'): ?>
                        <div class="w-full flex flex-col gap-2">
                            <button disabled class="w-full py-3 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed text-center flex items-center justify-center gap-1.5">
                                🔒 Locked (Growth/Pro Plan)
                            </button>
                            <span class="text-[10px] text-slate-400 font-semibold text-center">Google Calendar requires the Growth or Pro plan.</span>
                        </div>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/integrations/google/connect" class="w-full py-3 bg-black hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all text-center">
                            Connect Google Calendar
                        </a>
                    <?php endif; ?>
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
                    <?php if (($dbUser['plan'] ?? 'free') === 'free'): ?>
                        <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl border border-slate-200 cursor-not-allowed text-center">
                            🔒 Locked (Growth/Pro Plan)
                        </button>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/integrations/google/connect" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl border border-slate-200 transition-colors block text-center">
                            Connect Google Account
                        </a>
                    <?php endif; ?>
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

    <!-- Developer API Key Card -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-sm space-y-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-extrabold rounded-full uppercase tracking-wider mb-2">
                Developer API Access
            </div>
            <h2 class="text-xl font-extrabold text-slate-950 tracking-tight">API Key Management</h2>
            <p class="text-slate-500 text-xs font-medium">Generate an API key to securely connect DayCal scheduling to your custom web app or software.</p>
        </div>

        <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-4 font-sans">
            <div class="flex flex-col gap-1.5">
                <span class="text-xs font-bold text-slate-600">Your Developer API Token:</span>
                <?php if (!empty($apiKey)): ?>
                    <div class="flex items-center gap-3">
                        <input type="text" id="api_key_input" readonly value="<?= htmlspecialchars($apiKey) ?>"
                               class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-900 font-mono text-xs select-all focus:outline-none focus:ring-1 focus:ring-black">
                        <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($apiKey) ?>'); alert('API Key copied to clipboard!')"
                                class="px-4 py-2.5 bg-black hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex-shrink-0">
                            Copy Key
                        </button>
                    </div>
                <?php else: ?>
                    <span class="text-xs text-slate-400 font-semibold italic">No API key generated yet.</span>
                <?php endif; ?>
            </div>

            <div class="pt-4 border-t border-slate-200/80 flex items-center justify-between flex-wrap gap-4">
                <form action="<?= APP_URL ?>/integrations/api-key/generate" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                        <?= empty($apiKey) ? '⚡ Generate API Key' : '🔄 Regenerate Key' ?>
                    </button>
                </form>

                <?php if (!empty($apiKey)): ?>
                    <form action="<?= APP_URL ?>/integrations/api-key/revoke" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <button type="submit" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-bold rounded-xl transition-all">
                            Revoke Key
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mini Documentation -->
        <div class="space-y-4 pt-2">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">REST API Documentation & Usage:</h3>
            <div class="bg-slate-900 rounded-2xl p-6 text-slate-200 text-xs font-mono space-y-4 overflow-x-auto">
                <div>
                    <span class="text-indigo-400"># Authenticate request header:</span>
                    <p class="text-emerald-400">Authorization: Bearer YOUR_API_KEY</p>
                </div>
                
                <div class="space-y-1">
                    <span class="text-indigo-400"># 1. Fetch & Update Profile</span>
                    <p class="text-slate-300"><span class="text-amber-400">GET</span> <?= APP_URL ?>/api/v1/profile</p>
                    <p class="text-slate-300"><span class="text-indigo-400">POST</span> <?= APP_URL ?>/api/v1/profile</p>
                </div>

                <div class="space-y-1">
                    <span class="text-indigo-400"># 2. Fetch Event Types / Services</span>
                    <p class="text-slate-300"><span class="text-amber-400">GET</span> <?= APP_URL ?>/api/v1/events</p>
                </div>

                <div class="space-y-1">
                    <span class="text-indigo-400"># 3. Fetch Availability Slots</span>
                    <p class="text-slate-300"><span class="text-amber-400">GET</span> <?= APP_URL ?>/api/v1/availability</p>
                </div>

                <div class="space-y-1">
                    <span class="text-indigo-400"># 4. Fetch Bookings</span>
                    <p class="text-slate-300"><span class="text-amber-400">GET</span> <?= APP_URL ?>/api/v1/bookings</p>
                </div>

                <div class="space-y-1">
                    <span class="text-indigo-400"># 5. Fetch Custom Form Fields</span>
                    <p class="text-slate-300"><span class="text-amber-400">GET</span> <?= APP_URL ?>/api/v1/form-fields</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/layout/footer.php'; ?>
