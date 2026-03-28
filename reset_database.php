<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Resetting Database...</h2>";

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'town_voting_system';

// Connect to database
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    // Try to connect without database
    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Create database
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    echo "✓ Created database<br>";
}

echo "✓ Connected to database<br>";

// Drop all tables
$tables = ['admin', 'candidates', 'voters'];
foreach ($tables as $table) {
    if ($conn->query("DROP TABLE IF EXISTS $table")) {
        echo "✓ Dropped table: $table<br>";
    }
}

// Create admin table
$sql = "CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "✓ Created admin table<br>";
} else {
    die("✗ Failed to create admin: " . $conn->error);
}

// Insert admin
$sql = "INSERT INTO admin (username, password, name, email) 
        VALUES ('admin', 'admin123', 'Administrator', 'admin@townvote.gov')";
if ($conn->query($sql)) {
    echo "✓ Inserted admin user<br>";
} else {
    echo "⚠ Admin insert: " . $conn->error . "<br>";
}

// Create candidates table
$sql = "CREATE TABLE candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidate_id VARCHAR(20) UNIQUE NOT NULL,
    voter_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    party VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    village VARCHAR(100) NOT NULL,
    election_symbol VARCHAR(50) NOT NULL,
    experience INT DEFAULT 0,
    votes INT DEFAULT 0,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "✓ Created candidates table<br>";
} else {
    die("✗ Failed to create candidates: " . $conn->error);
}

// Insert candidates
$sql = "INSERT INTO candidates (candidate_id, voter_id, name, party, age, village, election_symbol, experience, votes) VALUES
('CAN2026001', 'VOT20260001', 'Viswajeet Kadam', 'BJP', 45, 'Islampur', 'Lotus', 10, 15230),
('CAN2026002', 'VOT20260002', 'Vishal Patil', 'Congress', 52, 'Palus', 'Hand', 15, 12450),
('CAN2026003', 'VOT20260003', 'Jayant Patil', 'NCP', 48, 'Walwa', 'Clock', 12, 8920),
('CAN2026004', 'VOT20260004', 'Sakshi Patil', 'Shiv Sena', 38, 'Nagarale', 'Bicycle', 5, 5680),
('CAN2026005', 'VOT20260005', 'Rohit Jadhav', 'Independent', 35, 'Kundal', 'Elephant', 0, 2950)";

if ($conn->query($sql)) {
    echo "✓ Inserted 5 candidates<br>";
} else {
    echo "⚠ Candidate insert: " . $conn->error . "<br>";
}

// Create voters table
$sql = "CREATE TABLE voters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voter_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    village VARCHAR(100) NOT NULL,
    ward INT NOT NULL,
    address TEXT,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    has_voted BOOLEAN DEFAULT FALSE,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "✓ Created voters table<br>";
} else {
    die("✗ Failed to create voters: " . $conn->error);
}

// Insert voters
$sql = "INSERT INTO voters (voter_id, name, age, gender, village, ward, address, status) VALUES
('VOT20260001', 'Amit Sharma', 28, 'Male', 'Islampur', 3, '123 Main Street, Islampur', 'Active'),
('VOT20260002', 'Priya Patil', 32, 'Female', 'Palus', 2, '456 Market Road, Palus', 'Active'),
('VOT20260003', 'Rajesh Desai', 45, 'Male', 'Walwa', 5, '789 Temple Street, Walwa', 'Active'),
('VOT20260004', 'Sunita Kadam', 29, 'Female', 'Islampur', 1, '321 College Road, Islampur', 'Active'),
('VOT20260005', 'Mahesh Jadhav', 38, 'Male', 'Kundal', 4, '654 Station Road, Kundal', 'Active')";

if ($conn->query($sql)) {
    echo "✓ Inserted 5 voters<br>";
} else {
    echo "⚠ Voter insert: " . $conn->error . "<br>";
}

// Verify
echo "<h3>✓ Database Reset Complete!</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin-top: 20px;'>";
echo "<tr style='background: #3498db; color: white;'><th>Table</th><th>Records</th></tr>";

$tables = ['admin', 'candidates', 'voters'];
foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        $color = $row['count'] > 0 ? 'green' : 'red';
        echo "<tr><td>$table</td><td style='color: $color; font-weight: bold;'>{$row['count']}</td></tr>";
    } else {
        echo "<tr><td>$table</td><td style='color: red;'>Error</td></tr>";
    }
}
echo "</table>";

echo "<br><a href='login.php' style='display: inline-block; background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Go to Login</a>";
echo "<a href='dashboard.php' style='display: inline-block; background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>";

$conn->close();
?>