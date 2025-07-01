<div class="container-fluid">
    
  <!--Busqueda-->
  <div class="card mb-4 mt-4">
    <div class="card-header">BUSCAR UBICACION</div>
    <div class="card-body">
      <form id="form_busqueda" method="post">
        <div class="row">
          <div class="col-lg-4 form-group">
            <label for="descripcion_ubicacion">DESCRIPCIÓN</label>
            <input type="text" name="descripcion_ubicacion" id="descripcion_ubicacion"  placeholder="Buscar ubicacion" class="form-control"/>
          </div>
          <div class="col-lg-8 form-group align-self-end">
            <button type="button" class="btn btn-info" id="buscar">Buscar</button>
            <button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
            <button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_ubicacion'>Nuevo</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!--Fin Busqueda-->

  <!--Resultado-->
  <div class="card mb-4">
      <div class="card-header">RELACIÓN DE UBICACIONES</div>
      <div class="card-body">
          <div class="table-responsive">
              <table class="table table-bordered table-sm" id="dataTable-ubicacion" width="100%" cellspacing="0">
                  <thead>
                      <tr>
                        <td style="width:55%" data-orderable="true">DESCRIPCIÓN</td>
                        <td style="width:10%" data-orderable="true">E.ASIGNADOS</td>
                        <td style="width:15%" data-orderable="true">E.UTILIZADOS</td>
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

<!-- MODAL UBICACION-->
<div id="add_ubicacion" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formUbicacion" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title">REGISTRAR TARIFA</h4>
                </div>
                <div class="modal-body panel panel-default">
                    <input type="hidden" id="ubicacion" name="ubicacion" value="">           
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="ubicacion_descripcion">DESCRIPCIÓN</label>
                            <input type="text" id="ubicacion_descripcion" name="ubicacion_descripcion" class="form-control" placeholder="Descripción de la ubicacion" value="">
                        </div>
                    </div>
                        <div class="row">
                                <div class="col-lg-12 form-group">
                                        <label for="ubicacion_easignado">ESPACIOS ASIGNADOS</label>
                                        <input type="text" id="ubicacion_easignado" name="ubicacion_easignado" class="form-control" placeholder="Espacios asignados" value="">
                                </div>
                        </div>   
                        <div class="row">
                                <div class="col-lg-12 form-group">
                                        <label for="ubicacion_eusado">ESPACIOS USADOS</label>
                                        <input type="text" id="ubicacion_eusado" name="ubicacion_eusado" class="form-control" placeholder="Espacios usdaos" value="">
                                </div>
                        </div>            
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                        <button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
                        <button type="button" class="btn btn-success" accesskey="x" onclick="registrar_ubicacion()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END MODAL UBICACION-->