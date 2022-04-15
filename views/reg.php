<?php
	//print_r($_GET);
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sqlReg = $con->prepare('SELECT * FROM `region` WHERE `id` = :id');
	$id = $_GET['dept'];
	$sqlReg->bindValue('id', $id);
	$sqlReg->execute();
	$depto = $sqlReg->fetch(PDO::FETCH_OBJ);
	$currentDept = $depto->nombre;
	$catImg = $depto->foto;
	$catFolder = substr($depto->folder, 3);
	$sqlProys = $con->prepare('
		SELECT pr.region AS departamento, pt.fechafinal AS fecha
		FROM `proy_regionales` pr
		INNER JOIN `proy_datostec` pt ON pr.proyectid = pt.proyectid
		WHERE pr.region = :depto
		ORDER BY pt.fechafinal DESC
 		');
	$sqlProys->bindValue(':depto', $currentDept);
	$sqlProys->execute();
	$years = [];
	$i = 0;
	while($proyecto = $sqlProys->fetch(PDO::FETCH_OBJ)) {
		$yearArr = explode("-", $proyecto->fecha);
		$sYear = $yearArr[0];
		if(!in_array($sYear, $years)) {
			$years[$i] = $sYear;
			$i++;
		}
	}
	//print_r($years);
	$count = count($years)

?>
<div id="deptowrap">
	<div id="reopener" onclick="reopenlist()"><i class="fas fa-angle-right"></i></div>
	<div id="menu_reg_years" class="sombra">
		<div id="flexcontainer">
			<div id="regsubmenu">
				<div id="navigation">
					<div id="bk" onclick="closelist()"><i class="fas fa-camera"></i></div>
					<div id="home"><a href="index.php"><i class="fas fa-home"></i></a></div>
				</div>
				<div class="btn">
					<i class="far fa-folder-open"></i>
				</div>
			</div>
			<div id="yearlist">
				<div id="submenu">
					<div id="subtitle">PROYECTOS POR AÑOS:</div>
				</div>
				<?php
					for($b = 0; $b < $count; $b++) {
						echo '<div class="year">';// principio del área
						echo '<div id="'.$years[$b].'_btn" class="yearbtn" onclick="openYear(\''.$years[$b].'_cont\')">'.$years[$b].'<div class="yerbtn"><i class="fas fa-clipboard-list"></i></div></div>';
						echo '<div id="'.$years[$b].'_cont" class="yearcont">';

						$sqlYearProy = $con->prepare('
							SELECT pr.region AS departamento, pt.fechafinal AS fecha, p.titulo AS titulo, pt.proyectid as id
							FROM `proy_regionales` pr
							INNER JOIN `proyecto` p ON pr.proyectid = p.proyectid
							INNER JOIN `proy_datostec` pt ON pr.proyectid = pt.proyectid
							WHERE pr.region = :depto AND pt.fechafinal LIKE :year
							ORDER BY pt.fechafinal DESC
					 		');
						$curYear = $years[$b].'%';
						$sqlYearProy->execute(array(':depto' => $currentDept, ':year' => $curYear));
						//$sqlYearProy->debugDumpParams();
						while ($proyectos = $sqlYearProy->fetch(PDO::FETCH_OBJ)) {
							echo '<div onclick="openproy(\''.$proyectos->id.'\')"><i class="far fa-file-alt"></i> '.$proyectos->titulo.'</div>';
						} 

						
						echo '</div>';
						echo '</div>';// fin del área
					}
				?>
			</div>
		</div>
	</div>

	<div id="proyoverlay" class="hidden" onclick="closeproy()">
		<div id="proyecontenido">
		</div>
	</div>
	<div id="departamento">
		<?php
			echo '<div id="deptbg"><div id="deptit">'.$currentDept.'</div>';
			echo '<img src="'.$catFolder.'/'.$catImg.'"></div>';
		?>
	</div>
</div>