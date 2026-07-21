<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> - MiniCal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex flex-col md:flex-row">
    
    <!-- Mobile Top Navigation Bar (Shown only on small screens) -->
    <header class="md:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200/80 sticky top-0 z-30">
        <a href="<?= APP_URL ?>" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-black text-white rounded-lg flex items-center justify-center font-extrabold text-sm shadow-sm">
                MC
            </div>
            <span class="font-extrabold text-base tracking-tight text-slate-900">MiniCal</span>
        </a>
        <button id="hamburger-btn" class="p-2 text-slate-700 hover:bg-slate-100 rounded-lg focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </header>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

    <!-- Sidebar Navigation Drawer (Slide-in on mobile, static on desktop) -->
    <aside id="app-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200/80 flex flex-col z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 min-h-screen flex-shrink-0">
        
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <a href="<?= APP_URL ?>" class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-black text-white rounded-xl flex items-center justify-center font-extrabold text-base shadow-sm">
                    MC
                </div>
                <div>
                    <span class="font-extrabold text-lg tracking-tight text-slate-900">MiniCal</span>
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">v1.0 MVP</span>
                </div>
            </a>
            
            <!-- Mobile Close Button (✕) -->
            <button id="close-sidebar-btn" class="md:hidden p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <a href="<?= APP_URL ?>/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($activeTab ?? '') === 'dashboard' ? 'bg-slate-100 text-slate-950 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Bookings
            </a>

            <a href="<?= APP_URL ?>/availability" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($activeTab ?? '') === 'availability' ? 'bg-slate-100 text-slate-950 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Availability
            </a>

            <a href="<?= APP_URL ?>/event" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($activeTab ?? '') === 'event' ? 'bg-slate-100 text-slate-950 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Event Types
            </a>

            <a href="<?= APP_URL ?>/form-builder" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($activeTab ?? '') === 'form-builder' ? 'bg-slate-100 text-slate-950 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Booking Form
            </a>

            <a href="<?= APP_URL ?>/integrations" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($activeTab ?? '') === 'integrations' ? 'bg-slate-100 text-slate-950 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2V4zm-6 8a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2v-1zm12 0a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2v-1zM4 18a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2v-1zm12 0a2 2 0 114 0v1a2 2 0 01-2 2 2 2 0 01-2-2v-1z"></path></svg>
                Integrations & Apps
            </a>

            <a href="<?= APP_URL ?>/profile" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($activeTab ?? '') === 'profile' ? 'bg-slate-100 text-slate-950 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' ?>">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profile & Settings
            </a>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= APP_URL ?>/admin" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all <?= ($activeTab ?? '') === 'admin' ? 'bg-slate-100 text-black font-bold border border-slate-200' : 'text-slate-600 hover:bg-slate-50' ?>">
                    <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Admin Panel
                </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-slate-100 space-y-3 bg-white">
            <a href="<?= APP_URL ?>/u/<?= htmlspecialchars($user['username'] ?? '') ?>" target="_blank"
               class="flex items-center justify-center gap-2 w-full py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold rounded-xl transition-all border border-slate-200/60">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                View Booking Page
            </a>

            <div class="flex items-center justify-between pt-1">
                <div class="truncate">
                    <p class="text-xs font-bold text-slate-900 truncate"><?= htmlspecialchars($user['name'] ?? 'User') ?></p>
                    <p class="text-[11px] text-slate-500 truncate">/u/<?= htmlspecialchars($user['username'] ?? '') ?></p>
                </div>
                <a href="<?= APP_URL ?>/logout" class="p-2 text-slate-400 hover:text-red-600 rounded-lg transition-colors" title="Sign out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-[#fafafa]">
