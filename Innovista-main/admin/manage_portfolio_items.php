<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\admin\manage_portfolio_items.php
require_once 'admin_header.php';
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc

$db = new Database();
$conn = $db->getConnection();

$message = '';
$status_type = '';

// Handle Add/Edit/Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_SANITIZE_NUMBER_INT);
    $provider_id = filter_input(INPUT_POST, 'provider_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Image Upload Handling
    $image_path = null;
    $has_file_upload = (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK);

    if ($has_file_upload) {
        $upload_dir = '../public/uploads/portfolio/'; // Publicly accessible path
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'portfolio_' . uniqid() . '.' . $file_ext;
            $destination_path = $upload_dir . $new_file_name;
            $public_image_path_for_db = 'uploads/portfolio/' . $new_file_name; // Path stored in DB

            if (move_uploaded_file($file_tmp, $destination_path)) {
                $image_path = $public_image_path_for_db;
            } else {
                $message = "Failed to move uploaded image.";
                $status_type = "error";
            }
        } else {
            $message = "Invalid image file type. Only JPG, JPEG, PNG, GIF allowed.";
            $status_type = "error";
        }
    }


    if ($action === 'add' && empty($message)) {
        if (!$image_path) { // Image is required for add
            $message = "Image is required for new portfolio item.";
            $status_type = "error";
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO portfolio_items (provider_id, title, description, image_path) VALUES (:provider_id, :title, :description, :image_path)");
                $stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':image_path', $image_path);
                $stmt->execute();
                $message = "Portfolio item added successfully.";
                $status_type = "success";
            } catch (PDOException $e) {
                $message = "Error adding item: " . $e->getMessage();
                $status_type = "error";
            }
        }
    } elseif ($action === 'edit' && $item_id && empty($message)) {
        try {
            $update_image_clause = $image_path ? ", image_path = :image_path" : "";
            $stmt = $conn->prepare("UPDATE portfolio_items SET provider_id = :provider_id, title = :title, description = :description {$update_image_clause} WHERE id = :id");
            $stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            if ($image_path) {
                $stmt->bindParam(':image_path', $image_path);
                // Delete old image file if it was locally uploaded and not a URL
                $old_image_stmt = $conn->prepare("SELECT image_path FROM portfolio_items WHERE id = :id");
                $old_image_stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
                $old_image_stmt->execute();
                $old_image_path_from_db = $old_image_stmt->fetchColumn();

                if ($old_image_path_from_db && 
                    !filter_var($old_image_path_from_db, FILTER_VALIDATE_URL) &&
                    file_exists('../public/' . $old_image_path_from_db)) {
                    unlink('../public/' . $old_image_path_from_db);
                }
            }
            $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $message = "Portfolio item updated successfully.";
            $status_type = "success";
        } catch (PDOException $e) {
            $message = "Error updating item: " . $e->getMessage();
            $status_type = "error";
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $item_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($item_id) {
        try {
            // Get image path before deleting record
            $old_image_stmt = $conn->prepare("SELECT image_path FROM portfolio_items WHERE id = :id");
            $old_image_stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
            $old_image_stmt->execute();
            $old_image_path = $old_image_stmt->fetchColumn();

            $stmt = $conn->prepare("DELETE FROM portfolio_items WHERE id = :id");
            $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // Delete image file if it was locally uploaded and not a URL
                if ($old_image_path && !filter_var($old_image_path, FILTER_VALIDATE_URL) && file_exists('../public/' . $old_image_path)) {
                    unlink('../public/' . $old_image_path);
                }
                $message = "Portfolio item deleted successfully.";
                $status_type = "success";
            } else {
                $message = "Item not found or failed to delete.";
                $status_type = "error";
            }
        } catch (PDOException $e) {
            $message = "Error deleting item: " . $e->getMessage();
            $status_type = "error";
        }
    }
}

// Fetch all portfolio items for display
$portfolio_items = [];
try {
    $stmt = $conn->prepare("SELECT pi.id, pi.provider_id, pi.title, pi.description, pi.image_path, u.name as provider_name FROM portfolio_items pi JOIN users u ON pi.provider_id = u.id ORDER BY pi.created_at DESC");
    $stmt->execute();
    $portfolio_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching portfolio items: " . $e->getMessage());
}

// Fetch all providers for the dropdown
$providers = [];
try {
    $stmt_providers = $conn->prepare("SELECT id, name FROM users WHERE role = 'provider' AND provider_status = 'approved' ORDER BY name");
    $stmt_providers->execute();
    $providers = $stmt_providers->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching providers: " . $e->getMessage());
}

?>

<h2>Manage Portfolio Items</h2>
<p>Add, edit, or remove work displayed on the homepage and provider profiles.</p>

<?php
if (isset($message) && $message !== '') {
    echo "<div class='alert alert-{$status_type}'>" . htmlspecialchars($message) . "</div>";
}
?>

<div class="content-card">
    <h3>Add New Portfolio Item</h3>
    <form action="manage_portfolio_items.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="form-grid">
            <div class="form-group">
                <label for="provider_id">Provider</label>
                <select id="provider_id" name="provider_id" required>
                    <option value="">Select Provider</option>
                    <?php foreach ($providers as $provider): ?>
                        <option value="<?php echo htmlspecialchars($provider['id']); ?>"><?php echo htmlspecialchars($provider['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image File</label>
            <input type="file" id="image" name="image" accept="image/*" required>
            <small>Max file size: 2MB. Allowed formats: JPG, PNG, GIF.</small>
        </div>
        <button type="submit" class="btn-submit">Add Item</button>
    </form>
</div>

<div class="content-card mt-4">
    <h3>Existing Portfolio Items</h3>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Provider</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($portfolio_items)): ?>
                    <?php foreach ($portfolio_items as $item): ?>
                        <tr>
                            <td><img src="<?php echo getImageSrc($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['provider_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($item['description'], 0, 70)) . (strlen($item['description']) > 70 ? '...' : ''); ?></td>
                            <td class="action-buttons">
                                <a href="edit_portfolio_item.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="manage_portfolio_items.php?action=delete&id=<?php echo htmlspecialchars($item['id']); ?>" class="btn-action delete" title="Delete" onclick="return confirm('Are you sure you want to delete this portfolio item?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No portfolio items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>