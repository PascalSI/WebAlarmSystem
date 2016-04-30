<?php
//configuraciones
include("../include/scriptdb/config.php");
include("../include/phpscript/init.php");
include("../include/phpscript/session.php");
include("../include/phpscript/generales.php");
include("../include/scriptdb/querys.php");

$query= new Querys();
$conex = $query->getConection($CONFIG);

//incluye los metadatas
include("include/diseno/i_header_metada.php");
?>
<title>Servicio Tecnico <?php echo $_SESSION["user"]["nombre_empresa"];?></title>

<?php

//incluye los css principales del framework
include("include/diseno/i_header_css_default.php");

//incluye el header principal
include("include/diseno/i_header.php");

//incluye el menu principal
include("include/diseno/i_menu.php");

//include el content de la pagina
include("include/diseno/i_content.php");

//incluye el bottom de la pagina
include("include/diseno/i_bottom.php");

//incluye los javascript principales del framework
include("include/diseno/i_bottom_script_default.php");
?>

<script>

	<?php if($MenuOrdList){?>
		menuAdmin({name:"Ordenes",url:"inicio",ancla:this});


	//actualiza las notificaciones
	Metronic.getNotifiCount();

	setInterval(function(){
		Metronic.getNotifiCount();
	}, 15000);

	<?php } ?>
	//fin
</script>

