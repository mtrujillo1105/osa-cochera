<html>
<head>
    <title><?php echo TITULO; ?></title>
    <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script>
        var cliente = "<?php echo $cliente; ?>";

        $(document).ready(function(){
            $('#cerrarCliente').click(function(){
                parent.$.fancybox.close();
            });
        });

        function seleccionarNota(codigo, costoTotal, tipoDoc, numero){
            parent.seleccionarNota(codigo, costoTotal, tipoDoc, numero);
            parent.$.fancybox.close();
        }

    </script>
</head>
<body>
<div align="center">
    <div id="tituloForm" class="header" style="width:95%; padding-top: 0;">
        <ul class="lista_tipodoc">
            <li>
                <a href="#">NOTA DE CREDITO</a>
            </li>
        </ul>
    </div>
    <div id="frmBusqueda" style="width:97%;">
        <table class="fuente8_2" width="100%" id="tabla_resultado" name="tabla_resultado" align="center" cellspacing="1"
               cellpadding="3" border="0">
            <tr>
                    <td><span class="spanTitulo" >Cliente *</span></td>
                    <td valign="middle">
                        <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente; ?>"/>
                        <input type="text" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10"
                               maxlength="11" readonly="readonly"
                               value="<?php echo $datosCliente->ruc; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                        <input type="text" name="nombre_cliente" class="cajaGeneral cajaSoloLectura" id="nombre_cliente"
                               size="40" maxlength="50" readonly="readonly" value="<?php echo $datosCliente->nombre; ?>"/>
                        &nbsp;
                        <a href="javascript:;" id="cerrarCliente"><img src="<?php echo base_url(); ?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                    </td>
            </tr>
        </table>
    </div>
    <br/>
    <br/>
    <div id="frmResultado" style="width:98%; height: 250px; overflow: auto;">
        <br/>
        <table class="fuente8_2" width="100%" id="tblMovimientoSerie" align="center" cellspacing="1" cellpadding="3"
               border="0">
            <tr class="cabeceraTabla">
                <td width="5%">ITEM</td>
                <td width="10%">FECHA</td>
                <td width="12%">CARGA</td>
                <td width="10%">ORIGEN</td>
                <td width="10%">NUMERO</td>
                <td width="10%">TOTAL</td>
                <td width="5%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
            </tr>
            <tbody>
            <?php
            $total = 0;
            if($notas != NULL){
                foreach($notas AS $notaCredito => $value){
                    $tipo = $value->CRED_TipoDocumento_inicio;
                    $documento = "";
                    if($tipo == "F"){
                        $documento = "FACTURA";
                    }else if($tipo == "B"){
                        $documento = "BOLETA";
                    }else if($tipo == "N"){
                        $documento = "COMPROBANTE";
                    }else{
                        $documento = "INDEPENDIENTE";
                        $numeros = "-------";
                    }
                    ?>
                    <tr>
                        <td><?php echo $total++; ?></td>
                        <td align="center" >
                            <?php echo $value->CRED_Fecha; ?>
                        </td>
                        <td align="center" >
                            <?php echo "GENERADA"; ?>
                        </td>
                        <td align="center" >
                            <?php
                                echo $documento;
                            ?>
                        </td>
                        <td align="center" >
                            <?php
                            echo $value->CRED_NumeroInicio;
                            ?>
                        </td>
                        <td align="center" >
                            <?php echo $value->CRED_total; ?>
                        </td>
                        <td>
                            <a href="#">
                                <img src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" alt="Ver"/>
                            </a>
                        </td>
                        <td>
                            <a href="#" onclick="seleccionarNota(<?php echo $value->CRED_Codigo ?>, <?php echo $value->CRED_total; ?>, '<?php echo $value->CRED_TipoDocumento_inicio; ?>', '<?php echo $value->CRED_NumeroInicio; ?>');" >
                                <img src="<?php echo base_url(); ?>public/images/icons/ir.png?=<?=IMG;?>" alt="Ir"/>
                            </a>
                        </td>
                        <td>
                            <a href="#">
                                <a href="#" >
                                    <img src="<?php echo base_url(); ?>public/images/icons/pdf.png?=<?=IMG;?>" width="16px" height="16px" alt="PDF"/>
                                </a>
                            </a>
                        </td>
                    </tr>
            <?php
                }
            }else{
                echo "<tr><td></td><td colspan='6' align='center' > <b>NO SE HAN ENCONTRADO NOTAS DE CREDITO</b></td> <td></td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <br/>

    <div id="frmResultado" style="width:98%; height: 150px; overflow: auto;">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="display:none"/>
        <table class="fuente8_2_3" width="100%" id="tblDocumentoDetalle" align="center" cellspacing="1" cellpadding="3"
               border="0" style="display:none">
            <tr class="cabeceraTabla">
                <td colspan="7">DETALLES DE LA FACTURA</td>
            </tr>
            <tr class="cabeceraTabla">
                <td width="10%">CODIGO</td>
                <td>DESCRIPCION</td>
                <td width="7%">CANT</td>
                <td width="9%">PU C/IGV</td>
                <td width="8%">IMPORTE</td>
            </tr>
        </table>
    </div>
</body>
</html>
