<section>
	<div class="row">
		<div class="col-md-12">
			<div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTES DISPONIBLES</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<form id="form_modulo" method="post">
						<div class="row">
							<div class="col-lg-1 form-group">
								<label for="">Seleccione</label>
							</div>					
							<div class="col-lg-3 form-group">
								<select id="search_modulo" name="search_modulo" class="form-control">
									<option value=""> :: TODOS :: </option> <?php
									foreach ($modulos['records'] as $i => $val){ ?>
										<option value="<?=$val->MENU_Url;?>"><?=$val->MENU_Titulo;?></option> <?php
									} ?>                    
								</select>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="stockproducto" class="contenido">
	<div class="row">
		<div class="col-md-12">
			<div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE STOCK DE PRODUCTOS</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
				<form id="form_stockproducto" method="post">
					<div class="card-body text-sm">
						<div class="row">
							<div class="col-md-2 form-group">
								<label for="txtCodigo">Código:</label>
								<input id="txtCodigo" name="txtCodigo" type="text" class="form-control form-control-sm" placeholder="Codigo" maxlength="30" value="">
							</div>
							<div class="col-md-3 form-group">
								<label for="txtNombre">Nombre: </label>
								<input id="txtNombre" name="txtNombre" type="text" class="form-control form-control-sm" maxlength="100" placeholder="Nombre producto" value="">
							</div>
							<div class="col-md-2 form-group">
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
							<div class="col-md-3 form-group">
								<label for="txtMarca">Marca: </label>
								<input type="text" id="txtMarca" name="txtMarca" class="form-control form-control-sm" maxlength="100" placeholder="Marca producto" value="">
							</div>
							<div class="col-md-2 form-group">
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
							<div class="col-md-5 form-group text-right">
								<button type="button" class="btn btn-info" id="buscarSP"><i class="fas fa-search"></i> Buscar</button>
								<button type="button" class="btn btn-dark" id="limpiarSP"><i class="fas fa-trash"></i> Limpiar</button>
								<button type="button" class="btn btn-success" id="imprimirSP"><i class="fas fa-file-excel"></i> Imprimir</button>							
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row"> 
		<div class="col-md-12">
			<div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REGISTROS</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body" style="overflow-y: auto;">
					<table class="table table-striped table-bordered text-sm" id="table-stockProducto">
						<thead>
							<tr>
								<th style="width: 10%" data-orderable="true">CÓDIGO</th>
								<th style="width: 40%" data-orderable="true">NOMBRE</th>
								<th style="width: 15%" data-orderable="true">FAMILIA</th>
								<th style="width: 15%" data-orderable="true">MARCA</th>
								<th style="width: 10%" data-orderable="true">MODELO</th>
								<th style="width: 5%" data-orderable="true">UNIDAD</th>
								<th style="width: 5%" data-orderable="true">STOCK</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="stockalmacenes"  class="contenido">
	<div class="row">
		<div class="col-md-12">
			<div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE STOCK POR ALMACEN</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
				<form id="form_stockalmacenes" method="post">
					<div class="card-body text-sm">
						<div class="row">
							<div class="col-md-2 form-group">
								<label for="txtAlmacenAP">Almacen: </label>
								<select name="txtAlmacenAP" id="txtAlmacenAP" class="form-control form-control-sm">
									<option value="">::TODOS::</option>
									<?php
									if ($almacenes != NULL) {
										foreach ($almacenes as $i => $val) { ?>
											<option value="<?= $val->ALMAP_Codigo; ?>"><?= $val->ALMAC_Descripcion; ?></option>
									<?php
										}
									} ?>
								</select>
							</div>
							<div class="col-md-2 form-group">
								<label for="txtCodigoAP">Código:</label>
								<input id="txtCodigoAP" name="txtCodigoAP" type="text" class="form-control form-control-sm" placeholder="Codigo" maxlength="30" value="">
							</div>
							<div class="col-md-2 form-group">
								<label for="txtNombreAP">Nombre: </label>
								<input id="txtNombreAP" name="txtNombreAP" type="text" class="form-control form-control-sm" maxlength="100" placeholder="Nombre producto" value="">
							</div>
							<div class="col-md-2 form-group">
								<label for="txtFamiliaAP">Familia: </label>
								<select name="txtFamiliaAP" id="txtFamiliaAP" class="form-control form-control-sm">
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
							<div class="col-md-2 form-group">
								<label for="txtMarcaAP">Marca: </label>
								<input type="text" id="txtMarcaAP" name="txtMarcaAP" class="form-control form-control-sm" maxlength="100" placeholder="Marca producto" value="">
							</div>
							<div class="col-md-2 form-group">
								<label for="txtModeloAP">Modelo: </label>
								<select name="txtModeloAP" id="txtModeloAP" class="form-control form-control-sm">
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
							<div class="col-md-5 form-group text-right">
								<button type="button" class="btn btn-info" id="buscarAP"><i class="fas fa-search"></i> Buscar</button>
								<button type="button" class="btn btn-dark" id="limpiarAP"><i class="fas fa-trash"></i> Limpiar</button>
								<button type="button" class="btn btn-success" id="imprimirAP"><i class="fas fa-file-excel"></i> Imprimir</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REGISTROS</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body" style="overflow-y: auto;">
					<table class="table table-striped table-bordered text-sm" id="table-stockAlmacen">
						<thead>
							<tr>
								<th style="width: 15%" data-orderable="true">ALMACEN</th>
								<th style="width: 10%" data-orderable="true">CÓDIGO</th>
								<th style="width: 30%" data-orderable="true">NOMBRE</th>
								<th style="width: 15%" data-orderable="true">FAMILIA</th>
								<th style="width: 10%" data-orderable="true">MARCA</th>
								<th style="width: 10%" data-orderable="true">MODELO</th>
								<th style="width: 05%" data-orderable="true">UNIDAD</th>
								<th style="width: 05%" data-orderable="true">STOCK</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>