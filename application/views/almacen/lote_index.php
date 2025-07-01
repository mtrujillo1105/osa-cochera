<html>
<head>

    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js?=<?=JS;?>"></script>
    <script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>

</head>
<body>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>
            <div id="frmBusqueda">
                <form id="form_busqueda2" name="form_busqueda2" method="post" action="<?php echo $action; ?>">
                     <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                        <tr>
                            <td width="16%">Nombre del Articulo:</td>
                            <td>
                                <input id="txtCodigo" type="hidden" class="cajaPequena" name="txtCodigo" placeholder="Codigo" maxlength="30" size="50" value="<?=$codigo;?>">
                                <input id="nombre_producto" type="text" class="cajaGrande" name="nombre_producto" placeholder="Descripción del articulo" maxlength="30" size="50" value="<?=$nombre_producto;?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Número de lote:</td>
                            <td><input id="txtNombre" name="txtNombre" type="text" class="cajaGrande" maxlength="100" placeholder="Número del lote" value="<?=$nombre;?>"></td>
                        </tr>
                     </table>
                </form>
            </div>

            <div id="cuerpoPagina" >
                <form id="frmpublicar" name="frmpublicar" method="post" enctype="multipart/form-data" action="">
                <div class="cargarBusqueda">
                  <div class="acciones">
                    <div id="botonBusqueda">
                        <ul id="nuevoLote" class="lista_botones" data-toggle="modal" data-target=".bd-example-modal-lg">
                            <li id="nuevo">Nuevo Lote</li>
                        </ul>
                        <ul id="cancelarLote" class="lista_botones">
                            <li id="limpiar">Limpiar</li>
                        </ul>
                        <ul id="buscarProductoLote" class="lista_botones">
                            <li id="buscar">Buscar</li>
                        </ul>
                    </div>
                    <div id="lineaResultado">
                        <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="50%" align="left">N de lotes encontradas: &nbsp;<?php echo $registros; ?> </td>
                        </table>
                    </div>
                </div>  
                </div>
                
                <a id='ingresar_series' class='fancybox' href='"<?php echo base_url(); ?>"index.php/almacen/producto/ventana_nueva_serie/'></a>

                <div id="cabeceraResultado" class="header">Lista de Lotes</div>
                <div id="frmResultado">
                    <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1" style="text-align: center;">
                        <tr class="cabeceraTabla">
                            <td width="5%">ITEM</td>
                            <td width="10%" align='center'>N. LOTE</td>
                            <td width="10%" align='center'>F. VENCIMIENTO</td>
                            <td width="10%" align='center'>CANT. INICIAL</td>
                            <td width="10%" align='center'>P. COSTO</td>
                            <td width="45%" align='left'>DESCRIPCIÓN</td>
                            <td width="10%" align='center'>Marca</td>
							<td align='center'>ACCIONES</td>
                        </tr>
                        <?php
                        if (count($lista) > 0) {
                            foreach ($lista as $indice => $valor){
                                $class = ($indice % 2 == 0) ? 'itemParTabla' : 'itemImparTabla'; ?>
                                    <tr class="<?=$class;?>">
                                	<td><?=$valor[0];?></td>
                                	<td style="text-align: center;"><?=$valor[1];?></td>
                                    <td style="text-align: center;"><?=$valor[2];?></td>
                                    <td style="text-align: center;"><?=$valor[3];?></td>
                                    <td style="text-align: center;"><?=$valor[4];?></td>
                                    <td style="text-align: left;"><?=$valor[5];?></td>
                                    <td style="text-align: left;"><?=$valor[6];?></td>
                                	<td><?=$valor[7];?>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?=$valor[8];?>
                                    </td>
								</tr> <?php
                            }
                        } ?>
                    </table>
                </div>
               <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
                <input type="hidden" id="iniciopagina" name="iniciopagina">
            </form>
            </div>
        </div>
    </div>

</div>



