<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");
?>

<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script> 
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>      
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/pedido.js?=<?=JS;?>"></script>

<script src="http://code.jquery.com/jquery-1.10.2.js?=<?=JS;?>"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js?=<?=JS;?>"></script>
<script language="javascript">

$(document).ready(function(){
    $("#nombre_cliente").autocomplete({
       source: function(request, response){
                  $.ajax({  url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                      type: "POST",
                      data:  {  term: $("#nombre_cliente").val()},
                      dataType: "json", 
                      success: function(data){response(data);}
                  });
              }, 

              select: function(event, ui){
                  $("#ruc_cliente").val(ui.item.ruc)
                  $("#cliente").val(ui.item.codigo);
                  $("#nombre_cliente").val(ui.item.nombre);
              },

              minLength: 2

          });
});
</script>   
<div id="pagina">
    <div id="zonaContenido">
                <div align="center">
                        <div id="tituloForm" class="header">Buscar PEDIDO </div>
                        <div id="frmBusqueda">
                        <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $action;?>">
                            <table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
                                    <tr>
                                        <td width="16%">Numero de  O. Pedido </td>
                                        <td width="68%"><input id="txtNumDoc" type="text" class="cajaPequena" NAME="txtNumDoc" maxlength="15" value="<?php echo $numdoc; ?>" onkeypress="return numbersonly(this,event,'.');">
                                        <td width="5%">&nbsp;</td>
                                        <td width="5%">&nbsp;</td>
                                        <td width="6%" align="right"></td>
                                    </tr>
                                    <tr>
                                        <td align='left'>Cliente</td>
                                        <td align='left'>
                                          <input type="hidden" name="cliente" value="<?php echo $cliente; ?>" id="cliente" size="5" />
                                          <input type="text" name="ruc_cliente" value="<?php echo $ruc_cliente; ?>" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11"  onkeypress="return numbersonly(this,event,'.');" readonly="readonly" />
                                          <input type="text" name="nombre_cliente" tabindex="-1" value="<?php echo $nombre_cliente; ?>"  class="cajaGrande cajaSoloLectura" id="nombre_cliente" size="40" />
                                        </td>
                                    </tr>
                                    <tr>
                                      <td align='left' width="10%">Fecha inicial</td>
                                      <td align='left' width="90%">
                                           <input name="fechai" id="fechai" value="<?php echo $fechai; ?>" type="text" class="cajaGeneral" size="10" maxlength="10"/>
                                           <img src="<?=$base_url;?>public/images/icons/calendario.png" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                                   <script type="text/javascript">
                                                                Calendar.setup({
                                                                    inputField     :    "fechai",      // id del campo de texto
                                                                    ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                                    button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                                                });
                                                     </script>
                                              <label style="margin-left: 90px;">Fecha final</label>
                                               <input name="fechaf" id="fechaf" value="<?php echo $fechaf; ?>" type="text" class="cajaGeneral" size="10" maxlength="10" />
                                               <img src="<?=$base_url;?>public/images/icons/calendario.png" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                                     <script type="text/javascript">
                                                                Calendar.setup({
                                                                    inputField     :    "fechaf",      // id del campo de texto
                                                                    ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                                    button         :    "Calendario2"   // el id del botón que lanzará el calendario
                                                                });
                                                     </script>
                                        </td>
                                   </tr>
                                     
                            </table>
                        </form>
                  </div>
                  <div class="acciones">
                    <div id="botonBusqueda">
                           <ul id="imprimirPedido" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
                           <ul id="nuevoPedido" class="lista_botones"><li id="nuevo">Nuevo Pedido</li></ul>
                           <ul id="limpiarPedido" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
                           <ul id="buscarPedido" class="lista_botones"><li id="buscar">Buscar</li></ul>
                    </div>
                  <div id="lineaResultado">
                      <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border="0">
                            <tr>
                            <td width="100%" align="left">N de pedidos encontrados:&nbsp;<?php echo $registros;?> </td>
                            <td width="50%" align="right">&nbsp;</td>
                      </table>
                  </div>
                  </div>
                        <div id="cabeceraResultado" class="header"> <?php echo $titulo_tabla; ?> </div>

                      
                        <div id="frmResultado">
                        <table class="fuente8" width="100%" border="0" ID="Table1">
                          <tr>
                            <th class="cabeceraTabla">PEDIDOS RELACIONADOS CON UNA ORDEN DE COMPRA</th>
                            <th class="cabeceraTabla">PEDIDOS REALIZADOS POR SEDE</th>
                          </tr>
                          <tr>
                            <td style="width: 55%;" valign="top">
                              <table class="fuente8" border="0" valign="top">
                                <tr class="cabeceraTabla">
                                  <td>ITEM</td>
                                  <td>SEDE</td>
                                  <td>SERIE</td>
                                  <td>NUMERO</td>
                                  <td>OC RELACIONADA</td>
                                  <td colspan="5">ACCIONES</td>
                                </tr>
                                <?php
                                $i=1;
                                if(count($listaV)>0){
                                  foreach($listaV as $indice=>$valor){
                                    $class = $indice%2==0?'itemParTabla':'itemImparTabla'; ?>
                                        <tr class="<?php echo $class;?>">
                                            <td><div align="center"><?php echo $valor[0];?></div></td>
                                            <td><div align="left"><?php echo $valor[12];?></div></td>
                                            <td><div align="center"><?php echo $valor[1];?></div></td>
                                            <td><div align="center"><?php echo $valor[2];?></div></td>
                                            <td><div align="center"><?php echo $valor[9];?></div></td>
                                            <!--<td><div align="center"><?php echo $valor[3];?></div></td>-->
                                              <!--<td width="3%"><div align="center"><?php echo $valor[5];?></div></td>-->
                                              <td width="3%"><div align="center"><?php echo $valor[10];?></div></td>
                                              <td width="3%"><div align="center"><?php echo $valor[7];?></div></td>
                                              <td width="19%"><div align="center"><?php echo $valor[11];?></div></td>
                                        </tr> <?php
                                        $i++;
                                  }
                                }
                                else{ ?>
                                    <tr>
                                      <td colspan="9" class="mensaje">No hay ning&uacute;n pedido que cumpla con los criterios de b&uacute;squeda</td>
                                    </tr><?php
                                } ?>
                              </table>
                            </td>
                            <td style="width: 40%;" valign="top">
                              <table class="fuente8" border="0" valign="top">
                                <tr class="cabeceraTabla">
                                  <td>ITEM</td>
                                  <td>SEDE</td>
                                  <td>SERIE</td>
                                  <td>NUMERO</td>
                                  <td colspan="5">ACCIONES</td>
                                </tr>
                                <?php
                                $i=1;
                                if(count($listaC)>0){
                                  foreach($listaC as $indice=>$valor){
                                    $class = $indice%2==0?'itemParTabla':'itemImparTabla'; ?>
                                        <tr class="<?php echo $class;?>">
                                            <td><div align="center"><?php echo $valor[0];?></div></td>
                                            <td><div align="left"><?php echo $valor[12];?></div></td>
                                            <td><div align="center"><?php echo $valor[1];?></div></td>
                                            <td><div align="center"><?php echo $valor[2];?></div></td>
                                            <!--<td><div align="center"><?php echo $valor[3];?></div></td>-->
                                              <!--<td width="3%"><div align="center"><?php echo $valor[5];?></div></td>-->
                                              <td width="3%"><div align="center"><?php echo $valor[10];?></div></td>
                                              <td width="3%"><div align="center"><?php echo $valor[7];?></div></td>
                                              <td width="19%"><div align="center"><?php echo $valor[11];?></div></td>
                                        </tr> <?php
                                        $i++;
                                  }
                                }
                                else{ ?>
                                    <tr>
                                      <td colspan="9" width="100%" class="mensaje">No hay ning&uacute;n pedido que cumpla con los criterios de b&uacute;squeda</td>
                                    </tr> <?php
                                } ?>
                              </table>
                            </td>
                          </tr>
                        </table>
                        <input type="hidden" id="iniciopagina" name="iniciopagina">
                        <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
                </div>
            <div style="margin-top: 15px;"><?php echo $paginacion;?></div>
            <input type="text" style="visibility:hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
        </div>
    </div>
</div>