<div class="container-fluid">
    
   <!--Busqueda-->
   <div class="card card-light mb-4 mt-4">
                
        <!--Card Header-->
        <div class="card-header">
            <h3 class="card-title">REPORTE TICKETS EMITIDOS</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div> 
        </div>
        <!--Fin Card Header-->

        <!--Card Body-->
        <div class="card-body text-sm">
            <form id="form_reporte" method="post">

                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="tarifa">Tarifa</label>
                        <select name="tarifa" id="tarifa" class="form-control form-control-sm w-porc-90 h-2">
                            <option value=""> :: TODOS :: </option> <?php
                            if ($tarifas != NULL){
                                foreach ($tarifas as $indice => $val){ ?>
                                    <option value="<?=$val->TARIFP_Codigo;?>"><?=$val->TARIFC_Descripcion;?></option> <?php
                                }
                            } ?>
                        </select>
                    </div>   
                    <div class="col-md-2 form-group">
                        <label for="cajero">Cajero: </label>
                        <select name="cajero" id="cajero" class="form-control form-control-sm">
                            <option value=""> :: TODOS :: </option> <?php
                            if ($tarifas != NULL){
                                foreach ($cajeros as $indice => $val){ ?>
                                    <option value="<?=$val->USUA_Codigo;?>"><?=$val->PERSC_Nombre." ".$val->PERSC_ApellidoPaterno;?></option> <?php
                                }
                            } ?>
                        </select>
                    </div>                              
                    <div class="col-md-2 form-group">
                        <label for="fechaing">Fecha Ingreso:</label>
                        <input id="fechaing" name="fechaing" type="date" class="form-control form-control-sm" placeholder="00/00/00" size="10">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="fechasal">Fecha Salida:</label>
                        <input id="fechasal" name="fechasal" type="date" class="form-control form-control-sm" placeholder="00/00/00" size="10">
                    </div>   
                    <div class="col-md-1 form-group">
                        <label for="placa">Placa:</label>
                        <input id="placa" name="placa" type="text" class="form-control form-control-sm" placeholder="Placa" size="10">
                    </div>                      
                    <div class="col-lg-3 form-group align-self-end text-right">
                        <button type="button" class="btn btn-info btn-sm" id="buscar"><i class="fas fa-search"></i> Buscar</button>
                        <button type="button" class="btn btn-dark btn-sm" id="limpiar"><i class="fas fa-trash"></i> Limpiar</button>
                        <button type="button" class="btn btn-success btn-sm" id="verReporte"><i class="fas fa-file-excel"></i> Imprimir</button>
                    </div>                            
                </div>                      

            </form>
        </div>
        <!--Fin Card Body-->

    </div>
    <!--Fin Busqueda-->
    
    <!--Resultado-->
    <div class="card mb-4">
        
        <div class="card-header">
            
            <div class="row">
                <div class="col-sm-6 col-lg-3">TICKETS EMITIDOS: <span id="ticket_emitidos"></span></div>    
                <div class="col-sm-6 col-lg-3">TICKETS ANULADOS: <span id="ticket_anulados"></span></div>    
                <div class="col-sm-6 col-lg-3">TICKETS PENDIENTES: <span id="ticket_pendientes"></span></div>    
                <div class="col-sm-6 col-lg-3">TICKETS FACTURADOS: <span id="ticket_facturados"></span></div>    
            </div>            
            
        </div>        
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-sm" id="datatable-tickets">
                    <!--div id="cargando_datos" class="loading-table">
                         <img src="< ?=base_url().'public/images/loading.gif?='.IMG;?>">
                     </div-->  
                    <thead>
                        <tr>
                             <td style="width:08%" data-orderable="true" title="Fecha de movimiento.">NÚMERO</td>
                             <td style="width:08%" data-orderable="true" title="Código de la caja">PLACA</td>
                             <td style="width:08%" data-orderable="true" title="Nombre de la caja">F.INGRESO</td>
                             <td style="width:08%" data-orderable="true" title="Tipo de caja">H.INGRESO</td>
                             <td style="width:08%" data-orderable="true" title="Moneda del movimiento">F.SALIDA</td>
                             <td style="width:08%" data-orderable="true" title="Importe del movimiento">H.SALIDA</td>
                             <td style="width:08%" data-orderable="true" title="Tiempo">TIEMPO</td>
                             <td style="width:08%" data-orderable="true" title="Monto del servicio">MONTO S/.</td>
                             <td style="width:10%" data-orderable="true" title="Tarifa">TARIFA</td>
                             <td style="width:10%" data-orderable="true" title="Numero de comprobante">N.COMPROBANTE</td>
                             <td style="width:10%" data-orderable="true" title="Nombre de caja">CAJERO</td>
                             <td style="width:06%" data-orderable="true" title="Situación">SITUACIÓN</td>
                             <td style="width:01%" data-orderable="true" title="Situación"></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <!--Fin Resultado-->
    
</div>