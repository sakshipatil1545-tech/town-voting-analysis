<?php
require_once 'config.php';
requireLogin();

// Get data for analysis
$total_voters = getTotalVoters($conn);
$total_votes = getTotalVotes($conn);

// Get candidates from database
$candidates = [];
$result = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

// If no candidates in DB, use defaults with colors
if (empty($candidates)) {
    $candidates = [
        ['id' => 1, 'name' => 'Viswajeet Kadam', 'party' => 'BJP', 'village' => 'Islampur', 'votes' => 15230, 'color' => '#3498db', 'initials' => 'VK'],
        ['id' => 2, 'name' => 'Vishal Patil', 'party' => 'Congress', 'village' => 'Palus', 'votes' => 12450, 'color' => '#e74c3c', 'initials' => 'VP'],
        ['id' => 3, 'name' => 'Jayant Patil', 'party' => 'NCP', 'village' => 'Walwa', 'votes' => 8920, 'color' => '#27ae60', 'initials' => 'JP'],
        ['id' => 4, 'name' => 'Sakshi Patil', 'party' => 'Shiv Sena', 'village' => 'Nagarale', 'votes' => 5680, 'color' => '#9b59b6', 'initials' => 'SP'],
        ['id' => 5, 'name' => 'Rohit Jadhav', 'party' => 'Independent', 'village' => 'Kundal', 'votes' => 2950, 'color' => '#f39c12', 'initials' => 'RJ']
    ];
    $total_votes = array_sum(array_column($candidates, 'votes'));
}

