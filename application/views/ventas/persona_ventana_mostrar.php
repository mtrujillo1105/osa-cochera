<html>
<head>
   <title><?php echo TITULO;?></title>
   <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <script type="text/javascript">
    $(document).ready(function(){
       $('#cerrarPersona').click(function(){
          parent.$.fancybox.close(); 
       });
    });
</script>
<body>
    <div id="pagina">
        <div id="zonaContenido"  >
        <div align="center">
        <div id="tituloForm" class="header" style="width:95%"><?php echo $titulo;?></div>
        <div id="frmBusqueda" style="width:95%">

        <div id="datosPersona" >
            <table class="fuente8" width="100%" cellspacing=0 cellpadding="6" border="0">
                    <tr>
                      <td width="20%">Nombres</td>
                      <td  width="35%">
                        <b> <?php echo $datos[0]->PERSC_Nombre;?></b>
                      </td>
                      <td  width="25%">Lugar de Nacimiento</td>
                      <td  width="20%"><b><?php echo $nacimiento;?></b></td>
                    </tr>
                    <tr>
                        <td>Apellidos Paterno</td>
                        <td><b><?php echo $datos[0]->PERSC_ApellidoPaterno;?></b></td>
                      <td>Sexo</td>
                      <td><b><?php echo $sexo;?></b></td>
                    </tr>
                    <tr>
                        <td>Apellidos Materno</td>
                        <td><b><?php echo $datos[0]->PERSC_ApellidoMaterno;?></b></td>
                      <td>Estado Civil</td>
                      <td><b><?php echo $estado_civil;?></b></td>

                    </tr>
                    <tr>
                        <td>Tipo de Documento</td>
                        <td><b><?php echo $tipo_doc;?></b></td>
                      <td>Nacionalidad</td>
                      <td><b><?php echo $nacionalidad;?></b></td>
                    </tr>
                    <tr>
                        <td>Número de Documento</td>
                        <td><b><?php echo $datos[0]->PERSC_NumeroDocIdentidad;?></b></td>
                       <td>R.U.C.</td>
                          <td><b><?php echo $datos[0]->PERSC_Ruc;?></b></td>
                    </tr>
            </table>
            </div>

            <div id="divDireccion">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="6" border="0">
              <tr height="10px">
                <td colspan="4"><hr></td>
              </tr>
                <tr>
                  <td width="20%">Departamento</td>
                  <td colspan="3">
                    <div id="divUbigeo">
                    <b><?php echo $dpto;?></b>
                    &nbsp;&nbsp;
                    Provincia&nbsp;&nbsp;
                    <b><?php echo $prov;?></b>
                    &nbsp;&nbsp;
                    Distrito&nbsp;&nbsp;
                    <b><?php echo $dist;?></b>
                    </div>
                  </td>
              </tr>
                <tr>
                  <td>Direcci&oacute;n fiscal</td>
                  <td><b><?php echo $direccion;?></b></td>
               </tr>
            </table>
             </div>
            <table width="100%" class="fuente8" cellspacing=0 cellpadding=6 border="0">
                        <tr>
                            <td width="20%">Tel&eacute;fono </td>
                            <td width="20%"><b><?php echo $telefono;?></b></td>
                            <td width="15%">M&oacute;vil</td>
                            <td width="20%"><b><?php echo $movil;?></b></td>
                            <td width="15%">Fax</td>
                            <td width="10%"><b><?php echo $fax;?></b></td>
                        </tr>
                        <tr>
                            <td>Correo electr&oacute;nico  </td>
                            <td><b><?php echo $email;?></b></td>
                            <td>Direcci&oacute;n web </td>
                            <td colspan="3"><b><?php echo $web;?></b></td>
                        </tr>
                    </table>
         </div>
         <div id="botonBusqueda" style="width:95%">
                <a href="javascript:;" id="cerrarPersona"><img src="<?=$base_url;?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" class="imgBoton" /></a>
         </div>
      </div>
      </div>
    </div>
</body>
</html>

