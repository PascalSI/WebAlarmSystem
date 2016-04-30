
<?php
include("../include/scriptdb/config.php");

if($_SESSION["cliente"]["idEmpresa"]=="" || $_SESSION["cliente"]["tipoUser"]!=3){
	header("LOCATION: ../");
}

include("../include/phpscript/init.php");
include("../include/phpscript/generales.php");
include("../include/scriptdb/querys.php");

//incluye los metadatas
include("../include/diseno/i_header_metada.php");

?>
<title>Web Access Asociado</title>
<?php
//incluye los css principales del framework
include("../include/diseno/i_header_css_default.php");

//incluye el header principal
include("../include/diseno/i_headerAsociado.php");

//incluye el menu principal
include("../include/diseno/i_menuAsociado.php");

//include el content de la pagina
include("../include/diseno/i_contentAsociado.php");


//incluye el bottom de la pagina
include("../include/diseno/i_bottom.php");


//incluye los javascript principales del framework
include("../include/diseno/i_bottom_script_default.php");

?>

<script>
	$(document).ready(function() {
		$("#menu-cuentas").click();
	});
</script>