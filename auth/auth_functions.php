<?php

require_once __DIR__ . '/../includes/Database.php';

class Auth {
    private $db;
    private $session_timeout = 3600; // 1 hour in seconds
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Authenticate user and create session
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array|bool User data if successful, false otherwise
     */
    public function login($username, $password) {
        // Get user by username
        $user = $this->db->getRecord("SELECT * FROM users WHERE username = ?", [$username]);
        
        if (!$user) {
            return false;
        }
        
        // Check if user is banned
        if (isset($user['is_banned']) && $user['is_banned'] == 1) {
            return false;
        }
        
        // Check if user is suspended
        if (isset($user['is_suspended']) && $user['is_suspended'] == 1) {
            // Check if suspension period has ended
            if (isset($user['suspend_until']) && strtotime($user['suspend_until']) > time()) {
                return false;
            } else {
                // Suspension period has ended, unsuspend user
                $this->unsuspendUser($user['id']);
            }
        }
        
        // Password check - verify hashed password for all users including admin
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            return $user;
        }
        
        return false;
    }
    
    /**
     * Get current logged in user data
     * 
     * @return array|bool User data if logged in, false otherwise
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $this->db->getRecord("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if user is logged in, false otherwise
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Check if user has a specific role
     * 
     * @param string $role The role to check
     * @return bool True if user has the role, false otherwise
     */
    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Check if session has timed out
     * 
     * @return bool True if session is still valid, false if timed out
     */
    public function checkSessionTimeout() {
        if (!isset($_SESSION['last_activity'])) {
            return false;
        }
        
        $current_time = time();
        if (($current_time - $_SESSION['last_activity']) > $this->session_timeout) {
            // Session has expired
            $this->logout();
            return false;
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = $current_time;
        return true;
    }
    
    /**
     * Log user out
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Ensure admin user exists
     */
    public function ensureAdminExists() {
        $admin = $this->db->getRecord("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
        
        if (!$admin) {
            // Create default admin user with plain text password (not recommended in prod!)
            $default_admin = [
                'username' => 'admin',
                'password' => 'admin',  // <-- plain text here
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('users', $default_admin);
        }
    }
    
    /**
     * Update admin credentials
     * 
     * @param string $username New admin username
     * @param string $password New admin password
     * @return bool True if successful, false otherwise
     */
    public function updateAdminCredentials($username, $password) {
        $data = [
            'username' => $username,
            'password' => $password  // <-- store plain text here
        ];
        
        return $this->db->update('users', $data, "role = 'admin'");
    }
    
    /**
     * Suspend a user for a specific number of days
     * 
     * @param int $user_id User ID to suspend
     * @param int $days Number of days to suspend the user
     * @param string $reason Reason for suspension
     * @return bool True if successful, false otherwise
     */
    public function suspendUser($user_id, $days, $reason = '') {
        $suspend_until = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        
        $data = [
            'is_suspended' => 1,
            'suspend_until' => $suspend_until,
            'suspend_reason' => $reason
        ];
        
        return $this->db->update('users', $data, 'id = ?', [$user_id]);
    }
    
    /**
     * Ban a user permanently
     * 
     * @param int $user_id User ID to ban
     * @param string $reason Reason for banning
     * @return bool True if successful, false otherwise
     */
    public function banUser($user_id, $reason = '') {
        $data = [
            'is_banned' => 1,
            'ban_reason' => $reason
        ];
        
        return $this->db->update('users', $data, 'id = ?', [$user_id]);
    }
    
    /**
     * Unban a user
     * 
     * @param int $user_id User ID to unban
     * @return bool True if successful, false otherwise
     */
    public function unbanUser($user_id) {
        $data = [
            'is_banned' => 0,
            'ban_reason' => null
        ];
        
        return $this->db->update('users', $data, 'id = ?', [$user_id]);
    }
    
    /**
     * Unsuspend a user
     * 
     * @param int $user_id User ID to unsuspend
     * @return bool True if successful, false otherwise
     */
    public function unsuspendUser($user_id) {
        $data = [
            'is_suspended' => 0,
            'suspend_until' => null,
            'suspend_reason' => null
        ];
        
        return $this->db->update('users', $data, 'id = ?', [$user_id]);
    }
}
