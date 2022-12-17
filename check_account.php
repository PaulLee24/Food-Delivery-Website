<?php
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try	{
		if (!isset($_REQUEST['uname']) || ($_REQUEST['uname'] != "0" && empty($_REQUEST['uname'])))
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
		
		$uname = $_REQUEST['uname'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("select account from users where account=:account");
		$stmt->execute(array('account' => $uname));
		
		if ($stmt->rowCount() == 0)
		{
			echo 'YES';
		}
		else
		{
			echo 'NO';
		}
	}
	catch(Exception $e)
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
?>