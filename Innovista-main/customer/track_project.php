<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\customer\track_project.php

require_once '../public/session.php';
require_once '../handlers/flash_message.php';

// --- User-specific authentication function ---
if (!function_exists('protectPage')) {
    function protectPage(string $requiredRole): void {
        if (!isUserLoggedIn()) {
            header("Location: ../public/login.php");
            exit();
        }
        if (getUserRole() !== $requiredRole && getUserRole() !== 'admin') { 
            set_flash_message('error', 'Access denied. You do not have permission to view this page.');
            header("Location: ../public/index.php");
            exit();
        }
    }
}
protectPage('customer');

$pageTitle = 'Track Project';
require_once '../includes/user_dashboard_header.php';
require_once '../config/Database.php';

$db = (new Database())->getConnection();
$loggedInUserId = getUserId();

$custom_quotation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$custom_quotation_id) {
    set_flash_message('error', 'Invalid Project ID provided.');
    header('Location: my_projects.php');
    exit();
}

$project_data = null;
$project_updates = []; // To store communication and progress updates

// Fetch project details, joining with custom_quotations and users
try {
    $stmt_project = $db->prepare("
        SELECT 
            p.id AS project_id, p.status AS project_status, p.start_date, p.end_date,
            cq.id AS custom_quotation_id, cq.project_description, cq.amount AS quoted_amount, cq.advance,
            prov.id AS provider_id, prov.name AS provider_name, prov.email AS provider_email,
            cust.id AS customer_id, cust.name AS customer_name
        FROM projects p
        JOIN custom_quotations cq ON p.quotation_id = cq.id
        JOIN users prov ON cq.provider_id = prov.id
        JOIN users cust ON cq.customer_id = cust.id
        WHERE p.quotation_id = :custom_quotation_id AND cq.customer_id = :customer_id
    ");
    $stmt_project->bindParam(':custom_quotation_id', $custom_quotation_id, PDO::PARAM_INT);
    $stmt_project->bindParam(':customer_id', $loggedInUserId, PDO::PARAM_INT);
    $stmt_project->execute();
    $project_data = $stmt_project->fetch(PDO::FETCH_ASSOC);

    if (!$project_data) {
        set_flash_message('error', 'Project not found or you do not have permission to view it.');
        header('Location: my_projects.php');
        exit();
    }

    // Fetch project updates (communication)
    // Order by created_at ascending for a timeline view
    $stmt_updates = $db->prepare("
        SELECT pu.id, pu.update_text, pu.image_path, pu.created_at, 
               u.name AS poster_name, u.role AS poster_role
        FROM project_updates pu
        JOIN users u ON pu.user_id = u.id
        WHERE pu.project_id = :project_id
        ORDER BY pu.created_at ASC
    ");
    $stmt_updates->bindParam(':project_id', $project_data['project_id'], PDO::PARAM_INT);
    $stmt_updates->execute();
    $project_updates = $stmt_updates->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Track Project page error: " . $e->getMessage());
    set_flash_message('error', 'Error loading project details. Please try again.');
    header('Location: my_projects.php');
    exit();
}
?>

<?php display_flash_message(); ?>

<h2>Track Project: <?php echo htmlspecialchars($project_data['project_description']); ?></h2>
<p>View the real-time progress of your project and communicate with your provider.</p>

<div class="project-details-grid">
    <div class="content-card">
        <h3>Project Timeline & Updates</h3>
        <ul class="progress-timeline">
            <?php if (!empty($project_updates)): ?>
                <?php foreach($project_updates as $update): ?>
                    <li class="timeline-item <?php echo (!empty($update['image_path']) || $update['poster_role'] === 'provider') ? 'completed' : ''; ?>">
                        <h4><?php echo htmlspecialchars($update['poster_name']); ?> (<?php echo htmlspecialchars(ucfirst($update['poster_role'])); ?>)</h4>
                        <p><?php echo nl2br(htmlspecialchars($update['update_text'])); ?></p>
                        <?php if (!empty($update['image_path'])): ?>
                            <div class="timeline-image mt-2">
                                <img src="<?php echo getImageSrc($update['image_path']); ?>" alt="Project Update Image" style="max-width: 200px; height: auto; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                        <span class="timeline-date"><?php echo date('d M Y, H:i', strtotime($update['created_at'])); ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="timeline-item">
                    <h4>No updates yet.</h4>
                    <p>Your project timeline will appear here as the work progresses.</p>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="content-card">
        <h3>Project Details</h3>
        <div class="details-list">
            <p><strong>Provider:</strong> <a href="../public/provider_profile.php?id=<?php echo htmlspecialchars($project_data['provider_id']); ?>"><?php echo htmlspecialchars($project_data['provider_name']); ?></a></p>
            <p><strong>Project:</strong> <?php echo htmlspecialchars($project_data['project_description']); ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-<?php 
                $status_lower = strtolower($project_data['project_status']);
                if ($status_lower === 'in_progress') echo 'pending';
                elseif ($status_lower === 'awaiting_final_payment') echo 'yellow'; 
                elseif ($status_lower === 'completed') echo 'approved';
                elseif ($status_lower === 'disputed') echo 'rejected';
                else echo 'info';
            ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $project_data['project_status']))); ?></span></p>
            <p><strong>Quoted Cost:</strong> Rs <?php echo number_format($project_data['quoted_amount'], 2); ?></p>
            <p><strong>Advance Paid:</strong> Rs <?php echo number_format($project_data['advance'], 2); ?></p>
            <p><strong>Proposed Start:</strong> <?php echo date('d M Y', strtotime($project_data['start_date'])); ?></p>
            <p><strong>Proposed End:</strong> <?php echo date('d M Y', strtotime($project_data['end_date'])); ?></p>
        </div>

        <h3 style="margin-top: 2rem;">Communicate with Provider</h3>
        <form action="../handlers/handle_project_communication.php" method="POST" class="form-section">
            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_data['project_id']); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($loggedInUserId); ?>">
            <div class="form-group">
                <textarea name="message" placeholder="Send a message or query to your provider..." rows="4" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Send Message</button>
        </form>
        
        <?php if ($project_data['project_status'] === 'awaiting_final_payment'): ?>
            <div class="action-buttons mt-4">
                <a href="payment_details.php?project_id=<?php echo htmlspecialchars($project_data['project_id']); ?>" class="btn-submit" style="background-color: var(--status-active);">Make Final Payment</a>
            </div>
        <?php endif; ?>
        <?php if ($project_data['project_status'] === 'completed'): ?>
            <div class="action-buttons mt-4">
                <a href="leave_review.php?provider_id=<?php echo htmlspecialchars($project_data['provider_id']); ?>&project_id=<?php echo htmlspecialchars($project_data['project_id']); ?>" class="btn-submit" style="background-color: var(--status-verified);">Leave Review</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>

