<?php

class Importacion_model extends CI_Model {

    var $somevar;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('configuracion_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('maestros/tipocambio_model');
        $this->load->model('almacen/seriemov_model');
        $this->load->model('ventas/importaciondetalle_model');
        $this->load->model('almacen/guiasa_model');
        $this->load->model('almacen/guiarem_model');
        $this->load->model('almacen/productounidad_model');
        $this->load->model('almacen/lote_model');
        $this->load->model('almacen/almaprolote_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('almacen/cuentaspago_model');
        $this->load->model('almacen/almacenproducto_model');
        $this->load->model('almacen/guiain_model');
        $this->load->model('almacen/kardex_model');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user'] = $this->session->userdata('user');
        $this->somevar['hoy'] = mdate("%Y-%m-%d %h:%i:%s", time());
    }

    public function getArticulos($idArticulo, $idCompañia)
    {
        return $this->db->select(array(
                        'imp.IMPOR_Codigo as', 'impdet.PROD_Codigo','impdet.IMPORDEC_Cantidad','impdet.IMPORDEC_descripcion', 'impdet.IMPORDEC_Costo_uni_liquidado'
                    ))
                    ->from('cji_importaciondetalle as impdet')
                    ->join('cji_importacion as imp','imp.IMPOR_Codigo = impdet.IMPOR_Codigo')
                    ->where(array(
                            'imp.COMPP_Codigo' => $idCompañia,
                            'impdet.PROD_Codigo' => $idArticulo,
                            'imp.IMPOR_Liquidada' => 1
                        ))
                    ->get()
                    ->result();
    }

public function select_cmbVendedor($dato=""){
     
    $this->db->select('p.PERSP_Codigo,p.PERSC_Nombre,p.PERSC_ApellidoPaterno');
    $this->db->join('cji_persona p','p.PERSP_Codigo=u.PERSP_Codigo');
    if($dato != ""){
     $this->db->where('u.PERSP_Codigo',$dato);
    }
$query = $this->db->get('cji_directivo u');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }

}
    public function listar_comprobantes($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '') {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CPC_FechaRegistro', 'DESC')->where($where)->get('cji_comprobante', $number_items, $offset);  //order_by('CPC_Serie','desc')->order_by('CPC_Numero','desc')
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

      /*   public function buscarRolUsuario($nombre){
        
        $sql ="select USUA_usuario,USUA_Password,rol.ROL_Codigo,ROL_Descripcion from cji_rol rol 
                inner join cji_usuario usuario on rol.ROL_Codigo = usuario.ROL_Codigo where USUA_usuario = '$nombre' ;";
        $query = $this->db->query($sql);
        if($query->num_rows() >0){
            foreach ($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
        
    }*/


    public function importacion_comprobante($importacion){
        $sql = "SELECT im.IMPOR_Codigo , com.CPC_Serie , com.CPC_Numero , com.PROVP_Codigo  ,em.EMPRC_Ruc  , em.EMPRC_RazonSocial  ,com.CPC_subtotal ,com.CPC_igv,com.CPC_total, m.MONED_Simbolo,m.MONED_smallName, com.CPC_TDC, com.CPC_TDC_opcional
       FROM cji_importacion im
       inner join cji_comprobante com on com.IMPOR_Nombre=im.IMPOR_Codigo
       inner join cji_proveedor pr on pr.PROVP_Codigo = com.PROVP_Codigo
       inner join cji_empresa em on em.EMPRP_Codigo =pr.EMPRP_Codigo
       inner JOIN cji_moneda m on m.MONED_Codigo = com.MONED_Codigo 
       WHERE im.IMPOR_Codigo='$importacion' ";

       $query = $this->db->query($sql);
       if($query->num_rows() >0){
        foreach ($query->result() as $fila){
            $data[] = $fila;
        }
        return $data;
    }

    }
public function importacion_comprobante_reporte($importacion){
         $sql = "SELECT im.IMPOR_Codigo , com.CPC_Fecha, com.CPC_Serie , com.CPC_Numero , com.PROVP_Codigo  , em.EMPRC_Ruc as ruc  , em.EMPRC_RazonSocial as nombre ,com.CPC_subtotal ,com.CPC_igv,com.CPC_total, m.MONED_Simbolo
FROM cji_importacion im
inner join cji_comprobante com on com.IMPOR_Nombre=im.IMPOR_Codigo
inner join cji_proveedor pr on pr.PROVP_Codigo = com.PROVP_Codigo
inner join cji_empresa em on em.EMPRP_Codigo =pr.EMPRP_Codigo
inner JOIN cji_moneda m on m.MONED_Codigo = com.MONED_Codigo 
WHERE im.IMPOR_Codigo= ".$importacion;

       $query = $this->db->query($sql);
        if($query->num_rows() >0){
            foreach ($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }

    }
   public function listado_detalle_importacion($importacion){
         $sql = "SELECT im.IMPOR_Serie,im.IMPOR_Numero,im.IMPOR_Nombre,pr.PROVP_Codigo, em.EMPRC_Ruc  , em.EMPRC_RazonSocial  ,im.IMPOR_Fecha
FROM cji_importacion im
inner join cji_proveedor pr on pr.PROVP_Codigo = im.PROVP_Codigo
inner join cji_empresa em on em.EMPRP_Codigo =pr.EMPRP_Codigo
WHERE IMPOR_Codigo= ".$importacion;

       $query = $this->db->query($sql);
        if($query->num_rows() >0){
            foreach ($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }

    } 
    public function listar_importacion($liquidada = NULL){
        $where = array("IMPOR_FlagEstado"=>2);

        if(!is_null($liquidada)) $where['IMPOR_Liquidada'] = $liquidada;

        $query = $this->db->order_by('IMPOR_Nombre')
                          ->where($where)
                          ->select('IMPOR_Codigo,IMPOR_Nombre')
                          ->from('cji_importacion')
                          ->get();
        if($query->num_rows()>0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function listar_comprobantes_factura($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '') {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CPC_Serie', 'desc')->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante', $number_items, $offset);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function busqueda_comprobante($tipo_oper = 'V', $tipo_docu = 'F', $filter = NULL) {

        $compania = $this->somevar['compania'];

        $where = '';
        if (isset($filter->seriei) && $filter->seriei != '') {
            $where .= ' and cp.CPC_Serie="' . $filter->seriei . '"';
        }
        if (isset($filter->numero) && $filter->numero != '') {
            $where .= ' and cp.CPC_Numero=' . $filter->numero;
        }

        if(isset($filter->fecha_ini) && $filter->fecha_ini != "" && !isset($filter->fecha_fin) || $filter->fecha_fin == ""){
            $where .= ' AND cp.CPC_Fecha >= "' . $filter->fecha_ini. '"';
            $where .= ' AND cp.CPC_Fecha <= ' . '"2020-12-12"';
        }else if($filter->fecha_fin != "" && isset($filter->fecha_fin) && $filter->fecha_ini == "" || !isset($filter->fecha_ini)){
            $where .= ' AND cp.CPC_Fecha <= "' . $filter->fecha_fin.'"';
            $where .= ' AND cp.CPC_Fecha > ' . '"2010-12-12"';
        }else{
            $where .= ' AND cp.CPC_Fecha >= "' . $filter->fecha_ini. '"';
            $where .= ' AND cp.CPC_Fecha <= "' . $filter->fecha_fin .'"';
        }

        if ($tipo_oper == 'V') {
            if (isset($filter->nombre_cliente) && $filter->nombre_cliente != '') {
                $where .= ' and EMPRC_RazonSocial LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_cliente.'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_cliente.'%"';
            }
            if (isset($filter->ruc_cliente) && $filter->ruc_cliente != '') {
                $where .= ' and EMPRC_Ruc LIKE "%' . $filter->ruc_cliente.'%"';
                $where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_cliente.'%"';
            }
        }
        else {
            if (isset($filter->nombre_proveedor) && $filter->nombre_proveedor != '') {
                $where .= ' and EMPRC_RazonSocial LIKE "%' . $filter->nombre_proveedor.'%"';
                $where .= ' OR PERSC_Nombre LIKE "%' . $filter->nombre_proveedor.'%"';
                $where .= ' OR PERSC_ApellidoPaterno LIKE "%' . $filter->nombre_proveedor.'%"';
            }
            if (isset($filter->ruc_proveedor) && $filter->ruc_proveedor != '') {
                $where .= ' and EMPRC_Ruc LIKE "%' . $filter->ruc_proveedor.'%"';
                $where .= ' OR PERSC_NumeroDocIdentidad LIKE "%' . $filter->ruc_proveedor.'%"';
            }
        }

        $sql = "SELECT cp.CPC_Fecha,
                       cp.CPP_Codigo,
                       cp.CPC_Serie,
                       cp.CPC_Numero,
                       cp.CPP_Codigo_canje,
                       cp.CPC_GuiaRemCodigo,
                       cp.CPC_DocuRefeCodigo,
                       cp.CPC_NombreAuxiliar,
                       cp.CLIP_Codigo,
                       cp.CPC_TipoDocumento,
                       m.MONED_Simbolo,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.CPC_total,
                       cp.CPC_FlagEstado
                FROM cji_comprobante cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_comprobantedetalle cpd ON cpd.CPP_Codigo=cp.CPP_Codigo
                " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'

                    WHERE cp.CPC_TipoOperacion='" . $tipo_oper . "'
                     AND cp.COMPP_Codigo =" . $compania . " " . $where . " AND cp.CPC_TipoDocumento='" . $tipo_docu . "'

                GROUP BY cp.CPP_Codigo
                ORDER BY cp.CPC_FechaRegistro DESC ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }
  public function buscar_comprobante_venta_3($anio ,$mes ,$fech1 ,$fech2 ,$tipodocumento) {
        //CPC_TipoOperacion => V venta, C compra
        //CPC_TipoDocumento => F factura, B boleta
        //CPC_total => total de la FACTURA o BOLETA CPC_FechaRegistro  BETWEEN '20121201' AND '20121202'

        $where="";
        //----------
        if($anio!="--" && $mes =="--"){// SOLO AÑO
        $where="AND YEAR(CPC_FechaRegistro)='" . $anio . "'";
        }
        if($anio!="--" && $mes !="--" ){// MES Y  AÑO
            $where="AND YEAR(CPC_FechaRegistro)='" . $anio . "' AND MONTH(CPC_FechaRegistro)='" . $mes ."'";
        }
         if($anio=="--" && $mes !="--"){//MES CON AÑO ACTUAL
            $where="AND YEAR(CPC_FechaRegistro)=' ".date("Y")."' AND MONTH(CPC_FechaRegistro)='" . $mes ."'";
        }

        //-----------------
       
        if($anio=="--" && $mes =="--" && $fech1!="--" && $fech2=="--"){//FECHA INICIAL
                $where="AND CPC_FechaRegistro > '" . $fech1 . "'";
            }
        if($anio=="--" && $mes =="--" && $fech1!="--" && $fech2!="--" ){//FECHA INICIAL Y FECHA FINAL
                $where="AND CPC_FechaRegistro >= '" . $fech1 . "' AND CPC_FechaRegistro <= '" . $fech2 . "'";
            }
        
      
            //------------

        
            $wheretdoc= "";
            if($tipodocumento !="--")
                $wheretdoc= " AND CPC_TipoDocumento='".$tipodocumento."' ";

           

        $sql = " SELECT com.*,CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as nombre , MONED_Simbolo from cji_comprobante com
        inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
        inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
        inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo 
        WHERE CPC_TipoOperacion='V' ".$wheretdoc.$where."
        
        UNION 
        SELECT com.* ,EMPRC_RazonSocial as nombre ,MONED_Simbolo from cji_comprobante com
        inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
        inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
        inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo 
        WHERE CPC_TipoOperacion='V' ".$wheretdoc.$where."";

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
    public function buscardep($dato) {
       
        $sql = " SELECT * FROM cji_ubigeo  WHERE UBIGP_Codigo=" . $dato . "";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }
    public function contar_comprobantes($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '', $fecha_registro = '') {
        $compania = $this->somevar['compania'];

        $limit = "";

        if ((string) $offset != '' && $number_items != '') {
            $limit = 'LIMIT ' . $offset . ',' . $number_items;
        }

        $sql = "SELECT COUNT(cp.IMPOR_Codigo) as total
                FROM cji_importacion cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_importaciondetalle cpd ON cpd.IMPOR_Codigo=cp.IMPOR_Codigo
                " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '1'

                    WHERE cp.IMPOR_TipoOperacion='" . $tipo_oper . "'
                      AND cp.IMPOR_TipoOperacion='" . $tipo_docu . "' AND cp.COMPP_Codigo =" . $compania . "
                " . $limit;

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row()->total;
        }
        return array();
    }

 /*public function buscar_comprobantes($tipo_oper = 'V', $tipo_docu = 'F', $number_items = '', $offset = '', $fecha_registro = '') {
        $compania = $this->somevar['compania'];

        $limit = "";

        if ((string) $offset != '' && $number_items != '') {
            $limit = 'LIMIT ' . $offset . ',' . $number_items;
        }

        $sql = "SELECT cp.IMPOR_Fecha,
                       cp.IMPOR_Codigo,
                       cp.IMPOR_Serie,
                       cp.IMPOR_Numero,
                       cp.IMPOR_Codigo_canje,
                       cp.IMPOR_GuiaRemCodigo,
                       cp.IMPOR_DocuRefeCodigo,
                       cp.IMPOR_NombreAuxiliar,
                       cp.CLIP_Codigo,
                       cp.IMPOR_TipoDocumento,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.IMPOR_total,
                       cp.IMPOR_FlagEstado
                FROM cji_importacion cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_importaciondetalle cpd ON cpd.IMPOR_Codigo=cp.IMPOR_Codigo
                " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
              
                    WHERE cp.IMPOR_TipoOperacion='" . $tipo_oper . "' 
                      AND cp.IMPOR_TipoDocumento='" . $tipo_docu . "' AND cp.COMPP_Codigo =" . $compania . "
                     
                GROUP BY cp.IMPOR_Codigo
                ORDER BY cp.IMPOR_FechaRegistro DESC " . $limit;

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }*/
public function buscar_comprobantes($tipo_oper = 'V', $filter = NULL, $number_items = '', $offset = '')
    {
        $compania = $this->somevar['compania'];

        $where = '';

        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
            $where = ' and cp.IMPOR_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
        if (isset($filter->serie) && $filter->serie != '' && isset($filter->numero) && $filter->numero != '')
            $where .= ' and cp.IMPOR_Serie="' . $filter->serie . '" and cp.IMPOR_Numero=' . $filter->numero;

        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where .= ' and cp.CLIP_Codigo=' . $filter->cliente;
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where .= ' and cp.PROVP_Codigo=' . $filter->proveedor;
        }
        if (isset($filter->producto) && $filter->producto != '')
            $where .= ' and cpd.PROD_Codigo=' . $filter->producto;

        $limit = "";

        if ((string)$offset != '' && $number_items != '') {
            $limit = 'LIMIT ' . $offset . ',' . $number_items;
        }

        $sql = "SELECT cp.IMPOR_Fecha,
                       cp.IMPOR_Codigo,
                       cp.IMPOR_Serie,
                       cp.IMPOR_Numero,
                       cp.IMPOR_Nombre,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.IMPOR_total,
                       cp.IMPOR_FlagEstado,
                     
                      cp.IMPOR_Fecha,
                       cp.IMPOR_Codigo,
                       cp.IMPOR_Serie,
                       cp.IMPOR_Numero,
                       cp.IMPOR_Codigo_canje,
                       cp.IMPOR_GuiaRemCodigo,
                       cp.IMPOR_DocuRefeCodigo,
                       cp.IMPOR_NombreAuxiliar,
                       cp.IMPOR_total,
                       cp.IMPOR_FlagEstado
                FROM cji_importacion cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                " . ($tipo_oper != 'C' ? "LEFT JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "INNER JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " = '1'
                WHERE cp.IMPOR_TipoOperacion='" . $tipo_oper . "'
                     AND cp.IMPOR_FlagEstado = 2
                      AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                ORDER BY cp.IMPOR_FechaRegistro DESC " . $limit;  //cp.CPC_Serie DESC, cp.CPC_Numero DESC
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }
      public function buscar_comprobantes_asoc($tipo_oper, $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '') {
        $compania = $this->somevar['compania'];

        $where = '';
        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
            $where .= ' and cp.CPC_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
        if (isset($filter->seriei) && $filter->seriei != '')
            $where.=' and cp.CPC_Serie="' . $filter->seriei . '"';
        if (isset($filter->numero) && $filter->numero != '')
            $where.=' and cp.CPC_Numero=' . $filter->numero;


        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where.=' and cp.CLIP_Codigo=' . $filter->cliente;
        } else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where.=' and cp.PROVP_Codigo=' . $filter->proveedor;
        }
        
        if (isset($filter->producto) && $filter->producto != '')
            $where.=' and cpd.PROD_Codigo=' . $filter->producto;
        $limit = "";
        if ((string) $offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT cp.CPC_Fecha,
                       cp.CPP_Codigo,
                       cp.CPC_Serie,
                       cp.CPC_Numero,
                       cp.CPP_Codigo_canje,
                       cp.CPC_GuiaRemCodigo,
                       cp.CPC_DocuRefeCodigo,
                       cp.CPC_NombreAuxiliar,
                       cp.CLIP_Codigo,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.CPC_total,
                       cp.CPC_FlagEstado
                FROM  cji_comprobante cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_comprobantedetalle cpd ON cpd.CPP_Codigo=cp.CPP_Codigo
                 " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
                
                WHERE cp.CPC_TipoOperacion='" . $tipo_oper . "' 
                      AND cp.CPC_TipoDocumento='" . $tipo_docu . "' AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                      AND cp.CPC_DocuRefeCodigo = ''
                      AND cp.CPC_FlagEstado = '1'
                GROUP BY cp.CPP_Codigo
                ORDER BY cp.CPC_Fecha DESC, cp.CPC_Numero DESC  " . $limit; 
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

    public function importacion_gastos_detalle($idImportacion)
    {
        return $this->db->from("cji_comprobante")
                        ->join("cji_comprobantedetalle", "cji_comprobantedetalle.CPP_Codigo = cji_comprobante.CPP_Codigo")
                        ->join("cji_moneda", "cji_moneda.MONED_Codigo = cji_comprobante.MONED_Codigo")
                        ->join("cji_producto", "cji_producto.PROD_Codigo = cji_comprobantedetalle.PROD_Codigo")
                        ->join("cji_familia", "cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo", "LEFT")
                        ->where("cji_comprobante.IMPOR_Nombre", $idImportacion)
                        ->where("cji_comprobante.CPC_FlagEstado", 1)
                        ->get()->result();
    }

/*SELECT* 
FROM cji_ordencompra o
INNER JOIN cji_importacion i ON i.OCOMP_Codigo=o.OCOMP_Codigo
INNER JOIN cji_comprobante c ON c.IMPOR_Nombre=i.IMPOR_Codigo
INNER JOIN cji_comprobantedetalle cd on cd.CPP_Codigo=c.CPP_Codigo
INNER JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo 
LEFT JOIN cji_familia f ON f.FAMI_Codigo = p.FAMI_Codigo
WHERE  o.OCOMP_Codigo =  '66'*/


    public function importacion_gastos_detalle_total($ocompra)
    {
    return $this->db->from("cji_ordencompra")
                    ->join("cji_importacion", "cji_importacion.OCOMP_Codigo = cji_ordencompra.OCOMP_Codigo")
                    ->join("cji_comprobante", "cji_comprobante.IMPOR_Nombre = cji_importacion.IMPOR_Codigo")    
                    ->join("cji_comprobantedetalle", "cji_comprobantedetalle.CPP_Codigo = cji_comprobante.CPP_Codigo")
                    ->join("cji_moneda", "cji_moneda.MONED_Codigo = cji_comprobante.MONED_Codigo")
                    ->join("cji_producto", "cji_producto.PROD_Codigo = cji_comprobantedetalle.PROD_Codigo")
                    ->join("cji_familia", "cji_familia.FAMI_Codigo = cji_producto.FAMI_Codigo", "LEFT")
                    ->where("cji_ordencompra.OCOMP_Codigo", $ocompra)
                    ->get()->result();
    }

    public function liquidar($id, $articulos)
    {
        $this->db->trans_begin();

        foreach ($articulos as $articulo) {
            $this->db->where(array(
                    'IMPOR_Codigo' => $id,
                    'PROD_Codigo' => $articulo['id']
                ))
                ->update("cji_importaciondetalle", array(
                        'IMPORDEC_Costo_uni_liquidado' => $articulo['gastoUnitario'],
                        'IMPORDEC_FechaModificacion' => date('U'),
                    ));
        }

        $this->db->where("IMPOR_Codigo", $id)
                    ->update('cji_importacion', array(
                            'IMPOR_Liquidada' => '1',
                            'IMPOR_FechaModificacion' => date('U')
                        ));

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return FALSE;
        }else{
            $this->db->trans_commit();
            return TRUE;
        }

        return FALSE;
    }  

    public function revertir_liquidacion($id)
      {
          $this->db->trans_begin();

          $this->db->where("IMPOR_Codigo", $id)
                    ->update('cji_importacion', array(
                            'IMPOR_Liquidada' => 0,
                            'IMPOR_FechaModificacion' => date('U')
                        ));

            $this->db->where("IMPOR_Codigo", $id)
                    ->update('cji_importaciondetalle', array(
                            'IMPORDEC_Costo_uni_liquidado' => NULL,
                            'IMPORDEC_FechaModificacion' => date('U')
                        ));

          if($this->db->trans_status() == FALSE){
            $this->db->trans_rollback();
            return FALSE;
          }else{
            $this->db->trans_commit();
            return TRUE;
          }

          return FALSE;
      }  
    
    public function comprobante_pago_pendiente($comprobante) {
        $query = $this->db->where('CUE_CodDocumento', $comprobante)->get('cji_cuentas');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_comprobante($comprobante) {
        $query = $this->db->where('IMPOR_Codigo', $comprobante)->get('cji_importacion');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function getById($id)
    {
        $data = $this->db->get_where("cji_importacion", array('IMPOR_Codigo' => $id))->result();

        return $data[0];
    }
  //  SELECT * FROM (`cji_importacion`) WHERE `OCOMP_Codigo` = '66'
    public function getById2($id)
    {
        $query = $this->db->get_where("cji_importacion", array('OCOMP_Codigo' => $id));

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    

    public function buscar_xserienum($serie, $numero, $doc, $oper) {
        $where = array('IMPOR_Serie' => $serie,
            'IMPOR_Numero' => $numero,
            'IMPOR_TipoDocumento    ' => $doc,
            'IMPOR_TipoOperacion' => $oper
        );
        $this->db->where($where);
        $query = $this->db->get('cji_importacion');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_comprobante_ref($guia_rem) {
        $query = $this->db->where('GUIAREMP_Codigo', $guia_rem)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener_comprobante_ref2($guia_rem) {
        $query = $this->db->where(array('GUIAREMP_Codigo' => $guia_rem, 'CPC_FlagEstado' => 1))->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function obtener_comprobante_ref3($cod_fac) {
    $guia = explode("-", $cod_fac);
    
        $query = $this->db->where(array('GUIAREMC_Serie' => $guia[0] ,'GUIAREMC_Numero' => $guia[1],'GUIAREMC_FlagEstado' => 1 ))->get('cji_guiarem');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function obtener_comprobante_guiaref($numeroref) {
    
        $query = $this->db->where('GUIAREMC_NumeroRef', $numeroref)->get('cji_guiarem');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar_comprobante($filter = null) {
        $ordenCompra = $filter->OCOMP_Codigo;
        unset($filter->OCOMP_Codigo);

       $insert = $this->db->insert('cji_importacion', (array)$filter);
        if ($insert) {
            $ultimo_id = $this->db->insert_id();
            if(is_array($ordenCompra)){
                foreach ($ordenCompra as $oc) {
                    $this->db->insert("cji_ocompra_importacion", array(
                        'OCOMP_Codigo' => $oc,
                        'IMPOR_Codigo' => $ultimo_id
                    ));
                }
            }else{
                $this->db->insert("cji_ocompra_importacion", array(
                        'OCOMP_Codigo' => $ordenCompra,
                        'IMPOR_Codigo' => $ultimo_id
                    ));
            }

            return $ultimo_id;
        }else{
            return NULL;
        }
    }

    public function insertar_importacion($filter = null)
    {
        $this->db->trans_begin();
        $ordenCompra = $filter->OCOMP_Codigo;
        //unset($filter->OCOMP_Codigo);

        $insert = $this->db->insert('cji_importacion', (array)$filter);
        $ultimo_id = $this->db->insert_id();

        //insertar compra_importacion si hay orden de compra previa
        if(!is_null($ordenCompra)){
            if(is_array($ordenCompra)){
                foreach ($ordenCompra as $oc) {
                    $this->db->insert("cji_ocompra_importacion", array(
                        'OCOMP_Codigo' => $oc,
                        'IMPOR_Codigo' => $ultimo_id
                        ));
                }
            }else{
                $this->db->insert("cji_ocompra_importacion", array(
                    'OCOMP_Codigo' => $ordenCompra,
                    'IMPOR_Codigo' => $ultimo_id
                    ));
            }
        }

        if($this->db->trans_status() == false){
            $this->db->trans_rollback();
            return NULL;
        }else {
            $this->db->trans_commit();
            return $ultimo_id;
        }
    }

    public function insertar_comprobante_guiarem($filter){
        if(count($filter)>0){
            $this->db->insert("cji_comprobante_guiarem", (array) $filter);
            $comprobanteGuiarem = $this->db->insert_id();
            return $comprobanteGuiarem;     
        }
    }
    
    
    public function insertar_comprobante2($filter) {
        $this->db->insert("cji_comprobante", (array) $filter);
        $comprobante = $this->db->insert_id();
        return $comprobante;
    }

    public function insertar_disparador($comprobante, $filter = null) {

        $compania = $this->somevar['compania'];
        $user = $this->somevar ['user'];
        switch ($filter->CPC_TipoDocumento) {
            case 'F': $codtipodocu = '8';
                break;
            case 'B': $codtipodocu = '9';
                break;
            case 'N': $codtipodocu = '14';
                break;
            default: $codtipodocu = '0';
                break;
        }

        $data = array(
            "CPC_FlagEstado" => 1
        );
        $this->db->where('CPP_Codigo', $comprobante);
        $this->db->update("cji_comprobante", $data);


        if ($filter->CPC_TipoOperacion == 'V')
            $this->configuracion_model->modificar_configuracion($compania, $codtipodocu, $filter->CPC_Numero);

        $filter2 = new stdClass();
        $filter2->CUE_TipoCuenta = $filter->CPC_TipoOperacion == 'V' ? 1 : 2;
        $filter2->DOCUP_Codigo = $codtipodocu;
        $filter2->CUE_CodDocumento = $comprobante;
        $filter2->MONED_Codigo = $filter->MONED_Codigo;
        $filter2->CUE_Monto = $filter->CPC_total;
        $filter2->CUE_FechaOper = $filter->CPC_Fecha;
        $filter2->COMPP_Codigo = $compania;
        $filter2->CUE_FlagEstado = '1';
        if (isset($filter->FORPAP_Codigo) && $filter->FORPAP_Codigo == 1) {
            $filter2->CUE_FlagEstadoPago = 'C';
        }
        $cuenta = $this->cuentas_model->insertar($filter2);

        if (isset($filter->FORPAP_Codigo) && $filter->FORPAP_Codigo == 1) {  //Si el pago es al contado           
            $filter3 = new stdClass();
            $filter3->PAGC_TipoCuenta = $filter->CPC_TipoOperacion == 'V' ? 1 : 2;
            $filter3->PAGC_FechaOper = $filter->CPC_Fecha;
            if ($filter3->PAGC_TipoCuenta == 1)
                $filter3->CLIP_Codigo = $filter->CLIP_Codigo;
            else
                $filter3->PROVP_Codigo = $filter->PROVP_Codigo;
            $filter4 = new stdClass();
            $filter4->TIPCAMC_Fecha = $filter->CPC_Fecha;
            $filter4->TIPCAMC_MonedaDestino = '2';
            $temp = $this->tipocambio_model->buscar($filter4);
            $tdc = is_array($temp) ? $temp[0]->TIPCAMC_FactorConversion : '';

            $filter3->PAGC_TDC = $tdc;
            $filter3->PAGC_Monto = $filter->CPC_total;
            $filter3->MONED_Codigo = $filter->MONED_Codigo;
            $filter3->PAGC_FormaPago = '1'; //Efectivo

            $filter3->PAGC_Obs = ($filter->CPC_TipoOperacion == 'V' ? 'INGRESO GENERADO' : 'SALIDA GENERADA') . ' AUTOMATICAMENTE POR EL PAGO AL CONTADO';
            $filter3->PAGC_Saldo = '0';

            $cod_pago = $this->pago_model->insertar($filter3, '', '', '');

            $filter5 = new stdClass();
            $filter5->CUE_Codigo = $cuenta;
            $filter5->PAGP_Codigo = $cod_pago;
            $filter5->CPAGC_TDC = $tdc;
            $filter5->CPAGC_Monto = $filter->CPC_total;
            $filter5->MONED_Codigo = $filter->MONED_Codigo;

            $this->cuentaspago_model->insertar($filter5);
            $filter3 = new stdClass();
        }
    }
       /* $query = $this->db->where('IMPOR_Codigo', $comprobante)->get('cji_importacion');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;*/
    public function modificar_comprobante($comprobante, $filter = null) {
      /*  $user = $this->somevar ['user'];
        $filter->USUA_Codigo = $user;*/

        $where = array("IMPOR_Codigo" => $comprobante);
        $this->db->where($where);
        $this->db->update('cji_importacion', (array) $filter);
    }
    
    public function buscarRolUsuario($nombre){
        
        $sql ="select USUA_usuario,USUA_Password,rol.ROL_Codigo,ROL_Descripcion from cji_rol rol 
                inner join cji_usuario usuario on rol.ROL_Codigo = usuario.ROL_Codigo where USUA_usuario = '$nombre' ;";
        $query = $this->db->query($sql);
        if($query->num_rows() >0){
            foreach ($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
        
    }
    
//     public function eliminar_guiarem(){
//      $sql = "select * from"
//     }

    public function eliminar_comprobante($comprobante, $userCod) {

        $compania = $this->somevar['user'];
        echo "<script>alert('user : '".$compania."')</script>";
        
        $list = $this->obtener_comprobante($comprobante);

        //  print_r($list);
        //conciderar si se obtiene 0 datos
        $oper = $list[0]->CPC_TipoOperacion;
        $docu = $list[0]->CPC_TipoDocumento;
        //hacer un artificio
        $gremp = $list[0]->GUIAREMP_Codigo;
        $gsap = $list[0]->GUIASAP_Codigo;
        $ginp = $list[0]->GUIAINP_Codigo;

        /* if ($gremp != Null) {
          $list_guiare = $this->guiarem_model->obtener($gremp);
          $gsap = $list_guiare[0]->GUIASAP_Codigo;
          $ginp = $list_guiare[0]->GUIAINP_Codigo;
          } */


        ///listamos los detalles del comprobante
        $detalle = $this->comprobantedetalle_model->listar($comprobante);
        for ($i = 0; $i < count($detalle); $i++) {
            $prodcod = $detalle[$i]->PROD_Codigo;
            $unid_medida = $detalle[$i]->UNDMED_Codigo;
            $cantidad = $detalle[$i]->CPDEC_Cantidad;
            //CUANDO SE TRATA DE UNA COMPRA
            if ($oper == "C") {
                //eliminacion logica de la guia 
                $data = array("GUIAINC_FlagEstado" => '0');
                $where = array("GUIAINP_Codigo" => $ginp);
                $this->db->where($where);
                $this->db->update('cji_guiain', $data);

                //obtener el almacen        
                $guiainp_datos = $this->guiain_model->obtener($ginp);
                $almacencod = $guiainp_datos[0]->ALMAP_Codigo;
                $docupcod = 5;
                //buscar lote 
                $lote_datos = $this->lote_model->obtener_x_guia($prodcod, $ginp);
                $codlote = $lote_datos[0]->LOTP_Codigo;

                //obtener el valor del stock
                $almacenproducto_datos = $this->almacenproducto_model->obtener($almacencod, $prodcod);
                $almacenprodcod = $almacenproducto_datos[0]->ALMPROD_Codigo;
                $stock = $almacenproducto_datos[0]->ALMPROD_Stock;

                $productoundad = $this->productounidad_model->obtener($prodcod, $unid_medida);
                if ($productoundad) {
                    $flagPrincipal = $productoundad->PRODUNIC_flagPrincipal;
                    $factor = $productoundad->PRODUNIC_Factor;
                    if ($flagPrincipal == 0) {
                        //  $cantidad = 0;
                        if ($factor > 0){
                            
                            ///stv
                            $cantidad = $cantidad / $factor;
                            if(strpos($cantidad,".")==true){
                            $cantidad=round($cantidad,3);  
                            }
                            ////
                            
                            //taba asi
                            //$cantidad = $cantidad / $factor;
                        }
                    }
                }

                $nuevostock = $stock - $cantidad;
                //------------------------------------------------
                //Eliminar Kardex
                $this->kardex_model->eliminar($docupcod, $ginp, $prodcod);

                //elimina almaprolote
                $this->almaprolote_model->eliminar($almacenprodcod, $codlote);
                //elimino lote
                $this->lote_model->eliminar($codlote);


                //obtener cuenta 
                $cuentaspago_datos = $this->cuentaspago_model->obtener($comprobante);
                if (count($cuentaspago_datos) > 0) {
                    $codpago = $cuentaspago_datos[0]->PAGP_Codigo;
                    //eliminar pago
                    $this->pago_model->anular($codpago);
                }
                //eliminar las cuentas
                $this->cuentaspago_model->eliminar($comprobante);
                $this->cuentas_model->eliminar($comprobante);

                //actualizar stock
                $data = array("ALMPROD_Stock" => $nuevostock);
                $where = array("ALMAC_Codigo" => $almacencod, "PROD_Codigo" => $prodcod, "COMPP_Codigo" => $compania);
                $this->db->where($where);
                $this->db->update('cji_almacenproducto', $data);
                //eliminar los alamacenproductoseri
                $this->db->delete('cji_almacenproductoserie', array("ALMPROD_Codigo" => $almacenprodcod));
                //obtenemos los datos del almacen stock
                $series_datos = $this->seriemov_model->buscar_x_guiainp($ginp, $prodcod);
                for ($j = 0; $j < count($series_datos); $j++) {
                    $serie = $series_datos[$j]->SERIC_Numero;
                    $numero = $series_datos[$j]->SERIP_Codigo;
                    //eliminar las series 
                    $this->db->delete('cji_seriemov', array("SERIP_Codigo" => $numero));
                    $this->db->delete('cji_serie', array("SERIP_Codigo" => $numero));
                }

//CUANDO SE TRATA DE VENDER         
            } else {
                //eliminacion logica de la guia 
                $data = array("GUIASAC_FlagEstado" => '0');
                $where = array("GUIASAP_Codigo" => $gsap);
                $this->db->where($where);
                $this->db->update('cji_guiasa', $data);

                //obtener el almacen        
                $guiasap_datos = $this->guiasa_model->obtener($gsap);
                if ($guiasap_datos):
                    $almacencod = $guiasap_datos->ALMAP_Codigo;
                    $docupcod = 6;

                    //buscar lote 
                    $lote_datos = $this->kardex_model->obtener_registros_x_dcto($prodcod, $docupcod, $gsap);
                    $codlote = $lote_datos[0]->LOTP_Codigo;

                    //obtener el valor del stock

                    $almacenproducto_datos = $this->almacenproducto_model->obtener($almacencod, $prodcod);
                    $almacenprodcod = $almacenproducto_datos[0]->ALMPROD_Codigo;
                    $stock = $almacenproducto_datos[0]->ALMPROD_Stock;
                    $costo = $almacenproducto_datos[0]->ALMPROD_CostoPromedio;


                    $productoundad = $this->productounidad_model->obtener($prodcod, $unid_medida);
                    if ($productoundad) {
                        $flagPrincipal = $productoundad->PRODUNIC_flagPrincipal;
                        $factor = $productoundad->PRODUNIC_Factor;
                        if ($flagPrincipal == 0) {
                            //  $cantidad = 0;
                            if ($factor > 0)
                                $cantidad = $cantidad / $factor;
                        }
                    }

                    $nuevostock = $stock + $cantidad;

                    //aumento almacenprolete
                    //$this->almaprolote_model->aumentar($almacenprodcod,$codlote,$prodcantidad,$costo);
                    //Eliminar Kardex
                    $this->kardex_model->eliminar($docupcod, $gsap, $prodcod);

                    //obtener cuenta 
                    $cuentaspago_datos = $this->cuentaspago_model->obtener($comprobante);
                    if (count($cuentaspago_datos) > 0) {
                        $codpago = $cuentaspago_datos[0]->PAGP_Codigo;
                        //eliminar pago
                        $this->pago_model->anular($codpago);
                    }
                    //eliminar las cuentas
                    $this->cuentaspago_model->eliminar($comprobante);
                    $this->cuentas_model->eliminar($comprobante);

                    //----------
                    //actualizar stock
                    $data = array("ALMPROD_Stock" => $nuevostock);
                    $where = array("ALMAC_Codigo" => $almacencod, "PROD_Codigo" => $prodcod, "COMPP_Codigo" => $compania);
                    $this->db->where($where);
                    $this->db->update('cji_almacenproducto', $data);

                    //obtenemos los datos de las series 

                    $series_datos = $this->seriemov_model->buscar_x_guiasap($gsap, $prodcod);
                    for ($j = 0; $j < count($series_datos); $j++) {
                        $serie = $series_datos[$j]->SERIC_Numero;
                        $numero = $series_datos[$j]->SERIP_Codigo;
                        //--obtener la guia de entrada por el serip_codigo
                        $guiaentrada_datos = $this->seriemov_model->obtener($numero);
                        $guiainps = $guiaentrada_datos[0]->GUIAINP_Codigo;
                        //Inserto datos en la serie
                        $data = array(
                            'PROD_Codigo' => $prodcod,
                            'SERIC_Numero' => $serie,
                            'SERIC_FlagEstado' => '1'
                        );
                        $this->db->insert('cji_serie', $data);
                        $seri = $this->db->insert_id();
                        //Inserto datos en la serieMOV
                        $datas = array(
                            'SERIP_Codigo' => $seri,
                            'SERMOVP_TipoMov' => '1',
                            'GUIAINP_Codigo' => $guiainps);
                        $this->db->insert('cji_seriemov', $datas);

                        //almacen producto
                        $datax = array('ALMPROD_Codigo' => $almacenprodcod,
                            'SERIP_Codigo' => $seri);
                        $this->db->insert('cji_almacenproductoserie', $datax);

                        //almacen producto serie
                        //eliminar las series 
                        $this->db->delete('cji_seriemov', array("SERIP_Codigo" => $numero));
                        $this->db->delete('cji_serie', array("SERIP_Codigo" => $numero));
                    }
                endif;
            }
        }




        $data = array("CPC_FlagEstado" => '0', "USUA_anula" => $userCod);
        $where = array("CPP_Codigo" => $comprobante);
        $this->db->where($where);
        $this->db->update('cji_comprobante', $data);

           $data = array("CPDEC_FlagEstado" => '0');
           $where = array("CPP_Codigo" => $comprobante);
           $this->db->where($where);
           $this->db->update('cji_comprobantedetalle', $data); 

        //anular comprobante
        //anular detalle comprobante
        //anular las guias
        //calcular el stock de los almacenes
        //devolver o eliminar las series segun el tipo de anulacion
    }

    public function buscar_x_numero_presupuesto($tipo_oper, $tipo_docu, $presupuesto) {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_TipoDocumento" => $tipo_docu, "CPC_FlagEstado" => "1", "PRESUP_Codigo" => $presupuesto);
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_x_numero_presupuesto_cualquiera($tipo_oper, $tipo_docu, $presupuesto) {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper, "CPC_FlagEstado" => "1", "PRESUP_Codigo" => $presupuesto);
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_x_numero_ocompra($tipo_oper, $ocompra) {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper,
            "CPC_FlagEstado" => "1", "OCOMP_Codigo" => $ocompra);
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    
     // gcbq
     //parametros:
    // tipo_orden : para la operacion, COMPRA o VENTA en la OC
    // tipo_guia : para la operacion, COMPRA o VENTA en la GUIA
    // cod_orden : codigo de la OC
    // cod_prod : codigo del producto
    public function buscar_x_producto_orden($tipo_orden, $tipo_guia, $cod_orden, $cod_prod) {
        $compania = $this->somevar['compania'];
        $where = array(
            "c.COMPP_Codigo" => $compania, "c.CPC_FlagEstado" => "1",
            "o.OCOMP_Codigo" => $cod_orden, "PROD_Codigo" => $cod_prod,
            "o.OCOMC_TipoOperacion" => $tipo_orden, "CPC_TipoOperacion" => $tipo_guia
        );

        $this->db->from('cji_comprobante c');
        $this->db->join('cji_comprobantedetalle cd', 'cd.CPP_Codigo = c.CPP_Codigo');
        $this->db->join('cji_ordencompra o', 'c.OCOMP_Codigo = o.OCOMP_Codigo');
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function buscar_x_orden($tipo_orden, $tipo_guia, $cod_orden) {
        $compania = $this->somevar['compania'];
        $where = array(
            "c.COMPP_Codigo" => $compania, "c.CPC_FlagEstado" => "1",
            "o.OCOMP_Codigo" => $cod_orden, "o.OCOMC_TipoOperacion" => $tipo_orden,
            "CPC_TipoOperacion" => $tipo_guia
        );

        $this->db->from('cji_comprobante c');
        $this->db->join('cji_comprobantedetalle cd', 'cd.CPP_Codigo = c.CPP_Codigo');
        $this->db->join('cji_ordencompra o', 'c.OCOMP_Codigo = o.OCOMP_Codigo');
        $query = $this->db->order_by('CPC_Numero', 'desc')->where($where)->group_by('c.CPP_Codigo')->get('');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        else
            return array();
    }

    public function buscar_x_numero_guiarem($guiarem) {
        $compania = $this->somevar['compania'];

        $where = array("COMPP_Codigo" => $compania,
            "CPC_FlagEstado" => "1", "GUIAREMP_Codigo" => $guiarem);
        $query = $this->db->where($where)->get('cji_comprobante');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function ultimo_serie_numero($tipo_oper, $tipo_docu) {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo" => $compania, "CPC_TipoOperacion" => $tipo_oper, "CPC_TipoDocumento" => $tipo_docu);
        $query = $this->db->order_by('CPC_Serie', 'desc')->order_by('CPC_Numero', 'desc')->where($where)->get('cji_comprobante', 1);
        $result['serie'] = "001";
        $result['numero'] = "1";
        if ($query->num_rows() > 0) {
            $data = $query->result();
            $result['serie'] = $data[0]->CPC_Serie;
            $result['numero'] = (int) $data[0]->CPC_Numero + 1;
        }
        return $result;
    }

    //REPORTES

    public function reporte_ocompra_5_clie_mas_importantes() {
        $sql = "SELECT Q.total,Q.nombre
                FROM
                        (SELECT SUM(o.OCOMC_total) total,
                                (CASE p.CLIC_TipoPersona WHEN '1' THEN e.EMPRC_RazonSocial 
                                ELSE CONCAT(pe.PERSC_Nombre, ' ', pe.PERSC_ApellidoPaterno, 
                                ' ', pe.PERSC_ApellidoMaterno) END) nombre
                        FROM cji_ordencompra o
                        INNER JOIN cji_cliente p ON p.CLIP_Codigo=o.CLIP_Codigo
                        LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1'
                        LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0'
                        WHERE o.OCOMC_FlagEstado='1' AND o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%'
                        GROUP BY o.CLIP_Codigo)Q
                ORDER BY Q.total DESC
                LIMIT 5";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_oventa_monto_x_mes() {
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
                WHERE o.OCOMC_FlagEstado='1' AND o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
        //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no est� aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_oventa_cantidad_x_mes() {
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
            WHERE o.OCOMC_FlagEstado='1' AND  o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
        //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no est� aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function reporte_comparativo_compras_ventas($tipo_op) {
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

    public function buscar_comprobante_venta($fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso,$tipo_oper ,$number_items = '', $offset = '') {
        $where = '';
        if ($fechai != '' && $fechaf != '')
            $where = ' and o.OCOMC_FechaRegistro BETWEEN "' . $fechai . '" AND "' . $fechaf . '"';
        if ($proveedor != '')
            $where.=' and o.CLIP_Codigo=' . $proveedor;//PROVP_Codigo &&  CLIP_Codigo
        if ($producto != '')
            $where.=' and od.PROD_Codigo=' . $producto;
        if ($aprobado != '')
            $where.=' and o.OCOMC_FlagAprobado=' . $aprobado;
        if ($ingreso != '')
            $where.=' and o.OCOMC_FlagIngreso=' . $ingreso;
        $limit = "";
        if ((string) $offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT DATE_FORMAT(o.OCOMC_FechaRegistro, '%d/%m/%Y') fecha,
                         o.OCOMP_Codigo,
                         o.PEDIP_Codigo,
                         o.PROVP_Codigo,
                         o.CENCOSP_Codigo,
                         o.OCOMC_Numero,
                         
                           (CASE WHEN o.COTIP_Codigo =0 THEN '***'
                           ELSE CAST(ct.COTIC_Numero AS char) END) cotizacion,
                       (CASE p.CLIC_TipoPersona WHEN '1'
                       THEN e.EMPRC_RazonSocial
                       ELSE CONCAT( pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
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
                INNER JOIN cji_cliente p ON p.CLIP_Codigo=o.CLIP_Codigo
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1'
                LEFT JOIN cji_cotizacion ct ON ct.COTIP_Codigo=o.COTIP_Codigo
                WHERE o.OCOMC_FlagEstado='1' " . $where . " AND o.OCOMC_TipoOperacion='".$tipo_oper."'
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

    public function buscar_comprobante_venta_2($anio) {
        //CPC_TipoOperacion => V venta, C compra
        //CPC_TipoDocumento => F factura, B boleta
        //CPC_total => total de la FACTURA o BOLETA
        $sql = " SELECT * FROM cji_comprobante c WHERE CPC_TipoOperacion='V' AND CPC_TipoDocumento='F' AND YEAR(CPC_FechaRegistro)=" . $anio . "";
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


    public function buscar_comprobante_compras($anio) {
        //CPC_TipoOperacion => V venta, C compra
        //CPC_TipoDocumento => F factura, B boleta
        //CPC_total => total de la FACTURA o BOLETA
        $sql = " SELECT c.*, m.MONED_Simbolo FROM cji_comprobante c  inner JOIN cji_moneda m ON m.MONED_Codigo=c.MONED_Codigo WHERE c.CPC_TipoOperacion='C' AND c.CPC_TipoDocumento='F' AND YEAR(c.CPC_FechaRegistro)=" . $anio . "";
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

    public function estadisticas_compras_ventas($tipo, $anio) {
        $sql = "SELECT p.CLIP_Codigo,e.EMPRC_RazonSocial,pe.PERSC_Nombre,MONTH(c.CPC_FechaRegistro) 
                AS mes,c.CPC_FechaRegistro,SUM(c.CPC_total) AS monto 
                FROM cji_cliente p 
                INNER JOIN cji_comprobante c ON p.CLIP_Codigo = c.CLIP_Codigo
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1'
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0' 
                WHERE c.CPC_TipoOperacion='" . $tipo . "' AND YEAR(CPC_FechaRegistro)=" . $anio . " AND CPC_TipoDocumento='F' 
                GROUP BY c.CLIP_Codigo,MONTH(CPC_FechaRegistro)
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

    public function anios_para_reportes($tipo) {
        $sql = "SELECT YEAR(CPC_FechaRegistro) as anio FROM cji_comprobante WHERE CPC_TipoOperacion='" . $tipo . "' GROUP BY YEAR(CPC_FechaRegistro)";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        return array();
    }

    public function estadisticas_compras_ventas_mensual($tipo, $anio, $mes) {
        $sql = "
                SELECT p.CLIP_Codigo,e.EMPRC_RazonSocial,e.EMPRC_Ruc,pe.PERSC_Nombre,pe.PERSC_NumeroDocIdentidad,MONTH(c.CPC_FechaRegistro) AS mes,
                c.CPC_FechaRegistro,c.CPC_subtotal,c.CPC_igv,c.CPC_total AS monto,c.COMPP_Codigo
                FROM cji_cliente p 
                INNER JOIN cji_comprobante c ON p.CLIP_Codigo = c.CLIP_Codigo
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1' 
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0' 
                WHERE c.COMPP_Codigo='".$this->somevar ['compania']."' and c.CPC_TipoOperacion='" . $tipo . "' AND MONTH(CPC_FechaRegistro) ='" . $mes . "' AND YEAR(CPC_FechaRegistro) ='" . $anio . "' AND CPC_TipoDocumento='F' 
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
    

    /**modificar estado segun documento y codigo asociados**/
    public function modificarEstadoDocumetoCodigoAsociado($codigo,$estado){
        $filter->COMPGUI_FlagEstado = $estado;
        $where = array("CPP_Codigo" => $codigo);
        $this->db->where($where);
        $this->db->update('cji_comprobante_guiarem', (array) $filter);
    }
    
    public function buscarComprobanteGuiarem($comprobante,$estadoAsociacion){
        $this->db->from('cji_comprobante_guiarem cg');
        $this->db->join('cji_guiarem g', 'g.GUIAREMP_Codigo=cg.GUIAREMP_Codigo');
        
        if($estadoAsociacion!=null && trim($estadoAsociacion)!="")
            $this->db->where("cg.COMPGUI_FlagEstado =",$estadoAsociacion);
        
        
        $this->db->where("cg.CPP_Codigo",$comprobante);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    /**obtener comprobantes tipo:N que crearon el comprbante tipo :F,B**/
    public function buscarComprobanteRelacionadoCanje($comprobanteCanje){
        $this->db->from('cji_comprobante c');
        $this->db->where("c.CPP_Codigo_Canje",$comprobanteCanje);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }
public function verificar_inventariado($cod){
    $this->db->select('PROD_Codigo');
    $this->db->where('PROD_Codigo',$cod);
     $query = $this->db->get('cji_inventariodetalle');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
 }   
   
 public function buscar_comprobante_producto($anio ,$mes ,$fech1 ,$fech2,$tipodocumento,$Prodcod) {
    
    $where="";
    //----------
    if($anio!="--" && $mes =="--" ){// SOLO AÃ‘O
        $where="AND YEAR(CPC_FechaRegistro)='" . $anio . "'";
    }
    if($anio!="--" && $mes !="--" ){// MES Y  AÃ‘O
        $where="AND YEAR(CPC_FechaRegistro)='" . $anio . "' AND MONTH(CPC_FechaRegistro)='" . $mes ."'";
    }
    if($anio=="--" && $mes !="--"){//MES CON AÃ‘O ACTUAL
        $where="AND YEAR(CPC_FechaRegistro)=' ".date("Y")."' AND MONTH(CPC_FechaRegistro)='" . $mes ."'";
    }
 
    //-----------------
     
    if($anio=="--" && $mes =="--" && $fech1!="--" && $fech2=="--" ){//FECHA INICIAL
        $where="AND CPC_FechaRegistro > '" . $fech1 . "'";
    }
    if($anio=="--" && $mes =="--" && $fech1!="--" && $fech2!="--"){//FECHA INICIAL Y FECHA FINAL
        $where="AND CPC_FechaRegistro >= '" . $fech1 . "' AND CPC_FechaRegistro <= '" . $fech2 . "'";
    }
 
    
    $wheretdoc= "";
    if($tipodocumento !="--")
        $wheretdoc= " AND CPC_TipoDocumento='".$tipodocumento."' ";
 
        $wherepro= "";
        if($Prodcod !="--")
            $wherepro= " AND PROD_Codigo='".$Prodcod."' ";//CPDEP_Codigo
 
            $sql = " SELECT com.*,CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as nombre , MONED_Simbolo from cji_comprobante com
         inner join cji_comprobantedetalle cd on cd.CPP_Codigo = com.CPP_Codigo
                    inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
        inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
        inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
        WHERE CPC_TipoOperacion='V' ".$wheretdoc.$where.$wherepro."
            
        UNION
        SELECT com.* ,EMPRC_RazonSocial as nombre ,MONED_Simbolo from cji_comprobante com
         inner join cji_comprobantedetalle cd on cd.CPP_Codigo = com.CPP_Codigo
        inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
        inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
        inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
        WHERE CPC_TipoOperacion='V' ".$wheretdoc.$where.$wherepro."";
            
//              $sql = " SELECT com.* from cji_comprobante com
//         inner join cji_comprobantedetalle cd on cd.CPP_Codigo = com.CPP_Codigo
//         WHERE CPC_TipoOperacion='V' ".$wherepro.$wheretdoc.$where."";
 
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
 public function autocompleteProducto($keyword){
    try {
        $sql = "SELECT  PROD_Nombre,PROD_Codigo FROM cji_producto where PROD_Nombre LIKE '%" . $keyword . "%' and PROD_FlagEstado = 1 ";
 
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

 public function buscar($filter)
 {
    $query = $this->db->from("cji_importacion");

    if(isset($filter->proveedor)) $query->where("PROVP_Codigo", $filter->proveedor);
    if(isset($filter->liquidada)) {
        $query->where("IMPOR_Liquidada", $filter->liquidada);

        if($filter->liquidada == 1) $query->where("GUIAREMP_Codigo", null);
    }

    return $query->get()->result();
 }



     public function verificar_cantidad($codigo,$prod)
    {
    $sql = "
    SELECT od.OCOMDEC_Cantidad,od.OCOMDEC_Pendiente,id.IMPORDEC_Cantidad
FROM cji_importacion i
inner join cji_ocompradetalle od on od.OCOMP_Codigo=i.OCOMP_Codigo
inner join cji_importaciondetalle id on id.IMPOR_Codigo=i.IMPOR_Codigo
WHERE od.OCOMP_Codigo=$codigo and od.PROD_Codigo=$prod and id.PROD_Codigo=$prod ";


            $query = $this->db->query($sql);
            $respuesta = $query->result();
            //var_dump($respuesta[0]);
            return $respuesta[0];
           
        }
         public function calcula_cantidad_pendiente($codigo,$prod)
        {
        $sql = "
         SELECT OCOMDEC_Cantidad,OCOMDEC_Pendiente
        FROM cji_ocompradetalle od
        WHERE OCOMP_Codigo=$codigo and PROD_Codigo=$prod ";

                $query = $this->db->query($sql);
                $respuesta = $query->result();
                //var_dump($respuesta[0]);
                return $respuesta[0];
           
        }

         public function ocompra_serienumero($id)
        {
            $sql = "
             SELECT o.OCOMC_Serie,o.OCOMC_Numero, o.OCOMP_Codigo,o.OCOMC_FlagTerminadoProceso,o.OCOMC_FlagTerminado
            FROM cji_importacion i
            inner join cji_ordencompra o on o.OCOMP_Codigo=i.OCOMP_Codigo
            WHERE i.IMPOR_Codigo=$id";
  
                    $query = $this->db->query($sql);
                    $respuesta = $query->result();
            
                    return $respuesta[0];
               
        }

    public function obtener_referencia_importacion_by_id_detalle_compra($id_detalle_compra)
    {
        $result = $this->db->from("cji_importaciondetalle")
                            ->where(array("cji_importaciondetalle.OCOMDEP_Codigo" => $id_detalle_compra, "cji_importaciondetalle.IMPORDEC_FlagEstado" => 1))
                            ->join("cji_ocompradetalle", "cji_ocompradetalle.OCOMDEP_Codigo = cji_importaciondetalle.OCOMDEP_Codigo")
                            ->select("cji_ocompradetalle.*")->get()->result();

        if(count($result) == 0) return null;

        return $result[0];
    }
    
}

?>