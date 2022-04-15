<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}
?>
<div id="proyecto_menu">
	<div id="plus">
		<a class="new sombra" href="index.php?nuevo_proy">
			<i class="fas fa-plus-circle"></i> Nuevo proyecto
		</a>
		<a id="delete_proy" class="delete sombra">
			borrar
		</a>
		<a id="atras" class="edit sombra" href="index.php">
			regresar
		</a>
		<a class="config sombra" href="index.php?region">
			<i class="fas fa-globe-americas"></i> configurar Departamentos
		</a>
		<a class="config sombra" href="index.php?programa">
			<i class="fas fa-graduation-cap"></i> configurar programas
		</a>
	</div>
</div>
<div id="proyectFilter">
	<form method="post" name="filters">
		<label for="title_filter">Filtrar por título:</label>
		<input type="text" name="title_filter" placeholder="Filtrar por título..." 
			<?php 
				if(isset($_SESSION['title_filter']) AND $_SESSION['title_filter'] !== "") {
					echo 'value="'.$_SESSION['title_filter'].'"'; 
				} else if(isset($_POST['title_filter']) AND $_POST['title_filter'] !== "") { 
					echo 'value="'.$_POST['title_filter'].'"'; 
				} 
			?>
		>
		<label for="region_filter">Departamento:</label>
		<select name="region_filter" id="region_filter">
			<option selected hidden disabled value=""> </option>
			<?php allRegionSelect(); ?>
		</select>
		<label for="year_filter">Año:</label>
		<select name="year_filter" id="year_filter">
			<option selected hidden disabled value=""> </option>
			<?php allYears(); ?>
		</select>
		<label for="limite"># por página</label>
		<select name="limite" id="limite">
			<option selected hidden disabled value=""> </option>
			<?php limitSelect(); ?>
		</select>
		<input type="submit" value="Filtrar" name="filtrar">
	</form>
	<form method="post" name="clearfilters" id="limiter-form">
		<input type="submit" name="limpiar" value="Quitar Filtros">
	</form>
</div>
<div id="proyecto_table">
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
			<div class="check"><input id="checkall" type="checkbox" name="proyecto-id-chk"></div>
			<div class="proy-id">ID</div>
			<div class="proy-title">Título</div>
			<div class="proy-region">Departamento</div>
			<div class="proy-fecha">Fecha finalización</div>
			<div class="proy-grupo"># Beneficiados</div>
			<div class="proy-foto"><i class="far fa-images"></i></div>
		</div>
		<div id="content-row">
			<?php  alllProyectos(); ?>
		</div>
	</div>
</div>