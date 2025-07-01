<html>
<head>
   <title><?php echo TITULO;?></title>
   <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <script>     
        //var time_digita=0;
        var base_url='';
        var cantidad=0;
        $(document).ready(function(){
            base_url=$('#base_url').val();
            cantidad=$('#cantidad').val();
            $('#imgGuardarSerie').click(function(){
               $('#frmProductoSerie').submit();
            });
            
            $('#imgCancelarSerie').click(function(){
               window.close();
            });
            
             $('#txtSerie').keyup(function(e){
               var key=e.keyCode || e.which;
                if(key==13){
                    var serie=$(this).val();
                    if(serie=='')
                        return false;
                    
                    var n = document.getElementById('tabla_resultado').rows.length;
                    var total=$('input[id^="accion"][value!="e"]').length;
                    if(total>=cantidad){
                        $('#imgGuardarSerie').focus();
                        $(this).val('');
                        alert('Ya se ha ingresado todas las series');
                        return false;
                    }
                    
                        
                    var fila='';
                    fila+='<tr class="itemParTabla">';
                    fila+='<td align="center" width="30">'+n+'</td>';
                    fila+='<td align="left"><input type="text" name="serie['+n+']" id="serie['+n+']" value="'+serie+'" class="cajaMedia" readonly="readonly" /></td>';
                    fila+='<td align="center" width="30">';
                    fila+='<a href="javascript:;" class="remove" ><img src="'+base_url+'images/icono_desaprobar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Retirar de la Lista"/>';
                    fila+='<input type="hidden" value="n" name="accion['+n+']" id="accion['+n+']" />';
                    fila+='</td>';
                    fila+='</tr>';
                    $("#tabla_resultado").append(fila);
                    $(this).val('').focus();
                    
                    
                }/*else{
                    var time=new Date();
                    time_digita=time.getTime();
                }*/
            });
            
            $('a.remove').live('click',function(){
                $(this).next().val('e');
                $(this).parent().parent().fadeOut('fast');
            })
            
            $('#txtSerie').focus();
        });
        /*var ent=self.setInterval(function(){clock(base_url,cantidad)},500);
        function clock(base_url,cantidad){
            var time=new Date();
            var time_actual=time.getTime();
            
            if($('#txtSerie').val()!='' && time_digita>0 && time_actual-time_digita>1000){
                alert(time_actual+'-'+time_digita)
                var serie=$('#txtSerie').val();  
                var n = document.getElementById('tabla_resultado').rows.length;
                var total=$('input[id^="accion"][value!="e"]').length;
                if(total>=cantidad){
                    $('#imgGuardarSerie').focus();
                    $('#txtSerie').val('');
                    alert('Ya se ha ingresado todas las series');
                    return false;
                }

                var fila='';
                fila+='<tr class="itemParTabla">';
                fila+='<td align="center" width="30">'+n+'</td>';
                fila+='<td align="left"><input type="text" name="serie['+n+']" id="serie['+n+']" value="'+serie+'" class="cajaMedia" readonly="readonly" /></td>';
                fila+='<td align="center" width="30">';
                fila+='<a href="javascript:;" class="remove" ><img src="'+base_url+'images/icono_desaprobar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Retirar de la Lista"/>';
                fila+='<input type="hidden" value="n" name="accion['+n+']" id="accion['+n+']" />';
                fila+='</td>';
                fila+='</tr>';
                $("#tabla_resultado").append(fila);

                $('#txtSerie').val('').focus();
                time_digita=time_actual;
            }
                
            //window.clearInterval(ent);
        }*/
        
   </script>
</head>
<body>
<div align="center">  
   <?php echo $form_open;?>
    <div id="frmBusqueda" style="width:95%">
    <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>					
        <tr class="cabeceraTabla" height="25px">
            <td align="center" colspan="3"><?php echo $nombre_producto; ?></td>
        </tr>
       </table>
    </div>
    <br />
    <div id="frmResultado" style="width:95%; height:300px; overflow:auto;">
    <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" cellspacing="1" cellpadding="3" border="0" >
           <tr align="center" class="cabeceraTabla">
               <td align="left" colspan="3"> <input type="text" id="txtSerie2"  class="cajaMedia" name="txtSerie2" readonly="readonly" style="background-color:#5F5F5F" /></td>
            </tr>
           <?php
        for($i=0;$i<count($numero_serie);$i++){
            ?>
            <tr class="itemParTabla">
                <td align="center" width="30"><?php echo $i+1;?></td> 
                <td align="left"><input type="hidden" name="serie[<?php echo $i+1; ?>]" id="serie[<?php echo $i+1; ?>]" value="<?php echo $numero_serie[$i]; ?>" class="cajaMedia" readonly="readonly" /><?php echo strtoupper($numero_serie[$i]); ?>
                      <!--  <a href="javascript:;" class="remove" ><img src="<?php echo base_url(); ?>public/images/icons/icono_desaprobar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Retirar de la Lista"/></a> -->
                        <input type="hidden" value="n" name="accion[<?php echo $i+1; ?>]" id="accion[<?php echo $i+1; ?>]" />
                </td>
            </tr>
            <?php
        }
        ?>
         
    </table>
    </div>
    <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
        <!-- <a href="javascript:;" id="imgGuardarSerie"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a> -->
        <a href="javascript:;" id="imgCancelarSerie"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <?php echo $form_hidden;?>
    </div>
    <?php echo $form_close;?>
</body>
</html>
