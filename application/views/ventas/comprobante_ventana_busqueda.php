<html>
<head>
   <title><?php echo TITULO;?></title>
   <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/domwindow.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/empresa/cliente_popup.js?=<?=JS;?>"></script>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <script type="text/javascript">
        var base_url;
        $(document).ready(function() {
            base_url   = $("#base_url").val();
            $("#buscarProveedor").click(function(){
              $("#form_busqueda").submit();
             });
            $("#limpiarProveedor").click(function(){
                url = base_url+"index.php/empresa/proveedor/ventana_busqueda_proveedor";
                location.href=url;
            });
            $('#cerrarProveedor').click(function(){
              parent.$.fancybox.close(); 
            });
        });
        function seleccionar_cliente(codigo,ruc,razon_social, empresa, persona, direccion){
             parent.seleccionar_cliente(codigo,ruc,razon_social, empresa, persona, direccion);
             parent.$.fancybox.close(); 
        }
   </script>
</head>
<body>
<div align="center">
<form name="form_busqueda" id="form_busqueda" method="post" action="<?php echo $action;?>" >
    <div id="frmBusqueda" style="width:95%">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0">
           <tr class="cabeceraTabla" height="25px">
                <td align="center" colspan="4">CLIENTES</td>
           </tr>
            <tr height="35px">
                <td width="15%">RUC / DNI </td>
                <td width="85%" colspan="2"><input id="numdoc" type="text" class="cajaPequena" NAME="numdoc" maxlength="11" onkeypress="return numbersonly(this,event)" value="<?php echo $numdoc;?>" />
            </tr>
            <tr height="25px">
                <td>Nombres</td>
                <td><input id="nombre" name="nombre" type="text" class="cajaGrande" maxlength="35" value="<?php echo $nombre;?>"></td>
                <td align="right"><a href="javascript:;" id="buscarCliente"><img src="<?=$base_url;?>public/images/icons/botonbuscar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                                 <a href="javascript:;" id="limpiarCliente"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                                 <a href="javascript:;" id="nuevoCliente"><img src="<?=$base_url;?>public/images/icons/botonnuevocliente.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                                 <a href="javascript:;" id="cerrarCliente"><img src="<?=$base_url;?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
                </td>
            </tr>
        </table>
   </div>
    <div id="lineaResultado" style="width:95%; margin-top: 10px; ">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>
            <tr>
                <td width="50%" align="left">N de registros encontrados:&nbsp;<?php echo $registros;?></td>
                <td width="50%" align="right">&nbsp;</td>
            </tr>
        </table>
    </div>
   <div id="frmResultado" style="width:95%; height: 380px; overflow: auto; background-color: #f5f5f5">
    <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" border="0" cellpadding="4" cellspacing="1">
           <tr class="cabeceraTabla">
                            <td width="5%"><div align="center"><b>Item</b></div></td>
                            <td width="10%"><div align="center"><b>RUC</b></div></td>
                            <td width="10%"><div align="center"><b>DNI</b></div></td>
                            <td width="50%"><div align="center"><b>Nombre o Raz&oacute;n Social</b></div></td>
                            <td width="20%"><div align="center"><b>Tipo Cliente</b></div></td>
                            <td width="5%"><div align="center"></div></td>
           </tr>
           <?php
           $indice = 0;
           foreach($lista as $valor){
                $classfila          = $indice%2==0?"itemImparTabla":"itemParTabla";
           ?>
             <tr class="<?php echo $classfila;?>">
                <td><div align="center"><?php echo $valor[0];?></div></td>
               <td><div align="center"><?php echo $valor[1];?></div></td>
               <td><div align="center"><?php echo $valor[2];?></div></td>
               <td><div align="left"><?php echo $valor[3];?></div></td>
               <td><div align="center"><?php echo $valor[4];?></div></td>
               <td><div align="center"><?php echo $valor[5];?></div></td>
           </tr>
           <?php
           $indice++;
           }
           ?>
    </table>
    <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>" />
</div>
</form>
<div style="margin-top:15px" class="fuente8"><?php echo $paginacion;?></div>
</div>
   
