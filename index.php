<?php
//Note: This is the presentation layer,

include 'config/db_connection.php';

if(isset($_GET['guest']) && $_GET['guest']== 'true') {
    $_SESSION['user_role'] = 'guest';
    $_SESSION['user_name'] = 'Guest';
    $_SESSION['auth'] = false;

    header("Location: index.php");
    exit();
}

/* if(isset($_SESSION['auth']) && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'guest'))
{
header("Location: views/login.php");
exit();
} */

$name= isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'guest';
$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';


//these commented codes are from previous implementation stages where I was testing the DB connection
//echo "<h1>Cyber Lens Project</h1>";
//echo "<h1>Connection is established successfully. </h1>";
//else {
//    echo "<h1>Connection is not established. </h1>";
//}
//echo "<p>Connected to database : " . $dbname . "</p>";
//It kept showing static / IDE warning (made it a comment in order to avoid future errors)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Cyber Lens</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: block !important;
            height : auto !important;
            padding : 0;
            margin: 0;
            background-color: #1a1a2e;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: #1b2a4e;
            border-bottom: 2px solid #efc07b;
            width: 100%;
            box-sizing: border-box;
        }
        .welcome-text {
            font-size: 1.1em;
            color: white;
            margin-right: 15px;
        }
        .user-controls{
            display: flex;
            align-items: center;
        }
        .logout-btn {
            color: #ff4d4d;
            text-decoration: none;
            border : 1px solid #ff4d4d;
            padding : 5px 15px;
            border-radius: 5px;
            transition: 0.3s;

        }
        .logout-btn:hover {
            background: #ff4d4d;
            color: white;
        }
        .login-link {
            color: #efc07b;
            text-decoration: none;
            border: 1px solid #efc07b;
            padding : 5px 15px;
            border-radius: 5px;
        }
        .container {
            width: 600px;
            margin : 80px auto ;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="dashboard-header">
<h2 style="margin: 0; color: #e94560;">Cyber Lens</h2>

<div class="user-controls">
    <span class="welcome-text">Welcome, <strong><?php echo htmlspecialchars($name); ?></strong></span>
    &nbsp; | &nbsp;
    <?php if($role ==='guest'): ?>
        <a href="views/login.html" class="login-link">Login / Sign Up</a>
    <?php else: ?>
        <a href="controllers/logout.php" class="logout-btn">Logout</a>
    <?php endif; ?>
</div>
</div>

<div class="container" style="margin: 15vh auto; width: 80%; max-width: 1200px;">
    <h1>Dashboard</h1>
    <p>Current Access Level: <strong style="color: #efc07b;"><?php echo ucfirst($role); ?></strong></p>

    <div id="live-threat-dashboard" style="margin-top: 40px; border: 1px dashed #efc07b; padding: 30px; border-radius: 10px;">
        <h3>🔴 Live Threat Intelligence </h3>
        <p>Loading API data...</p>
    </div>
</div>
</body>
</html>

