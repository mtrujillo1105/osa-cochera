<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR LINEA</h3>
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
							<input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Linea" class="form-control"/>
						</div>
						<div class="col-lg-8 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_linea'>Nuevo</button>
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
				<h3 class="card-title">RELACIÓN DE LINEAS</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" id="table-linea">
					<thead>
						<tr>
							<td style="width:10%" data-orderable="false">CÓDIGO</td>
							<td style="width:70%" data-orderable="false">LINEA</td>
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

<div id="add_linea" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="formLinea" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR LINEA</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="linea" name="linea" value="">

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="codigo_linea">CÓDIGO</label>
							<input type="text" id="codigo_linea" name="codigo_linea" class="form-control" placeholder="Código" value="">
						</div>
						<div class="col-lg-8 form-group">
							<label for="descripcion_linea">DESCRIPCIÓN DE LINEA *</label>
							<input type="text" id="descripcion_linea" name="descripcion_linea" class="form-control" placeholder="Linea" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_linea()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>