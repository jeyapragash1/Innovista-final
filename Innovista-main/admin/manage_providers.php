<?php
// manage_providers.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc

$db = new Database();
$conn = $db->getConnection();

// Fetch all providers (regardless of status for management)
// Joining with the 'service' table to display their main service if available
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.provider_status, u.credentials_verified, u.profile_image_path,
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
                    <th>Image</th>
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
                            <td>
                                <img src="<?php echo getImageSrc($provider['profile_image_path'] ?? 'assets/images/default-avatar.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($provider['name']); ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                            </td>
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
                                    <span class="status-badge status-pending">Not Submitted</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="view_provider.php?id=<?php echo $provider['id']; ?>" class="btn-link" title="View Details"><i class="fas fa-eye"></i></a>
                                <?php if (strtolower($provider['provider_status']) === 'pending'): ?>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=approve" class="btn-action edit" title="Approve" onclick="return confirm('Are you sure you want to APPROVE <?php echo htmlspecialchars($provider['name']); ?>?');"><i class="fas fa-check"></i></a>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=reject" class="btn-action delete" title="Reject" onclick="return confirm('Are you sure you want to REJECT <?php echo htmlspecialchars($provider['name']); ?>?');"><i class="fas fa-times"></i></a>
                                <?php elseif (strtolower($provider['provider_status']) === 'approved'): ?>
                                    <a href="edit_provider.php?id=<?php echo $provider['id']; ?>" class="btn-action edit" title="Edit Profile"><i class="fas fa-edit"></i></a>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=deactivate" class="btn-action delete" title="Deactivate" onclick="return confirm('Are you sure you want to DEACTIVATE <?php echo htmlspecialchars($provider['name']); ?>? This will also set their user status to inactive.');"><i class="fas fa-power-off"></i></a>
                                <?php elseif (strtolower($provider['provider_status']) === 'rejected'): ?>
                                    <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=re_evaluate" class="btn-action edit" title="Re-evaluate" onclick="return confirm('Are you sure you want to re-evaluate <?php echo htmlspecialchars($provider['name']); ?>? This will set their approval status to pending.');"><i class="fas fa-redo"></i></a>
                                <?php endif; ?>
                                <a href="provider_action.php?id=<?php echo $provider['id']; ?>&action=delete" class="btn-action delete" title="Delete" onclick="return confirm('Are you sure you want to DELETE <?php echo htmlspecialchars($provider['name']); ?>? This action cannot be undone and will delete all associated data.');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">No providers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>