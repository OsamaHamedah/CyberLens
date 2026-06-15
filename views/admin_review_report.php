<?php

session_start();
include '../config/db_connection.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<script>alert('Access Denied'); window.location.href='../index.php';</script>";
    exit();
}

/** @var mysqli $conn */
$report_id= isset($_GET['report_id']) ? intval($_GET['report_id']) : 0;

if($report_id <= 0){
    header('Location: admin_dashboard.php');
    exit();
}

$report = $conn->query("SELECT r.*, u.full_name as report_name
                               FROM reports r LEFT JOIN users u ON r.report_user_id = u.user_id
                               WHERE r.report_id = $report_id ")->fetch_assoc();

if (!$report) {
    header('Location: admin_dashboard.php');
    exit();
}

$content = null;
$table = '';
$id_col= '';
$content_field = '';

if ($report['target_type'] === 'community_post') {
    $table = 'community_posts';
    $id_col = 'post_id';
} elseif ($report['target_type'] === 'community_comment') {
    $table = 'community_comments';
    $id_col = 'comment_id';
} elseif ($report['target_type'] === 'research') {
    $table = 'research';
    $id_col = 'research_id';
} elseif ($report['target_type'] === 'research_comment') {
    $table = 'comments';
    $id_col = 'comment_id';
}

if ($table) {
    $content = $conn->query("SELECT * FROM $table WHERE $id_col = " .intval($report['target_id']))->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Report | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/adminDash.css">
</head>
<body>
<div class="dashboard-header">
    <h2 style="margin: 0; color: #e94560;">Cyber Library</h2>
    <div class="user-controls">
        <span class="welcome-text">Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
        &nbsp; | &nbsp;
        <a href="admin_dashboard.php" class="login-link" style="background-color: #00ff00; color: #0a0a0a; font-weight: bold; border: 2px solid #00ff00;">⚙ ADMIN</a>
        &nbsp; | &nbsp;
        <a href="../controllers/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="review-container">
    <a href="admin_dashboard.php" class="back-btn">← BACK TO DASHBOARD</a>

    <div class="review-header">
        <h1>>> REVIEW_REPORT_#<?php echo $report['report_id']; ?></h1>
        <p style="color: #00aa00; margin: 5px 0;">// View reported content</p>
    </div>

    <!-- Report Details -->
    <div class="section">
        <h2>📋 Report Details</h2>

        <div class="info-row">
            <span class="report-label">Report ID:</span>
            <span class="report-value">#<?php echo $report['report_id']; ?></span>
        </div>

        <div class="info-row">
            <span class="report-label">Reporter:</span>
            <span class="report-value">
                <?php
                if (isset($report['reporter_name']) && !empty($report['reporter_name'])) {
                    echo htmlspecialchars($report['reporter_name']);
                } else {
                    echo 'Unknown';
                }
                ?>
            </span>
        </div>

        <div class="info-row">
            <span class="report-label">Content Type:</span>
            <span class="report-value">[<?php echo strtoupper($report['target_type']); ?>]</span>
        </div>

        <div class="info-row">
            <span class="report-label">Content ID:</span>
            <span class="report-value">#<?php echo $report['target_id']; ?></span>
        </div>

        <div class="info-row">
            <span class="report-label">Date:</span>
            <span class="report-value"><?php echo date('M d, Y H:i:s', strtotime($report['created_at'])); ?></span>
        </div>

        <div class="content-box" style="margin-top: 15px;">
            <span class="report-label">> REASON:</span><br>
            <?php echo htmlspecialchars($report['reason']); ?>
        </div>
    </div>

    <!-- Reported Content -->
    <?php if ($content): ?>
        <div class="section">
            <h2>📄 Reported Content</h2>

            <div class="content-box">
                <?php if ($report['target_type'] === 'community_post'): ?>
                    <div class="title">POST: <?php echo htmlspecialchars($content['title']); ?></div>
                    <div class="meta">by User ID: <?php echo $content['user_id']; ?> • <?php echo date('M d, Y', strtotime($content['created_at'])); ?></div>
                    <div style="margin-top: 10px; white-space: pre-wrap;">
                        <?php echo htmlspecialchars($content['body']); ?>
                    </div>
                <?php elseif ($report['target_type'] === 'research'): ?>
                    <div class="title">RESEARCH: <?php echo htmlspecialchars($content['title']); ?></div>
                    <div class="meta">Category: <?php echo htmlspecialchars($content['category']); ?> | Severity: <?php echo htmlspecialchars($content['severity']); ?></div>
                    <div style="margin-top: 10px;">
                        <strong style="color: #00ff00;">Description:</strong><br>
                        <?php echo htmlspecialchars($content['description']); ?>
                    </div>
                <?php else: ?>
                    <div class="meta">
                        COMMENT ID:
                        <?php
                        if (isset($content['comment_id'])) {
                            echo $content['comment_id'];
                        } elseif (isset($content['id'])) {
                            echo $content['id'];
                        }
                        ?>
                    </div>
                    <div style="margin-top: 10px; white-space: pre-wrap;">
                        <?php
                        if (isset($content['body'])) {
                            echo htmlspecialchars($content['body']);
                        } elseif (isset($content['comment_text'])) {
                            echo htmlspecialchars($content['comment_text']);
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="section">
            <h2>⚠️ Content Not Found</h2>
            <p style="color: #ff4444;">The reported content has already been deleted.</p>
        </div>
    <?php endif; ?>

    <!-- Action Section -->
    <div class="section">
        <h2>⚡ Actions</h2>
        <p style="color: #00aa00; margin: 0 0 15px 0;">Go back to dashboard to take action on this report.</p>
        <a href="admin_dashboard.php" class="back-btn">← BACK TO DASHBOARD</a>
    </div>
</div>

</body>
</html>
