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
	function profile_data()
	{
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			var message;
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				document.getElementById("account").innerHTML = data[0];
				document.getElementById("status").innerHTML = data[1];
				document.getElementById("phone").innerHTML = data[2];
				document.getElementById("latitude").innerHTML = data[3];
				document.getElementById("longitude").innerHTML = data[4];
				document.getElementById("money").innerHTML = data[5];
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
		
		var xhttp = new XMLHttpRequest();
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
				}
			}
		};
		xhttp.open("POST", "edit_location.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("latitude="+latitude+"&"+"longitude="+longitude);
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
		
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			var message;
			if (this.readyState == 4 && this.status == 200) {
				const data = JSON.parse(this.responseText);
				if (data[0] == "fail") {
					alert("Cannot edit location.");
				}
				else {
					
				}
			}
		};
		xhttp.open("POST", "search_shop.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send();
	}
	
	function openShop()
	{
		const form = document.forms['openShopForm'];
		const name = form.elements.name.value;
		const category = form.elements.category.value;
		const latitude = form.elements.latitude.value;
		const longitude = form.elements.longitude.value;
		
		var xhttp = new XMLHttpRequest();
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
</script>


<body onload="profile_data()">
 
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand " href="#">WebSiteName</a>
      </div>
	  <div class="logout">
        <a class="navbar-brand " href="index.php">Logout</a>
      </div>
    </div>
  </nav>
  <div class="container">

    <ul class="nav nav-tabs">
      <li class="active"><a href="#home">Home</a></li>
      <li><a href="#menu1">shop</a></li>


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
                  <div class="modal-body">
                    <input type="text" class="form-control" id="value" placeholder="enter add value">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Add</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- 
                
             -->
        <h3>Search</h3>
        <div class=" row  col-xs-8">
          <form name='searchShopForm'>
            <div class="form-group">
              <label class="control-label col-sm-1" for="Shop">Shop</label>
              <div class="col-sm-5">
                <input type="text" name="shop" class="form-control" placeholder="Enter Shop name">
              </div>
              <label class="control-label col-sm-1" for="distance">distance</label>
              <div class="col-sm-5">


                <select name="distance" class="form-control" id="sel1">
                  <option>near</option>
                  <option>medium </option>
                  <option>far</option>

                </select>
              </div>

            </div>

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

            <div class="form-group">
              <label class="control-label col-sm-1" for="category"> category</label>
            
              
                <div class="col-sm-5">
                  <input type="text" name="category" list="categorys" class="form-control" id="category" placeholder="Enter shop category">
                  <datalist id="categorys">
                    <option value="fast food">
               
                  </datalist>
                </div>
                <button type="button" onclick="search_shop();" style="margin-left: 18px;"class="btn btn-primary">Search</button>
              
            </div>
          </form>
        </div>
        <div class="row">
          <div class="  col-xs-8">
            <table class="table" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">#</th>
                
                  <th scope="col">shop name</th>
                  <th scope="col">shop category</th>
                  <th scope="col">Distance</th>
               
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th scope="row">1</th>
               
                  <td>macdonald</td>
                  <td>fast food</td>
                
                  <td>near </td>
                  <td>  <button type="button" class="btn btn-info " data-toggle="modal" data-target="#macdonald">Open menu</button></td>
            
                </tr>
           

              </tbody>
            </table>

                <!-- Modal -->
  <div class="modal fade" id="macdonald"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">menu</h4>
        </div>
        <div class="modal-body">
         <!--  -->
  
         <div class="row">
          <div class="  col-xs-12">
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
              <tbody>
                <tr>
                  <th scope="row">1</th>
                  <td><img src="Picture/1.jpg" with="50" heigh="10" alt="Hamburger"></td>
                
                  <td>Hamburger</td>
                
                  <td>80 </td>
                  <td>20 </td>
              
                  <td> <input type="checkbox" id="cbox1" value="Hamburger"></td>
                </tr>
                <tr>
                  <th scope="row">2</th>
                  <td><img src="Picture/2.jpg" with="10" heigh="10" alt="coffee"></td>
                 
                  <td>coffee</td>
             
                  <td>50 </td>
                  <td>20</td>
              
                  <td><input type="checkbox" id="cbox2" value="coffee"></td>
                </tr>

              </tbody>
            </table>
          </div>

        </div>
        

         <!--  -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Order</button>
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
              <input class="form-control" id="ex5" placeholder="macdonald" type="text" name="name" >
            </div>
            <div class="col-xs-2">
              <label for="ex5">shop category</label>
              <input class="form-control" id="ex5" placeholder="fast food" type="text" name="category" >
            </div>
            <div class="col-xs-2">
              <label for="ex6">latitude</label>
              <input class="form-control" id="ex6" placeholder="121.00028167648875" type="text" name="latitude" >
            </div>
            <div class="col-xs-2">
              <label for="ex8">longitude</label>
              <input class="form-control" id="ex8" placeholder="24.78472733371133" type="text" name="longitude" >
            </div>
          </div>
        </div>

        <div class=" row" style=" margin-top: 25px;">
          <div class=" col-xs-3">
            <button type="button" class="btn btn-primary" onclick="openShop();" >register</button>
          </div>
        </div>
		</form>

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