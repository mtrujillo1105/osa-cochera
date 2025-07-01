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
						<div class="col-lg-3 form-group">
							<select id="search_modulo" name="search_modulo" class="form-control">
								<option value=""> :: TODOS :: </option> <?php
								foreach ($modulos as $i => $val){ ?>
									<option value="<?=$val->MENU_Codigo;?>"><?=$val->MENU_Titulo;?></option> <?php
								} ?>                    
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<input type="text" name="search_menu" id="search_menu" value="" placeholder="Descripción" class="form-control"/>
						</div>
						<div class="col-lg-6 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_menu'>Nuevo</button>
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
				<table class="table table-striped table-bordered" id="table-menu">
					<thead>
						<tr>
							<td style="width:10%" data-orderable="true">MODULO</td>
							<td style="width:10%" data-orderable="true">TITULO</td>
							<td style="width:10%" data-orderable="true">ICONO</td>
							<td style="width:10%" data-orderable="true">URL</td>
							<td style="width:10%" data-orderable="true">ACCESO</td>
							<td style="width:20%" data-orderable="true">ORDEN</td>
							<td style="width:10%" data-orderable="true">ESTADO</td>
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

<div id="add_menu" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="formMenu" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR MENU</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="menu" name="menu" value="">

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="modulo_padre">MODULO</label>
							<select id="modulo_padre" name="modulo_padre" class="form-control">
								<optgroup label="CREAR MODULO">
									<option value="0">MODULO</option>
								</optgroup>
								<optgroup label="ASIGNAR MODULO"> <?php
									foreach ($modulos as $i => $val){ ?>
										<option value="<?=$val->MENU_Codigo;?>"><?=$val->MENU_Titulo;?></option> <?php
									} ?>
								</optgroup>
							</select>
						</div>
						<div class="col-lg-8 form-group">
							<label for="modulo_titulo">TITULO *</label>
							<input type="text" id="modulo_titulo" name="modulo_titulo" class="form-control" placeholder="Titulo" value="">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6 form-group">
							<label for="modulo_url">URL</label>
							<input type="text" id="modulo_url" name="modulo_url" class="form-control" placeholder="carpeta/controlador/metodo" value="">
						</div>
						<div class="col-lg-3 form-group">
							<label for="modulo_access">ACCESO DIRECTO</label>
							<select id="modulo_access" name="modulo_access" class="form-control">
								<option value="0">INHABILITADO</option>
								<option value="1">HABILITADO</option>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="modulo_order">ORDEN</label>
							<input type="number" min="0" step="1" max="999" id="modulo_order" name="modulo_order" class="form-control " placeholder="0" value="0">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="modulo_icono">ICONO <i>(CLASES CSS)</i></label>
							<textarea id="modulo_icono" name="modulo_icono" class="form-control" placeholder="Clases css del icono (fa fa-address-book)"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_menu()">Guardar Registro</button>
				</div>
			</form>
		</div>
	</div>
</div>