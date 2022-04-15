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
<div id="administrar">

	<div id="top">
		<div id="logo">
			<?php getLogo(); ?>
		</div>
		<div id="title">
			ZONA ADMINISTRATIVA
		</div>
		<div id="user">
			<div id="logout"><a href="index.php?logout">Salida Segura <i class="fas fa-sign-out-alt"></i></a></div>
		</div>
	</div>

	<div id="admin_content">
		<div id="r_menu">
			<div class="r_boton">
				<a href="index.php" title="Inicio"><i class="fas fa-home"></i></a>
			</div>
			<div class="r_boton">
				<a href="index.php?articulos" title="Articulos"><i class="far fa-file"></i></a>
			</div>
			<div class="r_boton">
				<a href="index.php?proyectos" title="Proyectos"><i class="far fa-folder-open"></i></a>
			</div>
			<?php if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin')  { ?>
				<div class="r_boton">
					<a href="index.php?user" title="Usuarios"><i class="far fa-user-circle"></i></a>
				</div>
			<?php } ?>
			<?php if($results->usergroup === 'SuperAdmin')  { ?>
				<div class="r_boton">
					<a href="index.php?config" title="Configurar"><i class="fas fa-wrench"></i></a>
				</div>
			<?php } ?>
		</div>

		<div id="content">
			<div id="cloth"></div>
			<div id="cont-wrap">
				<?php
					///// ARTTICULOS
					if(array_key_exists("articulos", $_GET)) {
						include('views/articulos.php');
					} 
					///// ---> funciones de articulos, añadir, editar
						else if(array_key_exists("nuevo_art", $_GET)) {
							include('views/addart.php');
						}
						else if(array_key_exists("update_articulo", $_GET)) {
							include('views/updateart.php');
						}
					///// PROYECTOS
					else if(array_key_exists("proyectos", $_GET)) {
						include('views/proyects.php');
					} 
					///// ---> funciones de proyecto, añadir, editar
						else if(array_key_exists("nuevo_proy", $_GET)) {
							include('views/addproy.php');
						}
						else if(array_key_exists("update_proy", $_GET)) {
							include('views/updateproy.php');
						}
						else if(array_key_exists("region", $_GET)) {
							include('views/region.php');
						}
						else if(array_key_exists("update_region", $_GET)) {
							include('views/u_region.php');
						}
						else if(array_key_exists("programa", $_GET)) {
							include('views/programa.php');
						}
					///// USUARIOS
					else if(array_key_exists("user", $_GET)) {
						include('views/user.php');
					}	
					///// ---> funciones de usuarios, añadir, editar
						else if(array_key_exists("nuevo_user", $_GET)) {
							include('views/adduser.php');
						}
						else if(array_key_exists("update_user", $_GET)) {
							include('views/updateuser.php');
						}
					///// Configuracion
					else if(array_key_exists("config", $_GET)) {
						include('views/config.php');
					}
					else {
						include('views/home.php');
					}
				?>
			</div>
			<div id="footer">
				<?php contacto(); ?>
			</div>
			
			<script type="text/javascript" src="admin_template/js/admin.js"></script>

		</div>
		
	</div>


</div>
