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
 WHERE c.post_id=p.post_id AS answer_count)

FROM community_posts p
JOIN users u ON u.user_id = p.user_id
";

$params = [];
$types = "";

if ($q !== '') {
    if (mb_strlen($q) >= 3) {
        $sql .= " WHERE MATCH(p.title) AGAINST (? IN BOOLEAN MODE) ";
        $booleanQuery = $q;
        $words = preg_split('/\s+/', $q);
        //$words = array_filter($words, fn($w) => $w !== '');
        //$words = array_map(fn($w) => $w . '*' , $words);
        $booleanQuery = implode(' ', $words);

        $params[] = $booleanQuery;
        $types .= "s";
    } else {
        //to be continued
    }
}