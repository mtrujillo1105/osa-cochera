<html>
<head>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>

    <style>
        .busqueda_opcinal{
            position: relative;
            text-align: center;
        }

        .busqueda_opcinal_1{
            position: absolute;
            background-color: #004488;
            color: #f1f4f8;
            width: 98px;
            height: 70px;
            top: 14px;
            left: 135px;
            -webkit-box-shadow: 0px 0px 0px 3px rgba(47, 50, 50, 0.34);
            -moz-box-shadow:    0px 0px 0px 3px rgba(47, 50, 50, 0.34);
            box-shadow:         0px 0px 0px 3px rgba(47, 50, 50, 0.34);
            cursor: pointer;
        }

        .control_1 .seleccionado{
            position: absolute;
            border-radius: 3px;
            background-color: #29fb00;
            width: 98px;
            height: 5px;
            bottom: 20px;
            left: 135px;
        }

        .busqueda_opcinal_2{
            position: absolute;
            background: #109EC8;
            color: #f1f4f8;
            width: 95px;
            height: 70px;
            top: 14px;
            right: 102px;
            cursor: pointer;
            -webkit-box-shadow: 0px 0px 0px 3px rgba(47, 50, 50, 0.34);
            -moz-box-shadow:    0px 0px 0px 3px rgba(47, 50, 50, 0.34);
            box-shadow:         0px 0px 0px 3px rgba(47, 50, 50, 0.34);
        }

        .control_2 .seleccionado{
            position: absolute;
            border-radius: 3px;
            background-color: #ab1c27;
            width: 96px;
            height: 5px;
            bottom: 21px;
            right: 102px;
        }
    </style>
    <script language="javascript" >
        var cursor;
        if (document.all) {
            // Está utilizando EXPLORER
            cursor = 'hand';
        } else {
            // Está utilizando MOZILLA/NETSCAPE
            cursor = 'pointer';
        }

        $(document).ready(function () {
            base_url  = $('#base_url').val();
            flagBS  = $('#flagBS').val();
            
            $('#buscarProducto').click(function () {
                activarBusqueda();
            });
            
            $("#nuevoProducto").click(function(){
                url = base_url+"index.php/almacen/producto/nuevo_producto/"+flagBS;
                location.href = url;
            });
            
            $("#limpiarProducto").click(function(){
                url = base_url+"index.php/almacen/producto/productos/"+flagBS;
                location.href=url;
            });
            
            function activarBusqueda() {
                var url = $('#form_busqueda').attr('action');
                var dataString = $('#form_busqueda').serialize();
                var flagBS = $('#flagBS').val();
                $.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    beforeSend: function (data) {
                        $('#cargando_datos').show();
                    },
                    success: function (data) {
                        $('#cargando_datos').hide();
                        $('#cuerpoPagina').html(data);
                    },
                    error: function (HXR, error) {
                        $('#cargando_datos').hide();
                        console.log('errrorrr');
                    }
                });
            }

            $('#busqueda_1').click(function(){
                var seleccionado = Number($('#seleccionado_1').val());
                if(seleccionado == 0){
                    $('.control_1 .seleccionado').css('background', '#29fb00');
                    $('#seleccionado_1').val("1");
                    $('.control_2 .seleccionado').css('background', '#ab1c27');
                    $('#seleccionado_2').val("0");
                    activarBusqueda();
                }
            });

            $('#busqueda_2').click(function(){
                var seleccionado = Number($('#seleccionado_2').val());
                if(seleccionado == 0){
                    $('.control_2 .seleccionado').css('background', '#29fb00');
                    $('#seleccionado_2').val("1");
                    $('.control_1 .seleccionado').css('background', '#ab1c27');
                    $('#seleccionado_1').val("0");
                    activarBusqueda();
                }

            });

            $('#buscarProducto').click(function () {
                activarBusqueda();
            });

            $("a#linkPublicar").fancybox({
                'width': 650,
                'height': 150,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': false,
                'modal': false,
                'type': 'iframe'
            });
            $("a#familiaBusqueda").fancybox({
                'width': 650,
                'height': 250,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': false,
                'modal': false,
                'type': 'iframe'
            });

            $("a#ingresar_series").fancybox({
                'width': 300,
                'height': 500,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': false,
                'modal': true,
                'type': 'iframe'
            });

            $("a#subirdoc").fancybox({
                'width': 650,
                'height': 150,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': false,
                'modal': false,
                'type': 'iframe'
            });
        });

        function cargar_familia(nivel, nombre, codfamilia, idfamilia) {
            $("#txtFamilia").val(nombre);//nombre
            $("#familiaid").val(idfamilia);//codigo
        }

        //function guardar_series(codigo,series,hdseries){
        function guardar_series(codigo, series) {
            //dataString        = "codigo="+codigo+"&series="+series+"&hdseries="+hdseries;
            dataString = "codigo=" + codigo + "&series=" + series;
            url = base_url + "index.php/almacen/producto/guardarseries";
            $.post(url, dataString, function (data) {

            });
        }

        function cambiarEstado(estado, producto){
            url = '<?php echo base_url(); ?>index.php/almacen/producto/cambiarEstado/';
            $.ajax({
                url : url,
                type: "POST",
                data: {
                    estado : Number(estado),
                    cod_producto : producto
                },
                dataType: "json",
                beforeSend: function(data){
                    $('#cargando_datos').show();
                },
                success: function(data){
                    if(data.cambio == true || data.cambio == 'true'){
                        $('#cargando_datos').hide();
                        alert('Cambio de estado correctamente!');
                        window.location = "<?php echo base_url(); ?>index.php/almacen/producto/productos/B";
                    }else{
                        $('#cargando_datos').hide();
                        alert('Ah Ocurrido un error con el cambio de estado!');
                    }
                },
                error: function(data){
                    $('#cargando_datos').hide();
                    console.log('Error en cambio de fase');
                }
            });
        }
    </script>
