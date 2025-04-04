<?php
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT s.*, c.name as cname FROM sales_list s left join customer_list c on c.id = s.customer_id where s.id=".$_GET['id']);
	$sales = $qry->fetch_array();
	$inv = $conn->query("SELECT i.*, p.name as product_name FROM inventory i 
						LEFT JOIN product_list p ON p.id = i.product_id 
						WHERE i.type=2 and i.form_id=".$_GET['id']);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Print Receipt</title>
	<style>
		@page {
			size: 80mm auto;
			margin: 0;
		}
		body {
			font-family: 'Courier New', monospace;
			width: 80mm;
			margin: 0;
			padding: 10px;
			font-size: 10px;
			line-height: 1.2;
			background: #fff;
			color: #000;
		}
		.header {
			text-align: center;
			margin-bottom: 10px;
			padding-bottom: 10px;
			border-bottom: 1px dashed #000;
		}
		.store-name {
			font-size: 14px;
			font-weight: bold;
			margin-bottom: 5px;
			text-transform: uppercase;
		}
		.store-address {
			font-size: 9px;
			margin-bottom: 3px;
		}
		.store-contact {
			font-size: 9px;
			margin-bottom: 3px;
		}
		.receipt-info {
			margin-bottom: 10px;
			font-size: 9px;
			line-height: 1.2;
		}
		.receipt-info div {
			margin-bottom: 2px;
		}
		.items {
			width: 100%;
			margin-bottom: 10px;
			border-collapse: collapse;
		}
		.items th {
			text-align: left;
			font-size: 9px;
			padding: 2px 0;
			border-bottom: 1px dashed #000;
			font-weight: bold;
		}
		.items td {
			padding: 2px 0;
			font-size: 9px;
			border-bottom: 1px dotted #000;
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
			margin-bottom: 3px;
			font-size: 9px;
		}
		.total-row:last-child {
			margin-top: 5px;
			padding-top: 5px;
			border-top: 1px dashed #000;
			font-weight: bold;
		}
		.footer {
			margin-top: 15px;
			text-align: center;
			font-size: 9px;
			border-top: 1px dashed #000;
			padding-top: 10px;
			line-height: 1.2;
		}
		.footer div {
			margin-bottom: 3px;
		}
		/* Receipt-specific styles */
		.receipt-info div:first-child {
			font-weight: bold;
		}
		.items tr:last-child td {
			border-bottom: none;
		}
		.total-row span:last-child {
			font-family: 'Courier New', monospace;
			letter-spacing: 1px;
		}
		.footer div:first-child {
			font-weight: bold;
		}
		/* Add some thermal receipt characteristics */
		body::before {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: repeating-linear-gradient(
				transparent,
				transparent 1px,
				rgba(0,0,0,0.05) 1px,
				rgba(0,0,0,0.05) 2px
			);
			pointer-events: none;
			z-index: 1;
		}
		body > * {
			position: relative;
			z-index: 2;
		}
	</style>
</head>
<body>
	<div class="header">
		<div class="store-name">BEVERAGES STORE</div>
		<div class="store-address">123 Store Street, City, Country</div>
		<div class="store-contact">Tel: (123) 456-7890</div>
	</div>

	<div class="receipt-info">
		<div>Receipt #: <?php echo isset($sales['ref_no']) ? $sales['ref_no'] : '' ?></div>
		<div>Date: <?php echo isset($sales['date_updated']) ? date('Y-m-d H:i',strtotime($sales['date_updated'])) : date('Y-m-d H:i') ?></div>
		<div>Cashier: <?php echo isset($_SESSION['login_name']) ? $_SESSION['login_name'] : '' ?></div>
		<div>Customer: <?php echo isset($sales['cname']) ? $sales['cname'] : 'Guest' ?></div>
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
			if(isset($inv) && $inv->num_rows > 0):
				while($row = $inv->fetch_assoc()):
					foreach(json_decode($row['other_details']) as $k=>$v){
						$row[$k] = $v;
					}
			?>
			<tr>
				<td class="product-name"><?php echo isset($row['product_name']) ? $row['product_name'] : 'Unknown Product' ?></td>
				<td class="qty"><?php echo isset($row['qty']) ? $row['qty'] : '0' ?></td>
				<td class="price">₱<?php echo isset($row['price']) ? number_format($row['price'],2) : '0.00' ?></td>
				<td class="amount">₱<?php echo isset($row['qty']) && isset($row['price']) ? number_format($row['qty'] * $row['price'],2) : '0.00' ?></td>
			</tr>
			<?php 
				endwhile;
			else:
			?>
			<tr>
				<td colspan="4" class="text-center">No items found</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<div class="total-section">
		<div class="total-row">
			<span>Subtotal:</span>
			<span>₱<?php echo isset($sales['total_amount']) ? number_format($sales['total_amount'] / 1.06,2) : '0.00' ?></span>
		</div>
		<div class="total-row">
			<span>VAT (6%):</span>
			<span>₱<?php echo isset($sales['total_amount']) ? number_format(($sales['total_amount'] / 1.06) * 0.06,2) : '0.00' ?></span>
		</div>
		<div class="total-row">
			<span>Total:</span>
			<span>₱<?php echo isset($sales['total_amount']) ? number_format($sales['total_amount'],2) : '0.00' ?></span>
		</div>
		<div class="total-row">
			<span>Amount Tendered:</span>
			<span>₱<?php echo isset($sales['amount_tendered']) ? number_format($sales['amount_tendered'],2) : '0.00' ?></span>
		</div>
		<div class="total-row">
			<span>Change:</span>
			<span>₱<?php echo isset($sales['amount_tendered']) && isset($sales['total_amount']) ? number_format($sales['amount_tendered'] - $sales['total_amount'],2) : '0.00' ?></span>
		</div>
	</div>

	<div class="footer">
		<div>Thank you for your purchase!</div>
		<div>Please come again</div>
		<div>This is a computer-generated receipt</div>
		<div>No need for signature</div>
	</div>
</body>
</html> 