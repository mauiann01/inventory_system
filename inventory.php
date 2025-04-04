<?php include 'db_connect.php' ?>

<style>
  .card {
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
  }
  .card-body {
    font-weight: bold;
    font-size: 18px;
    color: #333;
  }
  .alert {
    padding: 20px;
    border-radius: 8px;
  }
  .icon {
    margin-right: 10px;
  }
  .table-container {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
  }
  .status-badge {
    padding: 8px 12px;
    border-radius: 4px;
    font-weight: 500;
    color: white;
  }
  .status-in-stock {
    background-color: #28a745;
  }
  .status-low-stock {
    background-color: #e69900;
  }
  .status-out-stock {
    background-color: #dc3545;
  }
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                <!-- Notification Area -->
                <div id="notification_area">
                    <?php
                        $low_stock_items_query = "SELECT p.name, 
                            COALESCE(SUM(CASE WHEN i.type = 1 AND i.status = 'completed' THEN i.qty ELSE 0 END), 0) -
                            COALESCE(SUM(CASE WHEN i.type = 2 AND i.status = 'completed' THEN i.qty ELSE 0 END), 0) as available
                        FROM product_list p
                        LEFT JOIN inventory i ON p.id = i.product_id
                        GROUP BY p.id, p.name
                        HAVING available > 0 AND available <= 100
                        ORDER BY available ASC";
                        
                        $low_stock_items = $conn->query($low_stock_items_query);
                        
                        if($low_stock_items->num_rows > 0) {
                            echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>";
                            echo "<strong>Low Stock Alert!</strong> The following items are running low:<br><ul>";
                            while($item = $low_stock_items->fetch_assoc()) {
                                echo "<li>{$item['name']} - {$item['available']} units remaining</li>";
                            }
                            echo "</ul>";
                            echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                            echo "<span aria-hidden='true'>&times;</span>";
                            echo "</button>";
                            echo "</div>";
                        }
                    ?>
                </div>
                <!-- End Notification Area -->
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="alert alert-success">
                            <h4>Total Products</h4>
                            <h4><b><?php 
                                $total = $conn->query("SELECT COUNT(*) as total FROM product_list");
                                echo $total->fetch_array()['total'];
                            ?></b></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-warning">
                            <h4>Low Stock Items</h4>
                            <h4><b><?php 
                                $low_stock_query = "SELECT COUNT(*) as count FROM (
                                    SELECT p.id,
                                           COALESCE(SUM(CASE WHEN i.type = 1 AND i.status = 'completed' THEN i.qty ELSE 0 END), 0) -
                                           COALESCE(SUM(CASE WHEN i.type = 2 AND i.status = 'completed' THEN i.qty ELSE 0 END), 0) as available
                                    FROM product_list p
                                    LEFT JOIN inventory i ON p.id = i.product_id
                                    GROUP BY p.id
                                    HAVING available > 0 AND available <= 100
                                ) as low_stock";
                                $low_stock_result = $conn->query($low_stock_query);
                                echo $low_stock_result->fetch_array()['count'];
                            ?></b></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-danger">
                            <h4>Out of Stock</h4>
                            <h4><b><?php 
                                $out_stock_query = "SELECT COUNT(*) as count FROM (
                                    SELECT p.id,
                                           COALESCE(SUM(CASE WHEN i.type = 1 AND i.status = 'completed' THEN i.qty ELSE 0 END), 0) -
                                           COALESCE(SUM(CASE WHEN i.type = 2 AND i.status = 'completed' THEN i.qty ELSE 0 END), 0) as available
                                    FROM product_list p
                                    LEFT JOIN inventory i ON p.id = i.product_id
                                    GROUP BY p.id
                                    HAVING available <= 0
                                ) as out_stock";
                                $out_stock_result = $conn->query($out_stock_query);
                                echo $out_stock_result->fetch_array()['count'];
                            ?></b></h4>
                        </div>
                    </div>
                </div>

                <!-- Inventory Table -->
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><b>Inventory List</b></h4>
                        <button class="btn btn-primary" id="refresh_inventory"><i class="fa fa-sync"></i> Refresh</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">#</th>
                                    <th class="text-center" style="width: 40%">Product Name</th>
                                    <th class="text-center" style="width: 15%">Stock In</th>
                                    <th class="text-center" style="width: 15%">Stock Out</th>
                                    <th class="text-center" style="width: 15%">Stock Available</th>
                                    <th class="text-center" style="width: 10%">Status</th>
                                </tr>
                            </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $product = $conn->query("SELECT * FROM product_list r order by name asc");
                                    while($row=$product->fetch_assoc()):
                                        $inn = $conn->query("SELECT sum(qty) as inn FROM inventory where `status`='completed' and type = 1 and product_id = ".$row['id']);
                                        $inn = $inn && $inn->num_rows > 0 ? $inn->fetch_array()['inn'] : 0;
                                        $out = $conn->query("SELECT sum(qty) as `out` FROM inventory where `status`='completed' and type = 2 and product_id = ".$row['id']);
                                        $out = $out && $out->num_rows > 0 ? $out->fetch_array()['out'] : 0;
                                        $available = $inn - $out;
                                        $status_class = $available <= 0 ? 'danger' : ($available <= 10 ? 'warning' : 'success');
                                        $status_text = $available <= 0 ? 'Out of Stock' : ($available <= 10 ? 'Low Stock' : 'In Stock');
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td><?php echo $row['name'] ?></td>
                                        <td class="text-right"><?php echo number_format($inn) ?></td>
                                        <td class="text-right"><?php echo number_format($out) ?></td>
                                        <td class="text-right"><?php echo number_format($available) ?></td>
                                        <td class="text-center">
                                            <span class="status-badge <?php 
    if($available <= 0) echo 'status-out-stock';
    else if($available <= 100) echo 'status-low-stock';
    else echo 'status-in-stock';
