<div class="row">
    <div class="col-md-12">
      <div class="card card-light">
        <div class="card-header">
          <h3 class="card-title"><?=$titulo_busqueda;?></h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
          
        <div class="card-body">
            <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo base_url(); ?>index.php/ventas/comprobante/comprobantes">
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label for="fechai">Fecha Desde:</label>
                        <input id="fechai" name="fechai" type="date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="fechaf">Fecha Hasta:</label>
                        <input id="fechaf" name="fechaf" type="date" class="form-control form-control-sm">
                    </div>
                    <!--div class="col-lg-1 form-group">
                        <label for="search_codigo">Serie</label>
                          <?php
            if ($tipo_oper == 'V'){ ?>
                    <select id="seriei" name="seriei" class="form-control form-control-sm"> 
                       <?php
                        if ($series_emitidas != NULL){
                        ?>
                        <option value="">::Seleccione::</option>
                        <?php
                    foreach ($series_emitidas as $i => $val){ ?>
                        <option value="<?=$val->CPC_Serie;?>" accesskey=""<?=($val->serie_actual == $val->CPC_Serie) ? "selected" : "";?>>
                            <?=$val->CPC_Serie;?>
                        </option> <?php
                    }
                } ?>
            </select> 
             <?php
        }
    else{ ?>
                                    <input type="text" name="seriei" id="seriei" value="" placeholder="Serie" class="form-control form-control-sm" autocomplete="off"/>
                            <?php } ?>
                    </div-->
                        <div class="col-lg-1 form-group">
                                <label for="search_documento">Número</label>
                                <input type="text" name="numero" id="numero" value="" placeholder="Número" class="form-control form-control-sm" autocomplete="off"/>
                        </div>											
                        <div class="col-md-2 form-group">
                                <?php if ($tipo_oper == 'V') { ?>
                                        <label for="ruc_cliente">Ruc Cliente:</label>
                                        <input type="hidden" name="cliente" id="cliente" size="5"/>
                                        <input type="text" name="ruc_cliente" id="ruc_cliente" placeholder="Ruc" class="form-control form-control-sm" autocomplete="off" maxlength="11" onkeypress="return numbersonly(this, event, '.');"/>
                                <?php } else { ?>
                                        <label for="ruc_proveedor">Ruc Proveedor:</label>
                                        <input type="hidden" name="proveedor" id="proveedor" size="5"/>
                                        <input type="text" name="ruc_proveedor" id="ruc_proveedor" placeholder="Ruc" class="form-control form-control-sm" autocomplete="off" maxlength="11" onblur="obtener_proveedor();" onkeypress="return numbersonly(this, event, '.');"/>
                                <?php } ?>
                        </div>	
                        <div class="col-md-4 form-group">
                                <?php if ($tipo_oper == 'V') { ?>
                                        <label for="nombre_cliente">Razón Social:</label>
                                        <input type="text" name="nombre_cliente" id="nombre_cliente" placeholder="Nombre cliente" class="form-control form-control-sm" autocomplete="off" size="40"/>
                                <?php } else { ?>
                                        <label for="nombre_proveedor">Razón Social:</label>
                                        <input type="text" name="nombre_proveedor" id="nombre_proveedor" placeholder="Nombre proveedor" class="form-control form-control-sm" autocomplete="off" size="40"/>							
                                <?php } ?>
                        </div>							
                </div>

                    <div class="row" id="inputFilters">
                            <div class="col-md-2 form-group oculto">
                                    <label for="numeroI">Número Inicio: </label>
                                    <input id="numeroI" name="numeroI" type="number" class="form-control form-control-sm" placeholder="Modelo" value="">
                            </div>
                            <div class="col-md-2 form-group oculto">
                                    <label for="numeroF">Número Fin: </label>
                                    <input id="numeroF" name="numeroF" type="number" class="form-control form-control-sm" placeholder="Modelo" value="">
                            </div>	
                            <div class="col-lg-4 form-group align-self-end oculto">
                                    <button type="button" class="btn btn-default btn-sm" id="imprimirRango">Imprimir Rango</button>
                            </div>					
                    </div>

                    <div class="row justify-content-end">
                            <div class="col-lg-5 form-group align-self-end text-right">
                                    <button type="button" class="btn btn-default oculto" id="btn-up""><i class="fas fa-angle-up"></i> Filtros</button>
                                    <button type="button" class="btn btn-default" id="btn-down"><i class="fas fa-angle-down"></i> Filtros</button>							
                                    <button type="button" class="btn btn-info" id="buscarC">Buscar</button>
                                    <button type="button" class="btn btn-dark" id="limpiarC">Limpiar</button>
                                    <button type="button" class="btn btn-success" id="nuevaComprobante" data-toggle='modal' data-target='#modal_addcliente'>Nuevo</button>
                                    <button type="button" class="btn btn-warning" id="imprimirComprobante">Imprimir</button>	
            <input type="hidden" name="Rtipo_docu" id="Rtipo_docu" value="<?=$tipo_docu;?>"/>
            <input type="hidden" name="Rtipo_oper" id="Rtipo_oper" value="<?=$tipo_oper;?>"/>													
                            </div>
                    </div>	
                    <?php echo $oculto; # ESTA VARIABLE CONTIENE EL TIPO DE OPERACION Y TIPO DE DOCUMENTO ?>				
            </form>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card card-light">
            <div class="card-header">
              <h3 class="card-title"><?=$titulo_tabla;?></h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" 
                        title="Collapse">
                        <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" id="table-comprobante">
                    <thead>
                        <tr>
                            <th style="width: 07%" data-orderable="true">F. REGISTRO</th>
                            <th style="width: 07%" data-orderable="true">FECHA</th>
                            <th style="width: 05%" data-orderable="true">SERIE</th>
                            <th style="width: 07%" data-orderable="true">NÚMERO</th>
                            <th style="width: 05%" data-orderable="true"><?=($tipo_oper == "V") ? "CLIENTE" : "";?></th>
                            <th style="width: 07%" data-orderable="true">R.U.C.</th>
                            <th style="width: 30%" data-orderable="true">RAZON SOCIAL</th>
                            <th style="width: 08%" data-orderable="true">TOTAL</th>
                            <th style="width: 01%" data-orderable="false">&nbsp;</th>
                            <th style="width: 01%" data-orderable="false">&nbsp;</th>
                            <th style="width: 01%" data-orderable="false">&nbsp;</th>
                            <th style="width: 01%" data-orderable="false">&nbsp;</th>
                            <th style="width: 01%" data-orderable="false">&nbsp;</th>
                            <th style="width: 01%" data-orderable="false">&nbsp;</th>
                            <th style="width: 01%" data-orderable="false">&nbsp;</th>
                            <th style="width: 07%" data-orderable="false">&nbsp;</th>
                            <th style="width: 07%" data-orderable="false">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--MODAL ENVIA CORREO-->
