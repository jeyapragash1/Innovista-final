
<?php 
$pageTitle = 'Manage Calendar';
require_once 'provider_header.php'; 
?>
<div class="booking-layout">
    <div class="booking-card">
        <div class="booking-header">Confirm Date & Time</div>
        <div class="booking-content">
            <div class="calendar-panel">
                <div class="calendar-header">
                    <button id="prev-month" class="calendar-arrow">&#8592;</button>
                    <span class="calendar-title" id="calendar-title"></span>
                    <button id="next-month" class="calendar-arrow">&#8594;</button>
                </div>
                <div class="calendar-card">
                                <div class="calendar-grid-header">
                                    <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                </div>
                                <div id="calendar-days" class="calendar-days"></div>
                </div>
            </div>
            <div class="time-panel">
                <div class="time-header">Confirm Time</div>
                <div class="time-slots" id="time-slots">
                    <!-- Time slots will be generated here -->
                </div>
                <button class="save-btn" id="save-btn">Save</button>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<style>
.booking-layout {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: #f5f6fa;
}
.booking-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 16px #e0e7ff;
    padding: 0;
    min-width: 700px;
    max-width: 900px;
    width: 900px;
}
.booking-header {
    background: #1eb6e9;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 700;
    padding: 18px 32px;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
    letter-spacing: 0.5px;
}
.booking-content {
    display: flex;
    flex-direction: row;
    padding: 32px 32px 24px 32px;
    gap: 32px;
}
.calendar-panel {
    min-width: 320px;
    max-width: 340px;
}
.calendar-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
    margin-bottom: 18px;
}
.calendar-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #222;
    min-width: 140px;
    text-align: center;
}
.calendar-arrow {
    background: #e0e7ff;
    border: none;
    border-radius: 8px;
    padding: 4px 14px;
    font-size: 1.1rem;
    color: #1eb6e9;
    font-weight: 700;
    cursor: pointer;
}
.calendar-card {
        background: #f5f6fa;
        border-radius: 12px;
        padding: 12px 8px 18px 8px;
        box-shadow: 0 2px 8px #e0e7ff;
            min-height: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
}
.calendar-grid-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0;
    margin-bottom: 8px;
    font-weight: 600;
    color: #1eb6e9;
    text-align: center;
    font-size: 0.98rem;
}
.calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
            grid-template-rows: repeat(6, 44px);
        gap: 0;
        min-height: 240px;
        text-align: center;
}
.calendar-day {
        background: transparent;
        border-radius: 0;
            width: 38px;
            height: 38px;
            justify-self: center;
            align-self: center;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 1.1rem;
        color: #222;
        font-weight: 600;
        position: relative;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        margin: 0 auto;
        box-shadow: none;
        border: none;
}
.calendar-day.selected {
        background: #1eb6e9;
        color: #fff;
        font-weight: 700;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(30,182,233,0.10);
        width: 36px;
        height: 36px;
        margin: auto;
}
.calendar-day.today {
    border: none;
    font-weight: normal;
    color: #222;
    background: transparent;
}
.calendar-day:hover {
    background: #e0e7ff;
    color: #1eb6e9;
    border-radius: 50%;
}
.time-panel {
    min-width: 220px;
    background: #fafbfc;
    border-radius: 12px;
    padding: 18px 18px 24px 18px;
    box-shadow: 0 2px 8px #e0e7ff;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.time-header {
    font-size: 1.05rem;
    font-weight: 600;
    color: #222;
    margin-bottom: 12px;
}
.time-slots {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 16px;
    margin-bottom: 18px;
    width: 100%;
}
.time-slot {
    background: #fff;
    border: 1px solid #e0e7ff;
    border-radius: 8px;
    padding: 8px 0;
    text-align: center;
    font-size: 1rem;
    color: #1eb6e9;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, border 0.2s;
}
.time-slot.selected, .time-slot:hover {
    background: #1eb6e9;
    color: #fff;
    border: 1px solid #1eb6e9;
}
.save-btn {
    background: #1eb6e9;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 32px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    align-self: center;
    margin-top: 8px;
    box-shadow: 0 2px 8px #e0e7ff;
    transition: background 0.2s;
}
.save-btn:hover {
    background: #159ac2;
}
</style>
<script src="../public/assets/js/calendar-provider.js"></script>
<script>
// Time slots for PM (example)
const timeSlots = [
    "12:00 PM", "12:30 PM", "01:00 PM", "01:30 PM",
    "02:00 PM", "02:30 PM", "03:00 PM", "03:30 PM",
    "04:00 PM", "04:30 PM"
];

function renderTimeSlots() {
    const container = document.getElementById('time-slots');
    if (!container) return;
    let html = '';
    timeSlots.forEach(slot => {
        html += `<div class="time-slot">${slot}</div>`;
    });
    container.innerHTML = html;
    // Add selection logic
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.addEventListener('click', function() {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
}

// Render time slots and calendar on page load
document.addEventListener('DOMContentLoaded', function() {
    renderTimeSlots();
    // Render the calendar with the current year and month
    const today = new Date();
    if (typeof renderCalendar === 'function') {
        renderCalendar(today.getFullYear(), today.getMonth());
    }
});
</script>
<?php require_once 'provider_footer.php'; ?>