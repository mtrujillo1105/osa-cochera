
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>

<script type="text/javascript" src="<?=$base_url;?>public/js/tesoreria/banco_movimiento.js?=<?=JS;?>"></script>
<script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>


<div id="pagina">
    <div id="zonaContenido">
    		<form id="formmoviminetoindex" name="formmoviminetoindex">
                    <div align="center">
 <div id="tituloForm" class="header"><?php echo $titulo;?></div>
                        
                        <div id="zonaContenido">
                          <table class="fuente8" cellspacing=0 cellpadding=3 border=0 style="width:100%;float:left;" >
                                    <tr>
                                            <td style="width:20%;">Bancos</td>
                                            <td>
                                            	<select id="cboNombreBanco" name="cboNombreBanco" class="comboMedio" onchange="seleccionaBanco(event)">
                            	 					<?php echo $cboBancos; ?>
                            	 	 			</select>
                            	 	 		</td>
                                    </tr>
                                    <tr>
                                    	<td>Cuentas</td>
                                    	<td>
                                    		<select id="cuentas" name="cuentas" class="comboMedio">
                                    		</select>
                                    	</td>
                                    </tr>
           	               </table>
           			   </div>
           			   <div class="acciones">
    					<div id="botonBusqueda">
        					<ul id="limpiarMovimiento" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
        					<ul id="buscarMovimiento" class="lista_botones"><li id="buscar">Buscar</li></ul>
    					</div>
					</div>

                    <div style="padding: 10px;text-align: left;">
                        <h4>Resumen de movimientos : </h4>
                        <hr>
                        <table cellpadding="5">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Ingresos</th>
                                    <th>Salidas</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>SOLES</th>
                                    <td align="right"><?php echo number_format($resumen["ingresos"]["soles"], 2) ?></td>
                                    <td align="right"><?php echo number_format($resumen["salidas"]["soles"], 2) ?></td>
                                    <td align="right"><?php echo number_format($resumen["ingresos"]["soles"] - $resumen["salidas"]["soles"], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>DOLARES</th>
                                    <td align="right"><?php echo number_format($resumen["ingresos"]["dolares"], 2) ?></td>
                                    <td align="right"><?php echo number_format($resumen["salidas"]["dolares"], 2) ?></td>
                                    <td align="right"><?php echo number_format($resumen["ingresos"]["dolares"] - $resumen["salidas"]["dolares"], 2) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
					
					
<!-- 					/*************************************************************************************/ -->
					
					
					       <table class="fuente8" width="100%" border="0" id="tablaregisnombrecaja">
                <tr class="cabeceraTabla">
                    <td>
                        <div align="center">FECHA</div>
                    </td>
                    <td>
                        <div align="center">CLIENTE / PROVEEDOR</div>
                    </td>
                    <td colspan="2">
                        <div align="center">MONTO</div>
                    </td>
                    <td>
                        <div align="center">Voucher</div>
                    </td>
                </tr>

                <tbody>
                    <?php foreach ($lista as $index => $movimiento): ?>
                    <tr>
                        <td>
                            <?php echo date("d/m/Y", strtotime($movimiento->CAJAMOV_FechaSistema)); ?>
                        </td>
                        <td>
                            <?php echo $movimiento->razonSocial ?>
                        </td>
                        <td><?php echo $movimiento->MONED_Simbolo ?></td>
                        <td align="right">
                            <?php echo ($movimiento->CAJAMOV_MovDinero == 1 ? '- ' : '') . number_format($movimiento->PAGC_Monto, 2) ?>
                        </td>
                        <td align="right">
                            <?php if(!is_null($movimiento->PAGP_Serie)) echo ($movimiento->CAJAMOV_MovDinero == 1 ? 'SAL ' : 'ING ') . str_pad($movimiento->PAGP_Serie, 3, '0', STR_PAD_LEFT) . " - " . str_pad($movimiento->PAGP_Numero, 8, 0, STR_PAD_LEFT) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>                                              
                </table>
					
        	</div>	
        </form>
    </div>
</div>