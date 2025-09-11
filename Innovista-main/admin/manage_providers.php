<?php
// manage_providers.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all providers (regardless of status for management)
// Joining with the 'service' table to display their main service if available
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.provider_status, u.credentials_verified,
           s.main_service, s.subcategories
    FROM users u
    LEFT JOIN service s ON u.id = s.provider_id
    WHERE u.role = 'provider'
    ORDER BY u.created_at DESC
");
$stmt->execute();
$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Provider Approvals</h2>
<p>Review new service providers and manage their verification status.</p>

<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_class = ($_GET['status'] === 'success') ? 'success' : 'error';
    echo "<div class='alert alert-{$status_class}'>" . htmlspecialchars($_GET['message']) . "</div>";
}
?>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name / Company</th>
                    <th>Email</th>
                    <th>Main Service</th>
                    <th>Approval Status</th>
                    <th>Credentials</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($providers)): ?>
                    <?php foreach($providers as $provider): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($provider['name']); ?></td>
                            <td><?php echo htmlspecialchars($provider['email']); ?></td>
                            <td><?php echo htmlspecialchars($provider['main_service'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($provider['provider_status']); ?>">
                                    <?php echo htmlspecialchars($provider['provider_status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($provider['credentials_verified'] === 'yes'): ?>
                                    <span class="status-badge status-verified">Verified</span>
                                <?php else: ?>
                                    Not Submitted
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="view_provider.php?id=<?php echo $provider['id']; ?>" class="btn-link" title="View Details"><i class="fas fa-eye"></i></a>
                                <?php if (strtolower($provider['provider_status']) === 'pending'): ?>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=approve" class="btn-link" onclick="return confirm('Are you sure you want to APPROVE <?php echo htmlspecialchars($provider['name']); ?>?');">Approve</a>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=reject" class="btn-link delete" onclick="return confirm('Are you sure you want to REJECT <?php echo htmlspecialchars($provider['name']); ?>?');">Reject</a>
                                <?php elseif (strtolower($provider['provider_status']) === 'approved'): ?>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=deactivate" class="btn-link delete" onclick="return confirm('Are you sure you want to DEACTIVATE <?php echo htmlspecialchars($provider['name']); ?>? This will also set their user status to inactive.');">Deactivate</a>
                                <?php elseif (strtolower($provider['provider_status']) === 'rejected'): ?>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=activate" class="btn-link" onclick="return confirm('Are you sure you want to ACTIVATE <?php echo htmlspecialchars($provider['name']); ?>? This will set their approval status to pending.');">Re-evaluate</a>
                                <?php endif; ?>
                                <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=delete" class="btn-link delete" onclick="return confirm('Are you sure you want to DELETE <?php echo htmlspecialchars($provider['name']); ?>? This action cannot be undone.');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No providers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>