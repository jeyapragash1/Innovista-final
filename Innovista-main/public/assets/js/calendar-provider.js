// Provider Calendar JS

let availableDates = {};
let currentYear = new Date().getFullYear();
let currentMonth = new Date().getMonth();
let selectedDates = [];

// Fetch provider's availability from the database
function fetchAvailability() {
    fetch('get_availability.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.dates)) {
                availableDates = {};
                data.dates.forEach(dateStr => {
                    const [year, month, day] = dateStr.split('-').map(Number);
                    const key = `${year}-${month-1}`;
                    if (!availableDates[key]) availableDates[key] = [];
                    availableDates[key].push(day);
                });
                renderCalendar(currentYear, currentMonth);
            }
        });
}

function renderSelectedDates() {
    // No-op: removed selected-range display for this UI
}

function renderCalendar(year, month) {
    renderSelectedDates();
    // No-op: removed calendar-year dropdown for this UI
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    document.getElementById('calendar-title').textContent = monthNames[month] + ' ' + year;

    const today = new Date();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    // Use Monday as first day (real-time calendar)
    let startDay = firstDay.getDay(); // 0=Sunday, 1=Monday, ...
    startDay = (startDay === 0) ? 6 : startDay - 1; // 0=Monday, 6=Sunday
    const daysInMonth = lastDay.getDate();
    const prevMonthLastDay = new Date(year, month, 0).getDate();

    let html = '';
    // Fill previous month's overflow days
    for (let i = 0; i < startDay; i++) {
        const prevMonth = month === 0 ? 11 : month - 1;
        const prevYear = month === 0 ? year - 1 : year;
        const prevMonthLastDay = new Date(prevYear, prevMonth + 1, 0).getDate();
        html += `<div class='calendar-day not-current-month'>${prevMonthLastDay - startDay + i + 1}</div>`;
    }
    const key = `${year}-${month}`;
    if (!availableDates[key]) availableDates[key] = [];
    for (let d = 1; d <= daysInMonth; d++) {
        let classes = 'calendar-day';
        // Highlight today
        if (
            year === today.getFullYear() &&
            month === today.getMonth() &&
            d === today.getDate()
        ) {
            classes += ' calendar-today';
        }
        // Highlight selected individual dates
        if (selectedDates.some(dt => dt.year === year && dt.month === month && dt.day === d)) {
            classes += ' calendar-range calendar-range-edge';
        }
        // Highlight available dates from database
        const key = `${year}-${month}`;
        if (availableDates[key] && availableDates[key].includes(d)) {
            classes += ' calendar-available';
        }
        // Style weekends
        let weekDay = (startDay + d - 1) % 7;
        if (weekDay === 0 || weekDay === 6) {
            classes += ' calendar-weekend';
        }
        html += `<div class='${classes}' data-day='${d}'>${d}</div>`;
    }
    const totalCells = startDay + daysInMonth;
    const nextDays = (7 - (totalCells % 7)) % 7;
    // Fill next month's overflow days
    for (let i = 1; i <= nextDays; i++) {
        html += `<div class='calendar-day not-current-month'>${i}</div>`;
    }
    document.getElementById('calendar-days').innerHTML = html;
    // Add click event for selecting a range
    document.querySelectorAll('.calendar-day').forEach(function(cell) {
        if (!cell.classList.contains('not-current-month')) {
            cell.onclick = function() {
                const day = parseInt(cell.getAttribute('data-day'));
                const idx = selectedDates.findIndex(dt => dt.year === year && dt.month === month && dt.day === day);
                if (idx === -1) {
                    selectedDates.push({ year, month, day });
                } else {
                    selectedDates.splice(idx, 1);
                }
                renderCalendar(year, month);
            };
        }
    });
}

document.getElementById('prev-month').onclick = function() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendar(currentYear, currentMonth);
};
document.getElementById('next-month').onclick = function() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar(currentYear, currentMonth);
};
// Removed calendar-year dropdown event for this UI
renderCalendar(currentYear, currentMonth);
document.addEventListener('DOMContentLoaded', function() {
    fetchAvailability();
});

document.getElementById('save-availability').onclick = function() {
    if (selectedDates.length === 0) {
        alert('Please select at least one date.');
        return;
    }
    // Prepare array of date strings
    const dates = selectedDates.map(d => `${d.year}-${String(d.month+1).padStart(2,'0')}-${String(d.day).padStart(2,'0')}`);
    // Send to backend
    fetch('save_availability.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ dates: dates })
    })
    .then(res => {
        // Check if response is valid JSON
        return res.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                alert('Error saving availability. Invalid server response.');
                throw e;
            }
        });
    })
    .then(data => {
        if (data.success) {
            alert('Availability saved successfully!');
            selectedDates = [];
            renderSelectedDates();
            fetchAvailability();
        } else {
            alert('Error saving availability. ' + (data.error || ''));
        }
    })
    .catch(() => alert('Error saving availability.'));
};
