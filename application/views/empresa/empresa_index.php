<div class="row">
	<div class="col-md-12">
	  <div class="card card-light">
	    <div class="card-header">
	      <h3 class="card-title"><?=$titulo_busqueda;?></h3>
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
							<label for="search_documento">RUC</label>
							<input type="text" name="search_documento" id="search_documento" value="" placeholder="Documento" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-5 form-group">
							<label for="nombre_empresa">Razón Social</label>
							<input type="text" name="nombre_empresa" id="nombre_empresa" value="" placeholder="Buscar empresa" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-5 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
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
	      <h3 class="card-title"><?=$titulo_tabla;?></h3>
	      <div class="card-tools">
	        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
	        	<i class="fas fa-minus"></i>
	        </button>
	      </div>
	    </div>
	    <div class="card-body">
				<table class="table table-striped table-bordered" id="table-empresa">
					<thead>
						<tr>
							<td style="width:10%" data-orderable="true">RUC</td>
							<td style="width:30%" data-orderable="true">RAZÓN SOCIAL</td>
							<td style="width:40%" data-orderable="false">DIRECCIÓN</td>
							<td style="width:12%" data-orderable="false">TELEFONOS</td>
							<td style="width:04%" data-orderable="false"></td>
							<td style="width:04%" data-orderable="false"></td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
	    </div>
	  </div>
	</div>
</div>

