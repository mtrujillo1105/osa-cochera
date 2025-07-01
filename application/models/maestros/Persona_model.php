<?php

class Persona_model extends CI_Model {

    private $compania;
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('date');
        $this->compania = $this->session->userdata('compania');
    }

    #######################
    ##### FUNCTIONS NEWS
    #######################

        public function insertar_persona($filter){
            $this->db->insert("cji_persona", (array) $filter);
            return $this->db->insert_id();
        }

        public function actualizar_persona($persona, $filter){
            $this->db->where('PERSP_Codigo',$persona);
            return $this->db->update('cji_persona', $filter);
        }

    #######################
    ##### FUNCTIONS OLDS
    #######################

    public function listar_personas($number_items = '', $offset = '') {
        if ($number_items == "" && $offset == "") {
            $limit = "";
        } else {
            $limit = "limit $offset,$number_items";
        }

        $sql = "SELECT * FROM  cji_persona WHERE PERSC_FlagEstado = '1'
					ORDER BY PERSC_ApellidoPaterno " . $limit . "";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_datosPersona($persona) {
        
        $query = $this->db->where('PERSP_Codigo', $persona)->get('cji_persona');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_datosPersona2($numdoc, $esRuc = true) {
        $this->db->where('PERSC_Ruc', $numdoc);
        $this->db->or_where('PERSC_NumeroDocIdentidad', $numdoc);
        $query = $this->db->get('cji_persona');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar_datosPersona($ubigeo_nacimiento, $ubigeo_domicilio, $estado_civil, $nacionalidad, $nombres, $paterno, $materno, $ruc, $tipo_documento, $numero_documento = '', $direccion = '', $telefono = '', $movil = '', $email = '', $domicilio = '', $sexo = '', $fax = '', $web = '', $fechanac='') {
        $data = array(
            "UBIGP_LugarNacimiento" => $ubigeo_nacimiento,
            "UBIGP_Domicilio" => $ubigeo_domicilio,
            "ESTCP_EstadoCivil" => $estado_civil,
            "NACP_Nacionalidad" => $nacionalidad,
            "PERSC_Nombre" => strtoupper($nombres),
            "PERSC_ApellidoPaterno" => strtoupper($paterno),
            "PERSC_ApellidoMaterno" => strtoupper($materno),
            "PERSC_Ruc" => $ruc,
            "PERSC_TipoDocIdentidad" => $tipo_documento,
            "PERSC_NumeroDocIdentidad" => $numero_documento,
            "PERSC_Direccion" => strtoupper($direccion),
            "PERSC_Telefono" => $telefono,
            "PERSC_Movil" => $movil,
            "PERSC_Email" => strtolower($email),
            "PERSC_Domicilio" => strtoupper($domicilio),
            "PERSC_Sexo" => $sexo,
            "PERSC_Fax" => $fax,
            "PERSC_Web" => $web,
//            "PERSC_CtaCteSoles" => $ctactesoles,
//            "PERSC_CtaCteDolares" => $ctactedolares,
            "PERSC_FechaNacz" => $fechanac
        );
        $this->db->insert("cji_persona", $data);
        return $this->db->insert_id();
    }

    public function modificar_datosPersona($persona, $ubigeo_nacimiento, $ubigeo_domicilio, $estado_civil, $nacionalidad, $nombres, $paterno, $materno, $ruc, $tipo_documento, $numero_documento, $direccion, $telefono, $movil, $email, $domicilio, $sexo, $fax, $web,$fechanac='') {
        $data = array(
            "UBIGP_LugarNacimiento" => $ubigeo_nacimiento,
            "UBIGP_Domicilio" => $ubigeo_domicilio,
            "ESTCP_EstadoCivil" => $estado_civil,
            "NACP_Nacionalidad" => $nacionalidad,
            "PERSC_Nombre" => strtoupper($nombres),
            "PERSC_ApellidoPaterno" => strtoupper($paterno),
            "PERSC_ApellidoMaterno" => strtoupper($materno),
            "PERSC_Ruc" => $ruc,
            "PERSC_TipoDocIdentidad" => $tipo_documento,
            "PERSC_NumeroDocIdentidad" => $numero_documento,
            "PERSC_Direccion" => strtoupper($direccion),
            "PERSC_Telefono" => $telefono,
            "PERSC_Movil" => $movil,
            "PERSC_Email" => strtolower($email),
            "PERSC_Domicilio" => $domicilio,
            "PERSC_Sexo" => $sexo,
            "PERSC_Fax" => $fax,
            "PERSC_Web" => strtolower($web),
            "PERSC_FechaNac"=>$fechanac
        );
        $this->db->where("PERSP_Codigo", $persona);
        $this->db->update("cji_persona", $data);
    }

    public function modificar_datosPersona_nombres($persona, $nombres, $paterno, $materno) {
        $data = array(
            "PERSC_Nombre" => strtoupper($nombres),
            "PERSC_ApellidoPaterno" => strtoupper($paterno),
            "PERSC_ApellidoMaterno" => strtoupper($materno)
        );
        $this->db->where("PERSP_Codigo", $persona);
        $this->db->update("cji_persona", $data);
    }

    public function eliminar_persona($persona) {
        $data = array("PERSC_FlagEstado" => '0');
        $where = array("PERSP_Codigo" => $persona);
        $this->db->where($where);
        $this->db->update('cji_persona', $data);
    }

    public function buscar_personas($filter, $number_items = '', $offset = '') {
        if (isset($filter->PERSC_NumeroDocIdentidad) && $filter->PERSC_NumeroDocIdentidad != "")
            $this->db->where('PERSC_NumeroDocIdentidad', $filter->PERSC_NumeroDocIdentidad);
        if (isset($filter->nombre) && $filter->nombre != "") {
            $this->db->like('PERSC_Nombre', $filter->nombre);
            $this->db->or_like('PERSC_ApellidoPaterno', $filter->nombre);
            $this->db->or_like('PERSC_ApellidoMaterno', $filter->nombre);
        }
        if (isset($filter->PERSC_Telefono) && $filter->PERSC_Telefono != "")
            $this->db->like('PERSC_Telefono', $filter->PERSC_Telefono)->or_like('PERSC_Movil', $filter->PERSC_Telefono);

        $query = $this->db->order_by('PERSC_Nombre')
                ->where('PERSC_FlagEstado', '1')
                ->get('cji_persona', $number_items = '', $offset = '');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    /* varios */

    public function valida_ruc($ruc, $id = '') {

        if ($id != '')
            $query = $this->db->where('PERSC_Ruc', $ruc)->not_like('PERSP_Codigo', $id)->get('cji_persona');
        else
            $query = $this->db->where('PERSC_Ruc', $ruc)->get('cji_persona');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function busca_xnumeroDoc($tipo_docummento, $numero_documento) {
        $query = $this->db->where('PERSC_NumeroDocIdentidad', $numero_documento)->where('PERSC_TipoDocIdentidad', $tipo_docummento)->get('cji_persona');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function verificaPersonaDetalle($ordAdj,$ordRuc){

    	$sql="SELECT * FROM cji_persona WHERE PERSC_NumeroDocIdentidad = $ordAdj
    	or PERSC_Ruc = $ordRuc";
    	$query = $this->db->query($sql);
    	
//     	$query = $this->db->where('PERSC_NumeroDocIdentidad', $ordAdj)->get('cji_persona');
    	if ($query->num_rows() > 0) {
    		foreach ($query->result() as $fila) {
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }

  
}

?>