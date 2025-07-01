<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR PROYECTOS</h3>
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
							<input type="text" name="search_proyecto" id="search_proyecto" value="" placeholder="Titulo del proyecto" class="form-control"/>
						</div>
						<div class="col-lg-2 form-group">
							<input type="hidden" name="search_cliente" id="search_cliente" value="" class="form-control"/>
							<input type="number" name="search_ruc" id="search_ruc" value="" placeholder="Número de ruc" class="form-control"/>
						</div>
						<div class="col-lg-3 form-group">
							<input type="text" name="search_razon_social" id="search_razon_social" value="" placeholder="Razón social" class="form-control"/>
						</div>

						<div class="col-lg-4 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#add_proyecto'>Nuevo</button>
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
				<h3 class="card-title">RELACIÓN DE PROYECTOS</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered" id="table-proyecto">
					<thead>
						<tr>
							<td style="width:10%" data-orderable="true">RUC</td>
							<td style="width:30%" data-orderable="true">RAZÓN SOCIAL</td>
							<td style="width:40%" data-orderable="true">PROYECTO</td>
							<td style="width:05%" data-orderable="false"></td>
							<td style="width:05%" data-orderable="false"></td>
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

<div id="add_proyecto" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formProyecto" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR PROYECTO</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="proyecto" name="proyecto" value="">

					<div class="row">
						<div class="col-lg-12 form-group">
							<span>CLIENTE</span>
							<input type="hidden" id="cliente" name="cliente" value="">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="ruc">RUC</label>
							<input type="number" id="ruc" name="ruc" class="form-control" placeholder="Número de documento" value="">
						</div>
						<div class="col-lg-9">
							<label for="razon_social">RAZÓN SOCIAL</label>
							<input type="text" id="razon_social" name="razon_social" class="form-control" placeholder="Razón social" value="">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<span>INFORMACIÓN DEL PROYECTO</span>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6 form-group">
							<label for="nombre_proyecto">NOMBRE *</label>
							<input type="text" id="nombre_proyecto" name="nombre_proyecto" class="form-control" placeholder="Nombre del proyecto" value="">
						</div>
						<div class="col-lg-3 form-group">
							<label for="fecha_inicio">FECHA DE INICIO *</label>
							<input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="">
						</div>
						<div class="col-lg-3 form-group">
							<label for="fecha_final">FECHA FINAL *</label>
							<input type="date" id="fecha_final" name="fecha_final" class="form-control" value="">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="codigo_proyecto">DESCRIPCIÓN *</label>
							<textarea id="descripcion_proyecto" name="descripcion_proyecto" class="form-control" placeholder="Descripción del proyecto"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="registrar_proyecto()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="modalDirections" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">DIRECCIONES REGISTRADAS</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 form-group">
						<span>CLIENTE</span>
					</div>
				</div>
				<div class="row">
          <div class="col-lg-2 form-group">
            <label>RUC / DNI:</label> <span class="modal_ruc"></span>
          </div>
          <div class="col-lg-10 form-group">
            <label>RAZÓN SOCIAL:</label> <span class="modal_razonSocial"></span>
          </div>
        </div>

        <div class="row">
					<div class="col-lg-12 form-group">
						<span>INFORMACIÓN DEL PROYECTO</span>
					</div>
				</div>
        <div class="row">
          <div class="col-lg-6 form-group">
            <label>TITULO: </label> <span class="modal_titulo"></span>
          </div>
          <div class="col-lg-3 form-group">
            <label>FECHA INICIO:</label> <span class="modal_fechaInicio"></span>
          </div>
          <div class="col-lg-3 form-group">
            <label>FECHA FINAL:</label> <span class="modal_fechaFinal"></span>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-10 form-group">
            <label>DESCRIPCIÓN:</label> <span class="modal_descripcion"></span>
          </div>
          <div class="col-lg-2 form-group">
          	<button type="button" class="btn btn-success" data-toggle='modal' data-target='#add_directions' onclick="clean_direction();">Agregar dirección</button>
          </div>
        </div>

				<div class="row">
          <div class="col-lg-12 form-group">
            <table class="table table-striped table-bordered" id="table-directions">
              <thead>
                <tr>
                  <td style="width:30%" data-orderable="true">DIRECCIÓN</td>
                  <td style="width:24%" data-orderable="true">REFERENCIA</td>
                  <td style="width:12%" data-orderable="true">DEPARTAMENTO</td>
                  <td style="width:12%" data-orderable="true">PROVINCIA</td>
                  <td style="width:12%" data-orderable="true">DISTRITO</td>
                  <td style="width:05%" data-orderable="false"></td>
                  <td style="width:05%" data-orderable="false"></td>
                </tr>
              </thead>
              <tbody></tbody>
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

