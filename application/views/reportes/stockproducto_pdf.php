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
							<th style="width: 10%" data-orderable="true">CÓDIGO</th>
							<th style="width: 30%" data-orderable="true">NOMBRE</th>
							<th style="width: 15%" data-orderable="true">FAMILIA</th>
							<th style="width: 10%" data-orderable="true">MARCA</th>
							<th style="width: 10%" data-orderable="true">MODELO</th>
							<th style="width: 05%" data-orderable="true">UNIDAD</th>
							<th style="width: 05%" data-orderable="true">STOCK</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 0;
							foreach($records as $value){
								$i++;
								?>
								<tr>
									<td><?php echo $value->PROD_Codigo;?></td>
									<td><?php echo $value->PROD_Nombre;?></td>
									<td><?php echo $value->FAMI_Descripcion;?></td>
									<td><?php echo $value->MARCC_Descripcion;?></td>
									<td><?php echo $value->PROD_Modelo;?></td>
									<td><?php echo $value->UNDMED_Simbolo;?></td>
									<td><?php echo $value->ALMPROD_Stock;?></td>
								</tr>
								<?php
								if($i>10) break;
							}							
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>