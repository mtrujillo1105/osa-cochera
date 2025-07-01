<?php

class Ocompra_model extends CI_Model{

    public $somevar;

    public function __construct(){
        parent::__construct();
        $this->load->helper('date');
        $this->load->model('maestros/configuracion_model');
        $this->load->model('empresa/proveedor_model');
        $this->load->model('ventas/comprobante_model');
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['usuario'] = $this->session->userdata('user');
    }

    public function seleccionar($ocompra = '')
    {
        $arreglo = array('' => ':: Seleccione ::');
        $lista = $this->listar();
        if (count($lista) > 0) {
            foreach ($lista as $indice => $valor) {
                if ($valor->OCOMC_FlagIngreso == 0 || ($ocompra != '' && $valor->OCOMP_Codigo == $ocompra)) {
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

    public function seleccionar2($ocompra = '')
    {
        $arreglo = array('' => ':: Seleccione ::');
        if (count($this->listar()) > 0) {
            foreach ($this->listar() as $indice => $valor) {
                if ($valor->OCOMC_FlagIngreso == 0 || ($ocompra != '' && $valor->OCOMP_Codigo == $ocompra)) {
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

    public function total_ocompra($tipo_oper = 'C')
    {
        $where = array("OCOMC_TipoOperacion" => $tipo_oper, "COMPP_Codigo" => $this->somevar['compania']);
        $query = $this->db->select('COUNT(OCOMP_Codigo) as total')
            ->order_by('OCOMC_Numero', 'desc')
            ->where_not_in('OCOMP_Codigo', '0')
            ->where($where)
            ->get('cji_ordencompra');
        return $query->row()->total;
    }

    public function listar($tipo_oper = 'C', $number_items = 50, $offset = 0){
        $compania = $this->somevar['compania'];
        
        if ($offset == ''){
            $offset = 0;
        }

        $sql = "SELECT ocP.*,
                    (SELECT CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                        FROM cji_persona p
                        WHERE p.PERSP_Codigo = ocP.OCOMC_MiPersonal
                    ) as vendedor,
                    (SELECT GUIAREMP_Codigo FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = ocP.OCOMP_Codigo LIMIT 1) as GUIAREMP_Codigo,
                    (SELECT CONCAT_WS('-', gr.GUIAREMC_Serie, gr.GUIAREMC_Numero) FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = ocP.OCOMP_Codigo LIMIT 1) as GUIAREMC_SerieNumero,
                    
                    (SELECT CPP_Codigo FROM cji_comprobante c WHERE c.OCOMP_Codigo = ocP.OCOMP_Codigo LIMIT 1) as CPP_Codigo,
                    (SELECT CONCAT_WS('-', c.CPC_Serie, c.CPC_Numero) FROM cji_comprobante c WHERE c.OCOMP_Codigo = ocP.OCOMP_Codigo LIMIT 1) as CPC_SerieNumero

                    FROM cji_ordencompra ocP
                    WHERE ocP.OCOMC_TipoOperacion = '$tipo_oper' AND ocP.COMPP_Codigo = $compania AND ocP.OCOMC_FlagIngreso = 0
                    ORDER BY ocP.OCOMC_Numero DESC
                    LIMIT $offset, $number_items
                ";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function seguimiento_listar($tipo_oper = 'C', $number_items = '', $offset = ''){
        $where = array("OCOMC_TipoOperacion" => $tipo_oper, "COMPP_Codigo" => $this->somevar['compania'], "OCOMC_FlagIngreso" => 0, "OCOMC_FlagEstado" => "1");

        if($tipo_oper =="C") $where["OCOMC_FlagBS"] = "B";

        $query = $this->db->order_by('OCOMC_Numero', 'desc')->where_not_in('OCOMP_Codigo', '0')->where($where)->get('cji_ordencompra', $number_items, $offset);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    /*Ordenes de compra que no han sido enlazadas a un comprobante*/
    public function listar_ocompras_nocomprobante($tipo_oper, $comprobante_codigo = '')
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
                $comprobante = $this->comprobante_model->buscar_x_numero_ocompra($tipo_oper, $fila->OCOMP_Codigo);
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

    public function listar_ocompras_x_producto($producto, $number_items = '', $offset = '')
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

    public function obtener_ocompra($ocompra){
      $sql = "SELECT oc.*,
                  (SELECT GUIAREMP_Codigo FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = oc.OCOMP_Codigo LIMIT 1) as GUIAREMP_Codigo,
                  (SELECT CONCAT_WS('-', gr.GUIAREMC_Serie, gr.GUIAREMC_Numero) FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = oc.OCOMP_Codigo LIMIT 1) as GUIAREMC_SerieNumero,
                  (SELECT CPP_Codigo FROM cji_comprobante c WHERE c.OCOMP_Codigo = oc.OCOMP_Codigo LIMIT 1) as CPP_Codigo,
                  (SELECT CONCAT_WS('-', c.CPC_Serie, c.CPC_Numero) FROM cji_comprobante c WHERE c.OCOMP_Codigo = oc.OCOMP_Codigo LIMIT 1) as CPC_SerieNumero,
                  (SELECT CONCAT_WS(' ', pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) FROM cji_persona pp WHERE pp.PERSP_Codigo = oc.OCOMC_Personal LIMIT 1) as personal,
                  CONCAT_WS(' ', pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) as mipersonal, pp.PERSC_Telefono, pp.PERSC_Movil, pp.PERSC_Email
                  FROM cji_ordencompra oc
                  LEFT JOIN cji_persona pp ON pp.PERSP_Codigo = oc.OCOMC_MiPersonal
                  WHERE oc.OCOMP_Codigo = '$ocompra'
              ";
      $query = $this->db->query($sql);

      if ($query->num_rows() > 0)
      	return $query->result();
      else
        return NULL;
    }

    public function obtener_ocompras_rango($inicio, $fin, $oper){
        $compania = $this->somevar['compania'];
        $sql = "SELECT oc.OCOMP_Codigo
                    FROM cji_ordencompra oc
                    WHERE oc.OCOMC_Numero >= $inicio AND oc.OCOMC_Numero <= $fin AND oc.OCOMC_FlagEstado <> 0 AND oc.COMPP_Codigo = $compania AND oc.OCOMC_TipoOperacion = '$oper'
                ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_ocompra_guia($ocompra)
    {

        $sql = "SELECT * FROM cji_ordencompra WHERE OCOMP_Codigo = (SELECT OCOMP_Codigo FROM cji_guiarem WHERE GUIAREMP_Codigo = $ocompra)";
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_detalle_ocompra($ocompra){

        $sql = "SELECT oc.*, pr.PROD_CodigoInterno, pr.PROD_CodigoUsuario, pr.PROD_CodigoOriginal, pr.PROD_FlagBienServicio, pr.PROD_Nombre, pr.FABRIP_Codigo, um.UNDMED_Simbolo, m.MARCC_CodigoUsuario, m.MARCC_Descripcion,
                    (SELECT ap.ALMPROD_Stock FROM cji_almacenproducto ap WHERE ap.PROD_Codigo = oc.PROD_Codigo AND ap.ALMAC_Codigo = o.ALMAP_Codigo) as stockAlmacen,
                    l.LOTC_Numero, l.LOTC_FechaVencimiento
                    FROM cji_ocompradetalle oc
                    INNER JOIN cji_ordencompra o ON o.OCOMP_Codigo = oc.OCOMP_Codigo
                    INNER JOIN cji_producto pr ON oc.PROD_Codigo = pr.PROD_Codigo
                    LEFT JOIN cji_marca m ON m.MARCP_Codigo = pr.MARCP_Codigo
                    LEFT JOIN cji_unidadmedida um ON um.UNDMED_Codigo = oc.UNDMED_Codigo
                    LEFT JOIN cji_lote l ON l.LOTP_Codigo = oc.LOTP_Codigo
                        WHERE oc.OCOMP_Codigo = '$ocompra' AND oc.OCOMDEC_FlagEstado = 1
                ";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
          return $query->result();
        else
          return NULL;
    }

    public function verificarStockLote_ocompra($ocompra){
        $sql = "SELECT oc.OCOMDEC_Cantidad, oc.OCOMDEC_Descripcion, pr.PROD_CodigoUsuario, m.MARCC_CodigoUsuario, l.LOTC_Numero, apl.ALMALOTC_Cantidad,

                    @pendienteOC := (SELECT SUM(ocd.OCOMDEC_Cantidad)
                        FROM cji_ordencompra oc
                        INNER JOIN cji_ocompradetalle ocd ON ocd.OCOMP_Codigo = oc.OCOMP_Codigo
                        WHERE oc.OCOMC_FlagEstado != '0' AND oc.OCOMC_TipoOperacion = 'V' AND oc.OCOMC_FlagTerminadoProceso = '0' AND oc.COMPP_Codigo IN(o.COMPP_Codigo) AND ocd.OCOMDEC_FlagEstado = '1' AND ocd.PROD_Codigo = oc.PROD_Codigo AND oc.ALMAP_Codigo = ap.ALMAC_Codigo AND NOT EXISTS(SELECT cS.OCOMP_Codigo FROM cji_comprobante cS WHERE cS.OCOMP_Codigo = oc.OCOMP_Codigo) AND NOT EXISTS(SELECT g.OCOMP_Codigo FROM cji_guiarem g WHERE g.OCOMP_Codigo = oc.OCOMP_Codigo)
                    ) as pendienteOC,
                    @pendienteGuia := (SELECT SUM(gd.GUIAREMDETC_Cantidad)
                        FROM cji_guiarem g
                        INNER JOIN cji_guiaremdetalle gd ON gd.GUIAREMP_Codigo = g.GUIAREMP_Codigo
                        WHERE g.GUIAREMC_FlagEstado != '0' AND g.GUIAREMC_TipoOperacion = 'V' AND g.COMPP_Codigo IN(o.COMPP_Codigo) AND gd.GUIAREMDETC_FlagEstado = '1' AND gd.PRODCTOP_Codigo = oc.PROD_Codigo AND gd.ALMAP_Codigo = ap.ALMAC_Codigo AND NOT EXISTS(SELECT cgr.GUIAREMP_Codigo FROM cji_comprobante_guiarem cgr WHERE cgr.GUIAREMP_Codigo = g.GUIAREMP_Codigo AND cgr.COMPGUI_FlagEstado = 1)
                     ) as pendienteGuia,
                    @pendienteComprobante := (SELECT SUM(cd.CPDEC_Cantidad)
                        FROM cji_comprobante c
                        INNER JOIN cji_comprobantedetalle cd ON cd.CPP_Codigo = c.CPP_Codigo
                        WHERE c.CPC_FlagEstado = '2' AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo IN(o.COMPP_Codigo) AND cd.CPDEC_FlagEstado = '1' AND cd.PROD_Codigo = oc.PROD_Codigo AND cd.ALMAP_Codigo = ap.ALMAC_Codigo
                    ) as pendienteComprobante

                    FROM cji_ocompradetalle oc
                    INNER JOIN cji_ordencompra o ON o.OCOMP_Codigo = oc.OCOMP_Codigo
                    
                    INNER JOIN cji_lote l ON l.LOTP_Codigo = oc.LOTP_Codigo
                    INNER JOIN cji_almaprolote apl ON apl.LOTP_Codigo = l.LOTP_Codigo
                    INNER JOIN cji_almacenproducto ap ON ap.ALMPROD_Codigo = apl.ALMPROD_Codigo AND ap.COMPP_Codigo = o.COMPP_Codigo AND ap.ALMAC_Codigo = o.ALMAP_Codigo

                    INNER JOIN cji_producto pr ON oc.PROD_Codigo = pr.PROD_Codigo
                    LEFT JOIN cji_marca m ON m.MARCP_Codigo = pr.MARCP_Codigo
                    LEFT JOIN cji_unidadmedida um ON um.UNDMED_Codigo = oc.UNDMED_Codigo
                        WHERE oc.OCOMP_Codigo = '$ocompra' AND oc.OCOMDEC_FlagEstado = 1
                ";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $fila->pendienteOC = ( $fila->pendienteOC == NULL ) ? 0 : $fila->pendienteOC;
                $fila->pendienteGuia = ( $fila->pendienteGuia == NULL ) ? 0 : $fila->pendienteGuia;
                $fila->pendienteComprobante = ( $fila->pendienteComprobante == NULL ) ? 0 : $fila->pendienteComprobante;
                $fila->ALMALOTC_Cantidad = $fila->ALMALOTC_Cantidad - ($fila->pendienteComprobante + $fila->pendienteGuia + $fila->pendienteOC);

                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_referencia_compra_by_id_detalle_venta($id_detalle_venta){
        $result = $this->db->from('cji_ocompradetalle')
                        ->where(array("cji_ocompradetalle.OCOMDEP_Codigo" => $id_detalle_venta, "cji_ocompradetalle.OCOMDEC_FlagEstado" => 1))
                        ->join("cji_ordencompra", "cji_ordencompra.OCOMP_Codigo = cji_ocompradetalle.OCOMP_Codigo", "LEFT")
                        ->join("cji_proyecto", "cji_proyecto.PROYP_Codigo = cji_ordencompra.PROYP_Codigo", "LEFT")
                        ->join("cji_cliente", "cji_cliente.CLIP_Codigo = cji_ordencompra.CLIP_Codigo", "LEFT")
                        ->join("cji_proveedor", "cji_proveedor.PROVP_Codigo = cji_ordencompra.PROVP_Codigo", "LEFT")
                        ->select('cji_ocompradetalle.*, cji_cliente.*, cji_proveedor.*, cji_proyecto.*')->get()->result();

        if(count($result) == 0) return null;

        return $result[0];
    }

    public function obtener_detalle_ocompra2($ocompra)
    {
        $where = array("cji_ocompradetalle.OCOMP_Codigo" => $ocompra, "cji_ocompradetalle.OCOMDEC_FlagEstado" => "1", "cji_ocompradetalle.OCOMDEC_FlagIngreso" => 0);

        $query = $this->db->from("cji_ocompradetalle")->join("cji_ordencompra", "cji_ordencompra.OCOMP_Codigo = cji_ocompradetalle.OCOMP_Codigo")->join("cji_proyecto", "cji_proyecto.PROYP_Codigo = cji_ordencompra.PROYP_Codigo", "LEFT")->where($where)->select("cji_ocompradetalle.*, cji_ordencompra.*, cji_proyecto.*")->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_detalle_ocompra_importacion($ocompra)
    {
        $where = array("cji_ocompradetalle.OCOMP_Codigo" => $ocompra, "cji_ocompradetalle.OCOMDEC_FlagEstado" => "1", "cji_ocompradetalle.OCOMDEC_FlagIngreso" => 0);

        $query = $this->db->from("cji_ocompradetalle")
                            ->join("cji_ordencompra", "cji_ordencompra.OCOMP_Codigo = cji_ocompradetalle.OCOMP_Codigo_venta", "LEFT")
                            ->join("cji_proyecto", "cji_ordencompra.PROYP_Codigo = cji_proyecto.PROYP_Codigo", "LEFT")
                            ->where($where)
                            ->select("cji_ocompradetalle.*, cji_ordencompra.*, cji_proyecto.*")->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar_ocompra($filter = null)
    {
        $compania = $this->somevar['compania'];
        $usuario = $this->somevar ['usuario'];
        #$datos_configuracion = $this->configuracion_model->obtener_numero_documento($compania, '3');
        #$numero = $datos_configuracion[0]->CONFIC_Numero + 1;

        $numero = $filter->OCOMC_Numero;
        $filter->USUA_Codigo = $usuario;
        $filter->COMPP_Codigo = $compania;

        $this->db->insert("cji_ordencompra", (array)$filter);
        $ocompra = $this->db->insert_id();
        #$this->configuracion_model->modificar_configuracion($compania, 3, $numero);
        return $ocompra;
    }

    public function modificar_ocompra($ocompra, $filter = null)
    {
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->update('cji_ordencompra', (array)$filter);
    }

    public function modificar_flagImportado($ocompra, $flag)
    {
        $data = array("OCOMC_FlagTerminado" => $flag);
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->update('cji_ordencompra', $data);
    }

    public function modificar_flagTerminado($ocompra, $flag)
    {
        $data = array("OCOMC_FlagTerminadoProceso" => $flag);
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->update('cji_ordencompra', $data);
    }
     public function modificar_flagTerminado_oventa($ocompra, $flag)
    {
        $data = array("OCOMC_FlagTerminado" => $flag);
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->update('cji_ordencompra', $data);
    }

    public function modificar_detocompra_flagIngreso($ocompra, $producto)
    {
        $data = array("OCOMDEC_FlagIngreso" => 1);
        $where = array("OCOMP_Codigo" => $ocompra, "PROD_Codigo" => $producto);
        $this->db->where($where);
        $this->db->update('cji_ocompradetalle', $data);
    }

    public function modificar_detocompra_flagsIngresos($ocompra)
    {
        $data = array("OCOMDEC_FlagIngreso" => 0);
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->update('cji_ocompradetalle', $data);
    }

    public function modificar_ocompra_flagIngreso($ocompra)
    {
        $where = array("OCOMP_Codigo" => $ocompra, "OCOMDEC_FlagEstado" => "1");
        $query = $this->db->where($where)->get("cji_ocompradetalle");
        $where2 = array("OCOMP_Codigo" => $ocompra, "OCOMDEC_FlagIngreso" => 1, "OCOMDEC_FlagEstado" => "1");
        $query2 = $this->db->where($where2)->get("cji_ocompradetalle");
        if ($query->num_rows() == $query2->num_rows) {
            $this->db->where(array("OCOMP_Codigo" => $ocompra))->update("cji_ordencompra", array("OCOMC_FlagIngreso" => 1));
        } else {
            $this->db->where(array("OCOMP_Codigo" => $ocompra))->update("cji_ordencompra", array("OCOMC_FlagIngreso" => 0));
        }
    }

    public function modificar_ocompra_flagRecibido($ocompra, $numero_factura)
    {
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where)->update("cji_ordencompra", array("OCOMC_FlagRecibido" => 1, "OCOMC_NumeroFactura" => $numero_factura));
    }

    public function eliminar($ocompra)
    {
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->delete('cji_ocompradetalle');
        $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->delete('cji_ordencompra');
    }

    public function evaluar_ocompra($flag, $checkO)
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

    public function obtenerOrdenCompra(stdClass $filter = NULL, $offset = '', $number_items = ''){

        $compania = $this->somevar['compania'];
        $union = '';
        $where = '';

        if ($filter->tipo_oper != 'C') {
            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '') {
                $where .= ' AND pe.PERSC_Nombre LIKE "%' . $filter->nombre_cliente .'%"';
                $where .= ' OR pe.PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_cliente .'%"';
                $where .= ' OR e.EMPRC_RazonSocial like "%' . $filter->nombre_cliente . '%"';
                $where .= ' OR cli.CLIC_CodigoUsuario like "%' . $filter->nombre_cliente . '%"';
            }
            if(isset($filter->ruc_cliente) && $filter->ruc_cliente != ''){
                $where .= ' AND pe.PERSC_NumeroDocIdentidad like "%' . $filter->ruc_cliente . '%"';
                $where .= ' OR e.EMPRC_Ruc like "%' . $filter->ruc_cliente . '%"';
            }
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '') {
                $where .= ' AND pe.PERSC_Nombre LIKE "%' . $filter->proveedor .'%"';
                $where .= ' OR pe.PERSC_ApellidoPaterno LIKE "%' . $filter->proveedor .'%"';
                $where .= ' OR e.EMPRC_RazonSocial like "%' . $filter->proveedor . '%"';
            }
            if(isset($filter->ruc_proveedor) && $filter->ruc_proveedor != ''){
                $where .= ' AND e.EMPRC_Ruc like "%' . $filter->ruc_proveedor . '%"';
                $where .= ' OR pe.PERSC_NumeroDocIdentidad like "%' . $filter->ruc_proveedor . '%"';
            }
        }

        if($filter->fechai != "" && $filter->fechaf == "")
            $where .= " AND o.OCOMC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '".date("Y-m-d")." 23:59:59'";
        else
            if ($filter->fechai != "" && $filter->fechaf != "")
                $where .= " AND o.OCOMC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '$filter->fechaf 23:59:59'";

        if ($filter->producto != '')
            $where .= ' AND od.PROD_Codigo = ' . $filter->producto;
        
        if ($filter->aprobado != '')
            $where .= ' AND o.OCOMC_FlagAprobado=' . $filter->aprobado;
        
        if ($filter->ingreso != '')
            $where .= ' AND o.OCOMC_FlagIngreso=' . $filter->ingreso;

        if ($filter->vendedor != '')
            $where .= ' AND o.OCOMC_MiPersonal=' . $filter->vendedor;

        if ($filter->empleado != ''){
            $union = " INNER JOIN cji_directivo d ON d.PERSP_Codigo = o.OCOMC_MiPersonal ";
            $where .= " AND d.DIREC_CodigoEmpleado LIKE '%$filter->empleado%' ";
        }
        
        $limit = "";
        if ((string)$offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        /*
            (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                FROM cji_cliente cc
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
                LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
                WHERE cc.CLIP_Codigo = o.CLIP_Codigo
            ) as razon_social_cliente,
            (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
                FROM cji_cliente cc
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
                LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
                WHERE cc.CLIP_Codigo = o.CLIP_Codigo
            ) as numero_documento_cliente,
            (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                FROM cji_proveedor pp
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
                LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
                WHERE pp.PROVP_Codigo = o.PROVP_Codigo
            ) as razon_social_proveedor,
            (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
                FROM cji_proveedor pp
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
                LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
                WHERE pp.PROVP_Codigo = o.PROVP_Codigo
            ) as numero_documento_proveedor
        */

        $sql = "SELECT DATE_FORMAT(o.OCOMC_FechaRegistro, '%d/%m/%Y') fecha, o.*,
                    (SELECT GUIAREMP_Codigo FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as GUIAREMP_Codigo,
                    (SELECT CONCAT_WS('-', gr.GUIAREMC_Serie, gr.GUIAREMC_Numero) FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as GUIAREMC_SerieNumero,
                    (SELECT CPP_Codigo FROM cji_comprobante c WHERE c.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as CPP_Codigo,
                    (SELECT CONCAT_WS('-', c.CPC_Serie, c.CPC_Numero) FROM cji_comprobante c WHERE c.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as CPC_SerieNumero,
                    (CASE WHEN o.COTIP_Codigo = 0 THEN '***' ELSE CAST(ct.COTIC_Numero AS char) END) cotizacion,

                    (SELECT CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                        FROM cji_persona p
                        WHERE p.PERSP_Codigo = o.OCOMC_MiPersonal
                    ) as vendedor,

                    m.MONED_Simbolo,
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
                    END) ingreso

                    FROM cji_ordencompra o
                    INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo = o.OCOMP_Codigo
                    $union
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = o.MONED_Codigo
                    LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = o.CLIP_Codigo
                    LEFT JOIN cji_proveedor pv ON pv.PROVP_Codigo = o.PROVP_Codigo

                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = cli.PERSP_Codigo OR pe.PERSP_Codigo = pv.PERSP_Codigo
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cli.EMPRP_Codigo OR e.EMPRP_Codigo = pv.EMPRP_Codigo

                    LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                    WHERE o.OCOMC_TipoOperacion = '$filter->tipo_oper' AND o.COMPP_Codigo = '$compania' $where
                    GROUP BY o.OCOMP_Codigo
                    ORDER BY o.OCOMC_Numero DESC " . $limit . "
                ";

        /*
            $sql = "SELECT DATE_FORMAT(o.OCOMC_FechaRegistro, '%d/%m/%Y') fecha,
                        o.*,
                        (SELECT GUIAREMP_Codigo FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as GUIAREMP_Codigo,
                        (SELECT CONCAT_WS('-', gr.GUIAREMC_Serie, gr.GUIAREMC_Numero) FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as GUIAREMC_SerieNumero,
                        (SELECT CPP_Codigo FROM cji_comprobante c WHERE c.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as CPP_Codigo,
                        (SELECT CONCAT_WS('-', c.CPC_Serie, c.CPC_Numero) FROM cji_comprobante c WHERE c.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as CPC_SerieNumero,

                           (CASE WHEN o.COTIP_Codigo = 0 THEN '***' ELSE CAST(ct.COTIC_Numero AS char) END) cotizacion,
                        (CASE " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                        (CASE " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                      m.MONED_Simbolo,
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
                        END) ingreso
                FROM cji_ordencompra o
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=o.MONED_Codigo
                INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo=o.OCOMP_Codigo

                " . ($filter->tipo_oper != 'C' ? "INNER JOIN cji_cliente p ON p.CLIP_Codigo= o.CLIP_Codigo" : "LEFT JOIN cji_proveedor p ON p.PROVP_Codigo= o.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . " = '0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND " . ($filter->tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "= '1'

                LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                WHERE o.OCOMC_FlagBS = 'B' " . $where . " AND o.OCOMC_TipoOperacion='" . $filter->tipo_oper . "'
                AND o.COMPP_Codigo = '" . $compania . "'
                GROUP BY o.OCOMP_Codigo
                ORDER BY o.OCOMC_Numero DESC " . $limit . "
                ";
        */

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

  public function getOcompra($filter = NULL){
    $compania = $this->somevar['compania'];

    $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
    $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

    $where = "";
    $union = '';

    ## FILTROS
		  if ($filter->tipo_oper != 'C') {
		      if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '')
		        $where .= " AND CONCAT_WS(' ',cli.CLIC_CodigoUsuario, pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, e.EMPRC_RazonSocial) LIKE '%".$filter->nombre_cliente."%'";
		      if(isset($filter->ruc_cliente) && $filter->ruc_cliente != '')
		        $where .= " AND CONCAT_WS(' ',pe.PERSC_NumeroDocIdentidad, e.EMPRC_Ruc) LIKE '%".$filter->ruc_cliente."%'";
		  }
		  else {
		      if (isset($filter->proveedor) && $filter->proveedor != '')
		        $where .= " AND CONCAT_WS(' ', pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, e.EMPRC_RazonSocial) LIKE '%".$filter->proveedor."%'";

		      if(isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '')
		        $where .= " AND CONCAT_WS(' ',pe.PERSC_NumeroDocIdentidad, e.EMPRC_Ruc) LIKE '%".$filter->ruc_proveedor."%'";
		  }

		  if($filter->fechai != "" && $filter->fechaf == "")
		      $where .= " AND o.OCOMC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '".date("Y-m-d")." 23:59:59'";
		  else
		      if ($filter->fechai != "" && $filter->fechaf != "")
		          $where .= " AND o.OCOMC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '$filter->fechaf 23:59:59'";

		  if ($filter->producto != ''){
		  	$union .= " INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo = o.OCOMP_Codigo ";
		    $where .= ' AND od.PROD_Codigo = ' . $filter->producto;
		  }
		  
		  if ($filter->aprobado != '')
		      $where .= ' AND o.OCOMC_FlagAprobado = ' . $filter->aprobado;
		  
		  if ($filter->ingreso != '')
		      $where .= ' AND o.OCOMC_FlagIngreso = ' . $filter->ingreso;

		  if ($filter->vendedor != '')
		      $where .= ' AND o.OCOMC_MiPersonal = ' . $filter->vendedor;

		  if ($filter->empleado != ''){
		      $union .= " INNER JOIN cji_directivo d ON d.PERSP_Codigo = o.OCOMC_MiPersonal ";
		      $where .= " AND d.DIREC_CodigoEmpleado LIKE '%$filter->empleado%' ";
		  }
		## END Filtros

		# El datatable ejecuta esta consulta 3 veces.
		# 	1 -> Obtiene la lista con resultados
		# 	2 -> Obtiene el total de registros filtrados
		# 	3 -> Obtiene el total de registros en la tabla
		# En el caso 2 y 3, es mejor ejecutar la consulta pasando un COUNT() solamente

    if ( isset($filter->count) && $filter->count == true ){
    	$sql = "SELECT COUNT(*) as registros
              FROM cji_ordencompra o
              $union
              LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = o.CLIP_Codigo
              LEFT JOIN cji_proveedor pv ON pv.PROVP_Codigo = o.PROVP_Codigo
              LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = cli.PERSP_Codigo OR pe.PERSP_Codigo = pv.PERSP_Codigo
              LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cli.EMPRP_Codigo OR e.EMPRP_Codigo = pv.EMPRP_Codigo
              LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
              WHERE o.OCOMC_TipoOperacion LIKE '$filter->tipo_oper' AND o.COMPP_Codigo = '$compania'
              $where
            ";
    }
    else{
    	$sql = "SELECT DATE_FORMAT(o.OCOMC_FechaRegistro, '%d/%m/%Y') fecha, o.*,
                (SELECT GUIAREMP_Codigo FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as GUIAREMP_Codigo,
                (SELECT CONCAT_WS('-', gr.GUIAREMC_Serie, gr.GUIAREMC_Numero) FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as GUIAREMC_SerieNumero,
                (SELECT gr.GUIAREMC_FlagEstado FROM cji_guiarem gr WHERE gr.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as GUIAREMC_FlagEstado,

                (SELECT CPP_Codigo FROM cji_comprobante c WHERE c.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as CPP_Codigo,
                (SELECT CONCAT_WS('-', c.CPC_Serie, c.CPC_Numero) FROM cji_comprobante c WHERE c.OCOMP_Codigo = o.OCOMP_Codigo LIMIT 1) as CPC_SerieNumero,
                (CASE WHEN o.COTIP_Codigo = 0 THEN '***' ELSE CAST(ct.COTIC_Numero AS char) END) cotizacion,

                cli.CLIC_CodigoUsuario,
                CONCAT_WS(' ', e.EMPRC_Ruc, pe.PERSC_NumeroDocIdentidad) as rucDni,
                CONCAT_WS(' ', e.EMPRC_RazonSocial, pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, pe.PERSC_ApellidoMaterno) as nombre,

                (SELECT CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                    FROM cji_persona p
                    WHERE p.PERSP_Codigo = o.OCOMC_MiPersonal
                ) as vendedor,

                (SELECT m.MONED_Simbolo FROM cji_moneda m WHERE m.MONED_Codigo = o.MONED_Codigo) MONED_Simbolo,
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

                (SELECT SUM(ocd.OCOMDEC_Cantidad)
                	FROM cji_ocompradetalle ocd
                	WHERE ocd.OCOMP_Codigo = o.OCOMP_Codigo
                		AND ocd.OCOMDEC_FlagEstado LIKE '1'
                ) productos_cotizados,

                (SELECT SUM(gd.GUIAREMDETC_Cantidad)
                	FROM cji_guiaremdetalle gd
                	WHERE gd.GUIAREMDETC_FlagEstado LIKE '1'
                		AND EXISTS(SELECT gr.GUIAREMP_Codigo
                									FROM cji_guiarem gr
                									WHERE gr.GUIAREMP_Codigo = gd.GUIAREMP_Codigo
                										AND gr.OCOMP_Codigo = o.OCOMP_Codigo
                										AND gr.GUIAREMC_FlagEstado <> '0')
                ) productos_guia,

                (SELECT SUM(cd.CPDEC_Cantidad)
                	FROM cji_comprobantedetalle cd
                	WHERE cd.CPDEC_FlagEstado LIKE '1'
                		AND EXISTS(SELECT cd.CPP_Codigo
                									FROM cji_comprobante c
                									WHERE c.CPP_Codigo = cd.CPP_Codigo
                										AND c.OCOMP_Codigo = o.OCOMP_Codigo
                										AND c.CPC_FlagEstado <> '0')
                ) productos_comprobante
              
              FROM cji_ordencompra o
              $union
              LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = o.CLIP_Codigo
              LEFT JOIN cji_proveedor pv ON pv.PROVP_Codigo = o.PROVP_Codigo
              LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = cli.PERSP_Codigo OR pe.PERSP_Codigo = pv.PERSP_Codigo
              LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cli.EMPRP_Codigo OR e.EMPRP_Codigo = pv.EMPRP_Codigo
              LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
              WHERE o.OCOMC_TipoOperacion LIKE '$filter->tipo_oper' AND o.COMPP_Codigo = '$compania'
              $where                  
              $order
              $limit
            ";
    }

    $query = $this->db->query($sql);

    if ($query->num_rows() > 0) {
    	if ( isset($filter->count) && $filter->count == true )
      	return $query->row();
      else
      	return $query->result();
    }
    else
    	return NULL;
  }

    public function obtenerOrdenCompraFiltro(stdClass $filter = NULL){

        $compania = $this->somevar['compania'];
        $where = '';

        if ($filter->tipo_oper != 'C') {
            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '') {
                $where .= ' AND pe.PERSC_Nombre LIKE "%' . $filter->nombre_cliente .'%"';
                $where .= ' OR pe.PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_cliente .'%"';
                $where .= ' OR e.EMPRC_RazonSocial like "%' . $filter->nombre_cliente . '%"';
                $where .= ' OR cli.CLIC_CodigoUsuario like "%' . $filter->nombre_cliente . '%"';
            }
            if(isset($filter->ruc_cliente) && $filter->ruc_cliente != ''){
                $where .= ' AND pe.PERSC_NumeroDocIdentidad like "%' . $filter->ruc_cliente . '%"';
                $where .= ' OR e.EMPRC_Ruc like "%' . $filter->ruc_cliente . '%"';
            }
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '') {
                $where .= ' AND pe.PERSC_Nombre LIKE "%' . $filter->proveedor .'%"';
                $where .= ' OR pe.PERSC_ApellidoPaterno LIKE "%' . $filter->proveedor .'%"';
                $where .= ' OR e.EMPRC_RazonSocial like "%' . $filter->proveedor . '%"';
            }
            if(isset($filter->ruc_proveedor) && $filter->ruc_proveedor != ''){
                $where .= ' AND e.EMPRC_Ruc like "%' . $filter->ruc_proveedor . '%"';
                $where .= ' OR pe.PERSC_NumeroDocIdentidad like "%' . $filter->ruc_proveedor . '%"';
            }
        }

        if($filter->fechai != "" && $filter->fechaf == "")
            $where .= " AND o.OCOMC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '".date("Y-m-d")." 23:59:59'";
        else
            if ($filter->fechai != "" && $filter->fechaf != "")
                $where .= " AND o.OCOMC_Fecha BETWEEN '$filter->fechai 00:00:00' AND '$filter->fechaf 23:59:59'";

        if ($filter->producto != '')
            $where .= ' AND od.PROD_Codigo = ' . $filter->producto;
        
        if ($filter->aprobado != '')
            $where .= ' AND o.OCOMC_FlagAprobado=' . $filter->aprobado;
        
        if ($filter->ingreso != '')
            $where .= ' AND o.OCOMC_FlagIngreso=' . $filter->ingreso;

        if ($filter->vendedor != '')
            $where .= ' AND o.OCOMC_MiPersonal=' . $filter->vendedor;
        
        $sql = "SELECT o.OCOMP_Codigo
                    FROM cji_ordencompra o
                    INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo = o.OCOMP_Codigo
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = o.MONED_Codigo
                    LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = o.CLIP_Codigo
                    LEFT JOIN cji_proveedor pv ON pv.PROVP_Codigo = o.PROVP_Codigo

                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo = cli.PERSP_Codigo OR pe.PERSP_Codigo = pv.PERSP_Codigo
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cli.EMPRP_Codigo OR e.EMPRP_Codigo = pv.EMPRP_Codigo

                    LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                    WHERE o.OCOMC_TipoOperacion = '$filter->tipo_oper' AND o.COMPP_Codigo = '$compania' $where
                    GROUP BY o.OCOMP_Codigo
                    ORDER BY o.OCOMC_Numero DESC
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

    public function buscar_ocompra($tipo_oper, $fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso, $number_items = '', $offset = '')
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
                WHERE o.OCOMC_FlagEstado=1 " . $where . " AND o.OCOMC_TipoOperacion='" . $tipo_oper . "'
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

    public function buscar_ocompra_asoc($tipo_oper = 'V', $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '')
    {
        $where = '';
        $compania = $this->somevar['compania'];

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
                        o.OCOMP_Codigo, o.PEDIP_Codigo, o.OCOMC_Fecha, o.CLIP_Codigo, o.OCOMC_TipoOperacion, o.OCOMC_FlagBS,
                       (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       o.CENCOSP_Codigo, o.OCOMC_Numero, o.OCOMC_Serie, m.MONED_Simbolo,
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
                        o.OCOMC_FlagEstado, o.OCOMC_MiPersonal, o.MONED_Codigo, o.OCOMC_descuento100, o.OCOMC_FactDireccion, o.PROYP_Codigo, o.OCOMC_PersonaAutorizada
                FROM cji_ordencompra o
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=o.MONED_Codigo
                INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo=o.OCOMP_Codigo
               " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente p ON p.CLIP_Codigo= o.CLIP_Codigo" : "LEFT JOIN cji_proveedor p ON p.PROVP_Codigo= o.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "='1'
                 
                 LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                 WHERE o.OCOMC_FlagEstado = '1' " . $where . " AND o.OCOMC_TipoOperacion='" . $tipo_oper . "' AND (o.OCOMC_FlagTerminadoProceso = '0' OR o.OCOMP_Codigo = (SELECT max(OCOMP_Codigo) FROM cji_comprobante WHERE OCOMP_Codigo = o.OCOMP_Codigo AND CPC_FlagEstado = 0 ) )
                 AND o.COMPP_Codigo = '" . $compania . "'
                GROUP BY o.OCOMP_Codigo
                ORDER BY o.OCOMP_Codigo DESC " . $limit . "
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

    public function getOcompraAsoc($filter = NULL){

        $compania = $this->somevar['compania'];
        $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
        $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

        $tipo_docu = $filter->tipo_docu;
        $tipo_oper = $filter->tipo_oper;

        $where = '';

        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= ' AND o.CLIP_Codigo=' . $filter->cliente;
        }
        else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= ' AND o.PROVP_Codigo=' . $filter->proveedor;
        }

        if (isset($filter->fecha) && $filter->fecha != '')
            $where .= " AND o.OCOMC_Fecha BETWEEN '$filter->fecha 00:00:00' AND '$filter->fecha 23:59:59' ";

        if (isset($filter->serie) && $filter->serie != '')
            $where .= " AND o.OCOMC_Serie LIKE '%$filter->serie%' ";

        if (isset($filter->numero) && $filter->numero != '')
            $where .= " AND o.OCOMC_Numero = '$filter->numero' ";

        $sql = "SELECT o.OCOMP_Codigo, o.PEDIP_Codigo, o.OCOMC_Fecha, o.CLIP_Codigo, o.OCOMC_TipoOperacion, o.OCOMC_FlagBS,
                       (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       o.CENCOSP_Codigo, o.OCOMC_Numero, o.OCOMC_Serie, m.MONED_Simbolo,
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
                        o.OCOMC_FlagEstado, o.OCOMC_MiPersonal, o.MONED_Codigo, o.OCOMC_descuento100, o.OCOMC_FactDireccion, o.PROYP_Codigo, o.OCOMC_PersonaAutorizada
                    FROM cji_ordencompra o
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo=o.MONED_Codigo
                    INNER JOIN cji_ocompradetalle od ON od.OCOMP_Codigo=o.OCOMP_Codigo
                   " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente p ON p.CLIP_Codigo= o.CLIP_Codigo" : "LEFT JOIN cji_proveedor p ON p.PROVP_Codigo= o.PROVP_Codigo") . "
                    LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . " ='0'
                    LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "p.CLIC_TipoPersona" : "p.PROVC_TipoPersona") . "='1'
                     
                     LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                     WHERE o.OCOMC_FlagEstado = '1' $where AND o.OCOMC_TipoOperacion LIKE '$tipo_oper' AND (o.OCOMC_FlagTerminadoProceso = '0' OR o.OCOMP_Codigo = (SELECT max(OCOMP_Codigo) FROM cji_comprobante WHERE OCOMP_Codigo = o.OCOMP_Codigo AND CPC_FlagEstado = 0 ) )
                     AND o.COMPP_Codigo = '$compania'

                    GROUP BY o.OCOMP_Codigo
                    $order
                    $limit
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

    public function getOcompraMail($ocompra){

        $sql = "SELECT o.*, m.MONED_Simbolo,
                    (SELECT e.EMPRP_Codigo 
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = o.CLIP_Codigo
                    ) as empresa,

                    (SELECT e.EMPRC_Email 
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = o.CLIP_Codigo
                    ) as email,

                    (SELECT CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, e.EMPRC_RazonSocial)
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = o.CLIP_Codigo
                    ) as razon_social,

                    (SELECT CONCAT_WS(' ', p.PERSC_NumeroDocIdentidad, e.EMPRC_Ruc)
                            FROM cji_cliente c
                            LEFT JOIN cji_persona p ON p.PERSP_Codigo = c.PERSP_Codigo
                            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = c.EMPRP_Codigo
                            WHERE c.CLIP_Codigo = o.CLIP_Codigo
                    ) as ruc
                    
                    FROM cji_ordencompra o
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = o.MONED_Codigo
                    WHERE o.OCOMP_Codigo = $ocompra
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

    public function buscar_ocompra_importacion($tipo_oper = 'V', $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '')
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
                 WHERE o.OCOMC_FlagEstado='1' " . $where . " AND o.OCOMC_TipoOperacion='" . $tipo_oper . "' AND o.OCOMC_FlagTerminado = '0'
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

    public function buscar_oventa_asoc($tipo_oper = 'V', $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '')
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
                 WHERE o.OCOMC_FlagTerminado='0' AND o.OCOMC_FlagEstado='1' " . $where . " AND o.OCOMC_TipoOperacion='" . $tipo_oper . "'
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


    public function reporte_ocompra_cantidad_x_mes()
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

    public function reporte_ocompra_5_prov_mas_importantes()
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

    public function reporte_ocompra_monto_x_mes()
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
    public function listar_ocompras_pdf($tipo_oper,$fecha_ini, $fecha_fin,$codigo,$nombre){
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
   /*  $where = array("OCOMP_Codigo" => $ocompra);
        $this->db->where($where);
        $this->db->update('cji_ordencompra', (array)$filter);
*/  
    public function esta_importado($oc){
       $sql = "
    SELECT od.OCOMDEC_Pendiente,o.OCOMP_Codigo
FROM cji_ordencompra o
inner join cji_ocompradetalle od on o.OCOMP_Codigo=od.OCOMP_Codigo
WHERE o.OCOMP_Codigo=$oc and od.OCOMDEC_Pendiente>0";

    $query = $this->db->query($sql);
    
                if($query->num_rows() > 0){
                    return FALSE;
                }else{
                    return TRUE;
                }

          
    }
     public function modificar_pendiente_importacion($ocompra,$producto,$cantidad){


        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente=OCOMDEC_Pendiente-$cantidad
        WHERE OCOMP_Codigo=$ocompra and OCOMDEP_Codigo=$producto
        ";
        $query=$this->db->query($sql);

    }

    public function modificar_pendiente_importacion_by_id_detalle_compra($id_detalle_compra,$cantidad){


        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente=OCOMDEC_Pendiente-$cantidad
        WHERE OCOMDEP_Codigo = $id_detalle_compra
        ";
        $query=$this->db->query($sql);

    }

     public function modificar_pendiente($ocompra,$producto,$cantidad){
       /*/ $data      = array("OCOMDEC_Pendiente"=>"OCOMDEC_Pendiente"- $cantidad);
        $where =array("OCOMP_Codigo" => $ocompra,
                      "PROD_Codigo" => $producto);
        $this->db->where($where); 
        $this->db->update('cji_ocompradetalle1',$data);*/

        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente=OCOMDEC_Pendiente-$cantidad
        WHERE OCOMP_Codigo=$ocompra and PROD_Codigo=$producto
        ";
        $query=$this->db->query($sql);

    }
    public function modificar_pendientecomprobante($ocompra,$cantidad){

        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente_pago=OCOMDEC_Pendiente_pago-$cantidad
        WHERE OCOMDEP_Codigo=$ocompra 
        ";
        $query=$this->db->query($sql);

    }

    public function modificar_pendiente_pago_by_id_detalle_venta($id_detalle_venta,$cantidad){

        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente_pago=OCOMDEC_Pendiente_pago-$cantidad
        WHERE OCOMDEP_Codigo = $id_detalle_venta
        ";
        $query=$this->db->query($sql);

    }

    public function modificar_pendienteoventa($ocompra,$producto,$cantidad){
        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente=OCOMDEC_Pendiente-$cantidad
        WHERE OCOMP_Codigo=$ocompra and PROD_Codigo=$producto
        ";
        $query=$this->db->query($sql);

    }

    public function modificar_pendiente_by_id_detalle_venta($id_detalle_venta, $cantidad){
        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente=OCOMDEC_Pendiente - $cantidad
        WHERE OCOMDEP_Codigo=$id_detalle_venta";
        $query=$this->db->query($sql);

    }

    public function modificar_pendienteoventa_comprobante($ocompra,$cantidad){

        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente_pago=OCOMDEC_Pendiente_pago-$cantidad
        WHERE OCOMP_Codigo=$ocompra 
        ";
        $query=$this->db->query($sql);

    }
    public function modificar_pendiente_cantidad($ocompra,$producto,$cantidad){
        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente=OCOMDEC_Pendiente+$cantidad
        WHERE OCOMP_Codigo=$ocompra and PROD_Codigo=$producto
        ";
        $query=$this->db->query($sql);

    }

    public function modificar_pendiente_cantidad_id_detalle_venta($id_detalle_venta, $cantidad){
        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente = OCOMDEC_Pendiente + $cantidad
        WHERE OCOMDEP_Codigo = $id_detalle_venta";
        $query=$this->db->query($sql);

    }

    public function modificar_pendiente_cantidad_id_detalle_compra($id_detalle_compra, $cantidad){
        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente = OCOMDEC_Pendiente + $cantidad
        WHERE OCOMDEP_Codigo = $id_detalle_compra";
        $query=$this->db->query($sql);

    }

     public function modificar_pendiente_cantidad_comprobante($ocompra,$cantidad){
        $sql="
        UPDATE cji_ocompradetalle set OCOMDEC_Pendiente_pago=OCOMDEC_Pendiente_pago+$cantidad
        WHERE OCOMDEP_Codigo=$ocompra 
        ";
        $query=$this->db->query($sql);

    }
     public function calcula_ocantidad_pendiente($codigo,$prod)
        {
        $sql = "
         SELECT OCOMDEC_Cantidad,OCOMDEC_Pendiente
        FROM cji_ocompradetalle
        WHERE OCOMP_Codigo=$codigo and PROD_Codigo=$prod";

                $query = $this->db->query($sql);
                $respuesta = $query->result();
                //var_dump($respuesta[0]);
                return $respuesta[0];
           
        }

        public function calcula_ocantidad_pendiente_by_id_detalle($codigo)
        {
        $sql = "
         SELECT OCOMDEC_Cantidad,OCOMDEC_Pendiente
        FROM cji_ocompradetalle
        WHERE OCOMDEP_Codigo=$codigo";

                $query = $this->db->query($sql);
                $respuesta = $query->result();
                //var_dump($respuesta[0]);
                return $respuesta[0];
           
        }

    public function calcula_ocantidad_pendiente_compro($codigo,$prod)
        {
        $sql = "
         SELECT OCOMDEC_Cantidad,OCOMDEC_Pendiente_pago
        FROM cji_ocompradetalle
        WHERE OCOMDEP_Codigo=$codigo and PROD_Codigo=$prod";

                $query = $this->db->query($sql);
                $respuesta = $query->result();
                //var_dump($respuesta[0]);
                return $respuesta[0];
           
        }

    public function verificar_ocantidad($codigo,$prod)
    {
    $sql = "
    SELECT OCOMDEC_Cantidad,OCOMDEC_Pendiente_pago,OCOMDEC_Pendiente
FROM cji_ocompradetalle
WHERE OCOMDEP_Codigo=$codigo and PROD_Codigo=$prod";


            $query = $this->db->query($sql);
            $respuesta = $query->result();
            //var_dump($respuesta[0]);
            return $respuesta[0];
           
        }
public function listar_by_id_proyecto_tipo($id_proyecto, $tipo)
    {
        $ordenes = array();

        $result = $this->db->from("cji_ordencompra")
                            ->join("cji_ocompradetalle", "cji_ordencompra.OCOMP_Codigo = cji_ocompradetalle.OCOMP_Codigo")
                            ->where("cji_ordencompra.PROYP_Codigo", $id_proyecto)
                            ->where("cji_ordencompra.OCOMC_FlagEstado", 1)
                            ->where("cji_ocompradetalle.OCOMDEC_FlagEstado", 1)
                            ->select("cji_ocompradetalle.OCOMDEP_Codigo")->get()->result();

        foreach ($result as $orden) {
            $ordenes[] = $orden->OCOMDEP_Codigo;
        }

        if(count($ordenes) == 0) return array();

        $selects = array('cji_ordencompra.*', "cji_moneda.*", "cji_persona.*","cji_empresa.*");
        $query = $this->db->from("cji_ordencompra");

        if($tipo == 'ov') {
            $query->join('cji_cliente', "cji_cliente.CLIP_Codigo = cji_ordencompra.CLIP_Codigo", "LEFT");

            $selects[] = "cji_cliente.*";
        }else{
            $query->join('cji_proveedor', "cji_proveedor.PROVP_Codigo = cji_ordencompra.PROVP_Codigo", "LEFT");
            $query->join('cji_ocompradetalle', "cji_ocompradetalle.OCOMP_Codigo = cji_ordencompra.OCOMP_Codigo", "LEFT");

            $selects[] = "cji_proveedor.*";
            $selects[] = "cji_ocompradetalle.*";
        }

        $query->join('cji_moneda', "cji_moneda.MONED_Codigo = cji_ordencompra.MONED_Codigo", "LEFT");
        $query->join('cji_persona', "cji_persona.PERSP_Codigo = " . ($tipo =='ov' ? "cji_cliente" : "cji_proveedor") . ".PERSP_Codigo", "LEFT");
        $query->join('cji_empresa', "cji_empresa.EMPRP_Codigo = " . ($tipo =='ov' ? "cji_cliente" : "cji_proveedor") . ".EMPRP_Codigo", "LEFT");

        $query->where('cji_ordencompra.OCOMC_TipoOperacion', $tipo == 'ov' ? 'V' : 'C');
        $query->where('cji_ordencompra.OCOMC_FlagEstado', 1);

        if($tipo == 'ov') {
            $query->where('cji_ordencompra.PROYP_Codigo', $id_proyecto);
        }else {
            $query->select_sum('cji_ocompradetalle.OCOMDEC_Subtotal', 'OCOMDEC_Subtotal_calculado');
            $query->where('cji_ordencompra.OCOMC_FlagBS', $tipo == "oc" ? 'B' : 'S');
            $query->where_in('cji_ocompradetalle.OCOMP_Codigo_venta', $ordenes);
            $query->group_by('cji_ocompradetalle.OCOMP_Codigo');
            $query->where("cji_ocompradetalle.OCOMDEC_FlagEstado", 1);
        }


        return $query->select(implode(',', $selects))->get()->result();
    }

    public function get_last_price_by_id_product($id_product)
    {
        return $this->db->from("cji_ocompradetalle")
                        ->select("cji_ocompradetalle.OCOMDEC_Pu")
                        ->join("cji_ordencompra", "cji_ordencompra.OCOMP_Codigo = cji_ocompradetalle.OCOMP_Codigo")
                        ->where("cji_ocompradetalle.PROD_Codigo", $id_product)
                        ->where("cji_ocompradetalle.OCOMDEC_FlagEstado", 1)
                        ->where("cji_ordencompra.OCOMC_TipoOperacion", 'C')
                        ->where("cji_ordencompra.OCOMC_FlagEstado", 1)
                        ->order_by("cji_ocompradetalle.OCOMDEP_Codigo", "DESC")
                        ->limit(1)
                        ->get()->result();   
    }

    public function get_by_id($id)
    {
        $result = $this->db->from("cji_ordencompra")
                            ->where("cji_ordencompra.OCOMP_Codigo", $id)
                            ->get()->result();

        if(count($result) == 0) return NULL;

        return $result[0];
    }

    public function listar_clientes_cotizacion($codigo = 3){

        // ECONC_Persona = PERSP_Codigo

        $sql = "SELECT cji_emprcontacto.ECONC_Persona, cji_emprcontacto.ECONC_Telefono, cji_emprcontacto.ECONC_Movil, cji_emprcontacto.ECONC_Email,
                cji_directivo.DIREP_Codigo,
                cji_persona.PERSC_Nombre, cji_persona.PERSC_ApellidoPaterno, cji_persona.PERSC_ApellidoMaterno
        FROM cji_emprcontacto INNER JOIN cji_directivo ON cji_emprcontacto.EMPRP_Codigo = cji_directivo.EMPRP_Codigo
            INNER JOIN cji_persona ON cji_persona.PERSP_Codigo = cji_emprcontacto.ECONC_Persona
        WHERE cji_emprcontacto.EMPRP_Codigo = $codigo GROUP BY cji_emprcontacto.ECONC_Persona";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query;
        else
            return NULL;
    }

    public function detalles_persona($id){

        $sql = "cji_persona.PERSC_ApellidoMaterno
        FROM cji_emprcontacto INNER JOIN cji_directivo ON cji_emprcontacto.EMPRP_Codigo = cji_directivo.EMPRP_Codigo
            INNER JOIN cji_persona ON cji_persona.PERSP_Codigo = cji_emprcontacto.ECONC_Persona
        WHERE cji_emprcontacto.EMPRP_Codigo = $codigo GROUP BY cji_emprcontacto.ECONC_Persona";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
            return $query;
        else
            return NULL;
    }
}
?>