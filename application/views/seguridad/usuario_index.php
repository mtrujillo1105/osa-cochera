<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR USUARIO</h3>
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
							<label for="searchNombres">NOMBRES</label>
							<input type="text" name="searchNombres" id="searchNombres" value="" placeholder="Nombres" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-3 form-group">
							<label for="searchUsuario">USUARIO</label>
							<input type="text" name="searchUsuario" id="searchUsuario" value="" placeholder="Usuario" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-3 form-group">
							<label for="searchRol">ROL</label>
							<input type="text" name="searchRol" id="searchRol" value="" placeholder="Rol" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-3 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_usuario'>Nuevo</button>
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
				<h3 class="card-title">RELACIÓN DE USUARIOS</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" id="table-usuarios">
					<thead>
						<tr>
							<th style="width: 30%" data-orderable="true">PERSONAL</th>
							<th style="width: 20%" data-orderable="true">USUARIO</th>
							<th style="width: 35%" data-orderable="true">ROLES</th>
							<th style="width: 05%" data-orderable="false">&nbsp;</th>
							<th style="width: 05%" data-orderable="false">&nbsp;</th>
							<th style="width: 05%" data-orderable="false">&nbsp;</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="add_usuario" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formUsuario" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR USUARIO</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="usuario" name="usuario" value=""/>

					<div class="row">
						<div class="col-lg-12 form-group">
							<span>INFORMACIÓN DEL EMPLEADO</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3">
							<label for="persona">EMPLEADO *</label>
							<select name="persona" id="persona" class="form-control cboDirectivo">
								<option value=""> :: SELECCIONE :: </option><?php
								foreach($directivos as $indice => $val){ ?>
									<option value="<?=$val->PERSP_Codigo;?>"><?=$val->nombre;?></option><?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3">
							<label for="txtNombres">NOMBRES</label>
							<input type="text" name="txtNombres" id="txtNombres" maxlength="30" class="form-control" value="" readonly placeholder="NOMBRES">
						</div>
						<div class="col-lg-3">
							<label for="txtPaterno">APELLIDO PATERNO</label>
							<input type="text" name="txtPaterno" id="txtPaterno" maxlength="30" class="form-control" value="" readonly placeholder="APELLIDO PATERNO">
						</div>
						<div class="col-lg-3">
							<label for="txtMaterno">APELLIDO MATERNO</label>
							<input type="text" name="txtMaterno" id="txtMaterno" maxlength="30" class="form-control" value="" readonly placeholder="APELLIDO MATERNO">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<span>DATOS DEL USUARIO</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4">
							<label for="txtUsuario">USUARIO *</label>
							<input type="text" name="txtUsuario" id="txtUsuario" maxlength="30" class="form-control" value="" placeholder="usuario" autocomplete="off">
						</div>
						<div class="col-lg-4">
							<label for="txtClave">CLAVE *</label>
							<input type="password" name="txtClave" id="txtClave" maxlength="30" class="form-control" value="" autocomplete="off" placeholder="contraseña">
						</div>
						<div class="col-lg-4">
							<label for="txtClave2">REPETIR CLAVE *</label>
							<input type="password" name="txtClave2" id="txtClave2" maxlength="30" class="form-control" value="" autocomplete="off" placeholder="contraseña">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<span>DEFINIR ACCESO</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<table class="table table-striped table-bordered" id="establecimientos-table">
								<thead>
									<tr>
										<th style="width: 40%;" data-orderable="true">EMPRESA</th>
										<th style="width: 20%;" data-orderable="false">ESTABLECIMIENTO</th>
										<th style="width: 20%;" data-orderable="false">ROL</th>
										<th style="width: 20%;" data-orderable="false">ACCESO</th>
									</tr>
								</thead>
								<tbody> <?php
									foreach ($establecimientos as $i => $val){ ?>
										<tr>
											<td><?=$val->EMPRC_RazonSocial;?></td>
											<td><?=$val->EESTABC_Descripcion;?>
												<input type="hidden" name="establecimientos[]" class="establecimientos-input" value="<?=$val->COMPP_Codigo;?>"/>
											</td>
											<td>
												<select name="rol[]" class="form-control form-control-sm establecimientos-rol_<?=$val->COMPP_Codigo;?>"> <?php
													foreach($roles as $j => $rol){ ?>
														<option value="<?=$rol->ROL_Codigo;?>"><?=$rol->ROL_Descripcion;?></option><?php
													} ?>
												</select>
											</td>
											<td>
												<select name="acceso[]" class="form-control form-control-sm establecimientos-acceso_<?=$val->COMPP_Codigo;?>">
													<option value="0">SIN ACCESO</option>
													<option value="1">PERMITIDO</option>
												</select>
											</td>
										</tr> <?php
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_usuario()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="view_user" class="modal fade" role="dialog">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">INFORMACIÓN DEL USUARIO</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 form-group">
						<span>EMPLEADO</span>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 form-group">
						<label>NOMBRES:</label>
						<span class="data-nombres"></span>
					</div>
					<div class="col-lg-4 form-group">
						<label>APELLIDO PATERNO:</label>
						<span class="data-apellidop"></span>
					</div>
					<div class="col-lg-4 form-group">
						<label>APELLIDO MATERNO:</label>
						<span class="data-apellidom"></span>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 form-group">
						<label>USUARIO:</label>
						<span class="data-usuario"></span>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 form-group">
						<span>INFORMACIÓN DE ACCESO</span>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<table class="table table-striped table-bordered" id="table-view-accesos">
							<thead>
								<tr>
									<th style="width: 40%;" data-orderable="false">EMPRESA</th>
									<th style="width: 30%;" data-orderable="false">ESTABLECIMIENTO</th>
									<th style="width: 30%;" data-orderable="false">ROL</th>
								</tr>
							</thead>
							<tbody class="info-accesos"></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
			</div>
		</div>
	</div>
</div>