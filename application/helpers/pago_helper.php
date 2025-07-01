<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function cambiar_moneda($monto, $tdc, $moneda, $moneda_destino)
{
    $resultado = $monto;

    if ($moneda == $moneda_destino) {
        $resultado = $monto;
    }
    else if ($moneda == '1') {
        $resultado = round($monto / $tdc, 2);
    }
    else {
        $resultado = round($monto * $tdc, 2);
    }
    return $resultado;
}

function obtener_estado_formato($total, $avance)
{
    $result = '';
    if ($total == $avance)
        $result = "<div style='width:70px; height:17px; background-color: #00D269; text-align:center; cursor:help;' title='Cancelado'>Cancelado</div>";
    elseif ($avance == 0)
        $result = "<div style='width:70px; height:17px; background-color: #FF6464; text-align:center; cursor:help;' title='Pendiente'>Pendiente</div>";
    else
        $result = "<div style='width:70px; height:17px; background-color: #FFB648; text-align:center; cursor:help;' title='Pendiente con Avance'>Pendiente (AV)</div>";

    return $result;
}

function obtener_estado_de_cuenta($saldo, $avance, $fecha, $fechaVencimiento, $viewLeyanda = true){
    $estado = fecha_limite_cuenta($fecha, $fechaVencimiento);

    switch ($estado) {
        case '1':
            $color = "cyan";
            break;
        case '2':
            $color = "yellow";
            break;
        case '3':
            $color = "gold";
            break;
        case '4':
            $color = "pink";
            break;
        case '5':
            $color = "red";
            break;
        
        default:
            $color = "green";
            break;
    }

    $result = '';
    $leyenda = ( $viewLeyanda == true ) ? "<span class='leyenda font-7'>LEYENDA:
                        <p style='display:block; padding:1em; background-color:cyan;'>Fase inicial de la cuenta.</p>
                        <p style='display:block; padding:1em; background-color:yellow;'>Cercano a la mitad de la fecha pago/cobro.</p>
                        <p style='display:block; padding:1em; background-color:gold;'>Pasada la mitad de la fecha pago/cobro.</p>
                        <p style='display:block; padding:1em; background-color:pink;'>Cercano al vencimiento de la cuenta.</p>
                        <p style='display:block; padding:1em; background-color:red;'>Cuenta vencida.</p>
                </span>" : "";

    if ($saldo <= 0)
        $result = "<button type='button' class='btn btn-default font-8 color-black estadoC' title='Cancelado'>Cancelado $leyenda</button>";
    else
        $result = "<button type='button' class='btn font-8 color-black estadoC' style='background-color:$color' title='Pendiente con Avance'>Pend. (AV) $leyenda</button>";
        
    if ($avance == 0)
        $result = "<button type='button' class='btn font-8 color-black estadoC' style='background-color:$color' title='Pendiente'>Pendiente $leyenda</button>";

    return $result;
}

function fecha_limite_cuenta($fechaEmision, $fechaVencimiento){
    ############################################################################################################################
    ## ESTA DIVIDIDO EN 3 PARTES, 1/4, 1/2 y 3 1/4 PARA UN TOTAL DE 5 FASES 1 INICIAL 2 MEDIA, 3 MEDIA 2, 4 FINAL, 5 VENCIDA
    ## ESTO RESULTA 4 EVALUACIONES DE FECHAS
    ## DESDE INICIO HASTA 1/4, UN COLOR, LA CUENTA ES RECIENTE
    ## MAYOR A 1/4 y MENOR A LA MITAD 1/2, LA CUENTA NO ES RECIENTE PERO ESTA A MITAD O MENOS DE CADUCIDAD 
    ## MAYOR A 1/2 y MENOR A 3 1/4, LA CUENTA TIENE MAS DE LA MITAD DEL TIEMPO DE REALIZADA NO ES RECIENTE PERO ESTA A MITAD 
    ## MAYOR A 3 1/4 y MENOR A 1, LA CUENTA ESTA EN LA ULTIMA FRACCION DE TIEMPO DISPONIBLE PARA PAGO
    ## MAYOR A 1, LA CUENTA ESTA VENCIDA.
    ############################################################################################################################

    $hoy = date('Y-m-d');

    $date1 = new DateTime($fechaEmision);
    $date2 = new DateTime($fechaVencimiento);
    $date3 = new DateTime($hoy);

    $diff1 = $date1->diff($date2);
    $diff2 = $date1->diff($date3);

    $days = $diff1->days;

    if ( $diff2->days <= $days / 4)
        return 1;
    
    if ( $diff2->days > $days / 4 && $diff2->days <= $days / 2)
        return 2;

    if ( $diff2->days > $days / 2 && $diff2->days <= ($days / 4) * 3 )
        return 3;

    if ( $diff2->days > ($days / 4) * 3 && $diff2->days <= $days )
        return 4;

    if ( $diff2->days > $days )
        return 5;
}

function obtener_forma_pago($forma_pago)
{
    $result = '';
    switch ($forma_pago) {
        case '1' :
            $result = 'EFECTIVO';
            break;
        case '2' :
            $result = 'DEPOSITO';
            break;
        case '3' :
            $result = 'CHEQUE';
            break;
        case '4' :
            $result = 'CANJE POR FACTURA';
            break;
        case '5' :
            $result = 'NOTA DE CREDITO';
            break;
        case '6' :
            $result = 'DESCUENTO';
            break;
    }
    return $result;
}

?>
