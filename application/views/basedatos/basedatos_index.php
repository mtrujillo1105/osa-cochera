<html>
<head>
    <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap.css?=<?=CSS;?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap-theme.css?=<?=CSS;?>" rel="stylesheet">
    

    <style>
        *{
            font-family: 'Arial', sans-serif;
        }

        .general{
            position: relative;
            width: 96%;
            text-align: justify;
            padding: 0.5% 2% 0.5% 2%;
            color: rgba(27,27,27,1);
        }

        .titulo{
            float: left;
            display: block;
            width: 96%;
            padding: 2%;
            left: 0;
        }

        .opciones{
            position: relative;
            width: 98%;
            left: 0%;
            padding: 1%;
        }

        .element{
            float: left;
            padding: 0.5em 1.5em 0.5em 1.5em;
            margin-top: 1em;
            margin-left: 1em;
            margin-bottom: 1.5em;
            width: 8em;
            height: 10em;
            text-align: center;
            border: thin rgba(200,200,210,1) solid;
            border-radius: 0.2em;
            cursor: pointer;

            background: rgba(247,247,247,1);
            background: -moz-linear-gradient(top, rgba(247,247,247,1) 0%, rgba(255,255,255,1) 41%, rgba(214,214,214,1) 100%);
            background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(247,247,247,1)), color-stop(41%, rgba(255,255,255,1)), color-stop(100%, rgba(214,214,214,1)));
            background: -webkit-linear-gradient(top, rgba(247,247,247,1) 0%, rgba(255,255,255,1) 41%, rgba(214,214,214,1) 100%);
            background: -o-linear-gradient(top, rgba(247,247,247,1) 0%, rgba(255,255,255,1) 41%, rgba(214,214,214,1) 100%);
            background: -ms-linear-gradient(top, rgba(247,247,247,1) 0%, rgba(255,255,255,1) 41%, rgba(214,214,214,1) 100%);
            background: linear-gradient(to bottom, rgba(247,247,247,1) 0%, rgba(255,255,255,1) 41%, rgba(214,214,214,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7f7f7', endColorstr='#d6d6d6', GradientType=0 );
        }

        .element img{
            display: block;
            margin-left: 0.5em;
            width: 4em;
            height: 4em;
            padding: 1em;
            border-radius: 2em;
            border: thin rgba(255,255,255,.2) solid;
            box-shadow: -0.9em -0.9em 0.1em rgba(255,255,255,.2);

            transition: all 500ms;
            -webkit-transition: all 500ms;
        }

        .element:hover img{
            border: thin rgba(0,0,0,.5) solid;
            box-shadow: 0.2em 0.2em 0.2em rgba(0,0,0,.5);
        }

        .element .Loading{
            display: none;
        }

        p{
            font-size: 9pt;
        }

        .color-red{
            color: red;
        }

        .cajaCabecera input{
            margin-bottom: 1%;
            border: 1px solid #ABA7A6;
            border-radius: 7px;
            height: 30px;
            width: 90%;
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            border-radius: 7px;
            outline: none;
        }
        
        .row{
            margin: auto;
        }

        label{
            font-weight: normal;
        }

        .btn-close{
            z-index: 3;
            position: absolute;
            top: 3em;
            right: 3em;
            cursor: pointer;
            font-size: 10pt;
        }

        .btn-close:hover{
            font-weight: bold;
        }
    </style>
</head>
    
<body>
    <section class="general">        
        <section class="opciones">
            <span class="titulo header">Agregar establecimientos</span>

            <section class="element" data-toggle="modal" data-target=".modal-empresa">
                <img src="<?=base_url().'public/images/icons/documento-add.png?='.IMG;?>">
                <p>Agregar empresa</p>
            </section>

            <section class="element" data-toggle="modal" data-target=".modal-compania">
                <img src="<?=base_url().'public/images/icons/documento-add.png?='.IMG;?>">
                <p>Agregar compañias</p>
            </section>
        </section>
        <section class="element" data-toggle="modal" data-target=".modal-demoras_pagos">
                <img src="<?=base_url().'public/images/icons/documento-add.png?='.IMG;?>">
                <p>Demoras y pagos</p>
        </section>

        <section class="opciones">
            <span class="titulo header">Vaciar tablas de la DB</span>
            <section class="element" id="truncate-all">
                <img src="<?=base_url().'public/images/icons/icon-db-del.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Reiniciar DB</p>
            </section>

            <section class="element" id="truncate-comprobantes">
                <img src="<?=base_url().'public/images/icons/icon-ventas.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Comp., notas y guias r.</p>
            </section>
            
            <section class="element" id="truncate-docs">
                <img src="<?=base_url().'public/images/icons/documento-delete.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Cotiz., oc y presupuesto.</p>
            </section>
            
            <section class="element" id="truncate-inventarios">
                <img src="<?=base_url().'public/images/icons/del-stock.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Inventario, almacen, stock y guias</p>
            </section>
            
            <section class="element" id="truncate-stock">
                <img src="<?=base_url().'public/images/icons/del-stock.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Stock y guias</p>
            </section>
            
            <section class="element" id="truncate-productos">
                <img src="<?=base_url().'public/images/icons/icono-doc.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Productos, familias, marcas</p>
            </section>

            <section class="element" id="truncate-usuarios">
                <img src="<?=base_url().'public/images/icons/user-remove.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Usuarios</p>
            </section>
            
            <section class="element" id="truncate-personal">
                <img src="<?=base_url().'public/images/icons/del-empleado.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Personal</p>
            </section>

            <section class="element" id="truncate-clientes-proveedores">
                <img src="<?=base_url().'public/images/icons/icon-clientes.png?='.IMG;?>" class="imgDocs">
                <img src="<?=base_url().'public/images/icons/loading.gif?='.IMG;?>" class="Loading">
                <p>Clientes y proveedores</p>
            </section>
        </section>
    </section>

<div class="modal fade modal-empresa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="width: 80%; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 18px;">
        <div class="titulo" style="text-align: center;">
            <h3>Agregar Empresa</h3>
        </div>
        <form method="post">
            <div class="contenido" style="width: 90%; margin: auto; height: auto; overflow: auto;">
                <div class="tempde_head">
                    <div class="">
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <label for="ruc">RUC</label>
                        </div>
                        <div class="col-sm-8 col-md-8 col-lg-8 tempde_stock">
                            <label for="razon_social">Razón Social</label>
                        </div>
                    </div>
                    <div class="cajaCabecera">
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <input type="text" class="form-control" name="ruc" id="ruc" style="width: 60%; display: inline-block;">
                        
                            <span class="input-group-btn" style="display: inline-block;">
                                <button class="btn btn-default search-ruc-empresa" type="button" role="button"> <img src="<?=base_url();?>public/images/icons/sunat.png?=<?=IMG;?>" width="30px"> </button>
                                <span class='icon-loading-lg'></span>
                            </span>
                        </div>

                        <div class="col-sm-8 col-md-8 col-lg-8 tempde_stock">
                            <input type="text" class="form-control" name="razon_social" id="razon_social">
                        </div>
                    </div>
                    <div>
                        <span id="tempde_message" style="display: none;"></span>
                    </div>
                </div>

                <div class="tempde_body">
                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 tempde_stock">
                            <label for="direccion">Dirección</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 tempde_stock">
                            <input type="text" class="form-control" name="direccion" id="direccion" value="" placeholder="Dirección">
                        </div>
                    </div>

                    <br>
                    
                    <div class="row">
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <label for="telefono">Telefono</label>
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <label for="movil">Movil</label>
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <label for="fax">Fax</label>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <label for="web">Web</label>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <label for="email">Email</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <input type="phone" class="form-control" width="80%" name="telefono" id="telefono" value="" placeholder="000 000 000">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <input type="phone" class="form-control" width="80%" name="movil" id="movil" value="" placeholder="000 000 000">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <input type="phone" class="form-control" width="80%" name="fax" id="fax" value="" placeholder="Fax">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <input type="text" class="form-control" width="80%" name="web" id="web" value="" placeholder="www.empresa.com">
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3 tempde_stock">
                            <input type="email" class="form-control" width="80%" name="email" id="email" value="" placeholder="empresa@empresa.com">
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4"></div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: right;">
                            <button type="button" class="btn btn-success tempde_addEmpresa" accesskey="C" title="Presione las teclas (Alt + C), para acceder rapidamente a este boton">Aceptar</button>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: left;">
                            <button type="button" class="btn btn-danger" onclick="cerrar_ventana_prodtemporal();">Cerrar</button>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="modal fade modal-compania" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="width: 80%; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 18px;">
        <div class="titulo" style="text-align: center;">
            <h3>Agregar Establecimiento</h3>
        </div>
        <form method="post">
            <div class="contenido" style="width: 90%; margin: auto; height: auto; overflow: auto;">
                <div class="tempde_head">
                    <div class="">
                        <div class="col-sm-11 col-md-11 col-lg-11 tempde_stock">
                            <label for="nombre">Empresa</label>
                        </div>
                    </div>
                    <div class="cajaCabecera">
                        <div class="col-sm-11 col-md-11 col-lg-11 tempde_stock">
                            <input type="hidden" class="form-control" name="empresa" id="empresa">
                            <input type="text" class="form-control" name="nombre" id="nombre">
                        </div>
                    </div>
                </div>

                <div class="tempde_body">
                    <div class="row">
                        <div class="col-sm-5 col-md-5 col-lg-5 tempde_stock">
                            <label for="descripcion">Nombre del establecimiento</label>
                        </div>
                        <div class="col-sm-5 col-md-5 col-lg-5 tempde_stock">
                            <label for="ubigeo">ubigeo</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-5 col-md-5 col-lg-5 tempde_stock">
                            <input type="text" class="form-control" name="descripcion" id="descripcion" value="" placeholder="SUCURSAL I">
                        </div>
                        <div class="col-sm-5 col-md-5 col-lg-5 tempde_stock">
                            <input type="text" class="form-control" name="ubigeo" id="ubigeo" value="" placeholder="150101">
                        </div>
                    </div>
                    
                    <br>

                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 tempde_stock">
                            <label for="direccionEstablecimiento">Dirección del establecimiento</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 tempde_stock">
                            <input type="text" class="form-control" name="direccionEstablecimiento" id="direccionEstablecimiento" value="" placeholder="Dirección">
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4"></div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: right;">
                            <button type="button" class="btn btn-success tempde_addCompany" accesskey="x">Aceptar</button>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: left;">
                            <button type="button" class="btn btn-danger" onclick="cerrar_ventana_prodtemporal();">Cerrar</button>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="modal fade modal-demoras_pagos" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="width: 80%; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 18px;">
        <div class="titulo" style="text-align: center;">
            <h3>DEMORAS Y PAGOS</h3>
        </div>
        <form method="post">
            <div class="contenido" style="width: 90%; margin: auto; height: auto; overflow: auto;">
                <div class="tempde_head">
                    <div class="">
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <label for="pago">Pago</label>
                        </div>
                    </div>
                    <div class="cajaCabecera">
                        <div class="col-sm-1 col-md-1 col-lg-1 tempde_stock">
                            <input type="checkbox" id="myCheck" onclick="checkbox_estado()">
                        </div>
                    </div><span id="text" style="display:none">La empresa se encuentra al día en los pagos</span>
                    <span id="text2" style="display:none; color:red;">La empresa tiene demora en el pago</span>
                </div>

                <div class="tempde_body">
                    <div class="row">
                        <div class="col-sm-11 col-md-11 col-lg-11 tempde_stock">
                            <label for="deposito">Número de la última transacción</label>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-sm-5 col-md-5 col-lg-5 tempde_stock">
                            <input type="text" class="form-control" name="deposito" id="deposito" value="" placeholder="deposito">
                        </div>
                        
                    </div>
                    
                    <br>

                    <br>

                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4"></div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: right;">
                            <button type="button" class="btn btn-success tempde_addPago" accesskey="x">Aceptar</button>
                        </div>
                        <div class="col-sm-3 col-md-3 col-lg-3" style="text-align: left;">
                            <button type="button" class="btn btn-danger" onclick="cerrar_ventana_pagos_demoras();">Cerrar</button>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </form>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        obtener_estado_pago();
        /********* FORMULARIO *******************/
            $("#ubigeo").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?=base_url();?>index.php/maestros/ubigeo/autocompleteUbigeo/",
                        type: "POST",
                        data: {
                            term: $("#ubigeo").val()
                        },
                        dataType: "json",
                        success: function (data) {
                            response( $.map(data, function(item) {
                                    return {
                                        descripcion: item.descripcion,
                                        label: item.descripcion,
                                        value: item.codigo
                                    }})
                                );
                        }
                    });
                },
                select: function (event, ui) {
                    $("#ubigeo").val(ui.item.codigo);
                },
                minLength: 2
            });

            $("#nombre").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?=base_url();?>index.php/empresa/empresa/searchEmpresa/",
                        type: "POST",
                        data: {
                            search: $("#nombre").val()
                        },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    $("#nombre").val(ui.item.value);
                    $("#empresa").val(ui.item.codigo);
                    $("#direccionEstablecimiento").val(ui.item.direccion);
                },
                minLength: 2
            });

            $(".tempde_addEmpresa").click(function(){
                var ruc = $("#ruc").val();
                var razon_social = $("#razon_social").val();
                var direccion = $("#direccion").val();

                var telefono = $("#telefono").val();
                var movil = $("#movil").val();
                var fax = $("#fax").val();
                var web = $("#web").val();
                var email = $("#email").val();

                if (ruc == ""){
                    Swal.fire({
                        icon: "warning",
                        title: "Debes indicar un número de ruc.",
                        timer: 2000
                    });
                    $("#ruc").focus();
                    return null;
                }

                if (ruc.length != 11){
                    Swal.fire({
                        icon: "error",
                        title: "Longitud del RUC incorrecta.",
                        timer: 2000
                    });
                    $("#ruc").focus();
                    return null;
                }
                
                if (razon_social == ""){
                    Swal.fire({
                        icon: "warning",
                        title: "Debes indicar una razón social.",
                        timer: 2000
                    });
                    $("#razon_social").focus();
                    return null;
                }

                if (direccion == ""){
                    Swal.fire({
                        icon: "warning",
                        title: "Debes indicar una dirección.",
                        timer: 2000
                    });
                    $("#direccion").focus();
                    return null;
                }

                var url = "<?php echo base_url(); ?>index.php/maestros/configuracion/agregar_empresa";
                $.ajax({
                    url:url,
                    type:"POST",
                    data:{ 
                            ruc: ruc,
                            razon_social: razon_social,
                            direccion: direccion,
                            telefono: telefono,
                            movil: movil,
                            fax: fax,
                            web: web,
                            email: email
                        },
                    dataType:"json",
                    error:function(data){
                    },
                    success:function(data){
                        if (data.result == "success"){
                            Swal.fire({
                                icon: "success",
                                title: "Empresa registrada.",
                                timer: 2000
                            });
                            $("#ruc").val("");
                            $("#razon_social").val("");
                            $("#direccion").val("");

                            $("#telefono").val("");
                            $("#movil").val("");
                            $("#fax").val("");
                            $("#web").val("");
                            $("#email").val("");
                        }
                        else{
                            Swal.fire({
                                icon: "error",
                                title: "El registro no fue agregado.",
                                timer: 2000
                            });
                        }
                    }
                });
            });

             $(".tempde_addPago").click(function(){
                var deposito = $("#deposito").val();
                
                var checkBox = document.getElementById("myCheck");

                
                if (checkBox.checked == true){
                   estado_pago = 1;
                } else {
                   estado_pago = 0;
                }

                var url = "<?php echo base_url(); ?>index.php/basedatos/basedatos/UpdateEstadoPago";
                $.ajax({
                    url:url,
                    type:"POST",
                    data: { deposito: deposito, estado_pago: estado_pago},
                    dataType:"json",
                    error:function(data){
                    },
                    success:function(data){
                        if (data.result == "success"){
                            Swal.fire({
                                icon: "success",
                                title: "Se ha realizado el cambio",
                                timer: 2000
                            });

                            cerrar_ventana_pagos_demoras();
                        }
                    }
                });
            });

            $(".tempde_addCompany").click(function(){
                var empresa = $("#empresa").val();
                var ubigeo = $("#ubigeo").val();
                var descripcion = $("#descripcion").val();
                var direccion = $("#direccionEstablecimiento").val();

                if (empresa == ""){
                    Swal.fire({
                        icon: "warning",
                        title: "Debes seleccionar una empresa.",
                        timer: 2000
                    });
                    $("#nombre").focus();
                    return null;
                }

                if (descripcion == ""){
                    Swal.fire({
                        icon: "warning",
                        title: "Debes indicar una descripción.",
                        timer: 2000
                    });
                    $("#descripcion").focus();
                    return null;
                }
                
                if (ubigeo == ""){
                    Swal.fire({
                        icon: "warning",
                        title: "Primero selecciona el ubigeo.",
                        timer: 2000
                    });
                    $("#ubigeo").focus();
                    return null;
                }

                if (direccion == ""){
                    Swal.fire({
                        icon: "warning",
                        title: "Debes indicar una direccion.",
                        timer: 2000
                    });
                    $("#direccionEstablecimiento").focus();
                    return null;
                }

                var url = "<?php echo base_url(); ?>index.php/maestros/configuracion/agregar_companias";
                $.ajax({
                    url:url,
                    type:"POST",
                    data: { empresa: empresa, ubigeo: ubigeo, descripcion: descripcion, direccion: direccion },
                    dataType:"json",
                    error:function(data){
                    },
                    success:function(data){
                        if (data.result == "success"){
                            Swal.fire({
                                icon: "success",
                                title: "compañia registrada",
                                timer: 2000
                            });

                            $("#empresa").val("");
                            $("#nombre").val("");
                            $("#ubigeo").val("");
                            $("#descripcion").val("");
                            $("#direccionEstablecimiento").val("");
                        }
                    }
                });
            });

            $(".search-ruc-empresa").click(function (event) {
                var url = "<?=base_url();?>" + "index.php/empresa/cliente/cliente_sunat";
                var ruc = $("#ruc").val();
                $.ajax({
                    type    : "POST",
                    url     : url,
                    dataType: "json",
                    data    : { ruc: ruc},
                    beforeSend: function(data){
                        $('.search-ruc-empresa').hide();
                        $(".icon-loading-lg").show();
                    },
                    success : function(data) {
                        var cliente = data.cliente;
                        
                        $("#ruc").val(ruc);
                        $("#razon_social").val(cliente.result['razon_social']);
                        $("#direccion").val(cliente.result['direccion']);
                    },
                    error : function(data){
                        Swal.fire({
                            icon: "info",
                            title: "No se encontro información disponible.",
                            timer: 2000
                        });

                        $('.search-ruc-empresa').show();
                        $(".icon-loading-lg").hide();
                    },
                    complete: function(data){
                        $('.search-ruc-empresa').show();
                        $(".icon-loading-lg").hide();
                    }
                });
            });

        /******* OPCIONES **********************/

            $("#truncate-all").click(function(){
                afectacion = "Reinicio completo de la DB";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea reiniciar la base de datos?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_all/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });

                                        $("#truncate-all .imgDocs").hide();
                                        $("#truncate-all .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-all .imgDocs").show();
                                        $("#truncate-all .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-all .imgDocs").show();
                                        $("#truncate-all .Loading").hide();
                                    }
                                });
                            }
                        });
            });

            $("#truncate-comprobantes").click(function(){
                afectacion = "<b>Afectación:</b> Comprobantes, notas de credito/debito y guias de remisión.";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea reiniciar el registro de ventas y compras?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_comprobantes/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });

                                        $("#truncate-comprobantes .imgDocs").hide();
                                        $("#truncate-comprobantes .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-comprobantes .imgDocs").show();
                                        $("#truncate-comprobantes .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-comprobantes .imgDocs").show();
                                        $("#truncate-comprobantes .Loading").hide();
                                    }
                                });
                            }
                        });
            });

            $("#truncate-docs").click(function(){
                afectacion = "<b>Afectación:</b> ordenes de compra, cotizaciones, presupuestos, pedidos, producción, despacho y letras";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea reiniciar el registro de documentos emitidos?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_docs/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });

                                        $("#truncate-docs .imgDocs").hide();
                                        $("#truncate-docs .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-docs .imgDocs").show();
                                        $("#truncate-docs .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-docs .imgDocs").show();
                                        $("#truncate-docs .Loading").hide();
                                    }
                                });
                            }
                        });
            });

            $("#truncate-inventarios").click(function(){
                afectacion = "<b>Afectación:</b> Almacenes e inventarios (stock, lotes, series), guias de salida, guias de ingreso, guias de transferencia y kardex.";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea reiniciar el stock de articulos incluyendo inventarios y almacenes?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_inventarios/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });

                                        $("#truncate-inventarios .imgDocs").hide();
                                        $("#truncate-inventarios .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-inventarios .imgDocs").show();
                                        $("#truncate-inventarios .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-inventarios .imgDocs").show();
                                        $("#truncate-inventarios .Loading").hide();
                                    }
                                });
                            }
                        });
            });

            $("#truncate-stock").click(function(){
                afectacion = "<b>Afectación:</b> stock producto, stock lotes, stock series, detalles de inventarios, guias de salida, guias de ingreso, guias de transferencia y kardex.";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea reiniciar el stock de articulos?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_stock/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });
                                        $("#truncate-stock .imgDocs").hide();
                                        $("#truncate-stock .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-stock .imgDocs").show();
                                        $("#truncate-stock .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-stock .imgDocs").show();
                                        $("#truncate-stock .Loading").hide();
                                    }
                                });
                            }
                        });
            });

            $("#truncate-productos").click(function(){
                afectacion = "<b>Afectación:</b> productos, familias, marcas y recetas";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea eliminar el registro de productos, familias y marcas?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_productos/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });
                                        $("#truncate-productos .imgDocs").hide();
                                        $("#truncate-productos .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-productos .imgDocs").show();
                                        $("#truncate-productos .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        $("#truncate-productos .imgDocs").show();
                                        $("#truncate-productos .Loading").hide();

                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });
                                    }
                                });
                            }
                        });
            });

            $("#truncate-usuarios").click(function(){
                afectacion = "<b>Afectación:</b> usuarios";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea eliminar el registro de usuarios?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){

                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_usuarios/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });

                                        $("#truncate-usuarios .imgDocs").hide();
                                        $("#truncate-usuarios .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-usuarios .imgDocs").show();
                                        $("#truncate-usuarios .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-usuarios .imgDocs").show();
                                        $("#truncate-usuarios .Loading").hide();
                                    }
                                });
                            }
                        });
            });

            $("#truncate-personal").click(function(){
                afectacion = "<b>Afectación:</b> personas, directivos";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea eliminar el registro del personal?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_personal/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });

                                        $("#truncate-personal .imgDocs").hide();
                                        $("#truncate-personal .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-personal .imgDocs").show();
                                        $("#truncate-personal .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-personal .imgDocs").show();
                                        $("#truncate-personal .Loading").hide();
                                    }
                                });
                            }
                        });
            });

            $("#truncate-clientes-proveedores").click(function(){
                afectacion = "<b>Afectación:</b> clientes, proveedores, empresas, personas, establecimientos (a excepción de default)";
                Swal.fire({
                            icon: "warning",
                            title: "¿Desea eliminar el registro de clientes y proveedores?",
                            html: "<b class='color-red'>Esta acción no se puede deshacer.</b><br>" + afectacion,
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "Cancelar"
                        }).then(result => {
                            if (result.value){
                                var url = '<?=base_url();?>'+'index.php/basedatos/basedatos/truncate_clientes_proveedores/';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: { truncate: result.value },
                                    dataType: 'json',
                                    beforeSend: function (data) {
                                        Swal.fire({
                                            icon: "warning",
                                            title: "Solicitud en proceso. ¡Espere!",
                                            timer: 2000
                                        });
                                        
                                        $("#truncate-clientes-proveedores .imgDocs").hide();
                                        $("#truncate-clientes-proveedores .Loading").show();
                                    },
                                    error: function (data) {
                                        $("#truncate-clientes-proveedores .imgDocs").show();
                                        $("#truncate-clientes-proveedores .Loading").hide();
                                    },
                                    success: function (data) {
                                    },
                                    complete: function(data){
                                        Swal.fire({
                                            icon: "success",
                                            title: "¡Acción completada!",
                                            timer: 2000
                                        });

                                        $("#truncate-clientes-proveedores .imgDocs").show();
                                        $("#truncate-clientes-proveedores .Loading").hide();
                                    }
                                });
                            }
                        });
            });
    });

    function cerrar_ventana_prodtemporal(){
        $('.modal-empresa').modal("hide");
        $('.modal-compania').modal("hide");
        $('.modal-demoras_pagos').modal("hide");

    }

    function cerrar_ventana_pagos_demoras(){
        $('.modal-demoras_pagos').modal("hide");
        obtener_estado_pago();

    }

    function obtener_estado_pago() {
        var text = document.getElementById("text");
            
        $.ajax({
            url: "<?=base_url();?>index.php/basedatos/basedatos/obtener_estado_pago/",
            type: "POST",
            data: {
                term: $("#ubigeo").val()
            },
            dataType: "json",
            success: function (data) {

                $("#deposito").val(data.deposito);
                if(data.pago==1){
                    document.getElementById("myCheck").checked = true;
                    text.style.display = "block";
                    text2.style.display = "none";
                }else{
                    document.getElementById("myCheck").checked = false;
                    text.style.display = "none";
                    text2.style.display = "block";
                }
            }
        });

    }
    
    function checkbox_estado() {
               
      var checkBox = document.getElementById("myCheck");
     
      var text  = document.getElementById("text");
      var text2 = document.getElementById("text2");
      
      if (checkBox.checked == true){
        text.style.display = "block";
        text2.style.display = "none";
      } else {
        text.style.display = "none";
        text2.style.display = "block";
        $("#deposito").val("");
      }
    }
</script>
</body>
</html>
