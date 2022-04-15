<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}
?>
<div id="articulo_menu">
	<div id="plus">
		<a class="new sombra" href="index.php?nuevo_art">
			<i class="fas fa-plus-circle"></i> Nuevo
		</a>
		<a id="delete_art" class="delete sombra">
			borrar
		</a>
		<a id="edit_art" class="edit sombra" href="index.php">
			regresar
		</a>
	</div>
</div>
<div id="articulos_table">
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
	<div id="article-list">
		<div id="title-row">
			<div class="check"><input id="checkall" type="checkbox" name="article-id-chk"></div>
			<div class="art-id">ID</div>
			<div class="art-title">Título</div>
			<div class="art-autor">Autor</div>
		</div>
		<div id="content-row">
			<?php allarticles(); ?>
		</div>
	</div>
</div>