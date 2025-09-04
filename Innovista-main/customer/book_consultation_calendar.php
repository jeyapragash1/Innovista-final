<?php
$provider_id = isset($_GET['provider_id']) ? $_GET['provider_id'] : '';
?>
<!-- Customer Booking Calendar UI -->
<div id="customer-calendar-container" style="max-width: 400px; margin: 32px auto;">
    <h3>Book a Consultation</h3>
    <div id="customer-calendar-nav" style="display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
        <button id="customer-prev-month" style="margin-right: 8px;">&#8592;</button>
        <span id="customer-calendar-title" style="font-weight: 600; font-size: 1.1rem;"></span>
        <button id="customer-next-month" style="margin-left: 8px;">&#8594;</button>
    </div>
    <div id="customer-calendar-days" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;"></div>
    <div id="customer-calendar-info" style="margin-top: 16px; color: #059669; font-weight: 500;"></div>
</div>
<script>
// --- Customer Calendar JS ---
const providerId = "<?php echo htmlspecialchars($provider_id); ?>";
let customerCurrentYear = new Date().getFullYear();
let customerCurrentMonth = new Date().getMonth();
let providerAvailableDates = {};
let selectedConsultDate = null;

function fetchProviderAvailability() {
    fetch('provider/get_provider_availability.php?provider_id=' + providerId)
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.dates)) {
                providerAvailableDates = {};
                data.dates.forEach(dateStr => {
                    const [year, month, day] = dateStr.split('-').map(Number);
                    const key = `${year}-${month-1}`;
                    if (!providerAvailableDates[key]) providerAvailableDates[key] = [];
                    providerAvailableDates[key].push(day);
                });
                renderCustomerCalendar(customerCurrentYear, customerCurrentMonth);
            }
        });
}

function renderCustomerCalendar(year, month) {
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    document.getElementById('customer-calendar-title').textContent = monthNames[month] + ' ' + year;
    const today = new Date();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    let startDay = firstDay.getDay();
    const daysInMonth = lastDay.getDate();
    let html = '';
    for (let i = 0; i < startDay; i++) {
        html += `<div class='calendar-day not-current-month'></div>`;
    }
    const key = `${year}-${month}`;
    for (let d = 1; d <= daysInMonth; d++) {
        let classes = 'calendar-day';
        if (providerAvailableDates[key] && providerAvailableDates[key].includes(d)) {
            classes += ' calendar-available';
        }
        if (selectedConsultDate && selectedConsultDate.year === year && selectedConsultDate.month === month && selectedConsultDate.day === d) {
            classes += ' calendar-selected';
        }
        html += `<div class='${classes}' data-day='${d}' style='padding:10px;border-radius:6px;cursor:pointer;'>${d}</div>`;
    }
    document.getElementById('customer-calendar-days').innerHTML = html;
    document.querySelectorAll('#customer-calendar-days .calendar-day').forEach(function(cell) {
        if (cell.classList.contains('calendar-available')) {
            cell.onclick = function() {
                const day = parseInt(cell.getAttribute('data-day'));
                selectedConsultDate = { year, month, day };
                document.getElementById('customer-calendar-info').textContent = `Selected date: ${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                renderCustomerCalendar(year, month);
            };
        }
    });
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
document.addEventListener('DOMContentLoaded', function() {
    fetchProviderAvailability();
});
</script>
<style>
.calendar-available {
    background: #e0f2fe;
    color: #2563eb;
    border: 2px solid #2563eb;
}
.calendar-selected {
    background: #059669 !important;
    color: #fff !important;
    border: 2px solid #059669 !important;
}
</style>
