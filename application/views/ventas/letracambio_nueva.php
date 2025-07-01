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
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/letracambio.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
       
        
        <script type="text/javascript">		

            $(document).ready(function() {

                

                

<?php

if ($tipo_oper == 'V'):

    switch ($tipo_docu) {

        case 'F':

            ?> 

                                setLimite(<?php echo VENTAS_FACTURA; ?>)	

            <?php

            break;

        case 'B':

            ?>

                                setLimite(<?php echo VENTAS_BOLETA; ?>)	

            <?php

            break;

        case 'N':

            ?>

                                setLimite(<?php echo VENTAS_COMPROBANTE; ?>)	

            <?php

            break;

        default:

            break;

    } elseif ($tipo_oper == 'C') :

    switch ($tipo_docu) {

        case 'F':

            ?> 

                                setLimite(<?php echo COMPRAS_FACTURA; ?>)	

            <?php

            break;

        case 'B':

            ?>

                                setLimite(<?php echo COMPRAS_BOLETA; ?>)	

            <?php

            break;

        default:

            break;

    }

endif;

?>

                

                

        if($('#tdc').val()==''){

            alert("Antes de registrar comprobantes debe ingresar Tipo de Cambio")

            top.location="<?php echo base_url(); ?>index.php/index/inicio";

        }

        base_url  = $("#base_url").val();

        tipo_oper = $("#tipo_oper").val();

        almacen = $("#cboCompania").val();

        $("a#linkVerCliente, a#linkSelecCliente, a#linkVerProveedor, a#linkSelecProveedor").fancybox({

            'width'          : 700,

            'height'         : 550,

            'autoScale'	 : false,

            'transitionIn'   : 'none',

            'transitionOut'  : 'none',

            'showCloseButton': false,

            'modal'          : false,

            'type'           : 'iframe'

        });

        $(", #linkSelecProducto").fancybox({

            'width'	         : 800,

            'height'         : 500,

            'autoScale'	 : false,

            'transitionIn'   : 'none',

            'transitionOut'  : 'none',

            'showCloseButton': false,

            'modal'          : false,

            'type'	         : 'iframe'

        });

        $("a#linkVerProducto").fancybox({

            'width'          : 800,

            'height'         : 650,

            'autoScale'	 : false,

            'transitionIn'   : 'none',

            'transitionOut'  : 'none',

            'showCloseButton': false,

            'modal'          : true,

            'type'	     : 'iframe'

        });

        $("#linkVerImpresion").fancybox({

            'transitionIn'   : 'none',

            'transitionOut'  : 'none',

            'showCloseButton': false,

            'modal'          : true

        });

        $("a#verDocuRefe").fancybox({

            'width'          : 670,

            'height'         : 420,

            'autoScale'      : false,

            'transitionIn'   : 'none',

            'transitionOut'  : 'none',

            'showCloseButton': false,

            'modal'          : false,

            'type'	     : 'iframe',

            'onStart'        : function(){

                if(tipo_oper=='V'){

                    if($('#cliente').val()==''){

                        alert('Debe seleccionar el cliente.');

                        $('#ruc_cliente').focus();

                        return false;

                    }else

                        $('#verDocuRefe').attr('href', base_url+'index.php/almacen/guiarem/ventana_muestra_guiarem/'+tipo_oper+'/'+$('#cliente').val()+'/SELECT_HEADER/F/'+almacen);

                }

                else{

                    if($('#proveedor').val()==''){

                        alert('Debe seleccionar el proveedor.');

                        $('#ruc_proveedor').focus();

                        return false;

                    }else

                        $('#verDocuRefe').attr('href', base_url+'index.php/almacen/guiarem/ventana_muestra_guiarem/'+tipo_oper+'/'+$('#proveedor').val()+'/SELECT_HEADER/F/'+almacen);

                }

                                            

            }

        });

                

                        

    });

    $(function() {

        $("#buscar_producto").autocomplete({

            //flag = $("#flagBS").val();

            source: function(request, response){

                $.ajax({ 

                    url: "<?php echo base_url(); ?>index.php/almacen/producto/autocomplete/"+$("#flagBS").val(),

                    type: "POST",

                    data:  { 

                        term: $("#buscar_producto").val()

                    },

                    dataType: "json", 

                    success: function(data){

                        response(data);

                    }

                });

            }, 

            select: function(event, ui){

                $("#buscar_producto").val(ui.item.codinterno);

                $("#producto").val(ui.item.codigo)

                $("#codproducto").val(ui.item.codinterno);

                $("#costo").val(ui.item.pcosto);
                    
                $("#cantidad").focus();

                listar_unidad_medida_producto(ui.item.codigo);

                // obtener_producto_desde_codigo(n);

                // return false;

            },

            minLength: 2

        });

        /* Descativado hasta corregir vico 22082013  */
         $("#nombre_cliente").autocomplete({

            //flag = $("#flagBS").val();

            source: function(request, response){

                $.ajax({ 

                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",

                    type: "POST",

                    data:  { 

                        term: $("#nombre_cliente").val()

                    },

                    dataType: "json", 

                    success: function(data){

                        response(data);

                    }

                });

            }, 

            select: function(event, ui){

                //$("#nombre_cliente").val(ui.item.codinterno);

                $("#buscar_cliente").val(ui.item.ruc)

                $("#cliente").val(ui.item.codigo);

                $("#ruc_cliente").val(ui.item.ruc);

                $("#buscar_producto").focus();

            },

            minLength: 2

        });
        
        
        
        /* Descativado hasta corregir vico 22082013  */
         $("#nombre_clientedos").autocomplete({

            //flag = $("#flagBS").val();

            source: function(request, response){

                $.ajax({ 

                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",

                    type: "POST",

                    data:  { 

                        term: $("#nombre_clientedos").val()

                    },

                    dataType: "json", 

                    success: function(data){

                        response(data);

                    }

                });

            }, 

            select: function(event, ui){

                //$("#nombre_cliente").val(ui.item.codinterno);

                $("#buscar_clientedos").val(ui.item.ruc)

                $("#clientedos").val(ui.item.codigo);

                $("#ruc_clientedos").val(ui.item.ruc);

            },

            minLength: 2

        });
        
        
        
        
        /* Descativado hasta corregir vico 22082013  */
         $("#nombre_clientetres").autocomplete({

            //flag = $("#flagBS").val();

            source: function(request, response){

                $.ajax({ 

                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",

                    type: "POST",

                    data:  { 

                        term: $("#nombre_clientetres").val()

                    },

                    dataType: "json", 

                    success: function(data){

                        response(data);

                    }

                });

            }, 

            select: function(event, ui){

                //$("#nombre_cliente").val(ui.item.codinterno);

                $("#buscar_clientetres").val(ui.item.ruc)

                $("#clientetres").val(ui.item.codigo);

                $("#ruc_clientetres").val(ui.item.ruc);

            },

            minLength: 2

        });




        //////proveedor
        /* Descativado hasta corregir vico 22082013  */
         $("#nombre_proveedor").autocomplete({

            //flag = $("#flagBS").val();

            source: function(request, response){

                $.ajax({

                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",

                    type: "POST",

                    data:  {

                        term: $("#nombre_proveedor").val()

                    },

                    dataType: "json",

                    success: function(data){

                        response(data);

                    }

                });

            },

            select: function(event, ui){

                //$("#nombre_cliente").val(ui.item.codinterno);

                $("#buscar_proveedor").val(ui.item.ruc)

                $("#proveedor").val(ui.item.codigo);

                $("#ruc_proveedor").val(ui.item.ruc);

            },

            minLength: 2

        });


        $("#nombre_proveedordos").autocomplete({

            //flag = $("#flagBS").val();

            source: function(request, response){

                $.ajax({

                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",

                    type: "POST",

                    data:  {

                        term: $("#nombre_proveedordos").val()

                    },

                    dataType: "json",

                    success: function(data){

                        response(data);

                    }

                });

            },

            select: function(event, ui){

                //$("#nombre_cliente").val(ui.item.codinterno);

                $("#buscar_proveedordos").val(ui.item.ruc)

                $("#proveedordos").val(ui.item.codigo);

                $("#ruc_proveedordos").val(ui.item.ruc);

            },

            minLength: 2

        });


        $("#nombre_proveedortres").autocomplete({

            //flag = $("#flagBS").val();

            source: function(request, response){

                $.ajax({

                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",

                    type: "POST",

                    data:  {

                        term: $("#nombre_proveedortres").val()

                    },

                    dataType: "json",

                    success: function(data){

                        response(data);

                    }

                });

            },

            select: function(event, ui){

                //$("#nombre_cliente").val(ui.item.codinterno);

                $("#buscar_proveedortres").val(ui.item.ruc)

                $("#proveedortres").val(ui.item.codigo);

                $("#ruc_proveedortres").val(ui.item.ruc);

            },

            minLength: 2

        });

        ////
        

    });
    
    /*-----------------------------------*/

    function seleccionar_cliente(codigo,ruc,razon_social){

        $("#cliente").val(codigo);

        $("#ruc_cliente").val(ruc);

        $("#nombre_cliente").val(razon_social);

    }

    function seleccionar_proveedor(codigo,ruc,razon_social){

        $("#proveedor").val(codigo);

        $("#ruc_proveedor").val(ruc);

        $("#nombre_proveedor").val(razon_social);

    }
    
    function seleccionar_proveedordos(codigo,ruc,razon_social){

        $("#proveedordos").val(codigo);

        $("#ruc_proveedordos").val(ruc);

        $("#nombre_proveedordos").val(razon_social);

    }
    
    function seleccionar_proveedortres(codigo,ruc,razon_social){

        $("#proveedortres").val(codigo);

        $("#ruc_proveedortres").val(ruc);

        $("#nombre_proveedortres").val(razon_social);

    }

    function seleccionar_producto(producto,cod_interno,familia,stock,costo,flagGenInd){

        $("#codproducto").val(cod_interno);

        $("#producto").val(producto);

        $("#cantidad").focus();

        $("#stock").val(stock);

        $("#costo").val(costo);

        $("#flagGenInd").val(flagGenInd);

        listar_unidad_medida_producto(producto);



    }

    function seleccionar_documento_detalle(producto,codproducto,nombre_producto,cantidad,flagBS,flagGenInd,unidad_medida,nombre_medida,precio_conigv,precio_sinigv,precio,igv,importe,stock,costo){

        agregar_fila(producto,codproducto,nombre_producto,cantidad,flagBS,flagGenInd,unidad_medida,nombre_medida,precio_conigv,precio_sinigv,precio,igv,importe,stock,costo);

    }

    function seleccionar_guiarem(guia,serieguia,numeroguia){
                agregar_todo(guia);
                //alert(guia);
                serienumero="Numero de guia :"+serieguia+ " - " + numeroguia;
                $("#dRef").val(guia);

                $("#serieguiaver").html(serienumero);
                $("#serieguiaver").show(2000);
            }

        



    function valida()

    {

        if(document.forms[0].seriep.value.length>2)

        {

            document.forms[0].presupuesto.focus();

            return false;

        }

        else

            return true;

    }
    
    
    
    
