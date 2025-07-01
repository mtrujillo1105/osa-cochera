<?php
/* *********************************************************************************
Dev: Martín Trujillo
/* ******************************************************************************** */
class Rptventas_Model extends CI_Model
{

  public function __construct(){
      parent::__construct();
      $this->load->database();
      $this->compania = $this->session->userdata('compania');
  }

  public function ventas_por_vendedor_detallado($vendedor, $inicio, $fin){
      $sql = "
                SELECT  c.CPP_Codigo, 
                        c.CPC_Serie, 
                        c.CPC_Numero, 
                        c.CPC_Total, 
                        c.CPC_Fecha,
                        cli.CLIC_CodigoUsuario,
                        CONCAT_WS(' ', (
                            SELECT CONCAT_WS(' - ', emp.EMPRC_Ruc, emp.EMPRC_RazonSocial) 
                            FROM cji_empresa emp 
                            WHERE emp.EMPRP_Codigo = cli.EMPRP_Codigo
                            ),(
                            SELECT  CONCAT_WS(' - ', pp.PERSC_NumeroDocIdentidad, 
                                    CONCAT_WS(' ',pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) ) 
                            FROM cji_persona pp WHERE pp.PERSP_Codigo = cli.PERSP_Codigo
                        )) as nombre_cliente,
                        p.PERSC_Nombre, 
                        p.PERSC_ApellidoPaterno, 
                        p.PERSC_ApellidoMaterno, 
                        p.PERSC_NumeroDocIdentidad, 
                        f.FORPAC_Descripcion, 
                        n.CRED_Serie, 
                        n.CRED_Numero, 
                        n.CRED_Total, 
                        n.CRED_Fecha
                FROM cji_comprobante c
                INNER JOIN cji_cliente cli ON cli.CLIP_Codigo = c.CLIP_Codigo
                INNER JOIN cji_persona p ON p.PERSP_Codigo = c.CPC_Vendedor
                INNER JOIN cji_formapago f ON f.FORPAP_Codigo = c.FORPAP_Codigo
                LEFT JOIN cji_nota n ON n.CRED_ComproInicio = c.CPP_Codigo AND n.CRED_FlagEstado = 1 AND n.CRED_TipoNota LIKE 'C'
                WHERE c.CPC_FlagEstado = 1 
                AND c.CPC_TipoOperacion = 'V' 
                AND c.CPC_Vendedor = $vendedor 
                AND c.CPC_Fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
                ORDER BY f.FORPAC_Descripcion DESC, c.CPC_Vendedor ASC, c.CPP_Codigo ASC;
              ";
              
      $query = $this->db->query($sql);
      $data = array();
      if ($query->num_rows() > 0){
        foreach ($query->result() as $fila) {
          $data[] = $fila;
        }
        return $data;
      }
      else
        return NULL;
  }
  
