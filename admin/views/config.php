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
		$query = $con->prepare('SELECT * FROM `config` WHERE `id` = 1');
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
	<form id="add_article" name="update_user" method="post" class="edit" enctype="multipart/form-data">
		<div id="form-title"><i class="fas fa-wrench"></i> Configurar</div>
		<div id="form-data">
			<div id="labels-user">
				<span><label for="name">Título</label><input required type="text" name="name" value="<?php echo $row->titulo ?>"></span>
				<span>
					<label for="name">Contacto</label>
					<textarea name="content" class="hid"><?php echo $row->contacto ?></textarea>
				</span>
				<?php
					if($row->logo !== '') {
					 	echo '<span class="logo">';
					 	echo '<div id="url">'.$row->logo.'</div>';
						echo '<img src="../'.$row->logo.'">';
						echo '</span>';
						echo '<div id="dele_logo">Si desea actualizar el logo actual, primero borre el anterior.<br><a class="cancel left-cancel" id="dlbot"><i class="fas fa-trash-alt"></i> borrar la foto</a></div>';
					} else {
						echo '<span><label for="logo">Subir un logo </label><input id="fileupload" type="file" name="logo" accept="image/*"></span>	';
					}
				?>
			</div>
		</div>
		<button class="edit" name="update_config" value="1"><i class="far fa-save"></i> ACTUALIZAR</button>
		<a class="cancel" href="index.php"><i class="far fa-window-close"></i> CANCELAR</a>
		<br clear="all">
	</form>
	<script src="https://cdn.tiny.cloud/1/er6yiuwafgvsns3yzan7r4y7cddcnt5ufwx7oxt3u48f6hrx/tinymce/5/tinymce.min.js"></script>
	<script type="text/javascript">
		tinymce.init({
			selector: 'textarea',
			height: 350
		});
	</script>
</div>
<?php }
	else {
?>
	<div id="error">Usted no tiene permitida esta acción</div>
<?php
	}
?>