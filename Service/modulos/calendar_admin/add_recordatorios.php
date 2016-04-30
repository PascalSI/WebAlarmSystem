<div class="modal fade" id="myModalRecordatorio"    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Agregar Recordatorio</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="form-add-reecord">
                    <div class="form-group">
                        <label class="control-label col-md-3">Fecha:</label>
                        <div class="col-md-5">
                            <div class="input-group   date date-picker" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control"  id="frm_date_record" name="frm_date_record" readonly >
                                <span class="input-group-btn">
                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                            <!-- /input-group -->
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control timepicker timepicker-no-seconds" id="frm_time_record" readonly>
                                <span class="input-group-btn">
                                    <button class="btn default" type="button"><i class="fa fa-clock-o"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="frm_user_record"> Usuario :</label>
                        <div class="col-md-9">
                            <input type="hidden" name="frm_user_record" id="frm_user_record" class="form-control"/>
                        </div>
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="frm_titulo_record">Titulo:</label>
                        <div class="col-md-9">
                            <input type="text" name="frm_titulo_record" id="frm_titulo_record" class="form-control"  placeholder="Titulo" maxlength="25"/>
                        </div>
                    </div><!-- /form-group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="frm_descrip_record">Descripci&oacute;n :</label>
                        <div class="col-md-9">
                            <textarea rows="5" class="form-control" id="frm_descrip_record" maxlength="300" name="frm_descrip_record" placeholder="Descripci&oacute;n"></textarea>
                        </div>
                    </div><!-- /form-group -->
                    <input type="hidden" id="id-orden-record" />
                    <input type="hidden" id="sts-orden-record" />
                </form>
            </div>
             <div class="modal-footer">
               <button class="btn " data-dismiss="modal" type="button" id="frm_send_recordad_exit">Salir</button>
               <button type="button" id="frm_send_recordad"  class="<?php echo getClassIcon("guardar");?>" onclick="guardarRecordatorio()">
                    <i class="<?php echo getImgIcon("guardar");?>"></i>  <span id="frm_send_recordad_text">Guardar</span>
               </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<input type="hidden" name="date_record" id="date_record" value='<?php echo date("d-m-Y", strtotime("+1 day"));?>'/>
<script>
setTimeout(function(){
    $('#frm_time_record').timepicker({
        autoclose: true,
        minuteStep: 5
    });

    $('#frm_user_record').select2({
        placeholder: "Buscar Usuarios",
        minimumInputLength: -1,
        allowClear: true,
        multiple:true,
        ajax: {
            url:'include/load_data.php?dat=load_all_user&x='+Math.random(),
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (dat, page) {
                    return {results: dat.aaData};
            }
        },
        initSelection: function(element, callback) {

        },
        formatSelection: function(a){
            if(a!=""){
                return  a.text;
            }
        },
        escapeMarkup: function (m) { return m; }
    });

});

//validator de formulario
var validatorFormRecord = $("#form-add-reecord").validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-block', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    ignore: "",
    rules: {
            frm_date_record:{
                required: true
            },
            frm_time_record:{
                required: true
            },
            frm_user_record:{
                required: true
            },
            frm_titulo_record:{
                required: true
            },
            frm_descrip_record:{
                required: true
            }
        },
    messages:{
        frm_date_record:{
            required: "Ingrese Fecha"
        },
        frm_time_record:{
            required: "Ingrese Hora"
        },
        frm_user_record:{
            required: "Ingrese Usuarios"
        },
        frm_titulo_record:{
            required: "Ingrese Titulo"
        },
        frm_descrip_record:{
            required: "Ingrese Descripci&oacute;n"
        }
    },
    invalidHandler: function (event, validator) { //display error alert on form submit
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

function setRecordatorio(obj){
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            language: 'es',
            autoclose: true
        });
        $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
    }

    $("#id-orden-record").val(obj.id);
    $("#sts-orden-record").val(obj.sts);

    $("#frm_date_record").val($("#date_record").val());
    $('#frm_user_record').select2("val", "");
    $('#frm_user_record').select2("data",{id:"<?php echo $_SESSION["user"]["idOperador"];?>",text:"<?php echo $_SESSION["user"]["nameOperador"];?>"});

    $('#frm_time_record').timepicker('setTime', '08:00 AM');

    $("#frm_titulo_record,#frm_descrip_record").val("");

    resetFormulario(validatorFormRecord);

    $('#myModalRecordatorio').modal('show');
}

function guardarRecordatorio(){
    if(validatorFormRecord.form()){
        var rels = setRelRecord($("#frm_user_record").val().split(","),"<?php echo $_SESSION["user"]["idOperador"];?>");

        $.ajax({
            url:'modulos/calendar_admin/controller.php',
            type: "POST",
            timeout:60000,
            data: {
                acc:'addRecord',
                fec:$("#frm_date_record").val(),
                time:convertTo24Hour($("#frm_time_record").val()),
                timeDesc:$("#frm_time_record").val(),
                users:rels,
                tit:$("#frm_titulo_record").val(),
                desc:$("#frm_descrip_record").val(),
                idO:$("#id-orden-record").val(),
                st:$("#sts-orden-record").val()
            },
            cache: false,
            beforeSend:function(){
                btnPrimaryRecordad(0)
            },
            success:function(data) {
                validateSession(data);
                alertSuccess({title:"Notificaci&oacute;n",title:"Recordatorio agregado exitosamente."});
                $("#myModalRecordatorio").modal("hide");
                 btnPrimaryRecordad(1);
            },
            error: function (x, t, m) {
                errorAjax(t);
                btnPrimaryRecordad(1);
            }

        });
    }
}


function btnPrimaryRecordad(st){
    var can = $('#frm_send_recordad_exit');
    var btn = $('#frm_send_recordad');
    var txtBtn = $("#frm_send_recordad_text");


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

function setRelRecord(array,user){
    if(!arraySearch(array,user)){
        array.push(user);
    }
    return array.toString();
}
</script>