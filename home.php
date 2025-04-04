<?php

    include 'db_connect.php';  

    
    $totalSuppliers = $conn->query("SELECT COUNT(*) as total FROM supplier_list");
    $totalSuppliersCount = $totalSuppliers->fetch_array()['total'];

    $totalPharmacists = $conn->query("SELECT COUNT(*) as total FROM users WHERE type = '2'");
    $totalPharmacistsCount = $totalPharmacists->fetch_array()['total'];
    
    $totalProducts = $conn->query("SELECT COUNT(*) as total FROM product_list");
    $totalProductsCount = $totalProducts->fetch_array()['total'];

    // $today = date('Y-m-d');
    // $expiredProducts = $conn->query("SELECT SUM(qty) as total FROM inventory WHERE expiration_date < '$today' AND stock_from = 'receiving'");
    // $expiredProductsCount = $expiredProducts->fetch_array()['total'];


?>

<style>
  .card {
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 20px;
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
  .welcome-msg {
    color: #555;
  }
</style>

<div class="container-fluid">

  <div class="row">
    <div class="col-lg-12"></div>
  </div>
  <div class="row mt-3 ml-3 mr-3">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body" style="font-weight: bold;">
          <?php echo "Welcome back " . $_SESSION['login_name'] . "!"; ?>
        </div>
        <hr style="margin-top: -15px; margin-bottom: -5px;">
<!-- New row for side-by-side columns -->
<div class="row" style="padding: 30px;">
  <div class="row mt-2">
    <!-- Total Suppliers -->
    <div class="col-md-4" style="width: 270px;">
      <div class="alert alert-success">
        <h4>Total Suppliers:</h4>
        <h4><b><?php echo $totalSuppliersCount; ?></b></h4>
      </div>
    </div>

    <!-- Total Pharmacists -->
    <div class="col-md-4" style="width: 270px;">
      <div class="alert alert-info">
        <h4>Total Users:</h4>
        <h4><b><?php echo $totalPharmacistsCount; ?></b></h4>
      </div>
    </div>

    <!-- Total Products -->
    <div class="col-md-4" style="width: 270px;">
      <div class="alert alert-warning">
        <h4>Total Products:</h4>
        <h4><b><?php echo $totalProductsCount; ?></b></h4>
      </div>
    </div>

    <!-- Total Expired Products -->
    <!-- <div class="col-md-3" style="width: 250px;">
      <div class="alert alert-danger">
        <h4>Expired Prd:</h4>
        <h4><b><?php echo $expiredProductsCount; ?></b></h4>
      </div>
    </div> -->
  </div>
            
          <!-- Left Column: Today's Sales -->
          <div class="col-md-6" style="margin-left: -15px;">
            <div class="alert alert-success" style="background-color: #e69900; color: white;">
              <h4>
                <i class="fas fa-cash-register icon"></i>
                <b>Today's Sales:</b>
              </h4>
              <center><img src="icon_1.png" alt="" style="width: 150px;"></center>
              <hr>
              <div class="d-flex justify-content-between">
                <h4>(<?php echo date('Y-m-d'); ?>)</h4>
                <h4><b>Php <?php 
                  $sales = $conn->query("SELECT SUM(total_amount) as amount FROM sales_list WHERE date(date_updated) = '" . date('Y-m-d') . "'");
                  echo $sales->num_rows > 0 ? number_format($sales->fetch_array()['amount'], 2) : "0.00";
                ?></b></h4>
              </div>
            </div>
          </div>

          <!-- Right Column: Total Sales of the Current Month -->
          <div class="col-md-6" style="margin-left: 15px;">
            <div class="alert alert-info" style="background-color: #2196F3; color: white;">
              <h4>
                <i class="fas fa-chart-line icon"></i>
                <b>Total Sales (<?php echo date('F'); ?>):</b>
              </h4>
              <center><img src="icon_2.png" alt="" style="width: 150px;"></center>
              <hr>
              <div class="d-flex justify-content-between">
                <h4>(<?php echo date('F Y'); ?>)</h4>
                <h4><b>Php <?php 
                  // Query to get total sales of the current month
                  $currentMonth = date('m'); // Get the current month
                  $currentYear = date('Y');  // Get the current year
                  $sales = $conn->query("SELECT SUM(total_amount) as amount FROM sales_list WHERE MONTH(date_updated) = $currentMonth AND YEAR(date_updated) = $currentYear");
                  echo $sales->num_rows > 0 ? number_format($sales->fetch_array()['amount'], 2) : "0.00";
                ?></b></h4>
              </div>
            </div>
          </div>
        </div> <!-- End of Row -->
    </div>
    


  </div>
</div>

<script>
// Additional JavaScript if needed
</script>