<div id="add_directions" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formDirections" method="POST">
				<div class="modal-header">
					<h4 class="modal-title"></h4>
				</div>

				<div class="modal-body">
					<input type="hidden" id="direction_id" name="direction_id" value="">
					<input type="hidden" id="proyecto_id" name="proyecto_id" value="">

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="direccion_proyecto">DIRECCIÓN *</label>
							<input type="text" id="direccion_proyecto" name="direccion_proyecto" class="form-control" placeholder="Indica la dirección del proyecto" value="" maxlength="200">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="referencia_proyecto">REFERENCIA</label>
							<input type="text" id="referencia_proyecto" name="referencia_proyecto" class="form-control" placeholder="Agrega una referencia" value="" maxlength="200">
						</div>
					</div>
					<div class="row">
            <div class="col-lg-4 form-group">
                <label for="departamento">Departamento</label>
                <select id="departamento" name="departamento" class="form-control"><?php
                    foreach ($departamentos as $i => $val){ ?>
                        <option value="<?=$val->UBIGC_CodDpto;?>" <?=($val->UBIGC_CodDpto == "15") ? "selected" : ""?> ><?=$val->UBIGC_DescripcionDpto;?></option> <?php
                    } ?>
                </select>
            </div>
            <div class="col-lg-4 form-group">
                <label for="provincia">Provincia</label>
                <select id="provincia" name="provincia" class="form-control"><?php
                    foreach ($provincias as $i => $val){ ?>
                        <option value="<?=$val->UBIGC_CodProv;?>" <?=($val->UBIGC_CodProv == "01") ? "selected" : "";?>><?=$val->UBIGC_DescripcionProv;?></option> <?php
                    } ?>
                </select>
            </div>
            <div class="col-lg-4 form-group">
                <label for="distrito">Distrito</label>
                <select id="distrito" name="distrito" class="form-control"><?php
                    foreach ($distritos as $i => $val){ ?>
                        <option value="<?=$val->UBIGC_CodDist;?>" <?=($val->UBIGC_CodDist == "01") ? "selected" : "";?>><?=$val->UBIGC_Descripcion;?></option> <?php 
                    } ?>
                </select>
            </div>
        	</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean_direction();">Limpiar</button>
					<button type="button" class="btn btn-success" accesskey="x" onclick="register_directions();">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="modal_infoProyecto" class="modal fade">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center">INFORMACIÓN DEL PROYECTO</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
					<div class="col-lg-12 form-group">
						<span>CLIENTE</span>
					</div>
				</div>
        <div class="row">
          <div class="col-lg-3 form-group">
            <label>RUC / DNI:</label> <span class="modal_ruc"></span>
          </div>
          <div class="col-lg-9 form-group">
            <label>RAZÓN SOCIAL:</label> <span class="modal_razonSocial"></span>
          </div>
        </div>

        <div class="row">
					<div class="col-lg-12 form-group">
						<span>PROYECTO</span>
					</div>
				</div>
        <div class="row">
          <div class="col-lg-6 form-group">
            <label>TITULO: </label> <span class="modal_titulo"></span>
          </div>
          <div class="col-lg-3 form-group">
            <label>FECHA INICIO:</label> <span class="modal_fechaInicio"></span>
          </div>
          <div class="col-lg-3 form-group">
            <label>FECHA FINAL:</label> <span class="modal_fechaFinal"></span>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12 form-group">
            <label>DESCRIPCIÓN:</label> <span class="modal_descripcion"></span>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12 form-group">
            <table class="table table-striped table-bordered" id="table-comprobantes">
              <thead>
                <tr>
                  <th style="width:10%;" data-orderable="true">FECHA</th>
                  <th style="width:35%;" data-orderable="true">EMPRESA EMISORA</th>
                  <th style="width:15%;" data-orderable="true">DOCUMENTO</th>
                  <th style="width:05%;" data-orderable="true">SERIE</th>
                  <th style="width:05%;" data-orderable="true">NÚMERO</th>
                  <th style="width:05%;" data-orderable="true">MONEDA</th>
                  <th style="width:10%;" data-orderable="true">IMPORTE</th>
                  <th style="width:10%;" data-orderable="true">ESTADO</th>
                  <th style="width:05%;" data-orderable="true"></th>
                </tr>
              </thead>
              <tbody class="comprobantes-info"></tbody>
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