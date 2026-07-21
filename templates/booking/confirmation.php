<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - MiniCal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex items-center justify-center p-4 selection:bg-black selection:text-white">
    <div class="w-full max-w-lg bg-white border border-slate-200/90 rounded-3xl shadow-xl p-8 space-y-6 text-center">
        
        <!-- Success Icon -->
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold shadow-sm">
            ✓
        </div>

        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                <?= ucfirst($booking['status']) ?>
            </span>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight mt-3">Appointment Confirmed!</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">A calendar invitation and confirmation email have been generated.</p>
        </div>

        <!-- Appointment Card -->
        <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl text-left space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Service</span>
                <span class="text-sm font-extrabold text-slate-950"><?= htmlspecialchars($event['name']) ?></span>
            </div>

            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Host</span>
                <span class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($hostUser['name']) ?></span>
            </div>

            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Date</span>
                <span class="text-sm font-semibold text-slate-900"><?= date('F j, Y', strtotime($booking['booking_date'])) ?></span>
            </div>

            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Time</span>
                <span class="text-sm font-semibold text-slate-900"><?= date('h:i A', strtotime($booking['start_time'])) ?> - <?= date('h:i A', strtotime($booking['end_time'])) ?></span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Client Email</span>
                <span class="text-xs font-medium text-slate-700"><?= htmlspecialchars($booking['customer_email']) ?></span>
            </div>
        </div>

        <!-- Google Meet Video Call Button -->
        <?php if (!empty($booking['meeting_link'])): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-2 text-left">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">📹 Google Meet Video Room</span>
                    <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Ready</span>
                </div>
                <a href="<?= htmlspecialchars($booking['meeting_link']) ?>" target="_blank"
                   class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <span>Join Google Meet Call</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </a>
                <p class="text-[11px] text-emerald-700 font-mono truncate">Link: <?= htmlspecialchars($booking['meeting_link']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Google Calendar Integration Button & Badge -->
        <div class="space-y-3 pt-2">
            <?php if (!empty($googleConnected)): ?>
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-2xl flex items-center justify-center gap-2 text-blue-800 text-xs font-bold">
                    <span>📅 Auto-synced with Host's Google Calendar</span>
                </div>
            <?php endif; ?>

            <a href="<?= $googleCalendarUrl ?>" target="_blank"
               class="w-full py-3.5 px-4 bg-black hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                <span>➕ Add to my Google Calendar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>

        <div class="pt-2">
            <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($hostUser['username']) ?>" class="text-xs font-bold text-slate-500 hover:text-black transition-colors">
                ← Book Another Appointment
            </a>
        </div>
    </div>
</body>
</html>
