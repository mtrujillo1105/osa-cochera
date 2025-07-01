<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>			
<script type="text/javascript" src="<?=$base_url;?>public/js/maestros/terminal.js?=<?=JS;?>"></script>
<div id="pagina">
    <div id="zonaContenido">
                <div align="center">
                        <div id="tituloForm" class="header">Buscar PROYECTO </div>
                        <div id="frmBusqueda">
                        <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $action;?>">
                            <table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
                                    <tr>
                                       <td width="16%">Nombre Proyecto </td>
                                       <td width="68%"><input id="nombres" type="text" class="cajaGrande" NAME="nombres" maxlength="15" value="<?php echo $nombres; ?>">
                                        <td width="5%">&nbsp;</td>
                                        <td width="5%">&nbsp;</td>
                                        <td width="6%" align="right"></td>
                                    </tr>
                                    
                                  
                            </table>
                        </form>
                  </div>
                 <div class="acciones">
                	<div id="botonBusqueda">
                    	<ul id="limpiarProyecto" class="lista_botones">
                        	<li id="limpiar">Limpiar</li>
                    	</ul>
                    	<ul id="buscarProyecto" class="lista_botones">
                        	<li id="buscar">Buscar</li>
                    	</ul>
                	</div>
                	<div id="lineaResultado">
                    	<table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
                        	<tr>
                            <td width="50%" align="left">N de guias de remision
                                encontrados:&nbsp;<?php echo $registros; ?> </td>
                        	</tr>
                    	</table>
                	</div>
            	</div>
                    
                 
                        <div id="cabeceraResultado" class="header">
                                <?php echo $titulo_tabla; ?> </div>
                        <div id="frmResultado">
                        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                                        <tr class="cabeceraTabla">
                                                <td width="8%">ITEM</td>
                                                <td width="8%">NOMBRE</td>
                                                <td width="15%">DESCRIPCION</td>
                                                <td width="13%">ENCARGADO</td>
                                                <td width="8%">ACCION</td>
                                                
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
                                                                <td width="100%" class="mensaje">No hay ninguna proyecto que cumpla con los criterios de búsqueda</td>
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