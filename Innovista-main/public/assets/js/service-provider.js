const providers = [
    {
        name: "Raj Kumar",
        category: "Interior Design",
        experience: "8 years",
        portfolio: "modern-bedroom-interior.jpg",
        quotation: "₹50,000 - ₹2,00,000",
        available: "Mon-Fri, 10am-6pm",
        description: "Serving the Northern Province of Sri Lanka. Expert in interior design for any kind of building."
    },
    {
        name: "Kamal Perera",
        category: "Lighting Installation",
        experience: "5 years",
        portfolio: "contemporary-ceiling-light.jpg",
        quotation: "₹5,000 - ₹25,000",
        available: "Mon-Sat, 9am-7pm",
        description: "Lighting solutions for all types of buildings in Northern Sri Lanka."
    },
    {
        name: "Nirosha Siva",
        category: "Bathroom Renovation",
        experience: "10 years",
        portfolio: "bathroom 1.webp",
        quotation: "₹30,000 - ₹1,50,000",
        available: "Tue-Sun, 11am-5pm",
        description: "Modern bathroom renovations for homes, offices, and commercial spaces in the Northern Province."
    },
    {
        name: "Suresh Rajan",
        category: "Kitchen Remodeling",
        experience: "7 years",
        portfolio: "kitchen-cabinet-1.jpg",
        quotation: "₹40,000 - ₹1,80,000",
        available: "Mon-Fri, 10am-6pm",
        description: "Expert kitchen remodeling for any building in Northern Sri Lanka."
    },
    {
        name: "Anjali Fernando",
        category: "Painting Services",
        experience: "12 years",
        portfolio: "elegant-wall-paint.jpg",
        quotation: "₹8,000 - ₹50,000",
        available: "Mon-Sat, 8am-8pm",
        description: "Professional painting for residential, commercial, and industrial buildings in the Northern Province."
    },
    {
        name: "Manoj Selvan",
        category: "Furniture Upholstery",
        experience: "6 years",
        portfolio: "elegant-bed-design.jpg",
        quotation: "₹15,000 - ₹60,000",
        available: "Mon-Sat, 10am-5pm",
        description: "Furniture upholstery and repair for all types of buildings in Northern Sri Lanka."
    },
    {
        name: "Priya Nadarajah",
        category: "Wall Finishing",
        experience: "9 years",
        portfolio: "modern-wall-finish.jpg",
        quotation: "₹10,000 - ₹40,000",
        available: "Mon-Fri, 9am-6pm",
        description: "Wall finishing and restoration for any building in the Northern Province."
    },
    {
        name: "Ramesh Tharmalingam",
        category: "Ceiling Design",
        experience: "11 years",
        portfolio: "modern-ceiling-design.jpg",
        quotation: "₹20,000 - ₹90,000",
        available: "Mon-Sat, 10am-7pm",
        description: "Ceiling design and installation for all building types in Northern Sri Lanka."
    }
];

const providerPortfolios = {
    "Raj Kumar": ["modern-bedroom-interior.jpg", "elegant-bed-design.jpg", "contemporary-bedroom.jpg"],
    "Kamal Perera": ["contemporary-ceiling-light.jpg", "modern-ceiling-design.jpg", "elegant-ceiling-light.jpg"],
    "Nirosha Siva": ["bathroom 1.webp", "bathroom 10.webp", "bathroom 12.webp"],
    "Suresh Rajan": ["kitchen-cabinet-1.jpg", "kitchen-cabinet-2.jpg", "kitchen-appliance-1.jpg"],
    "Anjali Fernando": ["elegant-wall-paint.jpg", "modern-wall-paint.jpg", "gold-wall-paint.jpg"],
    "Manoj Selvan": ["elegant-bed-design.jpg", "sophisticated-bedroom.jpg", "luxury-bed-frame.jpg"],
    "Priya Nadarajah": ["modern-wall-finish.jpg", "elegant-wall-design.jpg", "contemporary-wall-finish.jpg"],
    "Ramesh Tharmalingam": ["modern-ceiling-design.jpg", "elegant-ceiling-light.jpg", "contemporary-ceiling-fixture.jpg"]
};

function getQueryParams() {
    const params = {};
    window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        params[decodeURIComponent(key)] = decodeURIComponent(value);
    });
    return params;
}

const list = document.getElementById('service-provider-list');
const params = getQueryParams();

let filteredProviders = providers;

if (params.category) {
    filteredProviders = providers.filter(p => p.category.toLowerCase() === params.category.toLowerCase());
} else if (params.service && params.package === '1') {
    // Show all providers for a service type (e.g., all interior design related)
    if (params.service === 'interior-design') {
        filteredProviders = providers.filter(p => p.category.toLowerCase().includes('interior') || p.category.toLowerCase().includes('ceiling') || p.category.toLowerCase().includes('furniture') || p.category.toLowerCase().includes('lighting'));
    } else if (params.service === 'painting') {
        filteredProviders = providers.filter(p => p.category.toLowerCase().includes('paint'));
    } else if (params.service === 'restoration') {
        filteredProviders = providers.filter(p => p.category.toLowerCase().includes('renovation') || p.category.toLowerCase().includes('finishing') || p.category.toLowerCase().includes('upholstery'));
    }
}

