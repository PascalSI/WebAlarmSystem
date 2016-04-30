<div class="modal fade" id="myModalEstatusAdmin"   aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Eliga Estatus Asignar</h4>
            </div>
            <div class="modal-body">
                 <form class="form-horizontal" id="">
                 	<p>Orden a cambiar estatus : <b id="b-idordenv">0</b></p>
                	<div class="form-body">
                    	<div class="form-group">
                            <label class="col-md-4 control-label" for="">Estatus:</label>
                            <div class="col-md-8">
                            	<select class="form-control" id="select-chan-st-admin" name="select-chan-st-admin" onchange="showFormStAdmin()">
                                	<option value="">Seleccione</option>
									 <?php
                                        $MyRecordOrdStSub2 = $query->SelDB($conex,"site_sel_OrdSerStatus",array("and (id_status=4 or  id_proceso in(2))"));

										while($r=$query->getdata_object($MyRecordOrdStSub2)){
										   ?>
											<option value="<?php echo $r->id_status;?>" rel-id-st="<?php echo $r->id_status;?>">
												<?php echo $r->descripcion2;?>
											</option>
										  <?php
										}
									 ?>
                                </select>
                            </div>
                        </div><!-- /form-group -->
                    </div>
                 </form>
            </div>
             <div class="modal-footer">
               <button class="btn blue-madison" data-dismiss="modal" type="button">Salir</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div id="divHtmlStatusChange">
</div>