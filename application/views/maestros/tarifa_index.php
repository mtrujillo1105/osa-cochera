<div class="container-fluid">
    
  <!--Busqueda-->
  <div class="card mb-4 mt-4">
    <div class="card-header">BUSCAR TARIFA</div>
    <div class="card-body">
      <form id="form_busqueda" method="post">
        <div class="row">
          <div class="col-lg-4 form-group">
            <label for="nombre_rol">DESCRIPCIÓN</label>
            <input type="text" name="descripcion_tarifa" id="descripcion_tarifa" value="" placeholder="Buscar tarifa" class="form-control"/>
          </div>
          <div class="col-lg-8 form-group align-self-end">
            <button type="button" class="btn btn-info" id="buscar">Buscar</button>
            <button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
            <button type="button" class="btn btn-success" id="nuevo">Nuevo</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!--Fin de Busqueda-->
  
  <!--Resultado-->
  <div class="card mb-4">
      <div class="card-header">RELACIÓN DE TARIFAS</div>
      <div class="card-body">
          <div class="table-responsive">
              <table class="table table-bordered table-sm" id="dataTable-tarifa" width="100%" cellspacing="0">
                  <thead>
                      <tr>
                        <td style="width:75%" data-orderable="true">DESCRIPCIÓN</td>
                        <td style="width:15%" data-orderable="true">PRECIO</td>
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

<!-- MODAL tTARIFA-->
<div id="add_tarifa" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTarifa" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title">REGISTRAR TARIFA</h4>
                </div>
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="tarifa" name="tarifa" value="">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="rol_nombre">TIPO</label>
                            <select id="tarifa_tipo" name="tarifa_tipo" class="form-control">
                                <option value="">::Seleccione::</option>
                                <option value="1">NORMAL</option>
                                <option value="2">DE ABONADOS</option>
                                <option value="3">T.PLANA</option>
                                <option value="4">EXONERDADOS</option>
                            </select>
                        </div>
                    </div>            
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="rol_nombre">DESCRIPCIÓN</label>
                            <input type="text" id="tarifa_descripcion" name="tarifa_descripcion" class="form-control" 
                                   placeholder="Descripción del tarifa" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 form-group">
                            <label for="rol_nombre">PRECIO</label>
                            <input type="text" id="tarifa_precio" name="tarifa_precio" class="form-control" placeholder="Precio tarifa" value="">
                        </div>                       
                    </div>   
                    <div class="row" id="div_tarifa_horarios">
                        <div class="col-lg-6 col-md-6 form-group">
                            <label for="rol_nombre">HORA INICIO</label>
                            <input type="time" id="tarifa_hora_inicio" name="tarifa_hora_inicio" class="form-control" placeholder="00:00">
                        </div>
                        <div class="col-lg-6 col-md-6 form-group">
                            <label for="rol_nombre">HORA FIN</label>
                            <input type="time" id="tarifa_hora_fin" name="tarifa_hora_fin" class="form-control" placeholder="00:00">
                        </div>                        
                    </div>            
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
                    <button type="button" class="btn btn-success" accesskey="x" onclick="registrar_tarifa()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END MODAL TARIFA-->