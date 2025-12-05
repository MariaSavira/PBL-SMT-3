document.addEventListener("DOMContentLoaded", () => {
    const style = document.createElement("style");
    style.textContent = `
        .auto-muncul {
            opacity: 0;
            transform: translateY(40px);
            transition: 0.7s ease-out;
            will-change: opacity, transform;
        }
        .auto-muncul.show {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);

    const elements = document.querySelectorAll(
        "section:not(#hero), .fokus-riset-section"
    );
    
    elements.forEach(el => el.classList.add("auto-muncul"));
    
    function checkScroll() {
        elements.forEach(el => {
            const pos = el.getBoundingClientRect().top;
            const tinggi = window.innerHeight;

            if (pos < tinggi - 80) {
                el.classList.add("show");
            }
        });
    }

    window.addEventListener("scroll", checkScroll);
    checkScroll();
});