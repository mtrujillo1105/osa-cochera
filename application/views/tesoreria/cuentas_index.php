<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

<style>
	.estadoC{
		display: inline-block;
		position: relative;
		padding: 0.5em;
		text-align: center;
		cursor: help;
		width: 6.5em;
	}
	.estadoC:hover .leyenda{
		opacity: 1;
	}

	.leyenda{
		opacity: 0;
		position: absolute;
		top: 0%;
		right: 100%;
		padding: 1em;
		background: rgba(255,255,255,1);
		width: 30em;
		-webkit-transition: all 1s ease;
	}
	.leyenda i:first-child{
		border-radius: 0.5em 0.5em 0em 0em;
	}
	.leyenda i:last-child{
		border-radius: 0em 0em 0.5em 0.5em;
	}

	.info_formapago{
		display: none;
	}
	.viewNotas{
		display: none;
	}
</style>

<div class="container-fluid">
	<div class="row header">
		<div class="col-md-12 col-lg-12">
			<div><?=$titulo_busqueda;?></div>
		</div>
	</div>
	<form id="form_busqueda" method="post">
		<div class="row fuente8 py-1">
			<div class="col-sm-2 col-md-2 col-lg-2 form-group">
				<label for="fechai">Fecha inicial: </label>
				<input id="fechai" name="fechai" type="date" class="form-control w-porc-90 h-1" value="<?=$fechai;?>">
			</div>
			<div class="col-sm-2 col-md-2 col-lg-2 form-group">
				<label for="fechaf">Fecha Final: </label>
				<input id="fechaf" name="fechaf" type="date" class="form-control w-porc-90 h-1" value="<?=$fechaf;?>">
			</div>
			<div class="col-sm-1 col-md-1 col-lg-1 form-group">
				<label for="serie">Serie: </label>
				<input id="serie" name="serie" type="text" class="form-control w-porc-90 h-1" value="<?=$serie;?>">
			</div>
			<div class="col-sm-1 col-md-1 col-lg-1 form-group">
				<label for="numero">Número: </label>
				<input id="numero" name="numero" type="number" class="form-control w-porc-90 h-1" value="<?=$numero;?>">
			</div>
			<div class="col-sm-2 col-md-2 col-lg-2 form-group">
				<label for="estado_pago">Estado de pago: </label>
				<select name="estado_pago" id="estado_pago" class="form-control w-porc-90 h-2">
					<option value="T" <?=($cboestadopago == "" || $cboestadopago == "T") ? "selected" : "";?>>Todos</option>
					<option value="C" <?=($cboestadopago == "C") ? "selected" : "";?>>Cancelado</option>
					<option value="P" <?=($cboestadopago == "P") ? "selected" : "";?>>Pendiente</option>
				</select>
			</div>
			<div class="col-sm-2 col-md-2 col-lg-2 form-group">
				<label for="comprobante">Tipo de documento: </label>
				<select name="comprobante" id="comprobante" class="form-control w-porc-90 h-2">
					<option value="T" <?=($cboTipoDoc == "T") ? "selected" : "";?>>Todos</option>
					<option value="8" <?=($cboTipoDoc == "8") ? "selected" : "";?>>Factura</option>
					<option value="9" <?=($cboTipoDoc == "9") ? "selected" : "";?>>Boleta</option>
					<option value="14" <?=($cboTipoDoc == "14") ? "selected" : "";?>>Comprobantes</option>
				</select>
			</div>
		</div>

		<div class="row fuente8">
			<input id="cliente" name="cliente" type="hidden" value="">
			<input id="proveedor" name="proveedor" type="hidden" value="">

			<div class="col-sm-2 col-md-2 col-lg-2 form-group">
				<label for="">RUC/DNI: </label>
				<input id="ruc_cliente" name="ruc_cliente" type="<?=($tipo_cuenta == '1') ? "number" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="N° documento" value="">
				<input id="ruc_proveedor" name="ruc_proveedor" type="<?=($tipo_cuenta == '2') ? "number" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="N° documento" value="">
			</div>
			<div class="col-sm-3 col-md-3 col-lg-3 form-group">
				<label for="">Razón Social: </label>
				<input id="nombre_cliente" name="nombre_cliente" type="<?=($tipo_cuenta == '1') ? "text" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="Razón social" value="">
				<input id="nombre_proveedor" name="nombre_proveedor" type="<?=($tipo_cuenta == '2') ? "text" : "hidden";?>" class="form-control w-porc-90 h-1" placeholder="Razón social" value="">
			</div>

			<div class="col-sm-1 col-md-1 col-lg-1"><br>
				<button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal_totales">TOTALES</button>
			</div>
		</div>
	</form>
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12 pall-0">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12 pall-0">
					<div class="acciones">
						<div id="botonBusqueda">
							<ul class="lista_botones" data-toggle="modal" data-target="#modal_nuevo_pago">
								<li id="nuevo"> Nuevo <?=($tipo_cuenta == 1) ? "Cobro" : "Pago";?></li>
							</ul>
							<ul id="limpiarC" class="lista_botones">
								<li id="limpiar">Limpiar</li>
							</ul>
							<ul id="buscarC" class="lista_botones">
								<li id="buscar">Buscar</li>
							</ul>
						</div>
						<div id="lineaResultado">Registros encontrados</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12 pall-0">
					<div class="header text-align-center"><?=$titulo;?></div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12 pall-0">
					<div id="cargando_datos" class="loading-table">
						<img src="<?=base_url().'images/loading.gif?='.IMG;?>">
					</div>
					<table class="fuente8 display" width="100%" cellspacing="0" cellpadding="3" border="0" id="table-cuentas">
						<thead>
							<tr class="cabeceraTabla">
								<td style="width:08%" data-orderable="true">FECHA</td>
								<td style="width:12%" data-orderable="true">TIPO DOC</td>
								<td style="width:04%" data-orderable="true">SERIE</td>
								<td style="width:06%" data-orderable="true">NÚMERO</td>
								<td style="width:26%" data-orderable="true">RAZON SOCIAL</td>
								<td style="width:08%" data-orderable="false">TOTAL</td>
								<td style="width:08%" data-orderable="false">SALDO</td>
								<td style="width:10%" data-orderable="false">ESTADO</td>
								<td style="width:06%" data-orderable="false">&nbsp;</td>
								<td style="width:06%" data-orderable="false">&nbsp;</td>
								<td style="width:06%" data-orderable="false">&nbsp;</td>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal_totales" class="modal fade" role="dialog">
	<div class="modal-dialog w-porc-60">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center">TOTAL DE CUENTAS</h4>
			</div>
			<div class="modal-body panel panel-default">
				<div class="row form-group">
					<div class="col-sm-11 col-md-11 col-lg-11">
						<table class="fuente8 display" id="table-totales">
							<thead>
								<tr>
									<th style="width:33%;" data-orderable="true">CUENTAS</th>
									<th style="width:33%;" data-orderable="true">AVANCE</th>
									<th style="width:34%;" data-orderable="true">SALDO TOTAL</th>
								</tr>
							</thead>
							<tbody> <?php
							if ( isset($totalCuentas) && $totalCuentas != NULL){
								foreach ($totalCuentas as $i => $value) { ?>
									<tr>
										<td><?=number_format($value->cuentas,2);?></td>
										<td><?=number_format($value->pagos,2);?></td>
										<td><?=number_format($value->saldo,2);?></td>
										</tr> <?php
									}
								} ?>
							</tbody>
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

