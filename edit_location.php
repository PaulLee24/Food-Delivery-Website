<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try
	{
		if (!isset($_POST['latitude']) || !isset($_POST['longitude']))
		{
			throw new Exception('return login');
		}
		if (empty($_POST['latitude']) || empty($_POST['longitude']))
		{
			throw new Exception('Fail');
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
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("select account from users where account=:Account");
		$stmt->execute(array('Account' => $Account));
		
		$point = 'POINT('.$_POST['longitude']." ".$_POST['latitude'].')';
		
		if ($stmt->rowCount() == 1)
		{
			$stmt = $conn->prepare("update users set location=ST_GeomFromText(:point), latitude=:latitude, longitude=:longitude where account=:Account");
			$stmt->execute(array('point' => $point, 'latitude' => $latitude, 'longitude' => $longitude, 'Account' => $Account));
			$data = array($latitude, $longitude);
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
			$data = array("fail", "fail");
			echo json_encode($data);
		}
	}
?>