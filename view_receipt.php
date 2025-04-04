<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
    // Add error checking and proper SQL escaping
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $qry = $conn->query("SELECT * FROM sales_list where id = '$id'");
    
    if(!$qry) {
        echo "Error in query: " . $conn->error;
        exit;
    }
    
    $result = $qry->fetch_array();
    if($result) {
        foreach($result as $k => $val){
            $$k = $val;
        }
    } else {
        echo "No receipt found for ID: $id";
        exit;
    }
    
    // Get inventory items with error checking
    $inv = $conn->query("SELECT * FROM inventory where type=2 and form_id='$id'");
    if(!$inv) {
        echo "Error in inventory query: " . $conn->error;
        exit;
    }
}
if($customer_id > 0){
		$cname = $conn->query("SELECT * FROM customer_list where id = $customer_id ");
		$cname = $cname->num_rows > 0 ? $cname->fetch_array()['name']: "Guest";
	}else{
		$cname = "Guest";
	}
}
$product = $conn->query("SELECT * FROM product_list  order by name asc");
	while($row=$product->fetch_assoc()):
		$prod[$row['id']] = $row;
	endwhile;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Receipt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/font-awesome/css/all.min.css">
</head>
<body>
    <?php include 'topbar.php' ?>
    <?php include 'navbar.php' ?>
    
    <div class="container-fluid" id="print-sales">
        <style>
            body {
                font-family: 'Courier New', monospace;
                font-size: 12px;
                line-height: 1.2;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            .wborder {
                border: 1px solid #000;
            }
            .bbottom {
                border-bottom: 1px solid #000;
            }
            td p, th p {
                margin: unset;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            .clear {
                padding: 5px;
            }
            #uni_modal .modal-footer {
                display: none;
            }
            .store-header {
                text-align: center;
                margin-bottom: 10px;
                padding-bottom: 10px;
                border-bottom: 1px dashed #000;
            }
            .store-name {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .store-info {
                font-size: 10px;
                margin-bottom: 3px;
            }
            .receipt-info {
                margin-bottom: 10px;
                font-size: 10px;
            }
            .receipt-info div {
                margin-bottom: 2px;
            }
            .items-table th {
                font-size: 10px;
                padding: 2px 5px;
                border-bottom: 1px dashed #000;
            }
            .items-table td {
                font-size: 10px;
                padding: 2px 5px;
                border-bottom: 1px dotted #000;
            }
            .total-section {
                margin-top: 10px;
                border-top: 1px dashed #000;
                padding-top: 5px;
            }
            .total-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 2px;
                font-size: 10px;
            }
            .footer {
                margin-top: 15px;
                text-align: center;
                font-size: 10px;
                border-top: 1px dashed #000;
                padding-top: 10px;
            }
            @media print {
                body {
                    font-size: 10px;
                }
                .store-name {
                    font-size: 14px;
                }
                .store-info, .receipt-info, .items-table th, .items-table td, .total-row, .footer {
                    font-size: 9px;
                }
                #sidebar, .navbar, .btn {
                    display: none !important;
                }
            }
        </style>
        <div class="store-header">
            <div class="store-name">BEVERAGES STORE</div>
            <div class="store-info">123 Store Street, City, Country</div>
            <div class="store-info">Tel: (123) 456-7890</div>
        </div>

        <div class="receipt-info">
            <div>Receipt #: <?php echo $ref_no ?></div>
            <div>Date: <?php echo date("Y-m-d H:i",strtotime($date_updated)) ?></div>
            <div>Cashier: <?php echo isset($_SESSION['login_name']) ? $_SESSION['login_name'] : '' ?></div>
            <div>Customer: <?php echo ucwords($cname) ?></div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center">Qty</th>
                    <th>Product</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while($row = $inv->fetch_assoc()): 
                    foreach(json_decode($row['other_details']) as $k=>$v){
                        $row[$k] = $v;
                    }
                ?>
                <tr>
                    <td class="text-center"><?php echo $row['qty'] ?></td>
                    <td><?php echo $prod[$row['product_id']]['name'] ?></td>
                    <td class="text-right">₱<?php echo number_format($row['price'],2) ?></td>
                    <td class="text-right">₱<?php echo number_format($row['price'] * $row['qty'],2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₱<?php echo number_format($total_amount / 1.06,2) ?></span>
            </div>
            <div class="total-row">
                <span>VAT (6%):</span>
                <span>₱<?php echo number_format(($total_amount / 1.06) * 0.06,2) ?></span>
            </div>
            <div class="total-row">
                <span>Total:</span>
                <span>₱<?php echo number_format($total_amount,2) ?></span>
            </div>
            <div class="total-row">
                <span>Amount Tendered:</span>
                <span>₱<?php echo number_format($amount_tendered,2) ?></span>
            </div>
            <div class="total-row">
                <span>Change:</span>
                <span>₱<?php echo number_format($amount_change,2) ?></span>
            </div>
        </div>

        <div class="footer">
            <div>Thank you for your purchase!</div>
            <div>Please come again</div>
            <div>This is a computer-generated receipt</div>
            <div>No need for signature</div>
        </div>
    </div>

    <div class="text-right mt-3">
        <div class="col-md-12">
            <div class="row">
                <button type="button" class="btn btn-sm btn-primary" id="print"><i class="fa fa-print"></i> Print</button>&nbsp;
                <a class="btn btn-sm btn-secondary" href="index.php?page=pos&id=<?php echo $my_id;?>">Go back</a>
            </div>
        </div>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#print').click(function(){
            var _html = $('#print-sales').clone();
            var newWindow = window.open("","_blank","menubar=no,scrollbars=yes,resizable=yes,width=700,height=600");
            newWindow.document.write(_html.html())
            newWindow.document.close()
            newWindow.focus()
            newWindow.print()
            setTimeout(function(){;newWindow.close();}, 1500);
        })
    </script>
</body>
</html>

