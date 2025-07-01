<div class="row">
	<div class="col-md-4">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">Articulos/Servicios</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<form id="formArticulos" method="post">
					<div class="row">
						<div class="col-md-12 form-group">
							<label for="igv">IGV %:</label>
							<input type="number" name="igv" id="igv" value="<?=(isset($cfg->igv)) ? $cfg->igv : 0;?>" class="form-control"/>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 form-group">
							<label for="precio_igv">El Precio incluye IGV:</label>
							<select id="precio_igv" name="precio_igv" class="form-control">
								<option value="0" <?=(isset($cfg->precioConIgv) && $cfg->precioConIgv == 0) ? 'selected' : '';?>>No. No incluye el IGV</option>
								<option value="1" <?=(isset($cfg->precioConIgv) && $cfg->precioConIgv == 1) ? 'selected' : '';?>>Si. Incluye el IGV</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 form-group">
							<label for="codigo_productos">Código de productos/servicios:</label>
							<select id="codigo_productos" name="codigo_productos" class="form-control">
								<option value="1" <?=(isset($cfg->codigoProductos) && $cfg->codigoProductos == 1) ? 'selected' : '';?>>El sistema genera los códigos de producto/servicios</option>
								<option value="2" <?=(isset($cfg->codigoProductos) && $cfg->codigoProductos == 2) ? 'selected' : '';?>>El usuario administra sus códigos de producto</option>
								<option value="3" <?=(isset($cfg->codigoProductos) && $cfg->codigoProductos == 3) ? 'selected' : '';?>>Mixta. Incluye las 2 anteriores</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 form-group">
							<label for="codigo_unico">Código de productos/servicios unicos:</label>
							<select id="codigo_unico" name="codigo_unico" class="form-control">
								<option value="1" <?=(isset($cfg->bsCodigo) && $cfg->bsCodigo == 1) ? 'selected' : '';?>>Los códigos son unicos</option>
								<option value="0" <?=(isset($cfg->bsCodigo) && $cfg->bsCodigo == 0) ? 'selected' : '';?>>Los códigos se repiten</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 form-group">
							<label for="determina_precio">Tipo de precio:</label>
							<select id="determina_precio" name="determina_precio" class="form-control">
								<option value="0"<?=(isset($cfg->determinaPrecio) && $cfg->determinaPrecio == 0) ? 'selected' : '';?>>Los articulos poseen un precio unico</option>
								<option value="1"<?=(isset($cfg->determinaPrecio) && $cfg->determinaPrecio == 1) ? 'selected' : '';?>>El precio depende de la categoria del cliente</option>
								<option value="2"<?=(isset($cfg->determinaPrecio) && $cfg->determinaPrecio == 2) ? 'selected' : '';?>>El precio depende de la tienda</option>
							</select>
						</div>
					</div>
					<div class="row justify-content-end">
						<div class="col-md-auto">
							<button type="button" class="btn btn-success" id="guardarPrecios">Guardar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="card card-light">
			<div class="card-header">
				<h3 class="card-title">Serie de documentos</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<form id="formSeries" method="post">
					<div class="row"> <?php
						foreach ($series as $key => $val){ ?>
							<div class="col-md-6 form-group">
								<div class="row">
									<div class="col-md-4 form-group">
										<label class="font-10" for="i"><?=$val->DOCUC_Descripcion;?></label>
									</div>
									<div class="col-md-4 form-group">
										<input type="hidden" name="configuracion[]" value="<?=$val->CONFIP_Codigo;?>"/>
										<input type="text" name="serie[]" value="<?=$val->CONFIC_Serie;?>" maxlength="4" placeholder="" class="form-control form-control-sm <?=($val->CONFIC_Serie != "") ? 'is-valid' : '';?>"/>
									</div>
									<div class="col-md-4 form-group">
										<input type="number" step="1" min="0" name="numero[]" value="<?=$val->CONFIC_Numero;?>" placeholder="" class="form-control form-control-sm <?=($val->CONFIC_Numero != "") ? 'is-valid' : '';?>"/>
									</div>
								</div>
							</div> <?php
						} ?>
					</div>
					<div class="row justify-content-end">
						<div class="col-md-auto">
							<button type="button" class="btn btn-success" id="guardarSeries">Guardar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>