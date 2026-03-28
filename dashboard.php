<?php
require_once 'config.php';
requireLogin();

// Get statistics directly from database
$total_voters = getTotalVoters($conn);
$total_votes = getTotalVotes($conn);

// Get candidates
$candidates = [];
$result = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

// If no candidates in DB, use defaults
if (empty($candidates)) {
    $candidates = [
        ['id' => 1, 'name' => 'Viswajeet Kadam', 'party' => 'BJP', 'village' => 'Islampur', 'votes' => 15230, 'color' => '#3498db', 'initials' => 'Viswajeet Kadam'],
        ['id' => 2, 'name' => 'Vishal Patil', 'party' => 'Congress', 'village' => 'Palus', 'votes' => 12450, 'color' => '#e74c3c', 'initials' => 'Vishal Patil'],
        ['id' => 3, 'name' => 'Jayant Patil', 'party' => 'NCP', 'village' => 'Walwa', 'votes' => 8920, 'color' => '#27ae60', 'initials' => 'Jayant Patil'],
        ['id' => 4, 'name' => 'Sakshi Patil', 'party' => 'Shiv Sena', 'village' => 'Nagarale', 'votes' => 5680, 'color' => '#9b59b6', 'initials' => 'Sakshi Patil'],
        ['id' => 5, 'name' => 'Rohit Jadhav', 'party' => 'Independent', 'village' => 'Kundal', 'votes' => 2950, 'color' => '#f39c12', 'initials' => 'Rohit Jadhav']
    ];
    $total_votes = array_sum(array_column($candidates, 'votes'));
}

