<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\classes\Service.php

class Service {
    private $conn;
    private $table_name = "service";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new service entry for a provider.
     * @param int $provider_id
     * @param string $provider_name
     * @param string $provider_email
     * @param string $main_service_str Comma-separated string of main services.
     * @param string $subcategories_str Comma-separated string of subcategories.
     * @param string $provider_phone
     * @param string $provider_address
     * @param string $portfolio_text Provider's portfolio text/URL.
     * @param string $provider_bio Provider's biography.
     * @return bool True on success, false on failure.
     */
    public function create($provider_id, $provider_name, $provider_email, $main_service_str, $subcategories_str, $provider_phone, $provider_address, $portfolio_text, $provider_bio) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (provider_id, provider_name, provider_email, main_service, subcategories, provider_phone, provider_address, portfolio, provider_bio) 
                  VALUES 
                  (:provider_id, :provider_name, :provider_email, :main_service, :subcategories, :provider_phone, :provider_address, :portfolio_text, :provider_bio)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
        $stmt->bindParam(':provider_name', htmlspecialchars(strip_tags($provider_name)));
        $stmt->bindParam(':provider_email', htmlspecialchars(strip_tags($provider_email)));
        $stmt->bindParam(':main_service', htmlspecialchars(strip_tags($main_service_str)));
        $stmt->bindParam(':subcategories', htmlspecialchars(strip_tags($subcategories_str)));
        $stmt->bindParam(':provider_phone', htmlspecialchars(strip_tags($provider_phone)));
        $stmt->bindParam(':provider_address', htmlspecialchars(strip_tags($provider_address)));
        $stmt->bindParam(':portfolio_text', htmlspecialchars(strip_tags($portfolio_text))); // Use portfolio_text
        $stmt->bindParam(':provider_bio', htmlspecialchars(strip_tags($provider_bio)));

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // You can add other methods here like getServicesByProviderId, updateService, deleteService, etc.
}