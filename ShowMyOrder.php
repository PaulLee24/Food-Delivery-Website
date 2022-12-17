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
		
		$stmt = $conn->prepare("select account, user_id from users where account=:account");
		$stmt->execute(array('account' => $account));
		
		$user_id = "";
		if ($stmt->rowCount() == 1) {
			$row = $stmt->fetch();
			$user_id = $row['user_id'];
		}
		else {
			throw new Exception();
		}
		
		$statePost = $_POST['state'];
		
		if ($statePost == "All") {
			$stmt = $conn->prepare("select oid, state, start_time, end_time, total_price, user_id, sid from orderrecord where user_id=:user_id");
			$stmt->execute(array('user_id' => $user_id));
		}
		else {
			$stmt = $conn->prepare("select oid, state, start_time, end_time, total_price, user_id, sid from orderrecord where user_id=:user_id and state=:state");
			$stmt->execute(array('user_id' => $user_id, 'state' => $statePost));
		}		
		
		$data = array();
		$myCancelId = array();
		
		if ($stmt->rowCount() > 0)
		{
			while ($row = $stmt->fetch()) {
				$oid = $row['oid'];
				
				$myCancelId[$oid] = false;
				
				$state = $row['state'];
				$start_time = $row['start_time'];
				$end_time = $row['end_time'];
				$total_price = $row['total_price'];
				$sid = $row['sid'];
				
				$stmt2 = $conn->prepare("select shopName, sid from shop where sid=:sid");
				$stmt2->execute(array('sid' => $sid));
				
				$shopName = "";
				if ($stmt2->rowCount() == 1) {
					$row2 = $stmt2->fetch();
					$shopName = $row2['shopName'];
				}
				
				$tempData = array($oid, $state, $start_time, $end_time, $shopName, $total_price);
				array_push($data, $tempData);
			}
		}
		
		$_SESSION['myCancelId'] = $myCancelId;
		
		echo json_encode($data);
	}
	catch(Exception $e)
	{
		echo 'FAILED';
	}
?>