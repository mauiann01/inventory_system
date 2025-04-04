<?php 
include 'db_connect.php';

if(isset($_POST['but_search'])){
    $fromDate = $_POST['fromDate'];
    $endDate = $_POST['endDate'];

    $final_from = date('Y-m-d', strtotime($fromDate));
    $final_to= date('Y-m-d', strtotime($endDate));

    $add_qry = "and date_updated between '".$final_from."' and '".$final_to."' ";
}
else{
    $add_qry = "";
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $endDate = date('Y-m-d');
}
?>

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

    /* Form Styling */
    .date-filter-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .dateFilter {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 12px;
        margin: 0 10px;
    }

    .btn-filter {
        background: linear-gradient(45deg, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Summary Cards */
    .summary-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .summary-card h5 {
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .summary-card .value {
        font-size: 24px;
        font-weight: bold;
        color: #3498db;
    }
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><b>Sales Report</b></h4>
                    </div>
                    <div class="card-body">
                        <!-- Search filter -->
                        <form method='post' action='' class="date-filter-form">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="form-group mr-3">
                                        <label class="mr-2">From:</label>
                                        <input type='date' class='dateFilter' name='fromDate' value='<?php echo $fromDate; ?>' required>
                                    </div>
                                    <div class="form-group mr-3">
                                        <label class="mr-2">To:</label>
                                        <input type='date' class='dateFilter' name='endDate' value='<?php echo $endDate; ?>' required>
                                    </div>
                                    <button type='submit' name='but_search' class='btn btn-filter'>
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <h5>Total Sales</h5>
                                    <div class="value"><?php 
                                        $total_query = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM sales_list WHERE 1=1 $add_qry");
                                        $total = mysqli_fetch_assoc($total_query)['total'];
                                        echo '₱' . number_format($total, 2);
                                    ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <h5>Total Items Sold</h5>
                                    <div class="value"><?php 
                                        $items_query = mysqli_query($conn, "SELECT SUM(qty) as total FROM inventory WHERE stock_from = 'Sales' $add_qry");
                                        echo number_format(mysqli_fetch_assoc($items_query)['total']);
                                    ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <h5>Unique Products</h5>
                                    <div class="value"><?php 
                                        $products_query = mysqli_query($conn, "SELECT COUNT(DISTINCT product_id) as total FROM inventory WHERE stock_from = 'Sales' $add_qry");
                                        echo number_format(mysqli_fetch_assoc($products_query)['total']);
                                    ?></div>
                                </div>
                            </div>
                        </div>
                    <?php
                    // Attempt select query execution

                    $sql = "SELECT s.*, c.name as customer_name 
                            FROM sales_list s 
                            LEFT JOIN customer_list c ON c.id = s.customer_id 
                            WHERE 1=1 $add_qry 
                            ORDER BY s.date_updated DESC";

                    if($result = mysqli_query($conn, $sql)){

                        if(mysqli_num_rows($result) > 0){

                            echo "<table class='table table-hover' id='myTable'>";

                                echo "<thead class='thead-light'>";

                                    echo "<tr>";

                                        echo "<th>Reference No</th>";

                                        echo "<th>Customer</th>";

                                        echo "<th>Total Amount</th>";

                                        echo "<th>Amount Tendered</th>";

                                        echo "<th>Change</th>";

                                        echo "<th>Date</th>";

                                    echo "</tr>";

                                echo "</thead>";

                                echo "<tbody>";

                                while($row = mysqli_fetch_array($result)){

                                    echo "<tr>";

                                        echo "<td>" . $row['ref_no'] . "</td>";

                                        echo "<td>" . ($row['customer_name'] ? $row['customer_name'] : 'Walk-in') . "</td>";

                                        echo "<td>₱" . number_format($row['total_amount'], 2) . "</td>";

                                        echo "<td>₱" . number_format($row['amount_tendered'], 2) . "</td>";

                                        echo "<td>₱" . number_format($row['amount_change'], 2) . "</td>";

                                        echo "<td>" . date('M d, Y h:i A', strtotime($row['date_updated'])) . "</td>";

                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                    }
 
                    // Close connection
                    mysqli_close($conn);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</div>
</div>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>

    <!-- <script>
    $(document).ready( function () {
        $('#myTable').DataTable();
    } );
    </script> -->

    <script>
        $(document).ready(function() {
    $('#myTable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'print'
        ]
    } );
} );
    </script>