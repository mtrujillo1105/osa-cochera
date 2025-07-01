<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>

<div id="zonaContenido">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-light mt-3">
                
                <!--Card Header-->
                <div class="card-header">
                    <h3 class="card-title">REPORTE REGISTRO DE VENTAS</h3>
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
                            <div class="col-md-2 form-group">
                                <label for="fechaini">Desde:</label>
                                <input id="fechaini" name="fechaini" type="date" class="form-control form-control-sm" placeholder="00/00/00" 
                                       size="10" value="">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="fechafin">Hasta:</label>
                                <input id="fechafin" name="fechafin" type="date" class="form-control form-control-sm" placeholder="00/00/00" 
                                       size="10" value="">
                            </div>   
                            <div class="col-md-1 form-group">
                                <label for="tipo_doc">Tipo: </label>
                                <select name="tipo_doc" id="tipo_doc" class="form-control form-control-sm">
                                    <option value="">::Seleccione::</option>
                                    <option value="F">Factura</option>
                                    <option value="B">Boletas</option>
                                </select>
                            </div>  
                            <div class="col-md-1 form-group">
                                <label for="nro_ruc">RUC: </label>
                                <input id="nro_ruc" name="nro_ruc" type="text" class="form-control form-control-sm" placeholder="Numero de RUC" 
                                       size="10" value="">
                            </div>  
                            <div class="col-md-2 form-group">
                                <label for="razon_social">Razon Social: </label>
                                <input id="razon_social" name="razon_social" type="text" class="form-control form-control-sm" placeholder="Razon Social" 
                                       size="10" value="">
                            </div>  
                            <div class="col-md-1 form-group">
                                <label for="razon_social">Número: </label>
                                <input id="numero_doc" name="numero_doc" type="text" class="form-control form-control-sm" placeholder="Número Comprobante" 
                                       size="10" value="">
                            </div>                              
                            <div class="col-lg-3 form-group align-self-end text-right">
                                <button type="button" class="btn btn-info btn-sm" id="buscar"><i class="fas fa-search"></i> Buscar</button>
                                <button type="button" class="btn btn-dark btn-sm" id="limpiar"><i class="fas fa-trash"></i> Limpiar</button>
                                <button type="button" class="btn btn-success btn-sm" id="verReporte">
                                    <i class="fas fa-file-excel"></i> Imprimir
                                </button>	
                            </div>                            
                        </div>                      
                        
                    </form>
                </div>
                <!--Fin Card Body-->
                
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                
                <!--Card Header-->
                <!--div class="card-header">
                    <h3 class="card-title">REGISTROS</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div-->
                <!--Fin Card Header-->
                
                <!--Card body-->
                <div class="card-body" style="overflow-y: auto;">
                    <table class="table table-striped table-bordered table-sm text-sm" id="datatable_ventasdiario">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 10%" data-orderable="true">F.EMISION</th>
                                <th style="width: 5%" data-orderable="true">TIPO</th>
                                <th style="width: 5%" data-orderable="true">SERIE</th>
                                <th style="width: 5%" data-orderable="true">NUMERO</th>
                                <th style="width: 20%" data-orderable="true">NOMBRE Y/O RAZON SOCIAL</th>
                                <th style="width: 10%" data-orderable="true">RUC</th>
                                <th style="width: 5%" data-orderable="true">F.PAGO</th>
                                <th style="width: 10%" data-orderable="true">VALOR VENTA</th>
                                <th style="width: 10%" data-orderable="true">I.G.V</th>
                                <th style="width: 10%" data-orderable="true">TOTAL IMPORTE</th>
                                <th style="width: 10%" data-orderable="true">SITUACIÓN</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!--Fin Card body-->
                
            </div>
        </div>
    </div>
</div>