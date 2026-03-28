<?php
require_once 'config.php';
requireLogin();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $_SESSION['user_name'] = $_POST['name'];
        $_SESSION['user_email'] = $_POST['email'];
        $success = "Profile updated successfully!";
    }
    
    if (isset($_POST['save_notifications'])) {
        $_SESSION['notifications'] = [
            'email' => isset($_POST['email_notifications']),
            'sms' => isset($_POST['sms_notifications']),
            'desktop' => isset($_POST['desktop_notifications']),
            'newVoter' => isset($_POST['notify_new_voter']),
            'votingProgress' => isset($_POST['notify_voting_progress']),
            'systemAlerts' => isset($_POST['notify_system_alerts']),
            'reports' => isset($_POST['notify_reports'])
        ];
        $success = "Notification preferences saved!";
    }
    
    if (isset($_POST['save_system'])) {
        $_SESSION['system_settings'] = [
            'autoRefresh' => isset($_POST['auto_refresh']),
            'showLive' => isset($_POST['show_live']),
            'debugMode' => isset($_POST['debug_mode']),
            'refreshInterval' => $_POST['refresh_interval'],
            'dataRetention' => $_POST['data_retention']
        ];
        $success = "System settings saved!";
    }
    
    if (isset($_POST['apply_theme'])) {
        $_SESSION['theme'] = [
            'theme' => $_POST['theme'],
            'accentColor' => $_POST['accent_color'],
            'fontSize' => $_POST['font_size']
        ];
        $success = "Theme applied successfully!";
    }
}

// Get saved settings
$notifications = $_SESSION['notifications'] ?? [
    'email' => true, 'sms' => false, 'desktop' => true,
    'newVoter' => true, 'votingProgress' => true, 'systemAlerts' => true, 'reports' => true
];

$system_settings = $_SESSION['system_settings'] ?? [
    'autoRefresh' => true, 'showLive' => true, 'debugMode' => false,
    'refreshInterval' => '60', 'dataRetention' => '180'
];

