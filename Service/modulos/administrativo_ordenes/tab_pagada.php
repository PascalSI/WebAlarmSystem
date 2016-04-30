<form onsubmit="return false;" class="horizontal-form" id="form-admin-ord-search-pagada">
    <div class="form-body paddingAll1">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Cliente</label>
                    <input type="hidden" name="adOrd_client_pag" id="adOrd_client_pag" class="form-control"/>
                    <input type="hidden" name="adOrd_tipoc_pag" id="adOrd_tipoc_pag" class="form-control"/>
                </div>
            </div>
            <!--/span-->
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Problema</label>
                    <input type="text" class="form-control" id="adOrd_prob_pag" maxlength="100" name="adOrd_prob_pag" placeholder="Problema">
                </div>
            </div>
            <!--/span-->
            <div class="col-md-4">
                <label class="control-label">Fecha de Pago</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group   date date-picker date-picker-one" data-date-format="dd/mm/yyyy" >
                            <input type="text" class="form-control" id="adOrd_ini_pag" value="<?php echo diaFirst();?>" name="adOrd_ini_pag" readonly placeholder="Desde">
                            <span class="input-group-btn">
                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="input-group   date date-picker date-picker-two" data-date-format="dd/mm/yyyy" >
                            <input type="text" class="form-control" id="adOrd_fin_pag" value="<?php echo diaLast();?>"  name="adOrd_fin_pag" readonly placeholder="Hasta">
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
                     <button type="button" title="Buscar Orden" class="<?php echo getClassIcon("buscar");?> marginTopBtnFrom"  onclick="searchadOrdPag()">
                        <i class="<?php echo getImgIcon("buscar");?>"></i>
                    </button>
                    <button type="button" title="Limpiar Buscador" class="<?php echo getClassIcon("limpiar");?> marginTopBtnFrom" onclick="limpiaradOrdPag()">
                        <i class="<?php echo getImgIcon("limpiar");?>"></i>
                    </button>
                </div>
            </div>
            <!--/span-->
        </div>
        <!--/row-->
    </div>
</form>
<table class="table table-striped table-condensed table-hover" id="datatable_ajaxAdOrdPag">
<thead>
<tr role="row" class="heading">
    <th width="10"></th>
    <th width="50">Orden</th>
    <th width="">Cliente</th>
    <th width="">Problema</th>
    <th width="">Fecha Pago</th>
    <th width="">Identificador</th>
    <th width="">Tipo</th>
    <th width="100">Acciones</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
<script>
var tableWrapperAdOrdPag= null;
var TableAjaxAdOrdPag = $("#datatable_ajaxAdOrdPag").dataTable({
    "sDom": "<'row'<'col-md-6 col-sm-12'>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
    "aaSorting": [[ 1, "asc" ]],
    "sAjaxSource": 'modulos/administrativo_ordenes/controller.php?acc=load_ad_ord_pagadas&client=&tipoc=&sts=8&f1=<?php echo diaFirst();?>&f2=<?php echo diaLast();?>', // ajax source
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
                    url="<%=PAHT_httpAux%>admin/PanelCliente.php?q="+StrTrim(obj.id_crip)+"&mod=showOrdenes" ;
                    url_text = 'Ver Historial del Cliente: '+obj.nombre+ '">'+setMid(obj.nombre,40);
                }else if(obj.tipoc=="2"){
                    url="<%=PAHT_HTTP_SERVICE%>Admin/PanelCliente.php?q="+StrTrim(obj.id_crip)+"&mod=showOrdenes";
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
          "mData": "fechaPago",
          "bSortable": false
        },
        {
          "mData": "identiPago",
          "bSortable": false,
          "mRender":  function(data, type, obj) {
                return '<span class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+obj.identiPago+ '">'+setMid(obj.identiPago+"",10)+ '</span>';
          }
        },
        {
          "mData": "formaPago",
          "bSortable": false,
          "mRender":  function(data, type, obj) {
                return '<span class="padding0 popovers" data-trigger="hover" data-placement="top" data-content="'+obj.formaPago+ ' ">'+setMid(obj.formaPago+"",20)+ '</span>';
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

                <?php if($OrdAdminSt){?>
                url ="alertInfo({title:\"Notificaci&oacute;n\",text:\"Orden ya llego a su final.\"})";

				btnStatus= "<a class='maring0 popovers green btn btn-per btn-sm link-blanco' href='javascript:void(0)' onclick='"+url+"'";
				btnStatus+="data-content='Orden "+StrTrim(obj.estatus)+"' data-trigger='hover' data-placement='top'> <i class='"+setIconOrd(StrTrim(obj.id_status))+"'></i></a>";
                <?php } ?>

                <?php if($OrdAdminTime){?>
                btnComent= "<a class='maring0 btn btn-per bg-yellow btn-sm link-blanco' href='javascript:void(0)' onclick='timeLineOrdServicios({id:\""+obj.id_orden+"\",sts:\""+obj.id_status+"\",tipoOR:\""+obj.tipo_orden+"\",tipoc:\""+obj.tipoc+"\",admin:true,tipoLine:2})'";
                btnComent+="title='Comentarios'> <i class='<?php echo getImgIcon("coment");?>'></i></a>";
                <?php } ?>


                return btnStatus+"&nbsp;"+btnEdit+"&nbsp;"+btnComent;
           }
        }
    ]
});
tableWrapperAdOrdPag = TableAjaxAdOrdPag.parents('.dataTables_wrapper');

function searchadOrdPag(){
    var client_sa = $("#adOrd_client_pag").val(),
    problm_sa = $("#adOrd_prob_pag").val(),
    ini_sa = $("#adOrd_ini_pag").val(),
    fin_sa = $("#adOrd_fin_pag").val();

    var param= "&client="+client_sa+"&tipoc="+$("#adOrd_tipoc_pag").val()+"&problm="+problm_sa+"&f1="+ini_sa+"&f2="+fin_sa+"&sts=8";

    TableAjaxAdOrdPag.fnReloadAjax('modulos/administrativo_ordenes/controller.php?acc=load_ad_ord_pagadas'+param);
}

function limpiaradOrdPag(){

    $("#adOrd_prob_pag,#adOrd_ini_pag,#adOrd_fin_pag").val('');

    $("#adOrd_client_pag").select2("val", "");

    searchadOrdPag();
}

$('.date-picker-one,.date-picker-two').datepicker({
    language: 'es',
    autoclose: true
});


$('#adOrd_client_pag').select2({
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
            $("#adOrd_tipoc_pag").val(a.idT);
            searchadOrdPag();
            return  a.text;
        }
    },
    escapeMarkup: function (m) { return m; }
}).on("select2-removed", function(e) {
    $('#adOrd_client_pag').val("");
    $("#adOrd_tipoc_pag").val("");
    searchadOrdPag();
});
</script>