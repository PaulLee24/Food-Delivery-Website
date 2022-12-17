<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try
	{
		if (!isset($_POST['name']) || !isset($_POST['price']) || !isset($_POST['quantity']))
		{
			throw new Exception('return login');
		}
		if (empty($_POST['name']))
		{
			throw new Exception('Fail');
		}
		if (empty(empty($_POST['price']) || empty($_POST['quantity']))
		{
			throw new Exception('Please fill in every column.');
		}
		
		if (!is_numeric($_POST['price'])) {
			throw new Exception('Wrong input format.');
		}
		if (!is_numeric($_POST['quantity'])) {
			throw new Exception('Wrong input format.');
		}
		$price  = floatval($_POST['price']);
		$quantity = floatval($_POST['quantity']);
		if ($price < 0 || floor($price) != $price) {
			throw new Exception('Wrong input format.');
		}
		if ($quantity < 0 || floor($quantity) != $quantity) {
			throw new Exception('Wrong input format.');
		}
		
		$Account = $_SESSION['Account'];
		$name = $_POST['name'];
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select name from item where name=:name");
		$stmt->execute(array('name' => $name));
		
		if ($stmt->rowCount() == 1)
		{
			$stmt = $conn->prepare("update item set price=:price, quantity=:quantity where name=:name");
			$stmt->execute(array('price' => $price, 'quantity' => $quantity, 'name' => $name));
			$data = array($price, $quantity);
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