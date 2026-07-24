<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - DayCal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-800 min-h-screen flex flex-col">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200/80 py-4 px-6 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="<?= APP_URL ?>" class="flex items-center gap-2.5">
                <img src="<?= APP_URL ?>/public/logo.jpg" alt="DayCal Logo" class="w-8 h-8 rounded-xl object-cover">
                <span class="font-extrabold text-lg text-slate-900">DayCal</span>
            </a>
            <a href="<?= APP_URL ?>" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
                ← Back to Home
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-12 px-6">
        <div class="max-w-3xl mx-auto bg-white border border-slate-200/90 rounded-3xl shadow-sm p-8 md:p-12 space-y-6">
            <div class="border-b border-slate-100 pb-6">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Privacy Policy</h1>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-2">Last Updated: July 24, 2026</p>
            </div>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">1. Information We Collect</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    We collect personal information necessary to deliver appointment booking services. This includes:
                </p>
                <ul class="list-disc pl-5 text-sm text-slate-600 space-y-2">
                    <li><strong>Booking Details:</strong> Customer name, email address, custom form builder responses, and scheduled date/times.</li>
                    <li><strong>Google Calendar Details:</strong> If a host connects Google Calendar, we access calendar events (busy times only) to prevent schedule conflicts, and write confirmed bookings. We do not store your private calendar details permanently.</li>
                    <li><strong>Account Details:</strong> Consultant login passwords (securely hashed), timezone settings, and branding files.</li>
                </ul>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">2. How We Use Your Information</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Collected data is strictly utilized to operate DayCal:
                </p>
                <ul class="list-disc pl-5 text-sm text-slate-600 space-y-2">
                    <li>Scheduling appointments and sending automated email invitations.</li>
                    <li>Resolving schedule availability hours and break overlays.</li>
                    <li>Processing client billing details and manual UPI confirmation screenshots.</li>
                </ul>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">3. Data Sharing & Third Parties</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    DayCal does not sell, lease, or rent customer database info to third-party advertising companies. Your booking details are shared exclusively with the host partner you booked the appointment with.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">4. Information Security</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    We implement modern industry-standard encryption protocols (like HTTPS and password hash encryption) to ensure your data stays secure. However, no transmission method over the internet is 100% secure.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">5. Your Rights</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    You have the right to request deletion of your scheduled booking history or account at any time. For questions regarding privacy, please contact your account manager or the host directly.
                </p>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-100 bg-white py-6 text-center text-slate-400 text-[11px] font-medium">
        <p>© <?= date('Y') ?> DayCal. All rights reserved.</p>
    </footer>

</body>
</html>
