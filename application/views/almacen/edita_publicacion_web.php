<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/producto.js?=<?=JS;?>"></script>					
<div id="pagina">
     
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda">
                <?php echo validation_errors("<div class='error'>",'</div>');?>
                <?php echo $form_open;?>
                 <div id="publicacionweb" style="float:left;width:98%; text-align: left; display: block;">
                     
                        <!-- FlagBS = Imagenes -->
                        <!--               
                        <!-- inicio articulo 1 -->
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                        <?php  if($imagen!='') echo '<img style="margin-top:10px;" src="'.base_url().'/images/img_db/'.$imagen.'" alt="'.$imagen.'" width="120" height="120" border="1" />' ?>   
                               <input name="imagen" id="imagen" style="font-size:0.9em" type="file"/>
                             
                        <?php  if($imagen_1!='') echo '<img style="margin-top:10px;" src="'.base_url().'/images/img_db/'.$imagen_1.'" alt="'.$imagen_1.'" width="120" height="120" border="1" />' ?>   
                               <input name="imagen_1" id="imagen_1" style="font-size:0.9em" type="file"/>       
                         
                            <?php  if($imagen_2!='') echo '<img style="margin-top:10px;" src="'.base_url().'/images/img_db/'.$imagen_2.'" alt="'.$imagen_2.'" width="120" height="120" border="1" />' ?>   
                               <input name="imagen_2" id="imagen_1" style="font-size:0.9em" type="file"/>       
                              
                               
                               <table width="98%">
                               <tr>
                                  <input type="hidden" name="imppub_codigo_1" id="imppub_codigo_1" value="<?php echo $imppub_codigo_1;?>">
                                  <td rowspan="5" width="25%">Secci&oacute;n 1: <input type="text" class="cajaMedia" name="sec_descripcion_1" id="sec_descripcion_1" value="<?php echo $sec_descripcion_1;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col1_fil1_1" id="col1_fil1_1" value="<?php echo $col1_fil1_1;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil1_1" id="col2_fil1_1" value="<?php echo $col2_fil1_1;?>"></td>                                  
                               </tr>
                               <tr>                                  
                                  <td><input type="text" class="cajaGrande" name="col1_fil2_1" id="col1_fil2_1" value="<?php echo $col1_fil2_1;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil2_1" id="col2_fil2_1" value="<?php echo $col2_fil2_1;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil3_1" id="col1_fil3_1" value="<?php echo $col1_fil3_1;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil3_1" id="col2_fil3_1" value="<?php echo $col2_fil3_1;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil4_1" id="col1_fil4_1" value="<?php echo $col1_fil4_1;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil4_1" id="col2_fil4_1" value="<?php echo $col2_fil4_1;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil5_1" id="col1_fil5_1" value="<?php echo $col1_fil5_1;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil5_1" id="col2_fil5_1" value="<?php echo $col2_fil5_1;?>"></td>
                               </tr>
                            </table>  
                            </div>                            
                        </div>
                        <!-- inicio articulo 2 -->
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                                <table width="98%">
                               <tr>
                                  <input type="hidden" name="imppub_codigo_2" id="imppub_codigo_2" value="<?php echo $imppub_codigo_2;?>"> 
                                  <td rowspan="5" width="25%">Secci&oacute;n 2: <input type="text" class="cajaMedia" name="sec_descripcion_2" id="sec_descripcion_2" value="<?php echo $sec_descripcion_2;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col1_fil1_2" id="col1_fil1_2" value="<?php echo $col1_fil1_2;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil1_2" id="col2_fil1_2" value="<?php echo $col2_fil1_2;?>"></td>                                  
                               </tr>
                               <tr>                                  
                                  <td><input type="text" class="cajaGrande" name="col1_fil2_2" id="col1_fil2_2" value="<?php echo $col1_fil2_2;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil2_2" id="col2_fil2_2" value="<?php echo $col2_fil2_2;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil3_2" id="col1_fil3_2" value="<?php echo $col1_fil3_2;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil3_2" id="col2_fil3_2" value="<?php echo $col2_fil3_2;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil4_2" id="col1_fil4_2" value="<?php echo $col1_fil4_2;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil4_2" id="col2_fil4_2" value="<?php echo $col2_fil4_2;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil5_2" id="col1_fil5_2" value="<?php echo $col1_fil5_2;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil5_2" id="col2_fil5_2" value="<?php echo $col2_fil5_2;?>"></td>
                               </tr>
                            </table>  
                            </div>                         
                        </div>
                        <!-- inicio articulo 3 -->
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                                <table width="98%">
                               <tr>
                                  <input type="hidden" name="imppub_codigo_3" id="imppub_codigo_3" value="<?php echo $imppub_codigo_3;?>"> 
                                  <td rowspan="5" width="25%">Secci&oacute;n 3: <input type="text" class="cajaMedia" name="sec_descripcion_3" id="sec_descripcion_3" value="<?php echo $sec_descripcion_3;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col1_fil1_3" id="col1_fil1_3" value="<?php echo $col1_fil1_3;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil1_3" id="col2_fil1_3" value="<?php echo $col2_fil1_3;?>"></td>                                  
                               </tr>
                               <tr>                                  
                                  <td><input type="text" class="cajaGrande" name="col1_fil2_3" id="col1_fil2_3" value="<?php echo $col1_fil2_3;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil2_3" id="col2_fil2_3" value="<?php echo $col2_fil2_3;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil3_3" id="col1_fil3_3" value="<?php echo $col1_fil3_3;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil3_3" id="col2_fil3_3" value="<?php echo $col2_fil3_3;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil4_3" id="col1_fil4_3" value="<?php echo $col1_fil4_3;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil4_3" id="col2_fil4_3" value="<?php echo $col2_fil4_3;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil5_3" id="col1_fil5_3" value="<?php echo $col1_fil5_3;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil5_3" id="col2_fil5_3" value="<?php echo $col2_fil5_3;?>"></td>
                               </tr>
                            </table>  
                            </div>                         
                        </div>


                        <!-- inicio articulo 4 -->
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                                <table width="98%">
                               <tr>
                                  <input type="hidden" name="imppub_codigo_4" id="imppub_codigo_4" value="<?php echo $imppub_codigo_4;?>"> 
                                  <td rowspan="5" width="25%">Secci&oacute;n 4: <input type="text" class="cajaMedia" name="sec_descripcion_4" id="sec_descripcion_4" value="<?php echo $sec_descripcion_4;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col1_fil1_4" id="col1_fil1_4" value="<?php echo $col1_fil1_4;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil1_4" id="col2_fil1_4" value="<?php echo $col2_fil1_4;?>"></td>                                  
                               </tr>
                               <tr>                                  
                                  <td><input type="text" class="cajaGrande" name="col1_fil2_4" id="col1_fil2_4" value="<?php echo $col1_fil2_4;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil2_4" id="col2_fil2_4" value="<?php echo $col2_fil2_4;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil3_4" id="col1_fil3_4" value="<?php echo $col1_fil3_4;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil3_4" id="col2_fil3_4" value="<?php echo $col2_fil3_4;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil4_4" id="col1_fil4_4" value="<?php echo $col1_fil4_4;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil4_4" id="col2_fil4_4" value="<?php echo $col2_fil4_4;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil5_4" id="col1_fil5_4" value="<?php echo $col1_fil5_4;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil5_4" id="col2_fil5_4" value="<?php echo $col2_fil5_4;?>"></td>
                               </tr>
                            </table>  
                            </div>                         
                        </div>
                        <!-- inicio articulo 5 -->
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                                <table width="98%">
                               <tr>
                                  <input type="hidden" name="imppub_codigo_5" id="imppub_codigo_5" value="<?php echo $imppub_codigo_5;?>">
                                  <td rowspan="5" width="25%">Secci&oacute;n 5: <input type="text" class="cajaMedia" name="sec_descripcion_5" id="sec_descripcion_5" value="<?php echo $sec_descripcion_5;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col1_fil1_5" id="col1_fil1_5" value="<?php echo $col1_fil1_5;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil1_5" id="col2_fil1_5" value="<?php echo $col2_fil1_5;?>"></td>                                  
                               </tr>
                               <tr>                                  
                                  <td><input type="text" class="cajaGrande" name="col1_fil2_5" id="col1_fil2_5" value="<?php echo $col1_fil2_5;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil2_5" id="col2_fil2_5" value="<?php echo $col2_fil2_5;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil3_5" id="col1_fil3_5" value="<?php echo $col1_fil3_5;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil3_5" id="col2_fil3_5" value="<?php echo $col2_fil3_5;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil4_5" id="col1_fil4_5" value="<?php echo $col1_fil4_5;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil4_5" id="col2_fil4_5" value="<?php echo $col2_fil4_5;?>"></td>
                               </tr>
                               <tr>                                
                                  <td><input type="text" class="cajaGrande" name="col1_fil5_5" id="col1_fil5_5" value="<?php echo $col1_fil5_5;?>"></td>
                                  <td><input type="text" class="cajaGrande" name="col2_fil5_5" id="col2_fil5_5" value="<?php echo $col2_fil5_5;?>"></td>
                               </tr>
                            </table>  
                            </div>                         
                        </div>
                        <!-- inicio de comentario -->
                        <div style="width:100%;"><hr width="98%"></div>
                        <div style="width:100%;">
                            <div>
                                <table width="98%">
                              <tr>
                                <td valign="top"><p>&nbsp;</p>
                                <p>Comentarios Adicionales: </p></td>
                                <td><textarea rows="8" cols="140" class="cajaTextArea"  name="imppub_descripcion" id="imppub_descripcion"><?php echo $imppub_descripcion;?> </textarea></td>
                              </tr>
                                </table>  
                            </div>                         
                        </div>
                    </div>
                    <!-- fin de publicaion web -->
                    <div style="margin-top:20px; text-align: center">
                        <a href="#" id="grabarPublicacionWeb"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <a href="#" id="limpiarPublicacionWeb"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
                        <a href="#" id="cancelarPublicacionWeb"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                      <?php $producto?>  
                      <?php echo $oculto?>
                    </div>
                <?php echo $form_close;?>
            </div>
        </div>
    </div>
</div>