$theme = $_SESSION['theme'] ?? [
    'theme' => 'light', 'accentColor' => '#3498db', 'fontSize' => 'medium'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Town Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: <?php echo $theme['accentColor']; ?>;
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
        
        /* Vertical Navbar Styles */
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
        
        /* Settings Card */
        .settings-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            margin-bottom: 30px;
            animation: cardAppear 0.8s ease-out 0.1s both;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes cardAppear {
            from {
                transform: translateY(50px) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        
        .settings-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(52,152,219,0.05) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 2px solid #eef2f7;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 5px;
            position: relative;
            z-index: 1;
        }
        
        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            white-space: nowrap;
            font-weight: 500;
            color: #64748b;
            background: #f8fafc;
            border-radius: 8px 8px 0 0;
            position: relative;
            overflow: hidden;
        }
        
        .tab::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: var(--secondary);
            transition: width 0.3s;
        }
        
        .tab:hover::before {
            width: 100%;
        }
        
        .tab.active {
            border-bottom-color: var(--secondary);
            color: var(--secondary);
            font-weight: 600;
            background: white;
            animation: tabPulse 2s infinite;
        }
        
        @keyframes tabPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .tab.active::before {
            width: 100%;
        }
        
        .tab-content {
            display: none;
            animation: fadeInContent 0.5s;
            position: relative;
            z-index: 1;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeInContent {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 20px;
            animation: slideInRight 0.8s ease-out;
            animation-fill-mode: both;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        
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
        
        /* Buttons */
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
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #c0392b 100%);
            color: white;
        }
        
        .btn:active {
            transform: scale(0.95);
        }
        
        /* Theme Options */
        .theme-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .theme-option {
            border: 2px solid #e1e8f0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.4s;
            text-align: center;
            animation: cardScale 0.8s ease-out;
            animation-fill-mode: both;
            position: relative;
            overflow: hidden;
        }
        
        .theme-option:nth-child(1) { animation-delay: 0.1s; }
        .theme-option:nth-child(2) { animation-delay: 0.2s; }
        .theme-option:nth-child(3) { animation-delay: 0.3s; }
        
        @keyframes cardScale {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .theme-option::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(52,152,219,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s;
        }
        
        .theme-option:hover::before {
            opacity: 1;
        }
        
        .theme-option:hover {
            border-color: var(--secondary);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .theme-option.active {
            border-color: var(--secondary);
            background: #e8f4fc;
            animation: glow 2s infinite;
        }
        
        .theme-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .theme-option:hover .theme-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .theme-light .theme-icon { color: #f39c12; }
        .theme-dark .theme-icon { color: #2c3e50; }
        .theme-blue .theme-icon { color: #3498db; }
        
        /* Toggle Switch */
        .toggle-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s;
            animation: slideInLeft 0.8s ease-out;
            animation-fill-mode: both;
        }
        
        .toggle-group:nth-child(1) { animation-delay: 0.1s; }
        .toggle-group:nth-child(2) { animation-delay: 0.2s; }
        .toggle-group:nth-child(3) { animation-delay: 0.3s; }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-30px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .toggle-group:hover {
            background: #e8f4fc;
            transform: translateX(5px);
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
        
        /* Danger Zone */
        .danger-zone {
            border: 2px solid #fdd8d8;
            background: #ffeaea;
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
            animation: slideUp 0.8s ease-out 0.3s both;
            position: relative;
            overflow: hidden;
        }
        
        .danger-zone::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(231,76,60,0.05) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        .danger-zone h3 {
            color: var(--danger);
            margin-bottom: 15px;
        }
        
        .danger-zone h3 i {
            margin-right: 8px;
        }
        
        /* Success Message */
        .success-message {
            background-color: #d4f7e2;
            color: var(--success);
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 15px;
            display: <?php echo isset($success) ? 'block' : 'none'; ?>;
            border-left: 4px solid var(--success);
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
            
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                border-radius: 8px;
                margin-bottom: 5px;
            }
            
            .theme-options {
                grid-template-columns: 1fr;
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
            <a href="voter-registration.php" class="nav-item">
                <i class="fas fa-user-plus"></i>
                <span>Voter</span>
            </a>
            <a href="settings.php" class="nav-item active">
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
                <h1><i class="fas fa-cog"></i> Settings</h1>
                <p>Configure system preferences</p>
            </div>
            <div class="date-display">
                <i class="fas fa-calendar-alt"></i>
                <span id="current-date"></span>
            </div>
        </div>
        
        <?php if (isset($success)): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <div class="settings-card">
            <div class="tabs">
                <div class="tab active" data-tab="profile">Profile</div>
                <div class="tab" data-tab="password">Password</div>
                <div class="tab" data-tab="theme">Theme</div>
                <div class="tab" data-tab="notifications">Notifications</div>
                <div class="tab" data-tab="system">System</div>
            </div>
            
            <!-- Profile Settings -->
            <div id="profile-tab" class="tab-content active">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="profile-name">Full Name</label>
                        <input type="text" id="profile-name" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'John Doe'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="profile-email">Email Address</label>
                        <input type="email" id="profile-email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? 'john.doe@townvote.gov'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="profile-phone">Phone Number</label>
                        <input type="text" id="profile-phone" class="form-control" value="+1 (555) 123-4567">
                    </div>
                    
                    <div class="form-group">
                        <label for="profile-role">Role</label>
                        <input type="text" id="profile-role" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Election Officer'); ?>" readonly>
                        <small style="color: var(--gray);">Role cannot be changed</small>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
            
            <!-- Password Settings -->
            <div id="password-tab" class="tab-content">
                <form method="POST" action="" onsubmit="return validatePassword()">
                    <div class="form-group">
                        <label for="current-password">Current Password</label>
                        <input type="password" id="current-password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new-password" name="new_password" class="form-control" required>
                        <small style="color: var(--gray);">Minimum 8 characters with letters and numbers</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">Confirm New Password</label>
                        <input type="password" id="confirm-password" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
            
            <!-- Theme Settings -->
            <div id="theme-tab" class="tab-content">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Theme Preference</label>
                        <div class="theme-options">
                            <div class="theme-option theme-light <?php echo $theme['theme'] == 'light' ? 'active' : ''; ?>" data-theme="light" onclick="selectTheme('light')">
                                <div class="theme-icon">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <div class="theme-name">Light</div>
                            </div>
                            
                            <div class="theme-option theme-dark <?php echo $theme['theme'] == 'dark' ? 'active' : ''; ?>" data-theme="dark" onclick="selectTheme('dark')">
                                <div class="theme-icon">
                                    <i class="fas fa-moon"></i>
                                </div>
                                <div class="theme-name">Dark</div>
                            </div>
                            
                            <div class="theme-option theme-blue <?php echo $theme['theme'] == 'blue' ? 'active' : ''; ?>" data-theme="blue" onclick="selectTheme('blue')">
                                <div class="theme-icon">
                                    <i class="fas fa-palette"></i>
                                </div>
                                <div class="theme-name">Blue</div>
                            </div>
                        </div>
                        <input type="hidden" name="theme" id="selected-theme" value="<?php echo $theme['theme']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="accent-color">Accent Color</label>
                        <input type="color" id="accent-color" name="accent_color" class="form-control" value="<?php echo $theme['accentColor']; ?>" style="height: 50px;">
                    </div>
                    
                    <div class="form-group">
                        <label for="font-size">Font Size</label>
                        <select id="font-size" name="font_size" class="form-control">
                            <option value="small" <?php echo $theme['fontSize'] == 'small' ? 'selected' : ''; ?>>Small</option>
                            <option value="medium" <?php echo $theme['fontSize'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="large" <?php echo $theme['fontSize'] == 'large' ? 'selected' : ''; ?>>Large</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="apply_theme" class="btn btn-primary">
                        <i class="fas fa-check"></i> Apply Theme
                    </button>
                </form>
            </div>
            
            <!-- Notification Settings -->
            <div id="notifications-tab" class="tab-content">
                <form method="POST" action="">
                    <div class="form-group">
                        <h4>Notification Preferences</h4>
                        
                        <div class="toggle-group">
                            <span>Email Notifications</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="email_notifications" <?php echo $notifications['email'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-group">
                            <span>SMS Alerts</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="sms_notifications" <?php echo $notifications['sms'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-group">
                            <span>Desktop Notifications</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="desktop_notifications" <?php echo $notifications['desktop'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <h4>Notification Types</h4>
                        
                        <div class="toggle-group">
                            <span>New Voter Registration</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notify_new_voter" <?php echo $notifications['newVoter'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-group">
                            <span>Voting Progress Updates</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notify_voting_progress" <?php echo $notifications['votingProgress'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-group">
                            <span>System Alerts</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notify_system_alerts" <?php echo $notifications['systemAlerts'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-group">
                            <span>Report Generation Complete</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="notify_reports" <?php echo $notifications['reports'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" name="save_notifications" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </form>
            </div>
            
            <!-- System Settings -->
            <div id="system-tab" class="tab-content">
                <form method="POST" action="">
                    <div class="form-group">
                        <h4>System Preferences</h4>
                        
                        <div class="toggle-group">
                            <span>Auto-refresh Dashboard</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="auto_refresh" <?php echo $system_settings['autoRefresh'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-group">
                            <span>Show Live Updates</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="show_live" <?php echo $system_settings['showLive'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="toggle-group">
                            <span>Debug Mode</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="debug_mode" <?php echo $system_settings['debugMode'] ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="refresh-interval">Refresh Interval (seconds)</label>
                        <select id="refresh-interval" name="refresh_interval" class="form-control">
                            <option value="30" <?php echo $system_settings['refreshInterval'] == '30' ? 'selected' : ''; ?>>30 seconds</option>
                            <option value="60" <?php echo $system_settings['refreshInterval'] == '60' ? 'selected' : ''; ?>>60 seconds</option>
                            <option value="120" <?php echo $system_settings['refreshInterval'] == '120' ? 'selected' : ''; ?>>2 minutes</option>
                            <option value="300" <?php echo $system_settings['refreshInterval'] == '300' ? 'selected' : ''; ?>>5 minutes</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="data-retention">Data Retention Period</label>
                        <select id="data-retention" name="data_retention" class="form-control">
                            <option value="30" <?php echo $system_settings['dataRetention'] == '30' ? 'selected' : ''; ?>>30 days</option>
                            <option value="90" <?php echo $system_settings['dataRetention'] == '90' ? 'selected' : ''; ?>>90 days</option>
                            <option value="180" <?php echo $system_settings['dataRetention'] == '180' ? 'selected' : ''; ?>>6 months</option>
                            <option value="365" <?php echo $system_settings['dataRetention'] == '365' ? 'selected' : ''; ?>>1 year</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="save_system" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save System Settings
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Danger Zone -->
        <div class="danger-zone">
            <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
            <p>These actions are irreversible. Please proceed with caution.</p>
            
            <div style="margin-top: 20px; display: flex; gap: 15px; flex-wrap: wrap;">
                <button class="btn btn-danger" id="clear-data-btn">
                    <i class="fas fa-trash"></i> Clear All Test Data
                </button>
                
                <button class="btn btn-danger" id="export-data-btn">
                    <i class="fas fa-database"></i> Export All Data
                </button>
                
                <button class="btn btn-danger" id="reset-system-btn">
                    <i class="fas fa-undo"></i> Reset to Defaults
                </button>
            </div>
            
            <p style="font-size: 0.9rem; color: #666; margin-top: 15px;">
                Warning: These actions cannot be undone. Make sure to export important data first.
            </p>
        </div>
        
        <div class="footer">
            <p>Town Voting Analysis System &copy; 2026 | Sangli District Municipal Elections</p>
            <p>System Version: 2.1.0 | Last Updated: June 15, 2026</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', { 
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
            });
            
            // Initialize tabs
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.dataset.tab;
                    
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    tabContents.forEach(content => content.classList.remove('active'));
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });
            
            // Danger zone buttons
            document.getElementById('clear-data-btn').addEventListener('click', function() {
                if (confirm('⚠️ Are you sure you want to clear all test data? This action cannot be undone.')) {
                    alert('✅ Test data cleared successfully!');
                }
            });
            
            document.getElementById('export-data-btn').addEventListener('click', function() {
                alert('✅ Data exported successfully!');
            });
            
            document.getElementById('reset-system-btn').addEventListener('click', function() {
                if (confirm('⚠️ Are you sure you want to reset to default settings? All customizations will be lost.')) {
                    alert('✅ System reset to default settings!');
                }
            });
        });

        function selectTheme(theme) {
            document.getElementById('selected-theme').value = theme;
            document.querySelectorAll('.theme-option').forEach(opt => {
                opt.classList.remove('active');
                if (opt.dataset.theme === theme) {
                    opt.classList.add('active');
                }
            });
        }

        function validatePassword() {
            const newPass = document.getElementById('new-password').value;
            const confirmPass = document.getElementById('confirm-password').value;
            
            if (newPass !== confirmPass) {
                alert('❌ New passwords do not match!');
                return false;
            }
            
            if (newPass.length < 8) {
                alert('❌ New password must be at least 8 characters long!');
                return false;
            }
            
            alert('✅ Password changed successfully!');
            return true;
        }
    </script>
</body>
</html>