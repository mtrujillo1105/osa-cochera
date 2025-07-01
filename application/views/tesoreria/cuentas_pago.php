<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
    header("location:$url");
?>
<html>
<head>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/tesoreria/cuentas.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>"
          media="screen"/>
    <script type="text/javascript">
        $(document).ready(function () {
            <?php
            if ($tipo_cuenta == 1) {
            ?>
            if ($('#tdc').val() == '') {
                alert("Antes de registrar Cobros debe ingresar Tipo de Cambio");
                top.location = "<?php echo base_url(); ?>index.php/index/inicio";
            }
            <?php
            }else if ($tipo_cuenta == 2) {
            ?>
            if ($('#tdc').val() == '') {
                alert("Antes de registrar Pagos debe ingresar Tipo de Cambio");
                top.location = "<?php echo base_url(); ?>index.php/index/inicio";
            }
            <?php
            }
            ?>

            $("a#linkVerCliente, a#linkVerProveedor, a#linkNotaCredito").fancybox({
                'width': 800,
                'height': 550,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': false,
                'modal': true,
                'type': 'iframe'
            });
             $("a#linkVerCliente, a#linkSelecCliente, a#linkVerProveedor, a#linkSelecProveedor, a#linkedicliente").fancybox({
                'width': 800,
                'height': 550,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': true,
                'modal': false,
                'type': 'iframe'

            });

            $('#notaCredito').change(function () {
                var nota = $(this).val();
                var costoNota = $("#notaCredito option:selected").attr("id");
                if (nota == 0 || nota == '0') {
                    $('#detalle_nota').attr('onClick', '');
                } else {
                    $('#detalle_nota').attr('onClick', 'comprobante_ver_pdf_conmenbrete2(1, ' + nota + ')');
                    $('#monto').val(costoNota);
                }
            });

            $('#nombre_cliente').click(function(){
               $('#nombre_cliente').val("");
               $('#ruc_cliente').val("");
            });

            $('#ruc_cliente').click(function(){
                $('#nombre_cliente').val("");
                $('#ruc_cliente').val("");
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
                        
                        $('#aplicarpago').attr('name', 'aplica');
                        $('#linkNotaCredito').attr('href', base_url+'index.php/tesoreria/cuentas/ventana_muestra_notaCredito_cliente/'+ui.item.codigo);
                        $('#vistaNotaDetalle').hide('fast');
                        $('#codigoNota').val("");
                        $('#monto').val("");
                        mostrar_cuentas();
                        buscarBancosClien();
                        buscarMisBancos();

                    },
                    minLength: 2
            });

            // BUSQUEDA POR RUC
            $("#ruc_cliente").autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                            type: "POST",
                            data: {
                                term: $("#buscar_cliente").val()
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
                        $("#nombre_cliente").val(ui.item.nombre);
                        $("#buscar_cliente").val(ui.item.ruc);
                        $("#cliente").val(ui.item.codigo);
                        $("#ruc_cliente").val(ui.item.ruc);
                        $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                        
                        $('#aplicarpago').attr('name', 'aplica');
                        $('#linkNotaCredito').attr('href', base_url+'index.php/tesoreria/cuentas/ventana_muestra_notaCredito_cliente/'+ui.item.codigo);
                        $('#vistaNotaDetalle').hide('fast');
                        $('#codigoNota').val("");
                        $('#monto').val("");
                        mostrar_cuentas();
                        buscarBancosClien();
                        buscarMisBancos();
                    },
                    minLength: 2
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
                        
                        $('#linkNotaCredito').attr('href', base_url+'index.php/tesoreria/cuentas/ventana_muestra_notaCredito_proveedor/'+ui.item.codigo);
                        $('#vistaNotaDetalle').hide('fast');
                        $('#codigoNota').val("");
                        $('#monto').val("");
                        mostrar_cuentas();
                        buscarBancosProve();
                        buscarMisBancos();
                    },
                    minLength: 2
            });

            // BUSQUEDA POR RUC PROVEEDOR
            $("#ruc_proveedor").autocomplete({
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

                        $('#linkNotaCredito').attr('href', base_url+'index.php/tesoreria/cuentas/ventana_muestra_notaCredito_proveedor/'+ui.item.codigo);
                        $('#vistaNotaDetalle').hide('fast');
                        $('#codigoNota').val("");
                        $('#monto').val("");
                        mostrar_cuentas();
                        buscarBancosProve();
                        buscarMisBancos();
                    },
                    minLength:2 
            });

            $('#close').click(function(){
                $('#popup').fadeOut('slow');
                $('.popup-overlay').fadeOut('slow');
                return false;
            });
        });

        function seleccionarNota(codigo_nota, costoTotal_nota, tipoDoc_nota, numero_nota){
            $('#codigoNota').val(codigo_nota);
            $('#monto').val(costoTotal_nota);
            $('#vistaNotaDetalle').html('');
            if(tipoDoc_nota == "F"){
                $('#vistaNotaDetalle').html('[FACTURA] '+ numero_nota);
            }else if(tipoDoc_nota == "B"){
                $('#vistaNotaDetalle').html('[BOLETA] '+ numero_nota);
            }else if(tipoDoc_nota == "N"){
                $('#vistaNotaDetalle').html('[COMPROBANTE] '+ numero_nota);
            }else{
                $('#vistaNotaDetalle').html('[INDEPENDIENTE]');
            }
            $('#vistaNotaDetalle').show('slow');
        }

        function seleccionar_cliente(codigo, ruc, razon_social) {
            $("#cliente").val(codigo);
            $("#ruc_cliente").val(ruc);
            $("#nombre_cliente").val(razon_social);
            $('#aplicarpago').attr('name', 'aplica');
            $('#linkNotaCredito').attr('href', base_url+'index.php/tesoreria/cuentas/ventana_muestra_notaCredito_cliente/'+codigo);
            $('#vistaNotaDetalle').hide('fast');
            $('#codigoNota').val("");
            $('#monto').val("");
            mostrar_cuentas();
            buscarBancosClien();
            buscarMisBancos();
        }
        function seleccionar_proveedor(codigo, ruc, razon_social) {
            $("#proveedor").val(codigo);
            $("#ruc_proveedor").val(ruc);
            $("#nombre_proveedor").val(razon_social);
            $('#linkNotaCredito').attr('href', base_url+'index.php/tesoreria/cuentas/ventana_muestra_notaCredito_proveedor/'+codigo);
            $('#vistaNotaDetalle').hide('fast');
            $('#codigoNota').val("");
            $('#monto').val("");
            mostrar_cuentas();

            buscarBancosProve();
            buscarMisBancos();
        }

        function buscarBancosClien(){
            codigoCliente = $("#cliente").val();

            var url= "<?php echo base_url(); ?>index.php/tesoreria/cuentas/obtenerBancosCliente";
                         $.ajax({url: url,type: "POST",
                             data:{
                                 codigoCliente:codigoCliente
                                 },
                             dataType: "json",
                             success: function(data){
                                 $("#banco").html("");
                                 fila = "";
                                 fila+="<option value='00' > SELECCIONAR </option>";
                                     $.each(data, function (i, item) {                   
                                     fila+= "<option value='"+item.bancocodigo+"'>" + item.banconombre + "</option>";                    
                                     }); 
                                     $("#banco").html(fila);
                             }  
                                 
                                 
                         });
            }

        function buscarBancosProve(){
            codigoProveedor = $("#proveedor").val();

            var url= "<?php echo base_url(); ?>index.php/tesoreria/cuentas/obtenerBancosProveedor";
            $.ajax({url: url,type: "POST",
            data:{
                 codigoproveedor:codigoProveedor
            },
            dataType: "json",
            success: function(data){
                 $("#banco").html("");
                 fila = "";
                 fila+="<option value='00' > SELECCIONAR </option>";
                    $.each(data, function (i, item) {                    
                        fila+= "<option value='"+item.bancocodigo+"'>" + item.banconombre + "</option>";                     
                    }); 
                    $("#banco").html(fila);
                }    
            });
        }

        function buscarMisBancos(){
            codigoProveedor = $("#proveedor").val();

            var url= "<?php echo base_url(); ?>index.php/tesoreria/cuentas/obtenerMisBancos";
            $.ajax({url: url,type: "POST",
                data:{
                    codigoproveedor:codigoProveedor
                    },
                dataType: "json",
                success: function(data){
                    $("#miBanco").html("");
                    fila = "";
                    fila+="<option value='00' > SELECCIONAR </option>";
                        $.each(data, function (i, item) {                   
                        fila+= "<option value='"+item.bancocodigo+"'>" + item.banconombre + "</option>";                    
                        }); 
                        $("#miBanco").html(fila);
                }  
                    
                    
            });
        }


        function buscarCuentaProve(){ // BUSCA CUENTAS DE PROVEEDORES Y DE CLIENTES
            tipo_cuenta = $("#tipo_cuenta").val();
            var url;
            $("#divtxtmoneda").hide();
            codigoBanco = $("#banco").val();

            if(tipo_cuenta == 1)
                codigoPersona = $("#cliente").val(); // CLIENTE
            else
                codigoPersona = $("#proveedor").val(); // PROVEEDOR

            url = "<?php echo base_url(); ?>index.php/tesoreria/cuentas/obtenerCuentasProveedor";
            $.ajax({url: url,type: "POST",
                data:{
                    tipo_cuenta:tipo_cuenta,
                    codigobanco:codigoBanco,
                    codigopersona:codigoPersona
                    },
                    dataType: "json",
                    success: function(data){
                        $("#ctacte").html("");
                        fila = "";
                        fila+= "<option value='0'> SELECCIONAR </option>";
                            $.each(data, function (i, item){
                                fila+= "<option value='"+item.codigocuenta+"'>" + item.nombrecuenta + "</option>";                   
                                });
                        $("#ctacte").html(fila);
                        }
                    });
        
        }

        function buscarMisCuenta(){
            $("#divtxtmimoneda").hide();

            var url ;
            codigoBanco = $("#miBanco").val();

            url = "<?php echo base_url(); ?>index.php/tesoreria/cuentas/obtenerMisCuentas";
            $.ajax({url: url,type: "POST",
                data:{ codigobanco:codigoBanco },
                    dataType: "json",
                    success: function(data){
                        $("#miCtacte").html("");
                        fila = "";
                        fila+= "<option value='0'> SELECCIONAR </option>";
                            $.each(data, function (i, item){
                                fila+= "<option value='"+item.codigocuenta+"'>" + item.nombrecuenta + "</option>";                   
                                });
                        $("#miCtacte").html(fila);
                        }
                    });
        }

        function buscarDatosDeCuenta(){
            var sel2 = document.getElementById("ctacte");
            nombrecuentas = sel2.options[sel2.selectedIndex].text;  

            var idcaja = $("#ctacte").val();
            if(idcaja != '00'){
                $("#divtxtmoneda").show();
                $("#divtxtmoneda").show();
              var url = "<?php echo base_url(); ?>index.php/tesoreria/cuentas/buscarDatosDeCuenta";
                    $.ajax({url:url,type:"POST",
                            data:{
                                nombrecuentas:nombrecuentas
                                },
                                    dataType:"json",
                                    success: function(data){
                                        $("#divtxtmoneda").html("");
                                        fila="";
                                        $.each(data,function(i,item){
                                            fila+="<a>"+item.monedadescripcion+"</a>";
                                            
                                        });
                                        $("#divtxtmoneda").html(fila);
                                    }
                            });
            }else{
                $("#divtxtmoneda").hide();
            }
        }

        function buscarDatosDeMiCuenta(){
            var sel2 = document.getElementById("miCtacte");
            nombrecuentas = sel2.options[sel2.selectedIndex].text;  

            var idcaja = $("#miCtacte").val();
            if(idcaja != '00'){
                $("#divtxtmimoneda").show();
                $("#divtxtmimoneda").show();
              var url = "<?php echo base_url(); ?>index.php/tesoreria/cuentas/buscarDatosDeCuenta";
                    $.ajax({url:url,type:"POST",
                            data:{
                                nombrecuentas:nombrecuentas
                                },
                                    dataType:"json",
                                    success: function(data){
                                        $("#divtxtmimoneda").html("");
                                        fila="";
                                        $.each(data,function(i,item){
                                            fila+="<a>"+item.monedadescripcion+"</a>";
                                            
                                        });
                                        $("#divtxtmimoneda").html(fila);
                                    }
                            });
            }else{
                $("#divtxtmimoneda").hide();
            }
        }

        /**Obtener los bancos de la caja**/
        function obtenerBancosCajaDiaria(){
            var idcajadiaria = $("#idcajadiaria").val();

            if($('#idcajadiaria').val() != '00' /*&& ($('#forma_pago').val() == 2 || $('#forma_pago').val() == 3 || $('#forma_pago').val() == 7)*/){
                $('#tdCuentaCaja').hide();
                $('#idmonedacaja').hide();
                $("#tdBancoCaja").show();
                
                var url = "<?php echo base_url(); ?>index.php/tesoreria/cuentas/buscarBancosDeCaja";
                $.ajax({url:url,type:"POST",
                        data:{
                            idcajadiaria:idcajadiaria
                         },
                                dataType:"json",
                                success:function(data){
                                    $("#idbancoscaja").html("");
                                    fila="";
                                    fila+="<option value='00'>::Seleccionar::</option>";
                                    $.each(data,function(i,item){
                                        fila+="<option value='"+item.bancocodigo+"'>"+item.banconombre+"</option>";
                                    });
                                    $("#idbancoscaja").html(fila);
                                }
                        });
            }else{
                $('#tdCuentaCaja').hide();
                $('#idmonedacaja').hide();
                $("#tdBancoCaja").hide();
                
                }
        }


        /** Obtener las Cuentas de Cada BancoCaja **/
        function obtenerCuentasCaja(){
            var idcajadiaria = $("#idcajadiaria").val();
            var idbancoscaja = $("#idbancoscaja").val();

            if($("#idbancoscaja").val() != 00){
                $('#tdCuentaCaja').show();
                var url = "<?php echo base_url(); ?>index.php/tesoreria/cuentas/obtenerCuentasCaja";
                $.ajax({url:url,type:"POST",
                        data:{
                            idcajadiaria:idcajadiaria,
                            idbancoscaja:idbancoscaja
                        },
                            dataType:"json",
                            success:function(data){
                                $("#idcuentacaja").html("");
                                fila="";
                                fila+="<option value='00'>::Seleccionar::</option>";
                                $.each(data,function(i,item){
                                    fila+="<option  value='"+item.codigo+"'>"+item.numerocaja+"</option>";
                                });
                                $("#idcuentacaja").html(fila);
                            }
                        });
            }else{
                $('#tdCuentaCaja').hide();
                $('#idmonedacaja').hide();
                }
        }

        /*** Obtener los datos de las Cuentas de CAJA ***/
        function obtenerDatosCuentasCaja(){
            var CodigoNCuenta = document.getElementById("idcuentacaja");
              nombrecuentas = CodigoNCuenta.options[CodigoNCuenta.selectedIndex].text;
            var idcaja = $("#idcuentacaja").val();
            
            if(idcaja != 0){
                $("#idmonedacaja").show();
                $("#idmonedacaja").show();
                var url = "<?php echo base_url(); ?>index.php/tesoreria/cuentas/obtenerDatosCuentaCaja";    
                $.ajax({url:url,type:"POST",
                    data:{
                        nombrecuentas:nombrecuentas
                        },
                            dataType:"json",
                            success:function(data){
                                $("#idmonedacaja").html("");
                                $.each(data,function(i,item){
                                    fila="<a>"+item.monedadescripcion+"</a>";
                                });
                                    $("#idmonedacaja").html(fila);
                            }   
                });
            }else{
                $("#idmonedacaja").hide();
            }
        }
        
        
    </script>