<div id="modal_pagos" class="modal fade" role="dialog">
	<div class="modal-dialog w-porc-70">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center">CUENTA DE <?=($tipo_cuenta=='1') ? 'COBRO' : 'PAGO';?></h4>
			</div>
			<div class="modal-body panel panel-default">

				<div class="row form-group header">
					<div class="col-sm-11 col-md-11 col-lg-11">
						INFORMACIÓN DE LA CUENTA
					</div>
				</div>

				<div class="row form-group font-9">
					<div class="col-sm-2 col-md-2 col-lg-2">
						<label>RUC / DNI:</label> <span id="modal_ruc"></span>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-6">
						<label>RAZÓN SOCIAL:</label> <span id="modal_razonSocial"></span>
					</div>
				</div>
				<div class="row form-group font-9">
					<div class="col-sm-2 col-md-2 col-lg-2">
						<label>DOCUMENTO:</label> <span id="modal_tipoDocumento"></span>
					</div>
					<div class="col-sm-2 col-md-2 col-lg-2">
						<label>SERIE Y NÚMERO:</label> <span id="modal_serieNumero"></span>
					</div>
					<div class="col-sm-2 col-md-2 col-lg-2">
						<label>FECHA DE EMISIÓN:</label> <span id="modal_fechaEmision"></span>
					</div>
					<div class="col-sm-3 col-md-3 col-lg-3">
						<label>FECHA DE VENCIMIENTO:</label> <span id="modal_fechaVencimiento"></span>
					</div>
				</div>
				<div class="row form-group font-9">
					<div class="col-sm-2 col-md-2 col-lg-2">
						<label>TOTAL:</label> <span id="modal_total"></span>
					</div>
					<div class="col-sm-2 col-md-2 col-lg-2">
						<label>SALDO:</label> <span id="modal_saldo"></span>
					</div>
					<div class="col-sm-3 col-md-3 col-lg-3">
						<label>ESTADO:</label> <span id="modal_estado"></span>
					</div>

					<div class="col-sm-1 col-md-1 col-lg-1">
						<button type="button" class="btn btn-primary btn_prorroga" data-toggle="modal" data-target="#modal_prorroga">Prorroga</button>
					</div>
				</div>

				<div class="row form-group header">
					<div class="col-sm-11 col-md-11 col-lg-11">
						RELACIÓN DE PAGOS
					</div>
				</div>

				<div class="row form-group">
					<div class="col-sm-12 col-md-12 col-lg-12 pall-0">
						<table class="fuente8 display" id="table-pagos">
							<thead>
								<tr>
									<th style="width:08%;" data-orderable="true">FECHA</th>
									<th style="width:10%;" data-orderable="true">SERIE - NÚMERO</th>
									<th style="width:06%;" data-orderable="true">MONEDA</th>
									<th style="width:14%;" data-orderable="true">IMPORTE</th>
									<th style="width:15%;" data-orderable="true">FORMA DE PAGO</th>
									<th style="width:15%;" data-orderable="true">N# OPERACIÓN</th>
									<th style="width:32%;" data-orderable="true">OBSERVACIÓN</th>
								</tr>
							</thead>
							<tbody class="pagos-info"></tbody>
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

