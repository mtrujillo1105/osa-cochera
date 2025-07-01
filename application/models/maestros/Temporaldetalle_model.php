<?php

class Temporaldetalle_Model extends CI_Model {

    protected $_name = "temporal_detalle";

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->somevar['compania'] = $this->session->userdata('compania');
    }


    public function insertar_productodetalle($filter){
        return $this->db->insert('temporal_detalle',$filter);
    }

    public function obtener_producto_temporal($tempId, $producto, $session, $lote){
        $where = "";
        #if ($tempId != "")
        #    $where .= " AND td.TEMPDE_Codigo = $tempId ";
        if ($lote != "")
            $where .= " AND td.LOTP_Codigo = $lote ";
        $sql = "SELECT td.*,
            (SELECT ap.ALMPROD_Stock FROM cji_almacenproducto ap WHERE ap.PROD_Codigo = td.PROD_Codigo AND ap.ALMAP_Codigo = td.ALMAP_Codigo) as stock,
            (SELECT m.MARCC_Descripcion FROM cji_marca m INNER JOIN cji_producto p ON p.MARCP_Codigo = m.MARCP_Codigo WHERE p.PROD_Codigo = td.PROD_Codigo LIMIT 1) as marca
            FROM temporal_detalle td
            WHERE td.PROD_Codigo = $producto AND td.TEMPDE_SESSION = '$session' $where
                ";
        $query = $this->db->query($sql);
        #$query = $this->db->select('*')
        #                  ->where('PROD_Codigo', $producto)
        #                  ->where('TEMPDE_SESSION', $session)
        #                  ->where('LOTP_Codigo', $lote)
        #                  ->get('temporal_detalle');
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function modificar_prodtemporal($id,$filter,$session){
        $where = array(
            "TEMPDE_Codigo"  =>$id,
            "TEMPDE_SESSION" =>$session
        );
        $this->db->where($where);
        return $this->db->update('temporal_detalle',$filter);

    }

    public function eliminar_producto_temporal($producto,$session){
        $where = array(
            "PROD_Codigo"    => $producto,
            "TEMPDE_SESSION" => $session
        );
        $this->db->where($where);
        return $this->db->delete("temporal_detalle");
    }
    public function mostrar_temporal_producto($session){
        $sql = "SELECT td.*,
                    (SELECT LOTC_Numero FROM cji_lote l WHERE td.LOTP_Codigo <> 0 AND l.LOTP_Codigo = td.LOTP_Codigo LIMIT 1) as LOTC_Numero,
                    (SELECT LOTC_FechaVencimiento FROM cji_lote l WHERE td.LOTP_Codigo <> 0 AND l.LOTP_Codigo = td.LOTP_Codigo LIMIT 1) as LOTC_FechaVencimiento,
                    (SELECT m.MARCC_Descripcion FROM cji_marca m INNER JOIN cji_producto p ON p.MARCP_Codigo = m.MARCP_Codigo WHERE p.PROD_Codigo = td.PROD_Codigo LIMIT 1) as marca
                    
                    FROM temporal_detalle td
                    WHERE td.TEMPDE_SESSION = '$session' AND td.TEMPDE_FlagEstado = 1
                ";
        $query = $this->db->query($sql);

        #$query = $this->db->select('*')
        #                  ->where('TEMPDE_SESSION',$session)
        #                  ->where('TEMPDE_FlagEstado',1)
        #                  ->get('temporal_detalle');
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function eliminar_temporalProductos_session($session){
        $this->db->where('TEMPDE_SESSION',$session);
        return $this->db->delete('temporal_detalle');
    }

    public function obtener_flagIgv($compania) {
        $where = array("COMPP_Codigo" => $compania, "COMPCONFIC_FlagEstado" => "1");
        $query = $this->db->where($where)->get('cji_companiaconfiguracion');
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }

    public function listar_afectacion(){
        $query = $this->db->select('*')
                          ->where('AFECT_FlagEstado',1)
                          ->get('cji_tipo_afectacion');

        if($query->num_rows()>0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listar_familia(){
        
        $sql = "SELECT FAMI_Codigo, FAMI_Descripcion FROM cji_familia WHERE FAMI_FlagEstado = 1";
        $query = $this->db->query($sql);

        if($query->num_rows()>0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listar_marca(){
        
        $sql = "SELECT MARCP_Codigo, MARCC_Descripcion FROM cji_marca WHERE MARCC_FlagEstado = 1";
        $query = $this->db->query($sql);

        if($query->num_rows()>0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listar_modelo( $idMarca = NULL ){

        if ( $idMarca == NULL )
            return '';

        $sql = "SELECT DISTINCT PROD_Modelo FROM cji_producto WHERE PROD_Modelo IS NOT NULL AND PROD_Modelo <> '' AND MARCP_Codigo = $idMarca";
        $query = $this->db->query($sql);

        if($query->num_rows()>0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listarVentas( $producto = NULL, $cliente = NULL ){

        $compania = $this->somevar['compania'];

        if ( $producto == NULL || $cliente == NULL )
            return NULL;

        $sql = "SELECT c.CPC_TipoDocumento as documento, c.CPC_Serie as serie, c.CPC_Numero as numero, cd.CPDEC_Descripcion as nombreArticulo, cd.CPDEC_Pu_ConIgv as precioCigv, cd.CPDEC_Pu as precioSigv
                FROM cji_comprobante c
                INNER JOIN cji_comprobantedetalle cd ON c.CPP_Codigo = cd.CPP_Codigo
                    WHERE c.CLIP_Codigo = $cliente AND cd.PROD_Codigo = $producto AND c.COMPP_Codigo = $compania AND c.CPC_TipoOperacion = 'V' AND
                        c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 ";

        $query = $this->db->query($sql);

        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
        else
            return NULL;
    }

    public function listarCotizaciones( $producto = NULL, $cliente = NULL ){

        $compania = $this->somevar['compania'];

        if ( $producto == NULL || $cliente == NULL )
            return NULL;

        $sql = "SELECT c.OCOMC_Serie as serie, c.OCOMC_Numero as numero, cd.OCOMDEC_Descripcion as nombreArticulo, cd.OCOMDEC_Pu_ConIgv as precioCigv, cd.OCOMDEC_Pu as precioSigv
                FROM cji_ordencompra c
                INNER JOIN cji_ocompradetalle cd ON c.OCOMP_Codigo = cd.OCOMP_Codigo
                    WHERE c.CLIP_Codigo = $cliente AND cd.PROD_Codigo = $producto AND c.COMPP_Codigo = $compania AND c.OCOMC_TipoOperacion = 'V' AND
                        c.OCOMC_FlagEstado = 1 AND cd.OCOMDEC_FlagEstado = 1 ";

        $query = $this->db->query($sql);

        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
        else
            return NULL;
    }

}

?>