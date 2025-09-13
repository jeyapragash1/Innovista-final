<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'get_service') {
        // Get a sample of services (first 5 providers)
        $stmt = $db->prepare('SELECT provider_id, provider_name, main_service, subcategories FROM service LIMIT 5');
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'services' => $services,
            'message' => 'Services retrieved successfully'
        ]);
        
    } elseif ($action === 'get_all_services') {
        // Get all services from the database with complete provider information
        $stmt = $db->prepare('SELECT provider_id, provider_name, main_service, subcategories, provider_email, provider_phone, provider_address, portfolio FROM service ORDER BY provider_name');
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'services' => $services,
            'message' => 'All services retrieved successfully'
        ]);
        
    } elseif ($action === 'get_services_by_category') {
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        
        if (empty($category)) {
            echo json_encode([
                'success' => false,
                'message' => 'Category parameter is required'
            ]);
            exit;
        }
        
        // Process category name the same way as serviceprovider.php
        $categoryNoSpace = strtolower(str_replace([' ', '-', '_'], '', $category));
        
        // Get services filtered by category using FIND_IN_SET like serviceprovider.php
        $stmt = $db->prepare('SELECT provider_id, provider_name, main_service, subcategories, provider_email, provider_phone, provider_address, portfolio FROM service WHERE FIND_IN_SET(:category, LOWER(REPLACE(main_service, " ", ""))) ORDER BY provider_name');
        $stmt->bindParam(':category', $categoryNoSpace);
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If no services found for specific category, try a broader search
        if (empty($services)) {
            // Try with LIKE as fallback
            $stmt = $db->prepare('SELECT provider_id, provider_name, main_service, subcategories, provider_email, provider_phone, provider_address, portfolio FROM service WHERE LOWER(main_service) LIKE :category ORDER BY provider_name');
            $categoryParam = '%' . strtolower($category) . '%';
            $stmt->bindParam(':category', $categoryParam);
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Special handling for Painting - also check subcategories
        if (empty($services) && strtolower($category) === 'painting') {
            $stmt = $db->prepare('SELECT provider_id, provider_name, main_service, subcategories, provider_email, provider_phone, provider_address, portfolio FROM service WHERE LOWER(subcategories) LIKE :painting ORDER BY provider_name');
            $paintingParam = '%painting%';
            $stmt->bindParam(':painting', $paintingParam);
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode([
            'success' => true,
            'services' => $services,
            'message' => $category . ' services retrieved successfully'
        ]);
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action specified'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
