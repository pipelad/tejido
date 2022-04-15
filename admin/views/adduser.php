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
	$privilegio = false;
	if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin') {
		$privilegio = true;
	}

	if($privilegio === true) {
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
	<form id="add_article" name="add_user" method="post" class="new">
		<div id="form-title"><i class="fas fa-plus-square"></i> Nuevo</div>
		<div id="form-data">
			<div id="labels-user">
					<span><label for="name">Nomber y Apellido</label><input required type="text" name="name" placeholder="Ej. Andrés Restrepo" value="<?php if(isset($_POST['name']) AND $_POST['name'] !== "") { echo($_POST['name']); } ?>"></span>
					<span><label for="username">Nombre de Usuario</label><input required type="text" name="username" placeholder="Ej. andres.restrepo minimo 8 máximo 15 letras" minlength="8" maxlength="15" value="<?php if(isset($_POST['username']) AND $_POST['username'] !== "") { echo($_POST['username']); } ?>"></span>
					<span><label for="tipo">Tipo de documento</label>
					<select name="tipo">
						<option selected value="cc">cédula de ciudadanía</option>
						<option value="ce">Cédula de extranjería</option>
					</select>
					</span>
					<span><label for="documento">Numero de documento</label><input required type="text" name="documento" placeholder="Solo números" maxlength="15" pattern="\d*" value="<?php if(isset($_POST['documento']) AND $_POST['documento'] !== "") { echo($_POST['documento']); } ?>"></span>
					<span><label for="userpasword">Contraseña</label><input required type="password" name="userpasword"></span>
					<span><label for="usergroup">Grupo de usuario</label>
					<select name="usergroup" required>
						<option selected hidden disabled value="">Seleccione uno</option>
						<?php if($results->usergroup === 'SuperAdmin') {
							echo '<option value="SuperAdmin">Super Administrador</option>';
						} ?>
						<?php if($results->usergroup === 'SuperAdmin' or $results->usergroup === 'Admin') {
							echo '<option value="Admin">Administrador</option>';
						} ?>
						<option value="Revisor">Revisor</option>
					</select>
					</span>
			</div>
		</div>
		<button class="add" name="new_user" value="1"><i class="far fa-save"></i> GUARDAR</button>
		<a class="cancel" href="index.php?user"><i class="far fa-window-close"></i> CANCELAR</a>
		<br clear="all">
	</form>
</div>
<?php }
else {
?>

<?php
}
?>