<!-- MODAL EMPRESA-->
<div id="modal_addempresa" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formEmpresa" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRO DE EMPRESA</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="empresa" name="empresa" value="">

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>INFORMACIÓN DEL EMPRESA</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-2 form-group">
							<label for="tipo_documento">Tipo de documento</label>
							<select id="tipo_documento" name="tipo_documento" class="form-control"> <?php
								foreach ($documentosJuridico as $i => $val){ ?>
									<option class="DOC1" value="<?=$val->TIPCOD_Codigo;?>"><?=$val->TIPCOD_Inciales;?></option><?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="numero_documento">Número de documento (*)</label>
							<input type="number" id="numero_documento" name="numero_documento" class="form-control" placeholder="Número de documento" value="" autocomplete="off">
						</div>
						<div class="col-lg-7 form-group">
							<label for="razon_social">Razón social (*)</label>
							<input type="text" id="razon_social" name="razon_social" class="form-control" placeholder="Indique la razón social" value="" autocomplete="off">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="direccion">Dirección (*)</label>
							<textarea id="direccion" name="direccion" class="form-control" placeholder="Indique la dirección"></textarea>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="departamento">Departamento</label>
							<select id="departamento" name="departamento" class="form-control"><?php
								foreach ($departamentos as $i => $val){ ?>
									<option value="<?=$val->UBIGC_CodDpto;?>" <?=($val->UBIGC_CodDpto == "15") ? "selected" : ""?> ><?=$val->UBIGC_DescripcionDpto;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="provincia">Provincia</label>
							<select id="provincia" name="provincia" class="form-control"><?php
								foreach ($provincias as $i => $val){ ?>
									<option value="<?=$val->UBIGC_CodProv;?>" <?=($val->UBIGC_CodProv == "01") ? "selected" : "";?>><?=$val->UBIGC_DescripcionProv;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="distrito">Distrito</label>
							<select id="distrito" name="distrito" class="form-control"><?php
								foreach ($distritos as $i => $val){ ?>
									<option value="<?=$val->UBIGC_CodDist;?>" <?=($val->UBIGC_CodDist == "01") ? "selected" : "";?>><?=$val->UBIGC_Descripcion;?></option> <?php 
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="sector_comercial">Sector Comercial</label>
							<select id="sector_comercial" name="sector_comercial" class="form-control">
								<option value=""> :: SELECCIONE :: </option><?php
								foreach ($sector_comercial as $i => $val){ ?>
									<option value="<?=$val->SECCOMP_Codigo;?>"><?=$val->SECCOMC_Descripcion;?></option> <?php
								} ?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>INFORMACIÓN DE CONTACTO</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-2 form-group">
							<label for="telefono">Telefono</label>
							<input type="tel" id="telefono" name="telefono" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-2 form-group">
							<label for="movil">Movil</label>
							<input type="tel" id="movil" name="movil" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-2 form-group">
							<label for="fax">Fax</label>
							<input type="number" id="fax" name="fax" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">
							<label for="correo">Correo</label>
							<input type="email" id="correo" name="correo" class="form-control" placeholder="empresa@empresa.com" val="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">
							<label for="web">Facebook e Instragram</label>
							<input type="url" id="web" name="web" class="form-control" placeholder="" val="http://www.google.com" autocomplete="off">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" onclick="registrar_empresa()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- END MODAL EMPRESA-->

<!-- MODAL BANCOS -->
<div id="modal_bancos" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">CUENTAS BANCARIAS</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-3 form-group">
						<label>RUC:</label> <span class="titleRuc"></span>
					</div>
					<div class="col-lg-6 form-group">
						<label>RAZÓN SOCIAL:</label> <span class="titleRazonSocial"></span>
					</div>
					<div class="col-lg-3 form-group">
						<button type="button" class="btn btn-success btn-addBanco" value="">Agregar Cuenta</button>
						<button type="button" hidden id="btn-ctabancoempresa" value=""></button>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 form-group">
						<table class="table table-striped table-bordered" id="table-bancos">
							<thead>
								<tr>
									<td style="width:20%" data-orderable="true">BANCO</td>
									<td style="width:20%" data-orderable="true">TITULAR</td>
									<td style="width:10%" data-orderable="true">TIPO</td>
									<td style="width:10%" data-orderable="true">MONEDA</td>
									<td style="width:15%" data-orderable="false">N° CUENTA</td>
									<td style="width:15%" data-orderable="false">INTERBANCARIA</td>
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

<div id="modal_addctabancaria" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formCtaBancaria" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRO DE CUENTA BANCARIA</h4>
				</div>
				<div class="modal-body">

					<input type="hidden" id="cta_bancaria" name="cta_bancaria" value="">
					<input type="hidden" id="cta_bancaria_empresa" name="cta_bancaria_empresa" value="">
					<input type="hidden" id="cta_bancaria_persona" name="cta_bancaria_persona" value="">

					<div class="row">
						<div class="col-lg-6 form-group">
							<label for="banco">Banco *</label>
							<select id="banco" name="banco" class="form-control"><?php
								foreach ($bancos as $i => $val){ ?>
									<option value="<?=$val->BANP_Codigo;?>"><?=$val->BANC_Nombre;?></option> <?php
								} ?>
							</select>
						</div>

						<div class="col-lg-6 form-group">
							<label for="cta_bancaria_titular">Titular *</label>
							<input type="text" id="cta_bancaria_titular" name="cta_bancaria_titular" class="form-control" placeholder="Titular de la cuenta" value="" autocomplete="off">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="cta_bancaria_tipo">Tipo de cuenta *</label>
							<select id="cta_bancaria_tipo" name="cta_bancaria_tipo" class="form-control">
								<option value="1">AHORROS</option>
								<option value="2">CORRIENTE</option>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="cta_bancaria_moneda">Moneda *</label>
								<select id="cta_bancaria_moneda" name="cta_bancaria_moneda" class="form-control"><?php
								foreach ($monedas as $i => $val){ ?>
									<option value="<?=$val->MONED_Codigo;?>"><?="$val->MONED_Simbolo | $val->MONED_smallName";?></option> <?php
								} ?>
							</select>
						</div>

						<div class="col-lg-3 form-group">
							<label for="cta_bancaria_numero">N° de cuenta *</label>
							<input type="number" id="cta_bancaria_numero" name="cta_bancaria_numero" class="form-control" placeholder="Número de la cuenta" value="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">
							<label for="cta_bancaria_interbancaria">Interbancaria </label>
							<input type="number" id="cta_bancaria_interbancaria" name="cta_bancaria_interbancaria" class="form-control" placeholder="Número de cuenta interbancaria" value="" autocomplete="off">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean_CtaBancaria()">Limpiar</button>
					<button type="button" class="btn btn-success" onclick="registrar_CtaBancaria()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- END MODAL BANCOS -->