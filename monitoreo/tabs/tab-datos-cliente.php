<div class="tab-pane tab-datos fade tab-div " id="tabs-cierre-2">
	<div align='center' id="load_datos_cliente" style="display:none">
        <div class='splash-loader  text-center text-1x'>
            <img class='preload-medium' src='<?php echo $CONFIG['HOST'];?>img/loading-spinner-grey.gif' alt='' />
        </div><p class='lead text-center text-1x'>Cargando Datos del Cliente...</p>
    </div>
    <div class="row" id="table-datos-clientes">
        <div class="col-md-7">
            <div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-user"></i>Listado de Usuarios
                    </div>
                </div>
                <div class="portlet-body lista-data-user"  style="overflow:auto;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 0px;">
                    <div class="table-responsive">
                        <table class='table  table-striped  table-condensed table-bordered table-hover' style=' margin-bottom: 0px;font-size:13px;'>
                        <thead>
                            <tr>
                                <th align="left">ID</th>
                                <th>Nombre</th>
                                <th>Movil</th>
                                <th>Clave</th>
                                <th>Parentesco</th>
                                <th width="5px">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-usuarios-cli"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa  fa-compass"></i>Listado de Zonas
                    </div>
                </div>
                <div class="portlet-body lista-data-user"  style="overflow:auto;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 0px;">
                    <div class="table-responsive">
                        <table class='table  table-striped  table-condensed table-bordered table-hover' style=' margin-bottom: 0px;font-size:13px;'>
                        <thead>
                            <tr>
                                <th align="left">ID</th>
                                <th   align="left">Descripcion</th>
                                <th   align="left">Ubicacion</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-zonas-cli"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-5">
            <div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bell-o"></i>Numero de Emergencia
                    </div>
                </div>
                <div class="portlet-body" id="div-num-data" style="overflow:auto;padding-top: 0px;padding-bottom: 0px;padding-right: 0px;padding-left: 0px;">
                    <div class="table-responsive">
                        <table class='table  table-striped  table-condensed table-bordered table-hover' style=' margin-bottom: 0px;font-size:13px;'>
                        <thead>
                            <tr>
                                <th align="left">Descripcion</th>
                                <th   align="left">N&uacute;mero Contacto</th>
                                <th   align="left">Observacion</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-nummerge" class=" "></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>