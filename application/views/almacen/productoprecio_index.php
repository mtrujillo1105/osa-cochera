       <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/productoprecio.js?=<?=JS;?>"></script>		
        <script language="javascript">
            var base_url;
            jQuery(document).ready(function(){
                $('input[name^="PORC_"]').blur(function(){
                    if($(this).val()!=''){
                        var porc=parseFloat($(this).val());
                        $(this).parent().parent().next().find('input[name^="PREC_"]').val('');
                        var pc = parseFloat($(this).parent().parent().parent().find('input[name^="PC"]').val());
                        var pv = money_format(pc*(porc+100)/100);
                        $(this).parent().parent().next().find('input[name^="PREC_"]').val(pv);
                    }
                });
               
                $('input[name^="PORC_0_"]').blur(function(){
                    if($(this).val()!=''){
                        var porc=parseFloat($(this).val());
                        var sufijo=$(this).attr('name').substr(6,2);
                        $.each($('input[name^="PREC_"][name$="'+sufijo+'"]'), function(i,item){
                            var pc = parseFloat($(item).parent().parent().parent().find('input[name^="PC"]').val());
                            var pv = money_format(pc*(porc+100)/100);
                            $(item).parent().parent().prev().find('input[name^="PORC_"]').val(porc);
                            $(item).val(pv);
                        });
                    }
                });
           
                $('input[name^="PREC_"]').keyup(function(e){
                    var key=e.keyCode || e.which;
                    if ((key>=48 && key<=57) || key==8){
                        $(this).parent().parent().prev().find('input[name^="PORC_"]').val('');
                    }
                });
                
                $('#txtCodigo, #txtNombre, #txtFamilia, #txtFamilia, #txtMarca').keyup(function(e){
                    var key=e.keyCode || e.which;
                    $('#txtFechaIni, #txtCantMin').val('');
                });
                
                $('#txtFechaIni, #txtCantMin').keyup(function(e){
                    var key=e.keyCode || e.which;
                    $('#txtCodigo, #txtNombre, #txtFamilia, #txtFamilia, #txtMarca').val('');
                });
            });
        </script>		
        <div id="pagina">
            <div id="zonaContenido">
                <div align="center">
                    <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>
                    <div id="frmBusqueda" >
                        <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $action; ?>">
                            <table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>					
                                <tr>
                                    <td width="16%">Código</td>
                                    <td><input id="txtCodigo" type="text" class="cajaPequena" NAME="txtCodigo" maxlength="30" value="<?php echo $codigo; ?>" /></td>
                                    <td width="14%">Fecha Mínima de Ingreso</td>
                                    <td width="12%">
                                        <input name="txtFechaIni" type="text" class="cajaGeneral cajaSoloLectura" id="txtFechaIni" value="<?php echo $fechaIni; ?>" size="10" maxlength="10" readonly="readonly" />
                                        <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" id="Image1" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "txtFechaIni",      // id del campo de texto
                                                ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nombre</td>
                                    <td><input id="txtNombre" name="txtNombre" type="text" class="cajaGrande" maxlength="100" value="<?php echo $nombre; ?>"></td>
                                    <td>Stock Mínimo</td>
                                    <td><input name="txtCantMin" type="text" class="cajaGeneral" id="txtCantMin" value="<?php echo $cantMin; ?>" size="10" /></td>
                                </tr>
                                <tr>
                                    <td>Familia</td>
                                    <td><input id="txtFamilia" type="text" class="cajaGrande" NAME="txtFamilia" maxlength="100" value="<?php echo $familia; ?>"></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Marca</td>
                                    <td><input id="txtMarca" type="text" class="cajaGrande" NAME="txtMarca" maxlength="100" value="<?php echo $marca; ?>"></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </form>
                    </div>
                   <div class="acciones">	
	                    <div id="botonBusqueda">
	                        <ul id="imprimirProductoPrecio" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
	                        <ul id="buscarProductoPrecio" class="lista_botones"><li id="buscar">Buscar</li></ul>                                        
	                    </div>
	                    
	                    <div id="lineaResultado">
	                    	<table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
	                        	<tr>
	                            	<td width="50%" align="left">N de productos encontrados:&nbsp;<?php echo $registros; ?> </td>
	                           </tr>
	                         </table>
	                   </div>
                   </div>
                        <?php if (count($_POST) > 0) { ?>
                        <form id="frmProductoPrecio" name="frmProductoPrecio" method="post" action="<?php echo site_url('almacen/producto/productos_precios_grabar'); ?>">
                            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla; ?></div>
                            <div id="frmResultado">
                                <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                                    <tr class="cabeceraTabla">
                                        <td width="3%" valign="top"><div align="center">ITEM</td>
                                        <td width="5%" valign="top"><div align="center">CODIGO</div></td>
                                        <td valign="top"><div align="center">DESCRIPCION</div></td>	
                                        <td valign="top"><div align="center">STOCK</div></td>
                                        <td valign="top"><div align="center">PC</div></td>
                                        <?php
                                        if (count($lista_tipoclientes) > 0) {
                                            foreach ($lista_tipoclientes as $indice => $valor) {
                                                echo '<td width="3%"><div align="center">% <input type="text" class="cajaGeneral" name="PORC_0_' . $indice . '" id="PORC_0_' . $indice . '" style="width:20px;" /></div></td>';
                                                echo '<td width="6%" valign="top"><div align="center">PRECIO ' . ($indice + 1) . '</div></td>';
                                                echo '<td width="1%"><div align="center">&nbsp;</div></td>';
                                            }
                                        }
                                        ?>

                                    </tr>					
                                    <?php
                                    if (count($lista) > 0) {
                                        foreach ($lista as $indice => $producto) {
                                            $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                                            ?>
                                            <tr class="<?php echo $class; ?>">
                                                <td><div align="center"><input type="hidden" name="producto[<?php echo $indice; ?>]" id="producto[<?php echo $indice; ?>]" value="<?php echo $producto[7]; ?>" /><?php echo $producto[0]; ?></div></td>
                                                <td><div align="center"><?php echo $producto[1]; ?></div></td>
                                                <td><div align="left"><?php echo $producto[2]; ?></div></td>
                                                <td><div align="center"><?php echo $producto[3]; ?></div></td>
                                                <td><input type="text" class="cajaGeneral cajaSoloLectura" readonly="readonly" style="width:50px;" name="PC<?php echo $producto[7]; ?>" id="PC<?php echo $producto[7]; ?>" value="<?php echo $producto[4]; ?>" /></div></td>
                                                <?php
                                                foreach ($producto[5] as $indicePorc => $porc) {
                                                    echo '<td align="center"><div align="center"><input type="text" class="cajaGeneral" name="PORC_' . $producto[7] . '_' . $indicePorc . '" id="PORC_' . $producto[7] . '_' . $indicePorc . '" value="' . $producto[5][$indicePorc] . '" style="width:20px;" /></div></td>';
                                                    echo '<td align="center"><div align="center"><input type="text" class="cajaGeneral" name="PREC_' . $producto[7] . '_' . $indicePorc . '" id="PREC_' . $producto[7] . '_' . $indicePorc . '" value="' . $producto[6][$indicePorc] . '" style="width:50px;" /></div></td>';
                                                    echo '<td>&nbsp;</td>';
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>

                                        <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                                            <tbody>
                                                <tr>
                                                    <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                                                </tr>
                                            </tbody>
                                        </table>	
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>
                     	</form>
                    
                   
                <?php } ?>
                </div>
            </div>
            <?php if (count($_POST) > 0) { ?>
                <div style="margin-top:20px; text-align: center">
                    <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
                    <a href="javascript:;" id="grabarProductoPrecio"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <?php echo $oculto; ?>
                </div>
            <?php } ?>
        </div>
    </body>
</html>
