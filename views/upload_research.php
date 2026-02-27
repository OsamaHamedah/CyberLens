<?php
session_start();

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !==true) {
    header('Location: login.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Research | Cyber Lens</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .upload-form textarea{
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background: #1a1a2e;
            border: 1px solid #16213e;
            color : #fff;
            border-radius: 5px;
        }
        .upload-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background: #1a1a2e;
            border: 1px solid #16213e;
            color: #fff;
            border-radius: 5px;
        }
    </style>
    </head>
<body class="centered-body">
<div class="container">
    <h2 style="color: #e94560;">Upload Cyber Research</h2>

    <form action="../controllers/upload_research.php" method="post" class ="upload-form">
        <label style="color: #ccc;"> Research Category:</label>
            <select name="category" required>
                <option value="" selected disabled>Select Category</option>
                <option value="Malware">🦠 Malware</option>
                <option value="Phishing">🎣 Phishing & Social Eng</option>
                <option value="Dos">🧟 Denial-of-Service</option>
                <option value="SQLi">💉 SQL Injection</option>
                <option value="XSS">❌ XSS</option>
                <option value="other">❓ Other</option>
            </select>

        <label class="form-label">Severity Level:</label>
        <select name="severity" required>
            <option value="" selected disabled>Select Severity Level</option>
            <option value="Critical" style="color: #e74c3c;">🔴 Critical</option>
            <option value="High" style="color: #e67e22;">🟠 High</option>
            <option value="Medium" style="color: #f1c40f;">🟡 Medium</option>
            <option value="Low" style="color: #2ecc71;">🟢 Low</option>
        </select>

        <label class="form-label">Title:</label>
        <input type="text" name="title" placeholder="Research Title" required>

        <label class="form-label">Description:</label>
        <textarea name="description" placeholder="Research Description" rows="3" required></textarea>

        <label class="form-label">Research Full Details:</label>
        <textarea name="content" placeholder="Research Content & Details" rows="10" required></textarea>

        <label style="color: #ccc; display: flex; align-items: center; gap: 10px; margin: 10px 0">
            <input type="checkbox" name="is_ieee">
            This is an IEEE Academic Citation
        </label>

        <button type="submit" name="upload_btn" style="background-color: #e94560; color: white; padding: 12px;
            border: none; width: 100%; border-radius:: 5px; font-weight:bold; cursor: pointer; transition: 0.3s;">
            Upload Research</button>
        <a href="../index.php" class="guest-btn" style="text-align: center; display: block; margin-top: 15px;
            color: #aaa; text-decoration: none;">Cancel</a>
    </form>
</div>
</body>
</html>
