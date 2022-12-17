<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <title>Hello, world!</title>
  
<style>
.logout {
  float: right;
}
</style>
</head>


<script>
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			const data = JSON.parse(this.responseText);
			if (data.length > 0) {
				let new_tbody = "";
				for (let i = 0; i < data.length; i++) {
					let img = data[i][0];
					let name = data[i][1];
					let name_tag = '<td>' + name + '</td>';
					let price = data[i][2];
					let quantity = data[i][3];
					let pid = data[i][4];
					let newItem = '<tr>' +
					  '<th scope="row">1</th>'+
					  '<td><img src="Picture/1.jpg" width="10" height="10" alt="Hamburger"></td>'+
					  '<td>Hamburger</td>'+
					  '<td>80 </td>'+
					  '<td>20 </td>'+
					  '<td><input type="number" min="0" step="1" value="0"/></td>'+
					'</tr>';
					let rowNum = i + 1;
					let row = '<th scope="row">' + rowNum + '</th>';
					newItem = newItem.replace('<th scope="row">1</th>', row);
					newItem = newItem.replace('<td><img src="Picture/1.jpg" width="10" height="10" alt="Hamburger"></td>', img);
					newItem = newItem.replace('<td>Hamburger</td>', name_tag);
					newItem = newItem.replace('<td>80 </td>', price);
					newItem = newItem.replace('<td>20 </td>', quantity);
					let boxValue = 'value="' + pid + '">';
					newItem = newItem.replace('value="Hamburger">', boxValue);
					
					new_tbody = new_tbody + newItem;
				}
				document.getElementById("shop_item_tbody").innerHTML = new_tbody;
			}
		}
	};
	xhttp.open("POST", "shop_menu_get_item.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
	
	function back()
	{
		//window.location.replace("nav_owner.php");
		window.history.back();
	}
</script>

<body>
<?php
	session_start();
	
	if ($_SESSION['Authenticated'] == false)
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

<h3>Menu</h3>
<div class="logout">
    <button type="button" class="btn btn-default" onclick="back();">Back</button>
</div>

<table class="table" style=" margin-top: 15px;">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Picture</th>
      <th scope="col">meal name</th>
  
      <th scope="col">price</th>
      <th scope="col">Quantity</th>
      <th scope="col">Order check</th>
    </tr>	
  </thead>
  <tbody id = "shop_item_tbody">
    <tr>
      <th scope="row">1</th>
      <td><img src="Picture/1.jpg" width="50" height="50" alt="Hamburger"></td>
      <td>Hamburger</td>                
      <td>80 </td>
      <td>20 </td>                                  
      <td><input type="checkbox" id="cbox2" value="Hamburger"></td>
    </tr>
  </tbody>
</table>
      

</body>

</html>