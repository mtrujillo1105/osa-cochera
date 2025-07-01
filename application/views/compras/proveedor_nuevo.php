<html>
    <head>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.validate.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/empresa/proveedor.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>     
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/sunat.js?=<?=JS;?>"></script> 
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />

        <script type="text/javascript">
            $(document).ready(function(){
                $("a#linkVerMarca").fancybox({
                    'width'          : 700,
                    'height'         : 450,
                    'autoScale'  : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': false,
                    'modal'          : true,
                    'type'       : 'iframe'
                });
            
                $("a#linkVerTipo").fancybox({
                    'width'          : 700,
                    'height'         : 450,
                    'autoScale'  : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': false,
                    'modal'          : true,
                    'type'       : 'iframe'
                }); 
            });
        
            jQuery(document).ready(function(){
                modo = $("#modo").val();
                tipo = $("#tipo").val();
                if(modo=='insertar'){
                    $("#nombres").val('No usado');
                    $("#paterno").val('No usado');
                    $("#ruc").focus();
                }
                else if(modo=='modificar'){
                    if(tipo=='0'){
                        $("#ruc").val('00000000000');
                    }
                    else if(tipo=='1'){
                        $("#nombres").val('No usado');
                        $("#paterno").val('No usado');              
                    }
                }
            });
            function buscar_marca(){            /*alert(n);
                $("#fila").val(n);*/
                base_url = $("#base_url").val();
                $('a#linkVerMarca').click();
            }       
            function buscar_tipo(n){
                $("#filatipo").val(n);
                base_url = $("#base_url").val();
                $('#linkVerTipo').click();
            }
        
            function seleccionar_marca(codigo,nombre){
                fila = $("#fila").val();
                a    = "marcaCodigo["+fila+"]";
                b    = "marcaNombre["+fila+"]";
                document.getElementById(a).value = codigo;
                document.getElementById(b).value = nombre;
            }
            function cargar_ubigeo(ubigeo,valor){
                $("#cboNacimiento").val(ubigeo);
                $("#cboNacimientovalue").val(valor);
            }
            function cargar_ubigeo_complementario(departamento,provincia,distrito,valor,seccion,n){
                if(seccion=="sucursal"){
                    a = "dptoSucursal["+n+"]";
                    b = "provSucursal["+n+"]";
                    c = "distSucursal["+n+"]";
                    d = "distritoSucursal["+n+"]"
                    document.getElementById(a).value = departamento;
                    document.getElementById(b).value = provincia;
                    document.getElementById(c).value = distrito;
                    document.getElementById(d).value = valor;
                }
            }
            function cargar_familia(familia,nombre,codfamilia){
                /*
                        $("#codigo_familia_aux").val(codfamilia);
                        $("#codigo_familia").val(codfamilia);
                        document.getElementById('familia').value = familia;
                        document.getElementById('nombre_familia').value = nombre;
                        $("#nombre_producto").focus();
            
                 */
                fila = $("#filatipo").val();
                a    = "tipoCodigo["+fila+"]";
                b    = "tipoNombre["+fila+"]";
                document.getElementById(a).value = familia;
                document.getElementById(b).value = nombre;
            }
        </script>
        <style>
            .cab1{
                background-color: #5F5F5F;
                font-weight: bold; 
                color: #fff;
            }
        </style>
    </head>
    <body>
        <!-- Inicio -->
        <div id="VentanaTransparente" style="display:none;">
            <div class="overlay_absolute"></div>
            <div id="cargador" style="z-index:2000">
                <table width="100%" height="100%" border="0" class="fuente8">
                    <tr valign="middle">
                        <td> Por Favor Espere    </td>
                        <td><img src="<?php echo base_url(); ?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a> </td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- Fin -->    
        <div id="pagina">
            <div id="zonaContenido">
                <div align="center">
                    <div id="tituloForm" class="header"><?php echo $titulo; ?></div>
                    <div id="frmBusqueda">
                        <form id="frmProveedor" name="frmProveedor" method="post" action="">
                            <div id="container" class="container">
                                <ol>
                                    <h4>Primero debe completar los siguientes campos antes de enviar.</h4>                      
                                    <div id="containerEmpresa">
                                        <li><label for="ruc" class="error">Por favor ingrese su ruc con sólo campos numéricos.</label></li>
                                        <li><label for="razon_social" class="error">Por favor ingrese un nombre o razon social.</label></li>
                                    </div>
                                    <div id="containerPersona">
                                        <li><label for="nombres" class="error">Por favor ingrese el nombre de la persona.</label></li>
                                         <li><label for="cboNacimiento" class="error">Por favor seleccione el  Lugar de Nacimiento.</label></li>
                                          <li><label for="cboEstadoCivil" class="error">Por favor seleccione  Estado Civil de la persona.</label></li>
                                        <li><label for="paterno" class="error">Por favor ingrese el apellido de la persoa.</label></li>
                                        <li><label for="email" class="error">Por favor ingrese el correo de la persona.</label></li>
                                        <li><label for="tipo_documento" class="error">Por favor seleccione un tipo de documento.</label></li>
                                        <li><label for="cboSexo" class="error">Por favor seleccione el sexo de la persona.</label></li>
                                        <li><label for="cboNacionalidad" class="error">Por favor seleccione una nacionalidad.</label></li>
                                    </div>
                                </ol>
                            </div>                  
                            <div align="left"class="fuente8" style="<?php echo $display_datosEmpresa; ?>float:left;height:20px;border: 0px solid #000;margin-top:7px;margin-left: 15px;width: 450px;">
                                <a href="#" id="idGeneral">General&nbsp;&nbsp;&nbsp;</a>
                                <a href="#" id="idSucursales">|&nbsp;Sucursales&nbsp;&nbsp;&nbsp;|</a>&nbsp;
                                <a href="#" id="idContactos">Cont&aacute;ctos&nbsp;&nbsp;&nbsp;</a>&nbsp;
                                <?php
                                    if($datos->id!="" || $datos->id!="" ){ ?>
                                        <a href="#" id="idCuentas">|&nbsp;&nbsp;Cuentas&nbsp;&nbsp;&nbsp;</a>&nbsp;
                                        <?php
                                    }
                                ?>
                                <a href="#" id="idAreas" style="display:none;">&Aacute;reas</a>
                                <a href="#" id="idMarcas" style="display: none;">|&nbsp;Marcas</a>
                                <a href="#" id="idTipos"  style="display: none;">|&nbsp;Tipo de Proveedor</a>
                            </div>
                            <div id="nuevoRegistro" style="display:none;float:right;width:150px;height:20px;border:0px solid #000;margin-top:7px;"><a href="#">Nuevo <image src="<?php echo base_url(); ?>public/images/icons/add.png?=<?=IMG;?>" name="agregarFila" id="agregarFila" border="0" alt="Agregar"></a></div><br><br>               
                            <div id="datosGenerales">
                                <div id="tipoProveedor">
                                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="4" border="0">
                                        <tr <?php echo $display; ?>>
                                            <td width="16%">Tipo Persona (*)</td>
                                            <td width="42%">
                                                <input type="radio" id="tipo_persona" name="tipo_persona" value="0">Persona Natural
                                                <input type="radio" id="tipo_persona" name="tipo_persona" value="1" checked='checked'>Persona Jur&iacute;dica
                                            </td>
                                            <td width="42%" colspan="2" rowspan="1" align="left" valign="top"><ul id="lista-errores"></ul></td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="datosEmpresa" style="<?php echo $display_datosEmpresa; ?>">
                                    <table class="fuente8" width="98%" cellspacing=0 cellpadding="4" border="0">
                                        <tr>
                                            <td width="16%">Tipo de Código (*)</td>
                                            <td colspan="3">
                                                <select name="cboTipoCodigo" id="cboTipoCodigo" class="comboMedio">
                                                    <?php echo $tipocodigo; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="16%">RUC / NIC (*)</td>
                                            <td colspan="3">
                                                  <!--<input id="ruc" type="text" class="cajaPequena" NAME="ruc" maxlength="11" value="<?php echo $datos->ruc; ?>" onkeypress="return numbersonly('numero_documento',event);" onblur="c(<?php echo $datos->id; ?>, <?php echo $datos->tipo; ?>);">-->
           <input id="ruc" type="text" class="cajaPequena" NAME="ruc" maxlength="11" value="<?php echo $datos->ruc; ?>" onkeypress="return numbersonly('numero_documento',event);" onblur="buscar_empresa();" />
                                                <label id="empresa_msg" name="empresa_msg"></label>
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
                                            <td width="16%">Nombre o Raz&oacute;n Social(*)</td>
                                            <td colspan="3"><input name="razon_social" type="text" class="cajaGrande" id="razon_social" maxlength="150" value="<?php echo $datos->nombre; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td width="16%">Sector Comercial</td>
                                            <td colspan="3"><select id="sector_comercial" name="sector_comercial" class="comboMedio" style="width:240px">
                                                    <?php echo $cbo_sectorComercial; ?>
                                                </select></td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="datosPersona" style="<?php echo $display_datosPersona; ?>">
                                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="4" border="0">
                                        <tr>
                                            <td width="16%">Tipo de Documento&nbsp;(*)</td>
                                            <td>
                                                <select id="tipo_documento" name="tipo_documento" class="comboMedio" onchange="valida_tipoDocumento();">
                                                    <?php echo $tipo_documento; ?>
                                                </select>
                                            </td>
                                            <td>Número de Documento</td>
                                            <td><input name="numero_documento" type="text" class="cajaMedia" id="numero_documento" size="15" maxlength="8" value="<?php echo $numero_documento; ?>" onkeypress="return numbersonly('numero_documento',event);" onblur="buscar_persona();">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><label id="persona_msg" name="persona_msg"></label></td>
                                        </tr>
                                        <tr>
                                            <td>Nombres&nbsp;(*)</td>
                                            <td>
                                                <input id="nombres" type="text" class="cajaGrande" name="nombres" maxlength="45" value="<?php echo $nombres; ?>">
                                            </td>
                                            <td>Lugar de Nacimiento</td>
                                            <td>
                                                <input type="hidden" name="cboNacimiento" id="cboNacimiento" class="cajaMedia" value="<?php echo $cboNacimiento; ?>"/>
                                                <input type="text" name="cboNacimientovalue" id="cboNacimientovalue" class="cajaMedia cajaSoloLectura" readonly="readonly" value="<?php echo $cboNacimientovalue; ?>" ondblclick="abrir_formulario_ubigeo();"/>
                                                <a href="#" onclick="abrir_formulario_ubigeo();"><image src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border='0'></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Apellidos Paterno&nbsp;(*)</td>
                                            <td><input NAME="paterno" type="text" class="cajaGrande" id="paterno" size="45" maxlength="45" value="<?php echo $paterno; ?>"></td>
                                            <td>Sexo&nbsp;(*)</td>
                                            <td>
                                                <select name="cboSexo" id="cboSexo" class="comboMedio">
                                                    <option value=''>::Seleccione::</option>
                                                    <option value='0' <?php if ($sexo == '0') echo "selected='selected'"; ?>>MASCULINO</option>
                                                    <option value='1' <?php if ($sexo == '1') echo "selected='selected'"; ?>>FEMENINO</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Apellidos Materno</td>
                                            <td><input NAME="materno" type="text" class="cajaGrande" id="materno" size="45" maxlength="45" value="<?php echo $materno; ?>"></td>
                                            <td>Estado Civil</td>
                                            <td>
                                                <select required name="cboEstadoCivil" id="cboEstadoCivil" class="comboMedio">
                                                    <?php echo $cbo_estadoCivil; ?>
                                                </select>
                                            
                                            </td>

                                        </tr>
                                        </tr>
                                        <tr>
                                            <td>Nacionalidad&nbsp;(*)</td>
                                            <td>
                                                <select name="cboNacionalidad" id="cboNacionalidad" class="comboMedio">
                                                    <?php echo $cbo_nacionalidad; ?>
                                                </select>
                                            </td>
                                            <td>RUC</td>   
                                            <td><input id="ruc_persona" type="text" class="cajaMedia" name="ruc_persona" size="45" maxlength="11" value="<?php echo $ruc; ?>"></td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="divDireccion">

                                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="4" border="0">
                                        <tr height="10px">
                                            <td colspan="4"><hr></td>
                                        </tr>
                                        <tr>
                                            <td>Departamento&nbsp;</td>
                                            <td colspan="3">
                                                <div id="divUbigeo">
                                                    <select id="cboDepartamento" name="cboDepartamento" class="comboMedio" onchange="cargar_provincia(this);">
                                                        <?php echo $cbo_dpto; ?>
                                                    </select>&nbsp; &nbsp;
                                                    Provincia&nbsp;&nbsp;   &nbsp;
                                                    <select id="cboProvincia" name="cboProvincia" class="comboMedio" onchange="cargar_distrito(this);">
                                                        <?php echo $cbo_prov; ?>
                                                    </select>&nbsp; &nbsp;
                                                    Distrito&nbsp;&nbsp;    &nbsp;
                                                    <select id="cboDistrito" name="cboDistrito" class="comboMedio">
                                                        <?php echo $cbo_dist; ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="16%">Direcci&oacute;n fiscal</td>
                                            <td colspan="3"><input NAME="direccion" type="text" class="cajaSuperGrande" id="direccion" size="45" maxlength="250" value="<?php echo $datos->direccion; ?>" />
                                                TIPO VIA / NOMBRE VIA / N° / INTERIOR / ZONA
                                            </td>
                                        </tr>
                                        <tr height="10px">
                                            <td colspan="4"><hr></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <table width="100%" class="fuente8" cellspacing=0 cellpadding=3 border="0">
                                                    <tr>
                                                        <td width="16%">Tel&eacute;fono </td>
                                                        <td><input id="telefono" name="telefono" type="text" class="cajaPequena" maxlength="15" value="<?php echo $datos->telefono; ?>"></td>
                                                        <td>M&oacute;vil</td>
                                                        <td><input id="movil" name="movil" type="text" class="cajaPequena" maxlength="15" value="<?php echo $datos->movil; ?>"></td>
                                                        <td>Fax</td>
                                                        <td><input id="fax" name="fax" type="text" class="cajaPequena" maxlength="15" value="<?php echo $datos->fax; ?>"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Correo electr&oacute;nico  </td>
                                                        <td><input NAME="email" type="text" class="cajaGrande" id="email" size="35" maxlength="50" value="<?php echo $datos->email; ?>"></td>
                                                        <td>Direcci&oacute;n web </td>
                                                        <td colspan="3">
                                                            <input NAME="web" type="text" class="cajaGrande" id="web" size="45" maxlength="50" value="<?php echo $datos->web; ?>">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr height="10px">
                                            <td colspan="4"><hr></td>
                                        </tr>
                                        <tr>
                                            <td width="16%">Cta. Cte. Soles</td>
                                            <td colspan="3">
                                                <input NAME="ctactesoles" type="text" class="cajaMedia" id="ctactesoles" size="45" maxlength="50" value="<?php echo $datos->ctactesoles; ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="16%">Cta. Cte. Dolares</td>
                                            <td colspan="3">
                                                <input NAME="ctactedolares" type="text" class="cajaMedia" id="ctactedolares" size="45" maxlength="50" value="<?php echo $datos->ctactedolares; ?>" />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- Inicio de Tipos de proveedor -->        

                            <input type="hidden" name="clickedtipo" id="clickedtipo" value=""/>
                            <input type="hidden" name="filatipo" id="filatipo" value="<?php echo count($listado_empresaTipos); ?>" />
                            <a href="<?php echo base_url(); ?>index.php/almacen/tipoproveedor/nueva_familia" id='linkVerTipo'></a>
                            <div id="datosTipos" style="display:none;">
                                <table id="tablaTipo" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                    <tr align="center" class="cab1" height="10px;">
                                        <td>Nro</td>
                                        <td>Tipo de Proveedor</td>
                                        <td>Borrar</td>
                                        <td>Editar</td>
                                    </tr>
                                    <?php
                                    $kk = 1;
                                    $cantidad = count($listado_empresaTipos);
                                    if ($cantidad > 0) {
                                        foreach ($listado_empresaTipos as $indice => $valor) {
                                            $registro = $valor->registro;
                                            $numero = $valor->numero;
                                            $nombre_marca = $valor->nombre_tipo;
                                            ?>
                                            <tr bgcolor="#ffffff">
                                                <td align="center"><?php echo $kk; ?></td>
                                                <td align="left"><?php echo $valor->nombre_tipo; ?></td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <a href="#" onclick="eliminar_tipo(<?php echo $registro; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/delete.gif?=<?=IMG;?>" border="0"></a>
                                                </td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <!--
                                                            <div id="idEdit"><a href="#" onclick="insertar_tipo(<?php echo $registro; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/edit.gif?=<?=IMG;?>" border="0"></a></div>
                                                            <div id="idSave" style="display:none;"><a href="#"><img src="<?php echo base_url(); ?>public/images/icons/save.gif?=<?=IMG;?>" border="0"></a></div>
                                                    -->
                                                </td>
                                            </tr>                       
                                            <?php
                                            $kk++;
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                                $displayTipos = $cantidad != 0 ? "display:none;" : "";
                                ?>
                                <div id="msgRegistrosTipos" style="width:98%;text-align:center;height:20px;border:1px solid #000;<?php echo $displayTipos; ?>">NO EXISTEN REGISTROS</div>
                            </div>

                            <!-- Fin Tipos de proveedor -->
                            <input type="hidden" name="fila" id="fila" value="<?php echo count($listado_empresaMarcas); ?>" />
                            <div id="datosMarcas" style="display:none;">                  <a href="<?php echo base_url(); ?>index.php/maestros/marca/ventana_busqueda_marca/" id='linkVerMarca'></a>
                                <table id="tablaMarca" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                    <tr align="center"  height="10px;">
                                        <td class="cab1">Nro</td>
                                        <td class="cab1">Nombre de la Marca</td>
                                        <td class="cab1">Borrar</td>
                                        <td Class="cab1">Editar</td>
                                    </tr>
                                    <?php
                                    $kk = 1;
                                    $cantidad = count($listado_empresaMarcas);
                                    if ($cantidad > 0) {
                                        $i = 0;
                                        foreach ($listado_empresaMarcas as $indice => $valor) {
                                            $registro = $valor->registro;
                                            $numero = $valor->numero;
                                            $nombre_marca = $valor->nombre_marca;
                                            $i++;
                                            ?>
                                            <tr class="<?php if ($i % 2 == 0) echo "itemParTabla"; else echo "itemImparTabla" ?>" bgcolor="#ffffff">
                                                <td align="center"><?php echo $kk; ?></td>
                                                <td align="left"><?php echo $valor->nombre_marca; ?></td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <a href="#" onclick="eliminar_marca(<?php echo $registro; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/delete.gif?=<?=IMG;?>" border="0"></a>
                                                </td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <!--
                                                            <div id="idEdit"><a href="#" onclick="insertar_marca(<?php echo $registro; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/edit.gif?=<?=IMG;?>" border="0"></a></div>
                                                            <div id="idSave" style="display:none;"><a href="#"><img src="<?php echo base_url(); ?>public/images/icons/save.gif?=<?=IMG;?>" border="0"></a></div>
                                                    -->
                                                </td>
                                            </tr>                       
                                            <?php
                                            $kk++;
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                                $displayMarcas = $cantidad != 0 ? "display:none;" : "";
                                ?>
                                <div id="msgRegistrosMarcas" style="width:98%;text-align:center;height:20px;border:1px solid #000;<?php echo $displayMarcas; ?>">NO EXISTEN REGISTROS</div>
                            </div>
                            <div id="datosContactos" style="display:none;">
                                <table id="tablaContacto" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                    <tr align="center" class="cab1" height="10px;">
                                        <td>Nro</td>
                                        <td>Nombre del Contacto</td>
                                        <td>Area</td>
                                        <td>Cargo</td>
                                        <td>Telefonos</td>
                                        <td>E-mail</td>
                                        <td>Borrar</td>
                                        <td>Editar</td>
                                    </tr>
                                    <?php
                                    $kk = 1;
                                    $cantidad = count($listado_empresaContactos);
                                    if ($cantidad > 0) {
                                        foreach ($listado_empresaContactos as $indice => $valor) {
                                            $persona = $valor->persona;
                                            $telefono = $valor->telefono == '' ? '&nbsp;' : $valor->telefono;
                                            $movil = $valor->movil;
                                            $email = $valor->email == '' ? '&nbsp;' : $valor->email;
                                            if ($movil != '')
                                                $telefono = $telefono . "&nbsp;/" . $movil;
                                            ?>
                                            <tr bgcolor="#ffffff">
                                                <td align="center"><?php echo $kk; ?></td>
                                                <td align="left"><?php echo $valor->nombre_persona; ?></td>
                                                <td><?php echo $valor->nombre_area; ?></td>
                                                <td><?php echo $valor->nombre_cargo; ?></td>
                                                <td><?php echo $telefono; ?></td>
                                                <td><?php echo $valor->email; ?></td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <a href="#" onclick="eliminar_contacto(<?php echo $persona; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/delete.gif?=<?=IMG;?>" border="0"></a>
                                                </td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <div id="idEdit"><a href="#" onclick="editar_contacto(<?php echo $persona; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/edit.gif?=<?=IMG;?>" border="0"></a></div>
                                                    <div id="idSave" style="display:none;"><a href="#"><img src="<?php echo base_url(); ?>public/images/icons/save.gif?=<?=IMG;?>" border="0"></a></div>
                                                </td>
                                            </tr>                       
                                            <?php
                                            $kk++;
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                                $displayContactos = $cantidad != 0 ? "display:none;" : "";
                                ?>
                                <div id="msgRegistros" style="width:98%;text-align:center;height:20px;border:1px solid #000;<?php echo $displayContactos; ?>">NO EXISTEN REGISTROS</div>
                            </div>
                            <div id="datosSucursales" style="display:none;">
                                <table id="tablaSucursal" width="98%" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                    <tr align="center" class="cab1" height="10px;">
                                        <td width="30">Nro</td>
                                        <td width="70">Nombre</td>
                                        <td width="120">Tipo Establecimiento</td>                           
                                        <td width="350">Direccion Sucursal (*)</td>
                                        <td width="200">Departamento / Provincia / Distrito</td>
                                        <td>Borrar</td>
                                        <td>Editar</td>
                                    </tr>
                                    <?php
                                    $kk = 1;
                                    $cantidad2 = count($listado_empresaSucursal);
                                    if ($cantidad2 > 0) {
                                        foreach ($listado_empresaSucursal as $indice => $valor) {
                                            $sucursal = $valor->sucursal;
                                            ?>
                                            <tr bgcolor="#ffffff">
                                                <td align="center"><?php echo $kk; ?></td>
                                                <td align="left"><?php echo $valor->descripcion; ?></td>
                                                <td><?php echo $valor->nombre_tipo; ?></td>                         
                                                <td align="left"><?php echo $valor->direccion; ?></td>
                                                <td><?php echo $valor->des_ubigeo; ?></td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <a href="#" onclick="eliminar_sucursal(<?php echo $sucursal; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/delete.gif?=<?=IMG;?>" border="0"></a>
                                                </td>
                                                <td align="center" <?php if ($modo == 'insertar') echo "style='display:none;'"; ?>>
                                                    <div id="idEdit">
                                                        <a href="#" onclick="editar_sucursal(<?php echo $sucursal; ?>);"><img src="<?php echo base_url(); ?>public/images/icons/edit.gif?=<?=IMG;?>" border="0"></a>
                                                    </div>
                                                    <div id="idSave" style="display:none;"><a href="#"><img src="<?php echo base_url(); ?>public/images/icons/save.gif?=<?=IMG;?>" border="0"></a></div>
                                                </td>
                                            </tr>                       
                                            <?php
                                            $kk++;
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                                $displaySucursal = $cantidad2 != '0' ? "display:none;" : "";
                                ?>
                                <div id="msgRegistros2" style="width:98%;text-align:center;height:20px;border:1px solid #000;<?php echo $displaySucursal; ?>">NO EXISTEN REGISTROS</div>
                            </div>






<!------------------------------------------------------BEGIN CUENTASSSSSSS -->


                            <div id="datosCuentas" style="display:none;">
                                <!--<link href="<?=base_url()?>public/css/jquery.paginate.css?=<?=CSS;?>" rel="stylesheet" type="text/css"> 
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js?=<?=JS;?>"> </script>
                                <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js?=<?=JS;?>"></script>-->
                                <script type="text/javascript"></script>
                                <div id="contenedorCuenta">
                                    <table id="tableData" border="0" class="fuente8" width="98%" cellspacing="0" cellpadding="6">
                                        <tr>
                                            <td>Banco (*)</td>
                                            <td>
                                                <select id="txtBanco" name="txtBanco" autofocus >
                                                    <option value="S">::SELECCIONE::</option>
                                                    <?php
                                                        if(count($listBanco)>0){
                                                            foreach ($listBanco as $key => $value) { ?>
                                                                <option value="<?=$value->BANP_Codigo?>"><?php echo $value->BANC_Siglas.' - '.$value->BANC_Nombre?></option>
                                                                <?php
                                                            }
                                                        } ?>
                                                </select>
                                            </td>
                                            <td>N° Cuenta (*)</td>
                                            <td>
                                                <input  maxlength="20" type="text" id="txtCuenta" name="txtCuenta" onkeypress="return soloLetras_andNumero(event)" onkeyup="onkeypress_cuenta()">
                                            </td>
                                            <td>Titular (*)</td>
                                            <td>
                                                <input maxlength="20" type="text" id="txtTitular" name="txtTitular" onkeypress="return soloLetras_andNumero(event)">
                                            </td> 
                                        </tr>
                                        <tr>
                                            <td>Oficina (*)</td>
                                            <td><input type="text" name="txtOficina" id="txtOficina" onkeypress="return soloLetras_andNumero(event)"></td>
                                            <td>Sectoriza (*)</td>
                                            <td><input type="text" name="txtSectoriza" id="txtSectoriza" onkeypress="return soloLetras_andNumero(event)"></td>
                                            <td>Interbancaria (*)</td>
                                            <td><input type="text" name="txtInterban" id="txtInterban" onkeypress="return soloLetras_andNumero(event)" value=""></td>
                                        </tr>
                                        <tr>
                                            <td>Tipo de Cuenta (*)</td>
                                            <td>
                                                <select name="txtTipoCuenta" id="txtTipoCuenta" >
                                                    <option value="S">::SELECCIONE::</option>
                                                    <option value='1' >Ahorros</option>
                                                    <option value='2' >Corriente</option>
                                                </select>
                                            </td>
                                            <td>Moneda (*)</td>
                                            <td>
                                                <select id="txtMoneda" name="txtMoneda">
                                                    <option value="S">::SELECCIONE::</option>
                                                    <?php
                                                        if(count($listMoneda)>0){
                                                            foreach ($listMoneda as $key => $value) { ?>
                                                                <option value="<?=$value->MONED_Codigo?>" ><?=$value->MONED_Descripcion?></option> <?php
                                                            }
                                                        } ?>
                                                </select>
                                            </td>
                                            <td>
                                            </td>
                                            <td> 
                                                <input type="hidden" id="txtCodCuenEmpre" name="txtCodCuenEmpre" value="">
                                                <a href="#" id="btncancelarCuentaE" onclick="insertar_cuentaEmpresa()">
                                                <img src="<?=base_url()?>public/images/icons/botonagregar.jpg?=<?=IMG;?>"></a>
                                                <a href="#" id="btnCancelarCuentaE">
                                                <img src="<?=base_url()?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>"></a>
                                                <br> <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">campos obligatorios (*)</td>
                                        </tr>
                                    </table>
                                </div>

                                <div id="contenidoCuentaTable">
                                    <table id="tableBancos" class="table table-bordered table-striped fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                        <thead id="theadBancos">
                                            <tr align="center" class="cab1" height="10px;">
                                                <td>Item</td>
                                                <td>Banco</td>
                                                <td>N° Cuenta</td>
                                                <td>Nombre o Titular de la cuenta</td>
                                                <td>Moneda</td>
                                                <td>Tipo de cuanta</td>
                                                <td colspan="3">Acciones</td>
                                            </tr> 
                                        </thead>
                                        <tbody id="tbodyBancos">
                                            <?php
                                                $kk=1;
                                      
                                                if(count($listado_cuentaEmpresa)>0){
                                                    foreach ($listado_cuentaEmpresa as $key => $value) { ?>
                                                        <tr bgcolor="#ffffff">
                                                            <td align="center"><?=$key+1?></td>
                                                            <td align="left"><?=$value->BANC_Nombre?></td>
                                                            <td><?=$value->CUENT_NumeroEmpresa?></td>              
                                                            <td align="left"><?=$value->CUENT_Titular?></td>
                                                            <td align="left"><?=$value->MONED_Descripcion?></td>
                                                            <?php
                                                                if($value->CUENT_TipoCuenta==1){ ?>
                                                                    <td>Ahorros</td>
                                                                <?php
                                                                } else{ ?>
                                                                    <td>Corriente</td> <?php
                                                                }
                                                            ?>
                                                    
                                                            <td align="center">
                                                                <a href="#" onclick="eliminar_cuantaEmpresa(<?=$value->CUENT_Codigo?>);"><img src="<?=$base_url;?>public/images/icons/delete.gif?=<?=IMG;?>" border="0"></a>
                                                            </td>
                                                            <td align="center">
                                                                <a href="#" id="btnAcualizarE<?=$value->CUENT_Codigo?>" onclick="actualizar_cuentaEmpresa(<?=$value->CUENT_Codigo?>);"><img src="<?=$base_url;?>public/images/icons/modificar.png?=<?=IMG;?>" border="0"></a>
                                                            </td>
                                                            <td>
                                                                <a href="#"  onclick="ventanaChekera(<?=$value->CUENT_Codigo?>)"><img src="<?=base_url()?>public/images/icons/observaciones.png?=<?=IMG;?>"></a>
                                                            </td>
                                                        </tr>           
                                                        <?php
                                                    }
                                                } else{ ?>
                                                        <tr>
                                                            <td align="center" colspan="10">
                                                                <div>NO EXISTEN REGISTROS</div>
                                                            </td>
                                                        </tr>
                                                <?php
                                                } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

<!------------------------------------------------------END CUENTASSSSSSS -->

                            <div style="margin-top:20px; text-align: center">
                                <a href="#" id="imgGuardarProveedor"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
                                <a href="#" id="imgLimpiarProveedor"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
                                <a href="#" id="imgCancelarProveedor"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
                                <input id="accion" name="accion" value="alta" type="hidden">
                                <input id="modo" name="modo" type="hidden" value="<?php echo $modo; ?>">
                                <input type="hidden" name="opcion" id="opcion" value="1">
                                <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">
                                <input type="hidden" id="tipo" name="tipo" value="<?php echo $tipo_persona; ?>">
                                <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">              
                                <input type="hidden" name="empresa_persona" id="empresa_persona" value="<?php echo $datos->id; ?>"/>
                                <input type="hidden" name="TIP_Codigo" id="TIP_Codigo" value="<?=$datos->TIP_Codigo?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </body>
</html>