<div id="modal_prorroga" class="modal fade" role="dialog">
	<div class="modal-dialog w-30">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center">PRORROGA DE CUENTA</h4>
			</div>
			<div class="modal-body panel panel-default">
				<form method="POST" action="" id="prorroga">
					<input type="hidden" id="prorroga_comprobante" name="prorroga_comprobante" value=""/>
					<input type="hidden" id="cuenta" name="cuenta" value=""/>
					<div class="row form-group">
						<div class="col-sm-1 col-md-1 col-lg-1">
							<label for="">DÍAS</label>
						</div>
						<div class="col-sm-2 col-md-2 col-lg-2">
							<input type="number" step="1" min="1" id="prorroga_dias" name="prorroga_dias" value="0" placeholder="Días" class="form-control w-porc-70 h-1"/>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="prorroga()">Actualizar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
			</div>
		</div>
	</div>
</div>

<div id="modal_nuevo_pago" class="modal fade" role="dialog">
	<div class="modal-dialog w-porc-80">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title text-center">REGISTRAR <?=($tipo_cuenta=='1') ? 'COBRO' : 'PAGO';?> DE CUENTA</h3>
			</div>
			<div class="modal-body panel panel-default">
				<form id="form_nvopago" method="POST" action="#">
					<div class="row form-group header">
						<div class="col-sm-11 col-md-11 col-lg-11">
							CLIENTE
						</div>
					</div>

					<div class="row form-group font-9">
						<input type="hidden" id="nvopago_comprobante" name="nvopago_comprobante"/>
						<input type="hidden" id="nvopago_cliente" name="nvopago_cliente"/>
						<div class="col-sm-2 col-md-2 col-lg-2">
							<label for="nvopago_ruc">RUC / DNI:</label>
							<input type="number" id="nvopago_ruc" name="nvopago_ruc" class="form-control h-2 w-porc-90"/>
						</div>
						<div class="col-sm-6 col-md-6 col-lg-6">
							<label for="nvopago_razonSocial">RAZÓN SOCIAL:</label>
							<input type="text" id="nvopago_razonSocial" name="nvopago_razonSocial" class="form-control h-2 w-porc-90"/>
						</div>
					</div>

					<div class="row form-group header">
						<div class="col-sm-11 col-md-11 col-lg-11">
							INFORMACIÓN DEL PAGO
						</div>
					</div>

					<div class="row form-group font-9">
						<div class="col-sm-2 col-md-2 col-lg-2">
							<label for="nvopago_ruc">FECHA: *</label>
							<input type="date" id="nvopago_fecha" name="nvopago_fecha" class="form-control h-2 w-porc-90" value="<?=date('Y-m-d');?>" />
						</div>
						<div class="col-sm-2 col-md-2 col-lg-2">
							<label for="nvopago_formaPago">FORMA DE PAGO: *</label>
							<select id="nvopago_formaPago" name="nvopago_formaPago" class="form-control h-3">
								<option value="1">EFECTIVO</option>
								<option value="2">DEPOSITO</option>
								<option value="7">TRANSFERENCIA</option>
								<option value="3">CHEQUE</option>
								<option value="5">NOTA DE CREDITO</option>
							</select>
						</div>
						<div class="col-sm-2 col-md-2 col-lg-2" hidden>
							<label for="nvopago_caja">CAJA:</label>
							<select id="nvopago_caja" name="nvopago_caja" class="form-control h-3"> <?php
							foreach ($cajas as $i => $val) { ?>
								<option value="<?=$val->CAJA_Codigo?>"><?=$val->CAJA_Nombre;?></option> <?php
							} ?>
						</select>
					</div>
					<div class="col-sm-1 col-md-1 col-lg-2">
						<label for="nvopago_moneda">MONEDA: *</label>
						<select id="nvopago_moneda" name="nvopago_moneda" class="form-control h-3"> <?php
						foreach ($monedas as $i => $val) { ?>
							<option value="<?=$val->MONED_Codigo?>"><?="$val->MONED_Simbolo | $val->MONED_Descripcion";?></option> <?php
						} ?>
					</select>
				</div>
				<div class="col-sm-1 col-md-1 col-lg-1">
					<label for="nvopago_monto">MONTO: *</label>
					<input type="number" step="0.10" min="0.10" id="nvopago_monto" name="nvopago_monto" class="form-control h-2 w-porc-90"/>
				</div>
			</div>

			<div class="row form-group font-9 info_formapago">
				<div class="col-sm-6 col-md-6 col-lg-6 pall-0">
					<div class="row form-group">
						<div class="row form-group header">
							<div class="col-sm-11 col-md-11 col-lg-11">
								BANCO DEL <?=($tipo_cuenta=='1') ? 'CLIENTE' : 'PROVEEDOR';?>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-10 col-md-10 col-lg-10">
								<select id="nvopago_ctacliente" name="nvopago_ctacliente" class="form-control h-3">
									<option value=""> :: SELECCIONE :: </option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-6 col-lg-6 pall-0">
					<div class="row form-group">
						<div class="row form-group header">
							<div class="col-sm-11 col-md-11 col-lg-11">
								MI BANCO
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-10 col-md-10 col-lg-10">
								<select id="nvopago_miscta" name="nvopago_miscta" class="form-control h-3">
									<option value=""> :: SELECCIONE :: </option> <?php
									if ($mis_bancos != NULL){
										$banOptions = "";
										foreach ($mis_bancos as $i => $val) {
											if ($banOptions != $val->BANP_Codigo){
												if ($i != 0){ ?>
													</optgroup> <?php
												} ?>

												<optgroup label='<?="$val->BANC_Siglas | $val->BANC_Nombre";?>'> <?php
												$banOptions = $val->BANP_Codigo;
											} ?>

											<option value='<?=$val->CUENT_Codigo;?>'>
												<?="$val->MONED_Simbolo | $val->CUENT_NumeroEmpresa | $val->CUENT_Titular";?>
												</option> <?php
											} ?>
											</optgroup> <?php
										} ?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row form-group header info_formapago">
					<div class="col-sm-11 col-md-11 col-lg-11">
						DETALLES
					</div>
				</div>

				<div class="row form-group font-9 info_formapago">
					<div class="col-sm-2 col-md-2 col-lg-2 viewTransferencia">
						<label for="nvopago_nrotrans">TRANSFERENCIA:</label>
						<input type="number" id="nvopago_nrotrans" name="nvopago_nrotrans" placeholder="Número de transferencia" class="form-control h-2 w-porc-90"/>
					</div>
					<div class="col-sm-2 col-md-2 col-lg-2 viewDeposito">
						<label for="nvopago_nrodeposito">DEPOSITO:</label>
						<input type="number" id="nvopago_nrodeposito" name="nvopago_nrodeposito" placeholder="Número de deposito" class="form-control h-2 w-porc-90"/>
					</div>
					<div class="col-sm-2 col-md-2 col-lg-2 viewCheque">
						<label for="nvopago_nrocheque">NÚM. CHEQUE:</label>
						<input type="number" step="1" min="1" id="nvopago_nrocheque" name="nvopago_nrocheque" class="form-control h-2 w-porc-90"/>
					</div>
					<div class="col-sm-2 col-md-2 col-lg-2 viewCheque">
						<label for="nvopago_emisioncheque">F. EMISIÓN:</label>
						<input type="date" id="nvopago_emisioncheque" name="nvopago_emisioncheque" class="form-control h-2 w-porc-90"/>
					</div>
					<div class="col-sm-2 col-md-2 col-lg-2 viewCheque">
						<label for="nvopago_vencimientocheque">F. VENCIMIENTO:</label>
						<input type="date" id="nvopago_vencimientocheque" name="nvopago_vencimientocheque" class="form-control h-2 w-porc-90"/>
					</div>
				</div>

				<div class="row form-group viewNotas">
					<div class="col-sm-11 col-md-11 col-lg-11">
						<table class="fuente8 display" id="table-notas">
							<input type='hidden' id='cod_nota' name='cod_nota' value=''>
							<thead>
								<tr>
									<th style="width:15%" data-orderable="true">DOCUMENTO</th>
									<th style="width:10%" data-orderable="true">FECHA</th>
									<th style="width:10%" data-orderable="true">SERIE</th>
									<th style="width:10%" data-orderable="true">NÚMERO</th>
									<th style="width:10%" data-orderable="false">MONTO</th>
									<th style="width:15%" data-orderable="false">DOC. RELACIONADO</th>
									<th style="width:10%" data-orderable="false">SERIE</th>
									<th style="width:10%" data-orderable="false">NÚMERO</th>
									<th style="width:05%" data-orderable="false">&nbsp;</th>
									<th style="width:05%" data-orderable="false">&nbsp;</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>

				<div class="row form-group font-9">
					<div class="col-sm-10 col-md-10 col-lg-10">
						<label for="nvopago_observacion">OBSERVACIÓN</label>
						<textarea class="form-control" id="nvopago_observacion" name="nvopago_observacion" placeholder="Indique una observación"></textarea>
					</div>
				</div>

				<div class="row form-group header viewComprobantes">
					<div class="col-sm-11 col-md-11 col-lg-11">
						DOCUMENTOS CON CUENTAS POR <?=($tipo_cuenta == 1) ? "COBRAR" : "PAGAR";?>
					</div>
				</div>

				<div class="row form-group viewComprobantes">
					<div class="col-sm-12 col-md-12 col-lg-12 pall-0">
						<table class="fuente8 display" id="table-cuentasPendientes">
							<thead>
								<tr>
									<th style="width:10%" data-orderable="true">FECHA EMI.</th>
									<th style="width:10%" data-orderable="true">FECHA VENC.</th>
									<th style="width:12%" data-orderable="true">DOCUMENTO</th>
									<th style="width:08%" data-orderable="true">SERIE</th>
									<th style="width:10%" data-orderable="true">NÚMERO</th>
									<th style="width:10%" data-orderable="false">MONTO</th>
									<th style="width:10%" data-orderable="false">AVANCE</th>
									<th style="width:10%" data-orderable="false">SALDO</th>
									<th style="width:10%" data-orderable="false">ESTADO</th>
									<th style="width:05%" data-orderable="false">&nbsp;</th>
									<th style="width:05%" data-orderable="false">&nbsp;</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-info nvopago_limpiar">Limpiar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
	var base_url = "<?=base_url();?>";
	var tipo_cuenta = "<?=$tipo_cuenta;?>";

	var url_searchRazonSocial = (tipo_cuenta == "1") ? base_url + "index.php/empresa/cliente/autocomplete/" : base_url + "index.php/empresa/proveedor/autocomplete/";
	var url_searchRuc = (tipo_cuenta == "1") ? base_url + "index.php/empresa/cliente/autocomplete_ruc/" : base_url + "index.php/empresa/proveedor/autocomplete_ruc/";

	$(document).ready(function(){
		$('#table-totales').DataTable({ responsive: true,
			filter: false,
			destroy: true,
			autoWidth: false,
			paging: false,
			language: spanish
		});

		$('#table-cuentas').DataTable({ responsive: true,
			filter: false,
			destroy: true,
			processing: true,
			serverSide: true,
			autoWidth: false,
			ajax:{
				url : base_url + 'index.php/tesoreria/cuentas/datatable_cuentas/<?=$tipo_cuenta;?>',
				type: "POST",
				data: { dataString: "" },
				beforeSend: function(){
					$(".loading-table").show();
				},
				error: function(){
				},
				complete: function(){
					$(".loading-table").hide();
				}
			},
			language: spanish,
			order: [[ 0, "desc" ]]
		});

		$("#buscarC").click(function(){
			search();
		});

		$("#limpiarC").click(function(){
			search(false);
		});

		$("#nvopago_razonSocial").autocomplete({
			source: function (request, response) {
				$.ajax({
					url: url_searchRazonSocial,
					type: "POST",
					data: { term: $("#nvopago_razonSocial").val() },
					dataType: "json",
					success: function (data) {
						response(data);
					}
				});
			},
			select: function (event, ui) {
				$("#nvopago_razonSocial").val(ui.item.nombre);
				$("#nvopago_ruc").val(ui.item.ruc);
				$("#nvopago_cliente").val(ui.item.codigo);
				table_cuentas(ui.item.codigo);
				table_notas(ui.item.codigo);
				getBancosAsoc(ui.item.codigo);
			},
			minLength: 2
		});

    // BUSQUEDA POR RUC
    $("#nvopago_ruc").autocomplete({
    	source: function (request, response) {
    		$.ajax({
    			url: url_searchRuc,
    			type: "POST",
    			data: {
    				term: $("#nvopago_ruc").val()
    			},
    			dataType: "json",
    			success: function (data) {
    				response(data);
    			}
    		});
    	},
    	select: function (event, ui) {
    		$("#nvopago_razonSocial").val(ui.item.nombre);
    		$("#nvopago_ruc").val(ui.item.ruc);
    		$("#nvopago_cliente").val(ui.item.codigo);
    		table_cuentas(ui.item.codigo);
    		table_notas(ui.item.codigo);
    		getBancosAsoc(ui.item.codigo);
    	},
    	minLength: 2
    });

    $("#nvopago_formaPago").change(function(){
    	if ( $(this).val() == 1 ){
    		$(".info_formapago").hide("slow");
  			$(".viewNotas").hide('slow');
  			$(".viewComprobantes").show('slow');

    		$("#nvopago_nrotrans").val("");
    		$("#nvopago_nrodeposito").val("");
    		$("#nvopago_nrocheque").val("");
    		$("#nvopago_emisioncheque").val("");
    		$("#nvopago_vencimientocheque").val("");
    	}
    	else
    	if ( $(this).val() == 5 ){
    		$(".info_formapago").hide("slow");

    		$("#nvopago_nrotrans").val("");
    		$("#nvopago_nrodeposito").val("");
    		$("#nvopago_nrocheque").val("");
    		$("#nvopago_emisioncheque").val("");
    		$("#nvopago_vencimientocheque").val("");

  			$(".viewNotas").show('slow');
  			$(".viewComprobantes").hide('slow');
    	}
    	else{
  			$(".viewNotas").hide('slow');
  			$(".viewComprobantes").show('slow');
    		$(".info_formapago").show("slow");

    		if ( $(this).val() == 2 ){
    			$(".viewDeposito").show("slow");

    			$(".viewTransferencia").hide("slow");
    			$(".viewCheque").hide("slow");

    			$("#nvopago_nrotrans").val("");
    			$("#nvopago_nrocheque").val("");
    			$("#nvopago_emisioncheque").val("");
    			$("#nvopago_vencimientocheque").val("");
    		}
    		if ( $(this).val() == 7 ){
    			$(".viewTransferencia").show("slow");

    			$(".viewDeposito").hide("slow");
    			$(".viewCheque").hide("slow");

    			$("#nvopago_nrodeposito").val("");
    			$("#nvopago_nrocheque").val("");
    			$("#nvopago_emisioncheque").val("");
    			$("#nvopago_vencimientocheque").val("");
    		}
    		if ( $(this).val() == 3 ){
    			$(".viewCheque").show("slow");

    			$(".viewDeposito").hide("slow");
    			$(".viewTransferencia").hide("slow");

    			$("#nvopago_nrodeposito").val("");
    			$("#nvopago_nrotrans").val("");
    		}
    	}
    });

    $(".nvopago_limpiar").click(function(){
    	$("#form_nvopago")[0].reset();
    	$("#nvopago_cliente").val("");
    	$("#nvopago_comprobante").val("");
    	$(".info_formapago").hide("slow");
    	table_cuentas();
    	table_notas();
    });

    $("#nuevoCuenta").click(function () {
    	url = base_url + "index.php/tesoreria/cuentas/nuevo/<?=$tipo_cuenta;?>";
    	location.href = url;
    });
  });

