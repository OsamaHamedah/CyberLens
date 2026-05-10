<?php
session_start();
include '../config/db_connection.php';

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    echo "<script>alert('You must be logged in to report content.'); window.history.back();</script>";
    exit();
}

if (isset($_POST['report_btn'])) {
    $report_id = $_SESSION['user_id'];
    $target_type = $_POST['target_type']; // to match it with community_post , community_comment , etc....
    $target_id = $_POST['target_id'];

    $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : 'Inappropriate content';
    /** * @var mysqli $conn */
    $stmt = $conn->prepare("INSERT INTO reports (reporter_user_id, target_type, target_id, reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $reporter_id, $target_type, $target_id, $reason);

    if ($stmt->execute()) {
        echo "<script>alert('Report submitted successfully. An admin will review it.'); window.history.back();</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.'); window.history.back();</script>";
    }
    $stmt->close();
}