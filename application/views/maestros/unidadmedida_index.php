<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR UNIDAD DE MEDIDA</h3>
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
							<label for="search_descripcion">DESCRIPCIÓN</label>
							<input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Unidad de medida" class="form-control"/>
						</div>
						<div class="col-lg-8 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_um'>Nuevo</button>
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
				<h3 class="card-title">UNIDADES DE MEDIDA</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" id="table-um">
					<thead>
						<tr>
							<td style="width:10%" data-orderable="false">SIMBOLO</td>
							<td style="width:70%" data-orderable="false">DESCRIPCIÓN</td>
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

<div id="add_um" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="formUnidad" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR UNIDAD DE MEDIDA</h4>
				</div>

				<div class="modal-body">
					<input type="hidden" id="um" name="um" value="">

					<div class="row">
						<div class="col-lg-6 form-group">
							<label for="descripcion_um">UNIDAD DE MEDIDA *</label>
							<input type="text" id="descripcion_um" name="descripcion_um" class="form-control" placeholder="Unidad de medida" value="">
						</div>
						<div class="col-lg-6 form-group">
							<label for="simbolo_um">SIMBOLO *</label>
							<input type="text" id="simbolo_um" name="simbolo_um" class="form-control" placeholder="Simbolo (NIU)" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_um()">Guardar Registro</button>
				</div>
			</form>
		</div>
	</div>
</div>