?>"><?php echo $status_text ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Main Card Styling */
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .card-header {
        background: linear-gradient(45deg, #2c3e50, #3498db);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }

    .card-header h4 {
        margin: 0;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 12px;
    }

    .table td {
        padding: 12px;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
        transition: background-color 0.3s ease;
    }

    /* Button Styling */
    .btn {
        padding: 6px 12px;
        font-weight: 500;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #2980b9, #3498db);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* Badge Styling */
    .badge {
        padding: 8px 12px;
        font-weight: 500;
        border-radius: 4px;
    }

    .badge-success {
        background: linear-gradient(45deg, #2ecc71, #27ae60);
    }

    .badge-warning {
        background: linear-gradient(45deg, #f1c40f, #f39c12);
    }

    .badge-danger {
        background: linear-gradient(45deg, #e74c3c, #c0392b);
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#inventoryTable').DataTable({
            "pageLength": 10,
            "order": [[1, "asc"]],
            "dom": '<"top"f>rt<"bottom"lp><"clear">',
            "language": {
                "search": "",
                "searchPlaceholder": "Search products..."
            }
        });

        $('#refresh_inventory').click(function(){
            location.reload();
        });

        // Function to check and display notifications
        function checkStockNotifications() {
            console.log('Checking stock notifications in inventory page...');
            $.ajax({
                url: 'notifications.php?action=get_notifications',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response && Array.isArray(response) && response.length > 0) {
                        $('#notification_area').empty();
                        response.forEach((notification, index) => {
                            let alertContent = `
                                <div class="alert alert-${notification.type} alert-dismissible fade show" role="alert">
                                    ${notification.message}
                                    ${notification.has_order_button ? `
                                        <div class="mt-2">
                                            <a href="index.php?page=manage_receiving" class="btn btn-sm btn-light">Order Stock</a>
                                        </div>
                                    ` : ''}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            const alert = $(alertContent);
                            $('#notification_area').append(alert);
                        });
                        $('#notification_area .alert').each(function(index) {
                            $(this).delay(index * 200).fadeIn(300);
                        });
                    }
                }
            });
        }

        // Initial check
        checkStockNotifications();
        
        // Set up periodic checks every 5 minutes
        setInterval(checkStockNotifications, 300000);
    });
</script>

<style>
    .table thead th {
        background-color: #343a40;
        color: white;
        border-bottom: 2px solid #dee2e6;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        padding: 0.5em 1em;
        font-size: 0.85em;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
        margin-left: 10px;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
</style>