  public function ticket_emitidos($filter = NULL){
      
        $limit = (isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
         if(isset($filter->ordenar)){
             $order = "ORDER BY ";
             foreach($filter->ordenar as $value){
                 $order.=$value[0]." ".$value[1].",";
             }
             $order = substr($order,0,strlen($order)-1);
         }
         else{
             $order = (isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";	
         }
         $where = '';

         if (isset($filter->situacion) && $filter->situacion != '')  $where .= " AND p.PARQC_FlagSituacion = '$filter->situacion'";	
         if (isset($filter->placa) && $filter->placa != '')          $where .= " AND p.PARQC_Placa = '$filter->placa'";	
         if (isset($filter->tarifa) && $filter->tarifa != '')        $where .= " AND t.TARIFP_Codigo = '$filter->tarifa'";	
         if (isset($filter->fechaing) && $filter->fechaing != '')    $where .= " AND p.PARQC_FechaIngreso = '$filter->fechaing'";	
         if (isset($filter->fechasal) && $filter->fechasal != '')    $where .= " AND p.PARQC_FechaSalida  = '$filter->fechasal'";	
         if (isset($filter->cajero) && $filter->cajero != '')        $where .= " AND c.CPC_Vendedor = '$filter->cajero'";
         if (isset($filter->serie) && $filter->serie != '')          $where .= " AND c.CPC_Serie = '$filter->serie'";	
         if (isset($filter->numero) && $filter->numero != '')        $where .= " AND c.CPC_Numero = '$filter->numero'";	

         $rec = "SELECT  p.*,
                         DATE_FORMAT(p.PARQC_FechaIngreso,'%d/%m/%Y') PARQC_FechaIn,
                         p.PARQC_HoraIngreso PARQC_HoraIn,
                         t.*,c.CPC_Numero,c.CPC_Serie,c.CPC_TipoDocumento,
                         concat(c.CPC_Serie,'-',c.CPC_Numero) as CPC_SerieNumero,
                         cj.CAJA_Nombre,cj.CAJA_Usuario
                     FROM cji_parqueo p 
                     INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo
                     left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo
                     left join cji_caja cj on cj.CAJA_Codigo = c.CAJA_Codigo
                     WHERE p.PARQC_FlagEstado LIKE '1' 
                     AND p.COMPP_Codigo = ".$this->compania."
                     $where $order $limit";
         
         $recF = "SELECT COUNT(*) as registros FROM cji_parqueo p INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo "
                 . "left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo "
                 . "WHERE p.PARQC_FlagEstado LIKE '1' AND p.COMPP_Codigo = $this->compania $where";
         $recT = "SELECT COUNT(*) as registros FROM cji_parqueo p INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo "
                 . "left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo "
                 . "WHERE p.PARQC_FlagEstado LIKE '1' AND p.COMPP_Codigo = $this->compania $where";
         $recAc = "SELECT  p.PARQC_FlagSituacion,COUNT(*) as registros FROM cji_parqueo p INNER JOIN cji_tarifa t ON t.TARIFP_Codigo = p.TARIFP_Codigo "
                 . "left join cji_comprobante c on c.CPP_Codigo = p.CPP_Codigo "
                 . "WHERE p.PARQC_FlagEstado LIKE '1' AND p.COMPP_Codigo = $this->compania $where group by p.PARQC_FlagSituacion";        
         $records = $this->db->query($rec);

        $recordsFilter = $this->db->query($recF)->row()->registros;
        $recordsTotal = $this->db->query($recT)->row()->registros;
        $recordsAcum  = $this->db->query($recAc)->result();

         if ($records->num_rows() > 0){
            $info = array(
                    "records" => $records->result(),
                    "recordsFilter" => $recordsFilter,
                    "recordsTotal"  => $recordsTotal,
                    "recordsAcum"   => $recordsAcum
            );
         }
         else{
            $info = array(
                        "records" => NULL,
                        "recordsFilter" => 0,
                        "recordsTotal"  => $recordsTotal,
                        "recordsAcum"   => $recordsAcum
                         );
         }
         return $info;
  }

  public function ventas_por_producto_de_vendedor($finicio, $ffin){
    $empresa = $_SESSION['empresa'];
    $compania = $_SESSION['compania'];

    $vendedores = "SELECT p.*
                    FROM cji_persona p
                    INNER JOIN cji_directivo d ON p.PERSP_Codigo = d.PERSP_Codigo
                    INNER JOIN cji_cargo c ON c.CARGP_Codigo = d.CARGP_Codigo
                    WHERE p.PERSC_FlagEstado = 1 AND d.DIREC_FlagEstado = 1 AND d.EMPRP_Codigo = $empresa"; # AND c.CARGC_Descripcion LIKE '%VENDEDOR%'

    $vendedoresInfo = $this->db->query($vendedores);
    $col = "";

    foreach ($vendedoresInfo->result() as $key => $value) {
      if ($key > 0)
        $col .= ", ";

      $col .= "
                '$value->PERSC_Nombre $value->PERSC_ApellidoPaterno $value->PERSC_ApellidoMaterno' as vendedor$key,
                (
                  SELECT SUM(cd.CPDEC_Cantidad) FROM cji_comprobantedetalle cd
                  INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                  INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                  WHERE c.CPC_Fecha BETWEEN '$finicio 00:00:00' AND '$ffin 23:59:59' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_Vendedor = $value->PERSP_Codigo AND p.PROD_Codigo = pp.PROD_Codigo AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
                ) as cantidad$key,
                (
                  SELECT SUM(cd.CPDEC_Total) FROM cji_comprobantedetalle cd
                  INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                  INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                  WHERE c.CPC_Fecha BETWEEN '$finicio 00:00:00' AND '$ffin 23:59:59' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_Vendedor = $value->PERSP_Codigo AND p.PROD_Codigo = pp.PROD_Codigo AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
                ) as venta$key
              ";
    }

    $productos = "SELECT pp.PROD_CodigoUsuario, pp.PROD_Nombre, m.MARCC_CodigoUsuario, $col,
                  (
                    SELECT SUM(cd.CPDEC_Cantidad) FROM cji_comprobantedetalle cd
                    INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                    INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                    WHERE c.CPC_Fecha BETWEEN '$finicio 00:00:00' AND '$ffin 23:59:59' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND p.PROD_Codigo = pp.PROD_Codigo AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
                  ) as cantidadTotal,
                  (
                    SELECT SUM(cd.CPDEC_Total) FROM cji_comprobantedetalle cd
                    INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                    INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                    WHERE c.CPC_Fecha BETWEEN '$finicio 00:00:00' AND '$ffin 23:59:59' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND p.PROD_Codigo = pp.PROD_Codigo AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
                  ) as ventaTotal
                FROM cji_producto pp
                INNER JOIN cji_productocompania pc ON pc.PROD_Codigo = pp.PROD_Codigo AND pc.COMPP_Codigo = $compania
                LEFT JOIN cji_marca m ON m.MARCP_Codigo = pp.MARCP_Codigo
                
                  WHERE (
                      SELECT SUM(cd.CPDEC_Cantidad) FROM cji_comprobantedetalle cd
                      INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                      INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                      WHERE c.CPC_Fecha BETWEEN '$finicio 00:00:00' AND '$ffin 23:59:59' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND p.PROD_Codigo = pp.PROD_Codigo AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
                    ) IS NOT NULL

                ORDER BY ventaTotal DESC
              ";

    $productosInfo = $this->db->query($productos);
    
    $data = array();
    if($productosInfo->num_rows > 0){
      foreach($productosInfo->result_array() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }

  public function ventas_por_vendedor_general($vendedor, $inicio, $fin){
      /*$sql = "SELECT SUM(c.CPC_Total) as total, f.FORPAC_Descripcion,
                (SELECT CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                  FROM cji_persona p WHERE p.PERSP_Codigo = $vendedor) as vendedor
                FROM cji_comprobante c
                INNER JOIN cji_formapago f ON f.FORPAP_Codigo = c.FORPAP_Codigo
                WHERE c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND c.CPC_Vendedor = $vendedor AND c.CPC_Fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
                GROUP BY f.FORPAP_Codigo
                ORDER BY f.FORPAC_Descripcion DESC
              ";*/

      $sql = "
                SELECT  SUM(c.CPC_Total) as total, 
                        SUM(n.CRED_total) as totalNotas, 
                        f.FORPAC_Descripcion,(
                            SELECT SUM(ci.CPC_Total) 
                            FROM cji_comprobante ci 
                            WHERE ci.FORPAP_Codigo = c.FORPAP_Codigo 
                            AND ci.CPC_TipoDocumento LIKE 'F' 
                            AND ci.CPC_FlagEstado = 1 
                            AND ci.CPC_TipoOperacion = 'V' 
                            AND ci.CPC_Vendedor = $vendedor 
                            AND ci.CPC_Fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
                        ) as totalFacturas,(
                            SELECT SUM(ci.CPC_Total) 
                            FROM cji_comprobante ci 
                            WHERE ci.FORPAP_Codigo = c.FORPAP_Codigo 
                            AND ci.CPC_TipoDocumento LIKE 'B' 
                            AND ci.CPC_FlagEstado = 1 
                            AND ci.CPC_TipoOperacion = 'V' 
                            AND ci.CPC_Vendedor = $vendedor 
                            AND ci.CPC_Fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
                        ) as totalBoletas,(
                            SELECT SUM(ci.CPC_Total) 
                            FROM cji_comprobante ci 
                            WHERE ci.FORPAP_Codigo = c.FORPAP_Codigo 
                            AND ci.CPC_TipoDocumento LIKE 'N' 
                            AND ci.CPC_FlagEstado = 1 
                            AND ci.CPC_TipoOperacion = 'V' 
                            AND ci.CPC_Vendedor = $vendedor 
                            AND ci.CPC_Fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
                        ) as totalComprobantes,(
                            SELECT CONCAT_WS(' ', p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                            FROM cji_persona p 
                            left join cji_usuario u on u.PERSP_Codigo = p.PERSP_Codigo
                            WHERE u.USUA_Codigo = $vendedor
                        ) as vendedor
                        FROM cji_comprobante c
                        INNER JOIN cji_formapago f ON f.FORPAP_Codigo = c.FORPAP_Codigo
                        LEFT JOIN  cji_nota n ON n.CRED_ComproInicio = c.CPP_Codigo AND n.CRED_FlagEstado = 1 AND n.CRED_TipoNota LIKE 'C'
                        WHERE c.CPC_FlagEstado = 1 
                        AND c.CPC_TipoOperacion = 'V' 
                        AND c.CPC_Vendedor = $vendedor 
                        AND c.CPC_Fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
                        GROUP BY c.FORPAP_Codigo
                        ORDER BY f.FORPAC_Descripcion DESC
              ";

      $query = $this->db->query($sql);
      $data = array();
      if ($query->num_rows() > 0){
        foreach ($query->result() as $fila) {
          $data[] = $fila;
        }
        return $data;
      }
      else
        return NULL;
  }

  public function ventas_por_vendedor_general_suma($vendedor, $inicio, $fin){
      $sql = "
            SELECT SUM(c.CPC_Total) as total, 
                   f.FORPAC_Descripcion
            FROM cji_comprobante c
            INNER JOIN cji_formapago f ON f.FORPAP_Codigo = c.FORPAP_Codigo
            WHERE c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' 
            AND c.CPC_Vendedor = $vendedor 
            AND c.CPC_Fecha BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
            GROUP BY f.FORPAP_Codigo
            ORDER BY f.FORPAC_Descripcion DESC
            ";
      $query = $this->db->query($sql);
      $data = array();
      if ($query->num_rows() > 0){
        foreach ($query->result() as $fila) {
          $data[] = $fila;
        }
        return $data;
      }
      else
        return NULL;
  }

  public function ventas_por_vendedor_resumen($inicio,$fin, $comp = ""){
    $compania = ($comp == "") ? " c.COMPP_Codigo = ".$this->compania." AND" : "";
    $sql = "
            SELECT  SUM( IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total)) as VENTAS, 
                    p.PERSC_Nombre as NOMBRE, CONCAT_WS(' ',p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PATERNO 
            FROM cji_comprobante c 
            left join cji_usuario u on u.USUA_Codigo = c.CPC_Vendedor
            left JOIN cji_persona p ON p.PERSP_Codigo = u.PERSP_Codigo 
            WHERE $compania c.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin') 
            AND c.CPC_FlagEstado = 1 
            AND c.CPC_TipoOperacion = 'V' 
            GROUP BY c.CPC_Vendedor";

    $query = $this->db->query($sql);    
    $data = array();
    if($query->num_rows() > 0){
      foreach($query->result_array() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_vendedor_resumen_revisar($inicio,$fin)
  {
    //$inicio = human_to_mysql($inicio);
    //$fin = human_to_mysql($fin);
    $sql = "SELECT SUM(CPC_Total) as VENTAS,
	p.PERSC_Nombre as NOMBRE,
	p.PERSC_ApellidoPaterno as PATERNO 
	FROM cji_usuario u 
	LEFT JOIN cji_comprobante c ON c.USUA_Codigo = u.USUA_Codigo 
	JOIN cji_persona p ON u.PERSP_Codigo = p.PERSP_Codigo 
	WHERE c.CPC_Fecha BETWEEN '$inicio' AND '$fin' GROUP BY c.USUA_Codigo";
    $query = $this->db->query($sql);
    
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_vendedor_mensual($inicio,$fin, $comp = ""){
    $compania = ($comp == "") ? " c.COMPP_Codigo = ".$this->compania." AND" : "";
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio){
      $sql = " SELECT p.PERSC_Nombre as NOMBRE, CONCAT_WS(' ',p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PATERNO, ";
      for($j = $anioInicio; $j <= $anioFin; $j++){
        if($j == $anioFin){
          for($i = 1; $i <= intval($mesFin); $i++){
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$j$i,";
          }
        }
        else
          if($j==$anioInicio){
            for($i = intval($mesInicio); $i <= 12; $i++){
              $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$j$i,";
            }
        }
        else{
            for($i = 1; $i <= 12; $i++){
              $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$j$i,";
            }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= "
      FROM cji_comprobante c
      JOIN cji_persona p ON c.CPC_Vendedor = p.PERSP_Codigo 
      WHERE $compania YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' AND p.PERSC_Nombre != ''
      AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V'
      GROUP BY c.CPC_Vendedor";
    
    }
    else
      if($anioFin == $anioInicio){
        $sql = " SELECT p.PERSC_Nombre as NOMBRE, CONCAT_WS(' ',p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PATERNO, ";
        if($mesInicio == $mesFin) {
          $sql .= "SUM(IF(MONTH(CPC_Fecha)=".intval($mesInicio).",IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
        }
        else{
          for($i = intval($mesInicio); $i <= intval($mesFin); $i++) {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$anioFin$i,";
          }
          $sql = substr($sql,0,strlen($sql)-1);
        }
      
      $sql.= "
      FROM cji_comprobante c
      LEFT JOIN cji_persona p ON c.CPC_Vendedor = p.PERSP_Codigo 
      WHERE $compania YEAR(c.CPC_Fecha) = '$anioInicio' AND p.PERSC_Nombre != ''
      AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' 
      GROUP BY c.CPC_Vendedor";
    }

    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0){
      foreach($query->result_array() as $result){
        $data[] = $result;
      }
    }
  
    return $data;
  }
  
  public function ventas_por_vendedor_anual($inicio,$fin, $comp = ""){
    $compania = ($comp == "") ? " c.COMPP_Codigo = ".$this->compania." AND" : "";
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio){
      $sql = " SELECT p.PERSC_Nombre as NOMBRE, CONCAT_WS(' ',p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PATERNO, ";
      for($j = $anioInicio; $j <= $anioFin; $j++){
        if($j == $anioFin){
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as y$j,";
        }
        else{
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= "
      FROM cji_comprobante c 
      JOIN cji_persona p ON c.CPC_Vendedor = p.PERSP_Codigo 
      WHERE $compania YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' AND p.PERSC_Nombre != ''
      AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' 
      GROUP BY c.CPC_Vendedor";
    }
    else
      if($anioFin == $anioInicio){
      $sql = " SELECT p.PERSC_Nombre as NOMBRE, CONCAT_WS(' ',p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno) as PATERNO, ";
      $sql .= "SUM(IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total)) as y$anioFin ";
      $sql.= "
      FROM cji_comprobante c 
      LEFT JOIN cji_persona p ON c.CPC_Vendedor = p.PERSP_Codigo 
      WHERE $compania YEAR(c.CPC_Fecha) = '$anioInicio'  AND p.PERSC_Nombre != ''
      AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' 
      GROUP BY c.CPC_Vendedor";
    }
  
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0){
      foreach($query->result_array() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_cliente_resumen_general($inicio, $fin, $all = false)
  {
    $limit = ($all == false) ? "" : " LIMIT 10 ";

    $where="and com.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin')";
    $sql = "
    SELECT SUM( IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total)) as VENTAS,
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC
    from cji_comprobante com
    inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
    inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
    inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
    WHERE CPC_TipoOperacion = 'V' AND CPC_FlagEstado = 1 ".$where." GROUP BY com.CLIP_Codigo
     UNION
    SELECT SUM( IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total)) as VENTAS ,
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC
    from cji_comprobante com
    inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
    inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
    inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
    WHERE CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 ".$where." GROUP BY com.CLIP_Codigo ORDER BY VENTAS DESC $limit
    ";
    $query = $this->db->query($sql);
  
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_cliente_mensual_general($inicio, $fin, $all = false)
  {

    $limit = ($all == false) ? "" : " LIMIT 10 ";

    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
  
    if($anioFin > $anioInicio)
    {
      $sql = " SELECT
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' GROUP BY com.CLIP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC, ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      $i--;
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' GROUP BY com.CLIP_Codigo ORDER BY m$j$i DESC $limit";
  
    }elseif($anioFin == $anioInicio){
      $sql = "SELECT
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC,
      ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=".intval($mesInicio).",IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
      }
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) = '$anioInicio' GROUP BY com.CLIP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC, ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=".intval($mesInicio).",IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
          $colOrder = "m$anioFin".intval($mesInicio);
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
        $i--;
        $colOrder = "m$anioFin$i";
      }
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) = '$anioInicio' GROUP BY com.CLIP_Codigo ORDER BY $colOrder DESC $limit";
    }
  
    $query = $this->db->query($sql);
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
    return $data;
  }

  public function ventas_por_cliente_anual_general($inicio, $fin, $all = false)
  {

    $limit = ($all == false) ? "" : " LIMIT 10 ";

    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
  
    if($anioFin > $anioInicio)
    {
  
  
      $sql = " SELECT
     CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC ,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }else{
          $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 GROUP BY com.CLIP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC , ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          $sql .= "SUM(IF(YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }else{
          $sql .= "SUM(IF(YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      $j--;
  
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 GROUP BY com.CLIP_Codigo ORDER BY y$j DESC $limit
      ";
  
  
    }elseif($anioFin == $anioInicio){
  
      $sql = " SELECT
     CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC ,
      ";
      $sql .= "SUM(IF(com.MONED_Codigo=2,com.CPC_TDC * com.CPC_Total,com.CPC_Total)) as y$anioFin ";
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) = '$anioInicio' and CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 GROUP BY com.CLIP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC ,";
      $sql .= "SUM(IF(com.MONED_Codigo=2,com.CPC_TDC * com.CPC_Total,com.CPC_Total)) as y$anioFin ";
  
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) = '$anioInicio' and CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 GROUP BY com.CLIP_Codigo ORDER BY y$anioFin DESC $limit
      ";
    }
  
    $query = $this->db->query($sql);
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
    return $data;
  }

  public function ventas_por_cliente_resumen($inicio, $fin, $cliente)
  {
    $where="and com.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin')";
    $sql = "
    SELECT SUM( IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total)) as VENTAS,
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC
    from cji_comprobante com
    inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
    inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
    inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
    WHERE CPC_TipoOperacion='V'  AND CPC_FlagEstado = 1 ".$where." and com.CLIP_Codigo =".$cliente."
     UNION
    SELECT SUM( IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total)) as VENTAS ,
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC
    from cji_comprobante com
    inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
    inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
    inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
    WHERE CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 ".$where." and com.CLIP_Codigo =".$cliente."
    ";
    $query = $this->db->query($sql);
  
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }

  public function ventas_por_cliente_mensual($inicio,$fin,$cliente)
  {
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
  
    if($anioFin > $anioInicio)
    {
      $sql = " SELECT
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and com.CLIP_Codigo = ".$cliente."
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC, ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and com.CLIP_Codigo = ".$cliente." ";
  
    }elseif($anioFin == $anioInicio){
      $sql = "SELECT
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC,
      ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=".intval($mesInicio).",IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
      }
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) = '$anioInicio' and com.CLIP_Codigo = ".$cliente."
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC, ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=".intval($mesInicio).",IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
      }
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) = '$anioInicio' and com.CLIP_Codigo = ".$cliente." ";
  
    }
  
