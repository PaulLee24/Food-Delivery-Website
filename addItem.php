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
		
		if (!isset($_POST['name']) || !isset($_POST['price']) || !isset($_POST['quantity']))
		{
			throw new Exception('return login');
		}
		if (empty($_POST['name']))
		{
			throw new Exception('Meal name field required !!');
		}
		if ($_POST['price'] != "0") {
			if (empty($_POST['price']))
			{
				throw new Exception('Price field required !!');
			}
		}
		if ($_POST['quantity'] != "0") {
			if (empty($_POST['quantity']))
			{
				throw new Exception('Quantity field required !!');
			}
		}
		if (empty($_FILES["imgFile"]["tmp_name"]))
		{
			throw new Exception('Img field required !!');
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
		$file = fopen($_FILES["imgFile"]["tmp_name"], "rb");
		$fileContents = fread($file, filesize($_FILES["imgFile"]["tmp_name"]));
		fclose($file);
		$fileContents = base64_encode($fileContents);
		$imgType= $_FILES["imgFile"]["type"];
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select account, user_id from users where account=:Account");
		$stmt->execute(array('Account' => $Account));		
		$row = $stmt->fetch();
		$user_id = $row['user_id'];
				
		$stmt = $conn->prepare("select sid, user_id from shop where user_id=:user_id");
		$stmt->execute(array('user_id' => $user_id));		
		$row = $stmt->fetch();
		$sid = $row['sid'];
		
		$stmt = $conn->prepare("select itemName from item where itemName=:name and sid=:sid");
		$stmt->execute(array('name' => $name, 'sid' => $sid));
		
		if ($stmt->rowCount() == 0)
		{
			$stmt = $conn->prepare("insert into item (itemName, price, quantity, img, imgType, sid) values (:name, :price, :quantity, '$fileContents', :imgType, :sid)");
			$stmt->execute(array('name' => $name, 'price' => $price, 'quantity' => $quantity, 'imgType' => $imgType, 'sid' => $sid));
			echo "Add item success.";
		}
		else
		{
			throw new Exception('The meal is already exist in your shop.');
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
			echo $msg;
		}
	}
?>