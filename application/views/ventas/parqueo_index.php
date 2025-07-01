<div class="container-fluid">
    
    <!--Busqueda-->
    <div class="card mb-4 mt-4">
        <div class="card-header">BUSCAR TICKET</div>
        <div class="card-body">
            <form id="form_busqueda" method="post">
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <label for="search_tarifa">TARIFA</label>
                        <select id="search_tarifa" name="search_tarifa" class="form-control">
                            <option value=""> :: SELECCIONE :: </option> <?php
                            foreach ($tarifas as $i => $val){ ?>
                                    <option value="<?=$val->TARIFP_Codigo;?>"><?="$val->TARIFC_Descripcion";?></option> <?php
                            } ?>
                        </select>
                    </div>                     
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                      <label for="search_placa">PLACA</label>
                      <input type="text" name="search_placa" id="search_placa" value="" placeholder="Placa" class="form-control text-uppercase"/>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                      <label for="search_placa">SERIE</label>
                      <input type="text" name="search_serie" id="search_serie" value="" placeholder="Serie" class="form-control text-uppercase"/>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                      <label for="search_placa">COMPROBANTE</label>
                      <input type="text" name="search_numero" id="search_numero" value="" placeholder="Numero" class="form-control text-uppercase"/>
                    </div>  
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                      <label for="search_placa">NUMERO</label>
                      <input type="text" name="search_numero_ticket" id="search_numero_ticket" value="" placeholder="Numero Ticket" class="form-control text-uppercase"/>
                    </div>                      
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group align-self-end text-right">
                      <button type="button" class="btn btn-info" id="buscar">Buscar</button>
                      <button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
                      <?php if($rol == 7000 || $rol == 1):?>
                           <button type="button" class="btn btn-success" id="nuevo" data-toggle='modal'>Nuevo</button>
                      <?php endif;?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--Fin Busqueda-->    
    
    <!--Resultado-->
    <div class="card mb-4">
        <div class="card-header">
            
            <div class="row">
                <div class="col-lg-12"><b>RELACIÓN DE TICKETS</b></div>    
            </div>
            
            <!--Show Resume-->
            <!--div class="row">
                <div class="col-sm-6 col-lg-3">TICKETS EMITIDOS: <span id="ticket_emitidos"></span></div>    
                <div class="col-sm-6 col-lg-3">TICKETS ANULADOS: <span id="ticket_anulados"></span></div>    
                <div class="col-sm-6 col-lg-3">TICKETS PENDIENTES: <span id="ticket_pendientes"></span></div>    
                <div class="col-sm-6 col-lg-3">TICKETS FACTURADOS: <span id="ticket_facturados"></span></div>    
            </div-->            
            
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="datatable-parqueo" width="100%" cellspacing="0">
                    <div id="cargando_datos" class="loading-table">
                         <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                     </div>                    
                    <thead>
                        <tr>
                     <td style="width:10%" data-orderable="true">NUMERO</td>
                            <td style="width:10%" data-orderable="true">PLACA</td>
                            <td style="width:10%" data-orderable="true">F.INGRESO</td>
                            <td style="width:10%" data-orderable="true">H.INGRESO</td>
                            <td style="width:10%" data-orderable="true">H.SALIDA</td>
                            <td style="width:20%" data-orderable="true">TARIFA</td>
                            <td style="width:10%" data-orderable="true">SERIE</td>
                            <td style="width:10%" data-orderable="true">COMPROBANTE</td>
                            <td style="width:10%" data-orderable="true">SITUACIÓN</td>
                            <td style="width:10%" data-orderable="true">FECHA</td>
                            <td style="width:05%" data-orderable="false"></td>                          
                            <td style="width:05%" data-orderable="false"></td>     
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

<!-- MODAL MODAL TICKET-->
<div id="add_parqueo" class="modal fade">
    
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <!--Modal Header-->
            <div class="modal-header">
                    <h4 class="modal-title">SALIDA DE VEHICULOS</h4>
            </div>
            <!--/Modal Header-->

            <form id="formParqueo_edit" method="POST">

                <!--Moda Body-->
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="parqueo" name="parqueo">
                    
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="parqueo_placa_edit">PLACA</label>
                            <input type="text" id="parqueo_placa_edit" name="parqueo_placa_edit" class="form-control text-uppercase" 
                                   placeholder="Placa del Vehiculo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="parqueo_tarifa_edit">TARIFA</label>
                            <select id="parqueo_tarifa_edit" name="parqueo_tarifa_edit" class="form-control">
                                <option value=""> :: SELECCIONE :: </option> <?php
                                foreach ($tarifas as $i => $val){ ?>
                                        <option value="<?=$val->TARIFP_Codigo;?>"><?="$val->TARIFC_Descripcion";?></option> <?php
                                } ?>
                            </select>
                        </div>
                    </div> 
                    <div class="row">
                        
                        <div class="col-lg-6 form-group">
                            <label for="parqueo_fingreso_edit">FECHA DE INGRESO</label>
                            <input type="date" id="parqueo_fingreso_edit" name="parqueo_fingreso_edit" class="form-control">
                        </div>
                        
                        <div class="col-lg-6 form-group">
                            <label for="parqueo_hingreso_edit">HORA DE INGRESO</label>
                            <input type="time" id="parqueo_hingreso_edit" name="parqueo_hingreso_edit" class="form-control">
                        </div>
                        
                    </div>
	            <div class="row collapse datos_salida">

                        <div class="col-lg-6 form-group">
                            <label for="parqueo_fsalida_edit">FECHA DE SALIDA</label>
                            <input type="date" id="parqueo_fsalida_edit" name="parqueo_fsalida_edit" class="form-control">
                        </div>
                        
                        <div class="col-lg-6 form-group">
                            <label for="parqueo_hsalida_edit">HORA DE SALIDA</label>
                            <input type="time" id="parqueo_hsalida_edit" name="parqueo_hsalida_edit" class="form-control">
                        </div>                        
                        
                    </div>	
                    
                    <div class="row collapse datos_salida">

                        <div class="col-lg-6 form-group">
                            <label for="parqueo_fsalida_edit">TIEMPO (minutos)</label>
                            <input type="text" id="parqueo_tiempo_edit" name="parqueo_tiempo_edit" class="form-control">
                        </div>
                        
                        <div class="col-lg-6 form-group">
                            <label for="parqueo_hsalida_edit">MONTO A PAGAR S/.</label>
                            <input type="text" id="parqueo_monto_edit" name="parqueo_monto_edit" class="form-control">
                        </div>                        
                        
                    </div>     
                    
                    <div class="row collapse datos_salida">

                        <div class="col-lg-12 form-group">
                            <label for="parqueo_fsalida_edit">OBSERVACIONES</label>
                            <textarea class="form-control h-5" id="parqueo_observacion_edit" name="parqueo_observacion_edit" 
                                      placeholder="Indique una observación" maxlength="800"></textarea>                            
                        </div>                      
                        
                    </div>                       

                </div>
                <!--/Moda Body-->

                <!--Modal Footer-->
                <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="salir_actualizar">Salir</button>
                        <!--button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button-->
                        <button type="button" class="btn btn-success" accesskey="x" id="actualizar_parqueo">Guardar</button>
                </div>
                <!--/Modal Footer-->

            </form>
        </div>
    </div>
</div>
<!-- END MODAL EDITAR TICKET-->