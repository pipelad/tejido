<?php

if(!defined('tejido')) {
   header("Location: index.php");
}

$adminicon ="";
$error = "";
$advertencia = "";

//print_r($_POST);
//print_r($_SESSION);

//FUNCIONES ADMINISTRATIVAS

///////////////////////////////// FUNCIONES $_POST DEPENDANT

///////////////////////////////////////////////////////////////////////////////////////// Login
if (isset($_POST['log_form']) AND $_POST['log_form'] == "1") {
	if (isset($_POST['username']) AND $_POST['username'] != "") {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
		$sql = $con->prepare('SELECT * FROM `usuarios` WHERE `usuario` = :user');
		$user = $_POST['username'];
		$sql->bindValue(':user', $user);
		$sql->execute();
		//$sql->debugDumpParams();
		if ($row = $sql->fetch(PDO::FETCH_OBJ)) { 
			$hashedPassword = md5(md5($row->id).$_POST['password']);
			if ($hashedPassword == $row->password) {
				$_SESSION['tejidoadmin'] = $row->id;
			} else {
				$error = "El usuario y la contraseña no coinciden";
			}
		} else {
				$error = "Usuario o contraseña errada";
		}
	}
}


///////////////////////////////////////////////////////////////////////////////////////// Logout
if (array_key_exists("logout", $_GET)) {
	session_unset();
	session_destroy();
	$_SESSION = array();
	if (!headers_sent()) {    
	    header("Location: index.php");
	    exit;
	} else {  
	    echo '<script type="text/javascript">';
	    echo 'window.location.href="index.php";';
	    echo '</script>';
	    echo '<noscript>';
	    echo '<meta http-equiv="refresh" content="0;url=index.php" />';
	    echo '</noscript>'; exit;
	}
}
///////////////////////////////// ARTICLES 

if(array_key_exists("articulos", $_GET)) {
	

	function allarticles() {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		// $sql = $con->prepare('SELECT `id`, `titulo`, `autor` FROM `articulo`');
		// $sql->execute();
		$whereCount = "";

		$sqlCountlimit = 'SELECT * FROM `articulo`';
		if($whereCount != "") { $sqlCountlimit .= " WHERE ".$whereCount; }
		$sql = $con->prepare($sqlCountlimit);
		$sql->execute();
		$row_count = $sql->rowCount();
		if (!isset($_POST['limite']) AND !isset($_SESSION['limite'])) {
			$resultados_pagina = 10;
		} else if(isset($_POST['limite'])) {
			$_SESSION['limite'] = $_POST['limite'];
			$resultados_pagina = $_POST['limite'];
		} else if(isset($_SESSION['limite'])) {
			$resultados_pagina = $_SESSION['limite'];
		}
		// ultima página o total de páginas
		$numero_paginas = ceil($row_count/$resultados_pagina);
		
		// pagina actual o default
		if (!isset($_GET['pagina'])) {
			$pagina = 1;
		} else {
			$pagina = $_GET['pagina'];
		}
		// comienzo de links
		$start = (($pagina - 4) >0) ? $pagina - 4 : 1;
		$end = (($pagina + 4) < $numero_paginas) ? $pagina + 4 : $numero_paginas;
		// determinar el limite de SQL para paginacion
		$pagina_actual = ($pagina - 1) * $resultados_pagina;
		$limit = $pagina_actual.", ".$resultados_pagina;


		// Construir la string
		$sqlString = 'SELECT `id`, `titulo`, `autor` FROM `articulo`';

		$sqlString .= " ORDER BY `titulo` ASC";
		$sqlString .= " LIMIT ".$limit;			

		// Preparar la string
		$sql = $con->prepare($sqlString);
		$sql->execute();
		// DEBUG QUERY
		// $sql->debugDumpParams();

		while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
			echo '<div class="row">';
			echo '<div class="check"><input class="artcheck" type="checkbox" name="article-id-chk" id="'.$row->id.'" value="'.$row->id.'"></div>';
			echo '<div class="art-id">'.$row->id.'</div>';
			echo '<div class="art-title"><a href="index.php?update_articulo='.$row->id.'">'.$row->titulo.'</a></div>'; // AÑADIR LINK PARA EDITAR EL ARTICULO EN EL TITULO
			echo '<div class="art-autor">'.$row->autor.'</div>';
			echo '</div>';
		}

		//pagination advanced
		echo "<div id='pagination'>";
		if ($pagina == 1 && $numero_paginas > 1) {
			echo '<a class="disabled prev" href="">&laquo</a>';
		} else if($numero_paginas > 1) {
			$prevpage = $pagina - 1;
			echo '<a class="prev" href="?usuarios&pagina='.$prevpage.'">&laquo</a>';
		}
		if ($start > 1) {
			echo '<a href="?usuarios&pagina=1">1</a>';
			echo '<a class="disabled" href="">....</a>';
		}
		if ($numero_paginas > 1) {
			for ($pagelink=$start; $pagelink<=$end; $pagelink++) {
				if ($pagelink == $pagina) {
					$actual = ' class="current" ';
				} else {
					$actual = " ";
				}
				echo "<a".$actual."href='index.php?usuarios&pagina=".$pagelink."'> ".$pagelink." </a> ";
			}
		}
		if ($end < $numero_paginas) {
			echo '<a class="disabled" href="">....</a>';
			echo '<a href="?usuarios&pagina='.$numero_paginas.'">'.$numero_paginas.'</a>';
		}
		if ($pagina == $numero_paginas && $numero_paginas > 1) {
			echo '<a class="disabled next" href="">&raquo</a>';
		} else if($numero_paginas > 1) {
			$nextpage = $pagina + 1;
			echo '<a class="next" href="?usuarios&pagina='.$nextpage.'">&raquo</a>';
		}
		echo "</div>";
	}

}


///////////////////////////////////////////////////////////////////////////////////////// New article
if (isset($_POST['new_article']) AND $_POST['new_article'] === "1") {
	if(isset($_POST['content']) AND $_POST['content'] !== "") {
		/* if(strlen($_POST['content']) > 10) {
			SI NECESITAMOS QUE EL CONTENIDO TENGA UN NÚMERO MÍNIMO DE CARACTERES
		} */
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('INSERT INTO `articulo` (`titulo`, `contenido`, `autor`) VALUES (:titulo, :contenido, :autor) ');
		$titulo = $_POST['title'];
		$contenido = $_POST['content'];
		$autor = $_POST['autor'];
		if($sql->execute(array(':titulo' => $titulo, ':contenido' => $contenido, ':autor' => $autor))) {
			$_SESSION['advertencia'] = 'Artículo agregado correctamente';
			if (!headers_sent()) {    
			    header("Location: index.php?articulos");
			    exit;
			} else {  
			    echo '<script type="text/javascript">';
			    echo 'window.location.href="index.php?articulos";';
			    echo '</script>';
			    echo '<noscript>';
			    echo '<meta http-equiv="refresh" content="0;url=index.php?articulos" />';
			    echo '</noscript>'; exit;
			}

		} else {
			$error = 'se presentó un problema guardando el artículo en la base de datos, intente más tarde';
		}
	} else {
		$error = 'El contenido del artículo es obligatorio';
	}
}
///////////////////////////////////////////////////////////////////////////////////////// eddit article
if (isset($_POST['edit_article']) AND $_POST['edit_article'] !== "") { 
	if(isset($_POST['content']) AND $_POST['content'] !== "") { 
		//// variables para actualizar
		$id = $_POST['edit_article'];
		$titulo = $_POST['title'];
		$autor = $_POST['autor'];
		$contenido = $_POST['content'];

		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('UPDATE `articulo` SET `titulo` = :titulo WHERE id = :id LIMIT 1');
		if($sql->execute(array(':titulo' => $titulo, ':id' => $id))) {
			$sql = $con->prepare('UPDATE `articulo` SET `autor` = :autor WHERE id = :id LIMIT 1');
			if($sql->execute(array(':autor' => $autor, ':id' => $id))) {
				$sql = $con->prepare('UPDATE `articulo` SET `contenido` = :contenido WHERE id = :id LIMIT 1');
				if($sql->execute(array(':contenido' => $contenido, ':id' => $id))) { 
					$_SESSION['advertencia'] = 'Artículo actualizado correctamente';
					if (!headers_sent()) {    
					    header("Location: index.php?articulos");
					    exit;
					} else {  
					    echo '<script type="text/javascript">';
					    echo 'window.location.href="index.php?articulos";';
					    echo '</script>';
					    echo '<noscript>';
					    echo '<meta http-equiv="refresh" content="0;url=index.php?articulos" />';
					    echo '</noscript>'; exit;
					}
				} else {
					$error = 'Se presentó un error al guardar en la base de datos intente más tarde';
				}
			} else {
				$error = 'Se presentó un error al guardar en la base de datos intente más tarde';
			}
		} else {
			$error = 'Se presentó un error al guardar en la base de datos intente más tarde';
		}
	} else {
		$error = "El contenido del artículo es obligatorio";
	}
}

