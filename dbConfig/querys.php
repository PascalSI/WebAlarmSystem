<?php

class Querys {
	public $conex;
	public $host;
	public $user;
	public $pass;
	public $nombre;
	public $debug;

	function getConection($config){

		$this->debug=$config["DEBUG"];
		$this->host=$config["DB_HOST"];
		$this->user=$config["DB_USUARIO"];
		$this->pass=$config["DB_PASS"];
		$this->nombre=$config["DB_CHEMA"];

		$db_info = array('Database'=>$this->nombre, 'UID'=>$this->user, 'PWD'=>$this->pass, "CharacterSet" => "UTF-8");
	    $db_link = sqlsrv_connect($this->host, $db_info);
		return $db_link;
	}

	function SelDB($conn,$strQuery,$args,$op=array()){

		$db_link_ = $conn;

		if(!$db_link_){
			die(print_r( sqlsrv_errors(), true));
		}

		$var_auto = false;

		switch ($strQuery){

			case "site_sel_LoginClientes":
				$sql_statement= "SELECT  E.id_empresa, E.id_pais,E.logo, C.id_cliente, C.nombre_cliente, C.email, C.login, C.clave, C.status_web AS status,E.webTheme,  TA.descripcion AS panel, E.nombre AS name,Em.latitud, Em.longuitud,E.direccion, E.telefonos, E.email AS correo, E.web FROM t365_Empresas E  INNER JOIN   t365_Clientes C ON E.id_empresa = C.id_empresa LEFT OUTER JOIN t365_EquiposModelos TA ON  C.modelo = TA.id_modelo INNER JOIN t365_Empresas Em ON Em.id_empresa=E.id_empresa  WHERE     (C.login COLLATE Latin1_General_CS_AS = ?) AND (C.clave COLLATE Latin1_General_CS_AS = ?)";

				$var_auto=true;
			break;

			case "login_personal":
				$sql_statement=" SELECT o.idPersonal, o.nombre, o.id_empresa, o.correo, e.id_pais, e.nombre AS NameEmpresa, e.web, o.estatus, e.master, e.logo, e.direccion, o.imagen, e.webTheme, o.webTheme AS themePer, e.webThemeSoport,   o.WebThemeSoport AS themeSoportPer, o.id_perfil, e.correlativo_ordenes_cont, e.correlativo_ordenes_ini, e.correosHombre, e.timeNotifiHombre, e.timeHombreM, e.timeAlertPen, e.monitorea, e.longuitud,   e.latitud, e.notif_sms_servicio, e.notifi_email_servicio, e.puerto, e.ip, e.rif, e.status, e.clave, e.login, e.email, e.telefonos, ep.ipPanicPc, ep.puertoPanicPc,(SELECT count(*) FROM t365_Empresas where monitorea=o.id_empresa) as empCount FROM            t365_Personal AS o INNER JOIN t365_Empresas AS e ON o.id_empresa = e.id_empresa LEFT OUTER JOIN t365_EmpresasParametros AS ep ON e.id_empresa = ep.id_empresa WHERE        (o.usuario COLLATE Latin1_General_CS_AS = '".$args[0]."') AND (o.clave COLLATE Latin1_General_CS_AS = '".$args[1]."') AND (o.eliminado = 0)";
			break;

			case "site_sel_PermisosPersonal":
				$sql_statement=" SELECT * FROM t365_PermisosAdmin where (idUsuario='".$args."') ";
			break;

			case "site_sel_LoginAsociaos":
				$sql_statement=" SELECT asoc.id_asociado, asoc.id_empresa,  asoc.nombre, asoc.status,emp.nombre AS NameEmpresa, emp.status as statusE, emp.id_pais, emp.web, asoc.email, emp.logo,emp.direccion,emp.webTheme ,ce.* FROM  t365_asociados AS asoc INNER JOIN  t365_Empresas AS emp ON asoc.id_empresa = emp.id_empresa  INNER JOIN  t365_ConfigEmpresas ce ON asoc.id_empresa = ce.id_empresa WHERE (asoc.usuario COLLATE Latin1_General_CS_AS = '".$args[0]."')   AND (asoc.clave COLLATE Latin1_General_CS_AS = '".$args[1]."') ";
			break;

			case "site_sel_EmpresaRangoClientes":
				$sql_statement=" SELECT * FROM t365_EmpresasRangoClientes WHERE (id_empresa = '".$args."') ORDER BY idRango ";
			break;

			case "site_sel_EmpresaRangoClientesSel":
				$sql_statement=" SELECT prefijo FROM t365_EmpresasRangoClientes WHERE (id_empresa = '".$args."') group by prefijo order by prefijo desc ";
			break;

			case "site_sel_ReceptorLineas":
				$sql_statement=" SELECT * FROM t365_ConfigPort_Lineas WHERE (PortID = '".$args."') ORDER BY id_linea ";
			break;

			case "site_sel_allclientesBuscarListCount":
				$sql_statement.="SELECT id_cliente FROM t365_Clientes  where 1=1 ".$args[0];
			break;

			case "site_sel_allclientesBuscarList":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.="SELECT id_cliente,nombre_cliente,email,login,clave,status_web as st,telf_local,telf_movil,prefijo,cuenta,tipocuenta,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_Clientes  where 1=1 ".$args[0];
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_TotalesClientePanel":
				$sql_statement=" select (SELECT count(*)  FROM t365_ClienteZonas where id_cliente = '".$args[0]."') as zonas, (SELECT count(*) FROM t365_Usuarios WHERE (id_cliente = '".$args[0]."') ) as usuarios, (SELECT COUNT(*)  FROM  t365_Tramas WHERE  (cliente = '".$args[0]."') AND (DAY(fecha) = DAY(GETDATE()))  AND (MONTH(fecha) = MONTH(GETDATE())) AND (YEAR(fecha) = YEAR(GETDATE()))) as eventos, ( SELECT COUNT(*)  FROM t365_MensajesSend WHERE (id_cliente = '".$args[0]."') AND (DAY(send_date) = DAY(GETDATE())) AND (MONTH(send_date) = MONTH(GETDATE())) AND (YEAR(send_date) = YEAR(GETDATE())) ) as msjs,(SELECT count(*)  FROM t365_EmpresaEquipos where id_cliente= '".$args[0]."') as equipos";
			break;

			case "site_sel_UltimoStatusOPCL2":
				$sql_statement="SELECT  idCliente, cod_alarm AS codigo, Fecha FROM t365_StatusPanelCliente WHERE (idCliente = '".$args[0]."') ";
			break;

			case "site_sel_ClienteByCode":
				$sql_statement=" SELECT        E.nombre AS Empresa, C.id_cliente, C.latitud, C.longitud, C.rif, C.clave, C.prefijo, C.cuenta, C.fechinicio, C.id_status, TC.descrip AS TipoC, P.descrip AS Protocolo, TA.descripcion AS TipoAlarma, C.nombre_cliente,   C.ciudad, C.direccion, C.referencia, C.telf_local, C.imagen AS pic, C.telf_fax, C.telf_movil, C.email, C.web_site, C.tipocuenta, CASE WHEN CAST(C.fecha_corte AS varchar(20)) IS NULL   THEN 'Sin fecha de corte' ELSE CAST(C.fecha_corte AS varchar(20)) END AS fecha_corte,  (SELECT manual_file FROM t365_EquiposManuales AS m  WHERE        (id_tipo_manual = 1) AND (id_manual = TA.id_manual_help)) AS manu_help, C.login, C.clave AS Expr1, C.status_web, TC.img AS icon, pa.descripcion AS pais, es.descripcion AS estado,  C.clavemaster, ma.descripcion AS marca, sm.id_marca FROM t365_EquiposMarcas AS ma INNER JOIN  t365_EquiposSubTipos_Marcas AS sm ON ma.id_marca = sm.id_marca INNER JOIN  t365_EquiposModelos AS TA ON sm.id_subtipo_marca = TA.id_subtipo_marca RIGHT OUTER JOIN  t365_Paises AS pa INNER JOIN  t365_PaisEstados AS es ON pa.id_pais = es.id_pais RIGHT OUTER JOIN  t365_Clientes AS C INNER JOIN t365_Protocolos AS P ON C.id_protocolo = P.id_protocolo ON es.id_estado = C.id_estado LEFT OUTER JOIN t365_Empresas AS E ON C.id_empresa = E.id_empresa LEFT OUTER JOIN  t365_TypeCliente AS TC ON C.id_type_cliente = TC.id_type_empresa ON TA.id_modelo = C.modelo WHERE     (C.id_cliente = '".$args[0]."')";
			break;

			case "site_sel_UltimoStatusPanelByCliente":
				$sql_statement=" 	 SELECT  TOP 1 * FROM v365_UltimaSenalCliente WHERE     (cliente = '".$args[0]."') ORDER BY fecha DESC ";
			break;

			case "site_sel_AllZonasTotal":
				$sql_statement="SELECT  cast(id_zona as int) as id FROM  t365_ClienteZonas where id_cliente = '".$args[0]."'  ".$args[1]." ";
			break;

			case "site_sel_AllZonasParam":
				$sql_statement = " SELECT * FROM ( ";
				$sql_statement.="SELECT  id,id_zona, descrip as zona,ubicacion as ubi,type,ROW_NUMBER() OVER(ORDER BY id_zona ".$args[3].") AS RowID  FROM  t365_ClienteZonas where id_cliente = '".$args[0]."' ".$args[1];
				$sql_statement.= " ) as dat where ".$args[4]." ".$args[2]." ".$args[3]." ";
			break;

			case "site_sel_AllRondasTotal":
				$sql_statement="SELECT r.id_ronda, r.Nombre,r.Tolerancia, r.Hora_Inicio, r.Hora_Fin, t.Descripcion AS Tipo, rc.Descripcion AS Calendario, r.Hora_Fin_Total FROM  t365_Rondas r LEFT OUTER JOIN t365_RondasCalendario rc ON r.id_Calendario = rc.id_rondacalendario LEFT OUTER JOIN t365_RondasTipo t ON r.id_Tipo = t.id_Tipo WHERE     (r.id_Cliente = '".$args[0]."') and (r.id_padre = 0) and (r.Borrar = 0)";
			break;

			case "site_sel_AllRondasParam":
				$sql_statement = " SELECT * FROM ( ";
				$sql_statement.="SELECT r.id_ronda, r.Nombre,r.Tolerancia, r.Hora_Inicio, r.Hora_Fin, t.Descripcion AS Tipo, rc.Descripcion AS Calendario, r.Hora_Fin_Total,ROW_NUMBER() OVER( ".$args[1]." ) AS RowID FROM  t365_Rondas r LEFT OUTER JOIN t365_RondasCalendario rc ON r.id_Calendario = rc.id_rondacalendario LEFT OUTER JOIN t365_RondasTipo t ON r.id_Tipo = t.id_Tipo WHERE     (r.id_Cliente = '".$args[0]."') and (r.id_padre = 0) and (r.Borrar = 0) ";
				$sql_statement.= " ) as dat where  1=1 ".$args[2]."";
			break;

			case "site_sel_ClientesZonasImagen":
				$sql_statement=" SELECT * FROM t365_ClienteZonasImagen where id_zona='".$args[0]."' and id_cliente='".$args[1]."' ORDER BY imagen ";
			break;

			case "site_sel_ClientesZonasTecPanic":
				$sql_statement=" SELECT * FROM t365_ClienteZonasSOS where id_zona='".$args[0]."' ";
			break;

			case "site_sel_ClientesZonasGetTecPanic":
				$sql_statement=" SELECT STUFF((SELECT ',' + tecla  FROM t365_TeclasPanicPC where id in(".$args.") FOR XML PATH ('')) , 1, 1, '') as teclas ";
			break;

			case "site_sel_AllUsuariosTotal":
				$sql_statement=" SELECT * FROM   t365_Usuarios u left outer join t365_TypeUser t on u.id_type_user=t.id_type_user WHERE  (u.id_cliente = '".$args[0]."')   " .$args[1]."  ";
			break;

			case "site_sel_AllUsuariosParam":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.=" SELECT u.*,t.descrip,ROW_NUMBER() OVER(".$args[2].") AS RowID FROM   t365_Usuarios u left outer join t365_TypeUser t on u.id_type_user=t.id_type_user WHERE  (id_cliente = '".$args[0]."')   " .$args[1]."  ";
				$sql_statement.= " ) as dat  where ".$args[4]." ".$args[3]."  ";
			break;

			case "site_sel_AllClienteNumEmergenciaTotal":
				$sql_statement="select * from t365_NumEmergencia  WHERE (id_cliente = '".$args[0]. "') ".$args[1]. " ".$args[2]. " ";
			break;

			case "site_sel_AllClienteNumEmergenciaParam":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.="select *,ROW_NUMBER() OVER(".$args[2].") AS RowID  from t365_NumEmergencia  WHERE (id_cliente = '".$args[0]. "') ".$args[1]. "  ";
				$sql_statement.= " ) as dat  where ".$args[3]." ".$args[2]." ";
			break;


			case "site_sel_AllHorariosClientTotal":
				$sql_statement="SELECT * FROM   t365_HorariosOC WHERE  (id_cliente = '".$args[0]. "')   ".$args[1]. "  ";
			break;


			case "site_sel_AllHorariosClientParam":
				$sql_statement = " SELECT  * FROM ( ";
				$sql_statement.="SELECT *,ROW_NUMBER() OVER(".$args[2].") AS RowID FROM   t365_HorariosOC WHERE  (id_cliente = '".$args[0]. "')   ".$args[1]. "  ";
				$sql_statement.= " ) as dat  where ".$args[3]." ".$args[2]." ";
			break;

			case "site_sel_UltFecReportSMS":
				$sql_statement = "SELECT DISTINCT TOP 31 CAST(CAST(send_date AS varchar(11)) AS smalldatetime) AS fech  FROM    t365_MensajesSend WHERE     (id_cliente = ?) ORDER BY fech DESC" ;
				$var_auto=true;
			break;


			case "site_sel_BuscarPorSMSTotal":
				$sql_statement = " SELECT DISTINCT movil, sms FROM t365_MensajesSend  WHERE (id_cliente = '".$args[0]. "') AND (DAY(send_date) = '".$args[1]. "') AND  (MONTH(send_date) = '".$args[2]. "') AND (YEAR(send_date) = '".$args[3]. "')  ";
			break;

			case "site_sel_BuscarPorSMSParam":
				$sql_statement = " SELECT * FROM ( ";
				$sql_statement.= " SELECT DISTINCT movil, sms, send_date AS fecha,ROW_NUMBER() OVER(ORDER BY id_mensajes) AS RowID  FROM t365_MensajesSend   WHERE (id_cliente =  '".$args[0]. "') AND (DAY(send_date) =  '".$args[1]. "') AND  (MONTH(send_date) =  '".$args[2]. "') AND (YEAR(send_date) =  '".$args[3]. "') " ;
				$sql_statement.= " ) as dat  where ".$args[5]." ".$args[4]." ";

			break;

			case "site_sel_BuscarPorSMSExport":
				$sql_statement.= " SELECT DISTINCT movil, sms, send_date AS fecha FROM t365_MensajesSend   WHERE (id_cliente =  '".$args[0]. "') AND (DAY(send_date) =  '".$args[1]. "') AND  (MONTH(send_date) =  '".$args[2]. "') AND (YEAR(send_date) =  '".$args[3]. "')  ".$args[4]."  " ;
			break;

			case "site_sel_DatosCliente":
				$sql_statement = " SELECT * FROM t365_Clientes where id_cliente= ? ";
				$var_auto=true;
			break;

			case "site_sel_VerifiCliente":
				$sql_statement = " SELECT * FROM t365_Clientes where 1=1 ".$args. " ";
			break;

			case "site_sel_UltFecReport":
				$sql_statement = " SELECT DISTINCT TOP 20 CAST(LEFT(fecha, 12)  AS SMALLDATETIME) AS fechaSalida FROM t365_Tramas  WHERE     (cliente = ?) ORDER BY fechaSalida DESC ";
				$var_auto=true;
			break;

			case "site_sel_UltimaSenalesClienteTotal":
				$sql_statement = " SELECT TOP 100 * FROM v365_HistorialSenales WHERE (cliente = ?) ORDER BY fecha DESC";
				$var_auto=true;
			break;

			case "site_sel_UltimaSenalesClienteParam":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT TOP 100 *,ROW_NUMBER() OVER(ORDER BY id_trama desc) AS RowID FROM v365_HistorialSenales WHERE (cliente = ".$args[0].")";
				$sql_statement.= " ) as dat  where ".$args[1]."  order by fecha desc";

			break;

			case "site_sel_BuscarPorFechaTotal":
				$sql_statement = " SELECT * FROM v365_HistorialSenales WHERE (cliente = '".$args[0]."') AND (DAY(fecha) = '".$args[1]."') AND (MONTH(fecha) = '".$args[2]."')   AND (YEAR(fecha) = '".$args[3]."')   ";
			break;

			case "site_sel_BuscarPorFechaParam":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT *,ROW_NUMBER() OVER(ORDER BY id_trama desc) AS RowID FROM v365_HistorialSenales WHERE (cliente ='".$args[0]."') AND (DAY(fecha) ='".$args[1]."')  AND (MONTH(fecha) = '".$args[2]."') AND (YEAR(fecha) = '".$args[3]."')  ";
				$sql_statement.= " ) as dat  where ".$args[4]."  order by fecha desc";
			break;

			case "site_sel_GruposAlarmas":
				$sql_statement = " SELECT * FROM t365_GrupoCodigosAlarma ";
			break;

			case "site_sel_BuscarPorFechaRangeTotal":
				$sql_statement = " SELECT id_trama FROM v365_HistorialSenales  WHERE (cliente = '".$args[0]."' )  ".$args[1]." " ;
			break;

			case "site_sel_BuscarPorFechaRangeParam":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT *,ROW_NUMBER() OVER(ORDER BY id_trama desc) AS RowID FROM v365_HistorialSenales  WHERE (cliente = '".$args[0]."' )  ".$args[1]." " ;
				$sql_statement.= " ) as dat  where ".$args[2]."  ORDER BY fecha DESC";
			break;

			case "site_sel_NotasClientes":
				$sql_statement = " SELECT top 1 IdNota, IdCliente, NotaFija, NotaTemp, CAST(CONVERT(NVARCHAR, FechaIni, 112) AS DATETIME)  AS FechaIni, CAST(CONVERT(NVARCHAR, FechaFin, 112) AS DATETIME) AS FechaFin FROM t365_NotasClientes  WHERE (IdCliente = ?) ORDER BY IdNota DESC ";
				$var_auto=true;
			break;

			case "site_sel_ALLClienteCamTotal":
				$sql_statement = "SELECT  c.* FROM t365_ClientesCCTV  c left outer join t365_EquiposSubTipos t on c.id_tipo = t.id_subtipo left outer join t365_CCTVModoRegistro m on c.id_modo=m.id_modo WHERE (id_cliente = '".$args[0]."') ".$args[1]."";
			break;

			case "site_sel_ALLClienteCamParam":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT  c.*,t.descripcion as tipoc ,m.descripcion as modo,ROW_NUMBER() OVER(".$args[2].") AS RowID FROM t365_ClientesCCTV  c left outer join t365_EquiposSubTipos t on c.id_tipo = t.id_subtipo left outer join t365_CCTVModoRegistro m on c.id_modo=m.id_modo WHERE (c.id_cliente = '".$args[0]."') ".$args[1]."  ";
				$sql_statement.= " ) as dat where  ".$args[3]." ".$args[4]." ";

			break;

			case "site_sel_ClientesPlanes":
				$sql_statement = " SELECT p.* FROM t365_Clientes c INNER JOIN t365_Planes p ON c.id_protocolo = p.id_protocolo  WHERE     (c.id_cliente = ?) ";
				$var_auto=true;
			break;

			case "site_sel_AllUsuariosSelect":
				$sql_statement=" SELECT * FROM   t365_Usuarios WHERE  (id_cliente = '".$args[0]."') " .$args[1]." order by CASE WHEN ISNUMERIC(id_user) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(id_user) = 1 THEN CAST(id_user AS INT) ELSE 0 END ASC ";
			break;

			case "site_sel_ClientesPlanesDetalle":
				$sql_statement="SELECT EP.*, E.descript FROM t365_Clientes C INNER JOIN t365_Planes P ON C.id_protocolo = P.id_protocolo   INNER JOIN t365_EventosPlanes EP ON P.id_plan = EP.id_plan AND P.id_protocolo = EP.id_protocolo INNER JOIN t365_Eventos E ON EP.cod_evento = E.cod_event AND EP.id_protocolo = E.id_protocolo WHERE (C.id_cliente = ?) AND (P.id_plan = ?)";
				$var_auto=true;
			break;

			case "site_sel_AllUsuariosEventos":
				$sql_statement="SELECT DISTINCT  t365_Usuarios.nombre + ' ' + t365_Usuarios.apellido AS nombres,  CASE WHEN ISNUMERIC(t365_Usuarios.id_user) = 1 THEN CAST(t365_Usuarios.id_user AS INT) ELSE 0 END AS id_user, t365_Usuarios.id_cliente, t365_Usuarios.cod_user, t365_Usuarios.id_type_user, t365_Usuarios.nombre,   t365_Usuarios.apellido, t365_Usuarios.movil, t365_Usuarios.email, t365_Usuarios.FechaAniversario, t365_Usuarios.id_plan, t365_Usuarios.status, t365_Usuarios.send_mail, t365_Usuarios.frecuencia_mail, t365_Usuarios.active_email, t365_Usuarios.id_plan_email, t365_Usuarios.bbpin, t365_Usuarios.clavevoz, t365_Usuarios.imagen, t365_Usuarios.maximosms FROM    t365_Usuarios INNER JOIN t365_ClienteEventos ON t365_Usuarios.id_cliente = t365_ClienteEventos.id_cliente AND t365_Usuarios.id_user = t365_ClienteEventos.id_user WHERE        (t365_Usuarios.id_cliente = '".$args[0]."') ORDER BY  CASE WHEN ISNUMERIC(t365_Usuarios.id_user) = 1 THEN CAST(t365_Usuarios.id_user AS INT) ELSE 0 END";

				$var_auto=true;
			break;

			case "site_sel_ClientesEventosDetalle":
				$sql_statement="SELECT E.cod_event, E.descript, CE.type FROM t365_ClienteEventos CE INNER JOIN t365_Eventos E  ON CE.cod_evento = E.cod_event INNER JOIN t365_Clientes C ON CE.id_cliente = C.id_cliente AND E.id_protocolo = C.id_protocolo WHERE     (CE.id_user = ?) AND (CE.id_cliente = ?) ORDER BY CE.type DESC ";
				$var_auto=true;
			break;

			case "site_sel_ClientesEventosTextPlan":
				$sql_statement=" SELECT p.descrip FROM t365_Usuarios AS u INNER JOIN t365_Clientes AS c ON u.id_cliente = c.id_cliente INNER JOIN t365_Planes AS p ON c.id_protocolo = p.id_protocolo AND ".$args[0]." = p.id_plan where c.id_cliente='".$args[1]."' and u.id_user='".$args[2]."' ";
			break;

			case "site_sel_TypeUser":
				$sql_statement=" SELECT id_type_user, descrip FROM t365_TypeUser t order by id_type_user asc";
			break;

			case "site_sel_DatosUser":
				$sql_statement="SELECT u.* FROM t365_Usuarios u WHERE (id_user = ?) AND (id_cliente = ?)";
				$var_auto=true;
			break;

			case "site_sel_ClientesEventosDetalle":
				$sql_statement="SELECT E.cod_event, E.descript, CE.type FROM t365_ClienteEventos CE INNER JOIN t365_Eventos E  ON CE.cod_evento = E.cod_event INNER JOIN t365_Clientes C ON CE.id_cliente = C.id_cliente AND E.id_protocolo = C.id_protocolo WHERE     (CE.id_user = ?) AND (CE.id_cliente = ?) ORDER BY CE.type DESC ";
				$var_auto=true;
			break;

			case "site_sel_AllEquiposCliente":

				$sql_statement="SELECT eq.id_equipo, eq.tipo_entrega, eq.id_empresa, eq.id_cliente, eq.id_tipo_equipo, eq.id_modelo, eq.id_frecuencia_pago, eq.serial, eq.fecha_entrega, eq.fecha_devuelto, eq.estatus, eq.extra1, eq.extra2, mo.descripcion AS modelo, ma.descripcion AS marca, ti.descripcion AS tipoE, fr.descripcion AS frecuencia, mo.id_subtipo_marca FROM t365_FrecuenciaPagos AS fr RIGHT OUTER JOIN  t365_EmpresaEquipos AS eq INNER JOIN  t365_EquiposModelos AS mo ON eq.id_modelo = mo.id_modelo INNER JOIN  t365_EquiposTipos AS ti ON eq.id_tipo_equipo = ti.id_tipo_equipo INNER JOIN t365_EquiposMarcas AS ma INNER JOIN  t365_EquiposSubTipos_Marcas AS sm ON ma.id_marca = sm.id_marca ON mo.id_subtipo_marca = sm.id_subtipo_marca ON fr.id_frecuencia = eq.id_frecuencia_pago WHERE (eq.id_cliente = '".$args[0]."')";
			break;

			case "site_sel_ValidLogin":
				$sql_statement="SELECT 1 FROM v365_ValidarLogins WHERE  (login = ?) and (id <> ?)";
				$var_auto=true;
			break;

			case "site_sel_ValidLoginOnly":
				$sql_statement="SELECT 1 FROM v365_ValidarLogins WHERE  (login = ?) ";
				$var_auto=true;
			break;

			case "site_sel_MonitoreoMaps":
				$sql_statement=" SELECT * FROM v365_MapaUbicacionClientes  WHERE 1=1 ".$args[0]." ";
			break;

			case "site_sel_UltimoStatusOPCL2":
				$sql_statement="SELECT  idCliente, cod_alarm AS codigo, Fecha FROM t365_StatusPanelCliente WHERE (idCliente = ?) ";
				$var_auto=true;
			break;

			case "site_sel_ReportActivacionesTotal":
				$sql_statement=" SELECT t.cliente, COUNT(t.id_trama) AS cont, c.nombre_cliente  FROM t365_Clientes c INNER JOIN t365_Tramas t ON c.id_cliente = t.cliente INNER JOIN   t365_Eventos e ON t.evento = e.cod_event  AND t.protocolo = e.id_protocolo  INNER JOIN t365_CodigosAlarma alarm ON   e.cod_alarm = alarm.codigo INNER JOIN t365_GrupoCodigosAlarma g ON alarm.idGrupo = g.idGrupo  WHERE (g.idGrupo = 1)  ".$args[0]."  GROUP BY t.cliente, c.nombre_cliente ";
			break;

			case "site_sel_ReportActivacionesList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.=" SELECT t.cliente, COUNT(t.id_trama) AS cont, c.nombre_cliente,c.prefijo,c.cuenta,ROW_NUMBER() OVER(ORDER BY COUNT(t.id_trama) DESC) AS RowID  FROM t365_Clientes c INNER JOIN t365_Tramas t ON c.id_cliente = t.cliente INNER JOIN  t365_Eventos e ON t.evento = e.cod_event  AND t.protocolo = e.id_protocolo  INNER JOIN t365_CodigosAlarma alarm ON   e.cod_alarm = alarm.codigo INNER JOIN t365_GrupoCodigosAlarma g ON alarm.idGrupo = g.idGrupo WHERE (g.idGrupo = 1)  ".$args[0]." ";
				$sql_statement.= " GROUP BY cliente, nombre_cliente,c.prefijo,c.cuenta ) as dat where  ".$args[1]."  ORDER BY cont DESC ";
			break;

			case "site_sel_ReportActivacionesDetailClient":
				$sql_statement= " SELECT descript, UserZona, fecha,cod_alarm,evento  FROM v365_HistorialSenales  WHERE (idGrupo = 1) ".$args[0]."  ORDER BY fecha";
			break;

			case "site_sel_ReportActivacionesExport":
				$sql_statement=" SELECT t.cliente, COUNT(t.id_trama) AS cont, c.nombre_cliente,c.prefijo,c.cuenta  FROM t365_Clientes c INNER JOIN t365_Tramas t ON c.id_cliente = t.cliente INNER JOIN   t365_Eventos e ON t.evento = e.cod_event  AND t.protocolo = e.id_protocolo  INNER JOIN t365_CodigosAlarma alarm ON   e.cod_alarm = alarm.codigo INNER JOIN t365_GrupoCodigosAlarma g ON alarm.idGrupo = g.idGrupo  WHERE (g.idGrupo = 1)  ".$args[0]."  GROUP BY t.cliente, c.nombre_cliente,c.prefijo,c.cuenta ORDER BY cont DESC";
			break;

			case "site_sel_ReportGrupos":
				$sql_statement=" SELECT g.idGrupo, g.Descript, COUNT(t.id_trama) AS cont FROM t365_Tramas t INNER JOIN   t365_Clientes c ON t.cliente = c.id_cliente INNER JOIN t365_Eventos e ON t.evento = e.cod_event AND t.protocolo = e.id_protocolo  INNER JOIN t365_CodigosAlarma a ON e.cod_alarm = a.codigo INNER JOIN  t365_GrupoCodigosAlarma g ON a.idGrupo = g.idGrupo   WHERE (1 = 1) ".$args[0]." GROUP BY g.idGrupo, g.Descript ORDER BY cont DESC ";

			break;

			case "site_sel_CodigosAlarma":
				$sql_statement=" SELECT codigo, descript, idGrupo,web_color,web_colorBg FROM t365_CodigosAlarma ORDER BY idGrupo ";
			break;

			case "site_sel_ReportCodigoAlarmaTotal":
				$sql_statement=" SELECT t.cliente, c.nombre_cliente AS nombreCliente, COUNT(*) AS cont FROM v365_HistorialSenales t INNER JOIN t365_Clientes c ON t.cliente = c.id_cliente WHERE (c.id_cliente > 0) ".$args[0]." GROUP BY t.cliente, c.nombre_cliente ";
			break;

			case "site_sel_ReportCodigoAlarmaList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.=" SELECT t.cliente, c.nombre_cliente AS nombreCliente,c.prefijo,c.cuenta, COUNT(*) AS cont,ROW_NUMBER() OVER(ORDER BY COUNT(*) DESC) AS RowID FROM v365_HistorialSenales t INNER JOIN t365_Clientes c ON t.cliente = c.id_cliente  WHERE (c.id_cliente > 0) ".$args[0]."";
				$sql_statement.= " GROUP BY t.cliente, c.nombre_cliente,c.prefijo,c.cuenta ) as dat where  ".$args[1]."  ORDER BY cont DESC ";
			break;

			case "site_sel_ReportCodigoAlarmaExport":
				$sql_statement= "SELECT t.cliente, c.nombre_cliente AS nombreCliente,c.prefijo,c.cuenta, COUNT(*) AS cont FROM v365_HistorialSenales t INNER JOIN t365_Clientes c ON t.cliente = c.id_cliente  WHERE (c.id_cliente > 0) ".$args[0]." GROUP BY t.cliente, c.nombre_cliente,c.prefijo,c.cuenta ORDER BY cont DESC ";
			break;

			case "site_sel_ReportCodigoAlarmaCLiente":
				$sql_statement= " SELECT t.cliente, t.descript, t.evento,   t.fecha,t.cod_alarm,t.UserZona  FROM v365_HistorialSenales t INNER JOIN t365_Clientes c ON t.cliente = c.id_cliente   WHERE 1=1 ".$args[0]." ORDER BY t.fecha DESC";
			break;


			case "site_sel_EventosIndefinidosTotal":
				$sql_statement="SELECT DISTINCT evento FROM t365_Tramas p WHERE (evento NOT IN (SELECT cod_event FROM          t365_Eventos)) ORDER BY evento";
			break;

			case "site_sel_EventosIndefinidosList":
				$sql_statement= " SELECT distinct dat.evento , dat.RowID FROM ( SELECT TOP 100 PERCENT evento,ROW_NUMBER() OVER(ORDER BY evento) AS RowID  FROM t365_Tramas p WHERE (evento NOT IN (SELECT cod_event FROM t365_Eventos)) group by evento ORDER BY evento )  as dat where  ".$args[0]." ";
			break;

			case "site_sel_EventosIndefinidosDetalle":
				$sql_statement= " SELECT TOP 1 t.descrip, c.id_cliente,c.nombre_cliente, t.fecha FROM t365_Tramas t  LEFT OUTER JOIN t365_Clientes c ON t.cliente = c.id_cliente WHERE (t.evento = '".$args[0]."') ORDER BY t.fecha DESC ";
			break;

			case "site_sel_EventosIndefinidosExport":
				$sql_statement="SELECT DISTINCT evento FROM t365_Tramas p WHERE (evento NOT IN (SELECT cod_event FROM          t365_Eventos)) ORDER BY evento";
			break;

			case "site_sel_MonitoreoSenalesXProcesar":
				$sql_statement = "SELECT * FROM v365_TramasPorProcesar where 1=1 ".$args[0]." ORDER BY id_trama DESC";
			break;

			case "site_sel_MonitoreoSenalesPendientes":
				$sql_statement = "SELECT * FROM v365_TramasPendientes where 1=1 ".$args[0]." ORDER BY  id_trama DESC";
			break;

			case "site_sel_MonitoreoEstatico":
				$sql_statement = "SELECT top (100)  * FROM v365_MonitoreoEstatico  WHERE  1=1 ".$args[0]." ORDER BY  id_trama DESC";
			break;

			case "site_sel_MonitoreoSenalesProcesadas":
				$sql_statement = "SELECT top (100) * FROM v365_TramasProcesadas where 1=1 ".$args[0]." ORDER BY  Fecha_proc DESC , id_trama DESC";
 			break;

			case "site_sel_GetTypeUserListCount":
				$sql_statement= " SELECT id_type_user , descrip  FROM t365_TypeUser where 1=1  ".$args[0]." ";
			break;

			case "site_sel_GetTypeUser":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT id_type_user , descrip,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_TypeUser where 1=1  ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetTypeUserVerifi":
				$sql_statement= " SELECT id_type_user , descrip  FROM t365_TypeUser where 1=1  ".$args." ";
			break;

			case "site_sel_GetTypeClienteListCount":
				$sql_statement= "SELECT id_type_empresa  FROM  t365_TypeCliente where 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_GetTypeCliente":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM  t365_TypeCliente where 1=1 ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetIconsTipos":
				$sql_statement = " SELECT idIcon, descripcion, icono FROM t365_IconMapas  ";
			break;

			case "site_sel_GetMenPredefinidosListCount":
				$sql_statement = "  SELECT id FROM t365_MensajesCierre  where 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_GetMenPredefinidos":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM  t365_MensajesCierre where 1=1 ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetMenPredefinidosVerifi":
				$sql_statement = " SELECT * FROM t365_MensajesCierre  where 1=1 ".$args." ";
			break;


			case "site_sel_GetEventosListCount":
				$sql_statement = " SELECT e.*, p.descrip AS protocolo FROM  t365_Eventos e INNER JOIN t365_Protocolos p ON e.id_protocolo = p.id_protocolo where 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_GetEventos":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT e.*, p.descrip,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM  t365_Eventos e INNER JOIN t365_Protocolos p ON e.id_protocolo = p.id_protocolo where 1=1 ".$args[0]."   ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_ProtocolosALl":
				$sql_statement = " SELECT descrip, id_protocolo FROM t365_Protocolos";
			break;

			case "site_sel_GetEventosOne":
				$sql_statement = " SELECT cod_event FROM t365_Eventos WHERE (cod_event = '".$args[0]."') AND (id_protocolo = '".$args[1]."')";
			break;

			case "site_sel_GetDepartamentosEmpresaListCount":
				$sql_statement = " SELECT t.idDepartamento from t365_DepartamentosEmpresa t inner join t365_Empresas e on t.idEmpresa=e.id_empresa where 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_GetDepartamentosEmpresa":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT t.*,e.nombre as empresa,e.id_empresa,ROW_NUMBER() OVER(".$args[1].") AS RowID from t365_DepartamentosEmpresa t inner join t365_Empresas e on t.idEmpresa=e.id_empresa where 1=1 ".$args[0]."  ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[3]." ";
			break;

			case "site_sel_GetMotivosSoporteListCount":
				$sql_statement = " SELECT id_motivo FROM t365_SoporteMotivos s INNER JOIN t365_DepartamentosEmpresa d ON s.idDepartCorreo = d.idDepartamento WHERE 1=1 ".$args[0]." ".$args[1]."  ";
			break;

			case "site_sel_GetMotivosSoporte":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT s.*, d.nombre,e.nombre as empresa ,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_SoporteMotivos s INNER JOIN t365_DepartamentosEmpresa d ON s.idDepartCorreo = d.idDepartamento INNER JOIN t365_Empresas e ON d.idEmpresa = e.id_empresa WHERE 1=1 ".$args[0]."  ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetDepartamentosEmpresaOne":
				$sql_statement = "SELECT * FROM   t365_DepartamentosEmpresa where idEmpresa='".$args[0]."'   ";
			break;

			case "site_sel_GetCodigosAlarmaListCount":
				$sql_statement = "SELECT * FROM  t365_CodigosAlarma where 1=1 ".$args[0]." ".$args[1]." ";
			break;


			case "site_sel_GetCodigosAlarma":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM  t365_CodigosAlarma where 1=1 ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GruposAlarmas":
				$sql_statement = " SELECT * FROM t365_GrupoCodigosAlarma ";
			break;

			case "site_sel_GetCodigosAlarmaVerifi":
				$sql_statement = " SELECT * FROM  t365_CodigosAlarma where 1=1 ".$args[0]."  ";
			break;

			case "site_sel_GetGrupoCodigosAlarmaListCount":
				$sql_statement = " SELECT idGrupo FROM    t365_GrupoCodigosAlarma where 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_GetGrupoCodigosAlarma":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM    t365_GrupoCodigosAlarma where 1=1 ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_TiposVehiculosListCount":
				$sql_statement = " SELECT t.*, i.iconMap, i.descripcion AS descrip FROM t365_TiposVehiculos t LEFT OUTER JOIN   t365_IconMapVehiculos i ON t.id_icon = i.id_icon WHERE 1=1 ".$args[0]." ".$args[1]."";
			break;

			case "site_sel_TiposVehiculos":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT t.*, i.iconMap,ROW_NUMBER() OVER(".$args[3].") AS RowID FROM t365_TiposVehiculos t LEFT OUTER JOIN   t365_IconMapVehiculos i ON t.id_icon = i.id_icon WHERE 1=1 ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_IconMapVehiculos":
				$sql_statement= "SELECT * FROM t365_IconMapVehiculos order by descripcion asc";
			break;

			case "site_sel_MarcasVehiculosListCount":
				$sql_statement= "SELECT id_marca,descripcion FROM t365_MarcasVehiculos WHERE 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_MarcasVehiculos":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_MarcasVehiculos WHERE 1=1 ".$args[0]."  ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_TiposGPSListCount":
				$sql_statement= " SELECT * FROM t365_TiposGPS WHERE 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_TiposGPS":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_TiposGPS WHERE 1=1 ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_TiposGPSSelect":
				$sql_statement= " SELECT * FROM t365_TiposGPS WHERE 1=1 ".$args[0]." ";
			break;

			case "site_sel_ModelosVehiculosListCount":
				$sql_statement= "SELECT m.*, ma.descripcion AS marca FROM t365_ModelosVehiculos m INNER JOIN   t365_MarcasVehiculos ma ON m.id_marca = ma.id_marca WHERE 1=1 ".$args[0]." ".$args[1]."  ";
			break;

			case "site_sel_ModelosVehiculos":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT m.*,ROW_NUMBER() OVER(".$args[3].") AS RowID , ma.descripcion AS marca FROM t365_ModelosVehiculos m INNER JOIN   t365_MarcasVehiculos ma ON m.id_marca = ma.id_marca WHERE 1=1 ".$args[0]."   ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetConfigPortListCount":
				$sql_statement= " SELECT * FROM t365_ConfigPortII where 1=1 ".$args[0]." ".$args[1]."  ";
			break;

			case "site_sel_GetConfigPort":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_ConfigPortII where 1=1 ".$args[0]."  ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetReceivers":
				$sql_statement= "SELECT * FROM t365_Receptores where 1=1 ".$args." ";
			break;

			case "site_sel_GetConfigPortVerifi":
				$sql_statement= " SELECT * FROM t365_ConfigPortII where 1=1 ".$args."   ";
			break;

			case "site_sel_DiasFeriados":
				$sql_statement= "SELECT * FROM t365_DiasFeriados where 1=1 ".$args." order by fecha asc ";
			break;

			case "site_sel_AllPaisesList":
				$sql_statement= " SELECT * FROM t365_Paises where 1=1 order by descripcion asc";
			break;

			case "site_sel_AllPaisesEstatosListParam":
				$sql_statement= " SELECT * FROM t365_PaisEstados where (id_pais='".$args[0]."') order by descripcion ASC ";
			break;

			case "site_sel_GetEmpresasListCount":
				$sql_statement= " SELECT * FROM t365_Empresas e  where e.id_empresa>0 ".$args[0]." ";
			break;

			case "site_sel_GetEmpresas":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT  e.id_empresa, e.id_pais, e.nombre, e.logo, e.direccion, e.telefonos, e.email, e.web, e.login, e.clave, e.status, e.rif, e.ip, e.puerto, e.master, e.webTheme, e.webThemeSoport, e.latitud, e.longuitud, e.timeAlertPen, e.timeHombreM, e.timeNotifiHombre, e.correosHombre, e.correlativo_ordenes_ini, e.correlativo_ordenes_cont,e.monitorea,e.notif_sms_servicio,e.notifi_email_servicio ,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_Empresas e  where e.id_empresa>0 ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_EmpresasVerifi":
				$sql_statement= " SELECT * FROM  t365_Empresas where id_empresa>0  ".$args." ";
			break;

			case "site_sel_VerifiUsuarios":
				$sql_statement= " SELECT * FROM v365_ValidarLogins where login='".$args."'";
			break;

			case "site_sel_AsociadosListCount":
				$sql_statement= " SELECT COUNT(AA.id_asociado) AS Total, A.id_asociado,  A.id_empresa, A.nombre, A.direccion, A.telef_contacto, A.email,  A.usuario, A.clave, A.status FROM t365_asociados A LEFT OUTER JOIN  t365_asociados_abonados AA ON A.id_asociado = AA.id_asociado AND A.id_empresa = AA.id_empresa WHERE  (1=1  ".$args[0]." )  GROUP BY A.id_asociado, A.id_empresa, A.nombre, A.direccion, A.telef_contacto, A.email, A.usuario, A.clave, A.status  ";
			break;

			case "site_sel_Asociados":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT COUNT(AA.id_asociado) AS Total, A.id_asociado,  A.id_empresa, A.nombre, A.direccion, A.telef_contacto, A.email,  A.usuario, A.clave, A.status,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_asociados A LEFT OUTER JOIN  t365_asociados_abonados AA ON A.id_asociado = AA.id_asociado AND A.id_empresa = AA.id_empresa WHERE  (1=1  ".$args[0]." )  GROUP BY A.id_asociado, A.id_empresa, A.nombre, A.direccion, A.telef_contacto, A.email, A.usuario, A.clave, A.status  ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_VerifiAsociados":
				$sql_statement= "SELECT * FROM t365_asociados WHERE (usuario = '".$args."' ) ";
			break;

			case "site_sel_AsociadosAbonados":
				$sql_statement= "SELECT  t365_Clientes.id_cliente, t365_Clientes.nombre_cliente  as nombre,t365_Clientes.prefijo,t365_Clientes.cuenta FROM  t365_asociados_abonados ASO INNER JOIN  t365_Clientes ON ASO.id_cliente = t365_Clientes.id_cliente  WHERE  (ASO.id_asociado = '".$args[0]."') AND (ASO.id_empresa = '".$args[1]."')";
			break;

			case "site_listAbonadosNoAsociadosListCount":
				$sql_statement= "SELECT  id_cliente, nombre_cliente AS nombre,prefijo,cuenta FROM   t365_Clientes   WHERE (id_cliente NOT IN (SELECT  id_cliente FROM  t365_asociados_abonados   WHERE (id_asociado =  ".$args[0]."))) ".$args[1]." order by id_cliente  ";
			break;

			case "site_listAbonadosNoAsociados":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT  id_cliente, nombre_cliente,prefijo,cuenta ,ROW_NUMBER() OVER(order by id_cliente asc) AS RowID FROM   t365_Clientes   WHERE (id_cliente NOT IN (SELECT  id_cliente FROM  t365_asociados_abonados   WHERE (id_asociado =  ".$args[0]."))) ".$args[1]."   ";
				$sql_statement.= " ) as dat  where 1=1 ".$args[2]." order by id_cliente asc ";
			break;

			case "site_sel_TiposUsuariosListCount":
				$sql_statement= " SELECT t.*, e.nombre AS empresa FROM t365_TiposUsuarios t INNER JOIN t365_Empresas e ON t.idEmpresa = e.id_empresa  WHERE (t.eliminado = 0)  ".$args[0]." ";
			break;

			case "site_sel_TiposUsuarios":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT t.*, e.nombre ,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_TiposUsuarios t INNER JOIN t365_Empresas e ON t.idEmpresa = e.id_empresa  WHERE (t.eliminado = 0)  ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]."  ".$args[1]."";
			break;

			case "site_sel_EmpresaAll":
				$sql_statement= "  SELECT id_empresa, nombre FROM t365_Empresas e WHERE (id_empresa <> 0) ".$args[0]." ";
			break;

			case "site_sel_UserPerfil":
				$sql_statement= " SELECT * FROM t365_UsuariosPerfil where   (id_perfil NOT IN (3, 5)) ".$args[0]." ";
			break;

			case "site_sel_PermisosTipoUser":
				 $sql_statement= " SELECT * FROM t365_PermisosTipoUsuario where (idTipoUsuario='".$args."') ";
			break;

			case "site_sel_PaginasAdmin":
				$sql_statement= " SELECT p.*, a.idAccion, a.descripcion FROM t365_PaginasAdmin p INNER   JOIN t365_PaginasAcciones a ON p.idPagina = a.idPagina where 1=1 ".$args[0]." ORDER BY p.orden,p.idPagina,a.orden, p.nombre ";
			break;

			case "site_sel_PersonalEmpresasListCount":
				$sql_statement= "SELECT p.idPersonal  FROM t365_TiposUsuarios t INNER JOIN  t365_Personal p ON t.idtipoUsuario = p.idTipoUsuario INNER JOIN t365_Empresas e ON p.id_empresa = e.id_empresa WHERE (p.eliminado = 0) ".$args[0]." ";
			break;

			case "site_sel_PersonalEmpresas":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT p.idPersonal, e.nombre as Empresa, t.color, t.descripcion, p.id_empresa, p.idTipoUsuario,p.imagen,p.id_perfil,p.cedula, p.nombre , p.telefono, p.correo, p.Dirreccion, p.Telf_Habitacion, p.usuario, p.clave,p.estatus,p.notifi_serv_tec,p.notif_sms_servicio,p.notifi_email_servicio,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_TiposUsuarios t INNER JOIN  t365_Personal p ON t.idtipoUsuario = p.idTipoUsuario INNER JOIN  t365_Empresas e ON p.id_empresa = e.id_empresa WHERE (p.eliminado = 0) ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]."  ".$args[1]."";
			break;

