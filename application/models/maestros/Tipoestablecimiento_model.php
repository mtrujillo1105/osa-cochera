<?php

class Tipoestablecimiento_model extends CI_Model{

	private $empresa;
	private $compania;
	private $usuario;

	public function __construct(){
		parent::__construct();
        $this->load->helper('date');
        $this->empresa = $this->session->userdata('empresa');
        $this->compania = $this->session->userdata('compania');
        $this->usuario = $this->session->userdata('usuario');
	}

	##############################
	##### FUNCTIONS NEWS
	##############################

		public function getTipoEstablecimientos($filter = NULL){
			$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
            $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

            $where = '';
            if (isset($filter->descripcion) && $filter->descripcion != '')
                $where .= " AND te.TESTC_Descripcion LIKE '%$filter->descripcion%'";

            $sql = "SELECT te.*
                            FROM cji_tipoestablecimiento te
                            WHERE te.TESTC_FlagEstado LIKE '1'
                            $where
                            $order $limit
                    ";

            $query = $this->db->query($sql);
            if ($query->num_rows() > 0)
                return $query->result();
            else
                return NULL;
		}

	##############################
	##### FUNCTIONS OLDS
	##############################

	public function listar_tiposEstablecimiento($number_items='',$offset=''){
		$query = $this->db->order_by('TESTC_Descripcion')->where('TESTC_FlagEstado','1')->get('cji_tipoestablecimiento',$number_items,$offset);
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_tipoEstablecimiento($tipo)
	{
		$query = $this->db->where('TESTP_Codigo',$tipo)->get('cji_tipoestablecimiento');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function insertar_establecimiento($descripcion)
	{
        $compania = $this->compania;
		$data = array(
                                   "TESTC_Descripcion" => strtoupper($descripcion),
                                   "COMPP_Codigo"       => $compania
                                   );
		$this->db->insert("cji_tipoestablecimiento",$data);
	}

	public function modificar_establecimiento($establecimiento,$descripcion)
	{
		$data = array("TESTC_Descripcion"=>strtoupper($descripcion));
		$this->db->where("TESTP_Codigo",$establecimiento);
		$this->db->update("cji_tipoestablecimiento",$data);
	}

	public function eliminar_establecimiento($establecimiento)
	{
		$where  = array("TESTP_Codigo"=>$establecimiento);
		$this->db->delete("cji_tipoestablecimiento",$where);
	}

	public function buscar_establecimientos($filter,$number_items='',$offset='')
	{
            $this->db->where('COMPP_Codigo',$this->compania);
            $this->db->where_not_in('TESTP_Codigo','0');
            if(isset($filter->nombre_establecimiento) && $filter->nombre_establecimiento!="")
                $this->db->like('TESTC_Descripcion',$filter->nombre_establecimiento);
            $query = $this->db->get('cji_tipoestablecimiento',$number_items='',$offset='');
            if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
            return $data;
            }
	}
}
?>