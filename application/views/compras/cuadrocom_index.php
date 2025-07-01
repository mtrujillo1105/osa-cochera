<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>            
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/cuadrocom.js?=<?=JS;?>"></script>
<div id="pagina">
    <div id="zonaContenido">
                <div align="center">
                        <div id="tituloForm" class="header">Buscar CUADRO COMPARATIVO </div>
                        <div id="frmBusqueda">
                        <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $action;?>">
                            <table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
                                    <tr>
                                            <td width="16%">N. de Documento </td>
                                            <td width="68%"><input id="txtNumDoc" type="text" class="cajaPequena" NAME="txtNumDoc" maxlength="15" value="<?php echo $numdoc; ?>">
                                            <td width="5%">&nbsp;</td>
                                            <td width="5%">&nbsp;</td>
                                            <td width="6%" align="right"></td>
                                    </tr>
                                    <tr>
                                            <td>Observacion </td>
                                            <td><input id="txtNombre" name="txtNombre" type="text" class="cajaGrande" maxlength="45" value="<?php echo $nombre; ?>"></td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                    </tr>
                                     
                            </table>
                        </form>
                  </div>
                    <div id="botonBusqueda">
                           <ul id="imprimirCuadro" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
                           <ul id="nuevoCuadro" class="lista_botones"><li id="nuevo">Nuevo Cuadro Comparativo</li></ul>
                           <ul id="limpiarCuadro" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
                           <ul id="buscarCuadro" class="lista_botones"><li id="buscar">Buscar</li></ul>
                    </div>
                  <div id="lineaResultado">
                      <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border="0">
                            <tr>
                            <td width="50%" align="left">N de Cuadros encontrados:&nbsp;<?php echo $registros;?> </td>
                            <td width="50%" align="right">&nbsp;</td>
                      </table>
                  </div>
                        <div id="cabeceraResultado" class="header">
                                <?php echo $titulo_tabla; ?> </div>
                        <div id="frmResultado">
                        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                                        <tr class="cabeceraTabla">
                                                <td width="8%">ITEM</td>
                                                <td width="38%">OBSERVACION</td>
                                                <td width="13%">FECHA</td>
                                                <td width="19%">ESTADO</td>
                                                <td width="5%">&nbsp;</td>
                                                <td width="5%">&nbsp;</td>
                                                <td width="5%">&nbsp;</td>
                                        </tr>
                                        <?php
                                        $i=1;
                                        if(count($lista)>0){
                                        foreach($lista as $indice=>$valor){
                                                $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                                                ?>
                                                <tr class="<?php echo $class;?>">
                                                        <td><div align="center"><?php echo $valor[0];?></div></td>
                                                        <td><div align="left"><?php echo $valor[1];?></div></td>
                                                        <td><div align="left"><?php echo $valor[2];?></div></td>
                                                        <td><div align="center"><?php echo $valor[3];?></div></td>
                                                        <td><div align="center"><?php echo $valor[4];?></div></td>
                                                        <td><div align="center"><?php echo $valor[5];?></div></td>
                                                        <td><div align="center"><?php echo $valor[6];?></div></td>
                                                        
                                                </tr>
                                                <?php
                                                $i++;
                                                }
                                        }
                                        else{
                                        ?>
                                        <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                                                <tbody>
                                                        <tr>
                                                            <td width="100%" class="mensaje">No hay ning&uacute;n cuadro comparativo que cumpla con los criterios de b&uacute;squeda</td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                        <?php
                                        }
                                        ?>
                        </table>
                        <input type="hidden" id="iniciopagina" name="iniciopagina">
                        <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
                </div>
            <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
            <input type="text" style="visibility:hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
        </div>
    </div>
</div>