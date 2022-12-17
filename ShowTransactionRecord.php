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
		
		$actionPost = $_POST['action'];
		
		if ($actionPost == "all") {
			$stmt = $conn->prepare("select tid, type, amount, time, user_id, trading_partner from traderecord where user_id=:user_id");
			$stmt->execute(array('user_id' => $user_id));
		}
		else {
			$stmt = $conn->prepare("select tid, type, amount, time, user_id, trading_partner from traderecord where user_id=:user_id and type=:type");
			$stmt->execute(array('user_id' => $user_id, 'type' => $actionPost));
		}		
		
		$data = array();	
		
		if ($stmt->rowCount() > 0)
		{
			while ($row = $stmt->fetch()) {
				$tid = $row['tid'];
				$type = $row['type'];
				$time = $row['time'];
				$trading_partner = $row['trading_partner'];
				$amount = $row['amount'];
				
				$tempData = array($tid, $type, $time, $trading_partner, $amount);
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