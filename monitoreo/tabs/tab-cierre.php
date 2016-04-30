<div class="tab-pane tab-cierre tab-div fade in active container-fluid" id="tabs-cierre-1"  style="padding-left: 0px; padding-right: 0px;">
    <div class="row">
        <div class="col-md-6">
            <div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa  fa-comment " style="margin-top: 0px;"></i>HISTORIAL DE LA SEÑAL
                    </div>
                </div>
                <div class="portlet-body" style="padding: 0px; overflow:auto;height: 120px;"  id="tablesignalHistory">
                    <table  width="100%" class='table  table-striped  table-condensed table-bordered table-hover'  style="font-size:13px;margin-bottom: 0px;">
                        <thead>
                            <tr>
                                <th width='20%'>Fecha</th>
                                <th>Observación</th>
                                <th>Operador</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-coment-signal">
                            <?php
                                $ObserSignail = $query->SelDB($conex,"site_sel_SignalesObservacion",array(trim($_GET["trama"])));

                                while($ro=$query->getdata_object($ObserSignail)){
                                    ?>
                                        <tr>
                                            <td><?php echo date_format($ro->fecha,"d/m/Y H:i:s");?></td>
                                            <td><?php echo $ro->observacion;?></td>
                                            <td><?php echo $ro->nombre;?></td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6" >
            <div class="portlet box <?php echo $CONFIG['WEB_THEME'];?>">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bars "></i>SE&Ntilde;ALES SIN PROCESAR
                </div>
            </div>
            <div class="portlet-body" style="padding: 0px; overflow:auto;height: 120px;"  id="tablesignalClient">
                <table  width="100%" class='table  table-striped  table-condensed table-bordered table-hover'  style="font-size:13px;margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th width='25px'><input type='checkbox' onclick="check_all(this)" id-class="check-id-cierre" /></th>
                            <th width='20%'>Fecha</th>
                            <th>Se&ntilde;al</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-signal-client">
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="col-xs-3 top-5 "><b>MEN. PREDEFINIDO:</b></div>
            <div class="col-xs-9 top-5">
                <select class="form-control input-sm select-mot"  onchange="setMenPredefinido(this.value,'cierre-comentario')">
                    <option value="">--Seleccione--</option>
                    <?php
                        $MyRecordPre2 = $query->SelDB($conex,"site_sel_MonitoreoMenPredefinidos");

                        while($rp=$query->getdata_object($MyRecordPre2)){
                            ?>
                                <option  value="<?php echo $rp->Mensaje;?>"><?php echo $rp->Mensaje;?></option>
                            <?php
                        }
                    ?>
                </select>
             </div>
            <div class="col-xs-3 top-5 "><b>COMENTARIO:</b></div>
            <div class="col-xs-9 top-5">
                <textarea  id="cierre-comentario" rows="2" class="form-control text-coment" ></textarea>
            </div>
        </div>
        <div class="col-md-3">
            <input type="hidden" id="cierre-id"  />
            <button class="btn btn-block red-thunderbird" id="tb-cierre-btn-cancelar" type="button" title="Cancelar"  onClick="window.parent.tb_remove(true)">
                <i class="glyphicon glyphicon-remove "></i> Cancelar
            </button>
            <?php
                if($_GET["tipo"]!=2 && $_GET["tipo"]!=3){
                    $formatClose = "1";
                    $fnClick = "setPendiente()";
                    $titlePen = "Pasan a pendiente";
                    $textBoton = "Pendiente";
                }else{
                    //forma de cierre al Procesar
                    $formatClose = "2";
                    $fnClick = "addObservacion()";
                    $titlePen = "Agregar Comentario";
                    $textBoton = "Comentario";
                }

            ?>

            <button class="btn btn-block yellow-gold" id="tb-cierre-btn-pendiente" title="<?php echo $titlePen;?>" onClick="<?php echo $fnClick;?>" type="button">
                <i class="fa  fa-eye"></i> <?php echo $textBoton;?>
            </button>

            <button class="btn btn-block btn-primary" id="tb-cierre-btn-procesar" title="Procesar" onClick="cierreSignal('<?php echo $formatClose;?>')" id="btn-save-cierre" type="button">
                <i class="<?php echo getImgIcon("guardar");?>"></i> Procesar
             </button>
        </div>
    </div>
</div>