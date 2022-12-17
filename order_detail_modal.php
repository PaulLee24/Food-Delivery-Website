<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try	{
		if ($_SESSION['Authenticated'] == false)
		{
			echo <<<EOT
			<!DOCTYPE html>
			<html>
				<body>
					<script>
						window.location.replace("index.php");
					</script>
				</body>
			</html>
EOT;
		}
		
		$account = $_SESSION['Account'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$oid = $_POST['oid'];
		
		$data = array();
		
		$stmt = $conn->prepare("select oid, pid, price, quantity, img, imgType, itemName from orderdetail where oid=:oid");
		$stmt->execute(array('oid' => $oid));
		
		if ($stmt->rowCount() > 0)
		{
			while ($row = $stmt->fetch()) {
				$img = '<td><img src="data:'.$row['imgType'].';base64,'.$row['img'].'" width="80" height="80" alt="'.$row['itemName'].'"></td>';
				$name = '<td>'.$row['itemName'].'</td>';
				$price = '<td>'.$row['price'].'</td>';
				$quantity = '<td>'.$row['quantity'].'</td>';
				
				$tempData = array($img, $name, $price, $quantity);
				array_push($data, $tempData);
			}
		}
		
		$stmt = $conn->prepare("select oid, subtotal, deliFee, total_price from orderrecord where oid=:oid");
		$stmt->execute(array('oid' => $oid));
		
		$subtotal = "";
		$delivery_fee = "";
		$total_price = "";
		if ($stmt->rowCount() == 1) {
			$row = $stmt->fetch();
			$subtotal = $row['subtotal'];
			$delivery_fee = $row['deliFee'];
			$total_price = $row['total_price'];
		}
		
		array_unshift($data, array("success", $subtotal, $delivery_fee, $total_price));
		//array_push($data, array($stmt->rowCount(), "success"));
				
		echo json_encode($data);
	}
	
	catch(Exception $e)
	{
		$msg = $e->getMessage();
		if ($msg == 'return login') {
			echo <<<EOT
			<!DOCTYPE html>
				<html>
					<body>
						<script>
							window.location.replace("index.php");
						</script>
					</body>
				</html>
EOT;
		}
		else {
			$data = array(array("fail", $msg));
			echo json_encode($data);
		}
	}
?>