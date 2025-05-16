document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert, .success').forEach(msg => {
        setTimeout(() => {
            msg.style.transition = "opacity 1s";
            msg.style.opacity = 0;
            setTimeout(() => msg.remove(), 1000);
        }, 4000);
    });

    document.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.boxShadow = 'none';
        });
    });
});
