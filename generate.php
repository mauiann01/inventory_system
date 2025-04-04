<?php
  require 'db_connect.php';

  $query1 = "SELECT * FROM product_list";
  $query_run1 = mysqli_query($conn, $query1);

  if(mysqli_num_rows($query_run1) > 0)
  {
      foreach($query_run1 as $row1)
      {
        $prd_id = $row1['id'];
        $prd_name = $row1['name'];
        $ttl_qty = 0;
        $check = false;
        // QUERY 2
        $query2 = "SELECT SUM(qty) as sum_qty FROM inventory WHERE product_id = '$prd_id' AND stock_from = 'Sales'";
        $query_run2 = mysqli_query($conn, $query2);

        if(mysqli_num_rows($query_run2) > 0)
        {
            foreach($query_run2 as $row2)
            {
                $ttl_qty = $row2['sum_qty'];
            }
          }
        // Query 3
          $query3 = "SELECT * FROM product_sales WHERE Product_Name = '$prd_name'";
          $query_run3 = mysqli_query($conn, $query3);
          if(mysqli_num_rows($query_run3) > 0)
          {
            $check = true;;
          }
        if($check){
          $query4 = "UPDATE product_sales SET Product_Name = '$prd_name', Qty = '$ttl_qty' WHERE Product_Name = '$prd_name';"; 
          $update_result = mysqli_query($conn, $query4);
        }
        else{
        $query5 = "INSERT INTO product_sales (Product_Name, Qty) VALUES ('$prd_name', '$ttl_qty')";
        $insert_result = mysqli_query($conn, $query5);
        }
      }
    }
?>