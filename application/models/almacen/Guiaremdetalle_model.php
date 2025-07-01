<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Guiaremdetalle_Model extends CI_Model{
	protected $_name = "cji_guiaremdetalle";
  ##  -> Begin
	private $compania;
  ##  -> End

  ##  -> Begin
	public function  __construct(){
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
	}
  ##  -> End
	
	public function listar($guiarem, $number_items='',$offset='')
	{
		$where = array("GUIAREMP_Codigo"=>$guiarem, "GUIAREMDETC_FlagEstado"=>1);
		$query = $this->db->order_by('GUIAREMDETP_Codigo')->where($where)->get('cji_guiaremdetalle',$number_items,$offset);
		if($query->num_rows() > 0){
			return $query->result();
		}
	}
	public function listar2($guiarem)
	{
		$where = array("GUIAREMP_Codigo"=>$guiarem, "GUIAREMDETC_FlagEstado"=>1);
		$query = $this->db->order_by('GUIAREMDETP_Codigo')->where($where)->get('cji_guiaremdetalle');
		if($query->num_rows() > 0){
			return $query->result();
		}
	}
	public function obtener($id)
	{
		$where = array("GUIAREMDETP_Codigo"=>$id);
		$query = $this->db->where($where)->get('cji_guiaremdetalle',1);
		if($query->num_rows() > 0){
			return $query->result();
		}
	}
	public function obtener2($guiarem_id){
		$sql = "SELECT grd.*, pr.PROD_CodigoInterno, pr.PROD_CodigoUsuario, pr.PROD_CodigoOriginal, pr.PROD_FlagBienServicio, pr.PROD_Nombre, um.UNDMED_Simbolo, m.MARCC_CodigoUsuario, m.MARCC_Descripcion, l.LOTC_Numero, l.LOTC_FechaVencimiento
		FROM cji_guiaremdetalle grd
		INNER JOIN cji_guiarem gr ON grd.GUIAREMP_Codigo = gr.GUIAREMP_Codigo
		INNER JOIN cji_producto pr ON pr.PROD_Codigo = grd.PRODCTOP_Codigo
		LEFT JOIN cji_marca m ON m.MARCP_Codigo = pr.MARCP_Codigo
		LEFT JOIN cji_unidadmedida um ON um.UNDMED_Codigo = grd.UNDMED_Codigo
		LEFT JOIN cji_lote l ON l.LOTP_Codigo = grd.LOTP_Codigo
		WHERE grd.GUIAREMP_Codigo = $guiarem_id AND grd.GUIAREMDETC_FlagEstado = 1
		";
		$query = $this->db->query($sql);

		if( $query->num_rows() > 0 )
			return $query->result();
		else
			return NULL;
	}
	
	public function insertar(stdClass $filter = null)
	{
		$this->db->insert("cji_guiaremdetalle",(array)$filter);
	}
	public function modificar($id,$filter)
	{
		$this->db->where("GUIAREMDETP_Codigo",$id);
		$this->db->update("cji_guiaremdetalle",(array)$filter);
	}
	public function eliminar($id)
	{
		$this->db->delete('cji_guiaremdetalle', array('GUIAREMDETP_Codigo' => $id));
	}
	public function eliminar2($id)
	{
		$this->db->delete('cji_guiaremdetalle', array('GUIAREMP_Codigo' => $id));
	}
}
?>