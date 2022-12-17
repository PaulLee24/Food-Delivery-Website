<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	$out_of_stock = array();
	
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
		
		$stmt = $conn->prepare("select account, user_id, latitude, longitude, money from users where account=:account");
		$stmt->execute(array('account' => $account));
		
		$row = $stmt->fetch();
		$user_id = $row['user_id'];
		$user_point = 'POINT('.$row['longitude'].','.$row['latitude'].')';
				
		
		$orderNum = $_SESSION['orderNum'];
		$deliType = $_POST['deliType'];
		$subtotal = intval($_SESSION['subtotal']);
		$deliFee = intval($_SESSION['deliFee']);
		$totalPrice = intval($_SESSION['totalPrice']);
		$distance = $_SESSION['deliveryDistance'];
		
		$sid = "";
		
		$conn->beginTransaction();
		
		$not_available = array();
		
		foreach ($orderNum as $pid => $num) {
			if ($num > 0) {
				$stmt = $conn->prepare("select sid, pid, quantity, itemName from item where pid=:pid");
				$stmt->execute(array('pid' => $pid));
				
				if ($stmt->rowCount() == 1)
				{
					$row = $stmt->fetch();
					$sid = $row['sid'];	
					$quantity = intval($row['quantity']);
					
					if ($quantity >= $num) {
						$quantity = $quantity - $num;
						$stmt = $conn->prepare("update item set quantity=:quantity where pid=:pid");
						$stmt->execute(array('quantity' => $quantity, 'pid' => $pid));
					}
					else {
						array_push($out_of_stock, $pid);
					}
				}
				elseif ($stmt->rowCount() < 1) {
					throw new Exception('Some meals you ordered are no longer available.');
				}
			}
		}
		
		if (!empty($out_of_stock)) {
			throw new Exception('The meals you ordered are out of stock.');
		}
		
		$stmt = $conn->prepare("select sid, user_id, shopName from shop where sid=:sid");
		$stmt->execute(array('sid' => $sid));
		
		$shopName = "";
		
		if ($stmt->rowCount() == 1) {
			$row = $stmt->fetch();
			$owner_id = $row['user_id'];
			$shopName = $row['shopName'];
			
			$stmt = $conn->prepare("insert into traderecord (type, amount, user_id, trading_partner) values (:type, :amount, :user_id, :trading_partner)");
			$stmt->execute(array('type' => "income", 'amount' => $totalPrice, 'user_id' => $owner_id, 'trading_partner' => $account));
			
			$stmt = $conn->prepare("select user_id, money from users where user_id=:user_id");
			$stmt->execute(array('user_id' => $owner_id));
			
			if ($stmt->rowCount() == 1) {
				$row = $stmt->fetch();
				$owner_money = intval($row['money']);
				$owner_money = $owner_money + $totalPrice;
				
				$stmt = $conn->prepare("update users set money=:money where user_id=:user_id");
				$stmt->execute(array('money' => $owner_money, 'user_id' => $owner_id));
			}
		}
		
		$stmt = $conn->prepare("select account, money from users where account=:account");
		$stmt->execute(array('account' => $account));
		
		$row = $stmt->fetch();
		$user_money = intval($row['money']);
		
		if ($user_money >= $totalPrice) {
			$user_money = $user_money - $totalPrice;
			
			$stmt = $conn->prepare("update users set money=:money where account=:account");
			$stmt->execute(array('money' => $user_money, 'account' => $account));
			
			$stmt = $conn->prepare("insert into traderecord (type, amount, user_id, trading_partner) values (:type, :amount, :user_id, :trading_partner)");
			$stmt->execute(array('type' => "payment", 'amount' => $totalPrice, 'user_id' => $user_id, 'trading_partner' => $shopName));
		}
		else {
			throw new Exception('Your account balance is insufficient.');
		}		
		
		$stmt = $conn->prepare("insert into orderrecord (state, distance, subtotal, deliFee, total_price, delivery_type, user_id, sid) values (:state, :distance, :subtotal, :deliFee, :total_price, :delivery_type, :user_id, :sid)");
		$stmt->execute(array('state' => 'In Progress', 'distance' => $distance, 'subtotal' => $subtotal, 'deliFee' => $deliFee, 'total_price' => $totalPrice, 'delivery_type' => $deliType, 'user_id' => $user_id, 'sid' => $sid));
		
		$oid = $conn->lastInsertId();
		
		$orderPrice = $_SESSION['orderPrice'];
		
		foreach ($orderNum as $pid => $num) {
			if ($num > 0) {
				$stmt = $conn->prepare("select price, img, imgType, pid, itemName from item where pid=:pid");
				$stmt->execute(array('pid' => $pid));
				
				if ($stmt->rowCount() == 1)
				{
					$row = $stmt->fetch();
					$price = $orderPrice[$pid];
					$img = $row['img'];
					$imgType = $row['imgType'];
					$itemName = $row['itemName'];
					$stmt = $conn->prepare("insert into orderdetail (oid, pid, price, quantity, img, imgType, itemName) values (:oid, :pid, :price, :quantity, :img, :imgType, :itemName)");
					$stmt->execute(array('oid' => $oid, 'pid' => $pid, 'price' => $price, 'quantity' => $num, 'img' => $img, 'imgType' => $imgType, 'itemName' => $itemName));
				}
			}
		}
		
		$data = array("success", "Order Succesfully !");
		
		$conn->commit();
		
		echo json_encode($data);
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
			$data = array("fail", $msg, $out_of_stock);
			echo json_encode($data);
		}
	}
?>