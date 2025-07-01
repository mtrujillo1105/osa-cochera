<script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/letracambio.js?=<?=JS;?>"></script>
<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

<div class="container-fluid">
    <div class="row header">
        <div class="col-md-12 col-lg-12">
            <div><?=$titulo_busqueda;?></div>
        </div>
    </div>
    
    <form id="form_busqueda" method="post" action="<?=base_url();?>index.php/ventas/comprobante/comprobantes">
        <div class="row fuente8 py-1">
            <div class="col-sm-3 col-md-3 col-lg-3">
                <label for="nombre_cliente"><?=($tipo_oper=='V') ? 'Cliente: ' : 'Proveedor: ';?></label>
                
                <input type="text" name="ruc_cliente" id="ruc_cliente" value="" class="cajaPequena" <?=($tipo_oper=='C') ? 'style="display:none;"' : '';?>/>
                <input type="text" name="nombre_cliente" id="nombre_cliente" value="" class="cajaGeneral" <?=($tipo_oper=='C') ? 'style="display:none;"' : '';?>/>
                <input type="text" name="ruc_proveedor" id="ruc_proveedor" value="" class="cajaPequena" <?=($tipo_oper=='V') ? 'style="display:none;"' : '';?>/>
                <input type="text" name="nombre_proveedor" id="nombre_proveedor" value="" class="cajaGeneral" <?=($tipo_oper=='V') ? 'style="display:none;"' : '';?>/>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <label for="serie">Serie</label>-<label for="numero">número (letra): </label>
                <input type="text" name="seriel" id="seriel" value="" class="cajaMinima" maxlength="4"/>
                <input type="text" name="numerol" id="numerol" value="" class="cajaPequena" maxlength="10"/>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <label for="serie">Serie</label>-<label for="numero">número (comprobante): </label>
                <input type="text" name="serie" id="seriec" value="" class="cajaMinima" maxlength="4"/>
                <input type="text" name="numero" id="numeroc" value="" class="cajaPequena" maxlength="10"/>
            </div>
        </div>
        <div class="row fuente8 py-1">
            <div class="col-sm-3 col-md-3 col-lg-3">
                <label for="fechai">Fecha Inicial: </label>
                <input type="date" name="fechai" id="fechai" value="<?=$fechai;?>" class="cajaGeneral"/>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <label for="fechaf">Fecha final: </label>
                <input type="date" name="fechaf" id="fechaf" value="<?=$fechaf;?>" class="cajaGeneral"/>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <label for="estado_pago">Estado de pago: </label>
                <select id="estado_pago" name="estado_pago" class="cajaMedia">
                    <option value="">Seleccionar</option>
                    <option value="0">Pendientes</option>
                    <option value="1">Pagados</option>
                </select>
            </div>
        </div>
            <?=$oculto;?>
    </form>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div class="acciones">
                        <div id="botonBusqueda">
                            <ul class="lista_botones">
                                <li id="nuevo" data-toggle='modal' data-target='#ver_letras'>Letra</li>
                            </ul>
                            <ul id="limpiarC" class="lista_botones">
                                <li id="limpiar">Limpiar</li>
                            </ul>
                            <ul id="buscarC" class="lista_botones">
                                <li id="buscar">Buscar</li>
                            </ul> 
                        </div>
                        <div id="lineaResultado">Lista con documentos encontrados</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div class="header text-align-center"><?=$titulo_tabla;?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <table class="fuente8 display" id="table-letras">
                        <div id="cargando_datos" class="loading-table">
                            <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                        </div>
                        <thead>
                            <tr class="cabeceraTabla">
                                <td style="width:08%" data-orderable="true">FECHA DE PAGO</td>
                                <td style="width:08%" data-orderable="true">FECHA DE VENC.</td>
                                <td style="width:08%" data-orderable="true">SERIE</td>
                                <td style="width:08%" data-orderable="true">NÚMERO</td>
                                <td style="width:08%" data-orderable="false">RUC / DNI</td>
                                <td style="width:30%" data-orderable="false">RAZÓN SOCIAL</td>
                                <td style="width:06%" data-orderable="true">ESTADO</td>
                                <td style="width:08%" data-orderable="false">TOTAL</td>
                                <td style="width:08%" data-orderable="false"></td>
                                <td style="width:08%" data-orderable="false"></td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="ver_letras" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formLetras" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div style="text-align: center;">
                    <h3><b>REGISTRAR LETRAS</b></h3>
                </div>
                <div class="modal-body panel panel-default">
                    <div class="row">
                        <input type="hidden" id="cliente" name="cliente" value="">
                        <input type="hidden" id="proveedor" name="proveedor" value="">
                        <input type="hidden" id="operacion" name="operacion" value="<?=$tipo_oper;?>">
                        
                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <label for=""><?=($tipo_oper == "V") ? "CLIENTE" : "PROVEEDOR";?></label>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <input type="number" id="nruc" name="nruc" class="form-control" placeholder="RUC / DNI">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <input type="text" id="razon_social" name="razon_social" class="form-control" placeholder="RAZÓN SOCIAL">
                        </div>
                    </div>

                    <br><br>
                    
                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 header form-group">
                            <label>DETALLES</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="forma_pago">FORMA DE PAGO</label>
                            <select id="forma_pago" name="forma_pago" class="form-control h-3">
                                <option value="1">EFECTIVO</option>
                            </select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="moneda">MONEDA</label>
                            <select id="moneda" name="moneda" class="form-control h-3"> <?php
                                foreach ($monedas as $i => $val) { ?>
                                    <option value="<?=$val->MONED_Codigo?>"><?=$val->MONED_Descripcion;?></option> <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="banco">BANCO</label>
                            <select id="banco" name="banco" class="form-control h-3">
                                <option value="">:: SELECCIONE ::</option><?php
                                foreach ($bancos as $i => $val) { ?>
                                    <option value="<?=$val->BANP_Codigo?>" label="<?=$val->BANC_Siglas.' - '.$val->BANC_Nombre;?>"><?=$val->BANC_Nombre;?></option> <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="titular">TITULAR</label>
                            <input type="text" id="titular" name="titular" class="form-control h-2" placeholder="TITULAR">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="cuenta">NÚMERO DE CUENTA</label>
                            <input type="text" id="cuenta" name="cuenta" class="form-control h-2" placeholder="NÚMERO DE CUENTA BANCARIA">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 header form-group">
                            <label>DOCUMENTOS EMITIDOS</label>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <table class="fuente8 display" id="table-docs">
                                <div id="cargando_datos" class="loading-table">
                                    <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                                </div>
                                <thead>
                                    <th style="width: 25%;" data-orderable="true">FECHA EMISIÓN</th>
                                    <th style="width: 25%;" data-orderable="true">FECHA VENCIMIENTO</th>
                                    <th style="width: 10%;" data-orderable="true">SERIE</th>
                                    <th style="width: 10%;" data-orderable="true">NÚMERO</th>
                                    <th style="width: 10%;" data-orderable="true">MONEDA</th>
                                    <th style="width: 10%;" data-orderable="true">TOTAL</th>
                                    <th style="width: 10%;" data-orderable="false">SELECCIÓN</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                            <div class="row">
                                <div class="col-sm-11 col-md-11 col-lg-11 header form-group">
                                    <label>LETRAS</label>
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-danger h-2" onclick="removeNvaLetra()">-</button>
                                        <input type="text" id="cantidad_letras" name="cantidad_letras" class="form-control h-1 w-2 display-inline-block" readonly value="0">
                                        <button type="button" class="btn btn-info h-2" onclick="addNvaLetra()">+</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row letra_nva"></div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-sm-5 col-md-5 col-lg-5">
                            <label>IMPORTE EN DOCUMENTOS</label>
                            <input type="number" id="importe_documentos" class="form-control display-inline-block" value="0" readonly>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <label>IMPORTE EN LETRAS</label>
                            <input type="number" id="importe_letras" class="form-control display-inline-block" value="0" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="generar_letras()">Generar letras</button>
                    <button type="button" class="btn btn-info" onclick="clean()">Limpiar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="update_letras" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formUpdateLetra" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div style="text-align: center;">
                    <h3><b>ACTUALIZAR LETRA</b></h3>
                </div>
                <div class="modal-body panel panel-default">
                    <div class="row">
                        <input type="hidden" id="letraUP" name="letraUP" value="">
                        <input type="hidden" id="clienteUP" name="clienteUP" value="">
                        <input type="hidden" id="proveedorUP" name="proveedorUP" value="">
                        <input type="hidden" id="operacionUP" name="operacionUP" value="<?=$tipo_oper;?>">
                        
                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <label for=""><?=($tipo_oper == "V") ? "CLIENTE" : "PROVEEDOR";?></label>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <input type="number" id="nrucUP" name="nrucUP" class="form-control" placeholder="RUC / DNI" readonly>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <input type="text" id="razon_socialUP" name="razon_socialUP" class="form-control" placeholder="RAZÓN SOCIAL" readonly>
                        </div>
                    </div>

                    <br><br>
                    
                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 header form-group">
                            <label>DETALLES</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="forma_pagoUP">FORMA DE PAGO</label>
                            <select id="forma_pagoUP" name="forma_pagoUP" class="form-control h-3">
                                <option value="1">EFECTIVO</option>
                            </select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="monedaUP">MONEDA</label>
                            <select id="monedaUP" name="monedaUP" class="form-control h-3"> <?php
                                foreach ($monedas as $i => $val) { ?>
                                    <option value="<?=$val->MONED_Codigo?>"><?=$val->MONED_Descripcion;?></option> <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="bancoUP">BANCO</label>
                            <select id="bancoUP" name="bancoUP" class="form-control h-3">
                                <option value="">:: SELECCIONE ::</option><?php
                                foreach ($bancos as $i => $val) { ?>
                                    <option value="<?=$val->BANP_Codigo?>" label="<?=$val->BANC_Siglas.' - '.$val->BANC_Nombre;?>"><?=$val->BANC_Nombre;?></option> <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="titularUP">TITULAR</label>
                            <input type="text" id="titularUP" name="titularUP" class="form-control h-2" placeholder="TITULAR">
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                            <label for="cuentaUP">NÚMERO DE CUENTA</label>
                            <input type="text" id="cuentaUP" name="cuentaUP" class="form-control h-2" placeholder="NÚMERO DE CUENTA BANCARIA">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 header form-group">
                            <label>DOCUMENTOS EMITIDOS</label>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                            <div class="row">
                                <div class="col-sm-11 col-md-11 col-lg-11 header form-group">
                                    <label>LETRAS</label>
                                </div>
                            </div>
                            <br>
                            <div class='row'> <?php
                                if ($tipo_oper == "C"){ ?>
                                    <div class='col-sm-1 col-md-1 col-lg-1'>
                                        <label class='font-8' for=''>SERIE</label>
                                        <input type='text' name='serieUP[]' class='form-control h-2' value=''>
                                    </div>
                                    <div class='col-sm-1 col-md-1 col-lg-1'>
                                        <label class='font-8' for=''>NÚMERO</label>
                                        <input type='text' name='numeroUP[]' class='form-control h-2' value=''>
                                    </div><?php
                                }?>
                                
                                <div class='col-sm-1 col-md-1 col-lg-1'>
                                    <label class='font-8' for=''>FECHA DE PAGO</label>
                                    <input type='date' id="FechaPagoUP" name='FechaPagoUP[]' class='form-control h-2' value=''>
                                </div>
                                <div class='col-sm-1 col-md-1 col-lg-1'>
                                    <label class='font-8' for='' title='Fecha de vencimiento'>FECHA DE VENC.</label>
                                    <input type='date' id='FechaVencimientoUP' name='FechaVencimientoUP[]' class='form-control h-2' value='' title='Fecha de vencimiento'>
                                </div>
                                <div class='col-sm-1 col-md-1 col-lg-1'>
                                    <label class='font-8' for=''>IMPORTE</label>
                                    <input type='number' min='0.01' step='0.50' id='importeUP' name='importeUP[]' class='form-control h-2 importe_letrasUP' value=''>
                                </div>
                                <div class='col-sm-1 col-md-1 col-lg-1'>
                                    <label class='font-8' for=''>ESTADO</label>
                                    <select class='form-control h-3' id='estado_letraUP' name='estado_letraUP[]'>
                                        <option value='0'>PENDIENTE</option>
                                        <option value='1'>PAGADO</option>
                                    </select>
                                </div>
                                <div class='col-sm-4 col-md-4 col-lg-4'>
                                    <label class='font-8' for=''>OBSERVACIÓN</label>
                                    <textarea id='observacionUP' name='observacionUP[]' class='form-control h-2'></textarea>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="actualizar_letra()">Actualizar registro</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#table-letras').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : '<?=base_url();?>index.php/ventas/letra/datatable_letras/<?="$tipo_oper";?>',
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
            order: [[ 3, "desc" ]]
        });

        $("#buscarC").click(function(){

            fechai          = $("#fechai").val();
            fechaf          = $("#fechaf").val();

            seriel           = $("#seriel").val();
            numerol          = $("#numerol").val();
           
            seriec         = $("#seriec").val();
            numeroc        = $("#numeroc").val();

            ruc_cliente     = $("#ruc_cliente").val();
            nombre_cliente  = $("#nombre_cliente").val();

            ruc_proveedor     = $("#ruc_proveedor").val();
            nombre_proveedor  = $("#nombre_proveedor").val();
            
            estado_pago  = $("#estado_pago").val();

            $('#table-letras').DataTable({ responsive: true,
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                ajax:{
                        url : '<?=base_url();?>index.php/ventas/letra/datatable_letras/<?="$tipo_oper";?>',
                        type: "POST",
                        data: {
                                fechai: fechai, 
                                fechaf: fechaf,
                                seriel: seriel,
                                numerol: numerol,
                                seriec: seriec,
                                numeroc: numeroc,
                                ruc_cliente: ruc_cliente,
                                nombre_cliente: nombre_cliente,
                                ruc_proveedor: ruc_proveedor,
                                nombre_proveedor: nombre_proveedor,
                                estado_pago: estado_pago
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
                order: [[ 3, "desc" ]]
            });
        });

        $("#limpiar").click(function(){

            $("#fechai").val("");
            $("#fechaf").val("");
            
            $("#seriel").val("");
            $("#numerol").val("");

            $("#serieoc").val("");
            $("#numerooc").val("");
            
            $("#ruc_cliente").val("");
            $("#nombre_cliente").val("");

            $("#ruc_proveedor").val("");
            $("#nombre_proveedor").val("");
            $("#estado_pago").val("");

            fechai = "";
            fechaf = "";
            serie = "";
            numero = "";
            ruc_cliente = "";
            nombre_cliente = "";
            ruc_proveedor = "";
            nombre_proveedor = "";
            estado_pago = "";

            $('#table-letras').DataTable({ responsive: true,
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                ajax:{
                        url : '<?=base_url();?>index.php/ventas/letra/datatable_letras/<?="$tipo_oper";?>',
                        type: "POST",
                        data: {
                                fechai: fechai, 
                                fechaf: fechaf,
                                serie: serie,
                                numero: numero,
                               
                              
                                ruc_cliente: ruc_cliente,
                                nombre_cliente: nombre_cliente,
                                ruc_proveedor: ruc_proveedor,
                                estado_pago: estado_pago,
                                nombre_proveedor: nombre_proveedor
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
                order: [[ 3, "desc" ]]
            });
        });

        $("#razon_social").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                    type: "POST",
                    data: {term: $("#razon_social").val()},
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                $("#razon_social").val(ui.item.nombre);
                $("#nruc").val(ui.item.ruc);
                $("#cliente").val(ui.item.codigo);
                getDocumentos();
            },
            minLength: 2
        });

        // BUSQUEDA POR RUC
        $("#nruc").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                    type: "POST",
                    data: {
                        term: $("#nruc").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                $("#razon_social").val(ui.item.nombre);
                $("#nruc").val(ui.item.ruc);
                $("#cliente").val(ui.item.codigo);
                getDocumentos();
            },
            minLength: 2
        });

        $("#moneda").change(function(){
            if ( $("#cliente").val() != "" || $("#proveedor").val() != "" )
                getDocumentos();
        });

        getDocumentos();
    });

    function total_documentos(){
        total = 0;

        $('.documentos').each(function(){
            if (this.checked) {
                id = $(this).val();
                span = $(".span_importe_" + id).html();
                total = total + parseFloat(span);
            }
        });

        $("#importe_documentos").val(total);
    }

    function total_letras(){
        total = 0;

        $('.importe_letras').each(function(){
            if ( $(this).val() != "" ){
                total = parseFloat(total) + parseFloat( $(this).val() );
            }
        });

        $("#importe_letras").val(total);
    }

    function getDocumentos(){
        $('#table-docs').DataTable({ responsive: true,
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                        url : base_url + "index.php/ventas/letra/listar_comprobantes",
                        type: "POST",
                        data: {
                                cliente: $("#cliente").val(),
                                proveedor: $("#proveedor").val(),
                                moneda: $("#moneda").val()
                        },
                        beforeSend: function(){
                            $("#table-docs .loading-table").show();
                        },
                        error: function(){
                        },
                        complete: function(){
                            $("#table-docs .loading-table").hide();
                        }
                },
                language: spanish,
                order: [[ 2, "desc" ]]
            });
    }

    function total_coutas(){
        size = $(".total_cuotas_val").length;
        total = 0;
        for (i = 0; i < size; i++){
            total = total + parseFloat( $("#cuotap_"+i).val() );
        }
        $(".total_cuotas_span").html(total.toFixed(2));
    }

    function addNvaLetra(){
        var operacion = "<?=$tipo_oper;?>";

        fila = "<div class='row row-letras-inputs'>";

            if (operacion == "C"){
                fila += "<div class='col-sm-1 col-md-1 col-lg-1'>";
                    fila += "<label class='font-8' for=''>SERIE</label>";
                    fila += "<input type='text' name='serie[]' class='form-control h-2' value=''>";
                fila += "</div>";

                fila += "<div class='col-sm-1 col-md-1 col-lg-1'>";
                    fila += "<label class='font-8' for=''>NÚMERO</label>";
                    fila += "<input type='text' name='numero[]' class='form-control h-2' value=''>";
                fila += "</div>";
            }
            
            fila += "<div class='col-sm-1 col-md-1 col-lg-1'>";
                fila += "<label class='font-8' for=''>FECHA DE PAGO</label>";
                fila += "<input type='date' name='FechaPago[]' class='form-control h-2' value=''>";
            fila += "</div>";

            fila += "<div class='col-sm-1 col-md-1 col-lg-1'>";
                fila += "<label class='font-8' for='' title='Fecha de vencimiento'>FECHA DE VENC.</label>";
                fila += "<input type='date' name='FechaVencimiento[]' class='form-control h-2' value='' title='Fecha de vencimiento'>";
            fila += "</div>";

            fila += "<div class='col-sm-1 col-md-1 col-lg-1'>";
                fila += "<label class='font-8' for=''>IMPORTE</label>";
                fila += "<input type='number' min='0.01' step='0.50' name='importe[]' onchange='total_letras()' class='form-control h-2 importe_letras' value=''>";
            fila += "</div>";

            fila += "<div class='col-sm-1 col-md-1 col-lg-1'>";
                fila += "<label class='font-8' for=''>ESTADO</label>";
                fila += "<select class='form-control h-3' name='estado_letra[]'>";
                    fila += "<option value='0'>PENDIENTE</option>";
                    fila += "<option value='1'>PAGADO</option>";
                fila += "</select>";
            fila += "</div>";

            fila += "<div class='col-sm-4 col-md-4 col-lg-4'>";
                fila += "<label class='font-8' for=''>OBSERVACIÓN</label>";
                fila += "<textarea name='observacion[]' class='form-control h-2'></textarea>";
            fila += "</div>";
            
            fila += "<br>";

        fila += "</div>";
        
        $('.letra_nva').append(fila);

        $("#cantidad_letras").val( $(".row-letras-inputs").length );
    }

    function removeNvaLetra(){
        $('.row .row-letras-inputs:last-child').remove();
        $("#cantidad_letras").val( $(".row-letras-inputs").length );
    }

    function ver_letra(letra){
        var url = base_url + "index.php/ventas/letra/getLetra";
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data:{
                    letra: letra
            },
            beforeSend: function(){
                clean();
            },
            success: function(data){
                if (data.match == true) {
                    info = data.info;
                    $("#letraUP").val(info.letra);
                    $("#nrucUP").val(info.documento);
                    $("#razon_socialUP").val(info.razon_social);
                    
                    $("#titularUP").val(info.titular);
                    $("#cuentaUP").val(info.numero_cuenta);
                    $("#FechaPagoUP").val(info.fecha_pago);
                    $("#FechaVencimientoUP").val(info.fecha_vencimiento);
                    $("#FechaVencimientoUP").val(info.fecha_vencimiento);
                    $("#importeUP").val(info.importe);
                    $("#observacionUP").val(info.observacion);

                    $("#forma_pagoUP > option[value="+info.forma_pago+"]").attr("selected",true);
                    $("#monedaUP > option[value="+info.moneda+"]").attr("selected",true);
                    $("#bancoUP > option[value="+info.banco+"]").attr("selected",true);
                    $("#estado_letraUP > option[value="+info.estado+"]").attr("selected",true);

                    $("#update_letras").modal("toggle");
                }
                else{
                    Swal.fire({
                                icon: "info",
                                title: "Información no disponible.",
                                html: "<b class='color-red'></b>",
                                showConfirmButton: true,
                                timer: 4000
                            });
                }
            },
            complete: function(){
            }
        });
    }

    function actualizar_letra(){
        Swal.fire({
                    icon: "info",
                    title: "¿Esta seguro de actualizar la información de esta letra?",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.value){
                        var info = $("#formUpdateLetra").serialize();
                        var url = base_url + "index.php/ventas/letra/actualizar_letras";

                        validacion = true;

                        $('.importe_letrasUP').each(function(){
                            if ( $(this).val() == "" ){
                                Swal.fire({
                                        icon: "error",
                                        title: "Verifique los datos ingresados.",
                                        html: "<b class='color-red'>Debe añadir el importe de la letra.</b>",
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                                $(this).focus();
                                validacion = false;
                            }
                        });

                        if (validacion == true){
                            $.ajax({
                                type: 'POST',
                                url: url,
                                dataType: 'json',
                                data: info,
                                success: function(data){
                                    if (data.result == "success") {
                                        Swal.fire({
                                            icon: "success",
                                            title: "Registro actualizado.",
                                            showConfirmButton: true,
                                            timer: 2000
                                        });

                                        clean();
                                        $("#limpiar").click();

                                    }
                                    else{
                                        Swal.fire({
                                            icon: "error",
                                            title: "Sin cambios.",
                                            html: "<b class='color-red'>La información no fue registrada, intentelo nuevamente.</b>",
                                            showConfirmButton: true,
                                            timer: 4000
                                        });
                                    }
                                },
                                complete: function(){
                                }
                            });
                        }
                    }
                });
    }

    function generar_letras(){
        Swal.fire({
                    icon: "info",
                    title: "¿Esta seguro de generar las letras?",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.value){
                        var info = $("#formLetras").serialize();
                        var url = base_url + "index.php/ventas/letra/generar_letras";

                        var cliente = $("#cliente").val();
                        var proveedor = $("#proveedor").val();
                        validacion = true;

                        if (cliente == "" && proveedor == ""){
                            Swal.fire({
                                        icon: "error",
                                        title: "Verifique los datos ingresados.",
                                        html: "<b class='color-red'>Debe seleccionar un cliente / proveedor.</b>",
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                            $("#nruc").focus();
                            validacion = false;
                        }

                        asociado = false;
                        $('.documentos').each(function(){
                            if (this.checked)
                                asociado = true;
                        });

                        if (asociado == false){
                            Swal.fire({
                                        icon: "error",
                                        title: "Verifique los datos ingresados.",
                                        html: "<b class='color-red'>Debe seleccionar un documento relacionado para generar las letras.</b>",
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                            validacion = false;
                        }

                        if ( parseInt($("#cantidad_letras").val()) == 0 ){
                            Swal.fire({
                                        icon: "error",
                                        title: "Número de letras incorrecto.",
                                        html: "<b class='color-red'>Debe añadir al menos una letra.</b>",
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                                validacion = false;
                        }

                        $('.importe_letras').each(function(){
                            if ( $(this).val() == "" ){
                                Swal.fire({
                                        icon: "error",
                                        title: "Verifique los datos ingresados.",
                                        html: "<b class='color-red'>Debe añadir el importe de la letra.</b>",
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                                $(this).focus();
                                validacion = false;
                            }
                        });

                        if (validacion == true){
                            $.ajax({
                                type: 'POST',
                                url: url,
                                dataType: 'json',
                                data: info,
                                success: function(data){
                                    if (data.result == "success") {
                                        Swal.fire({
                                            icon: "success",
                                            title: "Letras registradas",
                                            showConfirmButton: true,
                                            timer: 2000
                                        });

                                        clean();
                                        $("#limpiar").click();
                                    }
                                    else{
                                        Swal.fire({
                                            icon: "error",
                                            title: "Sin cambios.",
                                            html: "<b class='color-red'>La información no fue registrada, intentelo nuevamente.</b>",
                                            showConfirmButton: true,
                                            timer: 4000
                                        });
                                    }
                                },
                                complete: function(){
                                }
                            });
                        }
                    }
                });
    }

    function clean(){
        $("#nruc").val("");
        $("#razon_social").val("");
        $("#cliente").val("");
        $("#proveedor").val("");
        $("#titular").val("");
        $("#cuenta").val("");
        $("#importe_documentos").val("0.00");
        $("#importe_letras").val("0.00");
        $('.letra_nva').html("");
        $("#cantidad_letras").val("0");

        $("#nrucUP").val("");
        $("#razon_socialUP").val("");
        $("#clienteUP").val("");
        $("#proveedorUP").val("");
        $("#titularUP").val("");
        $("#cuentaUP").val("");

        $("#forma_pagoUP").removeAttr("selected");
        $("#monedaUP").removeAttr("selected");
        $("#bancoUP").removeAttr("selected");
        $("#estado_letraUP").removeAttr("selected");

        getDocumentos();
    }
</script>