<script type="text/javascript" src="<?php echo base_url(); ?>public/js/sunat.js?=<?=JS;?>"></script>

<span class="input-group-btn" style="width: 6em; display: inline-block; opacity: 0;">
    <button class="btn btn-default" type="button" role="button" id="search-sunat-comprobante" name="search-sunat-comprobante"> <img src="<?=$base_url;?>public/images/icons/sunat.png?=<?=IMG;?>" width="24px"> <b>Sunat</b> </button>
    <img id="loading-sunat" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>">
</span>

<a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a>

<section class="containerNvoCliente" id="containerNvoCliente" name="containerNvoCliente">
    <section class="nvoCliente" id="juridico" name="juridico">
        <form action="#" method="POST">
            <table border="0" cellpadding="5" cellspacing="5" align="center">
                <tr>
                    <th colspan="2" style="text-align: center">NUEVO CLIENTE JURIDICO</th>
                </tr>
                <tr>
                    <td style="width: 15em">
                        <label for="nvoClienteRuc">ID CLIENTE:</label>
                    </td>
                    <td style="width: 15em">
                        <input type="text" id="nvoClienteCode" class="cajaGrande cajaSoloLectura" name="nvoClienteCode" placeholder="ID DEL CLIENTE" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10em">
                        <label for="nvoClienteRuc">RUC:</label>
                    </td>
                    <td style="width: 10em">
                        <input type="text" id="nvoClienteRuc" class="cajaGrande" name="nvoClienteRuc" placeholder="INDIQUE EL RUC" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10em;">
                        <label for="nvoClienteNombre">NOMBRE:</label>
                    </td>
                    <td style="width: 10em">
                        <input type="text" id="nvoClienteNombre" class="cajaGrande" name="nvoClienteNombre" placeholder="INGRESE LA RAZON SOCIAL" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10em;">
                        <label for="nvoClienteDireccion">DIRECCION:</label>
                    </td>
                    <td style="width: 10em">
                        <input type="text" id="nvoClienteDireccion" class="cajaGrande" name="nvoClienteDireccion" placeholder="INDIQUE LA DIRECCI¨®N" value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10em;">
                        <label for="nvoClienteCorreoMail">CORREO ELECTRONICO:</label>
                    </td>
                    <td style="width: 10em">
                        <input type="email" id="nvoClienteMail" class="cajaGrande" name="nvoClienteMail" placeholder="INDIQUE SU CORREO ELECTRONICO" value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10em;">
                        <label for="nvoClienteVendedor">VENDEDOR ASIGNADO:</label>
                    </td>
                    <td style="width: 10em">
                        <select id="nvoClienteVendedor" name="nvoClienteVendedor" class="cajaGrande">
                            <?=$cboVendedor;?>
                        </select>
                    </td>
                </tr>
                <tr style="display: none">
                    <td style="width: 10em;">
                        <label for="nvoClienteDigemin">ESTADO EN DIGEMID:</label>
                    </td>
                    <td style="width: 10em">
                        <select id="nvoClienteDigemin" name="nvoClienteDigemin" class="comboMedio">
                            <option value="1" selected>ACTIVO</option>
                            <option value="0">INACTIVO</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right">
                        <a href="javascript:;" id="grabarNvoClienteJuridico"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                        <a href="javascript:;" id="cancelarNvoClienteJuridico"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    </td>
                </tr>
            </table>
        </form>
    </section>

    <section class="nvoCliente" id="natural" name="natural">
        <form action="#" method="POST">
            <table border="0" cellpadding="5" cellspacing="5" align="center">
                <tr>
                    <th colspan="2" style="text-align: center">NUEVO CLIENTE NATURAL</th>
                </tr>
                <tr>
                    <td style="width: 15em">
                        <label for="nvoClienteDNI">ID CLIENTE:</label>
                    </td>
                    <td style="width: 15em">
                        <input type="text" id="nvoClienteCodeN" class="cajaGrande cajaSoloLectura" name="nvoClienteCodeN" placeholder="ID DEL CLIENTE" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15em">
                        <label for="nvoClienteDNI">DNI:</label>
                    </td>
                    <td style="width: 15em">
                        <input type="text" id="nvoClienteDNI" class="cajaGrande" name="nvoClienteDNI" placeholder="INDIQUE EL DNI" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15em;">
                        <label for="nvoClienteNombres">NOMBRES:</label>
                    </td>
                    <td style="width: 15em">
                        <input type="text" id="nvoClienteNombres" class="cajaGrande" name="nvoClienteNombres" placeholder="INGRESE EL NOMBRE COMPLETO" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15em;">
                        <label for="nvoClientePaterno">APELLIDO PATERNO:</label>
                    </td>
                    <td style="width: 15em">
                        <input type="text" id="nvoClientePaterno" class="cajaGrande" name="nvoClientePaterno" placeholder="INGRESE EL APELLIDO PATERNO" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15em;">
                        <label for="nvoClienteMaterno">APELLIDO MATERNO:</label>
                    </td>
                    <td style="width: 15em">
                        <input type="text" id="nvoClienteMaterno" class="cajaGrande" name="nvoClienteMaterno" placeholder="INGRESE EL APELLIDO MATERNO" readonly value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15em;">
                        <label for="nvoClienteGenero">GENERO:</label>
                    </td>
                    <td style="width: 15em">
                        <select id="nvoClienteGenero" class="cajaGrande" name="nvoClienteGenero">
                            <option value="0">MASCULINO</option>
                            <option value="1">FEMENINO</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15em;">
                        <label for="nvoClienteCorreomailN">CORREO ELECTRONICO:</label>
                    </td>
                    <td style="width: 15em">
                        <input type="email" id="nvoClienteMailN" class="cajaGrande" name="nvoClienteMailN" placeholder="INDIQUE SU CORREO ELECTRONICO" value=""/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10em;">
                        <label for="nvoClienteVendedorN">VENDEDOR ASIGNADO:</label>
                    </td>
                    <td style="width: 10em">
                        <select id="nvoClienteVendedorN" name="nvoClienteVendedorN" class="cajaGrande">
                            <?=$cboVendedor;?>
                        </select>
                    </td>
                </tr>
                <tr style="display: none">
                    <td style="width: 10em;">
                        <label for="nvoClienteDigeminN">ESTADO EN DIGEMID:</label>
                    </td>
                    <td style="width: 10em">
                        <select id="nvoClienteDigeminN" name="nvoClienteDigeminN" class="comboMedio">
                            <option value="1" selected>ACTIVO</option>
                            <option value="0">INACTIVO</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="text-align: right">
                        <a href="javascript:;" id="grabarNvoClienteNatural"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                        <a href="javascript:;" id="cancelarNvoClienteNatural"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    </td>
                </tr>
            </table>
        </form>
    </section>
</section>

<style rel="stylesheet">
.containerNvoCliente{
    display: none;
    z-index: 1000;
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.8);
}

.nvoCliente{
    position: absolute;
    margin-left: -25em;
    top: 15%;
    left: 50%;
    background: rgba(240,240,240,1);
    width: 50em;
    height: auto;
    padding: 2em;
    border-radius: 1em;
}

.nvoCliente td{
    font-size: 8pt;
}

#loading-sunat{
    opacity: 0;
}
</style>