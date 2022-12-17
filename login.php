<?php
	session_start();
	$_SESSION['Authenticated'] = false;
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try	{
		if (preg_match('/[^A-Za-z0-9]/', $_POST['Account']))
		{
			throw new Exception('Please input only English letters and numbers.');
		}
		
		if (!isset($_POST['Account']) || !isset($_POST['password']))
		{
			throw new Exception('fail.');
		}
		if (($_POST['Account'] != "0" && empty($_POST['Account'])) || ($_POST['password'] != "0" && empty($_POST['password'])))
		{
			throw new Exception('Please input user name and password.');
		}		
		
		$account = $_POST['Account'];
		$password = $_POST['password'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("select account, password, salt, status from users where account=:account");
		$stmt->execute(array('account' => $account));
		
		if ($stmt->rowCount() == 1)
		{
			$row = $stmt->fetch();
			if ($row['password'] == hash('sha256', $row['salt'].$password))
			{
				$_SESSION['Authenticated'] = true;
				$_SESSION['Account'] = $account;
				header("Location: nav_owner.php");
				exit();				
			}
			else
			{
				throw new Exception('Login failed.');
			}
		}
		else
		{
			throw new Exception('Login failed.');
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
							window.location.replace("index.php");
						</script>
					</body>
				</html>
EOT;
	}
	
?>