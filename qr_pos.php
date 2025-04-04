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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve the scanned QR code data from the POST request
        $scanned_data = $_POST['user_code'];
        $sql6 = "SELECT * FROM product_list WHERE sku = '$scanned_data'";
        $result6 = $connection->query($sql6);

        // IF PRODUCT IS VALID ACCORDING TO THE PRODUCT LIST
        if ($result6->num_rows > 0) {
            $sql7 = "SELECT * FROM product_list WHERE sku = '$scanned_data'";
            $result7 = $connection->query($sql7);
        
            if ($result7->num_rows > 0) {
                while ($row7 = $result7->fetch_assoc()) {
                    $prd_id = $row7["id"];
                    $prd_price = $row7["price"];
                    $prd_name = $row7["name"];
    
                }
            }

            $sql4 = "SELECT * FROM user_temp_tbl WHERE product_id = '$prd_id'";
            $result4 = $connection->query($sql4);
        
            if ($result4->num_rows > 0) {
                // Data already exists, so update the quantity
                while ($row4 = $result4->fetch_assoc()) {
                    $current_qty = $row4["qty"];
                }
                $new_qty = $current_qty + 1; // Increment the quantity
                $sql5 = "UPDATE user_temp_tbl SET qty = '$new_qty' WHERE product_id = '$prd_id'";
                if (mysqli_query($connection, $sql5)) {
                    // Data updated successfully
                } else {
                    // Error occurred during update
                    echo "Error: " . $sql5 . "<br>" . mysqli_error($connection);
                }
            } else {
                $other_details = '{"price":"'.$prd_price.'","qty":"1"}';
                // Data doesn't exist, so insert a new row
                $sql = "INSERT INTO user_temp_tbl (product_id, product_name, price, qty, amount, other_details) VALUES ('$prd_id', '$prd_name', '$prd_price', '1', '$prd_price', '$other_details')";
                if (mysqli_query($connection, $sql)) {
                    // Data inserted successfully
                } else {
                    // Error occurred during insertion
                    echo "Error: " . $sql . "<br>" . mysqli_error($connection);
                }
            }
        }
        else{
            echo'    <script>
                        alert("Invalid Item!");
                    </script>';
        }

        
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>

    <!-- Style CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha384-GLhlTQ8iN17PdL7vYR8+ftbcAqLbeWOvYA1N1PiKXIbbVcGqjFZerft7LXdEf2I+ " crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <style>
    .image-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: ;
    }

    .workout-box {
        text-align: center;
        margin: 10px;
        flex: 0 0 calc(20% - 20px); /* 20% width with 20px margin on both sides */
    }

    .responsive-image {
        width: 100%;
        max-width: 100%;
        height: auto;
        border-radius: 10px 10px 0 0;
    }

    .workout-box span {
        display: block;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 0 0 10px 10px;
        color: black;
    }

    #toggleImagesBtn {
            padding: 5%; /* Use relative padding */
        }

        #toggleImagesBtn img {
            max-width: 100%; /* Make sure the image doesn't exceed the button's width */
            height: auto; /* Maintain the aspect ratio */
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            #toggleImagesBtn {
                padding: 2%; /* Adjust padding for smaller screens */
            }
        }

    /* Media query for responsiveness */
    @media (max-width: 768px) {
        .workout-box {
            flex: 0 0 calc(100% - 20px); /* 100% width with 20px margin on both sides */
        }
    }
</style>


</head>
<body>
    <div class="main" style="background-color: #f2f2f2;">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand ml-3" href="#">Inventory Management System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="qr_pos.php">QR-POS |</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=home">Dashboard |</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="journal-container" style="width: 100%; background-color: #f2f2f2;" >
            <div class="search-journal">
                <div class="form-group">
                </div>
                        <div class="image-container5 workout-images5">
                            <div class="workout-box5">
                            <?php
          include 'phpqrcode/qrlib.php';
        ?>
  <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<div class = "panel-body" style="background-color: #333333; width: 100%; border-radius: 10px;">
    <div class="right-column" style="width: ;">
        <b><h2 style="padding: 10px; color: white;"><img src="qr_icon.png" style="width: 60px; height: auto;" alt=""> QR Code Scanner</h2></b>
        <video id="preview" style="width: 300px; height: auto; padding: 5px;"></video><br>
        <div style="padding: 10px; color: #d9d9d9;">

        <form id="equipmentForm" method="post" action="">
            <input name="user_code" type="text" id="qrResult" placeholder="Scanned QR Code will appear here" style="padding: 10px; float: right; margin-top: -10px; color: black;" hidden>
        </form>
        <audio id="notificationSound" src="notification.mp3" preload="auto"></audio> <!-- Replace 'notification.mp3' with your sound file -->


