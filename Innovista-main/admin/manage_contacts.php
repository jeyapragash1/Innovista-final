<?php
// manage_contacts.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all contact messages
$stmt = $conn->prepare("SELECT id, name, email, subject, message, created_at FROM contacts ORDER BY created_at DESC");
$stmt->execute();
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// You might want to add an action to mark as read or delete
if (isset($_GET['action']) && isset($_GET['id'])) {
    $contact_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($contact_id && $action) {
        if ($action === 'mark_read' && property_exists($conn, 'is_read')) { // Only if is_read column exists
            $update_stmt = $conn->prepare("UPDATE contacts SET is_read = 1 WHERE id = :id");
            $update_stmt->bindParam(':id', $contact_id, PDO::PARAM_INT);
            $update_stmt->execute();
            // Redirect to prevent re-submission
            header("Location: manage_contacts.php?status=success&message=Message marked as read.");
            exit();
        } elseif ($action === 'delete') {
            $delete_stmt = $conn->prepare("DELETE FROM contacts WHERE id = :id");
            $delete_stmt->bindParam(':id', $contact_id, PDO::PARAM_INT);
            $delete_stmt->execute();
            // Redirect
            header("Location: manage_contacts.php?status=success&message=Message deleted.");
            exit();
        }
    }
}
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
                            <td class="action-buttons">
                                <a href="view_contact_message.php?id=<?php echo $contact['id']; ?>" class="btn-link" title="View Message"><i class="fas fa-eye"></i></a>
                                <!-- Add mark as read action if 'is_read' column is present -->
                                <?php // if (property_exists($conn, 'is_read') && $contact['is_read'] == 0): ?>
                                    <!-- <a href="manage_contacts.php?id=<?php echo $contact['id']; ?>&action=mark_read" class="btn-link" title="Mark as Read"><i class="fas fa-check"></i></a> -->
                                <?php // endif; ?>
                                <a href="manage_contacts.php?id=<?php echo $contact['id']; ?>&action=delete" class="btn-link delete" title="Delete" onclick="return confirm('Are you sure you want to delete this message?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No contact messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>