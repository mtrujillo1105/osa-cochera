<div class="container-fluid">
    <div class="row">

        <!--Columna Izquierda-->
        <div class="col-lg-6 col-md-12" style="overflow: auto;height: 80vh;">
            <div class="card mb-4 mt-4">
                    
                <div class="card-header">
                    <!--VEHICULOS EN EL ESTACIONAMIENTO-->
                    <div class="row">
                        <div class="col-lg-2 col-sm-2 mt-1"><h6>PLACA: </h6></div>	

                        <div class="col-lg-3 col-sm-4">
                            <input type="text" id="txt_placa" id="txt_placa" class="form-control form-control-sm w-70 text-uppercase" maxlength="6"/>                                   <input type="hidden" name="usr_hour" id="usr_hour"/>
                            <input type="hidden" name="usr_date" id="usr_date"/>
                        </div>

                        <!--div class="col-lg-2 col-md-2 mt-1">LECTORA: </div-->
                        
                        <!--div class="col-lg-3 col-md-3">
                            <input type="password" id="txt_codparqueo" name="txt_codparqueo" class="form-control form-control-sm w-70 text-uppercase" maxlength="6"/>     
                        </div-->                        

                        <div class="col-lg-7 col-sm-6 text-right mt-1">
                            <!--button type="button" class="btn btn-success" id="nuevo">Agregar</button-->
                            <a href="#">
                                <img src="<?php echo base_url();?>public/images/icons/pdf.png" width="22" height="22" 
                                     class="imgBoton img-fluid imprimir_tickets_activos" id="pdf">
                            </a>

                            <!--a href="#">
                                <img src="<?php echo base_url();?>public/images/icons/xls.png" width="25" height="25" 
                                     class="imgBoton img-fluid imprimir_tickets_activos" id="excel">
                            </a-->

                        </div>
                    </div>
                    
                    <form id="frmParqueo" method="post">
                        <input type="hidden" id="tipo_reporte" name="tipo_reporte">     
                    </form>    

                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="dataTable-parqueo" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <td style="width:05%" data-orderable="true"></td>
                                    <td style="width:20%" data-orderable="true">PLACA</td>
                                    <td style="width:20%" data-orderable="true">F.INGRESO</td>
                                    <td style="width:20%" data-orderable="true">H.INGRESO</td>
                                    <td style="width:20%" data-orderable="true">F.COMPLETA</td>
                                    <td style="width:20%" data-orderable="true">TARIFA</td>
                                </tr>
                            </thead>
                            <tbody style="cursor: pointer;"></tbody>
                        </table>
                    </div>
                </div>            
                
            </div>
        </div>
         <!--/Columna Izquierda-->

        <!--Columna Derecha-->
        <div class="col-lg-6 col-md-12">
            
            <div class="card mb-2 mt-4">
                <div class="card-header">
                    <div id="clockDiv" class="mx-auto"></div>
                </div>
            </div>
            
            <!--Monitor tarifas planas y abonados-->
            <div class="card mb-4">    
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="dataTable-monitor">
                            <thead>
                                <tr>
                                    <td data-orderable="true">PLACA</td>
                                    <td data-orderable="true">H.INGRESO</td>
                                    <td data-orderable="true">TARIFA</td>
                                    <td data-orderable="true">HORARIO</td>
                                    <td data-orderable="true">SITUACIÓN</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!--Monitor abonados-->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="dataTable-abonados">
                            <thead>
                                <tr>
                                    <td data-orderable="true">NÚMERO</td>
                                    <td data-orderable="true">NOMBRE</td>
                                    <td data-orderable="true">F.INGRESO</td>
                                    <td data-orderable="true">SITUACIÓN</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>                
            </div>
            
        </div>	
        <!--/Columna Derecha-->

    </div>
</div>

