<?php 
$pageTitle = 'Manage Calendar';
require_once 'provider_header.php'; 
?>

<div class="booking-layout">
    <div class="booking-card">
        <div class="booking-header">Confirm Date & Time</div>
        <div class="booking-content">
            <!-- Calendar Panel -->
            <div class="calendar-panel">
                <div class="calendar-header">
                    <button id="prev-month" class="calendar-arrow">&#8592;</button>
                    <span class="calendar-title" id="calendar-title"></span>
                    <button id="next-month" class="calendar-arrow">&#8594;</button>
                </div>
                <div class="calendar-card">
                    <div class="calendar-grid-header">
                        <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span>
                        <span>Fri</span><span>Sat</span><span>Sun</span>
                    </div>
                    <div id="calendar-days" class="calendar-days"></div>
                </div>
            </div>

            <!-- Time Panel -->
            <div class="time-panel">
                <div class="time-header">Select Time</div>
                <div class="selected-time-display" id="selected-time-display">Selected Time: None</div>
                <div class="time-slots" id="time-slots"></div>
                <button class="save-btn" id="save-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<style>
.booking-layout {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: #e9eef6;
    font-weight: 600;
    color: #8a97ad;
}
.booking-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 16px #e0e7ff;
    width: 900px;
    max-width: 95%;
}
.booking-header {
    background: #1eb6e9;
    color: #fff;
    font-size: 1.2rem;
    font-weight: 700;
    padding: 18px 32px;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
}
.booking-content {
    display: flex;
    gap: 48px;
    padding: 32px;
}
.calendar-panel {
    flex: 1 1 560px;
}
.calendar-header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 18px;
    margin-bottom: 18px;
}
.calendar-title {
    font-size: 1.1rem;
    font-weight: 700;
    min-width: 140px;
    text-align: center;
    color: #222;
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
    padding: 24px 16px 32px 16px;
    min-height: 420px;
}
.calendar-grid-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0;
    margin-bottom: 8px;
    font-weight: 600;
    color: #1eb6e9;
    text-align: center;
}
.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-template-rows: repeat(6, 60px);
    gap: 8px;
    min-height: 360px;
    text-align: center;
}
.calendar-day {
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    border-radius: 50%;
    transition: 0.2s;
}
.calendar-day.today { border: 1px solid #1eb6e9; }
.calendar-day.selected { background: #1eb6e9; color: #fff; }
.calendar-day:hover { background: #e0e7ff; color: #1eb6e9; }

.time-panel {
    flex: 0 0 220px;
    background: #fafbfc;
    border-radius: 12px;
    padding: 18px;
    min-height: 400px;
    display: flex;
    flex-direction: column;
}
.time-header {
    font-size: 1.05rem;
    font-weight: 600;
    margin-bottom: 12px;
}
.selected-time-display {
    font-weight: 600;
    margin-bottom: 12px;
    color: #1eb6e9;
}
.time-slots {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 18px;
    max-height: 300px; /* scrollable */
    overflow-y: auto;
}
.time-slot {
    background: #fff;
    border: 1px solid #e0e7ff;
    border-radius: 8px;
    text-align: center;
    padding: 10px 0;
    cursor: pointer;
    color: #1eb6e9;
    transition: 0.2s;
}
.time-slot.selected {
    background: #1eb6e9;
    color: #fff;
    font-weight: 700;
}
.time-slot:hover { background: #e0e7ff; }
.save-btn {
    background: #1eb6e9;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 32px;
    font-weight: 600;
    cursor: pointer;
    margin-top: auto;
}
.save-btn:hover { background: #159ac2; }
</style>

<script>
const timeSlots = [
    "09:00 AM","09:30 AM","10:00 AM","10:30 AM",
    "11:00 AM","11:30 AM","12:00 PM","12:30 PM",
    "01:00 PM","01:30 PM","02:00 PM","02:30 PM",
    "03:00 PM","03:30 PM","04:00 PM","04:30 PM",
    "05:00 PM","05:30 PM","06:00 PM"
];

function renderTimeSlots() {
    const container = document.getElementById('time-slots');
    const selectedDisplay = document.getElementById('selected-time-display');
    container.innerHTML = '';
    timeSlots.forEach(slot => {
        const div = document.createElement('div');
        div.classList.add('time-slot');
        div.textContent = slot;
        div.addEventListener('click', () => {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            div.classList.add('selected');
            selectedDisplay.textContent = `Selected Time: ${slot}`;
        });
        container.appendChild(div);
    });
}

function renderCalendar(year, month) {
    const calendarDays = document.getElementById('calendar-days');
    const calendarTitle = document.getElementById('calendar-title');
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = (firstDay.getDay() + 6) % 7;
    const daysInMonth = lastDay.getDate();

    calendarTitle.textContent = firstDay.toLocaleString('default', { month: 'long', year: 'numeric' });
    calendarDays.innerHTML = '';

    for(let i=0;i<startDay;i++){ calendarDays.innerHTML += '<div></div>'; }
    for(let i=1;i<=daysInMonth;i++){
        const dayDiv = document.createElement('div');
        dayDiv.classList.add('calendar-day');
        dayDiv.textContent = i;
        const today = new Date();
        if(i===today.getDate() && month===today.getMonth() && year===today.getFullYear()){
            dayDiv.classList.add('today');
        }
        dayDiv.addEventListener('click', () => {
            document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
            dayDiv.classList.add('selected');
        });
        calendarDays.appendChild(dayDiv);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    renderTimeSlots();
    const today = new Date();
    renderCalendar(today.getFullYear(), today.getMonth());

    document.getElementById('prev-month').addEventListener('click', () => {
        today.setMonth(today.getMonth() - 1);
        renderCalendar(today.getFullYear(), today.getMonth());
    });
    document.getElementById('next-month').addEventListener('click', () => {
        today.setMonth(today.getMonth() + 1);
        renderCalendar(today.getFullYear(), today.getMonth());
    });
});
</script>

<?php require_once 'provider_footer.php'; ?>
