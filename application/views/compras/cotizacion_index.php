<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.min.js?=<?=JS;?>"></script>					
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/cotizacion.js?=<?=JS;?>"></script>	
<br>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
            <div id="frmBusqueda" >
                <form id="form_busquedaCargo" name="form_busquedaCargo" method="post" action="<?=$base_url;?>index.php/mantenimiento/buscar_cargos">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                        <tr>
                            <td align='left' width="10%">Fecha Inicial</td>
                            <td align='left' width="40%"><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
                            <td align='left' width="10%">Fecha Final</td>
                            <td align='left'><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
                        </tr>
                        <tr>
                            <td align='left'>N&uacute;mero</td>
                            <td align='left'><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
                            <td align='left'>Situaci&oacute;n</td>
                            <td align='left'>
                                <select name="conOcompra" id="conOcompra" class="comboPequeno">
                                    <option value="" selected>:: :: :: :: ::</option>
                                    <option value="">Pend.</option>
                                    <option value="">Atend.</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align='left'>Cliente</td>
                            <td align='left'><input id="txtCargo" name="txtCargo" type="text" class="cajaMedia" maxlength="45" value=""></td>
                            <td align='left'>&nbsp;</td>
                            <td align='left'>&nbsp;</td>
                        </tr>
                    </table>
                </form>
            </div>
            <div id="botonBusqueda">
                <ul id="imprimirCotizacion" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
                <ul id="nuevaCotizacion" class="lista_botones"><li id="nuevo">Nuevo Solicitud de Cotización</li></ul>
                <ul id="limpiarCotizacion" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
                <ul id="buscarCotizacion" class="lista_botones"><li id="buscar">Buscar</li></ul> 
            </div>
            
    <div id="lineaResultado">
        <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
            <tr>
                            <td width="50%" align="left">N de solicitudes de cotizaciones
                               encontrados:&nbsp;<?php echo $registros;?> </td>
                               <td width="50%" align="right">&nbsp;</td>
                        </tr>
                    </table>
        </div>
            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
            <div id="frmResultado">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                    <tr class="cabeceraTabla">
                            <td width="5%">ITEM</td>
     <td width="8%">FECHA</td>
    <td width="5%">N&Uacute;MERO</td>
                            <!--td width="10%">O.PEDIDO</td-->
                            <td width="52%">CLIENTE</td>
                            <td width="5%">O.COMPRA</td>
    <td width="5%">&nbsp;</td>
    <td width="5%">&nbsp;</td>
    <td width="5%">&nbsp;</td>
                    </tr>
                            <?php
                            if(count($lista)>0){
                            foreach($lista as $indice=>$valor){
                                    $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                                    ?>
                                    <tr class="<?php echo $class;?>">
                                            <td><div align="center"><?php echo $valor[0];?></div></td>
                                            <td><div align="center"><?php echo $valor[1];?></div></td>
                                            <td><div align="center"><?php echo $valor[2];?></div></td>
                                            <!--td><div align="center">< ?php echo $valor[3];?></div></td-->
                                            <td><div align="left"><?php echo $valor[4];?></div></td>
            <td><div align="center"><?php echo $valor[5];?></div></td>
            <td><div align="center"><?php echo $valor[6];?></div></td>
            <td><div align="center"><?php echo $valor[7];?></div></td>
            <td><div align="center"><?php echo $valor[8];?></div></td>
                                    </tr>
                                    <?php
                                    }
                            }
                            else{
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
            <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
            <input type="hidden" id="iniciopagina" name="iniciopagina">
            <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
            <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
    </div>
</div>			
</div>