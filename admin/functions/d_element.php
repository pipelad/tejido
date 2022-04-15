<?php
session_start(); // INICIAR SESIÓN PARA MENSAJES DE CONFIRMACIÓN
define('tejido', TRUE); // PARA INCLUIR ARCHIVO DE CONFIGURACIÓN
include('../../config/config.php');

//// Borrar artículo
if (array_key_exists("deletearticulo", $_POST) AND $_POST['deletearticulo'] === "1") {
	$count = count($_POST);
	$idArticulo = 0;
	for ($i = 1; $i < $count; $i++) {
		$b = $i - 1; /// esto es debido a que el post enviado tiene adicionalmente la key del tipo de borrado, es decir tiene un elemento mas que los id.
		$idArticulo = "id".$b; /// CLAVE, AL CONSTRUIR LA STRING LIMITA A LOS ELEMENTOS DEL ARRAY QUE TIENEN COMO LLAVE LA PALABRA DEFINIDA
		//confirmar sql
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('DELETE FROM `articulo` WHERE `id` = :articulo');
		$articulo = $_POST[$idArticulo];
		$sql->bindParam(':articulo', $articulo);
		$sql->execute();
		//DEBUG QUERY
		//$sql->debugDumpParams();
	}
	if($count > 2) {
		$_SESSION['advertencia'] = 'Artículos borrados correctamente'; 
	} else {
		$_SESSION['advertencia'] = 'Artículo borrado correctamente'; 
	}
	

} 
//// Borrar region
else if (array_key_exists("deleteregion", $_POST) AND $_POST['deleteregion'] === "1") {
		/// print_r($_POST); 
		$count = count($_POST);
		$idRegion = 0;
		$rcur = '';
		$folder = '';
		$foto = '';
		$err = false;
		for ($i = 1; $i < $count; $i++) {
			$b = $i - 1; /// esto es debido a que el post enviado tiene adicionalmente la key del tipo de borrado, es decir tiene un elemento mas que los id.
			$idRegion = "id".$b; /// CLAVE, AL CONSTRUIR LA STRING LIMITA A LOS ELEMENTOS DEL ARRAY QUE TIENEN COMO LLAVE LA PALABRA DEFINIDA
			$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			
			/// 1. verificar si hay elementos con las regiones seleccionadas.
			$sql = $con->prepare('SELECT * FROM `region` WHERE `id` = :id');
			$id = $_POST[$idRegion];
			$sql->bindParam(':id', $id);
			if($sql->execute()) { 
				$row = $sql->fetch(PDO::FETCH_OBJ);
				$rcur = $row->nombre;
				$folder = $row->folder;
				$foto = $row->foto;
			}
			$sql = $con->prepare('SELECT `proyectid` FROM `proy_regionales` WHERE `region` = :region');
			$sql->bindParam(':region', $rcur);
			if($sql->execute()) {
				$row_count = $sql->rowCount();
				if($row_count !== 0) {
					if($err === false) {
						$_SESSION['error'] = '';
					}
					//echo 'hay: '.$row_count.' en: '.$rcur.'/// ';
					$_SESSION['error'] .= $rcur.', ';
					$err = true;
				} else {
					/// 2. eliminar elmentos relacionados con la region
					/// 2.1 borrar la base de datos
					$sql = $con->prepare('DELETE FROM `region` WHERE `id` = :id');
					$id = $_POST[$idRegion];
					$sql->bindParam(':id', $id);
					if($sql->execute()) {
						/// 2.2 borrar foto
						unlink('../'.$folder.'/'.$foto);
						/// 2.3 borrar index
						unlink('../'.$folder.'/index.html');
						/// 2.4 borrar carpeta
						rmdir('../'.$folder);
					}
				}
			}
				
		}

		if($err === true) {
			$_SESSION['error'] .= ' tiene(n) al menos un proyecto, no pude ser borrada.';
		}

		if($count > 2) {
			$_SESSION['advertencia'] = 'Regiones borradas correctamente';
		} else {
			if($err !== true) {
				$_SESSION['advertencia'] = 'Region borrada correctamente'; 	
			}
		}
		
}
//// Borrar programa
else if (array_key_exists("deleteprograma", $_POST) AND $_POST['deleteprograma'] === "1") {
	$count = count($_POST);
	$idArticulo = 0;
	for ($i = 1; $i < $count; $i++) {
		$b = $i - 1; /// esto es debido a que el post enviado tiene adicionalmente la key del tipo de borrado, es decir tiene un elemento mas que los id.
		$idArticulo = "id".$b; /// CLAVE, AL CONSTRUIR LA STRING LIMITA A LOS ELEMENTOS DEL ARRAY QUE TIENEN COMO LLAVE LA PALABRA DEFINIDA
		//confirmar sql
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('DELETE FROM `programas` WHERE `id` = :id');
		$id = $_POST[$idArticulo];
		$sql->bindParam(':id', $id);
		$sql->execute();
		//DEBUG QUERY
		//$sql->debugDumpParams();
	}
	if($count > 2) {
		$_SESSION['advertencia'] = 'Programas borrados correctamente'; 
	} else {
		$_SESSION['advertencia'] = 'Programa borrado correctamente'; 
	}
}

/// Borrar foto de proyecto
else if (array_key_exists("deleteFoto", $_POST) AND $_POST['deleteFoto'] !== "") {
	$file = '../'.$_POST['url'];
	if(file_exists($file)) {
		unlink($file);
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('UPDATE `proy_datostec` SET `foto` = "" WHERE `id` = :id');
		$id = $_POST['deleteFoto'];
		$sql->bindParam(':id', $id);
		$sql->execute();

		$_SESSION['advertencia'] = 'Fotografía borrada correctamente.';
	} else {
		$_SESSION['error'] = 'Se presentó un error con la ubicación de la foto, contáctese con el administrador.';
	}
}