list.innerHTML = '';

// Modal HTML
if (!document.getElementById('portfolioModal')) {
    const modal = document.createElement('div');
    modal.id = 'portfolioModal';
    modal.style.display = 'none';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.background = 'rgba(0,0,0,0.7)';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    modal.style.zIndex = '9999';
    modal.innerHTML = `
        <div id="portfolioModalContent" style="background:#fff;padding:2rem;border-radius:12px;max-width:600px;width:90vw;max-height:90vh;overflow:auto;position:relative;">
            <button id="closePortfolioModal" style="position:absolute;top:10px;right:10px;font-size:1.5rem;background:none;border:none;cursor:pointer;">&times;</button>
            <h2 id="portfolioModalTitle" style="margin-top:0;"></h2>
            <div id="portfolioModalImages" style="display:flex;gap:1rem;flex-wrap:wrap;"></div>
        </div>
    `;
    document.body.appendChild(modal);
    document.getElementById('closePortfolioModal').onclick = function() {
        document.getElementById('portfolioModal').style.display = 'none';
    };
}

// Booking Modal HTML
if (!document.getElementById('bookingModalOverlay')) {
    const bookingModal = document.createElement('div');
    bookingModal.id = 'bookingModalOverlay';
    bookingModal.style.display = 'none';
    bookingModal.style.position = 'fixed';
    bookingModal.style.top = '0';
    bookingModal.style.left = '0';
    bookingModal.style.width = '100vw';
    bookingModal.style.height = '100vh';
    bookingModal.style.background = 'rgba(0,0,0,0.7)';
    bookingModal.style.justifyContent = 'center';
    bookingModal.style.alignItems = 'center';
    bookingModal.style.zIndex = '9999';
    bookingModal.innerHTML = `
        <div id="bookingModalContent" style="background:#fff;padding:0;border-radius:12px;max-width:520px;width:98vw;max-height:95vh;overflow:auto;position:relative;">
            <button id="closeBookingModal" style="position:absolute;top:10px;right:10px;font-size:1.5rem;background:none;border:none;cursor:pointer;z-index:2;">&times;</button>
            <iframe id="bookingIframe" src="" style="width:100%;height:80vh;border:none;border-radius:12px;"></iframe>
        </div>
    `;
    document.body.appendChild(bookingModal);
    document.getElementById('closeBookingModal').onclick = function() {
        document.getElementById('bookingModalOverlay').style.display = 'none';
        document.getElementById('bookingIframe').src = '';
    };
}

if (filteredProviders.length === 0) {
    list.innerHTML = '<div style="padding:2rem;text-align:center;">No service providers found for this category.</div>';
} else {
    filteredProviders.forEach(provider => {
        const card = document.createElement('div');
        card.className = 'provider-card';
        const providerNameParam = encodeURIComponent(provider.name);
        card.innerHTML = `
            <h2>${provider.name}</h2>
            <div class="provider-category">${provider.category}</div>
            <div class="provider-experience">Experience: ${provider.experience}</div>
            <div class="provider-portfolio"><button class="view-portfolio-btn" data-provider="${provider.name}">View Portfolio</button></div>
            <div class="provider-quotation">Quotation: ${provider.quotation}</div>
            <div class="provider-available"><a href="availability-calendar.html?provider=${providerNameParam}" target="_blank" class="check-availability-btn">Check Availability</a></div>
            <div class="provider-description">${provider.description}</div>
            <div class="provider-booking"><button class="book-now-btn" data-provider="${provider.name}">Book Services</button></div>
        `;
        list.appendChild(card);
    });
    // Add event listeners for portfolio buttons
    setTimeout(() => {
        document.querySelectorAll('.view-portfolio-btn').forEach(btn => {
            btn.onclick = function() {
                const provider = this.getAttribute('data-provider');
                const images = providerPortfolios[provider] || [];
                document.getElementById('portfolioModalTitle').textContent = provider + " - Completed Works";
                const imgDiv = document.getElementById('portfolioModalImages');
                imgDiv.innerHTML = images.map(img => `<img src='${img}' alt='${provider} work' style='width:180px;border-radius:8px;'>`).join('');
                document.getElementById('portfolioModal').style.display = 'flex';
            };
        });
        // Add event listeners for booking buttons
        document.querySelectorAll('.book-now-btn').forEach(btn => {
            btn.onclick = function() {
                document.getElementById('bookingIframe').src = 'booking.html';
                document.getElementById('bookingModalOverlay').style.display = 'flex';
            };
        });
    }, 0);
} 