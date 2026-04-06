<?php
include '../config/db_connection.php';

$isAuth = isset($_SESSION['auth']) && $_SESSION['auth'] === true;

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
if ($sort !== 'newest' && $sort !== 'top') {
    $sort = 'newest';
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$orderBy = "p.created_at DESC";
if ($sort === 'top') {
    $orderBy = "score DESC, p.created_at DESC";
}

$sql = "SELECT
p.post_id,
p.title,
p.body,
p.created_at,
p.user_id,
p.accepted_comment_id,
COALESCE(u.full_name, u.email) AS author_name,

(SELECT COALESCE(SUM(v.vote),0) FROM community_votes v
WHERE v.target_type='post' AND v.target_id=p.post_id) AS score,
    
(SELECT COUNT(*)
 FROM community_comments c
 WHERE c.post_id=p.post_id) AS answer_count

FROM community_posts p
JOIN users u ON u.user_id = p.user_id
";

$params = array();
$types = "";

if ($q !== '') {
    if (mb_strlen($q) >= 3) {
        $sql .= " WHERE MATCH(p.title, p.body) AGAINST (? IN BOOLEAN MODE) ";
        //$booleanQuery = $q;
        $words = preg_split('/\s+/', $q);
        $words = array_filter($words, function($w) {return $w !== '';});
        $words = array_map(function($w) {return $w . '*'; } , $words);
        $booleanQuery = implode(' ', $words);

        $params[] = $booleanQuery;
        $types .= "s";
    } else {
        $sql .= " WHERE (p.title LIKE CONCAT('%', ?, '%') OR p.body LIKE CONCAT('%', ?, '%')) ";
        $params[] = $q;
        $params[] = $q;
        $types .= "ss";
    }
}

$sql .= " ORDER BY $orderBy ";

/** @var mysqli $conn */
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("DB error: " . $conn->error . "<br><pre>" . htmlspecialchars($sql) . "</pre>");
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$posts = $stmt->get_result();
?>
<!-- Note: I wrote comments above each internal feature code for better code revision & reference -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Community</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="dashboard-header">
    <h2 style="margin:0; color:#e94560;">Cyber Lens Community</h2>
    <div class="user-controls">
        <a href="../index.php" class="login-link">← Dashboard</a>
    </div>
</div>

<div class="main-wrapper">
    <!-- Tabs + search + add post-->
    <div style="display:flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap:12px; margin-bottom: 15px;">
        <!-- Tabs -->
        <div style="display: flex; gap: 10px;">
            <a href="community.php?sort=newest<?php echo $q !== '' ? '&q=' . urlencode($q) : ''; ?>"
               style="padding: 8px 12px; border-radius: 8px; text-decoration: none; border: 1px solid #1f4068;
                   background:<?php echo $sort==='newest' ? '#0f3460' : '#162447'; ?>; color: #efc07b;">

                Newest
            </a>

            <a href="community.php?sort=top<?php echo $q !== '' ? '&q=' . urlencode($q) : ''; ?>"
               style="padding: 8px 12px; border-radius: 8px; text-decoration: none; border: 1px solid #1f4068;
               background:<?php echo $sort==='top' ? '#0f3460' : '#162447'; ?>; color: #efc07b;">

                Top
            </a>
        </div>

        <!-- search-->
        <form method="GET" action="community.php" style="display: flex; gap: 10px; align-items: stretch;">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search posts..."
            style="width: 260px; max-width: 70vw; height: 42px; padding:0 10px; border-radius: 8px; border: 1px solid #1f4068; background: #0f3460; color: #fff; box-sizing: border-box;">
            <button type="submit" style="height:42px; padding:0 14px; border-radius: 8px; border: 1px solid #1f4068; background: #0f3460; color: #fff; box-sizing: border-box;">
                Search
            </button>

            <?php if ($q !== ''): ?>
            <a href="community.php?sort=<?php echo htmlspecialchars($sort); ?>"
               style=" height:42px; padding:0 14px; border-radius: 8px; border: 1px solid #1f4068; background: #162447; color: #aaa; text-decoration: none; box-sizing: border-box;">
                Clear
            </a>
            <?php endif; ?>
        </form>

        <!-- post anchor-->
        <div>
            <?php if($isAuth): ?>
            <a href="#add-post" style="padding: 8px 12px; border-radius: 8px; text-decoration: none; border: 1px solid #efc07b; background: #e94560; color: #fff;">
                + Add Post
            </a>
            <?php else: ?>
            <a href="login.html" style="color: #efc07b; text-decoration: none;">
                Login to post
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!--show active search -->
    <?php if($q !== ''): ?>
    <div style="color: #aaa; margin-bottom: 12px;">
        Showing results for: <span style="color: #efc07b;"><?php echo htmlspecialchars($q); ?></span>
    </div>
    <?php endif; ?>

    <!-- status-->
    <?php if(isset($_GET['err']) && $_GET['err'] == 'empty'): ?>
    <div style="background: #3b1d1d; border: 1px solid #ff4d4d; color: #ffb3b3; padding:10px; border-radius: 8px; margin-bottom: 15px;">
        Please fill in all fields.
    </div>

    <?php elseif (isset($_GET['err']) && $_GET['err'] === 'db'): ?>
    <div style="background: #3b1d1d; border: 1px solid #ff4d4d; color: #ffb3b3; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
        Database error. Please try again.
    </div>

    <?php elseif (isset($_GET['ok']) && $_GET['ok'] === 'posted'): ?>
    <div style="background: #163b1d; border: 1px solid #4caf50; color: #b8ffbf; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
        Post Created Successfully!
    </div>
    <?php endif; ?>

    <!-- posts list -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php if($posts-> num_rows === 0): ?>
        <div style="background: #162447; border: 1px solid #1f4068; padding: 15px; border-radius: 8px; color: #aaa;">
            No posts found.
        </div>
        <?php endif; ?>

        <?php while($p = $posts->fetch_assoc()): ?>
        <?php
            $pid = intval($p['post_id']);
            $score = intval($p['score']);
            $answer = intval($p['answer_count']);
            $isSolved = !empty($p['accepted_comment_id']);
            ?>
        <a href="post.php?id=<?php echo $pid; ?>"
            style="display: block; text-decoration: none; background: #162447; border: 1px solid #1f4068; border-radius: 8px; padding: 15px;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap:10px;">
                <h3 style="margin: 0; color: #efc07b;"><?php echo htmlspecialchars($p['title']); ?></h3>
            <?php if($isSolved): ?>
            <span style="color: #4caf50; font-weight: bold;">Solved</span>
            <?php endif; ?>
            </div>

            <div style="color: #aaa; font-size: 0.85em; margin-top: 6px;">
                by <?php echo htmlspecialchars($p['author_name']); ?> • <?php echo htmlspecialchars($p['created_at']); ?>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 10px; color: #ddd;">
                <span style=" border:1px solid #1f4068; padding: 4px 10px; border-radius: 999px;">
                    Score: <?php echo $score; ?>
                </span>
                <span style=" border:1px solid #1f4068; padding: 4px 10px; border-radius: 999px;">
                    Answers: <?php echo $answer; ?>
                </span>
            </div>
        </a>
        <?php endwhile; ?>
    </div>

    <!-- add post (auth) -->
    <?php if($isAuth): ?>
    <div id="add-post" style="margin-top: 20px; background: #162447; border: 1px solid #1f4068; border-radius: 8px; padding: 15px;">
        <h3 style="color: #efc07b; margin-top:0;">Add Post</h3>


        <form method="POST" action="../controllers/community_create_post.php">
            <input type="text" name="title" placeholder="Title"
            style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #1f4068; background: #0f3460; color: #fff; margin-bottom: 10px;">
            <textarea name="body" rows="6" placeholder="Write your post..." style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #1f4068; background: #0f3460; color: #fff; box-sizing: border-box;"></textarea>
        <button type="submit" class="guest-btn"
                style="background: #e94560; color: #fff; border-color: #efc07b; width: auto; margin-top: 10px; box-sizing: border-box;">
            Post
        </button>
        </form>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
