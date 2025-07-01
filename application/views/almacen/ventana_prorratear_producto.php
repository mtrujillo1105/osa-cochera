<html>
<head>
   <title><?php echo TITULO;?></title>
   <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
   <link rel="stylesheet" href="<?=$base_url;?>public/css/calendario/calendar-win2k-2.css?=<?=CSS;?>" type="text/css" media="all" title="win2k-cold-1" />
   <script type="text/javascript" src="<?=$base_url;?>public/js/calendario/calendar.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/calendario/calendar-es.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/calendario/calendar-setup.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
   <link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <script>
        var base_url;
        var flagBS;
        $(document).ready(function(){
            base_url   = $("#base_url").val();
            lote       = $("#lote").val();
            producto   = $("#producto").val();
            
            $("a#verDocuRefe").fancybox({
                    'width'          : 670,
                    'height'         : 420,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': false,
                    'modal'          : false,
                    'type'	     : 'iframe'
            });
            
            $("#imgGuardarProducto").click(function(){
                $('img#loading').css('visibility','visible');
                url = base_url+"index.php/almacen/lote/prorratear_producto_grabar";
                dataString  = $('#frmProductoProrratear').serialize();
                $.post(url,dataString,function(data){
                        $('img#loading').css('visibility','hidden');
                        switch(data.result){
                            case 'ok':
                                    parent.location.href = base_url+"index.php/almacen/producto/prorratear_producto/"+producto;
                                    parent.$.fancybox.close();
                                    break;
                            case 'error': 
                                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                                    $('#'+data.campo).css('background-color', '#FFC1C1').focus();
                                    break;
                            case 'error2': 
                                    alert(data.msj);
                                    break;
                        }
                },'json');
            });
            
            $('#cboTipo').change(function(){
               $('#trValor,#trCantidadAdi, #trDocReferencia').hide();
               if($(this).val()!=''){
                   if($(this).val()=='1'){
                       $('#trValor').hide();
                       $('#trCantidadAdi').show();
                   }else{
                       $('#trValor').show();
                       $('#trCantidadAdi').hide();
                   }
                   
                   if($(this).val()=='1' || $(this).val()=='2')
                       $('#trDocReferencia').show();
                       
                }
            });
            
            $('#valor').blur(function(){
               if($(this).val()!=''){
                   var PCanterior=parseFloat($('#PCanterior').val());
                   var cantidad_actual=parseInt($('#cantidad_actual').val());
                   var valor=parseFloat($('#valor').val());
                   var PCnuevo=money_format((PCanterior*cantidad_actual-valor)/cantidad_actual);
                   $('#PCnuevo').val(PCnuevo);
               }
            });
            
            $('#cantidadAdi').blur(function(){
               if($(this).val()!=''){
                   var PCanterior=parseFloat($('#PCanterior').val());
                   var cantidad_actual=parseInt($('#cantidad_actual').val());
                   var cantidadAdi=parseFloat($('#cantidadAdi').val());
                   var PCnuevo=money_format((PCanterior*(cantidad_actual))/(cantidad_actual+cantidadAdi));
                   $('#PCnuevo').val(PCnuevo);
               }
            });

            $('#imgCancelarProducto').click(function(){
              parent.$.fancybox.close();
            });
            
            $('#recep_produ').click(function(){
               $('#verDocuRefe,#lblDocumentoDeta').hide();
               $('#guiarem_detalle, #comprobante_detalle')
               if($(this).is(':checked'))
                   $('#verDocuRefe').show();
            });
   
        });
        
        function seleccionar_documento_detalle(documento, documento_detalle){
            if(documento=='GUIAREM')
                $("#guiarem_detalle").val(documento_detalle);
            else
                $("#comprobante_detalle").val(documento_detalle);
            $('#lblDocumentoDeta').show();
        }
    
        function editar_prorrateo(prorrateo){
            location.href = base_url+"index.php/almacen/lote/prorratear_producto/"+lote+'/'+prorrateo;
        }
       
   </script>
