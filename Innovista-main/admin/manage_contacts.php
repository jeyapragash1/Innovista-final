<?php
// manage_contacts.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc if you used it in activity feed

$db = new Database();
$conn = $db->getConnection();

// Handle Mark as Read/Delete actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $contact_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($contact_id && $action) {
        if ($action === 'mark_read') { // Assumes 'is_read' column exists
            $update_stmt = $conn->prepare("UPDATE contacts SET is_read = 1 WHERE id = :id");
            $update_stmt->bindParam(':id', $contact_id, PDO::PARAM_INT);
            $update_stmt->execute();
            header("Location: manage_contacts.php?status=success&message=Message marked as read.");
            exit();
        } elseif ($action === 'delete') {
            $delete_stmt = $conn->prepare("DELETE FROM contacts WHERE id = :id");
            $delete_stmt->bindParam(':id', $contact_id, PDO::PARAM_INT);
            $delete_stmt->execute();
            if ($delete_stmt->rowCount() > 0) {
                header("Location: manage_contacts.php?status=success&message=Message deleted.");
            } else {
                header("Location: manage_contacts.php?status=error&message=Failed to delete message or message not found.");
            }
            exit();
        }
    }
}

// Fetch all contact messages
$stmt = $conn->prepare("SELECT id, name, email, subject, message, created_at, is_read FROM contacts ORDER BY created_at DESC");
$stmt->execute();
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Contact Form Messages</h2>
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
                    <th>From</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)): ?>
                    <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contact['name']); ?></td>
                            <td><?php echo htmlspecialchars($contact['email']); ?></td>
                            <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                            <td><?php echo htmlspecialchars(substr($contact['message'], 0, 100)) . (strlen($contact['message']) > 100 ? '...' : ''); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($contact['created_at'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo ($contact['is_read'] == 1) ? 'status-info' : 'status-pending'; ?>">
                                    <?php echo ($contact['is_read'] == 1) ? 'Read' : 'New'; ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="view_contact_message.php?id=<?php echo $contact['id']; ?>" class="btn-link" title="View Message"><i class="fas fa-eye"></i></a>
                                <?php if ($contact['is_read'] == 0): ?>
                                    <a href="manage_contacts.php?id=<?php echo $contact['id']; ?>&action=mark_read" class="btn-action edit" title="Mark as Read" onclick="return confirm('Mark this message as read?');"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="manage_contacts.php?id=<?php echo $contact['id']; ?>&action=delete" class="btn-action delete" title="Delete" onclick="return confirm('Are you sure you want to delete this message?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">No contact messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>