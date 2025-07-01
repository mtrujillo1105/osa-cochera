<div class="row">
	<div class="col-md-12">
	  <div class="card card-light">
	    <div class="card-header">
	      <h3 class="card-title">Buscar Familias</h3>
	      <div class="card-tools">
	        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
	        	<i class="fas fa-minus"></i>
	        </button>
	      </div>
	    </div>
	    <div class="card-body">
				<form id="form_busqueda" method="post">
					<div class="row">
						<div class="col-lg-2 form-group">
							<label for="search_tipo">Producto/Servicio</label>
							<select id="search_tipo" name="search_tipo" class="form-control">
								<option value=""> :: TODOS :: </option>
								<option value="B">Productos</option>
								<option value="S">Servicios</option>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="search_codigo">Código</label>
							<input type="text" name="search_codigo" id="search_codigo" value="" placeholder="Código" class="form-control"/>
						</div>
						<div class="col-lg-3 form-group">
							<label for="search_descripcion">Descripción</label>
							<input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Descripción" class="form-control"/>
						</div>
						<div class="col-lg-4 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_familia'>Nuevo</button>
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
	      <h3 class="card-title">Familias registradas</h3>
	      <div class="card-tools">
	        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
	        	<i class="fas fa-minus"></i>
	        </button>
	      </div>
	    </div>
	    <div class="card-body">
				<table class="table table-striped table-bordered" id="table-familia">
					<thead>
						<tr>
							<td style="width:15%" data-orderable="true">Producto/Servicio</td>
							<td style="width:15%" data-orderable="true">CÓDIGO</td>
							<td style="width:60%" data-orderable="true">DESCRIPCIÓN</td>
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

<div id="add_familia" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formFamilia" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR FAMILIA</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="familia" name="familia" value="">
					<div class="row">
						<div class="col-md-3 form-group">
							<label for="tipoFamilia">TIPO P/S</label>
							<select id="tipoFamilia" name="tipoFamilia" class="form-control">
								<option value="B">Productos</option>
								<option value="S">Servicios</option>
							</select>
						</div>
						<div class="col-md-3 form-group">
							<label for="codigoFamilia">CÓDIGO *</label>
							<input type="text" id="codigoFamilia" name="codigoFamilia" class="form-control" placeholder="Código" maxlength="20" value="">
						</div>
						<div class="col-md-6 form-group">
							<label for="descripcionFamilia">DESCRIPCIÓN</label>
							<input type="text" id="descripcionFamilia" name="descripcionFamilia" class="form-control" placeholder="Nombre de la familia" maxlength="350" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_familia()">Guardar Registro</button>
				</div>
			</form>
		</div>
	</div>
</div>