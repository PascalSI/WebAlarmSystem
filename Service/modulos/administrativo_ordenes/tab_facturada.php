<form onsubmit="return false;" class="horizontal-form" id="form-admin-ord-search-facturada">
    <div class="form-body paddingAll1">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Cliente</label>
                    <input type="hidden" name="adOrd_client_fact" id="adOrd_client_fact" class="form-control"/>
                    <input type="hidden" name="adOrd_tipoc_fact" id="adOrd_tipoc_fact" class="form-control"/>
                </div>
            </div>
            <!--/span-->
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Problema</label>
                    <input type="text" class="form-control" id="adOrd_prob_fact" maxlength="100" name="adOrd_prob_fact" placeholder="Problema">
                </div>
            </div>
            <!--/span-->
            <div class="col-md-4">
                <label class="control-label">Fecha de Facturaci&oacute;n</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group   date date-picker date-picker-one" data-date-format="dd/mm/yyyy" >
                            <input type="text" class="form-control" id="adOrd_ini_fact" name="adOrd_ini_fact" readonly placeholder="Desde">
                            <span class="input-group-btn">
                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="input-group   date date-picker date-picker-two" data-date-format="dd/mm/yyyy" >
                            <input type="text" class="form-control" id="adOrd_fin_fact" name="adOrd_fin_fact" readonly placeholder="Hasta">
                            <span class="input-group-btn">
                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!--/span-->
            <div class="col-md-2">
                <div class="form-group">
                     <button type="button" title="Buscar Orden" class="<?php echo getClassIcon("buscar");?> marginTopBtnFrom"  onclick="searchadOrdFact()">
                        <i class="<?php echo getImgIcon("buscar");?>"></i>
                    </button>
                    <button type="button" title="Limpiar Buscador" class="<?php echo getClassIcon("limpiar");?> marginTopBtnFrom" onclick="limpiaradOrdFact()">
                        <i class="<?php echo getImgIcon("limpiar");?>"></i>
                    </button>
                </div>
            </div>
            <!--/span-->
        </div>
        <!--/row-->
    </div>
</form>
<table class="table table-striped table-condensed table-hover" id="datatable_ajaxAdOrdFact">
<thead>
<tr role="row" class="heading">
    <th width="10"></th>
    <th width="50">Orden</th>
    <th width="">Cliente</th>
    <th width="">Problema</th>
    <th width="">Fecha Fact</th>
    <th width="">Factura</th>
    <th width="">Monto</th>
    <th width="100">Acciones</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
