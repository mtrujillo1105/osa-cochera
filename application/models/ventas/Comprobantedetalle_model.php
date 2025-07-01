<?php
class Comprobantedetalle_model extends CI_Model{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    
    public function listar($comprobante){
        $where = array("CPP_Codigo"=>$comprobante,"CPDEC_FlagEstado"=>"1");
        $query = $this->db->order_by('CPDEP_Codigo')->where($where)->get('cji_comprobantedetalle');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener($id){
        $sql = "SELECT cd.*
                    FROM cji_comprobantedetalle cd
                        WHERE cd.CPDEP_Codigo = $id
                ";

        $query = $this->db->query($sql);
        
        if( $query->num_rows() > 0 ){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
        else
            return NULL;
    }

    public function detalles($comprobante){
        $sql = "SELECT cd.*, pr.PROD_CodigoInterno, pr.PROD_CodigoUsuario, pr.PROD_CodigoOriginal, pr.PROD_FlagBienServicio, pr.PROD_Nombre, um.UNDMED_Simbolo, m.MARCC_CodigoUsuario, m.MARCC_Descripcion,
                    l.LOTC_Numero, l.LOTC_FechaVencimiento

                    FROM cji_comprobantedetalle cd
                    INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                    INNER JOIN cji_producto pr ON cd.PROD_Codigo = pr.PROD_Codigo
                    LEFT JOIN cji_marca m ON m.MARCP_Codigo = pr.MARCP_Codigo
                    LEFT JOIN cji_unidadmedida um ON um.UNDMED_Codigo = cd.UNDMED_Codigo
                    LEFT JOIN cji_lote l ON l.LOTP_Codigo = cd.LOTP_Codigo
                        WHERE cd.CPP_Codigo = $comprobante AND cd.CPDEC_FlagEstado = 1
                ";

        $query = $this->db->query($sql);
        
        if( $query->num_rows() > 0 ){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
        else
            return NULL;
    }

    public function insertar($filter=null){
        return $this->db->insert('cji_comprobantedetalle',(array)$filter);
    }
     public function modificar($comprobante_detalle,$filter=null)
    {
        $where = array("CPDEP_Codigo"=>$comprobante_detalle);
        $this->db->where($where);
        $this->db->update('cji_comprobantedetalle',(array)$filter);
    }
    public function eliminar($comprobante_detalle)
    {
        $data      = array("CPDEC_FlagEstado"=>'0');
        $where = array("CPDEP_Codigo"=>$comprobante_detalle);
        $this->db->where($where);
        $this->db->update('cji_comprobantedetalle',$data);
    }
    
    public function reporte_ganancia($producto, $f_ini, $f_fin, $companias=''){
        $where = "";

        if($producto != '')
            $where .= " AND cd.PROD_Codigo = $producto ";
            
        $whereCompanias = "";
        if ($companias != ""){
            if ( is_array($companias) ){
                $size = count($companias);
                for ($i = 0; $i < $size; $i++){
                    $whereCompanias .= ($whereCompanias != "") ? ",".$companias[$i] : $companias[$i];
                }
            }
            else
                $whereCompanias = $companias;
        }
        else
            $whereCompanias = $this->somevar['compania'];
        $where .= " AND c.COMPP_Codigo IN($whereCompanias)";

        $sql = "SELECT cd.*, m.MONED_Simbolo, c.CPC_Fecha, c.COMPP_Codigo, ee.EESTABC_Descripcion, p.PROD_Nombre, apl.ALMALOTC_Costo, l.LOTC_Numero, l.LOTC_FechaVencimiento
                    FROM cji_comprobantedetalle cd
                    INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                    LEFT JOIN cji_almaprolote apl ON apl.LOTP_Codigo = cd.LOTP_Codigo
                    LEFT JOIN cji_lote l ON l.LOTP_Codigo = cd.LOTP_Codigo
                    LEFT JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
                    LEFT JOIN  cji_compania co ON co.COMPP_Codigo = c.COMPP_Codigo
                    LEFT JOIN cji_emprestablecimiento ee ON ee.EESTABP_Codigo = co.EESTABP_Codigo
                        WHERE c.CPC_TipoOperacion = 'V' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_Fecha BETWEEN '$f_ini 00:00:00' AND '$f_fin 23:59:59' $where
                ";

        $query = $this->db->query($sql);

        if($query->num_rows() > 0)
            return $query->result();
        else
            return array();
        
    }
    
    public function promedio_ventas_articulos($producto, $f_ini, $f_fin){
        $where = "";

        if($producto != '')
            $where .= " AND cd.PROD_Codigo = $producto ";
            
        $compania = $this->somevar['compania'];
        $sql = "SELECT cd.*, m.MONED_Simbolo, c.CPC_Fecha, c.COMPP_Codigo, p.PROD_Nombre, mr.MARCC_Descripcion, mr.MARCC_CodigoUsuario,
                    (SELECT MIN(ccd.CPDEC_Pu_ConIgv) FROM cji_comprobantedetalle ccd INNER JOIN cji_comprobante cc ON cc.CPP_Codigo = ccd.CPP_Codigo WHERE ccd.CPDEC_FlagEstado = 1 AND cc.CPC_FlagEstado = 1 AND cc.CPC_TipoOperacion = 'V' AND cc.CPC_TipoDocumento != 'N' AND cc.COMPP_Codigo = c.COMPP_Codigo AND ccd.PROD_Codigo = cd.PROD_Codigo ) as pventa_minimo,
                    (SELECT MAX(ccd.CPDEC_Pu_ConIgv) FROM cji_comprobantedetalle ccd INNER JOIN cji_comprobante cc ON cc.CPP_Codigo = ccd.CPP_Codigo WHERE ccd.CPDEC_FlagEstado = 1 AND cc.CPC_FlagEstado = 1 AND cc.CPC_TipoOperacion = 'V' AND cc.CPC_TipoDocumento != 'N' AND cc.COMPP_Codigo = c.COMPP_Codigo AND ccd.PROD_Codigo = cd.PROD_Codigo ) as pventa_maximo,
                    (SELECT SUM(ccd.CPDEC_Pu_ConIgv) FROM cji_comprobantedetalle ccd INNER JOIN cji_comprobante cc ON cc.CPP_Codigo = ccd.CPP_Codigo WHERE ccd.CPDEC_FlagEstado = 1 AND cc.CPC_FlagEstado = 1 AND cc.CPC_TipoOperacion = 'V' AND cc.CPC_TipoDocumento != 'N' AND cc.COMPP_Codigo = c.COMPP_Codigo AND ccd.PROD_Codigo = cd.PROD_Codigo ) as total,
                    (SELECT COUNT(ccd.CPDEC_Pu_ConIgv) FROM cji_comprobantedetalle ccd INNER JOIN cji_comprobante cc ON cc.CPP_Codigo = ccd.CPP_Codigo WHERE ccd.CPDEC_FlagEstado = 1 AND cc.CPC_FlagEstado = 1 AND cc.CPC_TipoOperacion = 'V' AND cc.CPC_TipoDocumento != 'N' AND cc.COMPP_Codigo = c.COMPP_Codigo AND ccd.PROD_Codigo = cd.PROD_Codigo ) as cantidad_operaciones
                    FROM cji_comprobantedetalle cd
                    INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                    LEFT JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                    LEFT JOIN cji_marca mr ON mr.MARCP_Codigo = p.MARCP_Codigo
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
                        WHERE c.CPC_TipoOperacion = 'V' AND c.CPC_TipoDocumento != 'N' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_Fecha BETWEEN '$f_ini 00:00:00' AND '$f_fin 23:59:59' AND c.COMPP_Codigo = $compania $where
                        GROUP BY cd.PROD_Codigo
                ";

        $query = $this->db->query($sql);

        if($query->num_rows() > 0)
            return $query->result();
        else
            return array();
        
    }

    public function listar_estado_by_id_orden_tipo($id_orden, $tipo)
    {
        $query = $this->db->from("cji_comprobantedetalle");

        $query->select_sum("cji_comprobantedetalle.CPDEC_Subtotal - cji_comprobantedetalle.CPDEC_Descuento", "CPDEC_Subtotal_calculado");

        $query->join("cji_comprobante", "cji_comprobante.CPP_Codigo = cji_comprobantedetalle.CPP_Codigo");
        $query->join("cji_ocompradetalle", "cji_ocompradetalle.OCOMDEP_Codigo = cji_comprobantedetalle.OCOMP_Codigo_VC");
        $query->join("cji_moneda", "cji_moneda.MONED_Codigo = cji_comprobante.MONED_Codigo");

        if($tipo == 'ov') {
            $query->join("cji_cliente", "cji_cliente.CLIP_Codigo = cji_comprobante.CLIP_Codigo");
        }else {
            $query->join("cji_proveedor", "cji_proveedor.PROVP_Codigo = cji_comprobante.PROVP_Codigo");
        }

        $query->join("cji_persona", "cji_persona.PERSP_Codigo = ".($tipo == 'ov' ? 'cji_cliente' : 'cji_proveedor').".PERSP_Codigo", "LEFT");
        $query->join("cji_empresa", "cji_empresa.EMPRP_Codigo = ".($tipo == 'ov' ? 'cji_cliente' : 'cji_proveedor').".EMPRP_Codigo", "LEFT");

        $query->where("cji_comprobantedetalle.CPDEC_FlagEstado", 1);
        $query->where("cji_comprobante.CPC_FlagEstado", 1);

        $query->where("cji_ocompradetalle.OCOMP_Codigo", $id_orden);

        $query->group_by("cji_comprobantedetalle.CPP_Codigo");

        return $query->select(implode(",", array("cji_comprobantedetalle.*","cji_comprobante.*", "cji_".($tipo == 'ov' ? "cliente" : "proveedor").".*", "cji_persona.*", "cji_empresa.*", "cji_moneda.*")))->get()->result();
    }

    public function listar_ventas_by_id_orden_producto($id_orden, $id_producto)
    {
        $query = $this->db->from("cji_comprobantedetalle");

        $query->join("cji_comprobante", "cji_comprobante.CPP_Codigo = cji_comprobantedetalle.CPP_Codigo");
        $query->join("cji_ocompradetalle", "cji_ocompradetalle.OCOMDEP_Codigo = cji_comprobantedetalle.OCOMP_Codigo_VC");
        $query->join("cji_moneda", "cji_moneda.MONED_Codigo = cji_comprobante.MONED_Codigo");

        $query->where("cji_comprobantedetalle.CPDEC_FlagEstado", 1);
        $query->where("cji_comprobante.CPC_FlagEstado", 1);
        $query->where("cji_ocompradetalle.OCOMDEC_FlagEstado", 1);

        $query->where("cji_comprobantedetalle.PROD_Codigo", $id_producto);
        $query->where("cji_ocompradetalle.OCOMP_Codigo", $id_orden);

        return $query->select(array("cji_comprobantedetalle.*", "cji_comprobante.*", "cji_moneda.*"))->get()->result();
    }
    
}
?>