<?php
session_start();
include '../config/db_connection.php';

if(!isset($_SESSION['auth']) || $_SESSION['auth'] !== true){
    echo "<script>alert('Please login to comment.'); window.history.back();</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $research_id = intval($_POST['research_id']);
    $user_id = $_SESSION['user_id'];
    $comment_text = trim($_POST['comment_text']);

    $parent_id = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : NULL;
    // the purpose of this is to check if this is a comment or a reply to existing comment

    /**  @var mysqli $conn to avoid possible error I faced since I'm using IntelliJ Idea Ultimate */
    if (!empty($comment_text)) {
        $stmt = $conn->prepare("INSERT INTO comments (research_id, user_id, parent_id, comment_text) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $research_id, $user_id, $parent_id, $comment_text);

        if ($stmt->execute()) {
            header("location: ../views/read_research.php?id=" . $research_id . "#comments");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    else {
            echo "<script>alert('Comment cannot be empty!'); window.history.back();</script>";
        }
    }
