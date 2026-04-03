<?php
include '../config/db_connection.php';

$isAuth = isset($_SESSION['auth']) && $_SESSION['auth'] === true;
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($postId <= 0) {
    header("Location: community.php");
    exit();
}

$sqlPost = "
SELECT p.post_id, p.title, p.body, p.created_at, p.iser_id, p.accepted_comment_id, u.user_name, (SELECT COALESCE(SUM(v.vote),0) 
FROM community_votes v WHERE v.target_type='post' AND v.targert_id=p.post_id) AS score FROM community_posts p
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