<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }

    // Register user
    public function register($data) {
        $this->db->query('INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)');
        
        // Bind values
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role'] ?? 'member');

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Login user
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row && password_verify($password, $row->password)) {
            return $row;
        } else {
            return false;
        }
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        return ($this->db->rowCount() > 0);
    }

    // Get all users
    public function getUsers() {
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // Get user by ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Update user
    public function updateUser($data) {
        $this->db->query('UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);

        return $this->db->execute();
    }

    // Delete user
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}