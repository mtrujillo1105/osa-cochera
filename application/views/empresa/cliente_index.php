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
                        <label for="search_codigo">Código</label>
                        <input type="text" name="search_codigo" id="search_codigo" value="" placeholder="Código" class="form-control" autocomplete="off"/>
                     </div>
                     <div class="col-lg-2 form-group">
                        <label for="search_documento">RUC/DNI</label>
                        <input type="text" name="search_documento" id="search_documento" value="" placeholder="Documento" class="form-control" autocomplete="off"/>
                     </div>
                     <div class="col-lg-3 form-group">
                        <label for="nombre_cliente">Razón Social</label>
                        <input type="text" name="nombre_cliente" id="nombre_cliente" value="" placeholder="Buscar cliente" class="form-control" autocomplete="off"/>
                     </div>
                     <div class="col-lg-2 form-group">
                        <label for="search_placa">Placa</label>
                        <input type="text" name="search_placa" id="search_placa" value="" placeholder="Buscar placa" class="form-control text-uppercase" autocomplete="off"/>
                     </div>                    
                     <div class="col-lg-3 form-group align-self-end text-right">
                        <button type="button" class="btn btn-info" id="buscar">Buscar</button>
                        <button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
                        <button type="button" class="btn btn-success" id="nuevo">Nuevo</button>
                        <input type="hidden" name="tipo_clienteabonado" id="tipo_clienteabonado" value="<?php echo $tipo_clienteabonado;?>">
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
                <table class="table table-striped table-bordered" id="table-cliente">
                    <thead>
                        <tr>
                            <td style="width:10%" data-orderable="true">CÓDIGO</td>
                            <td style="width:10%" data-orderable="true">DOCUMENTO</td>
                            <td style="width:10%" data-orderable="true">NÚMERO</td>
                            <td style="width:30%" data-orderable="true">NOMBRE Ó RAZÓN SOCIAL</td>
                            <td style="width:10%" data-orderable="true">PLACA</td>
                            <?php
                            if($tipo_clienteabonado == 8){
                            ?>
                                <td style="width:10%" data-orderable="true">SITUACIÓN</td>
                            <?php
                            }
                            ?>
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