/// Borrar foto de proyecto
else if (array_key_exists("deleteproyecto", $_POST) AND $_POST['deleteproyecto'] === "1") {
	print_r($_POST);
	$count = count($_POST);
	$idArticulo = 0;
	//// 1. Borrar elementos foreach
	for ($i = 1; $i < $count; $i++) {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		//// 1.0 hacer variables re-usables:
		$b = $i - 1; /// esto es debido a que el post enviado tiene adicionalmente la key del tipo de borrado, es decir tiene un elemento mas que los id.
		$idArticulo = "id".$b; /// CLAVE, AL CONSTRUIR LA STRING LIMITA A LOS ELEMENTOS DEL ARRAY QUE TIENEN COMO LLAVE LA PALABRA DEFINIDA
		$id = $_POST[$idArticulo];
		//// 1.1 Antes de borrar obtener region del proyecto y el folder.
		//////// 1.1.1 obtener la region del proyecto
		$sql = $con->prepare('SELECT `region` FROM `proy_regionales` WHERE `proyectid` = :id');
		$sql->bindParam(':id', $id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_OBJ);
		$curReg = $result->region;
		//////// 1.1.2 con la region obtener folder
		$sql = $con->prepare('SELECT `folder` FROM `region` WHERE `nombre` = :reg');
		$sql->bindParam(':reg', $curReg);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_OBJ);
		$folder = $result->folder;
		//// 1.2 Borrar de proy_regionales
		$sql = $con->prepare('DELETE FROM `proy_regionales` WHERE `proyectid` = :id');
		$sql->bindParam(':id', $id);
		$sql->execute();
		//// 1.3 Borrar de proy_académicos
		$sql = $con->prepare('DELETE FROM `proy_academicos` WHERE `proyectid` = :id');
		$sql->bindParam(':id', $id);
		$sql->execute();
		//// 1.4 Borrar de proyecto
		$sql = $con->prepare('DELETE FROM `proyecto` WHERE `proyectid` = :id');
		$sql->bindParam(':id', $id);
		$sql->execute();
		//// 1.5 Borrar foto
		$sql = $con->prepare('SELECT `foto` FROM `proy_datostec` WHERE `proyectid` = :id');
		$sql->bindParam(':id', $id);
		$sql->execute();
		$result = $sql->fetch(PDO::FETCH_OBJ);
		$foto = '../'.$folder.'/'.$result->foto;
		if(file_exists($foto)) {
			/// unlink
			unlink($foto);
		}
		//// 1.6 Borrar de proy_datostec
		$sql = $con->prepare('DELETE FROM `proy_datostec` WHERE `proyectid` = :id');
		$sql->bindParam(':id', $id);
		$sql->execute();
		//// 1.7 terminar y establecer session

		if($count > 2) {
			$_SESSION['advertencia'] = 'Proyectos borrados correctamente'; 
		} else {
			$_SESSION['advertencia'] = 'Proyecto borrado correctamente'; 
		}
	}

	/*
	
		$b = $i - 1;
		$idArticulo = "id".$b; /// CLAVE, AL CONSTRUIR LA STRING LIMITA A LOS ELEMENTOS DEL ARRAY QUE TIENEN COMO LLAVE LA PALABRA DEFINIDA
		//confirmar sql
		
		$sql = $con->prepare('DELETE FROM `articulo` WHERE `id` = :articulo');
		$articulo = $_POST[$idArticulo];
		$sql->bindParam(':articulo', $articulo);
		$sql->execute();
		//DEBUG QUERY
		//$sql->debugDumpParams();
	
	if($count > 2) {
		$_SESSION['advertencia'] = 'Artículos borrados correctamente'; 
	} else {
		$_SESSION['advertencia'] = 'Artículo borrado correctamente'; 
	}*/
}

else if (array_key_exists("deleteusuario", $_POST) AND $_POST['deleteusuario'] === "1") {
	print_r($_POST);
	$count = count($_POST);
	$idUser = 0;
	for ($i = 1; $i < $count; $i++) {
		$b = $i - 1; /// esto es debido a que el post enviado tiene adicionalmente la key del tipo de borrado, es decir tiene un elemento mas que los id.
		$idUser = "id".$b; /// CLAVE, AL CONSTRUIR LA STRING LIMITA A LOS ELEMENTOS DEL ARRAY QUE TIENEN COMO LLAVE LA PALABRA DEFINIDA
		//confirmar sql
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$sql = $con->prepare('DELETE FROM `usuarios` WHERE `id` = :id');
		$id = $_POST[$idUser];
		$sql->bindParam(':id', $id);
		$sql->execute();
		//DEBUG QUERY
		//$sql->debugDumpParams();
	}
	if($count > 2) {
		$_SESSION['advertencia'] = 'Usuarios borrados correctamente'; 
	} else {
		$_SESSION['advertencia'] = 'Usuario borrado correctamente'; 
	}
}

/// prohibir acceso directo al archivo
else {
	if (!headers_sent()) {    
	    header("Location: ../index.php");
	    exit;
	} else {  
	    echo '<script type="text/javascript">';
	    echo 'window.location.href="../index.php";';
	    echo '</script>';
	    echo '<noscript>';
	    echo '<meta http-equiv="refresh" content="0;url=../index.php" />';
	    echo '</noscript>'; exit;
	}
}

?>
