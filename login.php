<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Check admin credentials
        $stmt = $conn->prepare("SELECT id, username, name, email, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // For demo, using plain text password (admin123)
            if ($password === 'admin123') {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['user_name'] = $admin['name'];
                $_SESSION['user_role'] = 'admin';
                $_SESSION['user_email'] = $admin['email'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'Invalid username';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Town Voting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated background */
        .animated-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .bg-circle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite;
        }
        
        .bg-circle:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }
        
        .bg-circle:nth-child(2) {
            width: 400px;
            height: 400px;
            bottom: -200px;
            right: -200px;
            animation-delay: -5s;
        }
        
        .bg-circle:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(50px, 50px) scale(1.1);
            }
            50% {
                transform: translate(0, 100px) scale(0.9);
            }
            75% {
                transform: translate(-50px, 50px) scale(1.05);
            }
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 2;
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .back-to-home {
            margin-bottom: 20px;
        }
        
        .back-to-home a {
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            padding: 8px 16px;
            background: rgba(52, 152, 219, 0.1);
            border-radius: 30px;
        }
        
        .back-to-home a:hover {
            background: var(--secondary);
            color: white;
            transform: translateX(-5px);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .login-icon i {
            font-size: 40px;
            color: white;
        }
        
        .login-header h1 {
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .login-header p span {
            color: var(--secondary);
            font-weight: 600;
        }
        
        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }
        
        .admin-badge i {
            margin-right: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 16px;
            color: #999;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .input-with-icon .toggle-password {
            left: auto;
            right: 16px;
            cursor: pointer;
            z-index: 10;
        }
        
        .input-with-icon .toggle-password:hover {
            color: var(--secondary);
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid #e1e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #fcfdfe;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
            background-color: white;
        }
        
        .form-control:focus + i {
            color: var(--secondary);
        }
        
        .admin-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        
        .admin-info i {
            color: var(--secondary);
            font-size: 1.2rem;
            margin-right: 8px;
        }
        
        .admin-info span {
            color: var(--primary);
            font-weight: 600;
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.7s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(52, 152, 219, 0.6);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .error-message {
            background-color: #fee;
            color: var(--danger);
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            display: <?php echo $error ? 'block' : 'none'; ?>;
            border-left: 4px solid var(--danger);
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .error-message i {
            margin-right: 8px;
        }
        
        .demo-credentials {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
            border: 1px solid #dee2e6;
        }
        
        .demo-credentials p {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .demo-credentials p i {
            color: var(--warning);
        }
        
        .credential-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            margin-bottom: 8px;
            font-size: 0.95rem;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }
        
        .credential-item:hover {
            transform: translateX(5px);
            border-color: var(--secondary);
        }
        
        .credential-item i {
            width: 20px;
            color: var(--secondary);
        }
        
        .credential-item .badge {
            background: var(--secondary);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-left: auto;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }
        
        .footer i {
            color: #ff6b6b;
            animation: heartbeat 1.5s infinite;
        }
        
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Loading spinner */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn-loading .btn-text {
            visibility: hidden;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 25px;
            }
            
            .login-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="animated-bg">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Back to Home link -->
            <div class="back-to-home">
                <a href="index.html">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>

            <!-- Login Header -->
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1>Admin Login</h1>
                <p><span>Town Voting System 2026</span></p>
                <div class="admin-badge">
                    <i class="fas fa-shield-alt"></i> Administrator Access Only
                </div>
            </div>

            <!-- Admin Info -->
            <div class="admin-info">
                <i class="fas fa-info-circle"></i>
                <span>Authorized personnel only</span>
            </div>

            <!-- Login Form -->
            <form id="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Admin Username
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter admin username" required autocomplete="off" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter admin password" required autocomplete="off">
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="login-btn">
                    <span class="btn-text">Login to Admin Dashboard</span>
                    <i class="fas fa-sign-in-alt"></i>
                </button>
                
                <div id="error-message" class="error-message">
                    <i class="fas fa-exclamation-circle"></i> 
                    <span id="error-text"><?php echo htmlspecialchars($error); ?></span>
                </div>
            </form>
            
         
        <div class="footer">
            <p>© 2026 Town Voting System | Sangli District Municipal Elections</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            // Auto-fill credentials on double-click
            const demoItems = document.querySelectorAll('.credential-item');
            demoItems.forEach(item => {
                item.addEventListener('dblclick', function() {
                    const text = this.querySelector('span').textContent;
                    if (text.includes('admin')) {
                        document.getElementById('username').value = 'admin';
                    } else if (text.includes('admin123')) {
                        document.getElementById('password').value = 'admin123';
                    }
                });
            });
            
            // Keyboard shortcut (Ctrl+1 for admin auto-fill)
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === '1') {
                    e.preventDefault();
                    document.getElementById('username').value = 'admin';
                    document.getElementById('password').value = 'admin123';
                }
            });
        });
    </script>
</body>
</html>