document.getElementById('search-icon').addEventListener('click', function() {
            document.getElementById('search-dropdown').classList.toggle('active');
        });
        // Toggle mobile menu (hamburger bar)
        document.getElementById('hamburger-menu').addEventListener('click', function() {
            document.getElementById('mobile-nav').classList.toggle('active');
        });
        // Toggle account dropdown in mobile view
        document.getElementById('mobile-account').addEventListener('click', function() {
            document.getElementById('mobile-account-dropdown').classList.toggle('active');
        });
