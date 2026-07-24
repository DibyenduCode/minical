<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - DayCal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/90 shadow-xl shadow-slate-200/50 p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="flex justify-center mb-2">
                <img src="<?= APP_URL ?>/public/logo.jpg" alt="DayCal Logo" class="w-12 h-12 rounded-2xl shadow-sm object-cover">
            </div>
            <h1 class="text-2xl font-extrabold text-slate-950 tracking-tight">Get started with DayCal</h1>
            <p class="text-slate-500 text-xs font-medium">Create your free account and personal booking link</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>/register" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full name</label>
                <input type="text" id="name" name="name" required placeholder="John Doe"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
            </div>

            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username (Your booking link)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400 text-sm font-semibold">/u/</span>
                    <input type="text" id="username" name="username" required placeholder="johndoe"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
                </div>
                <span id="username_status" class="block text-[10px] font-bold mt-1.5 hidden"></span>
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email address</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all">
            </div>

            <button type="submit"
                class="w-full py-3.5 px-4 bg-black hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md transition-all transform active:scale-[0.98] mt-2">
                Create free account
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-xs font-medium">Already have an account? 
                <a href="<?= APP_URL ?>/login" class="text-black font-bold hover:underline">Sign in</a>
            </p>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const usernameInput = document.getElementById('username');
        const usernameStatus = document.getElementById('username_status');
        let timeout = null;

        if (usernameInput) {
            usernameInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const username = usernameInput.value.trim().toLowerCase();
                usernameStatus.classList.add('hidden');

                if (!username) {
                    usernameStatus.className = 'block text-[10px] font-bold mt-1.5 text-red-600';
                    usernameStatus.innerText = 'Username cannot be empty.';
                    usernameStatus.classList.remove('hidden');
                    return;
                }

                if (!/^[a-z0-9_-]+$/i.test(username)) {
                    usernameStatus.className = 'block text-[10px] font-bold mt-1.5 text-red-600';
                    usernameStatus.innerText = 'Letters, numbers, underscores and hyphens only.';
                    usernameStatus.classList.remove('hidden');
                    return;
                }

                timeout = setTimeout(() => {
                    fetch(`<?= APP_URL ?>/api/check-username?username=${username}`)
                        .then(res => res.json())
                        .then(data => {
                            usernameStatus.classList.remove('hidden');
                            if (data.available) {
                                usernameStatus.className = 'block text-[10px] font-bold mt-1.5 text-emerald-600';
                                usernameStatus.innerText = '✓ Username is available';
                            } else {
                                usernameStatus.className = 'block text-[10px] font-bold mt-1.5 text-red-600';
                                usernameStatus.innerText = '✗ Username is already taken';
                            }
                        })
                        .catch(() => {
                            usernameStatus.className = 'block text-[10px] font-bold mt-1.5 text-red-600';
                            usernameStatus.innerText = 'Failed to verify username availability.';
                            usernameStatus.classList.remove('hidden');
                        });
                }, 300);
            });
        }
    });
    </script>
</body>
</html>
