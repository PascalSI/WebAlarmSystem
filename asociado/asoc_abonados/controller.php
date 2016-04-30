<div class="row">
	<div class="col-md-12">
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		<h3 class="page-title">
			Cuentas de clientes
		</h3>
		<!-- END PAGE TITLE & BREADCRUMB-->
	</div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
            <div class="portlet-title page-breadcrumb breadcrumb marginTop1">
                <div class="caption">
                    <i class="fa marginTop1 fa-user"></i>Lista de Cuentas
                </div>
            </div>
            <div class="portlet-body">
				<div class="tiles">
					<?php
						$i=1;
						$row=0;
						//agregar el campo por el cual se ordena
						$MyrecordData = $query->SelDB($conex,"site_sel_allclientebyAsociados",array($_SESSION["cliente"]["idAsociado"]));

						while($r=$query->getdata_object($MyrecordData)){

							$class_st ="";
							$html_st = '';
							$id = encode64_asp($r->id_cliente);
							if($r->status_web==0){
								$class_st ="inactive";
								$html_st = '<div class="corner"> </div>	<div class="check"> </div>';
								$id = "-1";
							}
							?>
								<div class="tile <?php echo $class_st;?>  <?php if(strlen($r->nombre_cliente)>20){ echo "double";} ?> <?php echo getColorArrayTiles($i);?>" onClick="viewData('<?php echo $id;?>','<?php echo $r->nombre_cliente;?>')">
									<?php echo $html_st;?>
									<div class="tile-body">
										<i class="fa fa-user"></i>
									</div>
									<div class="tile-object">
										<div class="name"><?php echo $r->nombre_cliente;?></div>
										<div class="number">
											<?php echo $r->id_cliente;?>
										</div>
									</div>
								</div>
							<?php
							$i++;
						}
					?>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<!-- END PAGE CONTENT-->
<script>
function viewData(id,name){
	if(id=="-1"){
		alertError({title:'Notificaci&oacute;n',text:'La cuenta <b>'+name+'</b> se encuentra desactivada.'});
	}else{
		var url="../site/?id="+id;
		window.open(url);
	}
}
</script>