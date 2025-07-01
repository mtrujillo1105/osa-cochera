<link href="<?=$base_url;?>public/js/fancybox/dist/jquery.fancybox.min.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=$base_url;?>public/js/fancybox/dist/jquery.fancybox.min.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/ocompra.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/nicEdit/nicEdit.js?=<?=JS;?>"></script>
<script type="text/javascript">
    bkLib.onDomLoaded(function() {
        new nicEditor({fullPanel : true}).panelInstance('mensaje');
    });
</script>

<div class="container-fluid">
  <div class="row header">
    <div class="col-md-11">
      <div><?=$titulo_busqueda;?></div>
    </div>
  </div>
  <form id="form_busqueda" method="post">
  	<div class="row fuente8 py-1">
	    <div class="col-sm-11 col-md-2">
	    	<label for="fechai">FECHA DESDE:</label>
	      <input type="date" name="fechai" id="fechai" value="" placeholder="Desde" class="form-control h-1"/>
	    </div>
	    <div class="col-sm-11 col-md-2">
	    	<label for="fechaf">FECHA HASTA:</label>
	      <input type="date" name="fechaf" id="fechaf" value="" placeholder="Hasta" class="form-control h-1"/>
	    </div>

	    <!--<div class="col-md-1"></div>-->
	    <!-- Vendedor -->
	    <div class="col-sm-11 col-md-1">
	    	<label for="codigoEmpleado">ID:</label>
	      <input type="text" name="codigoEmpleado" id="codigoEmpleado" value="" placeholder="ID Vendedor" class="form-control h-1"/>
	    </div>
	    <div class="col-sm-11 col-md-3">
	    	<label for="cboVendedor">VENDEDOR:</label>
	      <select id="cboVendedor" name="cboVendedor" class="form-control h-2">
	      	<option value=''>Seleccionar</option> <?php
	        foreach ($vendedores as $i => $val) { ?>
	          <option value='<?=$val->PERSP_Codigo;?>'><?="$val->PERSC_Nombre $val->PERSC_ApellidoPaterno $val->PERSC_ApellidoMaterno";?></option> <?php
	        } ?>
	      </select>
	    </div>
	    <!-- End Vendedor -->
	    <!--<div class="col-md-2"></div>-->
	  </div>

	  <div class="row fuente8 py-1">
	    <!-- CLIENTE -->
	      <div class="col-sm-11 col-md-1" <?=($tipo_oper == 'V') ? '' : 'hidden'; ?>>
	      	<label for="ruc_cliente">RUC / DNI:</label>
	        <input type="hidden" name="cliente" id="cliente" value=""/>
	        <input type="number" step="1" name="ruc_cliente" id="ruc_cliente" value="" placeholder="RUC ó DNI" class="form-control h-1"/>
	      </div>
	      <div class="col-sm-11 col-md-3" <?=($tipo_oper == 'V') ? '' : 'hidden'; ?>>
	      	<label for="nombre_cliente">NOMBRE Ó RAZÓN SOCIAL:</label>
	        <input type="text" id="nombre_cliente" name="nombre_cliente" class="form-control h-1" placeholder="Nombre ó razón social"/>
	      </div>
	    <!-- END CLIENTE -->

	    <!-- PROVEEDOR -->
	      <div class="col-sm-11 col-md-1" <?=($tipo_oper == 'C') ? '' : 'hidden'; ?>>
	      	<label for="ruc_proveedor">RUC / DNI:</label>
	        <input type="hidden" name="proveedor" id="proveedor" value=""/>
	        <input type="number" step="1" name="ruc_proveedor" id="ruc_proveedor" value="" placeholder="RUC ó DNI" class="form-control h-1"/>
	      </div>
	      <div class="col-sm-11 col-md-3" <?=($tipo_oper == 'C') ? '' : 'hidden'; ?>>
	      	<label for="nombre_proveedor">NOMBRE Ó RAZÓN SOCIAL:</label>
	        <input type="text" id="nombre_proveedor" name="nombre_proveedor" class="form-control h-1" placeholder="Nombre ó razón social"/>
	      </div>
	    <!-- END PROVEEDOR -->

	      <!--<div class="col-md-1"></div>-->
	      <div class="col-sm-11 col-md-3">
	      	<label for="nombre_producto">PRODUCTO:</label>
	        <input type="hidden" name="producto" id="producto">
	        <input type="text" name="nombre_producto" id="nombre_producto" value="" placeholder="Nombre del producto / servicio" class="form-control h-1"/>
	      </div>

		    <div class="col-sm-11 col-md-1">
		  		<label for="">Inicio</label>
					<input type="number" step="1" min="1" name="numeroI" id="numeroI" class="form-control h-1"/>
		    </div>
		    <div class="col-sm-11 col-md-1">
		  		<label for="">Número Fin</label>
		  		<input type="number" step="1" min="1" name="numeroF" id="numeroF" class="form-control h-1"/>
		    </div>
	  </div>

		<div class="row fuente8 py-1">
	    <div class="col-md-9"></div>
	  </div>
	</form>
		
    <!--
    	<span id="imprimirOcompraFiltro"> <a href="#"> <img src="<?=base_url().'images/pdf.png?='.IMG;?>" height="32px"> </a> </span>
    -->

  <div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
          <div class="acciones">
            <div id="botonBusqueda">
              <ul id="downloadOcompra" class="lista_botones" <?=($tipo_oper == 'V') ? '' : 'hidden';?>>
                <li id="excel">Cotizados</li>
              </ul>
              <ul id="imprimirOcompra" class="lista_botones" <?=($tipo_oper == 'V') ? '' : 'hidden';?>>
                <li id="imprimir">Imprimir</li>
              </ul>
              <ul id="nuevoOcompa" class="lista_botones">
                <li id="nuevo">Cotiz. de <?=$tOperacion;?></li>
              </ul>
              <ul id="limpiarO" class="lista_botones">
                <li id="limpiar">Limpiar</li>
              </ul>
              <ul id="buscarO" class="lista_botones">
                <li id="buscar">Buscar</li>
              </ul>
            </div>
            <div id="lineaResultado"><?=$titulo_tabla;?></div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
          <table class="fuente8 display" id="table-ocompra">
            <div id="cargando_datos" class="loading-table">
              <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
            </div>
            <thead>
              <tr class="cabeceraTabla">
                <th style="width:09%" data-orderable="true">FECHA</th>
                <th style="width:08%" data-orderable="true">NÚMERO</th>
                <th style="width:06%" data-orderable="true">ID</th>
                <th style="width:08%" data-orderable="true">RUC/DNI</th>
                <th style="width:27%" data-orderable="true">RAZON SOCIAL</th>
                <th style="width:10%" data-orderable="true">TOTAL</th>
                <th style="width:10%" data-orderable="true">GUIA</th>
                <th style="width:10%" data-orderable="true" title="Factura / Boleta / Comprobante">F/B/C</th>
                <th style="width:04%" data-orderable="false"></th>
                <th style="width:04%" data-orderable="false"></th>
                <th style="width:04%" data-orderable="false"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-envmail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="width: 700px; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 10pt;">
      <form method="post" id="form-mail">
        <div class="contenido" style="width: 100%; margin: auto; height: auto; overflow: auto;">
          <div class="tempde_head">
            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-7 col-md-7 col-lg-7" style="text-align: center;">
                <h3>ENVIO DE DOCUMENTOS POR CORREO</h3>
              </div>
            </div>
            <input type="hidden" id="idDocMail" name="idDocMail">
          </div>

          <div class="tempde_body">
              
            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-5 col-md-5 col-lg-5">
                  <label for="ncliente">Cliente:</label>
              </div>
              <div class="col-sm-2 col-md-2 col-lg-2">
                  <label for="doccliente">Ruc / Dni:</label>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-5 col-md-5 col-lg-5">
                  <input type="text" class="form-control" id="ncliente" name="ncliente" value="" placeholder="Razón social" readonly>
              </div>
              <div class="col-sm-2 col-md-2 col-lg-2">
                  <input type="text" class="form-control" id="doccliente" name="doccliente" value="" placeholder="N° documento" readonly>
              </div>
            </div>
            <br>

            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-7 col-md-7 col-lg-7">
                  <label for="destinatario">Destinatarios:</label>
                  <span class="mail-contactos"></span>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-7 col-md-7 col-lg-7">
                  <input type="text" class="form-control" id="destinatario" name="destinatario" value="" placeholder="Correo">
              </div>
            </div>
            <br>

            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-7 col-md-7 col-lg-7">
                  <label for="asunto">Asunto:</label>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-7 col-md-7 col-lg-7">
                  <input type="text" class="form-control" id="asunto" name="asunto" value="" placeholder="Asunto">
              </div>
            </div>
            <br>

            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-7 col-md-7 col-lg-7">
                  <label for="mensaje">Mensaje:</label>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-1 col-md-1 col-lg-1"></div>
              <div class="col-sm-7 col-md-7 col-lg-7">
                  <textarea id="mensaje" name="mensaje" style="width: 520px; height: 300px">
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
