<body class="page-header-fixed page-quick-sidebar-over-content page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="">
			<img src="<?php echo $CONFIG['HOST']."img/logo_empresas/".$CONFIG['WEB_THEME_LOGO'];?>" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right" id="nav-pull-right-head">
				<?php include("i_header_count.php");?>

				<!-- BEGIN USER LOGIN DROPDOWN -->
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"  data-close-others="true">
					<img alt="" class="img-circle hide1" src="<?php echo $CONFIG['HOST']."img/img_p/".$_SESSION["user"]["imagenUser"];?>" onerror="this.src='<?php echo $CONFIG['HOST']."img/avatar.png";?>'"/>
					<span class="username username-hide-on-mobile">
					<?php echo $_SESSION["user"]["nameOperador"];?>
					</span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu">
                	<!--<li>
						<a href="javascript:;" id="trigger_fullscreen">
							<i class="fa fa-arrows"></i> Pantalla Completa
						</a>
					</li>-->
					<li class="divider">
					</li>
					<li>
						<a href="javascript:void(0)" onClick="window.close()">
							<i class="fa fa-key"></i> Salir
						</a>
					</li>
				</ul>

				</li>
				<?php if(PermModAccUser(2,8)){?>
				<!-- END USER LOGIN DROPDOWN -->
				<li class="dropdown dropdown-quick-sidebar-toggler">
					<a class="dropdown-toggle" href="javascript:;">
						<i class="icon-logout"></i>
					</a>
				</li>
				<?php } ?>

			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<?php
	include($CONFIG['DIR_PROJECT']."include/diseno/sidebar/quick-sidebar.php");
?>
