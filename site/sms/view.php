<div class="row">
	<div class="col-md-12">
		<div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa marginTop1  fa-envelope-o"></i>
					<span id="title-sms">SMS Enviados</span>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-container">
					<table class="table table-hover table-striped table-condensed"  id="datatablesSms">
                        <thead>
                           <tr role="row" class="heading">
                                <th>M&oacute;vil</th>
	                            <th>Hora</th>
	                            <th>SMS</th>
                            </tr>
                        </thead>
                        <tbody></tbody> <!-- source with ajax -->
                    </table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var oTableSms = null;
var btn_pdfSignal='<button onclick="ExportPdfSMS()" class="btn font-8 <?php echo getClassIcon('pdf')?>">';
	btn_pdfSignal+='<i id="mini-icon" class="<?php echo getImgIcon('pdf')?>"></i></button>';

var btn_excelSignal='<button onclick="ExportExcelSMS()" class="btn font-8 <?php echo getClassIcon('excel')?>">';
	btn_excelSignal+='<i id="mini-icon" class="<?php echo getImgIcon('excel')?>"></i></button>';

var view_fecha_ = "&nbsp;&nbsp;&nbsp; <select  style='margin-bottom:8px;' id='selec_date' class='form-control  input-inline' onchange='changeDate()'>";

<?php

$query= new Querys();
$conex = $query->getConection($CONFIG);

$fechaAux = "";

$MyrecordSMS = $query->SelDB($conex,"site_sel_UltFecReportSMS",array($idCliente));
if($query->count_row($MyrecordSMS)){
	while($rSMS=$query->getdata_object($MyrecordSMS)){
		$fecha =  date_format($rSMS->fech,"d/m/Y");
	?>
		view_fecha_+="<option  value='<?php echo $fecha?>'><?php echo $fecha;?></option>";
	<?php
		if($fechaAux==""){
			$fechaAux = $fecha;
		}
	}
}else{
		$fechaAux = date("d/m/Y");
}

?>
view_fecha_+="</select>&nbsp;&nbsp;"+btn_excelSignal+"&nbsp;&nbsp;"+btn_pdfSignal;

function initialize_table_sms(){
	oTableSms = $("#datatablesSms").dataTable({
	    "aaSorting": [[1, "asc" ]],
	    "fnDrawCallback" : function(a) {
    		var text = a.jqXHR.responseJSON.date;
			$("#title-sms").html(text);
	    },
	     "dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'<'div_st'>>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
	    "sAjaxSource": '<?php echo $CONFIG['HOST'];?>site/sms/controller.php?acc=load_sms&fech=<?php echo $fechaAux;?>&id=<?php echo $idCliente;?>',
	    "aoColumns": [
	        { "mData": "m" },
	        { "mData": "fech" },
	        { "mData": "sms" }
	    ]
	});

	$("div.div_st").attr("align","right");
	$("div.div_st").html(view_fecha_);
}

function changeDate(){
	var date = $("#selec_date").val();
	oTableSms.fnReloadAjax('<?php echo $CONFIG['HOST'];?>site/sms/controller.php?acc=load_sms&fech='+date+'&id=<?php echo $idCliente;?>');
}

function ExportPdfSMS(){
	var date = $("#selec_date").val();
	var url ='../site/sms/pdf.php?acc=load_sms&fech='+date+'&id=<?php echo encode(5,$idCliente);?>';

	window.open(url,'','');
}

function ExportExcelSMS(){
	var date = $("#selec_date").val();
	var url ='../site/sms/excel.php?acc=load_sms&fech='+date+'&id=<?php echo encode(5,$idCliente);?>';

	window.open(url,'','');
}
</script>