<!-- MODAL INGRESO DE VEHICULOS-->
<div id="agregar_parqueo" class="modal fade">
    
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!--Modal Header-->
            <div class="modal-header" >
                <div class="col-lg-8">
                    <h2 class="text-primary">ENTRADA DE VEHICULOS</h2>        
                </div>
                <div class="col-lg-4 text-right">
                    <h2 class="text-primary" id="usr_hour_add"></h2>
                </div>
            </div>
            <!--/Modal Header-->

            <form id="formParqueo" method="POST">

                <!--Moda Body-->
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="parqueo" name="parqueo">
                    <input type="hidden" id="contacto" name="contacto">

                    <div class="row">

                        <!--Primera columna-->
                        <div class="col-md-7 col-lg-7">
                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    <label for="parqueo_placa">PLACA</label>
                                    <input type="text" id="parqueo_placa" name="parqueo_placa" class="form-control  text-uppercase" placeholder="Placa del Vehiculo" value="" maxlength="6" autocomplete="off">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    <label for="parqueo_tarifa">TARIFA</label>
                                    <select id="parqueo_tarifa" name="parqueo_tarifa" class="form-control">
                                        <option value=""> :: SELECCIONE :: </option> 
                                        <?php
                                        foreach ($tarifas as $i => $val){ 
                                            $tipo = $val->TARIFC_Tipo;
                                            ?>
                                            <option value="<?=$val->TARIFP_Codigo;?>" <?=($tipo==2?"class='d-none'":"");?> ><?="$val->TARIFC_Descripcion";?></option> 
                                            <?php
                                        } 
                                        ?>
                                    </select>
                                    <input type="hidden" id="parqueo_preciotarifa" name="parqueo_preciotarifa">
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    <label for="parqueo_fingreso">FECHA DE INGRESO</label>
                                    <input type="date" id="parqueo_fingreso" name="parqueo_fingreso" class="form-control"  readonly="readonly" placeholder="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    <label for="parqueo_hingreso">HORA DE INGRESO</label>
                                    <input type="text" id="parqueo_hingreso" name="parqueo_hingreso" class="form-control"  readonly="readonly">
                                </div>
                            </div>							
                        </div>
                        <!--/Primera columna-->

                        <!--Segunda columna-->
                        <div class="col-md-5 col-lg-5">
                            <div id="datos_abonado">
                                <div class="row">
                                    <div class="col-md-4">ABONADO</div>
                                    <div class="col-md-8">
                                        <label id="nombre_contacto"></label>    
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">FECHA PAGO: </div>
                                    <div class="col-md-8">
                                        <label id="parqueo_fpago"></label>    
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">SITUACIÓN:</div>
                                    <div class="col-md-4">
                                        <label id="parqueo_situacion"></label>
                                        <input type="hidden" name="cod_parqueo_situacion" id="cod_parqueo_situacion">
                                    </div>
                                </div>   
                                <div class="row">
                                    <div class="col-md-4">HORARIO:</div>
                                    <div class="col-md-4">
                                        <label id="parqueo_horario_abonado"></label>
                                    </div>
                                </div> 
                            </div>                             
                        </div>
                        <!--/Segunda columna-->

                    </div>

                </div>
                <!--/Moda Body-->

                <!--Modal Footer-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="salir">Salir</button>
                    <!--button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button-->
                    <button type="button" class="btn btn-success" id="registrar_parqueo" accesskey="x">Guardar</button>
                </div>
                <!--/Modal Footer-->

            </form>
        </div>
    </div>
</div>
<!-- /MODAL INGRESO DE VEHICULOS-->

