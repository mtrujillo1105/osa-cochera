<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">STOCK DEL ALMACEN</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<form id="form_busqueda" method="post">
				<div class="card-body text-sm">
					<div class="row">
						<div class="col-md-2 form-group">
							<label for="txtAlmacen">Almacen: </label>
							<select name="txtAlmacen" id="txtAlmacen" class="form-control form-control-sm">
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
							<label for="txtCodigo">Código:</label>
							<input id="txtCodigo" name="txtCodigo" type="text" class="form-control form-control-sm" placeholder="Codigo" maxlength="30" value="">
						</div>
						<div class="col-md-2 form-group">
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
						<div class="col-md-2 form-group">
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
						<div class="col-md-3 form-group text-right">
							<button type="button" class="btn btn-info" id="buscar">Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar">Limpiar</button>
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
							<th style="width: 35%" data-orderable="true">NOMBRE</th>
							<th style="width: 10%" data-orderable="true">FAMILIA</th>
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