<!-- MODAL CLIENTE-->
<div id="modal_addcliente" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <?php
                    if($tipo_clienteabonado == 8){
                    ?>
                        <b>REGISTRAR ABONADO</b>
                    <?php
                    }
                    elseif($tipo_clienteabonado == 9){
                        ?>
                         <b>REGISTRAR CLIENTE</b>
                    <?php
                    }
                    ?>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            
            <form id="formCliente" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="cliente" name="cliente" value="">

                    <div class="row">
                            <div class="col-lg-12 form-group bg-dark">
                                    <span>INFORMACIÓN DEL CLIENTE</span>
                            </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 form-group">
                            <label for="tipo_cliente">Tipo de cliente</label>
                            <select id="tipo_cliente" name="tipo_cliente" class="form-control">
                                    <option value="0">NATURAL</option>
                                    <option value="1">JURIDICO</option>
                            </select>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label for="tipo_documento">Tipo de documento</label>
                            <select id="tipo_documento" name="tipo_documento" class="form-control">
                                    <optgroup label="Natural" disabled class="documentosNatural"> <?php
                                            foreach ($documentosNatural as $i => $val){ ?>
                                                    <option class="DOC0" value="<?=$val->TIPDOCP_Codigo;?>"><?=$val->TIPOCC_Inciales;?></option> <?php
                                            } ?>
                                    </optgroup>
                                    <optgroup label="Juridico" class="documentosJuridico"> <?php
                                            foreach ($documentosJuridico as $i => $val){ ?>
                                                    <option class="DOC1" value="<?=$val->TIPCOD_Codigo;?>"><?=$val->TIPCOD_Inciales;?></option> <?php
                                            } ?>
                                    </optgroup>
                            </select>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label for="numero_documento">Número de documento (*)</label>
                            <input type="number" id="numero_documento" name="numero_documento" class="form-control" 
                                   placeholder="Número de documento" value="" autocomplete="off">
                        </div>
                        <!--div class="col-lg-3 form-group align-self-end">
                            <button type="button" class="btn btn-default btn-search-sunat">
                                <img src="< ?=$base_url;?>public/images/icons/sunat.png" class='image-size-1b'/>
                                Buscar
                            </button>
                            <span class="icon-loading-lg"></span>
                        </div-->
                    </div>

                    <!--********** JURIDICO **********-->
                    <div class="row form-group divJuridico">
                        <div class="col-lg-12">
                            <label for="razon_social">Razón social (*)</label>
                            <input type="text" id="razon_social" name="razon_social" class="form-control" 
                                   placeholder="Indique la razón social" value="" autocomplete="off">
                        </div>
                    </div>

                    <!--********** NATURAL **********-->
                    <div class="row divNatural" hidden>
                        <div class="col-lg-4 form-group">
                            <label for="nombres">Nombres (*)</label>
                            <input type="text" id="nombres" name="nombres" class="form-control" placeholder="Indique el nombre completo" 
                                   value="" autocomplete="off">
                        </div>
                        <div class="col-lg-4 form-group">
                            <label for="apellido_paterno">Apellido paterno (*)</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" 
                                   placeholder="Indique el apellido paterno" value="" autocomplete="off">
                        </div>
                        <div class="col-lg-4 form-group">
                                <label for="apellido_materno">Apellido materno (*)</label>
                                <input type="text" id="apellido_materno" name="apellido_materno" class="form-control" placeholder="Indique el apellido materno" value="" autocomplete="off">
                        </div>
                    </div>

                    <div class="row divNatural d-none" hidden>
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
                            <div class="col-lg-6 form-group">
                                    <label for="direccion">Dirección (*)</label>
                                    <textarea id="direccion" name="direccion" class="form-control" placeholder="Indique la dirección"></textarea>
                            </div>

                            <div class="col-lg-6 form-group">
                                <label for="categoria">Categoria</label>
                                <select id="categoria" name="categoria" class="form-control">
                                    <?php
                                    foreach ($categorias_cliente as $i => $val){ ?>
                                       <option value="<?=$val->TIPCLIP_Codigo;?>" 
                                    <?=($val->TIPCLIP_Codigo==$tipo_clienteabonado?"selected='selected'":"");?> >
                                          <?=$val->TIPCLIC_Descripcion;?>
                                       </option> <?php
                                    } ?>
                                </select>
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

                    <!--Datos de facturacion-->
                    <?php  $estilo_facturacion = $tipo_clienteabonado == 8?"":"d-none";?>
                    <div class="row datos_tacturacion">
                        <div class="col-lg-12 form-group bg-dark">
                                <span>DATOS DE FACTURACION</span>
                        </div>
                    </div>
                    
                    <div class="row datos_tacturacion">

                        <!--div class="col-lg-3 form-group">
                                <label for="idcliente">ID CLIENTE</label>
                                <input type="text" id="idcliente" name="idcliente" class="form-control" readonly style="cursor: pointer" title="Si desea ver el siguiente ID de cliente, haga click en la caja ID y espere."/>
                        </div-->
                        <div class="col-lg-3 form-group">
                            <label for="forma_pago">Fecha de facturación(*)</label>
                            <input type="date" id="fecha_ingreso_cliente" name="fecha_ingreso_cliente" class="form-control" title="Fecha de Ingreso">
                        </div>                                             
                        <div class="col-lg-3 form-group">
                            <label for="idcliente">Monto de facturacion mensual(*)</label>
                            <input type="text" id="monto_facturado" name="monto_facturado" class="form-control"  title="Monto de facturación" placeholder="Indique monto de facturacion mensual"/>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label for="forma_pago">Forma de pago(*)</label>
                            <select id="forma_pago" name="forma_pago" class="form-control">
                                <option value=""> :: SELECCIONE :: </option><?php
                                foreach ($forma_pago as $i => $val){ ?>
                                    <option value="<?=$val->FORPAP_Codigo;?>"><?=$val->FORPAC_Descripcion;?></option> <?php
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
                    <!--/Datos de facturacion-->

                    <div class="row d-none">                                           

                        <div class="col-lg-4 form-group">
                            <label for="vendedor">Vendedor Asignado</label>
                            <select id="vendedor" name="vendedor" class="form-control">
                                <option value=""> :: SELECCIONE :: </option> 
                                <?php
                                foreach ($vendedor as $i => $val){ ?>
                                    <option value="<?=$val->PERSP_Codigo;?>">
                                        <?="$val->PERSC_Nombre $val->PERSC_ApellidoPaterno $val->PERSC_ApellidoMaterno";?>
                                    </option> 
                                <?php
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
                                        <input type="email" id="correo" name="correo" class="form-control" placeholder="cliente@empresa.com" val="" autocomplete="off">
                                </div>
                                <div class="col-lg-3 form-group">
                                        <label for="web">Dirección web</label>
                                        <input type="url" id="web" name="web" class="form-control" placeholder="http://www.google.com" val="" autocomplete="off">
                                </div>
                        </div>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                        <button type="button" class="btn btn-dark" onclick="clean();">Limpiar</button>
                        <button type="button" class="btn btn-success" onclick="registrar_cliente();">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END MODAL CLIENTE-->

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
                    <div class="col-lg-5 form-group">
                        <label>RAZÓN SOCIAL:</label> <span class="titleRazonSocial"></span>
                    </div>
                    <div class="col-lg-3 form-group text-right">
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
                                    <td style="width:25%" data-orderable="true">NOMBRES Y APELLIDOS</td>
                                    <td style="width:15%" data-orderable="true">AREA</td>
                                    <td style="width:20%" data-orderable="true">CARGO</td>
                                    <td style="width:15%" data-orderable="false">CELULAR</td>
                                    <td style="width:15%" data-orderable="false">CORREO</td>
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
                    
                    <div class="row d-none">
                        
                        <div class="col-lg-4 form-group">
                            <label for="contacto_placa">Placa</label>
                            <input type="text" id="contacto_placa" name="contacto_placa" class="form-control text-uppercase" placeholder="Ingrese placa" 
                                   value="" autocomplete="off">
                        </div>                        
                        <div class="col-lg-4 form-group">
                            <label for="contacto_numerodoc">Número de Documento *</label>
                            <input type="text" id="contacto_numerodoc" name="contacto_numerodoc" class="form-control" placeholder="Numero de documento" 
                                   value="" autocomplete="off">
                        </div>

                    </div>
                    
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label for="contacto_nombre">Nombre y apellidos *</label>
                            <input type="text" id="contacto_nombre" name="contacto_nombre" class="form-control" placeholder="Nombre del contacto" 
                                   value="" autocomplete="off">
                        </div>
                        <div class="col-lg-4 form-group">
                            <label for="contacto_telefono">Telefono</label>
                            <input type="tel" id="contacto_telefono" name="contacto_telefono" class="form-control" placeholder="000 000 000" 
                                   val="" autocomplete="off">
                        </div>
                        <div class="col-lg-4 form-group">
                            <label for="contacto_area">Area</label>
                            <input type="text" id="contacto_area" name="contacto_area" class="form-control" placeholder="-> VENTAS" 
                                   value="" autocomplete="off">
                        </div>

                    </div>
                    
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label for="contacto_cargo">Cargo</label>
                            <input type="text" id="contacto_cargo" name="contacto_cargo" class="form-control" placeholder="-> SUPERVISOR" 
                                   value="" autocomplete="off">
                        </div>                        
                        <div class="col-lg-4 form-group">
                            <label for="contacto_movil">Celular</label>
                            <input type="tel" id="contacto_movil" name="contacto_movil" class="form-control" placeholder="000 000 000" 
                                   val="" autocomplete="off">
                        </div>
                        <div class="col-lg-4 form-group">
                            <label for="contacto_correo">Correo</label>
                            <input type="email" id="contacto_correo" name="contacto_correo" class="form-control" placeholder="cliente@empresa.com" 
                                   val="" autocomplete="off">
                        </div>                        
                    </div>
                    
                    <div class="row d-none">
                        <div class="col-lg-4 form-group">
                            <label for="contacto_fechai">Fecha Ingreso</label>
                            <input type="date" id="contacto_fechai" name="contacto_fechai" class="form-control" val="" autocomplete="off" 
                                   placeholder="Fecha Ingreso">
                        </div> 
                        <div class="col-lg-4 form-group">
                            <label for="contacto_monto">Monto mensual S/.</label>
                            <input type="text" id="contacto_monto" name="contacto_monto" class="form-control" placeholder="Monto mensual" 
                                   value="" autocomplete="off">
                        </div>                            
                        <div class="col-lg-4 form-group">
                            <label for="contacto_fax">Fáx</label>
                            <input type="number" id="contacto_fax" name="contacto_fax" class="form-control" placeholder="000 000 000" 
                                   val="" autocomplete="off">
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
                            <div class="modal-title">
                                <h4>DOCUMENTOS GENERADOS</h4>
                            </div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-2 form-group">
                                            <label>RUC:</label> <span class="titleRuc"></span>
					</div>
					<div class="col-lg-6 form-group">
                                            <label>RAZÓN SOCIAL:</label> <span class="titleRazonSocial"></span>
					</div>
                                        <div class="col-lg-3 form-group text-right">
                                            <button class="btn btn-success" id="facturar_abonado">Facturar</button>
                                            <input type="hidden" id="documento_tipo_persona" name="documento_tipo_persona">
                                        </div>
				</div>

				<div class="row">
					<div class="col-lg-12 form-group">
						<table class="table table-striped table-bordered" id="table-documentos">
							<thead>
								<tr>
									<td style="width:20%" data-orderable="true">EMPRESA REGISTRO</td>
									<td style="width:15%" data-orderable="true">DOCUMENTO</td>
									<td style="width:15%" data-orderable="true">FECHA REGISTRO</td>
									<td style="width:10%" data-orderable="false">FECHA EMISIÓN</td>
									<td style="width:10%" data-orderable="false">SERIE</td>
									<td style="width:10%" data-orderable="false">NÚMERO</td>
									<td style="width:10%" data-orderable="false">TOTAL</td>
                                                                        <td style="width:10%" data-orderable="false">SITUACIÓN</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                            <input type="hidden" id="documento_cliente" name="documento_cliente">
			</div>
		</div>
	</div>
</div>
<!-- END DOCUMENTOS -->

<!-- MODAL VEHICULOS -->
<div id="modal_vehiculo" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">VEHÍCULOS REGISTRADOS</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 form-group">
                        <label>RUC:</label> <span class="titleRuc"></span>
                    </div>
                    <div class="col-lg-5 form-group">
                        <label>RAZÓN SOCIAL:</label> <span class="titleRazonSocial"></span>
                    </div>
                    <div class="col-lg-3 form-group text-right">
                        <button type="button" class="btn btn-success btn-addVehiculo" value="">Agregar Vehiculo</button>
                        <button type="button" hidden id="btn-vehiculocliente" value=""></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 form-group">
                        <table class="table table-striped table-bordered" id="table-vehiculos">
                            <thead>
                                <tr>
                                    <td style="width:25%" data-orderable="true">NOMBRES Y APELLIDOS</td>
                                    <td style="width:15%" data-orderable="true">PLACA</td>
                                    <td style="width:20%" data-orderable="true">TARIFA</td>
                                    <td style="width:15%" data-orderable="false">TELÉFONO</td>
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

<div id="modal_addvehiculo" class="modal fade">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="formVehiculo" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title">REGISTRO DE VEHÍCULOS</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vehiculo" name="vehiculo" value="">
                    <input type="hidden" id="vehiculo_cliente" name="vehiculo_cliente" value="">
                    
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label for="vehiculo_placa">Placa *</label>
                            <input type="text" id="vehiculo_placa" name="vehiculo_placa" class="form-control text-uppercase" placeholder="Ingrese placa" 
                                   value="" autocomplete="off" maxlength="6">
                        </div>                        
                        <div class="col-lg-4 form-group">
                            <label for="vehiculo_numerodoc">Número de Documento *</label>
                            <input type="text" id="vehiculo_numerodoc" name="vehiculo_numerodoc" class="form-control" placeholder="Numero de documento" 
                                   value="" autocomplete="off" maxlength="8">
                        </div>
                        <!--div class="col-lg-4 form-group align-self-end">
                            <button type="button" class="btn btn-default btn-search-sunat-vehiculo">
                                    <img src="< ?=$base_url;?>public/images/icons/sunat.png" class='image-size-1b'/>
                                    Buscar
                            </button>
                            <span class="icon-loading-lg"></span>
                        </div-->
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label for="vehiculo_nombre">Nombre y apellidos *</label>
                            <input type="text" id="vehiculo_nombre" name="vehiculo_nombre" class="form-control" placeholder="Nombre del contacto" 
                                   value="" autocomplete="off">
                        </div>
                        <div class="col-lg-4 form-group">
                            <label for="vehiculo_telefono">Telefono</label>
                            <input type="tel" id="vehiculo_telefono" name="vehiculo_telefono" class="form-control" placeholder="000 000 000" 
                                   val="" autocomplete="off">
                        </div>
                        <div class="col-lg-4 form-group">
                            <label for="vehiculo_tarifa">Tarifa *</label>
                            <select id="vehiculo_tarifa" name="vehiculo_tarifa" class="form-control">
                                <option value=""> :: SELECCIONE :: </option><?php
                                foreach ($tarifas as $i => $val){ ?>
                                    <option value="<?=$val->TARIFP_Codigo;?>"><?=$val->EESTABC_Descripcion." - ".$val->TARIFC_Descripcion;?></option> <?php
                                } ?>
                            </select>
                        </div> 
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-dark" onclick="clean_vehiculo()">Limpiar</button>
                    <button type="button" class="btn btn-success" onclick="registrar_vehiculo()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END MODAL VEHICULOS -->