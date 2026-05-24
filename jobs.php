<?php
session_start();
require 'db.php';
require 'header.php';

$stmt = $pdo->prepare("SELECT * FROM jobs ORDER BY posted_at DESC");
$stmt->execute();
$jobs = $stmt->fetchAll();

renderHeader('Job Listings', 'jobs');
?>
<div class="page-header">
    <h1>Job Listings</h1>
    <p>Browse all open positions at NovaCorp. Find the right opportunity for you.</p>
</div>
<?php if(count($jobs) > 0): ?>
<div style="display:flex; flex-direction:column; gap:16px;">
    <?php foreach($jobs as $job): ?>
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="display:flex; align-items:center; justify-content:space-between; gap:20px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="width:48px; height:48px; border-radius:10px; background:#e6f1fb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="ti ti-briefcase" style="font-size:22px; color:#185fa5;"></i>
                </div>
                <div>
                    <div style="font-size:16px; font-weight:bold; color:#1a2332;"><?php echo htmlspecialchars($job['title']); ?></div>
                    <div style="font-size:13px; color:#888; margin-top:4px; display:flex; gap:16px;">
                        <span><i class="ti ti-building" style="font-size:13px;"></i> <?php echo htmlspecialchars($job['department']); ?></span>
                        <span><i class="ti ti-calendar" style="font-size:13px;"></i> Posted <?php echo date('M d, Y', strtotime($job['posted_at'])); ?></span>
                    </div>
                    <div style="font-size:13px; color:#555; margin-top:8px; max-width:600px; line-height:1.6;">
                        <?php echo htmlspecialchars(substr($job['description'], 0, 150)) . '...'; ?>
                    </div>
                </div>
            </div>
            <div style="flex-shrink:0;">
                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="apply.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary"><i class="ti ti-send"></i> Apply Now</a>
                <?php else: ?>
                <a href="login.php" class="btn btn-outline"><i class="ti ti-login"></i> Login to Apply</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body" style="text-align:center; padding:60px 20px;">
        <i class="ti ti-briefcase-off" style="font-size:48px; color:#ccc;"></i>
        <h3 style="color:#888; margin-top:16px;">No open positions at the moment</h3>
        <p style="color:#aaa; margin-top:8px; font-size:14px;">Please check back later.</p>
    </div>
</div>
<?php endif; ?>
<?php renderFooter(); ?>
