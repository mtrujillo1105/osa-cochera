<div class="container-fluid">
    
    <!--Busqueda-->
    <div class="card mb-4 mt-4">
        <div class="card-header"><?=$titulo;?></div>
        <div class="card-body">
            <form id="form_busqueda" method="post">
                <div class="row">
                    <div class="col-lg-3 form-group">
                      <label for="nombre_rol">CÓDIGO</label>
                      <input type="text" name="search_codigo" id="search_codigo" value="" placeholder="Código de caja" class="form-control"/>
                    </div>
                    <div class="col-lg-3 form-group">
                      <label for="nombre_rol">NOMBRE</label>
                      <input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Nombre de caja" class="form-control"/>
                    </div>
                    <!--div class="col-sm-3 col-md-3 col-lg-3">
                        <label for="nombre_rol">TIPO CAJA</label>
                        <select name="search_tipo" id="search_tipo" class="form-control w-porc-90 h-2 w-porc-90">
                            <option value=""> :: TODAS :: </option> <?php
                            if ($tipo_caja != NULL){
                                foreach ($tipo_caja as $indice => $val){ ?>
                                    <option value="<?=$val->tipCa_codigo;?>"><?=$val->tipCa_Descripcion;?></option> <?php
                                }
                            } ?>
                        </select>
                    </div-->                    
                    <div class="col-lg-6 col-md-6 form-group align-self-end text-right">
                      <button type="button" class="btn btn-info" id="buscarC">Buscar</button>
                      <button type="button" class="btn btn-dark" id="limpiarC">Limpiar</button>
                      <?php if($rolusu == 1 || $rolusu == 7000){?>
                        <button type="button" class="btn btn-success" id="nuevo">Nuevo</button>
                      <?php }?>
                    </div>                    
                </div>
            </form>
        </div>
    </div>  
    <!--Fin Busqueda-->
    
    <!--Resultado-->
    <div class="card mb-4">
        <div class="card-header">RELACIÓN DE CAJAS</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="table-caja" width="100%" cellspacing="0">
                    <!--div id="cargando_datos" class="loading-table">
                         <img src="<?=base_url().'public/images/loading.gif?='.IMG;?>">
                     </div-->                    
                    <thead>
                        <tr>
                         <td style="width:10%" data-orderable="false">N°</td>
                         <td style="width:10%" data-orderable="true">CÓDIGO</td>
                         <td style="width:30%" data-orderable="true">NOMBRE</td>
                         <td style="width:30%" data-orderable="true">CAJERO</td>
                         <td style="width:15%" data-orderable="true">SITUACION</td>
                         <td style="width:05%" data-orderable="false"></td>
                         <td style="width:05%" data-orderable="false"></td>                          
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <!--Fin Resultado-->
    
</div>

<!--MODAL REGISTRAR CAJA-->
<div id="add_caja" class="modal fade" role="dialog">
    <div class="modal-dialog w-porc-60">
        <div class="modal-content">
            <form id="formCaja" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title text-center">REGISTRAR CAJA</h4>
                </div>
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="caja" name="caja" value="">

                    <div class="row form-group">
                        <div class="col-md-6 col-lg-6">
                            <label for="codigo_caja">CÓDIGO</label>
                            <input type="text" id="codigo_caja" name="codigo_caja" class="form-control h-2 w-porc-90" placeholder="Indique el codigo" value="" maxlength="30">
                        </div>
                        
                        <div class="col-md-6 col-lg-6">
                            <label for="nombre_caja">NOMBRE *</label>
                            <input type="text" id="nombre_caja" name="nombre_caja" class="form-control h-2" placeholder="Indique la caja" value="" maxlength="200">
                        </div>

                        <!--div class="col-md-4 col-lg-4">
                            <label for="tipo_caja">TIPO</label>
                            <select name="tipo_caja" id="tipo_caja" class="form-control w-porc-90 h-3"> <?php
                                if ($tipo_caja != NULL){
                                    foreach ($tipo_caja as $indice => $val){ ?>
                                        <option value="<?=$val->tipCa_codigo;?>"><?=$val->tipCa_Descripcion;?></option> <?php
                                    }
                                } ?>
                            </select>
                        </div-->
                        
                    </div>
                    
                    <div class="row form-group">
                        
                        <div class="col-md-12 col-lg-6">
                            <label for="cajero_caja">CAJERO</label>
                            <?php
                            if($rolusu == 1 || $rolusu == 7000){
                            ?>
                                <select name="cajero_caja" id="cajero_caja" class="form-control w-porc-90 h-3"> 
                                    <option value="">:Seleccione::</option>
                                    <?php
                                    if($cajeros != NULL){
                                        foreach($cajeros as $value){
                                            ?>
                                            <option value="<?php echo $value->USUA_Codigo;?>"><?php echo $value->PERSC_Nombre." ".$value->PERSC_ApellidoPaterno." ".$value->PERSC_ApellidoMaterno;?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            <?php
                            }
                            else{
                                ?>
                                    <input type="hidden" id="cajero_caja" name="cajero_caja">
                                    <input type="text" id="nombrecajero_caja" name="nombrecajero_caja" class="form-control w-porc-90 h-3" 
                                           placeholder="Nombre cajero">
                                <?php
                            }
                            ?>
                        </div>                        
                        
                        <div class="col-md-12 col-lg-6">
                            <label for="estado_caja">SITUACIÓN</label>
                            <select name="estado_caja" id="estado_caja" class="form-control w-porc-90 h-3"> 
                                <option value="1"  selected="selected">ABIERTA</option>
                                <option value="0">CERRADA</option>
                            </select>
                            <input type="hidden" id="estado_caja_ant" name="estado_caja_ant">
                        </div>                        
                        
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <label for="obs_caja">OBSERVACIONES</label>
                            <textarea class="form-control h-5" id="obs_caja" name="obs_caja" placeholder="Indique las observaciones" maxlength="800"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" accesskey="x" onclick="registrar_caja()">Guardar Registro</button>
                    <!--button type="button" class="btn btn-info" onclick="clean()">Limpiar</button-->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--FIN MODAL REGISTRAR CAJA-->