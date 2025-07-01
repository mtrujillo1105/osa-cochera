<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>

<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/tipoproducto.js?=<?=JS;?>"></script>				
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
            <div id="frmBusqueda">
                <form id="form_busquedaTipoProd" name="form_busquedaTipoProd" method="post" action="<?php echo $action;?>">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="5" border="0">
                        <tr>
                            <td align='left' width="17%">Nombre del Tipo de <?php if($flagBS=='B') echo 'Artículo'; else echo 'Servicio';  ?></td>
                            <td align='left'><input id="txtTipoProd" name="txtTipoProd" type="text" class="cajaGrande" maxlength="45" value="<?php echo $txtTipoProd;?>"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </form>
            </div>
          
            <div class="acciones">	
	            <div id="botonBusqueda" >
	               <!-- <ul id="imprimirTipoProd" class="lista_botones"><li id="imprimir">Imprimir</li></ul>-->
	                <ul id="nuevoTipoProd" class="lista_botones"><li id="nuevo">Nuevo Tipo de <?php if($flagBS=='B') echo 'Artículo'; else echo 'Servicio';  ?></li></ul>
	                <ul id="limpiarTipoProd" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
	                <ul id="buscarTipoProd" class="lista_botones"><li id="buscar">Buscar</li></ul>   
	            </div>
	            <div id="lineaResultado">
	              <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
	                    <tr>
	                    <td width="50%" align="left">N de cargos encontrados:&nbsp;<?php echo $registros;?> </td>
	                    </tr>
	              </table>
	            </div>
            </div>
            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
            <div id="frmResultado">
                <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                    <tr class="cabeceraTabla">
                        <td width="5%">ITEM</td>
                        <td>NOMBRES DE TIPO DE PRODUCTOS</td>
                        <td width="20%">NÚMERO DE ATRIBUTOS</td>
                        <td width="5%">&nbsp;</td>
                        <td width="5%">&nbsp;</td>
                        <td width="6%">&nbsp;</td>
                    </tr>
                        <?php
                        if(count($lista)>0){
                        foreach($lista as $indice=>$valor){
                                $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                                ?>
                                <tr class="<?php echo $class;?>">
                                        <td><div align="center"><?php echo $valor[0];?></div></td>
                                        <td><div align="left"><?php echo $valor[1];?></div></td>
                                        <td><div align="center"><?php echo $valor[2];?></div></td>
                                        <td><div align="center"><?php echo $valor[3];?></div></td>
                                        <td><div align="center"><?php echo $valor[4];?></div></td>
                                        <td><div align="center"><?php echo $valor[5];?></div></td>
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
                <input type="hidden" id="iniciopagina" name="iniciopagina">
                <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
                <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>" />
                <input type="hidden" name="flagBS" id="flagBS" value="<?php echo $flagBS;?>" />
            </div>
            <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
        </div>
    </div>
</div>