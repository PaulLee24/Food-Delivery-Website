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
		
		$myCancelId = $_SESSION['myCancelId'];
		
		$conn->beginTransaction();
		
		foreach($myCancelId as $oid => $tf) {
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
						$stmt->execute(array('state' => "Cancel", 'oid' => $oid));
						
						$stmt = $conn->prepare("select sid, user_id, shopName from shop where sid=:sid");
						$stmt->execute(array('sid' => $sid));
						$shopName = "";
						if ($stmt->rowCount() == 1) {
							$row = $stmt->fetch();
							$owner_id = $row['user_id'];
							$shopName = $row['shopName'];
							
							$stmt = $conn->prepare("insert into traderecord (type, amount, user_id, trading_partner) values (:type, :amount, :user_id, :trading_partner)");
							$stmt->execute(array('type' => "payment", 'amount' => $total_price, 'user_id' => $owner_id, 'trading_partner' => $account));
							
							$stmt = $conn->prepare("select user_id, money from users where user_id=:user_id");
							$stmt->execute(array('user_id' => $owner_id));
							
							if ($stmt->rowCount() == 1) {
								$row = $stmt->fetch();
								$owner_money = intval($row['money']);
								$owner_money = $owner_money - $total_price;
								
								$stmt = $conn->prepare("update users set money=:money where user_id=:user_id");
								$stmt->execute(array('money' => $owner_money, 'user_id' => $owner_id));
							}
						}
						
						$stmt = $conn->prepare("select user_id, money from users where user_id=:user_id");
						$stmt->execute(array('user_id' => $buyer_id));				
						if ($stmt->rowCount() == 1) {
							$row = $stmt->fetch();
							$buyer_money = intval($row['money']);
							$buyer_money = $buyer_money + $total_price;
							
							$stmt = $conn->prepare("insert into traderecord (type, amount, user_id, trading_partner) values (:type, :amount, :user_id, :trading_partner)");
							$stmt->execute(array('type' => "income", 'amount' => $total_price, 'user_id' => $buyer_id, 'trading_partner' => $shopName));
							
							$stmt = $conn->prepare("update users set money=:money where user_id=:user_id");
							$stmt->execute(array('money' => $buyer_money, 'user_id' => $buyer_id));
						}
						
						$stmt = $conn->prepare("select oid, pid, quantity from orderdetail where oid=:oid");
						$stmt->execute(array('oid' => $oid));				
						if ($stmt->rowCount() > 0)
						{
							while ($row = $stmt->fetch()) {
								$pid = $row['pid'];
								$add_quantity = intval($row['quantity']);
								
								$stmt2 = $conn->prepare("select pid, quantity from item where pid=:pid");
								$stmt2->execute(array('pid' => $pid));
								if ($stmt2->rowCount() == 1) {
									$row2 = $stmt2->fetch();
									$cur_quantity = intval($row2['quantity']);
									$cur_quantity = $cur_quantity + $add_quantity;
									$stmt2 = $conn->prepare("update item set quantity=:quantity where pid=:pid");
									$stmt2->execute(array('quantity' => $cur_quantity, 'pid' => $pid));
								}
							}
						}
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