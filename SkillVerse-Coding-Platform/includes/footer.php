    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5 Bundle JS CDN (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- App Custom Javascript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/app.js"></script>
    
    <!-- Display session flash alerts if set -->
    <?php if (isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showToast("<?php echo addslashes(sanitize($_SESSION['success'])); ?>", "success");
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showToast("<?php echo addslashes(sanitize($_SESSION['error'])); ?>", "error");
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</body>
</html>
