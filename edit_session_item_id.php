<?php
	session_start();
	
	try
	{
		if ($_SESSION['Authenticated'] == false)
		{
			throw new Exception('return login');
		}
		if (!isset($_POST['pid']))
		{
			throw new Exception('return login');
		}		
		
		if (empty($_POST['pid']))
		{
			throw new Exception('Fail');
		}
		
		$_SESSION['EditItemId'] = $_POST['pid'];
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