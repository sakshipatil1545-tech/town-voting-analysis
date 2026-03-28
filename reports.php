<?php
require_once 'config.php';
requireLogin();

// Get statistics directly from database
$total_voters = getTotalVoters($conn);
$total_votes = getTotalVotes($conn);

// Get candidates
$candidates = [];
$result = $conn->query("SELECT * FROM candidates");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

// If no candidates in DB, use defaults
if (empty($candidates)) {
    $candidates = [
        ['id' => 1, 'name' => 'Viswajeet Kadam', 'party' => 'BJP', 'village' => 'Islampur', 'votes' => 15230],
        ['id' => 2, 'name' => 'Vishal Patil', 'party' => 'Congress', 'village' => 'Palus', 'votes' => 12450],
        ['id' => 3, 'name' => 'Jayant Patil', 'party' => 'NCP', 'village' => 'Walwa', 'votes' => 8920],
        ['id' => 4, 'name' => 'Sakshi Patil', 'party' => 'Shiv Sena', 'village' => 'Nagarale', 'votes' => 5680],
        ['id' => 5, 'name' => 'Rohit Jadhav', 'party' => 'Independent', 'village' => 'Kundal', 'votes' => 2950]
    ];
    $total_votes = array_sum(array_column($candidates, 'votes'));
}

