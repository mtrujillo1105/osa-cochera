<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR ROLES</h3>
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
							<label for="nombre_rol">DESCRIPCIÓN</label>
							<input type="text" name="nombre_rol" id="nombre_rol" value="" placeholder="Buscar rol" class="form-control"/>
						</div>
						<div class="col-lg-8 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_rol'>Nuevo</button>
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
				<h3 class="card-title">RELACIÓN DE ROLES</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" id="table-rol">
					<thead>
						<tr>
							<td style="width:90%" data-orderable="true">DESCRIPCIÓN</td>
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

<div id="add_rol" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="formRol" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR ROL</h4>
				</div>
				<div class="modal-body panel panel-default">
					<input type="hidden" id="rol" name="rol" value="">

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="rol_nombre">DESCRIPCIÓN</label>
							<input type="text" id="rol_nombre" name="rol_nombre" class="form-control" placeholder="Descripción del rol" value="">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<table class="table table-striped table-bordered" id="table-permisos">
								<thead>
									<th style="width:05%;" data-orderable="false"></th>
									<th style="width:95%;" data-orderable="false">DESCRIPCIÓN</th>
								</thead>
								<tbody> <?php
								if ($modulos != NULL){
									foreach ($modulos as $j => $permisos) {
										$size = count($permisos["permiso"]);
										for ( $i = 0; $i < $size; $i++ ){ ?>
											<tr>
												<td <?=($permisos["modulo"][$i] == true) ? "class='bg-dark'" : "";?>>
													<input type="checkbox" name="permiso[]" class="form-check-input position-static permiso <?=($permisos['modulo'][$i] == true) ? '' : 'auto-check-'.$j;?> check-<?=$permisos['permiso'][$i];?>" value="<?=$permisos['permiso'][$i];?>" <?=($permisos["modulo"][$i] == true) ? "onclick='autocheck($j)'" : "";?>>
												</td>
												<td <?=($permisos["modulo"][$i] == true) ? "class='bg-dark'" : "";?>> <?=$permisos["descripcion"][$i];?> </td>
												</tr> <?php
											}
										}
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_rol()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>