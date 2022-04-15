<?php

session_start();

define('tejido', TRUE);

?>

<!DOCTYPE html>
<html translate="no" lang="es">
<head>
	<?php include('views/head.php'); ?>
</head>
<body>

<?php 


if(file_exists("config/config.php")) {
	// include config file
	include('config/config.php');

	// include funciones
	include('functions/functions.php');

	// incluir header
	include('template/header.php');

	// incluir cuerpo
	// 1. cuerpo por region
	if (array_key_exists("dept", $_GET)) {
		include('views/reg.php');
	} elseif (array_key_exists("agradecimiento", $_GET)) {
		include('views/agradecimiento.php');
	} else {
		include('views/map.php'); 
	}
	

	// incluir footer
	include('template/footer.php');

} else {
	include('views/inst/inst.php');
}



?>

</body>
</html>