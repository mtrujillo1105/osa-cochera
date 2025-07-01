<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR DOCUMENTO</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<form id="form_busqueda" method="post">
					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="search_descripcion">Documento</label>
							<input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Buscar documento" class="form-control"/>
						</div>
						<div class="col-lg-8 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_documento'>Nuevo</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="card card-light">
	    <div class="card-header">
	      <h3 class="card-title">DOCUMENTO</h3>
	      <div class="card-tools">
	        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
	        	<i class="fas fa-minus"></i>
	        </button>
	      </div>
	    </div>
	    <div class="card-body">
				<table class="table table-striped table-bordered" id="table-documento">
					<thead>
						<tr>
							<td style="width:50%" data-orderable="true">DOCUMENTO</td>
							<td style="width:15%" data-orderable="true">INICIAL</td>
							<td style="width:15%" data-orderable="true">ESTADO</td>
							<td style="width:10%" data-orderable="true">ABREVIACIÓN</td>
							<td style="width:05%" data-orderable="false"></td>
							<td style="width:05%" data-orderable="false"></td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
	    </div>
	  </div>
	</div>
</div>

<div id="add_documento" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="formDocumento" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR DOCUMENTO</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="documento" name="documento" value="">

					<div class="row">
						<div class="col-md-12 form-group">
							<label for="descripcion_documento">DOCUMENTO *</label>
							<input type="text" id="descripcion_documento" name="descripcion_documento" maxlength="250" class="form-control" placeholder="Descripción del documento" value="">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 form-group">
							<label for="inicial_documento">INICIAL *</label>
							<input type="text" id="inicial_documento" name="inicial_documento" maxlength="4" class="form-control" placeholder="Inicial del documento" value="">
						</div>
						<div class="col-md-4 form-group">
							<label for="estado_documento">ESTADO *</label>
							<select id="estado_documento" name="estado_documento" class="form-control">
								<option value="1">ACTIVO</option>
								<option value="0">INACTIVO</option>
							</select>
						</div>
						<div class="col-md-4 form-group">
							<label for="abreviacion_documento">ABREVIACIÓN *</label>
							<input type="text" id="abreviacion_documento" name="abreviacion_documento" maxlength="10" class="form-control" placeholder="Abreviación del documento" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_documento()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>