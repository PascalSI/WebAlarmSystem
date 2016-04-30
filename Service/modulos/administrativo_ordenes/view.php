<?php
    $OrdAdminlist = PermModAccUser(35,177);
    $OrdAdminSt = PermModAccUser(35,178);
    $OrdAdminTime = PermModAccUser(35,179);
?>
<style>
	.dataTables_scrollBody { position: relative;
	z-index:1px !important; }
</style>
<div  id="id_panel_search_visit">
    <!-- BEGIN PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->
            <h3 class="page-title">
               Modulo Administrativo
            </h3>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12">
            <!-- Begin: life time stats -->
            <div class="portlet box  <?php echo $CONFIG['WEB_THEME'];?>">
                <div class="portlet-title page-breadcrumb breadcrumb marginTop1">
                    <div class="caption">
                        <i class=" marginTop1 fa fa-clipboard"></i>Ordenes de Servicio
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <?php if($OrdAdminlist){ ?>
                        <div class="tabbable-custom ">
                            <ul class="nav nav-tabs ">
                                <li class="bg-green active">
                                    <a href="#tab_1" data-toggle="tab" class="blanck">
                                         Por Facturar
                                    </a>
                                </li>
                                <li class="bg-blue-madison">
                                    <a href="#tab_2" data-toggle="tab" class="blanck">
                                         Facturada
                                    </a>
                                </li>
                                <li class="bg-yellow-casablanca">
                                    <a href="#tab_3" data-toggle="tab" class="blanck">
                                         Pagada
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <p>
                                    	<?php include("tab_x_facturar.php");?>
                                    </p>
                                </div>
                                <div class="tab-pane" id="tab_2">
                                    <p>
                                    	<?php include("tab_facturada.php");?>
                                    </p>
                                </div>
                                <div class="tab-pane" id="tab_3">
                                    <p>
                                        <?php include("tab_pagada.php");?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- End: life time stats -->
        </div>
    </div>
</div>
<?php include("change_status_ordAdmin.php"); ?>
<script>
var DatOdrAdmin = {
    idOrden:0,
    tipoO:1,
    _reset:function(){
        this.idOrden=0;
        this.tipoO=1;
    }
}

var configAjaxStAdmin = {
    HrAjaxOrdAdmin:null,
    HrAjaxOrdAdminLoad:false
};

function changeStOrdAdmin(obj){
    <?php if($OrdAdminSt){ ?>
    if(!configAjaxStAdmin.HrAjaxOrdAdminLoad){
        setOptionOrdAdmin(obj);
        $("#b-idordenv").text(obj.id+" - "+obj.name);

        DatOdrAdmin._reset();
        DatOdrAdmin.idOrden=obj.id;
        DatOdrAdmin.tipO = obj.tipO;

        $('#myModalEstatusAdmin').modal('show');
    }else{
        $('#myModalEstatusAdmin').modal('hide');
        alertError({title:"Notificacio&oacute;n",title:"Por Favor Espere, Pagina Cargando..."});
        return false;
    }
    <?php } ?>
}

function setOptionOrdAdmin(o){
    <?php if($OrdAdminSt){?>
    var aux = null;
    var x = o.sts
    switch(x){
        case "4":
            aux = new Array("7");
        break;
        case "7":
            aux = new Array("8");
        break;
    }

    $("#select-chan-st-admin").find('option').removeAttr("selected");
    $("#select-chan-st-admin option").css("display","none");
    $("#select-chan-st-admin option:first").css("display","block");

    if(aux!=null){
        $.each(aux, function( key, value ) {
            $("#select-chan-st-admin option[rel-id-st=\""+value+"\"]").css("display","block");
        });
        $("#select-chan-st-admin option:visible:first").attr("selected","selected");
        $("#select-chan-st-admin").change();
    }
    <?php } ?>
}

function showFormStAdmin(){
    <?php if($OrdAdminSt){ ?>
    var camp = $("#select-chan-st-admin").val();
    if(camp!=""){
        if(!configAjaxStAdmin.HrAjaxOrdVistLoad){
            $('#myModalEstatusAdmin').modal('hide');
            configAjaxStAdmin.HrAjaxOrdVist = $.ajax({
                url:'modulos/administrativo_ordenes/ordenes_admin_status.php',
                type: "POST",
                timeout:60000,
                data: {
                    idO:DatOdrAdmin.idOrden,
                    tipcoC:DatOdrAdmin.tipoc ,
                    stx:camp,
                    tipoOrden:DatOdrAdmin.tipO
                },
                cache: false,
                beforeSend:function(){
                    $("#divHtmlStatusChange").html("");
                    Metronic.scrollTop();
                    Metronic.startPageLoading("Cargando Formulario...");
                    configAjaxStAdmin.HrAjaxOrdVistLoad=true;
                },
                success:function(data) {
                    if(validateSession(data)){
                        $("#divHtmlStatusChange").html(data);
                        Metronic.stopPageLoading();
                        Metronic.initAjax(); // initialize core stuff
                        configAjaxStAdmin.HrAjaxOrdVistLoad=false;
                        $("#myModalFomEstatusAdmin").modal("show");
                    }
                },
                error: function (x, t, m) {
                    errorAjax(t);
                    Metronic.stopPageLoading();
                    configAjaxStAdmin.HrAjaxOrdVistLoad=false;
                }

            });
        }else{
            $('#myModalEstatusAdmin').modal('hide');
            alertError({title:"Notificacio&oacute;n",title:"Por Favor Espere,Pagina Cargando..."});
            return false;
        }
    }
    <?php } ?>
}


function loadTabsAdminOrd(){
    <?php if($OrdAdminlist){?>
    searchadOrdXFact();
    searchadOrdFact();
    searchadOrdPag();
    <?php } ?>
}
</script>