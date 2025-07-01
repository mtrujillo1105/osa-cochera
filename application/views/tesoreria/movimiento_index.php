<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

<div class="container-fluid">
    
    <!--Busqueda-->
    <div class="card mb-4 mt-4">
        <div class="card-header">
          <h3 class="card-title">REPORTE MOVIMIENTOS DE CAJA</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
            <form id="form_busqueda" method="post">
                
                <!--Primera Fila-->
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_codigo">CÓDIGO DE CAJA</label>
                        <select name="search_codigo" id="search_codigo" class="form-control w-porc-90 h-2">
                            <option value=""> :: TODOS :: </option> <?php
                            if ($caja != NULL){
                                foreach ($caja as $indice => $val){ ?>
                                    <option value="<?=$val->CAJA_Codigo;?>"><?=$val->CAJA_Nombre;?></option> <?php
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_descripcion">NOMBRE DE CAJA</label>
                        <input type="text" name="search_descripcion" id="search_descripcion" value="" 
                               placeholder="Nombre de la caja" class="form-control h-1 w-porc-90"/>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_tipo">TIPO DE CAJA</label>
                        <select name="search_tipo" id="search_tipo" class="form-control w-porc-90 h-2 w-porc-90">
                            <option value=""> :: TODAS :: </option> <?php
                            if ($tipo_caja != NULL){
                                foreach ($tipo_caja as $indice => $val){ ?>
                                    <option value="<?=$val->tipCa_codigo;?>"><?=$val->tipCa_Descripcion;?></option> <?php
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_fechai">FECHA INICIO</label>
                        <input type="date" name="search_fechai" id="search_fechai" class="form-control h-1 w-porc-90"/>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_fechaf">FECHA FIN</label>
                        <input type="date" name="search_fechaf" id="search_fechaf" class="form-control h-1 w-porc-90"/>
                    </div>  
                </div>
                <!--Fin Primera Fila-->
                
                <!--Segunda Fila-->
                <div class="row form-group">
                    <button type="button" class="btn btn-info" id="imprimir">Reporte</button>
                    <button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' 
                            data-target='#add_movimiento'>Nuevo</button>                    
                    <button type="button" class="btn btn-dark" id="limpiarC">Limpiar</button>
                    <button type="button" class="btn btn-warning" id="buscarC">Buscar</button>	
                </div>
                <!--Fin Segunda Fila-->
                
            </form>
        </div>
    </div>    
    <!--Fin Busqueda-->
    
    <!--Resultado-->
    <div class="card mb-4">
        <!--div class="card-header">RELACIÓN DE MOVIMIENTOS</div-->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="table-movimiento" width="100%" cellspacing="0">
                    <!--div id="cargando_datos" class="loading-table">
                         <img src="< ?=base_url().'public/images/loading.gif?='.IMG;?>">
                     </div-->  
                    <thead>
                        <tr>
                            <td style="width:20%" data-orderable="true" title="Fecha de registro.">FECHA REG.</td>
                             <td style="width:10%" data-orderable="true" title="Fecha de movimiento.">FECHA MOV.</td>
                             <td style="width:10%" data-orderable="true" title="Código de la caja">CÓDIGO</td>
                             <td style="width:20%" data-orderable="true" title="Nombre de la caja">NOMBRE</td>
                             <td style="width:10%" data-orderable="true" title="Moneda del movimiento">MONEDA</td>
                             <td style="width:10%" data-orderable="true" title="Importe del movimiento">MONTO</td>
                             <td style="width:10%" data-orderable="true" title="Tipo de movimiento">MOVIMIENTO</td>
                             <td style="width:05%" data-orderable="false" title=""></td>
                             <td style="width:05%" data-orderable="false" title=""></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>    
    <!--Fin Resultado-->
    
</div>

<!--MODAL REGISTRAR MOVIMIENTO -->
<div id="add_movimiento" class="modal fade" role="dialog">
    
    <div class="modal-dialog w-porc-60">
        <div class="modal-content">
            <form id="formMovimiento" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title text-center"><span id="modal_titulo">REGISTRAR</span> MOVIMIENTO</h4>
                </div>
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="movimiento" name="movimiento" value="">

                    <div class="row form-group">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="caja">CAJA</label>
                            <select name="caja" id="caja" class="form-control w-porc-90 h-3"> <?php
                                if ($caja != NULL){
                                    foreach ($caja as $indice => $val){ ?>
                                        <option value="<?=$val->CAJA_Codigo;?>"><?=$val->CAJA_Nombre;?></option> <?php
                                    }
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="tipo_movimiento">MOVIMIENTO</label>
                            <select name="tipo_movimiento" id="tipo_movimiento" class="form-control w-porc-90 h-3">
                                <option value="1">INGRESO</option>
                                <option value="2">SALIDA</option>
                            </select>
                        </div>

                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="fecha">FECHA *</label>
                            <input type="date" id="fecha" name="fecha" class="form-control h-2" value=""/>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="forma_pago">FORMA DE PAGO</label>
                            <select name="forma_pago" id="forma_pago" class="form-control w-porc-90 h-3"> <?php
                                if ($forma_pago != NULL){
                                    foreach ($forma_pago as $indice => $val){ ?>
                                        <option value="<?=$val->FORPAP_Codigo;?>"><?=$val->FORPAC_Descripcion;?></option> <?php
                                    }
                                } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="moneda">MONEDA</label>
                            <select name="moneda" id="moneda" class="form-control w-porc-90 h-3"> <?php
                                if ($moneda != NULL){
                                    foreach ($moneda as $indice => $val){ ?>
                                        <option value="<?=$val->MONED_Codigo;?>"><?="$val->MONED_Simbolo | $val->MONED_Descripcion";?></option> <?php
                                    }
                                } ?>
                            </select>
                        </div>

                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="importe">MONTO *</label>
                            <input type="number" step="1" min="0" id="importe" name="importe" class="form-control h-2 w-porc-90" placeholder="Total" value=""/>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <label for="justificacion">JUSTIFICACIÓN *</label>
                            <textarea class="form-control h-5" id="justificacion" name="justificacion" placeholder="Indique una justificación" maxlength="800"></textarea>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <label for="obs_movimiento">OBSERVACIÓN</label>
                            <textarea class="form-control h-5" id="obs_movimiento" name="obs_movimiento" placeholder="Indique una observación" maxlength="800"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success registrar_movimiento" accesskey="x" onclick="registrar_movimiento()">Guardar Registro</button>
                    <button type="button" class="btn btn-info" onclick="clean()">Limpiar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--FIN MODAL REGISTRAR MOVIMIENTO -->