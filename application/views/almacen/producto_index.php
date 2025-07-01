<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title"><?= $titulo_busqueda; ?></h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<form id="form_busqueda" method="post">
				<div class="card-body text-sm">
					<div class="row">
						<div class="col-lg-2 form-group">
							<label for="txtCodigo">Código:</label>
							<input id="txtCodigo" name="txtCodigo" type="text" class="form-control form-control-sm" placeholder="Codigo" maxlength="30" value="">
						</div>
						<div class="col-lg-3 form-group">
							<label for="txtNombre">Nombre: </label>
							<input id="txtNombre" name="txtNombre" type="text" class="form-control form-control-sm" maxlength="100" placeholder="Nombre producto" value="">
						</div>
						<div class="col-lg-2 form-group">
							<label for="txtFamilia">Familia: </label>
							<select name="txtFamilia" id="txtFamilia" class="form-control form-control-sm">
								<option value=""> TODOS </option>
								<?php
								if ($familias != NULL) {
									foreach ($familias as $i => $v) { ?>
										<option value="<?= $v->FAMI_Codigo; ?>"><?= $v->FAMI_Descripcion; ?></option>
								<?php
									}
								} ?>
							</select>
						</div>
						<div class="col-lg-2 form-group <?= ($flagBS == 'S') ? 'd-none' : ''; ?>">
							<label for="txtMarca">Marca: </label>
							<select name="txtMarca" id="txtMarca" class="form-control form-control-sm">
								<option value=""> TODOS </option>
								<?php
								if ($marcas != NULL) {
									foreach ($marcas as $i => $v) { ?>
										<option value="<?= $v->MARCP_Codigo; ?>"><?= $v->MARCC_Descripcion; ?></option>
								<?php
									}
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group <?= ($flagBS == 'S') ? 'd-none' : ''; ?>">
							<label for="txtModelo">Modelo: </label>
							<select name="txtModelo" id="txtModelo" class="form-control form-control-sm">
								<option value=""> TODOS </option>
								<?php
								if ($modelos != NULL) {
									foreach ($modelos as $indice => $val) {
										if ($val->PROD_Modelo != '') { ?>
											<option value="<?= $val->PROD_Modelo; ?>"><?= $val->PROD_Modelo; ?></option>
								<?php
										}
									}
								} ?>
							</select>
						</div>
					</div>
					<div class="row justify-content-end">
						<div class="col-lg-3 form-group text-right">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
							<button type="button" class="btn btn-success" id="nuevo">Nuevo <?= ($flagBS == 'B') ? 'Producto' : 'Servicio'; ?></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title"><?= $titulo_tabla; ?></h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered text-sm" id="table-productos">
					<thead>
						<tr>
							<th style="width: 10%" data-orderable="true">CÓDIGO</th>
							<th style="width: 40%" data-orderable="true">NOMBRE</th>
							<th style="width: 15%" data-orderable="true">FAMILIA</th>
							<th style="width: 15%" data-orderable="true"><?= ($flagBS == "B") ? "MARCA" : ""; ?></th>
							<th style="width: 15%" data-orderable="false">UNIDAD MEDIDA</th>
							<th style="width: 05%" data-orderable="false">EDITAR</th>
                                                        <th style="width: 05%" data-orderable="false">BARCODE</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="modal_producto" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">REGISTRAR <?= ($flagBS == 'B') ? 'PRODUCTO' : 'SERVICIO'; ?></h3>
			</div>
			<div class="modal-body text-sm">
				<form id="form_nvo" method="POST" action="#">
					<div class="row">
						<div class="col-lg-12 bg-dark p-2">
							DETALLES DEL <?= ($flagBS == 'B') ? 'PRODUCTO' : 'SERVICIO'; ?>
							<input type="hidden" id="id" name="id" />
							<input type="hidden" id="flagBS" name="flagBS" value="<?= $flagBS; ?>" />
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="nvo_codigo">CÓDIGO:
								<div class="input-group mt-2">
									<input type="text" id="nvo_codigo" name="nvo_codigo" class="form-control form-control-sm" autocomplete="off" <?= ($cfg->codigoProductos == '1') ? 'readonly' : ''; ?> aria-describedby='getCode' />
									<div class='input-group-append <?= ($cfg->codigoProductos == '2') ? "d-none" : ""; ?>' style="cursor: pointer">
										<span class='input-group-text bg-secondary' id='getCode'>
											<i class='fas fa-sync'></i>
										</span>
										<span class='input-group-text bg-info explainCode'>
											<i class='fas fa-exclamation'></i>
											<div class="explainImage">
												<?php
												if ($flagBS == 'B'){ ?>
													<img src="<?= $base_url; ?>public/images/docs/codigo_producto.jpg" alt="Explicación del código">
												<?php
												}
												else{ ?>
													<img src="<?= $base_url; ?>public/images/docs/codigo_servicio.jpg" alt="Explicación del código">
												<?php
												} ?>
											</div>
										</span>
									</div>
								</div>
						</div>
						<div class="col-lg-9 form-group">
							<label for="nvo_nombre">NOMBRE:</label>
							<input type="text" id="nvo_nombre" name="nvo_nombre" class="form-control form-control-sm" />
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="nvo_autocompleteCodigoSunat">CÓDIGO SUNAT:</label>
							<input type="text" id="nvo_autocompleteCodigoSunat" name="nvo_autocompleteCodigoSunat" class="form-control form-control-sm" />
						</div>
						<div class="col-lg-3 align-self-end form-group">
							<input type="text" id="nvo_codigoSunat" name="nvo_codigoSunat" class="form-control form-control-sm" readOnly />
						</div>
						<div class="col-lg-6 form-group">
							<label for="nvo_tipoAfectacion">AFECTACIÓN:</label>
							<select id="nvo_tipoAfectacion" name="nvo_tipoAfectacion" class="form-control form-control-sm">
								<?php
								foreach ($afectaciones as $i => $val) { ?>
									<option value="<?= $val->AFECT_Codigo ?>"><?= $val->AFECT_DescripcionSmall; ?></option>
								<?php
								} ?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="nvo_descripcion">DESCRIPCIÓN</label>
							<textarea class="form-control form-control-sm" id="nvo_descripcion" name="nvo_descripcion" maxlength="800" placeholder="Indique una descripción"></textarea>
							<div class="text-right text-sm font-weight-bold">
								Caracteres restantes:
								<span id="contadorCaracteres">800</span>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 form-group">
							<label for="nvo_familia">FAMILIA:</label>
							<select id="nvo_familia" name="nvo_familia" class="form-control form-control-sm">
								<?php
								foreach ($familias as $i => $val) { ?>
									<option value="<?= $val->FAMI_Codigo ?>"><?= $val->FAMI_Descripcion; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="nvo_fabricante">FABRICANTE:</label>
							<select id="nvo_fabricante" name="nvo_fabricante" class="form-control form-control-sm" <?= ($flagBS == 'S') ? 'disabled' : ''; ?>>
								<option value="0">::SELECCIONE::</option>
								<?php
								foreach ($fabricantes as $i => $val) { ?>
									<option value="<?= $val->FABRIP_Codigo ?>"><?= $val->FABRIC_Descripcion; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="nvo_marca">MARCA:</label>
							<select id="nvo_marca" name="nvo_marca" class="form-control form-control-sm" <?= ($flagBS == 'S') ? 'disabled' : ''; ?>>
								<option value="0">::SELECCIONE::</option>
								<?php
								foreach ($marcas as $i => $val) { ?>
									<option value="<?= $val->MARCP_Codigo ?>"><?= $val->MARCC_Descripcion; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="col-lg-3 form-group">
							<label for="nvo_modelo">MODELO:</label>
							<input type="text" id="nvo_modelo" name="nvo_modelo" class="form-control form-control-sm" <?= ($flagBS == 'S') ? 'disabled' : ''; ?> />
						</div>
					</div>

					<div class="row">
						<div class="col-lg-2 form-group">
							<label for="nvo_stockMin">STOCK MINIMO:</label>
							<input type="number" step="1" min="0" id="nvo_stockMin" name="nvo_stockMin" value="0" class="form-control form-control-sm" <?= ($flagBS == 'S') ? 'disabled' : ''; ?> />
						</div>

						<div class="col-lg-3 form-group">
							<label for="nvo_unidad">UNIDAD DE MEDIDA:</label>
							<select id="nvo_unidad[]" name="nvo_unidad[]" class="form-control form-control-sm">
								<?php
								foreach ($unidades as $i => $val) { ?>
									<option value="<?= $val->UNDMED_Codigo ?>" <?= ($flagBS == 'S' && trim($val->UNDMED_Simbolo) != 'ZZ') ? 'disabled' : ''; ?> <?= ($flagBS == 'B' && trim($val->UNDMED_Simbolo) == 'NIU') ? 'selected' : ''; ?> <?= ($flagBS == 'B' && trim($val->UNDMED_Simbolo) == 'ZZ') ? 'disabled' : ''; ?>><?= "$val->UNDMED_Descripcion | $val->UNDMED_Simbolo"; ?></option>
								<?php
								} ?>
							</select>
						</div>
					</div>

					<div class="row info_formapago">
						<div class="col-lg-12 form-group bg-dark p-2">
							PRECIOS
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<table class="table table-striped table-bordered" id="table-precios">
								<thead>
									<tr>
										<th data-orderable="false">CATEGORIA</th>
										<?php
										foreach ($precio_monedas as $i => $val) { ?>
											<th style="width: 15%" data-orderable="false"><?= $val->MONED_Descripcion; ?></th>
										<?php
										} ?>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($precio_categorias as $i => $val) { ?>
										<tr>
											<td><?= $val->TIPCLIC_Descripcion; ?></td>
											<?php
											foreach ($precio_monedas as $j => $value) { ?>
												<td>
													<input type="number" name="nvo_pcategoria[]" value="<?= $val->TIPCLIP_Codigo; ?>" hidden />
													<input type="number" name="nvo_pmoneda[]" value="<?= $value->MONED_Codigo; ?>" hidden />
													<input type="number" step="1.00" min="1" name="precios[]" value="0" class="form-control form-control-sm precio-<?= $val->TIPCLIP_Codigo . $value->MONED_Codigo; ?>" />
												</td>
											<?php
											} ?>
										</tr>
									<?php
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
				<button type="button" class="btn btn-dark nvo_limpiar">Limpiar</button>
				<button type="button" class="btn btn-success" onclick="registrar()">Guardar</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var flagBS = '<?= $flagBS; ?>';
	var codeProductCfg = '<?= $cfg->codigoProductos; ?>';
	var repiteCodigo = '<?= $cfg->bsCodigo; ?>';
</script>