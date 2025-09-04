<?php
class Database {
    // --- IMPORTANT: CHANGE THESE TO YOUR ACTUAL DATABASE DETAILS ---
    private $host = 'localhost';
    private $db_name = 'innovista'; // Your database name
    private $username = 'root';        // Your database username
    private $password = '';            // Your database password
    private $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            throw $e;
        }
        return $this->conn;
    }
}
?>