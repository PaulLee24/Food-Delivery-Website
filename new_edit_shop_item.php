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
			throw new Exception('return login');
		}
		
		if (!isset($_POST['price']) || !isset($_POST['quantity']))
		{
			throw new Exception('return login');
		}
		if (($_POST['price'] != "0" && empty($_POST['price'])) || ($_POST['quantity'] != "0" && empty($_POST['quantity'])))
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
		$pid = $_SESSION['EditItemId'];
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select pid from item where pid=:pid");
		$stmt->execute(array('pid' => $pid));
		
		if ($stmt->rowCount() == 1)
		{
			$stmt = $conn->prepare("update item set price=:price, quantity=:quantity where pid=:pid");
			$stmt->execute(array('price' => $price, 'quantity' => $quantity, 'pid' => $pid));			
		}
		echo <<<EOT
			<!DOCTYPE html>
				<html>
					<body>
						<script>
							window.location.replace("nav_owner.php");
						</script>
					</body>
				</html>
EOT;		
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
			echo <<<EOT
			<!DOCTYPE html>
				<html>
					<body>
						<script>
							alert("$msg");
							window.location.replace("edit_shop_item_form.php");
						</script>
					</body>
				</html>
EOT;
		}
	}
?>