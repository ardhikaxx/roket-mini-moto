document.addEventListener('DOMContentLoaded', function () {
    // 1. Sticky Navbar
    const navbar = document.querySelector('.navbar-custom');
    
    window.addEventListener('scroll', () => {
        if (navbar && navbar.getAttribute('data-is-home') === 'true') {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });

    // 2. Product Filtering & Live Search
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productItems = document.querySelectorAll('.product-item');
    const searchInput = document.getElementById('productSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    let currentFilter = 'all';

    function filterProducts() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        if (clearSearchBtn) {
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
        }

        let visibleCount = 0;
        productItems.forEach(item => {
            const categoryMatch = (currentFilter === 'all' || item.getAttribute('data-category') === currentFilter);
            const titleText = item.querySelector('.product-title')?.innerText.toLowerCase() || '';
            const descText = item.querySelector('.product-desc')?.innerText.toLowerCase() || '';
            const searchMatch = query === '' || titleText.includes(query) || descText.includes(query);

            if (categoryMatch && searchMatch) {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                }, 50);
                visibleCount++;
            } else {
                item.style.opacity = '0';
                item.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });

        // Show or hide empty search result notice
        const noProductMsg = document.getElementById('noProductMatchMsg');
        if (noProductMsg) {
            noProductMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    if (filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.getAttribute('data-filter');
                filterProducts();
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterProducts);
    }
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterProducts();
        });
    }

    // 3. Modal population
    const detailBtns = document.querySelectorAll('.btn-detail');
    const modalTitle = document.getElementById('productModalLabel');
    const modalImg = document.getElementById('modalProductImg');
    const modalDesc = document.getElementById('modalProductDesc');
    const modalCategory = document.getElementById('modalProductCategory');

    detailBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            const title = card.querySelector('.product-title').innerText;
            const desc = card.querySelector('.product-desc').innerText;
            const imgSrc = card.querySelector('img').src;
            const category = card.querySelector('.badge-category').innerText;

            modalTitle.innerText = title;
            modalDesc.innerText = desc;
            modalImg.src = imgSrc;
            modalCategory.innerText = category;
        });
    });

    // 4. Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if(targetId !== '#') {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const navbarHeight = document.querySelector('.navbar-custom').offsetHeight;
                    window.scrollTo({
                        top: targetElement.offsetTop - navbarHeight,
                        behavior: 'smooth'
                    });
                    
                    // If mobile menu is open, close it
                    const navbarToggler = document.querySelector('.navbar-toggler');
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        navbarToggler.click();
                    }
                }
            }
        });
    });


    // 5. Scroll Reveal Animation
    const revealElements = document.querySelectorAll('.category-card, .product-item, .feature-card, .about-img, .step-item, .section-padding h2, .cta-content, .reveal');
    
    revealElements.forEach(el => {
        if (!el.classList.contains('reveal')) {
            el.classList.add('reveal');
        }
    });

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, {
        root: null,
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    });

    revealElements.forEach(el => revealObserver.observe(el));
    
    // 6. Animated Stats Counter
    const counters = document.querySelectorAll('.counter');
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = +entry.target.getAttribute('data-target');
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // roughly 60fps
                
                let current = 0;
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        entry.target.innerText = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        entry.target.innerText = target;
                    }
                };
                updateCounter();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => counterObserver.observe(counter));
});