//    		
//     function cargar_ubigeo(ubigeo,valor){
//                $("#cboNacimiento").val(ubigeo);
//                $("#cboNacimientovalue").val(valor);
//     }     
//     function cargar_ubigeo_complementario(departamento,provincia,distrito,valor,seccion,n){
//                if(seccion=="sucursal"){
//                    a = "dptoSucursal["+n+"]";
//                    b = "provSucursal["+n+"]";
//                    c = "distSucursal["+n+"]";
//                    d = "distritoSucursal["+n+"]"
//                    document.getElementById(a).value = departamento;
//                    document.getElementById(b).value = provincia;
//                    document.getElementById(c).value = distrito;
//                    document.getElementById(d).value = valor;
//                }
//      }
      
      
      


    // End -->

        

        </script>

    </head>

    <body>

        <input type="hidden" name="codigoguia" id="codigoguia" value="<?php echo $guia; ?>"/>

        <?php

//echo date("Y-m-d H:i:s");

// stylo para ocultar botones combos, etc

        $style = "";

        if (FORMATO_IMPRESION == 8) {

            $style = "display:none;";

        }

        ?>

        <!-- Inicio -->

        <div id="VentanaTransparente" style="display:none;">

            <div class="overlay_absolute"></div>

            <div id="cargador" style="z-index:2000">

                <table width="100%" height="100%" border="0" class="fuente8">

                    <tr valign="middle">

                        <td> Por Favor Espere    </td>

                        <td><img src="<?php echo base_url(); ?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>	</td>

                    </tr>

                </table>

            </div>

        </div>

        <!-- Fin -->		

        <form id="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>">

            <div id="zonaContenido" align="center">

                <?php echo validation_errors("<div class='error'>", '</div>'); ?>

                <div id="tituloForm" class="header" style="height: 20px">

                    <?php echo $titulo; ?>

                    <?php

                    if ($tipo_docu != 'N') {

                        if ($codigo == '') {

                            ?>

                    <select id="cboTipoDocu" style="display: none;" name="cboTipoDocu" class="comboMedio"  >

                                <option value="F" <?php if ($tipo_docu == 'F') echo 'selected="selected"'; ?>>FACTURA</option>

                                <option value="B" <?php if ($tipo_docu == 'B') echo 'selected="selected"'; ?>>BOLETA</option>

                            </select>

                            <?php

                        }

                    }else {

                        ?><input type="hidden" value="N" id="cboTipoDocu"  name="cboTipoDocu"/><?php }; ?>

                </div>

                <div id="frmBusqueda">

                    <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">

                        <tr>

                            <td width="8%">N&uacute;mero *</td>

                            <td width="38%" valign="middle">

                                <input class="cajaGeneral" name="serie" type="text" id="serie" size="4" maxlength="5" value="<?php  echo $serie; ?>" />&nbsp;

                                Ref.Girador  <input class="cajaGeneral" name="numero" type="text" id="numero" size="10" maxlength="10" value="<?php echo $numero; ?>" />

                                <?php if ($tipo_oper == 'V') { ?>

                                    <a href="javascript:;" id="linkVerSerieNum" <?php if ($codigo != '') echo 'style="display:none"' ?>>

                                        <p class="boleta" style="display:none"><?php echo $serie_suger_b . '-' . '' . $numero_suger_b ?> 



                                        </p>

                                        <p class="factura" style="display:none"><?php echo $serie_suger_f . '-' . '' . $numero_suger_f ?>



                                        </p>

                                        <p class="comprobante" style="display:none"><?php echo $serie_suger_f . '-' . $numero_suger_f ?>



                                        </p>

                                        <image src="<?php echo base_url(); ?>public/images/icons/flecha.png?=<?=IMG;?>" border="0" alt="Serie y número sugerido" title="Serie y número sugerido" />

                                    </a>

                                <?php } ?>

                                <!--<label style="margin-left:80px; margin-right: 20px;">IGV</label>-->

                                <!--<input NAME="igv" type="text" class="cajaGeneral cajaSoloLectura" id="igv" size="2" maxlength="2" value="<?php //echo $igv; ?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();" readonly="readonly" /> %-->

                                <!--<input type="hidden" name="descuento" id="descuento" value="" />-->

                            </td>

                            <?php if ($tipo_oper == 'V') { ?>

                            <td width="9%" style="display: none;" valign="middle">Presupuesto</td>

                                <td width="23%" style="display: none;" valign="middle">

                                    <select name="presupuesto" style="display: none;" id="presupuesto" class="comboMedio"  onfocus="<?php echo $focus; ?>"  ><?php echo $cboPresupuesto; ?></select>

                                    <input type="hidden" class="docSerie" id="seriep" name="seriep" size="3" maxlength="3" onkeyup="return valida()"/> - 

                                    <input type="hidden" class="docNumero" name="presupuesto" id="presupuesto" size="3" />

                                    <input type="hidden" name="presupuesto_codigo" id="presupuesto_codigo">

                                </td>

                            <?php } ?>

                            
                            <td width="7%" valign="middle">Fecha</td>

                            <td width="22%" valign="middle"><input name="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $hoy; ?>" size="10" maxlength="10" readonly="readonly" />

                                <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" />

                                <script type="text/javascript">

                                    Calendar.setup({

                                        inputField     :    "fecha",      // id del campo de texto

                                        ifFormat       :    "%d/%m/%Y",       // formaClienteto de la fecha, cuando se escriba en el campo de texto

                                        button         :    "Calendario1"   // el id del botón que lanzará el calendario

                                    });

                                </script>

                            </td>
                            
                            
                            <td width="7%" valign="middle">Fecha Venc.</td>

                            <td width="22%" valign="middle"><input name="fechavenc" type="text" class="cajaGeneral cajaSoloLectura" id="fechavenc" value="<?php echo $hoyvenc; ?>" size="10" maxlength="10" readonly="readonly" />

                                <img height="16" border="0" width="16" id="Calendario2" name="Calendario2" src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" />

                                <script type="text/javascript">

                                    Calendar.setup({

                                        inputField     :    "fechavenc",      // id del campo de texto

                                        ifFormat       :    "%d/%m/%Y",       // formaClienteto de la fecha, cuando se escriba en el campo de texto

                                        button         :    "Calendario2"   // el id del botón que lanzará el calendario

                                    });

                                </script>

                            </td>

                        </tr>

                        
                        <tr>

                                
                            <td>Lugar de Giro</td>

                            <td colspan="5" valign="middle">

                            <div id="divDireccion">

                                <table class="fuente8" width="98%" cellspacing="0" cellpadding="0" border="0">
                                    <tr height="10px">
                                        <td colspan="4"><hr/></td>
                                    </tr>
                                    <tr>
                                        <td>Departamento&nbsp;</td>
                                        <td colspan="3">
                                            <div id="divUbigeo">
                                                <select id="cboDepartamento" name="cboDepartamento" class="comboMedio" onchange="cargar_provincia(this);">
                                                    <?php echo $cbo_dpto; ?>
                                                </select>&nbsp;	&nbsp;
                                                Provincia&nbsp;&nbsp;	&nbsp;
                                                <select id="cboProvincia" name="cboProvincia" class="comboMedio" onchange="cargar_distrito(this);">
                                                    <?php echo $cbo_prov; ?>
                                                </select>&nbsp;	&nbsp;
                                                Distrito&nbsp;&nbsp;	&nbsp;
                                                <select id="cboDistrito" name="cboDistrito" class="comboMedio">
                                                    <?php echo $cbo_dist; ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%">Direcci&oacute;n  </td>
                                        <td colspan="3">
                                            <input NAME="direccion" type="text" class="cajaSuperGrande" id="direccion" size="45" maxlength="250" value="<?php echo $direccion; ?>">
                                            
                                        </td>
                                    </tr>
                                    <tr height="10px">
                                        <td colspan="4"><hr/></td>
                                    </tr>
                                    
                                </table>
                            </div>                

                            </td>

                        </tr>
                        
                        <!--///-->
                        <tr>

                                
                            <td>&nbsp;</td> 
                            <!--Lugar de Pago-->

                            <td colspan="5" valign="middle">

