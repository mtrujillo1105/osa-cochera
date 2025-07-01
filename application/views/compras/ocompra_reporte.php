<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/ocompra.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
$(document).ready(function(){
    $("a#linkVerProveedor, a#linkVerProducto").fancybox({
            'width'	     : 700,
            'height'         : 450,
            'autoScale'	     : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
    });
});

function seleccionar_proveedor(codigo,ruc,razon_social, empresa, persona){
    $("#proveedor").val(codigo);
    $("#ruc_proveedor").val(ruc);
    $("#nombre_proveedor").val(razon_social);
}
function seleccionar_producto(codigo,interno,familia,stock,costo){
    $("#producto").val(codigo);
    $("#codproducto").val(interno);
    $("#cantidad").focus();
    obtener_nombre_producto(codigo);
}
function obtener_nombre_producto(producto){ 
    base_url   = $("#base_url").val();
    url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto; 
    $.getJSON(url,function(data){
          $.each(data, function(i,item){
                nombre_producto = item.PROD_Nombre;
          });
           $("#nombre_producto").val(nombre_producto);
    });
}
</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda" >
                <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                    <tr>
                        <td width="200" align="left" valign="top">
                            <h3 style="margin:5px 0px;">Reportes disponibles</h3>
                            <ul id="menureporte">
                                <li id="repo1">Listado de O. de Compra</li>
                                <!--<li id="repo2">Estadísticas de O. de Compra</li>-->
                								<li id="repo3">Reportes de Compras</li>
                								<li id="repo4">Estad&iacute;sticas de Compra Anual</li>
                								<li id="repo5">Estad&iacute;sticas de Compra Mensual</li>
                            </ul>
                        </td>
                        <td>&nbsp;</td>
                        <td valign="top">
                            <div class="lienzoreporte" id="divRepo1">
                                <h3>Listado de O. de Compra</h3>   
                                <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                                <tr>
                                    <td align='left' width="20%">Fecha inicial</td>
                                    <td align='left' width="15%">
                                        <?php echo $fechai?>
                                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "fechai",      // id del campo de texto
                                                ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>
                                    <td align='left' width="10%">Fecha final</td>
                                    <td align='left' width="45%">
                                        <?php echo $fechaf?>
                                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "fechaf",      // id del campo de texto
                                                ifFormat       :    "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario2"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Proveedor </td>
                                    <td colspan="3">
                                            <input type="hidden" name="proveedor" id="proveedor" size="5" class="cajaPequena" value="">
                                            <input type="text" name="ruc_proveedor" class="cajaPequena" id="ruc_proveedor" size="10" maxlength="11" onBlur="obtener_proveedor();" value="" onKeyPress="return numbersonly(this,event,'.');" />
                                            &nbsp;<input type="text" name="nombre_proveedor" class="cajaGrande cajaSoloLectura" id="nombre_proveedor" size="15" maxlength="15" readonly="readonly" value="" />
                                            <a href="<?=$base_url;?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Art&iacute;culo</td>
                                    <td colspan="3">
                                            <input name="producto" type="hidden" class="cajaPequena" id="producto" size="10" maxlength="11" />
                                            <input name="codproducto" type="text" class="cajaPequena" id="codproducto" size="10" maxlength="11" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');" />&nbsp;
                                            <input NAME="nombre_producto" type="text" class="cajaGrande cajaSoloLectura" id="nombre_producto" size="15" maxlength="15" readonly="readonly" />
                                            <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                 </tr>
                                 <tr>
                                     <td>Aprobación</td>
                                     <td colspan="3">
                                             <select name="aprobado" id="aprobado" class="comboMedio">
                                                 <option value="" selected="selected">::Seleccione::</option>
                                                 <option value="0">Pendiente</option>
                                                 <option value="1">Aprobado</option>
                                                 <option value="2">Desaprobado</option>
                                              </select>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td>Verificación de ingreso</td>
                                     <td colspan="3">
                                             <select name="ingreso" id="ingreso" class="comboMedio">
                                                 <option value="" selected="selected">::Seleccione::</option>
                                                 <option value="0">Pendiente</option>
                                                 <option value="1">Si</option>
                                              </select>
                                     </td>
                                 </tr>
                            </table> 
                            <a href="javascript:;" onclick="ver_reporte_pdf()" id="verReporte"><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                            
                            </div>
                            <div class="lienzoreporte" id="divRepo2">
                                
                            </div>
							<div class="lienzoreporte" id="divRepo3">
							<table>
								<tr>
									<td colspan="2">
										Reportes de Compra
									</td>
								</tr>
								<tr>
									<td>Seleccione A&ntilde;o</td>
									<td>
										<?php echo $combo; ?>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<a href="javascript:;" onclick="ver_reporte_pdf_compras()" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
									</td>
								</tr>
							</table>
                            </div>
							<div class="lienzoreporte" id="divRepo4">
							<table>
								<tr>
									<td colspan="2">
										Estad&iacute;sticas de Compra Anual
									</td>
								</tr>
								<tr>
									<td>Seleccione A&ntilde;o</td>
									<td>
										<?php echo $combo2; ?>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<a href="javascript:;" onclick="estadisticas_compras_ventas('C')" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
									</td>
								</tr>
							</table>
                            </div>
							<div class="lienzoreporte" id="divRepo5">
							<table>
								<tr>
									<td colspan="2">
										Estad&iacute;sticas de Compra Mensual
									</td>
								</tr>
								<tr>
									<td>Seleccione A&ntilde;o</td>
									<td>
										<?php echo $combo3; ?>
									</td>
									<td>
										<select id="mesVenta3" name="mesVenta3">
											<option value="0">Seleccione...</option>
											<option value="1">ENERO</option>
											<option value="2">FEBRERO</option>
											<option value="3">MARZO</option>
											<option value="4">ABRIL</option>
											<option value="5">MAYO</option>
											<option value="6">JUNIO</option>
											<option value="7">JULIO</option>
											<option value="8">AGOSTRO</option>
											<option value="9">SETIEMBRE</option>
											<option value="10">OCTUBRE</option>
											<option value="11">NOVIEMBRE</option>
											<option value="12">DICIEMBRE</option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<a href="javascript:;" onclick="estadisticas_compras_ventas_mensual('C')" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/botonreporte.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>

                    <a href="javascript:;" onclick="estadisticas_compras_ventas_mensual_excel('C')" ><img  style="margin:15px 0px;"  src="<?=$base_url;?>public/images/icons/xls.png?=<?=IMG;?>" width="22" height="22" class="imgBoton" ></a>
									</td>
								</tr>
							</table>
                            </div>
                        </td>
                       </tr>
                </table>
            </div>
            <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
        </div>
    </div>
</div>