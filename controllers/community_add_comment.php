<?php
include '../config/db_connection.php';

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../views/login.html');
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$postId = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$body = isset($_POST['body']) ? trim($_POST['body']) : '';

if ($userId <= 0 || $postId <= 0 || $body === '') {
    header("Location: ../views/post.php?id=" . $postId . "&err=empty");
    exit();
}

/** *@var mysqli $conn */
$stmt = $conn->prepare("INSERT INTO community_comments (post_id, user_id, body) VALUES (?, ?, ?)");
if (!$stmt) {
    header("Location: ../views/post.php?id=" . $postId . "&err=db");
    exit();
}
$stmt->bind_param("iis", $postId, $userId, $body);
$stmt->execute();

header("Location: ../views/post.php?id=" . $postId);
exit();