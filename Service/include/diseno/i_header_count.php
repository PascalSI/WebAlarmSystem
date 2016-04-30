<?php
$MenuOrdList = PermModAccUser(9,56);

?>
<!-- BEGIN NOTIFICATION DROPDOWN -->
<?php
	$ContXAsig = 0;
	$ContAsig = 0;
	$ContSegui = 0;
	$ContNotifi = 0;
	$ContNotifiPrivad = 0;
	$ContNotifiNom = 0;

	//obtiene el total de los registros
	$MyRecordContORdsA = $query->SelDB($conex,"site_sel_ContOrdServicioos",array($_SESSION["user"]["idEmpresa"],$_SESSION["user"]["idOperador"]));

	if($query->count_row($MyRecordContORdsA)>0){

		$MyRecordContORds = $query->getdata_object($MyRecordContORdsA);

		$ContXAsig = $MyRecordContORds->sinasginar;
		$ContAsig = $MyRecordContORds->asignada;
		$ContSegui = $MyRecordContORds->seguimiento;
		$ContNotifi = $MyRecordContORds->notificaciones;
		$ContNotifiNom = $MyRecordContORds->notifi_nom;
		$ContNotifiPrivad = $MyRecordContORds->notifi_privad;
	}
?>
<?php
if($ContNotifiPrivad == 0){
 	$textNotiPrivHead="Sin Notificaci&oacute;nes ";
 	$displayNotiPrivHead="none";
 	$heigtNotiPrivHead="0";
 }else{
	$textNotiPrivHead="Notificaci&oacute;nes Privadas";
	$displayNotiPrivHead="block";
	$heigtNotiPrivHead="250";
 }
?>

