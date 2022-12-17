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
		
		$stmt = $conn->prepare("select user_id, account from users where account=:account");		
		$stmt->execute(array('account' => $account));
		
		$row = $stmt->fetch();
		$user_id = $row['user_id'];
		
		$stmt = $conn->prepare("select shopName, category, slatitude, slongitude from shop where user_id=:user_id");
		$stmt->execute(array('user_id' => $user_id));
		
		if ($stmt->rowCount() == 1)
		{
			$row = $stmt->fetch();
			$data = array($row['shopName'], $row['category'], $row['slatitude'], $row['slongitude']);
			echo json_encode($data);
		}
		else
		{
			throw new Exception();
		}
		
		/*if ($stmt->rowCount() == 1)
		{
			$row = $stmt->fetch();
			$owner_id = $row['user_id'];
			$stmt2 = $conn->prepare("select name, category, latitude, longitude from shop where owner_id=:owner_id");
			$stmt2->execute(array('owner_id' => $owner_id));
			
			if ($stmt2->rowCount() == 1)
			{
				$row2 = $stmt2->fetch();
				$data = array($row2['name'], $row2['category'], $row2['latitude'], $row2['longitude']);
				echo json_encode($data);
			}			
		}*/
	}
	catch(Exception $e)
	{
		echo 'FAILED';
	}
?>