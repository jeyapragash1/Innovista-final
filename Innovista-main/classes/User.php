<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\classes\User.php

class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register a new user (customer or provider).
     * @param string $name User's full name or company name.
     * @param string $email User's email address.
     * @param string $password Raw password.
     * @param string $role 'customer' or 'provider'.
     * @param string $phone Optional phone number.
     * @param string $address Optional address.
     * @param string $bio Optional biography for providers.
     * @param string $portfolio_text Optional portfolio link/text for providers. (Renamed from $portfolio to avoid confusion with files)
     * @param string $profile_image_path Optional path to uploaded profile image.
     * @return array|string|false Array of user data on success, error message string, or false on DB error.
     */
    public function register($name, $email, $password, $role = 'customer', $phone = '', $address = '', $bio = '', $portfolio_text = '', $profile_image_path = 'assets/images/default-avatar.jpg') {
        // Check if email already exists
        if ($this->findByEmail($email)) {
            return "Email already exists.";
        }

        // Hash the password for security
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Sanitize inputs
        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $role = htmlspecialchars(strip_tags($role));
        $phone = htmlspecialchars(strip_tags($phone));
        $address = htmlspecialchars(strip_tags($address));
        $bio = htmlspecialchars(strip_tags($bio));
        $portfolio_text = htmlspecialchars(strip_tags($portfolio_text)); // Ensure portfolio is just text/URL
        $profile_image_path = htmlspecialchars(strip_tags($profile_image_path));


        if ($role === 'customer') {
            $query = "INSERT INTO " . $this->table_name . " (name, email, password, role, phone, address, profile_image_path) 
                      VALUES (:name, :email, :password, :role, :phone, :address, :profile_image_path)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':profile_image_path', $profile_image_path);

        } elseif ($role === 'provider') {
            // Providers start with 'pending' status
            $provider_status = 'pending';
            $query = "INSERT INTO " . $this->table_name . " (name, email, password, role, provider_status, phone, address, portfolio, bio, profile_image_path) 
                      VALUES (:name, :email, :password, :role, :provider_status, :phone, :address, :portfolio_text, :bio, :profile_image_path)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':provider_status', $provider_status); // Set default pending status
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':portfolio_text', $portfolio_text); // Bind renamed parameter
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':profile_image_path', $profile_image_path);
        } else {
            return "Error: Invalid user role specified.";
        }

        if ($stmt->execute()) {
            // Return the newly created user's ID
            $newUserId = $this->conn->lastInsertId();
            // Fetch and return the full user record
            return $this->findById($newUserId);
        }
        return false; // Return false on database execution error
    }

    /**
     * Authenticate a user.
     * @param string $email User's email.
     * @param string $password Raw password.
     * @return array|false Array of user data on successful login, false otherwise.
     */
    public function login($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct. Return the full user data.
            return $user;
        }
        // Either user not found or password incorrect
        return false;
    }
    
    /**
     * Helper function to find a user by email.
     * @param string $email Email address to search for.
     * @return array|false User data as an associative array, or false if not found.
     */
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

    /**
     * Helper function to find a user by ID.
     * @param int $id User ID to search for.
     * @return array|false User data as an associative array, or false if not found.
     */
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Update a user's profile information.
     * @param int $id User ID.
     * @param string $name User's full name.
     * @param string $email User's email address.
     * @param string $phone Optional phone number.
     * @param string $address Optional address.
     * @param string $bio Optional biography.
     * @param string $profile_image_path Optional path to uploaded profile image.
     * @return bool True on success, false on DB error.
     */
    public function update($id, $name, $email, $phone, $address, $bio, $profile_image_path) {
        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $phone = htmlspecialchars(strip_tags($phone));
        $address = htmlspecialchars(strip_tags($address));
        $bio = htmlspecialchars(strip_tags($bio));
        $profile_image_path = htmlspecialchars(strip_tags($profile_image_path));
        
        $query = "UPDATE " . $this->table_name . " SET 
                    name = :name, 
                    email = :email, 
                    phone = :phone, 
                    address = :address, 
                    bio = :bio, 
                    profile_image_path = :profile_image_path
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':profile_image_path', $profile_image_path);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}