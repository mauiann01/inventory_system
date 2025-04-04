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

    $totalAmount = $_GET['totalAmount'];
    $amountTendered = $_GET['amountTendered'];
    $change = $_GET['change'];
    $form_id = "";

    $sql009 = "INSERT INTO sales_list (ref_no, customer_id, `total_amount`, amount_tendered, amount_change) VALUES ('0', '0', '$totalAmount', '$amountTendered', '$change')";
    if (mysqli_query($connection, $sql009)) {
        // Data inserted successfully
    } else {
        // Error occurred during insertion
        echo "Error: " . $sql009 . "<br>" . mysqli_error($connection);
    }

    // SQL query to get the maximum ID from sales_list
    $sql10 = "SELECT MAX(id) AS max_id FROM sales_list";
    // Execute the query
    $result10 = $connection->query($sql10);
    // Check if the query was successful
    if ($result10) {
        $row10 = $result10->fetch_assoc();
        // Retrieve the maximum ID
        $form_id = $row10['max_id'];
    } else {
        // Handle the case where the query fails
        echo "Error: " . $connection->error;
    }

    $sql4 = "SELECT * FROM user_temp_tbl";
    $result4 = $connection->query($sql4);

        if ($result4->num_rows > 0) {
            while ($row4 = $result4->fetch_assoc()) {
                $prd_id = $row4["product_id"];
                $prd_qty = $row4["qty"];
                $prd_type = $row4["type"];
                $prd_stock_from = $row4["stock_from"];
                $prd_other_details = $row4["other_details"];
                $prd_remarks = $row4["remarks"];

                $sql = "INSERT INTO inventory (product_id, qty, `type`, stock_from, form_id, other_details, remarks) VALUES ('$prd_id', '$prd_qty', '$prd_type', '$prd_stock_from', '$form_id', '$prd_other_details', '$prd_remarks')";
                if (mysqli_query($connection, $sql)) {
                    // Data inserted successfully
                } else {
                    // Error occurred during insertion
                    echo "Error: " . $sql . "<br>" . mysqli_error($connection);
                }
            }
        }

    // Query to delete all rows from user_temp_tbl
    $sql11 = "DELETE FROM user_temp_tbl";
    $connection->query($sql11);

    echo'    <script>
                alert("Sales submitted successfully!");
                window.location.href = "qr_pos.php";
            </script>';


?>