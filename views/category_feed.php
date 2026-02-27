<?php
/** * @var mysqli $conn */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/db_connection.php';

$category = isset($_GET['cat']) ? $_GET['cat'] : '';
$valid_cats= ['Malware', 'Phishing', 'DoS', 'SQLi', 'XSS', 'MITM', 'Password', 'SupplyChain', 'ZeroDay'];
if (!in_array($category, $valid_cats)) {
    echo "<h2 style='color: white; text-align:center; margin-top: 50px;'>Invalid Category</h2>";
    exit();
}

$sql = "SELECT research.*, users.full_name FROM research 
    JOIN users ON research.user_id = users.user_id WHERE category = ? ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($category); ?> Research | Cyber Lens</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {background-color: #1a1a2e; color: #fff; font-family: 'Segoe UI', sans-serif;}
        .feed-container { max-width: 900px; margin: 40px auto; padding: 20px; }
        .header-area { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e94560; padding-bottom: 15px; margin-bottom: 30px;}
        .back-btn { text-decoration: none; color: #fff; background: #ef3460; padding: 8px 15px; border-radius: 5px; transition: 0.3s;}
        .back-btn:hover {background: #e94560;}
        .research-card { background: #162447; border: 1px solid #1f4068; border-radius: 10px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.2s;}
        .research-card:hover { transform: translateY(-5px); margin-bottom: 15px; }
        .card-header {display: flex; justify-content: space-between; margin-bottom: 15px;}
        .card-title {margin: 0;color: #efc07b; font-size: 1.4em;}
        .badge {padding: 5px 10px; border-radius: 4px; font-weight:bold; font-size: 0.85em; text-transform: uppercase;}
        .badge-critical {background: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c;}
        .badge-high {background: rgba(230, 126, 34, 0.2); color: #e67e22; border: 1px solid #e67e22;}
        .badge-medium{background: rgba(241,196,15,0.2); color: #f1c40f; border: 1px solid #f1c40f;}
        .badge-low {background: rgba(46,204,113,0.2); color: #2ecc71; border: 1px solid #2ecc71;}
        .meta {color: #888; font-size: 0.9em; margin-bottom: 15px; font-style: italic;}
        .desc {color: #dcdcdc; line-height: 1.6; margin-bottom: 20px;}
        .read-btn { display: inline-block; background: transparent; color:#e94560; border: 1px solid #e94560; padding: 8px 20px; text-decoration: none; border-radius: 5px;transition: 0.3s;}
        .read-btn:hover {background: #e94560; color: white;}
    </style>
</head>
<body>
<div class="feed-container">
    <div class="header-area">
        <h1><?php echo htmlspecialchars($category);?> Intelligence</h1>
        <a href="../index.php" class="back-btn">← Dashboard</a>
    </div>

<?php if($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<div class="research-card">
    <div class="card-header">
        <h3 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h3>
        <span class="badge badge-<?php echo strtolower($row['severity']);?>">
            <?php echo htmlspecialchars($row['severity']);?>
        </span>
    </div>
    <div class="meta">
Uploaded by <?php echo htmlspecialchars($row['full_name']); ?> •
    <?php echo date('M D, Y', strtotime($row['created_at'])); ?>
    <?php if($row['is_ieee']): ?>
    <span style="color: #3498db; margin-left: 10px;">🎓 IEEE Cited</span>
    <?php endif; ?>
</div>
<p class ="desc">
    <?php echo nl2br(htmlspecialchars($row['description'])); ?>
</p>
<a href ="#" class="read-btn">Read Full Paper</a>
</div>
<?php endwhile; ?>
<?php else: ?>
<div style="text-align: center; color: #888; padding: 50px;">
    <h3>No research found for this category yet.</h3>
    <p>Be the first to contribute!</p>
</div>
<?php endif; ?>
</div>
</body>
</html>