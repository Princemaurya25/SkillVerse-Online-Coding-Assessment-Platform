document.addEventListener('DOMContentLoaded', () => {
    // Theme Management (Dark / Light Mode)
    const themeToggleBtn = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;
    
    // Check saved theme or system preference
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        htmlElement.setAttribute('data-theme', 'dark');
        updateToggleIcon(true);
    } else {
        htmlElement.setAttribute('data-theme', 'light');
        updateToggleIcon(false);
    }
    
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcon(newTheme === 'dark');
        });
    }
    
    function updateToggleIcon(isDark) {
        if (!themeToggleBtn) return;
        const icon = themeToggleBtn.querySelector('i');
        if (icon) {
            if (isDark) {
                icon.className = 'bi bi-sun-fill text-warning';
            } else {
                icon.className = 'bi bi-moon-stars-fill';
            }
        }
    }

    // Sidebar Mobile Toggle Handling
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('show') && !sidebar.contains(e.target) && e.target !== sidebarToggleBtn) {
                sidebar.classList.remove('show');
            }
        });
    }

    // Active Navigation Link Highlighter
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href)) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // Custom Toast Alert System
    window.showToast = function(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        const id = 'toast-' + Date.now();
        const iconClass = type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger';
        
        const toastHTML = `
            <div id="${id}" class="toast align-items-center border-0 glass-panel shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="bi ${iconClass} fs-5"></i>
                        <span class="text-primary font-sans">${message}</span>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(id);
        const bsToast = new bootstrap.Toast(toastElement, { delay: 4000 });
        bsToast.show();
        
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    };
});
