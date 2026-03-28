<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'town_voting_system');

// Try different connection methods
$conn = null;
$ports_to_try = [3306, 3307, 3308];
$hosts_to_try = ['localhost', '127.0.0.1'];

$connection_error = null;

foreach ($hosts_to_try as $host) {
    foreach ($ports_to_try as $port) {
        try {
            $conn = @new mysqli($host, DB_USER, DB_PASS, DB_NAME, $port);
            if (!$conn->connect_error) {
                break 2;
            }
        } catch (Exception $e) {
            $connection_error = $e->getMessage();
        }
    }
}

// If no connection, show error
if (!$conn || $conn->connect_error) {
    die("
    <!DOCTYPE html>
    <html>
    <head>
        <title>Database Connection Error</title>
        <meta charset='UTF-8'>
        <style>
            body {
                font-family: 'Segoe UI', Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                padding: 20px;
            }
            .error-container {
                max-width: 600px;
                background: white;
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                text-align: center;
            }
            .error-icon {
                width: 80px;
                height: 80px;
                background: #e74c3c;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
            }
            .error-icon i {
                font-size: 40px;
                color: white;
            }
            h1 {
                color: #2c3e50;
                margin-bottom: 10px;
            }
            .error-message {
                background: #fef5f5;
                border-left: 4px solid #e74c3c;
                padding: 15px;
                margin: 20px 0;
                text-align: left;
                font-family: monospace;
                color: #c0392b;
            }
            .solution {
                background: #e8f4fc;
                border-left: 4px solid #3498db;
                padding: 15px;
                margin: 20px 0;
                text-align: left;
            }
            .solution h3 {
                color: #2980b9;
                margin-bottom: 10px;
            }
            .solution ul {
                margin-left: 20px;
                color: #555;
            }
            .solution li {
                margin: 8px 0;
            }
            .btn {
                display: inline-block;
                background: #3498db;
                color: white;
                padding: 12px 24px;
                border-radius: 8px;
                text-decoration: none;
                margin-top: 20px;
                font-weight: bold;
                transition: all 0.3s;
            }
            .btn:hover {
                background: #2980b9;
                transform: translateY(-2px);
            }
            .status-box {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 10px;
                margin-top: 20px;
                font-size: 12px;
                color: #666;
                text-align: left;
            }
            code {
                background: #f0f0f0;
                padding: 2px 5px;
                border-radius: 3px;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <div class='error-icon'>
                <i class='fas fa-database'></i>
            </div>
            <h1>Database Connection Error</h1>
            <p>Unable to connect to MySQL database.</p>
            
            <div class='error-message'>
                <strong>Error Details:</strong><br>
                " . htmlspecialchars($conn->connect_error ?? $connection_error ?? 'Connection failed') . "
            </div>
            
            <div class='solution'>
                <h3><i class='fas fa-tools'></i> How to Fix:</h3>
                <ul>
                    <li><strong>1. Start MySQL:</strong> Open XAMPP Control Panel and click 'Start' next to MySQL</li>
                    <li><strong>2. Run as Admin:</strong> Right-click XAMPP and select 'Run as administrator'</li>
                    <li><strong>3. Check Port:</strong> Make sure MySQL is using port 3306 (or update config)</li>
                    <li><strong>4. Restart XAMPP:</strong> Stop all services, then start again</li>
                    <li><strong>5. Reinstall Service:</strong> Click the 'X' next to MySQL, then 'Install', then 'Start'</li>
                </ul>
            </div>
            
            <a href='javascript:location.reload()' class='btn'>
                <i class='fas fa-sync-alt'></i> Retry Connection
            </a>
            
            <div class='status-box'>
                <strong>Quick Diagnostics:</strong><br>
                <span id='port-status'>Checking ports...</span><br>
                <strong>XAMPP Path:</strong> C:\\xampp\\<br>
                <strong>MySQL Path:</strong> C:\\xampp\\mysql\\bin\\mysqld.exe<br>
                <strong>Try these commands in Command Prompt (Admin):</strong><br>
                <code>netstat -ano | findstr :3306</code><br>
                <code>tasklist | findstr mysql</code>
            </div>
        </div>
        
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
        <script>
            // Check port status via AJAX
            fetch('/check_mysql.php')
                .then(response => response.text())
                .then(data => {
                    if(data.includes('running')) {
                        document.getElementById('port-status').innerHTML = '✓ Port check: MySQL appears to be running';
                        document.getElementById('port-status').style.color = 'green';
                    } else {
                        document.getElementById('port-status').innerHTML = '✗ Port check: MySQL is not responding';
                        document.getElementById('port-status').style.color = 'red';
                    }
                })
                .catch(() => {
                    document.getElementById('port-status').innerHTML = '✗ Could not verify port status';
                    document.getElementById('port-status').style.color = 'orange';
                });
        </script>
    </body>
    </html>
    ");
    exit();
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to generate unique candidate ID
function generateCandidateID($conn) {
    $year = date('Y');
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(candidate_id, 8) AS UNSIGNED)) as max_id FROM candidates WHERE candidate_id LIKE 'CAN{$year}%'");
    if ($result) {
        $row = $result->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;
        return "CAN{$year}" . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }
    return "CAN{$year}0001";
}

// Helper function to generate unique voter ID
function generateVoterID($conn) {
    $year = date('Y');
    $result = $conn->query("SELECT MAX(CAST(SUBSTRING(voter_id, 8) AS UNSIGNED)) as max_id FROM voters WHERE voter_id LIKE 'VOT{$year}%'");
    if ($result) {
        $row = $result->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;
        return "VOT{$year}" . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }
    return "VOT{$year}0001";
}

// Check if votes column exists
function hasVotesColumn($conn) {
    $check = $conn->query("SHOW COLUMNS FROM candidates LIKE 'votes'");
    return ($check && $check->num_rows > 0);
}

// Get total voters count
function getTotalVoters($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM voters");
    if ($result) {
        $voters = $result->fetch_assoc()['total'];
        return 68450 + $voters;
    }
    return 68450;
}

// Get total votes count
function getTotalVotes($conn) {
    if (hasVotesColumn($conn)) {
        $result = $conn->query("SELECT SUM(votes) as total FROM candidates");
        if ($result) {
            $row = $result->fetch_assoc();
            $candidate_votes = $row['total'] ?? 0;
            return 45230 + $candidate_votes;
        }
    }
    return 45230;
}

// Get default candidates
function getDefaultCandidates() {
    return [
        ['id' => 1, 'name' => 'Viswajeet Kadam', 'party' => 'BJP', 'village' => 'Islampur', 'votes' => 15230],
        ['id' => 2, 'name' => 'Vishal Patil', 'party' => 'Congress', 'village' => 'Palus', 'votes' => 12450],
        ['id' => 3, 'name' => 'Jayant Patil', 'party' => 'NCP', 'village' => 'Walwa', 'votes' => 8920],
        ['id' => 4, 'name' => 'Sakshi Patil', 'party' => 'Shiv Sena', 'village' => 'Nagarale', 'votes' => 5680],
        ['id' => 5, 'name' => 'Rohit Jadhav', 'party' => 'Independent', 'village' => 'Kundal', 'votes' => 2950]
    ];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Get user name safely
function getUserName() {
    return $_SESSION['user_name'] ?? 'John Doe';
}

// Get user role safely
function getUserRole() {
    return $_SESSION['user_role'] ?? 'Election Officer';
}

// Get user avatar initials
function getUserInitials() {
    $name = getUserName();
    $parts = explode(' ', $name);
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}
?>