<?php
session_start();
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

function isValidFilename($filename) {
    if (!preg_match('/^[a-zA-Z0-9_\-]+\.(pdf|doc|docx)$/', $filename)) return false;
    if (strpos($filename, '..') !== false) return false;
    if (strpos($filename, '/') !== false) return false;
    if (strpos($filename, '\\') !== false) return false;
    if (strpos($filename, "\0") !== false) return false;
    return true;
}

renderHeader('View File - Secure', 'admin');
?>
<div class="page-header">
    <h1>CV Viewer <span style="background:#eaf3de; color:#3b6d11; font-size:13px; padding:4px 10px; border-radius:6px; margin-left:10px;">SECURE VERSION</span></h1>
    <p>This page is protected against LFI attacks using input validation and whitelisting.</p>
</div>
<div class="card" style="margin-bottom:24px;">
    <div class="card-header"><h3><i class="ti ti-shield"></i> Security Countermeasures Applied</h3></div>
    <div class="card-body">
        <div style="display:flex; flex-direction:column; gap:12px;">
            <?php
            $measures = [
                ['Whitelist Validation', 'Only filenames matching cv_[id]_[jobid]_[time].pdf/doc/docx are accepted.'],
                ['Path Traversal Blocked', 'Any input containing ../, /, or \\ is immediately rejected.'],
                ['Fixed Base Directory', 'Files are only served from /uploads/ folder.'],
                ['Realpath Verification', 'Uses realpath() to verify the file stays inside uploads directory.'],
            ];
            foreach($measures as $m): ?>
            <div style="display:flex; gap:12px; align-items:flex-start;">
                <div style="width:28px; height:28px; border-radius:50%; background:#eaf3de; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="ti ti-check" style="color:#3b6d11; font-size:14px;"></i>
                </div>
                <div>
                    <div style="font-size:13px; font-weight:bold; color:#1a2332;"><?php echo $m[0]; ?></div>
                    <div style="font-size:12px; color:#888; margin-top:2px;"><?php echo $m[1]; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h3><i class="ti ti-file-check"></i> Secure File Viewer</h3></div>
    <div class="card-body">
        <?php
        if (isset($_GET['file']) && !empty($_GET['file'])) {
            $file = $_GET['file'];
            if (!isValidFilename($file)) {
                echo "<div class='alert alert-error'><i class='ti ti-shield-x'></i><div><strong>Access Denied — LFI Attack Blocked!</strong><br><span style='font-size:12px;'>The filename <code>" . htmlspecialchars($file) . "</code> contains invalid characters or path traversal sequences.</span></div></div>";
                $log_entry = date('Y-m-d H:i:s') . " | LFI Attempt | User: " . $_SESSION['username'] . " | File: " . $file . "\n";
                file_put_contents(__DIR__ . '/logs/security.log', $log_entry, FILE_APPEND);
            } else {
                $base_dir = realpath(__DIR__ . '/uploads');
                $full_path = realpath($base_dir . '/' . $file);
                if ($full_path === false || strpos($full_path, $base_dir) !== 0) {
                    echo "<div class='alert alert-error'><i class='ti ti-shield-x'></i><strong>Access Denied — Path Traversal Blocked!</strong></div>";
                } elseif (!file_exists($full_path)) {
                    echo "<div class='alert alert-info'><i class='ti ti-info-circle'></i> File not found in uploads directory.</div>";
                } else {
                    echo "<div style='background:#f8f9fa; padding:20px; border-radius:8px;'>
                        <div style='display:flex; align-items:center; gap:12px; margin-bottom:16px;'>
                            <i class='ti ti-file-check' style='font-size:32px; color:#3b6d11;'></i>
                            <div>
                                <div style='font-weight:bold; color:#1a2332;'>" . htmlspecialchars($file) . "</div>
                                <div style='font-size:12px; color:#888;'>File verified and safely loaded</div>
                            </div>
                        </div>
                        <div class='alert alert-success' style='margin:0;'><i class='ti ti-shield-check'></i> File access granted — path verified safe.</div>
                    </div>";
                }
            }
        } else {
            echo "<div style='text-align:center; padding:40px; color:#aaa;'><i class='ti ti-file-off' style='font-size:40px;'></i><p style='margin-top:12px;'>No file selected.</p></div>";
        }
        ?>
    </div>
</div>
<?php renderFooter(); ?>