///////////////////////////////////////////////////////////////////////////////////////// REGIONES
if(array_key_exists("region", $_GET)) { 
	function allRegions() {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT * FROM `region`');
		$sql->execute();
		$row_count = $sql->rowCount();
		if($row_count === 0) {
			echo '<div class="row"> No hay Regiones configuradas aún. Agregue una región para comenzar</div>';
		} else {
			while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
				echo '<div class="row">';
				echo '<div class="check"><input class="itemcheck" type="checkbox" name="region-id-chk" id="'.$row->id.'" value="'.$row->id.'"></div>';
				echo '<div class="region-id">'.$row->id.'</div>';
				echo '<div class="region-title"><a href="index.php?update_region='.$row->id.'">'.$row->nombre.'</a></div>';
				echo '<div class="region-foto">'.$row->foto.' <a href="#" title="'.$row->folder.'/'.$row->foto.'" ><i class="far fa-eye"></i></a></div>';
				echo '</div>';
			}
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////// add region
if (isset($_POST['region_form']) AND $_POST['region_form'] === "1") { 
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('INSERT INTO `region` (`nombre`, `foto`, `folder`) VALUES (:nombre, :foto, :folder)');
	$nombre = $_POST['nombre'];
	$foto = '';
	//////// Construir folder de fotos para las regiones
	$folderName = '../img/';
	$folderName .= strtolower(stripChars($nombre));
	if (!file_exists($folderName)) {
	    mkdir($folderName, 0777, true);
	}
	//////// Construir index.html para evitar accesos extraños al folder
	$indexHtml = $folderName.'/index.html';
	if(!file_exists($indexHtml)) {
		fileHtml($indexHtml);
	}
	//////// definir la ruta para la foto subida.
	if (array_key_exists("foto", $_FILES) AND $_FILES['foto']['name'] != "") {
		//// efectivamente se está subiendo una foto
		if ($_FILES["foto"]["error"] > 0) {
			// si hay un error subiendo el archivo
			$error = "Se presentó un error con la foto ".$_FILES["foto"]["error"].".";
		} 
		else if($_FILES["foto"]["size"] > 1500000) {
			$error = "La foto supera el límite máximo de 1.5 MB, corrija esto antes de continuar.";
		}
		else {
				// check if file already exit in "images" folder.
			if (file_exists($folderName."/".$_FILES["foto"]["name"])) {
				$error = "El archivo ".$_FILES["foto"]["name"]." ya existe.";
			} else {
				//move_uploaded_file guarda la imagen permanentemente en el lugar adecuado.
				if(move_uploaded_file($_FILES["foto"]["tmp_name"], $folderName."/".$_FILES["foto"]["name"])) {
					// If file has uploaded successfully, store its name in data base
					$foto = $_FILES["foto"]["name"];
				} else {
					$error = "Se presentó un error al subir el archivo, contáctese con el administrador, o inténtelo desde el administrador de la plataforma.";
				}
			}
		}
	}
	//////// una vez suba la foto ejecutar la query
	if($foto !== "") {
		if ($sql->execute(array(':nombre' => $nombre, ':foto' => $foto, ':folder' => $folderName))) {
			$advertencia = 'Región agregada exitosamente';
		} else {
			$error = 'La Región no pudo ser agregada, intente más tarde';
		}
	}

}

///////////////////////////////////////////////////////////////////////////////////////// eddit article
if (isset($_POST['update_region']) AND $_POST['update_region'] !== "") { 
	$id = $_POST['update_region'];
	$foto = '';
	$nombre = $_POST['title'];

	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	/// ---> NOMBRE DE LA REGIÓN
	$sql = $con->prepare('SELECT `nombre`  FROM `region` WHERE `id` = :id');
	$sql->bindValue(':id', $id);
	$sql->execute(); 
	/// 1. verificar si el nombre es el mismo que está guardado
	if($result = $sql->fetch(PDO::FETCH_OBJ)) {
		if($result->nombre !== $nombre) {
			/// 2. si no es el mismo actualizar
			$sql = $con->prepare('UPDATE `region` SET `nombre` = :nombre WHERE `id` = :id LIMIT 1');
			if($sql->execute(array(':nombre' => $nombre, ':id' => $id))) {
				$advertencia = 'Region actualizada correctamente';
			} else {
				$error = 'Se presentó un problema al actualizar la Región';
			}
		} 
	}
	/// ---> IMAGEN
	/// 1. verificar si hay imagen en el array
	$sql = $con->prepare('SELECT `foto`, `folder` FROM `region` WHERE `id` = :id');
	$sql->bindValue(':id', $id);
	$sql->execute();
	if($result = $sql->fetch(PDO::FETCH_OBJ)) {
		/// 1.1 verificar que no sea la misma imagen, o que no tenga el mismo nombre de archivo
		/// -> guardar la foto anterior para poder borrar el archivo anterior. ////////////////
		$oldFoto = $result->foto;
		if($result->foto !== $foto) {
			/// 2. subir la imagen
			//////// definir la ruta para la foto subida.
			if (array_key_exists("foto", $_FILES) AND $_FILES['foto']['name'] != "") {
				//// efectivamente se está subiendo una foto
				if ($_FILES["foto"]["error"] > 0) {
					// si hay un error subiendo el archivo
					$error = "Se presentó un error con la foto ".$_FILES["foto"]["error"].".";
				} 
				else if($_FILES["foto"]["size"] > 1500000) {
					$error = "La foto supera el límite máximo de 1.5 MB, corrija esto antes de continuar.";
				}
				else {
					// check if file already exit in "images" folder.
					if (file_exists($result->folder."/".$_FILES["foto"]["name"])) {
						$error = "El archivo ".$_FILES["foto"]["name"]." ya existe. Verifique";
					} else {
						//move_uploaded_file guarda la imagen permanentemente en el lugar adecuado.
						if(move_uploaded_file($_FILES["foto"]["tmp_name"], $result->folder."/".$_FILES["foto"]["name"])) {
							// If file has uploaded successfully, store its name in data base
							$foto = $_FILES["foto"]["name"];
						} else {
							$error = "Se presentó un error al subir el archivo, contáctese con el administrador, o inténtelo desde el administrador de la plataforma.";
						}
					}
				}
			}
			/// 2.1 actualizar en la base de datos 
			if($foto !== '') {
				$sql = $con->prepare('UPDATE `region` SET `foto` = :foto WHERE `id` = :id LIMIT 1');
				if($sql->execute(array(':foto' => $foto, ':id' => $id))) {
					/// 2.2 borrar la imagen anterior
					if(file_exists($result->folder."/".$oldFoto)) {
						unlink($result->folder."/".$oldFoto);
					}
					$advertencia = 'Region actualizada correctamente';
				} else {
					$error = 'Se presentó un problema al actualizar la Región';
				}
			}
			

		} else {
			$error = 'Está intentando subir una imagen con el mismo nombre, verifique.';
		}
		
		
		
	}
	
}

///////////////////////////////////////////////////////////////////////////////////////// PROGRAMAS
if(array_key_exists("programa", $_GET)) { 
	function allProgramas() {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT * FROM `programas`');
		$sql->execute();
		$row_count = $sql->rowCount();
		$i = 1;
		if($row_count === 0) {
			echo '<div class="row"> No hay Proramas configurados aún. Agregue un programa para comenzar</div>';
		} else {
			while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
				echo '<div class="row">';
				echo '<div class="check"><input class="itemcheck" type="checkbox" name="programa-id-chk" id="'.$row->id.'" value="'.$row->id.'"></div>';
				echo '<div class="programa-id">'.$row->id.'</div>';
				echo '<div class="programa-title">'.$row->nombre;
				echo '<div id="prog_'.$i.'" class="inline_edit hid"><form id="programa" method="post"><input type="text" name="nombre" value="'.$row->nombre.'"><input type="hidden" name="id" value="'.$row->id.'"><input type="hidden" name="update_programa" value="1"><input type="submit" name="submit" value="enviar"></form></div></div>';
				echo '<div id="progbtn_'.$i.'" class="programa-edit edlistener"> <i class="fas fa-edit"></i></div>';
				echo '</div>';
				$i++;
			}
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////// add programa
if (isset($_POST['programa_form']) AND $_POST['programa_form'] === "1") { 
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('INSERT INTO `programas` (`nombre`) VALUES (:nombre)');
	$nombre = $_POST['nombre'];
	$sql->bindValue(':nombre', $nombre);
	if($sql->execute()) {
		$_SESSION['advertencia'] = 'Programa agregado con éxito';
	} else {
		$_SESSION['error'] = 'Se presentó un error intente más tarde';
	}
}

///////////////////////////////////////////////////////////////////////////////////////// eddit programa
if (isset($_POST['update_programa']) AND $_POST['update_programa'] === "1") { 
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('UPDATE `programas` SET `nombre` = :nombre WHERE `id` = :id');
	$nombre = $_POST['nombre'];
	$id = $_POST['id'];
	if($sql->execute(array(':nombre' => $nombre, ':id' => $id))) {
		$_SESSION['advertencia'] = 'Programa actualizado con éxito';
	} else {
		$_SESSION['error'] = 'Se presentó un error intente más tarde';
	}
}


///////////////////////////////////////////////////////////////////////////////////////// PROYECTO
if(array_key_exists("proyectos", $_GET)) { 

	function allRegionSelect() { /// para hacer el select con todos las regiones disponibles
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT `nombre` FROM `region`');
		$sql->execute();
		$selected = '';
		while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
			echo '<option value="'.$row->nombre.'"'.$selected.'>'.$row->nombre.'</option>';
		}
	}

	function allYears() {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT `fechafinal` FROM `proy_datostec`');
		$sql->execute();
		$yearArray = [];
		while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
			$dateArray = explode("-", $row->fechafinal);
			$year = $dateArray[0];
			if(!in_array($year, $yearArray)) {
				echo '<option value="'.$year.'">'.$year.'</option>';
				array_push($yearArray, $year);
			}
		}
	}

	function limitSelect() {
		$opciones = array(0 => 5, 1 => 10, 2 => 15, 3 => 20);
		$countOP = count($opciones);
		$selected = ' ';
		for ($i = 0; $i < $countOP; $i++) {
			echo '<option value="'.$opciones[$i].'"'.$selected.'>'.$opciones[$i].'</option>';
		}
	}

	//// Clear Filters
	if(array_key_exists('limpiar', $_POST) AND $_POST['limpiar'] == "Quitar Filtros") {
		unset($_SESSION['title_filter']);
		unset($_SESSION['year_filter']);
		unset($_SESSION['region_filter']);
		unset($_SESSION['limite']);
		unset($_SESSION['filters']);
	}

	//// proyectos y filtros
	function alllProyectos() {

		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		/// 1. asignar los filtros
		$numFilters = array(); // arreglo de filtros
		echo '<div style="display:none;" id="filter_store">';
		//si hay filtros empujar al array y preparar para seleccionar por js
		if (isset($_POST['title_filter']) AND $_POST['title_filter'] != "") {
			$numFilters += ['title_filter' => 1];
		} else if (isset($_SESSION['title_filter'])) {
			$numFilters += ['title_filter' => 1];
		} 
		if (isset($_POST['region_filter']) AND $_POST['region_filter'] != "") {
			$numFilters += ['region_filter' => 1];
			echo '<div id="reg-data">'.$_POST['region_filter'].'</div>';
		} else if (isset($_SESSION['region_filter'])) {
			$numFilters += ['region_filter' => 1];
			echo '<div id="reg-data">'.$_SESSION['region_filter'].'</div>';
		}
		if (isset($_POST['year_filter']) AND $_POST['year_filter'] != "") {
			$numFilters += ['year_filter' => 1];
			echo '<div id="yer-data">'.$_POST['year_filter'].'</div>';
		} else if (isset($_SESSION['year_filter'])) {
			$numFilters += ['year_filter' => 1];
			echo '<div id="yer-data">'.$_SESSION['year_filter'].'</div>';
		}
		if (isset($_POST['limite']) AND $_POST['limite'] != "") {
			echo '<div id="limit-data">'.$_POST['limite'].'</div>';
		} else if (isset($_SESSION['limite'])) {
			echo '<div id="limit-data">'.$_SESSION['limite'].'</div>';
		}
		echo '</div>';

		//contar los filtros
		$countFilters = count($numFilters);
		//echo "<br>".$countFilters."<br>";
		//echo "<br>";
		
		//comienzan filtros para aplicar a paginación
		$whereFiltro = "";
		$prevfiltro = false;

		///// 1.1 asignar elementos en post o en session
		//////// 1.1.1 filtro título = primer filtro en query
		if (!isset($_POST['title_filter']) AND !isset($_SESSION['title_filter'])) {
			$whereFiltro .= "";
		} else if(isset($_POST['title_filter']) AND $_POST['title_filter'] != "") {
			$whereFiltro .= "(p.titulo LIKE '%".$_POST['title_filter']."%' COLLATE utf8_general_ci)";
			$_SESSION['title_filter'] = $_POST['title_filter'];
			$_SESSION['filters'] = "1";
		} else if (isset($_SESSION['title_filter'])) {
			$whereFiltro .= "(p.titulo LIKE '%".$_SESSION['title_filter']."%' COLLATE utf8_general_ci)";
		} 
		//////// 1.1.2 filtro Año = segundo filtro en query
		if (!isset($_POST['year_filter']) AND !isset($_SESSION['year_filter'])) {
			$whereFiltro .= "";
		} else if(isset($_POST['year_filter']) AND $_POST['year_filter'] != "") {
			if((isset($_POST['title_filter']) AND $_POST['title_filter'] != "") AND $countFilters >= 2) {
				$whereFiltro .= " AND ";
				echo "puta?";
			}
			$whereFiltro .= "(pdt.fechafinal LIKE '%".$_POST['year_filter']."%' COLLATE utf8_general_ci)";
			$_SESSION['year_filter'] = $_POST['year_filter'];
			$_SESSION['filters'] = "1";
		} else if (isset($_SESSION['year_filter'])) {
			if(isset($_SESSION['title_filter']) AND $countFilters >= 2) {
				$whereFiltro .= " AND ";
			} 
			$whereFiltro .= "(pdt.fechafinal LIKE '%".$_SESSION['year_filter']."%' COLLATE utf8_general_ci)";
		}
		//////// 1.1.3 filtro Region = tercer filtro en query
		if (!isset($_POST['region_filter']) AND !isset($_SESSION['region_filter'])) {
			$whereFiltro .= "";
		} else if(isset($_POST['region_filter']) AND $_POST['region_filter'] != "") {
			if($countFilters >= 2) {
				$whereFiltro .= " AND ";
			}
			$whereFiltro .= "(pr.region LIKE '%".$_POST['region_filter']."%' COLLATE utf8_general_ci)";
			$_SESSION['region_filter'] = $_POST['region_filter'];
			$_SESSION['filters'] = "1";
		} else if (isset($_SESSION['region_filter'])) {
			if($countFilters >= 2) {
				$whereFiltro .= " AND ";
			} 
			$whereFiltro .= "(pr.region LIKE '%".$_SESSION['region_filter']."%' COLLATE utf8_general_ci)";
		}

		/// 2. query string básica previa a filtros
		$sqlQuery = '
			SELECT p.titulo AS titulo, pa.beneficiarios AS grupo, pdt.fechafinal AS fecha, pdt.foto AS foto, pdt.proyectid AS id, pr.region AS region
			FROM `proy_datostec` pdt
			INNER JOIN `proyecto` p ON pdt.proyectid = p.proyectid
			INNER JOIN `proy_academicos` pa ON pdt.proyectid = pa.proyectid
			INNER JOIN `proy_regionales` pr ON pdt.proyectid = pr.proyectid
		';
		///// 2.1 ejecutar pre-filtros numero de paginas
		if($whereFiltro != "") { $sqlQuery .= " WHERE ".$whereFiltro; }
		$sql = $con->prepare($sqlQuery);
		$sql->execute();
		$row_count = $sql->rowCount();

		///// 2.1.1 asignar limite de paginación
		if (!isset($_POST['limite']) AND !isset($_SESSION['limite'])) {
			$resultados_pagina = 10;
		} else if(isset($_POST['limite'])) {
			$_SESSION['limite'] = $_POST['limite'];
			$resultados_pagina = $_POST['limite'];
		} else if(isset($_SESSION['limite'])) {
			$resultados_pagina = $_SESSION['limite'];
		}
		// ----------- ultima página o total de páginas
		$numero_paginas = ceil($row_count/$resultados_pagina);
		
		// ----------- pagina actual o default
		if (!isset($_GET['pagina'])) {
			$pagina = 1;
		} else {
			$pagina = $_GET['pagina'];
		}
		// ------------ comienzo de links
		$start = (($pagina - 4) >0) ? $pagina - 4 : 1;
		$end = (($pagina + 4) < $numero_paginas) ? $pagina + 4 : $numero_paginas;
		// ------------ determinar el limite de SQL para paginacion
		$pagina_actual = ($pagina - 1) * $resultados_pagina;
		$limit = $pagina_actual.", ".$resultados_pagina;

		/// 3. finalizar query y ejecutar
		$sqlString = '
			SELECT p.titulo AS titulo, pa.beneficiarios AS grupo, pdt.fechafinal AS fecha, pdt.foto AS foto, pdt.proyectid AS id, pr.region AS region
			FROM `proy_datostec` pdt
			INNER JOIN `proyecto` p ON pdt.proyectid = p.proyectid
			INNER JOIN `proy_academicos` pa ON pdt.proyectid = pa.proyectid
			INNER JOIN `proy_regionales` pr ON pdt.proyectid = pr.proyectid
		';
		if($whereFiltro != "") {
			$sqlString .= " WHERE ".$whereFiltro;
		}

		$sqlString .= " ORDER BY titulo ASC";
		$sqlString .= " LIMIT ".$limit;			

		//Preparar la string
		$sql = $con->prepare($sqlString);
		
		
		///// 3.1 print to page
		if($sql->execute()) { 
			while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
				echo '<div class="row">';
				echo '<div class="check"><input class="checkproy" type="checkbox" name="proyecto-id-chk" value="'.$row->id.'"></div>';
				echo '<div class="proy-id">'.$row->id.'</div>';
				echo '<div class="proy-title"><a href="index.php?update_proy='.$row->id.'">'.$row->titulo.'</a></div>';
				echo '<div class="proy-region">'.$row->region.'</div>';
				echo '<div class="proy-fecha">'.$row->fecha.'</div>';
				echo '<div class="proy-grupo">'.$row->grupo.'</div>';
				if($row->foto !== ' ') {
					echo '<div class="proy-foto"><i class="far fa-eye"></i></div>'; /// hay foto
				} else {
					echo '<div class="proy-foto"><i class="fas fa-eye-slash"></i></div>'; /// no hay foto
				}
				echo '</div>';
			}
		}

		/// 4. paginacion
		echo "<div id='pagination'>";
		if ($pagina == 1 && $numero_paginas > 1) {
			echo '<a class="disabled prev" href="">&laquo</a>';
		} else if($numero_paginas > 1) {
			$prevpage = $pagina - 1;
			echo '<a class="prev" href="?proyectos&pagina='.$prevpage.'">&laquo</a>';
		}
		if ($start > 1) {
			echo '<a href="?proyectos&pagina=1">1</a>';
			echo '<a class="disabled" href="">....</a>';
		}
		if ($numero_paginas > 1) {
			for ($pagelink=$start; $pagelink<=$end; $pagelink++) {
				if ($pagelink == $pagina) {
					$actual = ' class="current" ';
				} else {
					$actual = " ";
				}
				echo "<a".$actual."href='index.php?proyectos&pagina=".$pagelink."'> ".$pagelink." </a> ";
			}
		}
		if ($end < $numero_paginas) {
			echo '<a class="disabled" href="">....</a>';
			echo '<a href="?proyectos&pagina='.$numero_paginas.'">'.$numero_paginas.'</a>';
		}
		if ($pagina == $numero_paginas && $numero_paginas > 1) {
			echo '<a class="disabled next" href="">&raquo</a>';
		} else if($numero_paginas > 1) {
			$nextpage = $pagina + 1;
			echo '<a class="next" href="?proyectos&pagina='.$nextpage.'">&raquo</a>';
		}
		echo "</div>";
		/// ----------------------> FIN PAGINACIÓN

	}

}


///////////////////////////////////////////////////////////////////////////////////////// add proyecto
if(array_key_exists("nuevo_proy", $_GET)) {
 	
	function allProgsCheck() { /// para hacer el checkgroup con los programas existentes
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT * FROM `programas`');
		$sql->execute();
		while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
			echo '<div class="check-group"><input id="chk_'.$row->id.'" type="checkbox" name="programa[]" value="'.$row->nombre.'"> '.$row->nombre.'</div>';
		}

	}

	function allRegionSelect() { /// para hacer el select con todos las regiones disponibles
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT `nombre` FROM `region`');
		$sql->execute();
		while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
			echo '<option value="'.$row->nombre.'">'.$row->nombre.'</option>';
		}
	}


	////////////////////////////////////////////  MANEJO DEL FORMULARIO ENVIADO

	if (isset($_POST['new_proyecto']) AND $_POST['new_proyecto'] === "1") { 
		/// 0. Generar variables para mutar luego si es el caso y usar en las query
		$proyectID = '0'; // variable para mantener el id del proyecto
		$yayTitle = ''; // variable para éxito de la operación
		$errorNum = ''; // variable para mantener registro de cual error es y que se debe borrar
		$fileUploaded = false;

		$titulo = $_POST['proy_titulo'];
		$objetivo = $_POST['proy_objetivo'];
		$otronew = false;
		if(isset($_POST['otro']) AND $_POST['otro'] !== "") {
			$otronew = true;
		}
		if(isset($_POST['programa'])) {
			if($otronew === true) {
				$programa = implode (", ", $_POST['programa']); ///  array converir en string separada por comas.
				$programa .= ', '.$_POST['otro'];
			} else {
				$programa = implode (", ", $_POST['programa']); ///  array converir en string separada por comas.
			}
		} else if($otronew === false) {
			$errorStop = true;
			$advertencia = 'Seleccione al menos un programa, esto es obligatorio';
		}
		$cau = $_POST['cau'];
		$municipio = $_POST['municipio'];
		$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
		$final = $_POST['yearfinal'].'-'.$_POST['monthfinal'].'-'.$_POST['dayfinal'];
		$docente = $_POST['docente'];
		$espacioAcademico = $_POST['e-academico'];
		$investiga = $_POST['g-investiga'];
		$tipo = $_POST['tipologias'];
		$externos = ' '; /// al no ser obligatorio puede estar vacio
			if(isset($_POST['externos']) AND $_POST['externos'] !== "") {
				$externos = $_POST['externos'];
			}
		$beneficiarios = $_POST['beneficiarios'];
		$reg = $_POST['region'];
		$foto = ' '; /// puede no existir, en cuyo caso debe ir desocupada --> remplazo se determina en frontend si aplica
		$errorStop = false; /// ---> VERIFICAR QUE NO HAY ERROR Y CONTINUAR

		/// 1. Añadir datos a la primera sección ---> proy_datostec, PRImero verificar que sea único
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT `proyectid` FROM `proyecto` WHERE `titulo` = :titulo');
		$sql->bindValue(':titulo', $titulo);
		$sql->execute();
		$row_count = $sql->rowCount();
		if($row_count !== 0) {
			$errorStop = true;
			$errorNum = 0;
			$error = 'Ya existe un proyecto con este nombre, intente otra vez';
		} 

		///// 1.1 subir foto PASOS:

		/////// 1.1.1 establecer una variable del folder según region---> 
		$sql = $con->prepare('SELECT `folder` FROM `region` WHERE `nombre` = :reg');
		$sql->bindValue(':reg', $reg);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_OBJ);
		$folderName = $result->folder;


		/////// 1.1.2 subir la imagen al folder 
		if ($errorStop === false) {	
			if (array_key_exists("foto", $_FILES) AND $_FILES['foto']['name'] != "") {
				//// efectivamente se está subiendo una foto
				if ($_FILES["foto"]["error"] > 0) {
					// si hay un error subiendo el archivo
					$errorStop = true;
					$errorNum = 0;
					$error = "Se presentó un error con la foto ".$_FILES["foto"]["error"].".";
				} 
				else if($_FILES["foto"]["size"] > 1500000) {
					$errorStop = true;
					$errorNum = 0;
					$error = "La foto supera el límite máximo de 1.5 MB, corrija esto antes de continuar.";
				} else {
					// check if file already exit in "images" folder.
					if (file_exists($folderName."/".$_FILES["foto"]["name"])) {
						$errorStop = true;
						$errorNum = 0;
						$error = "El archivo ".$_FILES["foto"]["name"]." ya existe. Verifique";
					} else {
						//move_uploaded_file guarda la imagen permanentemente en el lugar adecuado.
						if(move_uploaded_file($_FILES["foto"]["tmp_name"], $folderName."/".$_FILES["foto"]["name"])) {
							// If file has uploaded successfully, store its name in data base
							$foto = $_FILES["foto"]["name"];
							$fileUploaded = true;
						} else {
							$errorStop = true;
							$errorNum = 0;
							$error = "Se presentó un error al subir el archivo, contáctese con el administrador, o inténtelo desde el administrador de la plataforma.";
						}
					}
				}
			}
		}

		///// 1.2 insertar en: proy_datostec
		if ($errorStop === false) {
			$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$sql = $con->prepare('INSERT INTO `proy_datostec` (`proyectid`, `foto`, `fecha`, `fechafinal`) VALUES (:pid, :foto, :fecha, :final)');
			if($sql->execute(array(':pid' => $proyectID, ':foto' => $foto, ':fecha' => $date, ':final' => $final))) {
				//$sql->debugDumpParams();
				/// 1.3 genear proyectID donde obtendremos el identificador que vinculará todas las tablas
				$sql = $con->prepare('UPDATE `proy_datostec` SET `proyectid` = :id WHERE `id` = :lastid LIMIT 1');
				$proyectID = $con->lastInsertId();
				if($sql->execute(array(':id' => $proyectID, ':lastid' => $proyectID))) { 
					/// continuar
				} else { 
					$errorNum = 1;		
					$errorStop = true; 
				}

				if($errorStop === false) {
					/// 2. Añadir datos a la tabla: proyecto
					$sql = $con->prepare('INSERT INTO `proyecto` (`proyectid`, `titulo`, `objetivo`, `tipologia`, `instituciones`) VALUES (:pid, :titulo, :objetivo, :tipo, :instituciones)');
					if($sql->execute(array(':pid' => $proyectID, ':titulo' => $titulo, ':objetivo' => $objetivo, ':tipo' => $tipo, ':instituciones' => $externos))) {
						/// 3. Añadir datos a la tabla: proy_academicos
						/// 3.1 Si hay un programa nuevo agregarlo a la tabal de programas primero
						if($otronew === true) {
							$sql = $con->prepare('INSERT INTO `programas` (`nombre`) VALUES (:nombre)');
							$nombre = $_POST['otro'];
							$sql->bindValue(':nombre', $nombre);
							if($sql->execute()) {
								
							} else {
								$errorNum = 2;
								$errorStop = true;
							}

						} 

						if($errorStop === false) {
							$sql = $con->prepare('INSERT INTO `proy_academicos` (`proyectid`, `programas`, `docente`, `espacioacad`, `grupoinvest`, `beneficiarios`) VALUES (:pid, :programas, :docente, :espacio, :grupo, :beneficiarios)');
							if($sql->execute(array(':pid' => $proyectID, ':programas' => $programa, ':docente' => $docente, ':espacio' => $espacioAcademico, ':grupo' => $investiga, ':beneficiarios' => $beneficiarios))) {
								/// 4. Añadir datos a la tabla: proy_regionales
								$sql = $con->prepare('INSERT INTO `proy_regionales` (`proyectid`, `municipio`, `cau`, `region`) VALUES (:pid, :municipio, :cau, :region)');
								if($sql->execute(array(':pid' => $proyectID, ':municipio' => $municipio, ':cau' => $cau, ':region' => $reg))) {
									$tituLen = strlen($titulo);
									if($tituLen > 25) {
										$yayTitle = substr($titulo, 0, 25);
										$yayTitle .= '..';
									} else {
										$yayTitle = $titulo; 
									}
									$_SESSION['advertencia'] = 'proyecto: '.$yayTitle.'. Agregado correctamente';

									if (!headers_sent()) {    
									    header("Location: index.php?proyectos");
									    exit;
									} else {  
									    echo '<script type="text/javascript">';
									    echo 'window.location.href="index.php?proyectos";';
									    echo '</script>';
									    echo '<noscript>';
									    echo '<meta http-equiv="refresh" content="0;url=index.php?proyectos" />';
									    echo '</noscript>'; exit;
									}

								} else {
									$errorNum = 3;
									$errorStop = true;
									$error = 'Se presentó un error insertando en la base de datos, intente más tarde o contáctese con el administrador';
								}

							} else {
								$errorNum = 2;
								$errorStop = true;
								$error = 'Se presentó un error insertando en la base de datos, intente más tarde o contáctese con el administrador';
							}

						} else {
							$errorStop = true;
							$errorNum = 1;
							$error = 'Se presentó un error insertando en la base de datos, intente más tarde o contáctese con el administrador';
						}

					} else {
						$errorStop = true;
						$errorNum = 1;
						$error = 'Se presentó un error insertando en la base de datos, intente más tarde o contáctese con el administrador';
					}

				} else {
					$errorStop = true;
					$errorNum = 0;
					$error = 'Se presentó un error insertando en la base de datos, intente más tarde o contáctese con el administrador';
				}

			} else {
				$errorStop = true;
				$errorNum = 0;
				$error = 'Se presentó un error insertando en la base de datos, intente más tarde o contáctese con el administrador';
			}
		}

		/// 5. si se presenta error en alguno de los ateriores desocupar las tablas creadas
		if($errorStop === true) {
			$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

			if($errorNum === 0) {
				/// no hay que borrar nada
			} else if($errorNum === 1) {
				/// borrar el ultimo elemento añadido
				$sql = $con->prepare('DELETE FROM `proy_datostec` WHERE `id` = :id');
				$sql->bindParam(':id', $proyectID);
				$sql->execute(); 

			} else if($errorNum === 2) {
				/// borrar el ultimo elemento añadido
				$sql = $con->prepare('DELETE FROM `proy_datostec` WHERE `id` = :id');
				$sql->bindParam(':id', $proyectID);
				$sql->execute(); 
				/// borrar el ultimo elemento añadido segunda tabla
				$sql = $con->prepare('DELETE FROM `proyecto` WHERE `proyectid` = :id');
				$sql->bindParam(':id', $proyectID);
				$sql->execute(); 

			} else if($errorNum === 3) {
				/// borrar el ultimo elemento añadido
				$sql = $con->prepare('DELETE FROM `proy_datostec` WHERE `id` = :id');
				$sql->bindParam(':id', $proyectID);
				$sql->execute(); 
				/// borrar el ultimo elemento añadido segunda tabla
				$sql = $con->prepare('DELETE FROM `proyecto` WHERE `proyectid` = :id');
				$sql->bindParam(':id', $proyectID);
				$sql->execute(); 
				/// borrar el ultimo elemento añadido tercera tabla
				$sql = $con->prepare('DELETE FROM `proy_academicos` WHERE `proyectid` = :id');
				$sql->bindParam(':id', $proyectID);
				$sql->execute(); 

			} else {
				/// por siacas
			}

			if ($fileUploaded === true) { 
				unlink($folderName."/".$foto);
			}


		}
	}

}
///////////////////////////////////////////////////////////////////////////////////////// eddit proyecto
if(array_key_exists("update_proy", $_GET)) {
 	
	function allProgsCheck() { /// para hacer el checkgroup con los programas existentes
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT * FROM `programas`');
		$sql->execute();
		$progsArray = [];
		while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
			echo '<div class="check-group"><input id="chk_'.$row->id.'" type="checkbox" name="programa[]" value="'.$row->nombre.'"> '.$row->nombre.'</div>';
			array_push($progsArray, $row->nombre);
		}
		$sql = $con->prepare('SELECT `programas` FROM `proy_academicos` WHERE `proyectid` = :id');
		$id = $_GET['update_proy'];
		$sql->bindValue(':id', $id);
		$sql->execute();
		$row = $sql->fetch(PDO::FETCH_OBJ);
		$proyProgs = explode(', ', $row->programas);
		$length = count($proyProgs);
		for($i = 0; $i < $length; $i++) {
			if(!in_array($proyProgs[$i], $progsArray)) {
				echo '<div class="check-group faltante"><input type="checkbox" name="programa[]" value="'.$proyProgs[$i].'"> '.$proyProgs[$i].'</div>';
			}
		}
	}

	function allRegionSelect() { /// para hacer el select con todos las regiones disponibles
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT `nombre` FROM `region`');
		$sql->execute();
		while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
			echo '<option value="'.$row->nombre.'">'.$row->nombre.'</option>';
		}
	}

	if (isset($_POST['edit_proy']) AND $_POST['edit_proy'] !== "") {
		$errorPause = false;
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$query = '
			SELECT 
			p.titulo AS titulo, p.objetivo AS objetivo, p.tipologia AS tipologia, p.instituciones AS instituciones, 
			pa.programas AS programas, pa.docente AS docente, pa.espacioacad AS espacio, pa.grupoinvest AS grupo, pa.beneficiarios AS beneficiarios,
			pdt.fechafinal AS fecha, pdt.foto AS foto, pdt.proyectid AS id, pdt.fechafinal AS duracion, 
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
		$id = $_POST['edit_proy'];
		$sql->bindValue(':id', $id);
		$sql->execute();
		$results = $sql->fetch(PDO::FETCH_OBJ);
		/// --------- > actualizar 1 por 1 si es necesario, si item es igual ignorarlo
		/// 1. verificar que el título siga siendo único y actualizarlo
		if(isset($_POST['proy_titulo']) AND $_POST['proy_titulo'] !== $results->titulo) {
			$vsql = $con->prepare('SELECT `proyectid` FROM `proyecto` WHERE `titulo` = :titulo');
			$vtitulo = $_POST['proy_titulo'];
			$vsql->bindValue(':titulo', $vtitulo);
			$vsql->execute();
			$uniqueTitle = $vsql->rowCount();
			if($uniqueTitle === 0) {
				updateTable('proyecto','titulo',$_POST['edit_proy'],$_POST['proy_titulo']);
				if(isset($_SESSION['errorPause']) AND $_SESSION['errorPause'] === 'pause') {
					$errorPause = true;
					echo 'pausar = ';
				}
			} else {
				$error = 'Esete título esta siendo usado por otro proyecto, este campo debe ser único. Verifique antes de continuar.';
				$errorPause = true;
			}
		}
		/// 2. objetivo
		if(isset($_POST['proy_objetivo']) AND $_POST['proy_objetivo'] !== $results->objetivo) {
			updateTable('proyecto','objetivo',$_POST['edit_proy'],$_POST['proy_objetivo']);
		}
		/// 3. programas
		if(isset($_POST['programa'])) {
			/// único caso en el que es mejor siempre actualizar, funcion para comparar si los programas ya están seleccionados es exesivamente complicada para valer la pena
			/// 1. construir la string del array de programas
			$programa = implode (", ", $_POST['programa']); ///  array converir en string separada por comas.
			updateTable('proy_academicos','programas',$_POST['edit_proy'],$programa);
		}
		/// 4. cau
		if(isset($_POST['cau']) AND $_POST['cau'] !== $results->cau) {
			updateTable('proy_regionales','cau',$_POST['edit_proy'],$_POST['cau']);
		}
		/// 5. municipio
		if(isset($_POST['municipio']) AND $_POST['municipio'] !== $results->municipio) {
			updateTable('proy_regionales','municipio',$_POST['edit_proy'],$_POST['municipio']);
		}
		/// 6. fecha
		if(isset($_POST['year'])) {
			$fechaFull = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
			if($fechaFull !== $results->fecha) {
				updateTable('proy_datostec','fecha',$_POST['edit_proy'],$fechaFull);
			}
		}
		/// 7. fecha final
		if(isset($_POST['yearfinal'])) {
			$fechaFull = $_POST['yearfinal'].'-'.$_POST['monthfinal'].'-'.$_POST['dayfinal'];
			if($fechaFull !== $results->duracion) {
				updateTable('proy_datostec','fechafinal',$_POST['edit_proy'],$fechaFull);
			}
		}
		/// 8. docente
		if(isset($_POST['docente']) AND $_POST['docente'] !== $results->docente) {
			updateTable('proy_academicos','docente',$_POST['edit_proy'],$_POST['docente']);
		}
		/// 9. espacio académico
		if(isset($_POST['e-academico']) AND $_POST['e-academico'] !== $results->espacio) {
			updateTable('proy_academicos','espacioacad',$_POST['edit_proy'],$_POST['e-academico']);
		}
		/// 10. grupo investigacion
		if(isset($_POST['g-investiga']) AND $_POST['g-investiga'] !== $results->grupo) {
			updateTable('proy_academicos','grupoinvest',$_POST['edit_proy'],$_POST['g-investiga']);
		}
		/// 11. tipologias
		if(isset($_POST['tipologias']) AND $_POST['tipologias'] !== $results->tipologia) {
			updateTable('proyecto','tipologia',$_POST['edit_proy'],$_POST['tipologias']);
		}
		/// 12. instituciones
		if(isset($_POST['externos']) AND $_POST['externos'] !== $results->instituciones) {
			updateTable('proyecto','instituciones',$_POST['edit_proy'],$_POST['externos']);
		}
		/// 13. beneficiarios
		if(isset($_POST['beneficiarios']) AND $_POST['beneficiarios'] !== $results->beneficiarios) {
			updateTable('proy_academicos','beneficiarios',$_POST['edit_proy'],$_POST['beneficiarios']);
		}
		/// 14. region
		if(isset($_POST['region']) AND $_POST['region'] !== $results->region) {
			//// si hay foto moverla
			if($results->foto !== '') {
				if(file_exists($results->folder."/".$results->foto)) {
					$sql = $con->prepare('SELECT `folder` FROM `region` WHERE `nombre` = :reg');
					$sql->bindValue(':reg', $_POST['region']);
					$sql->execute();
					$result = $sql->fetch(PDO::FETCH_OBJ);
					$fileToDelete = $results->folder."/".$results->foto;
					$newFile = $result->folder."/".$results->foto;
					if (!copy($fileToDelete, $newFile)) {
					    echo "Error al mover $fichero...\n";
					    $errorPause = true;
					} else {
						unlink($fileToDelete);
					}
				}
			} 
			///  una vez correctamente ubicada la foto actualize la region
			updateTable('proy_regionales','region',$_POST['edit_proy'],$_POST['region']);
		}
		/// 15. foto
		if (array_key_exists("foto", $_FILES) AND $_FILES['foto']['name'] !== $results->foto) { 
			/////// 15.1 establecer una variable del folder según region---> 
			$sql = $con->prepare('SELECT `folder` FROM `region` WHERE `nombre` = :reg');
			$sql->bindValue(':reg', $_POST['region']);
			$sql->execute();
			$result = $sql->fetch(PDO::FETCH_OBJ);
			$folderName = $result->folder;
			$foto = '';

			/////// 15.2 subir la imagen al folder 
			if (array_key_exists("foto", $_FILES) AND $_FILES['foto']['name'] != "") {
				//// efectivamente se está subiendo una foto
				if ($_FILES["foto"]["error"] > 0) {
					// si hay un error subiendo el archivo
					$error = "Se presentó un error con la foto ".$_FILES["foto"]["error"].".";
					$errorPause = true;
				} 
				else if($_FILES["foto"]["size"] > 1500000) {
					$error = "La foto supera el límite máximo de 1.5 MB, corrija esto antes de continuar.";
					$errorPause = true;
				} else {
					// check if file already exit in "images" folder.
					if (file_exists($folderName."/".$_FILES["foto"]["name"])) {
						$error = "El archivo ".$_FILES["foto"]["name"]." ya existe. Verifique";
						$errorPause = true;
					} else {
						//move_uploaded_file guarda la imagen permanentemente en el lugar adecuado.
						if(move_uploaded_file($_FILES["foto"]["tmp_name"], $folderName."/".$_FILES["foto"]["name"])) {
							// If file has uploaded successfully, store its name in data base
							$foto = $_FILES["foto"]["name"];
						} else {
							$errorPause = true;
							$error = "Se presentó un error al subir el archivo, contáctese con el administrador, o inténtelo desde el administrador de la plataforma.";
						}
					}
				}
			}
			/// ---------------------------------

			/////// 15.2 guardar info a db
			if($foto !== '') {
				updateTable('proy_datostec','foto',$_POST['edit_proy'],$foto);
			}
		
		}

		/// 16. finalizar
		if($errorPause === false) {
			$_SESSION['advertencia'] = 'Proyecto actualizado exitosamente';
			redirect('proyectos');
		}
	}

}


