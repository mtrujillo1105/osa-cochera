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
							<label for="search_documento">RUC/DNI</label>
							<input type="text" name="search_documento" id="search_documento" value="" placeholder="Documento" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-5 form-group">
							<label for="nombre_proveedor">Razón Social</label>
							<input type="text" name="nombre_proveedor" id="nombre_proveedor" value="" placeholder="Buscar proveedor" class="form-control" autocomplete="off"/>
						</div>
						<div class="col-lg-5 form-group align-self-end">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo" data-toggle='modal' data-target='#modal_addproveedor'>Nuevo</button>
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
				<table class="table table-striped table-bordered" id="table-proveedor">
					<thead>
						<tr>
							<td style="width:10%" data-orderable="true">DOCUMENTO</td>
							<td style="width:10%" data-orderable="true">NÚMERO</td>
							<td style="width:62%" data-orderable="true">NOMBRE Ó RAZÓN SOCIAL</td>
							<td style="width:03%" data-orderable="false"></td>
							<td style="width:03%" data-orderable="false"></td>
							<td style="width:03%" data-orderable="false"></td>
							<td style="width:03%" data-orderable="false"></td>
							<td style="width:03%" data-orderable="false"></td>
							<td style="width:03%" data-orderable="false"></td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
	    </div>
	  </div>
	</div>
</div>

