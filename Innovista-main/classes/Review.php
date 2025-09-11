<?php
class Review {
    private $pdo;

    // Accept PDO from Database.php
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create new feedback
    public function create($quotationId, $customerId, $providerId, $rating, $comment) {
        $sql = "INSERT INTO reviews (quotation_id, customer_id, provider_id, rating, comment)
                VALUES (:quotation_id, :customer_id, :provider_id, :rating, :comment)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':quotation_id' => $quotationId,
            ':customer_id'  => $customerId,
            ':provider_id'  => $providerId,
            ':rating'       => $rating,
            ':comment'      => $comment
        ]);
    }

    // Check if feedback already exists
    public function existsForBooking($quotationId, $customerId) {
        $sql = "SELECT id FROM reviews WHERE quotation_id = :quotation_id AND customer_id = :customer_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':quotation_id' => $quotationId,
            ':customer_id'  => $customerId
        ]);
        return $stmt->fetch() ? true : false;
    }

    // Get feedback for a specific booking + customer
    public function getFeedbackByBooking($quotationId, $customerId) {
        $sql = "SELECT * FROM reviews WHERE quotation_id = :quotation_id AND customer_id = :customer_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':quotation_id' => $quotationId,
            ':customer_id'  => $customerId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all reviews for a provider (for provider dashboard)
    public function byProvider($providerId) {
        $sql = "SELECT * FROM reviews WHERE provider_id = :provider_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':provider_id' => $providerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all reviews (for admin dashboard)
    public function all() {
        $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete review (admin only)
    public function delete($reviewId) {
        $sql = "DELETE FROM reviews WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $reviewId]);
    }

    // Average rating for provider
    public function providerAverage($providerId) {
        $sql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
                FROM reviews WHERE provider_id = :provider_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':provider_id' => $providerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