</head>
<body>
<?php echo $form_open; ?>
<div id="zonaContenido" align="center">
    <?php echo validation_errors("<div class='error'>", '</div>'); ?>
    <div id="tituloForm" class="header"><?php echo $titulo; ?></div>
    <div id="frmBusqueda">
        <input type="hidden" value="<?php echo $tipo_cuenta; ?>" id="tipo_cuenta" >
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
            <tr>
                <?php if ($tipo_cuenta == '1') { ?>
                    <td>Cliente *</td>
                    <td valign="middle" colspan="6">
                        <input type="text" name="cliente" id="cliente" size="5" hidden/>
                        <input type="text" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" placeholder="Ruc" onkeypress="return numbersonly(this,event,'.');" />
                        <input type="text" name="nombre_cliente" class="cajaGeneral cajaSoloLectura" id="nombre_cliente" size="40" maxlength="50" placeholder="Nombre cliente" />
                       <a href="<?php echo base_url(); ?>index.php/empresa/cliente_ventana_busqueda/" id="linkSelecCliente"></a>
                    </td>
                <?php } else { ?>
                    <td>Proveedor *</td>
                    <td valign="middle" colspan="6">
                        <input type="text" name="proveedor" id="proveedor" size="5" hidden/>
                        <input type="text" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onkeypress="return numbersonly(this,event,'.');" placeholder="Ruc" />
                        <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="40" maxlength="50" placeholder="Nombre Proveedor"/>
                        <a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkSelecProveedor"></a>
                    </td>
                <?php } ?>
            </tr>
            <tr>
                <td width="15%">Fecha *</td>
                <td width="15%">
                    <input NAME="fecha" type="text" class="cajaGeneral" id="fecha" value="<?php echo date('d/m/Y'); ?>"
                           size="10" maxlength="10" readonly="readonly"/>
                    <img height="16" border="0" width="16" id="Calendario1" name="Calendario1"
                         src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "fecha",      // id del campo de texto
                            ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                            button: "Calendario1"   // el id del botón que lanzará el calendario
                        });
                    </script>
                </td>
                <td width="10%">TDC *
                    <input NAME="tdc" type="text" class="cajaGeneral cajaSoloLectura" id="tdc"
                           value="<?php echo $tdc; ?>" size="7" maxlength="5" readonly="readonly"/>
                </td>
                <td width="10%">
                    Monto *
                    <input NAME="monto" type="text" class="cajaGeneral" id="monto" style="width:70px;"/>
                </td>
                <td width="18%" align="left">Moneda *<select name="moneda" id="moneda" class="comboPequeno"
                                                      style="width:150px;"><?php echo $cboMoneda; ?></select></td>
            </tr>
            <tr>
                <td align='left'>Estado Pago</td>
                <td align='left' colspan="3">
                    <select id="estado_pago2" name="estado_pago2" class="comboPequeno">
                        <option value="T">TODOS</option>
                        <option value="C">Cancelado</option>
                        <option value="P" selected="selected">Pendiente</option>
                    </select>
                </td>
                <td align="left">N°
               
                  <input type="text" name="compra_serie" id="compra_serie" size="3" value="<?php echo $serie_suger_f ?>" readonly>
                  <input type="text" name="compra_numero" id="compra_numero" size="6" value="<?php echo $numero_suger_f ?>" readonly>
              
                </td>
            </tr>
            <tr>
                <td align='left'>Tipo Documento</td>
                <td align='left' colspan="6">
                    <select id="tipo_docu" name="tipo_docu" class="comboPequeno">
                        <option value="T">TODOS</option>
                        <option value="BOLETA">Boleta</option>
                        <option value="FACTURA">Factura</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Forma de Pago *</td>
                <td>
                    <select name="forma_pago" id="forma_pago" class="comboPequeno" style="width:140px;">
                        <option value="1">EFECTIVO</option>
                        <option value="2">DEPOSITO</option>
                        <option value="3">CHEQUE</option>
                        <!--<option value="4">CANJE POR FACTURA</option>-->
                        <option value="5">NOTA DE CREDITO</option>
                        <option value="6">DESCUENTO</option>
                        <option value="7">TRANSFERENCIA</option>
                    </select>
                </td>
                
                <td>
                    <!-- DEPOSITO -->
                    <span class="formaPago deposito" id="formaPago2" style="display:none;">
                        BANCO DEL <?=($tipo_cuenta == 2) ? "PROVEEDOR" : "CLIENTE";?>
                        <select name="banco" id="banco" class="comboPequeno" style="width:210px;" <?=($tipo_cuenta == 2) ? "onchange='buscarCuentaProve()'" : "onchange='buscarCuentaProve()'"; ?>> </select><br>
                                
                        CTA. CTE. DEL <?=($tipo_cuenta == 2) ? "PROVEEDOR" : "CLIENTE";?><br>
                        <select name="ctacte" id="ctacte" class="comboPequeno" style="width:210px;" onchange="buscarDatosDeCuenta(this)"> </select> <a id="divtxtmoneda"></a>
                    </span>
                </td>
                <td colspan="2">
                    <!-- DEPOSITO -->
                    <span class="formaPago deposito" id="formaPago2" style="display:none;">
                        BANCO DE EMPRESA <?=$_SESSION['nombre_empresa'];?><br>
                        <select name="miBanco" id="miBanco" class="comboPequeno" style="width:210px;" <?=($tipo_cuenta == 1) ? "onchange='buscarMisCuenta()'" : "onchange='buscarMisCuenta()'"; ?>> </select><br>
                                
                        CTA. CTE.<br>
                        <select name="miCtacte" id="miCtacte" class="comboPequeno" style="width:210px;" onchange="buscarDatosDeMiCuenta(this)"> </select> <a id="divtxtmimoneda"></a>
                    </span>
                </td>
            </tr>
            <tr style="display: none;">
                <td>Caja : <select id='idcajadiaria' name='idcajadiaria' class="comboPequeno" style="width:140px;" onchange="obtenerBancosCajaDiaria(this)"  ><?php echo $cmbCaja; ?></select></td>
                <td id="tdBancoCaja" hidden>Bancos : <select id='idbancoscaja' name='idbancoscaja' style="width:210px;" class="comboPequeno"  onchange="obtenerCuentasCaja(this)"> </select></td>
                <td id="tdCuentaCaja" hidden>
                      Cuenta Caja: <select id='idcuentacaja' name='idcuentacaja' class="comboPequeno"  style="width:180px;" onchange="obtenerDatosCuentasCaja(this)" > </select>
                      <a id="idmonedacaja" hidden></a>
                </td>
            </tr>
                     <tr>
                     <td colspan="4">
                    <!-- CHEQUE -->
                    <span class="formaPago" id="formaPago3" style="display:none;">
                                Nro
                                <input NAME="nroCheque" type="text" class="cajaGeneral" id="nroCheque"
                                       style="width:70px;"/>
                                F. Emision
                                <input NAME="fechaEmi" type="text" class="cajaGeneral" id="fechaEmi" size="10"
                                       maxlength="10" readonly="readonly"/>
                                <img height="16" border="0" width="16" id="Calendario2" name="Calendario1"
                                     src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechaEmi",      // id del campo de texto
                                        ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario2"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                                F. Vencimiento
                                <input NAME="fechaVenc" type="text" class="cajaGeneral" id="fechaVenc" size="10"
                                       maxlength="10" readonly="readonly"/>
                                <img height="16" border="0" width="16" id="Calendario3" name="fechaVenc"
                                     src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechaVenc",      // id del campo de texto
                                        ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario3"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                            </span>
                    <!-- CANJE POR FACTURA -->
                            <span class="formaPago" id="formaPago4" style="display:none;">
                                Factura
                                <select name="factura" id="factura" class="comboGrande" style="width:180px;">
                                </select>
                            </span>
                    <!-- NOTA CREDITO -->

                            <div class="formaPago" id="formaPago5" style="display:none; position: relative">
                                <!--Nota de Crédito -->
                                <select name="notaCredito" id="notaCredito" class="comboGrande" style="width:180px; visibility: hidden;">
                                </select>
                                <a style="padding-left: 15px; position: relative" href="#" id="detalle_nota">
                                    <img style="top: -7px; position: absolute" src="<?php echo base_url(); ?>public/images/icons/ver_detalle.png?=<?=IMG;?>" alt=""/>
                                </a>
                                <div id="vistaNotaDetalle" style="background-color: #C70; color: #FFF; top: -5px; left: 32px; padding: 5px; width: auto; height: auto; display: none; position: absolute"> </div>
                                <input type="hidden" id="codigoNota" name="codigoNota" value=""/>
                                <a id="linkNotaCredito" >
                                    <img id="buscarNotas" src="<?php echo base_url(); ?>public/images/icons/icono-factura.gif?=<?=IMG;?>" alt="Refresh" style="cursor:pointer;"/>
                                </a>
                            </div>

                    <!-- DESCUENTO -->
                            <span class="formaPago" id="formaPago6" style="display:none;">
                                Observación
                                <input name="obsDesc" type="text" class="cajaGeneral" id="obsDesc" style="width:400px; text-transform: capitalize;" placeholder="Indique una observación"/>
                            </span>
                    <span class="formaPago" id="formaPago7" style="display:none;">
                    N de transferencia
                    <input type="text" name="trans" id="trans">
                    </span>
                </td>
               
                <td align="right">
                    <a href="javascript:;" id="aplicarpago" name="aplica" title="Aplicar el pago">
                        <img src="<?php echo base_url(); ?>public/images/icons/botonpago.png?=<?=IMG;?>" width="85" height="22" class="imgBoton" border="0"/></a>
                    <a href="javascript:;" id="verpagos" title="Ultimos Pagos realizados">
                        <img src="<?php echo base_url(); ?>public/images/icons/botonpagos.png?=<?=IMG;?>" width="56" height="22" class="imgBoton" border="0"/>
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <div id="frmBusqueda" style="height:250px; overflow: auto">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
            <tr class="cabeceraTabla">
                <td width="0.5%"><div align="center">ITEM</div></td>
                <td width="7.2%" style="cursor:pointer;" onclick="ordenar();"><div align="center" title="click para Ordenar">FECHA</div></td>
                <input type="hidden" name="ordenar" id="ordenar" value="DESC" />
                <td width="15%"><div align="center">Comprobante</div></td>
                <td width="13%"><div align="center">SERIE / N&Uacute;MERO</div></td>
                <td width="7%"><div align="center">MONEDA</div></td>
                <td width="7%"><div align="center">MONTO</div></td>
                <td width="7%"><div align="center">AVANCE</div></td>
                <td width="10%"><div align="center">SALDO</div></td>
                <td width="18%"><div align="center">ESTADO</div></td>
                <td width="10%"><div align="center">Ver PDF</div></td>
                <td width="2%"><div align="center"></div></td>
                <td width="2%"><div align="center"></div></td>
                <td width="2%"><div align="center"></div></td>
                <td width="2%"><div align="center"></div></td>
            </tr>
        </table>
        <div>
            <table id="tblDetallePago" class="fuente8" width="100%" border="0">
                <?php
                if (count($detalle_cuentas) > 0) {
                    foreach ($detalle_cuentas as $indice => $valor) {
                        $detacodi = $valor->CPDEP_Codigo;
                        $flagBS = $valor->flagBS;
                        $prodproducto = $valor->PROD_Codigo;
                        $unidad_medida = $valor->UNDMED_Codigo;
                        $codigo_interno = $valor->PROD_CodigoInterno;
                        $prodcantidad = $valor->CPDEC_Cantidad;
                        $nombre_producto = $valor->PROD_Nombre;
                        $nombre_unidad = $valor->UNDMED_Simbolo;
                        $prodpu = $valor->CPDEC_Pu;
                        $prodsubtotal = $valor->CPDEC_Subtotal;
                        $proddescuento = $valor->CPDEC_Descuento;
                        $prodigv = $valor->CPDEC_Igv;
                        $prodtotal = $valor->CPDEC_Total;
                        $prodpu_conigv = $valor->CPDEC_Pu_ConIgv;
                        $prodsubtotal_conigv = $valor->CPDEC_Subtotal_ConIgv;
                        $proddescuento_conigv = $valor->CPDEC_Descuento_ConIgv;
                        if (($indice + 1) % 2 == 0) {
                            $clase = "itemParTabla";
                        } else {
                            $clase = "itemImparTabla";
                        }
                        ?>
                        <tr class="<?php echo $clase; ?>">
                            <td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_comprobante(<?php echo $indice; ?>);"><span style="border:1px solid red; background: #ffffff; ">&nbsp;&nbsp;</span></a></strong></font></div></td>
                            <td width="4%"><div align="center"><?php echo $indice + 1; ?></div></td>
                            <td width="10%"><div align="center"><?php echo $codigo_interno; ?></div></td>
                            <td><div align="left"><input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[<?php echo $indice; ?>]" id="proddescri[<?php echo $indice; ?>]" value="<?php echo $nombre_producto; ?>" /></div></td>

                            <?php if ($tipo_docu != 'B') { ?>
                                <td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[<?php echo $indice; ?>]" id="prodcantidad[<?php echo $indice; ?>]" value="<?php echo $prodcantidad; ?>" onblur="calcula_importe(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" /><?php echo $nombre_unidad; ?></div></td>
                                <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu[<?php echo $indice; ?>]" id="prodpu[<?php echo $indice; ?>]" value="<?php echo $prodpu; ?>" onblur="modifica_pu(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" />
                                        <input type="hidden" name="prodpu_conigv[<?php echo $indice; ?>]" id="prodpu_conigv[<?php echo $indice; ?>]" value="<?php echo $prodpu_conigv; ?>" /></div></td>
                                <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[<?php echo $indice; ?>]" id="prodprecio[<?php echo $indice; ?>]" value="<?php echo $prodsubtotal; ?>" readonly="readonly" /></div></td>
                            <?php } else { ?>
                                <td width="10%"><div align="left"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[<?php echo $indice; ?>]" id="prodcantidad[<?php echo $indice; ?>]" value="<?php echo $prodcantidad; ?>" onblur="calcula_importe_conigv(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" /><?php echo $nombre_unidad; ?></div></td>
                                <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[<?php echo $indice; ?>]" id="prodpu_conigv[<?php echo $indice; ?>]" value="<?php echo $prodpu_conigv; ?>" onblur="calcula_importe_conigv(<?php echo $indice; ?>);" onkeypress="return numbersonly(this,event,'.');" /></div></td>
                                <td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[<?php echo $indice; ?>]" id="prodprecio_conigv[<?php echo $indice; ?>]" value="<?php echo $prodsubtotal_conigv; ?>" readonly="readonly" /></div></td>
                            <?php } ?>
                            <td width="6%">
                                <div align="center">
                                    <input type="hidden" class="cajaGeneral" readonly name="proddescuento100[<?php echo $indice; ?>]" id="proddescuento100[<?php echo $indice; ?>]" value="<?php echo $descuento; ?>" />
                                    <?php if ($tipo_docu != 'B') { ?>
                                        <input type="text" size="5" maxlength="10" class="cajaGeneral" readonly name="proddescuento[<?php echo $indice; ?>]" id="proddescuento[<?php echo $indice; ?>]" value="<?php echo $proddescuento; ?>" onblur="calcula_importe2(<?php echo $indice; ?>);" />
                                    <?php } else { ?>
                                        <input type="text" size="5" maxlength="10" class="cajaGeneral" readonly name="proddescuento_conigv[<?php echo $indice; ?>]" id="proddescuento_conigv[<?php echo $indice; ?>]" value="<?php echo $proddescuento_conigv; ?>" onblur="calcula_importe2_conigv(<?php echo $indice; ?>);" />
                                    <?php } ?>
                                </div>
                            </td>
                            <?php if ($tipo_docu != 'B') { ?>
                                <td width="6%">
                                    <div align="center">
                                        <input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[<?php echo $indice; ?>]" id="prodigv[<?php echo $indice; ?>]" readonly="readonly" value="<?php echo $prodigv; ?>" />
                                    </div>
                                </td>
                            <?php } ?>
                            <td width="6%">
                                <div align="center">
                                    <input type="hidden" name="detaccion[<?php echo $indice; ?>]" id="detaccion[<?php echo $indice; ?>]" value="m"/>
                                    <input type="hidden" name="prodigv100[<?php echo $indice; ?>]" id="prodigv100[<?php echo $indice; ?>]" value="<?php echo $igv; ?>"/>
                                    <input type="hidden" name="detacodi[<?php echo $indice; ?>]" id="detacodi[<?php echo $indice; ?>]" value="<?php echo $detacodi; ?>"/>
                                    <input type="hidden" name="flagBS[<?php echo $indice; ?>]" id="flagBS[<?php echo $indice; ?>]" value="<?php echo $flagBS; ?>" />
                                    <input type="hidden" name="prodcodigo[<?php echo $indice; ?>]" id="prodcodigo[<?php echo $indice; ?>]" value="<?php echo $prodproducto; ?>"/>
                                    <input type="hidden"  name="produnidad[<?php echo $indice; ?>]" id="produnidad[<?php echo $indice; ?>]" value="<?php echo $unidad_medida; ?>"/>
                                    <input type="text" size="5"  class="cajaGeneral cajaSoloLectura" name="prodimporte[<?php echo $indice; ?>]" id="prodimporte[<?php echo $indice; ?>]" readonly="readonly" value="<?php echo $prodtotal; ?>"/>
                                </div>
                            </td>
                        </tr>
                    <?php
                    } ?>
                        <tr>
                            <td colspan="14"><div align="center">REPORTE PDF</div></td>
                        </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
    <div id="frmBusqueda3">
        <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
            <tr>
                <td width="80%" rowspan="4" align="left">
                    <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
                        <tr>
                        <tr>
                            <td width="7%">Saldo</td>
                            <td><input name="saldo" type="text" class="cajaGeneral cajaSoloLectura" id="saldo" style="width:70px"/></td>
                        </tr>
                        <tr>
                            <td>Observación</td>
                            <td><textarea id="observacion" name="observacion" class="cajaTextArea" style="width:100%"
                                          rows="2"></textarea></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>
    <br/>

    <div id="botonBusqueda2" style="padding-top:20px;">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="visibility: hidden"/>
        <a href="javascript:;" id="grabarCuenta"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <a href="javascript:;" id="limpiarCuenta"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
        <a href="javascript:;" id="cancelarCuenta"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <?php echo $oculto ?>
    </div>

</div>
<?php echo $form_close; ?>
</body>
</html>