<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
            scanner.addListener('scan', function (content) {
                document.getElementById('qrResult').value = content;
                playNotificationSound(); // Play sound notification

                // Delay form submission by 2 seconds
                setTimeout(function() {
                    document.getElementById('equipmentForm').submit();
                }, 2000);
            });

            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('No cameras found.');
                }
            }).catch(function (e) {
                console.error(e);
            });

            function playNotificationSound() {
                let sound = document.getElementById('notificationSound');
                sound.play();
            }
        });

    </script>

        <br>
        <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th style="width: 40px;">Product ID#</th>
                <th style="width: 40px;">Product Name</th>
                <th style="width: 40px;">Price</th>
                <th style="width: 40px;">Qty</th>
                <th style="width: 40px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_amount = 0; // Initialize total amount variable

            $sql2 = "SELECT * FROM user_temp_tbl";
            $result2 = $connection->query($sql2);

            if ($result2->num_rows > 0) {
                while ($row2 = $result2->fetch_assoc()) {
                    $pr_id = $row2["product_id"];
                    $pr_name = $row2["product_name"];
                    $pr_price = $row2["price"];
                    $pr_qty = $row2["qty"];
                    $pr_amount = $pr_price * $pr_qty;

                    // Accumulate total amount
                    $total_amount += $pr_amount;
            ?>
                    <tr style="color: ;">
                        <td><?php echo $pr_id; ?></td>
                        <td><?php echo $pr_name; ?></td>
                        <td><?php echo $pr_price; ?></td>
                        <td>
                            <!-- Input field to make the quantity editable -->
                            <input type="text" name="qty" value="<?php echo $pr_qty; ?>" style="width:60px; padding: 5px;" disabled>
                        </td>
                        <td><?php echo $pr_amount; ?></td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>

    <!-- Display total amount -->
    <h4>Total Amount: <?php echo $total_amount; ?></h4><br>

    <!-- Input field for amount tendered -->
    <input type="number" min="0" id="amountTendered" placeholder="Amount Tendered" value="0" oninput="calculateChange()" style="width: 200px; padding: 5px;">

    <!-- Input field for change -->
    <input type="text" id="change" placeholder="Change" value="0" disabled style="width: 200px; padding: 5px;">

    <script>
        // Function to calculate change
        function calculateChange() {
            var totalAmount = <?php echo $total_amount; ?>;
            var amountTendered = parseFloat(document.getElementById('amountTendered').value);
            var change = amountTendered - totalAmount;

            // Update change input box
            document.getElementById('change').value = change.toFixed(2);
        }
    </script>

            <!-- Button to trigger the modal -->
            <a href="#" id="submitLink" onclick="submitWithData()" style="padding: 10px 100px 10px 100px; background-color: #0073e6; border-color: white; color: white;">Submit</a>
                <!-- <button style="padding: 10px 100px 10px 100px; background-color: #0073e6; border-color: white; color: white;">Submit</button> -->
                <script>
                    function submitWithData() {
                        // Get total amount, amount tendered, and change values
                        var totalAmount = <?php echo $total_amount; ?>;
                        var amountTendered = parseFloat(document.getElementById('amountTendered').value);
                        var change = parseFloat(document.getElementById('change').value);

                        // Check if amount tendered is greater than 0
                        if (amountTendered <= 0) {
                            alert("Please enter a valid amount tendered.");
                            return; // Stop further execution of the function
                        }

                        // Construct the URL with query parameters
                        var url = 'submit_all_checked.php?totalAmount=' + encodeURIComponent(totalAmount) +
                                '&amountTendered=' + encodeURIComponent(amountTendered) +
                                '&change=' + encodeURIComponent(change);

                        // Navigate to the URL
                        window.location.href = url;
                    }
                </script>


            <a href="delete_all_checked.php" id="submitLink2">
                <button style="padding: 10px 100px 10px 100px; background-color: #ff1a1a; border-color: white; color: white;">Cancel All</button>
            </a>
            <br>

            <script>
                document.getElementById('submitLink2').addEventListener('click', function(event) {
                    var confirmation = window.confirm("Are you sure you want to cancel all the data?");
                    
                    // If user clicks cancel, prevent the default action (going to submit_all_checked.php)
                    if (!confirmation) {
                        event.preventDefault();
                    }
                });
            </script>


            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- Script JS -->
    <script src="./assets/script.js"></script>
</body>
</html>
