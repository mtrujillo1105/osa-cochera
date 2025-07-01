<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script src="<?php echo base_url();?>resources/template/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>resources/template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url();?>resources/template/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url();?>resources/template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo base_url();?>public/js/almacen/producto.js"></script>
<script src="<?php echo base_url();?>public/js/jquery-barcode.js"></script>

<?php
if (!isset($_POST['txtfila'])) {
    $n_columnas = 1;
    $n_filas = 1;
    $n_codigo = $n_columnas * $n_filas;
} elseif (isset($_POST['txtfila'])) {
    $n_columnas = 1;
    $n_filas = $_POST['txtfila'];
    $n_codigo = $n_columnas * $n_filas;
}
?>
<script type="text/javascript">

    function imprSelec(nombre) {
        var ficha = document.getElementById(nombre);
        var ventimp = window.open(' ', 'popimpr');
        ventimp.document.write(ficha.innerHTML);
        ventimp.document.close();
        ventimp.print();
        ventimp.close();
    }


    function generateBarcode() {
        var value = $("#codigo_producto").val();//$("#codigo_producto").val()
        var i = 0;
        var btype = "code128";
        var renderer = "css";

        var quietZone = false;
        if ($("#quietzone").is(':checked') || $("#quietzone").attr('checked')) {
            quietZone = true;
        }

        var settings = {
            output: renderer,
            bgColor: "#FFFFFF",
            color: "#000000",
            barWidth: "2",
            barHeight: "140",
            moduleSize: "1",
            posX: "20",
            posY: "20",
            addQuietZone: "1"
        };
        if ($("#rectangular").is(':checked') || $("#rectangular").attr('checked')) {
            value = {code: value, rect: true};
        }
        if (renderer == 'canvas') {
            clearCanvas();
            $("#barcodeTarget").hide();
            <?php
    $num_codigo=$n_codigo;
    $i=0;
    do{
       $i++;
        $imagen1='$("#barcodeTarget'.$i.'").hide();';
        echo $imagen1;

      } while ($i<$num_codigo);

    ?>
            $("#canvasTarget").show().barcode(value, btype, settings);
        } else {
            $("#canvasTarget").hide();
            $("#barcodeTarget").html("").show().barcode(value, btype, settings);
            <?php
      $num_codigo=$n_codigo;
      $o=0;
    do{
       $o++;
        $imagen2='$("#barcodeTarget'.$o.'").html("").show().barcode(value, btype, settings);';
        echo $imagen2;

      } while ($o<$num_codigo);
    ?>
        }
    }


    function showConfig1D() {
        $('.config .barcode1D').show();
        $('.config .barcode2D').hide();
    }

    function showConfig2D() {
        $('.config .barcode1D').hide();
        $('.config .barcode2D').show();
    }

    function clearCanvas() {
        var canvas = $('#canvasTarget').get(0);
        var ctx = canvas.getContext('2d');
        ctx.lineWidth = 1;
        ctx.lineCap = 'butt';
        ctx.fillStyle = '#FFFFFF';
        ctx.strokeStyle = '#000000';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeRect(0, 0, canvas.width, canvas.height);
    }

    $(function () {
        $('input[name=btype]').click(function () {
            if ($(this).attr('id') == 'datamatrix') showConfig2D(); else showConfig1D();
        });
        $('input[name=renderer]').click(function () {
            if ($(this).attr('id') == 'canvas') $('#miscCanvas').show(); else $('#miscCanvas').hide();
        });
        generateBarcode();
    });

</script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        modo = $("#modo").val();
        tipo = $("#tipo").val();
        if (modo == 'insertar') {
            $("#nombres").val('&nbsp;');
            $("#paterno").val('&nbsp;');
            $("#ruc").focus();
            $("#cboSexo").val('0');
        }
        else if (modo == 'modificar') {
            if (tipo == '0') {
                $("#ruc").val('11111111111');
            }
            else if (tipo == '1') {
                $("#nombres").val('&nbsp;');
                $("#paterno").val('&nbsp;');
                $("#cboSexo").val('0');
            }
        }
    });
    function cargar_familia(familia, nombre) {
        document.getElementById('familia').value = familia;
        document.getElementById('nombre_familia').value = nombre;
    }
</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" style="height: 10%;background-color: skyblue;border: 1px solid !important;" class="header"><span><?php echo $titulo; ?></span></div>
            <div id="frmBusqueda">
                <?php echo validation_errors("<div class='error'>", '</div>'); ?>

                <div id="nuevoRegistro"
                     style="display:none;float:right;width:150px;height:20px;border:0px solid #000;margin-top:7px;"><a
                        href="#">Nuevo</a></div>
                <br><br>
                <div id="divPrincipales">
                    <div id="generator">
                        <div id="config">
                            <div class="config">
                                <br>
                                <div id="submit">
                                    <input name="codigo_producto" id="codigo_producto" type="hidden"
                                          value="<?php echo $cod_producto; ?>">
                                </div>
                            </div>
                            <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" rightmargin="0">
                            <DIV ID="seleccion">
                                <?php
                                $filas = $n_filas;

                                ?>
                                <table>

                                    <?php
                                    $i = 0;
                                    do {
                                        $i++;
                                        echo "<tr>";

                                        $j = 0;
                                        do {
                                            $j++;

                                            if ($j == $n_columnas) {
                                                $blanco = "";
                                            } else {
                                                $blanco = '<img src="' . base_url() . 'public/images/blanco.jpg" width="40" height="80" style="border-radius:15px" >';
                                            }

                                            echo '
<td><table width="140" height="200" bgcolor="#FFFFFF" >

    <tr align="center">
      <td style="width: 100%" >
        <div style="width: 100%; font-size: 16px; font-weight: bolder; display: block; text-align: center" >' . $nombre_producto . '</b>
        </div>
        
      </td>
    </tr>
    
    <tr>
    <td align="center">
    <div style="width=10px;" id="barcodeTarget' . $j . '" class="barcodeTarget" ></div>
    
    </td>
    </tr>
    </table>
    </td>
<td>
' . $blanco . '
</td>';


                                        } while ($j < $n_columnas);
                                        echo "
</tr>

";

                                    } while ($i < $n_filas);
                                    ?>

                                </table>

                                

                            </DIV>


                            </body>

                        </div>


                    </div>
                    <div id="botonBusqueda">
                        <a href="javascript:imprSelec('seleccion')">
                            <img src="<?php echo base_url(); ?>public/images/icons/botonimprimir.jpg" width="85" height="22"
                                 border="1">
                        </a>
                        <a href="#" id="cancelarCodigoBarra"><img
                                src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg" width="85" height="22"
                                border="1"></a>
                        <?php echo $oculto; ?>
                        <br/><br/><br/>
                    </div>
                </div>
            </div>
        </div>