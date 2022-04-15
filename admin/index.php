<?php
session_start();

define('tejido', TRUE);

?>
<!DOCTYPE html>
<html>
<head>
	<?php include('views/head.php'); ?>
</head>
<body>
	<?php 
		include('../config/config.php');
		include('functions/funciones.php');
		if (isset($_SESSION) AND array_key_exists("tejidoadmin", $_SESSION)) {
			include('views/administrar.php');
		} else { 
			include('views/login.php'); 
		}
	?>
</body>
</html>