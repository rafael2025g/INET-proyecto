// JavaScript para activar las animaciones de scroll y carga
document.addEventListener('DOMContentLoaded', function() {
    // Configuración del Intersection Observer
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    // Observer para animaciones de scroll
    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, observerOptions);

    // Seleccionar todos los elementos con clases de animación
    const animatedElements = document.querySelectorAll(`
        .scroll-animate,
        .fade-in-up,
        .fade-in-down,
        .fade-in-left,
        .fade-in-right,
        .scale-in,
        .stagger-item
    `);

    // Observar cada elemento
    animatedElements.forEach(el => {
        scrollObserver.observe(el);
    });

    // Loading Spinner
    window.addEventListener('load', function() {
        const spinner = document.querySelector('.loading-spinner');
        if (spinner) {
            spinner.classList.add('fade-out');
            setTimeout(() => {
                spinner.style.display = 'none';
            }, 500);
        }
    });

    // Progress Bar de carga
    let progress = 0;
    const progressBar = document.querySelector('.progress-bar');
    
    if (progressBar) {
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                setTimeout(() => {
                    document.querySelector('.loading-progress').style.display = 'none';
                }, 500);
            }
            progressBar.style.width = progress + '%';
        }, 200);
    }

    // Navbar animado al hacer scroll
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar-animated');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                navbar.classList.remove('visible');
            } else {
                // Scrolling up
                navbar.classList.add('visible');
            }
            lastScrollTop = scrollTop;
        });
    }

    // Agregar efectos hover a botones y cards existentes
    const buttons = document.querySelectorAll('button, .btn-primary, .btn-secondary, .hero-button, .services-button');
    buttons.forEach(btn => {
        btn.classList.add('btn-modern');
    });

    const cards = document.querySelectorAll('.package-card, .service-card, .reason-card');
    cards.forEach(card => {
        card.classList.add('card-hover');
    });
});
