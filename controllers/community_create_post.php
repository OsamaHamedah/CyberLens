<?php
include '../config/db_connection.php';

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../views/login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:../views/community.php");
    exit();
}
/*
$userId = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$body = trim ($_POST['body'] ?? '');
*/
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$body = isset($_POST['body']) ? trim($_POST['body']) : '';

if ($userId <= 0 || $title === '' || $body === '') {
    header("Location:../views/community.php?err=empty");
    exit();
}

/** * @var mysqli $conn */
$stmt = $conn->prepare("INSERT INTO community_posts (user_id, title, body) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $userId, $title, $body);
$stmt->execute();

header("Location:../views/community.php?ok=posted");
exit();