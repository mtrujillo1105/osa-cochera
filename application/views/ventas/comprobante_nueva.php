<?php
 //ini_set('error_reporting', 1); 
?>
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script-->

<script src="<?php echo base_url();?>public/js/jquery.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>
<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>
<!-- INICIO -->
<input type="hidden" name="codigoguia" id="codigoguia" value="<?php echo $guia; ?>"/>
<input value='<?php echo $compania; ?>' name="compania" type="hidden" id="compania"/>
<input type="hidden" name="idProyecto" id="id-proyecto" value="<?php echo $id_proyecto ?>">
<div id="VentanaTransparente" style="display:none;">
    <div class="overlay_absolute"></div>
    <div id="cargador" style="z-index:2000">
        <table width="100%" height="100%" border="0" class="fuente8">
            <tr valign="middle">
                <td> Por Favor Espere</td>
                <td>
                    <img src="<?php echo base_url();?>public/images/icons/cargando.gif?=<?=IMG;?>" border="0" title="CARGANDO"/>
                    <a href="#" id="hider2"></a>
                </td>
            </tr>
        </table>
    </div>
</div>

<form id="<?php echo $formulario;?>" method="post" action="<?php echo $url_action; ?>">
    <div id="popup" style="display: none;">
        <div class="content-popup">
            <div class="close">
            <a href="#" id="close">
            <img src="<?=base_url()?>public/images/icons/delete.gif?=<?=IMG;?>"/></a></div>
            <div>
               <h2>Falta Ingresar inventario</h2>
               <div id="contendio">
               </div>
               <a onclick="ejecutarModal()" target="_blank" href="<?=base_url()?>index.php/almacen/inventario/listar" id="btnInventario">IR A INGRESAR INVENTARIO </a>
            </div>
        </div>
    </div>
    <div id="zonaContenido" align="center" >
        <?php echo validation_errors("<div class='error'>", '</div>'); ?>
        <div id="tituloForm" class="header d-none" style="height: 20px">
            <?php echo $titulo; ?>
            <?php
            if ($tipo_docu != 'N') {
                if ($codigo == '') { ?>
                    <select id="cboTipoDocu" name="cboTipoDocu" class="comboMedio">
                        <option value="F" <?php if ($tipo_docu == 'F') echo 'selected="selected"'; ?>>FACTURA</option>
                        <option value="B" <?php if ($tipo_docu == 'B') echo 'selected="selected"'; ?>>BOLETA</option>
                    </select>
                <?php
                }else{ ?>
                	<input type="hidden" value="N" id="cboTipoDocu" name="cboTipoDocu"/>
               <?php }
            } else {
                ?>
                <input type="hidden" value="N" id="cboTipoDocu" name="cboTipoDocu"/>
            <?php } ?>
        </div>

        <div id="idDivGuiaRelacion" style="<?php echo (count($listaGuiaremAsociados)>0)?'':'display:none'; ?>">

        <div id="dialogSeriesAsociadas" title="Series Ingresadas">
          <div id="mostrarDetallesSeriesAsociadas">	
           <div id="detallesSeriesAsociadas"></div>
          </div>
        </div>
		
        <!-- dialogo para mostrarse que sleccionar el almacen de un producto -->
        <div id="dialogoSeleccionarALmacenProducto" title="Seleccionar Almacen">
            <div id="mostrarDetallesSeleecionarALmacen">	
                <table id="idTblAlmacen" >
                    <tr id="idTr_0">
                        <td></td>
                        <td width="200px" >Descripci&oacute;n</td>
                        <td width="50px">Stock</td>            	
                    </tr>
                </table>
            </div>
        </div>
        <!-- fin de dialogo -->
		
        <div id="tituloForm" class="header" style="height: 30px">
        <h3>GUIAS RELACIONADAS</h3>
        </div>
			<table class="fuente8" id="idTableGuiaRelacion">
			<tr id="idTrDetalleRelacion_0" >
				<td></td>
				<td>ITEM</td>
				<td>SERIE</td>
				<td>NUMERO</td>
				<td>BACKGROUND</td>
			</tr>
			<?php if(count($listaGuiaremAsociados)>0){ 
					foreach ($listaGuiaremAsociados as $indice=>$valorGuiarem){
							$codigoGuiarem=$valorGuiarem->codigoGuiarem;
							$serieGuiarem=$valorGuiarem->serie;
							$numeroGuiarem=$valorGuiarem->numero;
							$j=$indice+1;
							$colorGuiar[$codigoGuiarem]="#".dechex(rand(0,10000000));
							
				?>
			
		<tr id="idTrDetalleRelacion_<?php echo $j; ?>"> 
		 <td> 
		  <a href="javascript:void(0);" onclick="deseleccionarGuiaremision(<?php echo $codigoGuiarem; ?>,<?php echo $j; ?>)" title="Deseleccionar Guia de remision"> x </a> 
		 </td> 
		 <td><?php echo $j; ?></td> 
		 <td><?php echo $serieGuiarem; ?></td> 
		 <td><?php echo $numeroGuiarem; ?></td> 
		 <td><div style="width:10px;height:10px;background-color:<?php echo $colorGuiar[$codigoGuiarem] ?>; border:1px solid black"></div> 
		 	<input type="hidden" id="codigoGuiaremAsociada[<?php echo $j; ?>]"  name="codigoGuiaremAsociada[<?php echo $j; ?>]" value="<?php echo $codigoGuiarem; ?>" /> 
			<input type="text" id="accionAsociacionGuiarem[<?php echo $j; ?>]"  name="accionAsociacionGuiarem[<?php echo $j; ?>]" value="2" />
		 	<input type="hidden" id="proveedorRelacionGuiarem[<?php echo $j; ?>]"  name="proveedorRelacionGuiarem[<?php echo $j; ?>]" value="<?php echo $proveedor; ?>" />
		 </td> 
		
		 </tr> 
			<?php }} ?>
			</table>
		</div>


        <div id="frmBusqueda">
            <table class="" width="100%" cellspacing="0" cellpadding="5" border="0">
                <!-- ASOCIAR -->
                <tr class="d-none">
                    <td colspan="3" style="border-bottom: rgba(0,0,0,.5) thin solid;"></td>
                    <td valign="top" style="border-bottom: rgba(0,0,0,.5) thin solid;">
                        <label for="O" style="cursor: pointer;">
                            <img src="<?=($tipo_oper == 'V') ? base_url().'public/images/cotizacion.png?='.IMG : base_url().'images/ocompra.png?='.IMG;?>" class="imgBoton"/>
                        </label>
                        <input type="radio" name="referenciar" id="O" value="O" href="javascript:;" class="verDocuRefe" style="display:none;" data-fancybox data-type="iframe">
                        <div id="serieguiaverOC" name="serieguiaverOC" style="background-color: #cc7700; color: #fff; padding:5px; display:none; "></div>
                        <input type="hidden" name="ordencompra" id="ordencompra" size="5" value="<?=$ordencompra;?>"/>
                    </td>
                    <td valign="top" style="border-bottom: rgba(0,0,0,.5) thin solid;">
                        <label for="G" style="cursor: pointer;"><img src="<?php echo base_url() ?>public/images/icons/gremision.png?=<?=IMG;?>" class="imgBoton"/></label>
                        <input type="radio" name="referenciar" id="G" value="G" href="javascript:;" class="verDocuRefe" style="display:none;" data-fancybox data-type="iframe">
                        <input type="hidden" id="dRef"  name="dRef" value="<?php echo $dRef; ?>" >
                        <div id="serieguiaver" name="serieguiaver" style="background-color: #cc7700; color:fff; padding:5px;display:none"></div>
                    </td>
                    <td valign="top" style="border-bottom: rgba(0,0,0,.5) thin solid;">
                        <label for="R" style="display: none">
                            <img src="<?php echo base_url() ?>public/images/icons/docrecurrente.png?=<?=IMG;?>" class="imgBoton"/>
                        </label>
                        <input type="radio" name="referenciar" id="R" value="R" href="javascript:;" class="verDocuRefe" style="display:none;">
                        <span class="flecha_izquierda" id="serieguiaverRecuFlecha"></span>
                        <div id="serieguiaverRecu" name="serieguiaverRecu" class="serieguiaverRecu"></div>
                    </td>
                </tr>
                <!-- FIN ASOCIAR -->

                <tr>
                    <!--iNDEX DE FACTURA Y BOTELA-->
                    <td width="10%">Número*</td>
                    <td width="40%" valign="middle">
                    	<input type="hidden" id="guiaremision" value="<?php echo $guiaremision; ?>"/>
                    	<input type="hidden" id="posicionSeleccionadaSerie" value="" />
                    
                        <input class="cajaGeneral" name="serie" type="text" id="serie" size="3" maxlength="4" value="<?php echo $serie; ?>" <?=($tipo_oper == 'V') ? 'readonly' : '';?>/>&nbsp;
                        <input class="cajaGeneral" name="numero" type="text" id="numero" size="6" maxlength="8" value="<?php echo $numero; ?>" <?=($tipo_oper == 'V') ? 'readonly' : '';?>/>
                        <?php if ($tipo_oper == 'V') { ?>
                            <a href="javascript:;" id="linkVerSerieNum" <?php if ($codigo != '') echo 'style="display:none"' ?>>
                                <p class="boleta" style="display:none"><?php echo $serie_suger_b . '-'. $numero_suger_b ?></p>
                                <p class="factura" style="display:none"><?php echo $serie_suger_f . '-' . $numero_suger_f ?></p>
                                <p class="comprobante" style="display:none"><?php echo $serie_suger_f . '-' . $numero_suger_f ?></p>
                                <img src="<?php echo base_url(); ?>public/images/icons/flecha.png?=<?=IMG;?>" border="0" alt="Serie y nÃºmero sugerido" title="Serie y número sugerido"/>
                            </a>
                            
                            <input type="checkbox" name="numeroAutomatico"  id="numeroAutomatico" <?=($numeroAutomatico==1)?'checked=true':'';?> value="1" title="SERIE-NUMERO AUTOMATICO SI SE SELECCIONA">
                        <?php } ?>
                        <label style="margin-left:20px;">IGV</label>
                        <input name="igv" type="text" class="cajaGeneral cajaSoloLectura" id="igv" size="2" maxlength="2" value="<?=$igv;?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();" readonly="readonly"/> %
                        <!--input type="hidden" name="descuento" id="descuento" value=""/-->
                        <label hidden>
                            <input id="chk-exonera-igv" type="checkbox" <?php if($igv == 0) echo "checked";?>>Exonerar
                        </label>
                        <script>
                            $(document).ready(function () {
                                $("#chk-exonera-igv").change(function(event) {
                                    var isCheck = $(this).attr('checked'),
                                        igv = <?php echo $igv != 0 ? $igv : $igv_default; ?>;
                                    $("#igv").val(isCheck ? 0 : igv);
                                }).trigger('change');
                            });
                        </script>
                    </td>

                    <td width="10%">F. Emisión</td>
                    <td width="20%" class="text-left">
                        <input type="date" class="cajaGeneral cajaSoloLectura" id="fecha" name="fecha" value="<?=$hoy;?>">
                    </td>

                    <td width="10%" valign="middle">F. Vencimiento</td>
                    <td width="25%" valign="middle">
                        <input type="date" class="cajaGeneral cajaSoloLectura" id="fecha_vencimiento" name="fecha_vencimiento" value="<?=$fecha_vencimiento;?>">
                    </td>
                </tr>
                <tr>
                    <td><?=($tipo_oper=="V") ? "Cliente *" : "Proveedor *";?></td>
                    <td valign="middle"> <?php
                        if ($tipo_oper == "V") { ?>
                            <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>"/>
                            <input placeholder="ruc" name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" value="<?php echo $ruc_cliente; ?>" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                            <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                            <input placeholder="razon social" type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="37" maxlength="50" value="<?php echo $nombre_cliente; ?>"/>

                            <button type="button" class="btn btn-default btn-sm btn-search-sunat-comprobante">
                                    <img src="<?=$base_url;?>public/images/icons/sunat.png" class='image-size-1'/>
                                    Buscar
                            </button>
                            <span class="icon-loading-lg"></span>
                            
                            <!-- Add ingresar precio del cliente - -->
                            <?php if ( !isset($TIPCLIP_Codigo) ) $TIPCLIP_Codigo = ""; ?>
                            <input type="hidden" name="tempde_TipCli" id="TipCli" value="<?php echo $TIPCLIP_Codigo; ?>">
                            <!-- End add ingresar precio del cliente -  -->
                            <a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a> <?php
                        }
                        else { ?>
                            <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>"/>
                            <input name="buscar_proveedor" type="text" class="cajaGeneral" id="buscar_proveedor" size="10" placeholder="ruc" value="<?php echo $ruc_proveedor; ?>" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                            <input type="hidden" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>" placeholder="ruc" onkeypress="return numbersonly(this,event,'.');"/>
                            <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="25" maxlength="50" placeholder="razon social" value="<?php echo $nombre_proveedor; ?>"/>
                            <a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/" id="linkSelecProveedor"></a> <?php
                        } 
                        //$this->load->view('layout/modalClienteNuevo'); ?>
                    </td>
                    
                    
                    
                    
                    <!--td colspan="2" < ?=($tipo_oper == "C") ? "style='display:none'" : "";?>>
                        Vendedor * &nbsp;&nbsp;&nbsp;
                        <select id="cboVendedor" name="cboVendedor" class="comboMedio">
                            <option value="0"> :: SELECCIONAR :: </option>
                            < ?=$cboVendedor;?>
                        </select>
                        <input type="hidden" readonly id="VerificadoSuccess" name="VerificadoSuccess" value="<?=($tipo_oper == 'C') ? 1 : 0;?>"/>
                       <!-- <button type="button" class="btn btn-default" id="open_modal_credencial">
                            <img src='< ?=base_url();?>/images/icon-lock.png' class='image-size-1b'>
                        </button>-->
                    <!--/td-->
                    
                    <td colspan="4">
                        Forma de Pago: &nbsp;&nbsp;&nbsp;
                        <select id="forma_pago" name="forma_pago" class="comboMedio" onchange="necesitaCuota()">
                            <option value="0"> :: SELECCIONAR :: </option>
                            <?=$cboFormaPago;?>
                        </select>
                        <span class="btn btn-primary" id="btn-cuotas" data-toggle="modal" dsata-target=".modal-cuotas" style="display: none;">Cuotas</span>
                        <input id="cuotas-check" type="checkbox" name="cuotas" hidden="">
                    </td>                    
                    
                    <td class="d-none">Caja:</td>
                    <td valign="middle" style="position: relative;" class="d-none">
                        <select name="caja" id="caja" class="comboMedio">
                            <option value=""> :: SELECCIONE :: </option> <?php
                            if ($cajas != NULL){
                                foreach ($cajas as $indice => $val){ ?>
                                    <option value="<?=$val->CAJA_Codigo;?>" <?=($val->CAJA_Codigo == $caja) ? 'selected' : '';?>><?=$val->CAJA_Nombre;?></option> <?php
                                }
                            } ?>
                        </select>                        
                    </td>
                </tr>
                <tr class="d-none">
                    <td>Almacen*</td>
                    <td><?php echo $cboAlmacen; ?></td>
                    <td valign="middle">Moneda*</td>
                    <td valign="middle" id="idTdMoneda">
                        <select name="moneda" id="moneda" class="comboPequeno" style="width:150px;">
                            <?php echo $cboMoneda; ?>
                        </select>
                       <label id="textoMoneda"></label> 
                        
                        <?php if(count($listaGuiaremAsociados)>0){  ?> 
                        <script type="text/javascript">
                            $("#moneda").hide(200);
                            textoMoneda=$("#moneda option:selected").text();
                            $("#textoMoneda").html(textoMoneda);
                            $("#textoMoneda").show(200);
                        </script>
                        
                        <?php } ?>
                    </td>
                    <td colspan="2">
                        TDC
                        
                        Dolar : &nbsp;
                        <input name="tdcDolar" type="text" class="cajaGeneral cajaSoloLectura" style="width: 28px" id="tdcDolar" size="3" value="<?php echo $tdcDolar; ?>" onkeypress="return numbersonly(this,event,'.');" readonly="readonly"/>&nbsp;
                        <span id="tdcOpcional">
                            Euro : &nbsp;
                            <input name="tdcEuro" type="text" class="cajaGeneral cajaSoloLectura" style="width: 28px" id="tdcEuro" size="3" value="<?php echo $tdcEuro; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                        </span>
                    </td>
                    <script>
                        $("#moneda").change(function(event) {
                            var combo = $(this),
                                codigo = combo.val();
                            $("#tdcOpcional").css('display', codigo > 2 ? '' : 'none');
                            if(codigo > 2) $("#tdcEuro").focus();
                        });
                        $(document).ready(function () {
                            $("#moneda").trigger('change');
                        });
                    </script>
                </tr>
                 <?php if ($tipo_oper != 'C'){?>
                    <!--tr>
                        <td width="8%">
                            Dirección
                        </td>
                        <td>
                            <?php echo $direccionsuc; ?>
                            <a href="javascript:;" id="linkVerDirecciones">
                                 <img src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border="0"/>
                            </a>

                            <div id="lista_direcciones" class="cuadro_flotante" style="width:315px;">
                                <ul>
                                </ul>
                            </div>
                        </td>
                        <td colspan="4">
                            Orden de Compra:&nbsp;&nbsp;&nbsp;
                            <input style="width: 50px;" type="text" id="oc_cliente" name="oc_cliente" value="<?php echo $oc_cliente; ?>">
                        </td>
                    </tr-->

                <?php } ?>
                <?php if ($tipo_oper == 'V'){?>
                <tr class="d-none">
                    <td>Proyecto *</td>
                    <td> <?php echo $cboObra;?>	</td>
                    <td>Tipo de venta</td>
                    
                    <td ><select id="tipo_venta" name="tipo_venta"  class="comboPequeno" style="width:150px;">
                            <option value="1" <?php  if($tipo_venta=='1') echo "selected";?>>VENTA INTERNA</option>
                            <option value="2" <?php  if($tipo_venta=='2') echo "selected" ;?>>EXPORTACION</option>
                        </select>
                    </td>
                    <td colspan="3" style="text-align: left;">Descuento % &nbsp;&nbsp;&nbsp;
                        <input type="text" class="cajaMinima" id="descuento" name="descuento" size="2" maxlength="2" value="<?php echo $descuento; ?>" onKeyPress="return numbersonly(this,event,'.');" onblur="calcular_totales_tempdetalle();">
                        &nbsp;
                        <span style="display: <?php if(!$usa_adelanto == 1) echo 'none' ?>;" id="box-adelantos">
                            Adelanto : 
                            &nbsp;
                            <input type="hidden" class="cajaGeneral cajaSoloLectura" id="proyecto-adelanto" readonly style="width: 100px;text-align: right;">
                            <input type="text" class="cajaGeneral cajaSoloLectura" id="saldo-adelanto" readonly style="width: 100px;text-align: right;">
                            &nbsp;
                            <label><input type="checkbox" id="usa-adelanto" name="adelanto" <?php if($usa_adelanto == 1) echo "checked" ?>> Usar adelanto</label>
                        </span>
                    </td>
                    <td>

                    </td>
                </tr>
                <?php } else { ?>
                    <tr>                   
                        <td>Importacion*</td>
                        <td> 
                            <select name="importacion" id="importacion" class="comboGrande" style="width:150px;">
                            <?php echo $cboimportacion;?>
                        </select>
                        </td>
                        <td colspan="2" style="text-align: right;">Descuento %</td>
                        <td colspan="2">
                            <input type="text" class="cajaMinima" id="descuento" name="descuento" size="2" maxlength="2" value="<?php echo $descuento; ?>" onKeyPress="return numbersonly(this,event,'.');" onblur="calcular_totales_tempdetalle();">
                            &nbsp;
                            <span style="display: <?php if(!$usa_adelanto == 1) echo 'none' ?>;" id="box-adelantos">
                                Adelanto : 
                                &nbsp;
                                <input type="hidden" class="cajaGeneral cajaSoloLectura" id="proyecto-adelanto" readonly style="width: 100px;text-align: right;">
                                <input type="text" class="cajaGeneral cajaSoloLectura" id="saldo-adelanto" readonly style="width: 100px;text-align: right;">
                                &nbsp;
                                <label><input type="checkbox" id="usa-adelanto" name="adelanto" <?php if($usa_adelanto == 1) echo "checked" ?>> Usar adelanto</label>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <div id="frmBusqueda"  <?php echo $hidden; ?> class="box-add-product" style="text-align: right;" >
            <a href="#" id="addItems" name="addItems" style="color:#ffffff;" class="btn btn-primary" onclick="agregar_items(); ">Agregar Items</a></td>
        </div>
        
       <!-- LISTADO DE GUIAS ASOCIADAS  -->
        
       <!-- FIN DE LISTADO DE GUIAS ASOCIADAS --> 
       <!-- TABLA DETALLE DE TEMPORAL -->
        <?php $this->load->view('maestros/temporal_subdetalles'); ?>

       <!-- FIN DE TABLA TEMPORAL DETALLE -->

        <div id="frmBusqueda3">
            <table width="100%" border="0" align="right" cellpadding="0" cellspacing="0" class="fuente8">
                <tr>
                    <td width="80%" rowspan="10" align="left" valign="top">
                        <table width="100%" border="0" align="right" cellpadding="3" cellspacing="0" class="fuente8" style="width: 736px;">
                            <tr style="display: none">
                                <td width="14%" height="30"></td>
                                <td width="50%">
                                    <input hidden class="cajaGeneral" name="docurefe_codigo" type="text" id="docurefe_codigo" size="14" maxlength="26" value="<?php echo $docurefe_codigo; ?>"/>
                                </td>
                                <td width="7%" style="display: none;">Estado</td>
                                <td style="display: none;">
                                     <input type="hidden" name="estado" id="estado"  value="<?php echo $estado; ?>" />
                                </td>
                            </tr>

                            <tr class="d-none">
                                <td>Retención:</td>
                                <td colspan="2">
                                    <div class="form-group">
                                        <input type="hidden" value="false" name="applyRetencion_hidden">
                                        <input id="applyRetencion" name="applyRetencion" type="checkbox" value="1" style="display: none;">
                                        <div class="Switch Round On applyRetencion" style="vertical-align:bottom; margin-left:10px;"><div class="Toggle"></div></div>

                                        <div class="info-retencion" style="margin-left: 2em; display: none;">
                                            <input type="text" id="retencion_codigo" name="retencion_codigo" value="<?=$codigoRetencion;?>" placeholder="Código de retención" class="cajaMedia">
                                            &nbsp;&nbsp;&nbsp;
                                            <input type="number" id="retencion_porc" name="retencion_porc" value="<?=isset($porcRetencion) ? $porcRetencion : '0';?>" placeholder="3" class="cajaPequena" min="0" step="0.1"> %
                                            &nbsp;&nbsp;&nbsp;
                                            Total en retención: <span class="importe_retencion_span"></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!--tr>
                                <td style="width: 10em;">Forma de Pago:</td>
                                <td>
                                    <select name="forma_pago" id="forma_pago" class="comboMedio" onchange="necesitaCuota()"><?php echo $cboFormaPago; ?></select>
                                </td>
                                <td>
                                    <span class="btn btn-primary" id="btn-cuotas" data-toggle="modal" data-target=".modal-cuotas" style="display: none;">Cuotas</span>
                                    <input id="cuotas-check" type="checkbox" name="cuotas" hidden="">
                                </td>
                            </tr-->
                            
                            <tr <?=($tipo_oper == "C") ? "style='display:none'" : "";?> class="d-none">
                                <td style="width: 10em;">Vendedor: *</td>
                                <td>
                                    <select name="cboVendedor" id="cboVendedor" class="comboMedio"><?php echo $cboVendedor; ?></select>
                                </td>
                                <td>
                                <input type="hidden" readonly id="VerificadoSuccess" name="VerificadoSuccess" value="<?=($tipo_oper == 'C') ? 1 : 0;?>"/>
                                </td>
                            </tr>                                                 

                            <tr class="d-none">
                                <td>Observación</td>
                                <td colspan="2">
                                    <textarea id="observacion" name="observacion" class="cajaTextArea" style="width:97%; height:70px;"><?php echo $observacion; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <tr class="d-none">
                        <td class="busqueda">Descuento</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="descuentotal" type="text" id="descuentotal" size="12" align="right" readonly="readonly" value="<?php echo round($descuentotal, 2); ?>"/></div>
                        </td>
                    </tr>
                    <tr class="d-none">
                        <td class="busqueda">Exonerada</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="exoneradototal" type="text" id="exoneradototal" size="12" align="right" readonly="readonly" value="<?=(isset($exoneradototal)) ? round($exoneradototal, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr class="d-none">
                        <td class="busqueda">Inafecta</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="inafectototal" type="text" id="inafectototal" size="12" align="right" readonly="readonly" value="<?=(isset($inafectototal)) ? round($inafectototal, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr class="d-none">
                        <td class="busqueda">Gratuita</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="gratuitatotal" type="text" id="gratuitatotal" size="12" align="right" readonly="readonly" value="<?=(isset($gratuitatotal)) ? round($gratuitatotal, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr style="display: anone"> <!--Important-->
                        <td>Sub-total</td>
                        <td width="10%" align="top">
                            <div align="right"><input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" readonly="readonly" value="<?php echo round($preciototal, 2); ?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Gravada</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="gravadatotal" type="text" id="gravadatotal" size="12" align="right" readonly="readonly" value="<?=(isset($gravada)) ? round($gravada, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">IGV</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="igvtotal" type="text" id="igvtotal" size="12" align="right" readonly="readonly" value="<?php echo round($igvtotal, 2); ?>"/></div>
                        </td>
                    </tr>
                    <tr class="d-none">
                        <td class="busqueda">Impuesto a la Bolsa Plástica</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" id="importeBolsa" name="importeBolsa" type="text" size="12" align="right" readonly="readonly" value="0"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Importe Total</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="importetotal" type="text" id="importetotal" size="12" align="right" readonly="readonly" value="<?php echo round($importetotal, 2); ?>"/></div>
                        </td>
                    </tr>
                    <tr class="importe_retencion_tr" style="display: none;">
                        <td class="busqueda">Importe - Retención</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales importe_retencion" type="text" size="12" align="right" readonly="readonly" value=""/></div>
                        </td>
                    </tr>
            </table>
        </div>


    <div id="botonBusqueda2">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="visibility: hidden"/>
        <?php if($estado == 2): ?>
        <a href="javascript:;" id="grabarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <?php endif; ?>
        <!--a href="javascript:;" id="limpiarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a-->
        <?php  if($btnCancelVisible == 'S'){?>
        <a href="javascript:;" id="cancelarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <?php }?>
        <input type="hidden" name="salir" id="salir" value="0"/>
        <?=$oculto;?>
    </div>
</div>


<!-- MODAL CUOTAS -->
    <div class="modal fade modal-cuotas" tabindex="-1" role="dialog" style="width: 50%">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Cuotas</h3>
                </div>
                <div class="modal-body">
                    <div>
                        <button type="button" class="btn btn-danger del-cuota">-</button>
                            <input type="number" min="0" step="1" id="cant-cuotas" name="cant-cuotas" class="form-control" style="display: inline-block; width: 2em; padding: 0 12px;" value="<?=count($lista_cuotas);?>" readOnly>
                        <button type="button" class="btn btn-success add-cuota">+</button>
                    </div>
                    <div>
                        <table id="tbl-cuotas" width="100%" value="1" class="table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div> <?php

                if($estado == 2) : ?>
                    <div class="modal-footer" id="barrainf">
                        Total cuotas : <span id="suma-cuotas"><?=number_format($monto_cuotas, 2);?></span>
                        &nbsp;&nbsp;
                        <?php if(!isset($lista_cuotas)): ?>
                            <button type="button" class="btn btn-default btn-cuota-cancel">Cancelar / Borrar</button>
                        <?php endif; ?>
                            <button type="button" class="btn btn-info btn-cuota-recalc">Recalcular</button>
                            <button type="button" class="btn btn-primary btn-cuota-acept">Aceptar</button>
                    </div> <?php
                endif; ?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
<!-- /.modal -->

<div id="add_credencial" class="modal fade" role="dialog">
    <div class="modal-dialog w-porc-60">
        <div class="modal-content">
            <form id="formCredencial" method="POST">
                <div class="modal-header text-center">
                    <h4 class="modal-title">
                        <b>CREDENCIAL DEL VENDEDOR</b>
                    </h4>
                </div>
                <div class="modal-body panel panel-default">
                    <div class="row form-group">
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <b>VENVEDOR:</b>
                        </div>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <span id='modal_vendedor_span'></span>
                        </div>
                    </div>
                    <br>
                    <div class="row form-group">
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <label for="credencial_usuario">USUARIO *</label>
                        </div>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <input type="text" id="credencial_usuario" name="credencial_usuario" class="form-control h-2" placeholder="Nombre de usuario" value="" autocomplete="off">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1" id="getUsuario">
                            <span class="icon-loading"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row form-group">
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <label for="credencial_password">CONTRASEÑA *</label>
                        </div>
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <input type="password" id="credencial_password" name="credencial_password" class="form-control h-2" placeholder="Contraseña" value="" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" accesskey="x" id="verificarCredenciales">Verificar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                </div>
            </form>
        </div>
    </div>
</div>




<style type="text/css">
    #popup {
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 1001;
    }

    .content-popup {
        margin:0px auto;
        margin-top:150px;
        position:relative;
        padding:10px;
        width:300px;
        min-height:150px;
        border-radius:4px;
        background-color:#FFFFFF;
        box-shadow: 0 2px 5px #666666;
    }

    .content-popup h2 {
        color:#48484B;
        border-bottom: 1px solid #48484B;
        margin-top: 0;
        padding-bottom: 4px;
    }

    .popup-overlay {
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 999;
        display:none;
        background-color: #777777;
        cursor: pointer;
        opacity: 0.7;
    }

    .close {
        position: absolute;
        right: 15px;
    }
    #btnInventario{
        size: 20px;
        width: 200px;
        height: 50px;
        border-radius: 33px 33px 33px 33px;
        -moz-border-radius: 33px 33px 33px 33px;
        -webkit-border-radius: 33px 33px 33px 33px;
        border: 0px solid #000000;
        background-color:rgba(199, 255, 206, 1);
    }
