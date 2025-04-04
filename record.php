<?php
$con  = mysqli_connect("localhost","root","","sales_inventory_db");
 if (!$con) {
     # code...
    echo "Problem in database connection! Contact administrator!" . mysqli_error();
 }else{
         $sql ="SELECT * FROM inventory WHERE stock_from = 'Sales'";
         $result = mysqli_query($con,$sql);
         $chart_data="";
         while ($row = mysqli_fetch_array($result)) { 
 
            $productname[]  = $row['product_id']  ;
            $sales[] = $row['qty'];
        }
 
 }
?>