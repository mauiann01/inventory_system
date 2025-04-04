<?php
session_start();
include 'db_connect.php';
$id = $_GET['id'];
$qry = $conn->query("SELECT s.*, c.name as cname FROM sales_list s left join customer_list c on c.id = s.customer_id where s.id = $id");
$sales = $qry->fetch_array();
$inv = $conn->query("SELECT i.*, p.name as product_name, p.id as product_id FROM inventory i left join product_list p on p.id = i.product_id where i.type=2 and i.form_id=".$id);

// Debug information
if($inv->num_rows == 0) {
    echo "No items found for this sale.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Receipt</title>
	<style>
		body {
			font-family: 'Courier New', monospace;
			width: 80mm;
			margin: 0 auto;
			padding: 10px;
			font-size: 12px;
			line-height: 1.2;
			background: #fff;
		}
		.header {
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
		.store-address {
			font-size: 10px;
			margin-bottom: 5px;
		}
		.store-contact {
			font-size: 10px;
			margin-bottom: 5px;
		}
		.receipt-info {
			margin-bottom: 10px;
			font-size: 10px;
		}
		.items {
			width: 100%;
			margin-bottom: 10px;
		}
		.items th {
			text-align: left;
			font-size: 10px;
			padding: 2px 0;
		}
		.items td {
			padding: 2px 0;
			font-size: 10px;
		}
		.items .product-name {
			width: 40%;
		}
		.items .qty {
			width: 10%;
			text-align: right;
		}
		.items .price {
			width: 25%;
			text-align: right;
		}
		.items .amount {
			width: 25%;
			text-align: right;
		}
		.total-section {
			margin-top: 10px;
			border-top: 1px dashed #000;
			padding-top: 10px;
		}
		.total-row {
			display: flex;
			justify-content: space-between;
			margin-bottom: 5px;
			font-size: 10px;
		}
		.footer {
			margin-top: 20px;
			text-align: center;
			font-size: 10px;
			border-top: 1px dashed #000;
			padding-top: 10px;
		}
		@media print {
			body {
				width: 80mm;
				margin: 0;
				padding: 0;
				background: #fff;
			}
			.no-print {
				display: none !important;
			}
			.receipt-container {
				width: 80mm;
				margin: 0;
				padding: 0;
				background: #fff;
				border: none;
			}
		}
		@media screen {
			body {
				width: 100%;
				max-width: 1200px;
				margin: 20px auto;
				background: #fff;
				border-radius: 5px;
			}
			.receipt-container {
				width: 80mm;
				margin: 0 auto;
				background: #fff;
				padding: 10px;
				border: 1px solid #ddd;
			}
			.no-print {
				display: block;
				text-align: center;
				margin-top: 20px;
				padding: 20px;
				background: #f8f9fa;
				border-radius: 5px;
			}
			.btn-back {
				padding: 10px 20px;
				background: #007bff;
				color: white;
				border: none;
				border-radius: 5px;
				cursor: pointer;
				font-size: 14px;
				text-decoration: none;
				display: inline-block;
				margin-top: 10px;
			}
			.btn-back:hover {
				background: #0056b3;
			}
		}
	</style>
	<script>
		function printAndRedirect() {
			window.print();
			// Wait for print dialog to close before redirecting
			setTimeout(function() {
				window.location.href = 'index.php?page=sales';
			}, 1000);
		}
	</script>
</head>
<body>
	<div class="receipt-container">
		<div class="header">
			<div class="store-name">BEVERAGES STORE</div>
			<div class="store-address">123 Store Street, City, Country</div>
			<div class="store-contact">Tel: (123) 456-7890</div>
		</div>

		<div class="receipt-info">
			<div>Receipt #: <?php echo isset($sales['ref_no']) ? $sales['ref_no'] : 'N/A' ?></div>
			<div>Date: <?php echo isset($sales['date_created']) ? date('Y-m-d H:i',strtotime($sales['date_created'])) : date('Y-m-d H:i') ?></div>
			<div>Cashier: <?php echo isset($_SESSION['login_name']) ? $_SESSION['login_name'] : 'N/A' ?></div>
			<div>Customer: <?php echo isset($sales['cname']) ? $sales['cname'] : 'N/A' ?></div>
		</div>

		<table class="items">
			<thead>
				<tr>
					<th class="product-name">Item</th>
					<th class="qty">Qty</th>
					<th class="price">Price</th>
					<th class="amount">Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				while($row = $inv->fetch_assoc()):
					$other_details = json_decode($row['other_details'], true);
					if($other_details) {
						foreach($other_details as $k=>$v){
							$row[$k] = $v;
						}
					}
					
					// Debug information
					if(empty($row['product_name'])) {
						error_log("Missing product name for inventory ID: " . $row['id'] . ", product_id: " . $row['product_id']);
					}
				?>
				<tr>
					<td class="product-name"><?php echo !empty($row['product_name']) ? $row['product_name'] : 'Product ID: ' . $row['product_id'] ?></td>
					<td class="qty"><?php echo isset($row['qty']) ? $row['qty'] : '0' ?></td>
					<td class="price"><?php echo isset($row['price']) ? number_format($row['price'],2) : '0.00' ?></td>
					<td class="amount"><?php echo isset($row['qty']) && isset($row['price']) ? number_format($row['qty'] * $row['price'],2) : '0.00' ?></td>
				</tr>
				<?php endwhile; ?>
			</tbody>
		</table>

		<div class="total-section">
			<div class="total-row">
				<span>Subtotal:</span>
				<span><?php echo isset($sales['total_amount']) ? number_format($sales['total_amount'] / 1.06,2) : '0.00' ?></span>
			</div>
			<div class="total-row">
				<span>VAT (6%):</span>
				<span><?php echo isset($sales['total_amount']) ? number_format(($sales['total_amount'] / 1.06) * 0.06,2) : '0.00' ?></span>
			</div>
			<div class="total-row" style="font-weight: bold;">
				<span>Total:</span>
				<span><?php echo isset($sales['total_amount']) ? number_format($sales['total_amount'],2) : '0.00' ?></span>
			</div>
			<div class="total-row">
				<span>Amount Tendered:</span>
				<span><?php echo isset($sales['amount_tendered']) ? number_format($sales['amount_tendered'],2) : '0.00' ?></span>
			</div>
			<div class="total-row">
				<span>Change:</span>
				<span><?php echo isset($sales['amount_tendered']) && isset($sales['total_amount']) ? number_format($sales['amount_tendered'] - $sales['total_amount'],2) : '0.00' ?></span>
			</div>
		</div>

		<div class="footer">
			<div>Thank you for your purchase!</div>
			<div>Please come again</div>
			<div>This is a computer-generated receipt</div>
			<div>No need for signature</div>
		</div>
	</div>

	<div class="no-print">
		<button onclick="printAndRedirect()">Print Receipt</button>
		<br><br>
		<a href="index.php?page=sales" class="btn-back">Go Back to Sales</a>
	</div>
</body>
</html>

