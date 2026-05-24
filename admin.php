<?php
session_start();
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['app_id'], $_POST['status'])) {
    $allowed_statuses = ['pending','reviewed','accepted','rejected'];
    if (in_array($_POST['status'], $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], (int)$_POST['app_id']]);
    }
    header("Location: admin.php"); exit();
}

if (isset($_GET['delete_job']) && is_numeric($_GET['delete_job'])) {
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->execute([(int)$_GET['delete_job']]);
    header("Location: admin.php"); exit();
}

$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role='applicant'")->fetchColumn();
$total_jobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$total_apps = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$pending_apps = $pdo->query("SELECT COUNT(*) FROM applications WHERE status='pending'")->fetchColumn();

$applications = $pdo->query("SELECT a.*, u.username, u.email, j.title as job_title, j.department FROM applications a JOIN users u ON a.user_id = u.id JOIN jobs j ON a.job_id = j.id ORDER BY a.applied_at DESC")->fetchAll();
$jobs = $pdo->query("SELECT * FROM jobs ORDER BY posted_at DESC")->fetchAll();

renderHeader('Admin Panel', 'admin');
?>
<div class="page-header">
    <h1>Admin Panel</h1>
    <p>Manage applications, jobs, and users across NovaCorp HR system.</p>
</div>
<div class="stats-row">
    <div class="stat-card"><div class="stat-icon blue"><i class="ti ti-users"></i></div><div><div class="stat-label">Total Applicants</div><div class="stat-value"><?php echo $total_users; ?></div></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="ti ti-briefcase"></i></div><div><div class="stat-label">Open Jobs</div><div class="stat-value"><?php echo $total_jobs; ?></div></div></div>
    <div class="stat-card"><div class="stat-icon amber"><i class="ti ti-file-cv"></i></div><div><div class="stat-label">Total Applications</div><div class="stat-value"><?php echo $total_apps; ?></div></div></div>
    <div class="stat-card"><div class="stat-icon red"><i class="ti ti-clock"></i></div><div><div class="stat-label">Pending Review</div><div class="stat-value"><?php echo $pending_apps; ?></div></div></div>
</div>
<div class="card" style="margin-bottom:24px;">
    <div class="card-header"><h3><i class="ti ti-file-cv"></i> All Applications</h3><span style="font-size:12px; color:#888;"><?php echo $total_apps; ?> total</span></div>
    <?php if(count($applications) > 0): ?>
    <table>
        <thead><tr><th>#</th><th>Applicant</th><th>Position</th><th>Department</th><th>CV File</th><th>Applied On</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($applications as $i => $app): ?>
        <tr>
            <td style="color:#aaa;"><?php echo $i+1; ?></td>
            <td><div style="font-weight:bold; color:#1a2332;"><?php echo htmlspecialchars($app['username']); ?></div><div style="font-size:11px; color:#aaa;"><?php echo htmlspecialchars($app['email']); ?></div></td>
            <td style="font-weight:bold; color:#1a2332;"><?php echo htmlspecialchars($app['job_title']); ?></td>
            <td><span style="background:#f0f2f5; padding:4px 10px; border-radius:20px; font-size:12px; color:#555;"><?php echo htmlspecialchars($app['department']); ?></span></td>
            <td><a href="view.php?file=<?php echo urlencode($app['cv_filename']); ?>" style="font-size:12px; color:#4e73df; text-decoration:none;"><i class="ti ti-download"></i> <?php echo htmlspecialchars($app['cv_filename']); ?></a></td>
            <td style="color:#888; font-size:12px;"><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
            <td><span class="pill <?php echo $app['status']; ?>"><?php echo ucfirst($app['status']); ?></span></td>
            <td>
                <form method="POST" action="admin.php" style="display:flex; gap:6px;">
                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                    <select name="status" style="padding:5px 8px; border:1px solid #ddd; border-radius:5px; font-size:12px;">
                        <option value="pending" <?php echo $app['status']=='pending'?'selected':''; ?>>Pending</option>
                        <option value="reviewed" <?php echo $app['status']=='reviewed'?'selected':''; ?>>Reviewed</option>
                        <option value="accepted" <?php echo $app['status']=='accepted'?'selected':''; ?>>Accepted</option>
                        <option value="rejected" <?php echo $app['status']=='rejected'?'selected':''; ?>>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-check"></i></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align:center; padding:40px;"><i class="ti ti-file-off" style="font-size:40px; color:#ddd;"></i><p style="color:#aaa; margin-top:12px;">No applications yet.</p></div>
    <?php endif; ?>
</div>
<div class="card">
    <div class="card-header"><h3><i class="ti ti-briefcase"></i> Manage Jobs</h3><a href="add_job.php" class="btn btn-primary btn-sm"><i class="ti ti-plus"></i> Add New Job</a></div>
    <table>
        <thead><tr><th>#</th><th>Job Title</th><th>Department</th><th>Posted On</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($jobs as $i => $job): ?>
        <tr>
            <td style="color:#aaa;"><?php echo $i+1; ?></td>
            <td style="font-weight:bold; color:#1a2332;"><?php echo htmlspecialchars($job['title']); ?></td>
            <td><span style="background:#f0f2f5; padding:4px 10px; border-radius:20px; font-size:12px; color:#555;"><?php echo htmlspecialchars($job['department']); ?></span></td>
            <td style="color:#888; font-size:12px;"><?php echo date('M d, Y', strtotime($job['posted_at'])); ?></td>
            <td><a href="admin.php?delete_job=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this job?')"><i class="ti ti-trash"></i> Delete</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php renderFooter(); ?>