function asignar_importe_nota(importe){
	$("#nvopago_monto").val( importe );
}

function search( search = true ){
	if ( search == true){
		fechai          = $("#fechai").val();
		fechaf          = $("#fechaf").val();
		serie           = $("#serie").val();
		numero          = $("#numero").val();

		cliente     = $("#cliente").val();
		ruc_cliente     = $("#ruc_cliente").val();
		nombre_cliente  = $("#nombre_cliente").val();

		proveedor   = $("#proveedor").val();
		ruc_proveedor   = $("#ruc_proveedor").val();
		nombre_proveedor= $("#nombre_proveedor").val();

		estado_pago     = $("#estado_pago").val();
		comprobante     = $("#comprobante").val();
	}
	else{
		$("#fechai").val("");
		$("#fechaf").val("");
		$("#serie").val("");
		$("#numero").val("");
		$("#cliente").val("");
		$("#ruc_cliente").val("");
		$("#nombre_cliente").val("");
		$("#proveedor").val("");
		$("#ruc_proveedor").val("");
		$("#nombre_proveedor").val("");
		$("#estado_pago").val("");
		$("#comprobante").val("");

		fechai          = "";
		fechaf          = "";
		serie           = "";
		numero          = "";
		cliente         = "";
		ruc_cliente     = "";
		nombre_cliente  = "";
		proveedor       = "";
		ruc_proveedor   = "";
		nombre_proveedor= "";
		estado_pago     = "";
		comprobante     = "";
	}

	$('#table-cuentas').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : '<?=base_url();?>index.php/tesoreria/cuentas/datatable_cuentas/<?=$tipo_cuenta;?>',
			type: "POST",
			data: {
				fechai: fechai, 
				fechaf: fechaf,
				serie: serie,
				numero: numero,
				estado_pago: estado_pago,
				comprobante: comprobante,
				cliente: cliente,
				ruc_cliente: ruc_cliente,
				nombre_cliente: nombre_cliente,
				proveedor: proveedor,
				ruc_proveedor: ruc_proveedor,
				nombre_proveedor: nombre_proveedor
			},
			beforeSend: function(){
				$(".loading-table").show();
			},
			error: function(){
			},
			complete: function(){
				$(".loading-table").hide();
			}
		},
		language: spanish,
		order: [[ 0, "desc" ]]
	});
}

