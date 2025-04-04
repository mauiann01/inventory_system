<?php
  require 'db_connect.php';

    $qry_refresh = "UPDATE sales_monitoring SET Sales = '0'"; 
    $refresh_result = mysqli_query($conn, $qry_refresh);

    $query1 = "SELECT * FROM inventory WHERE stock_from = 'Sales'";
    $query_run1 = mysqli_query($conn, $query1);

  if(mysqli_num_rows($query_run1) > 0)
  {
      foreach($query_run1 as $row1)
      {
        $prd_id = $row1['product_id'];
        $prd_qty = $row1['qty'];
        $prd_price = 0;


        $year = date('Y', strtotime($row1['date_updated']));
        $month = date('m', strtotime($row1['date_updated']));
        $check = false;
        // QUERY 2
        $query2 = "SELECT * FROM product_list WHERE id = '$prd_id'";
        $query_run2 = mysqli_query($conn, $query2);

        if(mysqli_num_rows($query_run2) > 0)
        {
            foreach($query_run2 as $row2)
            {
                $prd_price = $row2['price'];
            }
          }
        // Query 3
          $query3 = "SELECT * FROM sales_monitoring WHERE `Year` = '$year' AND `Month` = '$month'";
          $query_run3 = mysqli_query($conn, $query3);
          if(mysqli_num_rows($query_run3) > 0)
          {
            foreach($query_run3 as $row3)
            {
                $last_sales = $row3['Sales'];
            }
            $check = true;
          }
        if($check){
            $total_sales = $last_sales + ($prd_qty * $prd_price);
            $query4 = "UPDATE sales_monitoring SET Sales = '$total_sales' WHERE `Year` = '$year' AND `Month` = '$month'"; 
            $update_result = mysqli_query($conn, $query4);
        }
        else{
            $total_sales = $prd_qty * $prd_price;
            $query5 = "INSERT INTO sales_monitoring (`Month`, `Year`, `Sales`) VALUES ('$month', '$year', '$total_sales')";
            $insert_result = mysqli_query($conn, $query5);
        }
      }
    }
?>