			case "site_sel_PermisosPersonal":
				$sql_statement= " SELECT * FROM t365_PermisosAdmin where (idUsuario='".$args[0]."') ";
			break;

			case "site_sel_TiposUsuarioEmpresaOne":
				$sql_statement= "SELECT idtipoUsuario , descripcion ,id_perfilUsuario FROM t365_TiposUsuarios where (idEmpresa = '".$args[0]."') and (eliminado=0) ";
			break;

			case "site_sel_DatosMapsCliente":
				$sql_statement= "SELECT     c.nombre_cliente, c.latitud AS latclie, c.longitud AS logclie, em.latitud AS latemp , em.longuitud AS logemp, c.direccion AS dirclie, c.referencia AS refclie, e.nombre AS nomemp, c.id_type_cliente, t.img AS icon FROM t365_Clientes c INNER JOIN  t365_Empresas em ON c.id_empresa = em.id_empresa INNER JOIN  t365_Empresas e ON em.id_empresa = e.id_empresa INNER JOIN t365_TypeCliente t ON c.id_type_cliente = t.id_type_empresa WHERE (c.id_cliente = ?)";
				$var_auto=true;
			break;

			case "site_sel_ALLClienteCam":
				$sql_statement= "SELECT     c.* FROM t365_ClientesCamaras c WHERE (id_cliente = ".$args.")";
			break;

			case "site_sel_NotasClientes":
				$sql_statement = "SELECT top 1 IdNota, IdCliente, NotaFija, NotaTemp, CAST(CONVERT(NVARCHAR, FechaIni, 112) AS DATETIME) AS FechaIni, CAST(CONVERT(NVARCHAR, FechaFin, 112) AS DATETIME) AS FechaFin FROM t365_NotasClientes WHERE (IdCliente = ?) ORDER BY IdNota DESC";
				$var_auto=true;
			break;

			case "site_sel_MonitoreoMenPredefinidos":
				$sql_statement = " SELECT c.* FROM t365_MensajesCierre c ";
			break;

			case "site_sel_DataCierreCliente":
				$sql_statement = " SELECT     direccion,referencia, telf_local, telf_movil FROM t365_Clientes WHERE (id_cliente = ?) ";
				$var_auto=true;
			break;

			case "site_sel_MonitoreoAsistencia":
				$sql_statement = " SELECT id_cliente, numero, descript,observacion FROM t365_NumEmergencia WHERE (id_cliente = '".$args[0]."') ";
			break;

			case "site_sel_MonitoreoUsuario":
				$sql_statement = " SELECT t365_Usuarios.id_cliente AS id_cliente, t365_Usuarios.movil AS numero, t365_Usuarios.nombre + ' ' + t365_Usuarios.apellido + ' (' + t365_TypeUser.descrip + ')' AS descript, t365_Usuarios.clavevoz,t365_Usuarios.id_user FROM t365_Usuarios INNER JOIN t365_TypeUser ON t365_Usuarios.id_type_user = t365_TypeUser.id_type_user WHERE (t365_Usuarios.id_cliente = '".$args[0]."')  order by t365_Usuarios.id_user asc";
			break;

			case "site_sel_Ultimas10Signales":
				$sql_statement = " SELECT TOP 10 * FROM v365_HistorialSenales WHERE (cliente = ?) ORDER BY fecha DESC ";
				$var_auto=true;
			break;

			case "site_sel_SearchCliente":
				$sql_statement = " SELECT  id_cliente,id_empresa,nombre_cliente,prefijo,cuenta  FROM  t365_Clientes   where 1=1 ".$args[0]." order by CASE WHEN ISNUMERIC(cuenta) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(cuenta) = 1 THEN CAST(cuenta AS INT) ELSE 0 END asc";
			break;