<?php
if(PermModAccUser(9,171)){
?>
<!-- BEGIN privados DROPDOWN -->
<li class="dropdown dropdown-extended dropdown-inbox li_head_ch " id="header_notification_bar" title="Notificaci&oacute;nes Privadas">
	<a href="#" class="dropdown-toggle"  style="padding-bottom: 5px;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" >
		<i class="fa   fa-lock"></i>
		<span class="badge badge-default"  id="badge-XAsig-head" style="display:<?php echo $displayNotiPrivHead;?>"  >
			 <?php echo setContNotifi($ContNotifiPrivad);?>
		</span>
	</a>
	<ul class="dropdown-menu extended inbox">
		<li>
			<p id="text-notifi-head">
				<?php echo $textNotiPrivHead;?>
			</p>
		</li>
		<li>
			<ul class="dropdown-menu-list scroller" style="height: <?php echo $heigtNotiPrivHead;?>px;">
				<?php
					$paramNotiPrivHead = " AND (n.vista = 0) AND (l.privado = 1) ";

					$MyRecordNotifiPrivDetA = $query->SelDB($conex,"site_sel_GetNotifiLinkUserRel",array("top 10",$_SESSION["user"]["idOperador"],$paramNotiPrivHead));

					while($MyRecordNotifiPrivDet=$query->getdata_object($MyRecordNotifiPrivDetA)){
					?>
					<li>
						<a href="javascript:;" onclick='showDetailTime({tipoOR:"<?php echo $MyRecordNotifiPrivDet->tipoOR;?>",id:"<?php echo $MyRecordNotifiPrivDet->id_orden;?>",sts:"<?php echo $MyRecordNotifiPrivDet->id_status;?>",tipoc:"<?php echo $MyRecordNotifiPrivDet->tipo_cliente;?>",idLog:"<?php echo $MyRecordNotifiPrivDet->id_objetivo;?>",idN:"<?php echo $MyRecordNotifiPrivDet->id_notificacion;?>",tipoLine:1})'>
							<span class="photo">
								<?php
									$urlAvatarDet =  $CONFIG['HOST']."img/avatar.jpg";
									if($MyRecordNotifiPrivDet->imagen != ""){
										$urlAvatarDet = $CONFIG['HOST']."img/img_p/".$MyRecordNotifiPrivDet->imagen;
									}
								?>
								<img src="<?php echo $urlAvatarDet;?>" alt=""/>
							</span>
							<span class="subject">
								<span class="from">
									 <?php echo $MyRecordNotifiPrivDet->nombre;?>
								</span>
								<span class="time">
									<?php echo setTimeEvent($MyRecordNotifiPrivDet->diif,date_format($MyRecordNotifiPrivDet->fecha,"d/m/Y"))?>
								</span>
							</span>
							<span class="message">
								 <?php echo $MyRecordNotifiPrivDet->descripcion;?> - #<?php echo $MyRecordNotifiPrivDet->correlativo;?>
								 <b>Cliente:</b> <?php echo $MyRecordNotifiPrivDet->cliente;?>
							</span>
						</a>
					</li>
					<?php
					}
				?>
			</ul>
		</li>
		 <li class="external">
			<a href="javascript:;" onclick="Metronic.showAllNOtifi(3)">
				 Todas las Notificaci&oacute;nes <i class="m-icon-swapright"></i>
			</a>
		</li>
	</ul>
</li>
<!-- END NOTIFICATION DROPDOWN -->


<!-- BEGIN privados DROPDOWN -->
<?php
if ($ContNotifiNom == 0 ){
 	$textNotiNomHead="Sin Notificaci&oacute;nes ";
 	$displayNotiNomHead="none";
 	$heigtNotiNomHead="0";
 }else{
	$textNotiNomHead="Notificaci&oacute;nes Mencionadas";
	$displayNotiNomHead="block";
	$heigtNotiNomHead="250";
 }
?>
<li class="dropdown dropdown-extended dropdown-inbox li_head_ch " id="header_notification_bar" title="Notificaci&oacute;nes donde te nombraron">
	<a href="#" class="dropdown-toggle"  style="padding-bottom: 5px;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" >
		<i class="fa  fa-star"></i>
		<span class="badge badge-default"  id="badge-XAsig-head"  style="display:<?php echo $displayNotiNomHead;?>"  >
			 <?php echo setContNotifi($ContNotifiNom);?>
		</span>
	</a>
	<ul class="dropdown-menu extended inbox">
		<li>
			<p id="text-notifi-head">
				<?php echo $textNotiNomHead;?>
			</p>
		</li>
		<li>
			<ul class="dropdown-menu-list scroller" style="height: <?php echo $heigtNotiNomHead;?>px;">
				<?php
					$paramNotiNomHead = " AND (n.vista = 0) AND (l.privado = 0) ";

					$MyRecordNotifiNomDetA = $query->SelDB($conex,"site_sel_GetNotifiLinkUserRel",array("top 10",$_SESSION["user"]["idOperador"],$paramNotiNomHead));

					while($MyRecordNotifiNomDet=$query->getdata_object($MyRecordNotifiNomDetA)){
				?>
				<li>
					<a href="javascript:;" onclick='showDetailTime({tipoOR:"<?php echo $MyRecordNotifiNomDet->tipoOR;?>",id:"<?php echo $MyRecordNotifiNomDet->id_orden;?>",sts:"<?php echo $MyRecordNotifiNomDet->id_status;?>",tipoc:"<?php echo $MyRecordNotifiNomDet->tipo_cliente;?>",idLog:"<?php echo $MyRecordNotifiNomDet->id_objetivo;?>",idN:"<?php echo $MyRecordNotifiNomDet->id_notificacion;?>",tipoLine:1})'>
						<span class="photo">
							<?php
								$urlAvatarDet = $CONFIG['HOST']."img/avatar.jpg";
								if($MyRecordNotifiNomDet->imagen != "" ){
									$urlAvatarDet = $CONFIG['HOST']."img/img_p/".$MyRecordNotifiNomDet->imagen;
								}
							?>
							<img src="<?php echo $urlAvatarDet;?>" alt=""/>
						</span>
						<span class="subject">
							<span class="from">
								 <?php echo $MyRecordNotifiNomDet->nombre?>
							</span>
							<span class="time">
								<?php echo setTimeEvent($MyRecordNotifiNomDet->diif,date_format($MyRecordNotifiNomDet->fecha,"d/m/Y"))?>
							</span>
						</span>
						<span class="message">
							 <?php echo $MyRecordNotifiNomDet->descripcion;?> - #<?php echo $MyRecordNotifiNomDet->correlativo;?>
							 <b>Cliente:</b> <?php echo $MyRecordNotifiNomDet->cliente;?>
						</span>
					</a>
				</li>
				<?php
					}
				?>
			</ul>
		</li>
		 <li class="external">
			<a href="javascript:;" onclick="Metronic.showAllNOtifi(2)">
				 Todas las Notificaci&oacute;nes <i class="m-icon-swapright"></i>
			</a>
		</li>
	</ul>
</li>
<!-- END NOTIFICATION DROPDOWN -->

<?php
	if($ContNotifi == 0){
	 	$textNotiHead="Sin Notificaci&oacute;nes por ver ";
	 	$displayNotiHead="none";
	 	$heigtNotiHead="0";
	 }else{
		$textNotiHead="Notificaci&oacute;nes Generales";
		$displayNotiHead="block";
		$heigtNotiHead="250";
	 }
	?>
<li class="dropdown dropdown-extended dropdown-inbox li_head_ch " id="header_inbox_bar" title="Todas Notificaci&oacute;nes sin ver">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
		<i class="fa fa-warning"></i>
		<span class="badge badge-default" id="badge-notifi-head" style="display:<?php echo $displayNotiHead;?>" >
			<?php echo setContNotifi($ContNotifi);?>
		</span>
	</a>
	<ul class="dropdown-menu extended inbox">
		<li>
			<p id="text-notifi-head">
				<?php echo $textNotiHead;?>
			</p>
		</li>
		<li>
			<ul class="dropdown-menu-list scroller" style="height: <?php echo $heigtNotiHead;?>px;">
				<?php
					$paramNotiHead = " AND (l.privado = 0) and (n.vista=0)";
					$MyRecordNotifiDetA = $query->SelDB($conex,"site_sel_GetNotifiLink",array("top 10",$_SESSION["user"]["idOperador"],$paramNotiHead));

					while($MyRecordNotifiDet=$query->getdata_object($MyRecordNotifiDetA)){
				?>
				<li>
					<a href="javascript:;" onclick='showDetailTime({tipoOR:"<?php echo $MyRecordNotifiDet->tipoOR;?>",id:"<?php echo $MyRecordNotifiDet->id_orden;?>",sts:"<?php echo $MyRecordNotifiDet->id_status;?>",tipoc:"<?php echo $MyRecordNotifiDet->tipo_cliente;?>",idLog:"<?php echo $MyRecordNotifiDet->id_objetivo;?>",idN:"<?php echo $MyRecordNotifiDet->id_notificacion;?>",tipoLine:1})'>
						<span class="photo">
							<?php
								$urlAvatarDet = $CONFIG['HOST']."img/avatar.jpg";
								if($MyRecordNotifiDet->imagen != ""){
									$urlAvatarDet = $CONFIG['HOST']."img/img_p/".$MyRecordNotifiDet->imagen;
								}
							?>
							<img src="<?php echo $urlAvatarDet?>" alt=""/>
						</span>
						<span class="subject">
							<span class="from">
								 <?php echo $MyRecordNotifiDet->nombre;?>
							</span>
							<span class="time">
								<?php echo setTimeEvent($MyRecordNotifiDet->diif,date_format($MyRecordNotifiDet->fecha,"d/m/Y"))?>
							</span>
						</span>
						<span class="message">
							 <?php echo $MyRecordNotifiDet->descripcion;?> - #<?php echo $MyRecordNotifiDet->correlativo;?>
							 <b>Cliente:</b> <?php echo $MyRecordNotifiDet->cliente;?>
						</span>
					</a>
				</li>
				<?php
					}
				?>
			</ul>
		</li>
		 <li class="external">
			<a href="javascript:;" onclick="Metronic.showAllNOtifi(1)">
				 Todas las Notificaci&oacute;nes <i class="m-icon-swapright"></i>
			</a>
		</li>
	</ul>
</li>
<!-- END NOTIFICATION DROPDOWN -->
<?php } ?>

