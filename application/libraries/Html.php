<?php if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script');

class Html {

    function optionHTML($arrayCombo,$indSel='',$arrayDefault=array()){
		$_optionsHTML = "";
		if(count($arrayDefault)>0){
			$indDefault   = $arrayDefault[0];
			$valorDefault = $arrayDefault[1];
		}
		else{
			$indDefault   = '';
			$valorDefault = 'Seleccionar';	
		}
        $_optionsHTML = $valorDefault=='null' || $valorDefault==null?"":"<option value=\"$indDefault\">$valorDefault</option>";
        foreach ($arrayCombo as $indice => $valor){
            $selected = $indSel == $indice?"selected='selected'":"";
            $_optionsHTML.= "\t"."<option value='$indice' $selected>".mb_convert_encoding($valor, "HTML-ENTITIES", "UTF-8")."</option>"."\n";
        }
        return $_optionsHTML;
    }


    function array_ubigeo($arrayDpto,$arrayProv,$arrayDist){
        $result=array();
        $i=1;
        foreach($arrayDpto as $value){
            $result[$i]=($value=='' ? '00' : $value).($arrayProv[$i]=='' ? '00' : $arrayProv[$i]).($arrayDist[$i]=='' ? '00' : $arrayDist[$i]);
            $i++;
        }
        return $result;
    }

}

?>