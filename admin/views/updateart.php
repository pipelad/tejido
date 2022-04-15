<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}

	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT * FROM `articulo` WHERE id = :userid LIMIT 1');
	$id = $_GET['update_articulo'];
	$sql->bindValue(':userid', $id);
	$sql->execute();
	if($results = $sql->fetch(PDO::FETCH_OBJ)) {

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
	<form id="add_article" name="actualizar_article" method="post" class="edit">
		<div id="form-title"><i class="fas fa-plus-square"></i> Editar</div>
		<div id="form-data">
			<div id="labels">
				<span><label for="title">Título</label><input type="text" name="title" value="<?php echo $results->titulo ?>" required></span>
				<span><label for="autor">Autor</label><input type="text" name="autor" required value="<?php echo $results->autor ?>"></span>
			</div>
			<label for="content">Contenido</label>
			<textarea name="content" class="hid"><?php echo $results->contenido ?></textarea>
		</div>
		<button class="edit" name="edit_article" value="<?php echo $_GET['update_articulo']; ?>"><i class="far fa-save"></i> ACTUALIZAR</button>
		<a class="cancel" href="index.php?articulos"><i class="far fa-window-close"></i> CANCELAR</a>
		<br clear="all">
	</form>
</div>
<script src="https://cdn.tiny.cloud/1/er6yiuwafgvsns3yzan7r4y7cddcnt5ufwx7oxt3u48f6hrx/tinymce/5/tinymce.min.js"></script>
<script type="text/javascript">
	tinymce.init({
		selector: 'textarea',
		height: 350
	});
</script>

<?php
} else {
	$error = 'El artículo que intenta editar no existe';
?>
	<?php
		if($error != "") {
			echo '<div id="error"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
		}

		if($advertencia != "") {
		  echo '<div id="advertencia"><i class="fas fa-exclamation-circle"></i> '.$advertencia.'</div>';
		}
	?>
	<div id="no-article">
		<a class="cancel" href="index.php?articulos"><i class="far fa-window-close"></i> REGRESAR</a>
	</div>
<?php
}
?>