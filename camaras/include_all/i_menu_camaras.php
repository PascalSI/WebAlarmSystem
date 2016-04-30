<div class="page-sidebar navbar-collapse collapse">
	<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
	<select class="form-control" onchange="addChanels(this.value)" name="" id="">
		<option value="0" >8 Canales</option>
		<option value="12" >12 Canales</option>
		<option value="16" >16 Canales</option>
		<option value="20" >20 Canales</option>
	</select>
	<?php
		$chanRel = decode64_asp($_REQUEST["q"]);
		$chanRel = explode(",", $chanRel);

		$id_cctv = decode64_asp($_REQUEST["xq"]);

		$paramIdc = "";

		if(trim($id_cctv)!=""){
			$paramIdc = " AND c.id_cctv='".$id_cctv."'";
		}

		$MyrecordData = $query->SelDB($conex,"site_sel_GetCamaraPanel",array($idCliente,$paramIdc));
		$contM = 1;
		$contMaxz = $query->count_row($MyrecordData);
		while($r=$query->getdata_object($MyrecordData)){
			?>
				<li class="<?php if($contM==1){ echo 'start open';}else if($contM==$contMaxz){ echo 'last'; }?>">
					<a href="javascript:;">
					<i class="fa fa fa-th-large"></i>
					<span class="title"><?php echo $r->descripcion;?></span>
					<span class="arrow <?php if($contM==1){ echo 'open';} ?>"></span>
					</a>
					<ul class="sub-menu"  <?php if($contM==1){ echo 'style="display:block;"';} ?>>
						<?php
						$paramChanel = " and id_cctv='$r->id_cctv'";
						$MyrecordUSer = $query->SelDB($conex,"site_sel_DataCCTV_Channel",array($paramChanel));
						while($rU=$query->getdata_object($MyrecordUSer)){
							$parpadea = '';
							$claseActive = '';
							if (in_array($rU->id_channel, $chanRel)) {
								$parpadea = '<i class="fa fa-exclamation-triangle font-red parpadea"></i>';
								$claseActive = 'active_relation';
							}
							?>
								<li>
									<a href="javascript:;" class="link-cam <?php echo $claseActive;?>" rel-name-cam="<?php echo $r->descripcion;?> - <?php echo $rU->descripcion;?>" onclick="setCamBox({_this:this,q:'<?php echo encode64_asp($r->id_cctv);?>',c:'<?php echo encode64_asp($rU->channel);?>'})">
									<i class="fa  fa-video-camera"></i>
									<?php echo $rU->descripcion.$parpadea;?>
									</a>
								</li>
							<?php
						}
						if(trim($id_cctv)!=""){
						?>
							<li>
								<a href="javascript:;" onclick="viewAllCam()">
								<i class="fa  fa-eye "></i>
									<i>Ver Todas</i>
								</a>
							</li>
						<?php } ?>
					</ul>
				</li>
			<?php
			$contM++;
		}
	?>
	</ul>
	<!-- END SIDEBAR MENU -->
</div>