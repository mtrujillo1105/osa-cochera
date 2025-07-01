<div class="row">
	<div class="col-md-12">
	  <div class="card card-light">
	    <div class="card-header">
	      <h3 class="card-title"><?=$titulo;?></h3>
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
							<label for="search_codigo">Código</label>
							<input type="text" name="search_codigo" id="search_codigo" value="" placeholder="Código" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-2 form-group">
							<label for="search_documento">RUC/DNI</label>
							<input type="text" name="search_documento" id="search_documento" value="" placeholder="Documento" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-4 form-group">
							<label for="nombre_empleado">Nombre</label>
							<input type="text" name="nombre_empleado" id="nombre_empleado" value="" placeholder="Buscar empleado" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-4 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_empleado'>Nuevo</button>
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
	      <h3 class="card-title"><?=$titulo;?></h3>
	      <div class="card-tools">
	        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
	        	<i class="fas fa-minus"></i>
	        </button>
	      </div>
	    </div>
	    <div class="card-body">
				<table class="table table-striped table-bordered" id="table-empleado">
					<thead>
						<tr>
							<td style="width:08%" data-orderable="true">ID</td>
							<td style="width:08%" data-orderable="true">DNI</td>
							<td style="width:20%" data-orderable="true">NOMBRES</td>
							<td style="width:20%" data-orderable="true">APELLIDOS</td>
							<td style="width:20%" data-orderable="true">CARGO</td>
							<td style="width:05%" data-orderable="false"></td>
							<td style="width:05%" data-orderable="false"></td>
							<td style="width:05%" data-orderable="false"></td>
							<td style="width:09%" data-orderable="false">CLIENTES</td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
	    </div>
	  </div>
	</div>
</div>

<div id="add_empleado" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formEmpleado" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR EMPLEADO</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="empleado" name="empleado" value="">

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>INFORMACIÓN DEL EMPLEADO</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="tipo_documento">Tipo de documento</label>
							<select id="tipo_documento" name="tipo_documento" class="form-control"><?php
								foreach ($documentos as $i => $val){ ?>
									<option value="<?=$val->TIPDOCP_Codigo;?>"><?=$val->TIPOCC_Inciales;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-4 form-group">
							<label for="numero_documento">Número de documento (*)</label>
							<input type="number" id="numero_documento" name="numero_documento" class="form-control" placeholder="Número de documento" value="" autocomplete="off">
						</div>
						<div class="col-lg-4 form-group">
							<label for="numero_ruc">Número de ruc</label>
							<input type="number" id="numero_ruc" name="numero_ruc" class="form-control" placeholder="Indique el número de RUC" value="" autocomplete="off">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="nombres">Nombres (*)</label>
							<input type="text" id="nombres" name="nombres" class="form-control" placeholder="Indique el nombre completo" value="" autocomplete="off">
						</div>
						<div class="col-lg-4 form-group">
							<label for="apellido_paterno">Apellido paterno (*)</label>
							<input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" placeholder="Indique el apellido paterno" value="" autocomplete="off">
						</div>
						<div class="col-lg-4 form-group">
							<label for="apellido_materno">Apellido materno (*)</label>
							<input type="text" id="apellido_materno" name="apellido_materno" class="form-control" placeholder="Indique el apellido materno" value="" autocomplete="off">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="fecha_nacimiento">Fecha de nacimiento (*)</label>
							<input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" val="">
						</div>
						<div class="col-lg-3 form-group">
							<label for="genero">Genero</label>
							<select id="genero" name="genero" class="form-control">
								<option value="M">Masculino</option>
								<option value="F">Femenino</option>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="edo_civil">Estado civil</label>
							<select id="edo_civil" name="edo_civil" class="form-control"> <?php
								foreach ($edo_civil as $i => $val) { ?>
									<option value="<?=$val->ESTCP_Codigo?>"><?=$val->ESTCC_Descripcion;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="nacionalidad">Nacionalidad</label>
							<select id="nacionalidad" name="nacionalidad" class="form-control"> <?php
								foreach ($nacionalidad as $i => $val) { ?>
									<option value="<?=$val->NACP_Codigo;?>" <?=($val->NACP_Codigo == 193) ? "selected" : '';?> ><?=$val->NACC_Descripcion;?></option> <?php
								} ?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="direccion">Dirección de residencia (*)</label>
							<textarea id="direccion" name="direccion" class="form-control"></textarea>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>INFORMACIÓN DEL CONTRATACIÓN</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="cargo">Cargo</label>
							<select id="cargo" name="cargo" class="form-control"> <?php
								foreach ($cargos as $i => $val) { ?>
									<option value="<?=$val->CARGP_Codigo;?>" title="<?=$val->CARGC_Descripcion;?>"><?=($val->CARGC_Nombre != NULL) ? $val->CARGC_Nombre : 'Debe asignar un nombre';?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3">
							<label for="numero_contrato">Número de contrato</label>
							<input type="number" id="numero_contrato" name="numero_contrato" class="form-control" placeholder="Indique el número de contrato" value="" autocomplete="off">
						</div>
						<div class="col-lg-3">
							<label for="fecha_inicio">Fecha de inicio</label>
							<input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" val="">
						</div>
						<div class="col-lg-3">
							<label for="fecha_final">Fecha de vencimiento</label>
							<input type="date" id="fecha_final" name="fecha_final" class="form-control" val="">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>CUENTA BANCARIA</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="banco">Banco</label>
							<select id="banco" name="banco" class="form-control">
								<option value=""> :: SELECCIONE :: </option> <?php
								foreach ($bancos as $i => $val) { ?>
									<option value="<?=$val->BANP_Codigo;?>" title="<?=$val->BANC_Nombre;?>"><?=$val->BANC_Siglas;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-4">
							<label for="cta_soles">CTA SOLES</label>
							<input type="tel" id="cta_soles" name="cta_soles" class="form-control" placeholder="000 000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-4">
							<label for="cta_dolares">CTA DOLARES</label>
							<input type="tel" id="cta_dolares" name="cta_dolares" class="form-control" placeholder="000 000 000 000" val="" autocomplete="off">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>INFORMACIÓN DE CONTACTO</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-2">
							<label for="telefono">Telefono</label>
							<input type="tel" id="telefono" name="telefono" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-2">
							<label for="movil">Movil</label>
							<input type="tel" id="movil" name="movil" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-2">
							<label for="fax">Fax</label>
							<input type="number" id="fax" name="fax" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-3">
							<label for="correo">Correo</label>
							<input type="email" id="correo" name="correo" class="form-control" placeholder="empleado@empresa.com" val="" autocomplete="off">
						</div>
						<div class="col-lg-3">
							<label for="web">Dirección web</label>
							<input type="url" id="web" name="web" class="form-control" placeholder="http://www.google.com" val="" autocomplete="off">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_empleado()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

