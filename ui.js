const header = document.getElementById('siteHeader');

if (header) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 8) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

const revealElements = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && revealElements.length > 0) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.14
    });

    revealElements.forEach((item) => observer.observe(item));
} else {
    revealElements.forEach((item) => item.classList.add('show'));
}

const toast = document.querySelector('[data-toast="true"]');
if (toast) {
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-8px)';
        setTimeout(() => {
            toast.remove();
        }, 250);
    }, 2800);
}

const mainImage = document.getElementById('mainGigImage');
const thumbs = document.querySelectorAll('.thumb');
if (mainImage && thumbs.length > 0) {
    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const image = thumb.getAttribute('data-image') || '';
            if (image.length > 0) {
                mainImage.src = image;
            }

            thumbs.forEach((item) => item.classList.remove('active'));
            thumb.classList.add('active');
        });
    });
}
