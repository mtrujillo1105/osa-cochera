
<script type="text/javascript"	src="<?php echo base_url(); ?>public/js/maestros/configuracionimpresion.js?=<?=JS;?>"></script>

<style>


#containment-wrapper {
	width: 50%;
	height: 500px;
	border: 2px solid #ccc;
	padding: 10px;
}
#contenedor1{
    width: 100%;
    height: 297mm;
    border: 2px solid #ccc;
    padding: 1px;
    position: relative;
}
.divModificar{
    position: absolute;
    border: 2px solid black;
}

h3 {
	clear: left;
}
</style>


<script>
  
  $( function() {
   $( "#tabs" ).tabs();
    
    $( ".divModificar" ).resizable({
      start: function() {
        console.log('fun start 1');
        var elemento = $(this);
          var id=elemento.attr("id");
          posicionLetra=id.indexOf("_");  
          posicionReal = id.substring(posicionLetra+1);
          divSeleccionadoModificar(posicionReal);
          },
      stop: function() {
        console.log('fun stop 1');
      var elemento = $(this);
          var id=elemento.attr("id");
          posicionLetra=id.indexOf("_");  
          posicionReal = id.substring(posicionLetra+1);
          $('#campodo_width'+posicionReal).val($(this).width().toFixed(2));
          $('#campodo_height'+posicionReal).val($(this).height().toFixed(2));
        }
    });

    $(".divModificar").draggable( {
        start: function() {
          console.log('fun start');
            var elemento = $(this);
            var id=elemento.attr("id");
            posicionLetra=id.indexOf("_");  
            posicionReal = id.substring(posicionLetra+1);
            divSeleccionadoModificar(posicionReal);
             },
        stop: function() {
          console.log('fun stop');
            var elemento = $(this);
                var posicionTotal = elemento.position();
                var id=$(this).attr("id");
                posicionLetra=id.indexOf("_");  
                posicionReal = id.substring(posicionLetra+1);
                $('#campodo_posx'+posicionReal).val(posicionTotal.left.toFixed(2) );
                $('#campodo_posy'+posicionReal).val(posicionTotal.top.toFixed(2));
              }}
   );
    
  } );


  var posGeneralAnteriorx=<?php echo $posicionGeneralX;?>;
  var posGeneralAnteriory=<?php echo $posicionGeneralY;?>;
  function modificarPosicionGeneralTotal(valor){
  	 n = document.getElementById('table1').rows.length;
  	 if(n!=0){
  		 posGeneralx = $('#posicionGeneralX').val();
  		 posGeneraly = $('#posicionGeneralY').val();
  		 if(posGeneralx.trim()=='')
  			 posGeneralx=0;
  		 
  		 if(posGeneraly.trim()=='')
  			 posGeneraly=0;
  		 
  		 for(x=0;x<(n-1);x++){
  			 posicionYmodificar=0;
  			 posicionXmodificar=0;
  			 /**para Y**/
  			 if(valor==0){
  				 posy=$('#campodo_posy'+x).val();
  				 valorY=parseFloat(posy);
  				 valorYGeneral=parseFloat(posGeneraly);
  				 posicionYmodificar=(valorY+valorYGeneral)-parseFloat(posGeneralAnteriory);
  				 $('#campodo_posy'+x).val(posicionYmodificar.toFixed(2));
  				 $('#divModificacion_'+x).animate({top:posicionYmodificar.toFixed(2)});
  			 }
  			 
  			 /**para X**/
  			 if(valor==1){
  				 posx = $('#campodo_posx'+x).val();
  				 valorX=parseFloat(posx);
  				 valorXGeneral=parseFloat(posGeneralx);
  				 posicionXmodificar=(valorX+valorXGeneral)-parseFloat(posGeneralAnteriorx);
  				 $('#campodo_posx'+x).val(posicionXmodificar.toFixed(2));
  				 $('#divModificacion_'+x).animate({left:posicionXmodificar.toFixed(2)});
  			 }
  		 } 
  		 posGeneralAnteriorx=posGeneralx;
  		 posGeneralAnteriory=posGeneraly;
  	 }
  	
  }

  	function obtenerVariables(){
  		n = document.getElementById('table1').rows.length;
		if(n!=0){
			fila='';
			 for(x=0;x<(n-1);x++){
				 valorGrupo=$("#grupo"+x).val();
				 if(valorGrupo.trim()!="" && valorGrupo.trim()!="0"){
					 fila+='<label id="labelVariableGrupo_'+x+'"> '+valorGrupo+'-</label>';
				 }
					 
				valorVariable=$("#variable"+x).val();
				fila+='<input type="hidden" id="variable_'+x+' value="'+valorVariable+'" >';
				fila+='<label id="labelVariable_'+x+'"> '+valorVariable+'</label>';
			 }
			 $("#idListaVariables").html(fila);
  		}
  			
	}

		function verificarSentenciaVariable(posicion){
			sentencia=$("#idSentencia_"+posicion).val();
			
			if(sentencia.trim()!=""){
				n = document.getElementById('table1').rows.length;
				variablePhp=$("#idVCodigoRelacionSentencia_"+posicion).val();
				valorDemo=$("#idVDemo_"+posicion).val();

				var sentencia = sentencia.replace(variablePhp,valorDemo);
				
				alert(sentencia);

				
				
				url = "<?=$base_url;?>index.php/maestros/configuracionimpresion/verificarSentenciaVariable";
				$.ajax({
		            type: "POST",
		            url: url,
		            data: {sentenciaReal:sentencia},
		            dataType: 'json',
		            async: false,
		            beforeSend: function (data) {
		            },
		            error: function (data) {
		                alert('No se puedo completar la operación - Revise los campos ingresados.');
		                $("#variablesBd_"+posicion).html("");
		            },
		            success: function (data) {
			            datos="";
		            	$.each(data, function (i, item) {
							variable=item.variableReal;
							//alert(variable);
							/**veriuficamos si las variables de la sentencia se encuentra  en las variables del documento**/
							if(n!=0){
								 for(x=0;x<(n-1);x++){
									valorVariable=$("#variable"+x).val();
									if(valorVariable==variable){
										$("#labelVariable_"+x).css('color', 'red');
										break;
									}	
								 }
					  		}
							/**fin de verificacion**/
							datos+="  "+variable;
			           });
		            $("#variablesBd_"+posicion).html(datos);
		            }  

		        });
				
			}else{
				alert("No se ingreso ninguna sentencia de verificacion");
				$("#variablesBd_"+posicion).html("");
			}
		}

		function agregarSentenciaSecundaria(){
			n = document.getElementById('detallesSentenciasSecundarias').rows.length;
			j=n+1;
			fila='<tr id="idTr_'+j+'">';
			fila += '<td>';
			fila += '<label>ID sentencia:</label>';
			fila += '<input type="text" id="idVCodigoRelacionSentencia_'+j+'" name="vCodigoRelacionSentencia['+j+']" value="" >';
			fila += '<label>ID Relacion:</label>';
			fila += '<input type="text" id="idCodigoRelacionSentencia_'+j+'" name="codigoRelacionSentencia['+j+']" value="" >';
			fila += '<label>valor demo:</label>';
			fila += '<input type="text" id="idVDemo_'+j+'" name="VDemo['+j+']" value="" >';
			fila += '<label>Grupo:</label>';
			fila += '<input type="text" id="sentenciaGrupo_'+j+'" name="sentenciaGrupo['+j+']" value="" ><br>';
			
			
			fila+='<textarea id="idSentencia_'+j+'" name="sentencia['+j+']" cols="50" rows="10"></textarea>';
			fila+='<br><a href="javascript:void(0);" onclick="verificarSentenciaVariable('+j+')">Verificar</a>';
			fila+='<input type="hidden" id="idTipoSentencia_'+j+'" name="tipoSentencia['+j+']" value="2" >';
			fila+='<div id="variablesBd_'+j+'"></div>';
			fila += '</td>';
			fila += '</tr>';
			$("#detallesSentenciasSecundarias").append(fila);




		} 

		