///////////////////////////////////////////////////////////////////////////////////////// USUARIO
if(array_key_exists("user", $_GET)) { 
	function allUsers() {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('SELECT `id`, `nombrecompleto`, `usergroup` FROM `usuarios`');
		if($sql->execute()) { 
			while($row = $sql->fetch(PDO::FETCH_OBJ)) {	
				echo '<div class="row">';
				if($row->usergroup !== 'SuperAdmin') {
					echo '<div class="check"><input class="checkuser" type="checkbox" name="user-id-chk" value="'.$row->id.'"></div>';	
				} else {
					echo '<div class="check"></div>';
				}
				echo '<div class="user-id">'.$row->id.'</div>';
				echo '<div class="user-nombre"><a href="index.php?update_user='.$row->id.'">'.$row->nombrecompleto.'</a></div>';
				echo '<div class="user-tipo">'.$row->usergroup.'</div>';
				echo '</div>';
			}
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////// add user
if(array_key_exists("nuevo_user", $_GET)) {
	if(array_key_exists("new_user", $_POST) AND $_POST["new_user"] === '1') {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		/// variables a usar
		$nombre = $_POST['name'];
		$docu = $_POST['documento'];
		$user = $_POST['username'];
		$tipo = $_POST['tipo'];
		$pass = $_POST['userpasword'];
		$group = $_POST['usergroup'];
		$fecha = date("Y-m-d");
		$errorStop = false;
		$errortype = [];
		/// 1. verificar que no exista ni documento ni usuario repetido.
		$sql = $con->prepare('SELECT `documento`, `usuario` FROM `usuarios`');
		$sql->execute();
		while($results = $sql->fetch(PDO::FETCH_OBJ)) {
			if($results->usuario === $user) {
				$errorStop = true;
				array_push($errortype, 'usuario');
			} 
			if($results->documento === $docu) {
				$errorStop = true;
				array_push($errortype, 'documento');
			}
		}
		/// 2. si no hay repetidos continuar
		if($errorStop === false) {
			$query = $con->prepare("INSERT INTO `usuarios` (`nombrecompleto`, `documento`, `usuario`, `tipodoc`, `password`, `usergroup`, `fecha_reg`) VALUES (:nombre,:docu,:user,:tipo,:pass,:group,:fecha)");
			if ($query->execute(array(':nombre' => $nombre, ':docu' => $docu, ':user' => $user, ':tipo' => $tipo, ':pass' => $pass, ':group' =>$group, ':fecha' => $fecha))) {
				$id = $con->lastInsertId();
				$query = $con->prepare('UPDATE `usuarios` SET `password` = :pass WHERE id = :id LIMIT 1');
				$md5pasword = md5(md5($id).$pass);
				if($query->execute(array(':pass' => $md5pasword, 'id' => $id))) {
					$_SESSION['advertencia'] = 'Usuario agregado exitosamente';
					redirect('user');
				}
			} else {
				$error = 'no se puede agregar el usuario, intente más tarde';
			}
		} else {
			$len = count($errortype);
			if($len > 1) {
				$error = 'El '.$errortype[0].' y el '.$errortype[1].' ya están registrados';
			} else {
				$error = 'El '.$errortype[0].' ya está registrado';
			}
		}
		
	}
}

///////////////////////////////////////////////////////////////////////////////////////// edit user
if(array_key_exists("update_user", $_GET)) {
	print_r($_POST);
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	/// query básica para reusar en comparaciones
	$updated = false;
	$errorPause = false;
	$queryCompare = $con->prepare('SELECT * FROM `usuarios` WHERE `id` = :id');
	$id = $_GET['update_user'];
	$queryCompare->bindValue(':id', $id);
	$queryCompare->execute();
	$rcomp = $queryCompare->fetch(PDO::FETCH_OBJ);
	/// if post
	if(array_key_exists("update_user", $_POST) AND $_POST["update_user"] === '1') {
		/// 1. comparar y actualizar nombre si cambia
		if($rcomp->nombrecompleto !== $_POST['name']) {
			$name = $_POST['name'];
			$sql = $con->prepare('UPDATE `usuarios` SET `nombrecompleto` = :name WHERE `id` = :id');
			if($sql->execute(array(':name' => $name, ':id' => $id))) {
				$updated = true;
			}
		}
		/// 2. comparar y actualizar usuario si cambia, verificar global
		if($rcomp->usuario !== $_POST['username']) { 
			$usuArrayComp = [];
			$sql = $con->prepare('SELECT `usuario` FROM `usuarios`');
			$sql->execute();
			while($result = $sql->fetch(PDO::FETCH_OBJ)) {
				/// verificar otros usuarios para no repetir
				array_push($usuArrayComp, $result->usuario);
			}
			if(!in_array($_POST['username'], $usuArrayComp)) {
				/// verificación completa, actualizar
				$user = $_POST['username'];
				$sql = $con->prepare('UPDATE `usuarios` SET `usuario` = :usuario WHERE `id` = :id');
				if($sql->execute(array(':usuario' => $user, ':id' => $id))) {
					$updated = true;
				}
				
			} else  {
				$error = 'Este nombre de usuario ya existe, escriba otro.';
				$errorPause = true;
			}
			
		} 
		/// 3. comparar y actualizar tipo de documento
		if($rcomp->tipodoc !== $_POST['tipo']) { 
			$tipo = $_POST['tipo'];
			$sql = $con->prepare('UPDATE `usuarios` SET `tipodoc` = :tipo WHERE `id` = :id');
			if($sql->execute(array(':tipo' => $tipo, ':id' => $id))) {
				$updated = true;
			}
		} 
		/// 4. comparar y actualizar documento
		if($rcomp->documento !== $_POST['documento']) { 
			$doc = $_POST['documento'];
			$sql = $con->prepare('UPDATE `usuarios` SET `documento` = :doc WHERE `id` = :id');
			if($sql->execute(array(':doc' => $doc, ':id' => $id))) {
				$updated = true;
			}
		} 
		/// 5. comparar y actualizar contraseña
		if($_POST['userpasword'] !== '') {
			$pass = $_POST['userpasword'];
			$md5pasword = md5(md5($id).$pass);
			if($rcomp->password !== $md5pasword) { 
			$sql = $con->prepare('UPDATE `usuarios` SET `password` = :pass WHERE `id` = :id');
				if($sql->execute(array(':pass' => $md5pasword, ':id' => $id))) {
					$updated = true;
				}
			} 
		}
		/// 6. comparar y actualizar grupo de usuario
		if($rcomp->usergroup !== $_POST['usergroup']) { 
			$grupo = $_POST['usergroup'];
			$sql = $con->prepare('UPDATE `usuarios` SET `usergroup` = :grupo WHERE `id` = :id');
			if($sql->execute(array(':grupo' => $grupo, ':id' => $id))) {
				$updated = true;
			}
		}
		/// 7. concluir y redirigir solo si no hay errores
		if($errorPause === false) {
			if ($updated === true) {
				$_SESSION['advertencia'] = 'Usuario actualizado exitosamente';
				if($rcomp->usergroup === 'SuperAdmin' or $rcomp->usergroup === 'Admin') {
					redirect('user');
				} else {
					redirect('');
				}
				
			} else {
				$_SESSION['advertencia'] = 'No ha realizado ninguna actualización en el usuario';
				if($rcomp->usergroup === 'SuperAdmin' or $rcomp->usergroup === 'Admin') {
					redirect('user');
				} else {
					redirect('');
				}
			}
		}
		
	}
	
}


///////////////////////////////////////////////////////////////////////////////////////// CONFIG
if(array_key_exists("config", $_GET)) {
	if(array_key_exists("update_config", $_POST) AND $_POST["update_config"] === '1') {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$query = $con->prepare('SELECT * FROM `config` WHERE `id` = 1');
		$query->execute();
		$configCompare = $query->fetch(PDO::FETCH_OBJ);
		$errorPause = false;
		$logo = '';
		/// 1. verificar y actualizar el titulo de la app
		if(array_key_exists('name', $_POST) and $_POST['name'] !== $configCompare->titulo) {
			$sql = $con->prepare('UPDATE `config` SET `titulo` = :titulo WHERE `id` = 1');
			$titulo = $_POST['name'];
			$sql->bindValue(':titulo', $titulo);
			if($sql->execute()) {
				$_SESSION['advertencia'] = 'Configuración guardada exitosamente';
			} else {
				$_SESSION['error'] = 'Se presentó un error al actualiar la base de datos';
			}
		}	
		/// 2. verificar y actualizar el contacto
		if(array_key_exists('content', $_POST) and $_POST['content'] !== $configCompare->contacto) {
			$sql = $con->prepare('UPDATE `config` SET `contacto` = :contacto WHERE `id` = 1');
			$contacto = $_POST['content'];
			$sql->bindValue(':contacto', $contacto);
			if($sql->execute()) {
				$_SESSION['advertencia'] = 'Configuración guardada exitosamente';
			} else {
				$_SESSION['error'] = 'Se presentó un error al actualiar la base de datos';
			}
		}	
		/// 3. si se sube imagen guardarla y actualizar el url de logo
		if (array_key_exists("logo", $_FILES) AND $_FILES['logo']['name'] != "") {
			if ($_FILES["logo"]["error"] > 0) {
					        // si hay un error subiendo el archivo
				$error = "Se presesnto un error con el logo ".$_FILES["logo"]["error"];
				$errorPause = true;
			} else {
					    		// check if file already exit in "images" folder.
				if (file_exists("img/".$_FILES["logo"]["name"])) {
					$error = "El archivo ".$_FILES["logo"]["name"]." ya existe.";
					$errorPause = true;
				} else {
						        	//move_uploaded_file guarda la imagen permanentemente en el lugar adecuado.
					if(move_uploaded_file($_FILES["logo"]["tmp_name"], "img/".$_FILES["logo"]["name"])) {
					                	// If file has uploaded successfully, store its name in data base
						$logo = "img/".$_FILES["logo"]["name"];
						$errorPause = true;
					} else {
						$error = "Se presesntó un error al subir el archivo, constactese con el administrador, o intentelo desde el administrador de la plataforma.";
						$errorPause = true;
					}
				}
			}
		}
						//Si no se sube un logo
		else {
			$logo = '';
		}
		//////// una vez suba la foto ejecutar la query
		if($logo !== '') {
			 $sql = $con->prepare('UPDATE `config` SET `logo` = :file WHERE `id` = 1');
			 $sql->bindValue(':file', $foto);
			if ($sql->execute()) {
				$_SESSION['advertencia'] = 'Configuración guardada exitosamente';
			} else {
				$error = 'No es posible actualizar la configuración intente más tarde';
			}
		}
		/// 4. si no hay errores para pausar, redirigir.
		
	}
}


///////////////////////////////// FUNCIONES REUSABLES

// Logo
function getLogo() {
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT * FROM `config` WHERE id = "1" LIMIT 1');
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);
	$logofile = '../'.$results->logo;
	if(file_exists($logofile)) {	
		echo '<img src="../'.$results->logo.'">';	
	} else {
		echo $results->title;
	}
}
// Contacto
function contacto() {
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$sql = $con->prepare('SELECT `contacto` FROM `config` WHERE id = "1" LIMIT 1');
	$sql->execute();
	$results = $sql->fetch(PDO::FETCH_OBJ);
	echo $results->contacto;
}
// crear archivo index.html vacio
function fileHtml($fileloc) {
	$emptyFile = fopen($fileloc, "w") or die("No fue posible crear la carpeta");
	fwrite($emptyFile, "<!DOCTYPE html>\n");
	fwrite($emptyFile, "<html>\n");
	fwrite($emptyFile, "<head>\n");
	fwrite($emptyFile, "	<title></title>\n");
	fwrite($emptyFile, "</head>\n");
	fwrite($emptyFile, "<body>\n");
	fwrite($emptyFile, " \n");
	fwrite($emptyFile, "</body>\n");
	fclose($emptyFile);
}

function stripChars($str) {
	$unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
	$str = strtr( $str, $unwanted_array );
	return $str;
}


function updateTable($table,$row,$id,$value) {
	$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$query = 'UPDATE `'.$table.'` SET `'.$row.'` = "'.$value.'" WHERE `proyectid` = '.$id.'';
	$sql = $con->prepare($query);
	if(!$sql->execute()) {
		$_SESSION['error'] = 'Se presentó un error guardando en la base de datos';
		$_SESSION['errorPause'] = 'pause';
	}
}

function redirect($url) {
	if (!headers_sent()) {    
		header("Location: index.php?".$url);
		exit;
	} else {  
		echo '<script type="text/javascript">';
		echo 'window.location.href="index.php?"'.$url;
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url=index.php?"'.$url.'/>';
		echo '</noscript>'; exit;
	}
}

?>