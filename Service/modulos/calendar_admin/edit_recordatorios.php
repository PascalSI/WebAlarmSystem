<div class="modal fade" id="myModalRecordEdit"    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title-change-record">Cambiar Recordatorio</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="form-change-reecord">
                    <div class="form-group">
                        <label class="col-md-2 control-label" id="frm_change_motivo_record_label" for="frm_change_motivo_record">Motivo:</label>
                        <div class="col-md-10">
                            <textarea rows="5" class="form-control" id="frm_change_motivo_record" maxlength="300" name="frm_change_motivo_record" placeholder="Motivo de cambio"></textarea>
                        </div>
                    </div><!-- /form-group -->
                </form>
                <input type="hidden" id="tipoChangeRecord" />
            </div>
             <div class="modal-footer">
               <button class="btn " data-dismiss="modal" type="button" onclick="salirChangeEvent();" id="frm_change_recordad_exit">Cancelar</button>
               <button type="button" id="frm_change_recordad"  class="<?php echo getClassIcon("guardar");?>" onclick="changeRecordatorio()">
                    <i class="<?php echo getImgIcon("guardar");?>"></i>  <span id="frm_change_recordad_text">Guardar</span>
               </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
function salirChangeEvent(){
      Calendar.init();
}


function changeRecordatorio(){
    var event = EventObject;
    if($("#tipoChangeRecord").val()==1){
        alertErrorReco = "Ingrese Motivo";
        alertSuccesReco = "Fecha cambiada Exitosamente.";
    }else{
        alertErrorReco = "Ingrese Observacion";
        alertSuccesReco = "Recordatorio realizado Exitosamente.";
    }

    if($("#frm_change_motivo_record").val()==""){
        alertError({title:"Error",text:alertErrorReco});
        return false;
    }

    $.ajax({
        url:'modulos/calendar_admin/controller.php',
        type: "POST",
        timeout:60000,
        data: {
            acc:"change_record",
            id:event.idRec,
            title:event.titleAux,
            fec:event.start.format("DD-MM-YYYY"),
            time:convertTo24Hour(event.start.format("h:mm:ss a")),
            timeDesc:event.start.format("h:mm:ss a"),
            idOrd:event.objetivo,
            st:event.id_status,
            motivo:$("#frm_change_motivo_record").val(),
            tipo:$("#tipoChangeRecord").val()
        },
        cache: false,
        beforeSend:function(){
            btnPrimaryChangeRecordad(0);
        },
        success:function(data) {
            validateSession(data);
            alertSuccess({title:"Notificaci&oacute;n",title:alertSuccesReco});
            $("#myModalRecordEdit").modal("hide");
             btnPrimaryChangeRecordad(1);
             Calendar.init();
        },
        error: function (x, t, m) {
            errorAjax(t);
             btnPrimaryChangeRecordad(0);
            Calendar.init();
        }

    });
}

function btnPrimaryChangeRecordad(st){
    var can = $('#frm_change_recordad_exit');
    var btn = $('#frm_change_recordad');
    var txtBtn = $("#frm_change_recordad_text");


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