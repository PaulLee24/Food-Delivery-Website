<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try
	{
		if (!isset($_POST['pid']))
		{
			throw new Exception('return login');
		}
		if (empty($_POST['pid']))
		{
			throw new Exception('Fail');
		}
		
		$Account = $_SESSION['Account'];
		$pid = $_POST['pid'];
		
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("delete from item where pid=:pid");
		$stmt->execute(array('pid' => $pid));
		
		echo "success";
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
			echo "fail";
		}
	}
?>