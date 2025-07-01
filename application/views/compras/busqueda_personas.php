<?php
$fila = "";
if(count($resultado_personas)>0){
    foreach($resultado_personas as $valor){
            $nombre = $valor->PERSC_Nombre." ".$valor->PERSC_ApellidoPaterno." ".$valor->PERSC_ApellidoMaterno;
            $codigo = $valor->PERSP_Codigo;
            $fila.="<tr onclick='obtener_persona(".$codigo.",".$n.");'>";
            $fila.="<td><a href='#'>".$nombre."</a></td>";
            $fila.="</tr>";
    }
}
?>

<table width="100%" class="fuente8">
	<?php echo $fila;?>
</table>
