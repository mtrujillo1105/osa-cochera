<?php
class Credito_model extends CI_Model{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->model('configuracion_model');
        $this->load->model('tesoreria/cuentas_model');
        $this->load->model('tesoreria/pago_model');
        $this->load->model('tesoreria/cuentaspago_model');
        $this->load->model('maestros/tipocambio_model');
        
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    public function listar_creditos($tipo_oper='V',$tipo_docu='F',$number_items='',$offset='')
    {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo"=>$compania,"CRED_TipoOperacion"=>$tipo_oper,
                      "CRED_TipoDocumento"=>$tipo_docu);
        $query = $this->db->order_by('CRED_FechaRegistro','DESC')->where($where)->get('cji_credito',$number_items,$offset);  //order_by('CPC_Serie','desc')->order_by('CPC_Numero','desc')
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function listar_comprobantes_factura($tipo_oper='V',$tipo_docu='C',$number_items='',$offset='')
    {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo"=>$compania,"CRED_TipoOperacion"=>$tipo_oper,
                      "CRED_TipoDocumento"=>$tipo_docu);
        $query = $this->db->order_by('CRED_Serie','desc')->order_by('CRED_Numero','desc')->where($where)->get('cji_credito',$number_items,$offset);
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function buscar_creditos($tipo_oper='V',$tipo_docu='C', $filter=NULL, $number_items='',$offset='')
    {   $compania = $this->somevar['compania'];
    
        $where='';
        if(isset($filter->fechai) && $filter->fechai!='' && isset($filter->fechaf) && $filter->fechaf!='')
            $where=' and cp.CRED_Fecha BETWEEN "'.human_to_mysql($filter->fechai).'" AND "'.human_to_mysql($filter->fechaf).'"';
        if(isset($filter->serie) && $filter->serie!='' && isset($filter->numero) && $filter->numero!='')
            $where.=' and cp.CRED_Serie="'.$filter->serie.'" and cp.CRED_Numero='.$filter->numero;

        if($tipo_oper!='C')
            if(isset($filter->cliente) && $filter->cliente!='')
                $where.=' and cp.CLIP_Codigo='.$filter->cliente;
        else
            if(isset($filter->proveedor) && $filter->proveedor!='')
                $where.=' and cp.PROVP_Codigo='.$filter->proveedor;
            
        if(isset($filter->producto) && $filter->producto!='')
            $where.=' and cpd.PROD_Codigo='.$filter->producto;
        $limit="";
        if((string)$offset!='' && $number_items!='')
            $limit = 'LIMIT '.$offset.','.$number_items;
        $sql = "SELECT cp.CRED_Fecha,
                       cp.CRED_Codigo,
                       cp.CRED_Serie,
                       cp.CRED_Numero,
                       cp.CRED_GuiaRemCodigo,
                       cp.CRED_DocuRefeCodigo,
                       (CASE ".($tipo_oper!='C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona")."  WHEN '1'THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.CRED_total,
                       cp.CRED_FlagEstado
                FROM cji_credito cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
                LEFT JOIN cji_creditodetalle cpd ON cpd.CRED_Codigo=cp.CRED_Codigo
                ".($tipo_oper!='C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "INNER JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo")."
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND ".($tipo_oper!='C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona")." ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND ".($tipo_oper!='C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona")."='1'
                WHERE cp.CRED_TipoOperacion='".$tipo_oper."' 
                      AND cp.CRED_TipoDocumento='".$tipo_docu."' AND cp.COMPP_Codigo =".$compania." ".$where."
                GROUP BY cp.CRED_Codigo
                ORDER BY cp.CRED_FechaRegistro DESC ".$limit;  //cp.CPC_Serie DESC, cp.CPC_Numero DESC
        $query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
    public function obtener_credito($credito)
    {
        $query = $this->db->where('CRED_Codigo',$credito)->get('cji_credito');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila)
            {
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function insertar_credito($filter=null)
    {   
        $compania=$this->somevar['compania'];
        $user=$this->somevar ['user'];
        
        $filter->COMPP_Codigo=$compania;
        $filter->USUA_Codigo=$user;
        $this->db->insert("cji_credito",(array)$filter);
        
        $comprobante = $this->db->insert_id();
        switch($filter->CRED_TipoDocumento){
            case 'C': $codtipodocu='8'; break;
            case 'D': $codtipodocu='9'; break;
            case 'N': $codtipodocu='14'; break;
            default:  $codtipodocu='0'; break;
        }
        if($filter->CRED_TipoOperacion=='V')            
            $this->configuracion_model->modificar_configuracion($compania,$codtipodocu,$filter->CRED_Numero);

        $filter2=new stdClass();
        $filter2->CUE_TipoCuenta=$filter->CRED_TipoOperacion=='V' ? 1 : 2;
        $filter2->DOCUP_Codigo=$codtipodocu;
        $filter2->CUE_CodDocumento=$comprobante;
        $filter2->MONED_Codigo=$filter->MONED_Codigo;
        $filter2->CUE_Monto=$filter->CRED_total;
        $filter2->CUE_FechaOper=$filter->CRED_Fecha;
        $filter2->COMPP_Codigo=$compania;
        $filter2->CUE_FlagEstado='1';
        $cuenta=$this->cuentas_model->insertar($filter2);
        
        if(isset($filter->FORPAP_Codigo) && $filter->FORPAP_Codigo==1){  //Si el pago es al contado           
            $filter3=new stdClass();
            $filter3->PAGC_TipoCuenta=$filter->CRED_TipoOperacion=='V' ? 1 : 2;
            $filter3->PAGC_FechaOper=$filter->CRED_Fecha;
            if($filter3->PAGC_TipoCuenta==1)
                $filter3->CLIP_Codigo=$filter->CLIP_Codigo;
            else
                $filter3->PROVP_Codigo=$filter->PROVP_Codigo;
            $filter4=new stdClass();
            $filter4->TIPCAMC_Fecha=$filter->CRED_Fecha;
            $filter4->TIPCAMC_MonedaDestino ='2';
            $temp=$this->tipocambio_model->buscar($filter4);
            $tdc=is_array($temp) ? $temp[0]->TIPCAMC_FactorConversion : '';
                    
            $filter3->PAGC_TDC=$tdc;
            $filter3->PAGC_Monto=$filter->CRED_total;
            $filter3->MONED_Codigo=$filter->MONED_Codigo;
            $filter3->PAGC_FormaPago='1'; //Efectivo
        
            $filter3->PAGC_Obs=($filter->CRED_TipoOperacion=='V' ? 'INGRESO GENERADO' : 'SALIDA GENERADA').' AUTOMATICAMENTE POR EL PAGO AL CONTADO';
            $filter3->PAGC_Saldo='0';
        
            $cod_pago=$this->pago_model->insertar($filter3);
            
            $filter5=new stdClass();
            $filter5->CUE_Codigo=$cuenta;
            $filter5->PAGP_Codigo=$cod_pago;
            $filter5->CPAGC_TDC=$tdc;
            $filter5->CPAGC_Monto=$filter->CRED_total;
            $filter5->MONED_Codigo=$filter->MONED_Codigo;

            $this->cuentaspago_model->insertar($filter5);
            
            $pago=$this->cuentas_model->modifica_estado($cuenta->CUE_Codigo,'C' );
            
            $filter3=new stdClass();
        }
                
        return $comprobante;
    }
    
    public function modificar_comprobante($comprobante,$filter=null)
    {   $user=$this->somevar ['user'];
        $filter->USUA_Codigo=$user;
        
        $where = array("CRED_Codigo"=>$comprobante);
        $this->db->where($where);
        $this->db->update('cji_credito',(array)$filter);
    }
   
    public function eliminar_comprobante($comprobante)
    {
        $data      = array("cred_FlagEstado"=>'0');
        $where = array("CRED_Codigo"=>$comprobante);
        $this->db->where($where);
        $this->db->update('cji_credito',$data);
        
        $data      = array("CREDET_FlagEstado"=>'0');
        $where = array("CRED_Codigo"=>$comprobante);
        $this->db->where($where);
        $this->db->update('cji_creditodetalle',$data);
    }
    
    public function buscar_x_numero_presupuesto($tipo_oper,$tipo_docu, $presupuesto)
    {   $compania=$this->somevar['compania'];
    
        $where = array("COMPP_Codigo"=>$compania,"CRED_TipoOperacion"=>$tipo_oper,
                      "CRED_TipoDocumento"=>$tipo_docu, "CRED_FlagEstado"=>"1", "PRESUP_Codigo"=>$presupuesto);
        $query = $this->db->order_by('CRED_Numero','desc')->where($where)->get('cji_credito');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
	
	public function buscar_x_numero_presupuesto_cualquiera($tipo_oper,$tipo_docu, $presupuesto)
    {   $compania=$this->somevar['compania'];
    
        $where = array("COMPP_Codigo"=>$compania,"CRED_TipoOperacion"=>$tipo_oper, "CRED_FlagEstado"=>"1", "PRESUP_Codigo"=>$presupuesto);
        $query = $this->db->order_by('CRED_Numero','desc')->where($where)->get('cji_credito');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
	
    public function buscar_x_numero_ocompra($tipo_oper,$ocompra)
    {   $compania=$this->somevar['compania'];
    
        $where = array("COMPP_Codigo"=>$compania,"CRED_TipoOperacion"=>$tipo_oper,
                      "CRED_FlagEstado"=>"1", "OCOMP_Codigo"=>$ocompra);
        $query = $this->db->order_by('CPC_Numero','desc')->where($where)->get('cji_credito');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function buscar_x_numero_guiarem($guiarem)
    {   $compania=$this->somevar['compania'];
    
        $where = array("COMPP_Codigo"=>$compania,
                      "CRED_FlagEstado"=>"1", "GUIAREMP_Codigo"=>$guiarem);
        $query = $this->db->where($where)->get('cji_credito');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function ultimo_serie_numero($tipo_oper, $tipo_docu)
     {
        $compania = $this->somevar['compania'];
        $where = array("COMPP_Codigo"=>$compania, "CRED_TipoOperacion"=>$tipo_oper, "CRED_TipoDocumento"=>$tipo_docu);
        $query = $this->db->order_by('CRED_Serie','desc')->order_by('CRED_Numero','desc')->where($where)->get('cji_credito',1);
        $result['serie']="001";
        $result['numero']="1";
        if($query->num_rows() > 0){
            $data=$query->result();
            $result['serie']=$data[0]->CRED_Serie;
            $result['numero']=(int)$data[0]->CRED_Numero+1;
        }
        return $result;
     }
	 
	 //REPORTES
	 
	 public function reporte_ocompra_5_clie_mas_importantes()
    {       
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
        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
	 
	 public function reporte_oventa_monto_x_mes()
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
                WHERE o.OCOMC_FlagEstado='1' AND o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
                //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no est� aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);
        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
	
	public function reporte_oventa_cantidad_x_mes()
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
            WHERE o.OCOMC_FlagEstado='1' AND  o.OCOMP_Codigo<>0 AND o.OCOMC_TipoOperacion='V' AND o.OCOMC_FlagAprobado like '%' AND YEAR(o.OCOMC_FechaRegistro)=YEAR(CURDATE())";
            //NOTA: en donde dice: o.OCOMC_FlagAprobado like '%' hay que reemplzar el comodin % por 1, pero como el usuario no est� aprobando las O compra lo estoy reemplazando por % para q salga el reporte
        $query = $this->db->query($sql);
        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
	
	public function reporte_comparativo_compras_ventas($tipo_op){
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
            WHERE c.CPC_TipoOperacion='".$tipo_op."' AND c.CPC_FlagEstado='1' AND  c.CPP_Codigo<>0 AND YEAR(c.CPC_FechaRegistro)=YEAR(CURDATE())";
        $query = $this->db->query($sql);
        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
	}
	
	public function buscar_comprobante_venta($fechai, $fechaf, $proveedor , $producto, $aprobado, $ingreso,$number_items='',$offset='')
    {
		$where='';
        if($fechai!='' && $fechaf!='')
            $where=' and o.OCOMC_FechaRegistro BETWEEN "'.$fechai.'" AND "'.$fechaf.'"';
        if($proveedor!='')
            $where.=' and o.PROVP_Codigo='.$proveedor;
        if($producto!='')
            $where.=' and od.PROD_Codigo='.$producto;
        if($aprobado!='')
            $where.=' and o.OCOMC_FlagAprobado='.$aprobado;
        if($ingreso!='')
            $where.=' and o.OCOMC_FlagIngreso='.$ingreso;
        $limit="";
        if((string)$offset!='' && $number_items!='')
            $limit = 'LIMIT '.$offset.','.$number_items;
        
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
                       ELSE CONCAT(	pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
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
                WHERE o.OCOMC_FlagEstado='1' ".$where." AND o.OCOMC_TipoOperacion='V'
                GROUP BY o.OCOMP_Codigo
                ORDER BY o.OCOMC_Numero DESC ".$limit."
                ";
        //echo $sql;
        $query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
	
	public function buscar_comprobante_venta_2($anio)
    {
		//CPC_TipoOperacion => V venta, C compra
		//CPC_TipoDocumento => F factura, B boleta
		//CPC_total => total de la FACTURA o BOLETA
        $sql = " SELECT * FROM cji_credito c WHERE CRED_TipoOperacion='V' AND CRED_TipoDocumento='F' AND YEAR(CRED_FechaRegistro)=".$anio."";
        //echo $sql;
        $query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
	
	public function buscar_comprobante_compras($anio)
    {
		//CPC_TipoOperacion => V venta, C compra
		//CPC_TipoDocumento => F factura, B boleta
		//CPC_total => total de la FACTURA o BOLETA
        $sql = " SELECT * FROM cji_credito c WHERE CRED_TipoOperacion='C' AND CRED_TipoDocumento='C' AND YEAR(CRED_FechaRegistro)=".$anio."";
        //echo $sql;
        $query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
	
	public function estadisticas_compras_ventas($tipo,$anio)
    {
        $sql = "SELECT p.CLIP_Codigo,e.EMPRC_RazonSocial,pe.PERSC_Nombre,MONTH(c.CPC_FechaRegistro) 
				AS mes,c.CPC_FechaRegistro,SUM(c.CPC_total) AS monto 
				FROM cji_cliente p 
				INNER JOIN cji_comprobante c ON p.CLIP_Codigo = c.CLIP_Codigo
				LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1'
				LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0' 
				WHERE c.CPC_TipoOperacion='".$tipo."' AND YEAR(CPC_FechaRegistro)=".$anio." AND CPC_TipoDocumento='F' 
				GROUP BY c.CLIP_Codigo,MONTH(CPC_FechaRegistro)
				";
        //echo $sql;
        $query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
    }
	
	public function anios_para_reportes($tipo){
		$sql ="SELECT YEAR(CPC_FechaRegistro) as anio FROM cji_comprobante WHERE CPC_TipoOperacion='".$tipo."' GROUP BY YEAR(CPC_FechaRegistro)";
		$query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();
	}
	
	public function estadisticas_compras_ventas_mensual($tipo,$anio,$mes){
		$sql ="
				SELECT p.CLIP_Codigo,e.EMPRC_RazonSocial,e.EMPRC_Ruc,pe.PERSC_Nombre,pe.PERSC_NumeroDocIdentidad,MONTH(c.CPC_FechaRegistro) AS mes,
				c.CPC_FechaRegistro,c.CPC_subtotal,c.CPC_igv,c.CPC_total AS monto
				FROM cji_cliente p 
				INNER JOIN cji_comprobante c ON p.CLIP_Codigo = c.CLIP_Codigo
				LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1' 
				LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0' 
				WHERE c.CPC_TipoOperacion='".$tipo."' AND MONTH(CPC_FechaRegistro) ='".$mes."' AND YEAR(CPC_FechaRegistro) ='".$anio."' AND CPC_TipoDocumento='F' 
		";
		$query = $this->db->query($sql);        
        if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
        }
        return array();		
	}

}
?>