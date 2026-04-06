<?php
include '../config/db_connection.php';

$isAuth = isset($_SESSION['auth']) && $_SESSION['auth'] === true;
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($postId <= 0) {
    header("Location: community.php");
    exit();
}
/* In the following SQL query, I mistakenly mismatched the full_name from the DB with user_name so I used: // COALESCE(existing) AS mismatched // to correctly contact the DB. */
$sqlPost = "
SELECT p.post_id, p.title, p.body, p.created_at, p.user_id, p.accepted_comment_id, COALESCE(u.full_name, u.email) AS user_name, (SELECT COALESCE(SUM(v.vote),0) 
FROM community_votes v WHERE v.target_type='post' AND v.target_id=p.post_id) AS score FROM community_posts p
JOIN users u ON u.user_id = p.user_id WHERE p.post_id=? LIMIT 1
";
/** @var mysqli $conn */
$stmt = $conn->prepare($sqlPost);
if (!$stmt) {
    die("DB error: " . $conn->error);
}
$stmt->bind_param("i", $postId);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    header("Location: community.php");
    exit();
}

$acceptedId = $post['accepted_comment_id'] ? intval($post['accepted_comment_id']) : 0;
//to be continued (load Comments + Score)

/* In the following SQL query, I mistakenly mismatched the full_name from the DB with user_name so I used: // COALESCE(existing) AS mismatched // to correctly contact the DB. */
$sqlComments = "SELECT c.comment_id, c.body, c.created_at, c.user_id, COALESCE(u.full_name, u.email) AS user_name, (SELECT COALESCE(SUM(v.vote),0)
FROM community_votes v
WHERE v.target_type='comment' AND v.target_id=c.comment_id) AS score
FROM community_comments c JOIN users u ON u.user_id = c.user_id
WHERE c.post_id=? ORDER BY (c.comment_id=?) DESC, score DESC, c.created_at ASC";

$stmt = $conn->prepare($sqlComments);
if (!$stmt) {
    die("DB error: " . $conn->error);
}
$stmt->bind_param("ii", $postId, $acceptedId);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?php echo htmlspecialchars($post['title']);?> | Community</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="dashboard-header">
    <h2 style="margin: 0; color: #e94560;">Cyber Lens Community</h2>
    <div class="user-controls">
        <a href="community.php" class="login-link">← Back</a>
    </div>
</div>

