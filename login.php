<?php
session_start();
require 'db.php';

if(isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Invalid username format.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: " . ($user['role'] == 'admin' ? 'admin.php' : 'dashboard.php'));
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NovaCorp HR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#f0f2f5; min-height:100vh; display:flex; }
        .left-panel { width:420px; background:#1a2332; display:flex; flex-direction:column; justify-content:space-between; padding:40px; flex-shrink:0; }
        .left-logo .company { color:#fff; font-size:22px; font-weight:bold; }
        .left-logo .system { color:rgba(255,255,255,0.4); font-size:11px; text-transform:uppercase; letter-spacing:1.5px; margin-top:4px; }
        .left-content { flex:1; display:flex; flex-direction:column; justify-content:center; }
        .left-content h2 { color:#fff; font-size:28px; font-weight:bold; line-height:1.3; margin-bottom:16px; }
        .left-content p { color:rgba(255,255,255,0.5); font-size:14px; line-height:1.7; }
        .left-stats { display:flex; gap:24px; margin-top:40px; }
        .left-stat .number { color:#6ea3f7; font-size:24px; font-weight:bold; }
        .left-stat .label { color:rgba(255,255,255,0.4); font-size:11px; margin-top:2px; }
        .left-footer { color:rgba(255,255,255,0.25); font-size:11px; }
        .right-panel { flex:1; display:flex; align-items:center; justify-content:center; padding:40px; }
        .login-box { background:#fff; border-radius:12px; border:1px solid #e8e8e8; padding:40px; width:100%; max-width:420px; }
        .login-box h3 { font-size:20px; font-weight:bold; color:#1a2332; margin-bottom:6px; }
        .login-box .subtitle { font-size:13px; color:#888; margin-bottom:28px; }
        .form-group { margin-bottom:18px; }
        .form-group label { display:block; font-size:11px; font-weight:bold; color:#555; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px; }
        .input-wrap input { width:100%; padding:11px 14px 11px 38px; border:1px solid #ddd; border-radius:6px; font-size:14px; color:#333; background:#fafafa; }
        .input-wrap input:focus { outline:none; border-color:#4e73df; background:#fff; box-shadow:0 0 0 3px rgba(78,115,223,0.1); }
        .btn-login { width:100%; padding:12px; background:#1a2332; color:white; border:none; border-radius:6px; font-size:14px; font-weight:bold; cursor:pointer; margin-top:8px; display:flex; align-items:center; justify-content:center; gap:8px; }
        .btn-login:hover { background:#4e73df; }
        .alert-error { background:#fcebeb; color:#a32d2d; border-left:4px solid #e24b4a; padding:11px 14px; border-radius:6px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
        .divider { text-align:center; color:#ccc; font-size:12px; margin:20px 0; position:relative; }
        .divider::before,.divider::after { content:''; position:absolute; top:50%; width:42%; height:1px; background:#eee; }
        .divider::before { left:0; } .divider::after { right:0; }
        .register-link { text-align:center; font-size:13px; color:#888; }
        .register-link a { color:#4e73df; font-weight:bold; text-decoration:none; }
    </style>
</head>
<body>
<div class="left-panel">
    <div class="left-logo">
        <div class="company">NovaCorp</div>
        <div class="system">HR Management System</div>
    </div>
    <div class="left-content">
        <h2>Connecting talent with opportunity.</h2>
        <p>NovaCorp's HR portal streamlines recruitment, application tracking, and workforce management in one secure platform.</p>
        <div class="left-stats">
            <div class="left-stat"><div class="number">24</div><div class="label">Open Positions</div></div>
            <div class="left-stat"><div class="number">138</div><div class="label">Applicants</div></div>
            <div class="left-stat"><div class="number">8</div><div class="label">Departments</div></div>
        </div>
    </div>
    <div class="left-footer">&copy; 2026 NovaCorp. All rights reserved.</div>
</div>
<div class="right-panel">
    <div class="login-box">
        <h3>Sign in to your account</h3>
        <p class="subtitle">Enter your credentials to access the HR portal</p>
        <?php if($error): ?>
        <div class="alert-error"><i class="ti ti-alert-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" onsubmit="return validateForm()">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="ti ti-user"></i>
                    <input type="text" name="username" pattern="[a-zA-Z0-9_]+" placeholder="Enter your username" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="ti ti-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" minlength="6" required>
                </div>
            </div>
            <button type="submit" class="btn-login"><i class="ti ti-login"></i> Sign In</button>
        </form>
        <div class="divider">or</div>
        <div class="register-link">Don't have an account? <a href="register.php">Create one here</a></div>
    </div>
</div>
<script>
function validateForm() {
    var username = document.querySelector('input[name="username"]').value;
    var password = document.querySelector('input[name="password"]').value;
    if (!/^[a-zA-Z0-9_]+$/.test(username)) { alert("Username: letters, numbers and underscores only."); return false; }
    if (password.length < 6) { alert("Password must be at least 6 characters."); return false; }
    return true;
}
</script>
</body>
</html>
