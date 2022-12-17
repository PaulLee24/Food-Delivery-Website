<?php
	session_start();
	$_SESSION['Authenticated'] = false;
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try	{
		if (!isset($_POST['name']) || !isset($_POST['phonenumber']) || !isset($_POST['Account']) || !isset($_POST['password']) || !isset($_POST['latitude']) || !isset($_POST['longitude']))
		{
			header("Location: index.php");
			exit();
		}
		if (($_POST['name'] != "0" && empty($_POST['name'])) || ($_POST['phonenumber'] != "0" && empty($_POST['phonenumber'])) || ($_POST['Account'] != "0" && empty($_POST['Account'])) || ($_POST['password'] != "0" && empty($_POST['password'])) || ($_POST['latitude'] != "0" && empty($_POST['latitude'])) || ($_POST['longitude'] != "0" && empty($_POST['longitude'])))
		{
			throw new Exception('Please fill in every column.');
		}
		
		if (preg_match('/[^A-Za-z\s]/', $_POST['name']))
		{
			throw new Exception('Wrong input format.(name)');
		}
		if (preg_match('/[^0-9]/', $_POST['phonenumber']))
		{
			throw new Exception('Wrong input format.(phonenumber)');
		}
		if (strlen($_POST['phonenumber']) != 10)
		{
			throw new Exception('Wrong input format.(phonenumber)');
		}
		if (preg_match('/[^A-Za-z0-9]/', $_POST['Account']))
		{
			throw new Exception('Wrong input format.(Account)');
		}
		if (preg_match('/[^A-Za-z0-9]/', $_POST['password']))
		{
			throw new Exception('Wrong input format.(password)');
		}
		if ($_POST['password'] != $_POST['re-password'])
		{
			throw new Exception('Re-type password != password');
		}		
		if (!is_numeric($_POST['latitude'])) {
			throw new Exception('Wrong input format.(latitude)');
		}
		if (!is_numeric($_POST['longitude'])) {
			throw new Exception('Wrong input format.(longitude)');
		}
		$latitude  = floatval($_POST['latitude']);
		$longitude = floatval($_POST['longitude']);
		if ($latitude < (-90) || $latitude > 90) {
			throw new Exception('Wrong input format.(latitude)');
		}
		if ($longitude < (-180) || $longitude > 180) {
			throw new Exception('Wrong input format.(longitude)');
		}
		
		
		$name = $_POST['name'];
		$phonenumber = $_POST['phonenumber'];
		$Account = $_POST['Account'];
		$password = $_POST['password'];
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("select account from users where account=:Account");
		$stmt->execute(array('Account' => $Account));
		
		$point = 'POINT('.$_POST['longitude']." ".$_POST['latitude'].')';
		$status = "user";
		$money = 0;
		
		if ($stmt->rowCount() == 0)
		{
			$salt = strval(rand(1000, 9999));
			$hashvalue = hash('sha256', $salt.$password);
			$stmt = $conn->prepare("insert into users (account, password, salt, name, phone, location, latitude, longitude, status, money) values (:account, :password, :salt, :name, :phone, ST_GeomFromText(:point), :latitude, :longitude, :status, :money)");
			$stmt->execute(array('account' => $Account, 'password' => $hashvalue, 'salt' => $salt, 'name' => $name, 'phone' => $phonenumber, 'point' => $point, 'latitude' => $latitude, 'longitude' => $longitude, 'status' => $status, 'money' => $money));
			$_SESSION['Authenticated'] = true;
			$_SESSION['Account'] = $Account;
			echo <<<EOT
				<!DOCTYPE html>
					<html>
						<body>
							<script>
								alert("Register success !!");
								window.location.replace("nav_owner.php");
							</script>
						</body>
					</html>
EOT;
			exit();
		}
		else
		{
			throw new Exception('Account has been registered.');
		}
	}
	
	catch(Exception $e)
	{
		$msg = $e->getMessage();
		session_unset();
		session_destroy();
		echo <<<EOT
			<!DOCTYPE html>
				<html>
					<body>
						<script>
							alert("$msg");
							window.location.replace("sign-up.php");
						</script>
					</body>
				</html>
EOT;
	}
	
?>