			case "site_sel_getVehiculosMaps":
				$sql_statement = "SELECT v.id_vehiculo, v.id_empresa, v.codigo_gps, v.alias, v.placa, v.sim, u.IdEventoGPS, u.FechaGPS, u.Lat, u.Logi, u.Velocidad, v.id_tipo_vehiculos, DATEDIFF([minute], u.FechaGPS, GETDATE()) AS [minute], i.iconMap, (SELECT descripcion FROM t365_MarcasVehiculos WHERE (id_marca = v.id_marca)) AS marca, (SELECT descripcion FROM  t365_ModelosVehiculos WHERE (id_modelo = v.id_modelo)) AS modelo, (SELECT TOP 1 imagen FROM t365_VehiculosImagen   WHERE (codgps = v.codigo_gps) ORDER BY id) AS imagen, v.anio, v.color FROM t365_IconMapVehiculos i INNER JOIN   t365_TiposVehiculos  tv ON i.id_icon = tv.id_icon RIGHT OUTER JOIN t365_GPSUltimaUbic u INNER JOIN t365_Vehiculos v ON u.CodVehiculo = v.codigo_gps ON tv.id_tipo_vehiculo = v.id_tipo_vehiculos WHERE (v.status = 1) AND (v.monitoreo = 1)";
			break;

			case "site_sel_ClienteTipos":
				$sql_statement = " SELECT id_type_empresa, descrip FROM t365_TypeCliente ";
			break;

			case "site_sel_AlarmasTipos":
				$sql_statement = " SELECT mo.id_modelo,mo.descripcion FROM t365_EquiposModelos mo inner join t365_EquiposMarcas ma on mo.id_marca=ma.id_marca where  mo.eliminado=0 and ma.eliminado=0 and ma.id_tipo_equipo=".$args[0]." order by mo.descripcion  ";
			break;

			case "site_sel_DatosClientePais":
				$sql_statement = " SELECT c.*, e.id_pais,  sm.id_marca,(SELECT p.id_punto FROM t365_ClientesPuntos p where p.id_cliente=c.id_cliente) as tag FROM t365_EquiposSubTipos_Marcas AS sm  LEFT OUTER JOIN  t365_EquiposModelos AS m ON sm.id_subtipo_marca = m.id_subtipo_marca RIGHT OUTER JOIN t365_Clientes AS c LEFT OUTER JOIN t365_PaisEstados AS e ON c.id_estado = e.id_estado ON m.id_modelo = c.modelo where (c.id_cliente='".$args[0]."') ";
			break;

			case "site_sel_VerifiZona":
				$sql_statement = " SELECT  cast(id_zona as int) as id_zona, descrip as str_zona FROM  t365_ClienteZonas where (id_zona='".$args['zona']."') AND (id_cliente = ".$args['client'].") ".$args['aux']." ";
			break;

			case "site_sel_AllEquiposComodatoListCount":
				$sql_statement = " SELECT eq.* FROM t365_EmpresaEquipos AS eq INNER JOIN t365_EquiposModelos AS em ON eq.id_modelo = em.id_modelo INNER JOIN t365_FrecuenciaPagos AS f ON eq.id_frecuencia_pago = f.id_frecuencia INNER JOIN  t365_EquiposTipos AS t ON eq.id_tipo_equipo = t.id_tipo_equipo INNER JOIN t365_Clientes AS c ON eq.id_cliente = c.id_cliente INNER JOIN  t365_EquiposSubTipos_Marcas AS sm ON em.id_subtipo_marca = sm.id_subtipo_marca INNER JOIN  t365_EquiposMarcas AS ema ON sm.id_marca = ema.id_marca WHERE (eq.tipo_entrega = 1) ".$args[0]." ".$args[1]."  ";
			break;

			case "site_sel_AllEquiposComodato":

				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT em.descripcion AS modelo, ema.descripcion AS marca, ema.id_marca, f.descripcion AS frecuencia, c.nombre_cliente, eq.id_equipo, eq.tipo_entrega, eq.id_empresa, eq.id_cliente, eq.id_tipo_equipo, eq.id_modelo, eq.id_frecuencia_pago, eq.serial, eq.fecha_entrega, eq.fecha_devuelto, eq.estatus, eq.extra1, eq.extra2, c.prefijo, c.cuenta,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_EmpresaEquipos AS eq INNER JOIN t365_EquiposModelos AS em ON eq.id_modelo = em.id_modelo INNER JOIN t365_FrecuenciaPagos AS f ON eq.id_frecuencia_pago = f.id_frecuencia INNER JOIN t365_EquiposTipos AS t ON eq.id_tipo_equipo = t.id_tipo_equipo INNER JOIN  t365_Clientes AS c ON eq.id_cliente = c.id_cliente INNER JOIN   t365_EquiposSubTipos_Marcas AS sm ON em.id_subtipo_marca = sm.id_subtipo_marca INNER JOIN t365_EquiposMarcas AS ema ON sm.id_marca = ema.id_marca WHERE (eq.tipo_entrega = 1) ".$args[0]."  ";
				$sql_statement.= " ) as dat  where ".$args[2]."  ";
			break;

			case "site_sel_AllTiposEquipo":
				$sql_statement= " SELECT * FROM t365_EquiposTipos where (eliminado=0) ".$args[0]."";
			break;

			case "site_sel_AllModeloEquipo":
				$sql_statement= "  SELECT * FROM t365_EquiposModelos where (id_marca='".$args[0]."')  AND (eliminado=0) ";
			break;

			case "site_sel_AllFrecuenciaPagos":
				$sql_statement= " SELECT * FROM t365_FrecuenciaPagos where (id_empresa='".$args[0]."')  ";
			break;

			case "site_sel_searchClientes":
				$sql_statement= "SELECT id_cliente, nombre_cliente,prefijo,cuenta FROM t365_Clientes c WHERE  1=1 ".$args[0]." order by CASE WHEN ISNUMERIC(cuenta) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(cuenta) = 1 THEN CAST(cuenta AS INT) ELSE 0 END asc ";
			break;

			case "site_sel_AllEquiposPrestamoListCount":
				$sql_statement= "  SELECT eq.* FROM t365_EmpresaEquipos AS eq INNER JOIN  t365_EquiposModelos AS em ON eq.id_modelo = em.id_modelo INNER JOIN t365_EquiposTipos AS t ON eq.id_tipo_equipo = t.id_tipo_equipo INNER JOIN  t365_Clientes AS c ON eq.id_cliente = c.id_cliente INNER JOIN  t365_EquiposSubTipos_Marcas AS sm ON em.id_subtipo_marca = sm.id_subtipo_marca INNER JOIN  t365_EquiposMarcas AS ema ON sm.id_marca = ema.id_marca WHERE (eq.tipo_entrega = 2) ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_AllEquiposPrestamo":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "  SELECT        em.descripcion AS modelo, ema.descripcion AS marca, c.nombre_cliente, c.prefijo, c.cuenta, eq.id_equipo, eq.tipo_entrega, eq.id_empresa, eq.id_cliente, eq.id_tipo_equipo, eq.id_modelo,  eq.id_frecuencia_pago, eq.serial, eq.fecha_entrega, eq.fecha_devuelto, eq.estatus, DATEDIFF(day, eq.fecha_devuelto, GETDATE()) AS diff, eq.extra1, eq.extra2, sm.id_marca ,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM            t365_EmpresaEquipos AS eq INNER JOIN t365_EquiposModelos AS em ON eq.id_modelo = em.id_modelo INNER JOIN t365_EquiposTipos AS t ON eq.id_tipo_equipo = t.id_tipo_equipo INNER JOIN  t365_Clientes AS c ON eq.id_cliente = c.id_cliente INNER JOIN   t365_EquiposSubTipos_Marcas AS sm ON em.id_subtipo_marca = sm.id_subtipo_marca INNER JOIN  t365_EquiposMarcas AS ema ON sm.id_marca = ema.id_marca WHERE (eq.tipo_entrega = 2) ".$args[0]."  ";
				$sql_statement.= " ) as dat  where ".$args[2]."  ";
			break;


			case "site_sel_ReportSMSTotal":
				$sql_statement=" SELECT  m.id_cliente, COUNT(m.id_mensajes) AS cont, c.nombre_cliente FROM t365_MensajesSend m INNER JOIN t365_Clientes c ON m.id_cliente = c.id_cliente WHERE 1=1 ".$args[0]." GROUP BY m.id_cliente, c.nombre_cliente ORDER BY cont DESC";
			break;

			case "site_sel_ReportSMS":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.=" SELECT  m.id_cliente, COUNT(m.id_mensajes) AS cont, c.nombre_cliente,c.prefijo,c.cuenta,ROW_NUMBER() OVER(ORDER BY COUNT(m.id_mensajes) DESC) AS RowID  FROM t365_MensajesSend m INNER JOIN t365_Clientes c ON m.id_cliente = c.id_cliente  WHERE 1=1 ".$args[0]." ";
				$sql_statement.= "GROUP BY m.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta) as dat  where 1=1 ".$args[1]." ORDER BY cont DESC";
			break;

			case "site_sel_ReportSMSReport":
				$sql_statement=" SELECT  m.id_cliente, COUNT(m.id_mensajes) AS cont, c.nombre_cliente,c.prefijo,c.cuenta  FROM t365_MensajesSend m INNER JOIN t365_Clientes c ON m.id_cliente = c.id_cliente  WHERE 1=1 ".$args[0]." ";
				$sql_statement.= "GROUP BY m.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta ORDER BY cont DESC ";
			break;

			case "site_sel_ReportSMS_Contador":
				$sql_statement= " SELECT SUM(cont) AS totalSMS FROM (SELECT COUNT(m.id_mensajes) AS cont FROM t365_MensajesSend m INNER JOIN t365_Clientes c ON m.id_cliente = c.id_cliente  WHERE (1 = 1) ".$args[0]." ) DERIVEDTBL ";
			break;

			case "site_sel_ReportSMS_Detail":
				$sql_statement= " SELECT m.movil, m.sms, m.send_date FROM t365_MensajesSend m INNER JOIN t365_Clientes c ON m.id_cliente = c.id_cliente  WHERE (1 = 1) ".$args[0]." ";
			break;

			case "site_sel_SMSEntradaTotal":
				$sql_statement= " SELECT e.movil , u.id_user FROM t365_MensajesRecib e INNER JOIN t365_Usuarios u ON e.movil = u.movil INNER JOIN  t365_Clientes c ON u.id_cliente = c.id_cliente ".$args[0]."  order by e.recib_date desc";
			break;

			case "site_sel_SMSEntrada":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.="SELECT e.movil , u.id_user, u.nombre + ' ' + u.apellido AS usuario, c.nombre_cliente, c.id_cliente,c.prefijo,c.cuenta,  e.sms, e.recib_date as fecha,ROW_NUMBER() OVER(ORDER BY e.recib_date DESC) AS RowID FROM t365_MensajesRecib e INNER JOIN t365_Usuarios u ON e.movil = u.movil INNER JOIN  t365_Clientes c ON u.id_cliente = c.id_cliente where 1=1 ".$args[0]." ) as dat where 1=1 ".$args[1]." ";
			break;

			case "site_sel_SMSEntradaReport":
				$sql_statement= " SELECT e.movil , u.id_user, u.nombre + ' ' + u.apellido AS usuario, c.nombre_cliente, c.id_cliente,c.prefijo,c.cuenta,  e.sms, e.recib_date as fecha FROM t365_MensajesRecib e INNER JOIN t365_Usuarios u ON e.movil = u.movil INNER JOIN  t365_Clientes c ON u.id_cliente = c.id_cliente ".$args[0]."  order by e.recib_date desc";
			break;

			case "site_sel_ReportStatusPanel":
				$sql_statement= "  SELECT COUNT(*) AS cont, s.cod_alarm FROM t365_StatusPanelCliente s INNER JOIN  t365_Clientes c ON s.idCliente = c.id_cliente where 1=1 ".$args[0]."  GROUP BY s.cod_alarm";
			break;


			case "site_sel_ClientesActivosSMSTotal":
				$sql_statement= " SELECT id_cliente, nombre_cliente, id_empresa,(SELECT COUNT(*) FROM t365_Usuarios u WHERE (u.id_cliente = c.id_cliente)  AND (u.status = 1) )AS cont FROM t365_Clientes c  WHERE   (id_status = 1) AND (id_cliente > 0) ".$args[0]."  ";
			break;

			case "site_sel_ClientesActivosSMSList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT id_cliente, nombre_cliente,prefijo,cuenta, id_empresa,(SELECT COUNT(*) FROM t365_Usuarios u WHERE (u.id_cliente = c.id_cliente)  AND (u.status = 1) )AS cont,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_Clientes c  WHERE   (id_status = 1) AND (id_cliente > 0) ".$args[0]." )";
				$sql_statement.= "  as dat  where 1=1 ".$args[2]." ".$args[1]."";
			break;

			case "site_sel_ClientesActivosSMSReport":
				$sql_statement= " SELECT id_cliente, nombre_cliente,prefijo,cuenta, id_empresa,(SELECT COUNT(*) FROM t365_Usuarios u WHERE (u.id_cliente = c.id_cliente)  AND (u.status = 1) )AS cont FROM t365_Clientes c  WHERE   (id_status = 1) AND (id_cliente > 0)  ".$args[0]."  ";
			break;

			case 'site_sel_ClientesActivosSMSUsuarios':
				$sql_statement= "SELECT id_user, nombre + ' ' + apellido AS nombre, movil FROM t365_Usuarios WHERE 1=1 AND (id_cliente = '".$args[0]."') AND (status = 1) ";
			break;

			case "site_sel_ClientesActivosSMSTotales":

				$sql_statement= "SELECT * FROM (SELECT     COUNT(*) AS abonados FROM t365_Clientes c WHERE (c.id_cliente > 0) AND (c.id_status = 1) ".$args[0].") DERIVEDTBL CROSS JOIN (SELECT COUNT(*) AS telefonos FROM t365_Usuarios u INNER JOIN  t365_Clientes c ON u.id_cliente = c.id_cliente INNER JOIN t365_Empresas e ON c.id_empresa = e.id_empresa WHERE 1=1 AND (u.status = 1) AND (c.id_status = 1) AND (c.id_cliente > 0)  ".$args[0].") DERIVEDTBL_1";
			break;

			case "site_sel_GetLineas":
				$sql_statement= " SELECT t.Linea FROM t365_Tramas t INNER JOIN t365_Clientes c ON t.cliente = c.id_cliente  WHERE t.Linea >0  and (YEAR(t.fecha) = ".$args[0].") ".$args[1]." GROUP BY t.Linea ORDER BY t.Linea";
			break;


			case "site_sel_ReporteLinea":
				$sql_statement= " SELECT DISTINCT t.cliente FROM t365_Tramas t INNER JOIN t365_Clientes c ON t.cliente = c.id_cliente WHERE 1=1 ".$args[0]." ";
			break;

			case "site_sel_GetLineasDetallesTotal":
				$sql_statement= "  SELECT DISTINCT c.id_cliente FROM t365_Tramas t INNER JOIN t365_Clientes c  ON t.cliente = c.id_cliente  WHERE    (t.Linea > 0) ".$args[0]." ORDER BY c.id_cliente ";
			break;

