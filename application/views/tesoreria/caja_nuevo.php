
 <style>
        #mapa{
            width: 900px;
            height: 300px;
            background: green;
        }
        #infor{
            width: 400px;
            height: 400px;
            float:left;
        }
        
        #tablaG {
    		font-family: arial, sans-serif;
    		border-collapse: collapse;
    		width: 100%;
    		border-style: solid;
		}

		td, th {
    		border: 1px solid #dddddd;
    		text-align: left;
    		padding: 8px;
    		border-style: solid;
		}
    </style>
		<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/tesoreria/caja.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
		<script>
  			$( function() {
    			$( "#tabs" ).tabs();
  			} );

  			
			
		    function soloLetras(e){
		       key = e.keyCode || e.which;
		       tecla = String.fromCharCode(key).toLowerCase();
		       letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
		       especiales = "8-37-39-46";

		       tecla_especial = false
		       for(var i in especiales){
		            if(key == especiales[i]){
		                tecla_especial = true;
		                break;
		            }
		        }

		        if(letras.indexOf(tecla)==-1 && !tecla_especial){
		            return false;
		        }
		    }

		    function soloNumeros(e) {
		        key = e.keyCode || e.which;
		        tecla = String.fromCharCode(key).toLowerCase();
		        letras = "0123456789";
		        especiales = [8, 37, 39, 46];

		        tecla_especial = false
		        for(var i in especiales) {
		            if(key == especiales[i]) {
		                tecla_especial = true;
		                break;
		            }
		        }

		        if(letras.indexOf(tecla) == -1 && !tecla_especial)
		            return false;
		    }

		    function limpia() {
		        var val = document.getElementById("limiteRetiro").value;
		        var tam = val.length;
		        for(i = 0; i < tam; i++) {
		            if(!isNaN(val[i]))
		                document.getElementById("limiteRetiro").value = '';
		        }
		    }		
  		</script>
<body>
<!-- Inicio -->
<div id="VentanaTransparente" style="display:none;">
  <div class="overlay_absolute"></div>
  <div id="cargador" style="z-index:2000">
    <table width="100%" height="100%" border="0" class="fuente8">
		<tr valign="middle">
		     <td> Por Favor Espere    </td>
		     <td><img src="<?=$base_url;?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>	</td>
		</tr>
    </table>
  </div>
</div>
	<div id="pagina">
			<div id="zonaContenido">
				<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo;?></div>
				<div id="frmBusqueda">
				<form id="frmProyecto" name="frmProyecto" method="post" action='<?php echo $url_action; ?>'>
					<div id="container" class="container">
						<ol>
						<h4>Primero debe completar los siguientes campos antes de enviar.</h4>						
							<div id="containerProyecto">								
							</div>
						</ol>
					</div>
					<div id="tabs">
  						<ul>
    						<li><a href="#tabs-1">General</a></li>
    						<li id="tabChequera" ><a href="#tabs-2" onClick="listar_bancos();">Datos Chequera</a></li>
  						</ul>
  						<div id="tabs-1">
   	 					<div id="datosProyecto" >
                           <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                             <tr>
                                 <td>Nombre (*)</td>
  <td><input name="nombreCaja" type="text" class="cajaGrande" onkeypress="return soloLetras(event), keypressError('nombreCaja')"
                                 			id="nombreCaja" maxlength="150" value="<?php echo $nombreCaja; ?>">
                                 </td>
                             </tr>
                             <tr>
                             	<td>Tipo Caja(*)</td>
                             	<td>
