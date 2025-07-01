<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Cliente_model extends CI_Model{

	##  -> Begin - El array somevar es reemplazado por atributos
	private $compania;
	private $empresa;
	##  -> End

	public function __construct(){
		parent::__construct();
		$this->load->model('empresa/empresa_model');
		$this->load->model('maestros/ubigeo_model');
		$this->load->model('maestros/persona_model');
		$this->load->model('maestros/companiaconfiguracion_model');

		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
	}

	##  -> Begin
	public function getClientes($filter = NULL) {

            $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
            $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

            $where = '';
            $join  = "";

            if (isset($filter->codigo) && $filter->codigo != '')
                $where .= " AND c.CLIC_CodigoUsuario LIKE '%$filter->codigo%'";

            if (isset($filter->tipo_clienteabonado) && $filter->tipo_clienteabonado != '')
                $where .= " AND c.TIPCLIP_Codigo = '".$filter->tipo_clienteabonado."'";  

            if (isset($filter->documento) && $filter->documento != '')
                $where .= " 
                        AND CASE c.CLIC_TipoPersona
                            WHEN 0 THEN p.PERSC_NumeroDocIdentidad
                            WHEN 1 THEN e.EMPRC_Ruc
                            ELSE ''
                        END LIKE '%$filter->documento%' ";

            if (isset($filter->nombre) && $filter->nombre != '')
                $where .= " AND CASE c.CLIC_TipoPersona
                            WHEN 0 THEN CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                            WHEN 1 THEN e.EMPRC_RazonSocial
                            ELSE ''
                            END LIKE '%$filter->nombre%'
                            ";

            if (isset($filter->placa) && $filter->placa != ''){
                $join  .= " left join cji_clientevehiculo cv on cv.CLIP_Codigo = c.CLIP_Codigo ";
                $where .= " and cv.CLIEVEHIP_Placa like '%$filter->placa%' and cv.CLIEVEHIP_FlagEstado = 1";
            }
                
            #cji_tipdocumento PERSONA
            #cji_tipocodigo EMPRESA

            $rec = "
                SELECT  c.*,
                        CASE c.CLIC_TipoPersona
                            WHEN 0 THEN tp.TIPOCC_Inciales
                            WHEN 1 THEN tc.TIPCOD_Inciales
                            ELSE ''
                        END as documento,
                        CASE c.CLIC_TipoPersona
                            WHEN 0 THEN p.PERSC_NumeroDocIdentidad
                            WHEN 1 THEN e.EMPRC_Ruc
                            ELSE ''
                            END as numero,
                        CASE c.CLIC_TipoPersona
                            WHEN 0 THEN CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                            WHEN 1 THEN e.EMPRC_RazonSocial
                            ELSE ''
                        END as razon_social,
                        tcli.TIPCLIC_Descripcion,(
                            SELECT 
                            GROUP_CONCAT(CLIEVEHIP_Placa) 
                            FROM cji_clientevehiculo cv
                            where cv.CLIP_Codigo = c.CLIP_Codigo and cv.CLIEVEHIP_FlagEstado=1
                            GROUP BY cv.CLIP_Codigo
                        ) Placas
                FROM cji_cliente c
                LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                LEFT JOIN cji_tipdocumento tp ON tp.TIPDOCP_Codigo = p.PERSC_TipoDocIdentidad
                LEFT JOIN cji_tipocodigo tc ON tc.TIPCOD_Codigo = e.TIPCOD_Codigo
                left join cji_tipocliente tcli on tcli.TIPCLIP_Codigo = c.TIPCLIP_Codigo
                $join                
                WHERE c.CLIC_FlagEstado LIKE '1'
                $where $order $limit
            ";

                
            $recF = "
                SELECT COUNT(*) as registros
                FROM cji_cliente c
                LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                LEFT JOIN cji_tipdocumento tp ON tp.TIPDOCP_Codigo = p.PERSC_TipoDocIdentidad
                LEFT JOIN cji_tipocodigo tc ON tc.TIPCOD_Codigo = e.TIPCOD_Codigo
                left join cji_tipocliente tcli on tcli.TIPCLIP_Codigo = c.TIPCLIP_Codigo
                $join
                WHERE c.CLIC_FlagEstado LIKE '1'
                $where
            ";

            $recT = "SELECT COUNT(*) as registros
		FROM cji_cliente c
		LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
		LEFT JOIN cji_tipdocumento tp ON tp.TIPDOCP_Codigo = p.PERSC_TipoDocIdentidad
		LEFT JOIN cji_tipocodigo tc ON tc.TIPCOD_Codigo = e.TIPCOD_Codigo
                left join cji_tipocliente tcli on tcli.TIPCLIP_Codigo = c.TIPCLIP_Codigo
		WHERE c.CLIC_FlagEstado LIKE '1'
            ";

            $records = $this->db->query($rec);
            $recordsFilter = $this->db->query($recF)->row()->registros;
            $recordsTotal = $this->db->query($recT)->row()->registros;

            if ($records->num_rows() > 0){
                $info = array(
                    "records" => $records->result(),
                    "recordsFilter" => $recordsFilter,
                    "recordsTotal" => $recordsTotal
                );
            }
            else{
                $info = array(
                    "records" => NULL,
                    "recordsFilter" => 0,
                    "recordsTotal" => $recordsTotal
                );
            }
            return $info;
	}
	##  -> End

	##  -> Begin
	public function getCliente($cliente) {
    # cji_tipdocumento PERSONA | cji_tipocodigo EMPRESA

		$sql = "SELECT c.*,
		p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno, p.PERSC_Sexo, p.ESTCP_EstadoCivil, p.NACP_Nacionalidad,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_TipoDocIdentidad
		WHEN 1 THEN e.TIPCOD_Codigo
		ELSE ''
		END as tipo_documento,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN tp.TIPOCC_Inciales
		WHEN 1 THEN tc.TIPCOD_Inciales
		ELSE ''
		END as documento,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_NumeroDocIdentidad
		WHEN 1 THEN e.EMPRC_Ruc
		ELSE ''
		END as numero,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
		WHEN 1 THEN e.EMPRC_RazonSocial
		ELSE ''
		END as razon_social,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_Direccion
		WHEN 1 THEN e.EMPRC_Direccion
		ELSE ''
		END as direccion,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.UBIGP_Domicilio
		WHEN 1 THEN (SELECT ep.UBIGP_Codigo FROM cji_emprestablecimiento ep WHERE ep.EMPRP_Codigo = e.EMPRP_Codigo AND ep.EESTABC_FlagTipo LIKE '1' AND ep.EESTABC_FlagEstado LIKE '1' LIMIT 1)
		ELSE ''
		END as ubigeo,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_Telefono
		WHEN 1 THEN e.EMPRC_Telefono
		ELSE ''
		END as telefono,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_Movil
		WHEN 1 THEN e.EMPRC_Movil
		ELSE ''
		END as movil,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_Fax
		WHEN 1 THEN e.EMPRC_Fax
		ELSE ''
		END as fax,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_Email
		WHEN 1 THEN e.EMPRC_Email
		ELSE ''
		END as correo,
		CASE c.CLIC_TipoPersona
		WHEN 0 THEN p.PERSC_Web
		WHEN 1 THEN e.EMPRC_Web
		ELSE ''
		END as web,
		e.SECCOMP_Codigo
		FROM cji_cliente c
		LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
		LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
		LEFT JOIN cji_tipdocumento tp ON tp.TIPDOCP_Codigo = p.PERSC_TipoDocIdentidad
		LEFT JOIN cji_tipocodigo tc ON tc.TIPCOD_Codigo = e.TIPCOD_Codigo
		WHERE c.CLIP_Codigo = '$cliente'
		";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return NULL;
	}
	##  -> End

	public function getVehiculos($filter = NULL, $onlyRecords = true){
            
            $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
            $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";
            $where = '';
            
            if (isset($filter->cliente) && $filter->cliente != '' && $filter->cliente != 0)
                    $where .= " AND ec.CLIP_Codigo = $filter->cliente";
            
            if (isset($filter->placa) && $filter->placa != '')
                    $where .= " AND ec.CLIEVEHIP_Placa = '".$filter->placa."'";            
            
            if (isset($filter->vehiculo) && $filter->vehiculo != '' && $filter->vehiculo != 0)
                    $where .= " AND ec.CLIEVEHIP_Codigo = $filter->vehiculo";                 
            
            $rec = "SELECT ec.*,tar.*
                    FROM cji_clientevehiculo ec
                    inner join cji_tarifa tar on (tar.TARIFP_Codigo=ec.TARIFP_Codigo)
                    WHERE ec.CLIEVEHIP_FlagEstado LIKE '1'
                    $where $order $limit
            ";

            $recF = "SELECT COUNT(*) as registros
                    FROM cji_clientevehiculo ec
                    inner join cji_tarifa tar on (tar.TARIFP_Codigo=ec.TARIFP_Codigo)
                    WHERE ec.CLIEVEHIP_FlagEstado LIKE '1'
                    $where
            ";

            $recT = "SELECT COUNT(*) as registros
                    FROM cji_clientevehiculo ec
                    inner join cji_tarifa tar on (tar.TARIFP_Codigo=ec.TARIFP_Codigo)
                    WHERE ec.CLIEVEHIP_FlagEstado LIKE '1'
            ";

            $records = $this->db->query($rec);
            $recordsFilter = $this->db->query($recF)->row()->registros;
            $recordsTotal = $this->db->query($recT)->row()->registros;

            if ($records->num_rows() > 0){
                if($onlyRecords){
                    $info = array(
                            "records" => $records->result(),
                            "recordsFilter" => $recordsFilter,
                            "recordsTotal" => $recordsTotal
                    ); 
                }
                else{
                    $info = $records->result();
                }
            }
            else{
                if($onlyRecords){
                    $info = array(
                            "records" => NULL,
                            "recordsFilter" => 0,
                            "recordsTotal" => $recordsTotal
                    );  
                }
                else{
                    $info = NULL;
                }
            }
            return $info;
	}      
        
	public function getClienteVehiculos($filter = NULL){
            $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
            $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";
            $where = '';
            
            if (isset($filter->tipo_clienteabonado) && $filter->tipo_clienteabonado != '')
                    $where .= " AND cli.TIPCLIP_Codigo = '".$filter->tipo_clienteabonado."'";             
            
            /*if (isset($filter->cliente) && $filter->cliente != '' && $filter->cliente != 0)
                    $where .= " AND ec.CLIP_Codigo = $filter->cliente";*/
            
            if (isset($filter->cliente) && $filter->cliente != '' && $filter->cliente != 0)
                    $where .= " AND ec.CLIP_Codigo = $filter->cliente";
            
            if (isset($filter->placa) && $filter->placa != '')
                    $where .= " AND ec.CLIEVEHIP_Placa = '".$filter->placa."'";            
            
            if (isset($filter->clientevehiculo) && $filter->clientevehiculo != '' && $filter->clientevehiculo != 0)
                    $where .= " AND ec.CLIEVEHIP_Codigo = $filter->clientevehiculo";                 
            
            $rec = "SELECT ec.*,
                           tar.*,
                           cli.*
                    FROM cji_clientevehiculo ec
                    left join cji_tarifa tar on (tar.TARIFP_Codigo=ec.TARIFP_Codigo)
                    left join cji_cliente cli on (cli.CLIP_Codigo = ec.CLIP_Codigo)
                    WHERE ec.CLIEVEHIP_FlagEstado LIKE '1'
                    AND tar.COMPP_Codigo = ".$this->compania."
                    $where $order $limit
            ";

            $records = $this->db->query($rec);

            if ($records->num_rows() > 0){
                $info = array("records" => $records->result());
            }
            else{
                $info = array("records" => NULL);
            }
            return $info;
	}        
                
	public function getClienteVehiculosTotal($filter = NULL){
            $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
            $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";
            $where = '';
            
            if (isset($filter->tipo_clienteabonado) && $filter->tipo_clienteabonado != '')
                    $where .= " AND cli.TIPCLIP_Codigo = '".$filter->tipo_clienteabonado."'";             
            
            /*if (isset($filter->cliente) && $filter->cliente != '' && $filter->cliente != 0)
                    $where .= " AND ec.CLIP_Codigo = $filter->cliente";*/
            
            if (isset($filter->cliente) && $filter->cliente != '' && $filter->cliente != 0)
                    $where .= " AND ec.CLIP_Codigo = $filter->cliente";
            
            if (isset($filter->placa) && $filter->placa != '')
                    $where .= " AND ec.CLIEVEHIP_Placa = '".$filter->placa."'";            
            
            if (isset($filter->clientevehiculo) && $filter->clientevehiculo != '' && $filter->clientevehiculo != 0)
                    $where .= " AND ec.CLIEVEHIP_Codigo = $filter->clientevehiculo";                 
            
            $rec = "SELECT ec.*,
                           tar.*,
                           cli.*
                    FROM cji_clientevehiculo ec
                    left join cji_tarifa tar on (tar.TARIFP_Codigo=ec.TARIFP_Codigo)
                    left join cji_cliente cli on (cli.CLIP_Codigo = ec.CLIP_Codigo)
                    WHERE ec.CLIEVEHIP_FlagEstado LIKE '1'
                    $where $order $limit
            ";

            $records = $this->db->query($rec);

            if ($records->num_rows() > 0){
                $info = array("records" => $records->result());
            }
            else{
                $info = array("records" => NULL);
            }
            return $info;
	}          
        
	public function getVehiculo($vehiculo){
            $sql = "SELECT cv.*
            FROM cji_clientevehiculo cv
            WHERE cv.CLIEVEHIP_Codigo = '$vehiculo'
            ";
            $query = $this->db->query($sql);

            if ($query->num_rows() > 0)
                    return $query->result();
            else
                    return NULL;
	}        
        
        
	##  -> Begin
	public function getCodeCliente(){
		$sql = "SELECT MAX(CLIP_Codigo) CLIP_Codigo FROM cji_cliente";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0){
			$id = $query->row()->CLIP_Codigo + 1;
			return $id;
		}
		else
			return 1;
	}
	##  -> End

	##  -> Begin
	public function insertar_cliente($filter){
		$this->db->insert("cji_cliente", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

        public function insertar_vehiculo($filter){
            $this->db->insert("cji_clientevehiculo", (array) $filter);
            return $this->db->insert_id();
        }
        
	##  -> Begin
	public function actualizar_cliente($cliente, $filter){
		$this->db->where('CLIP_Codigo',$cliente);
		return $this->db->update('cji_cliente', $filter);
	}
	##  -> End
        
        
        public function actualizar_vehiculo($vehiculo, $filter){
            $this->db->where('CLIEVEHIP_Codigo',$vehiculo);
            return $this->db->update('cji_clientevehiculo', $filter);
        }

	##  -> Begin
	public function docs_generated_exists($cliente){
		$sql = "SELECT cli.CLIP_Codigo
		FROM cji_cliente cli
		WHERE cli.CLIP_Codigo = $cliente
		AND
		( EXISTS(SELECT oc.OCOMP_Codigo FROM cji_ordencompra oc WHERE oc.CLIP_Codigo = cli.CLIP_Codigo)
		OR EXISTS(SELECT gr.GUIAREMP_Codigo FROM cji_guiarem gr WHERE gr.CLIP_Codigo = cli.CLIP_Codigo)
		OR EXISTS(SELECT c.CPP_Codigo FROM cji_comprobante c WHERE c.CLIP_Codigo = cli.CLIP_Codigo)
		OR EXISTS(SELECT n.CRED_Codigo FROM cji_nota n WHERE n.CLIP_Codigo = cli.CLIP_Codigo)
		)
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
			return true;
		else
			return false;
	}
	##  -> End

  ##  -> Begin
	public function docs_emitidos($cliente, $filter = NULL){
		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		if ( isset($filter->count) && $filter->count == true )
			$select = "COUNT(*) as registros";
		else
			$select = "e.EMPRC_RazonSocial, temp.*";

		$sql = "SELECT $select
		FROM (SELECT 
		CASE oc.OCOMC_TipoOperacion
		WHEN 'V' THEN 'COTIZACION'
		WHEN 'C' THEN 'ORDEN DE COMPRA'
		ELSE 'Unknow'
		END documento,
		oc.OCOMC_FechaRegistro as fechaRegistro, oc.OCOMC_Fecha as fecha, oc.OCOMC_Serie as serie, oc.OCOMC_Numero as numero, oc.OCOMC_Total as total,
		CASE oc.OCOMC_FlagEstado
		WHEN '0' THEN 'ANULADO'
		WHEN '1' THEN 'APROBADO'
		WHEN '2' THEN 'BORRADOR'
		ELSE 'Unknow'
		END estado,
		oc.COMPP_Codigo
		FROM cji_ordencompra oc
		WHERE oc.CLIP_Codigo = $cliente
                    
		UNION ALL
                
		SELECT 
		'GUIA DE REMISION' as documento,
		gr.GUIAREMC_FechaRegistro as fechaRegistro, gr.GUIAREMC_Fecha as fecha, gr.GUIAREMC_Serie as serie, gr.GUIAREMC_Numero as numero, gr.GUIAREMC_Total as total,
		CASE gr.GUIAREMC_FlagEstado
		WHEN '0' THEN 'ANULADO'
		WHEN '1' THEN 'APROBADO'
		WHEN '2' THEN 'BORRADOR'
		ELSE 'Unknow'
		END estado,
		gr.COMPP_Codigo
		FROM cji_guiarem gr
		WHERE gr.CLIP_Codigo = $cliente
                    
		UNION ALL
                
		SELECT 
		CASE c.CPC_TipoDocumento
		WHEN 'F' THEN 'FACTURA'
		WHEN 'B' THEN 'BOLETA'
		WHEN 'N' THEN 'COMPROBANTE'
		ELSE 'Unknow'
		END as documento,
		c.CPC_FechaRegistro as fechaRegistro, c.CPC_Fecha as fecha, c.CPC_Serie as serie, c.CPC_Numero as numero, c.CPC_Total as total,
		CASE c.CPC_FlagEstado
		WHEN '0' THEN 'ANULADO'
		WHEN '1' THEN 'APROBADO'
		WHEN '2' THEN 'BORRADOR'
		ELSE 'Unknow'
		END as estado,
		c.COMPP_Codigo
		FROM cji_comprobante c
		WHERE c.CLIP_Codigo = $cliente
                    
		UNION ALL
                
		SELECT 
		CASE n.CRED_TipoNota
		WHEN 'C' THEN 'CREDITO'
		WHEN 'D' THEN 'DEBITO'
		ELSE 'Unknow'
		END as documento,
		n.CRED_FechaRegistro as fechaRegistro, n.CRED_Fecha as fecha, n.CRED_Serie as serie, n.CRED_Numero as numero, n.CRED_Total as total,
		CASE n.CRED_FlagEstado
		WHEN '0' THEN 'ANULADO'
		WHEN '1' THEN 'APROBADO'
		WHEN '2' THEN 'BORRADOR'
		ELSE 'Unknow'
		END estado,
		n.COMPP_Codigo
		FROM cji_nota n
		WHERE n.CLIP_Codigo = $cliente
		) temp
		INNER JOIN cji_compania co ON co.COMPP_Codigo = temp.COMPP_Codigo
		INNER JOIN cji_empresa e ON e.EMPRP_Codigo = co.EMPRP_Codigo
		$order $limit
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0){
			if ( isset($filter->count) && $filter->count == true )
				return $query->row();
			else
				return $query->result();
		}
		else
			return NULL;
	}
	##  -> End

  ## FUNCTIONS OLDS

	public function cliente_exists($ruc = '', $dni = '', $ruc_personal = ''){

		$where = ($ruc != '') ? " e.EMPRC_Ruc = $ruc " : "";
		$where .= ($where == '' && $dni != '') ? " p.PERSC_NumeroDocIdentidad = $dni " : "";
		$where .= ($where != '' && $ruc_personal != '') ? " OR p.PERSC_Ruc = $ruc_personal " : "";

		$sql = "SELECT c.CLIP_Codigo
		FROM cji_cliente c
		LEFT JOIN cji_empresa e ON c.EMPRP_Codigo = e.EMPRP_Codigo
		LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
		WHERE $where";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0){
			foreach ($query->result() as $fila)
				$data[] = $fila;
			return true;
		}
		else
			return false;
	}

	public function listar_cliente($number_items = '', $offset = '') {
		$compania = $this->compania;

		if($number_items=="" && $offset=="")
			$limit = "";
		else
			$limit = " limit $offset, $number_items";

			$clientecompania=  " AND cc.COMPP_Codigo = $compania ";

		$sql = "SELECT
		CLIC_flagCalifica, cli.CLIP_Codigo CLIP_Codigo, cli.EMPRP_Codigo EMPRP_Codigo, cli.PERSP_Codigo PERSP_Codigo, cli.CLIC_TipoPersona CLIC_TipoPersona, cc.COMPP_Codigo COMPP_Codigo, emp.EMPRC_RazonSocial nombre, emp.EMPRC_Ruc ruc,
		'' dni, emp.EMPRC_Direccion direccion, emp.EMPRC_Telefono telefono, emp.EMPRC_Fax fax, cli.CLIC_CodigoUsuario
		FROM cji_clientecompania cc
		INNER JOIN cji_cliente AS cli ON cli.CLIP_Codigo = cc.CLIP_Codigo
		INNER JOIN cji_empresa AS emp ON cli.EMPRP_Codigo = emp.EMPRP_Codigo
		WHERE cli.CLIC_TipoPersona = 1 AND cli.CLIC_FlagEstado = 1 $clientecompania AND cli.CLIP_Codigo != 0
		UNION
		SELECT CLIC_flagCalifica, cli.CLIP_Codigo as CLIP_Codigo, cli.EMPRP_Codigo EMPRP_Codigo, cli.PERSP_Codigo PERSP_Codigo, cli.CLIC_TipoPersona CLIC_TipoPersona, cc.COMPP_Codigo COMPP_Codigo, concat(pers.PERSC_Nombre,' ',pers.PERSC_ApellidoPaterno) as nombre, pers.PERSC_Ruc ruc, pers.PERSC_NumeroDocIdentidad dni, pers.PERSC_Direccion direccion, pers.PERSC_Telefono telefono, pers.PERSC_Fax fax, cli.CLIC_CodigoUsuario
		FROM cji_clientecompania AS cc
		INNER JOIN cji_cliente AS cli ON cli.CLIP_Codigo = cc.CLIP_Codigo
		INNER JOIN cji_persona AS pers ON cli.PERSP_Codigo = pers.PERSP_Codigo
		WHERE cli.CLIC_TipoPersona = 0
		AND cli.CLIC_FlagEstado = 1 $clientecompania AND cli.CLIP_Codigo != 0
		ORDER BY CLIC_CodigoUsuario $limit
		";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener($cliente){
		if($cliente==""){
			$cliente='1';
		}
		$query = $this->db->where('CLIP_Codigo',$cliente)->get('cji_cliente');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$empresa_id   = $fila->EMPRP_Codigo;
				$persona_id   = $fila->PERSP_Codigo;
				$tipo         = $fila->CLIC_TipoPersona;
				$idCliente         = $fila->CLIC_CodigoUsuario;
				$resultado = new stdClass();
				if($tipo==1){
					$datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa_id);
					$datos_empresaSucursal = $this->empresa_model->obtener_establecimientoEmpresa($empresa_id,'1');
					if(count($datos_empresaSucursal)>0){
						$direccion = $datos_empresaSucursal[0]->EESTAC_Direccion;
						$ubigeo    = $datos_empresaSucursal[0]->UBIGP_Codigo;
					}
					else{
						$direccion = "";
						$ubigeo    = "000000";
					}
					$resultado->tipo                = $tipo;
					$resultado->tipoDocIdentidad    = $datos_empresa[0]->TIPCOD_Codigo;
					$resultado->empresa             = $empresa_id;
					$resultado->persona             = $persona_id;
					$resultado->cliente             = $cliente;
					$resultado->nombre              = $datos_empresa[0]->EMPRC_RazonSocial;
					$resultado->ruc                 = $datos_empresa[0]->EMPRC_Ruc;
					$resultado->dni                 = "";
					$resultado->direccion           = $direccion;
					$resultado->ubigeo              = $ubigeo;
					$resultado->telefono            = $datos_empresa[0]->EMPRC_Telefono;
					$resultado->movil               = $datos_empresa[0]->EMPRC_Movil;
					$resultado->fax                 = "";
					$resultado->correo              = $datos_empresa[0]->EMPRC_Email;
				}
				elseif($tipo==0){
					$datos_persona = $this->persona_model->obtener_datosPersona($persona_id);
					$ubigeo        = $datos_persona[0]->UBIGP_Domicilio;
					$resultado->tipo       = $tipo;
					$resultado->tipoDocIdentidad       = $datos_persona[0]->PERSC_TipoDocIdentidad;
					$resultado->empresa    = $empresa_id;
					$resultado->persona    = $persona_id;
					$resultado->cliente    = $cliente;
					$resultado->nombre     = $datos_persona[0]->PERSC_Nombre." ".$datos_persona[0]->PERSC_ApellidoPaterno." ".$datos_persona[0]->PERSC_ApellidoMaterno;
					$resultado->ruc        = $datos_persona[0]->PERSC_Ruc;
					$resultado->dni        = $datos_persona[0]->PERSC_NumeroDocIdentidad;
					$resultado->direccion  = $datos_persona[0]->PERSC_Direccion;
					$resultado->ubigeo     = $ubigeo;
					$resultado->telefono   = "";
					$resultado->fax        = "";
					$resultado->correo     = $datos_persona[0]->PERSC_Email;
					
				}
				$resultado->distrito     = "";
				$resultado->provincia    = "";
				$resultado->departamento = "";
				if($ubigeo!='' && $ubigeo!='000000'){
					$datos_ubigeo_dist = $this->ubigeo_model->obtener_ubigeo_dist($ubigeo);
					$datos_ubigeo_prov = $this->ubigeo_model->obtener_ubigeo_prov($ubigeo);
					$datos_ubigeo_dep  = $this->ubigeo_model->obtener_ubigeo_dpto($ubigeo);
					if(count($datos_ubigeo_dist)>0)
						$resultado->distrito     = $datos_ubigeo_dist[0]->UBIGC_Descripcion;
					if(count($datos_ubigeo_prov)>0)
						$resultado->provincia    = $datos_ubigeo_prov[0]->UBIGC_Descripcion;
					if(count($datos_ubigeo_dep)>0)
						$resultado->departamento = $datos_ubigeo_dep[0]->UBIGC_Descripcion;
				}
			}

			$resultado->idCliente = $idCliente;
			return $resultado;
		}
	}
	public function buscar_cliente($filter, $number_items='',$offset='') {

		if(isset($filter->calificaciones) && $filter->calificaciones!="")
			$where_calif='and CLIC_flagCalifica='.$filter->calificaciones.' ';
		else
			$where_calif='';

		$where = ($filter->codigoU != "") ? " c.CLIC_CodigoUsuario LIKE '%".$filter->codigoU."%'" : "";

		if ($filter->tipo == "N"){
			if ($filter->nombre != ""){
				$where .= ($where != "") ? " AND " : "";
				$where .= "Match(p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) AGAINST ('".$filter->nombre."')";
			}

			if ($filter->numdoc != ""){
				$where .= ($where != "") ? " AND " : "";
				$where .= "p.PERSC_NumeroDocIdentidad LIKE '%".$filter->numdoc."%' OR p.PERSC_Ruc LIKE '%".$filter->numdoc."%'";
			}

			if ($filter->telefono != ""){
				$where .= ($where != "") ? " AND " : "";
				$where .= "p.PERSC_Telefono LIKE '%".$filter->telefono."%' OR p.PERSC_Movil = '%".$filter->telefono."%'";
			}
		}
		else{
			if ($filter->nombre != ""){
				$where .= ($where != "") ? " AND " : "";
				$where .= "e.EMPRC_RazonSocial LIKE '%".$filter->nombre."%'";
			}

			if ($filter->numdoc != ""){
				$where .= ($where != "") ? " AND " : "";
				$where .= "e.EMPRC_Ruc LIKE '%".$filter->numdoc."%'";
			}

			if ($filter->telefono != ""){
				$where .= ($where != "") ? " AND " : "";
				$where .= "e.EMPRC_Telefono LIKE '%".$filter->telefono."%' OR e.EMPRC_Movil LIKE '%".$filter->telefono."%'";
			}
		}

		$limit = ($number_items == "" && $offset == "") ? "" : "limit $offset, $number_items";
		$compania = $this->compania;
		$clientecompania = " AND cc.COMPP_Codigo = $compania ";

		$sql = "SELECT c.CLIP_Codigo, c.CLIC_TipoPersona, c.EMPRP_Codigo, c.PERSP_Codigo, c.CLIC_Vendedor, c.CLIC_flagCalifica, c.CLIC_CodigoUsuario, c.CLIC_Digemin,

		CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PERSC_Nombre, p.PERSC_Ruc, p.PERSC_NumeroDocIdentidad, p.PERSC_Direccion, p.PERSC_Telefono, p.PERSC_Fax,

		e.EMPRC_RazonSocial, e.EMPRC_Ruc, e.EMPRC_Direccion, e.EMPRC_Telefono, e.EMPRC_Fax

		FROM cji_cliente c
		LEFT JOIN cji_clientecompania ce ON ce.CLIP_Codigo = c.CLIP_Codigo AND ce.COMPP_Codigo = $compania
		LEFT JOIN cji_empresa e ON c.EMPRP_Codigo = e.EMPRP_Codigo
		LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
		WHERE $where
		ORDER BY c.CLIC_CodigoUsuario $limit
		";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}
	public function obtener_datosCliente($cliente){
		$query = $this->db->where('CLIP_Codigo',$cliente)->get('cji_cliente');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}


	public function obtener_datosCliente2($empresa){
		$query = $this->db->where('EMPRP_Codigo',$empresa)->get('cji_empresa');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}
	public function obtener_datosCliente3($persona){
		$query = $this->db->where('PERSP_Codigo',$persona)->get('cji_persona');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function insertar_datosCliente($empresa,$persona,$tipo_persona, $categoria, $forma_pago, $calificaciones, $vendedor = NULL, $idNvoCliente, $digemin){
		$compania = $this->compania;
		if($forma_pago=='' || $forma_pago=='0')
			$forma_pago=NULL;
		$data = array(
			"EMPRP_Codigo"      => $empresa,
			"PERSP_Codigo"      => $persona,
			"CLIC_CodigoUsuario"=> $idNvoCliente,
			"CLIC_TipoPersona"  => $tipo_persona,
			"TIPCLIP_Codigo"    => $categoria,
			"CLIC_Vendedor"     => $vendedor,
			"FORPAP_Codigo"     => $forma_pago,
			"CLIC_Digemin"      => $digemin,
			"CLIC_flagCalifica" => $calificaciones
		);
		$this->db->insert("cji_cliente",$data);
		$cliente = $this->db->insert_id();

		$this->insertar_cliente_compania($cliente);
		return $cliente;
	}

	public function insertar_cliente_compania($cliente){
		$data = array(
			"CLIP_Codigo"        => $cliente,
			"COMPP_Codigo"       => $this->compania,
		);
		$this->db->insert("cji_clientecompania",$data);
	}
	public function modificar_datosCliente($cliente, $categoria, $forma_pago, $calificaciones, $vendedor, $digemin){
		if($forma_pago=='' || $forma_pago=='0')
			$forma_pago=NULL;

		$data = array(
			"TIPCLIP_Codigo"    => $categoria,
			"FORPAP_Codigo"     => $forma_pago,
			"CLIC_flagCalifica" => $calificaciones,
			"CLIC_Vendedor"     => $vendedor,
			"CLIC_Digemin"     => $digemin
		);
		$this->db->where("CLIP_Codigo", $cliente);
		$this->db->update("cji_cliente",$data);
	}
	public function eliminar_clienteSucursal($sucursal){
		$data  = array("EESTABC_FlagEstado"=>'0');
		$where = array("EESTABP_Codigo"=>$sucursal);
		$this->db->where($where);
		$this->db->update('cji_emprestablecimiento',$data);
	}
	public function eliminar_cliente($cliente){
		$compania = $this->compania;

		$sql = "SELECT
		(SELECT CLIP_Codigo FROM cji_comprobante WHERE CLIP_Codigo = $cliente LIMIT 1) as comprobantes,
		(SELECT CLIP_Codigo FROM cji_ordencompra WHERE CLIP_Codigo = $cliente LIMIT 1) as ocompras,
		(SELECT CLIP_Codigo FROM cji_guiarem WHERE CLIP_Codigo = $cliente LIMIT 1) as guias,
		(SELECT CLIP_Codigo FROM cji_presupuesto WHERE CLIP_Codigo = $cliente LIMIT 1) as presupuesto,
		(SELECT CLIP_Codigo FROM cji_nota WHERE CLIP_Codigo = $cliente LIMIT 1) as notas
		";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0){
			return 0;
		}
		else{
			$this->db->delete('cji_clientecompania',array('CLIP_Codigo' => $cliente));
			return 1;
		}
	}    

	public function TipoPersonaCliente ($keyword){
		$sql="select DISTINCT * from cji_cliente cliente
		inner join cji_empresa empresa on cliente.EMPRP_Codigo = empresa.EMPRP_Codigo
		where EMPRC_RazonSocial LIKE '%" . $keyword . "%' and EMPRC_FlagEstado = 1";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function autocompleteCliente($keyword){
		try {
			$compania = $this->compania;
			$sql = "SELECT c.CLIP_Codigo, c.CLIC_TipoPersona, c.EMPRP_Codigo, c.PERSP_Codigo, c.CLIC_Vendedor, c.TIPCLIP_Codigo,
			CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PERSC_Nombre, p.PERSC_Ruc, p.PERSC_NumeroDocIdentidad,
			e.EMPRC_RazonSocial, e.EMPRC_Ruc, e.EMPRC_Direccion, c.CLIC_CodigoUsuario, c.CLIC_Digemin
			FROM cji_cliente c
			LEFT JOIN cji_clientecompania ce ON ce.CLIP_Codigo = c.CLIP_Codigo AND ce.COMPP_Codigo = $compania
			LEFT JOIN cji_empresa e ON c.EMPRP_Codigo = e.EMPRP_Codigo
			LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
			WHERE EMPRC_FlagEstado = 1
				AND e.EMPRC_RazonSocial LIKE '%$keyword%'
				OR Match(p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) AGAINST ('$keyword')
				AND p.PERSC_FlagEstado = 1
				OR c.CLIC_CodigoUsuario LIKE '%$keyword%'
				LIMIT 0, 15";

			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $fila) {
					$data[] = $fila;
				}
				return $data;
			}

		} catch (Exception $e) {

		}
	}


	public function buscarClienteRuc($keyword, $compania){

		$sql = "SELECT c.CLIP_Codigo, c.CLIC_TipoPersona, c.EMPRP_Codigo, c.PERSP_Codigo, c.CLIC_Vendedor, c.TIPCLIP_Codigo,
		CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PERSC_Nombre, p.PERSC_Ruc, p.PERSC_NumeroDocIdentidad,
		e.EMPRC_RazonSocial, e.EMPRC_Ruc, e.EMPRC_Direccion, c.CLIC_CodigoUsuario, c.CLIC_Digemin
		FROM cji_cliente c
		LEFT JOIN cji_clientecompania ce ON ce.CLIP_Codigo = c.CLIP_Codigo AND ce.COMPP_Codigo = $compania
		LEFT JOIN cji_empresa e ON c.EMPRP_Codigo = e.EMPRP_Codigo
		LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
		WHERE EMPRC_FlagEstado = 1 AND e.EMPRC_Ruc LIKE '%$keyword%' OR p.PERSC_NumeroDocIdentidad LIKE '%$keyword%' AND p.PERSC_FlagEstado = 1 LIMIT 0, 15";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
		else{
			return NULL;
		}
	}

	public function autocompleteClienteNatural($keyword){
		$sql="SELECT c.CLIP_Codigo,  p.PERSP_Codigo,
		p.PERSC_Nombre, p.PERSC_ApellidoPaterno,p.PERSC_ApellidoMaterno
		FROM cji_cliente c
		INNER JOIN cji_clientecompania ce ON ce.CLIP_Codigo = c.CLIP_Codigo
		INNER JOIN cji_persona p ON c.PERSP_Codigo = p.PERSP_Codigo
		WHERE p.PERSC_Nombre LIKE '%" . $keyword . "%'
		or p.PERSC_ApellidoPaterno LIKE '%" . $keyword . "%' 
		or p.PERSC_ApellidoMaterno LIKE '%" . $keyword . "%' 
		and PERSC_FlagEstado = 1";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function getUsuarioNombre($cod){
		$this->db->select('USUA_usuario,PERSC_Nombre,ROL_Codigo');
		$this->db->join('cji_persona p','p.PERSP_Codigo=u.PERSP_Codigo');
		$where = array('USUA_Codigo' => $cod);
		$query = $this->db->where($where)->get('cji_usuario u');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_cliente_pdf($docum,$nombre,$telefono,$tipo){
		$where='';
		$where_empr='';
		$where_pers='';
		$where_calif='';

		if($tipo=="J"){
			$where=' and cli.CLIC_TipoPersona = "1"';

			if($docum !="--")
				$where_empr.=' and emp.EMPRC_Ruc like "'.$docum.'"';
			if($nombre!="--")
                    #Cambio10-08-2016
				$where_empr.=' and emp.EMPRC_RazonSocial like "%'.$nombre.'%"';
			if($telefono!="--")
				$where_empr.=' and (emp.EMPRC_Telefono like "%'.$telefono.'%" or emp.EMPRC_Movil like "%'.$telefono.'%")';
		}
		else{
			if($tipo=="N"){
				$where=' and cli.CLIC_TipoPersona = "0"';

				if($docum!="--")
					$where_pers.=' and (pers.PERSC_NumeroDocIdentidad like "'.$docum.'" or pers.PERSC_Ruc like "'.$docum.'")';
				if($nombre!="--")
					$where_pers.='and (pers.PERSC_Nombre like "%'.$nombre.'%" or  pers.PERSC_ApellidoPaterno like "%'.$nombre.'%"  or pers.PERSC_ApellidoMaterno like "%'.$nombre.'%")';
				if($telefono!="--")
					$where_pers.='and (pers.PERSC_Telefono like "%'.$telefono.'%" or pers.PERSC_Movil like "%'.$telefono.'%")';
			}
			else{
				if($docum!="--"){
					$where_empr.=' and emp.EMPRC_Ruc like "'.$docum.'"';
					$where_pers.=' and (pers.PERSC_NumeroDocIdentidad like "'.$docum.'" or pers.PERSC_Ruc like "'.$docum.'")';
				}
				if($nombre!="--"){
					$where_empr.=' and emp.EMPRC_RazonSocial like "%'.$nombre.'%"';
					$where_pers.='and (pers.PERSC_Nombre like "%'.$nombre.'%" or  pers.PERSC_ApellidoPaterno like "%'.$nombre.'%"  or pers.PERSC_ApellidoMaterno like "%'.$nombre.'%")';                   
				}
				if($telefono!="--"){
					$where_empr.=' and (emp.EMPRC_Telefono like "%'.$telefono.'%" or emp.EMPRC_Movil like "%'.$telefono.'%")';
					$where_pers.='and (pers.PERSC_Telefono like "%'.$telefono.'%" or pers.PERSC_Movil like "% '.$telefono.' %")';
				}
			}      
		}

		$compania = $this->compania;

			$clientecompania=  "and cc.COMPP_Codigo=".$compania." ";

		$sql = "
		select 
		CLIC_flagCalifica,
		cli.CLIP_Codigo CLIP_Codigo,
		cli.EMPRP_Codigo EMPRP_Codigo,
		cli.PERSP_Codigo PERSP_Codigo,
		cli.CLIC_TipoPersona CLIC_TipoPersona,
		cc.COMPP_Codigo COMPP_Codigo,
		emp.EMPRC_RazonSocial nombre,
		emp.EMPRC_Ruc ruc,
		'' dni,

		emp.EMPRC_Direccion direccion,


		emp.EMPRC_Telefono telefono,
		emp.EMPRC_Fax fax
		from cji_clientecompania as  cc
		inner join cji_cliente as cli on cli.CLIP_Codigo=cc.CLIP_Codigo
		inner join cji_empresa as emp on cli.EMPRP_Codigo=emp.EMPRP_Codigo
		where cli.CLIC_TipoPersona=1
		and cli.CLIC_FlagEstado=1
		".$clientecompania."
		and cli.CLIP_Codigo!=0 ".$where." ".$where_empr." ".$where_calif."
		UNION
		select
		CLIC_flagCalifica,
		cli.CLIP_Codigo as CLIP_Codigo,
		cli.EMPRP_Codigo EMPRP_Codigo,
		cli.PERSP_Codigo PERSP_Codigo,
		cli.CLIC_TipoPersona CLIC_TipoPersona,
		cc.COMPP_Codigo COMPP_Codigo,
		concat(pers.PERSC_Nombre,' ',pers.PERSC_ApellidoPaterno) as nombre,
		pers.PERSC_Ruc ruc,
		pers.PERSC_NumeroDocIdentidad dni,


		pers.PERSC_Direccion direccion,

		pers.PERSC_Telefono telefono,
		pers.PERSC_Fax fax
		from cji_clientecompania as cc
		inner join cji_cliente as cli on cli.CLIP_Codigo=cc.CLIP_Codigo
		inner join cji_persona as pers on cli.PERSP_Codigo=pers.PERSP_Codigo
		where cli.CLIC_TipoPersona=0
		and cli.CLIC_FlagEstado=1
		".$clientecompania."
		and cli.CLIP_Codigo!=0 ".$where." ".$where_pers." ".$where_calif."
		order by nombre";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function listar_bancos(){   
		$where = array("BANC_FlagEstado"=>"1");
		$query = $this->db->order_by('BANC_Nombre')->where($where)->get('cji_banco');
		if($query->num_rows() > 0){
			return $query->result();
		}
	}

	public function listMoneda(){
		$this->db->select('MONED_Codigo,MONED_Descripcion,MONED_Simbolo');
		$this->db->from('cji_moneda');
		$this->db->where('MONED_FlagEstado', '1');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila) {
				$data[] = $fila;
			}
			return $data;
		}
	}

	public function obtener_ubigueo($depa, $prov, $dist){
		$sql = "SET @depa = (SELECT MAX(UBIGC_CodDpto) FROM cji_ubigeo WHERE UBIGC_Descripcion LIKE '$depa' LIMIT 1);";
		$this->db->query($sql);
		$sql = "SET @prov = (SELECT MAX(UBIGC_CodProv) FROM cji_ubigeo WHERE UBIGC_CodDpto = @depa AND UBIGC_CodProv > 0 AND UBIGC_Descripcion LIKE '$prov' LIMIT 1);";
		$this->db->query($sql);
		$sql = "SELECT MAX(UBIGP_Codigo) as UBIGP_Codigo FROM cji_ubigeo WHERE UBIGC_CodDpto = @depa AND UBIGC_CodProv = @prov AND UBIGC_Descripcion LIKE '$dist'";

		$query = $this->db->query($sql);

		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
		}

		if (count($data) > 0)
			return $data;
		else{
			$data[0]->UBIGP_Codigo = "1";
			return $data;
		}
	}

}
?>