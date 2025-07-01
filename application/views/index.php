<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    
    <title>SISTEMA COCHERAS</title>
    <link rel="stylesheet" href="<?=$base_url;?>public/css/theme.css?=<?=CSS;?>" type="text/css"/>        
    <script language="javaScript" src="<?=$base_url;?>public/js/menu/JSCookMenu.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.min.js?=<?=JS;?>"></script>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?=$base_url;?>resources/assets/bootstrap/css/bootstrap.min.css">  
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=$base_url;?>resources/assets/css/look.css">    
  
    <link rel="shortcut icon" href="<?=base_url(); ?>resources/assets/img/favicon.png"> 
</head>

<body class="hold-transition fondo-login">    
    <div class="login-box">  
        
        <!--div class="login-logo">
            <p style="color: white">Login</p>
            <a href="#"><img src="<?=$base_url;?>resources/assets/img/demoosa.png" alt="Logo Empresa" width="53px"><b>OSA</b>ERP</a> 
        </div-->
        <!-- /.login-logo -->
        <div class="login-box-body mt-0">
            <center>
                <img src="<?=$base_url;?>resources/assets/img/logo_newcar.jpg" alt="Logo Empresa" style="width: 100%; padding-bottom: 1.5em;">
            </center>
            
            <p class="login-box-msg">Identifiquese para ingresar</p>
            <!--p class="login-box-msg">&nbsp;</p-->

            <form method="POST" id="frmLogin" action="<?=$base_url.'index.php/index/ingresar_sistema';?>">
                <div class="form-group has-feedback">
                    <!--input type="text" class="form-control" name="txtUsuario" id="txtUsuario" placeholder="Ingrese su usuario" 
                           required pattern="[a-zA-Z0-9._\-]{3,15}"-->
                    <input type="text" class="form-control" name="txtUsuario" id="txtUsuario" placeholder="Ingrese su usuario">                    
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="txtClave" id="txtClave" placeholder="Contraseña" required pattern="[a-zA-Z0-9._@#$\-]{3,15}">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <button type="submit" class="btn btn-primary btn-block btn-flat" id="ingresar" style="background: #153792">Ingresar</button>
                </div>
            </form>

            <!--center> <img src="<?=$base_url;?>resources/assets/img/osa-fact.jpg" alt="Logo osa-fact" width="100px"><br> &copy;OSA-Fact | Facturador Electrónico Integrado</center-->
            <?php
                if ( isset($msg) && $msg != NULL){ ?>
                    <section style="color: red">
                        <?=$msg;?>
                    </section> <?php
                } 

                $yr = "&copy;".date('Y');
            ?>
        </div>
        <!-- /.login-box-body -->
    </div>
    
    <div class="login-copy" >
        <a href="http://www.ccapasistemas.com"><?php echo $yr; ?>  Todos los derechos reservados | www.ccapasistemas.com</a>
    </div>
    <!-- /.login-box -->

    <!-- Bootstrap 3.3.6 -->
    <script src="<?=$base_url;?>resources/assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>