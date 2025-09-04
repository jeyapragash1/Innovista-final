<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register a new user
    public function register($name, $email, $password, $role = 'customer', $service = '') {
        // Check if email already exists
        if ($this->findByEmail($email)) {
            return "Email already exists.";
        }

        // Hash the password for security
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $role = htmlspecialchars(strip_tags($role));
        if (is_array($service)) {
            $service = implode(',', $service);
        }
        $service = htmlspecialchars(strip_tags($service));

    // Accept subcategories as an optional argument, but do not store in users table
    // Only store name, email, password, role in users table
    $query = "INSERT INTO " . $this->table_name . " (name, email, password, role) VALUES (:name, :email, :password, :role)";
    $stmt = $this->conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password_hash);
    $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            return true;
        }
        return "Error: Unable to register.";
    }

    // Login a user
    public function login($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct
            return $user;
        }
        // Either user not found or password incorrect
        return false;
    }
    
    // Helper function to find a user by email
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
?>