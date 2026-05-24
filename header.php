<?php
function renderHeader($title, $activePage = '') {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - NovaCorp HR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 220px; height: 100vh;
            background: #1a2332; overflow-y: auto; z-index: 200;
        }
        .sidebar-logo { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-logo .company { color: #fff; font-size: 16px; font-weight: bold; }
        .sidebar-logo .system { color: rgba(255,255,255,0.4); font-size: 10px; letter-spacing: 1px; text-transform: uppercase; margin-top: 4px; }
        .sidebar-section { padding: 12px 0; }
        .sidebar-label { font-size: 10px; letter-spacing: 1px; text-transform: uppercase; color: rgba(255,255,255,0.3); padding: 0 20px 8px; }
        .sidebar-item { display: flex; align-items: center; gap: 10px; padding: 10px 20px; color: rgba(255,255,255,0.6); font-size: 13px; text-decoration: none; }
        .sidebar-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .sidebar-item.active { background: rgba(78,115,223,0.2); color: #6ea3f7; border-left: 3px solid #4e73df; }
        .sidebar-item i { font-size: 16px; }
        .main-wrapper { margin-left: 220px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { position: sticky; top: 0; z-index: 100; background: #fff; border-bottom: 1px solid #e0e0e0; height: 56px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .topbar-left { font-size: 13px; color: #888; }
        .topbar-left span { color: #1a2332; font-weight: bold; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 34px; height: 34px; border-radius: 50%; background: #4e73df; color: white; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .user-name { font-size: 13px; font-weight: bold; color: #1a2332; }
        .user-role { font-size: 11px; color: #888; }
        .page-content { padding: 28px; }
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 22px; font-weight: bold; color: #1a2332; }
        .page-header p { font-size: 14px; color: #888; margin-top: 6px; }
        .stats-row { display: flex; gap: 16px; margin-bottom: 24px; }
        .stat-card { flex: 1; background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; padding: 18px; display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .stat-icon.blue { background: #e6f1fb; color: #185fa5; }
        .stat-icon.green { background: #eaf3de; color: #3b6d11; }
        .stat-icon.amber { background: #faeeda; color: #854f0b; }
        .stat-icon.red { background: #fcebeb; color: #a32d2d; }
        .stat-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .stat-value { font-size: 22px; font-weight: bold; color: #1a2332; }
        .card { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; margin-bottom: 20px; }
        .card-header { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; }
        .card-header h3 { font-size: 14px; font-weight: bold; color: #1a2332; }
        .card-header a { font-size: 12px; color: #4e73df; text-decoration: none; }
        .card-body { padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; color: #666; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; text-align: left; border-bottom: 1px solid #e8e8e8; }
        td { padding: 13px 16px; font-size: 13px; color: #333; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafbfc; }
        .pill { display: inline-block; font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: bold; }
        .pill.pending { background: #faeeda; color: #854f0b; }
        .pill.reviewed { background: #e6f1fb; color: #185fa5; }
        .pill.accepted { background: #eaf3de; color: #3b6d11; }
        .pill.rejected { background: #fcebeb; color: #a32d2d; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: bold; text-decoration: none; cursor: pointer; border: none; }
        .btn-primary { background: #4e73df; color: white; }
        .btn-primary:hover { background: #3a5fc8; color: white; }
        .btn-danger { background: #e24b4a; color: white; }
        .btn-success { background: #3b6d11; color: white; }
        .btn-outline { background: white; color: #4e73df; border: 1px solid #4e73df; }
        .btn-outline:hover { background: #e6f1fb; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; color: #555; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; color: #333; background: #fff; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78,115,223,0.1); }
        .form-row { display: flex; gap: 16px; }
        .form-row .form-group { flex: 1; }
        .alert { padding: 12px 16px; border-radius: 6px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-error { background: #fcebeb; color: #a32d2d; border-left: 4px solid #e24b4a; }
        .alert-success { background: #eaf3de; color: #3b6d11; border-left: 4px solid #639922; }
        .alert-info { background: #e6f1fb; color: #185fa5; border-left: 4px solid #378add; }
        .two-col { display: flex; gap: 20px; }
        .two-col .col-left { flex: 1; min-width: 0; }
        .two-col .col-right { width: 340px; flex-shrink: 0; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="company">NovaCorp</div>
        <div class="system">HR Management</div>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-label">Menu</div>
        <a href="index.php" class="sidebar-item <?php echo $activePage=='home'?'active':''; ?>">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
        <a href="jobs.php" class="sidebar-item <?php echo $activePage=='jobs'?'active':''; ?>">
            <i class="ti ti-briefcase"></i> Job Listings
        </a>
        <?php if(isset($_SESSION['user_id'])): ?>
        <a href="my_applications.php" class="sidebar-item <?php echo $activePage=='my_applications'?'active':''; ?>">
            <i class="ti ti-file-cv"></i> My Applications
        </a>
        <?php if($_SESSION['role'] == 'admin'): ?>
        <a href="admin.php" class="sidebar-item <?php echo $activePage=='admin'?'active':''; ?>">
            <i class="ti ti-shield"></i> Admin Panel
        </a>
        <?php endif; ?>
        <a href="logout.php" class="sidebar-item">
            <i class="ti ti-logout"></i> Logout
        </a>
        <?php else: ?>
        <a href="login.php" class="sidebar-item <?php echo $activePage=='login'?'active':''; ?>">
            <i class="ti ti-login"></i> Login
        </a>
        <a href="register.php" class="sidebar-item <?php echo $activePage=='register'?'active':''; ?>">
            <i class="ti ti-user-plus"></i> Register
        </a>
        <?php endif; ?>
    </div>
</div>
<div class="main-wrapper">
    <div class="topbar">
        <div class="topbar-left">NovaCorp / <span><?php echo $title; ?></span></div>
        <div class="topbar-right">
            <?php if(isset($_SESSION['username'])): ?>
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,2)); ?></div>
            <div>
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="page-content">
<?php
}
function renderFooter() {
?>
    </div>
</div>
</body>
</html>
<?php
}
?>
