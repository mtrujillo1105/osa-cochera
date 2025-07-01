<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/tesoreria/tipocaja.js?=<?=JS;?>"></script>

<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

<div class="container-fluid">
    
    <!--Busqueda-->
    <div class="card mb-4 mt-4">
        <div class="card-header"><?=$titulo;?></div>
        <div class="card-body">
            <form id="form_busqueda" method="post">
                <div class="row">
                    <div class="col-lg-4 form-group">
                      <label for="nombre_rol">DESCRIPCIÓN</label>
                      <input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Tipo de caja" class="form-control"/>
                    </div>
                    <div class="col-lg-8 form-group align-self-end">
                      <button type="button" class="btn btn-info" id="buscarC">Buscar</button>
                      <button type="button" class="btn btn-dark" id="limpiarC">Limpiar</button>
                      <button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_caja'>Nuevo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--Fin Busqueda-->
    
    <!--Resultado-->
    <div class="card mb-4">
        <div class="card-header">RELACIÓN EN TIPOS DE CAJA</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="table-tipocaja" width="100%" cellspacing="0">
                    <div id="cargando_datos" class="loading-table">
                         <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                     </div>                    
                    <thead>
                        <tr>
                          <td style="width:10%" data-orderable="false">N°</td>
                          <td style="width:10%" data-orderable="false">CÓDIGO</td>
                          <td style="width:70%" data-orderable="false">TIPO DE CAJA</td>
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

<div id="add_caja" class="modal fade" role="dialog">
    <div class="modal-dialog w-porc-60">
        <div class="modal-content">
            <form id="formTipoCaja" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div style="text-align: center;">
                    <h3><b>REGISTRAR TIPO DE CAJA</b></h3>
                </div>
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="tipocaja" name="tipocaja" value="">

                    <div class="row form-group">
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <label for="descripcion_tipocaja">TIPO DE CAJA *</label>
                            <input type="text" id="descripcion_tipocaja" name="descripcion_tipocaja" class="form-control h-2" 
                                   placeholder="Indique la descripción" value="">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <label for="codigo_tipocaja">CÓDIGO</label>
                            <input type="text" id="codigo_tipocaja" name="codigo_tipocaja" class="form-control h-2 w-porc-90" 
                                   placeholder="Indique el codigo" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" accesskey="x" onclick="registrar_caja()">Guardar Registro</button>
                    <button type="button" class="btn btn-info" onclick="clean()">Limpiar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                </div>
            </form>
        </div>
    </div>
</div>