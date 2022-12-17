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
	function shop_data()
	{
		let xhttp2 = new XMLHttpRequest();
		xhttp2.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				document.getElementsByName('shop_info_name')[0].placeholder = data[0];
				document.getElementsByName('shop_info_category')[0].placeholder = data[1];
				document.getElementsByName('shop_info_latitude')[0].placeholder = data[2];
				document.getElementsByName('shop_info_longitude')[0].placeholder = data[3];
			}
		};
		xhttp2.open("POST", "shop_data.php", true);
		xhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp2.send();
	}	
	
	function profile_data()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			let message;
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				document.getElementById("account").innerHTML = data[0];
				document.getElementById("status").innerHTML = data[1];
				document.getElementById("phone").innerHTML = data[2];
				document.getElementById("latitude").innerHTML = data[3];
				document.getElementById("longitude").innerHTML = data[4];
				document.getElementById("money").innerHTML = data[5];
				
				if (data[1] == "user") {
					document.getElementById("user_hidden1").style.display = "none";    // do not display
					document.getElementById("user_hidden2").style.display = "none";    // do not display
					document.getElementById("user_hidden3").style.display = "none";    // do not display
					document.getElementsByName('shop_info_name')[0].readOnly = false;
					document.getElementsByName('shop_info_category')[0].readOnly = false;
					document.getElementsByName('shop_info_latitude')[0].readOnly = false;
					document.getElementsByName('shop_info_longitude')[0].readOnly = false;
					document.getElementsByName('shop_info_register')[0].disabled = false;										
				}
				else if (data[1] == "owner") {
					document.getElementById("user_hidden1").style.display = "";    // display
					document.getElementById("user_hidden2").style.display = "";
					document.getElementById("user_hidden3").style.display = "none";
					document.getElementsByName('shop_info_name')[0].readOnly = true;
					document.getElementsByName('shop_info_category')[0].readOnly = true;
					document.getElementsByName('shop_info_latitude')[0].readOnly = true;
					document.getElementsByName('shop_info_longitude')[0].readOnly = true;
					document.getElementsByName('shop_info_register')[0].disabled = true;
					shop_data();
				}
				showItem();
				ShowMyOrder();
				ShowShopOrder();
				ShowTransactionRecord();
			}
		};
		xhttp.open("POST", "profile_data.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
	}
	
	function edit_location()
	{
		const form = document.forms['newLocForm'];
		const latitude = form.elements.latitude.value;
		const longitude = form.elements.longitude.value;
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			var message;
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				if (data[0] == "fail") {
					alert("Cannot edit location.");
				}
				else {
					document.getElementById("latitude").innerHTML = data[0];
					document.getElementById("longitude").innerHTML = data[1];
					document.getElementById("search_hidden1").style.display = "none";
					form.reset();
				}
			}
		};
		xhttp.open("POST", "edit_location.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("latitude="+latitude+"&"+"longitude="+longitude);
	}
	
	function add_value()
	{
		const form = document.forms['addValueForm'];
		const money = form.elements.addValueAmount.value;
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			var message;
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				if (data[0] == "fail") {
					alert(data[1]);
				}
				else if (data[0] == "success") {
					document.getElementById("money").innerHTML = data[1];
					form.reset();
					profile_data();
				}
			}
		};
		xhttp.open("POST", "add_value.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("money="+money);
	}
	
	function show_search_result(sortCond, page)
	{
		const form = document.forms['searchShopForm'];
		const shop = form.elements.shop.value;
		const distance = form.elements.distance.value;
		const priceLow = form.elements.priceLow.value;
		const priceHigh = form.elements.priceHigh.value;
		const meal = form.elements.meal.value;
		const category = form.elements.category.value;
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert(this.responseText);
				const data = JSON.parse(this.responseText);
				//alert(data.length);
				//alert(data[0][0]);
				//alert(data[0][1]);
				if (data[0][0] == "success") {
					let tbody_content = "";
					let curPage = Number(data[0][1]);
					for (let i = 1; i < data.length; i++) {
						//let rowID = "search_shop_result_" + i;
						//document.getElementById(rowID).style.display = "";
						
						let shop_name = data[i][0];
						let shop_category = data[i][1];
						let distance = data[i][2];
						let sid = data[i][3];
						let new_row = '<tr>'+
							'<th scope="row">1</th>'+
							  '<td>macdonald</td>'+
							  '<td>fast food</td>'+
							  '<td>near</td>'+
							  '<td><button type="button" class="btn btn-info" onclick="open_shop_menu();">'+
							  'Open menu</button></td>'+
							'</tr>';
						let rowNumcal = (curPage-1)*5 + i;
						let rownum = '<th scope="row">' + rowNumcal + '</th>';
						new_row = new_row.replace('<th scope="row">1</th>', rownum);
						new_row = new_row.replace('macdonald', shop_name);
						new_row = new_row.replace('fast food', shop_category);
						new_row = new_row.replace('near', distance);
						//let openParam = '<button type="button" value="' + sid + '" class="btn btn-info" onclick="open_shop_menu(this.value);">';
						let openParam = '<button type="button" value="' + sid + '" class="btn btn-info" onclick="show_shop_menu_modal(this.value);" data-toggle="modal" data-target="#macdonald">';
						new_row = new_row.replace('<button type="button" class="btn btn-info" onclick="open_shop_menu();">', openParam);
						
						tbody_content = tbody_content + new_row;
					}
					document.getElementById("search_shop_tbody").innerHTML = tbody_content;
				}
				else if (data[0][0] == "fail") {
					alert(data[0][1]);
				}
			}
		};
		xhttp.open("POST", "show_search_result.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("shop="+shop+"&"+"distance="+distance+"&"+"priceLow="+priceLow+"&"+"priceHigh="+priceHigh+"&"+"meal="+meal+"&"+"category="+category+"&"+"sortCond="+sortCond+"&"+"page="+page);
	}
	
	function search_shop()
	{
		const form = document.forms['searchShopForm'];
		const shop = form.elements.shop.value;
		const distance = form.elements.distance.value;
		const priceLow = form.elements.priceLow.value;
		const priceHigh = form.elements.priceHigh.value;
		const meal = form.elements.meal.value;
		const category = form.elements.category.value;
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				//alert(data[0]);
				//alert(data[1]);
				
				if (data[0] == "success") {
					document.getElementById("search_hidden1").style.display = "";
					
					let count = data[1];
					let NumCount = Number(count);
					
					let pbhtml = '<p>page</p>';
					
					let pageNum = (NumCount-1) / 5 + 1;
					
					for (let i = 1; i <= pageNum; i++) {
						 let temppb = ' <button type="button" value="' + i + '" onclick="show_search_result('+"'old'"+', this.value)">' + i + '</button>';
						 pbhtml = pbhtml + temppb;
					}
					
					document.getElementById("page_button").innerHTML = pbhtml;
					
					show_search_result("none", "1");
					
					//document.getElementById("searchResult1").style.display = "";					
				}
				else if (data[0] == "fail") {
					alert(data[1]);
				}
			}
		};
		xhttp.open("POST", "search_shop.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("shop="+shop+"&"+"distance="+distance+"&"+"priceLow="+priceLow+"&"+"priceHigh="+priceHigh+"&"+"meal="+meal+"&"+"category="+category);
	}
	
	function open_shop_menu(val)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = this.responseText;
				//alert(data);				
			}
		};
		xhttp.open("POST", "edit_session_shop_menu_id.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("sid="+val);
		
		window.location.assign("open_shop_menu.php");
	}
	
	function openShop()
	{
		const form = document.forms['openShopForm'];
		const name = form.elements.shop_info_name.value;
		const category = form.elements.shop_info_category.value;
		const latitude = form.elements.shop_info_latitude.value;
		const longitude = form.elements.shop_info_longitude.value;
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			var message;
			if (this.readyState == 4 && this.status == 200) {
				alert(this.responseText);
				if (this.responseText == "Register success !!") {
					window.location.href = "nav_owner.php";
				}
			}
		};
		xhttp.open("POST", "openShop.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("name="+name+"&"+"category="+category+"&"+"latitude="+latitude+"&"+"longitude="+longitude);
	}
	
	function addItem()
	{
		let form = $('form[name="addItemForm"]')[0];
		let formData = new FormData(form);
		$.ajax({
			url: "addItem.php",
			type: "POST",
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			success: function(data)
			{
				if (data != "Add item success.") {
					alert(data);
				}
				else {
					document.getElementById("shop_tbody").innerHTML = "";
					showItem();
				}
				form.reset();
			},
			error: function(data) 
			{
				alert("Add item error.");
			}
		})
	}
	
	function showItem()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				if (data.length > 0) {
					document.getElementById("user_hidden3").style.display = "";
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
						  '<td><button type="button" class="btn btn-info" onclick="edit_shop_item();">'+
						  'Edit'+
						  '</button></td>'+
						  '<td><button type="button" class="btn btn-danger" onclick="delete_shop_item();">Delete</button></td>'+
						'</tr>';
						let rowNum = i + 1;
						let row = '<th scope="row">' + rowNum + '</th>';
						newItem = newItem.replace('<th scope="row">1</th>', row);
						newItem = newItem.replace('<td><img src="Picture/1.jpg" width="10" height="10" alt="Hamburger"></td>', img);
						newItem = newItem.replace('<td>Hamburger</td>', name_tag);
						newItem = newItem.replace('<td>80 </td>', price);
						newItem = newItem.replace('<td>20 </td>', quantity);
						
						let editParam = '<button type="button" value="' + pid + '" class="btn btn-info" onclick="edit_shop_item(this.value);">'
						newItem = newItem.replace('<button type="button" class="btn btn-info" onclick="edit_shop_item();">', editParam);
						
						let deleteParam = '<td><button type="button" value="' + pid + '" class="btn btn-danger" onclick="delete_shop_item(this.value);">';
						newItem = newItem.replace('<td><button type="button" class="btn btn-danger" onclick="delete_shop_item();">', deleteParam);
						
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_tbody").innerHTML = new_tbody;					
				}
			}
		};
		xhttp.open("POST", "showItem.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
	}
	
	function edit_shop_item(val)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = this.responseText;
				//alert(data);				
			}
		};
		xhttp.open("POST", "edit_session_item_id.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("pid="+val);
		
		window.location.replace("edit_shop_item_form.php");
	}

	function delete_shop_item(val)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			var message;
			if (this.readyState == 4 && this.status == 200) {
				const data = this.responseText;
				if (data == "fail") {
					alert(data);
				}
				else {
					document.getElementById("shop_tbody").innerHTML = "";
					showItem();
				}
			}
		};
		xhttp.open("POST", "delete_shop_item.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("pid="+val);
	}
	
	function clear_search_table()
	{
		document.getElementById("search_hidden1").style.display = "none";
		let form = $('form[name="searchShopForm"]')[0];
		form.reset();
	}
	
	function check_shopName(sname)
	{
		if (sname != "")
		{
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				var message;
				if (this.readyState == 4 && this.status == 200) {
					switch (this.responseText) {
						case 'YES':
							message = 'Available';
							break;
						case 'NO':
							message = 'Not available';
							break;
						default:
							message = 'Oops. There is something wrong.';
							break;
					}
					document.getElementById("checkShopNameExist").innerHTML = message;
				}
			};
			xhttp.open("POST", "check_shop_name.php", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("uname="+sname);
		}
		else
		{
			document.getElementById("checkShopNameExist").innerHTML = "";
		}
	}
	
	function show_shop_menu_modal(sid)
	{
		document.getElementById("cal_price_header").style.display = "";
		document.getElementById("send_order_header").style.display = "none";
		document.getElementById("DeliTypeInput").style.display = "";
		document.getElementById("cal_price_footer").style.display = "";
		document.getElementById("send_order_footer").style.display = "none";
		
		let new_thead = '<tr> <th scope="col">#</th> <th scope="col">Picture</th> <th scope="col">Meal name</th> <th scope="col">Price</th> <th scope="col">Quantity</th> <th scope="col">Order</th> </tr>';
		document.getElementById("shop_item_thead").innerHTML = new_thead;
				
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
						let q_tag = '<td>' + quantity + '</td>';
						let pid = data[i][4];
						let rowNum = i + 1;
						let newItem = '<tr>' +
						  '<th scope="row">' + rowNum + '</th>'+ img + name_tag + price + q_tag +
						  '<th scope="row">'+'<button type="button" value="minus" onclick="add_item_amount('+pid+', this.value);">-</button>'+' '+
						  '<span id="shop_menu_modal_'+ pid +'">0</span>'+' '+
						  '<button type="button" value="add" onclick="add_item_amount('+pid+', this.value);">+</button>'+'</th>'+
						'</tr>';						
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_item_tbody").innerHTML = new_tbody;
				}
			}
		};
		xhttp.open("POST", "shop_menu_get_item.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("sid="+sid);
	}
	
	function add_item_amount(pid, operator)
	{
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
						let q_tag = '<td>' + quantity + '</td>';
						let pid = data[i][4];
						let amount = data[i][5];
						let rowNum = i + 1;
						let newItem = '<tr>' +
						  '<th scope="row">' + rowNum + '</th>'+ img + name_tag + price + q_tag +
						  '<th scope="row">'+'<button type="button" value="minus" onclick="add_item_amount('+pid+', this.value);">-</button>'+' '+
						  '<span id="shop_menu_modal_'+ pid +'">'+amount+'</span>'+' '+
						  '<button type="button" value="add" onclick="add_item_amount('+pid+', this.value);">+</button>'+'</th>'+
						'</tr>';						
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_item_tbody").innerHTML = new_tbody;
				}
			}
		};
		xhttp.open("POST", "change_order_num.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("pid="+pid+"&"+"operator="+operator);
	}
	
	function resume_shop_menu()
	{
		document.getElementById("cal_price_header").style.display = "";
		document.getElementById("send_order_header").style.display = "none";
		document.getElementById("DeliTypeInput").style.display = "";
		document.getElementById("cal_price_footer").style.display = "";
		document.getElementById("send_order_footer").style.display = "none";
		
		let new_thead = '<tr> <th scope="col">#</th> <th scope="col">Picture</th> <th scope="col">Meal name</th> <th scope="col">Price</th> <th scope="col">Quantity</th> <th scope="col">Order</th> </tr>';
		document.getElementById("shop_item_thead").innerHTML = new_thead;
		
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
						let q_tag = '<td>' + quantity + '</td>';
						let pid = data[i][4];
						let amount = data[i][5];
						let rowNum = i + 1;
						let newItem = '<tr>' +
						  '<th scope="row">' + rowNum + '</th>'+ img + name_tag + price + q_tag +
						  '<th scope="row">'+'<button type="button" value="minus" onclick="add_item_amount('+pid+', this.value);">-</button>'+' '+
						  '<span id="shop_menu_modal_'+ pid +'">'+amount+'</span>'+' '+
						  '<button type="button" value="add" onclick="add_item_amount('+pid+', this.value);">+</button>'+'</th>'+
						'</tr>';						
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_item_tbody").innerHTML = new_tbody;
				}
			}
		};
		xhttp.open("POST", "resume_shop_menu.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
	}
	
	function Calculate_price()
	{
		const form = document.forms['DeliTypeForm'];
		const deliType = form.elements.DeliType.value;
				
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = JSON.parse(this.responseText);
				//alert(data[0][0]);
				//alert(data[0][1]);
				if (data[0][0] == "success") {
					let new_tbody = "";
					let new_thead = '<tr> <th scope="col">Picture</th> <th scope="col">Meal name</th> <th scope="col">Price</th> <th scope="col">Order Quantity</th> </tr>';
					document.getElementById("shop_item_thead").innerHTML = new_thead;
					
					for (let i = 1; i < data.length; i++) {
						let img = data[i][0];
						let name = data[i][1];
						let name_tag = '<td>' + name + '</td>';
						let price = data[i][2];
						let amount = data[i][3];
						let amount_tag = '<td>' + amount + '</td>';
						
						let newItem = '<tr>' + img + name_tag + price + amount_tag  + '</tr>';
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_item_tbody").innerHTML = new_tbody;					
					document.getElementById("cal_price_header").style.display = "none";
					document.getElementById("send_order_header").style.display = "";
					document.getElementById("DeliTypeInput").style.display = "none";
					document.getElementById("send_order_subtotal").innerHTML = "Subtotal: &emsp; $" + data[0][1];
					document.getElementById("send_order_deliveryFee").innerHTML = "Delivery &thinsp; Fee: &emsp; $" + data[0][2];
					document.getElementById("send_order_totalPrice").innerHTML = "Total &thinsp; Price: &emsp; $" + data[0][3];
					document.getElementById("cal_price_footer").style.display = "none";
					document.getElementById("send_order_footer").style.display = "";					
				}
				else if (data[0][0] == "item_removed") {
					let new_tbody = "";
					let new_thead = '<tr> <th scope="col">Picture</th> <th scope="col">Meal name</th> <th scope="col">Price</th> <th scope="col">Order Quantity</th> </tr>';
					document.getElementById("shop_item_thead").innerHTML = new_thead;
					
					for (let i = 1; i < data.length; i++) {
						let img = data[i][0];
						let name = data[i][1];
						let name_tag = '<td>' + name + '</td>';
						let price = data[i][2];
						let amount = data[i][3];
						let amount_tag = '<td>' + amount + '</td>';
						
						let newItem = '<tr>' + img + name_tag + price + amount_tag  + '</tr>';
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_item_tbody").innerHTML = new_tbody;					
					document.getElementById("cal_price_header").style.display = "none";
					document.getElementById("send_order_header").style.display = "";
					document.getElementById("DeliTypeInput").style.display = "none";
					document.getElementById("send_order_subtotal").innerHTML = "Subtotal: &emsp; $" + data[0][1];
					document.getElementById("send_order_deliveryFee").innerHTML = "Delivery &thinsp; Fee: &emsp; $" + data[0][2];
					document.getElementById("send_order_totalPrice").innerHTML = "Total &thinsp; Price: &emsp; $" + data[0][3];
					document.getElementById("cal_price_footer").style.display = "none";
					document.getElementById("send_order_footer").style.display = "";
					alert("Some meals you ordered are no longer available.");
				}
				else if (data[0][0] == "fail") {
					alert(data[0][1]);
					resume_shop_menu();
				}
			}
		};
		xhttp.open("POST", "calculate_price.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("deliType="+deliType);
	}
	
	function show_out_of_stock(out_pid)
	{
		/*alert("***");
		for (let j = 0; j < out_pid.length; j++) {
			alert(out_pid[j]);
		}*/
		
		const form = document.forms['DeliTypeForm'];
		const deliType = form.elements.DeliType.value;
				
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = JSON.parse(this.responseText);
				//alert(data[0][0]);
				//alert(data[0][1]);
				if (data[0][0] == "success") {
					let new_tbody = "";
					let new_thead = '<tr> <th scope="col">Picture</th> <th scope="col">Meal name</th> <th scope="col">Price</th> <th scope="col">Order Quantity</th> </tr>';
					document.getElementById("shop_item_thead").innerHTML = new_thead;
					
					for (let i = 1; i < data.length; i++) {
						let img = data[i][0];
						let name = data[i][1];
						let name_tag = '<td>' + name + '</td>';
						let price = data[i][2];
						let amount = data[i][3];
						let pid = data[i][4];
						let anno = false;
						for (let j = 0; j < out_pid.length; j++) {
							if (pid == out_pid[j]) {
								//alert(pid);
								anno = true;
								break;
							}
						}
						let amount_tag = '<td>' + amount + '</td>';
						if (anno) {
							amount_tag = '<td style="color:red;">' + amount + ' (out of stock)</td>';
						}
						
						let newItem = '<tr>' + img + name_tag + price + amount_tag  + '</tr>';
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_item_tbody").innerHTML = new_tbody;
				}
				else if (data[0][0] == "fail") {
					alert(data[0][1]);
					resume_shop_menu();
				}
			}
		};
		xhttp.open("POST", "calculate_price.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("deliType="+deliType);
	}
	
	function send_order()
	{
		const form = document.forms['DeliTypeForm'];
		const deliType = form.elements.DeliType.value;
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = JSON.parse(this.responseText);
				//alert(data[0]);
				alert(data[1]);
				if (data[0] == "success") {
					window.location.reload();
				}
				else if (data[0] == "fail") {
					if (data[1] == 'The meals you ordered are out of stock.') {
						show_out_of_stock(data[2]);
					}
					if (data[1] == 'Some meals you ordered are no longer available.') {
						Calculate_price();
					}
				}
			}
		};
		xhttp.open("POST", "send_order.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("deliType="+deliType);
	}
	
	function order_detail_modal(oid)
	{
		//alert(oid);
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = JSON.parse(this.responseText);
				//alert(data[0][0]);
				//alert(data[0][1]);
				if (data[0][0] == "success") {
					let new_tbody = "";					
					for (let i = 1; i < data.length; i++) {
						let img = data[i][0];
						let name = data[i][1];
						let price = data[i][2];
						let quantity = data[i][3];
						
						let newItem = '<tr>' + img + name + price + quantity + '</tr>';
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("order_detail_tbody").innerHTML = new_tbody;
					document.getElementById("order_detail_subtotal").innerHTML = "Subtotal: &emsp; $" + data[0][1];
					document.getElementById("order_detail_deliveryFee").innerHTML = "Delivery &thinsp; Fee: &emsp; $" + data[0][2];
					document.getElementById("order_detail_totalPrice").innerHTML = "Total &thinsp; Price: &emsp; $" + data[0][3];
				}
			}
		};
		xhttp.open("POST", "order_detail_modal.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("oid="+oid);
	}
	
	function myOrder_checkbox(oid)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = this.responseText;
				//alert(data);				
			}
		};
		xhttp.open("POST", "myOrder_checkbox.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("oid="+oid);
	}
	
	function ShowMyOrder()
	{
		const form = document.forms['MyOrderStatusForm'];
		const state = form.elements.state.value;
		//alert(state);
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				if (data.length > 0) {
					let new_tbody = "";
					for (let i = 0; i < data.length; i++) {
						let oid = data[i][0];
						let state = data[i][1];
						let end = '<td>' + data[i][3] + '</td>';
						let action = '<td style="visibility:hidden"><button type="button" class="btn btn-danger" value="'+ oid +'" onclick="cancel_order(this.value);">Cancel</button></td>';
						let check = '<td style="visibility:hidden"> <input type="checkbox" id="'+ oid +'" value="Hamburger"></td>';
						if (state == "In Progress") {
							action = '<td><button type="button" class="btn btn-danger" value="'+ oid +'" onclick="cancel_order(this.value);">Cancel</button></td>';
							end = '<td style="visibility:hidden">2022-06-08 17:57:27</td>';
							check = '<td> <input type="checkbox" id="'+ oid +'" value="'+ oid +'" onclick="myOrder_checkbox(this.value);"></td>';
						}
						state = '<td>' + state + '</td>';
						let start = '<td>' + data[i][2] + '</td>';
						let name = '<td>' + data[i][4] + '</td>';
						let price = '<td>' + data[i][5] + '</td>';
						let detail = '<td><button type="button" class="btn btn-info" value="'+ oid +'" onclick="order_detail_modal(this.value);" data-toggle="modal" data-target="#OrderDetail">Detail</button></td>';
						//let newItem = '<tr>' + '<th scope="row">' + oid + '</th>' + state + start + end + name + price + detail + action + '</tr>';
						let newItem = '<tr>' + check + '<td>' + oid + '</td>' + state + start + end + name + price + detail + action + '</tr>';						
						
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("MyOrder_tbody").innerHTML = new_tbody;
				}
				else {
					document.getElementById("MyOrder_tbody").innerHTML = "";
				}
			}
		};
		xhttp.open("POST", "ShowMyOrder.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("state="+state);
	}
	
	function cancel_order(oid)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = this.responseText;
				if (data == "success") {
					profile_data();
				}
				else if (data == "fail") {
					alert("This order has already finished/been cancelled.");
					ShowMyOrder();
				}
				else {
					alert(data);
				}
			}
		};
		xhttp.open("POST", "cancel_order.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("oid="+oid);
	}
	
	function shop_order_detail_modal(oid)
	{
		//alert(oid);
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = JSON.parse(this.responseText);
				//alert(data[0][0]);
				//alert(data[0][1]);
				if (data[0][0] == "success") {
					let new_tbody = "";					
					for (let i = 1; i < data.length; i++) {
						let img = data[i][0];
						let name = data[i][1];
						let price = data[i][2];
						let quantity = data[i][3];
						
						let newItem = '<tr>' + img + name + price + quantity + '</tr>';
												
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("shop_order_detail_tbody").innerHTML = new_tbody;
					document.getElementById("shop_order_detail_subtotal").innerHTML = "Subtotal: &emsp; $" + data[0][1];
					document.getElementById("shop_order_detail_deliveryFee").innerHTML = "Delivery &thinsp; Fee: &emsp; $" + data[0][2];
					document.getElementById("shop_order_detail_totalPrice").innerHTML = "Total &thinsp; Price: &emsp; $" + data[0][3];
				}
			}
		};
		xhttp.open("POST", "order_detail_modal.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("oid="+oid);
	}
	
	function done_order(oid)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = this.responseText;
				if (data == "success") {
					profile_data();
				}
				else if (data == "fail") {
					alert("This order has already finished/been cancelled.");
					ShowShopOrder();
				}
				else {
					alert(data);
				}
			}
		};
		xhttp.open("POST", "done_order.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("oid="+oid);
	}
	
	function shop_order_checkbox(oid)
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = this.responseText;
				//alert(data);				
			}
		};
		xhttp.open("POST", "shop_order_checkbox.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("oid="+oid);
	}
	
	function ShowShopOrder()
	{
		const form = document.forms['ShopOrderStatusForm'];
		const state = form.elements.state.value;
		//alert(state);
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				if (data.length > 0) {
					let new_tbody = "";
					for (let i = 0; i < data.length; i++) {
						let oid = data[i][0];
						let state = data[i][1];
						let end = '<td>' + data[i][3] + '</td>';
						let action = '<td style="visibility:hidden"><button type="button" class="btn btn-success">Done</button></td>';
						let check = '<td style="visibility:hidden"> <input type="checkbox" id="'+ oid +'" value="Hamburger"></td>';
						if (state == "In Progress") {
							action1 = '<td><button type="button" class="btn btn-success" value="'+ oid +'" onclick="done_order(this.value);">Done</button>';
							action2 = '<button type="button" class="btn btn-danger" value="'+ oid +'" onclick="cancel_order(this.value);">Cancel</button></td>';
							action = action1 + "&nbsp;" + action2;
							end = '<td style="visibility:hidden">2022-06-08 17:57:27</td>';
							check = '<td> <input type="checkbox" id="'+ oid +'" value="'+ oid +'" onclick="shop_order_checkbox(this.value);"></td>';
						}
						state = '<td>' + state + '</td>';
						let start = '<td>' + data[i][2] + '</td>';
						let name = '<td>' + data[i][4] + '</td>';
						let price = '<td>' + data[i][5] + '</td>';
						let detail = '<td><button type="button" class="btn btn-info" value="'+ oid +'" onclick="shop_order_detail_modal(this.value);" data-toggle="modal" data-target="#shop_OrderDetail">Detail</button></td>';
						//let newItem = '<tr>' + '<th scope="row">' + oid + '</th>' + state + start + end + name + price + detail + action + '</tr>';
						let newItem = '<tr>' + check + '<td>' + oid + '</td>' + state + start + end + name + price + detail + action + '</tr>';
						
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("ShopOrder_tbody").innerHTML = new_tbody;
				}
				else {
					document.getElementById("ShopOrder_tbody").innerHTML = "";
				}
			}
		};
		xhttp.open("POST", "ShowShopOrder.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("state="+state);
	}
	
	function ShowTransactionRecord()
	{
		const form = document.forms['TransactionRecordForm'];
		const action = form.elements.action.value;
		//alert(action);
		
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				if (data.length > 0) {
					let new_tbody = "";
					for (let i = 0; i < data.length; i++) {
						let rid = data[i][0];
						let action = data[i][1];
						let time = '<td>' + data[i][2] + '</td>';
						let trading_partner = '<td>' + data[i][3] + '</td>';
						let amount = '<td>+' + data[i][4] + '</td>';
						if (action == "payment") {
							amount = '<td>-' + data[i][4] + '</td>';
						}
						action = '<td>' + action + '</td>';
						
						let newItem = '<tr>' + '<th scope="row">' + rid + '</th>' + action + time + trading_partner + amount + '</tr>';
						
						new_tbody = new_tbody + newItem;
					}
					document.getElementById("TransactionRecord_tbody").innerHTML = new_tbody;
				}
				else {
					document.getElementById("TransactionRecord_tbody").innerHTML = "";
				}
			}
		};
		xhttp.open("POST", "ShowTransactionRecord.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("action="+action);
	}
	
	function cancel_selected_order()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = this.responseText;
				if (data == "success") {
					profile_data();
				}
				else if (data == "fail") {
					alert("Some order has already finished/been cancelled.");
					ShowMyOrder();
				}
				else {
					alert(data);
				}
			}
		};
		xhttp.open("POST", "cancel_selected_order.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
	}
	
	function shop_cancel_selected_order()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = this.responseText;
				if (data == "success") {
					profile_data();
				}
				else if (data == "fail") {
					alert("Some order has already finished/been cancelled.");
					ShowShopOrder();
				}
				else {
					alert(data);
				}
			}
		};
		xhttp.open("POST", "shop_cancel_selected_order.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
	}
	
	function shop_finish_selected_order()
	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert("***");
				const data = this.responseText;
				if (data == "success") {
					profile_data();
				}
				else if (data == "fail") {
					alert("Some order has already finished/been cancelled.");
					ShowShopOrder();
				}
				else {
					alert(data);
				}
			}
		};
		xhttp.open("POST", "shop_finish_selected_order.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
	}
	
