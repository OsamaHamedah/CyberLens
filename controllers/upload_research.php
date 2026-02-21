<?php
session_start();
include'../config/db_connection.php';
/** * @var mysqli $conn */
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    echo "<script>alert('You must be logged in to upload research.'); 
    window.location.href='../views/login.html';</script>";
}

if (isset($_GET['upload_btn'])) {
    $user_id = $_SESSION['user_id'];
    $category = $_POST['category'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $content = $_POST['content'];
    $is_ieee = isset($_POST['is_ieee']) ? 1:0;

    $stmt = $conn->prepare("INSERT INTO research (user_id , category, title, description, content, is_ieee) VALUES (?,?,?,?,?,?)");
    $stmt ->bind_param("issssi", $user_id, $category, $title, $description, $content, $is_ieee);

    if($stmt->execute()){
        echo "<script>alert('Research successfully uploaded.');
    window.location.href='../index.php';</script>";
    }
    else{
        echo "<script>alert('Research failed to upload.');
    window.location.href='../index.php';</script>";
    }
    $stmt->close();

}
