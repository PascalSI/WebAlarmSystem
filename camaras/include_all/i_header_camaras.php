<body class="page-header-fixed page-quick-sidebar-over-content page-sidebar-fixed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="">
				<img src="<?php echo $CONFIG['HOST']."img/logo_empresas/".$CONFIG['WEB_THEME_LOGO'];;?>" alt="logo" class="logo-default"/>
			</a>
		</div>
		<div class="hor-menu">
			<ul class="nav navbar-nav">
				<li>
					<a href="javascript:;" >
						<i class="fa fa  fa-table"></i>Camaras del Cliente: <?php echo $rN->prefijo.'-'.$rN->cuenta.' '.$rN->nombre_cliente;?>
					</a>
				</li>
			</ul>
		</div>
		<!-- BEGIN TOP NAVIGATION MENU -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->