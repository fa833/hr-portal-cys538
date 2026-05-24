<?php
session_start();
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

renderHeader('View File - VULNERABLE', 'admin');
?>
<div class="page-header">
    <h1>CV Viewer <span style="background:#fcebeb; color:#a32d2d; font-size:13px; padding:4px 10px; border-radius:6px; margin-left:10px;">VULNERABLE VERSION</span></h1>
    <p>This page is intentionally vulnerable to demonstrate the LFI attack.</p>
</div>
<div class="card">
    <div class="card-header"><h3><i class="ti ti-file"></i> File Viewer</h3></div>
    <div class="card-body">
        <?php
        if(isset($_GET['file']) && !empty($_GET['file'])) {
            $file = $_GET['file'];
            // !! VULNERABLE CODE - No validation !!
            $filepath = 'uploads/' . $file;
            if(file_exists($filepath)) {
                echo "<div style='background:#f8f9fa; padding:20px; border-radius:8px;'>";
                echo "<pre style='font-size:13px; color:#333; white-space:pre-wrap;'>";
                echo htmlspecialchars(file_get_contents($filepath));
                echo "</pre></div>";
            } else {
                // VULNERABLE: reads any file on the system
                echo "<div style='background:#f8f9fa; padding:20px; border-radius:8px;'>";
                echo "<pre style='font-size:13px; color:#333; white-space:pre-wrap;'>";
                $content = file_get_contents($file);
                if($content !== false) { echo htmlspecialchars($content); }
                else { echo "Could not read file."; }
                echo "</pre></div>";
            }
        } else {
            echo "<div style='text-align:center; padding:40px; color:#aaa;'><i class='ti ti-file-off' style='font-size:40px;'></i><p style='margin-top:12px;'>No file selected.</p></div>";
        }
        ?>
    </div>
</div>
<?php renderFooter(); ?>
