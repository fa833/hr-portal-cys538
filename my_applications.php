<?php
session_start();
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$stmt = $pdo->prepare("SELECT a.*, j.title, j.department FROM applications a JOIN jobs j ON a.job_id = j.id WHERE a.user_id = ? ORDER BY a.applied_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();

$total = count($applications);
$pending = count(array_filter($applications, fn($a) => $a['status'] == 'pending'));
$reviewed = count(array_filter($applications, fn($a) => $a['status'] == 'reviewed'));
$accepted = count(array_filter($applications, fn($a) => $a['status'] == 'accepted'));

renderHeader('My Applications', 'my_applications');
?>
<div class="page-header">
    <h1>My Applications</h1>
    <p>Track the status of all your submitted job applications.</p>
</div>
<div class="stats-row">
    <div class="stat-card"><div class="stat-icon blue"><i class="ti ti-file-cv"></i></div><div><div class="stat-label">Total Applied</div><div class="stat-value"><?php echo $total; ?></div></div></div>
    <div class="stat-card"><div class="stat-icon amber"><i class="ti ti-clock"></i></div><div><div class="stat-label">Pending</div><div class="stat-value"><?php echo $pending; ?></div></div></div>
    <div class="stat-card"><div class="stat-icon blue"><i class="ti ti-eye"></i></div><div><div class="stat-label">Reviewed</div><div class="stat-value"><?php echo $reviewed; ?></div></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="ti ti-check"></i></div><div><div class="stat-label">Accepted</div><div class="stat-value"><?php echo $accepted; ?></div></div></div>
</div>
<div class="card">
    <div class="card-header">
        <h3><i class="ti ti-file-cv"></i> My Applications</h3>
        <a href="jobs.php" class="btn btn-primary btn-sm"><i class="ti ti-plus"></i> Apply for New Job</a>
    </div>
    <?php if(count($applications) > 0): ?>
    <table>
        <thead><tr><th>#</th><th>Position</th><th>Department</th><th>Applied On</th><th>CV File</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach($applications as $i => $app): ?>
            <tr>
                <td style="color:#aaa;"><?php echo $i+1; ?></td>
                <td style="font-weight:bold; color:#1a2332;"><?php echo htmlspecialchars($app['title']); ?></td>
                <td><span style="background:#f0f2f5; padding:4px 10px; border-radius:20px; font-size:12px; color:#555;"><?php echo htmlspecialchars($app['department']); ?></span></td>
                <td style="color:#888;"><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                <td style="font-size:12px; color:#4e73df;"><i class="ti ti-file"></i> <?php echo htmlspecialchars($app['cv_filename']); ?></td>
                <td><span class="pill <?php echo $app['status']; ?>"><?php echo ucfirst($app['status']); ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align:center; padding:60px 20px;">
        <i class="ti ti-file-off" style="font-size:48px; color:#ddd;"></i>
        <h3 style="color:#aaa; margin-top:16px; font-weight:normal;">No applications yet</h3>
        <a href="jobs.php" class="btn btn-primary" style="margin-top:20px;"><i class="ti ti-briefcase"></i> Browse Jobs</a>
    </div>
    <?php endif; ?>
</div>
<?php renderFooter(); ?>
