<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}

	if($advertencia === '') {
		$advertencia = 'Los programas son usados para el listado de selección, editar o borrar programas no afecta los proyectos.';
	}
?>
<div id="programa_menu">
	<div id="plus">
		<a id="delete_prog" class="delete sombra">
			borrar
		</a>
		<a id="atras" class="edit sombra" href="index.php?proyectos">
			regresar
		</a>
		<form  id="programa" method="post" enctype="multipart/form-data">
			<i class="fas fa-plus-square"></i>
			<div id="add_region">Añadir</div>
			<input type="text" name="nombre" placeholder="programa..." required>
			<input type="hidden" name="programa_form" value="1">
			<input type="submit" value="guardar">
		</form>
	</div>
</div>
<div id="programa_table">
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
	<div id="programa-list">
		<div id="title-row">
			<div class="check"><input id="checkall" type="checkbox" name="programa-id-chk"></div>
			<div class="programa-id">ID</div>
			<div class="programa-title">Región</div>
			<div class="programa-edit"><i class="far fa-edit"></i></div>
		</div>
		<div id="content-row">
			<?php allProgramas(); ?>
		</div>
	</div>
</div>