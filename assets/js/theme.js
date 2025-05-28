// assets/js/theme.js
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el evento del botón de tema
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
            document.cookie = `darkmode=${theme === 'dark'}; path=/; max-age=${60*60*24*365}`;
        });
    }

    // Aplicar tema al cargar según preferencias guardadas
    function applyInitialTheme() {
        // Verificar localStorage primero
        const localStorageTheme = localStorage.getItem('theme');
        
        // Si no hay en localStorage, verificar cookie
        const cookieMatch = document.cookie.match(/darkmode=(true|false)/);
        const cookieTheme = cookieMatch ? (cookieMatch[1] === 'true' ? 'dark' : 'light') : null;
        
        // Si no hay en cookie, usar preferencia del sistema
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        
        const savedTheme = localStorageTheme || cookieTheme || systemTheme;
        
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        }
    }

    applyInitialTheme();
});