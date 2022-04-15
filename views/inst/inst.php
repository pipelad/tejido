<?php

if(!defined('tejido')) {
   header("Location: index.php");
}


/// VARIABLES GLOBALES ///////////////////////////////////////////////////////
$error = '';
$errors = [];


/// verificación basica de contenidos en post, aunque espacios son requeridos mejor verificar.
if(array_key_exists('host', $_POST) AND $_POST['host'] !== "") {
	define ('DB_HOST', $_POST['host']); 
	define ('DB_USER', $_POST['dbuser']); 
	define ('DB_PASS', $_POST['dbpassword']); 
	define ('DB_NAME', $_POST['database']);
	try {
		$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
		$query = $con->prepare("SELECT * FROM `usuarios`");
		$query->execute();
		$cuenta = $query->rowCount();
		// echo $cuenta;
		if ($cuenta !== 0) {
			/// error básico
			$error .= 'Esta base de datos ya está siendo usada, asegúrese de usar una base de datos limpia para poder instalar la app';
		} else  {
			//// crear tablas en la base de datos:

			$table1 = "CREATE TABLE `usuarios` (
				`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`nombrecompleto` TINYTEXT NOT NULL,
				`documento` INT NOT NULL,
				`usuario` VARCHAR(30) NOT NULL,
				`tipodoc` VARCHAR(5) NOT NULL,
				`password` VARCHAR(50) NOT NULL,
				`usergroup` VARCHAR(20) NOT NULL,
				`fecha_reg` DATE
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table2 = "CREATE TABLE `config` (
				`id` INT(1) NOT NULL PRIMARY KEY,
				`titulo` VARCHAR(60) NOT NULL,
				`contacto` VARCHAR(60) NOT NULL,
				`logo` VARCHAR(50) NOT NULL
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table3 = "CREATE TABLE `articulo` (
				`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`titulo` VARCHAR(60) NOT NULL,
				`contenido` TEXT NOT NULL,
				`autor` VARCHAR(60) NOT NULL 
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table4 = "CREATE TABLE `region` (
				`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`nombre` VARCHAR(50) NOT NULL,
				`foto` VARCHAR(50) NOT NULL,
				`folder` VARCHAR(50) NOT NULL
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table5 = "CREATE TABLE `programas` (
				`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`nombre` VARCHAR(80) NOT NULL
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table6 = "CREATE TABLE `proy_datostec` (
				`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`proyectid` INT(11) NOT NULL,
				`foto` VARCHAR(80) NOT NULL,
				`fecha` DATE,
				`fecha-final` DATE 
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table7 = "CREATE TABLE `proy_regionales` (
				`proyectid` INT(11) NOT NULL PRIMARY KEY,
				`municipio` VARCHAR(70) NOT NULL,
				`cau` VARCHAR(70) NOT NULL,
				`region` VARCHAR(70) NOT NULL
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table8 = "CREATE TABLE `proy_academicos` (
				`proyectid` INT(11) NOT NULL PRIMARY KEY,
				`programas` TEXT NOT NULL,
				`docente` VARCHAR(70) NOT NULL,
				`espacioacad` VARCHAR(300) NOT NULL,
				`grupoinvest` VARCHAR(300) NOT NULL,
				`beneficiarios` INT(15) NOT NULL
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			$table9 = "CREATE TABLE `proyecto`(
				`proyectid` INT(11) NOT NULL PRIMARY KEY,
				`titulo` TEXT NOT NULL,
				`objetivo` TEXT NOT NULL,
				`tipologia` TEXT NOT NULL,
				`instituciones` TEXT NOT NULL
			) CHARACTER SET utf8 COLLATE utf8_general_ci";

			$tables = [$table1, $table2, $table3, $table4, $table5, $table6, $table7, $table8, $table9];

			foreach($tables as $k => $sql){
			    // pasar por todas las tablas y crearlas.
			    $query = $con->prepare($sql);
				$query->execute();
				// $query->debugDumpParams();
			    // log de errores
			    if(!$query){
			       array_push($errors, "Ocurrio un error creando la base de datos ".$k.", contacte al administrador para verificar");
			    }
			}

			// echo count($errors);

			if(count($errors) === 0) {
				/// Si no hay errores continuar ---> crear usuario admon
				$con = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$query = $con->prepare("INSERT INTO `usuarios` (`nombrecompleto`, `documento`, `usuario`, `tipodoc`, `password`, `usergroup`, `fecha_reg`) VALUES (:nombre,:docu,:user,:tipo,:pass,:group,:fecha)");
				$nombre = $_POST['name'];
				$docu = $_POST['documento'];
				$user = $_POST['username'];
				$tipo = $_POST['tipo'];
				$pass = $_POST['userpasword'];
				$group = 'SuperAdmin';
				$fecha = date("Y-m-d");
				if ($query->execute(array(':nombre' => $nombre, ':docu' => $docu, ':user' => $user, ':tipo' => $tipo, ':pass' => $pass, ':group' =>$group, ':fecha' => $fecha))) {
					$id = $con->lastInsertId();
					$query = $con->prepare('UPDATE `usuarios` SET `password` = :pass WHERE id = :id LIMIT 1');
					$md5pasword = md5(md5($id).$pass);
					if ($query->execute(array(':pass' => $md5pasword, 'id' => $id))) {
						/// USUARIO ADMON CREADO ----> CONTINUAR CON LA INSTALACIÓN.
						//si se sube un logo
						$query = $con->prepare("INSERT INTO `config` (`id`, `titulo`, `contacto`, `logo`) VALUES ('1',:titulo,:contacto,:logo)");
						$titulo = $_POST['titulo'];
						$contacto = $_POST['contacto'];
						$logo = '';

						/// definir la ruta para el archivo del logo subido.
						if (array_key_exists("logo", $_FILES) AND $_FILES['logo']['name'] != "") {
							if ($_FILES["logo"]["error"] > 0) {
					        // si hay un error subiendo el archivo
					        $error = "Se presesnto un error con el logo ".$_FILES["logo"]["error"];
					    	} else {
					    		// check if file already exit in "images" folder.
						        if (file_exists("img/".$_FILES["logo"]["name"])) {
						            $error = "El archivo ".$_FILES["logo"]["name"]." ya existe.";
						        } else {
						        	//move_uploaded_file guarda la imagen permanentemente en el lugar adecuado.
					            	if(move_uploaded_file($_FILES["logo"]["tmp_name"], "img/".$_FILES["logo"]["name"])) {
					                	// If file has uploaded successfully, store its name in data base
					                    $logo = "img/".$_FILES["logo"]["name"];
					                } else {
					                	$error = "Se presesntó un error al subir el archivo, constactese con el administrador, o intentelo desde el administrador de la plataforma.";
					                }
						        }
					    	}
						}
						//Si no se sube un logo
						else {
							$logo = '';
						}
						if($query->execute(array(':titulo' => $titulo, ':contacto' => $contacto, ':logo' => $logo))); {
							// contnuar ---> crear archivo de configuración
							$configfile = fopen("config/config.php", "w") or die("No se puede escribir el archivo de configruacion intente de nuevo o contactese con el administrador!");
							fwrite($configfile, "<?php\n");
							fwrite($configfile, " \n");
							fwrite($configfile, "if(!defined('tejido')) {\n");
							fwrite($configfile, "   header('Location: index.php');\n");
							fwrite($configfile, "} \n");
							fwrite($configfile, " \n");
							fwrite($configfile, "define ('DB_HOST', '".$_POST['host']."'); \n");
							fwrite($configfile, "define ('DB_USER', '".$_POST['dbuser']."'); \n");
							fwrite($configfile, "define ('DB_PASS', '".$_POST['dbpassword']."'); \n");
							fwrite($configfile, "define ('DB_NAME', '".$_POST['database']."'); \n");
							fwrite($configfile, " \n");
							fwrite($configfile, "?>\n");
							fclose($configfile);

							$_SESSION['endInstall'] = '1';
						}

						//
					}
				} else {
					$error = "No fue posible agregar el usuario, intente más tarde. No olvide limpiar la base de datos para evitar errores en la instalación";
				}

			}



		}
		
		//$query->debugDumpParams();

	} catch (PDOException $e) {
		$error .= 'Falló la conexión: ' . $e->getMessage();
	}
}

// print_r($_POST);

// $_SESSION['endInstall'] = '1';

?>


<?php 

if(array_key_exists('endInstall', $_SESSION)) { 

?>
	<div id="install_app">
		<div id="install_wrap">
			<div id="install_done">
				<div id="done_title"><i class="far fa-check-circle"></i> Instalación terminada</div>
				<div id="done_buttons">
					<a href="index.php"><i class="far fa-eye"></i> ver la portada</a>
					<a href="admin"><i class="fas fa-arrow-alt-circle-right"></i> ir al administrador</a>
				</div>
			</div>
		</div>
	</div>
<?php 
	unset($_SESSION['endInstall']);
} 

else {

?>

	<div id="install_app">
		<?php 

		if($error !== '') {
			echo '<div class="install_error"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
		}
		if(count($errors) !== 0) {
			echo '<div class="install_error"><i class="fas fa-exclamation-triangle"></i> ';
			echo implode ("// ", $errors);
			echo '</div>';
		}

		?>
		<div id="install_wrap">

			<form id="install" method="post" enctype="multipart/form-data">
				
				<div id="install_tit">INSTALACIÓN EN UN PASO</div>

				<div id="dbinfo">
					<div class="formtitle"><i class="fab fa-usb"></i> Conectar con la base de datos</div>
					<label for="host">Host</label><input required type="text" name="host" value="<?php if(isset($_POST['host']) AND $_POST['host'] !== "") { echo($_POST['host']); } ?>">
					<label for="dbuser">Usuario</label><input required type="text" name="dbuser" value="<?php if(isset($_POST['dbuser']) AND $_POST['dbuser'] !== "") { echo($_POST['dbuser']); } ?>">
					<label for="dbpassword">Contraseña base de datos</label><input required type="text" name="dbpassword" value="<?php if(isset($_POST['dbpassword']) AND $_POST['dbpassword'] !== "") { echo($_POST['dbpassword']); } ?>">
					<label for="database">Base de datos</label><input required type="text" name="database" value="<?php if(isset($_POST['database']) AND $_POST['database'] !== "") { echo($_POST['database']); } ?>">
				</div>

				<div id="userinfo">
					<div class="formtitle"><i class="far fa-user"></i> Usuario Administrativo</div>
					<label for="name">Nomber y Apellido</label><input required type="text" name="name" placeholder="Ej. Andrés Restrepo" value="<?php if(isset($_POST['name']) AND $_POST['name'] !== "") { echo($_POST['name']); } ?>">
					<label for="username">Nombre de Usuario</label><input required type="text" name="username" placeholder="Ej. andres.restrepo minimo 8 máximo 15 letras" minlength="8" maxlength="15" value="<?php if(isset($_POST['username']) AND $_POST['username'] !== "") { echo($_POST['username']); } ?>">
					<label for="tipo">Tipo de documento</label>
					<select name="tipo">
						<option selected value="cc">cédula de ciudadanía</option>
						<option value="ce">Cédula de extranjería</option>
					</select>
					<label for="documento">Numero de documento</label><input required type="text" name="documento" placeholder="Solo números" maxlength="15" pattern="\d*" value="<?php if(isset($_POST['documento']) AND $_POST['documento'] !== "") { echo($_POST['documento']); } ?>">
					<label for="userpasword">Contraseña</label><input required type="password" name="userpasword">
					<label for="confirmPassword">Confirmar contraseña</label><input required type="password" name="confirmPassword">
				</div>

				<div id="userinfo">
					<div class="formtitle"><i class="fas fa-wrench"></i> Configure la APP</div>
					<label for="titulo">Sitio:</label><input required type="text" name="titulo" value="<?php if(isset($_POST['titulo']) AND $_POST['titulo'] !== "") { echo($_POST['titulo']); } ?>">
					<label for="contacto">Numero de documento</label>
					<div id="textarea">
						<textarea name="contacto"></textarea>
					</div>
					<label for="logo">Subir un logo </label><input id="fileupload" type="file" name="logo" accept="image/*">
				</div>

				<button type="submit" name="submit" id="submit" value="install"><i class="far fa-hand-point-right"></i> Enviar</button>

			</form>

			<script src="https://cdn.tiny.cloud/1/er6yiuwafgvsns3yzan7r4y7cddcnt5ufwx7oxt3u48f6hrx/tinymce/5/tinymce.min.js"></script>
			<script type="text/javascript">
				tinymce.init({
					selector: 'textarea',
					languae: 'es',
					language_url : 'template/js/es.js' 
				});
			</script>
		</div>
	</div>

<?php } ?>