function getBancosAsoc(id){

	var tipo_cuenta = "<?=$tipo_cuenta;?>";

	var cliente = (tipo_cuenta == 1) ? id : "";
	var proveedor = (tipo_cuenta == 2) ? id : "";

	$.ajax({
		type: "POST",
		dataType: "json",
		url: base_url + "index.php/tesoreria/banco/getBancosAsoc",
		data: {
			cliente: cliente,
			proveedor: proveedor
		},
		beforeSend: function(){
			$("#nvopago_ctacliente").html("");
		},
		success: function(data){
			options = "";
			banco = "";

			if (data.match == true){
				$.each(data.bancos, function(i,info){
					if (banco != info.banco){
						if (i != 0)
							options += "</optgroup>";

						options += "<optgroup label='" + info.siglas + " | " + info.nombre + "'>";
						banco = info.banco;
					}

					options += "<option value='" + info.cuenta + "'>" + info.moneda + " | " + info.numero + " | " + info.titular + "</option>";
				});

                    options += "</optgroup>"; // CIERRA EL GRUPO LUEGO DE LA ULTIMA REPETICIÓN
                  }
                  else{
                  	options += "<option value=''> SIN REGISTRO DE BANCO. </option>";
                  }
                  $("#nvopago_ctacliente").append(options);
                }
              });
}

