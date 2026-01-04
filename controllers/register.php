<?php
//this is the Application Layer for the registration
//also the data access layer (that will communicate with the database)
require '../config/db_connection.php';
/** * @var mysqli $conn */

if (isset($_POST['register_btn'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); //thanks to Privacy of Healthcare Information elective course :)
    $check_email = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
       // echo "<script type='text/javascript'>alert('Email already exists');</script>";
        echo "<script>alert('Email already exists'); 
    window.location.href='../views/register.html';</script>";
    }
    else {
        $sql = "INSERT INTO users (full_name, email, password, role)
VALUES ('$full_name', '$email', '$hashed_password', 'user')";
        if ($conn->query($sql) === TRUE) {
            //echo "<script>alert('User registered successfully');";
            echo "<script> alert('Account Created Successfully');
            window.location.href='../index.php';</script>";
        }
            else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
}
