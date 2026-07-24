<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - DayCal</title>
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





        <?php if ($booking['status'] === 'awaiting_payment'): ?>
            <!-- Manual Payment Details Card -->
            <div class="p-6 border border-slate-200 rounded-2xl bg-indigo-50/20 text-left space-y-4">
                <h4 class="text-xs font-extrabold uppercase text-slate-800 tracking-wider">How to Pay & Confirm:</h4>
                
                <?php if (!empty($profile['upi_id'])): ?>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/60 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">UPI ID</span>
                            <span class="text-xs font-mono font-bold text-slate-900 select-all"><?= htmlspecialchars($profile['upi_id']) ?></span>
                        </div>
                        <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($profile['upi_id']) ?>'); alert('UPI ID copied!')"
                                class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-2 py-1 rounded transition-all">
                            Copy
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($profile['qr_code'])): ?>
                    <div class="text-center bg-white p-4 rounded-xl border border-slate-200/60 space-y-2">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Scan to Pay</span>
                        <div class="w-40 h-40 mx-auto border border-slate-100 rounded-lg overflow-hidden">
                            <img src="<?= APP_URL ?>/<?= htmlspecialchars($profile['qr_code']) ?>" class="w-full h-full object-contain">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="text-xs text-slate-600 space-y-2 font-medium leading-relaxed">
                    <p>After completing the payment, please send a screenshot of your transaction receipt with Booking ID <strong class="text-slate-900 font-bold">#<?= $booking['id'] ?></strong> to the host:</p>
                    <div class="flex flex-col gap-2 pt-1.5 font-sans">
                        <a href="mailto:<?= htmlspecialchars($hostUser['email']) ?>?subject=Payment Receipt for Booking #<?= $booking['id'] ?>&body=Hello, I have completed the payment. Booking ID: #<?= $booking['id'] ?>"
                           class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-center rounded-xl text-xs transition-all shadow-sm">
                            ✉ Send Receipt via Email
                        </a>

                        <?php if (!empty($profile['phone'])): ?>
                            <?php $waPhone = preg_replace('/[^0-9]/', '', $profile['phone']); ?>
                            <a href="https://wa.me/<?= $waPhone ?>?text=<?= urlencode("Hello, I have completed the payment for my booking. Booking Ref ID: #" . $booking['id'] . ". Date: " . $booking['booking_date']) ?>"
                               target="_blank"
                               class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-center rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-1">
                                💬 Send Receipt via WhatsApp
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="pt-2">
            <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($hostUser['username']) ?>" class="text-xs font-bold text-slate-500 hover:text-black transition-colors">
                ← Book Another Appointment
            </a>
        </div>
    </div>
</body>
</html>
