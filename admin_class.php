<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

    function update_stock(){
        extract($_POST);
        
        try {
            $this->db->begin_transaction();
            
            $product_ids = explode(',', $product_ids);
            foreach($product_ids as $product_id) {
                $product_id = (int)$product_id;
                
                // Get current stock
                $curr_stock = $this->db->query("SELECT * FROM product_list WHERE id = $product_id")->fetch_array()['stock'];
                
                // Calculate new stock based on action
                $new_stock = $curr_stock;
                switch($action) {
                    case 'add':
                        $new_stock = $curr_stock + (int)$quantity;
                        break;
                    case 'subtract':
                        $new_stock = max(0, $curr_stock - (int)$quantity);
                        break;
                    case 'set':
                        $new_stock = (int)$quantity;
                        break;
                }
                
                // Update product stock
                $update = $this->db->query("UPDATE product_list SET stock = $new_stock, last_movement = NOW() WHERE id = $product_id");
                if(!$update) throw new Exception("Failed to update stock for product ID: $product_id");
                
                // Record inventory movement
                $qty_change = $new_stock - $curr_stock;
                if($qty_change != 0) {
                    $type = $qty_change > 0 ? 1 : 2; // 1 for in, 2 for out
                    $qty = abs($qty_change);
                    $stock_movement = $this->db->query("INSERT INTO inventory (product_id, qty, type, stock_from, stock_to, remarks) 
                        VALUES ($product_id, $qty, $type, $curr_stock, $new_stock, '$note')");
                    if(!$stock_movement) throw new Exception("Failed to record inventory movement for product ID: $product_id");
                }
            }
            
            $this->db->commit();
            return json_encode(['success' => true]);
            
        } catch (Exception $e) {
            $this->db->rollback();
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    function check_low_stock(){
        $query = $this->db->query("SELECT COUNT(*) as count FROM product_list WHERE stock <= 10 AND stock > 0");
        $result = $query->fetch_assoc();
        return json_encode(['low_stock' => $result['count']]);
    }

    function get_stock_movement(){
        $days = 30; // Last 30 days
        $query = $this->db->query("SELECT 
            DATE(date_created) as date,
            SUM(CASE WHEN type = 1 THEN qty ELSE 0 END) as stock_in,
            SUM(CASE WHEN type = 2 THEN qty ELSE 0 END) as stock_out
            FROM inventory 
            WHERE date_created >= DATE_SUB(NOW(), INTERVAL $days DAY)
            GROUP BY DATE(date_created)
            ORDER BY date");

        $labels = [];
        $stock_in = [];
        $stock_out = [];

        while($row = $query->fetch_assoc()) {
            $labels[] = date('M d', strtotime($row['date']));
            $stock_in[] = $row['stock_in'];
            $stock_out[] = $row['stock_out'];
        }

        return json_encode([
            'labels' => $labels,
            'stock_in' => $stock_in,
            'stock_out' => $stock_out
        ]);
    }

    function get_category_sales(){
        $query = $this->db->query("SELECT 
            c.name,
            COUNT(p.id) as product_count,
            SUM(p.stock) as total_stock
            FROM category_list c
            LEFT JOIN product_list p ON p.category_id = c.id
            GROUP BY c.id
            ORDER BY total_stock DESC
            LIMIT 6");

        $labels = [];
        $values = [];

        while($row = $query->fetch_assoc()) {
            $labels[] = $row['name'];
            $values[] = $row['total_stock'];
        }

        return json_encode([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    function import_products(){
        extract($_POST);
        
        try {
            if(!isset($_FILES['import_file'])) {
                throw new Exception("No file uploaded");
            }

            $file = $_FILES['import_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            if(!in_array($ext, ['xlsx', 'xls', 'csv'])) {
                throw new Exception("Invalid file format");
            }

            // Read file using PhpSpreadsheet
            require_once 'vendor/autoload.php';
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Start transaction
            $this->db->begin_transaction();

            // Skip header row
            array_shift($rows);

            foreach($rows as $row) {
                $sku = $row[0];
                $name = $row[1];
                $category = $row[2];
                $price = $row[3];
                $stock = $row[4];

                // Get or create category
                $cat_query = $this->db->query("SELECT id FROM category_list WHERE name = '$category'");
                if($cat_query->num_rows > 0) {
                    $category_id = $cat_query->fetch_assoc()['id'];
                } else {
                    $this->db->query("INSERT INTO category_list (name) VALUES ('$category')");
                    $category_id = $this->db->insert_id;
                }

                // Check if product exists
                $prod_query = $this->db->query("SELECT id FROM product_list WHERE sku = '$sku'");
                if($prod_query->num_rows > 0 && isset($update_existing)) {
                    $product_id = $prod_query->fetch_assoc()['id'];
                    $this->db->query("UPDATE product_list SET 
                        name = '$name',
                        category_id = $category_id,
                        price = $price,
                        stock = $stock
                        WHERE id = $product_id");
                } else {
                    $this->db->query("INSERT INTO product_list (sku, name, category_id, price, stock) 
                        VALUES ('$sku', '$name', $category_id, $price, $stock)");
                }
            }

            $this->db->commit();
            return json_encode(['success' => true]);

        } catch (Exception $e) {
            $this->db->rollback();
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    function export_products(){
        require_once 'vendor/autoload.php';

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Headers
            $sheet->setCellValue('A1', 'SKU');
            $sheet->setCellValue('B1', 'Name');
            $sheet->setCellValue('C1', 'Category');
            $sheet->setCellValue('D1', 'Price');
            $sheet->setCellValue('E1', 'Stock');

            // Data
            $query = $this->db->query("SELECT p.*, c.name as category FROM product_list p 
                LEFT JOIN category_list c ON c.id = p.category_id");
            
            $row = 2;
            while($data = $query->fetch_assoc()) {
                $sheet->setCellValue('A'.$row, $data['sku']);
                $sheet->setCellValue('B'.$row, $data['name']);
                $sheet->setCellValue('C'.$row, $data['category']);
                $sheet->setCellValue('D'.$row, $data['price']);
                $sheet->setCellValue('E'.$row, $data['stock']);
                $row++;
            }

            // Auto-size columns
            foreach(range('A','E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Output file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="products_export.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    function download_template(){
        require_once 'vendor/autoload.php';

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Headers
            $sheet->setCellValue('A1', 'SKU');
            $sheet->setCellValue('B1', 'Name');
            $sheet->setCellValue('C1', 'Category');
            $sheet->setCellValue('D1', 'Price');
            $sheet->setCellValue('E1', 'Stock');

            // Example row
            $sheet->setCellValue('A2', 'PROD001');
            $sheet->setCellValue('B2', 'Example Product');
            $sheet->setCellValue('C2', 'Example Category');
            $sheet->setCellValue('D2', '99.99');
            $sheet->setCellValue('E2', '100');

            // Auto-size columns
            foreach(range('A','E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Output file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="product_import_template.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    function get_price_history(){
        extract($_POST);
        
        $query = $this->db->query("SELECT 
            ph.*,
            u.name as changed_by
            FROM price_history ph
            LEFT JOIN users u ON u.id = ph.user_id
            WHERE ph.product_id = $product_id
            ORDER BY ph.date_created DESC");

        $history = [];
        $chart_labels = [];
        $chart_prices = [];

        while($row = $query->fetch_assoc()) {
            $history[] = [
                'date' => date('M d, Y', strtotime($row['date_created'])),
                'old_price' => number_format($row['old_price'], 2),
                'new_price' => number_format($row['new_price'], 2),
                'changed_by' => $row['changed_by'],
                'reason' => $row['reason']
            ];

            $chart_labels[] = date('M d', strtotime($row['date_created']));
            $chart_prices[] = $row['new_price'];
        }

        return json_encode([
            'history' => $history,
            'chart' => [
                'labels' => array_reverse($chart_labels),
                'prices' => array_reverse($chart_prices)
            ]
        ]);
    }

    function save_variants(){
        extract($_POST);
        
        try {
            $this->db->begin_transaction();

            $variants = json_decode($variants, true);
            foreach($variants as $variant) {
                $name = $variant['name'];
                $sku = $variant['sku'];
                $price = $variant['price'];
                $stock = $variant['stock'];

                $this->db->query("INSERT INTO product_variants 
                    (product_id, variant_name, name, sku, price, stock) VALUES 
                    ($product_id, '$variant_name', '$name', '$sku', $price, $stock)");
            }

            $this->db->commit();
            return json_encode(['success' => true]);

        } catch (Exception $e) {
            $this->db->rollback();
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    function export_products_qr(){
        require_once('vendor/autoload.php'); // Make sure you have TCPDF installed
        extract($_POST);

        try {
            // Create new PDF document
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
            $pdf->SetCreator('Inventory System');
            $pdf->SetTitle('Product QR Codes');
            $pdf->SetMargins(10, 10, 10);
            $pdf->AddPage();

            // Get products
            $product_ids = implode(',', array_map('intval', $product_ids));
            $query = $this->db->query("SELECT * FROM product_list WHERE id IN ($product_ids)");

            $x = 10;
            $y = 10;
            $counter = 0;

            while($row = $query->fetch_assoc()) {
                // Generate QR code
                $qr_data = json_encode([
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'sku' => $row['sku']
                ]);
                
                $params = $pdf->serializeTCPDFtagParameters(array($qr_data, 'QRCODE,H', '', '', 30, 30, array('border' => 0, 'padding' => 2)));
                $pdf->writeHTML("<tcpdf method=\"write2DBarcode\" params=\"$params\" />", true, false, true, false, '');

                // Add product details
                $pdf->SetXY($x, $y + 32);
                $pdf->Cell(30, 5, $row['name'], 0, 2, 'C');
                $pdf->Cell(30, 5, $row['sku'], 0, 2, 'C');

                // Move to next position
                $counter++;
                if($counter % 6 == 0) {
                    $x = 10;
                    $y += 50;
                    if($y > 250) {
                        $pdf->AddPage();
                        $y = 10;
                    }
                } else {
                    $x += 35;
                }
            }

            // Output PDF
            return $pdf->Output('products_qr.pdf', 'S');

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    function bulk_delete_products(){
        extract($_POST);
        
        try {
            $this->db->begin_transaction();
            
            $product_ids = implode(',', array_map('intval', $product_ids));
            
            // Delete related inventory records
            $delete_inventory = $this->db->query("DELETE FROM inventory WHERE product_id IN ($product_ids)");
            if(!$delete_inventory) throw new Exception("Failed to delete inventory records");
            
            // Delete products
            $delete_products = $this->db->query("DELETE FROM product_list WHERE id IN ($product_ids)");
            if(!$delete_products) throw new Exception("Failed to delete products");
            
            $this->db->commit();
            return json_encode(['success' => true]);
            
        } catch (Exception $e) {
            $this->db->rollback();
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

	function login(){
		extract($_POST);
	
		// Check in users table
		$qry = $this->db->query("SELECT * FROM users WHERE username = '$username' AND password = '$password'");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key)) {
					$_SESSION['login_' . $key] = $value;
				}
			}
			return 1; // Login successful for users table
		}
	
		// Check in supplier_list table
		$qry = $this->db->query("SELECT * FROM supplier_list WHERE username = '$username' AND password = '$password'");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key)) {
					$_SESSION['login_' . $key] = $value;
				}
			}
			$_SESSION['login_name'] = $username;
			$_SESSION['supplier_status'] = "1";
			return 2; // Login successful for supplier_list table
		}
	
		return 3; // Login failed
	}
	
	function login2(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM user_info where email = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			$this->db->query("UPDATE cart set user_id = '".$_SESSION['login_user_id']."' where client_ip ='$ip' ");
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = " first_name = '$first_name' ";
		$data .= ", last_name = '$last_name' ";
		$data .= ", mobile = '$mobile' ";
		$data .= ", address = '$address' ";
		$data .= ", email = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM user_info where email = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO user_info set ".$data);
		if($save){
			$login = $this->login2();
			return 1;
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data." where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}

			return 1;
				}
	}

	
	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO category_list set ".$data);
		}else{
			$save = $this->db->query("UPDATE category_list set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM category_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_supplier(){
		extract($_POST);
		$data = " supplier_name = '$name' ";
		$data .= ", contact = '$contact' ";
		$data .= ", address = '$address' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO supplier_list set ".$data);
		}else{
			$save = $this->db->query("UPDATE supplier_list set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_supplier(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM supplier_list where id = ".$id);
		if($delete)
			return 1;
	}
	function save_product(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", sku = '$sku' ";
		$data .= ", category_id = '$category_id' ";
		$data .= ", description = '$description' ";
		$data .= ", price = '$price' ";

		if(empty($id)){
			$save = $this->db->query("INSERT INTO product_list set ".$data);
		}else{
			$save = $this->db->query("UPDATE product_list set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}

	function delete_product(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM product_list where id = ".$id);
		if($delete)
			return 1;
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}

	function save_receiving(){
		extract($_POST);
		$data = " supplier_id = '$supplier_id' ";
		$data .= ", total_amount = '$tamount' ";
		
		if(empty($id)){
			$ref_no = mt_rand(10000000,99999999);
			$chk = $this->db->query("SELECT * FROM receiving_list where ref_no ='$ref_no'")->num_rows;
			while($chk > 0){
				$ref_no = mt_rand(10000000,99999999);
				$chk = $this->db->query("SELECT * FROM receiving_list where ref_no ='$ref_no'")->num_rows;
			}
			$data .= ", ref_no = '$ref_no' ";
			$save = $this->db->query("INSERT INTO receiving_list set ".$data);
			$id =$this->db->insert_id;
			foreach($product_id as $k => $v){
				$data = " form_id = '$id' ";
				$data .= ", product_id = '$product_id[$k]' ";
				$data .= ", qty = '$qty[$k]' ";
				$data .= ", type = '1' ";
				$data .= ", stock_from = 'receiving' ";
				$details = json_encode(array('price'=>$price[$k],'qty'=>$qty[$k]));
				$data .= ", other_details = '$details' ";
				$data .= ", remarks = 'Stock from Receiving-".$ref_no."' ";

				$save2[]= $this->db->query("INSERT INTO inventory set ".$data);
			}
			if(isset($save2)){
				return 1;
			}
		}else{
			$save = $this->db->query("UPDATE receiving_list set ".$data." where id =".$id);
			$ids = implode(",",$inv_id);
			$this->db->query("DELETE FROM inventory where type = 1 and form_id ='$id' and id NOT IN (".$ids.") ");
			foreach($product_id as $k => $v){
				$data = " form_id = '$id' ";
				$data .= ", product_id = '$product_id[$k]' ";
				$data .= ", qty = '$qty[$k]' ";
				$data .= ", type = '1' ";
				$data .= ", stock_from = 'receiving' ";
				$details = json_encode(array('price'=>$price[$k],'qty'=>$qty[$k]));
				$data .= ", other_details = '$details' ";
				$data .= ", remarks = 'Stock from Receiving-".$ref_no."' ";
				$data .= ", status = '$status[$k]' ";
				if(!empty($inv_id[$k])){
									$save2[]= $this->db->query("UPDATE inventory set ".$data." where id=".$inv_id[$k]);
				}else{
					$save2[]= $this->db->query("INSERT INTO inventory set ".$data);
				}
			}
			if(isset($save2)){
				
				return 1;
			}

		}
	}

	function delete_receiving(){
		extract($_POST);
		$del1 = $this->db->query("DELETE FROM receiving_list where id = $id ");
		$del2 = $this->db->query("DELETE FROM inventory where type = 1 and form_id = $id ");
		if($del1 && $del2)
			return 1;
	}
	function save_customer(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", contact = '$contact' ";
		$data .= ", address = '$address' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO customer_list set ".$data);
		}else{
			$save = $this->db->query("UPDATE customer_list set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_customer(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM customer_list where id = ".$id);
		if($delete)
			return 1;
	}

	function chk_prod_availability(){
		extract($_POST);
		$product = $this->db->query("SELECT * FROM product_list where id = ".$id)->fetch_assoc();
		$price = $product['price'];
		
		// Skip all stock checks in supplier receiving page
		if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'supplier_receiving.php') !== false) {
			return json_encode(array('available'=>999999,'price'=>$price));
		}
		
		$inn = $this->db->query("SELECT sum(qty) as inn FROM inventory where type = 1 and status = 'completed' and product_id = ".$id);
		$inn = $inn && $inn->num_rows > 0 ? $inn->fetch_array()['inn'] : 0;
		$out = $this->db->query("SELECT sum(qty) as `out` FROM inventory where type = 2 and status = 'completed' and product_id = ".$id);
		$out = $out && $out->num_rows > 0 ? $out->fetch_array()['out'] : 0;
		$available = $inn - $out;
		
		return json_encode(array('available'=>$available,'price'=>$price));
	}
	function save_sales(){
		extract($_POST);
		$data = " customer_id = '$customer_id' ";
		$data .= ", total_amount = '$tamount' ";
		$data .= ", amount_tendered = '$amount_tendered' ";
		$data .= ", amount_change = '$change' ";
		
		if(empty($id)){
			$ref_no = mt_rand(10000000,99999999);
			$chk = $this->db->query("SELECT * FROM sales_list where ref_no ='$ref_no'")->num_rows;
			while($chk > 0){
				$ref_no = mt_rand(10000000,99999999);
				$chk = $this->db->query("SELECT * FROM sales_list where ref_no ='$ref_no'")->num_rows;
			}
			$data .= ", ref_no = '$ref_no' ";
			$save = $this->db->query("INSERT INTO sales_list set ".$data);
			$id =$this->db->insert_id;
			foreach($product_id as $k => $v){
				$data = " form_id = '$id' ";
				$data .= ", product_id = '$product_id[$k]' ";
				$data .= ", qty = '$qty[$k]' ";
				$data .= ", type = '2' ";
				$data .= ", stock_from = 'Sales' ";
				$data .= ", status = 'completed' ";
				$details = json_encode(array('price'=>$price[$k],'qty'=>$qty[$k]));
				$data .= ", other_details = '$details' ";
				$data .= ", remarks = 'Stock out from Sales-".$ref_no."' ";

				$save2[]= $this->db->query("INSERT INTO inventory set ".$data);
			}
			if(isset($save2)){
				return $id;
			}
		}else{
			$save = $this->db->query("UPDATE sales_list set ".$data." where id=".$id);
			$ids = implode(",",$inv_id);
			$this->db->query("DELETE FROM inventory where type = 2 and form_id ='$id' and id NOT IN (".$ids.") ");
			foreach($product_id as $k => $v){
				$data = " form_id = '$id' ";
				$data .= ", product_id = '$product_id[$k]' ";
				$data .= ", qty = '$qty[$k]' ";
				$data .= ", type = '2' ";
				$data .= ", stock_from = 'Sales' ";
				$data .= ", status = 'completed' ";
				$details = json_encode(array('price'=>$price[$k],'qty'=>$qty[$k]));
				$data .= ", other_details = '$details' ";
				$data .= ", remarks = 'Stock out from Sales-".$ref_no."' ";

				if(!empty($inv_id[$k])){
					$save2[]= $this->db->query("UPDATE inventory set ".$data." where id=".$inv_id[$k]);
				}else{
					$save2[]= $this->db->query("INSERT INTO inventory set ".$data);
				}
			}
			if(isset($save2)){
				return $id;
			}
		}
	}
	function delete_sales(){
		extract($_POST);
		
		// Verify password against admin user
		$admin = $this->db->query("SELECT * FROM users WHERE type = 1 LIMIT 1");
		if($admin->num_rows > 0){
			$admin_data = $admin->fetch_assoc();
			if($password !== $admin_data['password']){
				return 'invalid_password';
			}
		}

		$del1 = $this->db->query("DELETE FROM sales_list where id = $id");
		$del2 = $this->db->query("DELETE FROM inventory where type = 2 and form_id = $id");
		if($del1 && $del2)
			return 1;
		return 0;
	}

}