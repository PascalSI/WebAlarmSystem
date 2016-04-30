<div class="tab-pane tab-cierre fade tab-div container-fluid" id="tabs-cierre-3">
    <div><strong>Qu&eacute; Se&ntilde;ales</strong></div>
    <div class="row">
        <div class="col-xs-10 bordered">
            <div class="row">
                <div class="col-xs-12">
                    <?php if($_GET["fast"]==0){?>
                    <div class="col-xs-4">
                        <div class="control-group">
                            <div class="controls">
                                <label class="radio check-cierre">
                                    <input type="radio" class="radio-cierre" name="que_signales" value="1" id="que_signales_0"
                                    <?php if(PermModAccUser(1,52)){?>
                                        onclick="setTipo(1)"
                                    <?php }else{?>
                                        disabled="disabled"
                                    <?php } ?>
                                   /> Señales Seleccionadas
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-xs-4">
                        <div class="control-group">
                            <div class="controls">
                                <label class="radio check-cierre">
                                    <input  type="radio"  class="radio-cierre" name="que_signales" value="2" id="que_signales_1"
                                    <?php if(PermModAccUser(1,53)){?>
                                        onclick="setTipo(2)"
                                    <?php }else{?>
                                        disabled="disabled"
                                    <?php } ?>
                                   /> Por Cliente
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="control-group">
                            <div class="controls">
                                <label class="radio check-cierre">
                                    <input  type="radio"  class="radio-cierre" name="que_signales" value="2" id="que_signales_1"
                                    <?php if(PermModAccUser(1,122)){?>
                                        onclick="setTipo(5)"
                                    <?php }else{?>
                                        disabled="disabled"
                                    <?php } ?>
                                   /> Por Codigo Señal
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="control-group">
                            <div class="controls">
                                <label class="radio check-cierre">
                                    <input type="radio"  class="radio-cierre" name="que_signales" value="3" id="que_signales_2"
                                    <?php if(PermModAccUser(1,54)){?>
                                        onclick="setTipo(3)"
                                    <?php }else{?>
                                        disabled="disabled"
                                    <?php } ?>
                                   /> Por Codigo de Evento
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="control-group">
                            <div class="controls">
                                <label class="radio check-cierre">
                                    <input  type="radio"  class="radio-cierre" name="que_signales" value="4" id="que_signales_3"
                                    <?php if(PermModAccUser(1,55)){?>
                                        onclick="setTipo(4)"
                                    <?php }else{?>
                                        disabled="disabled"
                                    <?php } ?>
                                   /> TODAS LAS SEÑALES
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row ">
        <div id="tablesignal" class="col-xs-12 bordered" style="margin-top:10px;height:100px; overflow:auto">
            <table  width="100%" class=' table-striped table-hover'  style="font-size:13px">
                <thead>
                    <tr id="tr-cierre">
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Se&ntilde;al</th>
                    </tr>
                </thead>
                <tbody id="tbody-cierres">
                    <tr>
                        <td>&nbsp;
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row ">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-10">
                    <div class="row">
                        <div class="col-xs-12 paddleft0 top-5">
                            <select class="form-control select-mot"  onchange="setMenPredefinido(this.value,'cierre-comentario-2')">
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
                        <div class="col-xs-12 paddleft0 top-5">
                            <textarea rows="2" id="cierre-comentario-2" class="form-control text-coment" placeholder="Ingrese Comentario"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2 top-5">
                    <button class="btn btn-large btn-primary" id="btn-procesar" onClick="procesar_cierre()"  type="button">
                        <i class="<?php echo getImgIcon("guardar");?>"></i><br/>
                        &nbsp;Procesar
                    </button>
                    <br/>
                    <div id="msj-enviado" style="display:none">
                        Enviando...
                    </div>
                </div>
            </div>
            <div id="div-error"  style="display:none" class="alert alert-danger marginTop10 paddingAll5">
                <strong>Ups!!</strong>
                <span id="text-error"></span>
             </div>
        </div>
    </div>
</div>