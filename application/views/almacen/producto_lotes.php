<table id="tblOcompra" width="98%" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="1">
    <tr align="center" bgcolor="#BBBB20" height="10px;">
        <td width="7%">Fecha</td>
        <td width="20%">Almacen</td>
        <td width="10%">Num Doc</td>
        <td>Proveedor</td>
        <td width="5%">Cantidad</td>
        <td width="6%">Moneda</td>
        <td width="10%">Precio Compra</td>
    </tr>
    <?php
    $cantidad = count($lista_lotes);
    if($cantidad>0){
        foreach($lista_lotes as $indice=>$value){
        ?>
        <tr bgcolor="#ffffff">
            <td align="center"><?php echo $value->fecha;?></td>
            <td align="left"><?php echo $value->almacen;?></td>
            <td align="cneter"><?php echo $value->ruc;?></td>
            <td align="left"><?php echo $value->nombre;?></td>
            <td align="center"><?php echo $value->cantidad;?></td>
            <td align="center"><?php echo $value->moneda;?></td>
            <td align="right"><?php echo number_format($value->costo,2);?></td>
        </tr>
        <?php
        }
    }
    ?>
</table>
<?php
$display = $cantidad!='0'?"display:none;":"";
?>
<div id="msgRegistros2" style="width:98%;text-align:center;height:20px;border:1px solid #000;<?php echo $display;?>">NO EXISTEN REGISTROS</div>

