<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}

	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT `nombrecompleto`, `usergroup` FROM `usuarios` WHERE id = :userid LIMIT 1');
	$id = $_SESSION['tejidoadmin'];
	$sql->bindValue(':userid', $id);
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);

?>

<div id="home">
	<div id="adminzone">
		<div id="administrables">
			<div class="h_boton">
				<a href="index.php?articulos" title="Articulos"><i class="far fa-file"></i> Artículos</a>
			</div>
			<div class="h_boton">
				<a href="index.php?proyectos" title="Proyectos"><i class="far fa-folder-open"></i> Proyectos</a>
			</div>
			<?php if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin')  { ?>
				<div class="h_boton">
					<a href="index.php?user" title="Usuarios"><i class="far fa-user-circle"></i> Usuarios</a>
				</div>
			<?php } ?>
			<?php if($results->usergroup === 'SuperAdmin')  { ?>
				<div class="h_boton">
					<a href="index.php?config" title="Configurar"><i class="fas fa-wrench"></i> Configurar</a>
				</div>
			<?php } ?>
		</div>
		<div id="perfil">
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
			<div id="userinfo">
				<div id="user-icon"><i class="far fa-id-card"></i></div>
				<div id="bienvenida"><span>Bienvenido:</span> <?php echo $results->nombrecompleto; ?></div>
				<a href="index.php?update_user=<?php echo $id ?>"><i class="fas fa-user-edit"></i> Actualizar usuario</a>

			</div>
		</div>
	</div>
</div>