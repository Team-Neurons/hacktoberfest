<?php
session_start();
if (isset($_SESSION['id'])){
	header('location:dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin Login</title>
	
	<meta charset="utf-8">  
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	
	<style type="text/css">
		@import url(https://fonts.googleapis.com/css?family=Roboto:300);

		.login-page {
			width: 360px;
			padding: 8% 0 0;
			margin: auto;
		}
		.form {
			position: relative;
			z-index: 1;
			background: #FFFFFF;
			max-width: 360px;
			margin: 0 auto 100px;
			padding: 45px;
			text-align: center;
			box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
		}
		.form input {
			font-family: "Roboto", sans-serif;
			outline: 0;
			background: #f2f2f2;
			width: 100%;
			border: 0;
			margin: 0 0 15px;
			padding: 15px;
			box-sizing: border-box;
			font-size: 14px;
		}
		.form button {
			font-family: "Roboto", sans-serif;
			text-transform: uppercase;
			outline: 0;
			background: #4CAF50;
			width: 100%;
			border: 0;
			padding: 15px;
			color: #FFFFFF;
			font-size: 14px;
			-webkit-transition: all 0.3 ease;
			transition: all 0.3 ease;
			cursor: pointer;
		}
		.form button:hover,.form button:active,.form button:focus {
			background: #43A047;
		}
		.form .message {
			margin: 15px 0 0;
			color: #b3b3b3;
			font-size: 12px;
		}
		.form .message a {
			color: #4CAF50;
			text-decoration: none;
		}
		.form .register-form {
			display: none;
		}
		.container {
			position: relative;
			z-index: 1;
			max-width: 300px;
			margin: 0 auto;
		}
		.container:before, .container:after {
			content: "";
			display: block;
			clear: both;
		}
		.container .info {
			margin: 50px auto;
			text-align: center;
		}
		.container .info h1 {
			margin: 0 0 15px;
			padding: 0;
			font-size: 36px;
			font-weight: 300;
			color: #1a1a1a;
		}
		.container .info span {
			color: #4d4d4d;
			font-size: 12px;
		}
		.container .info span a {
			color: #000000;
			text-decoration: none;
		}
		.container .info span .fa {
			color: #EF3B3A;
		}
		body {
			font-family: "Roboto", sans-serif;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;      
		}
	</style>
</head>
<body>
	

	<section>
		<div class="login-page">
			<div class="form">
				<form class="register-form">
					<input type="text" placeholder="name"/>
					<input type="password" placeholder="password"/>
					<input type="text" placeholder="email address"/>
					<button>create</button>
					<p class="message">Already registered? <a href="#">Sign In</a></p>
				</form>
				<form class="login-form" action="login.php" method="POST" >
					<input type="text" placeholder="username" name="user" />
					<input type="password" placeholder="password" name="pass" />
					<button>login</button>
					<p class="message">Not registered? <a href="#">Create an account</a></p>
				</form>
			</div>
		</div>
	</section>

</body>
</html>

<?php
	
	include('dbconnect.php');

	if (isset($_POST['user']) && isset($_POST['pass'])){
		$username = $_POST['user'];
		$password = $_POST['pass'];
		$sql="SELECT * FROM `admin_user` WHERE `username`='$username' AND `password`='$password'";
		$result = mysqli_query($conn,$sql);
		// echo var_dump($result);
		$row = mysqli_num_rows($result);
		// echo $row;
		if ($row < 1){
			// echo "NOT found"
			?>
			<script>
				alert('username or password not correct');
				window.open('login.php','_self');
			</script>
			
		<?php
		}
		else{
			$data = mysqli_fetch_assoc($result);
			
			session_start();
			$id=$data['id'];
			$name=$data['name'];
			// echo "id=". $id;

			

			$_SESSION['id']=$id;
			$_SESSION['name']=$name;
			header('location:dashboard.php');

		}
		

	}

?>