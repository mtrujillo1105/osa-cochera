<div class="row">
	<div class="col-md-12">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">BUSCAR MOVIMIENTOS</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<form id="frmSearch" method="post">
				<div class="card-body text-sm">
					<div class="row">
						<div class="col-md-3 form-group">
							<label for="almacenSearch">Almacen</label>
							<select name="almacenSearch" id="almacenSearch" class="form-control form-control-sm">
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
							<label for="codigoSearch">Código:</label>
							<input type="hidden" name="productoSearch" id="productoSearch" value="" />
							<input id="codigoSearch" name="codigoSearch" type="text" class="form-control form-control-sm" placeholder="Código del producto" value="">
						</div>
						<div class="col-md-3 form-group">
							<label for="nombreSearch">Nombre: </label>
							<input id="nombreSearch" name="nombreSearch" type="text" class="form-control form-control-sm" placeholder="Nombre del producto" value="">
						</div>
						<div class="col-md-2 form-group">
							<label for="fechaIniSearch">Fecha Desde:</label>
							<input id="fechaIniSearch" name="fechaIniSearch" type="date" class="form-control form-control-sm" value="">
						</div>
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Fecha Hasta:</label>
							<input id="fechaFinSearch" name="fechaFinSearch" type="date" class="form-control form-control-sm" value="">
						</div>
					</div>
					<div class="row" id="inputFilters">
						<div class="col-md-2 form-group oculto">
							<label for="fabricanteSearch">Fabricante: </label>
							<select name="fabricanteSearch" id="fabricanteSearch" class="form-control form-control-sm">
								<option value=""> TODOS </option>
								<?php
								if ($fabricantes != NULL) {
									foreach ($fabricantes as $i => $v) { ?>
										<option value="<?= $v->FABRIP_Codigo; ?>"><?= $v->FABRIC_Descripcion; ?></option>
								<?php
									}
								} ?>
							</select>
						</div>
						<div class="col-md-2 form-group oculto">
							<label for="familiaSearch">Familia: </label>
							<select name="familiaSearch" id="familiaSearch" class="form-control form-control-sm">
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
						<div class="col-md-2 form-group oculto">
							<label for="marcaSearch">Marca: </label>
							<select name="marcaSearch" id="marcaSearch" class="form-control form-control-sm">
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
						<div class="col-md-2 form-group oculto">
							<label for="modeloSearch">Modelo: </label>
							<input id="modeloSearch" name="modeloSearch" type="text" class="form-control form-control-sm" placeholder="Modelo" value="">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 form-group align-self-end text-right">
							<button type="button" class="btn btn-default oculto" id="btn-up"><i class="fas fa-angle-up"></i> Filtros</button>
							<button type="button" class="btn btn-default" id="btn-down"><i class="fas fa-angle-down"></i> Filtros</button>
							<button type="button" class="btn btn-info" id="buscar"><i class="fas fa-search"></i> Buscar</button>
							<button type="button" class="btn btn-dark" id="limpiar"><i class="fas fa-trash"></i> Limpiar</button>
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
				<h3 class="card-title">MOVIMIENTOS <span id="prodDetails"></span></h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body" style="overflow-y: auto;">
				<table class="table table-striped table-bordered text-sm" id="table-movimiento">
					<thead>
						<tr>
							<th style="width:10%" data-orderable="false">FECHA MOV.</th>
							<th style="width:10%" data-orderable="false">TIPO MOV.</th>
							<th style="width:10%" data-orderable="false">ALMACEN</th>
							<th style="width:10%" data-orderable="false">DOCUMENTO</th>
							<th style="width:20%" data-orderable="false">NÚMERO</th>
							<th style="width:10%" data-orderable="false">CANTIDAD</th>
							<th style="width:10%" data-orderable="false">COSTO UNI.</th>
							<th style="width:10%" data-orderable="false">COSTO TOTAL</th>
						</tr>
					</thead>
					<tbody class="details"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>