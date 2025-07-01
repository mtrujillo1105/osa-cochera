<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR FORMAS DE PAGO</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<form id="form_busqueda" method="post">
					<div class="row">
						<div class="col-lg-6 form-group">
							<label for="search_descripcion">DESCRIPCIÓN</label>
							<input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Forma de pago" class="form-control"/>
						</div>
						<div class="col-lg-6 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_fpago'>Nuevo</button>
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
				<h3 class="card-title">FORMAS DE PAGO</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" id="table-fpago">
					<thead>
						<tr>
							<td style="width:90%" data-orderable="false">FORMA DE PAGO</td>
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

<div id="add_fpago" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="formFpago" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR FORMA DE PAGO</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="fpago" name="fpago" value="">

					<div class="row form-group">
						<div class="col-lg-12">
							<label for="descripcion_fpago">FORMA DE PAGO *</label>
							<input type="text" id="descripcion_fpago" name="descripcion_fpago" class="form-control" placeholder="Indique la forma de pago" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_fpago()">Guardar Registro</button>
				</div>
			</form>
		</div>
	</div>
</div>