<?php
include($CONFIG['DIR_PROJECT']."Service/modulos/calendar_admin/add_recordatorios.php");?>
<script>
var tableWrapperAdOrdFact= null;
var TableAjaxAdOrdFact = $("#datatable_ajaxAdOrdFact").dataTable({
    "sDom": "<'row'<'col-md-6 col-sm-12'>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
    "aaSorting": [[ 1, "asc" ]],
    "sAjaxSource": 'modulos/administrativo_ordenes/controller.php?acc=load_ad_ord_facturadas&client=&tipoc=&sts=7&f1=&f2=', // ajax source
    "fnDrawCallback" : function(a) {
        Metronic.initAjax();
    },
    "aoColumns": [
        { "mData": "prioridad",
          "bSortable": false ,
          "mRender":  function(data, type, obj) {

                var btnTipo = setIconTipoOrd(obj.tipo_orden);

                return btnTipo;
          }
        },
        { "mData": "correlativo" },
        { "mData": "nombre",
          "fnRender": function(obj){
                if(obj.tipoc=="1"){
                    url="<?php echo $CONFIG['HOST'];?>Panel/PanelCliente.php?q="+StrTrim(obj.id_crip)+"&mod=showOrdenes" ;
                    url_text = 'Ver Historial del Cliente: '+obj.nombre+ '">'+setMid(obj.nombre,40);
                }else if(obj.tipoc=="2"){
                    url="<?php echo $CONFIG['HOST'];?>Panel/PanelCliente.php?q="+StrTrim(obj.id_crip)+"&mod=showOrdenes";
                    url_text = 'Ver Historial del Cliente de Servicio: '+obj.nombre+ '">'+setMid(obj.nombre,40);
                }

                return '<a href="'+url+'" style="color:#000000" target="_blank" class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+url_text+'</a>';
          }
        },
        { "mData": "problema",
          "bSortable": false,
          "mRender":  function(data, type, obj) {
                return '<span class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+obj.problema+ '">'+setMid(obj.problema,30)+ '</span>';
          }
        },
        {
          "mData": "fechaFact",
          "bSortable": false
        },
        {
          "mData": "codeFact",
          "bSortable": false,
          "mRender":  function(data, type, obj) {
                return '<span class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+obj.codeFact+ '">'+setMid(obj.codeFact+"",10)+ '</span>';
          }
        },
        {
          "mData": "montoFact",
          "bSortable": false,
          "mRender":  function(data, type, obj) {
                return '<span class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+obj.montoFact+ ' <?php echo $CONFIG['MONEDA_SYSTEM'];?>">'+setMid(obj.montoFact+"<?php echo $CONFIG['MONEDA_SYSTEM'];?>",10)+ '</span>';
          }
        },
        {
           "mData": "acci",
           "bSortable": false ,
           "sClass": "control center",
           "mRender":  function(data, type, obj) {
                var btnEdit="";
                var btnComent="";
                var btnStatus="";
                var url ="";
                var btnRecorrd="";

                <?php if($OrdAdminSt){?>
                url ="changeStOrdAdmin({id:\""+obj.id_orden+"\",sts:\""+obj.id_status+"\",name:\""+obj.name+"\",tipO:\""+obj.id_tipo_orden+"\"})";

                btnStatus= "<a class='maring0 popovers btn btn green btn-per btn-sm link-blanco' href='javascript:void(0)' onclick='"+url+"' data-content='Orden "+StrTrim(obj.estatus)+"' data-trigger='hover' data-placement='top'> <i class='"+setIconOrd(StrTrim(obj.id_status))+"'></i></a>";
                <?php } ?>

                <?php if($OrdAdminTime){?>
                btnComent= "<a class='maring0 btn btn-per bg-yellow btn-sm link-blanco' href='javascript:void(0)' onclick='timeLineOrdServicios({id:\""+obj.id_orden+"\",sts:\""+obj.id_status+"\",tipoOR:\""+obj.tipo_orden+"\",tipoc:\""+obj.tipoc+"\",admin:true,tipoLine:2})'";
                btnComent+="title='Comentarios'> <i class='<?php echo getImgIcon("coment");?>'></i></a>";
                <?php } ?>

                btnRecorrd= "<a class='maring0 btn btn-per bg-purple-plum btn-sm link-blanco' href='javascript:void(0)' onclick='setRecordatorio({id:\""+obj.id_orden+"\",sts:\""+obj.id_status+"\"})'";
                btnRecorrd+="title='Agregar Recodatorio'> <i class='<?php echo getImgIcon("recordatorio");?>'></i></a>";


                return btnStatus+"&nbsp;"+btnEdit+"&nbsp;"+btnComent+"&nbsp;"+btnRecorrd;
           }
        }
    ]
});
tableWrapperAdOrdFact = TableAjaxAdOrdFact.parents('.dataTables_wrapper');

function searchadOrdFact(){
    var client_sa = $("#adOrd_client_fact").val(),
    problm_sa = $("#adOrd_prob_fact").val(),
    ini_sa = $("#adOrd_ini_fact").val(),
    fin_sa = $("#adOrd_fin_fact").val();

    var param= "&client="+client_sa+"&tipoc="+$("#adOrd_tipoc_fact").val()+"&problm="+problm_sa+"&f1="+ini_sa+"&f2="+fin_sa+"&sts=7";

    TableAjaxAdOrdFact.fnReloadAjax('modulos/administrativo_ordenes/controller.php?acc=load_ad_ord_facturadas'+param);
}

function limpiaradOrdFact(){

    $("#adOrd_prob_fact,#adOrd_ini_fact,#adOrd_fin_fact").val('');

    $("#adOrd_client_fact").select2("val", "");

    searchadOrdFact();
}

$('.date-picker-one,.date-picker-two').datepicker({
    language: 'es',
    autoclose: true
});


$('#adOrd_client_fact').select2({
    placeholder: "Buscar Cliente",
    minimumInputLength: 1,
    allowClear: true,
    ajax: {
        url:'modulos/administrativo_ordenes/controller.php?acc=load_client_ord&x='+Math.random(),
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
    formatInputTooShort:function (input, min) {
        var n = min - input.length;
        return "Introduzca " + n + " car" + (n == 1? "á" : "a") + "cter" + (n == 1? "" : "es"); }
    ,
    formatNoMatches: function () { return "Cliente sin Ordenes"; },
    formatSelection: function(a){
        if(a!=""){
            $("#adOrd_tipoc_fact").val(a.idT);
            searchadOrdFact();
            return  a.text;
        }
    },
    escapeMarkup: function (m) { return m; }
}).on("select2-removed", function(e) {
    $('#adOrd_client_fact').val("");
    $("#adOrd_tipoc_fact").val("");
    searchadOrdFact();
});
</script>