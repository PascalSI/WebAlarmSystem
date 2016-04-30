<?php

$MenuClieMoni = verPermModUser(2);
$MenuInvCate = verPermModUser(12);
$MenuInvDep = verPermModUser(14);
$MenuInvProv = verPermModUser(13);
$MenuInvProdc = verPermModUser(11);
$MenuOrd = verPermModUser(9);
$MenuOrdAdmin = verPermModUser(35);
?>
<div class="clearfix"> </div>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
				<li class="sidebar-search-wrapper">
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
					<form class="sidebar-search" method="POST">
					</form>
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				</li>
				<li class="start ">
					<a  href="javascript:;" <?php if($MenuOrdList){?>onclick='menuAdmin({name:"Inicio",url:"inicio",ancla:this})' <?php } ?>>
						<i class="fa fa-home"></i>
						<span class="title">
							Inicio
						</span>
					</a>
				</li>
                <?php if($MenuOrd){?>
                <li>
                    <a href="javascript:;" >
                        <i class="fa fa-tags"></i>
                        <span class="title">
                            Ordenes
                        </span>
                        <span class="selected">
                        </span>
                        <span class="arrow">
                        </span>
                    </a>
                    <ul  class="sub-menu">
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Resumen de Ordenes",url:"ordenes_resumen",ancla:this})'>
                                <i class="fa fa-bars"></i>
                                <span class="title">
                                    Resumen
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;"  onclick='menuAdmin({name:"Visitas Tecnicas",url:"ordenes_visitas",ancla:this})'>
                                <i class="fa fa- glyphicon glyphicon-user"></i>
                                <span class="title">
                                    Visita Tecnica
                                </span>
                            </a>
                        </li>
                        <?php /*?><li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Recepci&oacute;n de Equipos",url:"ordenes_recepcion",ancla:this})'>
                                <i class="fa fa- glyphicon glyphicon-list-alt"></i>
                                <span class="title">
                                    Recepci&oacute;n de Equipos
                                </span>
                            </a>
                        </li>*/?>
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Administrativo Tecnico",url:"calendarioi_tecnico",ancla:this})'>
                                <i class="fa  fa-calendar"></i>
                                <span class="title">
                                    Calendario
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php } ?>
                <?php if($MenuOrdAdmin){?>
                 <li>
                    <a href="javascript:;" >
                        <i class="glyphicon glyphicon-tag"></i>
                        <span class="title">
                            Admin. Ordenes
                        </span>
                        <span class="selected">
                        </span>
                        <span class="arrow">
                        </span>
                    </a>
                    <ul  class="sub-menu">
                        <li>
                             <a href="javascript:;" onclick='menuAdmin({name:"Administrativo Ordenes",url:"administrativo_ordenes",ancla:this})'>
                                <i class="fa  fa-clipboard"></i>
                                <span class="title">
                                    Ordenes
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Administrativo Calendario",url:"administrativo_calendario",ancla:this})'>
                                <i class="fa  fa-calendar"></i>
                                 <?php if($headerGlobalRecordAdmin > 0){?>
                                    <span class="badge badge-important"> <?php echo setContNotifi($headerGlobalRecordAdmin)?> </span>
                                <?php } ?>
                                <span class="title">
                                    Calendario
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php } ?>
                <?php if($MenuClieMoni){?>
                <!--<li>
                    <a href="javascript:;">
                        <i class="fa fa-group"></i>
                        <span class="title">
                             Clientes
                        </span>
                        <span class="selected">
                        </span>
                        <span class="arrow">
                        </span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Clientes Monitoreo",url:"clientes_monitoreo",ancla:this})'>
                            	 <i class="fa fa- glyphicon glyphicon-user"></i>
                                 Clientes Monitoreo
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Clientes Servicios",url:"clientes_servicios",ancla:this})'>
                                  <i class="fa fa-user"></i>
                                  Clientes Servicio
                            </a>
                        </li>
                    </ul>
                </li>-->
                <?php } ?>
                <?php if(verPermModUser(16)){?>
                <li class="">
                    <a href="javascript:;">
                    <i class="glyphicon glyphicon-globe"></i>
                    <span class="title">Maps</span>
                    <span class="arrow "></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if(PermModAccUser(16,90)){?>
                        <li>
                            <a href="../maps/PosicionClientes/" target="_blank">
                            <i class="glyphicon glyphicon-user"></i>
                            Posición Cliente</a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
                <?php if(verPermModUser(15)){?>
                 <li>
                    <a href="javascript:;" onclick='menuAdmin({name:"Reportes",url:"reportes",ancla:this})'>
                        <i class="glyphicon glyphicon-list-alt"></i>
                        <span class="title">
                            Reportes
                        </span>
                    </a>
                </li>
                <?php } ?>
                <!--<li>
                    <a href="javascript:;">
                        <i class="fa fa-male"></i>
                        <span class="title">
                            Usuarios
                        </span>
                    </a>
                </li>-->
                <?php /*if ($MenuInvCate || $MenuInvDep || $MenuInvProv || $MenuInvProdc){ ?>
                <li>
					<a href="javascript:;">
						<i class="fa fa- glyphicon glyphicon-inbox"></i>
						<span class="title">
							Inventario
						</span>
                        <span class="selected">
                        </span>
                        <span class="arrow">
                        </span>
					</a>
                    <ul  class="sub-menu">
                    	<?php if($MenuInvProdc){?>
                    	<li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Productos",url:"productos",ancla:this})'>
                                <i class="fa fa-sitemap"></i>
                                <span class="title">
                                    Productos
                                </span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if($MenuInvProv){?>
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Proveedores",url:"proveedores",ancla:this})'>
                                <i class="fa fa- glyphicon glyphicon-briefcase"></i>
                                <span class="title">
                                    Proveedores
                                </span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if($MenuInvCate){?>
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Categorias",url:"categorias",ancla:this})'>
                                <i class="fa fa- glyphicon glyphicon-th-list"></i>
                                <span class="title">
                                    Categoria de Productos
                                </span>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if($MenuInvDep){?>
                        <li>
                            <a href="javascript:;" onclick='menuAdmin({name:"Depositos",url:"depositos",ancla:this})'>
                                <i class="fa fa- glyphicon glyphicon-th"></i>
                                <span class="title">
                                    Depositos
                                </span>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
				</li>
                <?php }*/ ?>
                <!--<li  class="last ">
					<a href="javascript:;">
						<i class="fa fa-cogs"></i>
						<span class="title">
							Configuraciones
						</span>
					</a>
				</li>-->
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->