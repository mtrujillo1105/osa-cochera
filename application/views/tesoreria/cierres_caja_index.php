<div class="container-fluid">
    
    <!--Busqueda-->
    <div class="card mb-4 mt-4">
        <div class="card-header"><?=$titulo_busqueda;?></div>
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
                    </div>                    
                </div>
            </form>
        </div>
    </div>  
    <!--Fin Busqueda-->
    
    <!--Resultado-->
    <div class="card mb-4">
        <div class="card-header"><?php echo $titulo;?></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="table-cajacierre" width="100%" cellspacing="0">
                    <!--div id="cargando_datos" class="loading-table">
                         <img src="<?=base_url().'public/images/loading.gif?='.IMG;?>">
                     </div-->                    
                    <thead>
                        <tr>
                         <td style="width:05%" data-orderable="false">N°</td>
                         <td style="width:15%" data-orderable="true">F.APERTURA</td>
                         <td style="width:15%" data-orderable="true">F.CIERRE</td>
                         <td style="width:10%" data-orderable="true">CAJA</td>
                         <td style="width:10%" data-orderable="true">INGRESOS</td>
                         <td style="width:10%" data-orderable="true">EGRESOS</td>
                         <td style="width:10%" data-orderable="true">SALDO</td>
                         <td style="width:15%" data-orderable="false">F.REGISTRO</td>
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

<!-- MODAL MOVIMIENTOS CIERRE -->
<div id="modal_movimientoscierre" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h4>MOVIMIENTOS GENERADOS - CIERRE <span class="titleCierre"></span></h4>
                </div>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label>CAJA:</label> <span class="titleCaja"></span>
                        </div>
                        <div class="col-lg-4 form-group">
                            <label>RESPONSABLE:</label> <span class="titleResponsable"></span>
                        </div>
                        <div class="col-lg-4 form-group">
                            <a href='#' data-fancybox data-type='iframe' id="imprimir_detalle_cierres">
                                <img src="<?php echo base_url();?>public/images/icons/pdf.png" width="22" height="22" 
                                     class="imgBoton img-fluid">
                            </a> 
                            <!--a href="#" data-fancybox data-type='iframe' id="imprimir_detalle_cierres_excel">
                                <img src="< ?php echo base_url();?>public/images/icons/xls.png" width="25" height="25" 
                                     class="imgBoton img-fluid imprimir_tickets_activos">
                            </a-->                            
                        </div>
                    </div>
                    
                    <div class="row mt-0">
                        <div class="col-lg-4 form-group">
                            <label>INGRESOS:</label> <span class="titleIngresos"></span>
                        </div>
                        <div class="col-lg-4 form-group">
                            <label>EGRESOS:</label> <span class="titleEgresos"></span>
                        </div>
                        <div class="col-lg-4 form-group">
                            <label>SALDO:</label> <span class="titleSaldos"></span>
                        </div>                        
                    </div>                    

                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <table class="table table-striped table-bordered" id="table-movimientoscierre">
                                <thead>
                                    <tr>
                                        <td style="width:20%" data-orderable="true">F.REGISTRO</td>
                                        <td style="width:15%" data-orderable="true">F.MOVIMIENTO</td>
                                        <td style="width:10%" data-orderable="false">SERIE</td>
                                        <td style="width:15%" data-orderable="false">NÚMERO</td>
                                        <td style="width:15%" data-orderable="false">F.PAGO</td>
                                        <td style="width:15%" data-orderable="false">MONTO S/.</td>
                                        <td style="width:10%" data-orderable="false">MOVIMIENTO</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <input type="hidden" id="documento_cliente" name="documento_cliente">
                </div>
        </div>
    </div>
</div>
<!-- END MOVIMIENTOS CIERRE-->