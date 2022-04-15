<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}

	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT * FROM `region` WHERE id = :artid LIMIT 1');
	$id = $_GET['update_region'];
	$sql->bindValue(':artid', $id);
	$sql->execute();
	if($results = $sql->fetch(PDO::FETCH_OBJ)) {
		if($advertencia === '') {
			$advertencia = 'Cambios acá afectan los proyectos relacionados, si sube una nueva foto la anterior será borrada.';
		}
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
	<form id="u_region" name="new_article" method="post" class="edit" enctype="multipart/form-data">
		<div id="form-title"><i class="fas fa-plus-square"></i> Editar</div>
		<div id="form-data">
			<div id="labels">
				<span><label for="title">Departamento: </label><input type="text" name="title" value="<?php echo $results->nombre ?>" required></span>
				<div id="reg_foto_prev">Foto actual: <?php echo $results->foto ?><br><img src="<?php echo $results->folder.'/'.$results->foto ?>"></div>
				<span><label for="foto">Subir una foto </label><input id="fileupload" type="file" name="foto" accept="image/*"></span>
			</div>
		</div>
		<button 
			onclick="return confirm('El cambio que desea hacer afecta otras áreas de la aplicación. ¿Está seguro de continuar?')" 
			class="edit" 
			name="update_region" 
			value="<?php echo $_GET['update_region']; ?>">
			<i class="far fa-save"></i> ACTUALIZAR
		</button>
		<a class="cancel" href="index.php?region"><i class="far fa-window-close"></i> CANCELAR</a>
		<br clear="all">
	</form>
</div>


<?php
} else {
	$error = 'La Región que intenta editar no existe';
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
		<a class="cancel" href="index.php?region"><i class="far fa-window-close"></i> REGRESAR</a>
	</div>
<?php
}
?>