<select id="cboTipCaja" name="cboTipCaja" class="comboMedio" onclick="keypressError('cboTipCaja')">
                            	 			<?php echo $cboTipCaja; ?>
                            	 	 </select>
                                  <input id="tipo_caja" type="hidden" name="tipo_caja" value="0" >
                            	 </td>
                             </tr>
                            
                             <tr>
                            	 <td>Responsable</td>
                            	 <td>
                            	     <select id="cboResponsable" name="cboResponsable" class="comboMedio">
                            	 			<?php echo $cboResponsable; ?>
                            	 	 </select>
                            	 </td>
                             </tr>
                                                         
                              <tr>
                              	<td>Banco</td>
                             	<td>
                           <input type="hidden" name="cuentaCodigo" id="cuentaCodigo" value="0"  />
                      				<input type="hidden" name="posicionEditar" id="posicionEditar"  />
                             		<select id="cboBancos" name="cboBancos" class="comboMedio" onchange="cargar_cuenta(this);">
                             					<?php echo $cboBancos; ?>
                             		</select>
                             	</td>
                                <td>N&deg; de cuenta (*)</td>
                                <td>
                                	<select id="cboCuentas" name="cboCuentas" class="comboMedio" onchange="cargar_datosCuenta(this);">
                             					<?php echo $cboCuentas; ?>
                             		</select>
                                 </td>
                             </tr>
                             <tr>
                             	<td>Tipo Cuenta</td>
                             	<td>
                             	<div id="TipoCuenta">
                             		 <input name="tipCuenta" type="text" class="cajaGeneral" disabled
                                 			id="tipCuenta" maxlength="150" value="">
                                 			&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
                                 			&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;	
                                 	 Moneda&nbsp; &nbsp;&nbsp;
                                 	 <input name="monedaCuenta" type="text" class="cajaGeneral" disabled
                                 			id="monedaCuenta" maxlength="150" value="">
                                 	 
                                 </div>
                                </td>
                                
                             </tr>
                           
                             <tr>
                             	<td>Tipo</td>
                                 <td>
                                 	<select id="cboTipoCaja" name="cboTipoCaja" class="comboMedio">
                             				<option value=""> ::Seleccione:: </option>
                             				<option value="1">INGRESO</option>
                             				<option value="2">SALIDA</option>
                             		</select>
                                 </td>
                                <td>Limite de Retiro</td>
                                <td>
                                    <input name="limiteRetiro" type="text" class="cajaGeneral" onkeypress="return soloNumeros(event)"
                                    	   id="limiteRetiro" maxlength="150" value="<?php echo $limiteRetiro; ?>">
                                </td>
                                <td>
                                	<a href="javascript:;" onClick="agregar_cuenta();">
                      					<img src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" border="1" align="absbottom">
                      				</a>
                      			</td>
                             </tr>
                             
                             </table>
                             <div id="frmBusqueda" style="height:250px; overflow: auto">
                              <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                              	<tr class="cabeceraTabla">
                    						<td width="2%">
                        						<div align="center">&nbsp;</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">BANCO</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">NRO. CUENTA</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">TIPO CUENTA</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">MONEDA</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">TIPO CAJA</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">LIMITE DE RETIRO</div>
                    						</td>
                    						<td width="1%">
                        						<div align="center">ELIMINAR</div>
                    						</td>
                						</tr>
                              </table>
                              <table id="tblDetalleCuenta" class="fuente8" width="100%" border="1">
                          <?php
											if (count($detalle_cuenta) > 0) {
											  foreach ($detalle_cuenta as $indice => $valor) {
											  		$cuentaCodigo	 	= $valor->CAJCUENT_Codigo;
											  		$cboCuentas			= $valor->CUENT_Codigo;
											  		$cboBancos	 		= $valor->BANP_Codigo;
											  		$bancoNombre		= $valor->BANC_Nombre;
											  		$numroCuenta 		= $valor->CUENT_NumeroEmpresa;
											  		$tipCuenta 			= $valor->CUENT_TipoCuenta;
											  		$tipCuentaNombre= $valor->CUENT_TipoCuenta;
											  		$monedaCuenta		= $valor->MONED_Codigo;
											  		$monedaNombre		= $valor->MONED_Descripcion;
											  		$limiteRetiro 	= $valor->CAJCUENT_LIMITE;
											  		$tipoCaja 			= $valor->TIPOING_Codigo;
											  		$tipoNombre    	= $valor->TIPOING_Codigo;
                        if (($indice + 1) % 2 == 0) {
														$clase = "itemParTabla";
													} else {
														$clase = "itemImparTabla";
													}
                          $indice += 1;
											?>
										<tr id="<?=$indice?>" class="<?=$clase?>">
													
													<td width="5%"> <?php  echo $indice; ?>
<input type="hidden" name="txtCuentaCodigo[<?=$indice?>]" id="txtCuentaCodigo<?=$indice?>" value="<?=$valor->CAJA_Codigo?>">
<input type="hidden" name="cuentaCodigo[<?php echo $indice; ?>]"
                                   id="cuentaCodigo[<?php echo $indice; ?>]" 
                                   value="<?php echo $cuentaCodigo; ?>" />               
                          </td>
																			
													<td width="15%">
    												  <div align="left">
    			<label id="idlbancoCodigo<?php echo $indice; ?>" ><?php echo $bancoNombre; ?></label>
<input type="hidden" class="cajaGeneral"
  maxlength="250" name="cboBancos[<?php echo $indice; ?>]"
  id="cboBancos[<?php echo $indice; ?>]"
  value="<?php echo $cboBancos; ?>"/>
   													  </div>
                                				    </td>
                                				    <td width="15%">
    												  <div align="left">
    												    <label id="idlnumroCuenta<?php echo $indice; ?>" ><?php echo $numroCuenta; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="cboCuentas[<?php echo $indice; ?>]"
    									 					   id="cboCuentas[<?php echo $indice; ?>]"
   										 					   value="<?php echo $cboCuentas; ?>"/>
   													  </div>
                                				    </td>
                                				    <td width="15%">
    												  <div align="left">
    												    <label id="idltipCuenta<?php echo $indice; ?>" ><?php echo $tipCuentaNombre; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="tipCuenta[<?php echo $indice; ?>]"
    									 					   id="tipCuenta[<?php echo $indice; ?>]"
   										 					   value="<?php echo $tipCuenta; ?>"/>
                                            		  </div>
                                				    </td>
                                				    <td width="15%">
    												  <div align="left">
    												    <label id="idlmoneda<?php echo $indice; ?>" ><?php echo $monedaNombre; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="monedaCuenta[<?php echo $indice; ?>]"
    									 					   id="monedaCuenta[<?php echo $indice; ?>]"
   										 					   value="<?php echo $monedaCuenta; ?>"/>
   													  </div>
                                				    </td>
                                				    <td width="15%">
    												  <div align="left">
    												    <label id="idltipo<?php echo $indice; ?>" ><?php echo $tipoNombre; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="tipoCaja[<?php echo $indice; ?>]"
    									 					   id="tipoCaja[<?php echo $indice; ?>]"
   										 					   value="<?=$valor->TIPOING_C?>"/>
   
   													  </div>
                                				    </td>
                                				    <td>
    												  <div align="left">
    												    <label id="idllimiteRetiro<?php echo $indice; ?>" ><?php echo $limiteRetiro; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="limiteRetiro[<?php echo $indice; ?>]"
    									 					   id="limiteRetiro[<?php echo $indice; ?>]"
   										 					   value="<?php echo $limiteRetiro; ?>"/>
   													  </div>
                                				    </td>
                                				    <td width="3%">
   														<div align="center">
   															<font color="red">
   																<strong>
   																	<a href="javascript:;" onclick="eliminar_cuenta(<?php echo $indice; ?>);">
																		<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>
																	</a>
																</strong>
															</font>
        												</div>
                                					</td>
                                					<td width="3%">
	    												<div align="left"  style="width: 60%;" >
	    													<a href='javascript:;' onclick='editar_cuenta(<?php echo $indice; ?>)'><img src='<?php echo base_url(); ?>public/images/icons/modificar.png?=<?=IMG;?>' width='16' height='16' border='0' title='Modificar'></a>
	    												</div>
                               <input type="hidden" name="cuentaaccion[<?php echo $indice; ?>]"
                                id="cuentaaccion[<?php echo $indice; ?>]" value="m"/>
                                				    </td>
                                				    
												</tr>
												
												<?php } }?>
            				  </table>
            				  
            				  
                          
            				 </div>
            				 <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
            				 	<tr>
                             		<td>Observaciones</td>
                             		<td>
                             			<textarea name="observaciones" id="observaciones" cols="45" rows="5" maxlength="300">
                             						<?php echo $observaciones; ?>
                             			</textarea>
                             		</td>
                                </tr> 
                             </table>
  					 	</div>
  						</div>
  						<div id="tabs-2">  
    						<div id="datosChequera" >
    							<table id="TABLASS" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
    							
    							  <tr>
                      				<td>Discripci&#243;n</td>
                      				<td><input name="descripcion" type="text" class="cajaGrande" onkeypress="return soloLetras(event)"
                      						   id="descripcion" maxlength="150" value="<?php echo $descripcion; ?>">
                      				</td>
                      			  </tr>
                      			  <tr>
                      				<td>Banco</td>
                             		<td>
                             			<input type="hidden" name="chequeraCodigo" id="chequeraCodigo" value="0"  />
                      					<input type="text" name="posicionEditarDos" id="posicionEditarDos"  />
                             			<select id="cboBancoCuenta" name="cboBancoCuenta" class="comboMedio" onchange="cargar_cuentaCheque(this);">
                             					<?php echo $cboBancos; ?>
                             			</select>
                             		</td>
                             		<td>N&deg; de cuenta (*)</td>
                                	<td>
                                		<select id="cboCuentaCheque" name="cboCuentaCheque" class="comboMedio" onchange="cargar_serieCuenta(this);">
                             					<option value=""> ::Seleccione:: </option>
                             			</select>
                                 	</td>
                                 </tr>
                                 <tr>
                      				<td>Serie</td>
                      				<td>
                      					<select id="cboSerie" name="cboSerie" class="comboMedio" onchange="cargar_serieNumero(this);">
                                   				<option value=""> ::Seleccione:: </option>
                                   		</select>
                      				</td>
                      				<td><a href="javascript:;" onClick="agregar_chequera();">
                      					<img src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" border="1" align="absbottom">
                      					</a>
                      				</td>
                      			  </tr>    							  
                      			</table>
							</div>
							<div id="frmBusqueda" style="height:250px; overflow: auto">
            						<table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
                						<tr class="cabeceraTabla">
                    						<td width="1%">
                        						<div align="center">&nbsp;</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">DESCRIPCION</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">BANCO</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">NRO. CUENTA</div>
                    						</td>
                    						<td width="4%">
                        						<div align="center">SERIE</div>
                    						</td>
                    						<td width="1%">
                        						<div align="center">ACCION</div>
                    						</td>
                						</tr>
            						 </table>
            						 <div>
            						 <table id="tblDetalleChequera" class="fuente8" width="100%" border="1">
            						  	 <?php
											if (count($detalle_chequera) > 0) {
											  foreach ($detalle_chequera as $indice => $valor) {
											  	$chequeraCuentaCodigo 	= $valor->CAJCHEK_Codigo;
											  	$descripcion 			= $valor->CAJCHEK_Descripcion;
											  	$bancoCodigo 			= $valor->BANP_Codigo;
											  	$numroCuenta 			= $valor->CUENT_NumeroEmpresa;
											  	$cboSerie				= $valor->CHEK_Codigo;
											  	$serie					= $valor->SERIP_Codigo;
											  	$bancoNombre			= $valor->BANC_Nombre;
											  	$cuenta					= $valor->CUENT_Codigo;
											  	
								
													if (($indice + 1) % 2 == 0) {
														$clase = "itemParTabla";
													} else {
														$clase = "itemImparTabla";
													}
											?>
												<tr>
													
													<td width="4%"> <?php  echo $indice; ?></td>
															<input type="hidden" name="chequeraCodigo[<?php echo $indice; ?>]"
        												   id="chequeraCodigo[<?php echo $indice; ?>]" 
        												   value="<?php echo $chequeraCuentaCodigo; ?>" />					
													<td width="15%">
    												  <div align="left">
    												   <label id="idldescripcion<?php echo $indice; ?>" ><?php echo $descripcion; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="descripcion[<?php echo $indice; ?>]"
    									 					   id="descripcion[<?php echo $indice; ?>]"
   										 					   value="<?php echo $descripcion; ?>"/>
   													  </div>
                                				    </td>
                                				    <td width="14%">
    												  <div align="left">
    												    <label id="idlbancoCodigo<?php echo $indice; ?>" ><?php echo $bancoNombre; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="bancoCodigo[<?php echo $indice; ?>]"
    									 					   id="bancoCodigo[<?php echo $indice; ?>]"
   										 					   value="<?php echo $bancoCodigo; ?>"/>
   													  </div>
                                				    </td>
                                				    <td width="14%">
    												  <div align="left">
    												    <label id="idlnumroCuenta<?php echo $indice; ?>" ><?php echo $numroCuenta; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="cuenta[<?php echo $indice; ?>]"
    									 					   id="cuenta[<?php echo $indice; ?>]"
   										 					   value="<?php echo $cuenta; ?>"/>
                                            		  </div>
                                				    </td>
                                				    <td width="12%">
    												  <div align="left">
    												    <label id="idlchequera<?php echo $indice; ?>" ><?php echo $serie; ?></label>
    													<input type="hidden" class="cajaGeneral"
   										 					   maxlength="250" name="cboSerie[<?php echo $indice; ?>]"
    									 					   id="cboSerie[<?php echo $indice; ?>]"
   										 					   value="<?php echo $cboSerie; ?>"/>
                                            		  </div>
                                				    </td>
                                				    <td width="3%">
   														<div align="center">
   															<font color="red">
   																<strong>
   																	<a href="javascript:;" onclick="eliminar_chequera(<?php echo $indice; ?>);">
																		<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>
																	</a>
																</strong>
															</font>
        												</div>
                                					</td>
                                					<td width="3%">
	    												<div align="left"  style="width: 60%;" >
	    													<a href='javascript:;' onclick='editar_chequera(<?php echo $indice; ?>)'><img src='<?php echo base_url(); ?>public/images/icons/modificar.png?=<?=IMG;?>' width='16' height='16' border='0' title='Modificar'></a>
	    												</div>
                                				    </td>
                                				     <input type="hidden" name="cuentaaccion[<?php echo $indice; ?>]"
  															id="cuentaaccion[<?php echo $indice; ?>]" value="m"/>
												</tr>
												
												<?php }} ?>
            						 </table>
            						 </div>
            				</div>		
						</div>
				</div>
                <div id="datosProyecto" >
		 		</div>
				<div style="margin-top:20px; text-align: center">
                    <a href="#" id="imgGuardarProyecto"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="imgLimpiarProyecto"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="imgCancelarProyecto"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
 <input id="modo" name="modo" type="hidden" value="<?php echo $modo;?>">                                       
                    <input type="hidden" name="caja" id="caja" value="<?php echo $datos->id; ?>" />
                </div>
			  </form>
		  </div>
		  </div>
		</div></div>
	</body>