// Village data
$villages = [
    "Islampur" => ["voters" => 12500, "votes" => 8500, "turnout" => 68.0],
    "Nagarale" => ["voters" => 8500, "votes" => 5600, "turnout" => 65.9],
    "Palus" => ["voters" => 9200, "votes" => 6100, "turnout" => 66.3],
    "Walwa" => ["voters" => 7800, "votes" => 5200, "turnout" => 66.7],
    "Kundal" => ["voters" => 6200, "votes" => 4100, "turnout" => 66.1]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis - Town Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
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
        
        /* Analysis Stats */
        .analysis-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            border-top: 4px solid var(--secondary);
            animation: cardAppear 0.8s ease-out;
            animation-fill-mode: both;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
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
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .stat-card:nth-child(2) { border-top-color: var(--success); }
        .stat-card:nth-child(3) { border-top-color: var(--warning); }
        .stat-card:nth-child(4) { border-top-color: var(--danger); }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            animation: iconFloat 3s infinite;
        }
        
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .stat-card:nth-child(2) .stat-icon { background: linear-gradient(135deg, var(--success) 0%, #229954 100%); }
        .stat-card:nth-child(3) .stat-icon { background: linear-gradient(135deg, var(--warning) 0%, #d68910 100%); }
        .stat-card:nth-child(4) .stat-icon { background: linear-gradient(135deg, var(--danger) 0%, #c0392b 100%); }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0 5px;
            animation: countUp 1.5s ease-out;
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
            font-size: 0.9rem;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 2px solid #eef2f7;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 5px;
            animation: slideUp 0.8s ease-out 0.2s both;
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
        
        /* Cards */
        .card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
            border: 1px solid #eef2f7;
            animation: cardSlide 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes cardSlide {
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
        
        .chart-container {
            height: 400px;
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
        
        /* Comparison Grid */
        .comparison-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .comparison-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            animation: cardScale 0.8s ease-out;
            animation-fill-mode: both;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .comparison-card:nth-child(1) { animation-delay: 0.3s; }
        .comparison-card:nth-child(2) { animation-delay: 0.4s; }
        .comparison-card:nth-child(3) { animation-delay: 0.5s; }
        
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
        
        .comparison-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .comparison-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 0;
            background: var(--secondary);
            transition: height 0.3s;
        }
        
        .comparison-card:hover::before {
            height: 100%;
        }
        
        .comparison-card h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        /* Export Buttons */
        .export-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
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
            background: rgba(255,255,255,0.3);
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
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52,152,219,0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #229954 100%);
            color: white;
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
            
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                border-radius: 8px;
                margin-bottom: 5px;
            }
            
            .analysis-stats {
                grid-template-columns: 1fr;
            }
            
            .comparison-grid {
                grid-template-columns: 1fr;
            }
            
            .export-buttons {
                flex-direction: column;
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
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="analysis.php" class="nav-item active">
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
                <h1>Election Analysis 2026</h1>
                <p>Comprehensive voting data analysis</p>
            </div>
            
            <div class="date-display">
                <i class="fas fa-calendar-alt"></i>
                <span id="current-date"></span>
            </div>
        </div>
        
        <div class="analysis-stats" id="analysis-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_voters); ?></div>
                <div class="stat-label">Registered Voters</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-vote-yea"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_votes); ?></div>
                <div class="stat-label">Votes Cast</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?php echo round(($total_votes / $total_voters) * 100, 1); ?>%</div>
                <div class="stat-label">Voter Turnout</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value"><?php echo count($candidates); ?></div>
                <div class="stat-label">Candidates</div>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab active" data-tab="candidate">Candidate Analysis</div>
            <div class="tab" data-tab="village">Village Analysis</div>
            <div class="tab" data-tab="year">Year Comparison</div>
            <div class="tab" data-tab="gender">Gender Analysis</div>
            <div class="tab" data-tab="age">Age Group Analysis</div>
        </div>
        
        <!-- Candidate Analysis Tab -->
        <div id="candidate-tab" class="tab-content active">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Candidate-wise Vote Distribution</h3>
                    <div class="export-buttons">
                        <button class="btn btn-primary" onclick="exportChart('candidate', 'Candidate Vote Distribution')">
                            <i class="fas fa-download"></i> Export as PNG
                        </button>
                        <button class="btn btn-success" onclick="exportChartData('candidate')">
                            <i class="fas fa-file-csv"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="candidateChart"></canvas>
                </div>
            </div>
            
            <div class="comparison-grid" id="candidate-comparison">
                <?php
                $sorted = $candidates;
                usort($sorted, function($a, $b) {
                    return ($b['votes'] ?? 0) - ($a['votes'] ?? 0);
                });
                $top = $sorted[0] ?? null;
                $second = $sorted[1] ?? null;
                $vote_margin = $top ? ($top['votes'] ?? 0) - ($second['votes'] ?? 0) : 0;
                $growth = round(($total_votes / 40230) * 100 - 100, 1);
                ?>
                <div class="comparison-card">
                    <h3><i class="fas fa-crown"></i> Top Performer</h3>
                    <p style="font-size: 1.5rem; color: var(--primary); font-weight: bold;"><?php echo htmlspecialchars($top['name'] ?? 'N/A'); ?></p>
                    <p><?php echo htmlspecialchars($top['party'] ?? ''); ?> • <?php echo number_format($top['votes'] ?? 0); ?> votes</p>
                    <p style="color: var(--success); margin-top: 10px;"><?php echo round(($top['votes'] ?? 0) / $total_votes * 100, 1); ?>% of total votes</p>
                </div>
                
                <div class="comparison-card">
                    <h3><i class="fas fa-chart-line"></i> Growth Rate</h3>
                    <p style="font-size: 1.5rem; color: var(--primary); font-weight: bold;">+<?php echo $growth; ?>%</p>
                    <p>Voter registration increase from last election</p>
                    <p style="color: <?php echo $growth > 0 ? 'var(--success)' : 'var(--danger)'; ?>; margin-top: 10px;"><?php echo $growth > 0 ? 'Positive trend' : 'Negative trend'; ?></p>
                </div>
                
                <div class="comparison-card">
                    <h3><i class="fas fa-balance-scale"></i> Vote Margin</h3>
                    <p style="font-size: 1.5rem; color: var(--primary); font-weight: bold;"><?php echo number_format($vote_margin); ?></p>
                    <p>Difference between 1st and 2nd candidate</p>
                    <p style="color: <?php echo $vote_margin < 1000 ? 'var(--danger)' : ($vote_margin < 3000 ? 'var(--warning)' : 'var(--success)'); ?>; margin-top: 10px;">
                        <?php echo $vote_margin < 1000 ? 'Very close race' : ($vote_margin < 3000 ? 'Competitive race' : 'Comfortable lead'); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Village Analysis Tab -->
        <div id="village-tab" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Village-wise Voter Turnout</h3>
                    <div class="export-buttons">
                        <button class="btn btn-primary" onclick="exportChart('village', 'Village Turnout Analysis')">
                            <i class="fas fa-download"></i> Export as PNG
                        </button>
                        <button class="btn btn-success" onclick="exportChartData('village')">
                            <i class="fas fa-file-csv"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="villageChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Year Comparison Tab -->
        <div id="year-tab" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Year-wise Voting Comparison</h3>
                    <div class="export-buttons">
                        <button class="btn btn-primary" onclick="exportChart('year', 'Yearly Voting Comparison')">
                            <i class="fas fa-download"></i> Export as PNG
                        </button>
                        <button class="btn btn-success" onclick="exportChartData('year')">
                            <i class="fas fa-file-csv"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="yearChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Gender Analysis Tab -->
        <div id="gender-tab" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gender-wise Voting Pattern</h3>
                    <div class="export-buttons">
                        <button class="btn btn-primary" onclick="exportChart('gender', 'Gender Voting Pattern')">
                            <i class="fas fa-download"></i> Export as PNG
                        </button>
                        <button class="btn btn-success" onclick="exportChartData('gender')">
                            <i class="fas fa-file-csv"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Age Group Analysis Tab -->
        <div id="age-tab" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Age Group Voting Analysis</h3>
                    <div class="export-buttons">
                        <button class="btn btn-primary" onclick="exportChart('age', 'Age Group Analysis')">
                            <i class="fas fa-download"></i> Export as PNG
                        </button>
                        <button class="btn btn-success" onclick="exportChartData('age')">
                            <i class="fas fa-file-csv"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Town Voting Analysis System &copy; 2026 | Sangli District Municipal Elections</p>
            <p>Comprehensive analysis of 2026 election data with historical comparisons</p>
        </div>
    </div>

    <script>
        // Global data store
        let appData = {
            totalVoters: <?php echo $total_voters; ?>,
            votesCast: <?php echo $total_votes; ?>,
            candidates: <?php echo json_encode($candidates); ?>,
            villages: <?php echo json_encode($villages); ?>
        };

        let charts = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Set current date
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
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
                    
                    const activeContent = document.getElementById(`${tabId}-tab`);
                    const chartContainer = activeContent.querySelector('.chart-container');
                    if (chartContainer) {
                        chartContainer.classList.add('chart-loading');
                        setTimeout(() => {
                            chartContainer.classList.remove('chart-loading');
                        }, 1000);
                    }
                    
                    if (tabId === 'candidate' && !charts.candidateChart) {
                        setTimeout(() => initCandidateChart(), 500);
                    } else if (tabId === 'village' && !charts.villageChart) {
                        setTimeout(() => initVillageChart(), 500);
                    } else if (tabId === 'year' && !charts.yearChart) {
                        setTimeout(() => initYearChart(), 500);
                    } else if (tabId === 'gender' && !charts.genderChart) {
                        setTimeout(() => initGenderChart(), 500);
                    } else if (tabId === 'age' && !charts.ageChart) {
                        setTimeout(() => initAgeChart(), 500);
                    }
                });
            });
            
            // Initialize first chart with delay
            setTimeout(() => initCandidateChart(), 500);
        });

        function initCandidateChart() {
            const ctx = document.getElementById('candidateChart').getContext('2d');
            
            if (charts.candidateChart) {
                charts.candidateChart.destroy();
            }
            
            // Generate colors for candidates
            const colors = ['#3498db', '#e74c3c', '#27ae60', '#9b59b6', '#f39c12', '#1abc9c', '#e67e22', '#34495e'];
            
            charts.candidateChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: appData.candidates.map(c => c.name),
                    datasets: [{
                        data: appData.candidates.map(c => parseInt(c.votes) || 0),
                        backgroundColor: appData.candidates.map((c, i) => c.color || colors[i % colors.length]),
                        borderWidth: 2,
                        borderColor: 'white',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { size: 12, weight: 'bold' },
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const percentage = ((value / appData.votesCast) * 100).toFixed(1);
                                    return `${label}: ${value.toLocaleString()} votes (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        function initVillageChart() {
            const ctx = document.getElementById('villageChart').getContext('2d');
            
            if (charts.villageChart) {
                charts.villageChart.destroy();
            }
            
            charts.villageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(appData.villages),
                    datasets: [{
                        label: 'Voter Turnout %',
                        data: Object.values(appData.villages).map(v => parseFloat(v.turnout)),
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: 'rgb(52, 152, 219)',
                        borderWidth: 2,
                        borderRadius: 8,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                            title: {
                                display: true,
                                text: 'Turnout Percentage (%)',
                                font: { weight: 'bold', size: 12 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            title: {
                                display: true,
                                text: 'Villages',
                                font: { weight: 'bold', size: 12 }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Turnout: ${context.raw}%`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function initYearChart() {
            const ctx = document.getElementById('yearChart').getContext('2d');
            
            if (charts.yearChart) {
                charts.yearChart.destroy();
            }
            
            charts.yearChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['2017', '2020', '2023', '2026'],
                    datasets: [{
                        label: 'Voter Turnout %',
                        data: [62.5, 65.2, 68.7, (appData.votesCast / appData.totalVoters * 100).toFixed(1)],
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#3498db',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: {
                                display: true,
                                text: 'Turnout Percentage (%)',
                                font: { weight: 'bold' }
                            }
                        },
                        x: {
                            grid: { display: false },
                            title: {
                                display: true,
                                text: 'Election Year',
                                font: { weight: 'bold' }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        function initGenderChart() {
            const ctx = document.getElementById('genderChart').getContext('2d');
            
            if (charts.genderChart) {
                charts.genderChart.destroy();
            }
            
            const maleVotes = Math.round(appData.votesCast * 0.54);
            const femaleVotes = Math.round(appData.votesCast * 0.453);
            const otherVotes = appData.votesCast - maleVotes - femaleVotes;
            
            charts.genderChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Male', 'Female', 'Other'],
                    datasets: [{
                        data: [maleVotes, femaleVotes, otherVotes],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(231, 76, 60, 0.8)',
                            'rgba(46, 204, 113, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: 'white',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { size: 12, weight: 'bold' },
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const percentage = ((value / appData.votesCast) * 100).toFixed(1);
                                    return `${context.label}: ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        function initAgeChart() {
            const ctx = document.getElementById('ageChart').getContext('2d');
            
            if (charts.ageChart) {
                charts.ageChart.destroy();
            }
            
            const ageGroups = {
                '18-25': { registered: 12500, voted: Math.round(appData.votesCast * 0.18) },
                '26-35': { registered: 18500, voted: Math.round(appData.votesCast * 0.28) },
                '36-50': { registered: 27500, voted: Math.round(appData.votesCast * 0.41) },
                '51-65': { registered: 22500, voted: Math.round(appData.votesCast * 0.32) },
                '65+': { registered: 8500, voted: Math.round(appData.votesCast * 0.11) }
            };
            
            // Adjust to match total
            const totalVoted = Object.values(ageGroups).reduce((sum, g) => sum + g.voted, 0);
            const diff = appData.votesCast - totalVoted;
            ageGroups['36-50'].voted += diff;
            
            charts.ageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(ageGroups),
                    datasets: [
                        {
                            label: 'Voted (2026)',
                            data: Object.values(ageGroups).map(g => g.voted),
                            backgroundColor: 'rgba(52, 152, 219, 0.8)',
                            borderColor: 'rgb(52, 152, 219)',
                            borderWidth: 2,
                            borderRadius: 8,
                            barPercentage: 0.7
                        },
                        {
                            label: 'Registered',
                            data: Object.values(ageGroups).map(g => g.registered),
                            backgroundColor: 'rgba(149, 165, 166, 0.5)',
                            borderColor: 'rgb(149, 165, 166)',
                            borderWidth: 2,
                            borderRadius: 8,
                            barPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: {
                                display: true,
                                text: 'Number of Voters',
                                font: { weight: 'bold' }
                            }
                        },
                        x: {
                            grid: { display: false },
                            title: {
                                display: true,
                                text: 'Age Groups',
                                font: { weight: 'bold' }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    return `${context.dataset.label}: ${value.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Function to export chart as PNG
        function exportChart(chartType, title) {
            const chartId = chartType + 'Chart';
            const chart = charts[chartId];
            
            if (!chart) {
                alert('Chart not found!');
                return;
            }
            
            // Get the canvas element
            const canvas = document.getElementById(chartId);
            
            // Create a temporary link to download the image
            const link = document.createElement('a');
            link.download = `${title.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
            
            // Show success message
            alert(`✅ Chart exported as PNG successfully!\n\nFile: ${link.download}`);
        }

        // Function to export chart data as CSV
        function exportChartData(chartType) {
            let data = [];
            let filename = '';
            
            switch(chartType) {
                case 'candidate':
                    data = appData.candidates.map(c => ({
                        'Candidate': c.name,
                        'Party': c.party,
                        'Village': c.village,
                        'Votes': c.votes,
                        'Percentage': ((c.votes / appData.votesCast) * 100).toFixed(2) + '%'
                    }));
                    filename = 'candidate_vote_distribution';
                    break;
                    
                case 'village':
                    data = Object.keys(appData.villages).map(v => ({
                        'Village': v,
                        'Registered Voters': appData.villages[v].voters,
                        'Votes Cast': appData.villages[v].votes,
                        'Turnout %': appData.villages[v].turnout
                    }));
                    filename = 'village_turnout_analysis';
                    break;
                    
                case 'year':
                    data = [
                        { 'Year': '2017', 'Turnout %': 62.5 },
                        { 'Year': '2020', 'Turnout %': 65.2 },
                        { 'Year': '2023', 'Turnout %': 68.7 },
                        { 'Year': '2026', 'Turnout %': (appData.votesCast / appData.totalVoters * 100).toFixed(1) }
                    ];
                    filename = 'yearly_comparison';
                    break;
                    
                case 'gender':
                    const maleVotes = Math.round(appData.votesCast * 0.54);
                    const femaleVotes = Math.round(appData.votesCast * 0.453);
                    const otherVotes = appData.votesCast - maleVotes - femaleVotes;
                    data = [
                        { 'Gender': 'Male', 'Votes': maleVotes, 'Percentage': '54.0%' },
                        { 'Gender': 'Female', 'Votes': femaleVotes, 'Percentage': '45.3%' },
                        { 'Gender': 'Other', 'Votes': otherVotes, 'Percentage': '0.7%' }
                    ];
                    filename = 'gender_analysis';
                    break;
                    
                case 'age':
                    const ageGroups = {
                        '18-25': Math.round(appData.votesCast * 0.18),
                        '26-35': Math.round(appData.votesCast * 0.28),
                        '36-50': Math.round(appData.votesCast * 0.41),
                        '51-65': Math.round(appData.votesCast * 0.32),
                        '65+': Math.round(appData.votesCast * 0.11)
                    };
                    // Adjust to match total
                    const total = Object.values(ageGroups).reduce((a, b) => a + b, 0);
                    const diff = appData.votesCast - total;
                    ageGroups['36-50'] += diff;
                    
                    data = Object.keys(ageGroups).map(age => ({
                        'Age Group': age,
                        'Votes': ageGroups[age],
                        'Percentage': ((ageGroups[age] / appData.votesCast) * 100).toFixed(1) + '%'
                    }));
                    filename = 'age_group_analysis';
                    break;
            }
            
            if (data.length === 0) {
                alert('No data to export!');
                return;
            }
            
            // Convert to CSV
            const headers = Object.keys(data[0]);
            const csvRows = [];
            
            // Add headers
            csvRows.push(headers.join(','));
            
            // Add data rows
            for (const row of data) {
                const values = headers.map(header => {
                    const val = row[header];
                    return typeof val === 'string' && val.includes(',') ? `"${val}"` : val;
                });
                csvRows.push(values.join(','));
            }
            
            const csvString = csvRows.join('\n');
            
            // Create download link
            const blob = new Blob([csvString], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `${filename}_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
            
            window.URL.revokeObjectURL(url);
            
            alert(`✅ Data exported as CSV successfully!\n\nFile: ${link.download}`);
        }
    </script>
</body>
</html>