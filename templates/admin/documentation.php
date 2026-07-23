<?php
$title = "System Documentation";
$adminTab = "docs";
require_once TEMPLATES_DIR . '/admin/layout/header.php';
?>

<div class="max-w-5xl mx-auto space-y-8 pb-12">
    <!-- Title Banner -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-black text-white text-[10px] font-extrabold rounded-full uppercase tracking-wider mb-2">
            Admin Reference Manual
        </div>
        <h1 class="text-3xl font-extrabold text-slate-950 tracking-tight">System Documentation</h1>
        <p class="text-slate-500 text-xs font-medium mt-1">Architecture details, DNS routing instructions, SMTP, API guides, and database structure.</p>
    </div>

    <!-- Main Content Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Sidebar Navigation links -->
        <div class="md:col-span-1 space-y-3">
            <div class="bg-white border border-slate-200/90 rounded-3xl p-5 shadow-sm sticky top-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Sections</h3>
                <div class="flex flex-col gap-1 text-xs font-semibold">
                    <a href="#architecture" class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors">1. Platform Architecture</a>
                    <a href="#dns" class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors">2. DNS & Custom Domains</a>
                    <a href="#smtp" class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors">3. SMTP & Mail Setup</a>
                    <a href="#gateways" class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors">4. Global Payment Gateways</a>
                    <a href="#schema" class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors">5. Database Schema Details</a>
                    <a href="#api" class="px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors">6. REST API Connections</a>
                </div>
            </div>
        </div>

        <!-- Detail content blocks -->
        <div class="md:col-span-2 space-y-8">

            <!-- Section 1: Architecture -->
            <section id="architecture" class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-sm space-y-4">
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight border-b border-slate-100 pb-3">1. Platform Architecture</h2>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    MiniCal is built on a clean, light, and high-performance **custom MVC framework** in PHP. It does not use heavy external frameworks, making it load instantly and extremely easy to scale.
                </p>
                <div class="grid grid-cols-2 gap-4 text-xs font-sans">
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <span class="block font-bold text-slate-800">App Core (`app/core/`)</span>
                        <span class="block text-[11px] text-slate-500 mt-1">Handles URL routing, request parsing, session storage, security, and template rendering.</span>
                    </div>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <span class="block font-bold text-slate-800">Services Layer (`app/services/`)</span>
                        <span class="block text-[11px] text-slate-500 mt-1">Contains Google OAuth services, calendar sync engines, and SMTP mailing configurations.</span>
                    </div>
                </div>
            </section>

            <!-- Section 2: DNS & Custom Domains -->
            <section id="dns" class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-sm space-y-4">
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight border-b border-slate-100 pb-3">2. DNS & Custom Domain Routing</h2>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    MiniCal supports multi-tenant **custom domains** for Growth and Pro plan hosts. In order to route traffic:
                </p>
                <div class="bg-slate-900 rounded-2xl p-6 text-slate-200 text-[11px] font-mono leading-relaxed space-y-2">
                    <p class="text-indigo-400"># DNS record configuration for host subdomains:</p>
                    <p>Type: <span class="text-amber-400">CNAME</span></p>
                    <p>Host/Name: <span class="text-amber-400">booking</span> (e.g. booking.clientdomain.com)</p>
                    <p>Value/Target: <span class="text-emerald-400">yourdomain.com</span> (points to your main platform install)</p>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    The platform's router detects incoming host headers, scans the `profiles` custom domains records, and loads the corresponding host booking form dynamically without altering browser addresses.
                </p>
            </section>

            <!-- Section 3: SMTP Mail Setup -->
            <section id="smtp" class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-sm space-y-4">
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight border-b border-slate-100 pb-3">3. SMTP & Mail Setup</h2>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Transactional notifications (confirmations, Google Meet links, reminder notifications) are triggered asynchronously using an SMTP mailing client.
                </p>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Configure your credentials globally under **System & SMTP** settings in this dashboard. Supported encryption protocols:
                </p>
                <ul class="list-disc list-inside text-xs text-slate-600 font-semibold space-y-1">
                    <li>TLS (Port 587) - Recommended</li>
                    <li>SSL (Port 465)</li>
                    <li>SMTP Authentication (Username / Password)</li>
                </ul>
            </section>

            <!-- Section 4: Global Payment Gateways -->
            <section id="gateways" class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-sm space-y-4">
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight border-b border-slate-100 pb-3">4. Global Payment Gateways</h2>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    The platform provides **Stripe** and **Razorpay** out of the box for handling paid booking fees:
                </p>
                <ul class="list-disc list-inside text-xs text-slate-600 font-semibold space-y-1">
                    <li>Configure credentials in `config/config.php` or the main config settings.</li>
                    <li>If Stripe/Razorpay keys are missing, the booking engine falls back to the **Manual Payment / UPI scan flow** configured by hosts.</li>
                </ul>
            </section>

            <!-- Section 5: Database Schema -->
            <section id="schema" class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-sm space-y-4">
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight border-b border-slate-100 pb-3">5. Core Database Schema</h2>
                <div class="overflow-x-auto text-xs border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-slate-700">
                        <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Table</th>
                                <th class="px-4 py-3">Core Purpose</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr>
                                <td class="px-4 py-3 font-bold font-mono">users</td>
                                <td class="px-4 py-3 text-slate-500">Core accounts data, login details, global roles, plan slug.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-bold font-mono">profiles</td>
                                <td class="px-4 py-3 text-slate-500">Branding details, timezone, logo paths, custom domains, UPI IDs, QR Codes.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-bold font-mono">bookings</td>
                                <td class="px-4 py-3 text-slate-500">Scheduled appointments, client details, payment statuses, promo code tracking.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-bold font-mono">events</td>
                                <td class="px-4 py-3 text-slate-500">Consultation types/services, prices, buffer paddings, status.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-bold font-mono">availability</td>
                                <td class="px-4 py-3 text-slate-500">Host weekly working hours configuration.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-bold font-mono">promo_codes</td>
                                <td class="px-4 py-3 text-slate-500">System-wide global coupons or host-specific promo codes.</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-bold font-mono">api_tokens</td>
                                <td class="px-4 py-3 text-slate-500">Cryptographically secure bearer tokens for mobile auth and plugin integrations.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Section 6: API Connections -->
            <section id="api" class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-sm space-y-4">
                <h2 class="text-lg font-extrabold text-slate-950 tracking-tight border-b border-slate-100 pb-3">6. Developer REST API Reference</h2>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Hosts and external developers can interact with MiniCal through transactional endpoints. Set the authentication header on all requests:
                </p>
                <div class="bg-slate-900 rounded-2xl p-6 text-slate-200 text-[11px] font-mono space-y-3">
                    <p class="text-indigo-400"># Header Authentication:</p>
                    <p class="text-emerald-400">Authorization: Bearer YOUR_DEVELOPER_KEY</p>
                    <hr class="border-slate-800 my-2">
                    <p><span class="text-amber-400">GET</span> /api/v1/profile (Fetch Profile Details)</p>
                    <p><span class="text-amber-400">GET</span> /api/v1/events (Fetch Services/Consultations)</p>
                    <p><span class="text-amber-400">GET</span> /api/v1/availability (Fetch Working Hours)</p>
                    <p><span class="text-amber-400">GET</span> /api/v1/bookings (Retrieve Active Bookings)</p>
                    <p><span class="text-amber-400">GET</span> /api/v1/form-fields (Fetch Custom Field Builder configurations)</p>
                </div>
            </section>

        </div>
    </div>
</div>

<?php require_once TEMPLATES_DIR . '/admin/layout/footer.php'; ?>