</head>
<body>
<div id="cuerpoPagina" >
    <form id="frmpublicar" name="frmpublicar" method="post" enctype="multipart/form-data" action="">
        <div class="acciones">
            <div id="botonBusqueda">
                <ul id="imprimirProducto" class="lista_botones">
                    <li id="imprimir">Imprimir</li>
                </ul>
                <ul id="nuevoProducto" class="lista_botones">
                    <li id="nuevo">
                        Nuevo <?php if ($flagBS == 'B') echo 'Artículo'; else echo 'Servicio'; ?></li>
                </ul>
                <ul id="limpiarProducto" class="lista_botones">
                    <li id="limpiar">Limpiar</li>
                </ul>
                <ul id="buscarProducto" class="lista_botones">
                    <li id="buscar">Buscar</li>
                </ul>
                <ul id="buscarProducto2" class="lista_botones" style="display: none;">
                    <li id="buscar">Buscar2</li>
                </ul>

            </div>
            <div id="lineaResultado">
                <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
                    <tr>
                        <td width="50%" align="left">N de productos
                            encontrados:&nbsp;<?php echo $registros; ?> </td>
                </table>
            </div>
        </div>
        <a id='ingresar_series' class='fancybox' href='"<?php echo base_url(); ?>"index.php/almacen/producto/ventana_nueva_serie/'></a>

        <div id="cabeceraResultado" class="header"><?php
            echo $titulo_tabla;
            ?></div>
        <div id="frmResultado">
            <div id="cargando_datos" class="loading-table">
                <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
            </div>
                    <table class="fuente8 display" width="100%" cellspacing="0" cellpadding="3" border="0" id="table-productos">
                        <thead>
                            <tr class="cabeceraTabla">
                                <th width="3%">ITEM</th>
                                <th width="5%" align='center'>CODIGO</th>
                                <th>DESCRIPCION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <?php if (FORMATO_IMPRESION != 4) { ?>
                                    <th width="20%">FAMILIA</th><?php } ?>
                                <?php if ($flagBS == 'B') { ?>
                                    <th width="15%">Marca</th>
                                    <!--<td width="5%">P. VENTA</td>-->
                                    <th width="7%">P. COSTO</th>
                                <?php } ?>
                                
                                <th colspan="5">EDITAR</th>
                            </tr>
                        </thead>
                        <tbody><?php
                            if (count($lista) > 0) {
                                $ppc = -1;
                                foreach ($lista as $indice => $valor) { $ppc++;
                                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla'; ?>
                                    <tr class="<?php echo $class; ?>">
                                        <td><?php echo $valor[16]; ?>
                                            <div align="center"><?php echo $valor[0]; ?></div>
                                        </td>
                                        <td>
                                            <div align="center"><?php if ($valor[1] != '') 
                                                echo str_pad($valor[1], "3", "0", STR_PAD_LEFT); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div align="left"><?php echo $valor[2]; ?></div>
                                        </td>
                                        <td>
                                            <div align="left"><?php echo $valor[3]; ?></div>
                                        </td><?php

                                        if ($flagBS == 'B') { ?>
                                            <td>
                                                <div align="center"><?php echo $valor[5]; ?></div>
                                            </td>
                                            <!--<td> PRECIO VENTA
                                                <div align="right"><?php if ($valor[6] != 0 && $valor[6] != '') echo number_format($valor[6], 2); ?></div>
                                            </td>-->
                                            <td>
                                                <div style="text-align: left;">
                                                    <div class="costo"> <img src="<?=base_url().'images/icono_nuevo.png'?>" height="12px"/>
                                                        <span class="editar_costo">
                                                            <form action="<?=base_url();?>" method="POST">
                                                                <input type="hidden" id="1<?=$ppc;?>" name="1<?=$ppc;?>" value="<?=$valor[14];?>"/>
                                                                <input type="number" placeholder="Precio costo" style="width:6em;" id="2<?=$ppc;?>" name="2<?=$ppc;?>" step="0.01" min="0.01" value="<?=$valor[7];?>"/>
                                                                <a href="#" onclick="insertar_costo(<?=$ppc?>)"> <img src="<?=base_url().'images/botonaceptar.jpg'?>" name="envcosto"/> </a>
                                                            </form>
                                                        </span>
                                                    </div>
                                                    <span style="color:red; font-weight: bold"><?php echo number_format($valor[7],2); ?><span>
                                                </div>
                                            </td><?php
                                        } ?>
                                        <td>
                                            <!--<div align="center"><?php echo $valor[8]; ?></div>-->
                                        </td>
                                        <td>
                                            <div align="center"><?php echo $valor[9]; ?></div>
                                        </td>
                                        <td>
                                             <div align="center"><?php echo $valor[15]; ?></div>
                                        </td>
                                        <!-- <td>
                                            <div align="center"><?php echo $valor[11]; ?></div>
                                        </td>-->

                                        <td>
                                            <!--<div align="center"><?php echo $valor[12]; ?></div>-->
                                        </td>
                                    </tr> <?php
                                }
                            } ?>
                    </tbody>
                    </table>
                </div>
        <div style="margin-top: 15px;">
        <?php
       echo $paginacion;
        ?>
        </div>
        <input type="hidden" id="iniciopagina" name="iniciopagina">
        <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">

        <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>"/>

        <input type="hidden" name="flagBS" id="flagBS" value="<?php echo $flagBS; ?>"/>

    </form>
</div>
</body>

</html>