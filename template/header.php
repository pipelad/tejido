<?php

if(!defined('tejido')) {
   header("Location: index.php");
}

$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
$query = $con->prepare("SELECT * FROM `config` WHERE id = '1'");
$query->execute();
$rowp = $query->fetch(PDO::FETCH_OBJ);

?>

<div id="header">
	<div id="logo">
		<a href="index.php"><?php getLogo(); ?></a>
	</div>
	<div id="app_title">
		<div><?php echo $rowp->titulo; ?></div>
		<span id="app_subtitle">Decanatura de división de Educación Abierta y a Distancia</span>
	</div>
</div>