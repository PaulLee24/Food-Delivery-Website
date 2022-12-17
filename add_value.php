<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try
	{
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
		
		if (!isset($_POST['money']))
		{
			throw new Exception('return login');
		}
		if (empty($_POST['money']))
		{
			throw new Exception('Please fill in the add value.');
		}
		
		if (!is_numeric($_POST['money'])) {
			throw new Exception('Please fill in a positive integer.');
		}
		$money = floatval($_POST['money']);
		if ($money <= 0 || floor($money) != $money) {
			throw new Exception('Please fill in a positive integer.');
		}
		
		$Account = $_SESSION['Account'];
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select account, user_id, money from users where account=:Account");
		$stmt->execute(array('Account' => $Account));
		
		if ($stmt->rowCount() == 1)
		{
			$row = $stmt->fetch();
			$user_id = $row['user_id'];
			$user_money = intval($row['money']);
			
			$stmt = $conn->prepare("insert into traderecord (type, amount, user_id, trading_partner) values (:type, :amount, :user_id, :trading_partner)");
			$stmt->execute(array('type' => "add value", 'amount' => $money, 'user_id' => $user_id, 'trading_partner' => $Account));
			
			$money = $money + $user_money;
			
			$stmt = $conn->prepare("update users set money=:money where account=:Account");
			$stmt->execute(array('money' => $money, 'Account' => $Account));
			
			$data = array("success", $money);
			echo json_encode($data);
		}
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
			$data = array("fail", $msg);
			echo json_encode($data);
		}
	}
?>