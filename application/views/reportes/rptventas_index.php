<section id="reportes">
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
								<select id="txtModulo" name="txtModulo" class="form-control">
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
<section id="filtros">   
	<div class="row" id="filtroVendedor">
    <div class="col-md-12">
      <div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE VENTAS POR VENDEDOR</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
        <div class="card-body text-sm">
          <div class="row">
            <div class="col-md-2 form-group">
              <label for="txtTipoReporte">Tipo Reporte: </label>
              <select name="txtTipoReporte" id="txtTipoReporte" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <option value="1">Detalle</option>
                <option value="2">Acumulado</option>
                <option value="3">Comparativo</option>
              </select>
            </div>
            <div class="col-md-2 form-group">
              <label for="txtVendedor">Nombre Vendedor: </label>
              <select name="txtVendedor" id="txtVendedor" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <?php
                if ($vendedores != NULL) {
                  foreach ($vendedores as $indice => $val) {
                    if ($val->PERSP_Codigo != '') { ?>
                      <option value="<?= $val->PERSP_Codigo; ?>"><?= $val->PERSC_Nombre." ".$val->PERSC_ApellidoPaterno." ".$val->PERSC_ApellidoMaterno; ?></option>
                <?php
                    }
                  }
                } ?>
              </select>
            </div>  
						<div class="col-md-2 form-group">
							<label for="fechaIniSearch">Fecha Desde:</label>
							<input id="fechaIniSearch" name="fechaIniSearch" type="date" class="form-control form-control-sm" value="">
						</div>
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Fecha Hasta:</label>
							<input id="fechaFinSearch" name="fechaFinSearch" type="date" class="form-control form-control-sm" value="">
						</div> 
						<div class="col-lg-4 form-group align-self-end text-right">
              <button type="button" class="btn btn-info" id="buscarSP"><i class="fas fa-search"></i> Buscar</button>
              <button type="button" class="btn btn-dark" id="limpiarSP"><i class="fas fa-trash"></i> Limpiar</button>
              <button type="button" class="btn btn-success" id="imprimirSP"><i class="fas fa-file-excel"></i> Imprimir</button>		
						</div>            
          </div>
        </div>
      </div>
    </div>                
  </div>
	<div class="row" id="filtroMarca">
    <div class="col-md-12">
      <div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE VENTAS POR MARCA</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
        <div class="card-body text-sm">
          <div class="row">
            <div class="col-md-2 form-group">
              <label for="txtTipoReporte">Tipo Reporte: </label>
              <select name="txtTipoReporte" id="txtTipoReporte" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <option value="1">Detalle</option>
                <option value="2">Acumulado</option>
                <option value="3">Comparativo</option>
              </select>
            </div> 
						<div class="col-md-2 form-group">
							<label for="fechaIniSearch">Fecha Desde:</label>
							<input id="fechaIniSearch" name="fechaIniSearch" type="date" class="form-control form-control-sm" value="">
						</div>
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Fecha Hasta:</label>
							<input id="fechaFinSearch" name="fechaFinSearch" type="date" class="form-control form-control-sm" value="">
						</div> 
						<div class="col-md-6 form-group align-self-end text-right">
              <button type="button" class="btn btn-info" id="buscarSP"><i class="fas fa-search"></i> Buscar</button>
              <button type="button" class="btn btn-dark" id="limpiarSP"><i class="fas fa-trash"></i> Limpiar</button>
              <button type="button" class="btn btn-success" id="imprimirSP"><i class="fas fa-file-excel"></i> Imprimir</button>		
						</div>            
          </div>
        </div>
      </div>
    </div>  
  </div>  
	<div class="row" id="filtroFamilia">
    <div class="col-md-12">
      <div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE VENTAS POR FAMILIA</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
        <div class="card-body text-sm">
          <div class="row">
            <div class="col-md-2 form-group">
              <label for="txtTipoReporte">Tipo Reporte: </label>
              <select name="txtTipoReporte" id="txtTipoReporte" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <option value="1">Detalle</option>
                <option value="2">Acumulado</option>
                <option value="3">Comparativo</option>
              </select>
            </div> 
						<div class="col-md-2 form-group">
							<label for="fechaIniSearch">Fecha Desde:</label>
							<input id="fechaIniSearch" name="fechaIniSearch" type="date" class="form-control form-control-sm" value="">
						</div>
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Fecha Hasta:</label>
							<input id="fechaFinSearch" name="fechaFinSearch" type="date" class="form-control form-control-sm" value="">
						</div> 
						<div class="col-md-6 form-group align-self-end text-right">
              <button type="button" class="btn btn-info" id="buscarSP"><i class="fas fa-search"></i> Buscar</button>
              <button type="button" class="btn btn-dark" id="limpiarSP"><i class="fas fa-trash"></i> Limpiar</button>
              <button type="button" class="btn btn-success" id="imprimirSP"><i class="fas fa-file-excel"></i> Imprimir</button>		
						</div>            
          </div>
        </div>
      </div>
    </div>  
  </div>    
	<div class="row" id="filtroTienda">
    <div class="col-md-12">
      <div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE VENTAS POR TIENDA</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
        <div class="card-body text-sm">
          <div class="row">
            <div class="col-md-2 form-group">
              <label for="txtTipoReporte">Tipo Reporte: </label>
              <select name="txtTipoReporte" id="txtTipoReporte" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <option value="1">Detalle</option>
                <option value="2">Acumulado</option>
                <option value="3">Comparativo</option>
              </select>
            </div> 
						<div class="col-md-2 form-group">
							<label for="fechaIniSearch">Fecha Desde:</label>
							<input id="fechaIniSearch" name="fechaIniSearch" type="date" class="form-control form-control-sm" value="">
						</div>
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Fecha Hasta:</label>
							<input id="fechaFinSearch" name="fechaFinSearch" type="date" class="form-control form-control-sm" value="">
						</div> 
						<div class="col-md-6 form-group align-self-end text-right">
              <button type="button" class="btn btn-info" id="buscarSP"><i class="fas fa-search"></i> Buscar</button>
              <button type="button" class="btn btn-dark" id="limpiarSP"><i class="fas fa-trash"></i> Limpiar</button>
              <button type="button" class="btn btn-success" id="imprimirSP"><i class="fas fa-file-excel"></i> Imprimir</button>		
						</div>            
          </div>
        </div>
      </div>
    </div>  
  </div>    
	<div class="row" id="filtrocliente">
    <div class="col-md-12">
      <div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE VENTAS POR CLIENTE</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
        <div class="card-body text-sm">
          <div class="row">
            <div class="col-md-2 form-group">
              <label for="txtTipoReporte">Tipo Reporte: </label>
              <select name="txtTipoReporte" id="txtTipoReporte" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <option value="1">Detalle</option>
                <option value="2">Acumulado</option>
                <option value="3">Comparativo</option>
              </select>
            </div> 
						<div class="col-md-2 form-group">
							<label for="fechaIniSearch">Fecha Desde:</label>
							<input id="fechaIniSearch" name="fechaIniSearch" type="date" class="form-control form-control-sm" value="">
						</div>
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Fecha Hasta:</label>
							<input id="fechaFinSearch" name="fechaFinSearch" type="date" class="form-control form-control-sm" value="">
						</div> 
						<div class="col-md-6 form-group align-self-end text-right">
              <button type="button" class="btn btn-info" id="buscarSP"><i class="fas fa-search"></i> Buscar</button>
              <button type="button" class="btn btn-dark" id="limpiarSP"><i class="fas fa-trash"></i> Limpiar</button>
              <button type="button" class="btn btn-success" id="imprimirSP"><i class="fas fa-file-excel"></i> Imprimir</button>		
						</div>            
          </div>
        </div>
      </div>
    </div>  
  </div> 
	<div class="row" id="filtroDiario">
    <div class="col-md-12">
      <div class="card card-light">
				<div class="card-header">
					<h3 class="card-title">REPORTE VENTAS DIARIAS</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fas fa-minus"></i>
						</button>
					</div>
				</div>
        <div class="card-body text-sm">
          <div class="row">
            <div class="col-md-2 form-group">
              <label for="txtTipoReporte">Tipo Reporte: </label>
              <select name="txtTipoReporte" id="txtTipoReporte" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <option value="1">Detalle</option>
                <option value="2">Acumulado</option>
                <option value="3">Comparativo</option>
              </select>
            </div> 
            <div class="col-md-2 form-group">
              <label for="txtVendedor">Nombre Vendedor: </label>
              <select name="txtVendedor" id="txtVendedor" class="form-control form-control-sm">
                <option value=""> TODOS </option>
                <?php
                if ($vendedores != NULL) {
                  foreach ($vendedores as $indice => $val) {
                    if ($val->PERSP_Codigo != '') { ?>
                      <option value="<?= $val->PERSP_Codigo; ?>"><?= $val->PERSC_Nombre." ".$val->PERSC_ApellidoPaterno." ".$val->PERSC_ApellidoMaterno; ?></option>
                <?php
                    }
                  }
                } ?>
              </select>
            </div>   
            <div class="col-md-2 form-group">
              <label for="txtMarca">Cliente: </label>
              <input type="text" id="txtMarca" name="txtMarca" class="form-control form-control-sm" maxlength="100" placeholder="Marca producto" value="">
            </div>
            <div class="col-md-2 form-group">
              <label for="txtMarca">Tienda: </label>
              <input type="text" id="txtMarca" name="txtMarca" class="form-control form-control-sm" maxlength="100" placeholder="Marca producto" value="">
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
          <div class="row justify-content-end">
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Tipo Documento:</label>
              <input type="text" id="txtMarca" name="txtMarca" class="form-control form-control-sm" maxlength="100" placeholder="Marca producto" value="">
						</div>    
						<div class="col-md-2 form-group">
							<label for="fechaFinSearch">Forma de Pago:</label>
              <input type="text" id="txtMarca" name="txtMarca" class="form-control form-control-sm" maxlength="100" placeholder="Marca producto" value="">
						</div>                
						<div class="col-md-8 form-group align-self-end text-right">
              <button type="button" class="btn btn-info" id="buscarSP"><i class="fas fa-search"></i> Buscar</button>
              <button type="button" class="btn btn-dark" id="limpiarSP"><i class="fas fa-trash"></i> Limpiar</button>
              <button type="button" class="btn btn-success" id="imprimirSP"><i class="fas fa-file-excel"></i> Imprimir</button>		
						</div>     
          </div>          
        </div>
      </div>
    </div>  
  </div>           
</section>
<section id="detalle">
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