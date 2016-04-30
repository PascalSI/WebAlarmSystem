<div class="tab-pane tab-datos fade tab-div container-fluid" id="tabs-cierre-6" align="center">
    <div class="row margtop4x">
        <div class="col-xs-6">
            <div class="portlet box divDialog <?php echo $CONFIG['WEB_THEME'];?>">
                <div class="portlet-title page-breadcrumb breadcrumb marginTop1">
                    <div class="caption">
                        <i class="fa fa-bookmark marginTop1"></i>Nota Fija
                    </div>
                </div>
                <div class="portlet-body">
                    <textarea rows="10" class="form-control" id="notfija" placeholder="Ingrese Nota Fija" readonly><?php echo $NotaFija;?></textarea>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="portlet box divDialog <?php echo $CONFIG['WEB_THEME'];?>">
                <div class="portlet-title page-breadcrumb breadcrumb marginTop1">
                    <div class="caption">
                        <i class="glyphicon glyphicon-list-alt marginTop1"></i>Nota Temporal
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <span class="pull-left">Desde:</span>
                            <input type="text" class="form-control" id="desde"  name="desde" placeholder="Desde" readonly value="<?php echo $NotaIni;?>" />
                        </div>
                        <div class="col-xs-6">
                            <span class="pull-left">Hasta:</span>
                            <input type="text" class="form-control" id="hasta"  name="hasta" placeholder="Hasta" readonly value="<?php echo $NotaFin;?>" />
                        </div>
                    </div>
                    <div class="top-5">
                        <textarea rows="7" class="form-control" id="notTemp" placeholder="Ingrese Nota Temporal" readonly><?php echo $NotaTemp;?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>