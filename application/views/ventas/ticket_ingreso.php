<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket de Ingreso</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="<?php echo base_url();?>public/js/jquery-barcode.js"></script>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col">
        <h1>TICKET DE INGRESO</h1>
        <p>PLACA: <?php echo $ticket[0]->PARQC_Placa;?></p>
        <h3><?php echo $ticket[0]->TARIFC_Descripcion;?></h3>
        <p><hr></p>
        <h2>Ingreso : <?php echo $ticket[0]->PARQC_FechaIngreso;?></h2>
        <h2>S/ <?php echo $ticket[0]->TARIFC_Precio;?> hora o fraccion</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 align-self-center" id="bcTarget"></div>
    </div>
  </div>
</body>
</html>
    <script>
    $(document).ready(function () {
        var parqueo  = <?php echo $ticket[0]->PARQP_Codigo;?>;
        var btype    = "code128";
        var renderer = "css";

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
        $("#bcTarget").barcode(String(parqueo),btype,settings);  
    });
    </script>    