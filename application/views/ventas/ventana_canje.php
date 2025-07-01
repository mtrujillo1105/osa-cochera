<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/ui-lightness/jquery-ui-1.8.18.custom.css?=<?=CSS;?>" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css"/>
<!-- Calendario -->
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/calendario/calendar.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/calendario/calendar-es.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/calendario/calendar-setup.js?=<?=JS;?>"></script> 
<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/calendario/calendar-win2k-2.css?=<?=CSS;?>" type="text/css" media="all" title="win2k-cold-1" />

<!-- Calendario -->
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />



<script>

    $(document).ready(function() {
        $('.remove_item').click(function() {
            remove_item($(this));
        });

        $('#cerrar').click(function() {
            parent.$.fancybox.close();
        });

        $("#guardar").click(function() {

            var rpta = confirm('¿Esta seguro de realizar el canje de comprobante?.');

            if (rpta === false)
                return false;

            var url = $('#frmEnviar').attr('action');
            var dataString = $('#frmEnviar').serialize();

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                dataType: 'json',
                beforeSend: function(data) {
                    $('#loading').show();
                },
                error: function(data) {
                    $('#loading').hide();
                    alert('No se puedo completar la operación - Revise los campos ingresados.');
                    console.log(data);
                },
                success: function(data) {
                    if (data.result === 'success') {
                        alert('Se género el documento con el número : ' + data.serie + '-' + data.numero + '.');
                    } else {
                        alert(data.mensaje);
                    }
                    $('#loading').hide();
                    parent.$('#limpiarComprobante').click();
                    parent.$.fancybox.close();

                }
            });
        });

        $("#linkVerSerieNum").click(function () {
            var temp = $("#linkVerSerieNum p").html();
            var serienum = temp.split('-');
            alert(temp);
            $("#serie").val(serienum[0]);
            $("#numero").val(serienum[1]);
        });
        
    });

    function remove_item(e) {

        e.parent().parent().remove();

        if ($('.comprobante_total').length > 0) {
            var total = parseFloat(0);
            $('.comprobante_total').each(function() {
                var t_total = parseFloat($(this).val());
                total += t_total;
            });

            var html = '<tr><td style="text-align: right;" colspan="6"><b>\n\
<?php echo $moneda[0]->MONED_Simbolo; ?> ' + total.toFixed(2) + '</b></td><td></td></tr>';

            $('#tb_detalle tfoot').html(html);

        } else {
            $('#tb_detalle tfoot').html('');
        }
    }

    function cargar_propuesta() {

        var value = $('#cmbComprobante').val();

        if (value === '') {
            alert('No se ha seleccionado un comprobante valido.');
            return false;
        }

        var validacion = true;

        $('.cod_comprobante').each(function() {
            var codigo = $(this).val();
            if (parseInt(codigo) === parseInt(value))
                validacion = false;
        });

        if (validacion === false) {
            alert('El comprobante seleccionado ya ha sido ingresado.');
            return false;
        }

        var url = '<?php echo base_url(); ?>index.php/ventas/comprobante/cargar_comprobante/' + value;

        $.ajax({
            type: "GET",
            url: url,
            beforeSend: function(data) {
                $('#loading').show();
            },
            error: function(data) {
                $('#loading').hide();
                alert('No se puedo completar la operación - Revise los campos ingresados.')

            },
            success: function(data) {

                $('#loading').hide();

                var current = $('#tb_detalle tbody').html();

                $('#tb_detalle tbody').html(current + data);
                var item = 1;
                $('.tb_item').each(function() {
                    $(this).html(item);
                    item++;
                });

                var total = parseFloat(0);
                $('.comprobante_total').each(function() {
                    var t_total = parseFloat($(this).val());
                    total += t_total;
                });

                var html = '<tr><td style="text-align: right;" colspan="6"><b>\n\
<?php echo $moneda[0]->MONED_Simbolo; ?> ' + total.toFixed(2) + '</b></td><td></td></tr>';

                $('#tb_detalle tfoot').html(html);

                $('.remove_item').each(function() {
                    $(this).unbind();
                    $(this).bind("click", function() {
                        remove_item($(this));
                    });
                })


            }
        });

    }