</script>
<script type="text/javascript">
</script>

<form id="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>"   enctype='multipart/form-data'>
<div id="pagina">
<div id="tabs" style=" width: 930px;  margin: auto;">
  <ul>
    <li><a href="#zonaContenidoConfiguracion">CONFIGURACION</a></li>
    <li><a href="#zonaContenidoSentencia"  onclick="obtenerVariables()">CONFIGURACION SENTENCIAS</a></li>
  </ul>
  <div id="zonaContenidoSentencia">
    <div >
    <h3>Variables Documento</h3>
    <div id="idListaVariables"></div>
    ------------------------------------
    <br>
    <div id="idZonaSenteciaGeneral">
    <h3>Sentencia Principal</h3>
    <label>ID sentencia:</label>
    <input type="text" id="idVCodigoRelacionSentencia_0" name="vCodigoRelacionSentencia[0]" value="$CodigoPrincipal" readonly="readonly">
    
    <label>ID Relacion:</label>
    <input type="text" id="idCodigoRelacionSentencia_0" name="codigoRelacionSentencia[0]" value="" readonly="readonly">
    
    <label>valor demo:</label>
    <input type="text" id="idVDemo_0" name="VDemo[0]" value="" >
    
    <br>
    <textarea id="idSentencia_0" name="sentencia[0]" cols="50" rows="10"><?php  echo (isset($sentenciaPrincipal))?$sentenciaPrincipal:'';    ?></textarea>
    <br>
    <a href="javascript:void(0);" onclick="verificarSentenciaVariable(0)">Verificar</a>
    <br>
    <input type="hidden" id="idTipoSentencia_0" name="tipoSentencia[0]" value="1" >
    <input type="hidden" id="sentenciaGrupo_0" name="sentenciaGrupo[0]" value="" ><br>
      
    
    
    <!-- mostramos los campos relacionados segun la bd -->
    <div id="variablesBd_0"></div>
    
    </div>
    <div id="idZonaSenteciaSecundaria">
    <h3>Sentencias Secundarias</h3>
    <a href="javascript:void(0);" onclick="agregarSentenciaSecundaria()">Agregar</a>
    <table id="detallesSentenciasSecundarias">
      <?php if(isset($listaSentencia) && count($listaSentencia)>0){
          foreach ($listaSentencia as $indice=>$valor){
            $tipo=$valor->tipo;
            $codigoRelacion=$valor->codigoRelacion;
            $variableRelacion=$valor->variableRelacion;
            $sentencia=$valor->sentencia;
            $sentenciaGrupo=$valor->sentenciaGrupo;
            $j=$indice+1;
        
        ?>
        
      <tr id="idTr_<?php echo $j;  ?>">
      <td>
      <label>ID sentencia:</label>
      <input type="text" id="idVCodigoRelacionSentencia_<?php echo $j;  ?>" name="vCodigoRelacionSentencia[<?php echo $j;  ?>]" value="<?php echo $variableRelacion;  ?>" >
      <label>ID Relacion:</label>
      <input type="text" id="idCodigoRelacionSentencia_<?php echo $j;  ?>" name="codigoRelacionSentencia[<?php echo $j;  ?>]" value="<?php echo $codigoRelacion;  ?>" >
      <label>valor demo:</label>
      <input type="text" id="idVDemo_<?php echo $j;  ?>" name="VDemo[<?php echo $j;  ?>]" value="" ><br>

      <label>Grupo:</label>
      <input type="text" id="sentenciaGrupo_<?php echo $j;  ?>" name="sentenciaGrupo[<?php echo $j;  ?>]" value="<?php echo $sentenciaGrupo; ?>" ><br>
      
      <textarea id="idSentencia_<?php echo $j;  ?>" name="sentencia[<?php echo $j;  ?>]" cols="50" rows="10"><?php echo $sentencia;  ?></textarea>
      <br><a href="javascript:void(0);" onclick="verificarSentenciaVariable(<?php echo $j;  ?>)">Verificar</a>
      <input type="hidden" id="idTipoSentencia_<?php echo $j;  ?>" name="tipoSentencia[<?php echo $j;  ?>]" value="2" >
      <div id="variablesBd_<?php echo $j;  ?>"></div>
      </td>
      </tr>
        
      <?php }} ?>
    </table>
    </div>
    </div>  
  </div>
	<div id="zonaContenidoConfiguracion" style="width: none;height:none">
			<div id="tituloForm" >CONFIGURACION DOCUMENTO GENERAL</div>
		
				<input type="text" id="codigoCompConfDoc" name="codigoCompConfDoc" value="<?php echo $codigoCompConfDoc;?>" />
        <input type="text" id="companiadato" name="companiadato" value="<?php echo $companiadato;?>" />
				<table border="0" id="idGeneral" >
				 <tr>
				 <td>Imagen Documento:</td>
				 <td>
				 <input type="file" id="files" name="files[]" />
				 </td>
				 </tr>
				 
				 <tr>
				 <td>Posicion General Y:</td>
				 <td>
				 <input type="text" id="posicionGeneralY" name="posicionGeneralY" maxlength="3" size="5" 
				 onblur="modificarPosicionGeneralTotal(0)" onkeypress="return numbersonly(this,event,'.');"
				 value="<?php echo $posicionGeneralX;?>"/>
				 </td>
				 </tr>
				 
				  <tr>
				 <td>Posicion General X:</td>
				 <td>
				 <input type="text" id="posicionGeneralX" name="posicionGeneralX" maxlength="3" size="5" 
				 onblur="modificarPosicionGeneralTotal(1)" onkeypress="return numbersonly(this,event,'.');"
				 value="<?php echo $posicionGeneralY;?>" />
				 </td>
				 </tr>
				</table>
			
		
			<div id="tituloForm" ><?php echo $titulo_configuracioneditar ?></div>
			<table  width="100%" cellspacing="0" cellpadding="" border="1" id="table1">
                        <tr class="cabeceraTabla">
                            <td width="5%"><div align="center">Documento</div></td>
                            <td width="5%"><div align="center">Nombre</div></td>
                            <td width="5%"><div align="center">Width</div></td>
                            <td width="5%"><div align="center">Height</div></td>
                            <td width="5%"><div align="center">Posicion X</div></td>
                            <td width="5%"><div align="center">Posicion Y</div></td>
                            <td width="5%"><div align="center">Tama&ntilde;o de Letra</div></td>
                            <td width="5%"><div align="center">Tipo de Letra</div></td>
                            <td width="5%"><div align="center">Alineamiento</div></td>
                            <td width="5%"><div align="center">Variable</div></td>
                            <td width="5%"><div align="center">Grupo</div></td>
                            <td width="5%"><div align="center">Ocultar</div></td>
                        </tr>
                   
                    
                    <?php
                    
                    if (count($lista) > 0) {
                        foreach ($lista as $indice => $valor) {

                            if ($valor->COMPADOCUITEM_Codigo) {
                                $compadocumenitem_codigo=$valor->COMPADOCUITEM_Codigo;
                            }
                            else{
                                $compadocumenitem_codigo="";
                            }
                            
                        	$compacofidocum = $valor->COMPCONFIDOCP_Codigo;
                        	$documento_codigo = $valor->DOCUP_Codigo;
                        	$tipo_docu = $valor ->DOCUC_Descripcion;
                        	$item_nom = $valor ->ITEM_Nombre;
                        	$docuitem_wid = $valor ->DOCUITEM_Width;
                        	$docuitem_hei = $valor ->DOCUITEM_Height;
                        	$docuitem_posix = $valor ->DOCUITEM_PosicionX;
                        	$docuitem_posiy = $valor ->DOCUITEM_PosicionY;
                        	$docuitem_tamletra = $valor ->DOCUITEM_TamanioLetra;
                        	$docuitem_tipoletra = $valor ->DOCUITEM_TipoLetra;
                        	$variable = $valor ->DOCUITEM_Variable;
                        	$perteneceGrupo= $valor->COMPADOCUITEM_VGrupo;
                        	$alineamiento= $valor->COMPADOCUITEM_Alineamiento;
                        	$activacion=$valor->COMPADOCUITEM_Activacion;
                        	
                            $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                            ?>
                            
                            <input type="hidden" name="compaconfidocu[<?php echo $indice; ?>]" id="compaconfidocu[<?php echo $indice; ?>]" value="<?php echo $documento_codigo ?>" />
                            
                            <input type="text" name="compacofidocum[<?php echo $indice; ?>]" id="compacofidocum[<?php echo $indice; ?>]" value="<?php echo $compacofidocum ?>" />

                            <input type="hidden" name="documentoid[<?php echo $indice; ?>]" id="documentoid[<?php echo $indice; ?>]" value="<?php echo $documento_codigo ?>" />

                            <input type="hidden" name="tipo_docu[<?php echo $indice; ?>]" id="tipo_docu[<?php echo $indice; ?>]" value="<?php echo $tipo_docu ?>" />
                            <input type="hidden" name="item_nom[<?php echo $indice; ?>]" id="item_nom[<?php echo $indice; ?>]" value="<?php echo $item_nom ?>" />
                            <input type="text" name="compadocumenitem_codigo[<?php echo $indice; ?>]" id="compadocumenitem_codigo[<?php echo $indice; ?>]" value="<?php echo $compadocumenitem_codigo ?>" />

                            <tr  class="<?php echo $class; ?>">
                                <td> <label ><?php echo $tipo_docu; ?></label></td>
                                <td> <label ><?php echo $item_nom; ?></label></td>
                                <td> <input id="campodo_width<?php echo $indice; ?>" name="campodo_width[<?php echo $indice; ?>]" type="text" maxlength="3" size="1" value="<?php echo $docuitem_wid; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" onblur="modificarDivConfiguracionDocumento(<?php echo $indice; ?>)" />px</td>
                                <td> <input id="campodo_height<?php echo $indice; ?>"  name="campodo_height[<?php echo $indice; ?>]" type="text" maxlength="3" size="1" value="<?php echo $docuitem_hei; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" onblur="modificarDivConfiguracionDocumento(<?php echo $indice; ?>)" />px</td>
                                <td> <input id="campodo_posx<?php echo $indice; ?>"  name="campodo_posx[<?php echo $indice; ?>]" type="text" maxlength="3" size="1" value="<?php echo $docuitem_posix; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" onblur="modificarDivConfiguracionDocumento(<?php echo $indice; ?>)" />px</td>
                                <td> <input id="campodo_posy<?php echo $indice; ?>"  name="campodo_posy[<?php echo $indice; ?>]" type="text" maxlength="3" size="1" value="<?php echo $docuitem_posiy; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" onblur="modificarDivConfiguracionDocumento(<?php echo $indice; ?>)" />px</td>
                                <td> <input id="campodo_tamletra<?php echo $indice; ?>"  name="campodo_tamletra[<?php echo $indice; ?>]" type="text" maxlength="3" size="1" value="<?php echo $docuitem_tamletra; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" onblur="modificarDivConfiguracionDocumento(<?php echo $indice; ?>)" />px</td>
                                <td> <input id="campodo_tipoletra<?php echo $indice; ?>"  name="campodo_tipoletra[<?php echo $indice; ?>]" type="text" maxlength="10" size="7" value="<?php echo $docuitem_tipoletra; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" onblur="modificarDivConfiguracionDocumento(<?php echo $indice; ?>)" /></td>
                                
                                <td> 
                                <!--  <input id="alineamiento<?php echo $indice; ?>"  
                                Name="alineamiento[<?php echo $indice; ?>]" 
                                type="text" maxlength="10" size="7"
                                 value="<?php echo $alineamiento; ?>" 
                                 onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" 
                                  />-->
                                  <select Name="alineamiento[<?php echo $indice; ?>]" id="alineamiento<?php echo $indice; ?>"  
                                  onclick="divSeleccionadoModificar(<?php echo $indice; ?>)"
                                  >
                                  <?php foreach ($listadoAlineamiento as $valorAli){ ?>
                                  		<option value="<?php echo $valorAli->valor;?>"  <?php echo ($alineamiento==$valorAli->valor)?'selected':''; ?>><?php echo $valorAli->nombre;?></option> 
                                  <?php } ?>
                                  
                                  </select>
                                  
                                  
                                  
                                  </td>
                                
                                <td> <input id="variable<?php echo $indice; ?>"  name="variable[<?php echo $indice; ?>]" type="text" maxlength="10" size="7" value="<?php echo $variable; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)"  /></td>
                                <td> <input id="grupo<?php echo $indice; ?>"  name="grupo[<?php echo $indice; ?>]" type="text" maxlength="10" size="7" value="<?php echo $perteneceGrupo; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)"  /></td>
                               
                                <td>
     <!--                             <input id="activacion<?php echo $indice; ?>"  name="activacion[<?php echo $indice; ?>]" 
                                 type="text" maxlength="10" size="7"  
                                 value="<?php echo $activacion; ?>" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)"  />-->
                                 
                                 <input id="activacion<?php echo $indice; ?>"  name="activacion[<?php echo $indice; ?>]"
                                 type="checkbox" onclick="divSeleccionadoModificar(<?php echo $indice; ?>)" value="1"  <?php echo ($activacion==1)?'checked ':''; ?>>
                                 
                                 
                                 </td>
                                
                            </tr>
                            <?php
                            
                        }
                    }   ?>
                        


                </table>
                
	    <input type="hidden" id="imagenAnteriorNombre" name="imagenAnteriorNombre" value="<?php echo $imagenDocumento?>">
	    <div id="contenedor1"  style="background: url(<?php echo base_url(); ?>public/images/icons/documentos/<?php echo $imagenDocumento?>) no-repeat; background-size: 210mm 297mm;">
	        
	        <?php
	                    
	                    if (count($lista) > 0) {
	                        foreach ($lista as $indice => $valor) {
	                        	$item_nom = $valor ->ITEM_Nombre;
	                        	$docuitem_wid = $valor ->DOCUITEM_Width;
	                        	$docuitem_hei = $valor ->DOCUITEM_Height;
	                        	$docuitem_posix = $valor ->DOCUITEM_PosicionX;
	                        	$docuitem_posiy = $valor ->DOCUITEM_PosicionY;
	                        	$docuitem_tamletra = $valor ->DOCUITEM_TamanioLetra;
	                        	$docuitem_tipoletra = $valor ->DOCUITEM_TipoLetra;
	                        	
	                        
	        ?>
	        <div id="divModificacion_<?php echo $indice;?>" class="divModificar" 
	        onclick="divSeleccionadoModificar(<?php echo $indice; ?>)"
	        
	        		style="left: <?php echo $docuitem_posix;?>px;
				    top: <?php echo $docuitem_posiy;?>px;
				    height: <?php echo $docuitem_hei;?>px;
				    width: <?php echo $docuitem_wid;?>px;
				    font-size: <?php echo $docuitem_tamletra;?>px;">
	    		<?php echo $item_nom;?>
	    	</div>
	          <?php
	                        }
	                     }
	          ?>
	        
	    </div>
	
	
		<div id="botonBusqueda2" style="padding-top:20px;">
		       <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
		         <a href="javascript:;" id="grabarConfiguracionImpre"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
		         <a href="javascript:;" id="cancelarConfiguracionImpre"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
		                  
		         
		</div>
	
	</div>
				


</div>
</div>
</form>
<script>
              function archivo(evt) {
                  var files = evt.target.files; // FileList object
                  // Obtenemos la imagen del campo "file".
                  for (var i = 0, f; f = files[i]; i++) {
                    //Solo admitimos imágenes.
                    if (!f.type.match('image.*')) {
                        continue;
                    }
                    var reader = new FileReader();
                    reader.onload = (function(theFile) {
                        return function(e) {
                          // Insertamos la imagen
                        document.getElementById("contenedor1").style.background = "url('"+e.target.result+"') no-repeat";
                        document.getElementById("contenedor1").style.backgroundSize  = "210mm 297mm";
                        };
                    })(f);
             
                    reader.readAsDataURL(f);
                  }
              }
             
              document.getElementById('files').addEventListener('change', archivo, false);
      </script>

