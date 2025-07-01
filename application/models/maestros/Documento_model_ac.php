<?php
class documento_model_ac extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');

		$this->compania = $this->session->userdata('compania');
      $this->usuario = $this->session->userdata('user');
		//$this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
	}
	public function seleccionar($es_comprobante=NULL)
	{
		if($es_comprobante!=NULL)
			$lista = $this->listar($es_comprobante);
			else
				$lista = $this->listar();
				$arreglo = array(''=>':: Seleccione ::');
				foreach($lista as $indice=>$valor)
				{
					$indice1   = $valor->DOCUP_Codigo;
					$valor1    = $valor->DOCUC_Descripcion;
					$arreglo[$indice1] = $valor1;
				}
				return $arreglo;
	}

	 
	public function listar($companiaCodigo='') {
		
		$this->db->select('CCD.COMPCONFIP_Codigo,CCD.DOCUP_Codigo, D.DOCUC_Descripcion');
		$this->db->from('cji_companiaconfidocumento CCD');
		$this->db->join('cji_documento D','D.DOCUP_Codigo = CCD.DOCUP_Codigo','inner');
		$this->db->where('CCD.COMPCONFIP_Codigo',$companiaCodigo);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	
	public function obtener_configuracion_default($codigoDocumento='',$codigoCompania='') {


		$this->db->select('CCD.COMPCONFIDOCP_Codigo, CCD.DOCUP_Codigo, D.DOCUC_Descripcion, I.ITEM_Nombre, DI.DOCUITEM_Width, DI.DOCUITEM_Height, DI.DOCUITEM_PosicionX, DI.DOCUITEM_PosicionY, 
				DI.DOCUITEM_TamanioLetra, DI.DOCUITEM_TipoLetra, DI.DOCUITEM_Codigo,DI.DOCUITEM_Variable');

		$this->db->from('cji_companiaconfidocumento CCD');
		$this->db->join('cji_documento D','D.DOCUP_Codigo=CCD.DOCUP_Codigo','inner');
		$this->db->join('cji_documentoitem DI','DI.DOCUP_Codigo=D.DOCUP_Codigo','left');
		$this->db->join('cji_item I','I.ITEM_Codigo=DI.ITEM_Codigo','inner');
		$this->db->where('CCD.COMPCONFIP_Codigo',$codigoCompania);
		$this->db->where('CCD.DOCUP_Codigo',$codigoDocumento);

		 
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_configuracion($codigoDocumento='',$codigoCompania='')
	{
		$this->db->select('CCD.COMPCONFIDOCP_Codigo, CDI.COMPADOCUITEM_Codigo,  CCD.DOCUP_Codigo,CDI.COMPADOCUITEM_Codigo, CDI.DOCUITEM_Codigo,
			CDI.COMPADOCUITEM_Descripcion, CDI.COMPADOCUITEM_Width, 
			CDI.COMPADOCUITEM_Height, CDI.COMPADOCUITEM_PosicionX, 
			CDI.COMPADOCUITEM_PosicionY, CDI.COMPADOCUITEM_TamanioLetra, 
			CDI.COMPADOCUITEM_TipoLetra, CDI.COMPADOCUITEM_Nombre, D.DOCUC_Descripcion ,CDI.COMPADOCUITEM_Variable,CDI.COMPADOCUITEM_Listado,CDI.COMPADOCUITEM_VGrupo,CDI.COMPADOCUITEM_Alineamiento,CDI.COMPADOCUITEM_Activacion, CDI.COMPADOCUITEM_Convertiraletras');

		$this->db->from('cji_companiaconfidocumento CCD');
		$this->db->join('cji_compadocumenitem CDI','CDI.COMPCONFIDOCP_Codigo = CCD.COMPCONFIDOCP_Codigo','inner');
		$this->db->join('cji_documento D','D.DOCUP_Codigo = CCD.DOCUP_Codigo','inner');
		$this->db->where('CCD.DOCUP_Codigo',$codigoDocumento);
		$this->db->where('CCD.COMPCONFIP_Codigo',$codigoCompania);

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}


	public function insertar_configuracion($filter='')
	{
		$this->db->insert('cji_compadocumenitem',$filter);
	}
	public function modificar_configuracion($filter='',$codigoDetalle="",$codigoCompConfDoc="")
	{
		$this->db->where('cji_compadocumenitem.COMPADOCUITEM_Codigo',$codigoDetalle);
		$this->db->where('cji_compadocumenitem.COMPCONFIDOCP_Codigo',$codigoCompConfDoc);	
		$this->db->update('cji_compadocumenitem',$filter);
	}

}
?>