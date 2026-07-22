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
        
        <?php if ($booking['status'] === 'awaiting_payment'): ?>
            <!-- Pending Payment Icon -->
            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold shadow-sm">
                ⏳
            </div>

            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Awaiting Payment
                </span>
                <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight mt-3">Booking Request Received!</h1>
                <p class="text-xs text-slate-500 font-medium mt-1.5 leading-relaxed">Please complete your payment directly to the host to confirm your slot. Once the host verifies the payment, your booking will be officially approved.</p>
            </div>
        <?php else: ?>
            <!-- Success Icon -->
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl font-bold shadow-sm">
                ✓
            </div>

            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                    Confirmed
                </span>
                <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight mt-3">Appointment Confirmed!</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">A calendar invitation and confirmation email have been generated.</p>
            </div>
        <?php endif; ?>

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





        <div class="pt-2">
            <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($hostUser['username']) ?>" class="text-xs font-bold text-slate-500 hover:text-black transition-colors">
                ← Book Another Appointment
            </a>
        </div>
    </div>
</body>
</html>
