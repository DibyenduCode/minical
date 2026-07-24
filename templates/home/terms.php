<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - DayCal</title>
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
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Terms of Service</h1>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-2">Last Updated: July 24, 2026</p>
            </div>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">1. Acceptance of Terms</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    By accessing or using the DayCal scheduling platform (located at daycal.in), you agree to comply with and be bound by these Terms of Service. If you do not agree to these terms, please do not utilize our platform.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">2. Description of Service</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    DayCal offers appointment scheduling, booking slots availability checks, calendar sync integrations, custom booking form question inputs, and manual payment verification gateways.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">3. User Account Responsibilities</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    If you register an account as a scheduling consultant/host:
                </p>
                <ul class="list-disc pl-5 text-sm text-slate-600 space-y-2">
                    <li>You are responsible for keeping your login credentials confidential.</li>
                    <li>You agree to provide true, accurate, and current profile settings.</li>
                    <li>You assume all responsibility for events created under your handle.</li>
                </ul>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">4. Paid Services & Payments</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Certain consultation slots may require fee processing. DayCal facilitates the entry of UPI credentials and payment receipt validation. All monetary agreements and refunds are handled directly between the consultant and the booking client.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900">5. Limitation of Liability</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    DayCal is provided on an "as is" and "as available" basis. In no event shall DayCal, its developers, or host partners be liable for any direct, indirect, or incidental damages resulting from scheduling conflicts, missed consultations, or data transmissions.
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
