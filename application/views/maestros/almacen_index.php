<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR ALMACEN</h3>
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
							<label for="search_descripcion">DESCRIPCIÓN</label>
							<input type="text" name="search_descripcion" id="search_descripcion" value="" placeholder="Descripción" class="form-control"/>
						</div>
						<div class="col-lg-3 form-group">
							<label for="search_tipo">TIPO</label>
							<select id="search_tipo" name="search_tipo" class="form-control">
								<option value=""> :: TODOS :: </option> <?php
								foreach ($tipo_almacen as $i => $val){ ?>
									<option value="<?=$val->TIPALMP_Codigo;?>"><?=$val->TIPALM_Descripcion;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-6 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_almacen'>Nuevo</button>
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
				<h3 class="card-title">RELACIÓN DE ALMACENES</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered text-sm" id="table-almacen">
					<thead>
						<tr>
							<th style="width:10%" data-orderable="true">CÓDIGO</th>
							<th style="width:15%" data-orderable="true">ESTABLECIMIENTO</th>
							<th style="width:15%" data-orderable="true">NOMBRE ALMACEN</th>
							<th style="width:15%" data-orderable="true">TIPO DE ALMACEN</th>
							<th style="width:15%" data-orderable="true">COMPARTIDO</th>
							<th style="width:20%" data-orderable="true">DIRECCIÓN</th>
							<th style="width:05%" data-orderable="false"></th>
							<th style="width:05%" data-orderable="false"></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="add_almacen" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="formAlmacen" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR ALMACEN</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="almacen" name="almacen" value="">

					<div class="row">
						<div class="col-lg-5 form-group">
							<label for="establecimiento">ESTABLECIMIENTO</label>
							<input type="text" name="establecimiento" readOnly class="form-control" value="<?=$nombre_establecimiento;?>">
						</div>
						<div class="col-lg-7 form-group">
							<label for="tipo_almacen">TIPO</label>
							<select id="tipo_almacen" name="tipo_almacen" class="form-control h-3"> <?php
								foreach ($tipo_almacen as $i => $val){ ?>
									<option value="<?=$val->TIPALMP_Codigo;?>"><?=$val->TIPALM_Descripcion;?></option> <?php
								} ?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-5 form-group">
							<label for="codigo_almacen">CÓDIGO *</label>
							<input type="text" id="codigo_almacen" name="codigo_almacen" class="form-control" placeholder="Indique el codigo" value="">
						</div>
						<div class="col-lg-7 form-group">
							<label for="descripcion_almacen">DESCRIPCIÓN *</label>
							<input type="text" id="descripcion_almacen" name="descripcion_almacen" class="form-control" placeholder="Indique la almacen" value="">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="compartir_almacen">COMPARTIR ALMACEN</label>
							<select id="compartir_almacen" name="compartir_almacen" class="form-control">
								<option value="0">No compartido</option>
								<option value="1">Entre compañias</option>
								<option value="2">Entre empresas</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<label for="direccion_almacen">DIRECCIÓN DEL ALMACEN</label>
							<textarea id="direccion_almacen" name="direccion_almacen" class="form-control h-5" placeholder="Indique la dirección del almacen."></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_almacen()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>