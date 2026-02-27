<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

include'../config/db_connection.php';

/** * @var mysqli $conn */
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    echo "<script>alert('You must be logged in to upload research.'); 
    window.location.href='../views/login.html';</script>";
    exit();
}

if (isset($_POST['upload_btn'])) {
    $user_id = $_SESSION['user_id'];
    $category = $_POST['category'];
    $severity = $_POST['severity'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $content = $_POST['content'];
    $is_ieee = isset($_POST['is_ieee']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO research (user_id , category, severity, title, description, content, is_ieee) VALUES (?,?,?,?,?,?,?)");
    if($stmt) {
        $stmt->bind_param("isssssi", $user_id, $category, $severity, $title, $description, $content, $is_ieee);

        if ($stmt->execute()) {
        echo "<script>alert('Research successfully uploaded!');
    window.location.href='../index.php';</script>";
    }
    else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    }


    else {
        echo "Prepare failed: " . $conn->error;
        }
    }
    else {
        header("Location: ../index.php");
    exit();
    }