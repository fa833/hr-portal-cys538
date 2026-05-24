<?php
session_start();
require 'db.php';

if(isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username)||empty($email)||empty($password)||empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username: letters, numbers and underscores only.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'applicant')");
            $stmt->execute([$username, $email, $hashed_password]);
            $success = "Account created successfully! You can now sign in.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - NovaCorp HR</title>
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
        .steps { margin-top:36px; display:flex; flex-direction:column; gap:16px; }
        .step { display:flex; align-items:flex-start; gap:12px; }
        .step-num { width:26px; height:26px; border-radius:50%; background:rgba(78,115,223,0.3); color:#6ea3f7; font-size:12px; font-weight:bold; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .step-text .title { color:#fff; font-size:13px; font-weight:bold; }
        .step-text .desc { color:rgba(255,255,255,0.4); font-size:12px; margin-top:2px; }
        .left-footer { color:rgba(255,255,255,0.25); font-size:11px; }
        .right-panel { flex:1; display:flex; align-items:center; justify-content:center; padding:40px; }
        .register-box { background:#fff; border-radius:12px; border:1px solid #e8e8e8; padding:40px; width:100%; max-width:460px; }
        .register-box h3 { font-size:20px; font-weight:bold; color:#1a2332; margin-bottom:6px; }
        .register-box .subtitle { font-size:13px; color:#888; margin-bottom:28px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:11px; font-weight:bold; color:#555; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px; }
        .input-wrap input { width:100%; padding:11px 14px 11px 38px; border:1px solid #ddd; border-radius:6px; font-size:14px; color:#333; background:#fafafa; }
        .input-wrap input:focus { outline:none; border-color:#4e73df; background:#fff; box-shadow:0 0 0 3px rgba(78,115,223,0.1); }
        .form-row { display:flex; gap:14px; }
        .form-row .form-group { flex:1; }
        .hint { font-size:11px; color:#aaa; margin-top:4px; }
        .btn-register { width:100%; padding:12px; background:#1a2332; color:white; border:none; border-radius:6px; font-size:14px; font-weight:bold; cursor:pointer; margin-top:8px; display:flex; align-items:center; justify-content:center; gap:8px; }
        .btn-register:hover { background:#4e73df; }
        .alert-error { background:#fcebeb; color:#a32d2d; border-left:4px solid #e24b4a; padding:11px 14px; border-radius:6px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
        .alert-success { background:#eaf3de; color:#3b6d11; border-left:4px solid #639922; padding:11px 14px; border-radius:6px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
        .divider { text-align:center; color:#ccc; font-size:12px; margin:20px 0; position:relative; }
        .divider::before,.divider::after { content:''; position:absolute; top:50%; width:42%; height:1px; background:#eee; }
        .divider::before { left:0; } .divider::after { right:0; }
        .login-link { text-align:center; font-size:13px; color:#888; }
        .login-link a { color:#4e73df; font-weight:bold; text-decoration:none; }
    </style>
</head>
<body>
<div class="left-panel">
    <div class="left-logo"><div class="company">NovaCorp</div><div class="system">HR Management System</div></div>
    <div class="left-content">
        <h2>Start your career journey with us.</h2>
        <p>Create your account to browse open positions and submit your application in minutes.</p>
        <div class="steps">
            <div class="step"><div class="step-num">1</div><div class="step-text"><div class="title">Create your account</div><div class="desc">Register with your email and credentials</div></div></div>
            <div class="step"><div class="step-num">2</div><div class="step-text"><div class="title">Browse job openings</div><div class="desc">Explore positions across all departments</div></div></div>
            <div class="step"><div class="step-num">3</div><div class="step-text"><div class="title">Apply with your CV</div><div class="desc">Upload your resume and track your status</div></div></div>
        </div>
    </div>
    <div class="left-footer">&copy; 2026 NovaCorp. All rights reserved.</div>
</div>
<div class="right-panel">
    <div class="register-box">
        <h3>Create your account</h3>
        <p class="subtitle">Fill in your details to get started</p>
        <?php if($error): ?><div class="alert-error"><i class="ti ti-alert-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert-success"><i class="ti ti-check"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
        <form method="POST" action="register.php" onsubmit="return validateForm()">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap"><i class="ti ti-user"></i><input type="text" name="username" placeholder="Choose a username" pattern="[a-zA-Z0-9_]+" required></div>
                <div class="hint">Letters, numbers and underscores only</div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap"><i class="ti ti-mail"></i><input type="email" name="email" placeholder="your@email.com" required></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap"><i class="ti ti-lock"></i><input type="password" name="password" id="password" placeholder="Min. 6 characters" minlength="6" required></div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrap"><i class="ti ti-lock-check"></i><input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat password" required></div>
                </div>
            </div>
            <button type="submit" class="btn-register"><i class="ti ti-user-plus"></i> Create Account</button>
        </form>
        <div class="divider">or</div>
        <div class="login-link">Already have an account? <a href="login.php">Sign in here</a></div>
    </div>
</div>
<script>
function validateForm() {
    var username = document.querySelector('input[name="username"]').value;
    var email = document.querySelector('input[name="email"]').value;
    var password = document.getElementById('password').value;
    var confirm = document.getElementById('confirm_password').value;
    if (!/^[a-zA-Z0-9_]+$/.test(username)) { alert("Username: letters, numbers and underscores only."); return false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { alert("Please enter a valid email address."); return false; }
    if (password.length < 6) { alert("Password must be at least 6 characters."); return false; }
    if (password !== confirm) { alert("Passwords do not match."); return false; }
    return true;
}
</script>
</body>
</html>
