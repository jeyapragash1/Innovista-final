document.addEventListener('DOMContentLoaded', () => {
    // --- Determine if user is logged in (from a data attribute set by PHP) ---
    // First, check if the body element exists before trying to access its dataset
    const bodyElement = document.querySelector('body');
    const isUserLoggedIn = bodyElement ? bodyElement.dataset.loggedIn === 'true' : false;

    // --- Sidebar Filtering ---
    const serviceNavItems = document.querySelectorAll('.service-nav .nav-item');
    const categoryItems = document.querySelectorAll('.category-list .category-item');
    const productSections = document.querySelectorAll('.product-section');
    const allProducts = document.querySelectorAll('.product-item');
    const categoryFilterBlock = document.getElementById('category-filter-block');

    serviceNavItems.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            const service = item.dataset.service;
            
            serviceNavItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            productSections.forEach(section => {
                section.id === `${service}-section` ? section.classList.add('active') : section.classList.remove('active');
            });

            // Show/hide category filter block
            if (service === 'interior-design') {
                categoryFilterBlock.style.display = 'block';
            } else {
                categoryFilterBlock.style.display = 'none';
            }
        });
    });

    categoryItems.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            const category = item.dataset.category;
            
            categoryItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            allProducts.forEach(product => {
                const productBelongsToInterior = product.closest('#interior-design-section');
                if (productBelongsToInterior) {
                    if (category === 'all' || product.dataset.category === category) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                }
            });
        });
    });

    // --- Product Modal Functionality ---
    const productModal = document.getElementById('productModal');
    if (productModal) {
        const modalOverlay = productModal.querySelector('.modal-overlay');
        const modalCloseBtn = productModal.querySelector('.modal-close-btn');

        document.querySelectorAll('.product-image').forEach(image => {
            image.addEventListener('click', () => {
                if (!isUserLoggedIn) {
                    window.location.href = 'login.php'; // Redirect if not logged in
                    return;
                }
                const productItem = image.closest('.product-item');
                document.getElementById('modalImage').src = productItem.querySelector('img').src;
                document.getElementById('modalBrand').textContent = productItem.querySelector('.brand-name').textContent;
                document.getElementById('modalTitle').textContent = productItem.querySelector('h4').textContent;
                document.getElementById('modalPrice').textContent = productItem.querySelector('.price').textContent;
                productModal.classList.add('active');
            });
        });

        const closeModal = () => productModal.classList.remove('active');
        modalOverlay.addEventListener('click', closeModal);
        modalCloseBtn.addEventListener('click', closeModal);
    }

    // --- Cart Functionality ---
    const cartSidebar = document.getElementById('cartSidebar');
    if(cartSidebar){
        const cartCloseBtn = cartSidebar.querySelector('.cart-close-btn');
    
        document.body.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-add-cart, .btn-add-cart-modal');
            if (button) {
                e.preventDefault();
                if (!isUserLoggedIn) {
                    window.location.href = 'login.php'; // Redirect if not logged in
                    return;
                }
                alert('Product added to cart! (Functionality to be built)');
                cartSidebar.classList.add('active');
            }
        });
        
        cartCloseBtn.addEventListener('click', () => cartSidebar.classList.remove('active'));
    }
});