<script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/guiarem.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>

<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>
<script language="javascript">
    $(document).ready(function () {
        $("a#linkVerCliente, a#linkVerProveedor, a#linkVerProducto").fancybox({
            'width': 700,
            'height': 450,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true,
            'type': 'iframe'
        });
        
        $("a#ocompra, a#comprobante").fancybox({
            'width': 800,
            'height': 500,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe'
        });

        //agregado autocompletar gcbq
        $("#nombre_producto").autocomplete({

            source: function (request, response) {

                $.ajax({
                    //contiene flagbs-bien o servicio
                    //url: "<?php echo base_url(); ?>index.php/almacen/producto/autocomplete/"+$("#flagBS").val()+"/"+$("#compania").val(),

                    url: "<?php echo base_url(); ?>index.php/almacen/producto/autocomplete/B/" + $("#compania").val(),
                    type: "POST",
                    data: {term: $("#nombre_producto").val()},
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }

                });

            },

            select: function (event, ui) {

                $("#buscar_producto").val(ui.item.codinterno);
                $("#producto").val(ui.item.codigo)
                $("#codproducto").val(ui.item.codinterno);
            },

            minLength: 2

        });

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
                $("#buscar_cliente").val(ui.item.ruc)
                $("#cliente").val(ui.item.codigo);
                $("#ruc_cliente").val(ui.item.ruc);
            },

            minLength: 2

        });


        $("#nombre_proveedor").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",
                    type: "POST",
                    data: {term: $("#nombre_proveedor").val()},
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }

                });

            },
            select: function (event, ui) {
                $("#buscar_proveedor").val(ui.item.ruc)
                $("#proveedor").val(ui.item.codigo);
                $("#ruc_proveedor").val(ui.item.ruc);
            },

            minLength: 2

        });

        /////////////////7
    });
    function seleccionar_cliente(codigo, ruc, razon_social, empresa, persona) {
        $("#cliente").val(codigo);
        $("#ruc_cliente").val(ruc);
        $("#nombre_cliente").val(razon_social);
    }
    function seleccionar_proveedor(codigo, ruc, razon_social) {
        $("#proveedor").val(codigo);
        $("#ruc_proveedor").val(ruc);
        $("#nombre_proveedor").val(razon_social);
    }
    function seleccionar_producto(codigo, interno, familia, stock, costo) {
        $("#producto").val(codigo);
        $("#codproducto").val(interno);

        base_url = $("#base_url").val();
        url = base_url + "index.php/almacen/producto/listar_unidad_medida_producto/" + codigo;
        $.getJSON(url, function (data) {
            $.each(data, function (i, item) {
                nombre_producto = item.PROD_Nombre;
            });
            $("#nombre_producto").val(nombre_producto);
        });
    }

    function relacionado_comprobante(numero){
        alert('Guia de remision relacionada con el numero ' + numero);
    }

