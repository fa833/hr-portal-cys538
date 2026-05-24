<?php
session_start();
require 'header.php';
renderHeader('Home', 'home');
?>
<div class="page-header">
    <h1>Welcome to NovaCorp HR Portal</h1>
    <p>Manage job listings, applications, and recruitment in one place.</p>
</div>
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-briefcase"></i></div>
        <div><div class="stat-label">Open Positions</div><div class="stat-value">24</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-users"></i></div>
        <div><div class="stat-label">Total Applicants</div><div class="stat-value">138</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="ti ti-building"></i></div>
        <div><div class="stat-label">Departments</div><div class="stat-value">8</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-clock"></i></div>
        <div><div class="stat-label">Pending Reviews</div><div class="stat-value">17</div></div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h3>Quick Actions</h3></div>
    <div class="card-body" style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="jobs.php" class="btn btn-primary"><i class="ti ti-briefcase"></i> Browse Jobs</a>
        <?php if(!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn btn-outline"><i class="ti ti-user-plus"></i> Create Account</a>
        <a href="login.php" class="btn btn-outline"><i class="ti ti-login"></i> Login</a>
        <?php else: ?>
        <a href="my_applications.php" class="btn btn-outline"><i class="ti ti-file-cv"></i> My Applications</a>
        <?php endif; ?>
    </div>
</div>
<?php renderFooter(); ?>
