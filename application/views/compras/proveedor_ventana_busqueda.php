<html>
<head>
    <title><?php echo TITULO; ?></title>
    <link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/domwindow.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/empresa/proveedor_popup.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/sunat.js?=<?=JS;?>"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript">
        var base_url;
        $(document).ready(function () {
            base_url = $("#base_url").val();
            $("#buscarProveedor").click(function () {
                $("#form_busqueda").submit();
            });
            $("#limpiarProveedor").click(function () {
                url = base_url + "index.php/empresa/proveedor/ventana_busqueda_proveedor/0/1";
                location.href = url;
            });
            $('#cerrarProveedor').click(function () {
                parent.$.fancybox.close();
            });

        });
        function seleccionar_proveedor(codigo, ruc, razon_social, empresa, persona, ctactesoles, ctactedolares, direccion) {
            parent.seleccionar_proveedor(codigo, ruc, razon_social, empresa, persona, ctactesoles, ctactedolares, direccion);
            parent.$.fancybox.close();
        }
    </script>
</head>
<body>
<div align="center">

    <form name="form_busqueda" id="form_busqueda" method="post" action="<?php echo $action; ?>">
        <div id="frmBusqueda" style="width:98%">
            <table class="fuente8_2" width="100%" cellspacing="0" cellpadding="3" border="0">
                <tr class="cabeceraTabla" height="25px">
                    <td align="center" colspan="3">PROVEEDORES</td>
                </tr>
                <tr height="35px">
                    <td width="10%">RUC</td>
                    <td width="40%"><input id="ruc_search" type="text" class="cajaPequena" name="ruc" maxlength="11"
                                           value="<?php echo $ruc; ?>" onkeypress="return numbersonly(this,event)"/>
                    <td width="35%" align="right">&nbsp;</td>
                </tr>
                <tr height="25px">
                    <td>Nombre o Raz&oacute;n Social</td>
                    <td><input id="nombre" name="nombre" type="text" class="cajaGrande" maxlength="45"
                               value="<?php echo $nombre; ?>"/></td>
                    <td align="right"><a href="#"><img id="buscarProveedor"
                                                       src="<?php echo base_url(); ?>public/images/icons/botonbuscar.jpg?=<?=IMG;?>" border="1"
                                                       title="Buscar Proveedor"></a>
                        <a href="javascript:;" id="limpiarProveedor"><img
                                src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" class="imgBoton"/></a>
                        <a href="javascript:;" id="nuevoProveedor"><img
                                src="<?php echo base_url(); ?>public/images/icons/botonnuevocliente.jpg?=<?=IMG;?>" class="imgBoton"/></a>
                        <a href="javascript:;" id="cerrarProveedor"><img
                                src="<?php echo base_url(); ?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" class="imgBoton"/></a></td>
                </tr>
            </table>
        </div>
        <div id="lineaResultado" style="width:95%; margin-top:10px;">
            <table class="fuente8_2" width="100%" cellspacing=0 cellpadding=3 border=0>
                <tr>
                    <td width="50%" align="left">N de clientes encontrados:&nbsp;<?php echo $registros; ?></td>
                    <td width="50%" align="right">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div id="frmResultado" style="width:98%; height: 320px; overflow: auto; background-color: #f5f5f5">
            <table class="fuente8_2" width="100%" id="tabla_resultado" name="tabla_resultado" align="center" border="0"
                   cellpadding="4" cellspacing="1">
                <tr class="cabeceraTabla">
                    <td width="2%">
                        <div align="center"><b>Item</b></div>
                    </td>
                    <td width="5%">
                        <div align="center"><b>Ruc</b></div>
                    </td>
                    <td width="15%">
                        <div align="center"><b>Nombre o Raz&oacute;n Social</b></div>
                    </td>
                    <td width="10%">
                        <div align="center"><b>Tipo Proveedor</b></div>
                    </td>
                    <td width="2%">
                        <div align="center"></div>
                    </td>
                </tr>
                <?php
                $indice = 0;
                foreach ($lista as $valor) {
                    $classfila = $indice % 2 == 0 ? "itemImparTabla" : "itemParTabla";
                    $codigo = $valor[5];
                    $empresa = $valor[6];
                    $persona = $valor[7];
                    $ctactesoles = $valor[8];
                    $ctactedolares = $valor[9];
                    $ruc = $valor[1];
                    $razon_social = $valor[2];
                    $direccion = $valor[10];
                    ?>
                    <tr class="<?php echo $classfila;?>">
                        <td>
                            <div align="center"><?php echo $valor[0];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[1];?></div>
                        </td>
                        <td>
                            <div align="left"><?php echo $valor[2];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[3];?></div>
                        </td>
                        <td>
                            <div align="center"><?php echo $valor[4];?></div>
                        </td>
                    </tr>
                    <?php
                    $indice++;
                }
                ?>
            </table>
            <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>"/>
        </div>
        <div style="margin-top: 15px;"><?php echo $paginacion; ?></div>
    </form>
