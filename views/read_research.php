<?php
/** * @var mysqli $conn */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/db_connection.php';

$id = isset( $_GET['id'] ) ? intval($_GET['id']) : 0;

if ($id == 0) {
    echo "<h2 style='color: white; text-align: center; margin-top: 50px;'>Invalid Request</h2>";
    exit();
}

$sql= "SELECT research.*, users.full_name FROM research JOIN users ON research.user_id = users.user_id WHERE research.research_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$paper = $result->fetch_assoc();

if(!$paper) {
    echo "<h2 style='color: white; text-align: center; margin-top: 50px;'>Research not found.</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($paper['title']); ?> | Cyber Lens</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #1a1a2e; color: #fff; margin: 0; font-family: 'Segoe UI', sans-serif;}
    </style>
</head>
<body>
    <div class="paper-container">
        <div class="back-nav">
            <a href="category_feed.php?cat=<?php echo urlencode($paper['category']); ?>" class="back-link">
                ← Back to <?php echo htmlspecialchars($paper['category']); ?> Feed
            </a>
        </div>
        <div class="paper-header">
            <h1 class="paper-title"><?php echo htmlspecialchars($paper['title']); ?></h1>
            <div class="meta-info">
                <div> By <strong> <?php echo htmlspecialchars($paper['full_name']); ?></strong>
                • <?php echo date('F d, Y', strtotime($paper['created_at'])); ?>
            </div>

            <span class="badge badge-<?php echo strtolower($paper['severity']); ?>">
                Severity: <?php echo htmlspecialchars($paper['severity']); ?>
            </span>
        </div>
        <?php if($paper['is_ieee']): ?>
        <div class="ieee-citation">
            🎓 This Paper meets IEEE Academic Citation Standards
        </div>
        <?php endif; ?>
    </div>
    <div class="content-body">
        <?php echo nl2br(htmlspecialchars($paper['content'])); ?>
    </div>

<div class="footer-nav">
<a href="../index.php" class="back-link" style="color: #888;">Return to Dashboard</a>
</div>


</body>
</html>
