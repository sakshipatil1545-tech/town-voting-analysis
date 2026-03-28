<?php
require_once 'config.php';
requireLogin();

// Village data
$villages = [
    "Islampur" => [
        "voters" => 12500, "votes" => 8500, "turnout" => 68.0,
        "wards" => [
            ["name" => "Ward 1", "voters" => 2100, "votes" => 1450, "turnout" => 69.0, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 2", "voters" => 2050, "votes" => 1400, "turnout" => 68.3, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 3", "voters" => 2100, "votes" => 1420, "turnout" => 67.6, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 4", "voters" => 2100, "votes" => 1430, "turnout" => 68.1, "leading" => "Vishal Patil"],
            ["name" => "Ward 5", "voters" => 2050, "votes" => 1400, "turnout" => 68.3, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 6", "voters" => 2100, "votes" => 1400, "turnout" => 66.7, "leading" => "Viswajeet Kadam"]
        ]
    ],
    "Nagarale" => [
        "voters" => 8500, "votes" => 5600, "turnout" => 65.9,
        "wards" => [
            ["name" => "Ward 1", "voters" => 1500, "votes" => 980, "turnout" => 65.3, "leading" => "Sakshi Patil"],
            ["name" => "Ward 2", "voters" => 1400, "votes" => 920, "turnout" => 65.7, "leading" => "Sakshi Patil"],
            ["name" => "Ward 3", "voters" => 1300, "votes" => 850, "turnout" => 65.4, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 4", "voters" => 1450, "votes" => 950, "turnout" => 65.5, "leading" => "Sakshi Patil"],
            ["name" => "Ward 5", "voters" => 1400, "votes" => 900, "turnout" => 64.3, "leading" => "Vishal Patil"],
            ["name" => "Ward 6", "voters" => 1450, "votes" => 1000, "turnout" => 69.0, "leading" => "Sakshi Patil"]
        ]
    ],
    "Palus" => [
        "voters" => 9200, "votes" => 6100, "turnout" => 66.3,
        "wards" => [
            ["name" => "Ward 1", "voters" => 1600, "votes" => 1050, "turnout" => 65.6, "leading" => "Vishal Patil"],
            ["name" => "Ward 2", "voters" => 1550, "votes" => 1020, "turnout" => 65.8, "leading" => "Vishal Patil"],
            ["name" => "Ward 3", "voters" => 1500, "votes" => 1000, "turnout" => 66.7, "leading" => "Jayant Patil"],
            ["name" => "Ward 4", "voters" => 1550, "votes" => 1030, "turnout" => 66.5, "leading" => "Vishal Patil"],
            ["name" => "Ward 5", "voters" => 1500, "votes" => 1000, "turnout" => 66.7, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 6", "voters" => 1500, "votes" => 1000, "turnout" => 66.7, "leading" => "Vishal Patil"]
        ]
    ],
    "Walwa" => [
        "voters" => 7800, "votes" => 5200, "turnout" => 66.7,
        "wards" => [
            ["name" => "Ward 1", "voters" => 1300, "votes" => 870, "turnout" => 66.9, "leading" => "Jayant Patil"],
            ["name" => "Ward 2", "voters" => 1300, "votes" => 860, "turnout" => 66.2, "leading" => "Jayant Patil"],
            ["name" => "Ward 3", "voters" => 1300, "votes" => 870, "turnout" => 66.9, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 4", "voters" => 1300, "votes" => 870, "turnout" => 66.9, "leading" => "Jayant Patil"],
            ["name" => "Ward 5", "voters" => 1300, "votes" => 860, "turnout" => 66.2, "leading" => "Jayant Patil"],
            ["name" => "Ward 6", "voters" => 1300, "votes" => 870, "turnout" => 66.9, "leading" => "Vishal Patil"]
        ]
    ],
    "Kundal" => [
        "voters" => 6200, "votes" => 4100, "turnout" => 66.1,
        "wards" => [
            ["name" => "Ward 1", "voters" => 1050, "votes" => 690, "turnout" => 65.7, "leading" => "Rohit Jadhav"],
            ["name" => "Ward 2", "voters" => 1050, "votes" => 690, "turnout" => 65.7, "leading" => "Rohit Jadhav"],
            ["name" => "Ward 3", "voters" => 1050, "votes" => 700, "turnout" => 66.7, "leading" => "Viswajeet Kadam"],
            ["name" => "Ward 4", "voters" => 1050, "votes" => 690, "turnout" => 65.7, "leading" => "Rohit Jadhav"],
            ["name" => "Ward 5", "voters" => 1000, "votes" => 660, "turnout" => 66.0, "leading" => "Rohit Jadhav"],
            ["name" => "Ward 6", "voters" => 1000, "votes" => 670, "turnout" => 67.0, "leading" => "Rohit Jadhav"]
        ]
    ]
];

$candidates = [
    ["name" => "Viswajeet Kadam", "party" => "BJP", "village" => "Islampur", "votes" => 15230, "initials" => "Viswajeet Kadam"],
    ["name" => "Vishal Patil", "party" => "Congress", "village" => "Palus", "votes" => 12450, "initials" => "Vishal Patil"],
    ["name" => "Jayant Patil", "party" => "NCP", "village" => "Walwa", "votes" => 8920, "initials" => "Jayant Patil"],
    ["name" => "Sakshi Patil", "party" => "Shiv Sena", "village" => "Nagarale", "votes" => 5680, "initials" => "Sakshi Patil"],
    ["name" => "Rohit Jadhav", "party" => "Independent", "village" => "Kundal", "votes" => 2950, "initials" => "Rohit Jadhav"]
];

$selected_village = $_GET['village'] ?? 'Islampur';
if (!isset($villages[$selected_village])) {
    $selected_village = 'Islampur';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Village View - Town Voting System</title>
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
            margin: 4px 10px;
            border-radius: 8px;
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
            text-decoration: none;
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
        
        /* Village Selector */
        .village-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .village-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .village-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: var(--secondary);
        }
        
        .village-card.active {
            border-color: var(--secondary);
            background: linear-gradient(135deg, #f8fafc 0%, #e8f4fc 100%);
        }
        
        .village-name {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .voter-count {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin: 5px 0;
        }
        
        /* Village Stats */
        .village-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--secondary);
            transition: all 0.2s;
        }
        
        .stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .stat-box:nth-child(2) { border-left-color: var(--success); }
        .stat-box:nth-child(3) { border-left-color: var(--warning); }
        .stat-box:nth-child(4) { border-left-color: var(--danger); }
        
        .stat-title {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        /* Leading Candidate */
        .leading-candidate {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--secondary);
        }
        
        .candidate-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .candidate-header h3 {
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .candidate-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .candidate-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .candidate-info h4 {
            color: var(--primary);
            margin-bottom: 3px;
            font-size: 1.2rem;
        }
        
        .candidate-votes {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 5px;
        }
        
        /* Data Section */
        .data-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 992px) {
            .data-section {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f4f8;
        }
        
        .card-title {
            font-size: 1.2rem;
            color: var(--primary);
            font-weight: 600;
        }
        
        .chart-container {
            height: 280px;
        }
        
        /* Table */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #eef2f7;
            max-height: 350px;
            overflow-y: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background-color: #f8fafc;
            color: var(--primary);
            font-weight: 600;
            padding: 12px 15px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eef2f7;
        }
        
        .data-table tr:hover {
            background-color: #f0f9ff;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            background-color: #d1e8ff;
            color: #0a4b8c;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52,152,219,0.4);
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            border-top: 1px solid #eef2f7;
            color: var(--gray);
            font-size: 0.9rem;
            background: white;
            border-radius: 10px;
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
            
            .main-content {
                margin-left: 70px;
            }
            
            .village-selector {
                grid-template-columns: 1fr;
            }
            
            .candidate-content {
                flex-direction: column;
                text-align: center;
            }
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
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
            <a href="analysis.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Analysis</span></a>
            <a href="village-view.php" class="nav-item active"><i class="fas fa-map-marker-alt"></i><span>Village View</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            
            <a href="candidate-registration.php" class="nav-item"><i class="fas fa-user-tie"></i><span>Candidate</span></a>
            <a href="voter-registration.php" class="nav-item"><i class="fas fa-user-plus"></i><span>Voter</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
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
                <h1>Village & Ward Analysis</h1>
                <p>Detailed breakdown by village and ward</p>
            </div>
            <div class="date-display">
                <i class="fas fa-calendar-alt"></i>
                <span id="current-date"></span>
            </div>
        </div>
        
        <div class="village-selector" id="village-selector">
            <?php foreach ($villages as $village_name => $village_data): ?>
            <a href="?village=<?php echo urlencode($village_name); ?>" class="village-card <?php echo $village_name === $selected_village ? 'active' : ''; ?>">
                <div class="village-name"><?php echo $village_name; ?></div>
                <div class="voter-count"><?php echo number_format($village_data['voters']); ?></div>
                <div style="font-size:0.8rem;"><?php echo $village_data['turnout']; ?>% Turnout</div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div id="selected-village-content">
            <?php
            $data = $villages[$selected_village];
            $village_candidates = array_filter($candidates, function($c) use ($selected_village) {
                return $c['village'] === $selected_village;
            });
            $leading = !empty($village_candidates) ? $village_candidates[array_key_first($village_candidates)] : 
                      ['name' => 'No candidate', 'party' => '', 'votes' => 0, 'initials' => 'NC'];
            ?>
            
            <div class="village-stats">
                <div class="stat-box"><div class="stat-title">Total Voters</div><div class="stat-value"><?php echo number_format($data['voters']); ?></div></div>
                <div class="stat-box"><div class="stat-title">Votes Cast</div><div class="stat-value"><?php echo number_format($data['votes']); ?></div></div>
                <div class="stat-box"><div class="stat-title">Turnout</div><div class="stat-value"><?php echo $data['turnout']; ?>%</div></div>
                <div class="stat-box"><div class="stat-title">Leading</div><div class="stat-value"><?php echo $leading['initials']; ?></div></div>
            </div>
            
            <div class="leading-candidate">
                <div class="candidate-header"><h3>Leading Candidate</h3><span class="badge"><?php echo $leading['party']; ?></span></div>
                <div class="candidate-content">
                    <div class="candidate-avatar"><?php echo $leading['initials']; ?></div>
                    <div><h4><?php echo $leading['name']; ?></h4><p><?php echo $leading['party']; ?></p><div class="candidate-votes"><?php echo number_format($leading['votes']); ?></div></div>
                </div>
            </div>
            
            <div class="data-section">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ward-wise Turnout</h3>
                        <button class="btn btn-primary" onclick="alert('Export feature')"><i class="fas fa-download"></i> Export</button>
                    </div>
                    <div class="chart-container">
                        <canvas id="turnoutChart"></canvas>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ward Breakdown</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>Ward</th><th>Voters</th><th>Votes</th><th>%</th><th>Leading</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['wards'] as $w): ?>
                                <tr>
                                    <td><?php echo $w['name']; ?></td>
                                    <td><?php echo $w['voters']; ?></td>
                                    <td><?php echo $w['votes']; ?></td>
                                    <td><?php echo $w['turnout']; ?>%</td>
                                    <td><span class="badge"><?php echo $w['leading']; ?></span></td>
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
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
            
            // Initialize chart
            const wards = <?php echo json_encode($data['wards']); ?>;
            new Chart(document.getElementById('turnoutChart'), {
                type: 'bar',
                data: {
                    labels: wards.map(w => w.name),
                    datasets: [{
                        label: 'Turnout %',
                        data: wards.map(w => w.turnout),
                        backgroundColor: 'rgba(52,152,219,0.7)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 100 } },
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</body>
</html>