<?php
session_start();
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) { header("Location: jobs.php"); exit(); }

$job_id = (int)$_GET['job_id'];
$error = ''; $success = '';

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch();
if (!$job) { header("Location: jobs.php"); exit(); }

$stmt = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
$stmt->execute([$_SESSION['user_id'], $job_id]);
$already_applied = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$already_applied) {
    if (empty($_FILES['cv']['name'])) {
        $error = "Please upload your CV.";
    } else {
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        $file_ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_extensions)) {
            $error = "Only PDF, DOC, and DOCX files are allowed.";
        } elseif ($_FILES['cv']['size'] > 5 * 1024 * 1024) {
            $error = "File size must not exceed 5MB.";
        } else {
            $cv_filename = 'cv_' . $_SESSION['user_id'] . '_' . $job_id . '_' . time() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['cv']['tmp_name'], __DIR__ . '/uploads/' . $cv_filename)) {
                $stmt = $pdo->prepare("INSERT INTO applications (user_id, job_id, cv_filename) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $job_id, $cv_filename]);
                $success = "Your application has been submitted successfully!";
                $already_applied = true;
            } else {
                $error = "Failed to upload file. Please try again.";
            }
        }
    }
}

renderHeader('Apply for Job', 'jobs');
?>
<div class="page-header">
    <h1>Apply for Position</h1>
    <p>Submit your application for the selected role at NovaCorp.</p>
</div>
<div class="two-col">
    <div class="col-left">
        <div class="card">
            <div class="card-header"><h3><i class="ti ti-send"></i> Your Application</h3></div>
            <div class="card-body">
                <?php if($error): ?><div class="alert alert-error"><i class="ti ti-alert-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                <?php if($success): ?><div class="alert alert-success"><i class="ti ti-check"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
                <?php if($already_applied && !$success): ?>
                <div class="alert alert-info"><i class="ti ti-info-circle"></i> You have already applied for this position.</div>
                <?php endif; ?>
                <?php if(!$already_applied): ?>
                <form method="POST" action="apply.php?job_id=<?php echo $job_id; ?>" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Position Applying For</label>
                        <input type="text" value="<?php echo htmlspecialchars($job['title']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Upload CV / Resume</label>
                        <div style="border:2px dashed #ddd; border-radius:8px; padding:30px; text-align:center; background:#fafafa;">
                            <i class="ti ti-upload" style="font-size:32px; color:#aaa;"></i>
                            <p style="color:#888; font-size:13px; margin:10px 0 16px;">Click to upload your CV</p>
                            <input type="file" name="cv" id="cv" accept=".pdf,.doc,.docx" style="display:none;" onchange="showFileName(this)">
                            <label for="cv" class="btn btn-outline" style="cursor:pointer;"><i class="ti ti-file"></i> Choose File</label>
                            <p id="file-name" style="margin-top:12px; font-size:13px; color:#4e73df; font-weight:bold;"></p>
                            <p style="font-size:11px; color:#aaa; margin-top:8px;">PDF, DOC, DOCX — Max 5MB</p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:13px;">
                        <i class="ti ti-send"></i> Submit Application
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-right">
        <div class="card">
            <div class="card-header"><h3><i class="ti ti-briefcase"></i> Job Details</h3></div>
            <div class="card-body">
                <div style="font-size:18px; font-weight:bold; color:#1a2332;"><?php echo htmlspecialchars($job['title']); ?></div>
                <div style="font-size:13px; color:#888; margin-top:6px;"><i class="ti ti-building"></i> <?php echo htmlspecialchars($job['department']); ?></div>
                <div style="border-top:1px solid #f0f0f0; padding-top:16px; margin-top:16px;">
                    <p style="font-size:13px; color:#555; line-height:1.7;"><?php echo htmlspecialchars($job['description']); ?></p>
                </div>
                <div style="border-top:1px solid #f0f0f0; padding-top:16px; margin-top:16px; display:flex; flex-direction:column; gap:10px;">
                    <div style="display:flex; justify-content:space-between; font-size:13px;"><span style="color:#888;"><i class="ti ti-map-pin"></i> Location</span><span style="font-weight:bold;">Riyadh, KSA</span></div>
                    <div style="display:flex; justify-content:space-between; font-size:13px;"><span style="color:#888;"><i class="ti ti-clock"></i> Type</span><span style="font-weight:bold;">Full Time</span></div>
                </div>
                <a href="jobs.php" class="btn btn-outline" style="width:100%; justify-content:center; margin-top:20px;"><i class="ti ti-arrow-left"></i> Back to Jobs</a>
            </div>
        </div>
    </div>
</div>
<script>
function showFileName(input) {
    document.getElementById('file-name').textContent = input.files[0] ? '✓ ' + input.files[0].name : '';
}
function validateForm() {
    var cv = document.getElementById('cv');
    if (!cv.files[0]) { alert("Please upload your CV."); return false; }
    var ext = cv.files[0].name.split('.').pop().toLowerCase();
    if (!['pdf','doc','docx'].includes(ext)) { alert("Only PDF, DOC, DOCX allowed."); return false; }
    if (cv.files[0].size > 5*1024*1024) { alert("Max file size is 5MB."); return false; }
    return true;
}
</script>
<?php renderFooter(); ?>
