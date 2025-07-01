<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=$nombre_empresa;?></title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/dist/css/adminlte.min.css">
  <!-- jquery ui -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/jquery-ui/jquery-ui.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?=$base_url;?>resources/template/plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/css/others.css">
  <link rel="shortcut icon" href="<?=base_url(); ?>resources/assets/img/favicon.png">   
</head>
<body class="hold-transition sidebar-mini layout-fixed sidebar-closed sidebar-collapse" onload="verifica_caja_activa()">
<!--body class="hold-transition sidebar-mini layout-fixed"-->

<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo base_url();?>index.php/seguridad/inicio" class="nav-link">INICIO</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo base_url();?>index.php/ventas/comprobante/comprobante_nueva/V/B" class="nav-link">SERVICIOS</a>
      </li>
    </ul>

    <!-- SEARCH FORM -->
    <!--form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form-->

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
      	<form id="frmChangeSession">
	      	<select name="sessionCompany" id="sessionCompany" class="form-control" onchange="change_session();"> <?php
	          foreach ($lista_compania as $valor) { ?>
	            <option value="<?=$valor['compania'];?>" <?=($valor["tipo"] == 1) ? "class='font-weight-bold' disabled" : '';?> <?=($valor['compania'] == $compania) ? 'selected' : '';?>><?=$valor['nombre'];?> </option> <?php
	          } ?>
	        </select>
                <input type="hidden" name="caja_activa" id="caja_activa" value="<?php echo $caja_activa;?>"/>
	      </form>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container aca cambie-->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo base_url();?>index.php/seguridad/inicio" class="brand-link">
      <img src="<?=$base_url;?>resources/assets/img/icono_empresa.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><?=$nombre_empresa;?></span>
    </a>

    <!-- Sidebar -->
    <?=$menu_html;?>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!--div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"><?=$nombre_empresa;?></h1>
          </div><!-- /.col -->
          <!--div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div><!-- /.col -->
        <!--/div><!-- /.row -->
      <!--/div><!-- /.container-fluid -->
    <!--/div-->
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Main row -->
      	<?=$content_for_layout;?>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2009-<?=date("Y");?>
    	<a href="http://www.osa-erp.com" target="_blank">www.osa-erp.com</a> -
    	<a href="http://www.ccapasistemas.com" target="_blank">www.ccapasistemas.com</a>.
    </strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.0.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!--Modal Apertura de CAJA-->
<div id="modal-caja" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">APERTURAR CAJA: <?=date("d / m / Y ");?></h5>
                <!--button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                </button-->
            </div>
            <form id="frm_abrir_caja">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="apertura_caja_nombre">NOMBRE</label>
                            <select class="form-control" name="apertura_caja_nombre" id="apertura_caja_nombre">
                                <option value="">::Seleccione::</option>
                                <?php
                                if($cajas != NULL){
                                    foreach($cajas as $value){
                                        ?>
                                        <option value="<?php echo $value->CAJA_Codigo;?>"><?php echo $value->CAJA_Nombre;?></option>    
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>                
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="apertura_caja_responsable">RESPONSABLE</label>
                            <input type="text" id="apertura_caja_responsable" name="apertura_caja_responsable" class="form-control" readonly="readonly">
                            <input type="hidden" id="apertura_caja_responsable_id" name="apertura_caja_responsable_id">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="apertura_caja_clave">CONTRASEÑA</label>
                            <input type="password" id="apertura_caja_clave" name="apertura_caja_clave" class="form-control">
                        </div>
                    </div>                
                </div>
                <div class="modal-footer">
                    <!--button type="button" class="btn btn-secondary" data-dismiss="modal" id="salir_caja">Salir</button-->
                    <button type="button" class="btn btn-success tempde_addTipoCambio" id="btn_abrir_caja">Aperturar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Fin Modal Apertura de CAJA-->


<!-- jQuery -->
<script src="<?=$base_url;?>resources/template/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=$base_url;?>resources/template/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="<?=$base_url;?>public/js/jquery-barcode.js"></script>
<script>
	/* Resolve conflict in jQuery UI tooltip with Bootstrap tooltip */
  $.widget.bridge('uibutton', $.ui.button);
	/*  Datatables in spanish */
	var spanish = {lengthMenu: "_MENU_",search: "_INPUT_",searchPlaceholder: "Buscar",emptyTable: "No hay información",info: "Mostrando desde _START_ hasta _END_ de _TOTAL_ Registros",infoEmpty: "Mostrando desde 0 hasta 0 de 0 Registros",infoFiltered: "(Total: _MAX_ Registros)",infoPostFix: "",thousands: ",",lengthMenu: "Mostrar _MENU_ Registros",loadingRecords: "Cargando...",processing: "Procesando...",zeroRecords: "Sin resultados encontrados.",paginate: {"first": "Primero","last": "Ultimo","next": "Siguiente","previous": "Anterior"}};
	/* For all script */
	var base_url = '<?=$base_url;?>';
	var tipo_oper = '<?=$tipo_oper;?>';
        var id_compania = '<?=$compania;?>';
</script>
<!-- Bootstrap 4 -->
<script src="<?=$base_url;?>resources/template/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="<?=$base_url;?>resources/template/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=$base_url;?>resources/template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=$base_url;?>resources/template/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=$base_url;?>resources/template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?=$base_url;?>resources/template/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- ChartJS -->
<script src="<?=$base_url;?>resources/template/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?=$base_url;?>resources/template/plugins/sparklines/sparkline.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?=$base_url;?>resources/template/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?=$base_url;?>resources/template/plugins/moment/moment.min.js"></script>
<script src="<?=$base_url;?>resources/template/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?=$base_url;?>resources/template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?=$base_url;?>resources/template/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=$base_url;?>resources/template/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=$base_url;?>resources/template/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?=$base_url;?>resources/template/dist/js/demo.js"></script>

<link href="<?=$base_url;?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=$base_url;?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
<?php
	if (isset($scripts) && $scripts != NULL){
		foreach ($scripts as $val){ ?>
			<script type="text/javascript" src="<?=$base_url;?>public/js/<?=$val;?>?=<?=JS;?>"></script><?php
		}
	}
?>
<script type="text/javascript" src="<?=base_url();?>public/js/sunat.js?=<?=JS;?>"></script>
<script>
	$(document).ready(function(){
		obtener_demora();
		menu_activo('<?=$this->uri->segment(1,'');?>', '<?=str_replace('/', '_', uri_string());?>');
  });
  
  function verifica_caja_activa(){
    var caja_activa = document.getElementById("caja_activa").value;
    if(caja_activa == 0)
        //Se abre el modal caja y se BLOQUEA
        $('#modal-caja').modal({backdrop: 'static', keyboard: false});
}
</script>
</body>
</html>