<?php
session_start(); // INICIAR SESIÓN PARA MENSAJES DE CONFIRMACIÓN
define('tejido', TRUE); // PARA INCLUIR ARCHIVO DE CONFIGURACIÓN
include('../config/config.php');

if (array_key_exists("openarticulo", $_POST) AND $_POST['openarticulo'] === "1") { 
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$query = '
		SELECT 
		p.titulo AS titulo, p.objetivo AS objetivo, p.tipologia AS tipologia, p.instituciones AS instituciones, 
		pa.programas AS programas, pa.docente AS docente, pa.espacioacad AS espacio, pa.grupoinvest AS grupo, pa.beneficiarios AS beneficiarios,
		pdt.fecha AS fecha, pdt.foto AS foto, pdt.proyectid AS id, pdt.fechafinal AS duracion, 
		pr.municipio AS municipio, pr.cau AS cau, pr.region AS region,
		f.folder AS folder, f.nombre AS regfolder, f.foto AS catimg
		FROM `proy_datostec` pdt
		INNER JOIN `proyecto` p ON pdt.proyectid = p.proyectid
		INNER JOIN `proy_academicos` pa ON pdt.proyectid = pa.proyectid
		INNER JOIN `proy_regionales` pr ON pdt.proyectid = pr.proyectid
		INNER JOIN `region` f ON pr.region = f.nombre
		WHERE pdt.proyectid = :id
	';
	$sql = $con->prepare($query);
	$id = $_POST['id'];
	$sql->bindValue(':id', $id);
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);
	$folder = substr($results->folder, 3);
}

?>
<div id="close" onclick="closeproy()"><i class="far fa-window-close"></i></div>
<div id="proytit"><?php echo $results->titulo; ?></div>
<div id="flexrow">
	<div id="fechas"><b>Inicio:</b> <?php echo $results->fecha ?> <b>Finalización:</b> <?php echo $results->duracion ?></div>
	<div id="lider"><b>Docente lider:</b> <?php echo $results->docente ?></div>
</div>
<div id="flexrow">
	<div id="cau"><b>CAU:</b> <?php echo $results->cau ?></div>
	<div id="municipio"><b>Municipio:</b> <?php echo $results->municipio ?></div>
</div>
<div id="objetivo"><span class="big"><b>OBJETIVO:</b> </span><?php echo $results->objetivo; ?></div>
<div id="datarow">
	<div id="programas"><b>Programas participantes:</b> <?php echo $results->programas ?>	</div>
	<div id="academico"><b>Espacio académico:</b> <?php echo $results->espacio ?></div>
	<div id="investigacion"><b>Lineas de Investigación:</b> <?php echo $results->grupo ?></div>
	<div id="proysoc"><b>Estrategia de proyección Social:</b> <?php echo $results->tipologia ?></div>
	<?php 
		if($results->instituciones !== ' ') {
	?>
		<div id="instituto">Instituciones externas participantes: <?php echo $results->instituciones ?></div>
	<?php
		}
	?>
	<div id="beneficiarios"><b>Beneficiarios:</b> <?php echo $results->beneficiarios ?></div>
</div>
<?php 
	if($results->foto !== ' ') {
?>
	<div id="foto"><img src="<?php echo $folder.'/'.$results->foto ?>"></div>
<?php
	} else {
?>
	<div id="foto"><img src="<?php echo $folder.'/'.$results->catimg ?>"></div>
<?php
	}
?>