<div class="modal fade modal-envmail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 700px; padding: 1em 3em 1em 3em; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 10pt;">
            <form method="post" id="form-mail">
                <div class="contenido" style="width: 100%; margin: auto; height: auto; overflow: auto;">
                    <div class="tempde_head">

                        <div class="row">
                            <div class="col-sm-11 col-md-11 col-lg-11" style="text-align: center;">
                                <h3>ENVIO DE DOCUMENTOS POR CORREO</h3>
                            </div>
                        </div>

                        <input type="hidden" id="idDocMail" name="idDocMail">
                    </div>

                    <div class="tempde_body">
                        
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <label for="ncliente">Cliente:</label>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label for="doccliente">Ruc / Dni:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input type="text" class="form-control" id="ncliente" name="ncliente" value="" placeholder="Razón social" readonly>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" class="form-control" id="doccliente" name="doccliente" value="" placeholder="N° documento" readonly>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <label for="destinatario">Destinatarios:</label>
                                <span class="mail-contactos"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" class="form-control" id="destinatario" name="destinatario" value="" placeholder="Correo">
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <label for="asunto">Asunto:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" class="form-control" id="asunto" name="asunto" value="" placeholder="Asunto">
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <label for="mensaje">Mensaje:</label>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <textarea id="mensaje" name="mensaje" style="width: 650px; height: 300px">
                                    <p><b>SRES.</b> <span class="mail-cliente"></span></p>
                                    <p><span class="mail-empresa-envio"></span>, ENVÍA UN DOCUMENTO ELECTRÓNICO.</p>
                                    <p><b>SERIE Y NÚMERO:</b> <span class="mail-serie-numero"></span></p>
                                    <p><b>FECHA DE EMISIÓN:</b> <span class="mail-fecha"></span></p>
                                    <p><b>IMPORTE:</b> <span class="mail-importe"></span></p>
                                </textarea>
                            </div>
                        </div>
                        <br>
                    </div>

                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3">
                            <label for="">Documentos adjuntos:</label>
                        </div>
                        
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="form-group">
                                <img src="<?=base_url();?>public/images/icons/icono_imprimir.png" style="width: 22px"/>
                                <input type="hidden" value="false" name="adj-ticket_hidden">
                                <input class="form-control" id="adj-ticket" name="adj-ticket" type="checkbox" value="1" style="display: none;">
                                <div class="Switch Round On fib" style="vertical-align:top;margin-left:10px;">
                                    <div class="Toggle"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="form-group">
                                <img src="<?=base_url();?>public/images/icons/pdf.png" style="width: 22px"/>
                                <input type="hidden" value="false" name="adj-a4_hidden">
                                <input class="form-control" id="adj-a4" name="adj-a4" type="checkbox" value="1" style="display: none;">
                                <div class="Switch Round On fib" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="form-group">
                                <img src="<?=base_url();?>public/images/icons/xml.png" style="width: 22px"/>
                                <input type="hidden" value="false" name="adj-xml_hidden">
                                <input class="form-control" id="adj-xml" name="adj-xml" type="checkbox" value="1" style="display: none;">
                                <div class="Switch Round On fib" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="tempde_footer">
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <span class="icon-loading-md"></span>
                                <div style="float: right">
                                    <span class="btn btn-success btn-sendMail">Enviar</span>
                                    &nbsp;
                                    <span class="btn btn-danger btn-close-envmail">Cerrar</span>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--/MODAL ENVIA CORREO-->

