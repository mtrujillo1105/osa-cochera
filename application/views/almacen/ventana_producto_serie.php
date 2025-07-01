<html>
    <head>
        <title><?php echo TITULO; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=base_url()?>public/js/upload.js?=<?=JS;?>"></script>
        <script>    
            var base_url='';
            var cantidad=0;
            var tipoOperacion=0;
            $(document).ready(function(){
                base_url=$('#base_url').val();
                cantidad=$('#cantidad').val();
				tipoOperacion=$('#tipo_oper').val();


				/**verificamos si es tipo venta**/
				if(tipoOperacion!=null && tipoOperacion=='V'){
					/***ejecutamos el metodo que nos trae  los productos sin seleccionar**/
					listaSeriesNoseleccionadas('');					
				}	
				/**fin de verificacion**/
                
                $('#imgGuardarSerie').click(function(){
					/**verificamos que esten ingresadas todas las opciones***/	
					var n = document.getElementById('tabla_resultado').rows.length;
                    var total=$('input[id^="accion"][value!="e"]').length;
                    var cantidad=<?php echo $candidadTotalIngresar; ?>;
                    if(total>=cantidad){
						/**verificamos que no exista ninguno en blanco**/
						isGrabar=true;
                    	for(x=1;x<=n;x++){
                            valor= "serie["+x+"]"; 
                            var  valor_serie = document.getElementById(valor).value ;
                            valor= "accion["+x+"]"; 
                            var  valor_accion = document.getElementById(valor).value ;
                            if(valor_accion!='e')
                            {
                                if(valor_serie.trim()==''){
                                	isGrabar=false;
                                	break;
                                }
                            }
                        }
                        if(isGrabar==true){
                            url="<?php echo $actionForm; ?>";
                            dataString=$('#formularioProductoSerie').serialize();
                            $.post(url,dataString,function(data){
								if(data==1){
									
									almacenSeleccionado=$("#almacen").val();
									isSeleccionarAlmacen=$("#isSeleccionarAlmacen").val();
									if(isSeleccionarAlmacen==1){
										window.opener.cambiarAlmacenProductoCodigo(almacenSeleccionado); 
									}
									window.close();
								}else{
									alert("Consulte con el administrador, ocurrio un error.");
								}
                            });

                       	}else{
                        	alert("Ingresar serie que se encuentra en blanco");    
                        }
                    }else{
                        alert("debe ingresar todas las series.")
                    }    

                });
            
                $('#imgCancelarSerie').click(function(){
                    window.close();
                });

                $('#imgAgregarSerie').click(function(e){
                	agregarSerie();
                });

                $('#limpiarBusquedaSeriesS').click(function(e){
                	$('#txtSerieSeleccionar').val('');	
                	listaSeriesNoseleccionadas('');
                	$('#txtSerieSeleccionar').focus();	
                });
                

                $('#txtSerie').keyup(function(e){
                    var key=e.keyCode || e.which;
                    if(key==13){
                    	var serie=$(this).val();
                    	agregarSeriesTabla(serie);
                    }
                });
            
                $('a.remove').live('click',function(n){
                    $(this).next().val('e');
                    $(this).parent().parent().fadeOut('fast');
                	tipoOperacion=$('#tipo_oper').val();
                	posicion=$(this).attr('id');
                	removerListadoDeSeries(tipoOperacion,posicion);
                })
            
                if(tipoOperacion!=null && tipoOperacion=='V'){
                	$('#txtSerieSeleccionar').focus();				
				}else{
                	$('#txtSerie').focus();
				}
				
				/**VENTAS SELECCION DE SERIES**/
				 $('#txtSerieSeleccionar').keyup(function(e){
                    var key=e.keyCode || e.which;
                    if(key==13){
                    	 /**verificamos si se encuentra dentro del listado y se agrega automaticamente**/

    					isExisteLista=false;
    					valorBusqueda=$(this).val();
    					if(valorBusqueda!=null && valorBusqueda.trim()!=''){
    						var n = document.getElementById('idDetalleSerieriesBD').rows.length;
    						if(n!=null && n!=0){
    							for(x=1;x<=n;x++){
    	                            valor= "serieS["+x+"]"; 
    	                            var  valor_serie = document.getElementById(valor).value ;
    								if(valor_serie==valorBusqueda){
    									agregarSerieVenta(x);
    									isExisteLista=true;
    									$('#txtSerieSeleccionar').val('');
    									break;
    								}
                                    /*else{
                                        isExisteLista=true;
                                      $('#txtSerieSeleccionar').val('');
                                        break;  
                                    }*/

                                    
    	                        }
    						}
    						
    	                    /**fin de verificacion**/
    						/**sino existe buscamos en la bd**/
    						if(!isExisteLista){
    							listaSeriesNoseleccionadas('');
                                $('#txtSerieSeleccionar').val('');
    						}
    	                    /**finalizamos busquedqa**/
                    }
                    }      
                });
	                
                $("#txtSerieSeleccionar").blur(function(){
                    /**verificamos si se encuentra dentro del listado y se agrega automaticamente**/
					isExisteLista=false;
					valorBusqueda=$(this).val();
					if(valorBusqueda!=null && valorBusqueda.trim()!=''){
						var n = document.getElementById('idDetalleSerieriesBD').rows.length;
						if(n!=null && n!=0){
							for(x=1;x<=n;x++){
	                            valor= "serieS["+x+"]"; 
	                            var  valor_serie = document.getElementById(valor).value ;
								if(valor_serie==valorBusqueda){
									agregarSerieVenta(x);
									isExisteLista=true;
									$('#txtSerieSeleccionar').val('');	
									break;
								} 
	                        }
						}
						
	                    /**fin de verificacion**/
						/**sino existe buscamos en la bd**/
						if(!isExisteLista){
							listaSeriesNoseleccionadas('');
                            $('#txtSerieSeleccionar').val('');
						}
	                    /**finalizamos busquedqa**/
					}
                    
                });


                $("#buttonSubirExcel").on('click', function() {
                    subirArchivos();
                });

            });
                           
			function agregarSerie(){
				var serie=$('#txtSerie').val();
				agregarSeriesTabla(serie);
             }

            function agregarSeriesTabla(serie){
                     if(serie.trim()==''){
                         alert("Ingrese un numero de serie correcto.");
                         $('#txtSerie').val('');
                         $('#txtSerie').focus();
                         return false;
                     }
                     var n = document.getElementById('tabla_resultado').rows.length;
                     var total=$('input[id^="accion"][value!="e"]').length;
                     var cantidad=<?php echo $candidadTotalIngresar; ?>;
                     
                     if(total>=cantidad){
                    	 alert('Ya se ha ingresado todas las series');
                         $('#txtSerie').val('');
                         $('#imgGuardarSerie').focus();
                         return false;
                     }
                                        
                         $.ajax({
                             url: '<?php echo base_url() ?>index.php/almacen/producto/validarserie/'+serie,
                             type: 'get',
                             success:function(data){
                                 if(data == 0){
                                     alert('serie registrada anteriormente por favor ingresar otra serie') ;
                                     $('#txtSerie').val('');
                                     $('#txtSerie').focus();
                                     return false;     
                                 }else{
                                	var valores=false;
                                	if(n!=0)
                                   	{
                                		for(x=1;x<=n;x++){
                                            valor= "serie["+x+"]"; 
                                            var  valor_serie = document.getElementById(valor).value ;
                                            valor= "accion["+x+"]"; 
                                            var  valor_accion = document.getElementById(valor).value ;
                                            if(valor_accion!='e')
                                            {
	                                            if(serie.toUpperCase()==valor_serie.toUpperCase()){
	                                                valores=true    
	                                                break;
	                                            }
                                            }
                                        }
                                   	}	 
                                     if(valores){
                                         alert('serie repetida por favor ingresar otra serie') ;
                                         $('#txtSerie').val('');
                                         return false;
                                     }else{
                                    	 n=n+1;
                                    	 if (n % 2 == 0) {
                                    	 	clase = "itemParTabla";
                                    	 } else {
                                    	 	clase = "itemImparTabla";
                                    	 }
                                    	 serie=serie.toUpperCase();
                                    	 serieCodigo=null;
                                    	 serieDocumentoCodigo=null;
                                         var fila='';
                                         fila+='<tr class="'+clase+'">';
                                         fila+='<td align="center" width="30">'+n+'</td>';
                                         fila+='<input type="hidden" class="serieCodigo['+n+']" name="serieCodigo['+n+']" id="serieCodigo['+n+']" value="'+serieCodigo+'" class="cajaMedia"/>';
                                         fila+='<td align="left"><input type="text" onblur="verificarCampoAgregado('+n+')" class="serie['+n+']" name="serie['+n+']" id="serie['+n+']" value="'+serie+'" class="cajaMedia"/></td>';
                                         fila+='<td align="center" width="30">';
                                         fila+='<a href="javascript:;" class="remove" id="'+n+'" ><img src="'+base_url+'images/icono_desaprobar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Retirar de la Lista"/>';
                                         fila+='<input type="hidden" value="n" name="accion['+n+']" id="accion['+n+']" />';
                                         fila+='<input type="hidden" name="serieDocumentoCodigo['+n+']" id="serieDocumentoCodigo['+n+']"   value="'+serieDocumentoCodigo+'"  />';
                                         fila+='</td>';
                                         fila+='</tr>';
                                         $("#tabla_resultado").append(fila);
                                         /**si se completo todo focus en agregar**/
                                         if(total+1>=cantidad){                                        	 
                                             $('#txtSerie').val('');
                                             $('#imgGuardarSerie').focus();
                                             return;
                                         }else{
                                        	 $('#txtSerie').val("");
                                             $('#txtSerie').focus();
                                         }	                                                                                      
                                         return;
                                     }
                                	
                                 }
  
                             }
                         });
             }
                 
			/**validamos que no exista en serie y dentro de la lista**/
			 function verificarCampoAgregado(posicion){
				 valor= "serie["+posicion+"]"; 
                 var  valor_serieReal = document.getElementById(valor).value ;

                 
                 valorSerie= "serieCodigo["+posicion+"]"; 
                 var  serieCodigo = document.getElementById(valorSerie).value ;
                 
                 var n = document.getElementById('tabla_resultado').rows.length;
                 var total=$('input[id^="accion"][value!="e"]').length;
                 var cantidad=<?php echo $candidadTotalIngresar; ?>;
				 $.ajax({
                     url: '<?php echo base_url() ?>index.php/almacen/producto/validarserie/'+valor_serieReal+'/'+serieCodigo,
                     type: 'get',
                     success:function(data){
                         if(data == 0){
                        	
                             alert('serie registrada anteriormente por favor ingresar otra serie.') ;
                             document.getElementById(valor).value ='';
                             document.getElementById(valor).focus();
                             return false;     
                         }else{

                        	var valores=false;
                        	if(n!=0)
                           	{
                        		for(x=1;x<=n;x++){
                                    valor= "serie["+x+"]"; 
                                    var  valor_serie = document.getElementById(valor).value ;
                                    valor= "accion["+x+"]"; 
                                    var  valor_accion = document.getElementById(valor).value ;
                                    if(valor_accion!='e')
                                    {
                                        /**tiene que ser diferente al que se edita**/
                                        if(x!=posicion){
                                            
	                                        if(valor_serieReal.toUpperCase()==valor_serie.toUpperCase()){
	                                            valores=true    
	                                            break;
	                                        }
                                        }
                                    }
                                }
                           	}	 
                             if(valores){
                                 alert('serie repetida en la lista por favor ingresar otra serie') ;
                                 document.getElementById(valor).value ='';
                                 document.getElementById(valor).focus();
                                 return false;
                             }else{
                                 /**si se completo todo focus en agregar**/
                                 if(total+1>=cantidad){ 
                                     $('#imgGuardarSerie').focus();
                                     return;
                                 }else{
                                	 document.getElementById(valor).value ='';
                                     document.getElementById(valor).focus();
                                 }	                                                                                      
                                 return;
                             }
                         }
                     }
                 });
			} 

		/***buscamos los productos no seleccionados (venta)***/
		function listaSeriesNoseleccionadas(serieNumeroBusqueda){
			codigoAlmacenReal=$('#almacen').val();
			codigoProductoReal=$('#producto_id').val();
			 $("#idDetalleSerieriesBD").html(' ');
            url='<?php echo base_url() ?>index.php/almacen/producto/listaSeriesNoseleccionadasJson/'+codigoAlmacenReal+"/"+codigoProductoReal+"/"+serieNumeroBusqueda;
			 $.getJSON(url, { codigoAlmacen: codigoAlmacenReal, codigoProducto: codigoProductoReal} ,function (data) {
				 totalLista=data.length;
				 $.each(data, function (i, item) {
					serieCodigo=item.CodigoSerie;
					serie=item.numero;
					fecha=item.fechaRegistro;
					 n=i;
					 n=n+1;
                	 if (n % 2 == 0) {
                	 	clase = "itemParTabla";
                	 } else {
                	 	clase = "itemImparTabla";
                	 }
					 var fila='';
	                 fila+='<tr class="'+clase+'">';
	                 fila+='<td align="center" width="30">'+n+'</td>';
	                 fila+='<input type="hidden" class="serieCodigoS['+n+']" name="serieCodigoS['+n+']" id="serieCodigoS['+n+']" value="'+serieCodigo+'" class="cajaMedia"/>';
	                 fila+='<td align="left"><input type="text" class="serieS['+n+']" name="serieS['+n+']" id="serieS['+n+']" value="'+serie+'" class="cajaMedia" readonly/></td>';
	                 fila+='<td align="center" width="30">';
	                 fila+='<a href="javascript:;" onclick="agregarSerieVenta('+n+')" id="posicion_'+n+'" ><img src="'+base_url+'images/botonagregar.jpg?=<?=IMG;?>" width="16" height="16" border="0" title="Agregar"/>';
	                
	                 fila+='</td>';
	                 fila+='</tr>';
	                 $("#idDetalleSerieriesBD").append(fila);

				 });

				 
			 });		
		} 

		/***agregamos la serie seleccionada a la lista de seleccion**/
		function agregarSerieVenta(posicion){
			var n = document.getElementById('tabla_resultado').rows.length;
			valorSerieS= "serieCodigoS["+posicion+"]"; 
            var  serieCodigoS = document.getElementById(valorSerieS).value;
            valorSerieS= "serieS["+posicion+"]"; 
            var  numeroSerieS = document.getElementById(valorSerieS).value;

            var total=$('input[id^="accion"][value!="e"]').length;
            var cantidad=<?php echo $candidadTotalIngresar; ?>;
            
            if(total>=cantidad){
           	 alert('Ya se ha ingresado todas las series');
                $('#imgGuardarSerie').focus();
                return false;
            }


            
			serie=numeroSerieS.toUpperCase();
       	 	serieCodigo=serieCodigoS;
       	 	serieDocumentoCodigo=null;
            var fila='';
            n=n+1;
            fila+='<tr class="'+clase+'">';
            fila+='<td align="center" width="30">'+n+'</td>';
            fila+='<input type="hidden" class="serieCodigo['+n+']" name="serieCodigo['+n+']" id="serieCodigo['+n+']" value="'+serieCodigo+'" class="cajaMedia"/>';
            fila+='<td align="left"><input type="text" onblur="verificarCampoAgregado('+n+')" class="serie['+n+']" name="serie['+n+']" id="serie['+n+']" value="'+serie+'" class="cajaMedia" readonly/></td>';
            fila+='<td align="center" width="30">';
            fila+='<a href="javascript:;" class="remove" id="'+n+'"><img src="'+base_url+'images/icono_desaprobar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Retirar de la Lista"/>';
            fila+='<input type="hidden" value="n" name="accion['+n+']" id="accion['+n+']" />';
            fila+='<input type="hidden" name="serieDocumentoCodigo['+n+']" id="serieDocumentoCodigo['+n+']"   value="'+serieDocumentoCodigo+'"  />';
            fila+='</td>';
            fila+='</tr>';
            $("#tabla_resultado").append(fila);
            /** 1:seleccionado**/
            guardarSerieTemporalBD(serie,serieCodigo,1);
            if(total+1>=cantidad){        
                $('#imgGuardarSerie').focus();
                return;
            }else{
            	$('#txtSerieSeleccionar').focus();
            }
		}

		/**se actualiza la serie cuando se selecciona y no se selecciona bd***/
		function guardarSerieTemporalBD(numeroSerie,serieCodigo,estado){
			codigoProducto=$('#producto_id').val();
			almacen=$('#almacen').val();
			var url = base_url+"index.php/almacen/producto/seleccionarSerieBD/"+codigoProducto+"/"+numeroSerie+"/"+serieCodigo+"/"+estado+"/"+almacen;
		    $.get(url,function(data){
			    /**obtenemos la lista nuevamente de no seleccionados**/
		    	listaSeriesNoseleccionadas('');
			});
		}


		
   function subirArchivos() {
                $("#archivo").upload(base_url+'index.php/almacen/producto/cargarExcelSeries',
                {
                    nombre_archivo: $("#nombre_archivo").val()
                },
                function(respuesta) {
                    mostrarDatosExcelSerie();
                    //Subida finalizada.
                    $("#datos_ajax_cargando").val(0);
                   
                   // mostrarArchivos();
                }, function(progreso, valor) {
                    //Barra de progreso.
                    $("#datos_ajax_cargando").val(valor);
                });
            }   
	function mostrarDatosExcelSerie(){
  		var parametros = $(this).serialize();
             $.ajax({
                    type: "post",
                    url: base_url+'index.php/almacen/producto/mostrarDatosExcelSerie/'+$("#txtSerieCantidad").val(),
                    data: parametros,
                     beforeSend: function(objeto){
	                $("#datos_ajax_cargando").html("Mensaje: Cargando...");
	                      },
		            success: function(datos)
		            {
		            	$("#datos_ajax_cargando").html("");
		            	$("#tabla_resultado").html(datos);       
		            }
            });
	}  
	
	function seleccionarAlmacenProducto(codigoAlmacen,posicionRadio){
		almacenAnterior=$("almacen").val();
		if(confirm("Desea cambiar de almacen, se descartaran los cambios realizados")){
			/**descartamos todoa las series ingresadas**/
			var n = document.getElementById('tabla_resultado').rows.length;
			/**verificamos que no exista ninguno en blanco**/
			if(n>0){	
            	for(x=1;x<=n;x++){
            		posicion=x;
            		removerListadoDeSeries(tipoOperacion,posicion); 
           		}
			}
			/**fin de descartar todas las series**/
			$("#almacen").val(codigoAlmacen);
			if(tipoOperacion!=null && tipoOperacion=='V'){
				listaSeriesNoseleccionadas('');	
			}
			posicionAnteriorRadio=posicionRadio;
		}else{
			$("#almacen").val(almacenAnterior);
			document.getElementById('idRdAlmacen'+posicionAnteriorRadio).checked = true;
		}	
	}
		/**fin de busqueda**/		
		
		function removerListadoDeSeries(tipoOperacion,posicion){
			$("#"+posicion).parent().parent().fadeOut('fast');
			/***si es de tipo operacion venta debe de actualizar la serie como no seleccionado**/
			if(tipoOperacion=='V'){
				/**0:DESELECCIONADO***/
			 	valor= "serieCodigo["+posicion+"]"; 
                var  serieCodigo = document.getElementById(valor).value ;
                valorSerie= "serie["+posicion+"]"; 
                var  numeroSerie = document.getElementById(valorSerie).value;
                valorAccion= "accion["+posicion+"]"; 
                document.getElementById(valorAccion).value='e';
			 	guardarSerieTemporalBD(numeroSerie,serieCodigo,0);
			}else{
				valorAccion= "accion["+posicion+"]"; 
	            document.getElementById(valorAccion).value='e';
			}



		}
		
		
		
        </script>
        
        <style type="text/css">
        .divResponsive{
    float: left;
    margin: 4px;
    padding: 10px;
    max-width: 300px;
    height: 100%;
} 
        </style>
        
    </head>
    <body>
        <div align="center"> 

            
            <div  style="width:100%">
                <table  width="100%" cellspacing=0 cellpadding=3 border=1>					
                    <tr class="cabeceraTabla" height="25px">
                        <td align="center" colspan="3"><?php echo $nombre_producto; ?></td>
                    </tr>
                </table>
            </div>
            <br />
            
            <div  style="width:100%">
		<!--id="ExecelSerie" name="ExecelSerie" enctype="multipart/form-data" method="post" action="<?=base_url()?>index.php/almacen/producto/cargarExcelSeries"-->
		<form action="javascript:void(0);" id="ExecelSerie" name="ExecelSerie"  >
		<div id="datos_ajax_cargando"></div>
		<input id="archivo" type="file"  value="Subir" name="archivo" >
		<input type="submit" name="buttonSubirExcel" id="buttonSubirExcel" value="ingresar">
		<input type="hidden" name="txtSerieCantidad" id="txtSerieCantidad" value="<?=$candidadTotalIngresar?>">
		</form> 
      
       <form action="javascript:void(0);" id="formularioProductoSerie" name="formularioProductoSerie"  >
            <!-- Solo se muestra si es de tipo venta -->
            
            <input type="hidden" name="almacen"  id="almacen" value="<?php echo (isset($almacen))?$almacen:0; ?>" />
             <input type="hidden" name="isSeleccionarAlmacen"  id="isSeleccionarAlmacen" value="<?php echo (isset($isSeleccionarAlmacen))?$isSeleccionarAlmacen:0; ?>" />
           	<?php if($isSeleccionarAlmacen==1){ ?>
            <div id="idDivSeleccionarAlmacen" >
	            <table id="idTblAlmacen" >
	            	<tr>
	            	<td></td>
	            	<td>Descripcion</td>
	            	<td>Stock</td>            	
	            	</tr>
	            	
	            	<?php if($almacenesProducto!=null && count($almacenesProducto)>0){
	            				foreach ($almacenesProducto as $indice=>$valorAlmacen){
	            					$codigoAlmacen=$valorAlmacen->codigoAlmacen;
	            					$nombreAlmacen=$valorAlmacen->nombreAlmacen;
	            					$stock=$valorAlmacen->stock;
	            					if($almacen==null || trim($almacen)!=""){
	            						$almacen=$codigoAlmacen;
	            					?>
	            					<script>
	            						posicionAnteriorRadio=<?php echo $indice; ?>;
	            						$("#almacen").val("<?php echo $almacen; ?>");
	            					</script>
	            					<?php
	            					}	
	            		?>
					<tr>
	            	<td>
	            	<input type="radio" name="almacenListado" 
	            	id="idRdAlmacen<?php echo $indice; ?>"
	            	value="<?php echo $codigoAlmacen;?>" <?php echo($codigoAlmacen==$almacen)?'checked':''; ?>
	            		onclick="seleccionarAlmacenProducto(<?php echo $codigoAlmacen;?>,<?php echo $indice; ?>)"
	            	>
	            	</td>
	            	<td><?php echo $nombreAlmacen;?></td>
	            	<td><?php echo $stock;?></td>            	
	            	</tr>
	            	<?php }
	            	} ?>	
	            	
	            </table>
            </div>
            <script type="text/javascript">
            
				/**ejecutamos para que nos muestre las series para ventas**/
				if(tipoOperacion!=null && tipoOperacion=='V'){
					/***ejecutamos el metodo que nos trae  los productos sin seleccionar**/
					listaSeriesNoseleccionadas('');					
				}	
				/**fin de verificacion**/
            </script>
            <?php } ?>
            
            
            
            <?php if(isset($tipo_oper) &&  trim($tipo_oper)=='V') { ?>
            <div id="idSeleccionarSerie" class="divResponsive">
            
            <label>Serie:   </label>
	        <input  id="txtSerieSeleccionar" type="text" class="cajaMedia" name="txtSerieSeleccionar" maxlength="50" />
	        <a href="javascript:;" id="limpiarBusquedaSeriesS"><img src="<?php echo base_url(); ?>/images/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
            	<table  width="100%" align="center" cellspacing="1" cellpadding="3" border="0" >
                 	<tr>
                 		<td width="30">Nro.</td>
                 		<td>Serie</td>
                 		<td width="30">Acciones</td>
                 	</tr>
           		</table>
           	<div style="width:100%;overflow:auto; float:left;height: 80%;">
            	<table  width="100%" id="idDetalleSerieriesBD"  align="center" cellspacing="1" cellpadding="3" border="0" >
            	</table>
            	</div>
            </div>	
           	<?php } ?>
            <!-- FIN DE MOSTRAR5 -->
            
            
            
            	<div class="divResponsive">
            	<input type="hidden" name="tipo_oper"  id="tipo_oper" value="<?php echo $tipo_oper; ?>" />
            	
            	
	            	<label>Total cantidad:   </label><label><?php echo $candidadTotalIngresar; ?></label>
	            	<br>
	            	<!-- Solo se miuestra si es de tipo compr5a -->
	            	 <?php if(isset($tipo_oper) &&  trim($tipo_oper)=='C') { ?>
	            	<label>Serie:   </label>
	            	<input  id="txtSerie" type="text" class="cajaMedia" name="txtSerie" maxlength="50" />
	                <a href="javascript:;" id="imgAgregarSerie">
	                <img src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton">
	                </a>
	                <?php }?>
                
	                <!-- FIN DE MOSTRAR -->
	                 <table  width="100%" align="center" cellspacing="1" cellpadding="3" border="0" >
	                 	<tr>
	                 		<td width="30">Nro.</td>
	                 		<td>Serie</td>
	                 		<td width="30">Acciones</td>
	                 	</tr>
	                 </table>   
	              <div style="width:100%;overflow:auto; float:left;height: 80%;">   		     
					<table  width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" cellspacing="1" cellpadding="3" border="0" >
	                    <?php
	                    foreach ($numero_serie as $i => $valor) {
	                        ?>
	                        <tr class="itemParTabla">
	                            <td align="center" width="30"><?php echo $i + 1; ?></td> 
	                            <td align="left">
	                            <input type="hidden"  name="serieCodigo[<?php echo $i + 1; ?>]" id="serieCodigo[<?php echo $i + 1; ?>]" value="<?php echo $valor->serieCodigo; ?>" />
	                            <input type="hidden"  name="serieDocumentoCodigo[<?php echo $i + 1; ?>]" id="serieDocumentoCodigo[<?php echo $i + 1; ?>]" value="<?php echo $valor->serieDocumentoCodigo; ?>" />
	                            
	                            <input type="text" onblur="verificarCampoAgregado(<?php echo $i + 1; ?>)" name="serie[<?php echo $i + 1; ?>]" id="serie[<?php echo $i + 1; ?>]" value="<?php echo $valor->serieNumero; ?>" class="cajaMedia" 
	                            <?php echo ($tipo_oper=='V')?'readonly':''; ?>
	                            /></td>
	                            <td align="center" width="30">
	                                <a href="javascript:;" class="remove" id="<?php echo $i + 1; ?>" ><img src="<?php echo base_url(); ?>public/images/icons/icono_desaprobar.png?=<?=IMG;?>" width="16" height="16" border="0" title="Retirar de la Lista"/></a>
	    							<input type="hidden" value="n" name="accion[<?php echo $i + 1; ?>]" id="accion[<?php echo $i + 1; ?>]" />
	                            </td>
	                        </tr>
	                        <?php
	                    }
	                    ?>
	
	                </table>
	                </div>  
                </div> 
                <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
                	<a href="javascript:;" id="imgGuardarSerie"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                	<a href="javascript:;" id="imgCancelarSerie"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    	<?php echo $form_hidden; ?>
            	</div>
            	</form>
            </div>
       </div>
    </body>
</html>
