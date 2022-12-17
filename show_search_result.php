<?php
	session_start();
	
	$dbservername = 'localhost';
	$dbname = 'hw2';
	$dbusername = 'examdb';
	$dbpassword = 'examdb';
	
	try	{
		if ($_SESSION['Authenticated'] == false)
		{
			throw new Exception('return login');
		}
		
		if (empty($_POST['shop']))
		{
			$shop = "%_%";
		}
		else {
			$shop = "%".$_POST['shop']."%";
		}
		
		$distance = $_POST['distance'];
		
		if ($_POST['distance'] == "all") {
			$MINdistance = 0;
			$MAXdistance = PHP_INT_MAX;
		}
		elseif ($_POST['distance'] == "near") {
			$MINdistance = 0;
			$MAXdistance = 2000;
		}
		elseif ($_POST['distance'] == "medium") {
			$MINdistance = 2000;
			$MAXdistance = 5000;
		}
		else {
			$MINdistance = 5000;
			$MAXdistance = PHP_INT_MAX;
		}		
		
		if (empty($_POST['priceLow']))
		{
			$priceLow = 0;
		}
		else {
			if (!is_numeric($_POST['priceLow'])) {
				throw new Exception('Wrong input format.');
			}
			$priceLow = intval($_POST['priceLow']);
		}
		
		if (empty($_POST['priceHigh']))
		{
			$priceHigh = PHP_INT_MAX;
		}
		else {
			if (!is_numeric($_POST['priceHigh'])) {
				throw new Exception('Wrong input format.');
			}
			$priceHigh = intval($_POST['priceHigh']);
		}
		
		if (empty($_POST['meal']))
		{
			$meal = "%_%";
		}
		else {
			$meal = "%".$_POST['meal']."%";
		}
		
		if (empty($_POST['category']))
		{
			$category = "%_%";
		}
		else {
			$category = "%".$_POST['category']."%";
		}
		
		if (isset($_POST['sortCond'])) {
			if ($_POST['sortCond'] == 'old') {
				$sortCond = $_SESSION['sortCond'];
			}
			else{
				$sortCond = $_POST['sortCond'];
				$_SESSION['sortCond'] = $_POST['sortCond'];
			}
		}
		else {
			$sortCond = "none";
		}		
		
		
		if (isset($_POST['page'])) {
			if ($_POST['page'] == '0') {
				$page = $_SESSION['ShowShopPage'];
			}
			else{
				$page = intval($_POST['page']);
				$_SESSION['ShowShopPage'] = $_POST['page'];
			}
		}
		else {
			$page = 1;
		}
		
		$startRow = ($page-1)*5;
		
		$account = $_SESSION['Account'];
		$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $conn->prepare("select account, latitude, longitude from users where account=:account");
		$stmt->execute(array('account' => $account));
		
		$row = $stmt->fetch();
		$user_point = 'POINT('.$row['longitude'].','.$row['latitude'].')';
		
		
		if ($sortCond == "an") {
			$orderby = "order by shopName";
		}
		elseif ($sortCond == "dn") {
			$orderby = "order by shopName desc";
		}
		elseif ($sortCond == "ac") {
			$orderby = "order by category";
		}
		elseif ($sortCond == "dc") {
			$orderby = "order by category desc";
		}
		elseif ($sortCond == "ad") {
			$orderby = "order by ST_Distance_Sphere(".$user_point.", slocation)";
		}
		elseif ($sortCond == "dd") {
			$orderby = "order by ST_Distance_Sphere(".$user_point.", slocation) desc";
		}
		else{
			$orderby = "";
		}
		
		
		$condition = "where shopName like :shop and ST_Distance_Sphere(".$user_point.", slocation) >= :MINdistance and ST_Distance_Sphere(".$user_point.", slocation) < :MAXdistance and price >= :priceLow and price < :priceHigh and itemName like :meal and category like :category $orderby limit $startRow,5";
		
		$stmt = $conn->prepare("select distinct ST_Distance_Sphere(".$user_point.", slocation) as distance, shopName, category, sid from shop natural join item ".$condition);
		$stmt->execute(array('shop' => $shop, 'MINdistance' => $MINdistance, 'MAXdistance' => $MAXdistance, 'priceLow' => $priceLow, 'priceHigh' => $priceHigh, 'meal' => $meal, 'category' => $category));
		
		$data = array();
		
		if ($stmt->rowCount() > 0)
		{
			while ($row = $stmt->fetch()) {
				$return_shopName = $row['shopName'];
				$return_category = $row['category'];
				$return_sid = $row['sid'];
				$dis = floatval($row['distance']);
				
				if ($dis >= 0 && $dis < 2000) {
					$return_distance = "near";
				}
				elseif ($dis >= 2000 && $dis < 5000) {
					$return_distance = "medium";
				}
				elseif ($dis >= 5000) {
					$return_distance = "far";
				}
				
				$tempData = array($return_shopName, $return_category, $return_distance, $return_sid);
				array_push($data, $tempData);
			}
		}
		
		array_unshift($data, array("success", $page, "success"));
				
		echo json_encode($data);
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
			$data = array(array("fail", $msg, "fail"));
			echo json_encode($data);
		}
	}
?>