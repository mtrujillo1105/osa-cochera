<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Directivo_model extends CI_Model {

	##  -> Begin
	private $empresa;
	private $compania;
	private $usuario;
	##  -> End

	##  -> Begin
	public function __construct() {
		parent::__construct();
		$this->empresa = $this->session->userdata('empresa');
		$this->compania = $this->session->userdata('compania');
		$this->usuario = $this->session->userdata('user');
	}
	##  -> End

	##  -> Begin
	public function getDirectivos($filter = NULL) {

		$limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
		$order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

		$where = '';
		if (isset($filter->codigo) && $filter->codigo != '')
			$where .= " AND d.DIREC_CodigoEmpleado LIKE '%$filter->codigo%'";

		if (isset($filter->documento) && $filter->documento != '')
			$where .= " AND p.PERSC_NumeroDocIdentidad LIKE '%$filter->documento%'";

		if (isset($filter->nombre) && $filter->nombre != '')
			$where .= " AND CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) LIKE '%$filter->nombre%'";

		$rec = "SELECT d.*,
							p.PERSC_NumeroDocIdentidad, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno, p.PERSC_Telefono, p.PERSC_Movil,
							e.EMPRC_RazonSocial, c.CARGC_Nombre
							FROM cji_directivo d
							INNER JOIN cji_persona p ON p.PERSP_Codigo = d.PERSP_Codigo
							LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = d.EMPRP_Codigo
							LEFT JOIN cji_cargo c ON c.CARGP_Codigo = d.CARGP_Codigo
							WHERE d.DIREC_FlagEstado LIKE '1'
							$where
							$order $limit
						";

		$recF = "SELECT COUNT(*) as registros
							FROM cji_directivo d
							INNER JOIN cji_persona p ON p.PERSP_Codigo = d.PERSP_Codigo
							WHERE d.DIREC_FlagEstado LIKE '1'
							$where
						";
						
		$recT = "SELECT COUNT(*) as registros
							FROM cji_directivo d
							INNER JOIN cji_persona p ON p.PERSP_Codigo = d.PERSP_Codigo
							WHERE d.DIREC_FlagEstado LIKE '1'
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
	public function getDirectivo($codigo) {
		$sql = "SELECT d.*, p.*,
							CASE d.DIREC_FechaFin
								WHEN '0000-00-00' THEN 'INDEFINIDO'
								WHEN d.DIREC_FechaInicio THEN 'INDEFINIDO'
								ELSE d.DIREC_FechaFin
							END as contrato_fin,
							CASE p.PERSC_Sexo
								WHEN 'M' THEN 'MASCULINO'
								WHEN 'F' THEN 'FEMENINO'
								ELSE ''
							END as genero,
							td.TIPOCC_Inciales as tipo_documento, n.NACC_Descripcion, ec.ESTCC_Descripcion, ca.CARGC_Nombre, b.BANC_Nombre
							FROM cji_directivo d
							INNER JOIN cji_persona p ON p.PERSP_Codigo = d.PERSP_Codigo
							LEFT JOIN cji_tipdocumento td ON td.TIPDOCP_Codigo = p.PERSC_TipoDocIdentidad
							LEFT JOIN cji_nacionalidad n ON n.NACP_Codigo = p.NACP_Nacionalidad
							LEFT JOIN cji_estadocivil ec ON ec.ESTCP_Codigo = p.ESTCP_EstadoCivil
							LEFT JOIN cji_cargo ca ON ca.CARGP_Codigo = d.CARGP_Codigo
							LEFT JOIN cji_banco b ON b.BANP_Codigo = p.BANP_Codigo
							WHERE d.DIREP_Codigo = $codigo";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return $query->result();
		}
		return array();
	}
	##  -> End

	##  -> Begin
	public function insertar_directivo($filter){
		$this->db->insert("cji_directivo", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function insertar_persona($filter){
		$this->db->insert("cji_persona", (array) $filter);
		return $this->db->insert_id();
	}
	##  -> End

	##  -> Begin
	public function actualizar_directivo($directivo, $filter){
		$this->db->where('DIREP_Codigo',$directivo);
		return $this->db->update('cji_directivo', $filter);
	}
	##  -> End

	##  -> Begin
	public function actualizar_persona($directivo, $filter){
		$persona = $this->getDirectivo($directivo);
		if ($persona != NULL){
			$this->db->where('PERSP_Codigo',$persona[0]->PERSP_Codigo);
			return $this->db->update('cji_persona', $filter);
		}
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function deshabilitar_usuario($directivo){
		$persona = $this->getDirectivo($directivo);
		if ($persona != NULL){
			$sql = "UPDATE cji_usuario SET USUA_FlagEstado = 0 WHERE PERSP_Codigo = '".$persona[0]->PERSP_Codigo."'";
			$query = $this->db->query($sql);

			if ($query){
				$sql = "DELETE FROM cji_usuario_compania WHERE EXISTS(SELECT u.USUA_Codigo FROM cji_usuario u WHERE u.USUA_FlagEstado LIKE '0' AND u.USUA_Codigo = cji_usuario_compania.USUA_Codigo)";
				$query = $this->db->query($sql);
			}
			return $query;
		}
		else
			return NULL;
	}
	##  -> End

	##  -> Begin
	public function relacion_clientes($vendedor){
		$sql = "SELECT p.*, c.*,
							(SELECT CONCAT_WS(' ', pp.PERSC_NumeroDocIdentidad, ' - ', pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) FROM cji_persona pp WHERE pp.PERSP_Codigo = c.PERSP_Codigo) as nombre_cliente,
							(SELECT CONCAT_WS(' ', e.EMPRC_Ruc, e.EMPRC_RazonSocial) FROM cji_empresa e WHERE e.EMPRP_Codigo = c.EMPRP_Codigo) as razon_social,
							(SELECT SUM(CPP_Codigo) FROM cji_comprobante c WHERE c.CPC_Vendedor = p.PERSP_Codigo AND c.CPC_FlagEstado = 1) as total_documentos,
							(SELECT SUM(CPC_Total) FROM cji_comprobante c WHERE c.CPC_Vendedor = p.PERSP_Codigo AND c.CPC_FlagEstado = 1) as total_ventas

							FROM cji_directivo d
							INNER JOIN cji_persona p ON p.PERSP_Codigo = d.PERSP_Codigo
							INNER JOIN cji_cliente c ON c.CLIC_Vendedor = p.PERSP_Codigo
							WHERE d.DIREP_Codigo = $vendedor
							ORDER BY total_ventas DESC
		";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $fila)
				$data[] = $fila;
			return $data;
		}
		return array();
	}
	##  -> End

	##  -> Begin
	public function listarVendedores($vendedor = NULL){
		$empresa = $this->empresa;
		$where = "";

		if ($vendedor != NULL && $vendedor != "")
			$where = " AND p.PERSP_Codigo = $vendedor";

		$sql = "SELECT p.*, d.TIPCLIP_Codigo
							FROM cji_persona p
							INNER JOIN cji_directivo d ON d.PERSP_Codigo = p.PERSP_Codigo
							INNER JOIN cji_cargo c ON c.CARGP_Codigo = d.CARGP_Codigo
							WHERE (c.CARGC_Nombre LIKE '%VENDEDOR%' OR c.CARGC_Nombre LIKE '%VENTAS%')
								AND p.PERSC_FlagEstado LIKE '1'
								AND d.DIREC_FlagEstado LIKE '1'
							$where
            ";
            # AND d.EMPRP_Codigo = $empresa

    $query = $this->db->query($sql);

    if ($query->num_rows() > 0){
    	foreach ($query->result() as $value) {
    		$data[] = $value;
    	}
    	return $data;
    }
    else
    	return NULL;
  }
  ##  -> End

  ##  -> Begin
  public function getCodeDirectivo(){
   	$empresa = $_SESSION['empresa'];
   	$sql = "SET @DIREC_Codigo = (SELECT MAX(DIREC_CodigoEmpleado) FROM cji_directivo WHERE EMPRP_Codigo = $empresa);";
   	$this->db->query($sql);

   	$sql = "SELECT DIREC_CodigoEmpleado FROM cji_directivo WHERE DIREC_CodigoEmpleado = @DIREC_Codigo";
   	$query = $this->db->query($sql);

   	if ($query->num_rows() > 0){
   		foreach ($query->result() as $indice => $fila) {
   			$data = $fila->DIREC_CodigoEmpleado;
   		}
   		return $data;
   	}
   	else
   		return NULL;
   }
   ##  -> End

  ## FUNCTIONS OLDS
  ###########################

              public function lista_cumpleanios($fechaHoy) {
              	$this->db->select('cji_directivo.DIREP_Codigo,cji_persona.PERSC_FechaNac,
              		cji_persona.PERSC_Nombre,cji_persona.PERSC_ApellidoPaterno,
              		cji_cargo.CARGC_Descripcion,cji_directivo.DIREC_Imagen')
              	->from('cji_directivo')
              	->join('cji_persona', 'cji_directivo.PERSP_Codigo=cji_persona.PERSP_Codigo')
              	->join('cji_cargo', 'cji_directivo.CARGP_Codigo=cji_cargo.CARGP_Codigo')
              	->where('cji_directivo.DIREC_FlagEstado', 1)
              	->where('DATE_FORMAT(cji_persona.PERSC_FechaNac,"%m-%d")', $fechaHoy);
              	$query = $this->db->get();
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function listar_vendedores($empresa, $cargo = "") {
              	$where = array("cji_directivo.DIREC_FlagEstado" => 1, 'cji_directivo.EMPRP_Codigo' => $empresa);
              	if ($cargo != '')
              		$where['CARGP_Codigo'] = $cargo;
              	$query = $this->db->order_by('`cji_directivo`.PERSP_Codigo')
              	->join('cji_persona', 'cji_persona.PERSP_Codigo = cji_directivo.PERSP_Codigo', 'left')
              	->where_not_in('DIREP_Codigo', '0')->where($where)
              	->select('cji_directivo.DIREP_Codigo,cji_persona.PERSP_Codigo,cji_persona.PERSC_Nombre,cji_persona.PERSC_ApellidoPaterno,cji_persona.PERSC_ApellidoMaterno,cji_persona.PERSC_NumeroDocIdentidad')
              	->from('cji_directivo')
              	->get();
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function lista_vendedores2($empresa = '', $cargo = '', $number_items = '', $offset = '') {
              	$sql = "
              	select
              	dir.DIREP_Codigo DIREP_Codigo,
              	dir.EMPRP_Codigo EMPRP_Codigo,
              	dir.PERSP_Codigo PERSP_Codigo,
              	dir.CARGP_Codigo CARGP_Codigo,
              	dir.DIREC_FechaInicio Inicio,
              	dir.DIREC_FechaFin Fin,
              	dir.DIREC_NroContrato Nro_Contrato,
              	emp.EMPRC_RazonSocial empresa,
              	per.PERSC_Nombre nombre,
              	per.PERSC_ApellidoPaterno paterno,
              	per.PERSC_ApellidoMaterno materno,
              	per.PERSC_NumeroDocIdentidad dni,
              	car.CARGC_Descripcion cargo,
              	DIREC_CodigoEmpleado
              	from cji_directivo as dir
              	inner join cji_empresa as emp on dir.EMPRP_Codigo=emp.EMPRP_Codigo
              	inner join cji_persona as per on dir.PERSP_Codigo=per.PERSP_Codigo
              	inner join cji_cargo as car on dir.CARGP_Codigo=car.CARGP_Codigo
              	where dir.DIREC_FlagEstado=1 
              	and dir.DIREP_Codigo!=0 ";


              	if ($empresa != '' && $empresa != '0') {
              		$sql.=" and dir.EMPRP_Codigo=" . 2 . " ";
              	}
              	if ($cargo != '' && $cargo != '0') {
              		$sql.=" and dir.CARGP_Codigo=" . $cargo . " ";
              	}
              	$sql.=" order by nombre";

              	$query = $this->db->query($sql);
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function listar_combodirectivo($empresa) {
              	$sql = "SELECT cji_directivo.*, CONCAT(cji_persona.PERSC_Nombre,' ' , cji_persona.PERSC_ApellidoPaterno , ' ', cji_persona.PERSC_ApellidoMaterno) as nombre
              	FROM cji_directivo
              	INNER JOIN cji_persona ON cji_directivo.PERSP_Codigo=cji_persona.PERSP_Codigo
              	WHERE cji_directivo.EMPRP_Codigo = $empresa AND cji_directivo.DIREC_FlagEstado = 1 AND cji_persona.PERSC_FlagEstado = 1";

              	$query = $this->db->query($sql);
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

//     public function combo_directivos($number_items = '', $offset = '') {
//      $where = array("USUA_FlagEstado" => "1");
//      $query = $this->db->where($where)->join('cji_persona', 'cji_persona.PERSP_Codigo=cji_usuario.PERSP_Codigo')->order_by('cji_persona.PERSC_Nombre, cji_persona.PERSC_ApellidoPaterno, cji_persona.PERSC_ApellidoMaterno')->get('cji_usuario', $number_items, $offset);
//      if ($query->num_rows() > 0) {
//          foreach ($query->result() as $fila) {
//              $data[] = $fila;
//          }
//          return $data;
//      }
//     }

              public function combo_directivos($number_items = '', $offset = '') {
              	$sql="select directivo.DIREP_Codigo,PERSC_Nombre ,PERSC_ApellidoMaterno,PERSC_ApellidoPaterno   from cji_directivo directivo inner join cji_persona persona on directivo.PERSP_Codigo = persona.PERSP_Codigo GROUP BY PERSC_Nombre , PERSC_ApellidoMaterno, PERSC_ApellidoPaterno  and DIREC_FlagEstado = 1  ";
              	$query = $this->db->query($sql);
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function listar_directivo($empresa, $cargo = '') {
              	$where = array(
              		'cji_directivo.EMPRP_Codigo' => $empresa,
              		//'cji_cargo.COMPP_Codigo' => $this->compania,
              		'cji_directivo.DIREC_FlagEstado' => 1,
              		'cji_cargo.CARGC_Descripcion' => 'VENDEDOR'
              	);
              	$query = $this->db->order_by('cji_directivo.PERSP_Codigo')
		              	->join('cji_persona', 'cji_persona.PERSP_Codigo=cji_directivo.PERSP_Codigo')
		              	->join('cji_cargo', 'cji_directivo.CARGP_Codigo=cji_cargo.CARGP_Codigo')
		              	->where($where)
		              	->select('cji_directivo.DIREP_Codigo,cji_persona.PERSP_Codigo,cji_persona.PERSC_Nombre,cji_persona.PERSC_ApellidoPaterno,cji_persona.PERSC_ApellidoMaterno')
		              	->from('cji_directivo')
		              	->get();
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function listar_directivo_personal() {
              	$compania = $this->compania;
              	$empresa = $_SESSION['empresa'];
              	$sql = "SELECT d.DIREP_Codigo, p.PERSP_Codigo, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno
              	FROM cji_directivo d
              	INNER JOIN cji_persona p ON d.PERSP_Codigo = p.PERSP_Codigo
              	INNER JOIN cji_cargo c ON d.CARGP_Codigo = c.CARGP_Codigo
              	WHERE d.DIREC_FlagEstado = 1 AND c.COMPP_Codigo = $compania AND d.EMPRP_Codigo = $empresa
              	";

              	$query = $this->db->query($sql);
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }


              function obtener_directivo($directivo) {
              	$where = array('DIREP_Codigo' => $directivo);
              	$query = $this->db
              	->join('cji_persona', 'cji_persona.PERSP_Codigo = cji_directivo.PERSP_Codigo', 'left')
              	->where($where)
              	->select('cji_directivo.*, cji_persona.PERSC_ApellidoPaterno, cji_persona.PERSC_ApellidoMaterno, cji_persona.PERSC_Nombre')
              	->get('cji_directivo');
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              function buscar_directivo($empresa, $persona) {
              	$where = array('EMPRP_Codigo' => $empresa, 'PERSP_Codigo' => $persona);
              	$query = $this->db->where($where)->get('cji_directivo');
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function listar_directivos2($empresa = '', $cargo = '', $number_items = '', $offset = '') {
              	$sql = "
              	select
              	dir.DIREP_Codigo DIREP_Codigo,
              	dir.EMPRP_Codigo EMPRP_Codigo,
              	dir.PERSP_Codigo PERSP_Codigo,
              	dir.CARGP_Codigo CARGP_Codigo,
              	dir.DIREC_FechaInicio Inicio,
              	dir.DIREC_FechaFin Fin,
              	dir.DIREC_NroContrato Nro_Contrato,
              	emp.EMPRC_RazonSocial empresa,
              	per.PERSC_Nombre nombre,
              	per.PERSC_ApellidoPaterno paterno,
              	per.PERSC_ApellidoMaterno materno,
              	per.PERSC_NumeroDocIdentidad dni,
              	car.CARGC_Descripcion cargo
              	from cji_directivo as dir
              	inner join cji_empresa as emp on dir.EMPRP_Codigo=emp.EMPRP_Codigo
              	inner join cji_persona as per on dir.PERSP_Codigo=per.PERSP_Codigo
              	inner join cji_cargo as car on dir.CARGP_Codigo=car.CARGP_Codigo
              	where dir.DIREC_FlagEstado=1 
              	and dir.DIREP_Codigo!=0 ";
              	if ($empresa != '' && $empresa != '0') {
              		$sql.=" and dir.EMPRP_Codigo=" . $empresa . " ";
              	}
              	if ($cargo != '' && $cargo != '0') {
              		$sql.=" and dir.CARGP_Codigo=" . $cargo . " ";
              	}
              	$sql.=" order by nombre";

              	$query = $this->db->query($sql);
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

//A_listar directivos 2
              public function buscar_directivo2($filter, $number_items = '', $offset = '') {
              	$where = '';
              	$where_empr = '';
              	$where_pers = '';

              	if (isset($filter->numdoc) && $filter->numdoc != "")
              		$where_pers.=' and per.PERSC_NumeroDocIdentidad like "%' . $filter->numdoc . '%" ';

              	if (isset($filter->nombre) && $filter->nombre != "")
              		$where_pers.=' and per.PERSC_Nombre like "%' . $filter->nombre . '%" ';

              	if (isset($filter->empresa) && $filter->empresa != "" && $filter->empresa != "0")
              		$where_pers.=' and dir.EMPRP_Codigo = "' . $filter->empresa . '" ';

              	if (isset($filter->codigoEmpleado) && $filter->codigoEmpleado != "" && $filter->codigoEmpleado != "0")
              		$where_pers.=' and dir.DIREC_CodigoEmpleado = "' . $filter->codigoEmpleado . '" ';

              	if ($number_items == "" && $offset == "") {
              		$limit = "";
              	} else {
              		$limit = "limit $offset,$number_items";
              	}
              	$compania = $this->compania;

              	$sql = "
              	select
              	dir.DIREP_Codigo DIREP_Codigo,
              	dir.EMPRP_Codigo EMPRP_Codigo,
              	dir.PERSP_Codigo PERSP_Codigo,
              	dir.CARGP_Codigo CARGP_Codigo,
              	dir.DIREC_FechaInicio Inicio,
              	dir.DIREC_FechaFin Fin,
              	dir.DIREC_NroContrato Nro_Contrato,
              	emp.EMPRC_RazonSocial empresa,
              	per.PERSC_Nombre nombre,
              	per.PERSC_ApellidoPaterno paterno,
              	per.PERSC_ApellidoMaterno materno,
              	per.PERSC_NumeroDocIdentidad dni,
              	car.CARGC_Descripcion cargo,
              	dir.DIREC_CodigoEmpleado DIREC_CodigoEmpleado
              	from cji_directivo as dir
              	inner join cji_empresa as emp on dir.EMPRP_Codigo=emp.EMPRP_Codigo
              	inner join cji_persona as per on dir.PERSP_Codigo=per.PERSP_Codigo
              	inner join cji_cargo as car on dir.CARGP_Codigo=car.CARGP_Codigo
              	where dir.DIREC_FlagEstado=1
              	and dir.DIREP_Codigo!=0 " . $where . " " . $where_pers . "
              	order by nombre
              	" . $limit . "
              	";
              	$query = $this->db->query($sql);
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function obtener_empresa($idCompania) {

              	$query = $this->db->where('COMPP_Codigo', $idCompania)->select('EMPRP_Codigo')->get('cji_compania');
              	if ($query->num_rows() > 0) {
              		foreach ($query->result() as $fila) {
              			$data[] = $fila;
              		}
              		return $data;
              	}
              }

              public function insertar_datosDirectivo($empresa, $persona, $finicio, $ffin, $cargo, $contrato, $imagen, $categoria, $codigoEmpleado = NULL) {
              	$compania = $this->compania;

        /* if ($fcontrato == '' || $contrato == '0')
        $forma_pago = NULL; */
        $data = array(
        	"EMPRP_Codigo" => $empresa,
        	"PERSP_Codigo" => $persona,
        	"CARGP_Codigo" => $cargo,
        	"TIPCLIP_Codigo" => $categoria,
        	"DIREC_Imagen" => $imagen,
        	"DIREC_FechaInicio" => $finicio,
        	"DIREC_FechaFin" => $ffin,
        	"DIREC_NroContrato" => $contrato,
        	"DIREC_CodigoEmpleado" => $codigoEmpleado
        );
        $this->db->insert("cji_directivo", $data);
        $directivo = $this->db->insert_id();

        //$this->insertar_directivo_compania($directivo);
      }

      public function insertar_directivo_compania($directivo) {
      	$data = array(
      		"DIREP_Codigo" => $directivo,
      		"COMPP_Codigo" => $this->compania,
      	);
      	$this->db->insert("cji_directivocompania", $data);
      }

      public function eliminar_directivo($directivo) {
      	$data = array("DIREC_FlagEstado" => '0');
      	$where = array("DIREP_Codigo" => $directivo);
      	$this->db->where($where);
      	$this->db->update('cji_directivo', $data);
      }

      public function modificar_datosDirectivo($directivo, $empresa, $personacod, $cargo, $fecini, $fecfin, $contrato, $imagen, $categoria) {
      	date_default_timezone_set('America/Lima');
      	$Fec = date("Y-m-d");
      	$time = date("H:i:s");
      	$modified = $Fec . " " . $time;
      	if ($imagen == '') {
      		$data = array(
      			"EMPRP_Codigo" => $empresa,
      			"PERSP_Codigo" => $personacod,
      			"CARGP_Codigo" => $cargo,
      			"TIPCLIP_Codigo" => $categoria,
      			"DIREC_FechaInicio" => $fecini,
      			"DIREC_FechaFin" => $fecfin,
      			"DIREC_NroContrato" => $contrato,
      			"DIREC_FechaModificacion" => $modified
      		);
      	} else {
      		$data = array(
      			"EMPRP_Codigo" => $empresa,
      			"PERSP_Codigo" => $personacod,
      			"CARGP_Codigo" => $cargo,
      			"TIPCLIP_Codigo" => $categoria,
      			"DIREC_Imagen" => $imagen,
      			"DIREC_FechaInicio" => $fecini,
      			"DIREC_FechaFin" => $fecfin,
      			"DIREC_NroContrato" => $contrato,
      			"DIREC_FechaModificacion" => $modified
      		);
      	}
      	$where = array("DIREP_Codigo" => $directivo);
      	$this->db->where($where);
      	$this->db->update('cji_directivo', $data);
      }




    ////stv
      public function obtener_directivo_xusu($usuopt='') {

      	$where="";
      	if($usuopt!=""){
      		$where.=" and cji_usuario.USUA_Codigo='$usuopt' ";
      	}

      	$query = $this->db->query("select cji_directivo.DIREP_Codigo,cji_usuario.USUA_Codigo from cji_usuario,cji_directivo,cji_usuario_compania
      		where cji_usuario.PERSP_Codigo=cji_directivo.PERSP_Codigo and cji_usuario_compania.USUA_Codigo=cji_usuario.USUA_Codigo and USUA_FlagEstado=1 
      		and cji_usuario_compania.COMPP_Codigo='".$this->compania."' $where ");
      	if ($query->num_rows() > 0) {
      		foreach ($query->result() as $fila) {
      			$data[] = $fila;
      		}
      		return $data;
      	}
      }
    ///

      public function autocompleteDirectivo($keyword){
      	try {
      		$sql = "select * from cji_directivo dir inner join cji_persona per 
      		on dir.PERSP_Codigo = per.PERSP_Codigo
      		where PERSC_Nombre LIKE '%" . $keyword . "%' and DIREC_FlagEstado = 1 ";

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

      public function listar_directivo_pdf($documento,$nombre,$empresa)
      {
      	$where = '';
      	$where_empr = '';
      	$where_pers = '';

      	if ($documento != "--")
      		$where_pers.=' and (per.PERSC_NumeroDocIdentidad like "' . $documento . '") ';
      	if ($nombre != "--")
      		$where_pers.=' and (per.PERSC_Nombre like "%' .$nombre. '%") ';
       /* if ($empresa!= "--" && $empresa != "0")
       $where_pers.=' and (dir.EMPRP_Codigo = "' . $empresa . '") ';*/


       $compania = $this->compania;

       $sql = "
       select
       dir.DIREP_Codigo DIREP_Codigo,
       dir.EMPRP_Codigo EMPRP_Codigo,
       dir.PERSP_Codigo PERSP_Codigo,
       dir.CARGP_Codigo CARGP_Codigo,
       dir.DIREC_FechaInicio Inicio,
       dir.DIREC_FechaFin Fin,
       dir.DIREC_NroContrato Nro_Contrato,
       emp.EMPRC_RazonSocial empresa,
       per.PERSC_Nombre nombre,
       per.PERSC_ApellidoPaterno paterno,
       per.PERSC_ApellidoMaterno materno,
       per.PERSC_NumeroDocIdentidad dni,
       car.CARGC_Descripcion cargo
       from cji_directivo as dir
       inner join cji_empresa as emp on dir.EMPRP_Codigo=emp.EMPRP_Codigo
       inner join cji_persona as per on dir.PERSP_Codigo=per.PERSP_Codigo
       inner join cji_cargo as car on dir.CARGP_Codigo=car.CARGP_Codigo
       where dir.DIREC_FlagEstado=1
       and dir.DIREP_Codigo!=0 " . $where . " " . $where_pers . "
       order by nombre
       ";
       $query = $this->db->query($sql);
       if ($query->num_rows() > 0) {
       	foreach ($query->result() as $fila) {
       		$data[] = $fila;
       	}
       	return $data;
       }      
     }

   }

//FIN CLASE
   ?>