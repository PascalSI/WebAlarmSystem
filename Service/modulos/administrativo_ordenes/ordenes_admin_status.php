<?php
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/scriptdb/config.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/scriptdb/querys.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/phpscript/generales.php");
include(dirname(dirname(dirname(dirname(__FILE__))))."/include/phpscript/sessionAjax.php");

$query= new Querys();
$conex = $query->getConection($CONFIG);

$txtCampTex = "Comentario";
$contCampText = "";
$idBamP = 0;
$tipoOrden = $_REQUEST["tipoOrden"];
?>
<div class="modal fade" id="myModalFomEstatusAdmin"  role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cambiar Estatus de Orden</h4>
            </div>
            <div class="modal-body">
                 <form class="form-horizontal" id="form-add-sts-ord-admin">
                	<div class="form-body">
                    	<?php
							switch($_REQUEST["stx"]){
                                case "7":
									$txtCampTex="Observaci&oacute;n";
								?>
									<div class="form-group">
                                        <label class="control-label col-md-3">Fecha Fact. :</label>
                                        <div class="col-md-9">
                                            <div class="input-group   date date-picker" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control"  id="frm_st_odv_fechafact" name="frm_st_odv_fechafact" readonly value="<?php echo date("d-m-Y");?>">
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                            <!-- /input-group -->
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label" for="frm_st_odv_cod_Fact">Cod. Factura :</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" onkeypress="javascript:return validarNro(event)" id="frm_st_odv_cod_Fact" name="frm_st_odv_cod_Fact" placeholder="Cod. Factura" maxlength="10" >
                                        </div>
                                    </div><!-- /form-group -->
                                    <div class="form-group">
                                        <label class="col-md-3 control-label" for="frm_st_odv_mont_Fact">Monto Factura :</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" onkeypress="javascript:return validarNro(event)" id="frm_st_odv_mont_Fact" name="frm_st_odv_mont_Fact" placeholder="Monto Factura" maxlength="10" >
                                        </div>
                                    </div><!-- /form-group -->
								<?php
								break;
								case "8":
									$txtCampTex="Observaci&oacute;n";
									?>
										<div class="form-group">
                                            <label class="control-label col-md-3">Fecha Pago :</label>
                                            <div class="col-md-9">
                                                <div class="input-group   date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control"  id="frm_st_od_ad_fechapag" name="frm_st_od_ad_fechapag" readonly value="<?php echo date("d-m-Y");?>">
                                                    <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                </div>
                                                <!-- /input-group -->
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="frm_st_od_ad_tip_pago">Forma de Pago :</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="frm_st_od_ad_tip_pago" name="frm_st_od_ad_tip_pago" >
                                                <option value="" >Seleccione</option>
                                                <?php
			                                        $MyRecordOF = $query->SelDB($conex,"site_sel_FormasPago",array());

													while($rFp=$query->getdata_object($MyRecordOF)){
													   ?>
														<option value="<?php echo $rFp->idforma;?>">
															<?php echo $rFp->descripcion;?>
														</option>
													  <?php
													}
												 ?>
                                                </select>
                                            </div>
                                        </div><!-- /form-group -->
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="frm_st_od_ad_id_pago">Identificador :</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" onkeypress="javascript:return validarNro(event)" id="frm_st_od_ad_id_pago" name="frm_st_od_ad_id_pago" placeholder="Identificador del Pago" maxlength="20" >
                                            </div>
                                        </div><!-- /form-group -->
									<?php
								break;
							}

						?>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="frm_st_od_ad_coment"><?php echo $txtCampTex;?> :</label>
                            <div class="col-md-9">
                                <textarea rows="5" class="form-control" id="frm_st_od_ad_coment" maxlength="300" name="frm_st_od_ad_coment" placeholder="<?php echo $txtCampTex;?>"><?php echo $contCampText;?></textarea>
                            </div>
                        </div><!-- /form-group -->
                    </div>
                 </form>
            </div>
             <div class="modal-footer">
               <button class="btn default"  id="frm_st_odv_ad_btn1"   data-dismiss="modal" type="button">Cancelar</button>
               <button type="button" id="frm_st_odv_ad_btn2"  class="<?php echo getClassIcon("guardar");?>" onclick="guardarCambioStOdrV()">
                    <i class="<?php echo getImgIcon("guardar");?>"></i>  <span id="frm_st_odv_ad_txt2">Guardar</span>
               </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
