<?php

if(!defined('tejido')) {
   header("Location: index.php");
}

$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$query = $con->query('SELECT `logo` FROM `config` WHERE id = "1" LIMIT 1');
$results = $query->fetch(PDO::FETCH_OBJ);

?>
<div id="loginwrap">

	<div id="loginarea">
					<?php if($error != "") {
						echo '<div id="error"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
					} ?>
		<div id="login" class="right-box">
			
			<div id="loginmain">
				<div id="logdata">
					
					<div id="formulario" >
						<div id="user_label"><i id="log_i" class="fas fa-user-circle"></i></div>
						<form name="login" method="post" id="login_form" action="index.php">
							<div id="header">INGRESO ADMINISTRATIVO</div>
							<div id="cont">
								<div><label for="username"><input type="text" name="username" placeholder="Usuario"> <i class="fas fa-user"></i></label></div>
								<div><label for="password"><input type="password" name="password" placeholder="Contraseña"> <i class="fas fa-key"></i></label></div>
								<input type="hidden" name="log_form" value="1">
							</div>
							<button type="submit" name="btnLogin" id="submit" value="INGRESAR"><i class="fas fa-lock-open"></i> INGRESAR</button>
						</form>
					</div>
				</div>
				<div id="imagen">
				</div>
			</div>
		</div>
		<div id="tej_login">
			<div id="titulo"><img src="<?php echo '../'.$results->logo; ?>"></div>
		</div>
	</div>
</div>
