<?php
require_once 'config.php';
requireLogin();

$success = false;
$error = '';
$new_voter_id = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? '';
    $village = $_POST['village'] ?? '';
    $ward = intval($_POST['ward'] ?? 0);
    $address = trim($_POST['address'] ?? '');
    $status = isset($_POST['status']) ? 'Active' : 'Inactive';
    
    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if ($age < 18) $errors[] = 'Age must be at least 18';
    if (empty($gender)) $errors[] = 'Gender is required';
    if (empty($village)) $errors[] = 'Village is required';
    if ($ward < 1 || $ward > 6) $errors[] = 'Valid ward is required';
    
    if (empty($errors)) {
        // Generate unique voter ID
        $voter_id = generateVoterID($conn);
        
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO voters (voter_id, name, age, gender, village, ward, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssss", $voter_id, $name, $age, $gender, $village, $ward, $address, $status);
        
        if ($stmt->execute()) {
            $success = true;
            $new_voter_id = $voter_id;
        } else {
            $error = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $error = implode('<br>', $errors);
    }
}

// Get recent voters
$recent_voters = [];
$result = $conn->query("SELECT * FROM voters ORDER BY registration_date DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_voters[] = $row;
    }
}

// If no voters in DB, use defaults
if (empty($recent_voters)) {
    $recent_voters = [
        ['name' => 'Amit Sharma', 'village' => 'Islampur', 'ward' => 3, 'age' => 28, 'status' => 'Active'],
        ['name' => 'Priya Patil', 'village' => 'Palus', 'ward' => 2, 'age' => 32, 'status' => 'Active'],
        ['name' => 'Rajesh Desai', 'village' => 'Walwa', 'ward' => 5, 'age' => 45, 'status' => 'Active']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Registration - Town Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copy all CSS styles from candidate-registration.php here, changing candidates to voters */
        /* For brevity, use the same CSS structure - just change class names as needed */
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --gray: #95a5a6;
            --sidebar-width: 280px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            min-height: 100vh;
            display: flex;
        }
        
        /* Vertical Navbar Styles - Same as candidate-registration.php */
        .vertical-nav {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary) 0%, #1a2632 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s;
            animation: slideInLeft 0.8s ease-out;
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .nav-brand {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .nav-brand i {
            font-size: 3rem;
            color: var(--secondary);
            margin-bottom: 10px;
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .nav-brand h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .nav-brand span {
            color: var(--secondary);
        }
        
        .nav-brand p {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-top: 5px;
        }
        
        .nav-items {
            padding: 0 15px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(52, 152, 219, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .nav-item:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .nav-item i {
            width: 24px;
            font-size: 1.2rem;
            z-index: 1;
        }
        
        .nav-item span {
            z-index: 1;
        }
        
        .nav-item:hover {
            background: rgba(52, 152, 219, 0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-item.active {
            background: var(--secondary);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        }
        
        .nav-item.active i {
            color: white;
        }
        
        .user-section {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-details .name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .user-details .role {
            font-size: 0.8rem;
            opacity: 0.7;
        }
        
        .logout-btn {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }
        
        .logout-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(231, 76, 60, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .logout-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .logout-btn:hover {
            background: rgba(231, 76, 60, 0.2);
            border-color: var(--danger);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            animation: fadeIn 1s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .header h1 {
            color: var(--primary);
            font-size: 1.8rem;
            position: relative;
        }
        
        .header h1 i {
            color: var(--secondary);
            margin-right: 10px;
        }
        
        .header h1::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary);
            animation: expandLine 1.5s ease-out;
        }
        
        @keyframes expandLine {
            from { width: 0; }
            to { width: 50px; }
        }
        
        .date-display {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray);
            font-size: 1rem;
            padding: 10px 20px;
            background: #f8fafc;
            border-radius: 30px;
        }
        
        .date-display i {
            color: var(--secondary);
        }
        
        .registration-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            animation: cardAppear 0.8s ease-out;
        }
        
        @keyframes cardAppear {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .form-group {
            margin-bottom: 20px;
            animation: slideInRight 0.8s ease-out;
            animation-fill-mode: both;
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
        }
        
        .two-column .form-group:nth-child(1) { animation-delay: 0.1s; }
        .two-column .form-group:nth-child(2) { animation-delay: 0.2s; }
        .two-column .form-group:nth-child(3) { animation-delay: 0.3s; }
        
        @keyframes slideInRight {
            from {
                transform: translateX(30px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .form-group:hover label {
            color: var(--secondary);
            transform: translateX(5px);
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #fcfdfe;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
            background-color: white;
            transform: scale(1.02);
        }
        
        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 200px;
            height: 200px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #229954 100%);
            color: white;
        }
        
        .btn:active {
            transform: scale(0.95);
        }
        
        .btn-block {
            display: block;
            width: 100%;
            animation: slideUp 0.8s ease-out 0.4s both;
        }
        
        /* Success Message */
        .success-message {
            background-color: #d4f7e2;
            color: var(--success);
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 15px;
            display: <?php echo $success ? 'block' : 'none'; ?>;
            border-left: 4px solid var(--success);
            animation: slideInDown 0.5s ease-out;
        }
        
        .error-message {
            background-color: #fee;
            color: var(--danger);
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 15px;
            display: <?php echo $error ? 'block' : 'none'; ?>;
            border-left: 4px solid var(--danger);
            animation: slideInDown 0.5s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .voter-id-display {
            background: linear-gradient(135deg, #e8f4fc 0%, #d4e9ff 100%);
            border: 2px dashed var(--secondary);
            border-radius: 12px;
            padding: 25px;
            margin-top: 25px;
            text-align: center;
            display: <?php echo $success ? 'block' : 'none'; ?>;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .voter-id-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
            letter-spacing: 2px;
            margin: 15px 0;
            font-family: monospace;
            background: white;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: glow 2s infinite;
        }
        
        /* Status Toggle */
        .status-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: var(--success);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        /* Recent Voters */
        .recent-voters {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.8s ease-out 0.2s both;
        }
        
        .recent-voters h3 {
            color: var(--primary);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f4f8;
        }
        
        .voter-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            border-bottom: 1px solid #eef2f7;
            transition: all 0.3s;
        }
        
        .voter-item:hover {
            background: #f8fafc;
            transform: translateX(5px);
        }
        
        .voter-item:last-child {
            border-bottom: none;
        }
        
        .voter-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success), #229954);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .voter-details {
            flex: 1;
        }
        
        .voter-details h4 {
            color: var(--primary);
            font-size: 1rem;
            margin-bottom: 3px;
        }
        
        .voter-details p {
            color: var(--gray);
            font-size: 0.8rem;
        }
        
        .voter-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            background: #e8f4fc;
            color: var(--secondary);
        }
        
        .voter-badge.active {
            background: #d4f7e2;
            color: var(--success);
        }
        
        .voter-badge.inactive {
            background: #fee;
            color: var(--danger);
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            border-top: 1px solid #eef2f7;
            color: var(--gray);
            font-size: 0.9rem;
            background: white;
            border-radius: 12px;
            animation: fadeIn 1.5s ease-out;
        }
        
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .vertical-nav {
                position: relative;
                width: 100%;
                height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .user-section {
                position: relative;
            }
            
            .registration-container {
                padding: 20px;
            }
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Vertical Navbar -->
    <nav class="vertical-nav">
        <div class="nav-brand">
            <i class="fas fa-vote-yea"></i>
            <h2>Town<span>Vote</span></h2>
            <p>2026 Elections</p>
        </div>
        
        <div class="nav-items">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="analysis.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Analysis</span>
            </a>
            <a href="village-view.php" class="nav-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Village View</span>
            </a>
            <a href="reports.php" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            
            <a href="candidate-registration.php" class="nav-item">
                <i class="fas fa-user-tie"></i>
                <span>Candidate</span>
            </a>
            <a href="voter-registration.php" class="nav-item active">
                <i class="fas fa-user-plus"></i>
                <span>Voter</span>
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar" id="user-avatar"><?php echo substr($_SESSION['user_name'] ?? 'JD', 0, 2); ?></div>
                <div class="user-details">
                    <div class="name" id="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'John Doe'); ?></div>
                    <div class="role" id="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Officer'); ?></div>
                </div>
            </div>
            <a href="logout.php" class="logout-btn" id="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div>
                <h1><i class="fas fa-user-plus"></i> Voter Registration</h1>
                <p>Register new voters for 2026 elections</p>
            </div>
            <div class="date-display">
                <i class="fas fa-calendar-alt"></i>
                <span id="current-date"></span>
            </div>
        </div>
        
        <div class="registration-container">
            <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form id="voter-form" method="POST" action="">
                <div class="two-column">
                    <div>
                        <div class="form-group">
                            <label for="voter-name">Full Name *</label>
                            <input type="text" id="voter-name" name="name" class="form-control" placeholder="Enter full name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="voter-age">Age *</label>
                            <input type="number" id="voter-age" name="age" class="form-control" min="18" placeholder="Minimum 18 years" required value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="voter-gender">Gender *</label>
                            <select id="voter-gender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo ($_POST['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($_POST['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($_POST['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label for="voter-village">Village *</label>
                            <select id="voter-village" name="village" class="form-control" required>
                                <option value="">Select Village</option>
                                <option value="Nagarale" <?php echo ($_POST['village'] ?? '') === 'Nagarale' ? 'selected' : ''; ?>>Nagarale</option>
                                <option value="Palus" <?php echo ($_POST['village'] ?? '') === 'Palus' ? 'selected' : ''; ?>>Palus</option>
                                <option value="Pundi" <?php echo ($_POST['village'] ?? '') === 'Pundi' ? 'selected' : ''; ?>>Pundi</option>
                                <option value="Dhudhondi" <?php echo ($_POST['village'] ?? '') === 'Dhudhondi' ? 'selected' : ''; ?>>Dhudhondi</option>
                                <option value="Kundal" <?php echo ($_POST['village'] ?? '') === 'Kundal' ? 'selected' : ''; ?>>Kundal</option>
                                <option value="Burli" <?php echo ($_POST['village'] ?? '') === 'Burli' ? 'selected' : ''; ?>>Burli</option>
                                <option value="Wangi" <?php echo ($_POST['village'] ?? '') === 'Wangi' ? 'selected' : ''; ?>>Wangi</option>
                                <option value="Walwa" <?php echo ($_POST['village'] ?? '') === 'Walwa' ? 'selected' : ''; ?>>Walwa</option>
                                <option value="Islampur" <?php echo ($_POST['village'] ?? '') === 'Islampur' ? 'selected' : ''; ?>>Islampur</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="voter-ward">Ward Number *</label>
                            <select id="voter-ward" name="ward" class="form-control" required>
                                <option value="">Select Ward</option>
                                <option value="1" <?php echo ($_POST['ward'] ?? '') == 1 ? 'selected' : ''; ?>>Ward 1</option>
                                <option value="2" <?php echo ($_POST['ward'] ?? '') == 2 ? 'selected' : ''; ?>>Ward 2</option>
                                <option value="3" <?php echo ($_POST['ward'] ?? '') == 3 ? 'selected' : ''; ?>>Ward 3</option>
                                <option value="4" <?php echo ($_POST['ward'] ?? '') == 4 ? 'selected' : ''; ?>>Ward 4</option>
                                <option value="5" <?php echo ($_POST['ward'] ?? '') == 5 ? 'selected' : ''; ?>>Ward 5</option>
                                <option value="6" <?php echo ($_POST['ward'] ?? '') == 6 ? 'selected' : ''; ?>>Ward 6</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="voter-address">Address</label>
                            <textarea id="voter-address" name="address" class="form-control" rows="2" placeholder="Enter address (optional)"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <div class="status-toggle">
                        <label class="toggle-switch">
                            <input type="checkbox" id="voter-status-toggle" name="status" checked>
                            <span class="toggle-slider"></span>
                        </label>
                        <span id="voter-status-label">Active</span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" id="voter-submit-btn">
                    <i class="fas fa-user-plus"></i> Register Voter
                </button>
            </form>
            
            <div id="voter-id-display" class="voter-id-display">
                <h4>Voter Registered Successfully!</h4>
                <p>Generated Voter ID:</p>
                <div class="voter-id-value" id="generated-voter-id"><?php echo $new_voter_id; ?></div>
                <p>Please note this ID for voting purposes.</p>
                <button id="print-btn" class="btn btn-success" style="margin-top: 15px;">
                    <i class="fas fa-print"></i> Print Voter ID
                </button>
            </div>
        </div>
        
        <!-- Recent Voters -->
        <div class="recent-voters" id="recent-voters">
            <h3><i class="fas fa-history"></i> Recently Registered Voters</h3>
            <div id="recent-voters-list">
                <?php foreach ($recent_voters as $voter): ?>
                <div class="voter-item">
                    <div class="voter-icon"><?php echo substr($voter['name'], 0, 2); ?></div>
                    <div class="voter-details">
                        <h4><?php echo htmlspecialchars($voter['name']); ?></h4>
                        <p><?php echo htmlspecialchars($voter['village']); ?> • Ward <?php echo $voter['ward']; ?> • Age <?php echo $voter['age']; ?></p>
                    </div>
                    <div class="voter-badge <?php echo strtolower($voter['status']); ?>"><?php echo $voter['status']; ?></div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($recent_voters)): ?>
                <p style="text-align: center; color: #666; padding: 20px;">No voters registered yet</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>Town Voting Analysis System &copy; 2026 | Sangli District Municipal Elections</p>
            <p>Fields marked with * are required</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set current date
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            const statusToggle = document.getElementById('voter-status-toggle');
            const statusLabel = document.getElementById('voter-status-label');
            
            // Status toggle
            if (statusToggle) {
                statusToggle.addEventListener('change', function() {
                    statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
                    statusLabel.style.animation = 'none';
                    setTimeout(() => {
                        statusLabel.style.animation = 'pulse 1s';
                    }, 10);
                });
            }
            
            // Print button
            const printBtn = document.getElementById('print-btn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                        window.print();
                    }, 200);
                });
            }
        });
    </script>
</body>
</html>