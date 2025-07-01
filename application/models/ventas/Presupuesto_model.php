<?php

class Presupuesto_model extends CI_Model {

    var $somevar;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('configuracion_model');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user'] = $this->session->userdata('user');
        $this->somevar['hoy'] = mdate("%Y-%m-%d %h:%i:%s", time());
    }

    public function listar_presupuestos($number_items = '', $offset = '') {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania);
        $query = $this->db->order_by('PRESUC_Numero', 'desc')
                        ->where_not_in('PRESUP_Codigo', 0)
                        ->where($where)->get('cji_presupuesto', $number_items, $offset);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_presupuestos($filter, $number_items = '', $offset = '') {
        $compania = $this->somevar['compania'];
        $data_confi = $this->companiaconfiguracion_model->obtener($compania);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);

        $where = '';
        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
            $where = ' and p.PRESUC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
        switch ($data_confi_docu[0]->COMPCONFIDOCP_Tipo) {
            case '1': if (isset($filter->numero) && $filter->numero != '')
                    $where.=' and p.PRESUC_Numero=' . $filter->numero; break;
            case '2': if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
                    $where.=' and p.PRESUC_Serie="' . $filter->serie . '" and p.PRESUC_Numero=' . $filter->numero; break;
            case '3': if (isset($filter->codigo_usuario) && $filter->codigo_usuario != '')
                    $where.=' and p.PRESUC_CodigoUsuario=' . $filter->codigo_usuario; break;
        }
        if (isset($filter->cliente) && $filter->cliente != '')
            $where.=' and p.CLIP_Codigo=' . $filter->cliente;
        if (isset($filter->producto) && $filter->producto != '')
            $where.=' and pd.PROD_Codigo=' . $filter->producto;
        $limit = "";
        if ((string) $offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT p.PRESUC_Fecha,
                         p.PRESUP_Codigo,
                         p.PRESUC_Serie,
                         p.PRESUC_Numero,
                         p.CLIP_Codigo,
                         p.PRESUC_NombreAuxiliar,
                         p.PRESUC_CodigoUsuario,                        
                       (CASE c.CLIC_TipoPersona  WHEN '1'
                       THEN e.EMPRC_RazonSocial
                       ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       (CASE p.PRESUC_TipoDocumento WHEN 'F' THEN 'Factura' ELSE 'Boleta' END) nom_tipodocu,
                       m.MONED_Simbolo,
                       p.PRESUC_total,
                       p.PRESUC_FlagEstado
                FROM cji_presupuesto p
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=p.MONED_Codigo
                LEFT JOIN cji_presupuestodetalle pd ON pd.PRESUP_Codigo=p.PRESUP_Codigo
                INNER JOIN cji_cliente c ON c.CLIP_Codigo=p.CLIP_Codigo
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND c.CLIC_TipoPersona ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND c.CLIC_TipoPersona='1'
                WHERE p.COMPP_Codigo =" . $compania . " " . $where . "
                GROUP BY p.PRESUP_Codigo
                ORDER BY p.PRESUC_Fecha DESC,p.PRESUC_Numero DESC " . $limit . "

                ";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

	 public function buscar_presupuestos_asoc($tipo_oper , $docu_orig, $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '') {
        $compania = $this->somevar['compania'];

        $where = '';
        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
		 $where = ' and p.PRESUC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
        if (isset($filter->seriei) && $filter->seriei != '')
             $where.=' and p.PRESUC_Serie="' . $filter->serie . '" and p.PRESUC_Numero=' . $filter->numero;
        if (isset($filter->numero) && $filter->numero != '')
           $where.=' and p.PRESUC_Numero=' . $filter->numero;
		
		//if( $docu_orig != 'G'  && $docu_orig != 'N')
		//$where.=" and p.PRESUC_TipoDocumento='".$docu_orig."'";
					
        if (isset($filter->cliente) && $filter->cliente != '')
            $where.=' and p.CLIP_Codigo=' . $filter->cliente;
			
        if (isset($filter->producto) && $filter->producto != '')
            $where.=' and pd.PROD_Codigo=' . $filter->producto;
			
        $limit = "";
		
        if ((string) $offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "
		SELECT p.PRESUC_Fecha,
                         p.PRESUP_Codigo,
                         p.PRESUC_Serie,
                         p.PRESUC_Numero,
                         p.CLIP_Codigo,
                         p.PRESUC_NombreAuxiliar,
                         p.PRESUC_CodigoUsuario,                        
                       (CASE c.CLIC_TipoPersona  WHEN '1'
                       THEN e.EMPRC_RazonSocial
                       ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       (CASE p.PRESUC_TipoDocumento WHEN 'F' THEN 'Factura' ELSE 'Boleta' END) nom_tipodocu,
                       m.MONED_Simbolo,
                       p.PRESUC_total,
                       p.PRESUC_FlagEstado
                FROM cji_presupuesto p
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=p.MONED_Codigo
                LEFT JOIN cji_presupuestodetalle pd ON pd.PRESUP_Codigo=p.PRESUP_Codigo
                INNER JOIN cji_cliente c ON c.CLIP_Codigo=p.CLIP_Codigo
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND c.CLIC_TipoPersona ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND c.CLIC_TipoPersona='1'
                WHERE p.COMPP_Codigo =" . $compania . " " . $where . "
                AND p.PRESUP_Seleccion=0
                GROUP BY p.PRESUP_Codigo
                ORDER BY p.PRESUC_Fecha DESC,p.PRESUC_Numero DESC " . $limit . "

                ";
        //echo $sql."<br/>";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }
	
	
	
    public function obtener_presupuesto($presupuesto) {
        
        $where = array('PRESUP_Codigo' => $presupuesto);
        $query = $this->db->where($where)->get('cji_presupuesto');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_presupuesto_filtrado($presupuesto) {
        $tipo_oper = $this->uri->segment(4);
        $tipo_docu = $this->uri->segment(5);
        ///bloqueado stv
//        $comprobante = $this->buscar_x_numero_presupuesto($tipo_oper, $tipo_docu, $presupuesto);
//        if (count($comprobante) == "0") {
            $where = array('PRESUP_Codigo' => $presupuesto);
            $query = $this->db->where($where)->get('cji_presupuesto');
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $fila) {
                    $data[] = $fila;
                }
                return $data;
            }
//        }
    }
    
    public function obtener_presupuesto_filtrado1($serie,$numero) {
        $tipo_oper = $this->uri->segment(4);
        $tipo_docu = $this->uri->segment(5);
        $comprobante = $this->buscar_x_numero_presupuesto1($tipo_oper, $tipo_docu, $serie, $numero);
        if (count($comprobante) == "0") {
            $where = array('PRESUC_Serie' => $serie,'PRESUC_Numero'=>$numero);
            $query = $this->db->where($where)->get('cji_presupuesto');
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $fila) {
                    $data[] = $fila;
                }
                return $data;
            }
        }
    }

    public function buscar_x_numero_presupuesto($tipo_oper, $tipo_docu, $presupuesto) {

        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, 
                        "CPC_TipoOperacion" => $tipo_oper,
                        "CPC_FlagEstado" => "1", 
                        "PRESUP_Codigo" => $presupuesto);

        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function buscar_x_numero_presupuesto1($tipo_oper, $tipo_docu, $serie, $numero) {

        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, 
                        "CPC_TipoOperacion" => $tipo_oper,
                        "CPC_FlagEstado" => "1", 
                        "PRESUC_Serie" => $serie,
                        "PRESUC_Numero" => $numero);

        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function ultimo_numero(){
        $this->db->select("PRESUC_Numero");
        $this->db->from("cji_presupuesto");
       $this->db->order_by("PRESUC_Numero",'desc');
       $this->db->limit(1);
       $query= $this->db->get();
         if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        
    }

    public function insertar_presupuesto($filter = null) {
        $compania = $this->somevar['compania'];
        $user = $this->somevar ['user'];

        $filter->COMPP_Codigo = $compania;
        $filter->USUA_Codigo = $user;
        /* $datos_configuracion = $this->configuracion_model->obtener_numero_documento($compania,'13');
          $numero = $datos_configuracion[0]->CONFIC_Numero + 1;
          $filter->PRESUC_Numero=$numero; */

        $this->db->insert("cji_presupuesto", (array) $filter);

        $presupuesto = $this->db->insert_id();
        /* $this->configuracion_model->modificar_configuracion($compania,13,$numero); */
        return $presupuesto;
    }

    public function modificar_presupuesto($presupuesto, $filter = null) {
        $user = $this->somevar ['user'];
        $filter->USUA_Codigo = $user;

        $where = array("PRESUP_Codigo" => $presupuesto);
        $this->db->where($where);
        $this->db->update('cji_presupuesto', (array) $filter);
    }

    public function eliminar_presupuesto($presupuesto) {
        $data = array("PRESUC_FlagEstado" => '0');
        $where = array("PRESUP_Codigo" => $presupuesto);
        $this->db->where($where);
        $this->db->update('cji_presupuesto', $data);

        $data = array("PRESDEC_FlagEstado" => '0');
        $where = array("PRESUP_Codigo" => $presupuesto);
        $this->db->where($where);
        $this->db->update('cji_presupuestodetalle', $data);
    }

    /* Presupuesto que no han sido enlazadas a un comprobante */

    public function listar_presupuestos_nocomprobante($tipo_oper, $tipo_docu, $comprobante_codigo = '') {
        //echo "TIPO DE DOCUMENTO : $tipo_docu";
        $where = array("COMPP_Codigo" => $this->somevar['compania'], "PRESUC_TipoDocumento" => $tipo_docu, "PRESUC_FlagEstado" => "1");
        $query = $this->db->order_by('PRESUP_Codigo', 'desc')
                ->where_not_in('PRESUP_Codigo', '0')
                ->where($where)
                ->get('cji_presupuesto');
        $data = array();
        if ($query->num_rows() > 0) {

            foreach ($query->result() as $fila) {
                $comprobante = $this->comprobante_model->buscar_x_numero_presupuesto($tipo_oper, $tipo_docu, $fila->PRESUP_Codigo);
                if (count($comprobante) == 0 || ($comprobante_codigo != '' && $comprobante[0]->CPP_Codigo == $comprobante_codigo)) {
                    if ($tipo_oper == 'V') {
                        $datos_cliente = $this->cliente_model->obtener_datosCliente($fila->CLIP_Codigo);

                        $empresa = $datos_cliente[0]->EMPRP_Codigo;
                        $persona = $datos_cliente[0]->PERSP_Codigo;
                        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
                        if ($tipo == 0) {
                            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                            $nombre = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                        } elseif ($tipo == 1) {
                            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
                        }
                        $fila->nombre = $nombre;
                        $data[] = $fila;
                    }
					
                }
            }
        }
        return $data;
    }

    public function listar_presupuestos_nocomprobante_cualquiera($tipo_oper, $tipo_docu, $comprobante_codigo = '') {
        $where = array("COMPP_Codigo" => $this->somevar['compania'], "PRESUC_FlagEstado" => "1");
        $query = $this->db->order_by('PRESUP_Codigo', 'desc')
                ->where_not_in('PRESUP_Codigo', '0')
                ->where($where)
                ->get('cji_presupuesto');
        $data = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $comprobante = $this->comprobante_model->buscar_x_numero_presupuesto($tipo_oper, $tipo_docu, $fila->PRESUP_Codigo);
                if (count($comprobante) == 0 || ($comprobante_codigo != '' && $comprobante[0]->CPP_Codigo == $comprobante_codigo)) {
                    if ($tipo_oper == 'V') {
                        $datos_cliente = $this->cliente_model->obtener_datosCliente($fila->CLIP_Codigo);

                        $empresa = $datos_cliente[0]->EMPRP_Codigo;
                        $persona = $datos_cliente[0]->PERSP_Codigo;
                        $tipo = $datos_cliente[0]->CLIC_TipoPersona;
                        if ($tipo == 0) {
                            $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                            $nombre = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                        } elseif ($tipo == 1) {
                            $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                            $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
                        }
                        $fila->nombre = $nombre;
                        $data[] = $fila;
                    }
                }
            }
        }
        return $data;
    }

    /* Presupuesto que no han sido enlazadas a una guia de remisi��n */

    public function listar_presupuestos_noguiarem($tipo_docu, $guiarem_codigo = '') {
        $where = array("COMPP_Codigo" => $this->somevar['compania'], 'PRESUC_TipoDocumento' => $tipo_docu);
        $query = $this->db->order_by('PRESUP_Codigo', 'desc')
                ->where_not_in('PRESUP_Codigo', '0')
                ->where($where)
                ->get('cji_presupuesto');
        $data = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $guiarem = $this->guiarem_model->buscar_x_numero_presupuesto($fila->PRESUP_Codigo);
                if (count($guiarem) == 0 || ($guiarem_codigo != '' && $guiarem[0]->GUIAREMP_Codigo == $guiarem_codigo)) {
                    $datos_cliente = $this->cliente_model->obtener_datosCliente($fila->CLIP_Codigo);

                    $empresa = $datos_cliente[0]->EMPRP_Codigo;
                    $persona = $datos_cliente[0]->PERSP_Codigo;
                    $tipo = $datos_cliente[0]->CLIC_TipoPersona;
                    if ($tipo == 0) {
                        $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                        $nombre = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    } elseif ($tipo == 1) {
                        $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                        $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
                    }
                    $fila->nombre = $nombre;
                    $data[] = $fila;
                }
            }
        }
        return $data;
    }

    public function listar_presupuestos_noguiarem_cualquiera($tipo_docu='', $guiarem_codigo = '') {
        $where = array("COMPP_Codigo" => $this->somevar['compania']);
        $query = $this->db->order_by('PRESUP_Codigo', 'desc')
                ->where_not_in('PRESUP_Codigo', '0')
                ->where($where)
                ->get('cji_presupuesto');
        $data = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                
                ///bloqueado stv
//                $guiarem = $this->guiarem_model->buscar_x_numero_presupuesto($fila->PRESUP_Codigo);
//                if (count($guiarem) == 0 || ($guiarem_codigo != '' && $guiarem[0]->GUIAREMP_Codigo == $guiarem_codigo)) {
                ////     
                
                    $datos_cliente = $this->cliente_model->obtener_datosCliente($fila->CLIP_Codigo);

                    $empresa = $datos_cliente[0]->EMPRP_Codigo;
                    $persona = $datos_cliente[0]->PERSP_Codigo;
                    $tipo = $datos_cliente[0]->CLIC_TipoPersona;
                    if ($tipo == 0) {
                        $datos_persona = $this->persona_model->obtener_datosPersona($persona);
                        $nombre = $datos_persona[0]->PERSC_Nombre . " " . $datos_persona[0]->PERSC_ApellidoPaterno . " " . $datos_persona[0]->PERSC_ApellidoMaterno;
                    } elseif ($tipo == 1) {
                        $datos_empresa = $this->empresa_model->obtener_datosEmpresa($empresa);
                        $nombre = $datos_empresa[0]->EMPRC_RazonSocial;
                    }
                    $fila->nombre = $nombre;
                    $data[] = $fila;
//       stv         }
            }
        }
        return $data;
    }

    public function obtener_ultimo_numero($serie = '') {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania);

        if ($serie != '')
            $where['PRESUC_Serie'] = $serie;
        else
            $where['PRESUC_Serie'] = NULL;

        $query = $this->db->order_by('PRESUC_Serie', 'desc')->order_by('PRESUC_Numero', 'desc')->where($where)->get('cji_presupuesto', 1);
        $numero = 1;
        if ($query->num_rows() > 0) {
            $data = $query->result();
            $numero = (int) $data[0]->PRESUC_Numero + 1;
        }
        return $numero;
    }

	public function Insertar_correo_enviado($filter = null) {
		$user = $this->somevar ['user'];
        $filter->USUA_Codigo = $user;
        $this->db->insert('cji_correoenviar', (array) $filter); 
    }
	
	public function correoenviado_presu($codigo) {
		 $where = array('PRESUP_Codigo' => $codigo);
            $query = $this->db->where($where)->get('cji_correoenviar');
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $fila) {
                    $data[] = $fila;
                }
                return $data;
            }
    }
    
    /**modificamos que el poresupuesto este seleccionado o deseleccionado**/
    public function modificarTipoSeleccion($codigoPresupuesto,$estadoSeleccion){
    	$data  = array("PRESUP_Seleccion"=>$estadoSeleccion);
    	$where = array("PRESUP_Codigo"=>$codigoPresupuesto);
    	$this->db->where($where);
    	$this->db->update('cji_presupuesto',$data);
    }
	public function listar_presupuesto_pdf($flagBS, $fechain, $fechafin, $numero, $cliente, $producto){
		
		$compania = $this->somevar['compania'];
        $data_confi = $this->companiaconfiguracion_model->obtener($compania);
        $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);

        $where = '';
        if ($fechain != '--' && $fechafin != '--')
            $where = ' and p.PRESUC_Fecha BETWEEN "' . human_to_mysql($fechain) . '" AND "' . human_to_mysql($fechafin) . '"';
        switch ($data_confi_docu[0]->COMPCONFIDOCP_Tipo) {
            case '1': if ($numero!= '--')
                    $where.=' and p.PRESUC_Numero=' . $numero; break;
            
        }
        if ($cliente != '--')
            $where.=' and p.CLIP_Codigo=' . $cliente;
        if ($producto != '--')
            $where.=' and pd.PROD_Codigo=' . $producto;
       
        $sql = "SELECT p.PRESUC_Fecha,
                         p.PRESUP_Codigo,
                         p.PRESUC_Serie,
                         p.PRESUC_Numero,
                         p.CLIP_Codigo,
                         p.PRESUC_NombreAuxiliar,
                         p.PRESUC_CodigoUsuario,                        
                       (CASE c.CLIC_TipoPersona  WHEN '1'
                       THEN e.EMPRC_RazonSocial
                       ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       (CASE p.PRESUC_TipoDocumento WHEN 'F' THEN 'Factura' ELSE 'Boleta' END) nom_tipodocu,
                       m.MONED_Simbolo,
                       p.PRESUC_total,
                       p.PRESUC_FlagEstado
                FROM cji_presupuesto p
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=p.MONED_Codigo
                LEFT JOIN cji_presupuestodetalle pd ON pd.PRESUP_Codigo=p.PRESUP_Codigo
                INNER JOIN cji_cliente c ON c.CLIP_Codigo=p.CLIP_Codigo
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND c.CLIC_TipoPersona ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND c.CLIC_TipoPersona='1'
                WHERE p.COMPP_Codigo =" . $compania . " " . $where . "
                GROUP BY p.PRESUP_Codigo
                ORDER BY p.PRESUC_Fecha DESC,p.PRESUC_Numero DESC 

                ";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
	}
	
}



?>