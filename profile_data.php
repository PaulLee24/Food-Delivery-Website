<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try	{
		$account = $_SESSION['Account'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//$stmt = $conn->prepare("select account, status, phone, ST_AsText(location) as location from users where account=:account");		
		$stmt = $conn->prepare("select account, status, phone, latitude, longitude, money from users where account=:account");
		//$stmt = $conn->prepare("select account, status, phone from users where account=:account");
		$stmt->execute(array('account' => $account));
		
		if ($stmt->rowCount() == 1)
		{
			$row = $stmt->fetch();
			$data = array($row['account'], $row['status'], $row['phone'], $row['latitude'], $row['longitude'], $row['money']);
			echo json_encode($data);
		}
	}
	catch(Exception $e)
	{
		echo 'FAILED';
	}
?>