<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\admin\edit_portfolio_item.php
require_once 'admin_header.php';
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc

$db = new Database();
$conn = $db->getConnection();

$item_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$item_data = null;

if (!$item_id || !is_numeric($item_id)) {
    header("Location: manage_portfolio_items.php?status=error&message=Invalid portfolio item ID.");
    exit();
}

// Fetch item data
$stmt = $conn->prepare("SELECT id, provider_id, title, description, image_path FROM portfolio_items WHERE id = :id");
$stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
$stmt->execute();
$item_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item_data) {
    header("Location: manage_portfolio_items.php?status=error&message=Portfolio item not found.");
    exit();
}

$message = '';
$status_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider_id = filter_input(INPUT_POST, 'provider_id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Image Upload Handling
    $new_image_path = $item_data['image_path']; // Default to current image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../public/uploads/portfolio/';
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'portfolio_' . $item_id . '_' . uniqid() . '.' . $file_ext;
            $destination_path = $upload_dir . $new_file_name;
            $public_image_path_for_db = 'uploads/portfolio/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination_path)) {
                // Delete old image if it was a locally uploaded file (not a URL)
                if ($item_data['image_path'] && 
                    !filter_var($item_data['image_path'], FILTER_VALIDATE_URL) &&
                    file_exists('../public/' . $item_data['image_path'])) {
                    unlink('../public/' . $item_data['image_path']);
                }
                $new_image_path = $public_image_path_for_db;
            } else {
                $message = "Failed to move uploaded image.";
                $status_type = "error";
            }
        } else {
            $message = "Invalid image file type. Only JPG, JPEG, PNG, GIF allowed.";
            $status_type = "error";
        }
    }

    if (empty($message)) { // Only proceed if no image upload error
        try {
            $stmt_update = $conn->prepare("
                UPDATE portfolio_items SET 
                    provider_id = :provider_id, 
                    title = :title, 
                    description = :description, 
                    image_path = :image_path
                WHERE id = :id
            ");
            $stmt_update->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':title', $title);
            $stmt_update->bindParam(':description', $description);
            $stmt_update->bindParam(':image_path', $new_image_path);
            $stmt_update->bindParam(':id', $item_id, PDO::PARAM_INT);
            $stmt_update->execute();

            if ($stmt_update->rowCount() > 0) {
                $message = "Portfolio item updated successfully.";
                $status_type = "success";
            } else {
                $message = "No changes made or failed to update item.";
                $status_type = "info";
            }
            // Re-fetch data to show updated info immediately
            $stmt = $conn->prepare("SELECT id, provider_id, title, description, image_path FROM portfolio_items WHERE id = :id");
            $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $item_data = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $status_type = "error";
            error_log("Edit Portfolio Item Error: " . $e->getMessage());
        }
    }
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

<h2>Edit Portfolio Item: <?php echo htmlspecialchars($item_data['title']); ?></h2>

<?php
if (isset($message) && $message !== '') {
    echo "<div class='alert alert-{$status_type}'>" . htmlspecialchars($message) . "</div>";
}
?>

<div class="content-card">
    <form action="edit_portfolio_item.php?id=<?php echo htmlspecialchars($item_data['id']); ?>" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label for="provider_id">Provider</label>
                <select id="provider_id" name="provider_id" required>
                    <option value="">Select Provider</option>
                    <?php foreach ($providers as $provider): ?>
                        <option value="<?php echo htmlspecialchars($provider['id']); ?>" <?php echo ($item_data['provider_id'] == $provider['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($provider['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item_data['title']); ?>" required>
            </div>
        </div>
        <div class="form-group mt-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($item_data['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group mt-3">
            <label for="image">Image File</label>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Leave blank to keep current image. Max file size: 2MB. Allowed formats: JPG, PNG, GIF.</small>
            <?php if (!empty($item_data['image_path'])): ?>
                <p>Current Image:</p>
                <img src="<?php echo getImageSrc($item_data['image_path']); ?>" alt="Portfolio Image" style="max-width: 150px; height: auto; margin-top: 10px; border-radius: 8px;">
            <?php endif; ?>
        </div>

        <div class="action-buttons mt-4">
            <button type="submit" class="btn-submit">Save Changes</button>
            <a href="manage_portfolio_items.php" class="btn-link">Back to Portfolio Items</a>
        </div>
    </form>
</div>

<?php require_once 'admin_footer.php'; ?>