<div id="ventana" style="display:none">
    <div id="pagina">
        <div align="center">
            <div id="tituloForm" class="header"  style="width:500px; top:0px;">REGISTRAR CLIENTE</div>
            <div id="frmBusqueda" style="width:500px; top:0px">
                <form name="frmCliente" id="frmCliente" method="post" action="<?php echo base_url()?>index.php/empresa/cliente/insertar_corto">
                    <div id="tipoPersona">
                        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="28%">Tipo Persona (*)</td>
                                <td>
                                    <input type="radio" id="tipo_persona" name="tipo_persona" value="0" checked='checked' />Persona Natural
                                    <input type="radio" id="tipo_persona" name="tipo_persona" value="1" />Persona Jur&iacute;dica
                                </td>
                            </tr>
                        </table>
                    </div>
                     <div id="datosEmpresa">
                           <table class="fuente8" width="100%" cellspacing=0 cellpadding="3" border="0">
                            <tr>
                              <td width="28%">N° Documento</td>
                              <td>
                                  <select name="cboTipoCodigo" id="cboTipoCodigo" class="comboMedio">
                                  <?php echo $tipocodigo; ?>
                                  </select>  
                                  <input id="ruc" type="text" class="cajaGeneral" NAME="ruc" size="10" maxlength="11" onkeypress="return numbersonly('ruc',event);" />
                                  <label id="ruc_msg" class="etiqueta_error"></label>
                              </td>
                            </tr>
                            <tr>
                                <td>Nombre o Raz&oacute;n Social (*)</td>
                                <td><input name="razon_social" type="text" class="cajaGrande" id="razon_social" maxlength="150" /></td>
                            </tr>
                        </table>
                    </div>
                       <div id="datosPersona">
                        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0">
                                <tr>
                                  <td width="28%">N° Documento</td>
                                  <td><select id="tipo_documento" name="tipo_documento" class="comboMedio" onchange="valida_tipoDocumento();">
                                           <?php echo $tipo_documento;?>
                                      </select>
                                      <input name="numero_documento" type="text" class="cajaGeneral" size="6" maxlength="8" id="numero_documento" size="15" maxlength="8" onkeypress="return numbersonly('numero_documento',event);" />
                                      <label id="numero_documento_msg" class="etiqueta_error"></label>
                                      R.U.C. <input type="text"  class="cajaGeneral" size="9" maxlength="11"  name="ruc_persona" id="ruc_persona" />
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
                                    <td><input NAME="paterno" type="text" class="cajaGrande" id="paterno" size="45" maxlength="45"></td>
                                </tr>
                                <tr>
                                    <td>Apellidos Materno</td>
                                    <td><input NAME="materno" type="text" class="cajaGrande" id="materno" size="45" maxlength="45"></td>
                                </tr>
                                </tr>
                        </table>
                     </div>
                    	<div id="divDireccion">
                                <table width="100%" class="fuente8" cellspacing=0 cellpadding=3 border="0">
                                    <tr>
                                      <td width="28%">Direcci&oacute;n fiscal</td>
                                      <td>
                                          <input NAME="direccion" type="text" class="cajaGrande" id="direccion" size="45" maxlength="100">
                                      </td>
                                   </tr>
                                    <tr>
                                        <td>Tel&eacute;fono </td>
                                        <td><input id="telefono" name="telefono" type="text" class="cajaPequena" maxlength="15">
                                         &nbsp;M&oacute;vil
                                        <input id="movil" name="movil" type="text" class="cajaPequena" maxlength="15"/>
                                         &nbsp;Fax
                                        <input id="fax" name="fax" type="text" class="cajaPequena" maxlength="15"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Correo electr&oacute;nico  </td>
                                        <td><input NAME="email" type="text" class="cajaGrande" id="email" size="35" maxlength="50"></td>
                                    </tr>
                                    <tr>
                                        <td>Direcci&oacute;n web </td>
                                        <td>
                                                <input NAME="web" type="text" class="cajaGrande" id="web" size="45" maxlength="50"/>
                                        </td>
                                    </tr>
                                    <tr>
                              <td width="16%">Categoría</td>
                              <td colspan="3">
                                 <select id="categoria" name="categoria" class="comboMedio">
                                 <?php echo $cbo_categoria; ?>
                                 </select>
                              </td>
                           </tr>
                         </table>
                         <input type="hidden" value="000000" name="cboNacimiento" id="cboNacimiento" />
                         <input type="hidden" value="1" name="cboSexo" id="cboSexo" />
                         <input type="hidden" value="0" name="cboEstadoCivil" id="cboEstadoCivil" />
                         <input type="hidden" value="193" name="cboNacionalidad" id="cboNacionalidad" />
                         <input type="hidden" value="00" name="cboDepartamento" id="cboDepartamento" />
                         <input type="hidden" value="00" name="cboProvincia" id="cboProvincia" />
                         <input type="hidden" value="00" name="cboDistrito" id="cboDistrito" />
                  </div>
                    <div style="margin-top:20px;margin-bottom:10px; text-align: center">
                        <a href="javascript:;" id="imgGuardarCliente"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <a href="javascript:;" id="imgCancelarCliente"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
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

