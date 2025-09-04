<?php
require_once '../config/session.php';
protectPage('customer');

$pageTitle = 'Track Project';
require_once '../includes/user_dashboard_header.php';
require_once '../config/Database.php';

// Get quotation ID from URL, with basic security check
$quotation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$quotation_id) {
    echo "<h2>Invalid Project ID</h2>";
    require_once '../includes/user_dashboard_footer.php';
    exit();
}

// In a real app, you would query the database for this project's details
$project = [
    'provider_name' => 'Modern Living Designs',
    'project_description' => 'Living Room Renovation',
    'status' => 'In Progress',
    'price' => 7500.00,
    'timeline' => [
        ['title' => 'Project Started', 'date' => '10 Jul 2025', 'completed' => true],
        ['title' => 'Demolition Phase Complete', 'date' => '15 Jul 2025', 'completed' => true],
        ['title' => 'Electrical Work in Progress', 'date' => 'Current Stage', 'completed' => true],
        ['title' => 'Painting & Finishing', 'date' => 'Upcoming', 'completed' => false],
        ['title' => 'Final Handover', 'date' => 'Upcoming', 'completed' => false],
    ]
];
?>

<h2>Track Project</h2>
<p>View the real-time progress of your project and communicate with your provider.</p>

<div class="project-details-grid">
    <div class="content-card">
        <h3>Project Timeline</h3>
        <ul class="progress-timeline">
            <?php foreach($project['timeline'] as $item): ?>
                <li class="timeline-item <?php if($item['completed']) echo 'completed'; ?>">
                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                    <p><?php echo htmlspecialchars($item['date']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="content-card">
        <h3>Project Details</h3>
        <div class="details-list">
            <p><strong>Provider:</strong> <?php echo htmlspecialchars($project['provider_name']); ?></p>
            <p><strong>Project:</strong> <?php echo htmlspecialchars($project['project_description']); ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-pending"><?php echo htmlspecialchars($project['status']); ?></span></p>
            <p><strong>Total Cost:</strong> $<?php echo number_format($project['price'], 2); ?></p>
        </div>
        <h3 style="margin-top: 2rem;">Communicate</h3>
        <form action="#" method="POST" class="form-section">
            <div class="form-group">
                <textarea name="message" placeholder="Send a message to your provider..." rows="4"></textarea>
            </div>
            <button class="btn-submit">Send Message</button>
        </form>
    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>