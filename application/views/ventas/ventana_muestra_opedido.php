<html>
<head>
   <title><?php echo TITULO;?></title>
   <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <script>
        var base_url;
        var flagBS;
		
        $(document).ready(function(){
            base_url   = $("#base_url").val();

            $('#imgCancelarDocumento').click(function(){
              parent.$.fancybox.close();
            });
		
        });
        
	   function ver_detalle_documentoPedido(documento){
           if('<?php echo $tipo_oper; ?>'!='C'){
            url = base_url+"index.php/ventas/presupuesto/obtener_detalle_pedido/v/<?php echo $tipo_oper; ?>/"+documento;
		}else{
		    url = base_url+"index.php/ventas/presupuesto/obtener_detalle_pedido/c/<?php echo $tipo_oper; ?>/"+documento;
		}
		
            $("#tblDocumentoDetalle tr[class!='cabeceraTabla']").html('');    
            $('#tblDocumentoDetalle').hide();
            $('img#loading,').show();
            $.getJSON(url,function(data){
                      $('#tblDocumentoDetalle').show();
                      $('img#loading').hide();
                      $.each(data, function(i,item){
                            if(i%2==0){clase="itemParTabla";}else{clase="itemImparTabla";}

                            fila = '<tr class="'+clase+'">';
                            fila+= '<td><div align="left">'+item.PROD_CodigoInterno+'</div></td>';
                            fila+= '<td><div align="left">'+item.PROD_Nombre+'</div></td>';
                            fila+= '<td><div align="right">'+item.PRESDEC_Cantidad+' '+item.UNDMED_Simbolo+'</div></td>';
                            fila+= '<td ><div align="right">'+item.PRESDEC_Pu_ConIgv+'</div></td>';
                            fila+= '<td><div align="right">'+item.PRESDEC_Total+'</div></td>';
                            //fila+= '<td><div align="right">'+item.onclick+'</div></td>';
                            fila+= '<td><div align="center"><a href="javascript:;" onclick="seleccionar_documento_detalle('+item.onclick+')"><img src="'+base_url+'images/ir.png?=<?=IMG;?>" width="16" height="16" border="0" title="Seleccionar Detalle"></a></div></td>';
                            fila+= '</tr>';
                            $("#tblDocumentoDetalle").append(fila);
                      });
            });
        }
	   
	   function seleccionar_pedido(guia,serie,numero){
           parent.seleccionar_pedido(guia,serie,numero);
           parent.$.fancybox.close();
       }
	   
	   		
		
	   
   </script>
</head>
<body>
<div align="center">  
   <?php echo $form_open;?>
    <div id="tituloForm" class="header" style="width:95%; padding-top: 0px;">
        <ul class="lista_tipodoc">
			<li > <a href="<?=$base_url;?>index.php/ventas/presupuesto/ventana_muestra_presupuestoRecu/<?php echo $tipo_oper; ?>/<?php if($tipo_oper=='V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/<?php echo $almacen; ?>/P">PRESUPUESTO(RECURRENTE)</a></li>
			<li style="background-color: #FF3333;"><a href="<?=$base_url;?>index.php/ventas/presupuesto/ventana_muestra_Opedido/<?php echo $tipo_oper; ?>/<?php if($tipo_oper=='V') echo $cliente; else echo $proveedor; ?>/SELECT_HEADER/<?php echo $almacen; ?>/OP">ORDEN DE PEDIDO</a></li>
		
		</ul>
    </div>
    <div id="frmBusqueda" style="width:95%;">
    <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" cellspacing="1" cellpadding="3" border="0" >
           <tr>
                <td>Cliente *</td>
                <td valign="middle">
                     <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente?>" />
                     <input type="text" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" readonly="readonly" value="<?php echo $ruc_cliente;?>" onkeypress="return numbersonly(this,event,'.');" />
                     <input type="text" name="nombre_cliente" class="cajaGeneral cajaSoloLectura" id="nombre_cliente" size="40" maxlength="50" readonly="readonly" value="<?php echo $nombre_cliente;?>" />
                     <!--<a href="<?=$base_url;?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->
               	</td>
            </tr>
    </table>
    </div>
    <?php echo $form_hidden;?>
    <?php echo $form_close;?>
    <div id="frmResultado" style="width:95%; height: 150px; overflow: auto;">
    <table class="fuente8" width="100%" id="tblMovimientoSerie" align="center" cellspacing="1" cellpadding="3" border="0">
           <tr class="cabeceraTabla">
                <td colspan="8">
				<?php 
				if($comprobante=='P'){ echo 'PRESUPUESTOS RECURRENTES';}
				?>
				</td>
           </tr>
            <tr class="cabeceraTabla">
                <td width="10%">FECHA</td>
                <td width="6%">SERIE</td>
                <td width="10%">NUMERO</td>
                <td width="12%">NUM DOC</td>
                <td><?php if($tipo_oper=='V') echo 'CLIENTE'; else echo 'PROVEEDOR'; ?></td>
                <td width="10%">TOTAL</td>
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
                            <td><div align="center"><?php echo $valor[3];?></div></td>
                            <td><div align="left"><?php echo $valor[4];?></div></td>
                            <td><div align="right"><?php echo $valor[5];?></div></td>
                            <td><div align="center"><?php echo $valor[6];?></div></td>
							<td><div align="center"><?php echo $valor[7];?></div></td>
                    </tr>
                    <?php
                    }
            }
            else{
            ?>
                    <tr>
                              <td width="100%" class="" colspan="7">Aún No hay ning&uacute;n registro con dicha persona</td>
                    </tr>
            <?php
            }
            ?>
    </table>
    </div>
    <br/>
    <div id="frmResultado" style="width:95%; height: 150px; overflow: auto;">
        <img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>" style="display:none" />
        <table class="fuente8" width="100%" id="tblDocumentoDetalle" align="center" cellspacing="1" cellpadding="3" border="0" style="display:none">
               <tr class="cabeceraTabla">
                    <td colspan="7">DETALLES DE LA FACTURA</td>
               </tr>
                <tr class="cabeceraTabla">
                    <td width="10%">CODIGO</td>
                    <td>DESCRIPCION</td>
                    <td width="7%">CANT</td>
                    <td width="9%">PU C/IGV</td>
                    <td width="8%">IMPORTE</td>
                    <td width="4%">&nbsp;</td>
               </tr>
        </table>
    </div>
    <input type="hidden" name="almacen" id="almacen" value="<?php echo $almacen; ?>">
</body>
</html>