<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="width: 60%; height: 20em; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 18px;">
        <div class="titulo" style="text-align: center;">
            <h3>Detalle del Lote</h3>
        </div>
        <form id="form_tempdetalle" method="post">
            <div class="contenido" style="width: 90%; margin: auto; height: 650px; overflow: auto;">
                <div class="tempde_head">
                    <div class="">
                        Producto
                    </div>
                    <div class="cajaCabecera">
                        <input type="hidden" class="form-control" name="tempde_idLote" id="tempde_idLote">
                        <input type="hidden" class="form-control" name="tempde_estado" id="tempde_estado" value="1">
                        <input type="hidden" class="form-control" name="tempde_codproducto" id="tempde_codproducto" >
                        <input type="text" class="form-control" name="tempde_producto" id="tempde_producto">
                    </div>
                    <div>
                        <span id="tempde_message" style="display: none;"></span>
                    </div>
                 </div>
                 <br>

                <div class="tempde_body">
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <label for="infoNumeroLote">N° de lote</label>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <label for="infoVencimientoLote">F. Vencimiento</label>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 tempde_stock">
                            <label for="infoCosto">Precio Costo</label>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 tempde_stock">
                            <label for="infoCantidad">Cant. Inicial</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <input type="text" class="form-control" style="width: 80%" name="infoNumeroLote" id="infoNumeroLote" value="" placeholder="L-000000">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <input type="date" class="form-control" style="width: 80%" name="infoVencimientoLote" id="infoVencimientoLote" value="<?=date('Y-m-d');?>">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 tempde_stock">
                            <input type="number" class="form-control" style="width: 80%" name="infoCosto" id="infoCosto" value="" step="0.000">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 tempde_stock">
                            <input type="number" class="form-control" style="width: 80%" readonly name="infoCantidad" id="infoCantidad" value="">
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4"></div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: right;">
                            <a href="#" id="tempde_aceptar" class="btn btn-success btn-nvoLote" accesskey="x">Aceptar</a>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: left;">
                            <a href="#" id="tempde_cancelar" class="btn btn-danger" onclick="cerrar_ventana_prodtemporal();">Cerrar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <!--</div>-->
    </div>
  </div>
</div>

<script type="text/javascript">
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
                    $("#txtCodigo").val(ui.item.codigo);
                    $("#nombre_producto").val(ui.item.descripcion);
                },
                minLength: 1
            });

            $("#tempde_producto").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/maestros/temporaldetalle/autocomplete_producto/B//",
                        type: "POST",
                        data: {
                            term: $("#tempde_producto").val(), TipCli: "", marca: "", modelo: "" 
                        },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    $("#tempde_codproducto").val(ui.item.codigo);
                    $("#tempde_producto").val(ui.item.descripcion);
                },
                minLength: 1
            });
            $(".btn-nvoLote").click(function(){
                var idLote = $("#tempde_idLote").val();
                var producto = $("#tempde_codproducto").val();
                var infoNumeroLote = $("#infoNumeroLote").val();
                var infoVencimientoLote = $("#infoVencimientoLote").val();
                var infoCostoLote = $("#infoCosto").val();
                var infoCantidadLote = $("#infoCantidad").val();
                var estado = $("#tempde_estado").val();
                
                var url = "<?php echo base_url(); ?>index.php/almacen/lote/nuevo_lote/";

                if (producto > 0){
                    $.ajax({
                        url:url,
                        type:"POST",
                        data:{ idLote: idLote, producto: producto, numeroLote: infoNumeroLote, vencimientoLote: infoVencimientoLote, costoLote : infoCostoLote, cantidadLote: infoCantidadLote, estado : estado},
                        dataType:"json",
                        error:function(data){
                        },
                        success:function(data){
                            $.each(data, function (i, item){
                                if (item.result == "success"){
                                    alert(item.mensaje);
                                    $("#tempde_idLote").val('');
                                    $("#tempde_codproducto").val('');
                                    $("#tempde_producto").val('');
                                    $("#infoNumeroLote").val('');
                                    $("#infoVencimientoLote").val('');
                                    $("#infoCosto").val('');
                                    $("#infoCantidad").val('');
                                    $("#tempde_estado").val('');
                                }
                                else{
                                    alert(item.mensaje);
                                }
                            });
                        }
                    });
                }
            });
        });

        function cerrar_ventana_prodtemporal(){
            $('.bd-example-modal-lg').modal('toggle');
            $('.modal-backdrop fade in').hide();
        }

        function editar_lote(idLote){
            var url = "<?php echo base_url(); ?>index.php/almacen/lote/editar_lote/" + idLote;
                if (idLote > 0){
                    $.ajax({
                        url:url,
                        type:"POST",
                        data:{ idLote : idLote },
                        dataType:"json",
                        error:function(data){
                        },
                        success:function(data){
                            $.each(data, function (i, item){
                                if (item.result == "success"){
                                    console.log(item);
                                    $("#tempde_idLote").val(item.lote[0].LOTP_Codigo);
                                    $("#tempde_codproducto").val(item.lote[0].PROD_Codigo);
                                    $("#tempde_producto").val(item.lote[0].descripcion_producto);
                                    $("#infoNumeroLote").val(item.lote[0].LOTC_Numero);
                                    $("#infoVencimientoLote").val(item.lote[0].LOTC_FechaVencimiento);
                                    $("#infoCosto").val(item.lote[0].LOTC_Costo);
                                    $("#infoCantidad").val(item.lote[0].LOTC_Cantidad);
                                    $("#nuevoLote").click();
                                }
                                else{
                                    alert(item.mensaje);
                                }
                            });
                        }
                    });
                }
        }
    </script>
</body>
</html>