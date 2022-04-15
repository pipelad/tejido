<?php
//session_start(); // INICIAR SESIÓN PARA MENSAJES DE CONFIRMACIÓN
//define('tejido', TRUE); // PARA INCLUIR ARCHIVO DE CONFIGURACIÓN
//include('../../config/config.php');

///*********************** FALTA FINALIZAR

//// Borrar artículo
if (array_key_exists("deleteregion", $_POST) AND $_POST['deleteregion'] == 1) {
		print_r($_POST); 
		echo "hola";
		$_SESSION['advertencia'] = 'Region borrada correctamente'; 
		/*$count = count($_POST);
		$idRegion = 0;
		for ($i = 1; $i < $count; $i++) {
			$b = $i - 1;
			$idRegion = "id".$b; /// CLAVE, AL CONSTRUIR LA STRING LIMITA A LOS ELEMENTOS DEL ARRAY QUE TIENEN COMO LLAVE LA PALABRA DEFINIDA

			/// 2. Si no hay elementos borrar el elemento
			
				//confirmar sql
				$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$sql = $con->prepare('DELETE FROM `region` WHERE `id` = :id');
				$id = $_POST[$idRegion];
				$sql->bindParam(':id', $id);
				/// 3. eliminar elmentos relacionados con la region
				$sql->execute();
				}
				
		}

		if($count > 2) {
			$_SESSION['advertencia'] = 'Regiones borradas correctamente'; 
		} else {
			$_SESSION['advertencia'] = 'Region borrada correctamente'; 
		}*/
		
}
echo 'hola';

/// prohibir acceso directo al archivo
/*else {
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
}*/

?>
