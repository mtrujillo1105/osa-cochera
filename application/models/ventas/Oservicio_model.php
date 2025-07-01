<?php

class Oservicio_model extends CI_Model
{
    var $somevar;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('ventas/comprobante_model');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['usuario'] = $this->session->userdata('user');
        $this->somevar['hoy'] = mdate("%Y-%m-%d %h:%i:%s", time());
    }


    public function seleccionar($oservicio = '')
    {
        $arreglo = array('' => ':: Seleccione ::');
        $lista = $this->listar();
        if (count($lista) > 0) {
            foreach ($lista as $indice => $valor) {
                if ($valor->OCOMC_FlagIngreso == 0 || ($oservicio != '' && $valor->OCOMP_Codigo == $oservicio)) {
                    $indice1 = $valor->OCOMP_Codigo;
                    $valor1 = $valor->OCOMC_Numero;
                    $proveedor = $valor->PROVP_Codigo;
                    $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                    $nombre_proveedor = $datos_proveedor->nombre;
                    $arreglo[$indice1] = $valor1 . "::" . $nombre_proveedor;
                }
            }
        }
        return $arreglo;
    }

    public function seleccionar2($oservicio = '')
    {
        $arreglo = array('' => ':: Seleccione ::');
        if (count($this->listar()) > 0) {
            foreach ($this->listar() as $indice => $valor) {
                if ($valor->OCOMC_FlagIngreso == 0 || ($oservicio != '' && $valor->OCOMP_Codigo == $oservicio)) {
                    $indice1 = $valor->OCOMP_Codigo;
                    $valor1 = $valor->OCOMC_Numero;
                    $proveedor = $valor->PROVP_Codigo;
                    $datos_proveedor = $this->proveedor_model->obtener($proveedor);
                    $nombre_proveedor = $datos_proveedor->nombre;
                    $arreglo[$indice1] = $valor1 . "::" . $nombre_proveedor;
                }
            }
        }
        return $arreglo;
    }

    public function total_oservicio($tipo_oper = 'C')
    {
        $where = array("OCOMC_TipoOperacion" => $tipo_oper, "COMPP_Codigo" => $this->somevar['compania'], "OCOMC_FlagIngreso" => 0, "OCOMC_FlagEstado" => "1","OCOMC_FlagBS" => "S");
        $query = $this->db->select('COUNT(OCOMP_Codigo) as total')
            ->order_by('OCOMC_Numero', 'desc')
            ->where_not_in('OCOMP_Codigo', '0')
            ->where($where)
            ->get('cji_ordencompra');
        return $query->row()->total;
    }

    public function listar($tipo_oper = 'C', $number_items = '', $offset = '')
    {
        $where = array("OCOMC_TipoOperacion" => $tipo_oper, "COMPP_Codigo" => $this->somevar['compania'], "OCOMC_FlagIngreso" => 0, "OCOMC_FlagEstado" => "1", "OCOMC_FlagBS" => "S");  // .retiré : "OCOMC_FlagIngreso"=>0
        $query = $this->db->order_by('OCOMC_Numero', 'desc')->where_not_in('OCOMP_Codigo', '0')->where($where)->get('cji_ordencompra', $number_items, $offset);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    /*Ordenes de compra que no han sido enlazadas a un comprobante*/
    public function listar_oservicios_nocomprobante($tipo_oper, $comprobante_codigo = '')
    {
        $where = array("COMPP_Codigo" => $this->somevar['compania'], "OCOMC_FlagEstado" => "1",
            "OCOMC_TipoOperacion" => $tipo_oper, "OCOMC_FlagTerminado !=" => "1"); //Esta condicional lo saqué "OCOMC_FlagIngreso"=>1
        $query = $this->db->order_by('OCOMC_Numero', 'desc')
            ->where_not_in('OCOMP_Codigo', '0')
            ->where($where)
            ->get('cji_ordencompra');
        $data = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $comprobante = $this->comprobante_model->buscar_x_numero_oservicio($tipo_oper, $fila->OCOMP_Codigo);
                if (count($comprobante) == 0 || ($comprobante_codigo != '' && $comprobante[0]->CPP_Codigo == $comprobante_codigo)) {
                    if ($tipo_oper == 'C') {
                        $datos_proveedor = $this->proveedor_model->obtener($fila->PROVP_Codigo);
                        $fila->nombre = $datos_proveedor->nombre;
                        $data[] = $fila;
                    } else {
                        $datos_cliente = $this->cliente_model->obtener($fila->CLIP_Codigo);
                        $fila->nombre = $datos_cliente->nombre;
                        $data[] = $fila;
                    }

                }
            }
        }
        return $data;
    }

    public function listar_oservicios_x_producto($producto, $number_items = '', $offset = '')
    {
        $where = array('cji_ordencompra.OCOMC_FlagEstado' => 1, 'cji_ocompradetalle.OCOMDEC_FlagEstado' => 1, 'cji_ocompradetalle.PROD_Codigo' => $producto);
        $this->db->select('cji_ordencompra.OCOMC_FechaRegistro,cji_ordencompra.OCOMC_Numero,cji_ordencompra.PROVP_Codigo,cji_ocompradetalle.OCOMDEC_Cantidad,cji_ocompradetalle.OCOMDEC_Pu,cji_ocompradetalle.OCOMDEC_Total,cji_ordencompra.FORPAP_Codigo,cji_ocompradetalle.OCOMDEC_Igv');
        $this->db->from('cji_ordencompra', $number_items, $offset);
        $this->db->join('cji_ocompradetalle', 'cji_ocompradetalle.OCOMP_Codigo=cji_ordencompra.OCOMP_Codigo');
        $this->db->where($where);
        $this->db->order_by('cji_ordencompra.OCOMC_Numero', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_oservicio($oservicio)
    {
        $query = $this->db->where('OCOMP_Codigo', $oservicio)->get('cji_ordencompra');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_detalle_oservicio($oservicio)
    {
        $where = array("OCOMP_Codigo" => $oservicio, "OCOMDEC_FlagEstado" => "1");
        $query = $this->db->where($where)->get('cji_ocompradetalle');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_detalle_oservicio2($oservicio)
    {
        $where = array("OCOMP_Codigo" => $oservicio, "OCOMDEC_FlagEstado" => "1", "OCOMDEC_FlagIngreso" => 0);
        $query = $this->db->where($where)->get('cji_ocompradetalle');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar_oservicio($filter = null)
    {
        $compania = $this->somevar['compania'];
        $usuario = $this->somevar ['usuario'];
        $datos_configuracion = $this->configuracion_model->obtener_numero_documento($compania, '17');
        $numero = $datos_configuracion[0]->CONFIC_Numero + 1;

        $filter->OCOMC_Numero = $numero;
        $filter->USUA_Codigo = $usuario;
        $filter->COMPP_Codigo = $compania;
        $filter->OCOMC_FlagBS = "S";

        $this->db->insert("cji_ordencompra", (array)$filter);
        $oservicio = $this->db->insert_id();
        $this->configuracion_model->modificar_configuracion($compania, 17, $numero);
        return $oservicio;
    }

    public function modificar_oservicio($oservicio, $filter = null)
    {
        $where = array("OCOMP_Codigo" => $oservicio);
        $this->db->where($where);
        $this->db->update('cji_ordencompra', (array)$filter);
    }

    public function modificar_flagTerminado($oservicio, $flag)
    {
        $data = array("OCOMC_FlagTerminado" => $flag);
        $where = array("OCOMP_Codigo" => $oservicio);
        $this->db->where($where);
        $this->db->update('cji_ordencompra', $data);
    }

    public function modificar_detoservicio_flagIngreso($oservicio, $producto)
    {
        $data = array("OCOMDEC_FlagIngreso" => 1);
        $where = array("OCOMP_Codigo" => $oservicio, "PROD_Codigo" => $producto);
        $this->db->where($where);
        $this->db->update('cji_ocompradetalle', $data);
    }

    public function modificar_detoservicio_flagsIngresos($oservicio)
    {
        $data = array("OCOMDEC_FlagIngreso" => 0);
        $where = array("OCOMP_Codigo" => $oservicio);
        $this->db->where($where);
        $this->db->update('cji_ocompradetalle', $data);
    }

    public function modificar_oservicio_flagIngreso($oservicio)
    {
        $where = array("OCOMP_Codigo" => $oservicio, "OCOMDEC_FlagEstado" => "1");
        $query = $this->db->where($where)->get("cji_ocompradetalle");
        $where2 = array("OCOMP_Codigo" => $oservicio, "OCOMDEC_FlagIngreso" => 1, "OCOMDEC_FlagEstado" => "1");
        $query2 = $this->db->where($where2)->get("cji_ocompradetalle");
        if ($query->num_rows() == $query2->num_rows) {
            $this->db->where(array("OCOMP_Codigo" => $oservicio))->update("cji_ordencompra", array("OCOMC_FlagIngreso" => 1));
        } else {
            $this->db->where(array("OCOMP_Codigo" => $oservicio))->update("cji_ordencompra", array("OCOMC_FlagIngreso" => 0));
        }
    }

    public function modificar_oservicio_flagRecibido($oservicio, $numero_factura)
    {
        $where = array("OCOMP_Codigo" => $oservicio);
        $this->db->where($where)->update("cji_ordencompra", array("OCOMC_FlagRecibido" => 1, "OCOMC_NumeroFactura" => $numero_factura));
    }

    public function eliminar($oservicio)
    {
        $where = array("OCOMP_Codigo" => $oservicio);
        $this->db->where($where);
        $this->db->delete('cji_ocompradetalle');
        $where = array("OCOMP_Codigo" => $oservicio);
        $this->db->where($where);
        $this->db->delete('cji_ordencompra');
    }

    public function evaluar_oservicio($flag, $checkO)
    {
        foreach ($checkO as $indice => $valor) {
            if ($valor != '') {
                $data = array(
                    "OCOMC_FlagAprobado" => $flag,
                );
                $where = array("OCOMP_Codigo" => $valor);
                $this->db->where($where);
                $this->db->update('cji_ordencompra', $data);
            }
        }

    }

    public function obtenerOrdenServicio(stdClass $filter = NULL, $offset = '', $number_items = '')
    {

        $compania = $this->somevar['compania'];
        $where = '';

        if ($filter->tipo_oper != 'C') {
            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '') {
                $where .= ' and PERSC_Nombre LIKE "%' . $filter->nombre_cliente .'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_cliente .'%"';
                $where .= ' OR EMPRC_RazonSocial like "%' . $filter->nombre_cliente . '%"';
            }
            if(isset($filter->ruc_cliente) && $filter->ruc_cliente != ''){
                $where .= ' and PERSC_NumeroDocIdentidad like "%' . $filter->ruc_cliente . '%"';
                $where .= ' OR EMPRC_Ruc like "%' . $filter->ruc_cliente . '%"';
            }
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '') {
                $where .= ' and PERSC_Nombre LIKE "%' . $filter->proveedor .'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->proveedor .'%"';
                $where .= ' OR EMPRC_RazonSocial like "%' . $filter->proveedor . '%"';
            }
            if(isset($filter->ruc_proveedor) && $filter->ruc_proveedor != ''){
                $where .= ' and EMPRC_Ruc like "%' . $filter->ruc_proveedor . '%"';
                $where .= ' OR PERSC_NumeroDocIdentidad like "%' . $filter->ruc_proveedor . '%"';
            }
        }

        if($filter->fechai != "" && isset($filter->fechai) && $filter->fechaf == "" || !isset($filter->fechaf)){
            $where .= ' and o.OCOMC_Fecha >= ' . '"'.$filter->fechai.'"';
            $where .= ' and o.OCOMC_Fecha <= ' . '"2020-11-11"';
        }else if($filter->fechaf != "" && isset($filter->fechaf) && $filter->fechai == "" || !isset($filter->fechai)){
            $where .= ' and o.OCOMC_Fecha >= ' . '"2010-11-11"';
            $where .= ' and o.OCOMC_Fecha <= ' . '"'.$filter->fechaf.'"';
        }else if($filter->fechai != "" && isset($filter->fechai) && $filter->fechaf != "" && isset($filter->fechaf)){
            $where .= ' and o.OCOMC_Fecha >= ' . '"'.$filter->fechai.'"';
            $where .= ' and o.OCOMC_Fecha <= ' . '"'.$filter->fechaf.'"';
        }else{
            $where .= '';
        }

        if ($filter->producto != '')
            $where .= ' and od.PROD_Codigo=' . $filter->producto;
        if ($filter->aprobado != '')
            $where .= ' and o.OCOMC_FlagAprobado=' . $filter->aprobado;
        if ($filter->ingreso != '')
            $where .= ' and o.OCOMC_FlagIngreso=' . $filter->ingreso;
        $limit = "";
        if ((string)$offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT DATE_FORMAT(o.OCOMC_FechaRegistro, '%d/%m/%Y') fecha,
                         o.OCOMP_Codigo,
                         o.PEDIP_Codigo,
                         o.PROVP_Codigo,
                         o.CENCOSP_Codigo,
                         o.OCOMC_Numero,
                           (CASE WHEN o.COTIP_Codigo = 0 THEN '***'
                           ELSE CAST(ct.COTIC_Numero AS char) END) cotizacion,
                         (CASE " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                      m.MONED_Simbolo,
                       o.OCOMC_total,
                       (CASE o.OCOMC_FlagAprobado
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Aprob.'
                                WHEN '2' THEN 'Desaprob.'
                                ELSE ''
                        END) aprobado,
                        (CASE o.OCOMC_FlagIngreso
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Si.'
                                ELSE ''
                        END) ingreso,
                        o.OCOMC_FlagEstado
                FROM cji_ordencompra o
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=o.MONED_Codigo
                INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo=o.OCOMP_Codigo

				" . ($filter->tipo_oper != 'C' ? "INNER JOIN cji_cliente p ON p.CLIP_Codigo= o.CLIP_Codigo" : "LEFT JOIN cji_proveedor p ON p.PROVP_Codigo= o.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . " = '0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "= '1'

				LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                WHERE o.OCOMC_FlagBS = 'S' AND o.OCOMC_FlagEstado= '1' " . $where . " AND o.OCOMC_TipoOperacion='" . $filter->tipo_oper . "'
                AND o.COMPP_Codigo = '" . $compania . "'
                GROUP BY o.OCOMP_Codigo
                ORDER BY o.OCOMC_Numero DESC " . $limit . "
                ";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function buscar_oservicio($tipo_oper, $fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso, $number_items = '', $offset = '')
    {
        $compania = $this->somevar['compania'];
        $where = '';
        if ($fechai != '' && $fechaf != '')
            $where = ' and o.OCOMC_FechaRegistro BETWEEN "' . $fechai . '" AND "' . $fechaf . '"';

        if ($tipo_oper != 'C') {
            if (isset($proveedor) && $proveedor != '')
                $where .= ' and o.CLIP_Codigo LIKE "%' . $proveedor . '%"';
        } else {
            if (isset($proveedor) && $proveedor != '')
                $where .= ' and o.PROVP_Codigo LIKE "%' . $proveedor . '%"';
        }


        if ($producto != '')
            $where .= ' and od.PROD_Codigo=' . $producto;
        if ($aprobado != '')
            $where .= ' and o.OCOMC_FlagAprobado=' . $aprobado;
        if ($ingreso != '')
            $where .= ' and o.OCOMC_FlagIngreso=' . $ingreso;
        $limit = "";
        if ((string)$offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT DATE_FORMAT(o.OCOMC_FechaRegistro, '%d/%m/%Y') fecha,
                         o.OCOMP_Codigo,
                         o.PEDIP_Codigo,
                         o.PROVP_Codigo,
                         o.CENCOSP_Codigo,
                         o.OCOMC_Numero,
                         
                           (CASE WHEN o.COTIP_Codigo =0 THEN '***'
                           ELSE CAST(ct.COTIC_Numero AS char) END) cotizacion,
                         (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                      m.MONED_Simbolo,
                       o.OCOMC_total,
                       (CASE o.OCOMC_FlagAprobado 
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Aprob.'
                                WHEN '2' THEN 'Desaprob.'
                                ELSE ''
                        END) aprobado,
                        (CASE o.OCOMC_FlagIngreso 
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Si.'
                                ELSE ''
                        END) ingreso,
                        o.OCOMC_FlagEstado
                FROM cji_ordencompra o
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=o.MONED_Codigo
                INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo=o.OCOMP_Codigo
                
				" . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente p ON p.CLIP_Codigo= o.CLIP_Codigo" : "LEFT JOIN cji_proveedor p ON p.PROVP_Codigo= o.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "='1'
				 
				LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                WHERE o.OCOMC_FlagBS = 'S' AND o.OCOMC_FlagEstado='1' " . $where . " AND o.OCOMC_TipoOperacion='" . $tipo_oper . "'
                AND o.COMPP_Codigo = '" . $compania . "'
                GROUP BY o.OCOMP_Codigo
                ORDER BY o.OCOMC_Numero DESC " . $limit . "
                ";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function buscar_oservicio_asoc($tipo_oper = 'V', $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '')
    {
        $where = '';


        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= ' and o.CLIP_Codigo=' . $filter->cliente;
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= ' and o.PROVP_Codigo=' . $filter->proveedor;
        }


        $limit = "";
        if ((string)$offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;


        $sql = "SELECT 
                         o.OCOMP_Codigo,
                         o.PEDIP_Codigo,
						 o.OCOMC_Fecha,
                        o.CLIP_Codigo,
                        						o.OCOMC_TipoOperacion,
                       (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                      o.CENCOSP_Codigo,
                         o.OCOMC_Numero,
						  o.OCOMC_Serie,
                       m.MONED_Simbolo,
                       o.OCOMC_total,
                       (CASE o.OCOMC_FlagAprobado 
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Aprob.'
                                WHEN '2' THEN 'Desaprob.' 
                                ELSE ''
                        END) aprobado,
                        (CASE o.OCOMC_FlagIngreso 
                                WHEN '0' THEN 'Pend.'
                                WHEN '1' THEN 'Si.' 
                                ELSE ''
                        END) ingreso,
                        o.OCOMC_FlagEstado
                FROM cji_ordencompra o
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=o.MONED_Codigo
                INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo=o.OCOMP_Codigo
				
               " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente p ON p.CLIP_Codigo= o.CLIP_Codigo" : "LEFT JOIN cji_proveedor p ON p.PROVP_Codigo= o.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "='1'
				 
				 LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
				 WHERE o.OCOMC_FlagBS = 'S' AND o.OCOMC_FlagEstado='1' " . $where . " AND o.OCOMC_TipoOperacion='" . $tipo_oper . "' AND o.OCOMC_FlagTerminado = '0'
                GROUP BY o.OCOMP_Codigo
                ORDER BY o.OCOMC_Numero DESC " . $limit . "
                ";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }


    public function reporte_oservicio_cantidad_x_mes()
    {
        $sql = "SELECT
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='01' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) enero,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='02' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) febrero,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='03' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) marzo,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='04' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) abril,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='05' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) mayo,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='06' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) junio,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='07' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) julio,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='08' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) agosto,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='09' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) setiembre,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='10' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) octubre,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='11' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) noviembre,
                    SUM((CASE WHEN MONTH(o.OCOMC_FechaRegistro)='12' AND o.OCOMC_total<>0 THEN 1 ELSE 0 END)) diciembre
            FROM cji_ordencompra o
            WHERE o.OCOMC_FlagEstado='1' AND  o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='C' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
        //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no está aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_oservicio_5_prov_mas_importantes()
    {
        $sql = "SELECT Q.total,Q.nombre
                FROM
                        (SELECT SUM(o.OCOMC_total) total,
                                (CASE p.PROVC_TipoPersona WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre, ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) END) nombre
                        FROM cji_ordencompra o
                        INNER JOIN cji_proveedor p ON p.PROVP_Codigo=o.PROVP_Codigo
                        LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.PROVC_TipoPersona='1'
                        LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.PROVC_TipoPersona='0'
                        WHERE o.OCOMC_FlagEstado='1' AND o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='C' AND o.OCOMC_FlagAprobado like '%'
                        GROUP BY o.PROVP_Codigo)Q
                ORDER BY Q.total DESC
                LIMIT 5";
        //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no está aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_oservicio_monto_x_mes()
    {
        $sql = "SELECT
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '01' THEN o.OCOMC_total ELSE 0 END)) enero,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '02' THEN o.OCOMC_total ELSE 0 END)) febrero,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '03' THEN o.OCOMC_total ELSE 0 END)) marzo,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '04' THEN o.OCOMC_total ELSE 0 END)) abril,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '05' THEN o.OCOMC_total ELSE 0 END)) mayo,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '06' THEN o.OCOMC_total ELSE 0 END)) junio,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '07' THEN o.OCOMC_total ELSE 0 END)) julio,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '08' THEN o.OCOMC_total ELSE 0 END)) agosto,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '09' THEN o.OCOMC_total ELSE 0 END)) setiembre,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '10' THEN o.OCOMC_total ELSE 0 END)) octubre,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '11' THEN o.OCOMC_total ELSE 0 END)) noviembre,
                    SUM((CASE MONTH(o.OCOMC_FechaRegistro) WHEN '12' THEN o.OCOMC_total ELSE 0 END)) diciembre
                FROM cji_ordencompra o
                WHERE o.OCOMC_FlagEstado='1' AND o.OCOMP_Codigo<>0 AND OCOMC_TipoOperacion='C' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
        //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no está aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_comparativo_compras_ventas($tipo_op)
    {
        //CPC_TipoOperacion => V venta, C compra
        //CPC_TipoDocumento => F factura, B boleta
        //CPC_total => total de la FACTURA o BOLETA
        $sql = "SELECT
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '01' THEN c.CPC_total ELSE 0 END)) enero,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '02' THEN c.CPC_total ELSE 0 END)) febrero,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '03' THEN c.CPC_total ELSE 0 END)) marzo,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '04' THEN c.CPC_total ELSE 0 END)) abril,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '05' THEN c.CPC_total ELSE 0 END)) mayo,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '06' THEN c.CPC_total ELSE 0 END)) junio,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '07' THEN c.CPC_total ELSE 0 END)) julio,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '08' THEN c.CPC_total ELSE 0 END)) agosto,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '09' THEN c.CPC_total ELSE 0 END)) setiembre,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '10' THEN c.CPC_total ELSE 0 END)) octubre,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '11' THEN c.CPC_total ELSE 0 END)) noviembre,
                    SUM((CASE MONTH(c.CPC_FechaRegistro) WHEN '12' THEN c.CPC_total ELSE 0 END)) diciembre
            FROM cji_comprobante c
            WHERE c.CPC_TipoOperacion='" . $tipo_op . "' AND c.CPC_FlagEstado='1' AND  c.CPP_Codigo<>0 AND YEAR(c.CPC_FechaRegistro)=YEAR(CURDATE())";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }
    public function listar_oservicios_pdf($tipo_oper,$fecha_ini, $fecha_fin,$codigo,$nombre){
    }

    public function listarByIds($ids)
    {
        return $this->db->from("cji_ordencompra as oc")
                        ->join("cji_ocompradetalle as ocd", "ocd.OCOMP_Codigo = oc.OCOMP_Codigo")
                        ->join("cji_unidadmedida as um", "um.UNDMED_Codigo = ocd.UNDMED_Codigo","left")
                        ->join("cji_producto as prod", "prod.PROD_Codigo = ocd.PROD_Codigo")
                        ->where_in('oc.OCOMP_Codigo', $ids)
                        ->where("ocd.OCOMDEC_FlagEstado", 1)
                        ->get()
                        ->result();
    }

}

?>