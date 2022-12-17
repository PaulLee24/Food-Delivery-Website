<?php
	session_start();
	
	try
	{
		if ($_SESSION['Authenticated'] == false)
		{
			throw new Exception('return login');
		}
		else
		{
			echo <<<EOT
			<!DOCTYPE html>
				<html>
					<body>
						<form action="new_edit_shop_item.php" method="post">
							<label for="ex71">price</label>
							<input name="price" id="ex71" type="text"><br>
							<label for="ex41">quantity</label>
							<input name="quantity" id="ex41" type="text"><br>
							<input type="submit" value="Edit">
						</form>
						<form action="nav_owner.php">
							<input type="submit" value="Back">
						</form>
					</body>
				</html>
EOT;
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
	}
?>

