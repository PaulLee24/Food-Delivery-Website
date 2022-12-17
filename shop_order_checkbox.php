<?php
	session_start();
	
	try
	{
		if ($_SESSION['Authenticated'] == false)
		{
			throw new Exception('return login');
		}
		if (!isset($_POST['oid']))
		{
			throw new Exception('return login');
		}		
		
		if (empty($_POST['oid']))
		{
			throw new Exception('Fail');
		}
		
		$oid = $_POST['oid'];
		
		$shop_select_id = $_SESSION['shop_select_id'];
		
		$shop_select_id[$oid] = !($shop_select_id[$oid]);
		
		$_SESSION['shop_select_id'] = $shop_select_id;
		
		//echo "success";
		
		echo $shop_select_id[$oid];
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