<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DayCal - The better way to schedule your meetings</title>
    <meta name="description" content="Open, modern appointment scheduling platform. Share your link, let clients pick available time slots, and collect payments effortlessly.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Hind Siliguri', sans-serif; }
        .bg-grid-pattern {
            background-image: radial-gradient(rgba(15, 23, 42, 0.06) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex flex-col selection:bg-black selection:text-white">

    <!-- Header Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-10">
                <a href="<?= APP_URL ?>" class="flex items-center gap-2.5 group">
                    <img src="<?= APP_URL ?>/public/logo.jpg" alt="DayCal Logo" class="w-9 h-9 rounded-xl shadow-sm object-cover group-hover:scale-105 transition-transform">
                    <span class="font-extrabold text-xl tracking-tight text-slate-900">DayCal</span>
                </a>

                <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                    <a href="#how-it-works" class="hover:text-black transition-colors" data-i18n="nav_how_it_works">How it works</a>
                    <a href="#features" class="hover:text-black transition-colors" data-i18n="nav_features">Features</a>
                    <a href="#pricing" class="hover:text-black transition-colors" data-i18n="nav_pricing">Pricing</a>
                    <a href="#faq" class="hover:text-black transition-colors" data-i18n="nav_faq">FAQ</a>
                </nav>
            </div>

            <!-- Right Controls: Language Switcher & Buttons -->
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-semibold">
                    <button type="button" id="btn-lang-en" onclick="setLanguage('en')" class="px-2.5 py-1.2 rounded-lg transition-all text-slate-600">
                        🇬🇧 EN
                    </button>
                    <button type="button" id="btn-lang-bn" onclick="setLanguage('bn')" class="px-2.5 py-1.2 rounded-lg transition-all text-slate-600">
                        🇧🇩 বাংলা
                    </button>
                </div>

                <a href="<?= APP_URL ?>/login" class="hidden sm:inline-block px-4 py-2.5 text-slate-700 hover:text-black text-sm font-semibold transition-colors" data-i18n="btn_login">
                    Sign in
                </a>
                <a href="<?= APP_URL ?>/register" class="px-5 py-2.5 bg-black hover:bg-slate-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-all transform hover:-translate-y-0.5" data-i18n="btn_register">
                    Get started
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-16 lg:py-24 bg-grid-pattern relative">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Text Left -->
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 border border-slate-200/80 text-slate-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span data-i18n="hero_badge">Introducing DayCal 1.0</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-950 tracking-tight leading-[1.15]" data-i18n="hero_heading">
                    The better way to schedule your meetings
                </h1>

                <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-xl" data-i18n="hero_subheading">
                    DayCal simplifies appointment scheduling. Share your custom booking link, sync calendars, collect payments, and automate form questions seamlessly.
                </p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <a href="<?= APP_URL ?>/register" class="px-7 py-3.5 bg-black hover:bg-slate-800 text-white font-semibold text-base rounded-xl shadow-md transition-all text-center" data-i18n="hero_cta_primary">
                        Get started for free
                    </a>
                    <a href="<?= APP_URL ?>/u/admin" target="_blank" class="px-7 py-3.5 bg-white hover:bg-slate-50 text-slate-800 font-semibold text-base rounded-xl border border-slate-300 shadow-sm transition-all text-center" data-i18n="hero_cta_demo">
                        View live booking page
                    </a>
                </div>

                <!-- Rating / Social Proof Bar -->
                <div class="pt-6 border-t border-slate-200/80 flex flex-wrap items-center gap-6 text-xs text-slate-500 font-medium">
                    <div class="flex items-center gap-1 text-amber-500">
                        ★★★★★ <span class="text-slate-700 font-semibold ml-1">4.9/5</span>
                    </div>
                    <span>•</span>
                    <span data-i18n="social_proof_1">Free forever for individuals</span>
                    <span>•</span>
                    <span data-i18n="social_proof_2">No credit card required</span>
                </div>
            </div>

            <!-- Hero Interactive Preview Card Right -->
            <div class="lg:col-span-6">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/60 relative">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-6 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-black text-white font-bold rounded-2xl flex items-center justify-center text-lg">AU</div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Admin User</h3>
                                <p class="text-xs text-slate-500">/u/admin</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full" data-i18n="badge_active">
                            ● Active
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm" data-i18n="preview_event_title">30 Minute Strategy Session</h4>
                            <p class="text-xs text-slate-500" data-i18n="preview_event_desc">Select a date and available time slot to schedule our meeting.</p>
                            <div class="pt-2 flex items-center gap-4 text-xs font-semibold text-slate-600">
                                <span>⏱ 30 Mins</span>
                                <span>🌐 Google Meet</span>
                                <span>💳 Free</span>
                            </div>
                        </div>

                        <!-- Mini Slot Picker -->
                        <div class="pt-2 space-y-2">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400" data-i18n="preview_available_slots">Available Slots (Mon, Oct 24)</p>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" class="py-2.5 px-3 bg-black text-white text-xs font-semibold rounded-xl text-center shadow-sm">09:00 AM</button>
                                <button type="button" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl text-center transition-colors">11:30 AM</button>
                                <button type="button" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl text-center transition-colors">02:00 PM</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 bg-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-6 space-y-16">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400" data-i18n="nav_how_it_works">How it works</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight" data-i18n="how_title">Schedule in 3 simple steps</h2>
                <p class="text-slate-600 text-base" data-i18n="how_sub">Everything you need to automate your appointment booking flow.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-8 hover:shadow-lg transition-all space-y-4">
                    <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center font-bold text-lg shadow-sm">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-slate-900" data-i18n="how_step1_title">1. Connect your calendar</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium" data-i18n="how_step1_desc">
                        Sync with Google Calendar to automatically read busy times and prevent double bookings.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-8 hover:shadow-lg transition-all space-y-4">
                    <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center font-bold text-lg shadow-sm">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-slate-900" data-i18n="how_step2_title">2. Set your availability</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium" data-i18n="how_step2_desc">
                        Define your weekly working hours, breaks, and buffer times in your dashboard.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-8 hover:shadow-lg transition-all space-y-4">
                    <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center font-bold text-lg shadow-sm">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-slate-900" data-i18n="how_step3_title">3. Share your link</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium" data-i18n="how_step3_desc">
                        Share your custom booking link `/u/username` and let clients schedule instantly.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-[#fafafa] border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-6 space-y-16">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400" data-i18n="nav_features">Features</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight" data-i18n="feat_title">Everything you need to schedule bookings</h2>
                <p class="text-slate-600 text-base" data-i18n="feat_sub">Packed with premium features built for individuals and developers.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 hover:shadow-md transition-all space-y-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm">
                        📅
                    </div>
                    <h3 class="text-base font-bold text-slate-900" data-i18n="feat1_title">Smart Weekly Schedules</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" data-i18n="feat1_desc">
                        Configure flexible working hours and breaks for each day of the week.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 hover:shadow-md transition-all space-y-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm">
                        💳
                    </div>
                    <h3 class="text-base font-bold text-slate-900" data-i18n="feat2_title">Free & Paid Bookings</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" data-i18n="feat2_desc">
                        Collect payments upfront using secure Stripe or Razorpay integrations.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 hover:shadow-md transition-all space-y-3">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm">
                        📝
                    </div>
                    <h3 class="text-base font-bold text-slate-900" data-i18n="feat3_title">Custom Booking Forms</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" data-i18n="feat3_desc">
                        Build custom questions for your attendees with our form field builder.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 hover:shadow-md transition-all space-y-3">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm">
                        🔄
                    </div>
                    <h3 class="text-base font-bold text-slate-900" data-i18n="feat4_title">Google Calendar Sync</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" data-i18n="feat4_desc">
                        Two-way calendar synchronization. Automatic events and double booking prevention.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 hover:shadow-md transition-all space-y-3">
                    <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm">
                        👑
                    </div>
                    <h3 class="text-base font-bold text-slate-900" data-i18n="feat5_title">Super Admin Panel</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" data-i18n="feat5_desc">
                        Manage users, custom domains, plans, and global SMTP mail settings.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 hover:shadow-md transition-all space-y-3">
                    <div class="w-10 h-10 bg-slate-100 text-slate-800 rounded-xl flex items-center justify-center font-extrabold text-lg shadow-sm">
                        📡
                    </div>
                    <h3 class="text-base font-bold text-slate-900" data-i18n="feat6_title">REST API (v1)</h3>
                    <p class="text-xs text-slate-500 leading-relaxed" data-i18n="feat6_desc">
                        Secure bearer token endpoints for mobile apps or external integrations.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Cal.com Pricing Plans Section -->
    <section id="pricing" class="py-24 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-6 space-y-16">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400" data-i18n="pricing_tag">Flexible Tiers</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight" data-i18n="pricing_heading">Pick the plan that fits your schedule</h2>
                <p class="text-slate-600 text-base" data-i18n="pricing_subheading">Simple transparent pricing for individuals, teams, and organizations.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
                <?php foreach ($plans as $p): ?>
                    <?php $features = !empty($p['features']) ? json_decode($p['features'], true) : []; ?>
                    <div class="rounded-3xl p-8 border transition-all flex flex-col justify-between relative <?= !empty($p['is_featured']) ? 'bg-slate-950 text-white border-slate-900 shadow-2xl scale-[1.02] z-10' : 'bg-white text-slate-900 border-slate-200/90 shadow-sm hover:shadow-md' ?>">
                        
                        <?php if (!empty($p['badge'])): ?>
                            <span class="absolute -top-3 right-6 text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm <?= !empty($p['is_featured']) ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200' ?>">
                                <?= htmlspecialchars($p['badge']) ?>
                            </span>
                        <?php endif; ?>

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xl font-extrabold tracking-tight mb-2"><?= htmlspecialchars($p['name']) ?></h3>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-black tracking-tight"><?= htmlspecialchars($p['price']) ?></span>
                                    <span class="text-xs <?= !empty($p['is_featured']) ? 'text-slate-400' : 'text-slate-500' ?>">/ <?= htmlspecialchars($p['billing_cycle']) ?></span>
                                </div>
                                <p class="text-xs mt-3 leading-relaxed <?= !empty($p['is_featured']) ? 'text-slate-300' : 'text-slate-600' ?>"><?= htmlspecialchars($p['description']) ?></p>
                            </div>

                            <div class="border-t <?= !empty($p['is_featured']) ? 'border-slate-800' : 'border-slate-100' ?> pt-6 space-y-3">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider <?= !empty($p['is_featured']) ? 'text-slate-400' : 'text-slate-500' ?>">Features:</span>
                                <ul class="space-y-2.5 text-xs">
                                    <?php foreach ($features as $f): ?>
                                        <li class="flex items-start gap-2.5">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5 <?= !empty($p['is_featured']) ? 'text-white' : 'text-slate-900' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <span class="<?= !empty($p['is_featured']) ? 'text-slate-200' : 'text-slate-700' ?>"><?= htmlspecialchars($f) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="pt-8">
                            <a href="<?= APP_URL ?>/register" class="block w-full py-3.5 px-4 text-center text-xs font-extrabold rounded-xl transition-all shadow-sm <?= !empty($p['is_featured']) ? 'bg-white hover:bg-slate-100 text-slate-950' : 'bg-black hover:bg-slate-800 text-white' ?>">
                                <?= htmlspecialchars($p['button_text']) ?> →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-[#fafafa]">
        <div class="max-w-4xl mx-auto px-6 space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight" data-i18n="faq_title">Frequently asked questions</h2>
            </div>

            <div class="space-y-4">
                <details class="bg-white border border-slate-200 rounded-2xl p-6 group cursor-pointer">
                    <summary class="font-bold text-slate-900 text-base flex justify-between items-center" data-i18n="faq_q1">
                        Is DayCal completely free to use?
                    </summary>
                    <p class="text-slate-600 text-sm mt-4 leading-relaxed" data-i18n="faq_a1">
                        Yes! DayCal MVP includes unlimited bookings, custom booking links, availability management, and custom form builders completely free.
                    </p>
                </details>

                <details class="bg-white border border-slate-200 rounded-2xl p-6 group cursor-pointer">
                    <summary class="font-bold text-slate-900 text-base flex justify-between items-center" data-i18n="faq_q2">
                        How does Google Calendar synchronization work?
                    </summary>
                    <p class="text-slate-600 text-sm mt-4 leading-relaxed" data-i18n="faq_a2">
                        When connected, DayCal reads your busy times from Google Calendar to prevent double booking, and creates events automatically upon new bookings.
                    </p>
                </details>

                <details class="bg-white border border-slate-200 rounded-2xl p-6 group cursor-pointer">
                    <summary class="font-bold text-slate-900 text-base flex justify-between items-center" data-i18n="faq_q3">
                        Can I accept payments for appointments?
                    </summary>
                    <p class="text-slate-600 text-sm mt-4 leading-relaxed" data-i18n="faq_a3">
                        Yes. You can mark any event as paid, set your price amount & currency, and integrate Stripe or Razorpay.
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- Bottom Callout CTA Banner -->
    <section class="py-20 bg-white border-t border-slate-200/80 text-center">
        <div class="max-w-4xl mx-auto px-6 space-y-6">
            <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-950 tracking-tight" data-i18n="cta_banner_title">Smarter, simpler scheduling</h2>
            <p class="text-slate-600 text-base max-w-xl mx-auto" data-i18n="cta_banner_sub">Create your custom booking link in less than 2 minutes.</p>
            <div class="pt-2">
                <a href="<?= APP_URL ?>/register" class="inline-block px-8 py-4 bg-black hover:bg-slate-800 text-white font-bold text-base rounded-2xl shadow-lg transition-all transform hover:-translate-y-0.5" data-i18n="hero_cta_primary">
                    Get started for free
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200/80 bg-white py-12 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <img src="<?= APP_URL ?>/public/logo.jpg" alt="DayCal Logo" class="w-7 h-7 rounded-lg object-cover">
                <span class="font-bold text-slate-900 text-sm">DayCal</span>
            </div>
            <p>© <?= date('Y') ?> DayCal. All rights reserved. Inspired by Cal.com.</p>
            <div class="flex gap-4 font-semibold">
                <a href="<?= APP_URL ?>/privacy" class="hover:text-black transition-colors">Privacy Policy</a>
                <a href="<?= APP_URL ?>/terms" class="hover:text-black transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

    <!-- Bilingual JavaScript Translations Engine (English & Bengali) -->
    <script>
        const i18n = {
            en: {
                nav_how_it_works: "How it works",
                nav_features: "Features",
                nav_pricing: "Pricing",
                nav_faq: "FAQ",
                btn_login: "Sign in",
                btn_register: "Get started",
                hero_badge: "Introducing DayCal 1.0",
                hero_heading: "The better way to schedule your meetings",
                hero_subheading: "DayCal simplifies appointment scheduling. Share your custom booking link, sync calendars, collect payments, and automate form questions seamlessly.",
                hero_cta_primary: "Get started for free",
                hero_cta_demo: "View live booking page",
                social_proof_1: "Free forever for individuals",
                social_proof_2: "No credit card required",
                badge_active: "● Active",
                preview_event_title: "30 Minute Strategy Session",
                preview_event_desc: "Select a date and available time slot to schedule our meeting.",
                preview_available_slots: "Available Slots (Mon, Oct 24)",
                pricing_tag: "Flexible Tiers",
                pricing_heading: "Pick the plan that fits your schedule",
                pricing_subheading: "Simple transparent pricing for individuals, teams, and organizations.",
                faq_title: "Frequently asked questions",
                faq_q1: "Is DayCal completely free to use?",
                faq_a1: "Yes! DayCal MVP includes unlimited bookings, custom booking links, availability management, and custom form builders completely free.",
                faq_q2: "How does Google Calendar synchronization work?",
                faq_a2: "When connected, DayCal reads your busy times from Google Calendar to prevent double booking, and creates events automatically upon new bookings.",
                faq_q3: "Can I accept payments for appointments?",
                faq_a3: "Yes. You can mark any event as paid, set your price amount & currency, and integrate Stripe or Razorpay.",
                cta_banner_title: "Smarter, simpler scheduling",
                cta_banner_sub: "Create your custom booking link in less than 2 minutes.",
                how_title: "Schedule in 3 simple steps",
                how_sub: "Everything you need to automate your appointment booking flow.",
                how_step1_title: "1. Connect your calendar",
                how_step1_desc: "Sync with Google Calendar to automatically read busy times and prevent double bookings.",
                how_step2_title: "2. Set your availability",
                how_step2_desc: "Define your weekly working hours, breaks, and buffer times in your dashboard.",
                how_step3_title: "3. Share your link",
                how_step3_desc: "Share your custom booking link `/u/username` and let clients schedule instantly.",
                feat_title: "Everything you need to schedule bookings",
                feat_sub: "Packed with premium features built for individuals and developers.",
                feat1_title: "Smart Weekly Schedules",
                feat1_desc: "Configure flexible working hours and breaks for each day of the week.",
                feat2_title: "Free & Paid Bookings",
                feat2_desc: "Collect payments upfront using secure Stripe or Razorpay integrations.",
                feat3_title: "Custom Booking Forms",
                feat3_desc: "Build custom questions for your attendees with our form field builder.",
                feat4_title: "Google Calendar Sync",
                feat4_desc: "Two-way calendar synchronization. Automatic events and double booking prevention.",
                feat5_title: "Super Admin Panel",
                feat5_desc: "Manage users, custom domains, plans, and global SMTP mail settings.",
                feat6_title: "REST API (v1)",
                feat6_desc: "Secure bearer token endpoints for mobile apps or external integrations."
            },
            bn: {
                nav_how_it_works: "কিভাবে কাজ করে",
                nav_features: "বৈশিষ্ট্যসমূহ",
                nav_pricing: "মূল্য নির্ধারণ",
                nav_faq: "প্রশ্নোত্তর",
                btn_login: "সাইন ইন",
                btn_register: "শুরু করুন",
                hero_badge: "নতুন ডে-ক্যাল ১.০ সংস্করণ",
                hero_heading: "আপনার মিটিং সিডিউল করার সবচেয়ে সেরা মাধ্যম",
                hero_subheading: "ডে-ক্যাল অ্যাপয়েন্টমেন্ট সিডিউলিংকে সহজ করে তোলে। আপনার কাস্টম বুকিং লিংক শেয়ার করুন, ক্যালেন্ডার সিঙ্ক করুন, পেমেন্ট গ্রহণ করুন এবং ফর্মের প্রশ্নগুলি স্বয়ংক্রিয় করুন।",
                hero_cta_primary: "বিনামূল্যে শুরু করুন",
                hero_cta_demo: "লাইভ বুকিং পেজ দেখুন",
                social_proof_1: "ব্যক্তিগত ব্যবহারের জন্য চিরকাল ফ্রি",
                social_proof_2: "কোন ক্রেডিট কার্ড প্রয়োজন নেই",
                badge_active: "● সক্রিয়",
                preview_event_title: "৩০ মিনিটের স্ট্র্যাটেজি সেশন",
                preview_event_desc: "আমাদের মিটিং বুক করতে একটি তারিখ এবং উপলব্ধ সময় নির্বাচন করুন।",
                preview_available_slots: "উপলব্ধ সময়সূচী (সোমবার, ২৪ অক্টোবর)",
                pricing_tag: "সহজ প্ল্যানসমূহ",
                pricing_heading: "আপনার চাহিদানুযায়ী সঠিক প্ল্যান বেছে নিন",
                pricing_subheading: "ব্যক্তিগত এবং ব্যবসায়িক ব্যবহারের জন্য সহজ ও স্বচ্ছ মূল্য নির্ধারণ।",
                faq_title: "সাধারণ জিজ্ঞাসিত প্রশ্নাবলী",
                faq_q1: "ডে-ক্যাল কি সম্পূর্ণ বিনামূল্যে ব্যবহার করা যায়?",
                faq_a1: "হ্যাঁ! ডে-ক্যালে আনলিমিটেড বুকিং, কাস্টম বুকিং লিংক, এবং ফর্ম বিল্ডার সম্পূর্ণ ফ্রি।",
                faq_q2: "গুগল ক্যালেন্ডার সিঙ্ক্রোনাইজেশন কিভাবে কাজ করে?",
                faq_a2: "যুক্ত করার পর, ডে-ক্যাল আপনার গুগল ক্যালেন্ডার থেকে ব্যস্ত সময়গুলো পড়ে ডাবল বুকিং প্রতিরোধ করে।",
                faq_q3: "আমি কি অ্যাপয়েন্টমেন্টের জন্য পেমেন্ট নিতে পারি?",
                faq_a3: "হ্যাঁ। যেকোনো ইভেন্ট পেইড চিহ্নিত করে প্রাইস সেট করতে পারেন এবং স্ট্রাইপ বা রেজোরপে যুক্ত করতে পারেন।",
                cta_banner_title: "স্মার্ট ও সহজ সিডিউলিং",
                cta_banner_sub: "২ মিনিটেরও কম সময়ে আপনার কাস্টম বুকিং লিংক তৈরি করুন।",
                how_title: "৩টি সহজ ধাপে বুকিং সিডিউল করুন",
                how_sub: "আপনার অ্যাপয়েন্টমেন্ট বুকিং প্রবাহকে স্বয়ংক্রিয় করার জন্য প্রয়োজনীয় সবকিছু।",
                how_step1_title: "১. ক্যালেন্ডার যুক্ত করুন",
                how_step1_desc: "ব্যস্ত সময়গুলি স্বয়ংক্রিয়ভাবে বুঝতে এবং ডাবল বুকিং রোধ করতে গুগল ক্যালেন্ডার সিঙ্ক করুন।",
                how_step2_title: "২. সময়সূচী নির্ধারণ করুন",
                how_step2_desc: "আপনার ড্যাশবোর্ডে সাপ্তাহিক কাজের সময়, বিরতি এবং বাফার সময় নির্ধারণ করুন।",
                how_step3_title: "৩. আপনার বুকিং লিংক শেয়ার করুন",
                how_step3_desc: "আপনার কাস্টম বুকিং লিঙ্ক `/u/username` শেয়ার করুন এবং ক্লায়েন্টদের বুক করতে দিন।",
                feat_title: "বুকিং সিডিউল করার জন্য প্রয়োজনীয় সবকিছু",
                feat_sub: "ব্যক্তিগত এবং ডেভেলপারদের জন্য নির্মিত চমৎকার সব ফিচারের সুবিধা।",
                feat1_title: "স্মার্ট সাপ্তাহিক সময়সূচী",
                feat1_desc: "সপ্তাহের প্রতিটি দিনের জন্য নমনীয় কাজের সময় এবং বিরতি সেট করুন।",
                feat2_title: "ফ্রি ও পেইড বুকিং",
                feat2_desc: "স্ট্রাইপ বা রেজোরপে পেমেন্ট গেটওয়ে ব্যবহার করে অগ্রিম পেমেন্ট গ্রহণ করুন।",
                feat3_title: "কাস্টম বুকিং ফর্ম",
                feat3_desc: "আমাদের কাস্টম ফর্ম ফিল্ডার ব্যবহার করে বুকিংদাতাদের জন্য প্রশ্ন তৈরি করুন।",
                feat4_title: "গুগল ক্যালেন্ডার সিঙ্ক",
                feat4_desc: "দ্বিমুখী ক্যালেন্ডার সিঙ্ক। স্বয়ংক্রিয় ইভেন্ট তৈরি ও ডাবল বুকিং রোধ।",
                feat5_title: "সুপার এডমিন প্যানেল",
                feat5_desc: "ব্যবহারকারী, কাস্টম ডোমেন, সাবস্ক্রিপশন প্ল্যান এবং গ্লোবাল SMTP পরিচালনা করুন।",
                feat6_title: "ডেভেলপার REST API",
                feat6_desc: "মোবাইল অ্যাপ বা এক্সটার্নাল ইন্টিগ্রেশনের জন্য সিকিউর টোকেন সমৃদ্ধ এপিআই।"
            }
        };

        function setLanguage(lang) {
            if (!i18n[lang]) lang = 'en';
            localStorage.setItem('daycal_lang', lang);

            const btnEn = document.getElementById('btn-lang-en');
            const btnBn = document.getElementById('btn-lang-bn');

            if (lang === 'en') {
                btnEn.className = 'px-2.5 py-1.2 rounded-lg bg-black text-white font-bold shadow-sm transition-all';
                btnBn.className = 'px-2.5 py-1.2 rounded-lg text-slate-600 hover:text-black transition-all';
            } else {
                btnBn.className = 'px-2.5 py-1.2 rounded-lg bg-black text-white font-bold shadow-sm transition-all';
                btnEn.className = 'px-2.5 py-1.2 rounded-lg text-slate-600 hover:text-black transition-all';
            }

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (i18n[lang][key]) {
                    el.innerText = i18n[lang][key];
                }
            });
        }

        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        const savedLang = localStorage.getItem('daycal_lang') || 'en';
        setLanguage(urlLang || savedLang);
    </script>
</body>
</html>