$turnout = round(($total_votes / $total_voters) * 100, 1);
$leading = $candidates[0] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Town Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
            --sidebar-width: 260px;
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
        
        /* Vertical Navbar */
        .vertical-nav {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary) 0%, #1a252f 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
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
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            background: rgba(0,0,0,0.2);
        }
        
        .nav-brand i {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 10px;
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .nav-brand span {
            display: block;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .nav-brand small {
            font-size: 0.85rem;
            opacity: 0.7;
        }
        
        .nav-menu {
            padding: 20px 0;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            margin: 4px 10px;
            border-radius: 8px;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .nav-item:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .nav-item:hover {
            color: white;
            background: rgba(52,152,219,0.2);
            transform: translateX(5px);
        }
        
        .nav-item.active {
            background: var(--secondary);
            color: white;
            box-shadow: 0 4px 15px rgba(52,152,219,0.4);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 4px 15px rgba(52,152,219,0.4); }
            50% { box-shadow: 0 4px 25px rgba(52,152,219,0.8); }
        }
        
        .nav-item i {
            width: 24px;
            font-size: 1.2rem;
        }
        
        .nav-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .user-details h4 {
            font-size: 0.95rem;
            margin-bottom: 3px;
        }
        
        .user-details p {
            font-size: 0.8rem;
            opacity: 0.7;
        }
        
        .logout-btn {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
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
            background: rgba(231,76,60,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .logout-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .logout-btn:hover {
            background: var(--danger);
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
            from { opacity: 0; }
            to { opacity: 1; }
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
            display: inline-block;
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
            color: var(--gray);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .date-display i {
            color: var(--secondary);
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border-left: 5px solid var(--secondary);
            transition: all 0.5s;
            animation: cardAppear 0.8s ease-out;
            animation-fill-mode: both;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        
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
        
        .stat-card::before {
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
        
        .stat-card:hover::before {
            opacity: 1;
        }
        
        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card:nth-child(2) { border-left-color: var(--success); }
        .stat-card:nth-child(3) { border-left-color: var(--warning); }
        .stat-card:nth-child(4) { border-left-color: var(--danger); }
        
        .stat-value {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary);
            margin: 15px 0 5px;
            line-height: 1;
            animation: countUp 2s ease-out;
        }
        
        @keyframes countUp {
            from {
                transform: scale(0.5);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-bottom: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            animation: iconFloat 3s infinite;
        }
        
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .icon-voters { background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%); }
        .icon-votes { background: linear-gradient(135deg, var(--success) 0%, #229954 100%); }
        .icon-percentage { background: linear-gradient(135deg, var(--warning) 0%, #d68910 100%); }
        .icon-leading { background: linear-gradient(135deg, var(--danger) 0%, #c0392b 100%); }
        
        /* Dashboard Charts */
        .dashboard-charts {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-top: 25px;
        }
        
        @media (max-width: 1200px) {
            .dashboard-charts {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
            border: 1px solid #eef2f7;
            animation: slideIn 0.8s ease-out;
            animation-fill-mode: both;
            position: relative;
            overflow: hidden;
        }
        
        .card:nth-child(1) { animation-delay: 0.5s; }
        .card:nth-child(2) { animation-delay: 0.6s; }
        
        @keyframes slideIn {
            from {
                transform: translateX(-30px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(52,152,219,0.05) 0%, transparent 70%);
            transition: all 0.5s;
        }
        
        .card:hover::after {
            transform: scale(1.5);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f4f8;
        }
        
        .card-title {
            font-size: 1.3rem;
            color: var(--primary);
            font-weight: 600;
            position: relative;
        }
        
        .card-title::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--secondary);
            animation: blink 2s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            animation: badgePulse 2s infinite;
        }
        
        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .badge-success { background-color: #d4f7e2; color: #0a5c36; }
        .badge-primary { background-color: #d1e8ff; color: #0a4b8c; }
        
        .chart-container {
            height: 320px;
            margin-top: 20px;
            animation: chartFade 1.5s ease-out;
        }
        
        @keyframes chartFade {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Leading Candidate Card */
        .leading-candidate-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--secondary);
            display: flex;
            align-items: center;
            gap: 20px;
            animation: glowPulse 3s infinite;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 8px 20px rgba(52,152,219,0.2); }
            50% { box-shadow: 0 8px 30px rgba(52,152,219,0.4); }
        }
        
        .leading-candidate-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(52,152,219,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        .candidate-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            box-shadow: 0 6px 12px rgba(52,152,219,0.3);
            animation: avatarFloat 3s infinite;
        }
        
        @keyframes avatarFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-5px) rotate(5deg); }
        }
        
        .candidate-info h3 {
            color: var(--primary);
            margin-bottom: 5px;
            font-size: 1.4rem;
        }
        
        .candidate-info p {
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .candidate-votes {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        /* Tables */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #eef2f7;
            animation: tableAppear 1s ease-out;
        }
        
        @keyframes tableAppear {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #eef2f7;
            transition: all 0.3s;
        }
        
        .data-table th {
            background-color: #f8fafc;
            color: var(--primary);
            font-weight: 600;
            white-space: nowrap;
        }
        
        .data-table tr {
            transition: all 0.3s;
        }
        
        .data-table tr:hover {
            background-color: #f0f9ff;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .data-table td {
            position: relative;
        }
        
        .data-table td::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--secondary);
            transform: scaleY(0);
            transition: transform 0.3s;
        }
        
        .data-table tr:hover td::before {
            transform: scaleY(1);
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
            border-radius: 10px;
            animation: fadeIn 1.5s ease-out;
        }
        
        @media (max-width: 768px) {
            .vertical-nav {
                width: 70px;
            }
            
            .nav-brand span, .nav-brand small, .nav-item span, .user-details, .logout-btn span {
                display: none;
            }
            
            .nav-item {
                justify-content: center;
                padding: 14px 0;
            }
            
            .nav-item i {
                width: auto;
                font-size: 1.4rem;
            }
            
            .user-info {
                justify-content: center;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        /* Loading animation for charts */
        .chart-loading {
            position: relative;
        }
        
        .chart-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--secondary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Vertical Navbar -->
    <div class="vertical-nav">
        <div class="nav-brand">
            <i class="fas fa-vote-yea"></i>
            <span>TownVote 2026</span>
            <small>Sangli District</small>
        </div>
        
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item active">
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
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="nav-footer">
            <div class="user-info">
                <div class="user-avatar" id="user-avatar"><?php echo substr($_SESSION['user_name'] ?? 'JD', 0, 2); ?></div>
                <div class="user-details">
                    <h4 id="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'John Doe'); ?></h4>
                    <p id="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Election Officer'); ?></p>
                </div>
            </div>
            <a href="logout.php" class="logout-btn" id="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Dashboard</h1>
                <p id="page-subtitle">Election 2026 - Real-time Overview</p>
            </div>
            
            <div class="date-display">
                <i class="fas fa-calendar-alt"></i>
                <span id="current-date"></span>
            </div>
        </div>
        
        <div class="stats-container" id="stats-container">
            <div class="stat-card">
                <div class="stat-icon icon-voters">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_voters); ?></div>
                <div class="stat-label">Total Registered Voters</div>
                <div style="font-size: 0.9rem; color: #27ae60; margin-top: 5px;">
                    <i class="fas fa-arrow-up"></i> 12.5% from last election
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon icon-votes">
                    <i class="fas fa-vote-yea"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_votes); ?></div>
                <div class="stat-label">Votes Cast (2026)</div>
                <div style="font-size: 0.9rem; color: #27ae60; margin-top: 5px;">
                    <i class="fas fa-check-circle"></i> <?php echo $turnout; ?>% Turnout
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon icon-percentage">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?php echo $turnout; ?>%</div>
                <div class="stat-label">Current Turnout</div>
                <div style="font-size: 0.9rem; color: #e74c3c; margin-top: 5px;">
                    <i class="fas fa-arrow-down"></i> 2.6% from last election
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon icon-leading">
                    <i class="fas fa-trophy"></i>
                </div>
                <!-- Changed: Display full candidate name instead of initials -->
                <div class="stat-value"><?php echo $leading ? $leading['name'] : 'N/A'; ?></div>
                <div class="stat-label">Leading Candidate</div>
                <div style="font-size: 0.9rem; color: #f39c12; margin-top: 5px;">
                    <?php echo $leading ? round(($leading['votes'] ?? 0) / $total_votes * 100, 1) : '0'; ?>% of total votes
                </div>
            </div>
        </div>
        
        <div class="dashboard-charts">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Voting Progress - Election Day 2026</h3>
                    <span class="badge badge-primary">Live Data</span>
                </div>
                <div class="chart-container">
                    <canvas id="dashboardChart"></canvas>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Candidates</h3>
                    <span class="badge badge-success">Ranking</span>
                </div>
                <div id="leading-candidate-container">
                    <?php if ($leading): ?>
                    <div class="leading-candidate-card">
                        <!-- Changed: Show full name in avatar as well -->
                        <div class="candidate-avatar"><?php echo substr($leading['name'], 0, 2); ?></div>
                        <div class="candidate-info">
                            <h3><?php echo htmlspecialchars($leading['name']); ?></h3>
                            <p><?php echo htmlspecialchars($leading['party']); ?> • <?php echo htmlspecialchars($leading['village']); ?></p>
                            <div class="candidate-votes"><?php echo number_format($leading['votes'] ?? 0); ?></div>
                            <div style="font-size: 0.9rem; color: #666;"><?php echo round(($leading['votes'] ?? 0) / $total_votes * 100, 1); ?>% of votes</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 20px;" id="candidates-table-container">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Candidate</th>
                                    <th>Party</th>
                                    <th>Village</th>
                                    <th>Votes</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidates as $candidate): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['party']); ?></td>
                                    <td><?php echo htmlspecialchars($candidate['village']); ?></td>
                                    <td><?php echo number_format($candidate['votes'] ?? 0); ?></td>
                                    <td><?php echo round(($candidate['votes'] ?? 0) / $total_votes * 100, 1); ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Town Voting Analysis System &copy; 2026 | Sangli District Municipal Elections</p>
            <p>This is a demonstration system with sample data for 2026 elections</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set current date
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            // Initialize chart
            setTimeout(() => {
                initDashboardChart();
            }, 500);
        });

        function initDashboardChart() {
            const ctx = document.getElementById('dashboardChart').getContext('2d');
            const votesCast = <?php echo $total_votes; ?>;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['8:00 AM', '10:00 AM', '12:00 PM', '2:00 PM', '4:00 PM', '6:00 PM'],
                    datasets: [{
                        label: 'Votes Cast (in thousands)',
                        data: [8, 15, 22, 30, 38, votesCast / 1000],
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3498db',
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        borderDash: [5, 5]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { font: { size: 12, weight: 'bold' } }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Votes (in thousands)',
                                font: { weight: 'bold' }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time of Day (2026 Election)',
                                font: { weight: 'bold' }
                            },
                            grid: { display: false }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }
    </script>
</body>
</html>