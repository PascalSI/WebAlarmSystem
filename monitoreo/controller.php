<?php
include("../include/scriptdb/config.php");
include("../include/scriptdb/querys.php");
include("../include/phpscript/generales.php");
include("../include/phpscript/sessionAjax.php");

$acc= $_REQUEST['acc'];

$query= new Querys();
$conex = $query->getConection($CONFIG);

if($_SESSION["cliente"]["tipoUser"] == 3){
	$idEmpresa = $_SESSION["cliente"]["idEmpresa"];
	$masterEmp = 0;
}else{
	$idEmpresa = $_SESSION["user"]["idEmpresa"];
	$masterEmp = $_SESSION["user"]["master"];
}

switch($acc){
	//carga por procesar
	case "load_proccessin_signal":
		$optionCierre = 1;

		if($_SESSION["cliente"]["tipoUser"] == 3){

			$param.= " AND  (cliente in(SELECT id_cliente FROM t365_asociados_abonados a  ";
			$param.= " WHERE (id_asociado = '".$_SESSION["cliente"]["idAsociado"]."'))) ";

			$optionCierre = 0;

		}else{
			if($_SESSION["user"]["idEmpresa"]==$_SESSION["user"]["emp_monitorea"] || $_SESSION["user"]["emp_monitoreaCount"]>0){
				$param.= " AND (IdOperador = '".$_SESSION["user"]["idOperador"]."')";
			}else{
				$param.= " AND (id_empresa = '".$idEmpresa."') ";
				$optionCierre = 0;
			}
		}



		$MyRecord2 = $query->SelDB($conex,"site_sel_MonitoreoSenalesXProcesar",array($param));
		$i = $query->count_row($MyRecord2);

		while($r=$query->getdata_object($MyRecord2)){

			$colorBgAux = setColorBg($r->web_colorBg);
			$colorAux = setColor($r->web_color);
			$eventoAux = DecoEventoWS($r->Variante,$r->StrEvento,$r->protocolo);

			$style = "background:".$colorBgAux.";color:".$colorAux.";";

			$relInfo = '';
			$relInfo.= 'idTrama:"'.$r->id_trama.'",';
			$relInfo.= 'idCliente:"'.$r->id_cliente.'",';
			$relInfo.= 'Cuenta:"'.$r->cuen.'",';
			$relInfo.= 'Nombre:"'.clearString($r->NameCliente).'",';
			$relInfo.= 'evento:"'.clearString($eventoAux).'",';
			$relInfo.= 'eventD:"'.clearString($r->UserZona).'",';
			$relInfo.= 'fecha:"'.date_format($r->fecha,"d/m/Y H:i:s").'",';
			$relInfo.= 'handler:"SignalsProcesadas",';
			$relInfo.= 'style:"'.$style.'",';
			$relInfo.= 'pic:"'.$r->pic.'",';
			$relInfo.= 'idEvento:"'.$r->evento.'",';
			$relInfo.= 'pre:"PS-",';
			$relInfo.= 'grup:"'.$r->idGrupo.'",';
			$relInfo.= 'codi:"'.encode(7,$r->id_cliente).'",';
			$relInfo.= 'codA:"'.trim($r->codAlrm).'",';
			$relInfo.= 'codDesc:"'.trim($r->codDesc).'",';
			$relInfo.= 'dir:"'.clearString($r->direccion).'",';
			$relInfo.= 'ref:"'.clearString($r->referencia).'",';
			$relInfo.= 'lat:"'.$r->latitud.'",';
			$relInfo.= 'lon:"'.$r->longitud.'",';
			$relInfo.= 'img:"'.$r->img.'",';
			$relInfo.= 'telf:"'.$r->telf_local.'",';
			$relInfo.= 'movil:"'.$r->movil.'",';
			$relInfo.= 'back:"'.$colorBgAux.'",';
			$relInfo.= 'color:"'.$colorAux.'",';
			$relInfo.= 'stsPanel:"'.statusPanelCliente($r->staPanel).'",';
			$relInfo.= 'stsPanelC:"'.statusPanelClienteFont($r->staPanel).'",';
			$relInfo.= 'stsPanelF:"'.getFullDateShort($r->fechaStatusp,"d/m/Y H:i:s").'",';
			$relInfo.= 'claveMaster:"'.clearString($r->clavemaster).'",';
			$relInfo.= 'emp:"'.clearString($r->nombreempresa).'",';
			$relInfo.= 'id_emp:"'.clearString($r->id_empresa).'",';
			$relInfo.= 'llave:"'.clearString($r->llave).'",';
			$relInfo.= 'tipoevent:"'.clearString($r->tipoevent).'",';
			$relInfo.= 'idDisp:"'.clearString($r->numzonausuario).'",';
			$relInfo.= 'email:"'.clearString($r->email).'",';

			?>
				<tr  id="PS-<?php echo $r->id_trama;?>" align="left" rel-menu="xp" class="pointer contextMenu" style='<?php echo $style;?>' rel-info='<?php echo "{".$relInfo."}";?>' <?php if($optionCierre == 1){?> ondblclick='closeTrama({id:"<?php echo  "PS-".$r->id_trama;?>",tipo:1})' <?php } ?>>
                    <td class="count-ps"><?php echo $i;?></td>
                    <td><span class="info"><?php echo $r->cuen;?> - <?php echo substr($r->NameCliente,0,50)?></span></td>
                    <td><span class="info"><?php echo $r->evento;?>&nbsp;-&nbsp; <?php echo $eventoAux;?></span></td>
                    <td><span><?php echo $r->UserZona;?></span></td>
                    <td><span class="info"><?php echo date_format($r->fecha,"d/m/Y H:i:s");?></td>
                    <td >
                    	<?php if($_SESSION["user"]["tipoUser"] == 2 && $optionCierre == 1){?>
                    	<a class="delete btn blue btn-per btn-sm"  href="javascript:void(0)">
                        	<i class='fa fa-external-link'></i>
                        </a>
                        <?php } ?>
                     </td>
                </tr>
			<?php
			$i--;
		}
	break;

	//carga señales pendiente
	case "load_signal_pendientes":
		$optionCierre = 1;

		$i = 0;
		$count2 = 0;
		$param = "";

		if($_SESSION["cliente"]["tipoUser"] == 3){
			$param.= "AND  (cliente in(SELECT id_cliente FROM t365_asociados_abonados a  ";
			$param.= "WHERE (id_asociado = '".$_SESSION["cliente"]["idAsociado"]."'))) ";

			$optionCierre = 0;
		}else{
			if($_SESSION["user"]["idEmpresa"]==$_SESSION["user"]["emp_monitorea"] || $_SESSION["user"]["emp_monitoreaCount"]>0){
				$param.= " AND (IdOperador = '".$_SESSION["user"]["idOperador"]."')";
			}else{
				$param.= " AND (id_empresa = '".$idEmpresa."') ";
				$optionCierre = 0;
			}
		}

		$MyRecord4 = $query->SelDB($conex,"site_sel_MonitoreoSenalesPendientes",array($param));

		$i = $query->count_row($MyRecord4);
		$count2 = $query->count_row($MyRecord4);

		while($r=$query->getdata_object($MyRecord4)){

			$colorBgAux = setColorBg($r->web_colorBg);
			$colorAux = setColor($r->web_color);
			$eventoAux = DecoEventoWS($r->Variante,$r->StrEvento,$r->protocolo);

			$style = "background:".$colorBgAux.";color:".$colorAux.";";

			$relInfo = '';
			$relInfo.= 'idTrama:"'.$r->id_trama.'",';
			$relInfo.= 'idCliente:"'.$r->id_cliente.'",';
			$relInfo.= 'Cuenta:"'.$r->cuen.'",';
			$relInfo.= 'Nombre:"'.clearString($r->NameCliente).'",';
			$relInfo.= 'evento:"'.clearString($eventoAux).'",';
			$relInfo.= 'eventD:"'.clearString($r->UserZona).'",';
			$relInfo.= 'fecha:"'.date_format($r->fecha,"d/m/Y H:i:s").'",';
			$relInfo.= 'handler:"SignalsProcesadas",';
			$relInfo.= 'style:"'.$style.'",';
			$relInfo.= 'pic:"'.$r->pic.'",';
			$relInfo.= 'idEvento:"'.$r->evento.'",';
			$relInfo.= 'pre:"SP-",';
			$relInfo.= 'grup:"'.$r->idGrupo.'",';
			$relInfo.= 'codi:"'.encode(7,$r->id_cliente).'",';
			$relInfo.= 'codA:"'.trim($r->codAlrm).'",';
			$relInfo.= 'codDesc:"'.trim($r->codDesc).'",';
			$relInfo.= 'dir:"'.clearString($r->direccion).'",';
			$relInfo.= 'ref:"'.clearString($r->referencia).'",';
			$relInfo.= 'lat:"'.$r->latitud.'",';
			$relInfo.= 'lon:"'.$r->longitud.'",';
			$relInfo.= 'img:"'.$r->img.'",';
			$relInfo.= 'telf:"'.$r->telf_local.'",';
			$relInfo.= 'movil:"'.$r->movil.'",';
			$relInfo.= 'back:"'.$colorBgAux.'",';
			$relInfo.= 'color:"'.$colorAux.'",';
			$relInfo.= 'stsPanel:"'.statusPanelCliente($r->staPanel).'",';
			$relInfo.= 'stsPanelC:"'.statusPanelClienteFont($r->staPanel).'",';
			$relInfo.= 'stsPanelF:"'.getFullDateShort($r->fechaStatusp,"d/m/Y H:i:s").'",';
			$relInfo.= 'claveMaster:"'.clearString($r->clavemaster).'",';
			$relInfo.= 'emp:"'.clearString($r->nombreempresa).'",';
			$relInfo.= 'id_emp:"'.clearString($r->id_empresa).'",';
			$relInfo.= 'llave:"'.clearString($r->llave).'",';
			$relInfo.= 'tipoevent:"'.clearString($r->tipoevent).'",';
			$relInfo.= 'idDisp:"'.clearString($r->numzonausuario).'",';
			$relInfo.= 'email:"'.clearString($r->email).'",';

			?>
				<tr id="SP-<?php echo $r->id_trama;?>" style="<?php echo $style;?>"  class="pointer contextMenu" rel-menu='pen' rel-info='<?php echo "{".$relInfo."}";?>'  <?php if($optionCierre == 1){?>   ondblclick='closeTrama({id:"<?php echo "SP-".$r->id_trama;?>",tipo:2})' <?php } ?>>
                    <td ><?php echo $i;?></td>
                    <td ><?php echo $r->cuen;?> - <?php echo substr($r->NameCliente,0,50)?></td>
                    <td ><?php echo $r->evento;?>&nbsp;-&nbsp;<?php echo $eventoAux;?></td>
                    <td ><?php echo $r->UserZona;?></td>
                    <td ><?php echo date_format($r->fecha,"d/m/Y H:i:s");?></td>
                </tr>
			<?php
			$i--;
		}
		?>
			<input type="hidden" id="count-penx" value="<?php echo $count2;?>" />
		<?php
	break;

	//carga señales pendiente
	case "load_log_signal":
		$param = "  ";

		if($_REQUEST['MaxIdTramaLog']<>0){
			$MaxIdTramaLog = $_REQUEST['MaxIdTramaLog'];
			$param.= " AND (id_trama > '".$_REQUEST['MaxIdTramaLog']."') ";
		}

		if($_SESSION["cliente"]["tipoUser"] == 3){
			$param.= "AND  (cliente in(SELECT id_cliente FROM t365_asociados_abonados a  ";
			$param.= "WHERE (id_asociado = '".$_SESSION["cliente"]["idAsociado"]."'))) ";
		}else{
			if($_SESSION["user"]["idEmpresa"]==$_SESSION["user"]["emp_monitorea"] || $_SESSION["user"]["emp_monitoreaCount"]>0){
				$param.= " AND (EmpresaMonitorea = '".$idEmpresa."')";
			}else{
				$param.= " AND (id_empresa = '".$idEmpresa."') ";
			}
		}


		$MyRecord = $query->SelDB($conex,"site_sel_MonitoreoEstatico",array($param));

		while($r=$query->getdata_object($MyRecord)){
			if((int)$r->id_trama > (int)$MaxIdTramaLog){
				$MaxIdTramaLog = (int)$r->id_trama;
			}

			$html= $r->UserZona;

			$style = "background:".setColorBg($r->colorBg).";color:".setColor($r->color).";";
			$eventoAux = $r->StrEvento;

			$relInfo = '';
			$relInfo.= 'idCliente:"'.$r->cliente.'",';
			$relInfo.= 'Cuenta:"'.$r->cuen.'",';
			$relInfo.= 'Nombre:"'.clearString($r->NameCliente).'",';
			$relInfo.= 'pic:"'.$r->pic.'",';
			$relInfo.= 'evento:"",';
			$relInfo.= 'eventD:"'.$eventoAux.'",';
			$relInfo.= 'fecha:"",';
			$relInfo.= 'idTrama:"",';
			$relInfo.= 'handler:"",';
			$relInfo.= 'style:"'.$style.'",';
			$relInfo.= 'idEvento:"",';
			$relInfo.= 'pre:"LO-",';
			$relInfo.= 'codi:"'.encode(7,$r->cliente).'"';

			$nameOperador = clearString($r->operador)=="" ? "Sistema" : "Oper: ".clearString($r->operador);

			?>
				<tr  id="LO-<?php echo $r->id_trama;?>" style="<?php echo $style;?>"
					<?php if($r->Obser!="SO" && trim($r->Obser)!=""){?> class="pointer"  rel-info='<?php echo "{".$relInfo."}";?>' ondblclick='getCommentOpe({id:"<?php echo "LO-".$r->id_trama;?>",tipo:3})'
					<?php } /*else{ ?>
					 ondblclick='closeTrama({id:"<?php echo "LO-".$r->id_trama;?>",tipo:3})'
					<?php }*/ ?>
					 >
                    <td   class="count-sl">--</td>
                    <td  ><?php echo $r->cuen;?> - <?php echo substr($r->NameCliente,0,50)?></td>
                    <td >
					<span  <?php if($r->Obser!="SO" && trim($r->Obser)!=""){?>	class="negrita" <?php } ?>>
						<?php echo $r->codEvento;?>&nbsp;-&nbsp;<?php echo $eventoAux;?>
                    	<?php if($r->Obser!="SO" && trim($r->Obser)!=""){?> (<a href="javascript:void(0)"  >Ver</a>)<?php } ?>
					</span>
                    </td>
                    <td ><?php echo $html;?></td>
                    <td ><?php echo date_format($r->fecha,"d/m/Y H:i:s");?></td>
                 </tr>
			<?php
		}

		?>
			<script>
				system.MaxIdTramaLog = "<?php echo $MaxIdTramaLog;?>";
			</script>
		<?php
	break;

	//carga señales procesadas
	case "load_signal_procesadas":
		$MaxDateTrama = "";$paramDates="";

		$param = " ";

		if($_REQUEST["MaxDateTrama"]!=""){
			$MaxDateTrama = $_REQUEST["MaxDateTrama"];
			$dateAux= date("Y-m-d H:i:s",$_REQUEST["MaxDateTrama"]);
			if($dateAux!=""){
				$param.=" and (Fecha_proc > CONVERT(datetime,'".$dateAux.".999', 21)) ";
			}
		}

		if($_SESSION["cliente"]["tipoUser"] == 3){
			$param.= "AND  (cliente in(SELECT id_cliente FROM t365_asociados_abonados a  ";
			$param.= "WHERE (id_asociado = '".$_SESSION["cliente"]["idAsociado"]."'))) ";
		}else{
			if($_SESSION["user"]["idEmpresa"]==$_SESSION["user"]["emp_monitorea"] || $_SESSION["user"]["emp_monitoreaCount"]>0){
				$param.= " AND (EmpresaMonitorea = '".$idEmpresa."')";
			}else{
				$param.= " AND (id_empresa = '".$idEmpresa."') ";
			}
		}

		$MyRecord = $query->SelDB($conex,"site_sel_MonitoreoSenalesProcesadas",array($param));

		while($r=$query->getdata_object($MyRecord)){


			$html=$r->UserZona;

			$style = "background:".setColorBg($r->colorBg).";color:".setColor($r->color).";";

			if($MaxDateTrama == ""){
				$MaxDateTrama = strtotime(date_format($r->Fecha_proc,"Y/m/d H:i:s"));
			}

			if(strtotime(date_format($r->Fecha_proc,"Y/m/d H:i:s")) > $MaxDateTrama){
				$MaxDateTrama = strtotime(date_format($r->Fecha_proc,"Y/m/d H:i:s"));
			}

			$eventoAux = $r->StrEvento;

			$relInfo = '';
			$relInfo.= 'idCliente:"'.$r->cliente.'",';
			$relInfo.= 'Cuenta:"'.$r->cuen.'",';
			$relInfo.= 'Nombre:"'.clearString($r->NameCliente).'",';
			$relInfo.= 'pic:"'.$r->pic.'",';
			$relInfo.= 'evento:"",';
			$relInfo.= 'eventD:"'.$eventoAux.'",';
			$relInfo.= 'fecha:"",';
			$relInfo.= 'idTrama:"",';
			$relInfo.= 'handler:"",';
			$relInfo.= 'style:"'.$style.'",';
			$relInfo.= 'idEvento:"",';
			$relInfo.= 'pre:"PR-",';
			$relInfo.= 'codi:"'.encode(7,$r->cliente).'"';

			$nameOperador = clearString($r->operador)=="" ? "Sistema" : "Oper: ".clearString($r->operador);
			?>
			<tr align="left" style="<?php echo $style;?>" id="PR-<?php echo $r->id_trama;?>"  class="pointer"  rel-info='<?php echo "{".$relInfo."}";?>' ondblclick='getCommentOpe({id:"<?php echo "PR-".$r->id_trama;?>"})' >
                    <td class="count-pro">--</td>
                    <td ><?php echo $r->cuen;?> - <?php echo substr($r->NameCliente,0,50)?></td>
                    <td >
					<span  <?php if($r->Obser!="SO" && trim($r->Obser)!=""){?>	class="negrita" <?php } ?>>
						<?php echo $eventoAux;?>
						<?php if($r->Obser!="SO" && trim($r->Obser)!=""){?> (<a href="javascript:void(0)"  >Ver</a>)<?php } ?>
					</span>
                    </td>
                    <td ><?php echo $html;?></td>
                    <td ><?php echo date_format($r->fecha,"d/m/Y H:i:s");?></td>
                    <td ><?php echo date_format($r->Fecha_proc,"d/m/Y H:i:s");?></td>
                    <td ></td>
                </tr>
			<?php
		}
		?>
			<script>
				system.MaxDateTramaProc = "<?php echo $MaxDateTrama?>";
			</script>
		<?php
	break;

	//pase señal a señal pendiente
	case "update_pendiente":
		if($_POST["id"]!=""){
			if($_SESSION["cliente"]["tipoUser"] == 3){

				//registra log
				RegLog(array(0,$_SESSION["cliente"]["idAsociado"],3,244,$_POST["det"]));

			}else{

				//registra log
				RegLog(array($_SESSION["user"]["idOperador"],$_POST["idC"],1,101,$_POST["det"]));

			}

			//cambia estatus a la señal
			$query->UpdDB($conex,"site_upd_TramaStatus",array(4,"id_trama",$_POST["id"]));

			//guarda primer comentario de ser necesario
			setObservacion($_POST["id"],"Pendiente: ".$_POST["coment"]);


			echo "ok";
		}
	break;

	case "cierre-signal": //case cerrar señal

		$subAcc = $_POST["subAcc"];

		if($_POST["idC"]!=0){
			$tipoCAux = 1;
		}else{
			$tipoCAux = 0;
		}

		if($_POST["id"]!="" && $_POST["coment"]!=""){
			setObservacion($_POST["id"],"Procesada: ".$_POST["coment"]);

			$query->InsDB($conex,"site_ins_TramaProcesadas",array("Procesada: ".$_POST["coment"],"id_trama",$_POST["id"],$_SESSION["user"]["idOperador"],$_SESSION["user"]["idEmpresa"]));

			$query->UpdDB($conex,"site_upd_TramaStatus",array(2,"id_trama",$_POST["id"]));
		}


		//registra log

		if($_SESSION["cliente"]["tipoUser"] == 3){
			RegLog(array(0,$_SESSION["cliente"]["idAsociado"],3,245,$_POST["coment"]));
		}else{
			RegLog(array($_SESSION["user"]["idOperador"],$_POST["idC"],$tipoCAux,$_POST["idac"],$_POST["coment"]));
		}

		echo "ok";
	break;

	case "load_data_cliente":
		$json = new stdClass();
		$NumEmg = array();
		$Users = array();
		$Zonas = array();

		$SelectNumEmerg = $query->SelDB($conex,"site_sel_MonitoreoAsistencia",array($_GET["id"]));
		while($rNE=$query->getdata_object($SelectNumEmerg)){
			unset($rAux);

			$rAux->numero = clearString($rNE->numero);
			$rAux->descript = clearString($rNE->descript);
			$rAux->ob = clearString($rNE->observacion);

			$NumEmg[] = $rAux;
		}

		$json->numEmg = $NumEmg;


		$SelectUsuariosC = $query->SelDB($conex,"site_sel_UsuariosMonitoreo",array($_GET["id"],"order by CASE WHEN ISNUMERIC(u.id_user) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(u.id_user) = 1 THEN CAST(u.id_user AS INT) ELSE 0 END asc"));

		while($rU=$query->getdata_object($SelectUsuariosC)){
			unset($rAux);
			$rAux->id = $rU->id_user;
			$rAux->nom = trim($rU->nombre)." ".trim($rU->apellido);
			$rAux->movil = trim($rU->movil);
			$rAux->clavevoz = trim($rU->clavevoz);
			$rAux->parents = trim($rU->descrip);

			if(trim($rU->imagen)!=""){
				$imgAux = encode(5,$rU->imagen);
			}else{
				$imgAux = "";
			}

			$rAux->img = trim($imgAux);

			$Users[] = $rAux;
		}

		$json->users = $Users;

		$SelectZonasC = $query->SelDB($conex,"site_sel_ZonasMonitoreo",array($_GET["id"],"order by CASE WHEN ISNUMERIC(id_zona) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(id_zona) = 1 THEN CAST(id_zona AS INT) ELSE 0 END asc"));
		while($rZ=$query->getdata_object($SelectZonasC)){
			$Zonas[] = $rZ;
		}

		$json->zonas = $Zonas;

		echo json_encode($json);
	break;

	//carga vehiculos
	case "load_vehiculos":
		$vFilas = array();

		$MyrecordData = $query->SelDB($conex,"site_sel_getVehiculosMaps",array($param));
		while($r=$query->getdata_object($MyrecordData)){
			unset($rAux);
			$rAux->id = $r->id_vehiculo;
			$rAux->id_empresa = $r->id_empresa;
			$rAux->codigo_gps = $r->codigo_gps;
			$rAux->alias = $r->alias;
			$rAux->placa = $r->placa;
			$rAux->sim = $r->sim;
			$rAux->IdEventoGPS = $r->IdEventoGPS;
			$rAux->FechaGPS = date_format($r->FechaGPS,"d/m/Y H:i:s");
			$rAux->Lat = $r->Lat;
			$rAux->Logi = $r->Logi;
			$rAux->Velocidad = $r->Velocidad;
			$rAux->id_tipo_vehiculos = $r->id_tipo_vehiculos;
			$rAux->minute = $r->minute;
			$rAux->iconMap = $r->iconMap;
			$rAux->marca = $r->marca;
			$rAux->modelo = $r->modelo;
			$rAux->imagen = $r->imagen;
			$rAux->anio = $r->anio;
			$rAux->color = $r->color;



			$vFilas[] = $rAux;
		}

		echo json_encode(array(
			"aaData"=>$vFilas
		));
	break;

	//agrega comentarios a una señal
	case "add_comentario":
		setObservacion($_POST["id"],"Pendiente: ".$_POST["coment"]);
		echo "ok";
	break;

	case "load_cam_cliente_moni":
		$retunHtml = "";
		$chanelAlarm = "";
		$MyrecordData = $query->SelDB($conex,"site_sel_GetCamClienteMoni",array($_REQUEST["q"],$_REQUEST["idCliente"]));
		while($r=$query->getdata_object($MyrecordData)){
			if($r->tipo=="IP"){
				$icono = get_IconModoCam(1);
				$click = "onclick='showLinkCam({puerto:\"".$r->puerto."\",ip:\"".$r->ip."\"})'";
			}else{
				$chanelAlarm.=",".$r->id_channel;
				$icono = get_IconModoCam(2);
				$click = "onclick='showCamra({id:\"".encode64_asp($r->id_cctv)."\",channel:\"".encode64_asp($r->channel)."\",desc:\"".$r->nombredvr." - ".$r->nombrecam."\"})'";
			}
			$retunHtml.='<li  class="cie-list-cam-item">';
			$retunHtml.='<a href="javascript:;" '.$click.' ><i class="'.$icono.'"></i>'.$r->nombredvr.'-';
			$retunHtml.=$r->nombrecam.'</a></li>';
		}

		if(trim($retunHtml)==""){
			$retunHtml.='<li  class="cie-list-cam-item">';
			$retunHtml.='<a href="javascript:;">Sin Camaras asociadas</a></li>';
		}

		$htmlTodas = "";
		$MyrecordDataC = $query->SelDB($conex,"site_sel_GetCountCamarasRSTP",array($_REQUEST["idCliente"]));
		if($query->count_row($MyrecordDataC)>0){
			$chanelAlarm= encode64_asp(ltrim($chanelAlarm,","));

			$htmlTodas.='<li  class="cie-list-cam-item">';
			$htmlTodas.='<a href="javascript:;" onclick="showAllCamaras(this)" rel-channel="'.$chanelAlarm.'"  ><i class="fa  fa-table"></i> Ver Todas</a></li>';
		}

		echo $htmlTodas.$retunHtml;
	break;

	case "load_img_cliente_moni":
		$retunHtml = "";

		$MyrecordData = $query->SelDB($conex,"site_sel_GetImgZonaClientMoni",array($_REQUEST["q"],$_REQUEST["idCliente"]));
		$i=1;
		while($r=$query->getdata_object($MyrecordData)){

			$retunHtml.='<li  class="cie-list-img-item">';
			$retunHtml.='<a href="javascript:;" onclick="viewImgZonaOpen(\''.encode(5,$r->imagen).'\',\''.$i.'\')" ><i class="fa  fa-image"></i> Imagen '.$i.'</a></li>';

			$i++;
		}

		if(trim($retunHtml)==""){
			$retunHtml.='<li  class="cie-list-img-item">';
			$retunHtml.='<a href="javascript:;">Sin Imagenes asociadas</a></li>';
		}

		echo $retunHtml;
	break;

	case "send_sms_monitoreo":
		$query->InsDB($conex,"site_ins_SMSSalida",array(
			"id_cliente"=>trim($_REQUEST["id_cliente"]),
			"movil"=>trim($_REQUEST["numero"]),
			"sms"=>trim($_REQUEST["sms"])));

		setObservacion($_REQUEST["id"],"Se envio SMS: ".trim($_REQUEST["sms"]));

		echo "ok";
	break;

	case "send_email_monitoreo":
		$query->InsDB($conex,"site_ins_EMAILSalida",array(
			"id_cliente"=>trim($_REQUEST["id_cliente"]),
			"email"=>trim($_REQUEST["mail"]),
			"mensaje"=>trim($_REQUEST["texto"]),
			"asunto"=>trim($_REQUEST["asunto"])
		));

		setObservacion($_REQUEST["id"],"Se envio Correo: ".trim($_REQUEST["texto"]));

		echo "ok";
	break;

	case "load_Comentarios_Signal":
		$ObserSignail = $query->SelDB($conex,"site_sel_SignalesObservacion",array(trim($_GET["trama"])));

		while($ro=$query->getdata_object($ObserSignail)){
            ?>
                <tr>
                    <td><?php echo date_format($ro->fecha,"d/m/Y H:i:s");?></td>
                    <td><?php echo $ro->observacion;?></td>
                    <td><?php echo $ro->nombre;?></td>
                </tr>
            <?php
        }
	break;

	case "loadNumberUserContact":
		if($_GET["t"]=="2"){
			$SelectUsuariosC = $query->SelDB($conex,"site_sel_UsuariosMonitoreo",array($_GET["cl"],"order by CASE WHEN ISNUMERIC(u.id_user) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(u.id_user) = 1 THEN CAST(u.id_user AS INT) ELSE 0 END asc"));

			while($rU=$query->getdata_object($SelectUsuariosC)){
				unset($rAux);
				if(trim($rU->movil)!=""){
					$rAux->nom = trim($rU->nombre)." ".trim($rU->apellido);
					$rAux->movil = trim($rU->movil);
					$Users[] = $rAux;
				}

			}

			$json = $Users;
		}else{
			$SelectNumEmerg = $query->SelDB($conex,"site_sel_MonitoreoAsistencia",array($_GET["cl"]));
			while($rNE=$query->getdata_object($SelectNumEmerg)){
				unset($rAux);

				if(clearString($rNE->numero)!=""){
					$rAux->movil = clearString($rNE->numero);
					$rAux->nom = clearString($rNE->descript);
					$NumEmg[] = $rAux;
				}
			}

			$json = $NumEmg;
		}

		echo json_encode($json);
	break;

	case "loadEmailUserContact":
		$SelectUsuariosC = $query->SelDB($conex,"site_sel_UsuariosMonitoreo",array($_GET["cl"],"order by CASE WHEN ISNUMERIC(u.id_user) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(u.id_user) = 1 THEN CAST(u.id_user AS INT) ELSE 0 END asc"));

		while($rU=$query->getdata_object($SelectUsuariosC)){
			unset($rAux);
			if(trim($rU->email)!=""){
				$rAux->nom = trim($rU->nombre)." ".trim($rU->apellido);
				$rAux->email = trim($rU->email);
				$Users[] = $rAux;
			}

		}

		$json = $Users;

		echo json_encode($json);
	break;
}

function setObservacion($idx,$ob){
	global $query,$conex;

	if(trim($ob)!=""){

		$idArray = explode(",",$idx);
		foreach ($idArray as $valor) {

			$query->InsDB($conex,"site_ins_TramaObservacion",array(
			"trama"=>str_replace("'","",$valor),
			"observacion"=>trim($ob),
			"idoperador"=>$_SESSION["user"]["idOperador"]));
		}
	}
}
?>