</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>
            <div id="frmBusqueda">
                <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $accion; ?>">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                        <tr>
                            <td align='left' width="10%">Fecha inicial</td>
                            <td align='left' width="90%">
                                <input name="fechai" id="fechai" value="" type="text" class="cajaGeneral" size="10" maxlength="10"/>
                                <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechai",      // id del campo de texto
                                        ifFormat: "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario1"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                                <label style="margin-left: 90px;">Fecha final</label>
                                <input name="fechaf" id="fechaf" value="" type="text" class="cajaGeneral" size="10" maxlength="10"/>
                                <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechaf",      // id del campo de texto
                                        ifFormat: "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario2"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <td align='left'>Número</td>
                            <td align='left'> <?php
                            	if ($tipo_oper == 'V'){ ?>
	                            	<select id="seriei" name="seriei" class="cajaPequena h2"><?php
	                            		if ($series_emitidas != NULL){
			                            	foreach ($series_emitidas as $i => $val){ ?>
			                            		<option value="<?=$val->GUIAREMC_Serie;?>" <?=($val->serie_actual == $val->GUIAREMC_Serie) ? "selected" : "";?>><?=$val->GUIAREMC_Serie;?></option> <?php
			                            	}
			                            } ?>
		                            </select> <?php
		                          }
		                          else{ ?>
                              	<input type="text" name="seriei" id="seriei" value="" placeholder="Serie" class="cajaPequena"/> <?php
		                          } ?>
                              <input type="text" name="numero" id="numero" value="" placeholder="Numero" class="cajaGeneral" size="10" maxlength="6"/>
                            </td>
                        </tr>
                        <tr>
                            <?php if ($tipo_oper == 'V') { ?>
                                <td align='left'>Cliente</td>
                                <td align='left'>
                                    <input type="hidden" name="cliente" value="" id="cliente" size="5"/>
                                    <input type="text" name="ruc_cliente" value="" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" onkeypress="return numbersonly(this,event,'.');" readonly="readonly" placeholder="Ruc"/>
                                    <input type="text" name="nombre_cliente" value="" class="cajaGrande cajaSoloLectura" id="nombre_cliente" size="40" placeholder="Nombre cliente"/>
                                </td>
                            <?php } else { ?>
                                <td align='left'>Proveedor</td>
                                <td align='left'>
                                    <input type="hidden" name="proveedor" value="" id="proveedor" size="5"/>
                                    <input type="text" name="ruc_proveedor" value="" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" onkeypress="return numbersonly(this,event,'.');" readonly="readonly" placeholder="Ruc"/>
                                    <input type="text" name="nombre_proveedor" value="" class="cajaGrande cajaSoloLectura" id="nombre_proveedor" size="40" placeholder="Nombre proveedor"/>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td align='left'>Artículo</td>
                            <td align='left'>
                                <input name="compania" type="hidden" id="compania" value="">
                                <input name="producto" type="hidden" class="cajaPequena" id="producto" value="<?=$producto;?>" size="10" maxlength="11"/>
                                <input name="codproducto" type="text" class="cajaGeneral" id="codproducto" value="<?=$codproducto;?>" size="10" maxlength="20" onblur="obtener_producto();" readonly="readonly" placeholder="Codigo"/>
                                <input name="buscar_producto" type="hidden" class="cajaGeneral" id="buscar_producto" size="40"/>
                                <input name="nombre_producto" type="text" value="<?=$nombre_producto;?>" class="cajaGrande cajaSoloLectura" id="nombre_producto" size="40" placeholder="Nombre producto"/>
                                <!--<a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0'/></a>-->
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="acciones">
                <div id="botonBusqueda">
                   <!-- <ul id="imprimirGuiarem" class="lista_botones">
                        <li id="imprimir">Imprimir</li>
                    </ul>-->
                    <ul id="nuevaGuiarem" class="lista_botones">
                        <li id="nuevo">Guia de Remisión</li>
                    </ul>
                    <ul id="limpiarG" class="lista_botones">
                        <li id="limpiar">Limpiar</li>
                    </ul>
                    <ul id="buscarG" class="lista_botones">
                        <li id="buscar">Buscar</li>
                    </ul>
                </div>
                <div id="lineaResultado">
                    <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
                        <tr>
                            <td width="50%" align="left">Guias de remisión</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="cabeceraResultado" class="header"><?php echo $titulo_tabla; ?></div>
            <div id="frmResultado">
                <table class="fuente8 display" width="100%" cellspacing="0" cellpadding="3" border="0" id="table-guiarem">
                    <div id="cargando_datos" class="loading-table">
                        <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                    </div>
                    <thead>
                        <tr class="cabeceraTabla">
                            <th style="width:07%;" data-orderable="true">F. REGISTRO</th>
                            <th style="width:07%;" data-orderable="true">FECHA</th>
                            <th style="width:05%;" data-orderable="true">SERIE</th>
                            <th style="width:07%;" data-orderable="true">NUMERO</th>
                            <th style="width:20%;" data-orderable="true">RAZON SOCIAL</th>
                            <th style="width:06%;" data-orderable="false">BOLETA</th>
                            <th style="width:06%;" data-orderable="false">FACTURA</th>
                            <th style="width:06%;" data-orderable="false">COTIZACIÓN</th>
                            <th style="width:10.5%;" data-orderable="false">O. C.</th>
                            <th style="width:2.5%;" data-orderable="false"></th>
                            <th style="width:2.5%;" data-orderable="false"></th>
                            <th style="width:2.5%;" data-orderable="false"></th>
                            <th style="width:2.5%;" data-orderable="false"></th>
                            <th style="width:2.5%;" data-orderable="false"></th>
                            <th style="width:2.5%;" data-orderable="false"></th>
                            <th style="width:2.5%;" data-orderable="false"></th>
                            <th style="width:02%;" data-orderable="false"></th>
                            <th style="width:06%;" data-orderable="false"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        /*if (count($lista) < 0) {
                            foreach ($lista as $indice => $valor) {
                                $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla'; ?>
                                <tr class="<?php echo $class; ?>">
                                    <td>
                                        <div align="center"><?php echo $valor[0]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[1]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[2]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[3]; ?></div>
                                    </td>
                                    <td>
                                        <div align="left"><?php echo $valor[6]; ?></div>
                                    </td>
                                    <td>
                                    <!--No  visualiza la factura-->
                                        <div align="center"><?php echo $valor[14]; ?></div>
                                    </td>
                                    
                                    <td>
                                    <!--NO visualiza la guia de remision-->
                                        <div align="center"><?php echo $valor[13]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[12]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center" style="cursor:pointer; color:#003399; font-weight: normal; font-size: 11px;"><?php echo $valor[18]; ?></div> <!--HERE DATA OC -->
                                    </td>
                                    <td>
                                        <div align="center"><?php echo $valor[11]; ?></div>
                                    </td>
                                    <td>
                                        <div align="center" class="editar_data_<?=$valor[0]?>"><?=$valor[8];?></div>
                                    </td>
                                    <td>
                                        <div align="center"><?=$valor[9];?></div>
                                    </td>
                                    <td>
                                        <div align="left"><?=$valor[10];?></div>
                                    </td>
                                    <td>
                                        <div align="left"><?=$valor[20];?></div>
                                    </td>
                                    <td>
                                        <div align="left" class="pdfSunat_<?=$valor[0]?>">
                                            <span class="icon-loading"></span>
                                            <span class="pdfSunat_data_<?=$valor[0]?>"><?=$valor[19];?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div align="center"><?=$valor[17];?></div>
                                    </td>
                                    <td>
                                        <div align="center" class="disparador_<?=$valor[0]?>"> <!-- APROBAR -->
                                            <span class='icon-loading'></span>
                                            <span class="disparador_data_<?=$valor[0]?>"><?=$valor[15];?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                        }*/?>
                    </tbody>
                </table>
            </div>
            <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
            <?php echo $oculto ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $("#nombre_producto").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/maestros/temporaldetalle/autocomplete_producto/B//",
                    type: "POST",
                    data: {
                        term: $("#nombre_producto").val(), TipCli: "", marca: "", modelo: "" 
                    },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                $("#producto").val(ui.item.codigo);
                $("#nombre_producto").val(ui.item.descripcion);
                $("#codproducto").val(ui.item.codinterno);
            },
            minLength: 2
        });

        $("#nombre_producto").keyup(function(){
            var cadena = $("#nombre_producto").val();
            if ( cadena.length == 0 ){
                $("#producto").val("");
                $("#codproducto").val("");
            }
        });
    
        $('#table-guiarem').DataTable({
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : '<?=base_url();?>index.php/almacen/guiarem/datatable_guiarem/<?="$tipo_oper";?>',
                    type: "POST",
                    data: { dataString: "" },
                    beforeSend: function(){
                        $(".loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $(".loading-table").hide();

                    }
            },
            language: spanish,
            order: [[ 0, "desc" ]]
        });

        $("#buscarG").click(function(){

            fechai          = $("#fechai").val();
            fechaf          = $("#fechaf").val();

            seriei           = $("#seriei").val();
            numero          = $("#numero").val();

            ruc_cliente     = $("#ruc_cliente").val();
            nombre_cliente  = $("#nombre_cliente").val();

            ruc_proveedor   = $("#ruc_proveedor").val();
            nombre_proveedor = $("#nombre_proveedor").val();

            producto        = $("#producto").val();

            $('#table-guiarem').DataTable({
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                ajax:{
                        url : '<?=base_url();?>index.php/almacen/guiarem/datatable_guiarem/<?="$tipo_oper";?>',
                        type: "POST",
                        data: {
                                fechai: fechai, 
                                fechaf: fechaf,
                                seriei: seriei,
                                numero: numero,
                                ruc_cliente: ruc_cliente,
                                nombre_cliente: nombre_cliente,
                                ruc_proveedor: ruc_proveedor,
                                nombre_proveedor: nombre_proveedor,
                                producto: producto
                        },
                        beforeSend: function(){
                            $(".loading-table").show();
                        },
                        error: function(){
                        },
                        complete: function(){
                            $(".loading-table").hide();

                        }
                },
                language: spanish,
                order: [[ 0, "desc" ]]
            });
        });

        $("#limpiarG").click(function(){

            $("#form_busqueda")[0].reset();
            $("#cliente").val("");
            $("#proveedor").val("");
            $("#producto").val("");
            /*
            $("#fechaf").val("");
            
            $("#seriei").val("");
            $("#numero").val("");
            
            $("#ruc_cliente").val("");
            $("#nombre_cliente").val("");

            $("#ruc_proveedor").val("");
            $("#nombre_proveedor").val("");

						*/
            fechai = "";
            fechaf = "";
            seriei = "";
            numero = "";
            cliente = "";
            ruc_cliente = "";
            nombre_cliente = "";
            proveedor = "";
            ruc_proveedor = "";
            nombre_proveedor = "";
            producto = "";

            $('#table-guiarem').DataTable({
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                ajax:{
                        url : '<?=base_url();?>index.php/almacen/guiarem/datatable_guiarem/<?="$tipo_oper";?>',
                        type: "POST",
                        data: {
                                fechai: fechai, 
                                fechaf: fechaf,
                                seriei: seriei,
                                numero: numero,
                                cliente: cliente,
                                ruc_cliente: ruc_cliente,
                                nombre_cliente: nombre_cliente,
                                proveedor: proveedor,
                                ruc_proveedor: ruc_proveedor,
                                nombre_proveedor: nombre_proveedor,
                                producto: producto
                        },
                        beforeSend: function(){
                            $(".loading-table").show();
                        },
                        error: function(){
                        }
                        ,
                        complete: function(){
                            $(".loading-table").hide();

                        }
                },
                language: spanish,
                order: [[ 0, "desc" ]]
            });
        });
    });

    function comprobante_ver_pdf_conmenbrete_guia(cod, conv, img) {
        url = base_url+"index.php/almacen/guiarem/guiarem_ver_pdf_conmenbrete/"+cod;
        window.open(url,'',"width=800,height=600,menubars=no,resizable=no;")
    }
</script>