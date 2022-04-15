<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT `usergroup` FROM `usuarios` WHERE id = :userid LIMIT 1');
	$id = $_SESSION['tejidoadmin'];
	$sql->bindValue(':userid', $id);
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);

?>
<div id="user_menu">
	<div id="plus">
		<?php if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin')  { ?>
		<a class="new sombra" href="index.php?nuevo_user">
			<i class="fas fa-plus-circle"></i> Nuevo usuario
		</a>
		<?php } 
		if($results->usergroup === 'SuperAdmin')  { ?>
		<a id="delete_user" class="delete sombra">
			borrar
		</a>
		<?php } ?>
		<a id="atras" class="edit sombra" href="index.php">
			regresar
		</a>
	</div>
</div>
<div id="user_table">
	<?php
		if (array_key_exists('error', $_SESSION)) {
			$error = $_SESSION['error'];
			unset($_SESSION['error']);
		}
		if (array_key_exists('advertencia', $_SESSION)) {
			$advertencia = $_SESSION['advertencia'];
			unset($_SESSION['advertencia']);
		}
		if($error != "") {
			echo '<div id="error"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
		}
		if($advertencia != "") {
		  echo '<div id="advertencia"><i class="fas fa-exclamation-circle"></i> '.$advertencia.'</div>';
		}
	?>
	<div id="user-list">
		<div id="title-row">
			<div class="check"><input id="checkall" type="checkbox" name="user-id-chk"></div>
			<div class="user-id">ID</div>
			<div class="user-nombre">Nombre</div>
			<div class="user-tipo">Tipo</div>
		</div>
		<div id="content-row">
			<?php allUsers(); ?>
		</div>
	</div>
</div>