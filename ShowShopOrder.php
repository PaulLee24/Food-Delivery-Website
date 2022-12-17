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
		
		$stmt = $conn->prepare("select sid, user_id, shopName from shop where user_id=:user_id");
		$stmt->execute(array('user_id' => $user_id));
		$sid = "";
		$shopName = "";
		if ($stmt->rowCount() == 1) {
			$row = $stmt->fetch();
			$sid = $row['sid'];
			$shopName = $row['shopName'];
		}
		else {
			throw new Exception();
		}		
		
		$statePost = $_POST['state'];
		
		if ($statePost == "All") {
			$stmt = $conn->prepare("select oid, state, start_time, end_time, total_price, sid from orderrecord where sid=:sid");
			$stmt->execute(array('sid' => $sid));
		}
		else {
			$stmt = $conn->prepare("select oid, state, start_time, end_time, total_price, sid from orderrecord where sid=:sid and state=:state");
			$stmt->execute(array('sid' => $sid, 'state' => $statePost));
		}		
		
		$data = array();
		$shop_select_id = array();
		
		if ($stmt->rowCount() > 0)
		{
			while ($row = $stmt->fetch()) {
				$oid = $row['oid'];
				
				$shop_select_id[$oid] = false;
				
				$state = $row['state'];
				$start_time = $row['start_time'];
				$end_time = $row['end_time'];
				$total_price = $row['total_price'];
				
				$tempData = array($oid, $state, $start_time, $end_time, $shopName, $total_price);
				array_push($data, $tempData);
			}
		}
		
		$_SESSION['shop_select_id'] = $shop_select_id;
		
		echo json_encode($data);
	}
	catch(Exception $e)
	{
		echo 'FAILED';
	}
?>