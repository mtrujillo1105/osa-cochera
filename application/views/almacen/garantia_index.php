<!--<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.min.js?=<?=JS;?>"></script>-->
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/garantia.js?=<?=JS;?>"></script>
<div id="pagina">
    <div id="zonaContenido">
    <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
        <div id="frmBusqueda" >
            <?php echo $form_open;?>
                <table class="fuente8" width="98%" cellspacing=0 cellpadding="5" border=0>
                    <tr>
                        <td align='left' width="13%">Descripcion</td>
                        <td align='left'><?php echo $descripcion_garantia;?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            <?php echo $form_close;?>
        </div>
        <div class="acciones">
	        <div id="botonBusqueda">
	           <ul id="entregaCliente" class="lista_botones"><li id="entregacliente" >Entrega Cliente</li></ul>
	           <ul id="recepcionProveedor" class="lista_botones"><li id="recepcion">Recepcion Proveedor</li></ul>
	           <ul id="envioProveedor" class="lista_botones"><li id="envioproveedor">Envio Proveedor</li></ul>
	           <ul id="NuevoGarantia" class="lista_botones"><li id="nuevo">Nueva Garantia</li></ul>
	           <ul id="limpiarGarantia" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
	           <ul id="buscarGarantia" class="lista_botones"><li id="buscar">Buscar</li></ul> 
	           
	        </div>
	        
	        <div id="lineaResultado">
	            <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>
	                <tr>
	                    <td width="50%" align="left">N de garantias encontradas:&nbsp;<?php echo $registros;?> </td>
	                </tr>
	            </table>
	        </div>
 		</div>

<form id="frmgarantia" name="frmgarantia" method="post"
                  enctype="multipart/form-data" action="<?php echo $url_action;?>"
        >
     <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
  <div id="frmResultado">
    <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                   
                <tr class="cabeceraTabla">
                    <td width="5%">&nbsp;</td>
                        <td width="5%">ITEM</td>
                        <td width="15%">CLIENTE</td>
                        <td width="10%">PRODUCTO</td>
                        <td width="20%">DESCRIPCION</td>
                       
                        <td width="10%">FALLA</td>
                        <td width="10%">FECHA REGISTRO</td>
                        <td width="7%">ESTADO</td>
                       
                        <td width="4%">&nbsp;</td>
                        <td width="4%">&nbsp;</td>
                        <td width="4%">&nbsp;</td>
              </tr>
                    <?php
                    if(count($lista)>0){
                        foreach($lista as $indice=>$valor)
                        {
                            $class = $indice%2==0?'itemParTabla':'itemImparTabla';
                            ?>
                            <tr class="<?php echo $class;?>">
                            <td><div align="center"><?php echo $valor[0];?></div></td>
                            <td><div align="center"><?php echo $valor[1];?></div></td>
                            <td><div align="center"><?php echo $valor[2];?></div></td>
                            <td><div align="center"><?php echo $valor[3];?></div></td>
                           <td><div align="center"><?php echo $valor[4];?></div></td>
                            
                            <td><div align="center"><?php echo $valor[5];?></div></td>
                          
                            <td><div align="center" ><?php echo $valor[6];?></div></td>
                            <td  >
                               <?php
                                if($valor[7]== "Pendiente"){
                               ?>
                              <div  align="center" style="background-color:#FF6464;height:20px;" ><?php echo $valor[7];?></div>
                               
                               <?php
                               }
                               if($valor[7]== "Enviado"){
                               ?>  
                                 <div  align="center" style="background-color:orange;height:20px;" ><?php echo $valor[7];?></div>
                               <?php
                               }
                               if($valor[7]== "Recepcionado"){
                                 ?>  
                            <div  align="center" style="background-color:yellow;height:20px;" ><?php echo $valor[7];?></div>
                               <?php
                               }
                               if($valor[7]== "Solucionado"){
                               ?>     
                             <div  align="center" style="background-color:green;height:20px;" ><?php echo $valor[7];?></div>
                               <?php
                               }
                              
                                ?>
                                
                              </td>
                            <td><div align="center"><?php echo $valor[8];?></div></td>
                            <td><div align="center"><?php echo $valor[9];?></div></td>
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
    <p>
      <input type="hidden" id="iniciopagina" name="iniciopagina">
      <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
      <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
    </p>
    <p>
   <div class="acciones">
	        <div id="botonBusqueda">
			    <ul id="listaentregaCliente" class="lista_botones"><li id="entregacliente" > List Entrg Cliente</li></ul>
			    <ul id="listarecepcionProveedor" class="lista_botones"><li id="recepcion">List Recep Proveedor</li></ul>
			    <ul id="listaenvioProveedor" class="lista_botones"><li id="envioproveedor">List Envio Proveedor</li></ul>
			</div>
    </div>
    </p>
    <p>&nbsp;</p>
    <table width="303" border="0" a align="right">
      <tr>
        <td width="61" bgcolor="#FF6464">&nbsp;</td>
        <td width="226"><strong>Garantia Registrada</strong></td>
      </tr>
      <tr>
        <td bgcolor="orange">&nbsp;</td>
        <td><strong>Garantia enviada al Proveedor</strong></td>
      </tr>
      <tr>
        <td bgcolor="yellow">&nbsp;</td>
        <td><strong>Garantia recepcionada del Proveedor</strong></td>
      </tr>
      <tr>
        <td bgcolor="green">&nbsp;</td>
        <td><strong>Grantia Solucionada</strong></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </div>
            <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
  <p>
    </div>
   </p>
   <p>
  </div>			
     </div>
   </p>
  <p>&nbsp;</p>
</form>