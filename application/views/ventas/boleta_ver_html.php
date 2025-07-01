<!DOCTYPE html>
<html>
<head>
<title>Impresion - Boleta</title>
<style type="text/css" >

#contenedor{
    letter-spacing:0.6px;
    font-family:fantasy;
}
/*
#serie_factura{
	margin-left: 928px;
    margin-top: 112px;
	font-size:30px;
	color: white;
}*/
#seniores{
    top: 175px;
    left: 30px;
    width: 500px;
    position: absolute;
    font-size: 20px;
}
#ruc{
    top: 200px;
    left: 30px;
    width: 500px;
    position: absolute;
    font-size: 14px;    
}
#direccion {
    top: 240px;
    left: 30px;
    width: 700px;
    position: absolute;
    font-size: 14px;
}

#forma_pago{
    top: 175px;
    left: 650px;
    width: 100px;
    position: absolute;
    font-size: 14px;
}
#vendedor{
    top: 200px;
    left: 650px;
    width: 100px;
    position: absolute;
    font-size: 14px;
}

#dia_cabecera {
    top: 175px;
    left: 850px;
    position: absolute;
    font-size: 14px;
}
#numero_guia_remision {
    top: 240px;
    left: 850px;
    position: absolute;
    font-size: 14px;
}

#id_producto {
    float: left;
    top: 280px;
    left: -40px;
    width: 1000px;
    height: 700px;
    position: absolute;
    font-family:fantasy;
    font-size: 14px;
}
#detallef {
    float: left;
    width: 990px;
}
.item_codigo{
    float: left;
    font-family: "Times New Roman";
    margin-top: 2px;
    width: 50px;
    font-size: 12px;
    text-align: right;
}
.item_descripcion{
    float: left;
    font-family: "Times New Roman";
    margin-top: 2px;
    width: 730px;
    font-size: 12px;
    padding-left: 18px;
}
.item_cantidad{
    float: left;
    margin-top: 2px;
    width: 30px;
    font-size: 12px;
    text-align: right;
}
.item_precio_unitario{    
    float: left;
    margin-top: 2px;
    width: 75px;
    font-size: 12px;
    text-align: right;
}
.item_importe{
    float: left;
    margin-top: 2px;
    width: 75px;
    font-size: 12px;
    text-align: right;
}

#factor_conversion{
    top: 1000px;
    left: 50px;
    width: 250px;
    position: absolute;
    font-size: 14px;
}
#valido_fecha{
    top: 1000px;
    left: 300px;
    width: 400px;
    position: absolute;
    font-size: 14px;	
}
#total_soles{
    top: 1000px;
    left: 530px;
    width: 200px;
    position: absolute;
    font-size: 14px;	
}
#total_texto{
    top: 1020px;
    left: 50px;
    width: 600px;
    position: absolute;
    font-size: 14px;
}

#total{
    top: 1020px;
    left: 850px;
    width: 200px;
    position: absolute;
    font-size: 16px;
    font-weight: bold;
}

</style>
</head>
<body>
<div id="contenedor">
    
    <div id="seniores"><?php echo $seniores; ?></div>
    <div id="ruc"><?php if($ruc){echo $ruc;}else{echo '20131143584';} ?></div>
    <div id="direccion"><?php if($direccion){echo $direccion;}else{echo 'direccion';} ?></div>     
    
    <!--<div id="forma_pago"><?php //echo $cond_pago; ?></div>-->
    <div id="vendedor"><?php echo $vendedor; ?></div>
    
    <div id="dia_cabecera"><?php echo $fecha_pie;?></div>
    <div id="numero_guia_remision"><?php if($numero_guia_remision){echo $numero_guia_remision;}else{echo '00000000000';}?></div>
    
    <div id="id_producto">
        <?php foreach($lista_items as $item): ?>
        <div id="detallef">
            <div class="item_codigo"><?php if($item['item_codigo']==""){echo "AGMA024";} echo$item['item_codigo']; ?></div>
            <div class="item_descripcion"><?php echo $item['item_descripcion']; ?></div>
            <div class="item_cantidad"><?php echo $item['item_cantidad']; ?></div>
            <div class="item_precio_unitario"><?php echo $item['item_precio_unitario']; ?></div>
            <div class="item_importe"><?php echo $item['item_importe']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div id="factor_conversion">Tipo de Cambio: <?php echo number_format($factor_de_conversion,3)?></div>
    <div id="valido_fecha">Valido solo <?php echo $valido_fecha?></div>
    <div id="total_soles">// S/. <?php $tot = str_replace(',','',substr(trim($total),3)); echo number_format($factor_de_conversion*$tot,2);?></div>    
    <div id="total_texto"><?php echo $total_texto; ?></div>    
    
    <div id="total"><?php echo $subtotal; //no incluye igv?></div>
    
    <?php
    /*?>
		<div id="serie_factura"><?php echo $serie_numero?></div>
			<div id="seniores"><?php echo $seniores; ?></div>
			<div id="dia_cabecera"><?php echo $fecha_pie;?></div>
			<div id="ruc"><?php if($ruc){echo $ruc;}else{echo '20131143584';} ?></div>
			<div id="vendedor"><?php echo $vendedor; ?></div>
			<div id="direccion"><?php if($direccion){echo $direccion;}else{echo 'direccion';} ?></div>
			<div id="numero_guia_remision"><?php if($numero_guia_remision){echo $numero_guia_remision;}else{echo '00000000000';}?></div>
			<div id="espacio"></div>
		  <?php foreach($lista_items as $item){ ?>
				<!--<div class="item_codigo"><?php if($item['item_codigo']==""){echo "AGMA024";} echo$item['item_codigo']; ?></div>-->
				<div class="item_descripcion"><?php echo $item['item_descripcion']; ?></div>
				<!--<div class="item_unidad"><?php echo "- ".$item['item_unidad']; ?></div>-->
				<!--<div class="item_spacio">Dscto: %<?php echo $descuento100?></div>-->
				<!--<div class="item_numero"><?php echo $item['item_numero']; ?></div>-->
				<div class="item_cantidad"><?php echo $item['item_cantidad']; ?></div>
				<div class="item_precio_unitario"><?php echo $item['item_precio_unitario']; ?></div>
				<div class="item_importe"><?php echo $item['item_importe']; ?></div>
		 <?php } ?>
		<div id="total_texto"><?php echo $total_texto; ?></div>
		<div id="factor_conversion">Tipo de Cambio <?php echo $factor_de_conversion?></div>
		<div id="valido_fecha">Valido solo<?php echo $valido_fecha?></div>
		<div id="total"><?php echo $subtotal; //no incluye igv?></div>
		<!--<div id="fecha_pie_dia"><?php //echo $dia?></div>
		<div id="fecha_pie_mes"><?php //echo $mes_numero?></div>
		<div id="fecha_pie_anio"><?php //echo $anio?></div>-->
    <?php
    */?> 
</div>
</body>
</html>