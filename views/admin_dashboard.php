<?php
session_start();
include '../config/db_connection.php';

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<script>alert('Access Denied: Admin only'); window.location.href='../index.php';</script>";
    exit();
}

/** @var mysqli $conn */

// Get filter parameter
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $is_solved = isset($_POST['is_solved']) ? 1 : 0;

    if ($action === 'keep' || $action === 'remove') {
        $stmt = $conn->prepare("UPDATE reports SET admin_action = ?, is_solved = ? WHERE report_id = ?");
        $stmt->bind_param("sii", $action, $is_solved, $report_id);
        $stmt->execute();
        $stmt->close();

        // If action is remove, delete the content
        if ($action === 'remove' && $is_solved === 1) {
            $report = $conn->query("SELECT target_type, target_id FROM reports WHERE report_id = $report_id")->fetch_assoc();

            $table = '';
            $id_col = '';
            if ($report['target_type'] === 'community_post') { $table = 'community_posts'; $id_col = 'post_id'; }
            elseif ($report['target_type'] === 'community_comment') { $table = 'community_comments'; $id_col = 'comment_id'; }
            elseif ($report['target_type'] === 'research') { $table = 'research'; $id_col = 'research_id'; }
            elseif ($report['target_type'] === 'research_comment') { $table = 'comments'; $id_col = 'comment_id'; }

            if ($table) {
                $conn->query("DELETE FROM $table WHERE $id_col = " . intval($report['target_id']));
            }
        }

        header('Location: admin_dashboard.php?tab=' . $tab);
        exit();
    }
}

// Get reports based on tab
if ($tab === 'solved') {
    $result = $conn->query("
        SELECT r.*, u.full_name as reporter_name 
        FROM reports r 
        LEFT JOIN users u ON r.reporter_user_id = u.user_id 
        WHERE r.is_solved = 1 
        ORDER BY r.created_at DESC
    ");
} else {
    $result = $conn->query("
        SELECT r.*, u.full_name as reporter_name 
        FROM reports r 
        LEFT JOIN users u ON r.reporter_user_id = u.user_id 
        WHERE r.is_solved = 0 
        ORDER BY r.created_at DESC
    ");
}

// Get pending count
$pending_count = $conn->query("SELECT COUNT(*) as count FROM reports WHERE is_solved = 0")->fetch_assoc()['count'];
$solved_count = $conn->query("SELECT COUNT(*) as count FROM reports WHERE is_solved = 1")->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Cyber Library</title>
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

<div class="admin-container">
    <a href="../index.php" class="back-btn">← Back to Dashboard</a>

    <div class="admin-header">
        <h1>>> ADMIN_REPORTS_DASHBOARD</h1>
        <p>// Manage reported content</p>
    </div>

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-box">
            <h3>PENDING REPORTS</h3>
            <div class="stat-number"><?php echo $pending_count; ?></div>
        </div>
        <div class="stat-box">
            <h3>SOLVED REPORTS</h3>
            <div class="stat-number"><?php echo $solved_count; ?></div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-btn <?php echo ($tab === 'pending') ? 'active' : ''; ?>" onclick="window.location.href='?tab=pending'">
            ⧗ PENDING (<?php echo $pending_count; ?>)
        </button>
        <button class="tab-btn <?php echo ($tab === 'solved') ? 'active' : ''; ?>" onclick="window.location.href='?tab=solved'">
            ✓ SOLVED (<?php echo $solved_count; ?>)
        </button>
    </div>

    <!-- Reports List -->
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($report = $result->fetch_assoc()): ?>
            <div class="report-item">
                <div class="report-header">
                    <span class="report-id">REPORT #<?php echo $report['report_id']; ?></span>
                    <span class="report-date"><?php echo date('M d, Y H:i', strtotime($report['created_at'])); ?></span>
                </div>

                <div class="report-info">
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

                <div class="report-info">
                    <span class="report-label">Type:</span>
                    <span class="report-value">[<?php echo strtoupper(str_replace('_', '_', $report['target_type'])); ?>]</span>
                </div>

                <div class="report-info">
                    <span class="report-label">Target ID:</span>
                    <span class="report-value">#<?php echo $report['target_id']; ?></span>
                </div>

                <div class="report-reason">
                    <span class="report-label">> Reason:</span><br>
                    <?php echo htmlspecialchars($report['reason']); ?>
                </div>

                <div class="report-actions">
                    <!--<a href="admin_review_report.php?id=<?php echo $report['report_id']; ?>" class="action-btn"> -->
                    <a href="admin_review_report.php?report_id=<?php echo $report['report_id']; ?>" class="action-btn">
                        📄 REVIEW CONTENT
                    </a>

                    <?php if ($tab === 'pending'): ?>
                        <form method="POST" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                            <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">

                            <button type="submit" name="action" value="keep" class="action-btn">
                                ✓ KEEP
                            </button>

                            <button type="submit" name="action" value="remove" class="action-btn remove">
                                ✗ REMOVE
                            </button>

                            <div class="checkbox-group">
                                <input type="checkbox" id="solved_<?php echo $report['report_id']; ?>" name="is_solved" value="1">
                                <label for="solved_<?php echo $report['report_id']; ?>">MARK SOLVED?</label>
                            </div>
                        </form>
                    <?php else: ?>
                        <div style="color: #00aa00; font-size: 0.9em;">
                            <span class="report-label">Action Taken:</span>
                            <span class="report-value">
                                <?php
                                if (isset($report['admin_action']) && !empty($report['admin_action'])) {
                                    echo strtoupper($report['admin_action']);
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-reports">
            <?php if ($tab === 'pending'): ?>
                >> NO PENDING REPORTS<br>
                <span style="color: #008800; font-size: 0.85em;">All reports have been handled.</span>
            <?php else: ?>
                >> NO SOLVED REPORTS YET<br>
                <span style="color: #008800; font-size: 0.85em;">No reports have been resolved.</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>