<div class="main-wrapper">
    <!--post-->
    <div style="background: #162447; border:1px solid #1f4068; padding: 15px; border-radius:8px;">
        <h1 style="margin-top: 0; color: #efc07b;"><?php echo htmlspecialchars($post['title']); ?></h1>

        <div style="color: #aaa; font-size: 0.9em;">
            by <?php echo htmlspecialchars($post['user_name']); ?> • <?php echo htmlspecialchars($post['created_at']); ?>
        </div>

        <p style="color: #ddd; margin-top: 12px; white-space:pre-wrap;"> <?php echo htmlspecialchars($post['body']); ?></p>

        <!-- post's votes -->
        <div style="display: flex; align-items: center; gap:10px; margin-top: 10px;">
            <button onclick="vote('post', <?php echo intval($postId); ?>, 1)"
                    style="background: #0f3460; border: 1px solid #efc07b; color: #efc07b; padding:6px 10px; border-radius: 6px; cursor:pointer;">▲</button>
            <span id="score-post-<?php echo intval($postId); ?>" style="color:#fff;">
            <?php echo intval($post['score']);?>
            </span>

            <button onclick="vote('post', <?php echo intval($postId); ?>, -1)"
                    style="background: #0f3460; border: 1px solid #efc07b; color: #efc07b; padding: 6px 10px; border-radius: 6px; cursor: pointer;">▼</button>

            <?php if (!$isAuth): ?>
            <span style="color:#aaa; margin-left: 10px;">Login to vote/comment.</span>
            <?php endif; ?>
        </div>
    </div>

    <h2 style="color:#efc07b; margin-top:25px;">Answers</h2>

    <!-- comments list -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php while ($c = $comments->fetch_assoc()):
        $isAccepted= ($acceptedId > 0 && intval($c['comment_id'])=== $acceptedId);?>
        <div style="background: #162447; border:1px solid <?php echo $isAccepted ? '#4caf50' : '#1f4068'; ?>;
            padding: 15px; border-radius:8px;">
            <?php if ($isAccepted): ?>
            <div style="color: #4caf50; font-weight: bold; margin-bottom: 8px;">✓ Accepted Answer</div>
            <?php endif; ?>
            <div style="color: #aaa; font-size:0.85em;">
                <?php echo htmlspecialchars($c['user_name']); ?> • <?php echo htmlspecialchars($c['created_at']); ?>
            </div>

            <p style="color: #ddd; margin-top: 10px; white-space: pre-wrap;"><?php echo htmlspecialchars($c['body']);?></p>
            <button onclick="vote('comment', <?php echo intval($c['comment_id']); ?>, 1)"
                    style="background: #0f3460; border:1px solid #efc07b; color: #efc07b; padding: 6px 10px; border-radius: 6px; cursor: pointer;">▲</button>
            <span id="score-comment-<?php echo intval($c['comment_id']); ?>" style="color:#fff;">
                <?php echo intval($c['score']); ?>
            </span>
            <button onclick="vote('comment', <?php echo intval($c['comment_id']); ?>, -1)"
                    style="background: #0f3460; border:1px solid #efc07b; color:#efc07b; padding: 6px 10px; border-radius: 6px; cursor: pointer;">▼</button>

            <!-- answers approved by the post author -->
            <?php if ($isAuth && intval($post['user_id']) === $userId): ?>
            <form method="POST" action="../controllers/community_accept_answer.php" style="margin-left: auto;">
                <input type="hidden" name="post_id" value="<?php echo intval($postId); ?>">
                <input type="hidden" name="comment_id" value="<?php echo intval($c['comment_id']); ?>">
                <button type="submit"
                        style="background: #1a1a2e; border:1px solid #4caf50; color: #4caf50; padding: 6px 10px; border-radius: 6px; cursor: pointer;">
                    Mark as Accepted
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
  <!--Answer form-->
<?php if ($isAuth): ?>
<div style="margin-top: 20px; background: #162447; border:1px solid #1f4068; padding:15px; border-radius: 8px;">
<h3 style="color:#efc07b; margin-top: 0;">Add Answer</h3>
    <form Method="POST" action="../controllers/community_add_comment.php">
        <input type="hidden" name="post_id" value="<?php echo intval($postId); ?>">
        <textarea name="body" rows="5" placeholder="Write your answer..."
                  style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #1f4068; background: #0f3460; color: #fff; box-sizing: border-box;"></textarea>

        <button type="submit" class="guest-btn" style="background: #e94560; color:#fff; border-color: #efc07b; width: auto; margin-top: 10px;">
            Submit Answer
        </button>
    </form>
</div>
<?php else: ?>
<div style="margin-top: 20px; color: #aaa;">
    <a href="login.html" style="color: #efc07b;">Login</a> to Answer.
</div>
<?php endif; ?>
</div>

<script>
    async function vote(targetType, targetId, voteVal) {
        try {
            const res = await fetch('../controllers/community_vote.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ targetType: targetType, targetId: targetId, vote: voteVal })
            });

            const data = await res.json();

            if(!data.ok) {
                if (data.error === 'login_required') {
                    alert('Login required to vote.');
                    return;
                }
                alert('Vote failed.');
                return;
            }

            const el = document.getElementById(`score-${targetType}-${targetId}`);
            if (el) el.innerText = data.score;
        }
        catch(e) {
            console.error(e);
            alert('Network error.');
        }
    }
</script>
</body>
</html>