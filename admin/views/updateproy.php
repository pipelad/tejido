<?php
	
	if(!defined('tejido')) {
	   header("Location: index.php");
	}
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$query = '
		SELECT 
		p.titulo AS titulo, p.objetivo AS objetivo, p.tipologia AS tipologia, p.instituciones AS instituciones, 
		pa.programas AS programas, pa.docente AS docente, pa.espacioacad AS espacio, pa.grupoinvest AS grupo, pa.beneficiarios AS beneficiarios,
		pdt.fecha AS fecha, pdt.foto AS foto, pdt.proyectid AS id, pdt.fechafinal AS duracion, 
		pr.municipio AS municipio, pr.cau AS cau, pr.region AS region,
		f.folder AS folder, f.nombre AS regfolder
		FROM `proy_datostec` pdt
		INNER JOIN `proyecto` p ON pdt.proyectid = p.proyectid
		INNER JOIN `proy_academicos` pa ON pdt.proyectid = pa.proyectid
		INNER JOIN `proy_regionales` pr ON pdt.proyectid = pr.proyectid
		INNER JOIN `region` f ON pr.region = f.nombre
		WHERE pdt.proyectid = :id
	';
	$sql = $con->prepare($query);
	$id = $_GET['update_proy'];
	$sql->bindValue(':id', $id);
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);
	$faltante = false;
	// $sql->debugDumpParams();
?>
<div id="add">
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
	<form id="add_article" name="new_proyecto" method="post" class="edit proyocho" enctype="multipart/form-data">
		<div id="form-title"><i class="fas fa-plus-square"></i> Editar</div>
		<div id="form-data">
			<div  id="add_proyecto" class="row-single first">
				<label for="proy_titulo" class="grey">* Título de la experiencia:</label>
				<input type="text" name="proy_titulo" required <?php echo 'value="'.$results->titulo.'"'; ?>>
				<label for="proy_objetivo" class="grey">* Objetivo:</label>
				<input type="text" name="proy_objetivo" required <?php echo 'value="'.$results->objetivo.'"'; ?>>
			</div>
			<div class="row-layer">
				<label for="proy_programas">* Programa o prográmas académicos (seleccione al menos uno, puede seleccionar varios)</label>
				<div class="check-row" id="uprogschk">
					<?php allProgsCheck(); ?>
					<div class="sent-progdata"></div>
					<script type="text/javascript">
						let progsArray = '<?php echo $results->programas ?>'.split(', ');
					</script>
				</div>
				<div id="otro">
					Si no encuentra el programa agréguelo <a href="index.php?programa" >acá</a>
					<div id="faltante_msg" class="hid">hola</div>
				</div>
			</div>
			<div class="row-single">
				<label for="cau" class="grey">* CAU:</label>
				<input type="text" name="cau" required <?php echo 'value="'.$results->cau.'"'; ?>>
				<label for="municipio" class="grey">*Municipio:</label>
				<input type="text" name="municipio" required <?php echo 'value="'.$results->municipio.'"'; ?>>
			</div>
			<div class="row-double">
				<div id="date-piker">
					<label for="fecha" class="grey">* Fecha de inicio:</label>
					<div id="fechainputs">
						<?php 
							$fechaRes = explode('-', $results->fecha);
							echo '<input type="text" name="year" class="year" placeholder="-aaaa-" minlength="4"  maxlength="4" pattern="\d*" required value="'.$fechaRes[0].'">';
							echo '<input type="text" name="month" class="month" placeholder="-mm-" minlength="2"  maxlength="2" pattern="\d*" required value="'.$fechaRes[1].'">';
							echo '<input type="text" name="day" class="day" placeholder="-dd-" minlength="2"  maxlength="2" pattern="\d*" required value="'.$fechaRes[2].'">';
						?>
					</div>
				</div>
				<div id="date-piker">
					<label for="fecha" class="grey">* Fecha de Finalización:</label>
					<div id="fechainputs">
						<?php 
							$fechaRes = explode('-', $results->duracion);
							echo '<input type="text" name="yearfinal" class="year" placeholder="-aaaa-" minlength="4"  maxlength="4" pattern="\d*" required value="'.$fechaRes[0].'">';
							echo '<input type="text" name="monthfinal" class="month" placeholder="-mm-" minlength="2"  maxlength="2" pattern="\d*" required value="'.$fechaRes[1].'">';
							echo '<input type="text" name="dayfinal" class="day" placeholder="-dd-" minlength="2"  maxlength="2" pattern="\d*" required value="'.$fechaRes[2].'">';
						?>
					</div>
				</div>
			</div>
			<div class="row-single">
				<label for="docente" class="grey">* Docente Líder:</label>
				<input type="text" name="docente" required <?php echo 'value="'.$results->docente.'"'; ?>>
				<label for="e-academico" class="grey">* Espacio académico:</label>
				<input type="text" name="e-academico" required <?php echo 'value="'.$results->espacio.'"'; ?>>
				<label for="g-investiga" class="grey">* Línea de investigación:</label>
				<input type="text" name="g-investiga" required <?php echo 'value="'.$results->grupo.'"'; ?>>
				<label for="tipologias" class="grey">* Estrategia de proyección Social:</label> 
				<input type="text" name="tipologias" required <?php echo 'value="'.$results->tipologia.'"'; ?>>
				<label for="externos" class="grey">Instituciones externas participantes</label>
				<input type="text" name="externos" <?php echo 'value="'.$results->instituciones.'"'; ?>>
				<label for="beneficiarios" class="grey">* Número de beneficiarios:</label>
				<input type="text" name="beneficiarios" pattern="\d*" required <?php echo 'value="'.$results->beneficiarios.'"'; ?>>
			</div>
			<div class="row-single-blue">
				<label for="region">* Departamento:</label>
				<select id="reg-selec" name="region" required>
					<option selected hidden disabled value="">--Seleccione una--</option>
					<?php allRegionSelect(); ?>
				</select>
				<div class="sent-regdata"></div>
				<script type="text/javascript">
					let selectRegion = '<?php echo $results->region ?>';
				</script>
			</div>

			<?php 
				/// Si hay foto no permitir subir nueva hasta borrar la anterior
				if($results->foto !== '') {
			?>
			
					<div class="last-row">
						<img src="<?php echo $results->folder.'/'.$results->foto; ?>">
						<span id="imgurl" title="<?php echo $results->id ?>"><?php echo $results->folder.'/'.$results->foto; ?></span>
						<div id="dele_foto">
							Si desea actualizar la foto actual, primero borre la anterior.<br>
							<a class="cancel left-cancel"><i class="fas fa-trash-alt"></i> borrar la foto</a>
						</div>
					</div>

			<?php 
				} else {
			?>

				<div class="row-single-blue">
					<label for="foto">Subir una foto </label><input id="fileupload" type="file" name="foto" accept="image/*">
				</div>

			<?php 
				}
			?>


		</div>
		<button id="up_proy" type="submit" class="edit" name="edit_proy" value="<?php echo $_GET['update_proy']; ?>"><i class="far fa-save"></i> ACTUALIZAR</button>
		<a class="cancel" href="index.php?proyectos"><i class="far fa-window-close"></i> CANCELAR</a> 
		<br clear="all">
	</form>
</div>
