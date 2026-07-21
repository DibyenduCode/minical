    </main>

    <!-- Mobile Sidebar JavaScript Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const closeBtn = document.getElementById('close-sidebar-btn');
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (hamburgerBtn && closeBtn && sidebar && overlay) {
                // Open Sidebar Drawer
                hamburgerBtn.addEventListener('click', function () {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    setTimeout(() => {
                        overlay.classList.add('opacity-100');
                    }, 50);
                });

                // Close Sidebar Drawer
                function closeSidebar() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100');
                    setTimeout(() => {
                        overlay.classList.add('hidden');
                    }, 300);
                }

                closeBtn.addEventListener('click', closeSidebar);
                overlay.addEventListener('click', closeSidebar);
            }
        });
    </script>
</body>
</html>
