<div class="container-fluid">
    
    <!--Busqueda-->
    <div class="card mb-4 mt-4">
        
        <!--Card Header-->
        <div class="card-header">
          <h3 class="card-title">REPORTE MOVIMIENTOS DE CAJA</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!--Fin Card Header-->
        
        <!--Card Body-->
        <div class="card-body text-sm">
            <form id="form_busqueda" method="post">
                
                <!-- Fila-->
                <div class="row">
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_caja">CAJA</label>
                        <select name="search_caja" id="search_caja" class="form-control form-control-sm w-porc-90 h-2">
                            <option value=""> :: TODOS :: </option> <?php
                            if ($caja != NULL){
                                foreach ($caja as $indice => $val){ ?>
                                    <option value="<?=$val->CAJA_Codigo;?>"><?=$val->CAJA_Nombre;?></option> <?php
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_fechai">FECHA INICIO</label>
                        <input type="date" name="search_fechai" id="search_fechai" class="form-control form-control-sm h-1 w-porc-90"/>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_fechaf">FECHA FIN</label>
                        <input type="date" name="search_fechaf" id="search_fechaf" class="form-control form-control-sm h-1 w-porc-90"/>
                    </div>  
                    <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                        <label for="search_fpago">F.PAGO</label>
                        <select name="search_fpago" id="search_fpago" class="form-control form-control-sm w-porc-90 h-2">
                            <option value=""> :: TODOS :: </option> <?php
                            if ($caja != NULL){
                                foreach ($forma_pago as $indice => $val){ ?>
                                    <option value="<?=$val->FORPAP_Codigo;?>"><?=$val->FORPAC_Descripcion;?></option> <?php
                                }
                            } ?>
                        </select>
                    </div>                      
                    <div class="col-sm-4 col-md-4 col-lg-4 form-group align-self-end text-right">
                        <button type="button" class="btn btn-info btn-sm" id="buscar"><i class="fas fa-search"></i> Buscar</button>	
                        <button type="button" class="btn btn-dark btn-sm" id="limpiar"><i class="fas fa-trash"></i> Limpiar</button>
                        <button type="button" class="btn btn-success btn-sm" id="verReporte"><i class="fas fa-file-excel"></i> Imrpimir</button>            
                    </div>
                </div>
                <!--Fin Fila-->

            </form>
        </div>
        <!--Fin Card Body-->
        
    </div>    
    <!--Fin Busqueda-->
    
    <!--Resultado-->
    <div class="card mb-4">
        <!--div class="card-header">RELACIÓN DE MOVIMIENTOS</div-->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-sm" id="datatable_movimiento">
                    <!--div id="cargando_datos" class="loading-table">
                         <img src="< ?=base_url().'public/images/loading.gif?='.IMG;?>">
                     </div-->  
                    <thead>
                        <tr>
                             <td style="width:09%" data-orderable="true" title="Fecha de movimiento.">FECHA MOV.</td>
                             <td style="width:09%" data-orderable="true" title="Código de la caja">CAJA</td>
                             <td style="width:21%" data-orderable="true" title="Nombre de la caja">SERIE</td>
                             <td style="width:15%" data-orderable="true" title="Tipo de caja">NUMERO</td>
                             <td style="width:07%" data-orderable="true" title="Moneda del movimiento">F.PAGO</td>
                             <td style="width:10%" data-orderable="true" title="Importe del movimiento">MONTO S/.</td>
                             <td style="width:10%" data-orderable="true" title="Tipo de movimiento">T.MOVIMIENTO</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>    
    <!--Fin Resultado-->
    
</div>