<!--                            <div id="divDireccion">

                                <table class="fuente8" width="98%" cellspacing="0" cellpadding="0" border="0">
                                    <tr height="10px">
                                        <td colspan="4"><hr/></td>
                                    </tr>
                                    <tr>
                                        <td>Departamento&nbsp;</td>
                                        <td colspan="3">
                                            <div id="divUbigeopago">
                                                <select id="cboDepartamentopago" name="cboDepartamentopago" class="comboMedio" onchange="cargar_provinciapago(this);">
                                                    <?php //echo $cbo_dptopago; ?>
                                                </select>&nbsp;	&nbsp;
                                                Provincia&nbsp;&nbsp;	&nbsp;
                                                <select id="cboProvinciapago" name="cboProvinciapago" class="comboMedio" onchange="cargar_distritopago(this);">
                                                    <?php //echo $cbo_provpago; ?>
                                                </select>&nbsp;	&nbsp;
                                                Distrito&nbsp;&nbsp;	&nbsp;
                                                <select id="cboDistritopago" name="cboDistritopago" class="comboMedio">
                                                    <?php //echo $cbo_distpago; ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="16%">Direcci&oacute;n  </td>
                                        <td colspan="3">
                                            <input NAME="direccionpago" type="text" class="cajaGrande" id="direccionpago" size="45" maxlength="250" value="<?php //echo $direccionpago; ?>">
                                            
                                            Cuenta del Banco&nbsp;&nbsp; <input NAME="cuentabanco" type="text" class="cajaMedia" id="cuentabanco" onkeypress="return numbersonly(this,event,'.');" size="20" maxlength="40" value="<?php echo $cuentabanco; ?>"> 
                                            
                                        </td>
                                    </tr>
                                    <tr height="10px">
                                        <td colspan="4"><hr/></td>
                                    </tr>
                                    
                                </table>
                            </div>                -->

                            Lugar de Pago o Cargo en la Cuenta del Banco &nbsp;&nbsp;&nbsp;
                            <input NAME="direccionpago" type="text" class="cajaSuperGrande" id="direccionpago" size="45" maxlength="250" value="<?php echo $direccionpago; ?>">
                                

                            </td>

                        </tr>
                        <!--///-->
                        
                        <tr>

                            <?php if ($tipo_oper == 'V') { ?>

                                <td>Girado </td>

                                <td valign="middle">

                                    <?php

//                                    if ($tipo_docu != 'F' && $cliente != 1662) {

                                        ?>

<!--                                    <input type="hidden" name="cliente" id="cliente" size="5" value="1662" />

                                        <input name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                        <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="00000000000" onkeypress="return numbersonly(this,event,'.');" />

                                        <input type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="40" maxlength="50"  value="<?php //echo $nombre_cliente; ?>" />-->

                                        <?php

//                                    } else {

                                        ?>

                                        <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>" />

                                        <input name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                        <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente; ?>" onkeypress="return numbersonly(this,event,'.');" />

                                        <input type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="40" maxlength="50"  value="<?php echo $nombre_cliente; ?>" />

                                        <?php

//                                    }

                                    ?>

    <!--<a href="<?php //echo base_url(); ?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php //echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->

<!--                                    <a href="<?php //echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a>-->

                                </td>

                            <?php } else { ?>

                                <td>A Orden de  </td>

                                <td valign="middle">

                                    <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>" />

                                    <input name="buscar_proveedor" type="text" class="cajaGeneral" id="buscar_proveedor" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                    <input type="hidden" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>" onkeypress="return numbersonly(this,event,'.');" />

                                    <input type="text" name="nombre_proveedor" class="cajaGeneral" id="nombre_proveedor" size="40" maxlength="50" value="<?php echo $nombre_proveedor; ?>" />

                                    <!--<a href="<?php //echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php //echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->

<!--                                    <a href="<?php //echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/" id="linkSelecProveedor"></a>-->

                                </td>

                            <?php } ?>

                                <td colspan="4" >
                                TDC &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input NAME="tdc" type="text" class="cajaGeneral cajaSoloLectura" id="tdc" size="3" value="<?php echo $tdc; ?>" onkeypress="return numbersonly(this,event,'.');" readonly="readonly" />    
                                </td>

                        </tr>
                        
                        
                        
                        <!--/////-->
                        
                        <tr>

                            <?php if ($tipo_oper == 'V') { ?>

                                <td>Fiador </td>

                                <td valign="middle">

                                    <?php

//                                    if ($tipo_docu != 'F' && $cliente != 1662) {

                                        ?>

<!--                                        <input type="hidden" name="cliente" id="cliente" size="5" value="1662" />

                                        <input name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                        <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="00000000000" onkeypress="return numbersonly(this,event,'.');" />

                                        <input type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="40" maxlength="50"  value="<?php //echo $nombre_cliente; ?>" />-->

                                        <?php

//                                    } else {

                                        ?>

                                        <input type="hidden" name="clientedos" id="clientedos" size="5" value="<?php echo $clientedos ?>" />

                                        <input name="buscar_clientedos" type="text" class="cajaGeneral" id="buscar_clientedos" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                        <input type="hidden" name="ruc_clientedos" class="cajaGeneral" id="ruc_clientedos" size="10" maxlength="11" onblur="obtener_clientedos();" value="<?php echo $ruc_clientedos; ?>" onkeypress="return numbersonly(this,event,'.');" />

                                        <input type="text" name="nombre_clientedos" class="cajaGeneral" id="nombre_clientedos" size="40" maxlength="50"  value="<?php echo $nombre_clientedos; ?>" />

                                        <?php

//                                    }

                                    ?>

<!--                                    <a href="<?php //echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a>-->

                                </td>

                            <?php } else { ?>

                                <td>Fiador </td>

                                <td valign="middle">

                                    <input type="hidden" name="proveedordos" id="proveedordos" size="5" value="<?php echo $proveedordos ?>" />

                                    <input name="buscar_proveedordos" type="text" class="cajaGeneral" id="buscar_proveedordos" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                    <input type="hidden" name="ruc_proveedordos" class="cajaGeneral" id="ruc_proveedordos" size="10" maxlength="11" onblur="obtener_proveedordos();" value="<?php echo $ruc_proveedordos; ?>" onkeypress="return numbersonly(this,event,'.');" />

                                    <input type="text" name="nombre_proveedordos" class="cajaGeneral" id="nombre_proveedordos" size="40" maxlength="50" value="<?php echo $nombre_proveedordos; ?>" />

                                    <!--<a href="<?php //echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php //echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->

<!--                                    <a href="<?php //echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/" id="linkSelecProveedor"></a>-->

                                </td>

                            <?php } ?>

                            <td valign="middle" colspan="4">
                                Moneda  
                                <select name="moneda" id="moneda" class="comboPequeno" style="width:150px;"><?php echo $cboMoneda; ?></select>

                            </td>

                            <td colspan="2">




                            </td>

                        </tr>
                        
                        
                        
                                                <tr>

                            <?php if ($tipo_oper == 'V') { ?>

                                <td>Aval Permanente </td>

                                <td valign="middle">

                                        <input type="hidden" name="clientetres" id="clientetres" size="5" value="<?php echo $clientetres ?>" />

                                        <input name="buscar_clientetres" type="text" class="cajaGeneral" id="buscar_clientetres" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                        <input type="hidden" name="ruc_clientetres" class="cajaGeneral" id="ruc_clientetres" size="10" maxlength="11" onblur="obtener_clientetres();" value="<?php echo $ruc_clientetres; ?>" onkeypress="return numbersonly(this,event,'.');" />

                                        <input type="text" name="nombre_clientetres" class="cajaGeneral" id="nombre_clientetres" size="40" maxlength="50"  value="<?php echo $nombre_clientetres; ?>" />

<!--                                    <a href="<?php //echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a>-->

                                </td>
                                
                                
                                <td valign="middle" colspan="4">
                                Representante &nbsp;&nbsp;&nbsp;&nbsp;<input NAME="representante" type="text" class="cajaGrande" id="representante" size="14" maxlength="250" value="<?php echo $representante; ?>">
                                </td>
                                

                            <?php } else { ?>

                                <td>Aval Permanente </td>

                                <td valign="middle">

                                    <input type="hidden" name="proveedortres" id="proveedortres" size="5" value="<?php echo $proveedortres ?>" />

                                    <input name="buscar_proveedortres" type="text" class="cajaGeneral" id="buscar_proveedortres" size="10" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER." />&nbsp;

                                    <input type="hidden" name="ruc_proveedortres" class="cajaGeneral" id="ruc_proveedortres" size="10" maxlength="11" onblur="obtener_proveedortres();" value="<?php echo $ruc_proveedortres; ?>" onkeypress="return numbersonly(this,event,'.');" />

                                    <input type="text" name="nombre_proveedortres" class="cajaGeneral" id="nombre_proveedortres" size="40" maxlength="50" value="<?php echo $nombre_proveedortres; ?>" />

                                    <!--<a href="<?php //echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php //echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>-->

<!--                                    <a href="<?php //echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/" id="linkSelecProveedor"></a>-->

                                </td>
                                
                                
                                <td valign="middle" colspan="4">
                                Representante &nbsp;&nbsp;&nbsp;&nbsp;<input NAME="representante" type="text" class="cajaGrande" id="representante" size="14" maxlength="250" value="<?php echo $representante; ?>">
                                </td>

<!--                            <td valign="middle">
                                &nbsp;
                                <select name="moneda" id="moneda" class="comboPequeno" style="width:150px;"><?php //echo $cboMoneda; ?></select>

                            </td>

                            <td colspan="2">

                            </td>-->

                            <?php } ?>

                            

                        </tr>
                        
                        <!--/////-->
                        
                        <tr style="display:none">
                            <td colspan="6">
                                Importa a Debitar en la siguiente cuenta de Banco que se indica 
                            </td>    
                        </tr>

                        <tr style="display:none">

                            <td colspan="2">BANCO  

                                <select name="cbobanco" id="cbobanco" class="comboMedio"><?php echo $cbobanco; ?></select>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <span>

                                    Oficina

                                    <input NAME="txtoficina" type="text" class="cajaMedia" id="txtoficina" size="20" maxlength="40" value="<?php echo $oficina; ?>">
                                
                                </span>

                            </td>

                            <td>Numero de Cuenta</td>

                            <td>
                                <input NAME="txtnumcuenta" type="text" class="cajaMedia" id="txtnumcuenta" onkeypress="return numbersonly(this,event,'.');" size="20" maxlength="40" value="<?php echo $numerocuenta; ?>">
                            </td>

                            <td>DC *</td>

                            <td>
                                <input NAME="txtdc" type="text" class="cajaMedia" id="txtdc" size="20" maxlength="40" value="<?php echo $dc; ?>">
                            </td>

                        </tr>

                    </table>

                </div>	

                



                <div id="frmBusqueda3">

                    <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">

                        <tr>

                            <td width="80%" rowspan="5" align="left">

                                <table  width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">

                                    <tr>

                                        <td colspan="4">Observación</td>

                                    </tr>

                                    <tr>

                                        <td colspan="4"><textarea id="observacion" name="observacion" class="cajaTextArea" style="width:97%; height:70px;"><?php echo $observacion; ?></textarea></td>

                                    </tr>

                                </table>

                            </td>

                            

                        </tr>


                        <tr>

                            <td class="busqueda">Importe</td>

                            <td align="right">
                                <div align="right"><input class="cajaPequena" name="importetotal" type="text" onkeypress="return numbersonly(this,event,'.');" id="importetotal" size="12" align="right" <?php

                        ?> value="<?php echo round($importetotal, 2); ?>" /></div>
                            </td>

                        </tr>  
                        <tr>
                            <td class="busqueda">&nbsp;</td>
                            <td align="right"></td>
                        </tr>
                        <tr>
                            <td class="busqueda">&nbsp;</td>
                            <td align="right"></td>
                        </tr>
                        <tr>
                            <td class="busqueda">&nbsp;</td>
                            <td align="right"></td>
                        </tr>

                    </table>



                </div>	

                <br />

                <div id="botonBusqueda2" style="padding-top:20px;">

                    <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />

                    <a href="javascript:;" id="grabarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>

                    <a href="javascript:;" id="limpiarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>

                    <a href="javascript:;" id="cancelarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>

                    <?php echo $oculto ?>

                </div>



            </div>

            <?php

            if ($cambio_comp == 1 && $total_det != 0) {

                if ($tipo_docu != "B" && $tipo_docu != "N") {

                    ?>

                    <script lang="javascript" type="text/javascript">

                        calcular_importe_todos(<?= $total_det ?>)

                    </script>

                    <?php

                } else {

                    ?>

                    <script lang="javascript" type="text/javascript">

                        modificar_pu_conigv_todos(<?= $total_det ?>)

                    </script>

                    <?php

                }

            }

            ?>

        </form>

        <a id="linkVerImpresion" href="#ventana"></a>

        <div id="ventana" style="display: none;" >

            <div id="imprimir" style="padding:20px; text-align: center;display: none;">

                <span style="font-weight: bold;">

                    <?php if ($tipo_docu == 'F') echo 'FACTURA'; else echo 'BOLETA'; ?>

                    <br/>

                    <input type="text" name="ser_imp" id="ser_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="fsd" class="cajaGeneral" maxlength="3" size="3">-

                    <input type="text" name="num_imp" id="num_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="lknmlk" class="cajaGeneral" maxlength="10" size="10">

                </span>

                <br/>

                <a href="javascript:;" id="imprimirComprobante"><img src="<?php echo base_url(); ?>public/images/icons/impresora.jpg?=<?=IMG;?>" class="imgBoton"  alt="Imprimir"></a>

                <br/>

                <br/>

                <a href="javascript:;" id="cancelarImprimirComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>

            </div>      

        </div>

    </body>

</html>