<style>
</style>
<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
		<div class="sidebar-list">
				
				<?php if(isset($_SESSION['supplier_status'])): ?>
					<a href="index.php?page=supplier_receiving" class="nav-item nav-supplier_receiving nav-manage_receiving"><span class='icon-field'><i class="fa fa-file-alt"></i></span> Receiving</a>
				<?php else: ?>
					<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Home</a>
					<a href="index.php?page=inventory" class="nav-item nav-inventory"><span class='icon-field'><i class="fa fa-list"></i></span> Inventory</a>
					<a href="index.php?page=sales" class="nav-item nav-sales"><span class='icon-field'><i class="fa fa-coins"></i></span> Sales</a>
					<?php if($_SESSION['login_type'] == 1): ?>
						<a href="index.php?page=analytics_dashboard" class="nav-item nav-analytics_dashboard"><span class='icon-field'><i class="fa fa-chart-bar"></i></span> Analytics Dashboard</a>
						<a href="index.php?page=sales_report" class="nav-item nav-sales_report"><span class='icon-field'><i class="fa fa-file-alt"></i></span> Sales Report</a>
						<a href="index.php?page=receiving" class="nav-item nav-receiving nav-manage_receiving"><span class='icon-field'><i class="fa fa-file-alt"></i></span> Receiving</a>
						<a href="forecasting.php" class="nav-item nav-forecasting"><span class='icon-field'><i class="fa fa-chart-line"></i></span> Demand Forecasting</a>
						<a href="index.php?page=categories" class="nav-item nav-categories"><span class='icon-field'><i class="fa fa-list"></i></span> Category List</a>
						<a href="index.php?page=product" class="nav-item nav-product"><span class='icon-field'><i class="fa fa-boxes"></i></span> Product List</a>
						<a href="index.php?page=supplier" class="nav-item nav-supplier"><span class='icon-field'><i class="fa fa-truck-loading"></i></span> Supplier List</a>
						<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a>
					<?php else: ?>
						<a href="index.php?page=sales_report" class="nav-item nav-sales_report"><span class='icon-field'><i class="fa fa-file-alt"></i></span> Sales Report</a>
					<?php endif; ?>
				<?php endif; ?>
		</div>
</nav>
<script>
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>
<?php if($_SESSION['login_type'] != 1): ?>
	<style>
		.nav-item{
			display: none!important;
		}
		.nav-sales ,.nav-home ,.nav-inventory{
			display: block!important;
		}
	</style>
<?php endif ?>