function modal_pagos(cuenta){
	$.ajax({
		type: "POST",
		dataType: "json",
		url: '<?=base_url();?>index.php/tesoreria/cuentas/getCuentaInfo/',
		data: {
			cuenta: cuenta
		},
		beforeSend: function(){
			$("#modal_ruc").html("");
			$("#modal_razonSocial").html("");
			$("#modal_tipoDocumento").html("");
			$("#modal_serieNumero").html("");
			$("#modal_fechaEmision").html("");
			$("#modal_fechaVencimiento").html("");
			$("#modal_total").html("");
			$("#modal_saldo").html("");
			$("#modal_estado").html("");

			$("#prorroga_comprobante").val();
			$("#cuenta").val();

			$('#table-pagos').DataTable().destroy();
			$("#table-pagos .pagos-info").html("");
		},
		success: function(data){
			if (data.match == true){
				var info = data.cuenta;
				var pagos = data.pagos;

				$("#cuenta").val(info.cuenta);
				$("#prorroga_comprobante").val(info.comprobante);

				$("#modal_ruc").html(info.ruc);
				$("#modal_razonSocial").html(info.razon_social);
				$("#modal_tipoDocumento").html(info.documento);
				$("#modal_serieNumero").html(info.serie + " - " + info.numero);
				$("#modal_fechaEmision").html(info.fechaEmision);
				$("#modal_fechaVencimiento").html(info.fechaVencimiento);
				$("#modal_total").html(info.moneda + " " + info.total);
				$("#modal_saldo").html(info.moneda + " " + info.saldo);
				$("#modal_estado").html(info.estado);

				if (info.btn_prorroga == true)
					$(".btn_prorroga").show();
				else
					$(".btn_prorroga").hide();

				$.each(pagos, function(i,item){
					tr = '<tr>';
					tr += '<td>' + item.fecha + '</td>';
					tr += '<td>' + item.serieNumero + '</td>';
					tr += '<td>' + item.moneda + '</td>';
					tr += '<td>' + item.monto + '</td>';
					tr += '<td>' + item.formaPago + '</td>';
					tr += '<td>' + item.noperacion + '</td>';
					tr += '<td>' + item.observacion + '</td>';
					tr += '</tr>';

					$("#table-pagos .pagos-info").append(tr);
				});
			}
		},
		complete: function(){
			table_pagos();
			$("#modal_pagos").modal("toggle");
		}
	});
}