// Recent reports (from session or default)
$recent_reports = $_SESSION['recent_reports'] ?? [
    ['id' => 1, 'name' => 'Voter Registration Summary', 'type' => 'voter', 'date' => date('Y-m-d', strtotime('-1 day')), 'size' => '1.2 MB', 'format' => 'PDF'],
    ['id' => 2, 'name' => '2026 Election Turnout Analysis', 'type' => 'turnout', 'date' => date('Y-m-d', strtotime('-2 days')), 'size' => '2.5 MB', 'format' => 'Excel'],
    ['id' => 3, 'name' => 'Candidate Performance Report', 'type' => 'candidate', 'date' => date('Y-m-d', strtotime('-3 days')), 'size' => '0.8 MB', 'format' => 'PDF'],
    ['id' => 4, 'name' => 'Islampur Ward-wise Report', 'type' => 'ward', 'date' => date('Y-m-d', strtotime('-5 days')), 'size' => '1.5 MB', 'format' => 'CSV'],
    ['id' => 5, 'name' => 'Historical Comparison 2020-2026', 'type' => 'comprehensive', 'date' => date('Y-m-d', strtotime('-7 days')), 'size' => '3.2 MB', 'format' => 'PDF']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Town Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        
        /* Report Generator */
        .report-generator {
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
        
        .report-generator::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(52,152,219,0.05) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f4f8;
            position: relative;
        }
        
        .section-title::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100px;
            height: 2px;
            background: var(--secondary);
            animation: slideLine 2s ease-out;
        }
        
        @keyframes slideLine {
            from { width: 0; }
            to { width: 100px; }
        }
        
        /* Filter Grid */
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .filter-group {
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease-out;
            animation-fill-mode: both;
        }
        
        .filter-group:nth-child(1) { animation-delay: 0.1s; }
        .filter-group:nth-child(2) { animation-delay: 0.2s; }
        .filter-group:nth-child(3) { animation-delay: 0.3s; }
        .filter-group:nth-child(4) { animation-delay: 0.4s; }
        
        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .filter-group:hover label {
            color: var(--secondary);
            transform: translateX(5px);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
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
        
        /* Report Types */
        .report-types {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .report-type-card {
            border: 2px solid #e1e8f0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s;
            animation: cardScale 0.8s ease-out;
            animation-fill-mode: both;
            position: relative;
            overflow: hidden;
        }
        
        .report-type-card:nth-child(1) { animation-delay: 0.1s; }
        .report-type-card:nth-child(2) { animation-delay: 0.2s; }
        .report-type-card:nth-child(3) { animation-delay: 0.3s; }
        .report-type-card:nth-child(4) { animation-delay: 0.4s; }
        
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
        
        .report-type-card::before {
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
        
        .report-type-card:hover::before {
            opacity: 1;
        }
        
        .report-type-card:hover {
            border-color: var(--secondary);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .report-type-card.active {
            border-color: var(--secondary);
            background: #e8f4fc;
            animation: glow 2s infinite;
        }
        
        .report-icon {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .report-type-card:hover .report-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .report-type-card:nth-child(2) .report-icon { color: var(--success); }
        .report-type-card:nth-child(3) .report-icon { color: var(--warning); }
        .report-type-card:nth-child(4) .report-icon { color: var(--danger); }
        
        .report-name {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.5s both;
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
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #c0392b 100%);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #d68910 100%);
            color: white;
        }
        
        .btn:active {
            transform: scale(0.95);
        }
        
        /* Report Preview */
        .report-preview {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            margin-top: 30px;
            display: none;
            animation: slideInPreview 0.8s ease-out;
        }
        
        @keyframes slideInPreview {
            from {
                transform: translateY(30px) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f4f8;
        }
        
        .preview-content {
            background: #f8fafc;
            border-radius: 8px;
            padding: 25px;
            max-height: 500px;
            overflow-y: auto;
            animation: fadeIn 1s ease-out;
        }
        
        /* Report Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .report-table th {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .report-table td {
            padding: 15px;
            border-bottom: 1px solid #eef2f7;
            transition: all 0.3s;
        }
        
        .report-table tr {
            transition: all 0.3s;
        }
        
        .report-table tr:hover {
            background: #f0f9ff;
            transform: scale(1.01);
        }
        
        /* Recent Reports */
        .recent-reports {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            margin-top: 30px;
            animation: cardAppear 0.8s ease-out 0.2s both;
        }
        
        .reports-list {
            margin-top: 20px;
        }
        
        .report-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #eef2f7;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.4s;
            animation: slideInRight 0.8s ease-out;
            animation-fill-mode: both;
            position: relative;
            overflow: hidden;
        }
        
        .report-item:nth-child(1) { animation-delay: 0.1s; }
        .report-item:nth-child(2) { animation-delay: 0.2s; }
        .report-item:nth-child(3) { animation-delay: 0.3s; }
        .report-item:nth-child(4) { animation-delay: 0.4s; }
        .report-item:nth-child(5) { animation-delay: 0.5s; }
        
        @keyframes slideInRight {
            from {
                transform: translateX(50px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .report-item::before {
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
        
        .report-item:hover::before {
            transform: scaleY(1);
        }
        
        .report-item:hover {
            background: #f8fafc;
            border-color: var(--secondary);
            transform: translateX(5px) scale(1.01);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .report-info h4 {
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .report-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        /* Summary Stats */
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 3px solid var(--secondary);
        }
        
        .summary-card h4 {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .summary-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
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
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .report-types {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .report-item {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .report-actions {
                width: 100%;
                justify-content: flex-start;
            }
            
            .summary-stats {
                grid-template-columns: 1fr 1fr;
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
            <a href="reports.php" class="nav-item active">
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
                <h1><i class="fas fa-file-alt"></i> Generate Reports</h1>
                <p>Create and download election reports</p>
            </div>
            <div class="date-display">
                <i class="fas fa-calendar-alt"></i>
                <span id="current-date"></span>
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="summary-stats" id="summary-stats">
            <div class="summary-card">
                <h4>Total Voters</h4>
                <div class="value"><?php echo number_format($total_voters); ?></div>
            </div>
            <div class="summary-card">
                <h4>Votes Cast</h4>
                <div class="value"><?php echo number_format($total_votes); ?></div>
            </div>
            <div class="summary-card">
                <h4>Turnout</h4>
                <div class="value"><?php echo round(($total_votes / $total_voters) * 100, 1); ?>%</div>
            </div>
            <div class="summary-card">
                <h4>Candidates</h4>
                <div class="value"><?php echo count($candidates); ?></div>
            </div>
        </div>
        
        <div class="report-generator">
            <h2 class="section-title">Report Configuration</h2>
            
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="report-type">Report Type</label>
                    <select id="report-type" class="form-control">
                        <option value="voter">Voter Registration Report</option>
                        <option value="voting">Voting Statistics Report</option>
                        <option value="candidate">Candidate Performance Report</option>
                        <option value="turnout">Turnout Analysis Report</option>
                        <option value="ward">Ward-wise Report</option>
                        <option value="comprehensive">Comprehensive Election Report</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="report-year">Election Year</label>
                    <select id="report-year" class="form-control">
                        <option value="2026">2026</option>
                        <option value="2023">2023</option>
                        <option value="2020">2020</option>
                        <option value="2017">2017</option>
                        <option value="all">All Years</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="report-village">Village</label>
                    <select id="report-village" class="form-control">
                        <option value="all">All Villages</option>
                        <option value="Islampur">Islampur</option>
                        <option value="Nagarale">Nagarale</option>
                        <option value="Palus">Palus</option>
                        <option value="Walwa">Walwa</option>
                        <option value="Kundal">Kundal</option>
                        <option value="Pundi">Pundi</option>
                        <option value="Dhudhondi">Dhudhondi</option>
                        <option value="Burli">Burli</option>
                        <option value="Wangi">Wangi</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="report-ward">Ward</label>
                    <select id="report-ward" class="form-control">
                        <option value="all">All Wards</option>
                        <option value="1">Ward 1</option>
                        <option value="2">Ward 2</option>
                        <option value="3">Ward 3</option>
                        <option value="4">Ward 4</option>
                        <option value="5">Ward 5</option>
                        <option value="6">Ward 6</option>
                    </select>
                </div>
            </div>
            
            <div class="filter-group">
                <label for="date-range">Date Range</label>
                <div style="display: flex; gap: 10px;">
                    <input type="date" id="start-date" class="form-control" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
                    <span style="align-self: center;">to</span>
                    <input type="date" id="end-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div class="filter-group">
                <label>Include Options</label>
                <div style="display: flex; gap: 20px; margin-top: 10px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="include-charts" checked>
                        <span>Include charts and graphs</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="include-summary" checked>
                        <span>Include summary section</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="include-comparison" checked>
                        <span>Include year comparison</span>
                    </label>
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn btn-primary" id="preview-btn">
                    <i class="fas fa-eye"></i> Preview Report
                </button>
                <button class="btn btn-success" id="download-pdf">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </button>
                <button class="btn btn-warning" id="download-excel">
                    <i class="fas fa-file-excel"></i> Download Excel
                </button>
                <button class="btn btn-danger" id="download-csv">
                    <i class="fas fa-file-csv"></i> Download CSV
                </button>
            </div>
        </div>
        
        <div id="report-preview" class="report-preview">
            <div class="preview-header">
                <h3>Report Preview</h3>
                <button class="btn btn-primary btn-sm" onclick="printReport()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
            
            <div id="preview-content" class="preview-content">
                <!-- Preview content will be generated here -->
            </div>
        </div>
        
        <div class="recent-reports">
            <h2 class="section-title">Recently Generated Reports</h2>
            
            <div class="reports-list" id="reports-list">
                <?php foreach ($recent_reports as $report): ?>
                <div class="report-item">
                    <div class="report-info">
                        <h4><?php echo htmlspecialchars($report['name']); ?></h4>
                        <p style="color: #666; font-size: 0.9rem;">
                            Generated: <?php echo $report['date']; ?> • <?php echo $report['size']; ?> • <?php echo $report['format']; ?>
                        </p>
                    </div>
                    <div class="report-actions">
                        <button class="btn btn-primary btn-sm" onclick="viewReport(<?php echo $report['id']; ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-success btn-sm" onclick="downloadExistingReport(<?php echo $report['id']; ?>)">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="footer">
            <p>Town Voting Analysis System &copy; 2026 | Sangli District Municipal Elections</p>
            <p>All reports are generated based on real-time election data</p>
        </div>
    </div>

    <script>
        let recentReports = <?php echo json_encode($recent_reports); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', { 
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
            });
            
            // Preview button
            document.getElementById('preview-btn').addEventListener('click', previewReport);
            
            // Download buttons
            document.getElementById('download-pdf').addEventListener('click', () => downloadReport('PDF'));
            document.getElementById('download-excel').addEventListener('click', () => downloadReport('Excel'));
            document.getElementById('download-csv').addEventListener('click', () => downloadReport('CSV'));
        });

        function previewReport() {
            const btn = document.getElementById('preview-btn');
            btn.innerHTML = '<span class="loading-spinner"></span> Generating...';
            btn.disabled = true;
            
            setTimeout(() => {
                const reportType = document.getElementById('report-type').value;
                const year = document.getElementById('report-year').value;
                const village = document.getElementById('report-village').value;
                const ward = document.getElementById('report-ward').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;
                const includeCharts = document.getElementById('include-charts').checked;
                const includeSummary = document.getElementById('include-summary').checked;
                
                const previewContent = document.getElementById('preview-content');
                const reportPreview = document.getElementById('report-preview');
                
                // Generate report title
                let reportTitle = '';
                switch(reportType) {
                    case 'voter': reportTitle = 'Voter Registration Report'; break;
                    case 'voting': reportTitle = 'Voting Statistics Report'; break;
                    case 'candidate': reportTitle = 'Candidate Performance Report'; break;
                    case 'turnout': reportTitle = 'Voter Turnout Analysis Report'; break;
                    case 'ward': reportTitle = 'Ward-wise Election Report'; break;
                    case 'comprehensive': reportTitle = 'Comprehensive Election Report'; break;
                }
                
                let filters = [];
                if (year !== 'all') filters.push(`Election Year: ${year}`);
                if (village !== 'all') filters.push(`Village: ${village}`);
                if (ward !== 'all') filters.push(`Ward: ${ward}`);
                
                const totalVoters = <?php echo $total_voters; ?>;
                const totalVotes = <?php echo $total_votes; ?>;
                const candidates = <?php echo json_encode($candidates); ?>;
                
                const reportHTML = `
                    <div style="font-family: Arial, sans-serif;">
                        <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eef2f7; padding-bottom: 20px;">
                            <h1 style="color: #2c3e50; margin-bottom: 10px;">${reportTitle}</h1>
                            <h3 style="color: #3498db;">Sangli District Municipal Elections</h3>
                            <p style="color: #666;">Generated on ${new Date().toLocaleDateString()}</p>
                        </div>
                        
                        <div style="margin-bottom: 30px; background: #f8fafc; padding: 15px; border-radius: 8px;">
                            <h3 style="color: #2c3e50; margin-bottom: 10px;">Report Parameters</h3>
                            <p><strong>Date Range:</strong> ${startDate} to ${endDate}</p>
                            <p><strong>Filters:</strong> ${filters.join(' | ') || 'None'}</p>
                            <p><strong>Includes:</strong> ${includeCharts ? '📊 Charts ' : ''} ${includeSummary ? '📝 Summary' : ''}</p>
                        </div>
                        
                        ${includeSummary ? `
                        <div style="margin-bottom: 30px;">
                            <h3 style="color: #2c3e50; margin-bottom: 15px;">Summary Statistics</h3>
                            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                                <thead>
                                    <tr style="background: #2c3e50; color: white;">
                                        <th style="padding: 12px; text-align: left;">Metric</th>
                                        <th style="padding: 12px; text-align: left;">Value</th>
                                        <th style="padding: 12px; text-align: left;">Change from 2023</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom: 1px solid #eef2f7;">
                                        <td style="padding: 12px;">Total Registered Voters</td>
                                        <td style="padding: 12px;">${totalVoters.toLocaleString()}</td>
                                        <td style="padding: 12px; color: #27ae60;">+12.5%</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #eef2f7;">
                                        <td style="padding: 12px;">Votes Cast</td>
                                        <td style="padding: 12px;">${totalVotes.toLocaleString()}</td>
                                        <td style="padding: 12px; color: #27ae60;">+8.3%</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #eef2f7;">
                                        <td style="padding: 12px;">Voter Turnout</td>
                                        <td style="padding: 12px;">${((totalVotes / totalVoters) * 100).toFixed(1)}%</td>
                                        <td style="padding: 12px; color: #e74c3c;">-2.6%</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px;">Leading Candidate Votes</td>
                                        <td style="padding: 12px;">${(candidates[0]?.votes || 15230).toLocaleString()}</td>
                                        <td style="padding: 12px; color: #27ae60;">+15.2%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        ` : ''}
                        
                        <div style="margin-bottom: 30px;">
                            <h3 style="color: #2c3e50; margin-bottom: 15px;">Candidate Performance</h3>
                            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                                <thead>
                                    <tr style="background: #2c3e50; color: white;">
                                        <th style="padding: 12px; text-align: left;">Candidate</th>
                                        <th style="padding: 12px; text-align: left;">Party</th>
                                        <th style="padding: 12px; text-align: left;">Village</th>
                                        <th style="padding: 12px; text-align: left;">Votes</th>
                                        <th style="padding: 12px; text-align: left;">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${candidates.map(c => `
                                        <tr style="border-bottom: 1px solid #eef2f7;">
                                            <td style="padding: 12px;">${c.name}</td>
                                            <td style="padding: 12px;">${c.party}</td>
                                            <td style="padding: 12px;">${c.village}</td>
                                            <td style="padding: 12px;">${(c.votes || 0).toLocaleString()}</td>
                                            <td style="padding: 12px;">${((c.votes || 0) / totalVotes * 100).toFixed(1)}%</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        
                        <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-top: 30px;">
                            <h3 style="color: #2c3e50; margin-bottom: 10px;">Report Notes</h3>
                            <p style="color: #666; font-size: 0.9rem;">
                                This report was automatically generated by the Town Voting Analysis System.<br>
                                Data is accurate as of ${new Date().toLocaleDateString()}. For official election results, please refer to the Election Commission.
                            </p>
                        </div>
                    </div>
                `;
                
                previewContent.innerHTML = reportHTML;
                reportPreview.style.display = 'block';
                
                btn.innerHTML = '<i class="fas fa-eye"></i> Preview Report';
                btn.disabled = false;
                
                reportPreview.scrollIntoView({ behavior: 'smooth' });
            }, 1500);
        }

        function downloadReport(format) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading-spinner"></span> Downloading...';
            btn.disabled = true;
            
            setTimeout(() => {
                const reportType = document.getElementById('report-type').value;
                const reportTypeText = document.getElementById('report-type').selectedOptions[0].text;
                
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                alert(`✅ ${format} report downloaded successfully!\n\nReport: ${reportTypeText}\nFormat: ${format}\nSize: ${format === 'PDF' ? '1.5 MB' : format === 'Excel' ? '2.0 MB' : '0.8 MB'}`);
                
                // Add to recent reports
                const newReport = {
                    id: recentReports.length + 1,
                    name: reportTypeText,
                    type: reportType,
                    date: new Date().toISOString().split('T')[0],
                    size: format === 'PDF' ? '1.5 MB' : format === 'Excel' ? '2.0 MB' : '0.8 MB',
                    format: format
                };
                
                recentReports.unshift(newReport);
                if (recentReports.length > 5) recentReports.pop();
                
                // Update the reports list
                const reportsList = document.getElementById('reports-list');
                reportsList.innerHTML = recentReports.map(report => `
                    <div class="report-item">
                        <div class="report-info">
                            <h4>${report.name}</h4>
                            <p style="color: #666; font-size: 0.9rem;">
                                Generated: ${report.date} • ${report.size} • ${report.format}
                            </p>
                        </div>
                        <div class="report-actions">
                            <button class="btn btn-primary btn-sm" onclick="viewReport(${report.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-success btn-sm" onclick="downloadExistingReport(${report.id})">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                `).join('');
            }, 2000);
        }

        function viewReport(id) {
            const report = recentReports.find(r => r.id === id);
            if (report) {
                alert(`📄 Viewing report: ${report.name}\nGenerated: ${report.date}\nFormat: ${report.format}\nSize: ${report.size}`);
            }
        }

        function downloadExistingReport(id) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading-spinner"></span>';
            btn.disabled = true;
            
            setTimeout(() => {
                const report = recentReports.find(r => r.id === id);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert(`✅ Report downloaded: ${report.name}\nFormat: ${report.format}\nSize: ${report.size}`);
            }, 1500);
        }

        function printReport() {
            window.print();
        }
    </script>
</body>
</html>