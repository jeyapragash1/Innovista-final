<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\customer\book_consultation_calendar.php
// This file is likely included or loaded dynamically within another customer page.
// It assumes session is already started and user is authenticated by the parent page.

$provider_id = isset($_GET['provider_id']) ? filter_input(INPUT_GET, 'provider_id', FILTER_SANITIZE_NUMBER_INT) : '';
if (empty($provider_id)) {
    echo "<p style='color:red;'>Error: Provider ID is missing for booking calendar.</p>";
    exit();
}
?>
<!-- Customer Booking Calendar UI -->
<div id="customer-calendar-container" style="max-width: 400px; margin: 32px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
    <h3>Book a Consultation</h3>
    <div id="customer-calendar-nav" style="display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
        <button id="customer-prev-month" class="btn btn-secondary btn-sm" style="margin-right: 8px; padding: 6px 12px;">&#8592; Prev</button>
        <span id="customer-calendar-title" style="font-weight: 600; font-size: 1.1rem; color: #333;"></span>
        <button id="customer-next-month" class="btn btn-secondary btn-sm" style="margin-left: 8px; padding: 6px 12px;">Next &#8594;</button>
    </div>
    <div id="customer-calendar-days" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;">
        <!-- Day names -->
        <div class="calendar-weekday" style="font-weight: bold; text-align: center; color: #555;">Sun</div>
        <div class="calendar-weekday" style="font-weight: bold; text-align: center; color: #555;">Mon</div>
        <div class="calendar-weekday" style="font-weight: bold; text-align: center; color: #555;">Tue</div>
        <div class="calendar-weekday" style="font-weight: bold; text-align: center; color: #555;">Wed</div>
        <div class="calendar-weekday" style="font-weight: bold; text-align: center; color: #555;">Thu</div>
        <div class="calendar-weekday" style="font-weight: bold; text-align: center; color: #555;">Fri</div>
        <div class="calendar-weekday" style="font-weight: bold; text-align: center; color: #555;">Sat</div>
        <!-- Days will be rendered here -->
    </div>
    <div id="customer-calendar-info" style="margin-top: 16px; color: #059669; font-weight: 500;"></div>
    <button id="customer-book-selected-date" class="btn btn-primary" style="width: 100%; margin-top: 20px; display: none;">Book Selected Date</button>
</div>
<script>
// --- Customer Calendar JS ---
const providerId = "<?php echo htmlspecialchars($provider_id); ?>";
let customerCurrentYear = new Date().getFullYear();
let customerCurrentMonth = new Date().getMonth(); // 0-11
let providerAvailableDates = {}; // Stores available days per month: { 'YYYY-M': [D1, D2, ...]} (month is 0-indexed here)
let selectedConsultDate = null; // { year, month (0-indexed), day }

function fetchProviderAvailability() {
    // FIX: Corrected path for AJAX request from customer/ to provider/
    fetch('../provider/get_provider_availability.php?provider_id=' + providerId)
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.dates)) {
                providerAvailableDates = {};
                data.dates.forEach(dateStr => {
                    const [year, month, day] = dateStr.split('-').map(Number);
                    const key = `${year}-${month - 1}`; // Adjust month to be 0-indexed for JS
                    if (!providerAvailableDates[key]) providerAvailableDates[key] = [];
                    providerAvailableDates[key].push(day);
                });
                renderCustomerCalendar(customerCurrentYear, customerCurrentMonth);
            } else {
                 document.getElementById('customer-calendar-info').textContent = "No availability found for this provider.";
            }
        })
        .catch(error => {
            console.error('Error fetching provider availability:', error);
            document.getElementById('customer-calendar-info').textContent = "Error loading availability.";
        });
}

