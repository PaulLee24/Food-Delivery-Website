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
		
		$_SESSION['totalPrice'] = -1;
		$_SESSION['deliveryDistance'] = -1.0;
		
		$account = $_SESSION['Account'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select account, latitude, longitude from users where account=:account");
		$stmt->execute(array('account' => $account));
		
		$row = $stmt->fetch();
		$user_point = 'POINT('.$row['longitude'].','.$row['latitude'].')';
		
		
		$orderNum = $_SESSION['orderNum'];
		$deliType = $_POST['deliType'];
		
		$selectItemNum = 0;
		$sid = "";
		$subtotal = 0;
		
		$data = array();
		
		$orderPrice = array();
		
		$rmitem = array();
		
		foreach ($orderNum as $pid => $num) {
			$stmt = $conn->prepare("select itemName, price, img, imgType, sid, pid from item where pid=:pid");
			$stmt->execute(array('pid' => $pid));
			
			if ($stmt->rowCount() == 1)
			{
				if ($num > 0) {
					$selectItemNum = $selectItemNum + 1;
					$row = $stmt->fetch();
					$sid = $row['sid'];
					$img = '<td><img src="data:'.$row['imgType'].';base64,'.$row['img'].'" width="80" height="80" alt="'.$row['itemName'].'"></td>';
					$name = $row['itemName'];
					$price = '<td>'.$row['price'].'</td>';
					$orderPrice[$pid] = intval($row['price']);
					$priceInt = intval($row['price']);
					$subtotal = $subtotal + $priceInt * $num;
					$tempData = array($img, $name, $price, $num, $pid);
					array_push($data, $tempData);
				}
			}
			else {
				array_push($rmitem, $pid);
			}
		}
		
		$arrlength = count($rmitem);
		
		$return_state = "success";
		if ($arrlength > 0) {
			$return_state = "item_removed";
		}

		for($x = 0; $x < $arrlength; $x++) {
			$rmid = $rmitem[$x];
			unset($orderNum[$rmid]);
		}
		$_SESSION['orderNum'] = $orderNum;
		$_SESSION['orderPrice'] = $orderPrice;
		
		if ($selectItemNum == 0) {
			throw new Exception('Please select at least one item before you calculate the price.');
		}
		
		$delivery_fee = 0;
		$distance = 0;
		
		if ($deliType == "Delivery") {
			$stmt = $conn->prepare("select distinct ST_Distance_Sphere(".$user_point.", slocation) as distance, sid from shop where sid=:sid");
			$stmt->execute(array('sid' => $sid));
			
			$row = $stmt->fetch();
			$distance = floatval($row['distance']);
			$delivery_fee = round($distance / 100);		
			$delivery_fee = max($delivery_fee, 10);
		}
		
		$total_price = $subtotal + $delivery_fee;
		
		$_SESSION['subtotal'] = $subtotal;
		$_SESSION['deliFee'] = $delivery_fee;
		$_SESSION['totalPrice'] = $total_price;
		$_SESSION['deliveryDistance'] = $distance;
		
		array_unshift($data, array($return_state, $subtotal, $delivery_fee, $total_price));
				
		echo json_encode($data);
		
		/*$data = array(array("success", $subtotal));
		echo json_encode($data);*/
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