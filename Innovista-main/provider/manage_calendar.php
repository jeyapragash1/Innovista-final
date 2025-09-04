<?php 
$pageTitle = 'Manage Calendar';
require_once 'provider_header.php'; 
?>
<div class="calendar-dashboard-layout">
   
    <main class="calendar-main-content">
        <div class="calendar-header-bar">
            <div class="calendar-date-title" id="calendar-date-title"></div>
            <div class="calendar-header-icons">
                <i class="fas fa-bell"></i>
                <i class="fas fa-ellipsis-h"></i>
            </div>
        </div>
        <div class="calendar-controls">
            <button id="prev-month" class="calendar-arrow">&#8592;</button>
            <span id="calendar-title" class="calendar-title"></span>
            <select id="calendar-year" style="margin: 0 8px; font-size: 1rem;"></select>
            <button id="next-month" class="calendar-arrow">&#8594;</button>
        </div>
        <div class="calendar-grid-wrapper">
            <div class="calendar-grid">
                <div class="calendar-grid-header">
                    <span>SUN</span><span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span><span>SAT</span>
                </div>
                <div id="calendar-days" class="calendar-days"></div>
            </div>
            <div class="calendar-tasks-info" id="selected-range"></div>
            <div class="calendar-action-icons">
                <button class="calendar-action-btn" title="Pin"><i class="fas fa-thumbtack"></i></button>
                <button class="calendar-action-btn" title="Edit"><i class="fas fa-pen"></i></button>
            </div>
        </div>
    </main>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<style>
.calendar-dashboard-layout { display: flex; min-height: 100vh; background: linear-gradient(90deg,#e0e7ff 60%,#fdf2f8 100%); }
.calendar-sidebar { width: 260px; background: #f3f4fa; color: #222; display: flex; flex-direction: column; align-items: flex-start; padding: 32px 0 0 0; box-shadow: 2px 0 12px #e0e7ff; }
.sidebar-profile { display: flex; flex-direction: column; align-items: center; margin-bottom: 24px; }
.profile-name { font-weight: 600; margin-top: 8px; }
.sidebar-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 18px; margin-left: 32px; }
.calendar-nav-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; margin-left: 32px; }
.calendar-nav-list a { color: #222; text-decoration: none; font-weight: 500; padding: 6px 0; border-radius: 6px; }
.calendar-nav-list a.active { background: #e0e7ff; color: #4f46e5; font-weight: 700; }
.sidebar-months { margin-left: 32px; margin-bottom: 24px; }
.sidebar-months div { padding: 4px 0; color: #888; }
.sidebar-months .active-month { color: #4f46e5; font-weight: 700; }
.calendar-main-content { flex: 1; padding: 48px 64px; }
.calendar-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.calendar-date-title { font-size: 1.4rem; font-weight: 700; color: #222; }
.calendar-header-icons i { font-size: 1.2rem; color: #888; margin-left: 18px; cursor: pointer; }
.calendar-controls { display: flex; align-items: center; gap: 18px; margin-bottom: 18px; }
.calendar-title { font-size: 1.2rem; font-weight: 600; color: #4f46e5; }
.calendar-arrow { background: #e0e7ff; border: none; border-radius: 8px; padding: 6px 16px; font-size: 1.1rem; color: #4f46e5; font-weight: 700; cursor: pointer; }
.calendar-grid-wrapper { background: #fff; border-radius: 24px; box-shadow: 0 2px 16px #e0e7ff; padding: 32px 40px; display: flex; flex-direction: column; align-items: center; }
.calendar-grid { width: 100%; }
.calendar-grid-header { display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px; margin-bottom: 12px; font-weight: 600; color: #4f46e5; }
.calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px; min-height: 220px; }
.calendar-day { background: #f3f4fa; border-radius: 12px; padding: 16px 0; text-align: center; font-size: 1.1rem; color: #222; position: relative; cursor: pointer; transition: background 0.2s; }
.calendar-day.selected { background: #fde68a; color: #a16207; font-weight: 700; }
.calendar-day.today { border: 2px solid #4f46e5; }
.calendar-day.task { background: #e0f2fe; color: #2563eb; }
.calendar-day.completed { background: #f3e8ff; color: #a21caf; }
.calendar-tasks-info { margin-top: 18px; color: #888; font-size: 1rem; text-align: left; }
.calendar-action-icons { margin-top: 24px; display: flex; gap: 18px; }
.calendar-action-btn { background: #fff; border: none; border-radius: 50%; box-shadow: 0 2px 8px #e0e7ff; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #4f46e5; cursor: pointer; transition: background 0.2s; }
.calendar-action-btn:hover { background: #e0e7ff; }
</style>
<script src="../public/assets/js/calendar-provider.js"></script>
<?php require_once 'provider_footer.php'; ?>