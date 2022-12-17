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
		
		$shop_select_id = $_SESSION['shop_select_id'];
		
		$conn->beginTransaction();
		
		foreach($shop_select_id as $oid => $tf) {
			if ($tf == true) {
				$stmt = $conn->prepare("select oid, state, total_price, user_id, sid from orderrecord where oid=:oid");
				$stmt->execute(array('oid' => $oid));
				
				if ($stmt->rowCount() == 1) {
					$row = $stmt->fetch();
					
					$state = $row['state'];
					$total_price = intval($row['total_price']);
					$buyer_id = $row['user_id'];
					$sid = $row['sid'];
					
					if ($state == "In Progress") {
						$stmt = $conn->prepare("update orderrecord set state=:state, end_time=now() where oid=:oid");
						$stmt->execute(array('state' => "Finish", 'oid' => $oid));				
					}
					else {
						throw new Exception('fail');
					}
				}
			}
		}
		
		
		$conn->commit();		
		echo "success";
	}
	
	catch(Exception $e)
	{
		$conn->rollBack();
		
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
			echo $msg;
		}
	}
?>