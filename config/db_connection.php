<?php
session_start();

$host = "localhost";
$username = "cyberlens_user";
$password = "password123";
$dbname = "cyber_lens_db";

$conn = new mysqli($host, $username, $password, $dbname);

if($conn-> connect_error) {
    die("Connection failed : " .$conn->connect_error);
}

