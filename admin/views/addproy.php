<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT `nombrecompleto` FROM `usuarios` WHERE id = :userid LIMIT 1');
	$id = $_SESSION['tejidoadmin'];
	$sql->bindValue(':userid', $id);
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);
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
	<form id="add_article" name="new_proyecto" method="post" class="new proyocho" enctype="multipart/form-data">
		<div id="form-title"><i class="fas fa-plus-square"></i> Nuevo</div>
		<div id="form-data">
			<div  id="add_proyecto" class="row-single first">
				<label for="proy_titulo" class="grey">* Título de la experiencia:</label>
				<input type="text" name="proy_titulo" required <?php if(isset($_POST['proy_titulo']) AND $_POST['proy_titulo'] !== "") { echo 'value="'.$_POST['proy_titulo'].'"'; } ?>>
				<label for="proy_objetivo" class="grey">* Objetivo:</label>
				<input type="text" name="proy_objetivo" required <?php if(isset($_POST['proy_objetivo']) AND $_POST['proy_objetivo'] !== "") { echo 'value="'.$_POST['proy_objetivo'].'"'; } ?>>
			</div>
			<div class="row-layer">
				<label for="proy_programas">* Programa o prográmas académicos (seleccione al menos uno, puede seleccionar varios)</label>
				<div class="check-row">
					<?php allProgsCheck(); ?>
				</div>
				<div id="otro">
					<label for="otro">Si no encuentra el programa agréguelo acá: </label>
					<input type="text" name="otro" <?php if(isset($_POST['otro']) AND $_POST['otro'] !== "") { echo 'value="'.$_POST['otro'].'"'; } ?>>
					<a href="index.php?programa" >Si va a agregar más de uno hágalo acá</a>
				</div>
			</div>
			<div class="row-single">
				<label for="cau" class="grey">* CAU:</label>
				<input type="text" name="cau" required <?php if(isset($_POST['cau']) AND $_POST['cau'] !== "") { echo 'value="'.$_POST['cau'].'"'; } ?>>
				<label for="municipio" class="grey">*Municipio:</label>
				<input type="text" name="municipio" required <?php if(isset($_POST['municipio']) AND $_POST['municipio'] !== "") { echo 'value="'.$_POST['municipio'].'"'; } ?>>
			</div>
			<div class="row-double">
				<div id="date-piker">
					<label for="fecha" class="grey">* Fecha de inicio:</label>
					<div id="fechainputs">
						<input type="text" name="year" class="year" placeholder="-aaaa-" minlength="4"  maxlength="4" pattern="\d*" required <?php if(isset($_POST['year']) AND $_POST['year'] !== "") { echo 'value="'.$_POST['year'].'"'; } ?>>
						<input type="text" name="month" class="month" placeholder="-mm-" minlength="2"  maxlength="2" pattern="\d*" required <?php if(isset($_POST['month']) AND $_POST['month'] !== "") { echo 'value="'.$_POST['month'].'"'; } ?>>
						<input type="text" name="day" class="day" placeholder="-dd-" minlength="2"  maxlength="2" pattern="\d*" required <?php if(isset($_POST['day']) AND $_POST['day'] !== "") { echo 'value="'.$_POST['day'].'"'; } ?>>
					</div>
				</div>
				<div id="date-piker">
					<label for="fecha" class="grey">* Fecha de Finalización:</label>
					<div id="fechainputs">
						<input type="text" name="yearfinal" class="year" placeholder="-aaaa-" minlength="4"  maxlength="4" pattern="\d*" required <?php if(isset($_POST['yearfinal']) AND $_POST['yearfinal'] !== "") { echo 'value="'.$_POST['yearfinal'].'"'; } ?>>
						<input type="text" name="monthfinal" class="month" placeholder="-mm-" minlength="2"  maxlength="2" pattern="\d*" required <?php if(isset($_POST['monthfinal']) AND $_POST['monthfinal'] !== "") { echo 'value="'.$_POST['monthfinal'].'"'; } ?>>
						<input type="text" name="dayfinal" class="day" placeholder="-dd-" minlength="2"  maxlength="2" pattern="\d*" required <?php if(isset($_POST['dayfinal']) AND $_POST['dayfinal'] !== "") { echo 'value="'.$_POST['dayfinal'].'"'; } ?>>
					</div>
				</div>
			</div>
			<div class="row-single">
				<label for="docente" class="grey">* Docente Líder:</label>
				<input type="text" name="docente" required <?php if(isset($_POST['docente']) AND $_POST['docente'] !== "") { echo 'value="'.$_POST['docente'].'"'; } ?>>
				<label for="e-academico" class="grey">* Espacio académico:</label>
				<input type="text" name="e-academico" required <?php if(isset($_POST['e-academico']) AND $_POST['e-academico'] !== "") { echo 'value="'.$_POST['e-academico'].'"'; } ?>>
				<label for="g-investiga" class="grey">* Línea de investigación:</label>
				<input type="text" name="g-investiga" required <?php if(isset($_POST['g-investiga']) AND $_POST['g-investiga'] !== "") { echo 'value="'.$_POST['g-investiga'].'"'; } ?>>
				<label for="tipologias" class="grey">* Estrategia de proyección Social:</label> 
				<input type="text" name="tipologias" required <?php if(isset($_POST['tipologias']) AND $_POST['tipologias'] !== "") { echo 'value="'.$_POST['tipologias'].'"'; } ?>>
				<label for="externos" class="grey">Instituciones externas participantes</label>
				<input type="text" name="externos" <?php if(isset($_POST['externos']) AND $_POST['externos'] !== "") { echo 'value="'.$_POST['externos'].'"'; } ?>>
				<label for="beneficiarios" class="grey">* Número de beneficiarios:</label>
				<input type="text" name="beneficiarios" pattern="\d*" required <?php if(isset($_POST['beneficiarios']) AND $_POST['beneficiarios'] !== "") { echo 'value="'.$_POST['beneficiarios'].'"'; } ?>>
			</div>
			<div class="row-single-blue">
				<label for="region">* Departamento:</label>
				<select id="reg-selec" name="region" required>
					<option selected hidden disabled value="">--Seleccione una--</option>
					<?php allRegionSelect(); ?>
				</select>
			</div>
			<div class="row-single-blue">
				<label for="foto">Subir una foto </label><input id="fileupload" type="file" name="foto" accept="image/*">
			</div>
		</div>
		<button type="submit" class="add" name="new_proyecto" value="1"><i class="far fa-save"></i> GUARDAR</button> 
		<a class="cancel" href="index.php?proyectos"><i class="far fa-window-close"></i> CANCELAR</a> 
		<br clear="all">
		<?php 
			if(isset($_POST['programa']) AND $_POST['programa'] !== "") { 
				echo '<div class="hid sent-progdata"></div>';
				$max = sizeof($_POST['programa']);
				echo '<script type="text/javascript">';
				echo 'let progsArray = []; ';
				for($i = 0; $i < $max;$i++) {
					echo 'progsArray['.$i.'] = "'.$_POST['programa'][$i].'"; ';
				}
				echo '</script>';
			}
			if(isset($_POST['region']) AND $_POST['region'] !== "") {
				echo '<div class="hid sent-regdata"></div>';
				echo '<script type="text/javascript">';
				echo 'let selectRegion = "'.$_POST['region'].'";';
				echo '</script>';
			}

		?>
	</form>
</div>