function prorroga(){
	$.ajax({
		type: "POST",
		dataType: "json",
		url: '<?=base_url();?>index.php/tesoreria/cuentas/prorroga/',
		data: {
			comprobante: $("#prorroga_comprobante").val(),
			dias: $("#prorroga_dias").val()
		},
		success: function(data){
			if (data.result == "success"){
				Swal.fire({
					icon: "success",
					title: "Fecha de vencimiento actualizada.",
					html: "<b class='color-red'></b>",
					showConfirmButton: true,
					timer: 2000
				});
			}
			else{
				Swal.fire({
					icon: "error",
					title: "Sin cambios.",
					html: "<b class='color-red'>Verifique los datos ingresados e intentelo nuevamente.</b>",
					showConfirmButton: true,
					timer: 4000
				});
			}
		},
		complete: function(){
			$("#modal_prorroga").modal("hide");
			$("#modal_pagos").modal("hide");
			modal_pagos( $("#cuenta").val() );
		}
	});
}

function table_pagos(){
	$('#table-pagos').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		autoWidth: false,
		language: spanish
	});
}

function table_cuentas(id = ""){
	$('#table-cuentasPendientes').DataTable({ responsive: true,
		filter: true,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : '<?=base_url();?>index.php/tesoreria/cuentas/getCuentasPendientes/<?=$tipo_cuenta;?>',
			type: "POST",
			data: {
				cliente: id,
				proveedor: id
			},
			beforeSend: function(){
			},
			error: function(){
			},
			complete: function(){
			}
		},
		language: spanish,
		order: [[ 0, "desc" ]]
	});
}