<!-- Add custom styling for timeline to public/assets/css/main.css or dashboard.css -->
<style>
/* For track_project.php timeline */
.project-details-grid {
    display: grid;
    grid-template-columns: 2fr 1fr; /* Timeline wider, details narrower */
    gap: 2rem;
}
@media (max-width: 992px) {
    .project-details-grid {
        grid-template-columns: 1fr; /* Stack vertically on smaller screens */
    }
}
.progress-timeline {
    list-style: none;
    padding-left: 0;
    margin: 0;
    position: relative;
}
.progress-timeline:before {
    content: '';
    position: absolute;
    left: 10px; /* Adjust to align with dots */
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: var(--border-color, #e2e8f0);
}
.timeline-item {
    position: relative;
    padding: 1rem 1rem 1rem 3.5rem; /* Left padding for dot and text */
    margin-bottom: 1.5rem;
    background-color: var(--card-bg, #fff);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color, #e2e8f0);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.timeline-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-item:before {
    content: '';
    position: absolute;
    left: 0; /* Position relative to the timeline-item */
    top: 1.5rem; /* Vertical position of the dot */
    transform: translateX(-50%); /* Center dot on the line */
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background-color: var(--border-color, #e2e8f0);
    border: 4px solid var(--admin-bg, #f0f2f5); /* White ring around dot, matching page background */
    z-index: 1;
}
.timeline-item.completed:before {
    background-color: var(--primary-color, #0d9488); /* Green dot for completed */
    box-shadow: 0 0 0 2px rgba(var(--primary-color-rgb, 13, 148, 136), 0.5); /* Subtle glow */
}
.timeline-item h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.2rem;
    color: var(--text-dark);
}
.timeline-item p {
    font-size: 0.95rem;
    color: var(--text-light);
    margin-bottom: 0.5rem;
}
.timeline-date {
    display: block;
    font-size: 0.8rem;
    color: #999;
    text-align: right;
    margin-top: 0.8rem;
    font-style: italic;
}
.timeline-image img {
    max-width: 100%;
    height: auto;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}
.details-list p {
    margin-bottom: 0.5rem;
    font-size: 1rem;
    color: var(--text-dark);
}
.details-list strong {
    color: var(--text-dark);
}
.details-list a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}
.details-list a:hover {
    text-decoration: underline;
}
/* Form styling for communication section, using existing btn-submit */
.form-section textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    resize: vertical;
    min-height: 80px;
}
.form-section .btn-submit {
    margin-top: 1rem;
    width: auto;
    padding: 0.8rem 1.5rem;
}
</style>