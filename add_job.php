<?php
session_start();
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $department = trim($_POST['department']);
    $description = trim($_POST['description']);
    if (empty($title)||empty($department)||empty($description)) {
        $error = "All fields are required.";
    } elseif (strlen($title) > 100) {
        $error = "Job title is too long.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO jobs (title, department, description) VALUES (?, ?, ?)");
        $stmt->execute([$title, $department, $description]);
        $success = "Job posted successfully!";
    }
}
renderHeader('Add New Job', 'admin');
?>
<div class="page-header"><h1>Post New Job</h1><p>Add a new open position to the HR portal.</p></div>
<div style="max-width:680px;">
    <div class="card">
        <div class="card-header"><h3><i class="ti ti-briefcase"></i> Job Details</h3></div>
        <div class="card-body">
            <?php if($error): ?><div class="alert alert-error"><i class="ti ti-alert-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert alert-success"><i class="ti ti-check"></i> <?php echo htmlspecialchars($success); ?> <a href="admin.php" style="color:#3b6d11; font-weight:bold;">Back to Admin</a></div><?php endif; ?>
            <form method="POST" action="add_job.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="title" placeholder="e.g. Software Engineer" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" required>
                            <option value="">Select Department</option>
                            <option value="Engineering">Engineering</option>
                            <option value="IT Security">IT Security</option>
                            <option value="Human Resources">Human Resources</option>
                            <option value="Business Intelligence">Business Intelligence</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Legal">Legal</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Job Description</label>
                    <textarea name="description" rows="6" placeholder="Describe the role, responsibilities and requirements..." required></textarea>
                </div>
                <div style="display:flex; gap:12px;">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-send"></i> Post Job</button>
                    <a href="admin.php" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php renderFooter(); ?>

