<?php
require_once '../config/Database.php';
require_once '../config/session.php';

class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getNotifications($userId) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :userId 
                 ORDER BY created_at DESC 
                 LIMIT 20";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE user_id = :userId AND `read` = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function markAsRead($notificationId, $userId) {
        $query = "UPDATE " . $this->table . " 
                 SET `read` = 1 
                 WHERE id = :notificationId AND user_id = :userId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':notificationId', $notificationId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function markAllAsRead($userId) {
        $query = "UPDATE " . $this->table . " 
                 SET `read` = 1 
                 WHERE user_id = :userId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function createNotification($userId, $title, $message, $type = 'general') {
        $query = "INSERT INTO " . $this->table . " 
                 (user_id, title, message, type) 
                 VALUES (:userId, :title, :message, :type)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $database = new Database();
    $db = $database->getConnection();
    $notification = new Notification($db);
    
    $userId = $_SESSION['user_id'];
    
    $notifications = $notification->getNotifications($userId);
    $unreadCount = $notification->getUnreadCount($userId);
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unreadCount' => $unreadCount
    ]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $database = new Database();
    $db = $database->getConnection();
    $notification = new Notification($db);
    
    $userId = $_SESSION['user_id'];
    
    if ($data['action'] === 'mark_read') {
        $success = $notification->markAsRead($data['notification_id'], $userId);
    } elseif ($data['action'] === 'mark_all_read') {
        $success = $notification->markAllAsRead($userId);
    }
    
    echo json_encode(['success' => $success]);
}
?>
