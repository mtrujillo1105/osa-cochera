<?php

class Letracambio_model extends CI_Model {

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
        $this->load->model('ventas/comprobantedetalle_model');
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

    public function buscar_comprobantes($tipo_oper = 'V', $tipo_docu = 'F', $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '') {
        $compania = $this->somevar['compania'];

        $where = '';
        if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
            $where .= ' and cp.LET_Fecha BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
        if (isset($filter->seriei) && $filter->seriei != '')
            $where.=' and cp.LET_Serie="' . $filter->seriei . '"';
        if (isset($filter->numero) && $filter->numero != '')
            $where.=' and cp.LET_Numero=' . $filter->numero;


        if ($tipo_oper != 'C') {
            if (isset($filter->cliente) && $filter->cliente != '')
                $where.=' and cp.CLIP_Codigo=' . $filter->cliente;
        }
        else {
            if (isset($filter->proveedor) && $filter->proveedor != '')
                $where.=' and cp.PROVP_Codigo=' . $filter->proveedor;
        }
//        if (isset($filter->producto) && $filter->producto != '')
//            $where.=' and cpd.PROD_Codigo=' . $filter->producto;
//        $limit = "";
        if ((string) $offset != '' && $number_items != '')
            $limit = 'LIMIT ' . $offset . ',' . $number_items;

        $sql = "SELECT cp.LET_Fecha,
                       cp.LET_Codigo,
                       cp.LET_Serie,
                       cp.LET_Numero,
                       cp.LET_Codigo_canje,
                       cp.LET_GuiaRemCodigo,
                       cp.LET_DocuRefeCodigo,
                       cp.LET_NombreAuxiliar,
                       cp.CLIP_Codigo,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       cp.LET_total,
                       cp.LET_FlagEstado
                FROM cji_letra cp
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo                
                " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
                WHERE cp.LET_TipoOperacion='" . $tipo_oper . "' 
                      AND cp.LET_TipoDocumento='" . $tipo_docu . "' AND cp.COMPP_Codigo =" . $compania . " " . $where . "
                GROUP BY cp.LET_Codigo
                ORDER BY cp.LET_Fecha DESC, cp.LET_Numero DESC  " . $limit; 
        
        
        
        
//                $sql = "SELECT cp.CPC_Fecha,
//                       cp.CPP_Codigo,
//                       cp.CPC_Serie,
//                       cp.CPC_Numero,
//                       cp.CPP_Codigo_canje,
//                       cp.CPC_GuiaRemCodigo,
//                       cp.CPC_DocuRefeCodigo,
//                       cp.CPC_NombreAuxiliar,
//                       cp.CLIP_Codigo,
//                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) numdoc,
//                       (CASE " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "  WHEN '1' THEN e.EMPRC_RazonSocial ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
//                       m.MONED_Simbolo,
//                       cp.CPC_total,
//                       cp.CPC_FlagEstado
//                FROM cji_letra cp
//                LEFT JOIN cji_moneda m ON m.MONED_Codigo=cp.MONED_Codigo
//                LEFT JOIN cji_letradetalle cpd ON cpd.CPP_Codigo=cp.CPP_Codigo
//                " . ($tipo_oper != 'C' ? "INNER JOIN cji_cliente c ON c.CLIP_Codigo=cp.CLIP_Codigo" : "LEFT JOIN cji_proveedor c ON c.PROVP_Codigo=cp.PROVP_Codigo") . "
//                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . " ='0'
//                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND " . ($tipo_oper != 'C' ? "c.CLIC_TipoPersona" : "c.PROVC_TipoPersona") . "='1'
//                WHERE cp.CPC_TipoOperacion='" . $tipo_oper . "' 
//                      AND cp.CPC_TipoDocumento='" . $tipo_docu . "' AND cp.COMPP_Codigo =" . $compania . " " . $where . "
//                GROUP BY cp.CPP_Codigo
//                ORDER BY cp.CPC_Fecha DESC, cp.CPC_Numero DESC  " . $limit; 

        
        
        
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
        $query = $this->db->where('LET_Codigo', $comprobante)->get('cji_letra');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function buscar_xserienum($serie, $numero, $doc, $oper) {
        $where = array('LET_Serie' => $serie,
            'LET_Numero' => $numero,
            'LET_TipoDocumento' => $doc,
            'LET_TipoOperacion' => $oper
        );
        $this->db->where($where);
        $query = $this->db->get('cji_letra');
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

    public function insertar_comprobante($filter = null) {
        $compania = $this->somevar['compania'];
        $user = $this->somevar ['user'];

        $filter->COMPP_Codigo = $compania;
        $filter->USUA_Codigo = $user;
        $this->db->insert("cji_letra", (array) $filter);

        $comprobante = $this->db->insert_id();
        switch ($filter->LET_TipoDocumento) {
            case 'F': $codtipodocu = '16';
                break;
            case 'B': $codtipodocu = '9';
                break;
            case 'N': $codtipodocu = '14';
                break;
            default: $codtipodocu = '0';
                break;
        }
        if ($filter->LET_TipoOperacion == 'V')
            $this->configuracion_model->modificar_configuracion($compania, $codtipodocu, $filter->LET_Numero);
//
//        $filter2 = new stdClass();
//        $filter2->CUE_TipoCuenta = $filter->CPC_TipoOperacion == 'V' ? 1 : 2;
//        $filter2->DOCUP_Codigo = $codtipodocu;
//        $filter2->CUE_CodDocumento = $comprobante;
//        $filter2->MONED_Codigo = $filter->MONED_Codigo;
//        $filter2->CUE_Monto = $filter->CPC_total;
//        $filter2->CUE_FechaOper = $filter->CPC_Fecha;
//        $filter2->COMPP_Codigo = $compania;
//        $filter2->CUE_FlagEstado = '1';
//        if (isset($filter->FORPAP_Codigo) && $filter->FORPAP_Codigo == 1){
//            $filter2->CUE_FlagEstadoPago='C';
//        }
//        $cuenta = $this->cuentas_model->insertar($filter2);
//
//        if (isset($filter->FORPAP_Codigo) && $filter->FORPAP_Codigo == 1) {  //Si el pago es al contado           
//            $filter3 = new stdClass();
//            $filter3->PAGC_TipoCuenta = $filter->CPC_TipoOperacion == 'V' ? 1 : 2;
//            $filter3->PAGC_FechaOper = $filter->CPC_Fecha;
//            if ($filter3->PAGC_TipoCuenta == 1)
//                $filter3->CLIP_Codigo = $filter->CLIP_Codigo;
//            else
//                $filter3->PROVP_Codigo = $filter->PROVP_Codigo;
//            $filter4 = new stdClass();
//            $filter4->TIPCAMC_Fecha = $filter->CPC_Fecha;
//            $filter4->TIPCAMC_MonedaDestino = '2';
//            $temp = $this->tipocambio_model->buscar($filter4);
//            $tdc = is_array($temp) ? $temp[0]->TIPCAMC_FactorConversion : '';
//
//            $filter3->PAGC_TDC = $tdc;
//            $filter3->PAGC_Monto = $filter->CPC_total;
//            $filter3->MONED_Codigo = $filter->MONED_Codigo;
//            $filter3->PAGC_FormaPago = '1'; //Efectivo
//
//            $filter3->PAGC_Obs = ($filter->CPC_TipoOperacion == 'V' ? 'INGRESO GENERADO' : 'SALIDA GENERADA') . ' AUTOMATICAMENTE POR EL PAGO AL CONTADO';
//            $filter3->PAGC_Saldo = '0';
//
//            $cod_pago = $this->pago_model->insertar($filter3,'', '', '');
//
//            $filter5 = new stdClass();
//            $filter5->CUE_Codigo = $cuenta;
//            $filter5->PAGP_Codigo = $cod_pago;
//            $filter5->CPAGC_TDC = $tdc;
//            $filter5->CPAGC_Monto = $filter->CPC_total;
//            $filter5->MONED_Codigo = $filter->MONED_Codigo;
//
//            $this->cuentaspago_model->insertar($filter5);
//            $filter3 = new stdClass();
//        }

        return $comprobante;
    }

    public function insertar_comprobante2($filter) {

        $this->db->insert("cji_comprobante", (array) $filter);

        $comprobante = $this->db->insert_id();

        return $comprobante;
    }

    
    ////por mientras asta ver letras stock
    public function aprobar_estadoletra($letra, $filter = null) {
    
        $data = array(
            "LET_FlagEstado" => 1
        );
        $this->db->where('LET_Codigo', $letra);
        $this->db->update("cji_letra", $data);
        
    }
    /////
    
    
    public function insertar_disparador($comprobante, $filter = null) {

        $compania = $this->somevar['compania'];
        $user = $this->somevar ['user'];
        switch ($filter->CPC_TipoDocumento) {
            case 'F': $codtipodocu = '16';
                break;
            case 'B': $codtipodocu = '9';
                break;
            case 'N': $codtipodocu = '14';
                break;
            default: $codtipodocu = '0';
                break;
        }

        $data = array(
            "LET_FlagEstado" => 1
        );
        $this->db->where('LET_Codigo', $comprobante);
        $this->db->update("cji_letra", $data);


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

    public function modificar_comprobante($comprobante, $filter = null) {
        $user = $this->somevar ['user'];
        $filter->USUA_Codigo = $user;

        $where = array("LET_Codigo" => $comprobante);
        $this->db->where($where);
        $this->db->update('cji_letra', (array) $filter);
    }

    public function eliminar_comprobante($comprobante, $userCod) {

        $compania = $this->somevar['compania'];
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
        /*
          $data = array("CPDEC_FlagEstado" => '0');
          $where = array("CPP_Codigo" => $comprobante);
          $this->db->where($where);
          $this->db->update('cji_comprobantedetalle', $data); */

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

    public function buscar_comprobante_venta($fechai, $fechaf, $proveedor, $producto, $aprobado, $ingreso, $number_items = '', $offset = '') {
        $where = '';
        if ($fechai != '' && $fechaf != '')
            $where = ' and o.OCOMC_FechaRegistro BETWEEN "' . $fechai . '" AND "' . $fechaf . '"';
        if ($proveedor != '')
            $where.=' and o.PROVP_Codigo=' . $proveedor;
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
                WHERE o.OCOMC_FlagEstado='1' " . $where . " AND o.OCOMC_TipoOperacion='V'
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
        $sql = " SELECT * FROM cji_comprobante c WHERE CPC_TipoOperacion='C' AND CPC_TipoDocumento='F' AND YEAR(CPC_FechaRegistro)=" . $anio . "";
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
				c.CPC_FechaRegistro,c.CPC_subtotal,c.CPC_igv,c.CPC_total AS monto
				FROM cji_cliente p 
				INNER JOIN cji_comprobante c ON p.CLIP_Codigo = c.CLIP_Codigo
				LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=p.EMPRP_Codigo AND p.CLIC_TipoPersona='1' 
				LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=p.PERSP_Codigo AND p.CLIC_TipoPersona='0' 
				WHERE c.CPC_TipoOperacion='" . $tipo . "' AND MONTH(CPC_FechaRegistro) ='" . $mes . "' AND YEAR(CPC_FechaRegistro) ='" . $anio . "' AND CPC_TipoDocumento='F' 
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