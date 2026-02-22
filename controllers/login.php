<?php
//Login Controller

session_start();
include '../config/db_connection.php';
/** * @var mysqli $conn */

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $check_email = $conn->prepare("SELECT user_id, full_name, password, role FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password_from_db = $row['password'];

        if (password_verify($password, $hashed_password_from_db)) {
            $_SESSION['auth'] = TRUE;
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['full_name'];
            $_SESSION['user_role'] = $row['role'];

            echo "<script>alert('Logged in successfully!'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Invalid Password. Please try again.'); window.location.href = '../views/login.html';</script>";
        }
    }

        else {
        echo "<script>alert('No Account Found with this Email.'); window.location.href = '../views/register.html';</script>";
        }
}
