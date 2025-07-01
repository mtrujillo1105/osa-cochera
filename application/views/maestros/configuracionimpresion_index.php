
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/maestros/configuracionimpresion.js?=<?=JS;?>"></script>

<div id="pagina">
	<div id="zonaContenido">
		<div align="center">
			 <div id="tituloForm" class="header"><?php echo $titulo_configuracion ?></div>
	<div class="acciones">
			 <div id="botonBusqueda">
                <ul id="configuracionimprecion_nueva" class="lista_botones"><li id="nuevo">Nuevo Configuracion Impresion</li></ul>
            </div>
            <div id="lineaResultado">
                <table class="fuente" width="100%" cellspacing=0 cellpadding=3 border="0">
                    <tr>
                        <td width="50%" align="left">N de documentos:&nbsp;<?php echo $registros; ?> </td>
                </table>
            </div>
	</div>
            <div id="cabeceraResultado" class="header">
                <?php echo $titulo_tabla; ?> </div>
            <div id="frmResultado">
                <table class="fuente8" width="40%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                    <tr class="cabeceraTabla">
                        <td width="6%">NRO</td>
                        <td width="8%">NOMBRE DE DOCUMENTO</td>
                        <td width="20%">EDITAR</td>
                    </tr>
                    <?php
                    $i = 1;
                    if (count($lista) > 0) {
                        foreach ($lista as $indice => $valor) {
                        	$class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                            ?>
                            <tr class="<?php echo $class; ?>">
                                <td><div align="center"><?php echo $valor[0]; ?></div></td>
                                <td><div align="center"><?php echo $valor[1]; ?></div></td>
                                <td><div align="center"><?php echo $valor[2]; ?></div></td>
                                
                            </tr>
                            <?php
                            $i++;
                        }
                    }   ?>
                        


                </table>
                
            </div>
	</div>
	</div>
</div>