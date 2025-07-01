<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR CARGO</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<form id="form_busqueda" method="post">
					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="nombre_cargo">Cargo</label>
							<input type="text" name="nombre_cargo" id="nombre_cargo" value="" placeholder="Nombre del cargo" class="form-control"/>
						</div>
						<div class="col-lg-9 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_cargo'>Nuevo</button>
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
				<h3 class="card-title">RELACIÓN DE CARGOS</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" id="table-cargo">
					<thead>
						<tr>
							<td style="width:05%" data-orderable="false">N°</td>
							<td style="width:25%" data-orderable="true">NOMBRE DEL CARGO</td>
							<td style="width:60%" data-orderable="false">DESCRIPCIÓN</td>
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


<div id="add_cargo" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="formcargo" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR CARGO</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="cargo" name="cargo" value="">

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="cargo_nombre">NOMBRE</label>
							<input type="text" id="cargo_nombre" name="cargo_nombre" class="form-control" placeholder="Nombre" value="">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="cargo_descripcion">DESCRIPCIÓN</label>
							<textarea id="cargo_descripcion" name="cargo_descripcion" class="form-control h-5" placeholder="Descripcion"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_cargo()">Guardar Registro</button>
				</div>
			</form>
		</div>
	</div>
</div>