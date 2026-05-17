    <footer>
        <div class="container" style="background: none; border: none; box-shadow: none;">
            <p>&copy; <?php echo date('Y'); ?> Lakastech. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const icon = themeToggle.querySelector('i');

        // Atualizar ícone baseado no tema atual
        function updateIcon() {
            if (document.body.classList.contains('dark-theme')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }

        updateIcon();

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const isDark = document.body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateIcon();
        });
    </script>
<?php ob_end_flush(); ?>
</body>
</html>
