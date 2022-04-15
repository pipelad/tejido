<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}

?>
<div id="region_menu">
	<div id="plus">
		<a id="delete_reg" class="delete sombra">
			borrar
		</a>
		<a id="atras" class="edit sombra" href="index.php?proyectos">
			regresar
		</a>
		<form  id="region" method="post" enctype="multipart/form-data">
			<i class="fas fa-plus-square"></i>
			<div id="add_region">Añadir</div>
			<input type="text" name="nombre" placeholder="nombre del departamento" required>
			<label for="foto">Subir una foto </label><input id="fileupload" type="file" name="foto" accept="image/*" required>
			<input type="hidden" name="region_form" value="1">
			<input type="submit" value="guardar">
		</form>
	</div>
</div>
<div id="region_table">
	<div id="foto-preview" class="hid">
	</div>
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
	<div id="proyecto-list">
		<div id="title-row">
			<div class="check"><input id="checkall" type="checkbox" name="region-id-chk"></div>
			<div class="region-id">ID</div>
			<div class="region-title">Departamento</div>
			<div class="region-foto"><i class="far fa-images"></i></div>
		</div>
		<div id="content-row">
			<?php  allRegions(); ?>
		</div>
	</div>
</div>