</script>



<style>
    textarea{
        font-family: Helvetica;
        font-size: 8pt;
        margin: 0;
        border: 1px solid #696969;
        text-transform: uppercase;
    }
    .cajaPadding{
        padding: 1px 10px;
    }

    .tb_listado td{
        padding-top: 2px;
        padding-bottom: 2px;
    }

    img{
        position: relative;
        top: 3px;
    }

    input, select{
        margin: 0;
        height: 22px !important;
    }
    .tb_titulo{
        font-weight: bold;
        padding-left: 20px;
        text-align: right;
        padding-right: 20px;
    }

    #pagination_container a{
        cursor: pointer;
        font-family: Arial !important
    }



    .cajaCalendar{
        width: 90px !important;
        cursor: pointer;
        background: url('<?php echo base_url(); ?>public/images/icons/calendar.png?=<?=IMG;?>') 70px center no-repeat;
    }



    .cajaBusquedaMedia{
        cursor: pointer;
        padding-right: 25px !important;
        background: url('<?php echo base_url(); ?>public/images/icons/search.png?=<?=IMG;?>') 220px center no-repeat;
    }

    .cajaBusquedaGrande{
        width: 300px;
        cursor: pointer;
        padding-right: 25px !important;
        background: url('<?php echo base_url(); ?>public/images/icons/search.png?=<?=IMG;?>') 280px center no-repeat;

    }



    .cajaBusqueda{
        display: none;
        z-index: 100;
        width: 100%; position: absolute; height: auto; max-height: 120px;
        overflow-y: auto;
        background: #FFF; border: 1px solid #CCCCCC;
    }

    .busqueda_box, .message_error{
        padding: 2px 10px;
        cursor: pointer;
        color: #000;
        background: #FFF;
    }

    .busqueda_box:hover, .busqueda_box_select{
        color: #FFF;
        background: #000;
    }

    .ui-autocomplete{
        padding: 0;
        margin: 0;
        width: 500px;
        list-style: none;
    }
    .ui-autocomplete a{
        color: #000;
        font-family: Arial;
        font-size: 8pt;
        display: block;
        padding: 4px 10px;
    }
    .ui-autocomplete a:hover{
        color: #000;
        font-weight: bold;;

    }
    .ui-state-hover{
        background: black !important;
        color: #FFF !important;
        border: 0px !important;
    }
    .remove_item{
        cursor: pointer;
    }
    select{
        font-family: Arial;
        font-size: 11px;
        border-color: #696969;
        border-style: solid;
        border-width: 1px;
    }
    .tb_listado 
    {
        width: 100%;
        font-family: Arial;
        font-size: 11px;
        margin-top: 15px;
    }
    #tb_detalle 
    {
        width: 880px;
        margin: 0 auto;
        border-collapse: collapse;
    }
    #tb_detalle thead td{
        border: 1px solid #000;
    }
    #tb_detalle tbody td, #tb_detalle tfoot td{
        text-align: center;
        padding: 2px 10px;
        border-bottom: 1px solid #000;
    }
</style>



