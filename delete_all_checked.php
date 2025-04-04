<?php
    // Database configuration
    $servername = "localhost"; // Change this to your database server hostname
    $username = "root"; // Change this to your database username
    $password = ""; // Change this to your database password
    $database = "sales_inventory_db"; // Change this to your database name

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    } else {

    }

    // Query to delete all rows from user_temp_tbl
    $sql11 = "DELETE FROM user_temp_tbl";
    $connection->query($sql11);

    echo'    <script>
                alert("Products was removed successfully!");
                window.location.href = "qr_pos.php";
            </script>';


?>