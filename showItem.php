<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try	{
		$Account = $_SESSION['Account'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select account, user_id from users where account=:Account");
		$stmt->execute(array('Account' => $Account));
		$row = $stmt->fetch();
		$user_id = $row['user_id'];
				
		$stmt = $conn->prepare("select sid, user_id from shop where user_id=:user_id");
		$stmt->execute(array('user_id' => $user_id));		
		$row = $stmt->fetch();
		$sid = $row['sid'];
		
		$stmt = $conn->prepare("select itemName, price, quantity, img, imgType, sid, pid from item where sid=:sid");
		$stmt->execute(array('sid' => $sid));
		
		$data = array();
		
		if ($stmt->rowCount() > 0)
		{
			while ($row = $stmt->fetch()) {
				$img = '<td><img src="data:'.$row['imgType'].';base64,'.$row['img'].'" width="80" height="80" alt="'.$row['itemName'].'"></td>';
				$name = $row['itemName'];
				$price = '<td>'.$row['price'].'</td>';
				$quantity = '<td>'.$row['quantity'].'</td>';
				$pid = $row['pid'];
				$tempData = array($img, $name, $price, $quantity, $pid);
				array_push($data, $tempData);
			}
		}
		
		echo json_encode($data);
	}
	catch(Exception $e)
	{
		echo 'FAILED';
	}
?>