<!-- MODAL PROVEEDOR-->
<div id="modal_addproveedor" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formProveedor" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRAR PROVEEDOR</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="proveedor" name="proveedor" value="">

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>INFORMACIÓN DEL PROVEEDOR</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="tipo_proveedor">Tipo de proveedor</label>
							<select id="tipo_proveedor" name="tipo_proveedor" class="form-control">
								<option value="0">NATURAL</option>
								<option value="1" selected>JURIDICO</option>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="tipo_documento">Tipo de documento</label>
							<select id="tipo_documento" name="tipo_documento" class="form-control">
								<optgroup label="Natural" disabled class="documentosNatural"><?php
									foreach ($documentosNatural as $i => $val){ ?>
										<option class="DOC0" value="<?=$val->TIPDOCP_Codigo;?>"><?=$val->TIPOCC_Inciales;?></option><?php
									} ?>
								</optgroup>

								<optgroup label="Juridico" class="documentosJuridico"> <?php
									foreach ($documentosJuridico as $i => $val){ ?>
										<option class="DOC1" value="<?=$val->TIPCOD_Codigo;?>"><?=$val->TIPCOD_Inciales;?></option><?php
									} ?>
								</optgroup>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="numero_documento">Número de documento (*)</label>
							<input type="number" id="numero_documento" name="numero_documento" class="form-control" placeholder="Número de documento" value="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">&nbsp;<br>
							<button type="button" class="btn btn-default btn-search-sunat">
								<img src="<?=$base_url;?>public/images/icons/sunat.png" class='image-size-2'/>
							</button>
							<span class="icon-loading-lg"></span>
						</div>
					</div>

					<!-- JURIDICO -->
					<div class="row divJuridico">
						<div class="col-lg-12 form-group">
							<label for="razon_social">Razón social (*)</label>
							<input type="text" id="razon_social" name="razon_social" class="form-control" placeholder="Indique la razón social" value="" autocomplete="off">
						</div>
					</div>

					<!-- NATURAL -->
					<div class="row divNatural" hidden>
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

					<div class="row divNatural" hidden>
						<div class="col-lg-3 form-group">
							<label for="genero">Genero</label>
							<select id="genero" name="genero" class="form-control">
								<option value="M">MASCULINO</option>
								<option value="F">FEMENINO</option>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="edo_civil">Estado civil</label>
							<select id="edo_civil" name="edo_civil" class="form-control"> <?php
								foreach ($edo_civil as $i => $val) { ?>
									<option value="<?=$val->ESTCP_Codigo?>" <?=($val->ESTCC_Descripcion == "SOLTERO") ? "selected" : "";?>><?=$val->ESTCC_Descripcion;?></option> <?php
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
						<div class="col-lg-3 form-group">
							<label for="fecha_nacimiento">Fecha de nacimiento</label>
							<input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="direccion">Dirección (*)</label>
							<textarea id="direccion" name="direccion" class="form-control" placeholder="Indique la dirección"></textarea>
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

					<div class="row">
						<div class="col-lg-12 form-group bg-dark">
							<span>OTROS DATOS</span>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="sector_comercial">Sector Comercial</label>
							<select id="sector_comercial" name="sector_comercial" class="form-control">
								<option value=""> :: SELECCIONE :: </option><?php
								foreach ($sector_comercial as $i => $val){ ?>
									<option value="<?=$val->SECCOMP_Codigo;?>"><?=$val->SECCOMC_Descripcion;?></option> <?php
								} ?>
							</select>
						</div>

						<div class="col-lg-4 form-group" hidden>
							<label for="forma_pago">Forma de pago</label>
							<select id="forma_pago" name="forma_pago" class="form-control">
								<option value=""> :: SELECCIONE :: </option><?php
								foreach ($forma_pago as $i => $val){ ?>
									<option value="<?=$val->FORPAP_Codigo;?>"><?=$val->FORPAC_Descripcion;?></option> <?php
								} ?>
							</select>
						</div>

						<div class="col-lg-4 form-group" hidden>
							<label for="categoria">Categoria</label>
							<select id="categoria" name="categoria" class="form-control">
								<option value=""> :: SELECCIONE :: </option><?php
								foreach ($categorias_proveedor as $i => $val){ ?>
									<option value="<?=$val->TIPCLIP_Codigo;?>"><?=$val->TIPCLIC_Descripcion;?></option> <?php
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
							<input type="email" id="correo" name="correo" class="form-control" placeholder="proveedor@empresa.com" val="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">
							<label for="web">Dirección web</label>
							<input type="url" id="web" name="web" class="form-control" placeholder="http://www.google.com" val="" autocomplete="off">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean()">Limpiar</button>
					<button type="button" class="btn btn-success" onclick="registrar_proveedor()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- END MODAL PROVEEDOR-->

<!-- MODAL SUCURSALES -->
<div id="modal_sucursales" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">SUCURSALES</h4>
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
						<button type="button" class="btn btn-success btn-addSucursal" value="">Agregar Sucursal</button>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 form-group">
						<table class="table table-striped table-bordered" id="table-sucursales">
							<thead>
								<tr>
									<td style="width:15%" data-orderable="true">NOMBRE</td>
									<td style="width:15%" data-orderable="true">TIPO</td>
									<td style="width:40%" data-orderable="true">DIRECCIÓN</td>
									<td style="width:20%" data-orderable="true">UBIGEO</td>
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

<div id="modal_addsucursal" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formSucursal" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRO DE SUCURSAL</h4>
				</div>
				<div class="modal-body">

					<input type="hidden" id="sucursal" name="sucursal" value="">
					<input type="hidden" id="sucursal_empresa" name="sucursal_empresa" value="">

					<div class="row">
						<div class="col-lg-6 form-group">
							<label for="establecimiento_nombre">Nombre *</label>
							<input type="text" id="establecimiento_nombre" name="establecimiento_nombre" class="form-control" placeholder="Nombre del establecimiento" value="" autocomplete="off">
						</div>
						<div class="col-lg-6 form-group">
							<label for="establecimiento_tipo">Tipo de establecimiento</label>
							<select id="establecimiento_tipo" name="establecimiento_tipo" class="form-control"><?php
								foreach ($tipo_establecimiento as $i => $val){ ?>
									<option value="<?=$val->TESTP_Codigo?>"><?=$val->TESTC_Descripcion;?></option><?php
								} ?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="establecimiento_direccion">Dirección (*)</label>
							<textarea id="establecimiento_direccion" name="establecimiento_direccion" class="form-control" placeholder="Indique la dirección del establecimiento"></textarea>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="establecimiento_departamento">Departamento</label>
							<select id="establecimiento_departamento" name="establecimiento_departamento" class="form-control"><?php
							foreach ($departamentos as $i => $val){ ?>
									<option value="<?=$val->UBIGC_CodDpto;?>" <?=($val->UBIGC_CodDpto == "15") ? "selected" : ""?> ><?=$val->UBIGC_DescripcionDpto;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-4 form-group">
							<label for="establecimiento_provincia">Provincia</label>
							<select id="establecimiento_provincia" name="establecimiento_provincia" class="form-control"><?php
								foreach ($provincias as $i => $val){ ?>
									<option value="<?=$val->UBIGC_CodProv;?>" <?=($val->UBIGC_CodProv == "01") ? "selected" : "";?>><?=$val->UBIGC_DescripcionProv;?></option> <?php
								} ?>
							</select>
						</div>
						<div class="col-lg-4 form-group">
							<label for="establecimiento_distrito">Distrito</label>
							<select id="establecimiento_distrito" name="establecimiento_distrito" class="form-control"><?php
							foreach ($distritos as $i => $val){ ?>
									<option value="<?=$val->UBIGC_CodDist;?>" <?=($val->UBIGC_CodDist == "01") ? "selected" : "";?>><?=$val->UBIGC_Descripcion;?></option> <?php 
								} ?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean_sucursal()">Limpiar</button>
					<button type="button" class="btn btn-success" onclick="registrar_sucursal()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- END MODAL SUCURSALES -->

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
						<button type="button" hidden id="btn-ctabancopersona" value=""></button>
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

<!-- MODAL CONTACTOS -->
<div id="modal_contactos" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">CONTACTOS REGISTRADOS</h4>
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
						<button type="button" class="btn btn-success btn-addContacto" value="">Agregar Contacto</button>
						<button type="button" hidden id="btn-contactoempresa" value=""></button>
						<button type="button" hidden id="btn-contactopersona" value=""></button>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 form-group">
						<table class="table table-striped table-bordered" id="table-contactos">
							<thead>
								<tr>
									<td style="width:20%" data-orderable="true">CONTACTO</td>
									<td style="width:15%" data-orderable="true">AREA</td>
									<td style="width:15%" data-orderable="true">CARGO</td>
									<td style="width:10%" data-orderable="false">TELEFONO</td>
									<td style="width:10%" data-orderable="false">MÓVIL</td>
									<td style="width:10%" data-orderable="false">FÁX</td>
									<td style="width:10%" data-orderable="false">CORREO</td>
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

<div id="modal_addcontacto" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="formContacto" method="POST">
				<div class="modal-header">
					<h4 class="modal-title">REGISTRO DE CONTACTO</h4>
				</div>
				<div class="modal-body">

					<input type="hidden" id="contacto" name="contacto" value="">
					<input type="hidden" id="contacto_empresa" name="contacto_empresa" value="">
					<input type="hidden" id="contacto_persona" name="contacto_persona" value="">

					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="contacto_nombre">Nombre y apellidos *</label>
							<input type="text" id="contacto_nombre" name="contacto_nombre" class="form-control" placeholder="Nombre del contacto" value="" autocomplete="off">
						</div>

						<div class="col-lg-4 form-group">
							<label for="contacto_area">Area</label>
							<input type="text" id="contacto_area" name="contacto_area" class="form-control" placeholder="-> VENTAS" value="" autocomplete="off">
						</div>

						<div class="col-lg-4 form-group">
							<label for="contacto_cargo">Cargo</label>
							<input type="text" id="contacto_cargo" name="contacto_cargo" class="form-control" placeholder="-> SUPERVISOR" value="" autocomplete="off">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="contacto_telefono">Telefono</label>
							<input type="tel" id="contacto_telefono" name="contacto_telefono" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">
							<label for="contacto_movil">Móvil</label>
							<input type="tel" id="contacto_movil" name="contacto_movil" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">
							<label for="contacto_fax">Fáx</label>
							<input type="number" id="contacto_fax" name="contacto_fax" class="form-control" placeholder="000 000 000" val="" autocomplete="off">
						</div>
						<div class="col-lg-3 form-group">
							<label for="contacto_correo">Correo</label>
							<input type="email" id="contacto_correo" name="contacto_correo" class="form-control" placeholder="proveedor@empresa.com" val="" autocomplete="off">
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					<button type="button" class="btn btn-dark" onclick="clean_contacto()">Limpiar</button>
					<button type="button" class="btn btn-success" onclick="registrar_contacto()">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- END MODAL CONTACTOS -->

<!-- MODAL DOCUMENTOS -->
<div id="modal_documentos" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">DOCUMENTOS GENERADOS</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-2 form-group">
						<label>RUC:</label> <span class="titleRuc"></span>
					</div>
					<div class="col-lg-10 form-group">
						<label>RAZÓN SOCIAL:</label> <span class="titleRazonSocial"></span>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 form-group">
						<table class="table table-striped table-bordered" id="table-documentos">
							<thead>
								<tr>
									<td style="width:30%" data-orderable="true">EMPRESA REGISTRO</td>
									<td style="width:15%" data-orderable="true">DOCUMENTO</td>
									<td style="width:15%" data-orderable="true">FECHA REGISTRO</td>
									<td style="width:10%" data-orderable="false">FECHA EMISIÓN</td>
									<td style="width:10%" data-orderable="false">SERIE</td>
									<td style="width:10%" data-orderable="false">NÚMERO</td>
									<td style="width:10%" data-orderable="false">TOTAL</td>
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
<!-- END DOCUMENTOS -->