<!--MODAL CANJE DOCUMENTOS-->
<div class="modal fade modal-canje" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 800px; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 10pt;">
            <form method="post" id="form-canje">
                <div class="contenido" style="width: 100%; margin: auto; height: auto; overflow: auto;">
                    <div class="tempde_head">

                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-7 col-md-7 col-lg-7" style="text-align: center;">
                                <h3>CANJE DE DOCUMENTO</h3>
                            </div>
                        </div>

                       
                    </div>

                    <div class="tempde_body">
                        <input type="hidden" name="cod_cliente" id="cod_cliente" class="">
                        <input type="hidden" name="cod_comprobante" id="cod_comprobante" class="">
                            
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="placa_mail">TIPO DE OPERACION:</label>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label for="kilometraje_mail">SERIE-NUMERO:</label>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label for="kilometraje_mail">TOTAL COMPROBANTE:</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="hidden" name="tipo_operacion" id="tipo_operacion" class="comboPequeno" value="<?=$tipo_oper;?>">
                                <input type="text" name="operacion" id="operacion" class="comboPequeno" readonly>

                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="serie_numero" id="serie_numero" class="comboMedio" readonly>
                               
                            </div>
                             <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="total_comprobante" id="total_comprobante" class="comboMedio" readonly>
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="cli_prov">CLIENTE/PROVEEDOR:</label>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label for="kilometraje_mail">RUC/DNI:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="nombre_cliente_canje" id="nombre_cliente_canje" class="comboGrande">

                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <input type="text" name="ruc_cliente_canje" id="ruc_cliente_canje" class="comboGrande">
                               
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-7 col-md-7 col-lg-7">
                                <label for="destinatario">DIRECCION:</label>
                               
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" name="direccion_cliente" id="direccion_cliente" class="cajaGrande" style="width: 100%">
                                
                            </div>
                        </div>
                       <div class="form-group" align="center"><label>DOCUMENTO FINAL</label></div>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">Tipo Documento:</label>
                               
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">SERIE:</label>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">NUMERO:</label>
                               
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">FECHA:</label>
                               
                            </div>
                        </div>

                        <div class="row form-group">

                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="cmbDocumento" class="cajaPadding" id="cmbDocumento" onchange="obtenerSerieNumero()">
                                    <option value="F">FACTURA</option>
                                    <option value="B">BOLETA</option>
                                </select>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="serie_suger_b" id="serie_suger_b" class="cajaPequena">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="numero_suger_b" id="numero_suger_b" class="cajaPequena">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="date" name="fecha_comprobante" id="fecha_comprobante" class="cajaMediana">
                            </div>

                        </div>
                        
                        

                        <div class="row ">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-7 col-md-7 col-lg-7">
                                <label for="asunto">OBSERVACIONES:</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                               <textarea type="text" name="observaciones" id="observaciones"  style="width: 100%"></textarea>
                            </div>
                        </div>
                        <br>

                        
                        
                        <br>
                    </div>
                    <div class="tempde_footer">
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <span class="icon-loading-md"></span>
                                <div style="float: right">
                                    <span class="btn btn-success btn-canjear_docu">Canjear</span>
                                    &nbsp;
                                    <span class="btn btn-danger btn-close-canje">Cerrar</span>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--/MODAL CANJE DOCUMENTOS-->