</script>


<body onload="profile_data()">
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand " href="#">NCTU Eats</a>
      </div>
	  <div class="logout">
        <a class="navbar-brand " href="index.php">Logout</a>
      </div>
    </div>
  </nav>
  <div class="container">

    <ul class="nav nav-tabs">
      <li class="active"><a href="#home">Home</a></li>
      <li><a href="#menu1">Shop</a></li>
	  <li><a href="#MyOrder">My Order</a></li>
	  <li><a href="#ShopOrder">Shop Order</a></li>
	  <li><a href="#TransactionRecord">Transaction Record</a></li>
    </ul>

    <div class="tab-content">
      <div id="home" class="tab-pane fade in active">
        <h3>Profile</h3>
        <div class="row">
          <div class="col-xs-12">
            Account: <span id=account> *account* </span>, <span id=status> *status* </span>, PhoneNumber: <span id=phone> *phone* </span>, location: <span id=latitude> *latitude* </span>, <span id=longitude> *longitude* </span>
            
            <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal"
            data-target="#location">edit location</button>
            <!--  -->
            <div class="modal fade" id="location"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog  modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">edit location</h4>
                  </div>
				  <form name='newLocForm'>
                  <div class="modal-body">
                    <label class="control-label " for="latitude">latitude</label>
                    <input type="text" name="latitude" class="form-control" id="latitude" placeholder="enter latitude">
                      <br>
                      <label class="control-label " for="longitude">longitude</label>
                    <input type="text" name="longitude" class="form-control" id="longitude" placeholder="enter longitude">
                  </div>
                  <div class="modal-footer">
                    <button type="button" onclick="edit_location();" class="btn btn-default" data-dismiss="modal">Edit</button>
                  </div>
				  </form>
                </div>
              </div>
            </div>



            <!--  -->
            walletbalance: <span id=money> *money* </span>
            <!-- Modal -->
            <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal"
              data-target="#myModal">Add value</button>
            <div class="modal fade" id="myModal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog  modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add value</h4>
                  </div>
				  <form name='addValueForm'>
                  <div class="modal-body">
                    <input type="text" name="addValueAmount" class="form-control" id="value" placeholder="enter add value">
                  </div>				  
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="add_value();">Add</button>					
                  </div>
				  </form>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- 
                
             -->
        <h3>Search</h3>
		<form name='searchShopForm'>
        <div class=" row  col-xs-8">          
            <div class="form-group">
              <label class="control-label col-sm-1" for="Shop">Shop</label>
              <div class="col-sm-5">
                <input type="text" name="shop" class="form-control" placeholder="Enter Shop name">
              </div>
              <label class="control-label col-sm-1" for="distance">distance</label>
              <div class="col-sm-5">
                <select name="distance" class="form-control" id="sel1">
                  <option>all</option>
				  <option>near</option>
                  <option>medium </option>
                  <option>far</option>
                </select>
              </div>
            </div>
		</div>		
		<div class=" row  col-xs-8">
            <div class="form-group">

              <label class="control-label col-sm-1" for="Price">Price</label>
              <div class="col-sm-2">

                <input type="text" name="priceLow" class="form-control">

              </div>
              <label class="control-label col-sm-1" for="~">~</label>
              <div class="col-sm-2">

                <input type="text" name="priceHigh" class="form-control">

              </div>
              <label class="control-label col-sm-1" for="Meal">Meal</label>
              <div class="col-sm-5">
                <input type="text" name="meal" list="Meals" class="form-control" id="Meal" placeholder="Enter Meal">
                <datalist id="Meals">
                  <option value="Hamburger">
                  <option value="coffee">
                </datalist>
              </div>
            </div>
		</div>
		<div class=" row  col-xs-8">
            <div class="form-group">
              <label class="control-label col-sm-1" for="category"> category</label>
            
              
                <div class="col-sm-5">
                  <input type="text" name="category" list="categorys" class="form-control" id="category" placeholder="Enter shop category">
                  <datalist id="categorys">
                    <option value="fast food">
               
                  </datalist>
                </div>
                <button type="button" onclick="clear_search_table();" style="margin-left: 18px;"class="btn btn-primary">Clear</button>
				<button type="button" value="none" onclick="search_shop(this.value);" style="margin-left: 18px;"class="btn btn-primary">Search</button>
            </div>          
        </div>
		</form>
        <div class="row" id="search_hidden1" style="display:none">
          <div class="  col-xs-8">
		    <div id="page_button">
		      
		    </div>
            <table class="table" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">#</th>                
                  <th scope="col">shop name
				    <button type="button" value="an" onclick="show_search_result(this.value, '0');">&uarr;</button>
				    <button type="button" value="dn" onclick="show_search_result(this.value, '0');">&darr;</button>
				  </th>
                  <th scope="col">shop category
				    <button type="button" value="ac" onclick="show_search_result(this.value, '0');">&uarr;</button>
				    <button type="button" value="dc" onclick="show_search_result(this.value, '0');">&darr;</button>
				  </th>
                  <th scope="col">Distance
				    <button type="button" value="ad" onclick="show_search_result(this.value, '0');">&uarr;</button>
				    <button type="button" value="dd" onclick="show_search_result(this.value, '0');">&darr;</button>
				  </th>               
                </tr>
              </thead>
              <tbody id="search_shop_tbody">
			    
              </tbody>
            </table>
			
                <!-- Modal -->
  <div class="modal fade" id="macdonald"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="cal_price_header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Menu</h4>
        </div>
		<div class="modal-header" id="send_order_header" style="display:none">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Order</h4>
        </div>
        <div class="modal-body">
         <!--  -->  
         <div class="row">
          <div class="  col-xs-12">
            <table class="table" style=" margin-top: 15px;">
              <thead id="shop_item_thead">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Picture</th>                 
                  <th scope="col">Meal name</th>               
                  <th scope="col">Price</th>
                  <th scope="col">Quantity</th>                
                  <th scope="col">Order</th>
                </tr>
              </thead>
              <tbody id="shop_item_tbody">
                
              </tbody>
            </table>
			<div id="DeliTypeInput">
			<form name='DeliTypeForm'>
			  <label class="control-label col-sm-1" for="DeliType">Type</label>
              <div class="col-sm-5">
                <select name="DeliType" class="form-control">
                  <option>Delivery</option>
			  	  <option>Pick-up</option>
                </select>
              </div>
			</form>
			</div>
          </div>
        </div>
         <!--  -->
        </div>
        <div class="modal-footer" id="cal_price_footer">
          <button type="button" class="btn btn-default" onclick="Calculate_price();">Calculate the price</button>
        </div>
		<div class="modal-footer" id="send_order_footer" style="display:none">
          <p id="send_order_subtotal">Subtotal &emsp; $</p>
		  <p id="send_order_deliveryFee">Delivery &nbsp; Fee &emsp; $</p> <br>
		  <h4 id="send_order_totalPrice">Total &nbsp; Price &emsp; $</h4> <br>
		  <button type="button" class="btn btn-default" onclick="resume_shop_menu();">Back to menu</button>
		  <button type="button" class="btn btn-default" onclick="send_order();">Order</button>
        </div>
      </div>      
    </div>
  </div>
			
          </div>
        </div>        
      </div>
      
    
      <div id="menu1" class="tab-pane fade">
        <h3> Start a business </h3>
		<form name='openShopForm'>
        <div class="form-group ">
          <div class="row">
            <div class="col-xs-2">
              <label for="ex5">shop name</label>
              <input class="form-control" id="ex5" placeholder="macdonald" type="text" name="shop_info_name" oninput="check_shopName(this.value)" readonly="readonly">
			  <label for="ex5" id="checkShopNameExist"></label>
            </div>
            <div class="col-xs-2">
              <label for="ex5">shop category</label>
              <input class="form-control" id="ex5" placeholder="fast food" type="text" name="shop_info_category" readonly="readonly">
            </div>
            <div class="col-xs-2">
              <label for="ex6">latitude</label>
              <input class="form-control" id="ex6" placeholder="24.78472733371133" type="text" name="shop_info_latitude" readonly="readonly">
            </div>
            <div class="col-xs-2">
              <label for="ex8">longitude</label>
              <input class="form-control" id="ex8" placeholder="121.00028167648875" type="text" name="shop_info_longitude" readonly="readonly">
            </div>
          </div>
        </div>

        <div class=" row" style=" margin-top: 25px;">
          <div class=" col-xs-3">
            <button type="button" name="shop_info_register" disabled="disabled" class="btn btn-primary" onclick="openShop();">register</button>
          </div>
        </div>
		</form>
        <hr>		
		
        <h3 id="user_hidden1">ADD </h3>
        <div style="display:none" id="user_hidden2" class="form-group ">
		<form name='addItemForm'>
          <div class="row">
            <div class="col-xs-6">
              <label for="ex3">meal name</label>
              <input class="form-control" id="ex3" type="text" name="name">
            </div>
          </div>
          <div class="row" style=" margin-top: 15px;">
            <div class="col-xs-3">
              <label for="ex7">price</label>
              <input class="form-control" id="ex7" type="text" name="price">
            </div>
            <div class="col-xs-3">
              <label for="ex4">quantity</label>
              <input class="form-control" id="ex4" type="text" name="quantity">
            </div>
          </div>
          <div class="row" style=" margin-top: 25px;">
            <div class=" col-xs-3">
              <label for="ex12">上傳圖片</label>
              <input id="myFile" type="file" name="imgFile" multiple class="file-loading">
            </div>
            <div class=" col-xs-3">
              <button style=" margin-top: 15px;" type="button" class="btn btn-primary" onclick="addItem();">Add</button>
            </div>
          </div>
        </form>
		</div>

        <div style="display:none" id="user_hidden3" class="row">
          <div class="  col-xs-8">
            <table class="table" id="shopItems" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Picture</th>
                  <th scope="col">meal name</th>
              
                  <th scope="col">price</th>
                  <th scope="col">Quantity</th>
                  <th scope="col">Edit</th>
                  <th scope="col">Delete</th>
                </tr>
              </thead>
              <tbody id = "shop_tbody">
			  </tbody>
            </table>
          </div>
        </div>
      </div>

	  <div id="MyOrder" class="tab-pane fade">
	    <div class=" row  col-xs-8">
		  <form name='MyOrderStatusForm'>
			<div class="form-group ">
			  <br>
			  <br>
		      <label class="control-label col-sm-1" for="state">Status</label>
              <div class="col-sm-5">
                <select name="state" class="form-control" onchange="ShowMyOrder()">
                  <option>All</option>
			      <option>Finish</option>
                  <option>In progress</option>
                  <option>Cancel</option>
                </select>
              </div>
			</div>
		  </form>
		</div>
		<br>
		<br>
		<div class=" row  col-xs-12">
		  <div class="control-label col-sm-12">
		    <form name='MyOrderSelectForm'>
			<div>
			  <br>
			  <button type="button" class="btn btn-danger" onclick="cancel_selected_order();">Cancel selected orders</button>
			</div>
		    <table class="table" style=" margin-top: 15px;">
			  <colgroup>
			     <col span="1" style="width: 4%;">
				 <col span="1" style="width: 8%;">
				 <col span="1" style="width: 10%;">
			     <col span="1" style="width: 15%;">
				 <col span="1" style="width: 15%;">
				 <col span="1" style="width: 14%;">
				 <col span="1" style="width: 10%;">
				 <col span="1" style="width: 10%;">
				 <col span="1" style="width: 14%;">
			  </colgroup>
		      <thead>
                <tr>
                  <th scope="col"></th>
				  <th scope="col">Order ID</th>
                  <th scope="col">Status</th>
                  <th scope="col">Start</th>              
                  <th scope="col">End</th>
                  <th scope="col">Shop Name</th>
                  <th scope="col">Total Price</th>
                  <th scope="col">Order Detail</th>
				  <th scope="col">Action</th>
                </tr>
              </thead>
			  <tbody id="MyOrder_tbody">
			    
			  </tbody>
		    </table>
			</form>
		  </div>
		</div>
	  </div>
	  
  <!-- Modal -->
  <div class="modal fade" id="OrderDetail"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Order</h4>
        </div>
        <div class="modal-body">
         <!--  -->  
         <div class="row">
          <div class="  col-xs-12">
            <table class="table" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">Picture</th>                 
                  <th scope="col">Meal name</th>               
                  <th scope="col">Price</th>
                  <th scope="col">Quantity</th>
                </tr>
              </thead>
              <tbody id="order_detail_tbody">
                
              </tbody>
            </table>
          </div>
         </div>
         <!--  -->
        </div>
		<div class="modal-footer">
          <p id="order_detail_subtotal">Subtotal &emsp; $</p>
		  <p id="order_detail_deliveryFee">Delivery &nbsp; Fee &emsp; $</p> <br>
		  <h4 id="order_detail_totalPrice">Total &nbsp; Price &emsp; $</h4>
        </div>
      </div>      
    </div>
  </div>
	  
	  
	  <div id="ShopOrder" class="tab-pane fade">
	    <div class=" row  col-xs-8">
		  <form name='ShopOrderStatusForm'>
			<div class="form-group ">
			  <br>
			  <br>
		      <label class="control-label col-sm-1" for="state">Status</label>
              <div class="col-sm-5">
                <select name="state" class="form-control" onchange="ShowShopOrder()">
                  <option>All</option>
			      <option>Finish</option>
                  <option>In progress</option>
                  <option>Cancel</option>
                </select>
              </div>
			</div>
		  </form>
		</div>
		<br>
		<br>
		<div class=" row  col-xs-12">
		  <div class="control-label col-sm-12">
		    <div>
			  <br>
			  <button type="button" class="btn btn-success" onclick="shop_finish_selected_order();">Finish selected orders</button>
			  <button type="button" class="btn btn-danger" onclick="shop_cancel_selected_order();">Cancel selected orders</button>
			</div>
			<table class="table" style=" margin-top: 15px;">
			  <colgroup>
			     <col span="1" style="width: 4%;">
				 <col span="1" style="width: 8%;">
				 <col span="1" style="width: 10%;">
			     <col span="1" style="width: 15%;">
				 <col span="1" style="width: 15%;">
				 <col span="1" style="width: 14%;">
				 <col span="1" style="width: 10%;">
				 <col span="1" style="width: 10%;">
				 <col span="1" style="width: 14%;">
			  </colgroup>
		      <thead>
                <tr>
                  <th scope="col"></th>
				  <th scope="col">Order ID</th>
                  <th scope="col">Status</th>
                  <th scope="col">Start</th>              
                  <th scope="col">End</th>
                  <th scope="col">Shop Name</th>
                  <th scope="col">Total Price</th>
                  <th scope="col">Order Detail</th>
				  <th scope="col">Action</th>
                </tr>
              </thead>
			  <tbody id="ShopOrder_tbody">
			    
			  </tbody>
		    </table>
		  </div>
		</div>
	  </div>
	  
  <!-- Modal -->
  <div class="modal fade" id="shop_OrderDetail" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Order</h4>
        </div>
        <div class="modal-body">
         <!--  -->  
         <div class="row">
          <div class="  col-xs-12">
            <table class="table" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">Picture</th>                 
                  <th scope="col">Meal name</th>               
                  <th scope="col">Price</th>
                  <th scope="col">Quantity</th>
                </tr>
              </thead>
              <tbody id="shop_order_detail_tbody">
                
              </tbody>
            </table>
          </div>
         </div>
         <!--  -->
        </div>
		<div class="modal-footer">
          <p id="shop_order_detail_subtotal">Subtotal &emsp; $</p>
		  <p id="shop_order_detail_deliveryFee">Delivery &nbsp; Fee &emsp; $</p> <br>
		  <h4 id="shop_order_detail_totalPrice">Total &nbsp; Price &emsp; $</h4>
        </div>
      </div>      
    </div>
  </div>
	  
	  <div id="TransactionRecord" class="tab-pane fade">
	    <div class=" row  col-xs-8">
		  <form name='TransactionRecordForm'>
			<div class="form-group ">
			  <br>
			  <br>
		      <label class="control-label col-sm-1" for="action">Action</label>
              <div class="col-sm-5">
                <select name="action" class="form-control" onchange="ShowTransactionRecord()">
                  <option>all</option>
			      <option>income</option>
                  <option>payment</option>
                  <option>add value</option>
                </select>
              </div>
			</div>
		  </form>
		</div>
		<br>
		<br>
		<div class=" row  col-xs-12">
		  <div class="control-label col-sm-12">
		    <table class="table" style=" margin-top: 30px;">
			  <colgroup>
			     <col span="1" style="width: 15%;">
			     <col span="1" style="width: 15%;">
			     <col span="1" style="width: 30%;">
				 <col span="1" style="width: 25%;">
				 <col span="1" style="width: 15%;">
			  </colgroup>
		      <thead>
                <tr>
                  <th scope="col">Record ID</th>
                  <th scope="col">Action</th>
                  <th scope="col">Time</th>              
                  <th scope="col">Trading Partner</th>
                  <th scope="col">Amount Change</th>
                </tr>
              </thead>
			  <tbody id="TransactionRecord_tbody">
			    
			  </tbody>
		    </table>
		  </div>
		</div>
	  </div>

    </div>
  </div>

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
  <script>
    $(document).ready(function () {
      $(".nav-tabs a").click(function () {
        $(this).tab('show');
      });
    });
  </script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>