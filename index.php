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

<div class="main-wrapper">
    <h1>Dashboard</h1>
    <p>Current Access Level: <strong style="color: #efc07b;"><?php echo ucfirst($role); ?></strong></p>

    <!--upload research feature -->
    <div style="margin: 20px 0; text-align: right;">
        <?php if($role !=='guest'): ?>
        <!-- button is accessible by registered users only -->
        <a href="views/upload_research.php" class="guest-btn" style="background-color: #e94560; color: white; border-color: #efc07b;">
            + Upload New Research
        </a>
        
        <?php else: ?>
    <!-- This one is for the guest users (CTA) -->
    <span style="color: #ccc; margin-right: 10px;">Want to contribute?</span>
    <a href="views/register.html" class="guest-btn" style="background-color: #e94560; color: white; border-color: #efc07b;">
        Join Our Community!
    </a>
    <?php endif; ?>
    </div>

    <div id="live-threat-dashboard" class = "api_container">
        <div style="display:flex; justify-content:space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin:0; color:#fff;">🔴 Live Threat Intelligence</h3>
            <span style="font-size: 0.8em; color: gray;">Source: CIRCL CVE Feed | Updates Hourly</span>
        </div>
        <h4 style="color: #efc07b; margin-top: 0; font-weight: normal; font-size: 1rem;">Top 5 Trending Vulnerabilities (Live)</h4>
        <!--Note:
        Vulnerability is the weakness just like bug or loophole
        Threat is the possible cause of the harm like malware or script
        -->
        <div id="threat-container" style ="display:flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
            <p style="color: #efc07b;">Connecting to API...</p>
        </div>
    </div>
    <?php include 'views/components/attack_knowledge_base.php'; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('controllers/feed.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('threat-container');
                container.innerHTML = '';

                if (data.length === 0) {
                    container.innerHTML = '<p style="color: #ff4d4d;">Unable to load vulnerability feed.</p>';
                return;
                }

                data.forEach(cve => {const card = document.createElement('div');
                    card.setAttribute("style", "background : #162447; border: 1px solid #1f4068; padding: 15px; border-radius: 8px; width: 18%; min-width: 200px; text-align: left; box-shadow: 0 4px 6px rgba(0,0,0,0.3);");
                    //card.style.cssText = "background : #162447; border: 1px solid #1f4068; padding: 15px; border-radius: 8px; width: 18%; min-width: 200px; text-align: left; box-shadow: 0 4px 6px rgba(0,0,0,0.3);";
                //false positive warning
                let summary = cve['summary'] ? cve['summary'].substring(0,85) + "..." : "No details available.";
                card.innerHTML =`<strong style="color: #e94560; font-size: 0.95em; display: block; margin-bottom: 5px;">${cve['id']}</strong>
                    <span style="color: #efc07b; font-size: 0.75em; background: #1a1a2e; padding: 2px 6px; border-radius: 4px;">Modified: ${cve['Modified'].substring(0,10)}</span>
                    <p style="color: #ddd; font-size: 0.8em; line-height: 1.4; margin-top: 10px;">${summary}</p>
                        `;
                    container.appendChild(card);
                });
            })

    .catch(error => {
    console.error('Error:', error);
    document.getElementById('threat-container').innerHTML = '<p style="color: #ff4d4d;">System Error: Feed unreachable.</p>';
    });
});
</script>
</body>
</html>

