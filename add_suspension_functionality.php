<?php
/**
 * Add User Suspension Functionality
 * 
 * This script adds the necessary database columns and functions to support
 * user suspension and ban functionality in the system.
 */

// Include database connection
require_once __DIR__ . '/config/database.php';

// Initialize Database
$db = Database::getInstance();
$conn = $db->getConnection();

echo "Starting implementation of user suspension functionality...\n";

// Check if suspension columns exist in users table
$checkSuspendedColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'is_suspended'");
$suspendedColumnExists = ($checkSuspendedColumn && $checkSuspendedColumn->rowCount() > 0);

$checkSuspendUntilColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'suspend_until'");
$suspendUntilColumnExists = ($checkSuspendUntilColumn && $checkSuspendUntilColumn->rowCount() > 0);

$checkSuspendReasonColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'suspend_reason'");
$suspendReasonColumnExists = ($checkSuspendReasonColumn && $checkSuspendReasonColumn->rowCount() > 0);

// Add suspension columns if they don't exist
if (!$suspendedColumnExists) {
    echo "Adding 'is_suspended' column to users table...\n";
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN is_suspended TINYINT(1) DEFAULT 0");
        echo "'is_suspended' column added successfully.\n";
    } catch (PDOException $e) {
        echo "Error adding 'is_suspended' column: " . $e->getMessage() . "\n";
    }
}

if (!$suspendUntilColumnExists) {
    echo "Adding 'suspend_until' column to users table...\n";
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN suspend_until DATETIME DEFAULT NULL");
        echo "'suspend_until' column added successfully.\n";
    } catch (PDOException $e) {
        echo "Error adding 'suspend_until' column: " . $e->getMessage() . "\n";
    }
}

if (!$suspendReasonColumnExists) {
    echo "Adding 'suspend_reason' column to users table...\n";
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN suspend_reason VARCHAR(255) DEFAULT NULL");
        echo "'suspend_reason' column added successfully.\n";
    } catch (PDOException $e) {
        echo "Error adding 'suspend_reason' column: " . $e->getMessage() . "\n";
    }
}

// Create helper functions for suspension in auth_functions.php if they don't exist
$authFunctionsFile = __DIR__ . '/auth/auth_functions.php';
$authFunctionsContent = file_get_contents($authFunctionsFile);

// Check if suspension functions already exist
if (strpos($authFunctionsContent, 'suspendUser') === false) {
    echo "Adding suspension functions to auth_functions.php...\n";
    
    // Find the end of the Auth class
    $classEndPos = strrpos($authFunctionsContent, '}');
    
    // Suspension functions to add
    $suspensionFunctions = <<<EOT
    
    /**
     * Suspend a user for a specified number of days
     * 
     * @param int \$userId User ID to suspend
     * @param int \$days Number of days to suspend the user
     * @param string \$reason Reason for suspension
     * @return bool True if successful, false otherwise
     */
    public function suspendUser(\$userId, \$days, \$reason = '') {
        // Calculate suspension end date
        \$suspendUntil = date('Y-m-d H:i:s', strtotime("+{\$days} days"));
        
        // Update user with suspension info
        \$result = \$this->db->execute(
            "UPDATE users SET is_suspended = 1, suspend_until = ?, suspend_reason = ? WHERE id = ?", 
            [\$suspendUntil, \$reason, \$userId]
        );
        
        return \$result ? true : false;
    }
    
    /**
     * Unsuspend a user
     * 
     * @param int \$userId User ID to unsuspend
     * @return bool True if successful, false otherwise
     */
    public function unsuspendUser(\$userId) {
        // Update user to remove suspension
        \$result = \$this->db->execute(
            "UPDATE users SET is_suspended = 0, suspend_until = NULL, suspend_reason = NULL WHERE id = ?", 
            [\$userId]
        );
        
        return \$result ? true : false;
    }
    
    /**
     * Check if a user is suspended
     * 
     * @param int \$userId User ID to check
     * @return array|false Suspension info or false if not suspended
     */
    public function checkSuspension(\$userId) {
        \$user = \$this->db->getRecord(
            "SELECT is_suspended, suspend_until, suspend_reason FROM users WHERE id = ?", 
            [\$userId]
        );
        
        if (\$user && \$user['is_suspended'] == 1 && !empty(\$user['suspend_until'])) {
            \$suspendUntil = strtotime(\$user['suspend_until']);
            \$currentTime = time();
            
            if (\$currentTime < \$suspendUntil) {
                return [
                    'suspended' => true,
                    'until' => \$user['suspend_until'],
                    'reason' => \$user['suspend_reason'] ?? 'No reason provided'
                ];
            } else {
                // Suspension period has ended, update user status
                \$this->unsuspendUser(\$userId);
                return false;
            }
        }
        
        return false;
    }

}
EOT;
    
    // Insert the suspension functions before the last closing brace
    $updatedContent = substr($authFunctionsContent, 0, $classEndPos) . $suspensionFunctions;
    
    // Write the updated content back to the file
    file_put_contents($authFunctionsFile, $updatedContent);
    
    echo "Suspension functions added to auth_functions.php successfully.\n";
} else {
    echo "Suspension functions already exist in auth_functions.php.\n";
}

echo "\nUser suspension functionality implementation completed.\n";
echo "To run this script, use: php add_suspension_functionality.php\n";
echo "After running this script, you will be able to suspend and ban users from the admin panel.\n";
?>