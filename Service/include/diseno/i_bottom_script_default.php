<!--[if lt IE 9]>
<script src="<?php echo $CONFIG['HOST'];?>plugins/respond.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/excanvas.min.js"></script>
<![endif]-->


<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/flot/jquery.flot.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/flot/jquery.flot.pie.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/flot/jquery.flot.stack.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/flot/jquery.flot.crosshair.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery.blockui.min.js" type="text/javascript"></script>
<!--<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery.cokie.min.js" type="text/javascript"></script>-->
<script src="<?php echo $CONFIG['HOST'];?>plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>js/jquery.equalHeight.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootbox/bootbox.min.js"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-toastr/toastr.min.js"></script>
<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/fancybox/source/jquery.fancybox.pack.js"></script>

<script type="text/javascript" src="<?php echo $CONFIG['HOST'];?>plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/fullcalendar/lib/moment.min.js"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="<?php echo $CONFIG['HOST'];?>plugins/fullcalendar/lang-all.js"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/fuelux/js/spinner.min.js"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery.sortable.min.js"></script>

<script src="<?php echo $CONFIG['HOST'];?>plugins/jquery.pulsate.min.js"></script>

<!-- END PAGE LEVEL PLUGINS -->



<!-- BEGIN PAGE LEVEL SCRIPTS -->
<?php include("../js/metronic.js.php");?>
<script src="<?php echo $CONFIG['HOST'];?>js/layout.js" type="text/javascript"></script>
<?php include("../js/apptheme.js.php");?>
<?php include("../js/generales.js.php") ?>


<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   AppTheme.init();
 });
</script>
</body>

<!-- END BODY -->
</html>