function renderCustomerCalendar(year, month) {
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    document.getElementById('customer-calendar-title').textContent = monthNames[month] + ' ' + year;
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Normalize today to start of day

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    let startingDayOfWeek = firstDayOfMonth.getDay(); // 0 for Sunday, 1 for Monday...

    const daysInMonth = lastDayOfMonth.getDate();
    let html = '';

    // Add weekday headers
    const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    weekdays.forEach(day => {
        html += `<div class="calendar-day calendar-weekday" style="font-weight: bold; text-align: center; color: #555; padding: 10px;">${day}</div>`;
    });

    // Fill leading empty days
    for (let i = 0; i < startingDayOfWeek; i++) {
        html += `<div class='calendar-day not-current-month'></div>`;
    }

    const currentMonthKey = `${year}-${month}`; // Key for providerAvailableDates

    for (let d = 1; d <= daysInMonth; d++) {
        let classes = 'calendar-day';
        const currentDate = new Date(year, month, d);
        currentDate.setHours(0,0,0,0); // Normalize current day to start of day

        let isAvailable = providerAvailableDates[currentMonthKey] && providerAvailableDates[currentMonthKey].includes(d);
        let isPastDate = currentDate < today; // Check if date is in the past

        if (isAvailable && !isPastDate) {
            classes += ' calendar-available';
        } else if (isPastDate) {
            classes += ' calendar-past'; // Style past dates differently
        }

        if (selectedConsultDate && 
            selectedConsultDate.year === year && 
            selectedConsultDate.month === month && 
            selectedConsultDate.day === d) {
            classes += ' calendar-selected';
        }

        html += `<div class='${classes}' data-day='${d}' style='padding:10px;border-radius:6px;cursor:pointer;'>${d}</div>`;
    }
    document.getElementById('customer-calendar-days').innerHTML = html;

    // Add event listeners for selectable days
    document.querySelectorAll('#customer-calendar-days .calendar-day.calendar-available').forEach(function(cell) {
        cell.onclick = function() {
            // Clear previous selection
            document.querySelectorAll('.calendar-day.calendar-selected').forEach(s => s.classList.remove('calendar-selected'));

            const day = parseInt(cell.getAttribute('data-day'));
            selectedConsultDate = { year, month, day };
            document.getElementById('customer-calendar-info').textContent = `Selected date: ${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            cell.classList.add('calendar-selected'); // Add class to the newly selected cell
            document.getElementById('customer-book-selected-date').style.display = 'block'; // Show book button
        };
    });

    // Hide book button if no date is selected or if current month has no available dates
    if (!selectedConsultDate || !providerAvailableDates[currentMonthKey] || providerAvailableDates[currentMonthKey].length === 0) {
        document.getElementById('customer-book-selected-date').style.display = 'none';
        document.getElementById('customer-calendar-info').textContent = "Select an available date.";
    }
    // If a date was selected in a previous month, and now we move to a different month, clear it
    if (selectedConsultDate && (selectedConsultDate.year !== year || selectedConsultDate.month !== month)) {
        selectedConsultDate = null;
        document.getElementById('customer-book-selected-date').style.display = 'none';
        document.getElementById('customer-calendar-info').textContent = "Select an available date.";
    }
}

document.getElementById('customer-prev-month').onclick = function() {
    customerCurrentMonth--;
    if (customerCurrentMonth < 0) {
        customerCurrentMonth = 11;
        customerCurrentYear--;
    }
    renderCustomerCalendar(customerCurrentYear, customerCurrentMonth);
};
document.getElementById('customer-next-month').onclick = function() {
    customerCurrentMonth++;
    if (customerCurrentMonth > 11) {
        customerCurrentMonth = 0;
        customerCurrentYear++;
    }
    renderCustomerCalendar(customerCurrentYear, customerCurrentMonth);
};

// Event listener for the "Book Selected Date" button
document.getElementById('customer-book-selected-date').onclick = function() {
    if (selectedConsultDate) {
        const fullDate = `${selectedConsultDate.year}-${String(selectedConsultDate.month+1).padStart(2,'0')}-${String(selectedConsultDate.day).padStart(2,'0')}`;
        // You would typically open a booking form or make an AJAX request here
        // For now, let's just alert the selection
        alert(`You are booking a consultation with Provider ID: ${providerId} on ${fullDate}`);
        // Here you could redirect to a form or open a modal:
        // window.location.href = `request_quotation.php?provider_id=${providerId}&date=${fullDate}`;
    } else {
        alert("Please select a date first.");
    }
};


document.addEventListener('DOMContentLoaded', function() {
    fetchProviderAvailability();
});
</script>
<style>
/* Add these styles to your public/assets/css/main.css or a new customer-calendar.css */
#customer-calendar-container {
    max-width: 400px;
    margin: 32px auto;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}
#customer-calendar-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}
#customer-calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    text-align: center;
}
.calendar-day {
    padding: 10px;
    border-radius: 6px;
    cursor: pointer;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    color: #4b5563;
    transition: all 0.2s ease;
}
.calendar-day.calendar-weekday {
    font-weight: bold;
    background-color: #f2f2f2;
    border-color: #d0d0d0;
    color: #333;
}
.calendar-day:hover:not(.not-current-month):not(.calendar-past):not(.calendar-available) {
    background-color: #e5e7eb;
}
.calendar-day.not-current-month,
.calendar-day.calendar-past {
    color: #cbd5e1;
    cursor: not-allowed;
    background-color: #f0f2f5;
    border-color: #e2e8f0;
}
.calendar-available {
    background: #e0f2fe;
    color: #2563eb;
    border: 1px solid #2563eb;
    font-weight: 600;
}
.calendar-available:hover {
    background: #bfdbfe;
    color: #1d4ed8;
    border-color: #1d4ed8;
}
.calendar-selected {
    background: #059669 !important; /* Innovista primary green */
    color: #fff !important;
    border: 1px solid #059669 !important;
    box-shadow: 0 2px 5px rgba(5, 150, 105, 0.3);
}
#customer-calendar-info {
    margin-top: 16px;
    color: #059669;
    font-weight: 500;
    text-align: center;
}
/* Style for custom buttons if you have them */
.btn.btn-primary, .btn.btn-secondary {
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
}
.btn.btn-primary {
    background-color: #0d9488; /* Innovista primary color */
    color: #fff;
}
.btn.btn-primary:hover {
    background-color: #0a756b;
}
.btn.btn-secondary {
    background-color: #e2e8f0;
    color: #4b5563;
}
.btn.btn-secondary:hover {
    background-color: #cbd5e1;
}
</style>