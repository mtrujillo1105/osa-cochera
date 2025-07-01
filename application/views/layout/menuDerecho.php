<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/estiloMenuDerecho.css?=<?=CSS;?>" type="text/css"/>
<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/cssmodal.css?=<?=CSS;?>" type="text/css"/>

<nav class="menu_">
	<ul>
		<li>
			<span class="icon-side-bar"> <img src="<?=base_url();?>public/images/icons/icon-menu.png" class="icon-p">
				<ul class="menu_opciones"> <?php
					foreach ($subMenu as $acceso_rapido) {
				    	if (trim($acceso_rapido->MENU_AccesoRapido) == '1'){ ?>
					        <li class="menu_element">
					            <a class='' href='<?=base_url()."index.php/$acceso_rapido->MENU_Url";?>'>
									<img class="imagenMenuDer" alt="<?=$acceso_rapido->MENU_Titulo;?>" src="data:;base64,<?=$acceso_rapido->MENU_Icon;?>">
									<span class="idSpanMenuDer"><?=$acceso_rapido->MENU_Titulo;?></span>
								</a>
					        </li> <?php
						}
					}

					if ( $productos == true){ ?>
						<li class="menu_element">
							<a class="" href="javascript:;" id="stockMin" name="stockMin">
								<img class="imagenMenuDer" alt="" src="<?=$base_url;?>public/images/icons/palet-box.png" >
								<span class="idSpanMenuDer">Alerta de Stock</span>
							</a>
						</li> <?php
					} ?>

					<section class="menu_element">
						<a class="" href="https://e-consulta.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm" target="_blank">
							<img class="imagenMenuDer" alt="Contabilidad" src="<?=$base_url;?>public/images/icons/sunat.png?=<?=IMG;?>">
							<span class="idSpanMenuDer">Consultar CPE Sunat</span>
						</a>
				    </section>
						
					<!--<section class="menu_element">
						<a class="cAMenuD viewCalendar" href="javascript:;">
							<img class="imagenMenuDer" alt="Despacho" src="<?=$base_url;?>public/images/icons/calendar-icon.png?=<?=IMG;?>">
							<span class="idSpanMenuDer">Programación de despacho</span>
						</a>
					</section>-->					
				</ul>
			</span>
		</li>
	</ul>
</nav>

<div>
	<div class="w3-container" style="font-family: Verdana, sans-serif; font-size: 8pt">
		<div id="id01" class="w3-modal">
			<div class="w3-modal-content w3-animate-zoom" style="width: 85%">
				<header class="w3-container w3-teal" style="background-color: orange; opacity: 0.7"> 
					<span onclick="document.getElementById('id01').style.display = 'none'" class="w3-button w3-display-topright">&times;</span>
					<h2 style="text-align: center;">Productos con stock Minimo</h2>
				</header>
				<div class="w3-container">
					<table width="100%" >
						<thead>
							<tr>
								<th>Item</th>
								<th>Codigo</th>
								<th>Nombre</th>
								<th>Stock Actual</th>
								<th>Stock Minimo</th>
								<th>Pendiente en Cot.</th>
								<th>Pendiente en Guia</th>
								<th>Pendiente en F/B/C</th>
							</tr>
						</thead>
						<tbody id="tableStockMin">
						</tbody>
					</table>
				</div>
				<br>
			</div>
		</div>
	</div>
</div>

<div class="w3-container" style="font-family: Verdana, sans-serif; font-size: 9pt">
	<div class="viewDespacho w3-modal">
		<div class="w3-modal-content w3-animate-zoom" style="width: 85%">
			<header class="w3-container w3-teal" style="background-color: orange; opacity: 0.7"> 
				<span class="w3-button w3-display-topright closeCalendar">&times;</span>
				<h2 style="text-align: center;">Programación de despacho</h2>
			</header>
			<div class="">
                <div class="responsive-calendar">
                    <div class="controls">
                        <h2>
	                        <span class="pull-left" data-go="prev">
	                            <div class="btn btn-warning">Anterior</div>
	                        </span>
	                            <span data-head-month></span>
	                            <span> del </span>
	                            <span data-head-year></span>
	                        <span class="pull-right" data-go="next">
	                            <div class="btn btn-warning">Siguiente</div>
	                        </span>
                        </h2>
                    </div>
                    <br>
                    <div class="day-headers">
                        <div class="day header">Lunes</div>
                        <div class="day header">Martes</div>
                        <div class="day header">Miercoles</div>
                        <div class="day header">Jueves</div>
                        <div class="day header">Viernes</div>
                        <div class="day header">Sabado</div>
                        <div class="day header">Domingo</div>
                    </div>
                    <div class="days" data-group="days"></div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
    	$(".viewCalendar").click(function(){
			var base_url   = $("#base_url").val();
			url = base_url + "index.php/almacen/produccion/consultarCalendario/";
			$.ajax({
				url: url,
				dataType: 'json',
				success:function (data){
					if(data != null){
						$(".viewDespacho").show();						
				        $(".responsive-calendar").responsiveCalendar({
				          time: '<?=date("Y-m");?>',
				          events: { data }
				        });
					}
				},
				error:function (error){
				}
			});
    	});
    });

    $(".closeCalendar").click(function(){
    	$(".viewDespacho").hide();
    });
	
	$("#stockMin").click(function(){
		$("#id01").show();
		var base_url   = $("#base_url").val();
		url = base_url + "index.php/almacen/producto/verificarStockAlert/"+true;
		$.ajax({
			url: url,
			dataType: 'json',
			success:function (data){
				if(data != null){
					$('#divStockMin').show();
					$('#divStockMin').css({'background':'orange'});
					var fila = '';
					$("#tableStockMin").html('');
					$.each(data, function(i, item) {
						i = i+1;
						fila += '<tr>';
						fila += '<td style="text-align: center">' + i +'</td>';
						fila += '<td style="text-align: center">' + item.codigoProducto +'</td>';
						fila += '<td style="text-align: center">' + item.nombreProducto + '</td>';
						fila += '<td style="text-align: center">' + item.stockActual + '</td>';
						fila += '<td style="text-align: center">' + item.stockMinimo + '</td>';
						fila += '<td style="text-align: center">' + item.pendienteOC + '</td>';
						fila += '<td style="text-align: center">' + item.pendienteGuia + '</td>';
						fila += '<td style="text-align: center">' + item.pendienteComprobante + '</td>';
						fila += '</tr>';
					});
					$("#tableStockMin").append(fila);
				}
			},
			error:function (error){
			}
		});
	});
</script>