<?php if($MenuOrdList){?>
<!-- BEGIN Sin Asignar DROPDOWN -->
<li class="dropdown  li_head_ch hidden-xs" id="header_notification_bar" title="Ordenes sin Asignar">
	<a href="#" class="dropdown-toggle"  style="padding-bottom: 5px;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" >
		<i class="fa glyphicon glyphicon-thumbs-down"></i>
		<span class="badge badge-default"  id="badge-XAsig-head" <?php if($ContXAsig == 0){?> style="display:none"<?php } ?> >
			 <?php echo setContNotifi($ContXAsig);?>
		</span>
	</a><ul></ul>
</li>
<!-- END NOTIFICATION DROPDOWN -->
<!-- BEGIN Asignadas DROPDOWN -->
<li class="dropdown  li_head_ch hidden-xs" id="header_inbox_bar" title="Ordenes Asignadas">
	<a href="#" class="dropdown-toggle" style="padding-bottom: 5px;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
		<i class="fa glyphicon glyphicon-thumbs-up"></i>
		<span class="badge badge-default"  id="badge-Asig-head" <?php if($ContAsig == 0){?> style="display:none"<?php } ?>>
			<?php echo setContNotifi($ContAsig);?>
		</span>
	</a><ul></ul>
</li>
<!-- END INBOX DROPDOWN -->
<!-- BEGIN Seguimiento DROPDOWN -->
<li class="dropdown  li_head_ch hidden-xs" id="header_task_bar" title="Ordenes en Seguimiento">
	<a href="#" class="dropdown-toggle" style="padding-bottom: 5px;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
		<i class="fa glyphicon glyphicon-hand-right"></i>
		<span class="badge badge-default" id="badge-Segui-head" <?php if($ContSegui == 0){?> style="display:none"<?php } ?>>
			 <?php echo setContNotifi($ContSegui);?>
		</span>
	</a><ul></ul>
</li>
<!-- END TODO DROPDOWN -->

<?php }?>