<div id="pagina">

    <div id="zonaContenido" style="position: relative;">

        <div id="cabeceraResultado" class="header"  style="width: 100% !important"><?php echo $titulo_tabla ?></div>


        <form method="post" action="<?php echo base_url(); ?>index.php/ventas/comprobante/canjear_documento/" id="frmEnviar">
            <table class="tb_listado">
                <tr>
                    <td class="tb_titulo">CLIENTE:</td>
                    <td>
                        <input type="hidden" name="cod_cliente" id="cod_cliente" value="<?php echo $codigo_cliente; ?>">
                        <input class="cajaPadding cajaGrande cajaBusquedaGrande" type="text" id="nombre_cliente"
                               value="<?php echo $nombre_cliente; ?>">

                        <script>
                            $(document).ready(function() {

                                $("#nombre_cliente").autocomplete({
                                    //flag = $("#flagBS").val();
                                    source: function(request, response) {
                                        $.ajax({
                                            url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                                            type: "POST",
                                            data: {
                                                term: $("#nombre_cliente").val()
                                            },
                                            dataType: "json",
                                            success: function(data) {
                                                response(data);
                                            }
                                        });
                                    },
                                    select: function(event, ui) {
                                        //$("#nombre_cliente").val(ui.item.codinterno);
                                        $("#cod_cliente").val(ui.item.codigo);
                                        $("#ruc_cliente").val(ui.item.ruc)
                                        $("#direccion_cliente").val(ui.item.direccion)

                                        cargar_listado_comprobantes(ui.item.codigo)

                                        $("#buscar_producto").focus();
                                        $("#buscar_producto").select();
                                    },
                                    minLength: 2
                                });

                            });

                            function cargar_listado_comprobantes(codigo_cliente) {
                                var url = '<?php echo base_url(); ?>index.php/ventas/comprobante/cargar_listado_comprobantes/' + codigo_cliente;

                                $.ajax({
                                    type: "GET",
                                    url: url,
                                    beforeSend: function(data) {
                                        $('#loading').show();
                                    },
                                    error: function(data) {
                                        $('#loading').hide();
                                        alert('No se puedo completar la operación - Revise los campos ingresados.')

                                    },
                                    success: function(data) {

                                        $('#loading').hide();
                                        $('#cmbComprobante').html(data);
                                    }
                                });
                            }
                        </script>

                    </td>
                    <td ></td>
                    <td class="tb_titulo">TIPO DOCUMENTO:</td>
                    <td>
                        <select name="cmbDocumento" class="cajaPadding" id="cmbDocumento">
                            <option value="F">FACTURA</option>
                            <option value="B">BOLETA</option>
                        </select>
                    </td>
                </tr>
                
                
                <tr>
                    <td class="tb_titulo">Numero : </td>
                    <td>
                    <input class="cajaGeneral" name="serie" type="text" id="serie" size="3" maxlength="3"
                               value="<?php echo $serie; ?>"/>&nbsp;
                    <input class="cajaGeneral" name="numero" type="text" id="numero" size="6" maxlength="6" value="<?php echo $numero; ?>"/>
                     
                   <a href="javascript:;" id="linkVerSerieNum" >
                        <p class="comprobante" style="display:none"><?php echo $serie_suger_b . '-' . $numero_suger_b ?>
                        </p>
                    <img src="<?php echo base_url(); ?>public/images/icons/flecha.png?=<?=IMG;?>" border="0"
                                       alt="Serie y número sugerido" title="Serie y número sugerido"/>
                                       
                  </a>
                  <input type="checkbox" name="numeroAutomatico"  id="numeroAutomatico" <?php echo($numeroAutomatico==1)?'checked=true':''; ?>" value="1" title="SERIE-NUMERO AUTOMATICO SI SE SELECCIONA">
                            
                     
                    
                    
                    </td>
                </tr>
                
                
                <tr>
                    <td class="tb_titulo">RUC:</td>
                    <td>
                        <input readonly="" class="cajaPadding cajaGrande" id="ruc_cliente" type="text" style="width: 90px"
                               value="<?php echo $ruc_cliente; ?>">
                    </td>
                    <td></td>
                    <td class="tb_titulo">FECHA: </td>
                    <td>
                        <input readonly="" name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" type="text" class="cajaPequena cajaPadding" size="10" maxlength="10"/>
                        <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor = 'pointer'" title="Calendario"/>
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField: "fecha", // id del campo de texto
                                ifFormat: "%d/%m/%Y", // formato de la fecha, cuando se escriba en el campo de texto
                                button: "Calendario1"   // el id del botón que lanzará el calendario
                            });
                        </script>
                    </td>
                </tr>
                <tr>
                    <td class="tb_titulo">DIRECCION:</td>
                    <td>
                        <input readonly="" class="cajaPadding cajaGrande" id="direccion_cliente" type="text" style="width: 400px;"
                               value="<?php echo $direccion_cliente; ?>">
                    </td>
                    <td></td>
                    <td class="tb_titulo">COMPROBANTE:</td>
                    <td>
                        <select class="cajaPadding" id="cmbComprobante">
                            <option value="">::SELECCIONE::</option>
                            <?php
                            for ($i = 0; $i < count($comprobantes); $i++):
                                if ($comprobantes[$i]->CPP_Codigo_canje == '' || $comprobantes[$i]->CPP_Codigo_canje == NULL || $comprobantes[$i]->CPP_Codigo_canje == 0) {
                                    ?>
                                    <option value="<?php echo $comprobantes[$i]->CPP_Codigo; ?>">
                                        <?php echo $comprobantes[$i]->CPC_Serie . '-' . $comprobantes[$i]->CPC_Numero; ?>
                                    </option>
                                    <?php
                                }
                            endfor;
                            ?>
                        </select>
                    </td>
                    <td>

                        <a style="top: -4px;
                           position: relative;" href="javascript:;" onclick="cargar_propuesta();">
                            <img  src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" border="1" align="">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <table class="fuente8" width="100%" ID="tb_detalle">
                            <thead>
                                <tr class="cabeceraTabla">
                                    <td style="width: 40px;">ITEM</td>
                                    <td style="width: 50px;">FECHA</td>
                                    <td style="width: 60px;">SERIE</td>
                                    <td style="width: 80px;">NUMERO</td>
                                    <td style="width: 450px;">RAZON SOCIAL</td>
                                    <td style="width: 100px;">TOTAL</td>
                                    <td style="width: 50px;">&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="tb_item">
                                        1
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($datos[0]->CPC_Fecha)); ?>
                                    </td>
                                    <td>
                                        <?php echo $datos[0]->CPC_Serie; ?>
                                    </td>
                                    <td>
                                        <?php echo $datos[0]->CPC_Numero; ?>
                                    </td>
                                    <td style="text-align: left;">
                                        <input class="cod_comprobante" type="hidden" name="cod_comprobante[]" 
                                               value="<?php echo $datos[0]->CPP_Codigo; ?>">
                                               <?php echo $nombre_cliente; ?>
                                    </td>
                                    <td style="text-align: right">
                                        <input class="comprobante_total" type="hidden"
                                               value="<?php echo $datos[0]->CPC_total; ?>">
                                               <?php echo $moneda[0]->MONED_Simbolo . ' ' . $datos[0]->CPC_total; ?>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align: right;" colspan="6">
                                        <b><?php echo $moneda[0]->MONED_Simbolo . ' ' . $datos[0]->CPC_total; ?></b>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
        <div style="width: 880px; margin: 0 auto;" align="right">

            <a id="guardar"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="70" height="22" border="1"></a>&nbsp;

            <a  id="cerrar"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="70" height="22" border="1"></a>


        </div>
        <table id="data_inicial" style="display: none;">
            <tbody>
                <tr>
                    <td class="tb_item">
                        1
                    </td>
                    <td>
                        <?php echo date('d/m/Y', strtotime($datos[0]->CPC_Fecha)); ?>
                    </td>
                    <td>
                        <?php echo $datos[0]->CPC_Serie; ?>
                    </td>
                    <td>
                        <?php echo $datos[0]->CPC_Numero; ?>
                    </td>
                    <td style="text-align: left;">
                        <input type="hidden" name="cod_comprobante[]" 
                               value="<?php echo $datos[0]->CPP_Codigo; ?>">
                               <?php echo $nombre_cliente; ?>
                    </td>
                    <td>
                        <?php echo $moneda[0]->MONED_Simbolo . ' ' . $datos[0]->CPC_total; ?>
                    </td>
                    <td></td>
                </tr>

            </tbody>
        </table>
        <div id="loading" align="center" style="background: #FFF;
             position: absolute;
             z-index: 9999; 
             top: 229px;
             display: none;
             left: 380px; margin: 0 auto; width: 140px; height: 32px;padding: 30px 40px; border: 1px solid #cccccc;" class="fuente8">

            <b>ESPERE POR FAVOR...</b><br>

            <img src="<?php echo base_url() ?>public/images/icons/cargando.gif?=<?=IMG;?>" border='0' />

        </div>


    </div>

</div>