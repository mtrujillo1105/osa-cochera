<?php
class Emprcontacto_model extends CI_Model
{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    public function seleccionar($empresa)
    {
        $arreglo = array(''=>':: Seleccione ::');
        $lista = $this->listar($empresa);
        if(count($lista)>0){
            foreach($lista as $indice=>$valor)  //1: Empresa de transporte
            {   $indice1   = $valor->PERSP_Codigo.'-'.$valor->AREAP_Codigo ;
                $valor1    = $valor->PERSC_Nombre." ".$valor->PERSC_ApellidoPaterno." ".$valor->PERSC_ApellidoMaterno.($valor->AREAP_Codigo!='0' && $valor->AREAP_Codigo!='' ? " - AREA: ".$valor->AREAC_Descripcion : '');                
                $arreglo[$indice1] = $valor1;
            }
        }
        return $arreglo;
    }
    public function listar($empresa){
        $where = array('ECONC_FlagEstado'=>'1','cji_emprcontacto.EMPRP_Codigo'=>$empresa);
        
        $query = $this->db->order_by('cji_emprcontacto.ECONP_Contacto')
                            ->join('cji_persona', 'cji_persona.PERSP_Codigo = cji_emprcontacto.ECONC_Persona', 'left')
                            ->join('cji_directivo', 'cji_directivo.EMPRP_Codigo = cji_emprcontacto.EMPRP_Codigo and cji_directivo.PERSP_Codigo=cji_emprcontacto.ECONC_Persona', 'left')
                            ->join('cji_cargo', 'cji_cargo.CARGP_Codigo = cji_directivo.CARGP_Codigo', 'left')
                            ->join('cji_emprarea', 'cji_emprarea.EMPRP_Codigo = cji_emprcontacto.EMPRP_Codigo and cji_emprarea.DIREP_Codigo=cji_directivo.DIREP_Codigo', 'left')
                            ->join('cji_area', 'cji_area.AREAP_Codigo = cji_emprarea.AREAP_Codigo', 'left')
                            ->where($where)
                            ->group_by('cji_emprcontacto.ECONP_Contacto')
                            ->from('cji_emprcontacto')
                            ->select('cji_emprcontacto.*, cji_persona.PERSP_Codigo, cji_persona.PERSC_Nombre , cji_persona.PERSC_ApellidoPaterno , 
                                   cji_persona.PERSC_ApellidoMaterno, cji_area.AREAP_Codigo  AREAP_Codigo,
                                   cji_area.AREAC_Descripcion AREAC_Descripcion,cji_cargo.CARGC_Descripcion  CARGC_Descripcion')
                            ->get();

        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function buscar_xpersona($persona){
            $where = array('ECONC_Persona'=>$persona, 'ECONC_FlagEstado'=>'1');
            $query = $this->db->where($where)->get('cji_emprcontacto');
            if($query->num_rows() > 0){
                    foreach($query->result() as $fila){
                            $data[] = $fila;
                    }
                    return $data;
            }
    }
	 public function listar_contactosCliente($contacto){
        $where = array('ECONC_FlagEstado'=>'1','cji_emprcontacto.ECONP_Contacto'=>$contacto);
        
        $query = $this->db->order_by('cji_emprcontacto.ECONP_Contacto')
                            ->join('cji_persona', 'cji_persona.PERSP_Codigo = cji_emprcontacto.ECONC_Persona', 'left')
                            ->join('cji_directivo', 'cji_directivo.EMPRP_Codigo = cji_emprcontacto.EMPRP_Codigo and cji_directivo.PERSP_Codigo=cji_emprcontacto.ECONC_Persona', 'left')
                            ->join('cji_cargo', 'cji_cargo.CARGP_Codigo = cji_directivo.CARGP_Codigo', 'left')
                            ->join('cji_emprarea', 'cji_emprarea.EMPRP_Codigo = cji_emprcontacto.EMPRP_Codigo and cji_emprarea.DIREP_Codigo=cji_directivo.DIREP_Codigo', 'left')
                            ->join('cji_area', 'cji_area.AREAP_Codigo = cji_emprarea.AREAP_Codigo', 'left')
                            ->where($where)
                            ->group_by('cji_emprcontacto.ECONP_Contacto')
                            ->from('cji_emprcontacto')
                            ->select('cji_emprcontacto.*, cji_persona.PERSC_Nombre , cji_persona.PERSC_ApellidoPaterno , 
                                   cji_persona.PERSC_ApellidoMaterno, cji_area.AREAP_Codigo  AREAP_Codigo,
                                   cji_area.AREAC_Descripcion AREAC_Descripcion,cji_cargo.CARGC_Descripcion  CARGC_Descripcion')
                            ->get();

        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function getcustom_contactosCliente($contacto){
    	$where = array('ECONC_FlagEstado'=>'1','cji_emprcontacto.ECONP_Contacto'=>$contacto);
    
    	$query = $this->db->order_by('cji_emprcontacto.ECONP_Contacto')
    	->from('cji_emprcontacto')
    	->select('cji_emprcontacto.*, cji_persona.PERSC_Nombre , cji_persona.PERSC_ApellidoPaterno ,
                                   cji_persona.PERSC_ApellidoMaterno, cji_area.AREAP_Codigo  AREAP_Codigo,
                                   cji_area.AREAC_Descripcion AREAC_Descripcion,cji_cargo.CARGC_Descripcion  CARGC_Descripcion')
                                       ->get();
    
                                       if($query->num_rows() > 0){
                                       	foreach($query->result() as $fila){
                                       		$data[] = $fila;
                                       	}
                                       	return $data;
                                       }
    }


}
?>