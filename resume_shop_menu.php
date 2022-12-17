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
		$orderNum = $_SESSION['orderNum'];
		
		$data = array();
		
		$rmitem = array();
		
		foreach ($orderNum as $pid => $num) {
			$stmt = $conn->prepare("select itemName, price, quantity, img, imgType, sid, pid from item where pid=:pid");
			$stmt->execute(array('pid' => $pid));
			
			if ($stmt->rowCount() == 1)
			{
				$row = $stmt->fetch();
				$img = '<td><img src="data:'.$row['imgType'].';base64,'.$row['img'].'" width="80" height="80" alt="'.$row['itemName'].'"></td>';
				$name = $row['itemName'];
				$price = '<td>'.$row['price'].'</td>';
				$quantity = $row['quantity'];
				
				$tempData = array($img, $name, $price, $quantity, $pid, $num);
				array_push($data, $tempData);
			}
			else {
				array_push($rmitem, $pid);
			}
		}
		
		$arrlength = count($rmitem);

		for($x = 0; $x < $arrlength; $x++) {
			$rmid = $rmitem[$x];
			unset($orderNum[$rmid]);
		}		
		$_SESSION['orderNum'] = $orderNum;
		
		echo json_encode($data);
	}
	catch(Exception $e)
	{
		echo 'FAILED';
	}
?>