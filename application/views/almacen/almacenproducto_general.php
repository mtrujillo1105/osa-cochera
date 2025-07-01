<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/almacenproducto.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>

<div class="container-fluid">
    <div class="row header">
        <div class="col-md-12 col-lg-12">
            <div><?=$titulo_busqueda;?></div>
        </div>
    </div>
    <form id="form_busqueda" method="post" action="<?=$action;?>">
        <div class="row fuente8 py-1">
            <div class="col-sm-1 col-md-1 col-lg-1 form-group">
                <label for="txtCodigo">Código producto: </label>
                <input id="txtCodigo" name="txtCodigo" type="text" class="form-control w-porc-90 h-1" placeholder="Código" maxlength="30" value="<?=$codigo;?>"/>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2 form-group">
                <label for="txtNombre">Nombre: </label>
                <input id="txtNombre" name="txtNombre" type="text" class="form-control w-porc-90 h-1" placeholder="Nombre del producto" maxlength="100" value="<?=$nombre;?>">
            </div>
            <div class="col-sm-1 col-md-1 col-lg-1"><br>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal_totales">Total de productos</button>
            </div>

            <input id="codigoInterno" name="codigoInterno" type="hidden" class="cajaGrande" maxlength="100" placeholder="Codigo original" value="<?=$codigoInterno;?>">
        </div>
    </form>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div class="acciones">
                        <div id="botonBusqueda">
                            <ul id="limpiarC" class="lista_botones">
                                <li id="limpiar">Limpiar</li>
                            </ul>
                            <ul id="buscarC" class="lista_botones">
                                <li id="buscar">Buscar</li>
                            </ul>
                        </div>
                        <div id="lineaResultado">Registros encontrados</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div class="header text-align-center"><?=$titulo;?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                    <div id="cargando_datos" class="loading-table">
                        <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                    </div>
                    <table class="fuente8 display" id="table-productos">
                        <thead>
                            <tr class="cabeceraTabla">
                                <th style="width: 05%" data-orderable="true">CÓDIGO</th>
                                <th style="width: 30%" data-orderable="true">DESCRIPCION</th> <?php
                                    foreach ($lista_establec as $key => $val){ ?>
                                        <th style="text-indent: 0;" data-orderable="false"><?=$val->EESTABC_Descripcion;?></th> <?php
                                    } ?>

                                <th style="width: 10%" data-orderable="true">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_totales" class="modal fade" role="dialog">
    <div class="modal-dialog w-porc-60">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div style="text-align: center;">
                <h3><b>TOTAL DE ARTICULOS POR FAMILIA</b></h3>
            </div>
            <div class="modal-body panel panel-default">
                <div class="row form-group">
                    <div class="col-sm-11 col-md-11 col-lg-11">
                        <table class="fuente8 display" id="table-totales">
                            <thead>
                                <tr>
                                    <th data-orderable="false">ESTABLECIMIENTOS</th>
                                    <th data-orderable="false">TOTAL</th>
                                    <th data-orderable="false">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody> <?php
                                if ( isset($totalesCat) && $totalesCat != NULL){
                                    $StockG = 0;
                                    foreach ($totalesCat as $key => $value) { ?>
                                        <tr>
                                            <td><?=$value->descripcion;?></td>
                                            <td><?=number_format($value->stock,0,'.',',');?></td>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <th>FAMILIA</th>
                                                        <th>CANTIDAD</th>
                                                    </tr><?php
                                                    foreach ($totalesFami as $key => $valueF){
                                                        if ($valueF->descripcion == $value->descripcion){
                                                            $desFami = ($valueF->FAMI_Descripcion != '') ? $valueF->FAMI_Descripcion : "N/A"; ?>
                                                            <tr>
                                                                <td><?=$desFami;?></td>
                                                                <td><?=number_format($valueF->stock);?></td>
                                                            </tr><?php
                                                        }
                                                    } ?>
                                                </table>
                                            </td>
                                        </tr> <?php
                                        $StockG += $value->stock;
                                    }
                                } ?>
                            </tbody>
                            <tfoot>
                                    <tr>
                                        <td>TOTAL EN TODAS LAS SEDES</td>
                                        <td><?=number_format($StockG,0,'.',',');?></td>
                                        <td>&nbsp;</td>
                                    </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#table-productos').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : '<?=base_url();?>index.php/almacen/almacenproducto/datatable_almacen_producto_general/',
                    type: "POST",
                    data: { dataString: "" },
                    beforeSend: function(){
                        $("#table-productos .loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $("#table-productos .loading-table").hide();
                    }
            },
            language: spanish,
            columnDefs: [{"className": "dt-center", "targets": 0}],
            order: [[ 1, "asc" ]]
        });

        $('#table-totales').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            autoWidth: false,
            paging: false,
            language: spanish,
            columnDefs: [{"className": "dt-center", "targets": 0}]
        });

        $("#buscarC").click(function(){
            search();
        });

        $("#limpiarC").click(function(){
            search(false);
        });

        $('#form_busqueda').keypress(function(e){
            if ( e.which == 13 ){
                return false;
            } 
        });

        $('#txtCodigo, #txtNombre').keyup(function(e){
            if ( e.which == 13 ){
                if( $(this).val() != '' )
                    search();
            }
        });
    });

    function search( search = true){
        if (search == true){
            txtCodigo = $("#txtCodigo").val();
            txtNombre = $("#txtNombre").val();
        }
        else{
            $("#txtCodigo").val("");
            $("#txtNombre").val("");
            txtCodigo = "";
            txtNombre = "";
        }
        
        $('#table-productos').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            ajax:{
                    url : '<?=base_url();?>index.php/almacen/almacenproducto/datatable_almacen_producto_general/',
                    type: "POST",
                    data: {
                            txtCodigo: txtCodigo,
                            txtNombre: txtNombre
                    },
                    beforeSend: function(){
                        $("#table-productos .loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $("#table-productos .loading-table").hide();
                    }
            },
            language: spanish,
            columnDefs: [{"className": "dt-center", "targets": 0}],
            order: [[ 1, "asc" ]]
        });
    }
    
</script>