function table_notas(id = ""){
	$('#table-notas').DataTable({ responsive: true,
		filter: false,
		destroy: true,
		processing: true,
		serverSide: true,
		ajax:{
			url : '<?=base_url();?>index.php/tesoreria/cuentas/getNotas/<?=$tipo_cuenta;?>',
			type: "POST",
			data: {
				cliente: id,
				proveedor: id
			},
			beforeSend: function(){
			},
			error: function(){
			},
			complete: function(){
			}
		},
		language: spanish,
		order: [[ 1, "desc" ]],
		pageLength: 5
	});
}

function registrar_pago(comprobante, saldo = 0, serie = "", cod_nota = '', importe_nota = '', moneda_nota = '' ){

	if (cod_nota != '')
		$("#cod_nota").val( cod_nota );

	if (moneda_nota != '')
		$("#nvopago_moneda").val( moneda_nota );

	if (importe_nota != '')
		$("#nvopago_monto").val( importe_nota );

	var fecha = $("#nvopago_fecha").val();
	var importe = parseFloat($("#nvopago_monto").val());
	var moneda = $("#nvopago_moneda option:selected").text();
	$("#nvopago_comprobante").val(comprobante);


	Swal.fire({
		icon: "info",
		title: "¿Esta seguro de guardar este pago?",
		html: "<b class='color-green font-9'>Documento: " + serie + ".<br>Moneda: " + moneda + "<br>Importe a pagar: " + importe + "</b>",
		showConfirmButton: true,
		showCancelButton: true,
		confirmButtonText: "Aceptar",
		cancelButtonText: "Cancelar"
	}).then(result => {
		if (result.value){
			var info = $("#form_nvopago").serialize();
			var url = base_url + "index.php/tesoreria/cuentas/guardar_pago/<?=$tipo_cuenta;?>";

			validacion = true;

			if ( $("#nvopago_formaPago").val() != "1" ){
				vcheque = $("#nvopago_nrocheque").val();
				vemision = $("#nvopago_emisioncheque").val();
				vvence = $("#nvopago_vencimientocheque").val();
				validacionCheque = true;

				if ( vcheque != "" && vemision == "" || vcheque != "" && vvence == "")
					validacionCheque = false;

				if ( vcheque == "" && vemision != "" || vcheque == "" && vvence != "")
					validacionCheque = false;

				if ( validacionCheque == false ){
					Swal.fire({
						icon: "info",
						title: "Verifique los datos ingresados.",
						html: "<b class='color-red'>Si la forma de pago es cheque, debe completar los datos (número, fecha de emision, fecha de vencimiento). </b>",
						showConfirmButton: true,
						timer: 4000
					});
					validacion = false;
					return null;
				}
			}

			if (fecha == ""){
				Swal.fire({
					icon: "info",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>Debe seleccionar una fecha.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#nvopago_fecha").focus();
				validacion = false;
				return null;
			}

			if (importe == "" || importe <= 0){
				Swal.fire({
					icon: "info",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>El importe ingresado no es valido.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#nvopago_monto").focus();
				validacion = false;
				return null;
			}
			/*
			if (importe > saldo){
				Swal.fire({
					icon: "info",
					title: "Verifique los datos ingresados.",
					html: "<b class='color-red'>El importe ingresado es mayor al saldo de la cuenta.</b>",
					showConfirmButton: true,
					timer: 4000
				});
				$("#nvopago_monto").focus();
				validacion = false;
				return null;
			}
			*/

			if (validacion == true){
				$.ajax({
					type: 'POST',
					url: url,
					dataType: 'json',
					data: info,
					success: function(data){
						if (data.result == "success") {
							Swal.fire({
								icon: "success",
								title: "Pago registrado",
								showConfirmButton: true,
								timer: 2000
							});

							table_cuentas($("#nvopago_cliente").val());
							table_notas($("#nvopago_cliente").val());
						}
						else{
							if (data.message != undefined && data.message != null && data.message != "")
								message = data.message;
							else
								message = "Sin cambios.";

							if (data.detalle != undefined && data.detalle != null && data.detalle != "")
								detalle = data.detalle;
							else
								detalle = "La información no fue registrada, intentelo nuevamente.";

							Swal.fire({
								icon: "error",
								title: message,
								html: "<b class='color-red'>" + detalle + "</b>",
								showConfirmButton: true,
								timer: 20000
							});
						}
					},
					complete: function(){
					}
				});
			}
		}
	});
}

function clean(){
	$("#form_nvopago")[0].reset();
	$("#nvopago_cliente").val("");
	$("#nvopago_comprobante").val("");
	$(".info_formapago").hide("slow");
	table_cuentas();
	table_notas();
}
</script>

<script type="text/javascript" src="<?=base_url();?>public/js/tesoreria/cuentas.js?=<?=JS;?>"></script>