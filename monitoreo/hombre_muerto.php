<div class="modal fade" id="myModalHombreMuerto" tabindex="-1" role="basic" aria-hidden="true"  data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notificaci&oacute;n - Hombre Muerto</h4>
            </div>
            <div class="modal-body">
                <p>Por favor Ingrese el siguiente Codigo: &nbsp;&nbsp;&nbsp;<b id="text-code-hombre" style="letter-spacing: 10px; ">B59K2GA</b></p>
                <form class="form-horizontal" id="form-cod-hombreMuerto" onSubmit="return false;">
                    <div class="form-body">
                        <div class="alert alert-danger paddingAll5 display-hide">
                           <?php msgRequiredForm();?>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="codeInpuntHm">Codigo:<span class="required">
                                        * </span>
                            </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" style="text-transform:uppercase" class="form-control"    maxlength="5"  id="codeInpuntHm" name="codeInpuntHm" placeholder="Ingrese Codigo"/>
                                </div>
                            </div>
                        </div><!-- /form-group -->
                    </div>
                 </form>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="hidden-code-muerto" id="hidden-code-muerto" />
                <input type="hidden" name="hidden-fecha-muerto" id="hidden-fecha-muerto" />
       		   <button class="<?php echo getClassIcon("guardar");?>" onclick="validarCodigoHm()" >
               		<i class="<?php echo getImgIcon("guardar");?>"></i> Enviar
               </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<div style="display:none">
    <audio id="audio_h_muertos" loop="true">
        <source type="audio/wav" src="<?php echo $CONFIG['HOST'];?>include/sonidos/h_muerto.wav"></source>,
        <source type="audio/ogg" src="<?php echo $CONFIG['HOST'];?>include/sonidos/h_muerto.ogg"></source>,
        <source type="audio/mpeg" src="<?php echo $CONFIG['HOST'];?>include/sonidos/h_muerto.mp3"></source>
    </audio>
</div>

<script>
<?php
$HM_TIME_SHOW_WINDOW = intval(HM_TIME_SHOW_WINDOW);
$HM_TIME_FOR_NOTIFI  = intval(HM_TIME_FOR_NOTIFI);

?>
<?php if($HM_TIME_SHOW_WINDOW!=0) {//si es cero no desea mostrar hombre muerto' ?>
//bandeera para saber si esta el hombre muerto activado
var BamShowHombreMuerto = false;

//contadores de tiempo luego de abrir el hombre muerto
var contador_time=null;
var cont_mille=0;
var cont_test=1000;

//tiempo para activar hm
var timeout = '<?php echo $HM_TIME_SHOW_WINDOW;?>';
var ActNotifiHM = '<?php echo $HM_TIME_FOR_NOTIFI;?>';

ActNotifiHM = ActNotifiHM/1000;

//contador de inactividad
(function($){
    $(document).bind("idle.idleTimer", function(){
        //verifica que no este el hombre muerto abierto
        if(!BamShowHombreMuerto){
            window.parent.tb_remove(true);

            //asigna codigo y activa sonido
            $("#audio_h_muertos")[0].play();

            //agrega datos al modal
            $("#codeInpuntHm").val("");
            var code = randomString(4);
            $("#text-code-hombre").text(code);
            $("#hidden-code-muerto").val(code);
            $("#hidden-fecha-muerto").val(new Date());

            $("#myModalHombreMuerto").modal("show");

            //contador de espera de notificacion
            cont_mille=0;
            contador_time = setInterval(function(){
                cont_mille++;
            },1000);

            BamShowHombreMuerto = true;
        }
    });
    $(document).bind("active.idleTimer", function(){

    });

    $.idleTimer(parseInt(timeout));
})(jQuery);

//valida formulario de hombre muerto
var validator_hombre_muerto = null;

var rulesHombreM ={
    codeInpuntHm:{
        required: true
    }
};

var messagesHombreM ={
    codeInpuntHm:{
        required: "Ingrese Codigo"
    }
};

$(document).ready(function() {
    validator_hombre_muerto = handleValidationForm("#form-cod-hombreMuerto",rulesHombreM,messagesHombreM);
});


function validarCodigoHm(){
    resetFormulario(validator_hombre_muerto);
    if(validator_hombre_muerto.form()){
        if($("#codeInpuntHm").val().trim().toUpperCase()==$("#hidden-code-muerto").val().trim()){//verifica codigo
            $("#audio_h_muertos")[0].pause();

            //detiene hombre muerto
            clearInterval(contador_time);

            if(parseInt(cont_mille)>parseInt(ActNotifiHM)){ //verifica si paso el tiempo para enviar notificacion
                $.ajax({
                    url:'send_notification_hombre.php?x='+Math.random(),
                    type: "POST",
                    timeout:60000,
                    error: function(x, t, m) {

                    },
                    data: {
                        acc:'send_hombre',
                        fech:$("#hidden-fecha-muerto").val(),
                        timex:converSegAhora(cont_mille*1000),
                        sgun:cont_mille
                    },
                    cache: false,
                    beforeSend:function(){

                    },
                    success:function(data) {

                    }
                });
            }

            $("#myModalHombreMuerto").modal("hide");
            BamShowHombreMuerto = false;
        }else{
            alertError({title:"Notificaci&oacute;n",text:"Codigo no Coinciden"});
            $("#codeInpuntHm").val("");
        }
    }
}
<?php } ?>
</script>