</style>

</form>

<?php $this->load->view('maestros/temporal_detalles'); ?>

<script>
    /* PRIMARY */
        $(document).ready(function () { <?php
            if ($tipo_oper == 'V'){
                switch ($tipo_docu) {
                    case 'F': ?> 
                        setLimite(<?=VENTAS_FACTURA;?>); <?php
                        break;
                    case 'B': ?>
                        setLimite(<?=VENTAS_BOLETA;?>); <?php
                        break;
                    case 'N': ?>
                        setLimite(<?=VENTAS_COMPROBANTE;?>); <?php
                        break;
                    default:
                        break;
                }
            }
            else
                if ($tipo_oper == 'C') {
                    switch ($tipo_docu) {
                        case 'F': ?>
                            setLimite(<?php echo COMPRAS_FACTURA; ?>); <?php
                            break;
                        case 'B': ?>
                            setLimite(<?php echo COMPRAS_BOLETA; ?>); <?php
                            break;
                        default:
                            break;
                    }
                } ?>

            /**dialogo series asosicadas**/
                $("#dialogSeriesAsociadas").dialog({
                    resizable: false,
                    height: "auto",
                    width: 400,
                    autoOpen: false,
                    show: {
                      effect: "blind",
                      duration: 500
                    },
                    hide: {
                      effect: "blind",
                      duration: 500
                    }
                });
            /**fin **/

            /**dialogo series asosicadas**/
                $("#dialogoSeleccionarALmacenProducto").dialog({
                    resizable: false,
                    height: "auto",
                    width: 400,
                    autoOpen: false,
                    show: {
                      effect: "blind",
                      duration: 500
                    },
                    hide: {
                      effect: "blind",
                      duration: 500
                    },
                    buttons: {
                        "Aceptar": function() {
                            grabarSeleccionarAlmacen();
                        },
                        Cancel: function() {
                          $(this).dialog( "close" );
                        }
                      }
                });
            /**fin **/

            /***verificacion de si es editar y esta relacionada con otras guias **/
                <?php
                    if(count($listaGuiaremAsociados)>0){  ?>
                        document.getElementById("tempde_producto").readOnly = true;
                        $("#addItems").hide(200);
                <?php } ?>
            /***fin de realizar verificacion**/
            
            /**ejecutar mostrar orden de compra vista si existe**/
            <?php if($ordencompra!=0 &&  trim($ordencompra)!="" && $ordencompra!=null){   ?>
            //mostrarOdenCompraVista(<?php echo $ordencompra.",".$serieOC.",".$numeroOC.",". $valorOC; ?>);
            <?php } ?>
            /**no mostrar**/
            /**ejecutar mostrar PRESUPUESTO vista si existe**/
            <?php if($presupuesto_codigo!=0 &&  trim($presupuesto_codigo)!="" && $presupuesto_codigo!=null){   ?>
            mostrarPresupuestoVista(<?php echo $presupuesto_codigo.",'".$seriePre."',".$numeroPre.",'". $tipo_oper."'"; ?>);7
            <?php } ?>
            /**no mostrar**/
            
            
            
            if ($('#tdcDolar').val() == '') {
                //alert("Antes de registrar comprobantes debe ingresar Tipo de Cambio");
                Swal.fire({
                    icon: "info",
                    title: "Antes de registrar comprobantes debe ingresar Tipo de Cambio",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    timer: 1500
                });
                top.location = "<?php echo base_url(); ?>index.php/index/inicio";
            }

            base_url = $("#base_url").val();
            tipo_oper = $("#tipo_oper").val();
            almacen = $("#cboCompania").val();

            $(".verDocuRefe").click(function(){
                    tipoMoneda=$("#moneda").val();
                    almacen=$("#almacen").val();
                    if (tipo_oper == 'V') {
                        if ($('#cliente').val() == '') {
                            //alert('Debe seleccionar el cliente.');
                            Swal.fire({
                                icon: "info",
                                title: "Debe seleccionar el cliente.",
                                html: "<b class='color-red'></b>",
                                showConfirmButton: true,
                                timer: 1500
                            });
                            $('#nombre_cliente').focus();
                            return false;
                        }
                        else {
                            if ($(".verDocuRefe::checked").val() == 'G')
                                baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_guiarem/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/G/'+tipoMoneda;
                            else if ($('.verDocuRefe::checked').val() == 'P')
                                baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                            else if ($('.verDocuRefe::checked').val() == 'O')
                                baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompraCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/O';
                            else if ($('.verDocuRefe::checked').val() == 'R')
                                baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/R';
                         
                            $('.verDocuRefe::checked').attr('href', baseurl);
                        }
                    }
                    else {

                        if ($('#proveedor').val() == '') {
                            //alert('Debe seleccionar el proveedor.');
                            Swal.fire({
                                icon: "info",
                                title: "Debe seleccionar el proveedor.",
                                html: "<b class='color-red'></b>",
                                showConfirmButton: true,
                                timer: 1500
                            });
                            $('#nombre_proveedor').focus();
                            return false;
                        }
                        else {
                            if ($('.verDocuRefe::checked').val() == 'G')
                                baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_guiarem/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/G/'+tipoMoneda;
                            else
                                if ($('.verDocuRefe::checked').val() == 'P') {
                                    if (tipo_oper == 'V')
                                        baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                                    else
                                        baseurl = base_url + 'index.php/compras/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                                }
                            else
                                if ($('.verDocuRefe::checked').val() == 'O')
                                    baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompraCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/O';
                            else
                                if ($('.verDocuRefe::checked').val() == 'R')
                                    baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/R';

                            $('.verDocuRefe::checked').attr('href', baseurl);
                        }
                    }
            });
        });

        $(function () {
            // BUSQUEDA POR RAZON SOCIAL O CODIGO
            $("#nombre_cliente").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                        type: "POST",
                        data: {term: $("#nombre_cliente").val()},
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },

                select: function (event, ui) {
                    $("#nombre_cliente").val(ui.item.nombre);
                    $("#buscar_cliente").val(ui.item.ruc);
                    $("#cliente").val(ui.item.codigo);
                    $("#ruc_cliente").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    $("#TipCli").val(ui.item.TIPCLIP_Codigo);
                    $("#cboVendedor > option[value="+ ui.item.vendedor +"]").attr("selected",true) // Selecciona el vendedor asociado al cliente - 

                    if ( ui.item.contactos != null ){
                        var size = ui.item.contactos.length;
                        $('#contacto option').remove();

                        for (x = 0; x < size; x++){
                            $('#contacto').append("<option value='"+ui.item.contactos[x].ECONC_Contacto+"'>"+ui.item.contactos[x].ECONC_Descripcion+"</option>");
                        }
                    }
                    get_obra(ui.item.codigo);
                },
                minLength: 2
            });

            // BUSQUEDA POR RUC
            $("#buscar_cliente").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                        type: "POST",
                        data: {
                            term: $("#buscar_cliente").val()
                        },
                        dataType: "json",
                        success: function (data) {
                            if (data.length == 0){
                                $(".input-group-btn").css("opacity",1);
                        }
                            else{
                                $(".input-group-btn").css("opacity",0);
 
                                response(data);
                            }
                        }
                    });
                },
                select: function (event, ui) {
                    $("#nombre_cliente").val(ui.item.nombre);
                    $("#buscar_cliente").val(ui.item.ruc);
                    $("#cliente").val(ui.item.codigo);
                    $("#ruc_cliente").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    $("#TipCli").val(ui.item.TIPCLIP_Codigo); // Codigo del cliente para el precio del producto - 
                    $("#cboVendedor > option[value="+ ui.item.vendedor +"]").attr("selected",true) // Selecciona el vendedor asociado al cliente - 

                    if ( ui.item.contactos != null ){
                        var size = ui.item.contactos.length;
                        $('#contacto option').remove();

                        for (x = 0; x < size; x++){
                            $('#contacto').append("<option value='"+ui.item.contactos[x].ECONC_Contacto+"'>"+ui.item.contactos[x].ECONC_Descripcion+"</option>");
                        }
                    }
                    get_obra(ui.item.codigo);
                    //$("#addItems").click();
                },
                minLength: 2
            });
            
            $("#buscar_cliente").change(function(){
                if ($("#buscar_cliente").val().length == 0)
                    $(".input-group-btn").css("opacity",0);
            });

            // BUSQUEDA POR RAZON SOCIAL PROVEEDOR
            $("#nombre_proveedor").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",
                        type: "POST",
                        data: { term: $("#nombre_proveedor").val() },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    $("#buscar_proveedor").val(ui.item.ruc);
                    $("#nombre_proveedor").val(ui.item.nombre);
                    $("#proveedor").val(ui.item.codigo);
                    $("#ruc_proveedor").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                },
                minLength: 2
            });

            // BUSQUEDA POR RUC PROVEEDOR
            $("#buscar_proveedor").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete_ruc/",
                        type: "POST",
                        data: {
                            term: $("#buscar_proveedor").val()
                        },
                        dataType: "json",
                        success: function (data) {
                            if (data.length == 0)
                                $(".input-group-btn").css("opacity",1);
                            else{
                                $(".input-group-btn").css("opacity",0);
                                response(data);
                            }
                        }
                    });
                },
                select: function (event, ui) {
                    $("#buscar_proveedor").val(ui.item.ruc);
                    $("#nombre_proveedor").val(ui.item.nombre);
                    $("#proveedor").val(ui.item.codigo);
                    $("#ruc_proveedor").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);

                    $("#addItems").click();
                },
                minLength:2 
            });

            // Credenciales del vendedor
            $("#cboVendedor").change(function(){
                /*$("#VerificadoSuccess").val("0");
                var image = "<img src='<?=base_url();?>/images/icon-lock.png' class='image-size-1b'>";
                $("#open_modal_credencial").html(image);
                
                if ( $("cboVendedor").val() != "0" ){
                    $("#modal_vendedor_span").html($("#cboVendedor option:selected").text());
                    $("#add_credencial").modal("toggle");
                }

                url = "<?=base_url();?>index.php/seguridad/usuario/getUserPers";
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    url: url,
                    data: { vendedor: $(this).val() },
                    beforeSend: function(){
                        $("#getUsuario .icon-loading").show();
                        $("#credencial_usuario").attr({"readOnly": "readOnly"});
                    },
                    success:function(data){
                        if (data.match == true){
                            $("#credencial_usuario").val(data.nombre);
                            $("#credencial_password").val("");
                        }
                    },
                    complete: function(){
                        $("#getUsuario .icon-loading").hide();
                        $("#credencial_usuario").removeAttr("readOnly");
                    },
                });*/
            });

            // Credenciales del vendedor
            $("#open_modal_credencial").click(function(){
                vendedor = $("#cboVendedor").val();

                if (vendedor != "0")
                    $("#add_credencial").modal("toggle");
                else{
                        Swal.fire({
                            icon: "info",
                            title: "Debe seleccionar un vendedor primero.",
                            html: "<b class='color-red'></b>",
                            showConfirmButton: true,
                            timer: 3000
                        });
                }
            });

            // Credenciales del vendedor
            $("#verificarCredenciales").click(function(){
                vendedor = $("#cboVendedor").val();
                credencial_usuario = $("#credencial_usuario").val();
                credencial_password = $("#credencial_password").val();

                url = "<?=base_url();?>index.php/seguridad/usuario/credencialVendedor";
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    url: url,
                    data:{
                        vendedor: vendedor,
                        usuario: credencial_usuario,
                        password: credencial_password
                    },
                    success:function(data){
                        if (data.match == true){
                            $("#VerificadoSuccess").val("1");
                            var image = "<img src='<?=base_url();?>/images/icono_aprobar.png' class='image-size-1b'>";
                            $("#open_modal_credencial").html(image);
                            
                            Swal.fire({
                                icon: "success",
                                title: "Credenciales aceptadas.",
                                html: "<b class='color-red'></b>",
                                showConfirmButton: true,
                                timer: 3000
                            });
                            
                            $("#add_credencial").modal("hide");
                        }
                        else{
                            $("#VerificadoSuccess").val("0");
                            $("#open_modal_credencial").html(image);
                            var image = "<img src='<?=base_url();?>/images/icon-lock.png' class='image-size-1b'>";
                            $("#open_modal_credencial").html(image);

                            Swal.fire({
                                icon: "error",
                                title: "Credenciales invalidas.",
                                html: "<b class='color-red'>" + data.mensaje + "</b>",
                                showConfirmButton: true,
                                timer: 5000
                            });
                        }
                    }
                });
            });

            $("#fecha").change(function(){
                tdc_cambiar();
            });

            $("#fecha_vencimiento").change(function(){
                tdc_cambiar();
            });

            $("#moneda").change(function(){
                tdc_cambiar();
            });
        });

        $("#linkVerproyectoss").click(function () {
            if (tipo_oper == 'V')
                var url = base_url + "index.php/maestros/proyecto/JSON_listar_proyectos/" +$("#cliente").val();
            $("#lista_proyecto ul").html('');
            $("#lista_proyecto").slideToggle("fast", function () {
                $.getJSON(url, function (data) {
                    $.each(data, function (i, item) {
                        fila = '';
                            fila += '<li><a href="javascript:;">';
                            if (item.nombre != '')
                                fila += ' ' + item.nombre;
                            if (item.descripcion != '')
                                fila += ' - ' + item.descripcion;
                            fila += '</a></li>';
                      $("#lista_proyecto  ul").append(fila);
                    });
                });
            });
        });

        $('a').bind('click', function(){
          window.last_clicked_time = new Date().getTime();
          window.last_clicked = $(this);
        });

        $(window).bind('beforeunload', function() {
            if ( $("#salir").val() == 0 ){
              var time_now = new Date().getTime();
              var link_clicked = window.last_clicked != undefined;
              var within_click_offset = (time_now - window.last_clicked_time) < 100;

              if (link_clicked && within_click_offset) {
                return 'You clicked a link to '+window.last_clicked[0].href+'!';
              } else {
                return 'Estas abandonando la página!';
              }
            }
        });

        function seleccionarOdenCompra(oCompra){
            mostrarOdenCompraVista(oCompra);
            // obtener_detalle_ocompra(oCompra);
            obtener_comprobantes_temproductos(oCompra,'ocompras');
            // Calcular totales
            calcular_totales_tempdetalle();

            /**quitamos lista de guiarem **/
            listadoGuiaremEstadoDeseleccionado();
            verificarOcultarListadoGuiaremAsociado();
        }
        

        function mostrarOdenCompraVista(oCompra){
            $.ajax({
                url: "<?=base_url();?>index.php/compras/ocompra/relacionar_oc",
                type: "POST",
                data: {
                    ocompra: oCompra
                },
                dataType: "json",
                beforeSend: function(data){
                    $("#cboVendedor option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                    $("#moneda option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                    $("#obra option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                },
                success: function (data) {
                    if (data.result == "success"){
                        $('#ordencompra').val(data.info.ocompra);
                        $('#direccionsuc').val(data.info.direccion);
                        $('#oc_cliente').val(data.info.OCcliente);
                        $('#descuento').val(data.info.descuento);
                        
                        if(data.info.operacion == 1)
                            $("#serieguiaverOC").html("Orden de compra número: " + data.info.serie + " - " + data.info.numero);
                        else
                            $("#serieguiaverOC").html("Orden de venta número: " + data.info.serie + " - " + data.info.numero);

                        if (data.info.vendedor != "" && data.info.vendedor != null)
                            $("#cboVendedor > option[value="+data.info.vendedor+"]").attr("selected",true);

                        if (data.info.moneda != "" && data.info.moneda != null)
                            $("#moneda > option[value="+data.info.moneda+"]").attr("selected",true);

                        if (data.info.forma_pago != "" && data.info.forma_pago != null)
                            $("#forma_pago > option[value="+data.info.forma_pago+"]").attr("selected",true);

                        if (data.info.proyecto != "" && data.info.proyecto != null)
                            $("#obra > option[value="+data.info.proyecto+"]").attr("selected",true);

                        $("#serieguiaverOC").show(200);
                        $("#serieguiaverPre").hide(200);
                        $("#serieguiaver").hide(200);
                        $("#serieguiaverRecu").hide(200);
                    }
                },
                complete: function(data){
                    codigoPresupuesto = $("#presupuesto_codigo").val();
                    if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                        modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
                    }
                    $("#presupuesto_codigo").val("");
                }
            });
        }

        function seleccionar_guiarem(guia, serieguia, numeroguia, ocCliente = '') {
            realizado = verificar_agregar(guia);

            if(realizado!=false){
                obtener_comprobantes_temproductos(guia,'guiarem');
                if (ocCliente != '')
                    $("#oc_cliente").val(ocCliente);
                
                $("#serieguiaverPre").hide(200);
                $("#serieguiaverOC").hide(200);
                $("#serieguiaverRecu").hide(200);
                $('#ordencompra').val('');
            }

            codigoPresupuesto=$("#presupuesto_codigo").val();
            if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                    modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
            }
            $("#presupuesto_codigo").val("");
        }

        function seleccionar_presupuesto(guia, serieguia, numeroguia) {
            isRealizado=modificarTipoSeleccionPrersupuesto(guia,1);
            if(isRealizado){
                tipo_oper = $("#tipo_oper").val();
                //agregar_todopresupuesto(guia, tipo_oper);
                obtener_comprobantes_temproductos(guia,'presupuesto');
                mostrarPresupuestoVista(guia, serieguia, numeroguia,tipo_oper);
                /**quitamos lista de guiarem **/
                listadoGuiaremEstadoDeseleccionado();
                verificarOcultarListadoGuiaremAsociado();
            }
        }

        function mostrarPresupuestoVista(guia, serieguia, numeroguia,tipo_oper){
            if(tipo_oper=="V")
                serienumero = "NÚMERO DE PRESUPUESTO :" + serieguia + " - " + numeroguia;
            else
                serienumero = "NÚMERO DE COTIZACIÓN :" + serieguia + " - " + numeroguia;
                
            $("#serieguiaverPre").html(serienumero);
            $("#serieguiaverPre").show(200);
            $("#serieguiaver").hide(200);
            $("#serieguiaverOC").hide(200);
            $("#serieguiaverRecu").hide(200);
            $("#docurefe_codigo").val('');
            $("#dRef").val('');
            $('#ordencompra').val('');
            $("#numero_ref").val('');
            $("#presupuesto_codigo").val(guia);
        }
        
        function seleccionar_comprobante_recu(guia, serieguia, numeroguia) {
            obtener_comprobantes_temproductos(guia,'comprobantes');
            serienumero = "N° de Comprobante: <br>" + serieguia + " - " + numeroguia;
            $("#serieguiaverRecu").html('<span style="font-size:15px" >Doc. Recurrente: <br>' + serienumero + '</span>');
            $("#serieguiaverRecu").show(200);
            $("#serieguiaver").hide(200);
            $("#serieguiaverRecuFlecha").show(400);
            $("#serieguiaverPre").hide(200);
            $("#serieguiaverOC").hide(200);
            $("#numero_ref").val('');
            $("#dRef").val('');
            $('#ordencompra').val('');
            $("#docurefe_codigo").val('');

            codigoPresupuesto=$("#presupuesto_codigo").val();
            if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
            }
            $("#presupuesto_codigo").val("");

            /**quitamos lista de guiarem **/
            listadoGuiaremEstadoDeseleccionado();
            verificarOcultarListadoGuiaremAsociado();
        }

        function tdc_cambiar() {
            fecha_emision = new Date( $("#fecha").val() );
            fecha_vencimiento = new Date( $("#fecha_vencimiento").val() );
            if ( fecha_emision.getTime() > fecha_vencimiento.getTime() )
                $("#fecha_vencimiento").val( $("#fecha").val() );
            if ( $('#moneda').val() != 1 ){
                $.ajax({
                    url: "<?php echo base_url();?>index.php/maestros/tipocambio/buscar_json",
                    type: "POST",
                    dataType: "json",
                    data: {
                        fecha: $('#fecha').val(),
                        moneda: $('#moneda').val()
                    },
                    success: function (data) {
                        if (data.match == false) {
                            Swal.fire({
                                    icon: "info",
                                    title: "La fecha seleccionada no tiene registrado el tipo de cambio.",
                                    showConfirmButton: true,
                                    timer: 2500
                            });

                            $("#grabarComprobante").hide();
                        }
                        else {
                            $('#tdc').val(data.tdc);
                            $("#grabarComprobante").show();
                        }
                    }
                });
            }
            else{
                    $("#grabarComprobante").show();
            }
        }

        function get_obra(codigo) {
            $.post("<?php echo base_url(); ?>index.php/compras/pedido/obra", {
                    "codigoempre" : codigo
                },
                function(data) {
                    var c = JSON.parse(data);
                    $('#obra').html('');
                    $('#obra').append("<option value='0'>::Seleccione::</option>");
                    $.each(c,function(i,item){
                        $('#obra').append("<option value='"+item.PROYP_Codigo+"'>"+item.proyecto+"</option>");
                    });

                    var idProyecto = $("#id-proyecto").val();
                    if(idProyecto != "") $("#obra").val(idProyecto).trigger('change');
                }
            );
        }

    /* SECONDS */

    var totalAmountOrden = 0;
    $("#moneda").change(function () {
        var combo = $(this),
            codigo = combo.val();

        $("#tdcOpcional").css('display', codigo > 2 ? '' : 'none');

        if(codigo > 2) $("#tdcOpcional").focus();
    });

    $(function() {
        $("#moneda").trigger('change');
        
        $("#obra").change(function (event) {
            var value = event.target.value;
        }).trigger('change');
        
        $("#usa-adelanto").change(function (event) {
            var isCheck = $(event.target).attr('checked');
            var adelanto = $("#proyecto-adelanto").val();
            var descuento = isCheck ? $("#descuentotal").val() : 0;
            $("#saldo-adelanto").val(parseFloat(adelanto - descuento).format());
            $("#descuento").val(isCheck ? descuentoPercent : 0).trigger("blur");
        });

        $(".applyRetencion").click(function(){
            if ( $("#applyRetencion").is(":checked") == true ){
                $(".info-retencion").css({"display":"inline-block"});
                $(".importe_retencion_tr").show();

                importeRetencion = $("#gravadatotal").val() * $("#retencion_porc").val() / 100;
                importetotal = $("#importetotal").val() - importeRetencion;

                $(".importe_retencion_span").html( importeRetencion.toFixed(2) );
                $(".importe_retencion").val( importetotal.toFixed(2) );
            }
            else{
                $(".importe_retencion_tr").hide();
                $("#retencion_codigo").val("");
                $("#retencion_porc").val("");
                $(".info-retencion").css({"display":"none"});
            }
        });

        $(".add-cuota").click(function(){
            $("#cant-cuotas").val( parseInt( $("#cant-cuotas").val() ) + 1 );
            cant_cuotas();
        });

        $(".del-cuota").click(function(){
            $("#cant-cuotas").val( parseInt( $("#cant-cuotas").val() ) - 1 );
            cant_cuotas();
        });

        $(".btn-cuota-recalc").click(function(){
            cuota_total(true);
        });

        $(".btn-cuota-cancel").click(function(){
            $("#cant-cuotas").val(0);
            $("#tbl-cuotas tbody").html("");
            $(".modal-cuotas").modal("hide");
        });

        $(".btn-cuota-acept").click(function(){
            cuotas = $("#cant-cuotas").val();
            i = cuotas - 1;
            $("#fecha_vencimiento").val( $(".cuota-fechaf" + i).val() );

            $(".modal-cuotas").modal("hide");
        });

        $("#retencion_porc").change(function(){
            importeRetencion = $("#gravadatotal").val() * $("#retencion_porc").val() / 100;
            importetotal = $("#importetotal").val() - importeRetencion;

            $(".importe_retencion_span").html( importeRetencion.toFixed(3) );
            $(".importe_retencion").val( importetotal.toFixed(2) );
        });

        retencion(<?=$codigoRetencion;?>);
        necesitaCuota();
    });

    function consultarAdelantos($id) {
        $.getJSON("<?php echo base_url() ?>index.php/maestros/proyecto/get_adelantos_saldo/"+$id+"/"+tipo_oper, {}, function(json, textStatus) {
                if(textStatus == 'success' && json.porcentaje > 0) {
                    $("#box-adelantos").show();
                    
                    if("insertar" == "<?php echo $modo ?>") {
                        $("#usa-adelanto").attr('checked', 'checked');
                        descuentoPercent = json.porcentaje;
                        $("#descuento").val(descuentoPercent);
                    }
                    $("#proyecto-adelanto").val(json.saldo_dolares.format(false));
                    //$("#usa-adelanto").trigger('change');
                    $("#descuento").trigger('blur');
                }
            });
    }

    function retencion( act = "" ){
        if (act != ""){
            $(".applyRetencion").click();
            $(".info-retencion").css({"display":"inline-block"});
            $(".importe_retencion_tr").show();

            importeRetencion = parseFloat($("#gravadatotal").val()) * parseFloat($("#retencion_porc").val()) / 100;
            importetotal = $("#importetotal").val() - importeRetencion;

            $(".importe_retencion_span").html( importeRetencion.toFixed(2) );
            $(".importe_retencion").val( importetotal.toFixed(2) );
        }
    }

    function verificarPorcentaje(evt) {
        var value = evt.target.value.toLowerCase();

        if(/[0-9\.]{1,}(?=\%)/.test(value)) {

            if(tipo_oper == 'C') {
                var porcentaje = parseFloat(value.match(/[0-9\.]{1,}(?=\%)/));
                var puTag = document.getElementById("prodpu[0]");

                if(porcentaje > 100 || porcentaje < 1) {
                    alert("El porcentaje no es correcto");
                    evt.target.focus();
                }else{
                    puTag.value = (totalAmountOrden * (porcentaje / 100)).toFixed(2);
                    puTag.focus();
                    puTag.blur();
                }
            }
        }else {
            alert("Debe ingresar un porcentaje en la descripcion del adelanto.");
            evt.target.focus();
        }
    }

    function necesitaCuota() {
        var _this = $("#forma_pago");
        var cuotasCheck = $("#cuotas-check");
        var currentText = document.getElementById('forma_pago').options[document.getElementById('forma_pago').options.selectedIndex].innerText.toLowerCase();
        var requiereCuota = /cuota|credito/g.test(currentText);
        $("#btn-cuotas").css('display', requiereCuota ? '' : 'none');
        
        if(!requiereCuota)
            cuotasCheck.removeAttr('checked');

        view_coutas(<?=$codigo;?>);
    }

    function cant_cuotas(){
        cuotas = $("#cant-cuotas").val();
        var fecha = "<?=date('Y-m-d');?>";

        cantidadA = $(".cantidad-cuotas").length;
        if ( cantidadA > cuotas ){
            $(".cantidad-cuotas:last-child").remove();
            $(".cantidad-cuotas:last-child .cuota-fechaf").removeAttr("onchange");
        }
        else{
                i = cuotas - 1;
                j = i + 1;
                
                if (i > 0){
                    fecha = $(".cuota-fechaf" + parseInt(i-1) ).val();
                    $(".cuota-fechaf"+parseInt(i-1)).attr({ "onchange": "fecha_fin_cuota("+parseInt(i)+")" });
                }

                inputs = '<tr class="cantidad-cuotas">';
                    inputs += '<td> ' + j + ' </td>';
                    inputs += '<td> <input type="date" id="cuota-fechai[' + i + ']" name="cuota-fechai[' + i + ']" class="cajaGeneral cuota-fechai'+i+'" value="' + fecha + '"> </td>';
                    inputs += '<td> <input type="date" id="cuota-fechaf[' + i + ']" name="cuota-fechaf[' + i + ']" class="cajaGeneral cuota-fechaf'+i+' cuota-fechaf" > </td>';
                    inputs += '<td> <input type="number" step="0.1" min="0" id="cuota-monto[' + i + ']" name="cuota-monto[' + i + ']" class="cajaPequena cuota-monto'+i+'" onchange="cuota_total(' + true + ')" value=""> </td>';
                inputs += '</tr>';

                $("#tbl-cuotas tbody").append(inputs);
        }
        cuota_total(false);
    }

    function view_coutas(comprobante = ""){
        if (comprobante != ""){
            url = base_url+"index.php/tesoreria/cuota/obtener_cuotas_comprobante";
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: { comprobante: comprobante },
                beforeSend:function(data){
                    $('#tbl-cuotas tbody').html('');
                },
                error: function (XRH, error){
                    Swal.fire({
                        icon: "warning",
                        title: "No fue posible acceder a las cuotas.",
                        showConfirmButton: false,
                        showCancelButton: false,
                        timer: 2000
                    });
                },
                success: function (data){
                    if (data.match == true) {                        
                        $.each(data.info, function(i,item){
                            cuotas = $("#cant-cuotas").val();
                            indice = i + 1;

                            inputs = '<tr class="cantidad-cuotas">';
                                inputs += '<td> ' + indice + ' </td>';
                                inputs += '<td> <input type="date" value="' + item.fechaiv + '" id="cuota-fechai[' + i + ']" name="cuota-fechai[' + i + ']" class="cajaGeneral cuota-fechai'+i+'" value="' + fecha + '"> </td>';

                                inputs += '<td> <input value="' + item.fechafv + '" type="date" id="cuota-fechaf[' + i + ']" name="cuota-fechaf[' + i + ']" class="cajaGeneral cuota-fechaf'+i+' cuota-fechaf"> </td>';
                                    
                                inputs += '<td> <input type="number" step="0.1" min="0" id="cuota-monto[' + i + ']" name="cuota-monto[' + i + ']" class="cajaPequena cuota-monto'+i+'" onchange="cuota_total(' + true + ')" value="' + item.cuota + '"> </td>';
                            inputs += '</tr>';

                            $("#tbl-cuotas tbody").append(inputs);
                        });

                        cuota_total(true);
                    }
                }
            });
        }
    }

    function fecha_fin_cuota(pos){
        i = parseInt(pos-1);
        $(".cuota-fechai"+pos).val( $(".cuota-fechaf"+i).val() );
    }

    function cuota_total(quetions = false){
        cuotas = $("#cant-cuotas").val();
        if ( $("#retencion_codigo").val() != "" )
            montoTotal = parseFloat($(".importe_retencion").val());
        else
            montoTotal = parseFloat($("#importetotal").val());

        importe = montoTotal / cuotas;
        total = 0;

        if (quetions == true){
            Swal.fire({
                        icon: "warning",
                        title: "¿Desea recalcular automaticamente las cuotas?",
                        showConfirmButton: true,
                        showCancelButton: true,
                        confirmButtonText: "Si",
                        cancelButtonText: "No"
                    }).then(result => {
                        if (result.value){
                            for ( i=0; i<cuotas; i++ ){
                                $(".cuota-monto"+i).val(importe);
                                
                                if ( $(".cuota-monto"+i).val() != "" )
                                    total = parseFloat(total) + parseFloat($(".cuota-monto"+i).val());
                            }
                            $("#suma-cuotas").html( total.toFixed(2) );
                        }
                    });
        }
        else{
            for ( i=0; i<cuotas; i++ ){
                $(".cuota-monto"+i).val(importe);
                
                if ( $(".cuota-monto"+i).val() != "" )
                    total = parseFloat(total) + parseFloat($(".cuota-monto"+i).val());
            }
            $("#suma-cuotas").html( total.toFixed(2) );
        }
    }

    descuentoPercent = <?php echo isset($descuento) ? $descuento : 0 ?>;
</script>