<?php
include("../include/scriptdb/config.php");
include("../include/scriptdb/querys.php");
include("../include/phpscript/generales.php");
include("../include/plugins/imgUp/class_imgUpldr.php");

$acc = $_REQUEST["acc"];

$query= new Querys();
$conex = $query->getConection($CONFIG);

switch($acc){

	//sube imagenes del cliente
	case "setImgCliente":

		$idClient = trim($_REQUEST["id_cliente"]);
		$imgOld = trim($_REQUEST["imgOld"]);

		if($idClient=="" || $idClient=="0"){
			$response->status=false;
			$response->msg="Cliente Invalido";
			exit(json_encode($response));
		}

		if($_FILES['file']['tmp_name']==""){
			$response->status=false;
			$response->msg="Debe Seleccionar una Imagen";
			exit(json_encode($response));
		}

		//elimina la foto anterior
		if($imgOld!=""){
			if(file_exists($CONFIG['PATH_IMG_CLIENT'].$imgOld)){
				unlink($CONFIG['PATH_IMG_CLIENT'].$imgOld);
			}
		}


		$subir= new imgUpldr;
		$subir->_name = "C_".$idClient;
		$subir->_width = 640;
		$subir->_height = 480;
		$subir->_dest =$CONFIG['PATH_IMG_CLIENT'];
		$res = $subir->init($_FILES['file']);
		$NameImgClient = $subir->getNameImg();

		if($res=="ok"){
			$response->status=true;
			$response->msg="Imagen creada exitosamente";

			$query->UpdDB($conex,"site_upd_cliente_img",array($NameImgClient,$idClient));
		}else{
			$response->status=false;
			$response->msg = getMessageErrorUpload($res);
		}


		exit(json_encode($response));
	break;

	//sube imagenes del los usuarios del panel
	case "setImgUserClient":

		$idUser = trim($_REQUEST["id_user"]);
		$idClient = trim($_REQUEST["id_cliente"]);
		$imgOld = trim($_REQUEST["imgOld"]);


		if(($idClient=="" || $idClient=="0") || ($idUser=="" || $idUser=="0")){
			$response->status=false;
			$response->msg="Cliente Invalido";
			exit(json_encode($response));
		}

		//si desea eliminar la foto y desar sin imagenes el usuario
		if(intval($_REQUEST["DeleteImg"])==1){
			if(file_exists($CONFIG['PATH_IMG_USER_CLIENT'].$imgOld)){
				unlink($CONFIG['PATH_IMG_USER_CLIENT'].$imgOld);
			}

			$query->UpdDB($conex,"site_upd_img_user_cliente",array("",$idUser,$idClient));

			$response->status=true;
			$response->msg = "Imagen eliminada exitosamente";
			exit(json_encode($response));
		}


		//valida que se haya seleccionado una imagen
		if($_FILES['file']['tmp_name']==""){
			$response->status=false;
			$response->msg="Debe Seleccionar una Imagen";
			exit(json_encode($response));
		}


		//elimina la foto anterior
		if($imgOld!=""){
			if(file_exists($CONFIG['PATH_IMG_USER_CLIENT'].$imgOld)){
				unlink($CONFIG['PATH_IMG_USER_CLIENT'].$imgOld);
			}
		}

		$subir= new imgUpldr;
		$subir->_name = "uc_".$idUser."_".$idClient;
		$subir->_width = 200;
		$subir->_height = 200;
		$subir->_dest = $CONFIG['PATH_IMG_USER_CLIENT'];
		$res = $subir->init($_FILES['file']);
		$NameImgUser = $subir->getNameImg();

		if($res=="ok"){
			$response->status=true;
			$response->msg="Imagen creada exitosamente";

			$query->UpdDB($conex,"site_upd_img_user_cliente",array($NameImgUser,$idUser,$idClient));
		}else{
			$response->status=false;
			$response->msg = getMessageErrorUpload($res);
		}


		exit(json_encode($response));
	break;

	//sube imagenes del personal
	case "setImgPersonal":
		$Personal = trim($_REQUEST["id_personal"]);
		$imgOld = trim($_REQUEST["imgOld"]);

		if($Personal=="" || $Personal=="0"){
			$response->status=false;
			$response->msg="Personal Invalido";
			exit(json_encode($response));
		}

		if($_FILES['file']['tmp_name']==""){
			$response->status=false;
			$response->msg="Debe Seleccionar una Imagen";
			exit(json_encode($response));
		}

		//elimina la foto anterior
		if($imgOld!=""){
			if(file_exists($CONFIG['PATH_IMG_PERSONAL'].$imgOld)){
				unlink($CONFIG['PATH_IMG_PERSONAL'].$imgOld);
			}
		}


		$subir= new imgUpldr;
		$subir->_name = "P_".$Personal;
		$subir->_width = 200;
		$subir->_height = 200;
		$subir->_dest =$CONFIG['PATH_IMG_PERSONAL'];
		$res = $subir->init($_FILES['file']);
		$NameImgPersonal = $subir->getNameImg();

		if($res=="ok"){
			$response->status=true;
			$response->msg="Imagen creada exitosamente";

			$query->UpdDB($conex,"site_upd_PersonalImagen",array($NameImgPersonal,$Personal));
		}else{
			$response->status=false;
			$response->msg = getMessageErrorUpload($res);
		}


		exit(json_encode($response));
	break;

	//sube manual de equipo
	case "setManualEquipo":
		$id_manual = trim($_REQUEST["id_manual"]);

		if($id_manual=="" || $id_manual=="0"){
			$response->status=false;
			$response->msg="Manual Invalido";
			exit(json_encode($response));
		}

		if($_FILES['file']['tmp_name']==""){
			$response->status=false;
			$response->msg="Debe Seleccionar un Archivo";
			exit(json_encode($response));
		}

		if ($_FILES['file']['type'] !='application/pdf'){
			$response->status=false;
			$response->msg="Debe Seleccionar un Archivo PDF";
			exit(json_encode($response));
		}

		if( $_FILES['file']['size'] > 10000000 ) {
			$response->status=false;
			$response->msg="Archivo supera perso permitido";
			exit(json_encode($response));
		}

		if(file_exists($CONFIG['PATH_FILE_MANUAL']."ma_".$id_manual.".pdf")){
			unlink($CONFIG['PATH_FILE_MANUAL']."ma_".$id_manual.".pdf");
		}
		$query->UpdDB($conex,"site_upd_SetEquiposManual",array("ma_".$id_manual.".pdf",$id_manual));

		move_uploaded_file($_FILES["file"]["tmp_name"],$CONFIG['PATH_FILE_MANUAL']."ma_".$id_manual.".pdf");

		$response->status=true;
		$response->msg="Manual creado exitosamente";
		exit(json_encode($response));

	break;
}
?>