<?php
    switch($_REQUEST["stx"]){
        case "7":
        ?>
            if (jQuery().datepicker) {
                $('.date-picker').datepicker({
                    rtl: Metronic.isRTL(),
                    language: 'es',
                    autoclose: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }

                //validator de formulario
                var validatorChangeStatus = $("#form-add-sts-ord-admin").validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    ignore: "",
                    rules: {
                            frm_st_odv_fechafact:{
                                required: true
                            },
                            frm_st_odv_cod_Fact:{
                                required: true
                            },
                            frm_st_odv_mont_Fact:{
                                required: true
                            }
                        },
                    messages:{
                        frm_st_odv_fechafact:{
                            required: "Ingrese Fecha Factura"
                        },
                        frm_st_odv_cod_Fact:{
                            required: "Ingrese Cod. Factura"
                        },
                        frm_st_odv_mont_Fact:{
                            required: "Ingrese Monto Factura"
                        }
                    },
                    invalidHandler: function (event, validator) { //display error alert on form submit
                        Metronic.scrollTo($("#form-add-sts-ord"), -100);
                    },
                    highlight: function (element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },

                    unhighlight: function (element) { // revert the change done by hightlight
                        $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                    },
                    success: function (label) {
                        label
                            .closest('.form-group').removeClass('has-error'); // set success class to the control group
                    }
                });
                function guardarCambioStOdrV(){
                    if(validatorChangeStatus.form()){
                        var data = {
                            acc:'chageStatus',
                            fecha:$("#frm_st_odv_fechafact").val(),
                            codigo:$("#frm_st_odv_cod_Fact").val(),
                            monto:$("#frm_st_odv_mont_Fact").val(),
                            coment:$("#frm_st_od_ad_coment").val(),
                            st:'7',
                            id:'<?php echo $_REQUEST["idO"];?>',
                            tipoOrdn:'<?php echo $tipoOrden;?>'
                        }
                        SendInfoAjax(data);
                    }
                }
        <?php
        break;
        case "8":
        ?>
            if (jQuery().datepicker) {
                $('.date-picker').datepicker({
                    rtl: Metronic.isRTL(),
                    language: 'es',
                    autoclose: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }

            setTimeout(function(){
                $("#frm_st_od_ad_tip_pago").select2({
                    placeholder: "Seleccione Tipo de Pago",
                    minimumResultsForSearch: -1,
                    allowClear: true
                });
            },100);


            //validator de formulario
            var validatorChangeStatus = $("#form-add-sts-ord-admin").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                        frm_st_od_ad_fechapag:{
                            required: true
                        },
                        frm_st_od_ad_tip_pago:{
                            required: true
                        }
                    },
                messages:{
                    frm_st_od_ad_fechapag:{
                        required: "Ingrese Fecha Pago"
                    },
                    frm_st_od_ad_tip_pago:{
                        required: "Seleccione Forma"
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo($("#form-add-sts-ord"), -100);
                },
                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },
                success: function (label) {
                    label
                        .closest('.form-group').removeClass('has-error'); // set success class to the control group
                }
            });
            function guardarCambioStOdrV(){
                if(validatorChangeStatus.form()){
                    var tipPaModal = "";
                    //obtiene nombre del tecnico asignado
                    if($("#frm_st_od_ad_tip_pago").val()!="0" && $("#frm_st_od_ad_tip_pago").val()!="" ){
                        tipPaModal =$('#frm_st_od_ad_tip_pago').select2('data').text
                    }
                    var data = {
                        acc:'chageStatus',
                        fecha:$("#frm_st_od_ad_fechapag").val(),
                        tipoP:$("#frm_st_od_ad_tip_pago").val(),
                        idtP:$("#frm_st_od_ad_id_pago").val(),
                        coment:$("#frm_st_od_ad_coment").val(),
                        st:'8',
                        id:'<?php echo $_REQUEST["idO"];?>',
                        tipoPText:tipPaModal,
                        tipoOrdn:'<?php echo $tipoOrden;?>'
                    }
                    SendInfoAjax(data);
                }
            }
        <?php
        break;
    }
?>

function SendInfoAjax(info){
    $.ajax({
        url:'modulos/administrativo_ordenes/controller.php',
        type: "POST",
        timeout:60000,
        data: info,
        cache: false,
        beforeSend:function(){
            btnPrimaryChgangeStOdvAdm(0)
        },
        success:function(data) {
            validateSession(data);
            loadTabsAdminOrd();
            alertSuccess({title:"Notificaci&oacute;n",title:"Estatus Asignado Exitosamente."});
            $("#myModalFomEstatusAdmin").modal("hide");
        },
        error: function (x, t, m) {
            errorAjax(t);
            btnPrimaryChgangeStOdvAdm(1);
        }

    });
}


function btnPrimaryChgangeStOdvAdm(st){
    var can = $('#frm_st_odv_ad_btn1');
    var btn = $('#frm_st_odv_ad_btn2');
    var txtBtn = $("#frm_st_odv_ad_txt2");


    if(st==1){
        btn.removeClass('btn-danger');
        btn.addClass('btn-primary');
        txtBtn.text('Guardar');
        btn.attr('disabled',false);
        can.attr('disabled',false);
    }else{
        btn.removeClass('btn-primary');
        btn.addClass('btn-danger');
        txtBtn.text('Guardando...');
        btn.attr('disabled', 'disabled');
        can.attr('disabled', 'disabled');
    }
}

</script>