    $query = $this->db->query($sql);
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
    return $data;
  }
  
  public function ventas_por_cliente_anual($inicio,$fin,$cliente)
  {
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
  
    if($anioFin > $anioInicio)
    {
  
  
      $sql = " SELECT
     CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC ,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }else{
          $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and com.CLIP_Codigo = ".$cliente."
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC , ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          $sql .= "SUM(IF(YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }else{
          $sql .= "SUM(IF(YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
  
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and com.CLIP_Codigo = ".$cliente."
      ";
  
  
    }elseif($anioFin == $anioInicio){
  
      $sql = " SELECT
     CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC ,
      ";
      $sql .= "SUM(IF(com.MONED_Codigo=2,com.CPC_TDC * com.CPC_Total,com.CPC_Total)) as y$anioFin ";
  
      $sql.= "
      from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = cl.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) = '$anioInicio' and CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and com.CLIP_Codigo = ".$cliente."
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC ,";
      $sql .= "SUM(IF(com.MONED_Codigo=2,com.CPC_TDC * com.CPC_Total,com.CPC_Total)) as y$anioFin ";
  
      $sql.= " from cji_comprobante com
      inner join cji_cliente cl on cl.CLIP_Codigo = com.CLIP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = cl.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) = '$anioInicio' and CPC_TipoOperacion='V' AND CPC_FlagEstado = 1 and com.CLIP_Codigo = ".$cliente."
      ";
    }
  
    $query = $this->db->query($sql);
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
    return $data;
  }

  public function ventas_por_proveedor_resumen_general($inicio, $fin, $all = false)
  {
    $limit = ($all == false) ? "" : " LIMIT 10 ";

    $where="and com.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin')";
    $sql = "SELECT SUM( IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total)) as VENTAS,
              EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC
            from cji_comprobante com
            inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
            inner join cji_empresa es on es.EMPRP_Codigo = pv.EMPRP_Codigo
            inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
            WHERE CPC_TipoOperacion='C' AND CPC_FlagEstado = 1 ".$where." GROUP BY com.PROVP_Codigo ORDER BY VENTAS DESC $limit
            ";
    $query = $this->db->query($sql);
  
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }

  public function resumen_ventas($inicio, $fin){
    $where = " AND c.CPC_FechaRegistro BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'";

    $sql = "SELECT c.*,
              (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, ' - ', e.EMPRC_RazonSocial) FROM cji_empresa e WHERE e.EMPRP_Codigo = cli.EMPRP_Codigo) as clienteEmpresa,
              (SELECT CONCAT_WS(' ', pp.PERSC_NumeroDocIdentidad, ' - ', pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) FROM cji_persona pp WHERE pp.PERSP_Codigo = cli.PERSP_Codigo) as clientePersona,

              n.CRED_Serie, n.CRED_Numero, n.CRED_Total

              FROM cji_comprobante c
              LEFT JOIN  cji_nota n ON n.CRED_ComproInicio = c.CPP_Codigo AND n.CRED_FlagEstado = 1 AND n.CRED_TipoNota LIKE 'C'
              INNER JOIN cji_cliente cli ON cli.CLIP_Codigo = c.CLIP_Codigo

              WHERE CPC_TipoOperacion = 'V' AND CPC_FlagEstado = 1 $where
              ORDER BY c.CPC_Fecha, c.CPC_Numero ASC
            ";
    $query = $this->db->query($sql);
    $data = array();
    if($query->num_rows() > 0){
      foreach($query->result() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }

  public function resumen_ventas_detallado($inicio, $fin){
    $where = " AND c.CPC_FechaRegistro BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'";

    $sql = "
            SELECT  c.CPC_Fecha, 
                    c.CPC_FechaRegistro, 
                    c.CPC_Serie, 
                    c.CPC_Numero, 
                    cd.*, 
                    p.PROD_Nombre, 
                    m.MARCC_CodigoUsuario, 
                    l.LOTC_Numero, 
                    l.LOTC_FechaVencimiento,(
                        SELECT CONCAT_WS(' ', e.EMPRC_Ruc, ' - ', e.EMPRC_RazonSocial) 
                        FROM cji_empresa e 
                        WHERE e.EMPRP_Codigo = cli.EMPRP_Codigo) 
                    as clienteEmpresa,(
                        SELECT CONCAT_WS(' ', pp.PERSC_NumeroDocIdentidad, ' - ', pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) 
                        FROM cji_persona pp 
                        WHERE pp.PERSP_Codigo = cli.PERSP_Codigo
                    ) as clientePersona,
                    n.CRED_Serie, 
                    n.CRED_Numero, 
                    nd.CREDET_Cantidad, 
                    nd.CREDET_Pu_ConIgv, 
                    nd.CREDET_Total,
                    e.EESTABC_Descripcion
            FROM cji_comprobantedetalle cd
            INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
            LEFT JOIN  cji_nota n ON n.CRED_ComproInicio = c.CPP_Codigo AND n.CRED_FlagEstado = 1 AND n.CRED_TipoNota LIKE 'C'
            LEFT JOIN  cji_notadetalle nd ON nd.CRED_Codigo = n.CRED_Codigo AND nd.CREDET_FlagEstado = 1 AND nd.PROD_Codigo = cd.PROD_Codigo
            INNER JOIN cji_cliente cli ON cli.CLIP_Codigo = c.CLIP_Codigo
            INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
            LEFT JOIN cji_lote l ON l.LOTP_Codigo = cd.LOTP_Codigo
            LEFT JOIN cji_marca m ON m.MARCP_Codigo = p.MARCP_Codigo
            LEFT JOIN cji_emprestablecimiento e ON e.EESTABP_Codigo = c.COMPP_Codigo
            WHERE CPC_TipoOperacion = 'V' 
            AND CPC_FlagEstado = 1 
            AND cd.CPDEC_FlagEstado = 1 
            $where
            ORDER BY c.CPC_Fecha, c.CPC_Numero ASC
            ";
    $query = $this->db->query($sql);
    $data = array();
    if($query->num_rows() > 0){
      foreach($query->result() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }

  public function resumen_compras_detallado($inicio, $fin){
    $where = " AND c.CPC_FechaRegistro BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'";

    $sql = "SELECT c.CPC_Fecha, c.CPC_FechaRegistro, c.CPC_Serie, c.CPC_Numero, cd.*, p.PROD_Nombre, m.MARCC_CodigoUsuario, l.LOTC_Numero, l.LOTC_FechaVencimiento,
              (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, ' - ', e.EMPRC_RazonSocial) FROM cji_empresa e WHERE e.EMPRP_Codigo = pv.EMPRP_Codigo) as proveedorEmpresa,
              (SELECT CONCAT_WS(' ', pp.PERSC_NumeroDocIdentidad, ' - ', pp.PERSC_Nombre, pp.PERSC_ApellidoPaterno, pp.PERSC_ApellidoMaterno) FROM cji_persona pp WHERE pp.PERSP_Codigo = pv.PERSP_Codigo) as proveedorPersona

              FROM cji_comprobantedetalle cd
              INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
              INNER JOIN cji_proveedor pv ON pv.PROVP_Codigo = c.PROVP_Codigo
              INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
              LEFT JOIN cji_lote l ON l.LOTP_Codigo = cd.LOTP_Codigo
              LEFT JOIN cji_marca m ON m.MARCP_Codigo = p.MARCP_Codigo

              WHERE CPC_TipoOperacion = 'C' AND CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 $where
              ORDER BY c.CPC_Fecha
            ";
    $query = $this->db->query($sql);
    $data = array();
    if($query->num_rows() > 0){
      foreach($query->result() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_proveedor_mensual_general($inicio, $fin, $all = false)
  {

    $limit = ($all == false) ? "" : " LIMIT 10 ";

    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
  
    if($anioFin > $anioInicio)
    {
      $sql = " SELECT
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
  
      $sql.= "
      from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = pv.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE CPC_TipoOperacion='C'  AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' GROUP BY com.PROVP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC, ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i AND YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      $i--;
      $sql.= " from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = pv.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE CPC_TipoOperacion='C' AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' GROUP BY com.PROVP_Codigo ORDER BY m$j$i DESC $limit";
  
    }elseif($anioFin == $anioInicio){
      $sql = "SELECT
      CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC,
      ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=".intval($mesInicio).",IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
      }
  
      $sql.= "
      from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = pv.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE CPC_TipoOperacion='C'  AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) = '$anioInicio' GROUP BY com.PROVP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC, ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=".intval($mesInicio).",IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
          $colOrder = "m$anioFin".intval($mesInicio);
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(com.CPC_Fecha)=$i,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
        $i--;
        $colOrder = "m$anioFin$i";
      }
      $sql.= " from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = pv.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE CPC_TipoOperacion='C' AND CPC_FlagEstado = 1 and YEAR(com.CPC_Fecha) = '$anioInicio' GROUP BY com.PROVP_Codigo ORDER BY $colOrder DESC $limit";
    }
  
    $query = $this->db->query($sql);
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
    return $data;
  }

  public function ventas_por_proveedor_anual_general($inicio, $fin, $all = false)
  {

    $limit = ($all == false) ? "" : " LIMIT 10 ";

    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
  
    if($anioFin > $anioInicio)
    {
  
  
      $sql = " SELECT
     CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC ,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }else{
          $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
  
      $sql.= "
      from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = pv.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and CPC_TipoOperacion='C' AND CPC_FlagEstado = 1 GROUP BY com.PROVP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC , ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          $sql .= "SUM(IF(YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }else{
          $sql .= "SUM(IF(YEAR(com.CPC_Fecha)=$j,IF(com.MONED_Codigo=2,com.CPC_TDC*com.CPC_Total,com.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      $j--;
  
      $sql.= " from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = pv.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and CPC_TipoOperacion='C' AND CPC_FlagEstado = 1 GROUP BY com.PROVP_Codigo ORDER BY y$j DESC $limit
      ";
  
  
    }elseif($anioFin == $anioInicio){
  
      $sql = " SELECT
     CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) as NOMBRE, PERSC_NumeroDocIdentidad AS RUC ,
      ";
      $sql .= "SUM(IF(com.MONED_Codigo=2,com.CPC_TDC * com.CPC_Total,com.CPC_Total)) as y$anioFin ";
  
      $sql.= "
      from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_persona pe on pe.PERSP_Codigo = pv.PERSP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo=com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) = '$anioInicio' and CPC_TipoOperacion='C' AND CPC_FlagEstado = 1 GROUP BY com.PROVP_Codigo
      UNION
      SELECT
      EMPRC_RazonSocial as NOMBRE , EMPRC_Ruc AS RUC ,";
      $sql .= "SUM(IF(com.MONED_Codigo=2,com.CPC_TDC * com.CPC_Total,com.CPC_Total)) as y$anioFin ";
  
      $sql.= " from cji_comprobante com
      inner join cji_proveedor pv on pv.PROVP_Codigo = com.PROVP_Codigo
      inner join cji_empresa es on es.EMPRP_Codigo = pv.EMPRP_Codigo
      inner JOIN cji_moneda m ON m.MONED_Codigo = com.MONED_Codigo
      WHERE YEAR(com.CPC_Fecha) = '$anioInicio' and CPC_TipoOperacion='C' AND CPC_FlagEstado = 1 GROUP BY com.PROVP_Codigo ORDER BY y$anioFin DESC $limit
      ";
    }
  
    $query = $this->db->query($sql);
  
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
    return $data;
  }

  public function ventas_por_marca_de_vendedor($finicio, $ffin){
    $empresa = $_SESSION['empresa'];
    $compania = $_SESSION['compania'];

    $vendedores = "SELECT p.*
                    FROM cji_persona p
                    INNER JOIN cji_directivo d ON p.PERSP_Codigo = d.PERSP_Codigo
                    INNER JOIN cji_cargo c ON c.CARGP_Codigo = d.CARGP_Codigo
                    WHERE c.CARGC_Descripcion LIKE '%VENDEDOR%' AND p.PERSC_FlagEstado = 1 AND d.DIREC_FlagEstado = 1 AND d.EMPRP_Codigo = $empresa";

    $vendedoresInfo = $this->db->query($vendedores);
    $col = "";

    foreach ($vendedoresInfo->result() as $key => $value) {
      if ($key > 0)
        $col .= ", ";

      $col .= "
                '$value->PERSC_Nombre $value->PERSC_ApellidoPaterno $value->PERSC_ApellidoMaterno' as vendedor$key,
                (
                  SELECT SUM(cds.CPDEC_Total) FROM cji_comprobantedetalle cds
                  INNER JOIN cji_comprobante cs ON cs.CPP_Codigo = cds.CPP_Codigo
                  INNER JOIN cji_producto p ON p.PROD_Codigo = cds.PROD_Codigo
                  WHERE cs.CPC_Fecha BETWEEN '$finicio 00:00:00' AND '$ffin 23:59:59' AND cs.CPC_FlagEstado = 1 AND cds.CPDEC_FlagEstado = 1 AND cs.CPC_Vendedor = $value->PERSP_Codigo AND p.MARCP_Codigo = m.MARCP_Codigo AND cs.CPC_TipoOperacion = 'V' AND cs.COMPP_Codigo = $compania
                ) as venta$key
              ";
    }

    $marcas = "SELECT m.MARCC_Descripcion, $col,
                (
                  SELECT SUM(cd.CPDEC_Total) FROM cji_comprobantedetalle cd
                  INNER JOIN cji_comprobante c ON c.CPP_Codigo = cd.CPP_Codigo
                  INNER JOIN cji_producto p ON p.PROD_Codigo = cd.PROD_Codigo
                  WHERE c.CPC_Fecha BETWEEN '$finicio 00:00:00' AND '$ffin 23:59:59' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND p.MARCP_Codigo = m.MARCP_Codigo AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
                ) as total
                FROM cji_marca m
                WHERE m.MARCC_FlagEstado = 1
                ORDER BY total DESC
              ";

    $marcasInfo = $this->db->query($marcas);
    
    $data = array();
    if($marcasInfo->num_rows > 0){
      foreach($marcasInfo->result_array() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }

  
  public function ventas_por_marca_resumen($inicio,$fin){
    $compania = $this->somevar['compania'];
    $sql = "SELECT m.MARCC_Descripcion AS NOMBRE, SUM( IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total) ) AS VENTAS
              FROM cji_comprobantedetalle cd
              JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
              JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
              JOIN cji_marca m ON p.MARCP_Codigo = m.MARCP_Codigo
              WHERE c.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin')
              AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND cd.CPDEC_FlagEstado = 1 AND c.COMPP_Codigo = $compania
              GROUP BY p.MARCP_Codigo
              ORDER BY VENTAS DESC
          ";
    $query = $this->db->query($sql);
    
    $data = array();
    if($query->num_rows() > 0){
      foreach($query->result_array() as $result){
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_marca_mensual($inicio,$fin){
    $compania = $this->somevar["compania"];
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio){
      $sql = "SELECT m.MARCC_Descripcion AS NOMBRE, ";
      for($j = $anioInicio; $j <= $anioFin; $j++) {
        if($j == $anioFin) {
          for($i = 1; $i <= intval($mesFin); $i++) {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
          }
        }
        else
          if($j==$anioInicio){
            for($i = intval($mesInicio); $i <= 12; $i++) {
              $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
            }
        }
        else{
          for($i = 1; $i <= 12; $i++) {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= "
      FROM cji_comprobantedetalle cd
      JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      JOIN cji_marca m ON p.MARCP_Codigo = m.MARCP_Codigo
      WHERE YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND cd.CPDEC_FlagEstado = 1 AND c.COMPP_Codigo = $compania
      GROUP BY p.MARCP_Codigo";
    
    }
    else
      if($anioFin == $anioInicio){
        $sql = "SELECT m.MARCC_Descripcion AS NOMBRE, ";
      if($mesInicio == $mesFin) {
        $sql .= "SUM(IF(MONTH(CPC_Fecha)=".intval($mesInicio).",IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$anioFin".intval($mesInicio)."";
      }
      else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++) {
          $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
      }
      
      $sql.= "
      FROM cji_comprobantedetalle cd
      JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      JOIN cji_marca m ON p.MARCP_Codigo = m.MARCP_Codigo
      WHERE YEAR(c.CPC_Fecha) = '$anioInicio' AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND cd.CPDEC_FlagEstado = 1 AND c.COMPP_Codigo = $compania
      GROUP BY p.MARCP_Codigo";
    }

    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
  return $data;
  }
  
  public function ventas_por_marca_anual($inicio,$fin){
    $compania = $this->somevar["compania"];
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio) {
      $sql = "SELECT m.MARCC_Descripcion AS NOMBRE, ";
      for($j = $anioInicio; $j <= $anioFin; $j++) {
        if($j == $anioFin) {
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as y$j,";
        }
        else{
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      $sql.= " FROM cji_comprobantedetalle cd
        JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
        JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
        JOIN cji_marca m ON p.MARCP_Codigo = m.MARCP_Codigo
        WHERE YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin'
        AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND cd.CPDEC_FlagEstado = 1 AND c.COMPP_Codigo = $compania
        GROUP BY p.MARCP_Codigo";
    }
    else
      if($anioFin == $anioInicio){
      $sql = "SELECT m.MARCC_Descripcion AS NOMBRE, ";
      $sql .= "SUM(IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total)) as y$anioFin ";
      $sql.= "
      FROM cji_comprobantedetalle cd
      JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      JOIN cji_marca m ON p.MARCP_Codigo = m.MARCP_Codigo
      WHERE YEAR(c.CPC_Fecha) = '$anioInicio'
      AND c.CPC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND cd.CPDEC_FlagEstado = 1 AND c.COMPP_Codigo = $compania
      GROUP BY p.MARCP_Codigo";
    }
  
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0) {
      foreach($query->result_array() as $result) {
        $data[] = $result;
      }
    }
  
  return $data;
  }
  
  
  /* FAMILIAS */

  
  public function ventas_por_familia_resumen($inicio,$fin){
    $compania = $this->compania;
    $sql = "SELECT f.FAMI_Descripcion AS NOMBRE, SUM( IF(c.MONED_Codigo = 2, c.CPC_TDC * cd.CPDEC_Total, cd.CPDEC_Total) ) AS VENTAS
              FROM cji_comprobantedetalle cd
              JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
              JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
              JOIN cji_familia f ON p.FAMI_Codigo  = f.FAMI_Codigo 
              WHERE c.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin') AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania GROUP BY p.FAMI_Codigo";
    $query = $this->db->query($sql);
    
    $data = array();
    if($query->num_rows() > 0) {
      foreach($query->result_array() as $result) {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_familia_mensual($inicio,$fin) {
    $compania = $this->compania;
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio){
      $sql = "SELECT f.FAMI_Descripcion AS NOMBRE, ";
      for($j = $anioInicio; $j <= $anioFin; $j++) {
        if($j == $anioFin) {
          for($i = 1; $i <= intval($mesFin); $i++) {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
          }
        }
        else
          if($j==$anioInicio){
            for($i = intval($mesInicio); $i <= 12; $i++) {
              $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
            }
        }
        else{
          for($i = 1; $i <= 12; $i++) {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= "
      FROM cji_comprobantedetalle cd
      JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      JOIN cji_familia f ON p.FAMI_Codigo  = f.FAMI_Codigo 
      WHERE YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
      GROUP BY p.FAMI_Codigo";
    }
    else
      if($anioFin == $anioInicio){
        $sql = "SELECT f.FAMI_Descripcion AS NOMBRE, ";
        if($mesInicio == $mesFin) {
          $sql .= "SUM(IF(MONTH(CPC_Fecha)=".intval($mesInicio).",IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$anioFin".intval($mesInicio)."";
        }
        else{
          for($i = intval($mesInicio); $i <= intval($mesFin); $i++) {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$anioFin$i,";
          }
          $sql = substr($sql,0,strlen($sql)-1);
        }
      $sql.= "
      FROM cji_comprobantedetalle cd
      JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      JOIN cji_familia f ON p.FAMI_Codigo  = f.FAMI_Codigo 
      WHERE YEAR(c.CPC_Fecha) = '$anioInicio' AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
      GROUP BY p.FAMI_Codigo";
    }

    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0) {
      foreach($query->result_array() as $result) {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_familia_anual($inicio,$fin) {
    $compania = $this->compania;
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio) {
      $sql = "SELECT f.FAMI_Descripcion AS NOMBRE, ";
      for($j = $anioInicio; $j <= $anioFin; $j++) {
        if($j == $anioFin) {
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as y$j,";
        }
        else{
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= " FROM cji_comprobantedetalle cd
              JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
              JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
              JOIN cji_familia f ON p.FAMI_Codigo  = f.FAMI_Codigo 
              WHERE YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin'
              AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
              GROUP BY p.FAMI_Codigo";
    
    }
    else
      if($anioFin == $anioInicio){
        $sql = "SELECT f.FAMI_Descripcion AS NOMBRE, ";
        $sql .= "SUM(IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total)) as y$anioFin ";
        $sql.= " FROM cji_comprobantedetalle cd
                JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo
                JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
                JOIN cji_familia f ON p.FAMI_Codigo  = f.FAMI_Codigo 
                WHERE YEAR(c.CPC_Fecha) = '$anioInicio'
                AND c.CPC_FlagEstado = 1 AND cd.CPDEC_FlagEstado = 1 AND c.CPC_TipoOperacion = 'V' AND c.COMPP_Codigo = $compania
                GROUP BY p.FAMI_Codigo";
    }
  
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0) {
      foreach($query->result_array() as $result) {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_dia($inicio,$fin){
    $sql = "
        SELECT c.CPC_Fecha as FECHA, 
               date(cp.CPAGC_FechaRegistro) as FECHAPAGO, 
               c.CPC_Serie AS SERIE,
               c.CPC_Numero AS NUMERO,
               CPC_TipoOperacion, 
               CPC_Total AS VENTAS, 
               cp.CPAGC_Monto, 
               c.CPC_TipoDocumento AS TIPO, 
               c.CPP_Codigo as CODIGO , 
               c.CPC_TDC , c.MONED_Codigo, 
               c.CPC_FlagEstado, c.FORPAP_Codigo, 
               cc.CUE_Codigo, 
               SUM(cp.CPAGC_Monto) AS pagos
        FROM cji_comprobante c
        INNER JOIN cji_cuentas cc ON cc.CUE_CodDocumento = c.CPP_Codigo
        INNER JOIN cji_cuentaspago cp ON cp.CUE_Codigo = cc.CUE_Codigo
        WHERE cp.CPAGC_FechaRegistro BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59' 
        AND c.CPC_TipoOperacion='V' 
        AND c.CPC_FlagEstado='1' 
        AND c.COMPP_Codigo = '$this->compania'
        GROUP BY CUE_Codigo
        ORDER BY CPC_Numero ASC
        ";
    
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }

  public function ingreso_ventas_por_dia($inicio,$fin){
    $compania = $this->somevar['compania'];

    $sql = "SELECT c.CPC_Fecha as FECHA, date(cp.CPAGC_FechaRegistro) as FECHAPAGO, c.CPC_Serie AS SERIE,c.CPC_Numero AS NUMERO,CPC_TipoOperacion, CPC_Total AS VENTAS, cp.CPAGC_Monto, c.CPC_TipoDocumento AS TIPO, c.CPP_Codigo as CODIGO , c.CPC_TDC , c.MONED_Codigo, c.CPC_FlagEstado, c.FORPAP_Codigo, cc.CUE_Codigo, SUM(cp.CPAGC_Monto) AS pagos
              FROM cji_comprobante c
                INNER JOIN cji_cuentas cc ON cc.CUE_CodDocumento = c.CPP_Codigo
                INNER JOIN cji_cuentaspago cp ON cp.CUE_Codigo = cc.CUE_Codigo
                WHERE cp.CPAGC_FechaRegistro BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59' AND c.CPC_TipoOperacion='V' AND c.CPC_FlagEstado='1' AND c.COMPP_Codigo = '$compania'
                GROUP BY CUE_Codigo
                ORDER BY CPC_Numero ASC
                ";
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function producto_stock()
  {
    $sql = "SELECT DISTINCT P.PROD_Nombre, DATE_FORMAT(C.CPDEC_FechaRegistro,'%m-%d-%Y') as fecha, DATEDIFF( CURDATE( ) , C.CPDEC_FechaRegistro ) AS dias
        FROM  `cji_comprobantedetalle` C
        INNER JOIN cji_producto P ON P.PROD_Codigo = C.PROD_Codigo
        INNER JOIN (
        SELECT CPDEC_Descripcion, MAX( CPDEC_FechaRegistro ) AS MaxDateTime
        FROM cji_comprobantedetalle
        GROUP BY CPDEC_Descripcion
        )CD ON C.CPDEC_Descripcion = CD.CPDEC_Descripcion
        AND C.CPDEC_FechaRegistro = CD.MaxDateTime
        where DATEDIFF( CURDATE( ) , C.CPDEC_FechaRegistro ) >=15 AND P.PROD_FlagBienServicio = 'B' 
        ORDER BY dias ASC limit 150";
    
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $result)
      {
        $data[] = $result;
      }
    }
  
    return $data;
  }
  
  public function ventas_diarios2($filter = NULL){
    $limit = ( isset($filter->start) && isset($filter->length) ) ? " LIMIT $filter->start, $filter->length " : "";
    $order = ( isset($filter->order) && isset($filter->dir) ) ? "ORDER BY $filter->order $filter->dir " : "";

    $where = "";
    
    if ( isset($filter->tipo_doc) && trim($filter->tipo_doc) != "")     $where .= " AND com.CPC_TipoDocumento LIKE '$filter->tipo_doc' ";
    if ( isset($filter->numero_doc) && trim($filter->numero_doc) != "") $where .= " AND com.CPC_Numero LIKE '".trim($filter->numero_doc)."' ";
    if ( isset($filter->fechaini) && trim($filter->fechaini) != "")     $where .= " AND com.CPC_Fecha >= '$filter->fechaini' ";
    if ( isset($filter->fechafin) && trim($filter->fechafin) != "")     $where .= " AND com.CPC_Fecha <= '$filter->fechafin' ";
    
    if (isset($filter->nro_ruc) && $filter->nro_ruc != '')
            $where .= " AND CASE cli.CLIC_TipoPersona
                        WHEN 0 THEN per.PERSC_NumeroDocIdentidad
                        WHEN 1 THEN emp.EMPRC_Ruc
                        ELSE ''
                        END LIKE '%$filter->nro_ruc%' ";

    if (isset($filter->razon_social) && $filter->razon_social != ''){
            $where .= " AND CASE cli.CLIC_TipoPersona
                        WHEN 0 THEN CONCAT_WS(' ', per.PERSC_Nombre, per.PERSC_ApellidoPaterno, per.PERSC_ApellidoMaterno)
                        WHEN 1 THEN emp.EMPRC_RazonSocial
                        ELSE ''
                        END LIKE '%$filter->razon_social%'";
    }    
    
    $rec = "
        SELECT com.CPC_Fecha, 
               com.CPC_FlagEstado, 
               com.CPC_TipoDocumento, 
               com.CPC_Serie, 
               com.CPC_Numero, 
               com.CPC_subtotal, 
               com.CPC_igv, 
               com.CPC_total,                
               emp.EMPRC_RazonSocial, 
               emp.EMPRC_Ruc, 
               per.PERSC_Nombre, 
               per.PERSC_ApellidoPaterno, 
               per.PERSC_ApellidoMaterno, 
               per.PERSC_Ruc, 
               cli.CLIC_TipoPersona,(
                    case cli.CLIC_TipoPersona
                    when 0 then CONCAT_WS(' ',per.PERSC_Nombre,per.PERSC_ApellidoPaterno,per.PERSC_ApellidoMaterno)
                    when 1 then CONCAT_WS(' ',emp.EMPRC_RazonSocial)
                    else ''
                    end
               ) as razon_social,(
                    case cli.CLIC_TipoPersona
                    when 0 then per.PERSC_NumeroDocIdentidad
                    when 1 then emp.EMPRC_Ruc
                    else ''
                    end
               ) as numero_doc,
               mon.MONED_Simbolo, 
               mon.MONED_Codigo, 
               fpago.FORPAC_Descripcion 
        FROM cji_comprobante as com
        LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = com.CLIP_Codigo
        LEFT JOIN cji_persona per ON per.PERSP_Codigo = cli.PERSP_Codigo
        LEFT JOIN cji_empresa emp ON emp.EMPRP_Codigo = cli.EMPRP_Codigo 
        LEFT JOIN cji_moneda mon ON mon.MONED_Codigo = com.MONED_Codigo 
        LEFT JOIN cji_formapago fpago ON fpago.FORPAP_Codigo = com.FORPAP_Codigo
        WHERE com.COMPP_Codigo = '$this->compania'

        AND com.CPC_TipoOperacion = 'V' 
        $where $order $limit       
        ";
    
    $recF = "
        SELECT count(*) as registros
        FROM cji_comprobante as com
        LEFT JOIN cji_cliente cli ON cli.CLIP_Codigo = com.CLIP_Codigo
        LEFT JOIN cji_persona per ON per.PERSP_Codigo = cli.PERSP_Codigo
        LEFT JOIN cji_empresa emp ON emp.EMPRP_Codigo = cli.EMPRP_Codigo         
        WHERE com.COMPP_Codigo = '$this->compania'
 
        AND com.CPC_TipoOperacion = 'V' 
        $where 
        ";
    
    $recT = "
        SELECT count(*) as registros
        FROM `cji_comprobante` 
        WHERE `cji_comprobante`.`COMPP_Codigo` = '$this->compania'

        AND `cji_comprobante`.`CPC_TipoOperacion` = 'V' 
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
  
   public function ventas_diarios($tipo,$fechaini,$hoy)
  {
      $compania = $this->compania;
      $this->db->select('cji_comprobante.CPC_Fecha,cji_comprobante.CPC_FlagEstado,cji_comprobante.CPC_TipoDocumento,cji_comprobante.CPC_Serie,cji_comprobante.CPC_Numero,
      cji_empresa.EMPRC_RazonSocial,cji_empresa.EMPRC_Ruc,cji_persona.PERSC_Nombre,cji_persona.PERSC_ApellidoPaterno,
      cji_persona.PERSC_ApellidoMaterno,  cji_persona.PERSC_Ruc,cji_comprobante.CPC_subtotal,cji_comprobante.CPC_igv,
      cji_comprobante.CPC_total,cji_cliente.CLIC_TipoPersona,cji_moneda.MONED_Simbolo,cji_moneda.MONED_Codigo,cji_formapago.FORPAC_Descripcion');
      $this->db->join('cji_cliente','cji_cliente.CLIP_Codigo=cji_comprobante.CLIP_Codigo','left');
      $this->db->join('cji_persona','cji_persona.PERSP_Codigo=cji_cliente.PERSP_Codigo','left');
      $this->db->join('cji_empresa','cji_empresa.EMPRP_Codigo=cji_cliente.EMPRP_Codigo','left');
     $this->db->join('cji_moneda','cji_moneda.MONED_Codigo=cji_comprobante.MONED_Codigo','left');
     $this->db->join('cji_formapago','cji_formapago.FORPAP_Codigo=cji_comprobante.FORPAP_Codigo','left');
      $this->db->from('cji_comprobante');
      $this->db->where('cji_comprobante.COMPP_Codigo',$compania);
      $this->db->where('cji_comprobante.CPC_Fecha>=',$fechaini);
    $this->db->where('cji_comprobante.CPC_Fecha<=',$hoy);
       $this->db->where('cji_comprobante.CPC_TipoDocumento',$tipo);
      $this->db->where('cji_comprobante.CPC_TipoOperacion','V');
      $this->db->order_by('cji_comprobante.CPC_Fecha','asc');
      
     $query= $this->db->get();
    return $query->result();

  }
   public function registro_ventas($tipo_oper, $tipo, $mes, $anio){
      $compania = $this->somevar['compania'];
      $this->db->select('cji_comprobante.CPC_Fecha,cji_comprobante.CPC_FlagEstado,cji_comprobante.CPC_TipoDocumento,cji_comprobante.CPC_Serie,cji_comprobante.CPC_Numero,
      cji_empresa.EMPRC_RazonSocial,cji_empresa.EMPRC_Ruc,cji_persona.PERSC_Nombre,cji_persona.PERSC_ApellidoPaterno,
      cji_persona.PERSC_ApellidoMaterno,  cji_persona.PERSC_Ruc,cji_comprobante.CPC_subtotal,cji_comprobante.CPC_igv,
      cji_comprobante.CPC_total,cji_cliente.CLIC_TipoPersona,cji_proveedor.PROVC_TipoPersona,cji_moneda.MONED_Simbolo,cji_moneda.MONED_Codigo');

      $this->db->join('cji_cliente','cji_cliente.CLIP_Codigo=cji_comprobante.CLIP_Codigo','left');
    $this->db->join('cji_proveedor','cji_proveedor.PROVP_Codigo=cji_comprobante.PROVP_Codigo','left');
    $this->db->join('cji_moneda','cji_moneda.MONED_Codigo=cji_comprobante.MONED_Codigo','left');
      if($tipo_oper=='V'){
    $this->db->join('cji_persona','cji_persona.PERSP_Codigo=cji_cliente.PERSP_Codigo','left');
     $this->db->join('cji_empresa','cji_empresa.EMPRP_Codigo=cji_cliente.EMPRP_Codigo','left');
    
    }else{
    $this->db->join('cji_persona','cji_persona.PERSP_Codigo=cji_proveedor.PERSP_Codigo','left');
    $this->db->join('cji_empresa','cji_empresa.EMPRP_Codigo=cji_proveedor.EMPRP_Codigo','left');
    }
    
      $fecha1 = "$anio-$mes-01";
      $fecha2 = "$anio-$mes-".date("d");
    
      $this->db->from('cji_comprobante');

      $this->db->where('cji_comprobante.COMPP_Codigo',$compania);
      $this->db->where('cji_comprobante.CPC_TipoOperacion',$tipo_oper);
      $this->db->where('cji_comprobante.CPC_Fecha >=',$fecha1);
      $this->db->where('cji_comprobante.CPC_Fecha <=',$fecha2);
      $this->db->where('cji_comprobante.CPC_TipoDocumento',$tipo);
    
      $this->db->order_by('cji_comprobante.CPC_Numero','asc');
      
     $query = $this->db->get();    

     if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
     }
  }

  public function getAnioVentas(){
    $sql = "SELECT YEAR(CPC_Fecha) as anio FROM cji_comprobante GROUP BY CPC_Fecha";
    $query = $this->db->query($sql);
    if ($query->num_rows() > 0)
      return $query->result();
    else
      return NULL;
  }

    public function resumen_ventas_mensual_old($tipo, $anio, $mes, $totales = false) {
        $compania = $this->somevar['compania'];
        $sqlNotas = "";
        if ($totales == false){
            $sql = "SELECT MONTH(c.CPC_Fecha) AS mes, c.CPC_Fecha, c.CPC_subtotal, c.CPC_igv, c.CPC_total, c.CPC_TDC, c.COMPP_Codigo, c.CPC_Serie, c.CPC_Numero, c.CPC_TipoDocumento, c.CPC_FlagEstado, c.MONED_Codigo, m.MONED_Simbolo, m.MONED_Descripcion,

                      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                          FROM cji_cliente cc
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
                          WHERE cc.CLIP_Codigo = c.CLIP_Codigo
                      ) as razon_social_cliente,

                      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
                          FROM cji_cliente cc
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
                          WHERE cc.CLIP_Codigo = c.CLIP_Codigo
                      ) as numero_documento_cliente,

                      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                          FROM cji_proveedor pp
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
                          WHERE pp.PROVP_Codigo = c.PROVP_Codigo
                      ) as razon_social_proveedor,

                      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
                          FROM cji_proveedor pp
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
                          WHERE pp.PROVP_Codigo = c.PROVP_Codigo
                      ) as numero_documento_proveedor, 

                    (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo = 1 AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as gravada,
                    (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo = 8 AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as exonerada,
                    (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo = 9 AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as inafecta,
                    (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo NOT IN(1,8,9) AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as gratuita

                    FROM cji_comprobante c
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo

                    WHERE c.CPC_FlagEstado <> 2 AND c.COMPP_Codigo = '$compania' AND c.CPC_TipoOperacion = '$tipo' AND MONTH(CPC_Fecha) = '$mes' AND YEAR(CPC_Fecha) = '$anio' ORDER BY c.CPC_Fecha, c.CPC_Numero
                  ";

              $sqlNotas = "SELECT MONTH(c.CRED_Fecha) AS mes, c.CRED_Fecha as CPC_Fecha, c.CRED_subtotal as CPC_subtotal, c.CRED_igv as CPC_igv, c.CRED_total as CPC_total, c.CRED_TDC as CPC_TDC, c.COMPP_Codigo, CONCAT_WS('', CRED_TipoDocumento_inicio, c.CRED_Serie) as CPC_Serie, c.CRED_Numero as CPC_Numero, c.CRED_TipoNota as CPC_TipoDocumento, c.CRED_FlagEstado as CPC_FlagEstado, c.MONED_Codigo, m.MONED_Simbolo, m.MONED_Descripcion,

                      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                          FROM cji_cliente cc
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
                          WHERE cc.CLIP_Codigo = c.CLIP_Codigo
                      ) as razon_social_cliente,

                      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
                          FROM cji_cliente cc
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
                          WHERE cc.CLIP_Codigo = c.CLIP_Codigo
                      ) as numero_documento_cliente,

                      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
                          FROM cji_proveedor pp
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
                          WHERE pp.PROVP_Codigo = c.PROVP_Codigo
                      ) as razon_social_proveedor,

                      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
                          FROM cji_proveedor pp
                          LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
                          LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
                          WHERE pp.PROVP_Codigo = c.PROVP_Codigo
                      ) as numero_documento_proveedor, 

                    (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo = 1 AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as gravada,
                    (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo = 8 AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as exonerada,
                    (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo = 9 AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as inafecta,
                    (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo NOT IN(1,8,9) AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as gratuita

                    FROM cji_nota c
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo

                    WHERE c.CRED_FlagEstado <> 2 AND c.COMPP_Codigo = '$compania' AND c.CRED_TipoOperacion = '$tipo' AND MONTH(CRED_Fecha) = '$mes' AND YEAR(CRED_Fecha) = '$anio' ORDER BY c.CRED_Fecha, c.CRED_Numero
                  ";
        }
        else{
            $sql = "SELECT SUM(c.CPC_total) AS total, m.MONED_Simbolo, c.CPC_TipoDocumento
                    FROM cji_comprobante c
                    LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
                    WHERE c.CPC_FlagEstado = 1 AND c.COMPP_Codigo = '$compania' AND c.CPC_TipoOperacion = '$tipo' AND MONTH(CPC_Fecha) = '$mes' AND YEAR(CPC_Fecha) = '$anio' GROUP BY c.CPC_TipoDocumento, c.MONED_Codigo";
        }

        $query = $this->db->query($sql);

        $data = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
        }

        if ( $sqlNotas != "" ){
          $queryNotas = $this->db->query($sqlNotas);
          if ($queryNotas->num_rows > 0) {
            foreach ($queryNotas->result() as $fila) {
                $data[] = $fila;
            }
          }
        }
        return $data;
    }

     public function resumen_ventas_mensual($filter=""){

    $compania = $this->somevar['compania'];
    $sqlNotas = "";
    $where    = "";
    $where_n  = ""; 


    if($filter->tipo!="T" && $filter->tipo!=""){
      if($filter->tipo == "V"){
        $where .= " AND c.CPC_TipoDocumento IN ('F','B')";
      }else{
        $where .= " AND c.CPC_TipoDocumento = '$filter->tipo'";
      }
    }
    
    if($filter->forma_pago!=""){
      $where .= " AND c.FORPAP_Codigo='$filter->forma_pago'";
    }

    if($filter->vendedor!=""){
      $where .= " AND c.CPC_Vendedor='$filter->vendedor'";

    }

    if($filter->moneda!=""){
      $where    .= " AND c.MONED_Codigo='$filter->moneda'";
      $where_n  .= " AND c.MONED_Codigo='$filter->moneda'";
    }

    if($filter->consolidado == 0){
      $where    .= " AND c.COMPP_Codigo='$compania'";
      $where_n  .= " AND c.COMPP_Codigo='$compania'";
    }

    if ($totales == false){
      $sql = "SELECT c.CPC_Fecha, c.CPC_subtotal, c.CPC_igv, c.CPC_total, c.CPC_TDC, c.COMPP_Codigo, c.CPC_Serie, c.CPC_Numero, c.CPC_TipoDocumento, c.CPC_FlagEstado, c.MONED_Codigo, m.MONED_Simbolo, m.MONED_Descripcion, c.FORPAP_Codigo, fp.FORPAC_Descripcion,

      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
      FROM cji_cliente cc
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
      WHERE cc.CLIP_Codigo = c.CLIP_Codigo
      ) as razon_social_cliente,

      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
      FROM cji_cliente cc
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
      WHERE cc.CLIP_Codigo = c.CLIP_Codigo
      ) as numero_documento_cliente,

      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
      FROM cji_proveedor pp
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
      WHERE pp.PROVP_Codigo = c.PROVP_Codigo
      ) as razon_social_proveedor,

      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
      FROM cji_proveedor pp
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
      WHERE pp.PROVP_Codigo = c.PROVP_Codigo
      ) as numero_documento_proveedor, 

      (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo = 1 AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as gravada,
      (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo = 8 AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as exonerada,
      (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo = 9 AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as inafecta,
      (SELECT SUM(cd.CPDEC_Subtotal) FROM cji_comprobantedetalle cd WHERE cd.AFECT_Codigo NOT IN(1,8,9) AND cd.CPP_Codigo = c.CPP_Codigo AND cd.CPDEC_FlagEstado = 1) as gratuita

      FROM cji_comprobante c
      LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
      LEFT JOIN cji_formapago fp ON fp.FORPAP_Codigo = c.FORPAP_Codigo
      WHERE  c.CPC_FlagEstado != 2 AND c.CPC_TipoOperacion = '$filter->tipo_oper' AND  c.CPC_Fecha BETWEEN '$filter->fecha1' AND '$filter->fecha2' $where ORDER BY c.CPC_Fecha, c.CPC_Numero
      ";

      $sqlNotas = "SELECT MONTH(c.CRED_Fecha) AS mes, c.CRED_Fecha as CPC_Fecha, c.CRED_subtotal as CPC_subtotal, c.CRED_igv as CPC_igv, c.CRED_total as CPC_total, c.CRED_TDC as CPC_TDC, c.COMPP_Codigo,  c.CRED_Serie as CPC_Serie, c.CRED_Numero as CPC_Numero, c.CRED_TipoNota as CPC_TipoDocumento, c.CRED_FlagEstado as CPC_FlagEstado, c.MONED_Codigo, m.MONED_Simbolo, m.MONED_Descripcion,

      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
      FROM cji_cliente cc
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
      WHERE cc.CLIP_Codigo = c.CLIP_Codigo
      ) as razon_social_cliente,

      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
      FROM cji_cliente cc
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = cc.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = cc.PERSP_Codigo
      WHERE cc.CLIP_Codigo = c.CLIP_Codigo
      ) as numero_documento_cliente,

      (SELECT CONCAT_WS(' ', e.EMPRC_RazonSocial, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno)
      FROM cji_proveedor pp
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
      WHERE pp.PROVP_Codigo = c.PROVP_Codigo
      ) as razon_social_proveedor,

      (SELECT CONCAT_WS(' ', e.EMPRC_Ruc, p.PERSC_NumeroDocIdentidad)
      FROM cji_proveedor pp
      LEFT JOIN cji_empresa e ON e.EMPRP_Codigo = pp.EMPRP_Codigo
      LEFT JOIN cji_persona p ON p.PERSP_Codigo = pp.PERSP_Codigo
      WHERE pp.PROVP_Codigo = c.PROVP_Codigo
      ) as numero_documento_proveedor, 

      (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo = 1 AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as gravada,
      (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo = 8 AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as exonerada,
      (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo = 9 AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as inafecta,
      (SELECT SUM(cd.CREDET_Subtotal) FROM cji_notadetalle cd WHERE cd.AFECT_Codigo NOT IN(1,8,9) AND cd.CRED_Codigo = c.CRED_Codigo AND cd.CREDET_FlagEstado = 1) as gratuita

      FROM cji_nota c
      LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo

      WHERE c.CRED_FlagEstado != 2 AND c.CRED_TipoOperacion = '$filter->tipo_oper' AND  c.CRED_Fecha BETWEEN '$filter->fecha1' AND '$filter->fecha2' $where_n ORDER BY c.CRED_Fecha, c.CRED_Numero
      ";
    }
    else{
      $sql = "SELECT SUM(c.CPC_total) AS total, m.MONED_Simbolo, c.CPC_TipoDocumento
      FROM cji_comprobante c
      LEFT JOIN cji_moneda m ON m.MONED_Codigo = c.MONED_Codigo
      WHERE c.CPC_FlagEstado = 1 AND c.COMPP_Codigo = '$compania' AND c.CPC_TipoOperacion = '$tipo' AND MONTH(CPC_Fecha) = '$mes' AND YEAR(CPC_Fecha) = '$anio' GROUP BY c.CPC_TipoDocumento, c.MONED_Codigo";
    }

    $query = $this->db->query($sql);
    $data = array();
    if($filter->tipo=="T" || $filter->tipo==""){
      if ($query->num_rows() > 0) {
        foreach ($query->result() as $fila) {
          $data[] = $fila;
        }
    }

    if ( $sqlNotas != "" ){
      $queryNotas = $this->db->query($sqlNotas);
      if ($queryNotas->num_rows > 0) {
        foreach ($queryNotas->result() as $fila) {
          $data[] = $fila;
        }
      }
    }
    }elseif($filter->tipo=="F" || $filter->tipo=="B" || $filter->tipo=="N"){
      if ($query->num_rows() > 0) {
        foreach ($query->result() as $fila) {
          $data[] = $fila;
        }
      }
    }elseif($filter->tipo=="C"){
      if ( $sqlNotas != "" ){
        $queryNotas = $this->db->query($sqlNotas);
        if ($queryNotas->num_rows > 0) {
          foreach ($queryNotas->result() as $fila) {
            $data[] = $fila;
          }
        }
      }
    }elseif($filter->tipo=="V"){
      if ($query->num_rows() > 0) {
        foreach ($query->result() as $fila) {
          $data[] = $fila;
        }
      }if ( $sqlNotas != "" ){
        $queryNotas = $this->db->query($sqlNotas);
        if ($queryNotas->num_rows > 0) {
          foreach ($queryNotas->result() as $fila) {
            $data[] = $fila;
          }
        }
      }
    }

    return $data;
  }
  
  
   public function ventas_por_producto_resumen($inicio,$fin)
  {
    $sql = "SELECT p.PROD_CodigoUsuario as codigo, SUM(cd.CPDEC_Cantidad) as cantidad,
    p.PROD_Nombre AS NOMBRE,p.PROD_Comentario as comentario,
    SUM( IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total) ) AS VENTAS
    FROM cji_comprobantedetalle cd
    JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo and c.COMPP_Codigo=".$this->somevar ['compania']."
  
    JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
    WHERE c.CPC_FlagEstado=1 and c.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin') and c.CPC_TipoOperacion = 'V'
  GROUP BY p.PROD_Codigo";
    $query = $this->db->query($sql);
    
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_producto_mensual($inicio,$fin)
  {
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio)
    {
      $sql = "SELECT p.PROD_CodigoUsuario as codigo, SUM(cd.CPDEC_Cantidad) as cantidad,
       p.PROD_Nombre AS NOMBRE,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= " p.PROD_Comentario as comentario
      FROM cji_comprobantedetalle cd
     JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo and c.COMPP_Codigo=".$this->somevar ['compania']." JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      WHERE c.CPC_FlagEstado=1 and YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and and c.CPC_TipoOperacion = 'V'
      GROUP BY p.PROD_Nombre";
    
    }elseif($anioFin == $anioInicio){
      $sql = "SELECT p.PROD_CodigoUsuario as codigo, SUM(cd.CPDEC_Cantidad) as cantidad,
       p.PROD_Nombre AS NOMBRE,
      ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(CPC_Fecha)=".intval($mesInicio).",IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$anioFin".intval($mesInicio)."";
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
      }
      
      $sql.= " , p.PROD_Comentario as comentario
      FROM cji_comprobantedetalle cd
      JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo and c.COMPP_Codigo=".$this->somevar ['compania']."
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
     
      WHERE c.CPC_FlagEstado=1 and YEAR(c.CPC_Fecha) = '$anioInicio' and c.CPC_TipoOperacion = 'V'
      GROUP BY p.PROD_Nombre";
    }

    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
  return $data;
  }
  
  public function ventas_por_producto_anual($inicio,$fin)
  {
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio)
    {
    
      $sql = "SELECT p.PROD_CodigoUsuario as codigo, SUM(cd.CPDEC_Cantidad) as cantidad,
      p.PROD_Nombre AS NOMBRE,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as y$j,";
        }else{
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= "  p.PROD_Comentario as comentario
      FROM cji_comprobantedetalle cd
   JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo and c.COMPP_Codigo=".$this->somevar ['compania']."
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      WHERE c.CPC_FlagEstado=1 and YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin' and c.CPC_TipoOperacion = 'V'
      GROUP BY  p.PROD_Nombre";
    
    }elseif($anioFin == $anioInicio){
    
      $sql = "SELECT p.PROD_CodigoUsuario as codigo, SUM(cd.CPDEC_Cantidad) as cantidad,
      p.PROD_Nombre AS NOMBRE,
      ";
      $sql .= "SUM(IF(c.MONED_Codigo=2,c.CPC_TDC*cd.CPDEC_Total,cd.CPDEC_Total)) as y$anioFin ";
      $sql.= "  , p.PROD_Comentario as comentario
      FROM cji_comprobantedetalle cd
     JOIN cji_comprobante c ON cd.CPP_Codigo = c.CPP_Codigo and c.COMPP_Codigo=".$this->somevar ['compania']."
      JOIN cji_producto p ON cd.PROD_Codigo = p.PROD_Codigo
      WHERE c.CPC_FlagEstado=1 and  YEAR(c.CPC_Fecha) = '$anioInicio' and c.CPC_TipoOperacion = 'V'
      GROUP BY  p.PROD_Nombre";
    }
  
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
  return $data;
  }
  
   public function ventas_por_tienda_resumen($inicio,$fin)
  {
    //SELECT SUM( IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total)) as VENTAS, p.PERSC_Nombre as NOMBRE, p.PERSC_ApellidoPaterno as PATERNO 
    $sql = "
        SELECT SUM( IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total)) as VENTAS, e.EESTABC_Descripcion as nombre ,e.EESTAC_Direccion as direccion
        FROM cji_comprobante c 
        LEFT JOIN cji_emprestablecimiento e ON e.EESTABP_Codigo = c.COMPP_Codigo
        WHERE c.CPC_Fecha BETWEEN DATE('$inicio') AND DATE('$fin') 
        GROUP BY COMPP_Codigo ORDER BY 1 ASC
  
  ";
    $query = $this->db->query($sql);
  
    
    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
    return $data;
  }
  
  public function ventas_por_tienda_mensual($inicio,$fin)
  {
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio)
    {
      $sql = " SELECT  e.EESTABC_Descripcion as nombre ,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
          for($i = 1; $i <= intval($mesFin); $i++)
          {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$j$i,";
          }
        }else if($j==$anioInicio){
          for($i = intval($mesInicio); $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$j$i,";
          }
        }else{
          for($i = 1; $i <= 12; $i++)
          {
            $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i AND YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$j$i,";
          }
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= "
     FROM cji_comprobante c 
  LEFT JOIN cji_emprestablecimiento e ON e.EESTABP_Codigo = c.COMPP_Codigo
      WHERE YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin'
      GROUP BY COMPP_Codigo ORDER BY 1 ASC";
    
    }elseif($anioFin == $anioInicio){
      $sql = " SELECT  e.EESTABC_Descripcion as nombre ,
      ";
      if($mesInicio == $mesFin)
      {
        $sql .= "SUM(IF(MONTH(CPC_Fecha)=".intval($mesInicio).",IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$anioFin".intval($mesInicio)."";
      }else{
        for($i = intval($mesInicio); $i <= intval($mesFin); $i++)
        {
          $sql .= "SUM(IF(MONTH(CPC_Fecha)=$i,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as m$anioFin$i,";
        }
        $sql = substr($sql,0,strlen($sql)-1);
      }
      
      $sql.= "
     FROM cji_comprobante c 
  LEFT JOIN cji_emprestablecimiento e ON e.EESTABP_Codigo = c.COMPP_Codigo
      WHERE YEAR(c.CPC_Fecha) = '$anioInicio'
      GROUP BY COMPP_Codigo ORDER BY 1 ASC";
    }

    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
  return $data;
  }
  
  public function ventas_por_tienda_anual($inicio,$fin)
  {
    $inicio = explode('-',$inicio);
    $mesInicio = $inicio[1];
    $anioInicio = $inicio[0];
    $fin = explode('-',$fin);
    $mesFin = $fin[1];
    $anioFin = $fin[0];
    
    if($anioFin > $anioInicio)
    {
    
      $sql = " SELECT  e.EESTABC_Descripcion as nombre ,
      ";
      for($j = $anioInicio; $j <= $anioFin; $j++)
      {
        if($j == $anioFin)
        {
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as y$j,";
        }else{
            $sql .= "SUM(IF(YEAR(CPC_Fecha)=$j,IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total),0)) as y$j,";
        }
      }
      $sql = substr($sql,0,strlen($sql)-1);
      
      $sql.= "
     FROM cji_comprobante c 
  LEFT JOIN cji_emprestablecimiento e ON e.EESTABP_Codigo = c.COMPP_Codigo
      WHERE YEAR(c.CPC_Fecha) BETWEEN '$anioInicio' AND '$anioFin'
      GROUP BY COMPP_Codigo ORDER BY 1 ASC";
     
    
    }elseif($anioFin == $anioInicio){
    
      $sql = " SELECT  e.EESTABC_Descripcion as nombre ,
      ";
      $sql .= "SUM(IF(c.MONED_Codigo=2,c.CPC_TDC*c.CPC_Total,c.CPC_Total)) as y$anioFin ";
      $sql.= "
      FROM cji_comprobante c 
  LEFT JOIN cji_emprestablecimiento e ON e.EESTABP_Codigo = c.COMPP_Codigo
      WHERE YEAR(c.CPC_Fecha) = '$anioInicio'
     GROUP BY COMPP_Codigo ORDER BY 1 ASC";
    }
  
    $query = $this->db->query($sql);

    $data = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result_array() as $result)
      {
        $data[] = $result;
      }
    }
  
  return $data;
  }
  
  
  
  
  
  }
?>