<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('obtener_val_x_defecto'))
{
    function obtener_val_x_defecto($lista)
    {
        $dafault='';
        if(count($lista) == 2){
            foreach($lista as $indice=>$value)
                $dafault=$indice;
        }
        return $dafault;
    }

}