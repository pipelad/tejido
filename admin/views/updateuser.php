<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}

	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT `usergroup`, `id` FROM `usuarios` WHERE id = :userid LIMIT 1');
	$id = $_SESSION['tejidoadmin'];
	$sql->bindValue(':userid', $id);
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);
	$privilegio = false;
	if ($id === $results->id) {
		$privilegio = true;
	} else if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin') {
		$privilegio = true;
	}  

	if($privilegio === true) {
		$query = $con->prepare('SELECT * FROM `usuarios` WHERE `id` = :id');
		$id = $_GET['update_user'];
		$query->bindValue(':id', $id);
		$query->execute();
		$row = $query->fetch(PDO::FETCH_OBJ);
?>
<div id="add">
	<?php
		if($error != "") {
			echo '<div id="error"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
		}

		if($advertencia != "") {
		  echo '<div id="advertencia"><i class="fas fa-exclamation-circle"></i> '.$advertencia.'</div>';
		}
	?>
	<form id="add_article" name="update_user" method="post" class="edit">
		<div id="form-title"><i class="fas fa-plus-square"></i> Editar</div>
		<div id="form-data">
			<div id="labels-user">
					<span><label for="name">Nomber y Apellido</label><input required type="text" name="name" placeholder="Ej. Andrés Restrepo" value="<?php echo $row->nombrecompleto; ?>"></span>
					<span><label for="username">Nombre de Usuario</label><input required type="text" name="username" placeholder="Ej. andres.restrepo minimo 8 máximo 15 letras" minlength="8" maxlength="15" value="<?php echo $row->usuario; ?>"></span>
					<span><label for="tipo">Tipo de documento</label>
					<select name="tipo">
						<?php
							if($row->tipodoc === 'cc') {
								echo '<option selected value="cc">cédula de ciudadanía</option>';
								echo '<option value="ce">Cédula de extranjería</option>';
							} else {
								echo '<option value="cc">cédula de ciudadanía</option>';
								echo '<option selected value="ce">Cédula de extranjería</option>';
							}
							
						?>
					</select>
					</span>
					<span><label for="documento">Numero de documento</label><input required type="text" name="documento" placeholder="Solo números" maxlength="15" pattern="\d*" value="<?php echo $row->documento; ?>"></span>
					<span><label for="userpasword">Contraseña</label><input type="password" name="userpasword" placeholder="solo llene este campo si desea cambiar su contrasña"></span>
					<span><label for="usergroup">Grupo de usuario</label>
					<select name="usergroup" required>
						<?php
							if($row->usergroup === 'SuperAdmin') {
								if($results->usergroup === 'SuperAdmin') {
									echo '<option selected value="SuperAdmin">Super Administrador</option>';
								}
								if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin') {
									echo '<option value="Admin">Administrador</option>';
								}
								echo '<option value="Revisor">Revisor</option>';
							} else if($row->usergroup === 'Admin') {
								if($results->usergroup === 'SuperAdmin') {
									echo '<option value="SuperAdmin">Super Administrador</option>';
								}
								if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin') {
									echo '<option selected value="Admin">Administrador</option>';
								}
								echo '<option value="Revisor">Revisor</option>';
							} else {
								if($results->usergroup === 'SuperAdmin') {
									echo '<option value="SuperAdmin">Super Administrador</option>';
								}
								if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin') {
									echo '<option value="Admin">Administrador</option>';
								}
								echo '<option selected value="Revisor">Revisor</option>';
							}
							
						?>
					</select>
					</span>
			</div>
		</div>
		<button class="edit" name="update_user" value="1"><i class="far fa-save"></i> ACTUALIZAR</button>
		<?php
			if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin') {
				echo '<a class="cancel" href="index.php?user"><i class="far fa-window-close"></i> CANCELAR</a>';
			} else {
				echo '<a class="cancel" href="index.php"><i class="far fa-window-close"></i> CANCELAR</a>';
			}
			
		?>
		<br clear="all">
	</form>
</div>
<?php }
	else {
?>
	<div id="error">Usted no tiene permitida esta acción</div>
<?php
	}
?>