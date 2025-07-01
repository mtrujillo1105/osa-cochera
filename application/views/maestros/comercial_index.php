<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR SECTOR COMERCIAL</h3>
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
							<label for="search_descripcion">Sector comercial</label>
							<input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Buscar sector comercial" class="form-control"/>
						</div>
						<div class="col-lg-8 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_comercial'>Nuevo</button>
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
	      <h3 class="card-title">SECTOR COMERCIAL</h3>
	      <div class="card-tools">
	        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
	        	<i class="fas fa-minus"></i>
	        </button>
	      </div>
	    </div>
	    <div class="card-body">
				<table class="table table-striped table-bordered" id="table-comercial">
					<thead>
						<tr>
							<td style="width:90%" data-orderable="true">SECTOR COMERCIAL</td>
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

<div id="add_comercial" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="formComercial" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR SECTOR COMERCIAL</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="comercial" name="comercial" value="">

					<div class="row">
						<div class="col-md-12 form-group">
							<label for="descripcion_comercial">SECTOR COMERCIAL *</label>
							<input type="text" id="descripcion_comercial" name="descripcion_comercial" class="form-control" placeholder="Indique la comercial" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_comercial()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>