</div>
<div id="ventana" style="display:none" >
    <div id="pagina">
        <div align="center">
            <div id="tituloForm" class="header" style="width:500px; top:0px;">REGISTRAR PROVEEDOR</div>
            <div id="frmBusqueda" style="top:0px">
                <form name="frmProveedor" id="frmProveedor" method="post" action="">
                    <div id="tipoPersona" align="left" >
                        <table class="fuente8_2_3_4" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="28%">Tipo Persona (*)</td>
                                <td>
                                    <input type="radio" id="tipo_persona" name="tipo_persona" value="0"
                                           checked='checked'/>Persona Natural
                                    <input type="radio" id="tipo_persona" name="tipo_persona" value="1"/>Persona Jur&iacute;dica
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="datosEmpresa"  align="left">
                        <table class="fuente8_2_3_4" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="28%">N° Documento</td>
                                <td>
                                    <select name="cboTipoCodigo" id="cboTipoCodigo" class="comboMedio">
                                        <?php echo $tipocodigo; ?>
                                    </select>
                                </td>
                            <tr>
                            <tr>
                                <td>
                                </td>
                                <td>
                                    <input id="ruc" type="text" class="cajaGeneral" NAME="ruc" size="10" maxlength="11"
                                           onkeypress="return numbersonly('ruc',event);"/>
                                    <label id="ruc_msg" class="etiqueta_error"></label>
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                      <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" role="button" id="search-sunat">
                                          <img src="<?=$base_url;?>public/images/icons/sunat.png?=<?=IMG;?>" width="16px"> <b>Sunat</b>
                                        </button>
                                        <img id="loading-sunat" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>">
                                      </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre o Raz&oacute;n Social (*)</td>
                                <td><input name="razon_social" type="text" class="cajaGrande" id="razon_social"
                                           maxlength="150"/></td>
                            </tr>
                        </table>
                    </div>
                    <div id="datosPersona" align="left" >
                        <table class="fuente8_2_3_4" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="28%">N° Documento</td>
                                <td><select id="tipo_documento" name="tipo_documento" class="comboMedio"
                                            onchange="valida_tipoDocumento();">
                                        <?php echo $tipo_documento; ?>
                                    </select>
                                    <input name="numero_documento" type="text" class="cajaGeneral" size="6"
                                           maxlength="8" id="numero_documento" size="15" maxlength="8"
                                           onkeypress="return numbersonly('numero_documento',event);"/>
                                    <label id="numero_documento_msg" class="etiqueta_error"></label>
                                    R.U.C. <input type="text" class="cajaGeneral" size="9" maxlength="11"
                                                  name="ruc_persona" id="ruc_persona"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Nombres&nbsp;(*)</td>
                                <td>
                                    <input id="nombres" type="text" class="cajaGrande" name="nombres" maxlength="45">
                                </td>
                            </tr>
                            <tr>
                                <td>Apellidos Paterno&nbsp;(*)</td>
                                <td><input NAME="paterno" type="text" class="cajaGrande" id="paterno" size="45"
                                           maxlength="45"></td>
                            </tr>
                            <tr>
                                <td>Apellidos Materno</td>
                                <td><input NAME="materno" type="text" class="cajaGrande" id="materno" size="45"
                                           maxlength="45"></td>
                            </tr>

                        </table>
                    </div>
                    <div id="divDireccion" align="left" >
                        <table class="fuente8_2_3_4" cellspacing="0" cellpadding=3 border="0">
                            <tr>
                                <td width="28%">Direcci&oacute;n fiscal</td>
                                <td>
                                    <input NAME="direccion" type="text" class="cajaGrande" id="direccion" size="45"
                                           maxlength="100">
                                </td>
                            </tr>
                            <tr>
                                <td>Tel&eacute;fono</td>
                                <td><input id="telefono" name="telefono" type="text" class="cajaPequena" maxlength="15">
                                    &nbsp;M&oacute;vil
                                    <input id="movil" name="movil" type="text" class="cajaPequena" maxlength="15"/>
                                    &nbsp;Fax
                                    <input id="fax" name="fax" type="text" class="cajaPequena" maxlength="15"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Correo electr&oacute;nico</td>
                                <td><input NAME="email" type="text" class="cajaGrande" id="email" size="35"
                                           maxlength="50"></td>
                            </tr>
                            <tr>
                                <td>Direccion web</td>
                                <td>
                                    <input NAME="web" type="text" class="cajaGrande" id="web" size="45" maxlength="50"/>
                                </td>
                            </tr>
                            <td width="16%">Categoria</td>
                            <td colspan="3">
                                <select id="categoria" name="categoria" class="comboMedio">
                                    <?php echo $cbo_categoria; ?>
                                </select>
                            </td>


                        </table>
                        <input type="hidden" value="000000" name="cboNacimiento" id="cboNacimiento"/>
                        <input type="hidden" value="1" name="cboSexo" id="cboSexo"/>
                        <input type="hidden" value="193" name="cboNacionalidad" id="cboNacionalidad"/>
                        <input type="hidden" value="00" name="cboDepartamento" id="cboDepartamento"/>
                        <input type="hidden" value="00" name="cboProvincia" id="cboProvincia"/>
                        <input type="hidden" value="00" name="cboDistrito" id="cboDistrito"/>
                    </div>
                    <div align="left"  style="margin-top:20px;margin-bottom:10px; text-align: center">
                        <a href="javascript:;" id="imgGuardarProveedor"><img
                                src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22"
                                class="imgBoton"></a>
                        <a href="javascript:;" id="imgCancelarProveedor"><img
                                src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22"
                                class="imgBoton"></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<a href="#ventana" id="open" class="defaultDOMWindow"></a>
<a href="#ventana" id="close" class="defaultCloseDOMWindow"></a>
</body>
</html>