<!-- MODAL SALIDA DE VEHICULOS-->
<div id="add_parqueo" class="modal fade">
    
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!--Modal Header-->
            <div class="modal-header">
                <div class="col-lg-8">
                    <h2 class="text-warning">SALIDA DE VEHICULOS</h2>    
                </div>
                <div class="col-lg-4 text-right">
                    <h2 class="text-warning" id="usr_hour_edit"></h2>
                </div>
            </div>
            <!--/Modal Header-->

            <form id="formParqueo_edit" method="POST">

                <!--Moda Body-->
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="parqueo_edit" name="parqueo_edit">
                    <input type="hidden" id="parqueo_codservicio_edit" name="parqueo_codservicio_edit">
                    
                        <div class="row">
                            <!--Primera columna-->
                            <div class="col-lg-8 col-md-12">
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label for="parqueo_placa_edit">PLACA</label>
                                        <input type="text" id="parqueo_placa_edit" name="parqueo_placa_edit" class="form-control text-uppercase" 
                                               placeholder="Placa del Vehiculo" value="" maxlength="6" readonly="readonly">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label for="parqueo_tarifa_edit">TARIFA</label>
                                        <input type="hidden" id="parqueo_tarifa_edit" name="parqueo_tarifa_edit">
                                        <input type="hidden" id="parqueo_tipotarifa_edit" name="parqueo_tipotarifa_edit">
                                        <select id="parqueo_tarifa_edit2" name="parqueo_tarifa_edit2" class="form-control" disabled="disabled">
                                            <option value=""> :: SELECCIONE :: </option> <?php
                                            foreach ($tarifas as $i => $val){ ?>
                                                    <option value="<?=$val->TARIFP_Codigo;?>"><?="$val->TARIFC_Descripcion";?></option> <?php
                                            } ?>
                                        </select>
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 form-group">
                                        <label for="parqueo_fingreso_edit">FECHA DE INGRESO</label>
                                        <input type="text" id="parqueo_fingreso_edit" name="parqueo_fingreso_edit" class="form-control"  readonly="readonly">
                                    </div>
                                    <div class="col-lg-6 col-md-12 form-group">
                                        <label for="parqueo_hingreso_edit">HORA DE INGRESO</label>
                                        <input type="text" id="parqueo_hingreso_edit" name="parqueo_hingreso_edit" class="form-control"  readonly="readonly">
                                    </div>
                                </div>	
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 form-group">
                                        <label for="parqueo_fsalida_edit">FECHA DE SALIDA</label>
                                        <input type="text" id="parqueo_fsalida_edit" name="parqueo_fsalida_edit" class="form-control"  value="" readonly="readonly">
                                    </div>
                                    <div class="col-lg-6 col-md-12 form-group">
                                        <label for="parqueo_hsalida_edit">HORA DE SALIDA</label>
                                        <input type="text" id="parqueo_hsalida_edit" name="parqueo_hsalida_edit" class="form-control"  value="" readonly="readonly">
                                    </div>
                                </div>	
                            </div>
                            <!--/Primera columna-->

                            <!--Segunda columna-->
                            <div class="col-lg-4 col-md-12">
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label for="parqueo_tiempo_edit">TIEMPO TRANSCURRIDO (minutos)</label>
                                        <input type="text" id="parqueo_tiempo_edit" name="parqueo_tiempo_edit" class="form-control"  readonly="readonly">
                                    </div>
                                </div>						
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label for="parqueo_placa">VALOR DE VENTA S/.</label>
                                        <input type="text" id="parqueo_valor_venta_edit" name="parqueo_valor_venta_edit" class="form-control"  readonly="readonly">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label for="parqueo_placa">I.G.V. S/.</label>
                                        <input type="text" id="parqueo_igv_edit" name="parqueo_igv_edit" class="form-control"  readonly="readonly">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label for="parqueo_placa">MONTO A PAGAR S/.</label>
                                        <input type="text" id="parqueo_monto_edit" name="parqueo_monto_edit" class="form-control"  value="" readonly="readonly">
                                    </div>
                                </div>		
                            </div>
                            <!--/Segunda columna-->

                    </div>

                    <!--Imprimi comprobantes-->
                    <div class="row text-center collapse" id="div_imprimir_comprobantes">                        
                        <div class="col-lg-5 col-md-5">
                            <a href="#" class="btn btn-info create_comprobante" id="F">Imprimir Factura</a>
                            <a href="#" class="btn btn-info create_comprobante" id="B">Imprimir Boleta</a>
                        </div>	 
                    </div>       
                    
                    <!--Datos abonado-->
                    <div class="row text-center collapse" id="div_datos_abonado_edit">
                        <div class="col-lg-12 col-md-12 text-left">
                            <div class="row">
                                <div class="col-md-2">ABONADO:</div>
                                <div class="col-md-8">
                                    <label id="parqueo_abonado_edit"></label>
                                </div>                              
                            </div>  
                            <div class="row">
                                <div class="col-md-2">FECHA PAGO.:</div>
                                <div class="col-md-8">
                                    <label id="parqueo_fpago_edit"></label>
                                </div>                                  
                            </div>
                            <div class="row">
                                <div class="col-md-2">SITUACION.:</div>
                                <div class="col-md-8">
                                    <label id="parqueo_situacion_edit"></label>
                                    <input type="hidden" name="cod_parqueo_situacion_edit" id="cod_parqueo_situacion_edit"/>
                                </div>                                  
                            </div>                            
                        </div>                            
                    </div>                    
                    
                    <!--Situacion tarifa plana-->
                    <div class="row text-center collapse" id="div_datos_tarifa_plana_edit">
                        <div class="col-lg-12 col-md-12 text-left">
                            <div class="row">
                                <div class="col-md-2">SITUACIÓN:</div>
                                <div class="col-md-10">
                                    <label id="parqueo_tarifa_situacion_edit"></label>
                                </div>                              
                            </div>  
                            <div class="row">
                                <div class="col-md-2">COMPROB.:</div>
                                <div class="col-md-10">
                                    <label id="parqueo_tarifa_comprobante_edit"></label>
                                </div>                                  
                            </div>
                        </div>                            
                    </div>
                </div>
                <!--/Moda Body-->

                <!--Modal Footer-->
                <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="salir_actualizar">Salir</button>
                        <!--button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button-->
                        <button type="button" class="btn btn-success" accesskey="x" id="actualizar_parqueo"><i class="fas fa-sign-out-alt"></i> Grabar</button>
                </div>
                <!--/Modal Footer-->

            </form>
        </div>
    </div>
</div>
<!-- END MODAL SALIDA DE VEHICULSO-->

<script>
    
function imprSelec(nombre) {
    var ficha = document.getElementById(nombre);
    var ventimp = window.open(' ', 'popimpr');
    ventimp.document.write(ficha.innerHTML);
    ventimp.document.close();
    ventimp.print();
    ventimp.close();
}

</script>