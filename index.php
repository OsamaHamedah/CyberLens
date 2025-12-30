<?php
//Personal note: This is the presentation layer".
include 'config/db_connection.php';

echo "<h1>Cyber Lens Project</h1>";

if(isset($conn) && $conn->ping()) {
    echo "<h1>Connection is established successfully. </h1>";
    //echo "<p>Connected to database : " . $dbname . "</p>";
    //It kept showing static / IDE warning (made it a comment in order to avoid future errors)
}

else {
    echo "<h1>Connection is not established. </h1>";
}
