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
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white border border-slate-200/90 rounded-3xl shadow-xl shadow-slate-200/50 p-8 text-center space-y-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full border border-emerald-200 mb-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Booking Confirmed!</h1>
            <p class="text-slate-500 text-xs font-medium mt-1">A calendar invitation has been sent to your email.</p>
        </div>

        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 text-left space-y-4">
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Host</span>
                <p class="font-bold text-slate-950 text-sm"><?= htmlspecialchars($hostUser['name']) ?></p>
            </div>

            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Event</span>
                <p class="font-bold text-slate-950 text-sm"><?= htmlspecialchars($event['name']) ?></p>
            </div>

            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Date & Time</span>
                <p class="font-bold text-slate-950 text-sm"><?= date('F j, Y', strtotime($booking['booking_date'])) ?></p>
                <p class="text-xs font-semibold text-slate-600"><?= date('h:i A', strtotime($booking['start_time'])) ?> - <?= date('h:i A', strtotime($booking['end_time'])) ?></p>
            </div>

            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Status</span>
                <span class="block w-fit px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase mt-1">
                    <?= htmlspecialchars($booking['status']) ?>
                </span>
            </div>
        </div>

        <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($hostUser['username']) ?>"
           class="inline-block w-full py-3 px-4 bg-black hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md transition-all">
            Book Another Meeting
        </a>
    </div>
</body>
</html>
