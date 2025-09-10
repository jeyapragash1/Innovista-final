document.addEventListener('DOMContentLoaded', () => {
    // --- Painting Section DOM Variables ---
    const paintingBrandGrid = document.getElementById('painting-brand-grid');
    const paintingColorPanel = document.getElementById('painting-color-panel');
    const paintingColorGrid = document.getElementById('painting-color-grid');
    const colorPanelTitle = document.getElementById('color-panel-title');
    const backToBrandsBtn = document.getElementById('back-to-brands');
    const paintTypeSelect = document.getElementById('paintTypeSelect');
    const paintTypeSheen = document.getElementById('paintTypeSheen');

    // --- Paint Type Selection (now only after brand is selected) ---
    const paintTypeBlock = document.getElementById('paint-type-select-block');
    if (paintTypeSelect && paintTypeBlock) {
        paintTypeSelect.addEventListener('change', function() {
            if (paintTypeSheen) {
                paintTypeSheen.textContent = paintTypeSelect.value ? `Best type: ${paintTypeSelect.value}` : '';
            }
            // Show colors for selected brand and type
            if (window._selectedBrandIdx !== undefined) {
                showBrandColors(window._selectedBrandIdx, true);
            }
        });
        // Hide paint type block initially
        paintTypeBlock.style.display = 'none';
    }
    // --- Painting Brands and Colors Data ---
    const standardColors = [
        { name: 'Royal Blue', hex: '#2a3eb1', price: 1200 },
        { name: 'Sunshine Yellow', hex: '#ffe066', price: 1150 },
        { name: 'Classic White', hex: '#fff', price: 1100 },
        { name: 'Emerald Green', hex: '#50c878', price: 1050 },
        { name: 'Coral Red', hex: '#ff6f61', price: 1250 },
        { name: 'Ivory', hex: '#fffff0', price: 1000 },
        { name: 'Ocean Blue', hex: '#0077be', price: 1300 },
        { name: 'Peach', hex: '#ffdab9', price: 1200 },
        { name: 'Charcoal Grey', hex: '#36454f', price: 1350 },
        { name: 'Lavender', hex: '#b57edc', price: 1280 },
        { name: 'Mint Green', hex: '#98ff98', price: 1180 },
        { name: 'Rose Pink', hex: '#ff66cc', price: 1220 }
    ];
    const paintingBrands = [
        {
            name: 'Asian Paints',
            logo: 'https://5.imimg.com/data5/SELLER/Default/2022/11/DQ/TC/MM/150810776/asian-paints-apex-ultima-weatherproof-exterior-emulsion-paint-1000x1000.png',
            colors: standardColors
        },
        {
            name: 'Nippon Paint',
            logo: 'https://dkpo4ygqb6rh6.cloudfront.net/GLOBALPAINT_COM/imageresized/3543/1f394db21cee9ca1c626828b31db53cd/330_440_9_normalpng/finish_satin.png',
            colors: standardColors
        },
        {
            name: 'Dulux',
            logo: 'https://www.britishpaints.in/media/images/product/thumbnail/all-rounder-primer-thumb-1617107592.jpg',
            colors: standardColors
        }
    ];

    // --- Painting Section Interactivity ---
    // (Removed duplicate DOM variable declarations above)

    function renderPaintingBrands() {
        paintingBrandGrid.innerHTML = '';
        paintingBrands.forEach((brand, idx) => {
            const card = document.createElement('div');
            card.className = 'brand-card';
            card.innerHTML = `
                <img src="${brand.logo}" alt="${brand.name}" class="brand-logo" />
                <div class="brand-title">${brand.name}</div>
            `;
            card.addEventListener('click', () => {
                window._selectedBrandIdx = idx;
                // Show paint type dropdown, hide colors
                paintingBrandGrid.style.display = 'none';
                paintingColorPanel.style.display = 'block';
                if (paintTypeBlock) {
                    paintTypeBlock.style.display = 'block';
                    paintTypeSelect.selectedIndex = 0;
                    paintTypeSheen.textContent = '';
                }
                colorPanelTitle.textContent = `${brand.name} - Available Colors`;
                paintingColorGrid.innerHTML = '';
            });
            paintingBrandGrid.appendChild(card);
        });
        paintingBrandGrid.style.display = 'grid';
        paintingColorPanel.style.display = 'none';
        if (paintTypeBlock) paintTypeBlock.style.display = 'none';
    }

    function showBrandColors(brandIdx, forceShow) {
        // Only show colors if paint type is selected
        if (!forceShow && (!paintTypeSelect || !paintTypeSelect.value)) {
            paintingColorGrid.innerHTML = '';
            return;
        }
        const brand = paintingBrands[brandIdx];
        colorPanelTitle.textContent = `${brand.name} - Available Colors`;
        paintingColorGrid.innerHTML = '';
        brand.colors.forEach(color => {
            const colorCard = document.createElement('div');
            colorCard.className = 'color-card';
            colorCard.innerHTML = `
                <div class="color-swatch" style="background:${color.hex}"></div>
                <div class="color-info">
                    <div class="color-name">${color.name}</div>
                    <div class="color-price">Rs. ${color.price} / L</div>
                    <div class="color-liters">
                        <label>Liters: </label>
                        <select class="color-liters-select">
                            <option value="1">1L</option>
                            <option value="2">2L</option>
                            <option value="5">5L</option>
                            <option value="10">10L</option>
                        </select>
                    </div>
                </div>
                <button class="btn-add-cart painting-purchase-btn" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
            `;
            paintingColorGrid.appendChild(colorCard);
        });
        // Add custom color option
        const customCard = document.createElement('div');
        customCard.className = 'color-card';
        customCard.innerHTML = `
            <div class="color-swatch" style="background:linear-gradient(135deg,#fff,#eee,#ccc,#000,#f00,#0f0,#00f,#ff0,#0ff,#f0f,#fa0,#0af)"></div>
            <div class="color-info custom-color-info" style="display:flex;flex-direction:column;gap:0.5rem;align-items:flex-start;">
                <div class="custom-color-label">Custom Color</div>
                <input type="color" class="custom-color-picker" value="#ffffff" style="width:40px;height:40px;">
                <input type="text" class="custom-color-hex" value="#ffffff" readonly>
                <input type="text" class="custom-color-name" placeholder="Color Name" readonly>
                <input type="number" class="custom-color-price" placeholder="Price/L" min="1" readonly>
                <div style="display:flex;align-items:center;gap:8px;">
                    <label>Liters:</label>
                    <select class="color-liters-select">
                        <option value="1">1L</option>
                        <option value="2">2L</option>
                        <option value="5">5L</option>
                        <option value="10">10L</option>
                    </select>
                </div>
            </div>
            <button class="btn-add-cart painting-purchase-btn" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
        `;
        paintingColorGrid.appendChild(customCard);
        // Auto-generate price based on color value
        const colorInput = customCard.querySelector('.custom-color-picker');
        const priceInput = customCard.querySelector('.custom-color-price');
        function autoPrice(hex) {
            let sum = 0;
            for (let i = 0; i < hex.length; i++) sum += hex.charCodeAt(i);
            return 1000 + (sum % 501); // 1000-1500
        }
        function autoColorName(hex) {
            // Simple mapping for demo: use hex as name, or map to a basic color
            const colorNames = {
                '#ffffff': 'White', '#000000': 'Black', '#ff0000': 'Red', '#00ff00': 'Green', '#0000ff': 'Blue',
                '#ffff00': 'Yellow', '#00ffff': 'Cyan', '#ff00ff': 'Magenta', '#b57edc': 'Lavender', '#98ff98': 'Mint', '#ff66cc': 'Rose',
                '#ffe066': 'Sunshine', '#2a3eb1': 'Royal Blue', '#50c878': 'Emerald', '#ff6f61': 'Coral', '#0077be': 'Ocean', '#ffdab9': 'Peach', '#36454f': 'Charcoal'
            };
            return colorNames[hex.toLowerCase()] || hex.toUpperCase();
        }
        function updateCustomFields() {
            priceInput.value = autoPrice(colorInput.value);
            nameInput.value = autoColorName(colorInput.value);
            hexInput.value = colorInput.value.toUpperCase();
        }
        const nameInput = customCard.querySelector('.custom-color-name');
        const hexInput = customCard.querySelector('.custom-color-hex');
        colorInput.addEventListener('input', updateCustomFields);
        // Set initial values
        updateCustomFields();
    }

    if (paintingBrandGrid && paintingColorPanel) {
        // Show brands when painting section is activated
        document.querySelectorAll('.service-nav .nav-item').forEach(item => {
            item.addEventListener('click', e => {
                if (item.dataset.service === 'painting') {
                    paintingBrandGrid.style.display = 'grid';
                    renderPaintingBrands();
                }
            });
        });
        // Back to brands
        backToBrandsBtn.addEventListener('click', renderPaintingBrands);
        // Initial render if painting is default
        if (document.querySelector('.service-nav .nav-item.active')?.dataset.service === 'painting') {
            paintingBrandGrid.style.display = 'grid';
            renderPaintingBrands();
        }
    }
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