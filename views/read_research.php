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

//fetch the research
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

//fetch the comments
$comment_sql= "SELECT comments.*, users.full_name FROM comments JOIN users ON comments.user_id = users.user_id WHERE research_id = ? ORDER BY created_at ASC";

$c_stmt = $conn->prepare($comment_sql);
$c_stmt->bind_param("i", $id);
$c_stmt->execute();
$comments_result = $c_stmt->get_result();

$comments = [];
$replies = [];

while ($row = $comments_result->fetch_assoc()) {
    if($row['parent_id'] === NULL) {
        $comments[]= $row; // as a top level comment
    } else {
        $replies[$row['parent_id']][]= $row; //reply
    }
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

<div class ="comments-section" id="comments">
    <h3>Discussion (<?php echo $comments_result->num_rows; ?>)</h3>

    <?php if(isset($_SESSION['auth']) && $_SESSION['auth'] === true): ?>
    <form action="../controllers/submit_comment.php" method="POST" style="margin-bottom: 30px;">
        <input type="hidden" name="research_id" value="<?php echo $id; ?>">
        <textarea name="comment_text" class="comment-input" rows="3" placeholder="Add to Comments..." required></textarea>
        <button type="submit" class="read-btn">Post Comment</button>
    </form>
    <?php else: ?>
    <p style="color: #888; margin-bottom: 20px;">
        <a href="login.html" style="color: #e94560;">Login</a> to join the discussion.
    </p>
    <?php endif; ?>

    <?php foreach($comments as $comment): ?>
    <div class="comment-box">
        <div class="comment-header">
            <strong><?php echo htmlspecialchars($comment['full_name']); ?></strong>
            <span><?php echo date('M d, H:i', strtotime($comment['created_at'])); ?></span>
    </div>
        <div class="comment-text">
            <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
        </div>

        <?php if(isset($_SESSION['auth'])): ?>
        <button class="reply-btn" onclick="toggleReply('reply-form-<?php echo $comment['comment_id']; ?>')">↩ Reply</button>

        <div id="reply-form-<?php echo $comment['comment_id']; ?>" class="reply-form-container" style="display: none;">
            <form action="../controllers/submit_comment.php" method="POST">
                <input type="hidden" name="research_id" value="<?php echo $id; ?>">
                <input type="hidden" name="parent_id" value="<?php echo $comment['comment_id']; ?>">
                <textarea name="comment_text" class="comment-input" rows="2" placeholder="Reply to <?php echo htmlspecialchars($comment['full_name']); ?>..." required></textarea>
                <button type="submit" class="reply-btn" style="font-size: 0.8em; padding: 5px 15px;">Post Reply</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if(isset($replies[$comment['comment_id']])): ?>
        <?php foreach ($replies[$comment['comment_id']] as $reply): ?>
        <div class="reply-box">
            <div class="comment-header">
                <strong><?php echo htmlspecialchars($reply['full_name']); ?></strong>
                <span><?php echo date('M d,H:i', strtotime($reply['created_at'])); ?></span>
            </div>
            <div class="comment-text">
                <?php echo nl2br(htmlspecialchars($reply['comment_text'])); ?>
            </div>
</div>
       <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endforeach;?>

    <?php if(count($comments) === 0): ?>
    <p style="color: #888; text-align: center;">No Comments yet. Be the first to share your thoughts!</p>
    <?php endif; ?>
</div>

<div class="footer-nav">
<a href="../index.php" class="back-link" style="color: #888;">Return to Dashboard</a>
</div>
    </div>

<script>
    function toggleReply(id) {
        var x= document.getElementById(id);
        if (x.style.display=== 'none' || x.style.display === "") {
            x.style.display = 'block';
        } else {
            x.style.display = 'none';
        }
    }
</script>

</body>
</html>
