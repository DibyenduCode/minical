<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Super Admin') ?> - MiniCal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex">
    <!-- Dedicated Super Admin Sidebar Navigation -->
    <aside class="w-64 bg-white border-r border-slate-200/90 flex flex-col flex-shrink-0 min-h-screen">
        <!-- Logo & Admin Badge -->
        <div class="p-6 border-b border-slate-100 flex items-center gap-3">
            <a href="<?= APP_URL ?>/admin" class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-black text-white rounded-xl flex items-center justify-center font-extrabold text-base shadow-sm">
                    SA
                </div>
                <div>
                    <span class="font-extrabold text-lg tracking-tight text-slate-950">MiniCal</span>
                    <span class="block text-[10px] bg-black text-white font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider w-fit mt-0.5">Super Admin</span>
                </div>
            </a>
        </div>

        <!-- Super Admin Only Navigation Options -->
        <nav class="flex-1 p-4 space-y-1.5">
            <a href="<?= APP_URL ?>/admin" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'overview' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Platform Overview
            </a>

            <a href="<?= APP_URL ?>/admin/domains" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'domains' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8"></path></svg>
                Custom Domains
            </a>

            <a href="<?= APP_URL ?>/admin/plans" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'plans' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Pricing Plans
            </a>

            <a href="<?= APP_URL ?>/admin/users" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'users' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Manage Users
            </a>

            <a href="<?= APP_URL ?>/admin/promo-codes" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'promos' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zM9 16h6"></path></svg>
                Promo Codes
            </a>

            <a href="<?= APP_URL ?>/admin/bookings" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'bookings' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Global Appointments
            </a>

            <a href="<?= APP_URL ?>/admin/documentation" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'docs' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                System Documentation
            </a>

            <a href="<?= APP_URL ?>/admin/settings" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($adminTab ?? '') === 'settings' ? 'bg-black text-white font-bold shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                System & SMTP
            </a>
        </nav>

        <!-- Bottom Actions -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between">
            <div class="truncate">
                <p class="text-xs font-bold text-slate-900 truncate"><?= htmlspecialchars($admin['name'] ?? 'Super Admin') ?></p>
                <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($admin['email'] ?? '') ?></p>
            </div>
            <a href="<?= APP_URL ?>/logout" class="p-2 text-slate-400 hover:text-red-600 rounded-lg transition-colors" title="Sign out">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-8 bg-[#fafafa]">
