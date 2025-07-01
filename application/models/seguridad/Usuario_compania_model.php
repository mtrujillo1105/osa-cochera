<?php
class Usuario_compania_model extends CI_Model{

	public $somevar;

	public function __construct(){
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
		$this->usuario  = $this->session->userdata('usuario');
		$this->load->model('empresa/empresa_model');
		$this->load->model('maestros/emprestablecimiento_model');
	}

  ## FUNCTIONS NEWS
  ########################

        public function getUsuariosCompania(){
            $sql = "
                SELECT u.USUA_Codigo,
                       p.*
                FROM cji_usuario_compania uc
                INNER JOIN cji_usuario u on (u.USUA_Codigo = uc.USUA_Codigo)
                INNER JOIN cji_persona p on (p.PERSP_Codigo = u.PERSP_Codigo)
                WHERE uc.COMPP_Codigo = $this->compania
            ";
            $query = $this->db->query($sql);

            if ($query->num_rows() > 0)
                return $query->result();
            else
                return NULL;
        }
        
	public function registrar_acceso_usuario($filter){
		$this->db->insert("cji_usuario_compania", (array) $filter);
		return $this->db->insert_id();
	}

	public function clean_acceso_usuario($id){
		$sql = "DELETE FROM cji_usuario_compania WHERE USUA_Codigo = $id";
		$this->db->query($sql);
	}

  ## FUNCTIONS OLDS
  ########################

	public function listar($usuario, $empresa=''){
		$compania = $this->compania;
		$where = array("cji_usuario.USUA_Codigo"=>$usuario,"USUCOMC_Default"=>"1");

		if($empresa!='')
			$where["cji_compania.EMPRP_Codigo"]=$empresa;

		$query = $this->db->
		join('cji_usuario','cji_usuario.USUA_Codigo=cji_usuario_compania.USUA_Codigo')->
		join('cji_compania','cji_compania.COMPP_Codigo=cji_usuario_compania.COMPP_Codigo')->
		where($where)->
		get('cji_usuario_compania');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_empresas(){
		$user = $this->session->userdata('user');

		$query = $this->db->select('cji_compania.EMPRP_Codigo')
		->where('cji_compania.COMPC_FlagEstado','1')
		->where('cji_usuario_compania.USUA_Codigo', $user)
		->join('cji_compania', 'cji_compania.COMPP_Codigo = cji_usuario_compania.COMPP_Codigo', 'left')
		->group_by('cji_compania.EMPRP_Codigo')
		->get('cji_usuario_compania');


		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
		else
			return array();
	}

	public function listar_establecimiento($user = '',$compania = '',$default = false){
		if($user == '')
			$where['cji_usuario_compania.USUA_Codigo'] = $this->session->userdata('user');
		else{
			$where['cji_usuario_compania.USUA_Codigo'] = $user;

			if ($compania != '')
				$where['cji_compania.EMPRP_Codigo'] = $compania;
		}
		if($default==true)
			$where['cji_usuario_compania.USUCOMC_Default'] = '1';

		$query = $this->db->where($where)
		->join('cji_compania', 'cji_compania.COMPP_Codigo = cji_usuario_compania.COMPP_Codigo', 'left')
		->get('cji_usuario_compania');

		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
		else
			return array();
	}

	public function listar_establecimiento_inicio($compania='',$default=false){    
		$where['cji_usuario_compania.USUA_Codigo'] = $this->session->userdata('user');
		$where['cji_compania.EMPRP_Codigo'] = $compania;

		if($default==true)
			$where['cji_usuario_compania.USUCOMC_Default'] = '1';

		$query = $this->db->where($where)
		->join('cji_compania', 'cji_compania.COMPP_Codigo = cji_usuario_compania.COMPP_Codigo', 'left')
                ->order_by('cji_compania.COMPP_Codigo','desc')        
		->get('cji_usuario_compania');

		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
		else
			return array();
	}

	public function listar_compania(){
            $array_empresas = $this->listar_empresas();
            $arreglo = array();
            foreach($array_empresas as $indice=>$valor){
                    $empresa           = $valor->EMPRP_Codigo;
                    $datos_empresa     = $this->empresa_model->obtener_datosEmpresa($empresa);

                    $razon_social      = $datos_empresa[0]->EMPRC_RazonSocial;

                    $arreglo[]=array('tipo'=>'1', 'nombre'=>$razon_social, 'compania'=>'');

                    $array_establecimiento = $this->listar_establecimiento_inicio($empresa);
                    foreach($array_establecimiento as $indice=>$valor){
                        $compania               = $valor->COMPP_Codigo;
                        $datos_establecimiento  = $this->emprestablecimiento_model->obtener($valor->EESTABP_Codigo);
                        $nombre_establecimiento = $datos_establecimiento[0]->EESTABC_Descripcion;
                        $arreglo[]=array('tipo'=>'2', 'nombre'=>$nombre_establecimiento, 'compania'=>$compania);
                    }
            }
            return $arreglo;
	}

	public function insertar(stdClass $filter = null){
		$this->db->insert("cji_usuario_compania",(array)$filter);
	}
}

?>