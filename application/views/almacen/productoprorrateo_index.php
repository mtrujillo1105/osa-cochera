<html>
	<head>
        <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
        <script language="javascript">
            var cursor;
            if (document.all) {
            // Está utilizando EXPLORER
            cursor='hand';
            } else {
            // Está utilizando MOZILLA/NETSCAPE
            cursor='pointer';
            }
            
            
            $(document).ready(function(){
                base_url  = $('#base_url').val();
                producto  = $('#producto').val();
                $("a#linkProrrateo").fancybox({
                        'width'          : 840,
                        'height'         : 540,
                        'autoScale'	 : false,
                        'transitionIn'   : 'none',
                        'transitionOut'  : 'none',
                        'showCloseButton': false,
                        'modal'          : false,
                        'type'	     : 'iframe'
                });
                
                $("a#linkVerProveedor").fancybox({
                        'width'          : 700,
                        'height'         : 550,
                        'autoScale'	 : false,
                        'transitionIn'   : 'none',
                        'transitionOut'  : 'none',
                        'showCloseButton': false,
                        'modal'          : true,
                        'type'           : 'iframe'
                }); 
                
                 $("#buscarCompra").click(function(){
                    $("#form_busqueda").submit();
                 });	
                 $("#limpiarCompra").click(function(){
                    url = base_url+"index.php/almacen/producto/prorratear_producto/"+producto;
                    location.href=url;
                  });
            });
            
            function prorratear_producto(lote){
                $('#linkProrrateo').attr('href', base_url+"index.php/almacen/lote/prorratear_producto/"+lote).click();
            
            }
            
            function seleccionar_proveedor(codigo,ruc,razon_social, empresa, persona, ctactesoles, ctactedolares, direccion){
                $("#proveedor").val(codigo);
                $("#ruc_proveedor").val(ruc);
                $("#nombre_proveedor").val(razon_social);
            }
            
        </script>		
	</head>
	<body>
		<br>
		<div id="pagina">
			<div id="zonaContenido">
			<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
				<div id="frmBusqueda" >
				<form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $action;?>">
					<table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>					
					    <tr>
						<td>Proveedor</td>
                                                <td>
                                                     <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor?>" />
                                                     <input type="text" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor;?>" onkeypress="return numbersonly(this,event,'.');" />
                                                     <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_proveedor;?>" />
                                                     <a href="<?=$base_url;?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                                </td>
						</tr>
						<tr>
						  <td>Fecha Inicio</td>
                                                  <td><input name="fechaIni" type="text" class="cajaGeneral" id="fechaIni" value="<?php echo $fechaIni;?>" size="10" maxlength="10" readonly="readonly" />
                                                      <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" id="Image1" onMouseOver="this.style.cursor='pointer'" title="Calendario">
                                                      <script type="text/javascript">
                                                            Calendar.setup({
                                                                inputField     :    "fechaIni",      // id del campo de texto
                                                                ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                                            });
                                                      </script>
                                                      Fecha Fin
                                                      <input name="fechaFin" type="text" class="cajaGeneral" id="fechaFin" value="<?php echo $fechaFin;?>" size="10" maxlength="10" readonly="readonly" />
                                                        <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" id="Image1" onMouseOver="this.style.cursor='pointer'" title="Calendario">
                                                        <script type="text/javascript">
                                                            Calendar.setup({
                                                                inputField     :    "fechaFin",      // id del campo de texto
                                                                ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                                button         :    "Calendario2"   // el id del botón que lanzará el calendario
                                                            });
                                                        </script>
                                                  </td>
						  </tr>
                                         </table>
				</form>
			  </div>
			 	<a href="<?=$base_url;?>index.php/almacen/producto/publicar_producto/" id="linkPublicar"></a>
                <style type="text/css">
                    #botonBusqueda{margin-right: 15%;}
                    #lineaResultado{margin-left:207px; }
                    #cabeceraResultado{margin-top: 60px;}
                </style>
             <div id="botonBusqueda">
                 <ul id="imprimirCompra" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
                 <ul id="limpiarCompra" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
                <ul id="buscarCompra" class="lista_botones"><li id="buscar">Buscar</li></ul>                                        
                </div>
			  <div id="lineaResultado">
			  <table  width="100%" cellspacing=0 cellpadding=3 border=0>
			  	<tr>
				<td width="50%" align="left">N de productos encontrados:&nbsp;<?php echo $registros;?> </td>
				
			  </table>
				</div>
				<div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
				<div id="frmResultado">
				<table class="fuente8" width="100%" cellspacing="1" cellpadding="3" border="0" ID="Table1">
					<!--<tr class="cabeceraTabla">
                        <td width="6%" rowspan="2">FECHA</td>
                        <td width="7%" rowspan="2">NUM DOC</td>
                        <td rowspan="2">PROVEEDOR</td>  
                        <td width="4%" rowspan="2">CANT</td>
                        <td width="6%" rowspan="2">PC</td>
                        <td width="43%" colspan="6">ULTIMO PRORRATEO</td>
                    </tr>-->
                    <tr class="cabeceraTabla">
                        <td width="5%">FECHA</td>
                        <td width="8%" rowspan="2">NUM DOC</td>
						<td width="4%">TIPO</td>
                        <td width="6%">CANT ADI</td>

                        <td width="36%" rowspan="2">PROVEEDOR</td>  
                        <td width="9%">VALOR</td>
                        
						<td width="10%">NUEVO PC</td>
                        <td width="10%" rowspan="2">PC</td>
                        <td width="30%" colspan="6">ULTIMO PRORRATEO</td>
					</tr>
						<?php
						if(count($lista)>0){
						foreach($lista as $indice=>$valor){
							$class = $indice%2==0?'itemParTabla':'itemImparTabla';
							?>
							<tr class="<?php echo $class;?>">
								<td><div align="center"><?php echo $valor[0];?></div></td>
                                                                <td><div align="center"><?php echo $valor[1];?></div></td>
                                                                <td><div align="left"><?php echo $valor[2];?></div></td>
                                                                <td><div align="center"><?php echo $valor[3];?></div></td>
                                                                <td><div align="right"><?php echo $valor[4];?></div></td>
                                                                <td><div align="center"><?php echo $valor[5];?></div></td>
                                                                <td><div align="left"><?php echo $valor[6];?></div></td>
                                                                <td><div align="center"><?php echo $valor[7];?></div></td>
                                                                <td><div align="right"><?php echo $valor[8];?></div></td>
                                                                <td><div align="right"><?php echo $valor[9];?></div></td>
                                                                <td><div align="center"><?php echo $valor[10];?></div></td>
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
                                <a id="linkProrrateo"></a>
				<?php echo $oculto; ?>
			</div>
		  </div>			
		</div>
	</body>
</html>
