<html>
<head>
	<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
	<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>			
	<script type="text/javascript" src="<?=$base_url;?>public/js/maestros/terminal.js?=<?=JS;?>"></script>	
</head>
	<body>
		<div id="pagina">
			<div id="zonaContenido">
				<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo_tabla; ?></div>
                        <div id="frmResultado">
                        <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                                        <tr class="cabeceraTabla">
                                                <td width="5%">ITEM</td>
                                                <td width="15%">DIRECCION</td>
                                                <td width="15%">REFERENCIA</td>
                                                <td width="5%">ACCION</td>
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
				
				<div style="margin-top:20px; text-align: center">
					<input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
					<a href="#" onclick="atras_proyecto();"><img src="<?=$base_url;?>public/images/icons/botonatras.jpg?=<?=IMG;?>" width="85" height="22" border="1" onMouseOver="style.cursor=cursor"></a>
			  	</div>
		  </div>
		  </div>
		</div>
	</body>
</html>
