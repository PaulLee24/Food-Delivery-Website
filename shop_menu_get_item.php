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
		
		$Account = $_SESSION['Account'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//$sid = $_SESSION['OpenShopMenuId'];
		$sid = $_REQUEST['sid'];		
		
		$stmt = $conn->prepare("select itemName, price, quantity, img, imgType, sid, pid from item where sid=:sid");
		$stmt->execute(array('sid' => $sid));
		
		$data = array();		
		$orderNum = array();		
		
		if ($stmt->rowCount() > 0)
		{
			while ($row = $stmt->fetch()) {
				$img = '<td><img src="data:'.$row['imgType'].';base64,'.$row['img'].'" width="80" height="80" alt="'.$row['itemName'].'"></td>';
				$name = $row['itemName'];
				$price = '<td>'.$row['price'].'</td>';
				$quantity = $row['quantity'];
				$pid = $row['pid'];
				$orderNum[$pid] = 0;
				$tempData = array($img, $name, $price, $quantity, $pid);
				array_push($data, $tempData);
			}
		}
		
		$_SESSION['orderNum'] = $orderNum;
		
		echo json_encode($data);
	}
	catch(Exception $e)
	{
		echo 'FAILED';
	}
?>