</head>
<body>
<div align="center">  
   <?php echo $form_open;?>
    <div id="tituloForm" class="header" style="width:95%">PRORRATEAR PRECIO DE COMPRA</div>
    <div id="frmBusqueda" style="width:95%;">
    <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" cellspacing="1" cellpadding="3" border="0" >
           <tr>
              <td width="20%">Fecha</td>
              <td><input name="fecha" type="text" class="cajaGeneral" id="fecha" value="<?php echo $fecha;?>" size="10" maxlength="10" readonly="readonly" />
                  <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" id="Image1" onMouseOver="this.style.cursor='pointer'" title="Calendario" align="absmiddle">
                  <script type="text/javascript">
                        Calendar.setup({
                            inputField     :    "fecha",      // id del campo de texto
                            ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                            button         :    "Calendario2"   // el id del botón que lanzará el calendario
                        });
                  </script>
              </td>
              <td>PC Anterior</td>
              <td><input name="PCanterior" type="text" class="cajaGeneral cajaSoloLectura" id="PCanterior" value="<?php echo $PCanterior; ?>" size="10" readonly="readonly" /></td>
           </tr>
           <tr>
               <td>Tipo:</td>
               <td>
                   <?php echo $cboTipo ?>
               </td>
               <td>Cantidad Presente</td>
              <td><input name="cantidad_actual" type="text" class="cajaGeneral cajaSoloLectura" id="cantidad_actual" value="<?php echo $cantidad_actual; ?>" size="10" readonly="readonly" /></td>
           </tr>
           <tr id="trDocReferencia" <?php if($prorrateo=='' || ($tipo!='1' && $tipo!='2' ) ) echo 'style="display:none;"'; ?>>
               <td>Mercadería Recepcionada</td>
               <td><input type="checkbox" name="recep_produ" id="recep_produ" value="1" <?php if($recep_produ=='1') echo 'checked="checked"'; ?> />
                   <a href="<?php echo base_url() ?>index.php/almacen/guiarem/ventana_muestra_guiarem/C/<?php echo $proveedor; ?>" id="verDocuRefe" <?php if($recep_produ=='0') echo 'style="display:none;"'; ?>><img src="<?php echo base_url() ?>public/images/icons/referenciardoc.png?=<?=IMG;?>" class="imgBoton" /></a>
                   <input type="hidden" name="guiarem_detalle" id="guiarem_detalle" />
                   <input type="hidden" name="comprobante_detalle" id="comprobante_detalle" />
                   <label id="lblDocumentoDeta" <?php if($recep_produ=='0') echo 'style="display:none;"'; ?> ><img src="<?php echo base_url() ?>public/images/icons/icono_aprobar.png?=<?=IMG;?>" /> Ya seleccionado</label>
               </td>
           </tr>
           <tr id="trValor" <?php if($prorrateo=='' || $tipo=='1' ) echo 'style="display:none;"'; ?>>
               <td>Valor</td>
               <td colspan="3">
                   <input type="text" id="valor" name="valor" class="cajaPequena" value="<?php echo $valor; ?>" />
               </td>
           </tr>
           <tr id="trCantidadAdi" <?php if($prorrateo=='' || $tipo!='1' ) echo 'style="display:none;"'; ?>>
               <td>Cantidad Adicional</td>
               <td colspan="3">
                   <input type="text" id="cantidadAdi" name="cantidadAdi" class="cajaGeneral" style="width:40px;" value="<?php echo $cantidadAdi; ?>" />
               </td>
           </tr>
           <tr>
               <td>Observación</td>
               <td colspan="3">
                   <textarea id="observacion" name="observacion" class="cajaTextArea" style="width:100%" rows="3"><?php echo $observacion;?></textarea>
               </td>
           </tr>
           <tr>
               <td>Nuevo PC</td>
               <td colspan="3">
                   <input type="text" id="PCnuevo" name="PCnuevo" class="cajaPequena cajaSoloLectura" readonly="readonly" value="<?php echo $PCnuevo; ?>" />
               </td>
           </tr>
    </table>
    </div>
    <br />
    <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
        <img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
        <a href="javascript:;" id="imgGuardarProducto"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <a href="javascript:;" id="imgCancelarProducto"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <?php echo $form_hidden;?>
    </div>
    <?php echo $form_close;?>
    <div id="frmResultado" style="width:95%; height: 225px; overflow: auto;">
    <table class="fuente8" width="100%" id="tblMovimientoSerie" align="center" cellspacing="1" cellpadding="3" border="0">
           <tr class="cabeceraTabla">
                <td colspan="8">PRORRATEOS REALIZADOS</td>
           </tr>
            <tr class="cabeceraTabla">
                <td width="10%"><div align="center">PC ANT</div></td>
                <td width="10%"><div align="center">CANT</div></td>
                <td width="15%"><div align="center">FECHA</div></td>
                <td><div align="center">TIPO</div></td>
                <td width="12%"><div align="center">CANT ADI</div></td>
                <td width="10%"><div align="center">VALOR</div></td>
                <td width="12%"><div align="center">NUEVO PC</div></td>
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
                            <td><div align="left"><?php echo $valor[3];?></div></td>
                            <td><div align="center"><?php echo $valor[4];?></div></td>
                            <td><div align="right"><?php echo $valor[5];?></div></td>
                            <td><div align="right"><?php echo $valor[6];?></div></td>
                            <td><div align="center"><?php echo $valor[7];?></div></td>
                    </tr>
                    <?php
                    }
            }
            else{
            ?>
                    <tr>
                            <td width="100%" class="mensaje" colspan="7">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                    </tr>
            <?php
            }
            ?>
    </table>
    </div>
</body>
</html>
