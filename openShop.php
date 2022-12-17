<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try
	{
		if (!isset($_POST['name']) || !isset($_POST['category']) || !isset($_POST['latitude']) || !isset($_POST['longitude']))
		{
			throw new Exception('return login');
		}
		if (empty($_POST['name']))
		{
			throw new Exception('Shop name field required !!');
		}
		if (empty($_POST['category']))
		{
			throw new Exception('Shop category field required !!');
		}
		if (empty($_POST['latitude']))
		{
			throw new Exception('Latitude field required !!');
		}
		if (empty($_POST['longitude']))
		{
			throw new Exception('Longitude field required !!');
		}
		
		
		if (!is_numeric($_POST['latitude'])) {
			throw new Exception('Wrong input format.');
		}
		if (!is_numeric($_POST['longitude'])) {
			throw new Exception('Wrong input format.');
		}
		$latitude  = floatval($_POST['latitude']);
		$longitude = floatval($_POST['longitude']);
		if ($latitude < (-90) || $latitude > 90) {
			throw new Exception('Wrong input format.');
		}
		if ($longitude < (-180) || $longitude > 180) {
			throw new Exception('Wrong input format.');
		}
		
		$Account = $_SESSION['Account'];
		$name = $_POST['name'];
		$category = $_POST['category'];
		$point = 'POINT('.$_POST['longitude']." ".$_POST['latitude'].')';
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select account, user_id from users where account=:Account");
		$stmt->execute(array('Account' => $Account));
		
		$row = $stmt->fetch();
		$user_id = $row['user_id'];
				
		$stmt = $conn->prepare("select shopName from shop where shopName=:name");
		$stmt->execute(array('name' => $name));
		
		if ($stmt->rowCount() == 0)
		{
			$stmt = $conn->prepare("insert into shop (shopName, category, slocation, slatitude, slongitude, user_id) values (:name, :category, ST_GeomFromText(:point), :latitude, :longitude, :user_id)");
			$stmt->execute(array('name' => $name, 'category' => $category, 'point' => $point, 'latitude' => $latitude, 'longitude' => $longitude, 'user_id' => $user_id));
			
			$stmt = $conn->prepare("update users set status=:status where account=:Account");
			$stmt->execute(array('status' => 'owner', 'Account' => $Account));
			
			echo "Register success !!";
		}
		else
		{
			throw new Exception('Shop name has been registered.');
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