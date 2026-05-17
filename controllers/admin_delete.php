<?php
session_start();
include '../config/db_connection.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<script>alert('Unauthorized access.'); window.history.back();</script>";
    exit();
}

if(isset($_POST['delete_btn'])) {
    $type = $_POST['target_type'];
    $id = intval($_POST['target_id']);

    $table= "";
    $id_column = "";

    if($type === 'community_post') {$table = 'community_posts'; $id_column = 'post_id';}
    elseif ($type === 'community_comment') {$table = 'community_comments'; $id_column = 'comment_id';}
    elseif ($type ==='research') {$table = 'research'; $id_column = 'research_id';}
    elseif ($type === 'research_comment') {$table = 'comments'; $id_column = 'comment_id';}

    if($table!== "") {
        /** * @var mysqli $conn */
        $stmt = $conn->prepare("DELETE FROM $table WHERE $id_column = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
           echo "<script>alert('Item successfully deleted by Admin.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error deleting item.'); window.history.back();</script>";
        }
        $stmt->close();
    }
}