			case "site_sel_GetLineasDetalles":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT DISTINCT c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, c.direccion,DENSE_RANK() OVER(order by c.id_cliente asc) AS RowID  FROM t365_Tramas t INNER JOIN t365_Clientes c  ON t.cliente = c.id_cliente  WHERE    (t.Linea > 0)  ".$args[0].") as dat where 1=1 ".$args[1]."";
			break;

			case "site_sel_GetLineasDetallesReport":
				$sql_statement= "  SELECT DISTINCT c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, c.direccion FROM t365_Tramas t INNER JOIN t365_Clientes c  ON t.cliente = c.id_cliente  WHERE    (t.Linea > 0) ".$args[0]." ORDER BY c.id_cliente ";
			break;

			case "site_sel_EmpresasClientesTotal":
				$sql_statement= "  SELECT c.id_cliente FROM t365_Clientes c  INNER JOIN t365_Empresas e ON c.id_empresa = e.id_empresa where 1=1 ".$args[0]." ";
			break;

			case "site_sel_EmpresasClientesList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "  SELECT c.id_cliente, c.id_empresa, c.nombre_cliente,c.prefijo,c.cuenta, c.direccion, c.telf_local ,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_Clientes c  INNER JOIN t365_Empresas e ON c.id_empresa = e.id_empresa where 1=1 ".$args[0]." ";
				$sql_statement.= " ) as dat where 1=1 ".$args[2]." ";
			break;

			case "site_sel_EmpresasClientesReport":
				$sql_statement= "  SELECT c.id_cliente, c.id_empresa, c.nombre_cliente,c.prefijo,c.cuenta, c.direccion, c.telf_local  FROM t365_Clientes c  INNER JOIN t365_Empresas e ON c.id_empresa = e.id_empresa where 1=1 ".$args[0]." ".$args[1]." ";
			break;

			case "site_sel_ClienteZonaReport":
				$sql_statement= "SELECT  cast(id_zona as int) as id_zona, descrip as str_zona,ubicacion  FROM  t365_ClienteZonas where id_cliente = ".$args." ";
			break;

			case "site_sel_ClienteUserReport":
				$sql_statement= "SELECT * FROM   t365_Usuarios WHERE  (id_cliente = '".$args."')   ";
			break;

			case "site_sel_ClienteContactReport":
				$sql_statement= "select * from t365_NumEmergencia  WHERE (id_cliente = '".$args."') ";
			break;

			case "site_sel_ClientesSinCoordenadasTotal":
				$sql_statement= " SELECT id_cliente FROM t365_Clientes c  WHERE (latitud = 0) AND (longitud = 0) AND (id_cliente > 0) ".$args[0]." ";
			break;

			case "site_sel_ClientesSinCoordenadasList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT id_cliente, nombre_cliente,prefijo,cuenta, ciudad, direccion, latitud, longitud,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_Clientes c  WHERE (latitud = 0) AND (longitud = 0) AND (id_cliente > 0) ".$args[0]." ";
				$sql_statement.= " ) as dat where 1=1 ".$args[2]." ";
			break;


			case "site_sel_ClientesSinCoordenadasExport":
				$sql_statement= " SELECT id_cliente, nombre_cliente,prefijo,cuenta, ciudad, direccion, latitud, longitud FROM t365_Clientes c  WHERE (latitud = 0) AND (longitud = 0) AND (id_cliente > 0) ".$args[0]." ";
			break;

			case "site_sel_Empresas":
				$sql_statement= " SELECT * FROM  t365_Empresas where id_empresa>0 ".$args[0]." ";
			break;

			case "site_sel_CantidadClienteEmpresas":
				$sql_statement= " SELECT  2 AS aux,'Activos con SMS' AS descrip, COUNT(*) AS contador FROM t365_Clientes WHERE (id_empresa = '".$args[0]."') AND (id_status = 1) AND (id_cliente>0) UNION SELECT  1 AS aux,'Cant. Abonados' AS descrip, COUNT(*) AS contador FROM t365_Clientes WHERE (id_empresa = '".$args[0]."') AND (id_cliente>0) UNION SELECT  3 AS aux,'Cant. Telefonos SMS' AS descrip, COUNT(*) AS contador FROM t365_Usuarios u INNER JOIN t365_Clientes c  ON u.id_cliente = c.id_cliente INNER JOIN t365_Empresas e ON c.id_empresa = e.id_empresa WHERE 1=1 AND (u.status = 1) AND (c.id_status = 1) AND (c.id_empresa = '".$args[0]."')  AND (c.id_cliente>0)  UNION SELECT 4 AS aux, 'Cant. SMS Enviados ' + '(' + CONVERT(nvarchar(4), MONTH(GETDATE())) + '-' + CONVERT(nvarchar (4), YEAR(GETDATE())) + ')'  descrip, COUNT(*) AS contador FROM t365_MensajesSend sms INNER JOIN   t365_Clientes c ON sms.id_cliente = c.id_cliente WHERE (c.id_empresa = '".$args[0]."') AND (c.id_cliente > 0) AND   (MONTH(sms.send_date) = MONTH(GETDATE())) AND (YEAR(sms.send_date) = YEAR(GETDATE())) AND (sms.status = 1) ORDER BY aux ASC ";
			break;

			case "site_sel_CantidadClienteEmpresasParam":

				$sql_statement= " SELECT  2 AS aux,'Activos con SMS' AS descrip, COUNT(*) AS contador FROM t365_Clientes WHERE  (id_empresa = '".$args[0]."') AND (id_status = 1) AND (id_cliente>0) UNION SELECT  1 AS aux,'Cant. Abonados'  AS descrip, COUNT(*) AS contador FROM t365_Clientes WHERE (id_empresa = '".$args[0]."') AND (id_cliente>0) UNION SELECT  3 AS aux,'Cant. Telefonos SMS' AS descrip, COUNT(*) AS contador FROM t365_Usuarios u INNER JOIN t365_Clientes c  ON u.id_cliente = c.id_cliente INNER JOIN t365_Empresas e ON c.id_empresa = e.id_empresa WHERE  1=1 AND (u.status = 1) AND (c.id_status = 1) AND (c.id_empresa = '".$args[0]."')  AND (c.id_cliente>0) UNION SELECT 4 AS aux, 'Cant. SMS Enviados ".$args[1]."' as descrip, COUNT(*) AS contador FROM t365_MensajesSend sms   INNER JOIN t365_Clientes c ON sms.id_cliente = c.id_cliente WHERE (c.id_empresa = '".$args[0]."') AND (c.id_cliente > 0) AND  (sms.status = 1) ".$args[2]."  ORDER BY aux ASC ";
			break;

			case "site_sel_ZonasEmpresas":
				$sql_statement= "SELECT * FROM t365_ZonasClientes WHERE (idEmpresa = ".$args[0].")";
			break;

			case "site_sel_GetClienteInZonaTotal":
				$sql_statement= " SELECT c.id_cliente  FROM t365_Clientes c INNER JOIN t365_ClientesDatosExtras ce ON c.id_cliente = ce.id_cliente INNER JOIN  t365_ZonasClientes zc ON ce.idZona = zc.id_zona WHERE 1=1  ".$args[0]." ORDER BY zc.id_zona,c.id_cliente ASC ";
			break;

			case "site_sel_GetClienteInZona":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, zc.nombre,ROW_NUMBER() OVER(ORDER BY zc.id_zona,c.id_cliente ASC) AS RowID FROM t365_Clientes c INNER JOIN t365_ClientesDatosExtras ce ON c.id_cliente = ce.id_cliente INNER JOIN  t365_ZonasClientes zc ON ce.idZona = zc.id_zona WHERE 1=1  ".$args[0]." ) as dat WHERE 1=1 ".$args[1]." order by cast(cuenta as int) asc";
			break;

			case "site_sel_GetClienteInZonaReport":
				$sql_statement= " SELECT c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, zc.nombre  FROM t365_Clientes c INNER JOIN t365_ClientesDatosExtras ce ON c.id_cliente = ce.id_cliente INNER JOIN  t365_ZonasClientes zc ON ce.idZona = zc.id_zona WHERE 1=1  ".$args[0]." ORDER BY zc.id_zona,cast(c.cuenta as int) ASC ";
			break;

			case "site_sel_GetClienteNotInZonaTotal":
				$sql_statement= " SELECT id_cliente  FROM t365_Clientes c  WHERE (id_empresa = '".$args[0]."') AND (NOT EXISTS (SELECT     id_cliente FROM t365_ClientesDatosExtras e  WHERE e.id_cliente = c.id_cliente)) ";
			break;


			case "site_sel_GetClienteNotInZona":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT  nombre_cliente, id_cliente,prefijo,cuenta, 'No Definida' AS nombre ,ROW_NUMBER() OVER(ORDER BY id_cliente ASC) AS RowID   FROM t365_Clientes c  WHERE (id_empresa = '".$args[0]."') AND (NOT EXISTS (SELECT     id_cliente FROM t365_ClientesDatosExtras e  WHERE e.id_cliente = c.id_cliente)) ) as dat WHERE  1=1 ".$args[1]." order by cast(cuenta as int) asc";
			break;

			case "site_sel_GetClienteNotInZonaReport":
				$sql_statement = " SELECT  nombre_cliente, id_cliente,prefijo,cuenta, 'No Definida' AS nombre FROM t365_Clientes c  WHERE (id_empresa = '".$args[0]."') AND (NOT EXISTS (SELECT     id_cliente FROM t365_ClientesDatosExtras e  WHERE e.id_cliente = c.id_cliente)) order by cast(cuenta as int) asc";
			break;

			case "site_sel_UltimaSignalClienteTotal":
				$sql_statement = " SELECT s.id_trama FROM t365_GrupoCodigosAlarma AS gc INNER JOIN t365_CodigosAlarma AS ca ON gc.idGrupo = ca.idGrupo RIGHT OUTER JOIN t365_Eventos AS e ON ca.codigo = e.cod_alarm RIGHT OUTER JOIN t365_TramasUltimaSignal AS s ON e.cod_event = s.evento AND e.id_protocolo = s.protocolo RIGHT OUTER JOIN t365_Clientes AS c ON s.cliente = c.id_cliente ".$args[0]."   ";
			break;

			case "site_sel_UltimaSignalClienteList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT DISTINCT s.id_trama, s.descrip, s.status, s.cliente, s.evento, s.user_zone, CONVERT(DATETIME, s.fecha, 100) AS fecha, DATEDIFF(day, s.fecha, GETDATE()) AS Dif, s.protocolo, s.Variante, s.Linea, c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, CASE WHEN s.evento is null and e.descript is null THEN 'Sin transmision' WHEN s.evento is not null and e.descript is null THEN 'Evento '+s.evento+' no definido' ELSE s.evento+' - '+e.descript end as descript, DATEDIFF(hour, s.fecha, GETDATE()) AS horas, gc.idGrupo ,ROW_NUMBER() OVER(ORDER BY s.fecha asc) AS RowID FROM t365_GrupoCodigosAlarma AS gc INNER JOIN t365_CodigosAlarma AS ca ON gc.idGrupo = ca.idGrupo RIGHT OUTER JOIN t365_Eventos AS e ON ca.codigo = e.cod_alarm RIGHT OUTER JOIN t365_TramasUltimaSignal AS s ON e.cod_event = s.evento AND e.id_protocolo = s.protocolo RIGHT OUTER JOIN t365_Clientes AS c ON s.cliente = c.id_cliente ".$args[0]." ";
				$sql_statement.= ") as dat where 1=1 ".$args[1]."  order by horas desc";
			break;

			case "site_sel_UltimaSignalClienteReport":
				$sql_statement= "SELECT DISTINCT s.id_trama, s.descrip, s.status, s.cliente, s.evento, s.user_zone, CONVERT(DATETIME, s.fecha, 100) AS fecha, DATEDIFF(day, s.fecha, GETDATE()) AS Dif, s.protocolo, s.Variante, s.Linea, c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, CASE WHEN s.evento is null and e.descript is null THEN 'Sin transmision' WHEN s.evento is not null and e.descript is null THEN 'Evento '+s.evento+' no definido' ELSE s.evento+' - '+e.descript end as descript, DATEDIFF(hour, s.fecha, GETDATE()) AS horas, gc.idGrupo FROM t365_GrupoCodigosAlarma AS gc INNER JOIN t365_CodigosAlarma AS ca ON gc.idGrupo = ca.idGrupo RIGHT OUTER JOIN t365_Eventos AS e ON ca.codigo = e.cod_alarm RIGHT OUTER JOIN t365_TramasUltimaSignal AS s ON e.cod_event = s.evento AND e.id_protocolo = s.protocolo RIGHT OUTER JOIN t365_Clientes AS c ON s.cliente = c.id_cliente ".$args[0]." ORDER BY horas desc ";
			break;

			case "site_sel_ReportUltAPECIETotal":
				$sql_statement= "SELECT c.id_cliente, c.nombre_cliente, c.direccion, c.telf_local, sp.cod_alarm,  CONVERT(DATETIME, sp.Fecha, 100) AS fecha, DATEDIFF(day, sp.Fecha, GETDATE()) AS Dif,case when sp.cod_alarm='CIE' then 'Cierre' when sp.cod_alarm='APE' then 'Apertura' when sp.cod_alarm is null then'Sin transmision'end as status FROM t365_Clientes AS c LEFT OUTER JOIN t365_StatusPanelCliente AS sp ON c.id_cliente=sp.idCliente ".$args[0]." ";
			break;

			case "site_sel_ReportUltAPECIEList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, c.direccion, c.telf_local, sp.cod_alarm,  CONVERT(DATETIME, sp.Fecha, 100) AS fecha, DATEDIFF(day, sp.Fecha, GETDATE()) AS Dif,case when sp.cod_alarm='CIE' then 'Cierre' when sp.cod_alarm='APE' then 'Apertura' when sp.cod_alarm is null then'Sin transmision'end as status ,ROW_NUMBER() OVER(ORDER BY DATEDIFF(day, sp.Fecha, GETDATE()) desc) AS RowID  FROM t365_Clientes AS c LEFT OUTER JOIN t365_StatusPanelCliente AS sp ON c.id_cliente=sp.idCliente ".$args[0].") as dat where 1=1 ".$args[1]."";
			break;

			case "site_sel_ReportUltAPECIEReport":
				$sql_statement= "SELECT c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta, c.direccion, c.telf_local, sp.cod_alarm,  CONVERT(DATETIME, sp.Fecha, 100) AS fecha, DATEDIFF(day, sp.Fecha, GETDATE()) AS Dif,case when sp.cod_alarm='CIE' then 'Cierre' when sp.cod_alarm='APE' then 'Apertura' when sp.cod_alarm is null then'Sin transmision'end as status FROM t365_Clientes AS c LEFT OUTER JOIN t365_StatusPanelCliente AS sp ON c.id_cliente=sp.idCliente ".$args[0]." order by Dif desc";
			break;

			case "site_sel_ClientesSinImagenTotal":
				$sql_statement= "SELECT id_cliente  FROM t365_Clientes c WHERE (id_cliente > 0) AND (imagen IS NULL OR imagen = '') ".$args[0]." ";
			break;

			case "site_sel_ClientesSinImagenList":
				$sql_statement= " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT id_cliente, nombre_cliente,prefijo,cuenta, imagen,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_Clientes c WHERE (id_cliente > 0) AND  (imagen IS NULL OR imagen = '') ".$args[0]." ";
				$sql_statement.= ") as dat where 1=1 ".$args[2]."";
			break;

			case 'site_sel_ClientesSinImagenReport':
				$sql_statement= " SELECT id_cliente, nombre_cliente,prefijo,cuenta, imagen,ciudad,direccion FROM t365_Clientes c WHERE (id_cliente > 0) AND  (imagen IS NULL OR imagen = '') ".$args[0]."";
			break;

			case 'site_sel_AllTypoLog':
				$sql_statement= " SELECT * from t365_TypeLog where tipo=1 order by orden asc";
			break;

			case 'site_sel_AllPersonalSelect':
				$sql_statement= " SELECT idPersonal ,nombre  FROM  t365_Personal where id_empresa= '".$args[0]."' order by nombre asc  ";
			break;

			case 'site_sel_ReportAccUserTotal':
				$sql_statement.= " SELECT l.IdLog FROM t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal LEFT OUTER JOIN t365_Clientes AS c ON l.idAbonado = c.id_cliente and  c.id_cliente<>0 where l.tipoAbonado=0 ".$args[0]." ";

				//carga los log de usuarios relacionados a clientes'
				$sql_statement.= " union SELECT l.IdLog FROM t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal LEFT OUTER JOIN t365_Clientes AS c ON l.idAbonado = c.id_cliente and  c.id_cliente<>0 where l.tipoAbonado=1 ".$args[0]." ";

				//carga los log de usuarios relacionados asociados'
				$sql_statement.= " union SELECT l.IdLog  FROM t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal  LEFT OUTER JOIN t365_asociados AS a ON l.idAbonado = a.id_asociado WHERE        (l.tipoAbonado = 3)  ".$args[0]." ";

			break;

			case 'site_sel_ReportAccUserList':
				$sql_statement= "WITH rowx AS (";
				//'carga los log del usuario'
				$sql_statement.=" SELECT l.IdLog, l.idOperador, l.idAbonado, l.tipoAbonado, l.idTipoLog, l.idStation, l.Extra, l.Fecha, t.Descript AS accion, c.nombre_cliente AS cliente, p.nombre AS usuario FROM t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal LEFT OUTER JOIN t365_Clientes AS c ON l.idAbonado = c.id_cliente and  c.id_cliente<>0 where l.tipoAbonado=0 ".$args[0]." ";

				//'carga los log de usuarios relacionados a clientes'
				$sql_statement.=" union SELECT l.IdLog, l.idOperador, l.idAbonado, l.tipoAbonado, l.idTipoLog, l.idStation, l.Extra, l.Fecha, t.Descript AS accion, c.nombre_cliente AS cliente, p.nombre AS usuario FROM           t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal LEFT OUTER JOIN t365_Clientes AS c ON l.idAbonado = c.id_cliente and  c.id_cliente<>0 where l.tipoAbonado=1 ".$args[0]." ";

				//'carga los log de usuarios relacionados asociados'
				$sql_statement.=" union SELECT        l.IdLog, l.idOperador, l.idAbonado, l.tipoAbonado, l.idTipoLog, l.idStation, l.Extra, l.Fecha, t.Descript AS accion, a.nombre AS cliente, p.nombre AS usuario FROM t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal  LEFT OUTER JOIN t365_asociados AS a ON l.idAbonado = a.id_asociado WHERE        (l.tipoAbonado = 3)  ".$args[0]." ";

				$sql_statement.=") select dat.* from (SELECT *, DENSE_RANK() over(order by fecha desc) as RowID FROM rowx ) as dat where 1=1 ".$args[1]."";

			break;

			case 'site_sel_ReportAccUserReport':
				//carga los log del usuario'
				$sql_statement= "SELECT l.IdLog, l.idOperador, l.idAbonado, l.tipoAbonado, l.idTipoLog, l.idStation, l.Extra, l.Fecha, t.Descript AS accion, c.nombre_cliente AS cliente, p.nombre AS usuario FROM t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal LEFT OUTER JOIN t365_Clientes AS c ON l.idAbonado = c.id_cliente and  c.id_cliente<>0 where l.tipoAbonado=0 ".$args[0]." ";

				//carga los log de usuarios relacionados a clientes'
				$sql_statement.= "union SELECT l.IdLog, l.idOperador, l.idAbonado, l.tipoAbonado, l.idTipoLog, l.idStation, l.Extra, l.Fecha, t.Descript AS accion, c.nombre_cliente AS cliente, p.nombre AS usuario FROM           t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal LEFT OUTER JOIN t365_Clientes AS c ON l.idAbonado = c.id_cliente and  c.id_cliente<>0 where l.tipoAbonado=1 ".$args[0]." ";

				//carga los log de usuarios relacionados asociados'
				$sql_statement.= "union SELECT l.IdLog, l.idOperador, l.idAbonado, l.tipoAbonado, l.idTipoLog, l.idStation, l.Extra, l.Fecha, t.Descript AS accion, a.nombre AS cliente, p.nombre AS usuario FROM t365_LogOperador AS l INNER JOIN t365_TypeLog AS t ON l.idTipoLog = t.idTipoLog INNER JOIN t365_Personal AS p ON l.idOperador = p.idPersonal  LEFT OUTER JOIN t365_asociados AS a ON l.idAbonado = a.id_asociado WHERE        (l.tipoAbonado = 3)  ".$args[0]." ";

				$sql_statement.= "order by l.Fecha desc ";

			break;

			case "site_sel_SoporteMotivos":
				$sql_statement= "SELECT d.idEmpresa AS idempresa, s.descripcion AS descripcion, d.correo AS correo  FROM t365_DepartamentosEmpresa d INNER JOIN  t365_SoporteMotivos s ON d.idEmpresa = s.idEmpresa  AND d.idDepartamento = s.idDepartCorreo WHERE (d.idEmpresa =  ".$args[0].")";
			break;

			case "site_sel_allclientebyAsociados":
				$sql_statement= " SELECT  t365_Clientes.* FROM  t365_asociados_abonados ASO  INNER JOIN t365_Clientes ON ASO.id_cliente = t365_Clientes.id_cliente where ASO.id_asociado = ".$args[0]." ";
			break;

			case "site_sel_allCamarasMarca":
				$sql_statement= " SELECT  * FROM t365_CamarasMarcas";
			break;


			case "site_sel_allCamarasModelos":
				$sql_statement= " SELECT  * FROM t365_CamarasModelos where id_marca='".$args[0]."'";
			break;

			case "site_sel_allCCTVModoRegistro":
				$sql_statement= " SELECT  * FROM t365_CCTVModoRegistro";
			break;

			case "site_sel_allSubTipo":
				$sql_statement= " SELECT  * FROM t365_EquiposSubTipos where id_tipo='".$args[0]."'";
			break;

			case "site_sel_DatosViewCamCliente":
				$sql_statement= " SELECT * FROM t365_ClientesCCTV  where id_cliente='".$args[1]."' and id_cctv='".$args[0]."' ";
			break;

			case "site_sel_DatosViewCamClienteChannel":
				$sql_statement= " SELECT c.*,m.string_acceso,ch.descripcion as descC FROM t365_ClientesCCTV c  inner join t365_EquiposModelos m on c.id_modelo=m.id_modelo inner join t365_ClientesCCTV_Channel ch on c.id_cctv=ch.id_cctv where c.id_cliente='".$args[1]."' and c.id_cctv='".$args[0]."' and ch.channel='".$args[2]."' ";
			break;

			case "site_sel_GetVehiculosListCount":
				$sql_statement= "SELECT v.id_vehiculo, v.id_empresa, v.id_tipo_gps, v.id_tipo_vehiculos, v.id_marca, v.id_modelo,v.monitoreo, v.codigo_gps, v.alias, v.placa, v.sim, v.imei, v.color, v.anio, v.vel_maxima, v.status,v.nota, e.nombre AS emp FROM t365_Vehiculos v LEFT OUTER JOIN t365_Empresas e ON v.id_empresa = e.id_empresa ".$args[0]." ";
			break;

			case "site_sel_GetVehiculosList":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT v.id_vehiculo, v.id_empresa, v.id_tipo_gps, v.id_tipo_vehiculos, v.id_marca, v.id_modelo,v.monitoreo, v.codigo_gps, v.alias, v.placa, v.sim, v.imei, v.color, v.anio, v.vel_maxima, v.status,v.nota, e.nombre AS emp,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_Vehiculos v LEFT OUTER JOIN t365_Empresas e ON v.id_empresa = e.id_empresa where 1=1  ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_VehiculosImagen":
				$sql_statement= "SELECT * FROM  t365_VehiculosImagen WHERE codgps='".$args."' order by id asc ";
			break;


			case "site_sel_VerifiCodgpsVehiculos":
				$sql_statement= "SELECT * FROM t365_Vehiculos WHERE (codigo_gps = '".$args[0]."') ";
			break;

			case "site_sel_ClienteListSMS":
				$sql_statement= "SELECT c.id_cliente, c.nombre_cliente,c.prefijo,c.cuenta FROM t365_Clientes c WHERE   (c.id_empresa = '".$args[0]."') ".$args[1]." ";
			break;

			case "site_sel_UserClienteSMS":
				$sql_statement= "SELECT distinct id_user,nombre, movil  as recept,id_cliente FROM t365_Usuarios WHERE (id_cliente = '".$args[0]."') AND (movil IS NOT NULL)  AND (LEN(movil) > 8) ";
			break;

			case "site_sel_UserClienteEMAIL":
				$sql_statement= "SELECT distinct id_user,nombre, email as recept ,id_cliente FROM t365_Usuarios WHERE (id_cliente = '".$args[0]."') AND (email IS NOT NULL)  ";
			break;

			case "site_sel_PersonalEmpresasSearch":
				$sql_statement= "SELECT * FROM t365_Personal p WHERE (p.eliminado = 0) ".$args[0]." ";
			break;


			case "site_sel_VeiricarSessionOperador":
				$sql_statement= "SELECT * FROM t365_OperadorSession where IdOperador = '".$args[0]." '";
			break;

			case "site_sel_SignalesObservacion":
				$sql_statement= " SELECT p.idPersonal, p.nombre, o.fecha, o.observacion FROM t365_TramasObservaciones AS o LEFT OUTER JOIN t365_Personal AS p ON o.idoperador = p.idPersonal where o.idtrama='".$args[0]."' order by o.fecha desc";
			break;


			case "site_sel_DispMarcas":
				$sql_statement= " SELECT m.id_marca,m.descripcion FROM t365_EquiposSubTipos t inner join  t365_EquiposSubTipos_Marcas s on t.id_subtipo = s.id_subtipo inner join   t365_EquiposMarcas m on s.id_marca = m.id_marca where 1=1 ".$args[0]."";
			break;

			case "site_sel_DispModelos":
				$sql_statement= "  SELECT   m.*,sm.id_marca FROM t365_EquiposModelos AS m INNER JOIN t365_EquiposSubTipos_Marcas AS sm ON m.id_subtipo_marca = sm.id_subtipo_marca where 1=1 ".$args[0]."";
			break;

			case "site_sel_DataCCTV":
				$sql_statement= " SELECT  * FROM t365_ClientesCCTV where 1=1 ".$args[0]."";
			break;

			case "site_sel_DataCCTV_Channel":
				$sql_statement= " SELECT  * FROM t365_ClientesCCTV_Channel where 1=1 ".$args[0]." order by channel asc";
			break;

			case "site_sel_PrefijoRangoEmpresa":
				$sql_statement= "SELECT  distinct prefijo FROM t365_ConfigPort_Lineas order by prefijo asc";
			break;

			case "site_sel_TypeDispositivoCuenta":
				$sql_statement= "SELECT * FROM t365_EquiposSubTipos where 1=1 ".$args[0]."";
			break;

			case "site_sel_TypeClienteSelect":
				$sql_statement= "SELECT * FROM t365_TypeCliente where 1=1 ".$args[0]."";
			break;

			case "site_sel_TypeRondas":
				$sql_statement= "SELECT * FROM t365_RondasTipo where 1=1 ".$args[0]."";
			break;

			case "site_sel_CalendarioRondas":
				$sql_statement= "SELECT * FROM t365_RondasCalendario where 1=1 ".$args[0]."";
			break;

			case "site_sel_GetRondasCliente":
				$sql_statement= "SELECT id ,id_zona as id_punto,descrip  FROM t365_ClienteZonas where 1=1 ".$args[0]."";
			break;

			case "site_sel_GetDepartRondas":
				$sql_statement= "SELECT * from t365_DepartamentosEmpresa   where 1=1 ".$args[0]."";
			break;

			case "site_sel_VerifiDepartaSoporte":
				$sql_statement= "SELECT * from t365_SoporteMotivos where idDepartCorreo='".$args[0]."' ";
			break;

			case "site_sel_VerifiCodeAlamEvento":
				$sql_statement= "SELECT * from t365_Eventos where cod_alarm='".$args[0]."' ";
			break;

			case "site_sel_RondaData":
				$sql_statement= " SELECT  * FROM t365_Rondas where 1=1 ".$args[0]."";
			break;

			case "site_sel_RondaDataPuntos":
				$sql_statement= "SELECT p.id, p.id_zona AS id_punto, p.descrip FROM t365_ClienteZonas AS p INNER JOIN t365_RondasPuntos AS pa ON p.id = pa.id_punto WHERE 1=1 ".$args[0]."";
			break;

			case "site_sel_ComentariosSignalOpe":
				$sql_statement= " SELECT        t365_TramasProcesadasObservaciones.fecha, t365_TramasProcesadasObservaciones.observacion, t365_Personal.nombre FROM t365_TramasProcesadasObservaciones INNER JOIN t365_Personal ON t365_TramasProcesadasObservaciones.idoperador = t365_Personal.idPersonal WHERE (t365_TramasProcesadasObservaciones.idtrama = '".$args[0]."') ORDER BY t365_TramasProcesadasObservaciones.fecha DESC";
			break;

			case "site_sel_TypoEvento":
				$sql_statement= "SELECT * FROM t365_TypeEvento";
			break;

			case "site_sel_GetIdZonaClienteZonas":
				$sql_statement= "select top 1 id_zona from t365_ClienteZonas where type=3 AND id_cliente='".$args[0]."' order by id  desc";
			break;

			case "site_sel_GetTeclasPanicPc":
				$sql_statement= "select * from t365_TeclasPanicPC";
			break;

			case "site_sel_ContOrdServicioos":
				$sql_statement= " SELECT * FROM (SELECT COUNT(*) AS sinasginar FROM t365_OrdenServicio WHERE      (id_status = 1) AND id_empresa = '".$args[0]."') DERIVEDTBL CROSS JOIN (SELECT COUNT(*) AS asignada FROM t365_OrdenServicio WHERE (id_status = 2) AND id_empresa = '".$args[0]."') DERIVEDTBL_1 CROSS JOIN (SELECT COUNT(*) AS seguimiento FROM t365_OrdenServicio WHERE (id_status = 3) AND id_empresa = '".$args[0]."') DERIVEDTBL_2  CROSS JOIN (SELECT COUNT(*) AS notificaciones FROM t365_OrdServNotifi AS n INNER JOIN t365_OrdSerLog AS l ON n.id_objetivo = l.id_log WHERE (n.id_destino = '".$args[1]."') AND (n.vista = 0) AND (l.privado = 0)) DERIVEDTBL_3 CROSS JOIN (SELECT TOP 10 COUNT(*) AS notifi_nom FROM t365_OrdServNotifi AS n INNER JOIN t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN t365_OrdServ_UserRelacionado AS ur ON l.id_log = ur.id_log WHERE (n.id_destino =  '".$args[1]."') AND (ur.id_personal =  '".$args[1]."') AND (n.vista = 0) AND (l.privado = 0)) DERIVEDTBL_4 CROSS JOIN (SELECT TOP 10 COUNT(*) AS notifi_privad FROM t365_OrdServNotifi AS n INNER JOIN t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN t365_OrdServ_UserRelacionado AS ur ON l.id_log = ur.id_log WHERE (n.id_destino =  '".$args[1]."') AND (ur.id_personal =  '".$args[1]."') AND (n.vista = 0) AND (l.privado = 1)) DERIVEDTBL_5";
			break;

			case "site_sel_GetNotifiLinkUserRel":
				$sql_statement= "SELECT  ".$args[0]."  n.id_notificacion, n.id_destino, n.id_autor, n.id_objetivo, n.accion, n.vista, n.fecha, a.descripcion, a.iconWeb, s.colorbg, o.correlativo, p.nombre, p.imagen, l.fecha AS dateLog, DATEDIFF(minute, l.fecha, GETDATE()) AS diif, o.id_orden, o.id_status, o.tipo_orden AS tipoOR, o.tipo_cliente,CASE WHEN  o.tipo_cliente = 1 THEN  (SELECT c.nombre_cliente  FROM t365_Clientes c  WHERE c.id_cliente = o.id_cliente) ELSE  (SELECT nombre_cliente  FROM t365_ClientesServicios cs  WHERE cs.id_cliente = o.id_cliente) END AS cliente  FROM t365_OrdServNotifi AS n INNER JOIN t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN t365_OrdSerAccionesLog AS a ON n.accion = a.id_accion INNER JOIN  t365_OrdSerStatus AS s ON l.id_status = s.id_status INNER JOIN  t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN  t365_Personal AS p ON n.id_autor = p.idPersonal INNER JOIN  t365_OrdServ_UserRelacionado AS ur ON l.id_log = ur.id_log WHERE (n.id_destino = '".$args[1]."')  and (ur.id_personal = '".$args[1]."') ".$args[2]." order by n.fecha desc ";

			break;

			case "site_sel_GetNotifiLink":
				$sql_statement= "SELECT ".$args[0]." n.id_notificacion, n.id_destino, n.id_autor, n.id_objetivo, n.accion, n.vista, n.fecha, a.descripcion, a.iconWeb, s.colorbg, o.correlativo, p.nombre, p.imagen, l.fecha as dateLog,DATEDIFF(minute,l.fecha,getdate()) AS diif, o.id_orden, o.id_status, o.tipo_orden as tipoOR, o.tipo_cliente,CASE WHEN  o.tipo_cliente = 1 THEN  (SELECT c.nombre_cliente  FROM t365_Clientes c  WHERE c.id_cliente = o.id_cliente) ELSE  (SELECT nombre_cliente  FROM t365_ClientesServicios cs  WHERE cs.id_cliente = o.id_cliente) END AS cliente  FROM t365_OrdServNotifi AS n INNER JOIN  t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN  t365_OrdSerAccionesLog AS a ON n.accion = a.id_accion INNER JOIN   t365_OrdSerStatus AS s ON l.id_status = s.id_status INNER JOIN  t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN  t365_Personal AS p ON n.id_autor = p.idPersonal WHERE (n.id_destino = '".$args[1]."') ".$args[2]." order by n.fecha desc ";

		 	break;

		 	case "site_sel_TipoOdrServicio":
				$sql_statement = "SELECT * FROM t365_EquiposTipos WHERE (eliminado=0) ".$args[0]." ORDER BY descripcion";
			break;

			case "site_sel_allclientesBuscarOrdenes":
				$sql_statement = " SELECT 1 AS tipoc, id_cliente,prefijo,cuenta, nombre_cliente, rif, telf_local, telf_movil, email, direccion,id_empresa FROM t365_Clientes where 1=1 ".$args[0]." ".$args[1]."   ORDER BY CASE WHEN ISNUMERIC(cuenta) = 1 THEN 0 ELSE 1 END, CASE WHEN ISNUMERIC(cuenta) = 1 THEN CAST(cuenta AS INT) ELSE 0 END ASC ";
			break;

			case "site_sel_SearchUser":
				$sql_statement = "SELECT idPersonal, nombre,notif_sms_servicio,notifi_email_servicio FROM t365_Personal WHERE (id_empresa = '".$args[0]."')  ".$args[1]." AND eliminado=0 ";
			break;

			case "site_sel_SearchItemsOrd":
				$sql_statement = "SELECT id_modelo as id ,descripcion  as text,eliminado FROM  t365_EquiposModelos where eliminado=0 ".$args[0]."";

			break;

			case "site_sel_getDataOrdenIni":
				$sql_statement = "SELECT  * FROM t365_OrdenServicio where 1=1  ".$args[0]."  ";
			break;

			case "site_sel_GetUSerNotificaciones":
				$sql_statement = " SELECT idPersonal,nombre, correo,notifi_email_servicio FROM t365_Personal WHERE 1=1  ".$args[0]."  ";
			break;

			case "site_sel_GetDatOrdBasic":
				$sql_statement = " SELECT distinct o.correlativo, o.tipo_orden, o.fechaAtencion, o.problema, (SELECT nombre FROM t365_Personal WHERE (idPersonal = o.id_tecnico)) AS tecnico,  (SELECT c.nombre_cliente FROM t365_Clientes c WHERE c.id_cliente = o.id_cliente) AS cliente, st.descripcion as sts, (SELECT descripcion FROM t365_OrdSerLog where id_log='".$args[0]."') as coment FROM  t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS st ON o.id_status = st.id_status where id_orden='".$args[1]."' ";
			break;

			case "site_sel_GetDatosStatus":
				$sql_statement = "SELECT o.correlativo, o.tipo_orden, o.fechaAtencion, o.problema, (SELECT nombre FROM t365_Personal WHERE (idPersonal = o.id_tecnico)) AS tecnico, (SELECT c.nombre_cliente FROM t365_Clientes c WHERE c.id_cliente = o.id_cliente)  AS cliente, st.descripcion as sts, (SELECT descripcion FROM t365_OrdSerLog where id_log='".$args[0]."') as coment FROM  t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS st ON o.id_status = st.id_status where o.id_orden='".$args[1]."' ";
			break;

			case "site_sel_OrdenServicioVisitaCount":
				$sql_statement = "SELECT o.id_orden  FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN  t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]."  ";
			break;

			case "site_sel_OrdenServicioVisita":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT o.id_orden,o.tipo_orden, o.id_empresa, o.id_cliente, o.tipo_cliente, o.id_tipo_orden, o.fechaCreada, DATEDIFF(day, o.fechaCreada, GETDATE()) AS diff, o.prioridad, o.fechaAtencion,   o.problema, o.pre_observacion, o.contacto, o.telf_contacto, c.nombre_cliente,c.prefijo,c.cuenta, o.id_status, o.id_ord_garantia, s.descripcion, s.id_status AS st, o.id_tecnico, p.nombre AS tecnico, s.descripcion AS estatus,   s.colorbg, s.color, o.correlativo ,ROW_NUMBER() OVER(".$args[3].") AS RowID  FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN  t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]."  ";
				$sql_statement.= " ) as dat  where ".$args[6]." ".$args[7]." ";
			break;

			case "site_sel_OrdenServicioVisitaComentIni":
				$sql_statement =" select top 1  descripcion from t365_OrdSerLog   where id_orden='".$args[0]."' and  id_status=".$args[1]." order by fecha desc ";
			break;


			case "site_sel_OrdenServicioVisitaDatos":
				$sql_statement = "SELECT o.*, c.nombre_cliente, c.rif, c.telf_local, c.telf_movil, c.email, c.direccion,c.id_empresa, t.nombre AS tecnico,   (SELECT per.nombre FROM t365_Personal per  WHERE (per.idPersonal = o.id_usuario))  AS creador FROM t365_OrdenServicio o INNER JOIN ".$args[0]." c ON o.id_cliente = c.id_cliente  LEFT OUTER JOIN t365_Personal t ON o.id_tecnico = t.idPersonal  WHERE (o.id_empresa = '".$args[1]."') AND (o.id_orden = '".$args[2]."') ";
			break;

			case "site_sel_OrdenesServicioTimeline":
				$sql_statement = "SELECT l.id_log,l.fecha, p.nombre, a.descripcion AS accion, s.descripcion AS estatus, l.descripcion, p.imagen, a.iconWeb,l.privado,o.id_status,l.id_usuario as autorAux FROM t365_OrdSerLog AS l INNER JOIN t365_Personal AS p ON l.id_usuario = p.idPersonal INNER JOIN t365_OrdSerAccionesLog AS a ON l.id_accion = a.id_accion  INNER JOIN t365_OrdenServicio AS o ON l.id_orden = o.id_orden  INNER JOIN  t365_OrdSerStatus AS s ON l.id_status = s.id_status WHERE (o.id_orden ='".$args[0]."')  AND (o.id_empresa ='".$args[1]."') and (l.privado=0)  ".$args[3]." union SELECT l.id_log, l.fecha, p.nombre, a.descripcion AS accion, s.descripcion AS estatus, l.descripcion, p.imagen, a.iconWeb, l.privado, o.id_status,l.id_usuario as autorAux  FROM t365_OrdSerLog AS l INNER JOIN t365_Personal AS p ON l.id_usuario = p.idPersonal INNER JOIN t365_OrdSerAccionesLog AS a ON l.id_accion = a.id_accion INNER JOIN t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN t365_OrdSerStatus AS s ON l.id_status = s.id_status INNER JOIN t365_OrdServ_UserRelacionado AS ur ON l.id_log = ur.id_log WHERE (o.id_orden = '".$args[0]."') AND (o.id_empresa = '".$args[1]."') AND (l.privado = 1) AND (ur.id_personal = '".$args[2]."') ".$args[3]." ORDER BY o.id_status, l.fecha";
			break;

			case "site_sel_GetUSerRelData":
				$sql_statement = " SELECT p.nombre FROM t365_OrdServ_UserRelacionado AS ur INNER JOIN t365_Personal AS p ON ur.id_personal = p.idPersonal INNER JOIN t365_OrdSerLog AS l ON ur.id_log = l.id_log WHERE (ur.id_log = '".$args[0]."') AND (ur.id_personal <> '".$args[1]."')";
			break;

			case "site_sel_OrdSearchCliente":
				$sql_statement = " SELECT DISTINCT c.id_cliente AS id_cliente, 1 AS tipoc, o.id_empresa, c.nombre_cliente,c.prefijo,CASE WHEN ISNUMERIC(c.cuenta) = 1 THEN CAST(c.cuenta AS INT) ELSE 0 END  as cuenta FROM t365_OrdenServicio o INNER JOIN t365_Clientes c ON o.id_cliente = c.id_cliente WHERE (o.tipo_cliente = 1) AND (o.id_empresa = '".$args[0]."')  ".$args[1]."  ORDER BY CASE WHEN ISNUMERIC(c.cuenta) = 1 THEN CAST(c.cuenta AS INT) ELSE 0 END ASC";
			break;

			case "site_sel_GetUserRelOrd":
				$sql_statement = " SELECT id_tecnico  as id FROM t365_OrdSerTecRelacionados WHERE (id_orden = '".$args[0]."') union all SELECT id_tecnico  as id FROM t365_OrdenServicio WHERE (id_orden = '".$args[0]."') union all SELECT id_usuario  as id FROM t365_OrdenServicio WHERE (id_orden = '".$args[0]."')";

			break;

			case "site_sel_GetNotifIniGenCount":
				$sql_statement = "SELECT ".$args[0]." n.id_notificacion FROM t365_OrdServNotifi AS n INNER JOIN  t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN  t365_OrdSerAccionesLog AS a ON n.accion = a.id_accion INNER JOIN   t365_OrdSerStatus AS s ON l.id_status = s.id_status INNER JOIN  t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN  t365_Personal AS p ON n.id_autor = p.idPersonal WHERE (n.id_destino = '".$args[1]."') ".$args[2]." order by n.fecha desc ";
			break;

			case "site_sel_GetNotifIniGen":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT ".$args[0]." n.id_notificacion, n.id_destino, n.id_autor, n.id_objetivo, n.accion, n.vista, n.fecha, a.descripcion, a.iconWeb, s.colorbg, o.correlativo, p.nombre, p.imagen, l.fecha as dateLog,DATEDIFF(minute,l.fecha,getdate()) AS diif, o.id_orden, o.id_status, o.tipo_cliente,o.tipo_orden,CASE WHEN  o.tipo_cliente = 1 THEN  (SELECT CONCAT(c.prefijo,'-',c.cuenta,' ',c.nombre_cliente)  FROM t365_Clientes c  WHERE c.id_cliente = o.id_cliente) ELSE  (SELECT nombre_cliente  FROM t365_ClientesServicios cs  WHERE cs.id_cliente = o.id_cliente) END AS cliente ,l.id_status as id_statusL,s.descripcion as status, l.descripcion as coment,ROW_NUMBER() OVER(order by n.fecha desc) AS RowID FROM t365_OrdServNotifi AS n INNER JOIN  t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN  t365_OrdSerAccionesLog AS a ON n.accion = a.id_accion INNER JOIN   t365_OrdSerStatus AS s ON l.id_status = s.id_status INNER JOIN  t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN  t365_Personal AS p ON n.id_autor = p.idPersonal WHERE (n.id_destino = '".$args[1]."') ".$args[2]."  ";
				$sql_statement.= " ) as dat  where  ".$args[3]." ";
			break;

			case "site_sel_GetNotifIniUserRelCount":
				$sql_statement = "SELECT  ".$args[0]."  n.id_notificacion FROM t365_OrdServNotifi AS n INNER JOIN t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN t365_OrdSerAccionesLog AS a ON n.accion = a.id_accion INNER JOIN  t365_OrdSerStatus AS s ON l.id_status = s.id_status INNER JOIN  t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN  t365_Personal AS p ON n.id_autor = p.idPersonal INNER JOIN  t365_OrdServ_UserRelacionado AS ur ON l.id_log = ur.id_log WHERE (n.id_destino = '".$args[1]."')  and (ur.id_personal = '".$args[1]."') ".$args[2]." order by n.fecha desc ";
			break;

			case "site_sel_GetNotifIniUserRel":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT  ".$args[0]."  n.id_notificacion, n.id_destino, n.id_autor, n.id_objetivo, n.accion, n.vista, n.fecha, a.descripcion, a.iconWeb, s.colorbg, o.correlativo, p.nombre, p.imagen, l.fecha AS dateLog, DATEDIFF(minute, l.fecha, GETDATE()) AS diif, o.id_orden, o.id_status, o.tipo_cliente,o.tipo_orden,CASE WHEN  o.tipo_cliente = 1 THEN  (SELECT CONCAT(c.prefijo,'-',c.cuenta,' ',c.nombre_cliente)   FROM t365_Clientes c  WHERE c.id_cliente = o.id_cliente) ELSE  (SELECT nombre_cliente  FROM t365_ClientesServicios cs  WHERE cs.id_cliente = o.id_cliente) END AS cliente ,l.id_status as id_statusL,s.descripcion as status, l.descripcion as coment,ROW_NUMBER() OVER(order by n.fecha desc) AS RowID FROM t365_OrdServNotifi AS n INNER JOIN t365_OrdSerLog AS l ON n.id_objetivo = l.id_log INNER JOIN t365_OrdSerAccionesLog AS a ON n.accion = a.id_accion INNER JOIN  t365_OrdSerStatus AS s ON l.id_status = s.id_status INNER JOIN  t365_OrdenServicio AS o ON l.id_orden = o.id_orden INNER JOIN  t365_Personal AS p ON n.id_autor = p.idPersonal INNER JOIN  t365_OrdServ_UserRelacionado AS ur ON l.id_log = ur.id_log WHERE (n.id_destino = '".$args[1]."')  and (ur.id_personal = '".$args[1]."') ".$args[2]."  ";
				$sql_statement.= " ) as dat  where ".$args[3]." ";
			break;

			case "site_sel_OrdenServicioVisitaDatos":
				$sql_statement = "SELECT o.*, c.nombre_cliente, c.rif, c.telf_local, c.telf_movil, c.email, c.direccion, t.nombre AS tecnico,   (SELECT per.nombre FROM t365_Personal per  WHERE (per.idPersonal = o.id_usuario))  AS creador FROM t365_OrdenServicio o INNER JOIN ".$args[0]." c ON o.id_cliente = c.id_cliente  LEFT OUTER JOIN t365_Personal t ON o.id_tecnico = t.idPersonal  WHERE (o.id_empresa = '".$args[1]."') AND (o.id_orden = '".$args[2]."')";
			break;

			case "site_sel_OrdenesServicioVisitaAyudantesTecnicos":
				$sql_statement = "SELECT o.*, p.nombre FROM t365_OrdSerTecRelacionados o INNER JOIN  t365_Personal p ON o.id_tecnico = p.idPersonal WHERE (o.id_orden = '".$args."') ORDER BY o.id";
			break;

			case "site_sel_OrdenesServicioVisitaTipServ":
				$sql_statement = "SELECT t.*, s.descripcion AS descripS  FROM t365_OrdSerTipoServicios t INNER JOIN  t365_EquiposTipos s ON t.id_tipo_servicio = s.id_tipo_equipo WHERE (t.id_orden = '".$args."') ORDER BY t.id DESC";
			break;

			case "site_sel_OrdSerStatus":
				$sql_statement = " SELECT * FROM t365_OrdSerStatus where 1=1 ".$args[0]." order by orden asc";
			break;

			case "site_sel_GetPartDatosOrdSer":
				$sql_statement = "SELECT fechaAtencion, pre_observacion,id_tipo_orden FROM t365_OrdenServicio WHERE (id_orden = '".$args."')";
			break;

			case "site_sel_SearchIniOrdServSinAsignarCount":
				$sql_statement = "	SELECT 1 AS tipoc,o.correlativo, o.id_orden, c.id_cliente, c.nombre_cliente, o.fechaCreada, o.problema FROM t365_OrdenServicio o INNER JOIN t365_Clientes c ON o.id_cliente = c.id_cliente WHERE  (o.id_status = 1) AND (o.id_empresa = '".$args[0]."') AND (o.tipo_cliente = 1)".$args[1]."   ";
			break;

			case "site_sel_SearchIniOrdServSinAsignar":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "	SELECT 1 AS tipoc,o.correlativo, o.id_orden, c.id_cliente,c.prefijo,c.cuenta, c.nombre_cliente, o.fechaCreada, o.problema,ROW_NUMBER() OVER(order by o.correlativo desc) AS RowID FROM t365_OrdenServicio o INNER JOIN t365_Clientes c ON o.id_cliente = c.id_cliente WHERE  (o.id_status = 1) AND (o.id_empresa = '".$args[0]."') AND (o.tipo_cliente = 1) ".$args[1]."   ";
				$sql_statement.= " ) as dat  where ".$args[2]." ";
			break;

			case "site_sel_SearchIniOrdServAsigTecnicosCount":
				$sql_statement = "SELECT 1 AS tipoc,o.correlativo, o.id_orden, c.id_cliente, c.nombre_cliente, o.fechaCreada, o.problema,  (SELECT nombre FROM t365_Personal p WHERE (idPersonal = o.id_tecnico)) AS tecnico FROM t365_OrdenServicio o INNER JOIN  t365_Clientes c ON o.id_cliente = c.id_cliente WHERE  (o.id_status = 2) AND (o.id_empresa = '".$args[0]."') AND (o.tipo_cliente = 1) ".$args[1]." ";
			break;

			case "site_sel_SearchIniOrdServAsigTecnicos":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT 1 AS tipoc,o.correlativo, o.id_orden, c.id_cliente,c.prefijo,c.cuenta, c.nombre_cliente, o.fechaCreada, o.problema,  (SELECT nombre FROM t365_Personal p WHERE (idPersonal = o.id_tecnico)) AS tecnico,ROW_NUMBER() OVER(order by o.correlativo desc) AS RowID  FROM t365_OrdenServicio o INNER JOIN  t365_Clientes c ON o.id_cliente = c.id_cliente WHERE  (o.id_status = 2) AND (o.id_empresa = '".$args[0]."') AND (o.tipo_cliente = 1) ".$args[1]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ";
			break;

			case "site_sel_SearchIniOrdServSeguimientoCount":
				$sql_statement = "	SELECT id_cliente,o.correlativo,  tipo_cliente, problema, id_orden FROM t365_OrdenServicio o WHERE (id_status = 3) AND (id_empresa = '".$args[0]."')   ";
			break;

			case "site_sel_SearchIniOrdServSeguimiento":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "	SELECT id_cliente,o.correlativo,  tipo_cliente, problema, id_orden,(SELECT TOP 1 l.descripcion FROM t365_OrdSerLog l  WHERE      (l.id_orden = o.id_orden) ORDER BY fecha DESC) AS obs,(SELECT CONCAT(c.prefijo,'-',c.cuenta,' ',c.nombre_cliente)  FROM t365_Clientes c  WHERE c.id_cliente = o.id_cliente) AS cliente,ROW_NUMBER() OVER(order by o.correlativo desc) AS RowID FROM t365_OrdenServicio o WHERE (id_status = 3) AND (id_empresa = '".$args[0]."')  ".$args[1]."";
				$sql_statement.= " ) as dat  where ".$args[2]." ";
			break;


			case "site_sel_SearchIniOrdServFinalizadasCount":
				$sql_statement = "SELECT id_cliente,o.correlativo,  tipo_cliente, problema, id_orden, fechaCreada  FROM t365_OrdenServicio o WHERE (id_empresa = '".$args[0]."') and id_status=4 ";
			break;

			case "site_sel_SearchIniOrdServFinalizadas":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT id_cliente,o.correlativo,  tipo_cliente, problema, id_orden, fechaCreada, (SELECT TOP 1 l.fecha FROM t365_OrdSerLog l WHERE (l.id_orden = o.id_orden) ORDER BY fecha DESC) AS fechafinal, (SELECT CONCAT(c.prefijo,'-',c.cuenta,' ',c.nombre_cliente) FROM t365_Clientes c WHERE      c.id_cliente = o.id_cliente) AS cliente,ROW_NUMBER() OVER(order by o.correlativo desc) AS RowID FROM t365_OrdenServicio o WHERE (id_empresa = '".$args[0]."') and id_status=4  ";
				$sql_statement.= " ) as dat  where ".$args[2]." ";
			break;

			case "site_sel_OrdenModAdminFinalizadasCount":
				$sql_statement = " SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status  WHERE (o.id_status_admin=0) and  (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]."  ";
			break;

			case "site_sel_OrdenModAdminFinalizadas":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT o.id_orden,o.tipo_orden, o.id_empresa, o.id_cliente, o.tipo_cliente, o.id_tipo_orden, CONVERT(char(10), o.fechaCreada, 111) AS fechaCreada,  o.prioridad, o.fechaAtencion,   o.problema, o.pre_observacion,c.cuenta,c.prefijo, c.nombre_cliente, o.id_status, o.id_ord_garantia, s.descripcion, s.id_status AS st, o.id_tecnico,  s.descripcion AS estatus,   s.colorbg, s.color, o.correlativo,ROW_NUMBER() OVER(".$args[3].") AS RowID   FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status  WHERE (o.id_status_admin=0) and  (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]."  ";
				$sql_statement.= " ) as dat  where  ".$args[6]." ".$args[7]." ";
			break;

			case "site_sel_OrdenServicioVisitaObFInal":
				$sql_statement = "SELECT top 1  descripcion FROM t365_OrdSerLog   where id_orden='".$args[0]."' and  id_status=4 order by fecha desc";
			break;

			case "site_sel_FormasPago":
				$sql_statement =" SELECT * FROM t365_FormasPago ";
			break;

			case "site_sel_OrdenModAdminFacturadasCount":
				$sql_statement = "SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status_admin = s.id_status  WHERE  (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]." ";
			break;

			case "site_sel_OrdenModAdminFacturadas":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT o.id_orden,o.tipo_orden, o.id_empresa, o.id_cliente, o.tipo_cliente, o.id_tipo_orden, CONVERT(char(10), o.fechaCreada, 111) AS fechaCreada, o.prioridad,  o.problema,c.prefijo,c.cuenta, c.nombre_cliente, o.id_status, s.descripcion, s.id_status AS st, o.id_tecnico, s.descripcion AS estatus,   s.colorbg, s.color, o.correlativo,o.fecha_factura,o.codigo_factura,o.monto_factura,o.id_status_admin,ROW_NUMBER() OVER(".$args[3].") AS RowID  FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status_admin = s.id_status  WHERE  (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]." ";
				$sql_statement.= " ) as dat  where  ".$args[6]." ".$args[7]." ";
			break;

			case "site_sel_OrdenModAdminPagadasCount":
				$sql_statement = " SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status_admin = s.id_status LEFT OUTER JOIN t365_FormasPago f on o.id_forma_pago=f.idforma  WHERE  (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]."  ";
			break;

			case "site_sel_OrdenModAdminPagadas":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT o.id_orden,o.tipo_orden, o.id_empresa, o.id_cliente, o.tipo_cliente, o.id_tipo_orden, CONVERT(char(10), o.fechaCreada, 111) AS fechaCreada, o.prioridad,  o.problema,c.prefijo,c.cuenta, c.nombre_cliente, o.id_status, s.descripcion, s.id_status AS st, o.id_tecnico, s.descripcion AS estatus,   s.colorbg, s.color, o.correlativo, o.id_status_admin ,o.fecha_pago, o.id_forma_pago, o.identity_pago , f.descripcion as formaPago,ROW_NUMBER() OVER(".$args[3].") AS RowID  FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN t365_OrdSerStatus AS s ON o.id_status_admin = s.id_status LEFT OUTER JOIN t365_FormasPago f on o.id_forma_pago=f.idforma  WHERE  (o.tipo_cliente = 1) AND (o.tipo_orden=1) AND (o.id_empresa ='".$args[0]."' ) ".$args[1]." ";
				$sql_statement.= " ) as dat  where  ".$args[6]." ".$args[7]." ";
			break;

			case "site_sel_GetRecordatoriosUser":
				$sql_statement = "SELECT re.*, DATEDIFF(minute, GETDATE(), re.fecha_recordar)*-1 AS diff, CASE WHEN o.id_status_admin = 0 THEN o.id_status else o.id_status_admin end as id_status, o.tipo_cliente,o.tipo_orden,c.nombre_cliente,c.telf_local,c.telf_movil  FROM t365_OrdRecordatorios AS re INNER JOIN t365_OrdRecordRelacionados AS r ON re.id_recordatorio = r.id_recordatorio  INNER JOIN  t365_OrdenServicio AS o ON re.id_objetivo = o.id_orden  INNER JOIN  t365_Clientes c on o.id_cliente=c.id_cliente where o.tipo_cliente=1 ".$args[0]." ";
			break;


			case "site_sel_GetRecordatoriosUserRel":
				$sql_statement = " SELECT id_personal  FROM t365_OrdRecordRelacionados where id_recordatorio= '".$args[0]."'";
			break;

			case "site_sel_ReportOrdListClienteCount":
				$sql_statement = "SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE (o.id_empresa = '".$args[0]."') ".$args[1]."  ";
			break;

			case "site_sel_ReportOrdListCliente":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT o.*, p.nombre as tecnico,s.descripcion as estatus, s.colorbg, s.color ,(SELECT TOP 1 fecha  FROM  t365_OrdSerLog where id_status=4 and id_orden=o.id_orden and privado=0  order by fecha desc) as fecha_fin,(SELECT TOP 1 descripcion  FROM  t365_OrdSerLog where id_status=o.id_status and id_orden=o.id_orden and privado=0  order by fecha desc) as comnt_fin,ROW_NUMBER() OVER(order by o.fechaCreada desc) AS RowID FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE (o.id_empresa = '".$args[0]."') ".$args[1]."  ";
				$sql_statement.= " ) as dat where  ".$args[2]." ";
			break;

			case "site_sel_OrdListCuentaClienteCount":
				$sql_statement = "SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE 1=1 ".$args[0]."  ";
			break;

			case "site_sel_OrdListCuentaCliente":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT o.*, p.nombre as tecnico,s.descripcion as estatus, s.colorbg, s.color ,(SELECT TOP 1 fecha  FROM  t365_OrdSerLog where id_status=4 and id_orden=o.id_orden and privado=0  order by fecha desc) as fecha_fin,(SELECT TOP 1 descripcion  FROM  t365_OrdSerLog where id_status=o.id_status and id_orden=o.id_orden and privado=0  order by fecha desc) as comnt_fin,ROW_NUMBER() OVER(order by o.fechaCreada desc) AS RowID FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE 1=1 ".$args[0]."  ";
				$sql_statement.= " ) as dat where  ".$args[1]." ";
			break;

			case "site_sel_ReportOrdListTecnicoCount":
				$sql_statement = " SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE (o.id_empresa = '".$args[0]."') ".$args[1]." ";
			break;

			case "site_sel_ReportOrdListTecnico":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT o.*, p.nombre as tecnico,s.descripcion as estatus, s.colorbg, s.color ,(SELECT TOP 1 fecha  FROM  t365_OrdSerLog where id_status=4 and id_orden=o.id_orden and privado=0  order by fecha desc) as fecha_fin,(SELECT TOP 1 descripcion  FROM  t365_OrdSerLog where id_status=o.id_status and id_orden=o.id_orden and privado=0  order by fecha desc) as comnt_fin,(SELECT c.prefijo+'-'+c.cuenta+' '+c.nombre_cliente  FROM t365_Clientes c  WHERE c.id_cliente = o.id_cliente) AS cliente ,ROW_NUMBER() OVER(order by o.fechaCreada desc) AS RowID FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE (o.id_empresa = '".$args[0]."') ".$args[1]." ";
				$sql_statement.= " ) as dat where  ".$args[2]." ";
			break;

			case "site_sel_ReportOrdListTecnicoContador":
				$sql_statement = " SELECT count(*) as cont,o.id_status,s.descripcion,s.orden,s.colorbg FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal  WHERE (o.id_empresa = '".$args[0]."') ".$args[1]." group by o.id_status,o.id_status,s.descripcion,s.orden,s.colorbg order by s.orden asc";
			break;

			case "site_sel_ReportOrdListTecnicoExport":
				$sql_statement = "SELECT o.id_orden, o.correlativo, o.id_empresa, o.id_status, o.id_tecnico, o.id_cliente, o.tipo_cliente, o.problema,o.pre_observacion, o.contacto, o.telf_contacto, s.descripcion AS estatus, (SELECT c.prefijo+'-'+c.cuenta+' '+c.nombre_cliente FROM t365_Clientes c WHERE c.id_cliente=o.id_cliente) AS cliente, (SELECT c.direccion FROM t365_Clientes c WHERE c.id_cliente=o.id_cliente) AS direccion, (SELECT c.telf_local +' - '+ c.telf_movil FROM t365_Clientes c WHERE c.id_cliente=o.id_cliente) AS tel FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status=s.id_status WHERE  (o.id_empresa = '".$args[0]."') ".$args[1]."  ORDER BY o.fechaCreada DESC";
			break;

			case "site_sel_ReportOrdXTiposCount":
				$sql_statement = "SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerTipoServicios AS so ON o.id_orden = so.id_orden  INNER JOIN    t365_OrdSerStatus AS s ON  s.id_status = CASE WHEN o.id_status_admin=0 THEN o.id_status else o.id_status_admin end WHERE (o.id_empresa ='".$args[0]."' ) ".$args[1]." ";
			break;

			case "site_sel_ReportOrdXTipos":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.="SELECT o.*, s.id_status as st ,s.descripcion as estatus, s.colorbg, s.color,(SELECT c.prefijo+'-'+c.cuenta+' '+c.nombre_cliente  FROM t365_Clientes c  WHERE c.id_cliente = o.id_cliente) AS cliente ,(SELECT TOP 1 descripcion  FROM  t365_OrdSerLog where id_status=o.id_status and id_orden=o.id_orden   and privado=0  order by fecha desc) as comnt_fin,ROW_NUMBER() OVER(order by o.fechaCreada desc) AS RowID FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerTipoServicios AS so ON o.id_orden = so.id_orden  INNER JOIN    t365_OrdSerStatus AS s ON  s.id_status = CASE WHEN o.id_status_admin=0 THEN o.id_status else o.id_status_admin end WHERE (o.id_empresa ='".$args[0]."' ) ".$args[1]." ";
				$sql_statement.= " ) as dat where  ".$args[2]." ";
			break;

			case "site_sel_OrdenesServicioVisitaProductos":
				$sql_statement = " SELECT p.id_modelo, p.descripcion, i.* FROM t365_OrdSerItems AS i INNER JOIN t365_EquiposModelos AS p ON i.id_items = p.id_modelo where p.eliminado=0 ".$args[0]."";
			break;

			case "site_sel_ReportOrdListClienteFinalCount":
				$sql_statement = "SELECT o.id_orden FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE 1=1 ".$args[0]."  ";
			break;

			case "site_sel_ReportOrdListClienteFinal":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= "SELECT o.*, p.nombre as tecnico,s.descripcion as estatus, s.colorbg, s.color ,(SELECT TOP 1 fecha  FROM  t365_OrdSerLog where id_status=4 and id_orden=o.id_orden and privado=0  order by fecha desc) as fecha_fin,(SELECT TOP 1 descripcion  FROM  t365_OrdSerLog where id_status=o.id_status and id_orden=o.id_orden and privado=0  order by fecha desc) as comnt_fin,ROW_NUMBER() OVER(order by o.fechaCreada desc) AS RowID FROM t365_OrdenServicio AS o INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal WHERE 1=1 ".$args[0]."  ";
				$sql_statement.= " ) as dat where  ".$args[1]." ";
			break;

			case "site_sel_UsuariosMonitoreo":
				$sql_statement=" SELECT u.id_user, u.nombre, u.apellido, u.email, u.movil, u.clavevoz,t.descrip,u.imagen FROM   t365_Usuarios u left outer join t365_TypeUser t on u.id_type_user=t.id_type_user WHERE  (u.id_cliente = '".$args[0]."')  ".$args[1]."";
			break;

			case "site_sel_ZonasMonitoreo":
				$sql_statement ="SELECT  id,id_zona, descrip as zona,ubicacion as ubi  FROM  t365_ClienteZonas where id_cliente = '".$args[0]."' ".$args[1];
			break;

			case "site_sel_GetLogoEmpresaClient":
				$sql_statement = "SELECT e.logo FROM t365_Clientes AS c INNER JOIN t365_Empresas AS e ON c.id_empresa = e.id_empresa where id_cliente='".$args[0]."'";
            break;

            case "site_sel_GetCamarasZona":
				$sql_statement = "SELECT t365_ClientesCCTV_Channel.descripcion AS nombrecanal,  t365_ClientesCCTV_Channel.id_channel, t365_ClientesCCTV.descripcion AS nombredvr, 'RTSP' as TipoCam FROM t365_ClientesCCTV INNER JOIN t365_ClientesCCTV_Channel ON t365_ClientesCCTV.id_cctv = t365_ClientesCCTV_Channel.id_cctv WHERE (t365_ClientesCCTV.id_cliente = '".$args[0]."') AND (t365_ClientesCCTV.id_modo = 2) UNION ALL SELECT descripcion AS nombrecanal,id_cctv as id_channel,'Camara IP' as nombredvr,'IP' as TipoCam FROM t365_ClientesCCTV  WHERE t365_ClientesCCTV.id_tipo = 3 and (t365_ClientesCCTV.id_cliente = '".$args[0]."') ORDER BY nombredvr, TipoCam";
            break;

            case "site_sel_GetCamarasZonaAsig":
            	$sql_statement ="SELECT id_channel, tipo from t365_ClienteZonasCCTV where id_zona = '".$args[0]."'";
            break;

            case "site_sel_GetDataNotifiClientStatus":
            	$sql_statement ="SELECT e.nombre, e.email AS correoEmp, c.id_cliente, c.nombre_cliente, c.telf_movil, c.email, o.correlativo, o.id_status, s.descripcion, o.fechaAtencion, o.problema, e.web, o.fechaCreada FROM t365_Clientes AS c INNER JOIN t365_OrdenServicio AS o ON c.id_cliente = o.id_cliente INNER JOIN t365_Empresas AS e ON c.id_empresa = e.id_empresa INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status where o.id_orden='".$args[0]."'";
            break;

            case "site_sel_GetDataNotifiClientStatusTecnico":
            	$sql_statement ="SELECT e.nombre,e.email as correoEmp, c.id_cliente, c.nombre_cliente, c.telf_movil, c.email, o.correlativo, o.id_status,o.fechaAtencion, s.descripcion, o.problema, e.web, o.fechaCreada, p.nombre AS tecnico FROM t365_Clientes AS c INNER JOIN t365_OrdenServicio AS o ON c.id_cliente = o.id_cliente INNER JOIN t365_Empresas AS e ON c.id_empresa = e.id_empresa INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status LEFT OUTER JOIN t365_Personal AS p ON o.id_tecnico = p.idPersonal where o.id_orden='".$args[0]."'";
            break;

            case "site_sel_GetDataNotifiClientStatusComentario":
            	$sql_statement ="SELECT e.nombre, e.email AS correoEmp, c.id_cliente, c.nombre_cliente, c.telf_movil, c.email, o.correlativo, o.id_status, s.descripcion, o.fechaAtencion, o.problema, e.web, o.fechaCreada ,(SELECT  TOP 1 l.descripcion FROM t365_OrdSerLog l where l.id_orden=o.id_orden and l.id_status=o.id_status order by id_log asc) as comentario,(SELECT  TOP 1 l.fecha FROM t365_OrdSerLog l where l.id_orden=o.id_orden and l.id_status=o.id_status order by id_log asc) as fechaStatus FROM t365_Clientes AS c INNER JOIN t365_OrdenServicio AS o ON c.id_cliente = o.id_cliente INNER JOIN t365_Empresas AS e ON c.id_empresa = e.id_empresa INNER JOIN t365_OrdSerStatus AS s ON o.id_status = s.id_status where o.id_orden='".$args[0]."'";
            break;

            case "site_sel_GetDataNotifiTecnicoOrden":
            	$sql_statement = "SELECT o.correlativo, o.id_cliente, o.problema, o.id_tecnico, p.telefono as celTecnico, c.cuenta, c.prefijo, c.nombre_cliente, c.direccion, c.ciudad, c.referencia, c.latitud, c.longitud, p.correo, p.notif_sms_servicio, p.notifi_email_servicio FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente INNER JOIN  t365_Personal AS p ON o.id_tecnico = p.idPersonal  where o.id_orden='".$args[0]."'";
            break;

            case "site_sel_GetCamClienteMoni":
            	$sql_statement = "SELECT  t365_ClientesCCTV.id_cctv,t365_ClientesCCTV_Channel.descripcion AS nombrecam, t365_ClientesCCTV_Channel.channel, t365_ClientesCCTV.puerto, t365_ClientesCCTV.usuario, t365_ClientesCCTV.clave, t365_ClientesCCTV.ip,  t365_ClientesCCTV.descripcion AS nombredvr, 'RTSP' AS tipo, t365_ClientesCCTV.id_modelo,t365_ClienteZonasCCTV.id_channel FROM t365_ClienteZonasCCTV INNER JOIN t365_ClientesCCTV_Channel ON t365_ClienteZonasCCTV.id_channel = t365_ClientesCCTV_Channel.id_channel INNER JOIN t365_ClientesCCTV ON t365_ClientesCCTV_Channel.id_cctv = t365_ClientesCCTV.id_cctv INNER JOIN t365_ClienteZonas ON t365_ClienteZonasCCTV.id_zona = t365_ClienteZonas.id WHERE        (t365_ClienteZonasCCTV.tipo = 'RTSP') AND (t365_ClienteZonas.id_zona = '".$args[0]."') and t365_ClienteZonas.id_cliente = '".$args[1]."' UNION ALL SELECT  t365_ClientesCCTV.id_cctv,t365_ClientesCCTV.descripcion AS nombrecam, '0' AS channel, t365_ClientesCCTV.puerto, t365_ClientesCCTV.usuario, t365_ClientesCCTV.clave, t365_ClientesCCTV.ip, 'Camara IP' AS nombredvr, 'IP' AS tipo, '0' AS id_modelo,t365_ClienteZonasCCTV.id_channel FROM t365_ClientesCCTV INNER JOIN  t365_ClienteZonasCCTV ON t365_ClientesCCTV.id_cctv = t365_ClienteZonasCCTV.id_channel INNER JOIN  t365_ClienteZonas ON t365_ClienteZonasCCTV.id_zona = t365_ClienteZonas.id WHERE (t365_ClienteZonasCCTV.tipo = 'IP') AND (t365_ClienteZonas.id_zona = '".$args[0]."') and t365_ClienteZonas.id_cliente = '".$args[1]."'";
            break;

            case "site_sel_VerifiarUsuariCliente":
            	$sql_statement = "SELECT id_user FROM t365_Usuarios where id_user='".$args['id_user']."' AND id_cliente='".$args['id_cliente']."' ".$args['aux']." ";
            break;

            case "site_sel_GetCountCamarasRSTP":
            	$sql_statement = "SELECT  (SELECT count(*) from t365_ClientesCCTV_Channel ch where ch.id_cctv=id_cctv)FROM  t365_ClientesCCTV  where id_cliente='".$args[0]."' and id_modo=2";
            break;

            case "site_sel_GetCamaraPanel":
            	$sql_statement = "SELECT  c.id_cctv,c.descripcion  FROM t365_ClientesCCTV  c left outer join t365_EquiposSubTipos t on c.id_tipo = t.id_subtipo left outer join t365_CCTVModoRegistro m on c.id_modo=m.id_modo WHERE (c.id_cliente = '".$args[0]."') and c.id_modo=2 ".$args[1]." ";
            break;

            case "site_sel_GetDispTiposListCount":
            	$sql_statement= " SELECT id_tipo_equipo  FROM t365_EquiposTipos where eliminado=0  ".$args[0]." ";
            break;

            case "site_sel_GetDispTipos":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_EquiposTipos where eliminado=0  ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetDispSubTiposListCount":
            	$sql_statement= " SELECT id_subtipo  FROM t365_EquiposSubTipos s inner join t365_EquiposTipos t on s.id_tipo=t.id_tipo_equipo where s.eliminado=0  ".$args[0]." ";
            break;

            case "site_sel_GetDispSubTipos":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT s.*,t.descripcion as tipo,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_EquiposSubTipos s inner join t365_EquiposTipos t on s.id_tipo=t.id_tipo_equipo where s.eliminado=0  ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[3]." ";
			break;

			case "site_sel_GetDispMarcasListCount":
            	$sql_statement= " SELECT id_marca  FROM t365_EquiposMarcas where eliminado=0  ".$args[0]." ";
            break;

            case "site_sel_GetDispMarcas":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT *,ROW_NUMBER() OVER(".$args[1].") AS RowID  FROM t365_EquiposMarcas where eliminado=0  ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[1]." ";
			break;

			case "site_sel_GetDispModeloListCount":
            	$sql_statement= "SELECT mo.id_modelo FROM t365_EquiposModelos AS mo INNER JOIN  t365_EquiposSubTipos_Marcas AS t ON mo.id_subtipo_marca = t.id_subtipo_marca INNER JOIN t365_EquiposSubTipos AS st ON t.id_subtipo = st.id_subtipo INNER JOIN t365_EquiposMarcas AS ma ON t.id_marca = ma.id_marca WHERE (mo.eliminado = 0)  ".$args[0]." ";
            break;

            case "site_sel_GetDispModelos":
				$sql_statement = " SELECT dat.* FROM ( ";
				$sql_statement.= " SELECT mo.id_modelo, mo.id_subtipo_marca, mo.descripcion, mo.string_acceso, mo.id_manual_help, mo.id_manual_user, mo.id_manual_prog, mo.id_manual_insta, st.id_tipo, t.id_subtipo, t.id_marca, st.descripcion AS sub_tipo,  ma.descripcion AS marca,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_EquiposModelos AS mo INNER JOIN t365_EquiposSubTipos_Marcas AS t ON mo.id_subtipo_marca = t.id_subtipo_marca INNER JOIN t365_EquiposSubTipos AS st ON t.id_subtipo = st.id_subtipo INNER JOIN t365_EquiposMarcas AS ma ON t.id_marca = ma.id_marca WHERE (mo.eliminado = 0)  ".$args[0]." ";
				$sql_statement.= " ) as dat  where ".$args[2]." ".$args[3]." ";
			break;

			case "site_sel_GetDispMarcasALL":
				$sql_statement = "SELECT * FROM t365_EquiposMarcas where eliminado=0 order by descripcion asc";
			break;

			case "site_sel_GetDispManuales":
				$sql_statement = "SELECT * FROM t365_EquiposManuales where 1=1  ".$args[0]." order by descripcion asc";
			break;

			case "site_sel_GetVariablesCCTV":
				$sql_statement = "SELECT * FROM t365_VariablesCCTV order by id_variable asc";
			break;

			case "site_sel_GetSubTipoMarca":
				$sql_statement = "SELECT top 1 * FROM t365_EquiposSubTipos_Marcas where id_subtipo='".$args[0]."' and id_marca='".$args[1]."' ";
			break;

			case "site_sel_GetDispManualListCount":
            	$sql_statement= "  SELECT t365_EquiposManuales.id_manual FROM t365_EquiposManuales INNER JOIN
                         t365_TypeManual ON t365_EquiposManuales.id_tipo_manual = t365_TypeManual.id_tipo_manual where 1=1 ".$args[0]." ";
            break;

            case "site_sel_GetDispManualList":
            	$sql_statement = " SELECT dat.* FROM ( ";
            	$sql_statement.= "   SELECT t365_EquiposManuales.*, t365_TypeManual.descripcion AS tipo,ROW_NUMBER() OVER(".$args[1].") AS RowID FROM t365_EquiposManuales INNER JOIN t365_TypeManual ON t365_EquiposManuales.id_tipo_manual = t365_TypeManual.id_tipo_manual  where 1=1 ".$args[0]." ";
            	$sql_statement.= " ) as dat  where ".$args[2]." ".$args[3]." ";
            break;

            case "site_sel_GetTipoManual":
				$sql_statement = "SELECT  * FROM t365_TypeManual ";
			break;

			 case "site_sel_GetImgZonaClientMoni":
				$sql_statement = "SELECT z.id, i.imagen FROM t365_ClienteZonas AS z INNER JOIN                         t365_ClienteZonasImagen AS i ON z.id = i.id_zona where z.id_zona='".$args[0]."' and z.id_cliente='".$args[1]."' ORDER BY imagen";
			break;

			case "site_sel_GetInstaladorCliente":
				$sql_statement = "SELECT t365_Personal.idPersonal, t365_Personal.nombre FROM t365_Personal INNER JOIN t365_TiposUsuarios ON t365_Personal.idTipoUsuario = t365_TiposUsuarios.idtipoUsuario INNER JOIN t365_UsuariosPerfil ON t365_TiposUsuarios.id_perfilUsuario = t365_UsuariosPerfil.id_perfil WHERE t365_UsuariosPerfil.id_perfil = 7 and t365_Personal.id_empresa = '".$args[0]."' order by t365_Personal.nombre asc";
			break;

			case "site_sel_PrintOrdenService":
				$sql_statement = "SELECT        o.*,c.cuenta, c.prefijo, c.nombre_cliente, c.ciudad, c.direccion, c.referencia, c.telf_local, c.telf_movil, (select p.nombre from t365_Personal p where p.idPersonal=o.id_tecnico) as tecnico, ( SELECT        t365_EquiposTipos.descripcion + ', ' AS 'data()'  FROM           t365_OrdSerTipoServicios INNER JOIN t365_EquiposTipos ON t365_OrdSerTipoServicios.id_tipo_servicio = t365_EquiposTipos.id_tipo_equipo where t365_OrdSerTipoServicios.id_orden=o.id_orden FOR XML PATH('')) as serv FROM  t365_Clientes AS c INNER JOIN                      t365_OrdenServicio AS o ON c.id_cliente = o.id_cliente where o.id_orden='".$args[0]."'";
			break;

			case "site_sel_CalendarTecnico":
				$sql_statement = "SELECT o.*,c.nombre_cliente, c.cuenta, c.prefijo FROM t365_OrdenServicio AS o INNER JOIN
                         t365_Clientes AS c ON o.id_cliente = c.id_cliente WHERE (YEAR(o.fechaAtencion) <> '1900') ".$args[0]." ";
			break;

			case "site_sel_CalendarTecnicoSinAsignar":
				$sql_statement = "SELECT o.*,c.nombre_cliente, c.cuenta, c.prefijo,(select p.nombre from t365_Personal p where p.idPersonal=o.id_tecnico) as tecnico FROM t365_OrdenServicio AS o INNER JOIN t365_Clientes AS c ON o.id_cliente = c.id_cliente WHERE  year(o.fechaAtencion)=1900  ".$args[0]." ";
			break;

			default:
				echo "query invalido:".$strQuery;

		}

		if($op['query_text']==true){
			echo $sql_statement;
		}

		if($var_auto){
			$params = $args;
		}else{
			$params = array();
		}

		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
		$stmt = sqlsrv_query($db_link_,$sql_statement,$params,$options);

		if( $stmt === false && $this->debug) {
			echo "ERROR:".$sql_statement."<br>";
			die(print_r(sqlsrv_errors(),true));
		}

		return $stmt;
	}

	function InsDB($conn,$strQuery,$args,$op){

		$db_link_ = $conn;

		if(!$db_link_){
			die(print_r( sqlsrv_errors(), true));
		}

		switch ($strQuery){

			case "site_ins_LogUser":
				$sql_statement=" INSERT INTO t365_LogOperador (idOperador ,idAbonado ,tipoAbonado ,idTipoLog ,idStation , Extra ,Fecha ) VALUES('".$args[0]."' ,'".$args[1]."' ,'".$args[2]."' ,'".$args[3]."' ,'".$args[4]."' , '".$args[5]."' ,GETDATE()) ";
			break;

			case "site_ins_NotasClientesFija":
				$sql_statement="  INSERT INTO t365_NotasClientes (IdCliente, NotaFija)   VALUES  ('".$args[0]."','".$args[1]."') ";
			break;

			case "site_ins_ClienteCamaras":
				$sql_statement="INSERT INTO t365_ClientesCamaras (id_cliente, usuario, clave, ip, descrip,id_modelo,chanel) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','".$args[5]."','".$args[6]."')";
			break;

			case "site_ins_ClienteHorarios":
				$sql_statement=" INSERT INTO t365_HorariosOC (id_cliente,diaapertura,horaapertura,toleranciaapertura ,diacierre, horacierre,toleranciacierre,fecha) VALUES  ('".$args[0]."','".$args[1]."',Convert(TIME,'".$args[2]."'),'".$args[3]."','".$args[1]."',Convert(TIME,'".$args[4]."'),'".$args[5]."',GETDATE()) ";

			break;

			case "site_ins_NumEmergencia":
				$sql_statement=" INSERT INTO t365_NumEmergencia (numero, descript,observacion,id_cliente,prioridad)   VALUES(?,?,?,?,?)";

				$var_auto = true;
			break;

			case "site_ins_EventoUser":
				$sql_statement =" INSERT INTO t365_ClienteEventos (id_cliente, cod_evento, id_user, status, variante, type)   SELECT  '".$args[0]."', cod_evento, '".$args[1]."', 1, variante, '".$args[2]."'  FROM t365_EventosPlanes where id_plan = ".$args[3];

				$var_auto= true;
			break;

			case "site_ins_Usuarios":
				$sql_statement="INSERT INTO t365_Usuarios (id_user, id_cliente, cod_user, id_type_user, nombre , apellido, movil, email, FechaAniversario, status, send_mail, frecuencia_mail, bbpin,clavevoz,active_email,id_plan,id_plan_email)  VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','".$args[5]."','".$args[6]."','".$args[7]."',".$args[8].",'".$args[9]."','".$args[10]."','".$args[11]."','".$args[12]."','".$args[13]."','".$args[14]."',0,0)";
			break;

			case "site_ins_TypeUser":
				$sql_statement="INSERT INTO t365_TypeUser (descrip) VALUES ('".$args[0]."')";
			break;

			case "site_ins_TypeCliente":
				$sql_statement="INSERT INTO  t365_TypeCliente  (descrip,img,id_empresa,id_dispositivo) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."')";
			break;

			case "site_ins_MensajesCierre":
				$sql_statement="INSERT INTO t365_MensajesCierre (Mensaje) VALUES ('".$args[0]."')";
			break;

			case "site_ins_Eventos":
				$sql_statement="INSERT INTO  t365_Eventos (cod_event, id_protocolo, descript, mensaje, type_evento, monitorea, cod_alarm, web_color, prioridad,color,web_colorBg) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','".$args[5]."','".$args[6]."','".$args[7]."','".$args[8]."','0','".$args[9]."')";
			break;

			case "site_ins_DepartamentosEmpresa":
				$sql_statement="INSERT INTO t365_DepartamentosEmpresa (idEmpresa, nombre, correo, nombregerente, telefono) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."')";
			break;

			case "site_ins_SoporteMotivos":
				$sql_statement=" INSERT INTO  t365_SoporteMotivos ( idEmpresa, descripcion, idDepartCorreo) VALUES ('".$args[0]."','".$args[1]."','".$args[0]."')";
			break;

			case "site_ins_CodigoAlarmas":
				$sql_statement="INSERT INTO t365_CodigosAlarma (codigo, descript, prioridad, idGrupo, web_color,color, grupo,web_colorBg) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','0','','".$args[5]."')";
			break;

			case "site_ins_GrupoCodigoAlarmas":
				$sql_statement=" INSERT INTO  t365_GrupoCodigosAlarma (Descript) VALUES ('".$args[0]."')";
			break;

			case "site_ins_TiposdeVehiculos":
				$sql_statement="INSERT INTO t365_TiposVehiculos (descripcion,id_icon) VALUES ('".$args[0]."','".$args[1]."')";
			break;

			case "site_ins_MarcasVehiculos":
				$sql_statement="INSERT INTO t365_MarcasVehiculos (descripcion) VALUES ('".$args[0]."')";
			break;

			case "site_ins_TiposdeGPS":
				$sql_statement="INSERT INTO t365_TiposGPS (descripcion) VALUES ('".$args[0]."')";
			break;

			case "site_ins_ModelosVehiculos":
				$sql_statement=" INSERT INTO t365_ModelosVehiculos (descripcion,id_marca) VALUES ('".$args[0]."','".$args[1]."')";
			break;

			case "site_ins_ConfigPortII":
				$sql_statement=" INSERT INTO t365_ConfigPortII (PortID, Descrip, Config, type, Port, idReceptor, Heartbeat, Status, fechaCreator,prefijo,Server)  VALUES ('".$args["PortID"]."','".$args["Descrip"]."','".$args["Config"]."','".$args["type"]."','".$args["Port"]."','".$args["idReceptor"]."','".$args["Heartbeat"]."','".$args["Status"]."',GETDATE(),'".$args["prefijo"]."','".$args["server"]."')";
			break;


			case "site_ins_DiasFeriados":
				$sql_statement="  INSERT INTO t365_DiasFeriados (id_empresa, id_pais, id_estado, descripcion, fecha, tipo) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."',CAST('".$args[4]." 00:00:00' AS smalldatetime),'".$args[5]."')";
			break;

			case "site_ins_DatosEmpresa":
				$sql_statement=" INSERT INTO t365_Empresas (id_pais, nombre, direccion, telefonos, email, web, status, rif, ip, puerto, master,latitud, longuitud,timeAlertPen, timeHombreM, timeNotifiHombre, correosHombre, monitorea, webTheme, webThemeSoport, notif_sms_servicio, notifi_email_servicio)VALUES (1,'".$args["nombre"]."','".$args["direccion"]."','".$args["telefonos"]."','".$args["email"]."','".$args["web"]."','".$args["status"]."','".$args["rif"]."',0,0,0,'".$args["latitud"]."','".$args["longuitud"]."','".$args["timeAlertPen"]."','".$args["timeHombreM"]."','".$args["timeNotifiHombre"]."','".$args["correosHombre"]."','".$args["monitorea"]."','".$args["tema"]."','".$args["tema"]."','".$args["notif_sms_servicio"]."','".$args["notifi_email_servicio"]."')";
			break;

			case "site_ins_DatosEmpresa2":
				$sql_statement="INSERT INTO t365_Empresas (id_empresa, nombre, latitud, longuitud) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."')";
			break;

			case "site_ins_DatosEmpresaConfig":
				$sql_statement=" INSERT INTO t365_ConfigEmpresas (id_empresa, timeAlertPen, timeHombreM, timeNotifiHombre, correosHombre) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."')";
			break;

			case "site_ins_Asociados":
				$sql_statement="INSERT INTO t365_asociados (id_empresa, nombre,   direccion, telef_contacto, email, usuario, clave, status)  VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','".$args[5]."','".$args[6]."','".$args[7]."')";
			break;

			case "site_ins_asociados_abonados":
				$sql_statement="INSERT INTO t365_asociados_abonados  (id_asociado, id_empresa, id_cliente) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."')";
			break;

			case "site_ins_TiposUsuarios":
				$sql_statement="INSERT INTO t365_TiposUsuarios (idEmpresa, color, descripcion, eliminado,id_perfilUsuario) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."')";
			break;

			case "site_ins_PermisosTipoUserio":
				$sql_statement=" INSERT INTO t365_PermisosTipoUsuario SELECT p.idPagina, a.idAccion, ".$args[0]."    FROM t365_PaginasAdmin p INNER JOIN  t365_PaginasAcciones a ON p.idPagina = a.idPagina WHERE   (a.idAccion IN (".$args[1].")) ";
			break;

			case "site_ins_PermisosPersonal":
				$sql_statement="INSERT INTO t365_PermisosAdmin SELECT p.idPagina, a.idAccion,".$args[0]."  FROM t365_PaginasAdmin p INNER JOIN  t365_PaginasAcciones a ON p.idPagina = a.idPagina WHERE  (a.idAccion IN (".$args[1].")) ";
			break;

			case "site_ins_PersonalEmpresa":
				$sql_statement=" INSERT INTO t365_Personal (id_empresa, idTipoUsuario, cedula, nombre, telefono, correo, Dirreccion, Telf_Habitacion, usuario, clave, eliminado, estatus, id_perfil, webTheme, webThemeSoport, notifi_serv_tec, notif_sms_servicio, notifi_email_servicio) VALUES ('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','".$args[5]."','".$args[6]."','".$args[7]."','".$args[8]."','".$args[9]."' ,'".$args[10]."','".$args[11]."','".$args[12]."','blue','blue','".$args[13]."','".$args[14]."','".$args[15]."')";
			break;

			case "site_ins_PermisosUserOne":
				$sql_statement="INSERT INTO t365_PermisosAdmin SELECT idPagina, idAccion,".$args[0]." FROM t365_PermisosTipoUsuario where idTipoUsuario='".$args[1]."' ";
			break;

			case "site_ins_TramaProcesadasPendiente"://se saco quiere parte por cuando se hace por clientes o eventos
				$sql_statement="insert into t365_TramasProcesadas (id_trama,descrip, cliente, status, evento, protocolo, user_zone, fecha, Variante, Linea, observacion,IdOperador) SELECT id_trama,descrip, cliente,status, evento,  protocolo, user_zone, fecha, Variante, Linea, '".$args[0]."','".$args[3]."' FROM t365_Tramas WHERE   (".$args[1]." in (".$args[2]."))";

			break;

			case "site_ins_TramaProcesadas":
				$sql_statement="insert into t365_TramasProcesadas (id_trama,descrip, cliente, status, evento, protocolo, user_zone, fecha, Variante, Linea, observacion,IdOperador,EmpresaMonitorea) SELECT id_trama,descrip, cliente,status, evento,  protocolo, user_zone, fecha, Variante, Linea, '".$args[0]."','".$args[3]."','".$args[4]."' FROM t365_Tramas WHERE  1=1 and  (".$args[1]." in (".$args[2]."))";
			break;

			case "site_ins_ClienteDatos":
				$sql_statement=" INSERT INTO t365_Clientes (cuenta, prefijo, id_empresa, id_protocolo, nombre_cliente, ciudad, direccion, referencia, telf_local, telf_fax, telf_movil, email, web_site, id_type_cliente, id_status, login, clave, status, status_web, latitud, longitud, clavemaster, fechinicio, rif, id_estado, status_mail, status_monitoreo, tipocuenta, modelo, llave,id_instalador";

				if(trim($args['fechC'])!=""){
					$sql_statement.=",fecha_corte";
				}

				$sql_statement.=" )  VALUES ('".$args["cuenta"]."', '".$args["prefijo"]."', '".$args["id_empresa"]."', '".$args["id_protocolo"]."', '".$args["nombre_cliente"]."', '".$args["ciudad"]."', '".$args["direccion"]."', '".$args["referencia"]."', '".$args["telf_local"]."', '".$args["telf_fax"]."', '".$args["telf_movil"]."', '".$args["email"]."', '".$args["web_site"]."', '".$args["id_type_cliente"]."', '".$args["id_status"]."', '".$args["login"]."', '".$args["clave"]."', '".$args["status"]."', '".$args["status_web"]."', '".$args["latitud"]."', '".$args["longitud"]."', '".$args["clavemaster"]."', GETDATE(), '".$args["rif"]."', '".$args["id_estado"]."', '".$args["status_mail"]."', '".$args["status_monitoreo"]."', '".$args["tipocuenta"]."', '".$args["modelo"]."', '".$args["llave"]."', '".$args["id_instalador"]."' ".$args['fechC'].") ";
			break;

			case "site_ins_Zona":
				$sql_statement="INSERT INTO t365_ClienteZonas (id_zona, descrip,ubicacion, id_cliente,type) VALUES ('".$args['id']."','".$args['desc']."','".$args['ubi']."','".$args['client']."','".$args['type']."')";
			break;

			case "site_ins_ClientesZonasImages":
				$sql_statement="INSERT INTO t365_ClienteZonasImagen (id_zona,id_cliente, imagen) VALUES ('".$args['zona']."','".$args['cliente']."','".$args['imagen']."')";
			break;

			case "site_ins_EquipoComodate":
				$sql_statement=" INSERT INTO t365_EmpresaEquipos (tipo_entrega, id_empresa, id_cliente, id_tipo_equipo, id_modelo, id_frecuencia_pago, serial, fecha_entrega,extra1,extra2) VALUES ('".$args['tipo_entrega']."','".$args['id_empresa']."','".$args['id_cliente']."','".$args['id_tipo_equipo']."','".$args['id_modelo']."','".$args['id_frecuencia_pago']."','".$args['serial']."',CAST('".$args['fecha_entrega']." 12:00:00' AS smalldatetime),'".$args['extra1']."','".$args['extra2']."')";
			break;

			case "site_ins_EquipoPrestamo":
				$sql_statement=" INSERT INTO t365_EmpresaEquipos (tipo_entrega, id_empresa, id_cliente, id_tipo_equipo, id_modelo, id_frecuencia_pago, serial, fecha_entrega,fecha_devuelto,extra1,extra2) VALUES ('".$args['tipo_entrega']."','".$args['id_empresa']."','".$args['id_cliente']."','".$args['id_tipo_equipo']."','".$args['id_modelo']."','".$args['id_frecuencia_pago']."','".$args['serial']."',CAST('".$args['fecha_entrega']." 12:00:00' AS smalldatetime),CAST('".$args['fecha_devuelto']." 12:00:00' AS smalldatetime),'".$args['extra1']."','".$args['extra2']."')";
			break;

			case "site_ins_Vehiculos":
				$sql_statement=" INSERT INTO t365_Vehiculos (id_empresa, id_tipo_gps, id_tipo_vehiculos, id_marca, id_modelo,codigo_gps, alias, placa, sim, imei, color, anio, vel_maxima, status, nota,monitoreo) VALUES ('".$args['id_empresa']."','".$args['id_tipo_gps']."','".$args['id_tipo_vehiculos']."','".$args['id_marca']."','".$args['id_modelo']."','".$args['codigo_gps']."','".$args['alias']."','".$args['placa']."','".$args['sim']."','".$args['imei']."','".$args['color']."','".$args['anio']."','".$args['vel_maxima']."','".$args['status']."','".$args['nota']."','".$args['monitoreo']."')";

			break;

			case "site_ins_VehiculosImgen":
				$sql_statement="INSERT INTO t365_VehiculosImagen (codgps, imagen) VALUES ('".$args['codigogps']."','".$args['imagen']."')";

			break;

			case "site_ins_SmsMasivo":
				$sql_statement=" EXEC websp_InsertSmsSalida  '".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','".$args[5]."','".$args[6]."' ,'".$args[7]."','".$args[8]."'";
			break;

			case "site_ins_EmailMasivo":
				$sql_statement=" EXEC websp_InsertSmsSalida  '".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."','".$args[4]."','".$args[5]."','".$args[6]."'";
			break;

			case "site_ins_hearBeatOperadorSession":
				$sql_statement=" INSERT INTO t365_OperadorSession (IdSession,IdOperador,FechaPIN,Ip,StatusLogin,StatusMonitoreo".$args["campos"].") VALUES ('".$args["IdSession"]."','".$args["IdOperador"]."',getdate(),'".$args["ip"]."',1,'".$args["StatusMonitoreo"]."'".$args["values"].")";
			break;

			case "site_ins_TramaObservacion":
				$sql_statement="INSERT INTO t365_TramasObservaciones (idtrama, fecha,observacion,idoperador) VALUES ('".$args['trama']."',getdate(),'".$args['observacion']."','".$args['idoperador']."')";
			break;

			case "site_ins_CCTVCliente":
				$sql_statement="INSERT INTO t365_ClientesCCTV (id_cliente,id_modo,id_tipo,id_marca,id_modelo, descripcion,ip,puerto,usuario,clave) VALUES ('".$args['id_cliente']."','".$args['id_modo']."','".$args['id_tipo']."','".$args['id_marca']."','".$args['id_modelo']."','".$args['descripcion']."','".$args['ip']."','".$args['puerto']."','".$args['usuario']."','".$args['clave']."')";
			break;

			case "site_ins_CCTVClienteChannel":
				$sql_statement="INSERT INTO t365_ClientesCCTV_Channel (id_cctv,channel,descripcion) VALUES ('".$args['id_cctv']."','".$args['channel']."','".$args['descripcion']."')";
			break;

			case "site_ins_CCTVClienteChannel":
				$sql_statement="INSERT INTO t365_ClientesCCTV_Channel (id_cctv,channel,descripcion) VALUES ('".$args['id_cctv']."','".$args['channel']."','".$args['descripcion']."')";
			break;

			case "site_ins_EmpresasRangoClientes":
				$sql_statement="INSERT INTO t365_EmpresasRangoClientes (id_empresa,prefijo,IniRango,FinRango) VALUES ('".$args['id_empresa']."','".$args['prefijo']."','".$args['IniRango']."','".$args['FinRango']."')";
			break;

			case "site_ins_ReceptorLineas":
				$sql_statement="INSERT INTO t365_ConfigPort_Lineas (linea,PortID,descripcion,prefijo) VALUES ('".$args['linea']."','".$args['PortID']."','".$args['descripcion']."','".$args['prefijo']."')";
			break;

			case "site_ins_RondasClientes":
				$sql_statement="INSERT INTO t365_Rondas (id_Cliente,Nombre,Tolerancia,Hora_Inicio,Hora_Fin,id_Calendario,id_Tipo,id_padre,Intervalo,Hora_Fin_Total,Tomada,Borrar) VALUES ('".$args['id_Cliente']."','".$args['Nombre']."','".$args['Tolerancia']."',Convert(TIME,'".$args['Hora_Inicio']."'),Convert(TIME,'".$args['Hora_Fin']."'),'".$args['id_Calendario']."','".$args['id_Tipo']."','".$args['id_padre']."','".$args['Intervalo']."',Convert(TIME,'".$args['Hora_Fin_Total']."'),0,0)";
			break;

			case "site_ins_RondasPuntosClientes":
				$sql_statement=" INSERT INTO  t365_RondasPuntos (id_ronda,id_punto,orden)  ".$args['values']." ";
			break;

			case "site_ins_ClienteTag":
				$sql_statement=" INSERT INTO  t365_ClientesPuntos (id_cliente,id_punto)  VALUES ('".$args['id_cliente']."','".$args['id_punto']."') ";
			break;

			case "site_ins_ClienteZonaPanicPcTec":
				$sql_statement=" INSERT INTO  t365_ClienteZonasSOS (id_zona,evento,tecla1,tecla2,tecla3)  VALUES ('".$args['id_zona']."','".$args['evento']."','".$args['tecla1']."','".$args['tecla2']."','".$args['tecla3']."') ";
			break;

			case "site_ins_OrdenesServicio":
				$sql_statement = "INSERT INTO t365_OrdenServicio  (correlativo, tipo_orden, id_empresa, id_usuario, id_status, id_tecnico, id_cliente, tipo_cliente, id_tipo_orden, fechaCreada, prioridad, fechaAtencion, problema, pre_observacion, contacto, telf_contacto,  id_ord_garantia, id_equipo, estado_equipo) VALUES  ('".$args['correlativo']."', '".$args['tipo_orden']."', '".$args['id_empresa']."', '".$args['id_usuario']."', '".$args['id_status']."', '".$args['id_tecnico']."', '".$args['id_cliente']."', '".$args['tipo_cliente']."', '".$args['id_tipo_orden']."', getdate(), '".$args['prioridad']."', CAST('".$args['fechaAtencion']." 12:00:00' AS smalldatetime), '".$args['problema']."', '".$args['pre_observacion']."', '".$args['contacto']."', '".$args['telf_contacto']."',  '".$args['id_ord_garantia']."', '".$args['id_equipo']."', '".$args['estado_equipo']."') ";
			break;

			case "site_ins_OrdenesServicioVisitaTecnico":
				$sql_statement = "INSERT INTO t365_OrdSerTecRelacionados (id_orden, id_tecnico, fechaAsignacion) VALUES ('".$args['id']."' ,'".$args['tecnico']."',GETDATE())";
			break;

			case "site_ins_OrdenesServicioVisitaItems":
				$sql_statement = "INSERT INTO t365_OrdSerItems (id_orden, id_items,tipo_item, cantidad, fechaAgregado) VALUES   ('".$args['id_orden']."','".$args['id_items']."','".$args['tipo_item']."','".$args['cantidad']."',GETDATE())";
			break;

			case "site_ins_OrdenesServicioServicios":
				$sql_statement = "INSERT INTO t365_OrdSerTipoServicios (id_orden, id_tipo_servicio, fecha) VALUES ('".$args['id_orden']."','".$args['id_tipo_servicio']."',GETDATE())";
			break;

			case "site_ins_OrdenesServicioLogs":
				$sql_statement = "INSERT INTO t365_OrdSerLog (id_orden, id_status, id_accion, id_usuario, descripcion,privado,fecha) VALUES ('".$args['id_orden']."','".$args['id_status']."','".$args['id_accion']."','".$args['id_usuario']."','".$args['descripcion']."','".$args['privado']."',GETDATE())";
			break;

			case "site_ins_OrdenesServicioNotificacion":
				$sql_statement = "INSERT INTO t365_OrdServNotifi SELECT idPersonal,".$args[1].",".$args[2].",".$args[3].",".$args[4].",getdate() FROM t365_Personal where idPersonal in (".$args[0].") ";
			break;

			case "site_ins_OrdenesServicioLogUSerRel":
				$sql_statement = " INSERT INTO t365_OrdServ_UserRelacionado SELECT ".$args[0].",idPersonal FROM t365_Personal where idPersonal in (".$args[1].")";
			break;

			case "site_ins_OrdenesServicioRecordatorios":
				$sql_statement = " insert into t365_OrdRecordatorios (id_autor,id_objetivo,fecha_recordar,titulo,descripcion,fecha)values ('".$args[0]."','".$args[1]."',CAST('".$args[2]."' AS smalldatetime),'".$args[3]."','".$args[4]."',GETDATE()) ";
			break;

			case "site_ins_OrdenesServicioRecordatoriosUsrRel":
				$sql_statement = "INSERT INTO t365_OrdRecordRelacionados SELECT ".$args[0].",idPersonal FROM t365_Personal where idPersonal in (".$args[1].")";
			break;

			case "site_ins_ClienteZonaPanicPcDisproc":
				$sql_statement=" INSERT INTO  t365_SS365Disprog (id_cliente,zona,status)  VALUES ('".$args['id_cliente']."','".$args['zona']."',0) ";
			break;


			case "site_ins_ZonaCCTV":
				$sql_statement="INSERT INTO t365_ClienteZonasCCTV (id_zona,id_channel,tipo) VALUES ('".$args['id']."','".$args['cam']."','".$args['camsT']."')";
			break;

			case "site_ins_SMSSalida":
				$sql_statement="INSERT INTO t365_BsalidaSpeed (id_cliente,movil,sms,server,status) VALUES ('".$args['id_cliente']."','".$args['movil']."','".$args['sms']."','1','0')";
			break;

			case "site_ins_EMAILSalida":
				$sql_statement="INSERT INTO t365_BsalidaMail (id_cliente,email,asunto,mensaje) VALUES ('".$args['id_cliente']."','".$args['email']."','".$args['asunto']."','".$args['mensaje']."')";
			break;

			case "site_ins_EquiposTipos":
				$sql_statement="INSERT INTO t365_EquiposTipos (descripcion,eliminado) VALUES ('".$args[0]."','0')";
			break;

			case "site_ins_EquiposSubTipos":
				$sql_statement ="INSERT INTO t365_EquiposSubTipos (descripcion,id_tipo,eliminado,monitoreoequipo) VALUES ('".$args[0]."','".$args[1]."','0','0')";
			break;

			case "site_ins_EquiposMarca":
				$sql_statement="INSERT INTO t365_EquiposMarcas (descripcion,eliminado) VALUES ('".$args[0]."','0')";
			break;

			case "site_ins_DispSubTipoMarca":
				$sql_statement="INSERT INTO t365_EquiposSubTipos_Marcas (id_subtipo,id_marca) VALUES ('".$args[0]."','".$args[1]."')";
			break;

			case "site_ins_EquiposModelo":
				$sql_statement="INSERT INTO t365_EquiposModelos (id_subtipo_marca, descripcion, string_acceso, id_manual_help, id_manual_user, id_manual_prog, id_manual_insta,eliminado) VALUES ('".$args["id_subtipo_marca"]."','".$args["descripcion"]."','".$args["string_acceso"]."','".$args["id_manual_help"]."','".$args["id_manual_user"]."','".$args["id_manual_prog"]."','".$args["id_manual_insta"]."','0')";
			break;

			case "site_ins_EquiposManual":
				$sql_statement="INSERT INTO t365_EquiposManuales (descripcion,id_tipo_manual) VALUES ('".$args[0]."','".$args[1]."')";
			break;

			default:
				echo "query invalido:".$strQuery;

		}

		if($op['query_text']==true){
			echo $sql_statement;
		}

		if($var_auto){
			$params = $args;
		}else{
			$params = array();
		}

		if($op['type']!="stored"){
			$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
		}
		$stmt = sqlsrv_query($db_link_,$sql_statement,$params,$options);

		if( $stmt === false && $this->debug) {
			echo "ERROR:".$sql_statement."<br>";
			die(print_r(sqlsrv_errors(),true));
		}


		$dbres = sqlsrv_query($db_link_,'SELECT SCOPE_IDENTITY()');
		$dbrow = sqlsrv_fetch_array($dbres);
		$lastInsertId = $dbrow[0];

		return $lastInsertId;
	}

	function UpdDB($conn,$strQuery,$args,$op){

		$db_link_ = $conn;

		if(!$db_link_){
			die(print_r( sqlsrv_errors(), true));
		}

		switch ($strQuery){
			case "site_upd_ClienteNotaFija":
				$sql_statement =" UPDATE t365_NotasClientes SET NotaFija ='".$args[0]."' where IdCliente ='".$args[1]."' ";
			break;

			case "site_upd_ClienteNotaTemporal":
				$sql_statement ="UPDATE t365_NotasClientes SET NotaTemp ='".$args[0]."', FechaIni =".$args[1].", FechaFin =".$args[2]." where  IdCliente = '".$args[3]."'";

			break;

			case "site_upd_ClientePosicion":
				$sql_statement =" UPDATE t365_Clientes SET latitud =?, longitud =? WHERE (id_cliente = ?)";
				$var_auto= true;
			break;

			case "site_upd_ThemePersonal":
				$sql_statement =" UPDATE t365_Personal SET webTheme ='".$args[0]."'  WHERE (idPersonal = '".$args[1]."')";

			break;

			case "site_upd_ClientesCamaras":
				$sql_statement ="UPDATE t365_ClientesCamaras SET usuario =?, clave =?, ip =?, descrip =?, id_modelo =?, chanel=? where (id_cam=?) ";
				$var_auto= true;
			break;

			case "site_upd_ClienteHorario":
				$sql_statement =" UPDATE t365_HorariosOC SET diaapertura ='".$args[0]."', diacierre ='".$args[0]."', horaapertura =Convert(TIME,'".$args[1]."'), toleranciaapertura ='".$args[2]."', horacierre =Convert(TIME,'".$args[3]."'), toleranciacierre ='".$args[4]."' WHERE (id = '".$args[5]."') ";

			break;

			case "site_upd_NumEmergencia":
				$sql_statement =" UPDATE    t365_NumEmergencia SET descript =? ,numero =?, observacion=?, prioridad=? where  (id_numero=?) AND (id_cliente =?) ";

				$var_auto= true;
			break;

			case "site_upd_PlanUser":
				$sql_statement =" UPDATE t365_Usuarios SET ".$args[0]." ='".$args[1]."'  where (id_user='".$args[2]."') AND (id_cliente='".$args[3]."')" ;
				$var_auto= true;
			break;

			case "site_upd_img_user_cliente":
				$sql_statement =" UPDATE t365_Usuarios SET imagen = ?  where (id_user= ?) AND (id_cliente= ? )" ;
				$var_auto= true;
			break;

			case "site_upd_Usuarios":
				$sql_statement =" UPDATE t365_Usuarios SET id_type_user ='".$args[0]."', nombre ='".$args[1]."', apellido ='".$args[2]."', movil ='".$args[3]."', email ='".$args[4]."', FechaAniversario =".$args[5].", status ='".$args[6]."', send_mail ='".$args[7]."', frecuencia_mail ='".$args[8]."', bbpin ='".$args[9]."', clavevoz ='".$args[10]."' , active_email='".$args[11]."',id_user ='".$args[12]."',cod_user ='".$args[12]."' WHERE (id_user ='".$args[14]."') AND (id_cliente ='".$args[13]."') ";
				$var_auto= true;
			break;

			case "site_upd_LoginCliente":
				$sql_statement =" UPDATE t365_Clientes SET login = ?, clave = ? WHERE  (id_cliente = ?)";
				$var_auto= true;
			break;

			case "site_upd_StatusCliente":
				$sql_statement =" UPDATE t365_Clientes SET  status_web = ? WHERE (id_cliente = ?)";
				$var_auto= true;
			break;

			case "site_upd_TypeUser":
				$sql_statement ="UPDATE t365_TypeUser SET descrip ='".$args[1]."' WHERE (id_type_user = '".$args[0]."')";
			break;

			case "site_upd_TypeCliente":
				$sql_statement ="UPDATE  t365_TypeCliente SET descrip='".$args[1]."' , img='".$args[2]."', id_empresa='".$args[3]."', id_dispositivo='".$args[4]."' WHERE (id_type_empresa = '".$args[0]."')";
			break;

			case "site_upd_MensajesCierre":
				$sql_statement ="UPDATE  t365_MensajesCierre SET Mensaje ='".$args[1]."' WHERE (id = '".$args[0]."')";
			break;

			case "site_upd_Eventos":
				$sql_statement = "UPDATE  t365_Eventos SET cod_event = '".$args[0]."', id_protocolo = '".$args[1]."', descript = '".$args[2]."', mensaje = '".$args[3]."', type_evento = '".$args[4]."', monitorea = '".$args[5]."', cod_alarm = '".$args[6]."', web_color = '".$args[7]."', prioridad = '".$args[8]."' , web_colorBg='".$args[9]."' WHERE (cod_event = '".$args[10]."' and id_protocolo = '".$args[1]."')";
			break;

			case "site_upd_DepartamentosEmpresa":
				$sql_statement = "UPDATE t365_DepartamentosEmpresa SET nombre ='".$args[1]."', correo ='".$args[2]."', idEmpresa ='".$args[3]."', nombregerente ='".$args[4]."', telefono ='".$args[5]."' WHERE     (idDepartamento = '".$args[0]."')";
			break;

			case "site_upd_SoporteMotivos":
				$sql_statement = "UPDATE t365_SoporteMotivos SET descripcion ='".$args[1]."', idDepartCorreo ='".$args[2]."' WHERE     (id_motivo = '".$args[0]."')";
			break;

			case "site_upd_CodigosAlarmas":
				$sql_statement = "UPDATE t365_CodigosAlarma SET descript ='".$args[1]."', prioridad ='".$args[2]."', idGrupo ='".$args[3]."',web_color='".$args[4]."', web_colorBg='".$args[5]."' WHERE (codigo ='".$args[0]."') ";
			break;

			case "site_upd_GrupoCodigosAlarmas":
				$sql_statement = " UPDATE  t365_GrupoCodigosAlarma SET Descript ='".$args[0]."' WHERE (idGrupo  = '".$args[1]."')";
			break;

			case "site_upd_TipodeVehiculos":
				$sql_statement = "UPDATE t365_TiposVehiculos SET descripcion ='".$args[0]."', id_icon ='".$args[1]."'  where  id_tipo_vehiculo = '".$args[2]."'";
			break;

			case "site_upd_MarcaVehiculos":
				$sql_statement = " UPDATE t365_MarcasVehiculos SET descripcion ='".$args[0]."' WHERE     (id_marca = '".$args[1]."')";
			break;

			case "site_upd_TipodeGPS":
				$sql_statement = "  UPDATE t365_TiposGPS SET descripcion ='".$args[0]."' WHERE     (id_tipos_gps = '".$args[1]."')";
			break;

			case "site_upd_ModeloVehiculos":
				$sql_statement = " UPDATE t365_ModelosVehiculos SET descripcion ='".$args[0]."',id_marca = '".$args[1]."'  WHERE (id_modelo='".$args[2]."')";
			break;

			case "site_upd_ConfigPortII":
				$sql_statement = " UPDATE t365_ConfigPortII SET Descrip ='".$args["Descrip"]."', Config ='".$args["Config"]."', type ='".$args["type"]."', Port ='".$args["Port"]."', idReceptor ='".$args["idReceptor"]."', Heartbeat ='".$args["Heartbeat"]."', Status ='".$args["Status"]."', prefijo ='".$args["prefijo"]."', Server ='".$args["server"]."' WHERE (PortID = '".$args["PortID"]."')";
			break;

			case "site_upd_DiaFeriado":
				$sql_statement = " UPDATE t365_DiasFeriados SET id_pais ='".$args[0]."', id_estado = '".$args[1]."', descripcion = '".$args[2]."', tipo = '".$args[3]."' where id_feriado='".$args[4]."'";
			break;

			case "site_upd_DiaFechaFeriado":
				$sql_statement = " UPDATE t365_DiasFeriados SET fecha = CAST('".$args[0]." 00:00:00' AS smalldatetime) where   id_feriado='".$args[1]."' ";
			break;

			case "site_upd_DatosEmpresas":
				$sql_statement = " UPDATE t365_Empresas SET nombre ='".$args["nombre"]."', direccion ='".$args["direccion"]."', telefonos ='".$args["telefonos"]."', email ='".$args["email"]."', web ='".$args["web"]."',  rif ='".$args["rif"]."' , latitud ='".$args["latitud"]."', longuitud ='".$args["longuitud"]."',timeAlertPen ='".$args["timeAlertPen"]."', timeHombreM ='".$args["timeHombreM"]."', timeNotifiHombre ='".$args["timeNotifiHombre"]."', correosHombre ='".$args["correosHombre"]."',monitorea='".$args["monitorea"]."',webTheme='".$args["tema"]."',webThemeSoport='".$args["tema"]."',notif_sms_servicio='".$args["notif_sms_servicio"]."',notifi_email_servicio='".$args["notifi_email_servicio"]."' ".$args["extra"]." WHERE     (id_empresa = '".$args["id_empresa"]."')";
			break;

			case "site_upd_DatosEmpresas2":
				$sql_statement = "UPDATE t365_Empresas SET nombre ='".$args[0]."', latitud ='".$args[1]."', longuitud ='".$args[2]."' WHERE     (id_empresa = '".$args[3]."')";
			break;

			case "site_upd_DatosEmpresaConfig":
				$sql_statement = "UPDATE t365_ConfigEmpresas SET timeAlertPen ='".$args[0]."', timeHombreM ='".$args[1]."', timeNotifiHombre ='".$args[2]."', correosHombre ='".$args[3]."' WHERE     (id_empresa = '".$args[4]."')";
			break;

			case "site_upd_EstatusEMpresa":
				$sql_statement = "UPDATE t365_Empresas SET status ='".$args[1]."' WHERE     (id_empresa = '".$args[0]."')";
			break;

			case "site_upd_Asociados":
				$sql_statement = " UPDATE t365_asociados SET nombre = '".$args[0]."', direccion = '".$args[1]."',   telef_contacto = '".$args[2]."', email = '".$args[3]."', usuario = '".$args[4]."', clave = '".$args[5]."', status = '".$args[6]."'   where id_asociado = '".$args[7]."'";
			break;

			case "site_upd_StatusAsociados":
				$sql_statement = " UPDATE t365_asociados SET  status = '".$args[0]."' WHERE  (id_asociado = '".$args[1]."')";
			break;

			case "site_upd_TiposUsuarios":
				$sql_statement = " UPDATE t365_TiposUsuarios SET color ='".$args[0]."', descripcion ='".$args[1]."', id_perfilUsuario ='".$args[2]."' where idtipoUsuario='".$args[3]."'";
			break;

			case "site_upd_Estatus_PersonalEmpresa":
				$sql_statement = "UPDATE t365_Personal SET estatus ='".$args[0]."'  where (idPersonal='".$args[1]."')";
			break;

			case "site_upd_PersonalImagen":
				$sql_statement = " UPDATE t365_Personal SET imagen = '".$args[0]."' WHERE (idPersonal = '".$args[1]."') ";
			break;

			case "site_upd_Personal":
				$sql_statement = " UPDATE t365_Personal SET  id_empresa ='".$args[0]."',idTipoUsuario ='".$args[1]."', cedula ='".$args[2]."', nombre ='".$args[3]."', telefono ='".$args[4]."', correo ='".$args[5]."', Dirreccion ='".$args[6]."', Telf_Habitacion = '".$args[7]."' ".$args[8]." ,id_perfil='".$args[9]."' ,notifi_serv_tec='".$args[11]."',notif_sms_servicio='".$args[12]."',notifi_email_servicio='".$args[13]."' where idPersonal='".$args[10]."' ";
			break;

			case "site_upd_TramaStatus":
				$sql_statement = "UPDATE t365_TramasPorProcesar SET status =".$args[0]." where 1=1 and  (".$args[1]." in (".$args[2].")) ";
			break;

			case "site_upd_cliente_img":
				$sql_statement = "UPDATE t365_Clientes SET imagen ='".$args[0]."' WHERE (id_cliente ='".$args[1]."')";
			break;

			case "site_upd_ClienteDatos":
				$sql_statement = " UPDATE t365_Clientes SET  cuenta='".$args['cuenta']."',prefijo='".$args['prefijo']."',id_protocolo='".$args['id_protocolo']."',nombre_cliente='".$args['nombre_cliente']."',ciudad='".$args['ciudad']."',direccion='".$args['direccion']."',referencia='".$args['referencia']."',telf_local='".$args['telf_local']."',telf_fax='".$args['telf_fax']."',telf_movil='".$args['telf_movil']."',email='".$args['email']."',web_site='".$args['web_site']."',id_type_cliente='".$args['id_type_cliente']."',id_status='".$args['id_status']."',login='".$args['login']."',status='".$args['status']."',status_web='".$args['status_web']."',latitud='".$args['latitud']."',longitud='".$args['longitud']."',clavemaster='".$args['clavemaster']."',rif='".$args['rif']."',id_estado='".$args['id_estado']."',status_mail='".$args['status_mail']."',status_monitoreo='".$args['status_monitoreo']."',tipocuenta='".$args['tipocuenta']."',modelo='".$args['modelo']."',llave='".$args['llave']."',id_instalador='".$args['id_instalador']."' ".$args['fechC']." ".$args["clave"]." WHERE (id_cliente = '".$args['id_cliente']."') ";
			break;

			case "site_upd_Zonas":
				$sql_statement = "UPDATE t365_ClienteZonas SET descrip ='".$args['desc']."',ubicacion ='".$args['ubi']."',id_zona ='".$args['id_zona']."' WHERE  (id ='".$args['id']."') ";
			break;

			case "site_upd_EquipoEmpresaComodato":

				$sql_statement = "UPDATE t365_EmpresaEquipos SET id_cliente='".$args['cliente']."',id_tipo_equipo='".$args['tipo']."' , id_modelo ='".$args['modelo']."' , id_frecuencia_pago='".$args['frec']."' , serial ='".$args['serial']."' , fecha_entrega =CAST('".$args['fechaIni']." 12:00:00' AS smalldatetime), extra1 ='".$args['extra1']."' , extra2 ='".$args['extra2']."' where id_equipo ='".$args['id_equipo']."' ";

			break;

			case "site_upd_EquipoEmpresaPrestamo":

				$sql_statement = "UPDATE t365_EmpresaEquipos SET id_cliente='".$args['cliente']."',id_tipo_equipo='".$args['tipo']."' , id_modelo ='".$args['modelo']."' , fecha_devuelto=CAST('".$args['fecha_devuelto']." 12:00:00' AS smalldatetime) , serial ='".$args['serial']."' , fecha_entrega =CAST('".$args['fechaIni']." 12:00:00' AS smalldatetime), extra1 ='".$args['extra1']."' , extra2 ='".$args['extra2']."' where id_equipo ='".$args['id_equipo']."' ";
			break;

			case "site_upd_CambioClaveAsociado":
				$sql_statement = "UPDATE t365_asociados SET  clave = '".$args[0]."' where id_asociado  = '".$args[1]."' and  id_empresa = '".$args[2]."'";

			break;

			case "site_upd_Vehiculos":
				$sql_statement = "UPDATE t365_Vehiculos SET  id_empresa = '".$args["id_empresa"]."',id_tipo_gps = '".$args["id_tipo_gps"]."',id_tipo_vehiculos = '".$args["id_tipo_vehiculos"]."',id_marca = '".$args["id_marca"]."',id_modelo = '".$args["id_modelo"]."',alias = '".$args["alias"]."',placa = '".$args["placa"]."',sim = '".$args["sim"]."',imei = '".$args["imei"]."',color = '".$args["color"]."',anio = '".$args["anio"]."',vel_maxima = '".$args["vel_maxima"]."',status = '".$args["status"]."',nota = '".$args["nota"]."',monitoreo = '".$args["monitoreo"]."' where id_vehiculo  = '".$args["id_vehiculo"]."'  ";

			break;

			case "site_upd_hearBeatOperadorSession":
				$sql_statement = "UPDATE t365_OperadorSession SET  FechaPIN=getdate() , StatusLogin=1 , Ip='".$args["ip"]."', StatusMonitoreo ='".$args["StatusMonitoreo"]."' ".$args["values"]."  where IdOperador ='".$args["IdOperador"]."' ";

			break;

			case "site_upd_CCTVCliente":
				$sql_statement = "UPDATE t365_ClientesCCTV SET  id_modo = '".$args["id_modo"]."',id_tipo = '".$args["id_tipo"]."',id_marca = '".$args["id_marca"]."',id_modelo = '".$args["id_modelo"]."',descripcion = '".$args["descripcion"]."',ip = '".$args["ip"]."',puerto = '".$args["puerto"]."',usuario = '".$args["usuario"]."',clave = '".$args["clave"]."' where   id_cctv = '".$args["id_cctv"]."' ";

			break;

			case "site_upd_RondaCliente":
				$sql_statement = "UPDATE t365_Rondas SET  Nombre = '".$args["Nombre"]."',Intervalo = '".$args["Intervalo"]."',Tolerancia = '".$args["Tolerancia"]."',Hora_Inicio = Convert(TIME,'".$args["Hora_Inicio"]."'),Hora_Fin = Convert(TIME,'".$args["Hora_Fin"]."'),id_Calendario = '".$args["id_Calendario"]."',id_Tipo = '".$args["id_Tipo"]."',Hora_Fin_Total = Convert(TIME,'".$args["Hora_Fin_Total"]."'),Tomada=0 where  id_ronda='".$args["id_ronda"]."'";

			break;

			case "site_upd_setVistoNotificacion":
				$sql_statement = " UPDATE t365_OrdServNotifi SET vista = '".$args[0]."'  WHERE (id_notificacion = '".$args[1]."') ";
			break;

			case "site_upd_OrdenesServicioVisita":
				$sql_statement = "UPDATE t365_OrdenServicio SET id_tipo_orden ='".$args["id_tipo_orden"]."', prioridad ='".$args["prioridad"]."' ,fechaAtencion =CAST('".$args["fechaAtencion"]." 12:00:00' AS smalldatetime), problema ='".$args["problema"]."', pre_observacion ='".$args["pre_observacion"]."'  , contacto ='".$args["contacto"]."',telf_contacto ='".$args["telf_contacto"]."', id_tecnico ='".$args["id_tecnico"]."',  id_ord_garantia ='".$args["id_ord_garantia"]."' ".$args["extra"]." where id_orden='".$args["id_orden"]."' ";
			break;

			case "site_upd_ordenes_st":
				$sql_statement = " UPDATE t365_OrdenServicio set  ".$args[0]."   where (id_orden = '".$args[1]."')";
			break;

			case "site_upd_changeFechaRecordatorio":
				$sql_statement = "  UPDATE t365_OrdRecordatorios SET ".$args[0]." WHERE (id_recordatorio = '".$args[1]."') ";
			break;

			case "site_upd_EquiposTipos":
				$sql_statement = "  UPDATE t365_EquiposTipos SET descripcion = '".$args[0]."' WHERE (id_tipo_equipo = '".$args[1]."') ";
			break;

			case "site_upd_EquiposSubTipos":
				$sql_statement = "  UPDATE t365_EquiposSubTipos SET descripcion = '".$args[0]."' , id_tipo = '".$args[1]."' WHERE (id_subtipo = '".$args[2]."') ";
			break;

			case "site_upd_EquiposMarca":
				$sql_statement = "  UPDATE t365_EquiposMarcas SET descripcion = '".$args[0]."' WHERE (id_marca = '".$args[1]."') ";
			break;

			case "site_upd_EquiposModelo":
				$sql_statement = "  UPDATE t365_EquiposModelos SET id_subtipo_marca = '".$args["id_subtipo_marca"]."' ,descripcion = '".$args["descripcion"]."' ,string_acceso = '".$args["string_acceso"]."' ,id_manual_help = '".$args["id_manual_help"]."' ,id_manual_user = '".$args["id_manual_user"]."' ,id_manual_prog = '".$args["id_manual_prog"]."' ,id_manual_insta = '".$args["id_manual_insta"]."' WHERE (id_modelo = '".$args["id_modelo"]."') ";
			break;

			case "site_upd_SetEquiposManual":
				$sql_statement = "  UPDATE t365_EquiposManuales SET manual_file = '".$args[0]."' WHERE (id_manual = '".$args[1]."') ";
			break;

			case "site_upd_EquiposManual":
				$sql_statement = "  UPDATE t365_EquiposManuales SET descripcion = '".$args[0]."' , id_tipo_manual = '".$args[1]."' WHERE (id_manual = '".$args[2]."') ";
			break;

			case "site_upd_CalendarAsignarOrden":
				$sql_statement = "  UPDATE t365_OrdenServicio SET fechaAtencion =CAST('".$args["fechaAtencion"]."' AS smalldatetime) ,id_tecnico='".$args["id_tecnico"]."'  WHERE (id_orden = '".$args["id_orden"]."') ";
			break;

			default:
				echo "query invalido:".$strQuery;

		}

		if($op['query_text']==true){
			echo $sql_statement;
		}

		if($var_auto){
			$params = $args;
		}else{
			$params = array();
		}

		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
		$stmt = sqlsrv_query($db_link_,$sql_statement,$params,$options);

		if( $stmt === false && $this->debug) {
			echo "ERROR:".$sql_statement."<br>";
			die(print_r(sqlsrv_errors(),true));
		}

		return $stmt;
	}

	function DelDB($conn,$strQuery,$args,$op){

		$db_link_ = $conn;

		if(!$db_link_){
			die(print_r( sqlsrv_errors(), true));
		}

		switch ($strQuery){
			case "site_del_DelCamaras":
				$sql_statement = "DELETE FROM t365_ClientesCamaras WHERE ( id_cam = ?) AND (id_cliente = ?)";
				$var_auto=true;
			break;

			case "site_del_HorarioCliente":
				$sql_statement = "DELETE FROM t365_HorariosOC WHERE   (Id = ?) ";
				$var_auto=true;
			break;

			case "site_del_DelAsistencia":
				$sql_statement = " DELETE FROM t365_NumEmergencia WHERE ( id_numero = ?) AND (id_cliente = ?) ";
				$var_auto=true;
			break;

			case "site_del_DelClienteEventoTipo":
				$sql_statement = "DELETE FROM t365_ClienteEventos WHERE (id_cliente=?) and (id_user=?) and (type=?)";
				$var_auto=true;
			break;

			case "site_del_DelClienteEvento":
				$sql_statement="DELETE FROM t365_ClienteEventos where (id_cliente=?) AND (id_user=?) AND (type=?)";
				$var_auto = true;
			break;

			case "site_del_UserClient":
				$sql_statement="DELETE FROM t365_Usuarios WHERE   (id_user = ?) AND (id_cliente = ?)";
				$var_auto = true;
			break;

			case "site_del_TypeUser":
				$sql_statement="DELETE FROM  t365_TypeUser WHERE (id_type_user = ?)";
				$var_auto = true;
			break;

			case "site_del_TypeCliente":
				$sql_statement=" DELETE FROM t365_TypeCliente WHERE (id_type_empresa = ?) ";
				$var_auto = true;
			break;


			case "site_del_MensajesCierre":
				$sql_statement=" DELETE FROM t365_MensajesCierre WHERE (id = ?)";
				$var_auto = true;
			break;

			case "site_del_Eventos":
				$sql_statement=" DELETE FROM t365_Eventos WHERE (cod_event = ?) and (id_protocolo = ?)";
				$var_auto = true;
			break;

			case "site_del_DepartamentosEmpresa":
				$sql_statement="  DELETE FROM t365_DepartamentosEmpresa WHERE (idDepartamento = ?)";
				$var_auto = true;
			break;

			case "site_del_SoporteMotivos":
				$sql_statement=" DELETE FROM t365_SoporteMotivos WHERE (id_motivo = ?)";
				$var_auto = true;
			break;

			case "site_del_CodigosAlarma":
				$sql_statement=" DELETE FROM t365_CodigosAlarma  WHERE (codigo = ?)";
				$var_auto = true;
			break;

			case "site_del_GrupoCodigosAlarma":
				$sql_statement=" DELETE FROM t365_GrupoCodigosAlarma WHERE (idGrupo = ?)";
				$var_auto = true;
			break;

			case "site_del_TiposdeVehiculos":
				$sql_statement=" DELETE FROM t365_TiposVehiculos WHERE (id_tipo_vehiculo = ?) ";
				$var_auto = true;
			break;

			case "site_del_MarcaVehiculos":
				$sql_statement=" DELETE FROM t365_MarcasVehiculos WHERE ( id_marca = ?) ";
				$var_auto = true;
			break;

			case "site_del_TiposdeGPS":
				$sql_statement="  DELETE FROM t365_TiposGPS WHERE (id_tipos_gps = ?) ";
				$var_auto = true;
			break;

			case "site_del_ModeloVehiculos":
				$sql_statement=" DELETE FROM t365_ModelosVehiculos WHERE ( id_modelo = ?) ";
				$var_auto = true;
			break;


			case "site_del_ConfigPortII":
				$sql_statement="  DELETE FROM  t365_ConfigPortII WHERE (PortID = ?)";
				$var_auto = true;
			break;

			case "site_upd_DelDiaFeriado":
				$sql_statement=" DELETE FROM t365_DiasFeriados WHERE   (id_feriado = '".$args."') ";
			break;

			case "site_del_Empresas":
				$sql_statement=" DELETE FROM t365_Empresas WHERE (id_empresa = '".$args."')";
			break;

			case "site_del_Empresas2":
				$sql_statement="DELETE FROM t365_Empresas WHERE (id_empresa = '".$args."')";
			break;

			case "site_del_Empresas3":
				$sql_statement="DELETE FROM t365_ConfigEmpresas WHERE (id_empresa = '".$args."')";
			break;

			case "site_del_Asociados":
				$sql_statement="DELETE FROM t365_asociados WHERE (id_asociado = '".$args[0]."')  ";
			break;

			case "site_del_asociados_abonados":
				$sql_statement="DELETE FROM t365_asociados_abonados   WHERE (id_asociado = '".$args[0]."')  ";
			break;

			case "site_del_TipoUsuario":
				$sql_statement=" UPDATE t365_TiposUsuarios SET eliminado =1 WHERE (idtipoUsuario = '".$args[0]."')";
			break;

			case "site_del_PermisosTipoUser":
				$sql_statement=" DELETE FROM t365_PermisosTipoUsuario WHERE (idTipoUsuario = '".$args[0]."')";
			break;

			case "site_del_Personal":
				$sql_statement="UPDATE t365_Personal SET eliminado =1 where (idPersonal='".$args[0]."')";
			break;

			case "site_del_PermisosPersonal":
				$sql_statement="DELETE FROM t365_PermisosAdmin WHERE (idUsuario ='".$args[0]."')";
			break;


			case "site_del_ZonaImg":
				$sql_statement=" DELETE FROM  t365_ClienteZonasImagen WHERE (id_zona = '".$args['zona']."') and (id_cliente='".$args['client']."') and (imagen='".$args['img']."')";
			break;

			case "site_del_DelZone":
				$sql_statement=" DELETE FROM t365_ClienteZonas WHERE (id = '".$args['id']."') ";
			break;

			case "site_del_DelZoneImg":
				$sql_statement=" DELETE FROM  t365_ClienteZonasImagen WHERE (id_zona = '".$args['id']."')";
			break;

			case "site_upd_DelEquipoEmpresa":
				$sql_statement="DELETE FROM t365_EmpresaEquipos WHERE   (id_equipo = '".$args[0]."') ";
			break;

			case "site_del_Vehiculos":
				$sql_statement= "DELETE FROM t365_Vehiculos WHERE ( id_vehiculo = '".$args["id"]."') ";
			break;

			case "site_del_VehiculosImgGPS":
				$sql_statement= "DELETE FROM t365_VehiculosImagen WHERE ( codgps = '".$args["codigogps"]."') ";
			break;

			case "site_del_VehiculosImg":
				$sql_statement= " DELETE FROM  t365_VehiculosImagen WHERE (codgps = '".$args["codgps"]."') and (imagen='".$args["imagen"]."')";
			break;

			case "site_del_CCTVClienteChannel":
				$sql_statement= " DELETE FROM  t365_ClientesCCTV_Channel WHERE id_cctv='".$args["id_cctv"]."'";
			break;

			case "site_del_CCTVCliente":
				$sql_statement= " DELETE FROM  t365_ClientesCCTV WHERE id_cctv='".$args["id_cctv"]."'";
			break;

			case "site_del_EmpresasRangoClientes":
				$sql_statement= " DELETE FROM  t365_EmpresasRangoClientes WHERE id_empresa='".$args."'";
			break;

			case "site_del_RecepLineas":
				$sql_statement= " DELETE FROM  t365_ConfigPort_Lineas WHERE PortID='".$args."'";
			break;

			case "site_del_RondaHijasCliente":
				$sql_statement= " DELETE  FROM  t365_Rondas where 1=1 ".$args[0]." ";
			break;

			case "site_del_RondaClientePuntosActulizar":
				$sql_statement= " DELETE  FROM  t365_RondasPuntos where id_ronda in(SELECT id_ronda FROM t365_Rondas where id_ronda='".$args[0]."' or id_Padre='".$args[0]."')";
			break;

			case "site_del_RondaCliente":
				$sql_statement= "UPDATE t365_Rondas set Borrar=1 where id_ronda='".$args[0]."' and id_Cliente='".$args[1]."'";
			break;

			case "site_del_CuentaCLientes":
				$sql_statement = "exec websp_DeleteCuentaCliente ".$args[0]." ";
			break;

			case "site_del_Clientetag":
				$sql_statement = "DELETE FROM t365_ClientesPuntos where id_cliente='".$args."'";
			break;

			case "site_del_ClienteZonaPanic":
				$sql_statement = "DELETE FROM t365_ClienteZonasSOS where id_zona='".$args["id_zona"]."'";
			break;

			case "site_del_OrdenesServicioVisitaTecnico":
				$sql_statement = "DELETE FROM t365_OrdSerTecRelacionados WHERE (id_orden = '".$args."')";
			break;

			case "site_del_OrdenesServicioVisitaItems":
				$sql_statement = "DELETE FROM t365_OrdSerItems WHERE (id_orden = '".$args."')";
			break;

			case "site_del_OrdenesServicioVisitaServicios":
				$sql_statement = "DELETE FROM t365_OrdSerTipoServicios WHERE (id_orden = '".$args."')";
			break;

			case "site_del_DelZoneCCTV":
				$sql_statement = "DELETE t365_ClienteZonasCCTV WHERE (id_zona ='".$args["id"]."')";
			break;

			case "site_del_DelDispTipo":
				$sql_statement = "UPDATE t365_EquiposTipos set eliminado = 1 WHERE (id_tipo_equipo ='".$args[0]."')";
			break;

			case "site_del_DelDispSubTipo":
				$sql_statement = "UPDATE t365_EquiposSubTipos set eliminado = 1 WHERE (id_subtipo ='".$args[0]."') and monitoreoequipo=0";
			break;

			case "site_del_DelDispMarca":
				$sql_statement = "UPDATE t365_EquiposMarcas set eliminado = 1 WHERE (id_marca ='".$args[0]."')";
			break;

			case "site_del_DelDispModelo":
				$sql_statement = "UPDATE t365_EquiposModelos set eliminado = 1 WHERE (id_modelo ='".$args[0]."')";
			break;

			case "site_del_DelDispManual":
				$sql_statement = "DELETE FROM  t365_EquiposManuales WHERE (id_manual ='".$args[0]."')";
			break;

			default:
				echo "query invalido:".$strQuery;

		}

		if($op['query_text']==true){
			echo $sql_statement;
		}

		if($var_auto){
			$params = $args;
		}else{
			$params = array();
		}

		if($op['type']!="stored"){
			$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
		}
		$stmt = sqlsrv_query($db_link_,$sql_statement,$params,$options);

		if( $stmt === false && $this->debug) {
			echo "ERROR:".$sql_statement."<br>";
			die(print_r(sqlsrv_errors(),true));
		}

		return $stmt;
	}


	function count_row($record){
		return sqlsrv_num_rows($record);
	}

	function getdata($record){
		return sqlsrv_fetch_array($record,SQLSRV_FETCH_ASSOC);
	}

	function getdata_numeric($record){
		return sqlsrv_fetch_array($record,SQLSRV_FETCH_NUMERIC);
	}

	function getdata_object($record){
		return sqlsrv_fetch_object($record);
	}
}
?>