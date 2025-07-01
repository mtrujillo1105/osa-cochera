<?php
class Pedido_model extends CI_Model{
    var $somevar;
  function __construct()
        {
            parent::__construct();
            $this->load->database();
            $this->load->helper('date');
            $this->load->model('mantenimiento_model');
            $this->somevar ['compania'] = $this->session->userdata('compania');
            $this->somevar ['usuario']    = $this->session->userdata('user');
            $this->somevar['hoy']              = mdate("%Y-%m-%d %h:%i:%s",time());
  }
  
  public function seleccionar(){
      $arreglo = array(''=>':: Seleccione ::');
      $lista = $this->listar_pedidos();
      if(count($lista)>0){
          foreach($lista as $indice=>$valor)
          {   $indice1   = $valor->PEDIP_Codigo;
              $valor1    = $valor->PEDIC_Numero." ".$valor->PEDIC_Observacion." [".$valor->PEDIC_Tipo.']';                
              $arreglo[$indice1] = $valor1;
          }
      }
      return $arreglo;
  }
  public function selecionarPedido(){

  }////
 public function UpdatEstado($pedido,$P){
  $data = array('PEDIC_FlagEstado' => 1,'PEDI_Estado' =>$P);
  $this->db->where("PEDIP_Codigo",$pedido);
  $this->db->update("cji_pedido",$data);
}
public function updatePedido($pedido){
  $data = array('PEDIC_FlagEstado' => 1,'PEDI_Estado' =>'4');
  $this->db->where("PEDIP_Codigo",$pedido);
  $this->db->update("cji_pedido",$data);
}
public function finalisar_pedido($pedido){
   $data = array('PEDIC_FlagCotizado' => '1');
  $this->db->where("PEDIP_Codigo",$pedido);
  $this->db->update("cji_pedido",$data);
}
public function updateSolicitudCotizacion($pedido){
   $data = array('PRESUP_Estado' =>'4');
  $this->db->where("PRESUP_Codigo",$pedido);
  $this->db->update("cji_presupuesto",$data);
}


   public function seleccionar_finalizados()
   {
       $arreglo = array(''=>':: Seleccione ::');
       $lista = $this->listar_pedidos_finalizados();
       if(count($lista)>0){
           foreach($lista as $indice=>$valor)
           {   $indice1   = $valor->PEDIP_Codigo;
               $valor1    = $valor->PEDIC_Numero." ".$valor->PEDIC_Observacion." [".$valor->PEDIC_Tipo.']';                
               $arreglo[$indice1] = $valor1;
           }
       }
       return $arreglo;
   }


  function listar_pedidos2($number_items='',$offset=''){
        $compania = $this->somevar['compania'];
        $this->db->select('*');
        $this->db->from('cji_pedido',$number_items,$offset);
        $this->db->join('cji_centrocosto','cji_pedido.CENCOST_Codigo = cji_centrocosto.CENCOSP_Codigo','left');
        $this->db->where('cji_pedido.COMPP_Codigo',$compania);
        $this->db->where('PEDIC_FlagEstado','1');
        $this->db->where('PEDI_Estado','1');
        $this->db->or_where('PEDI_Estado','2');
        $this->db->or_where('PEDI_Estado','4');
        $this->db->where_not_in('PEDI_Estado','3');
        $this->db->where('PEDIC_FlagCotizado','0');
        $query = $this->db->get();
        if($query->num_rows() > 0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
        }     

  }
  public function contactos($emp){
    
    $sql="select p.PERSC_Nombre, p.PERSP_Codigo 
    from cji_persona as p
    join cji_emprcontacto as c on c.ECONC_Persona=p.PERSP_Codigo
    join cji_empresa as e  on e.EMPRP_Codigo =c.EMPRP_Codigo
    where c.EMPRP_Codigo =".$emp."
    ";
     $query = $this->db->query($sql);
        
        return $query->result();
  }
   public function obras($emp){
    
    $sql="select PROYP_Codigo,CONCAT(PROYC_Nombre,' - ',PROYC_Descripcion)as proyecto
    from cji_proyecto
    where CLIP_Codigo =".$emp."
    ";
     $query = $this->db->query($sql);
        
        return $query->result();
  }
  public function getPedigoCliente($codigo){
    $this->db->select('*');
    $this->db->where('CLIP_Codigo',$codigo);
    $this->db->where_not_in('PEDI_Estado','0');  
    $this->db->where_not_in('PEDIC_FlagCotizado','1');  
    $this->db->where_not_in('PEDI_Estado','3');  
    $query = $this->db->get('cji_pedido');
    if($query->num_rows()>0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
    }  
  }
  public function verificarSiYaSeCompleto($codi){
        $this->db->select('cp.PRESUP_Codigo,cp.PRESUC_FlagEstado,
          cp.PRESUP_Estado,p.PEDIP_Codigo');
        $this->db->join('cji_presupuesto cp','cp.PEDIP_Codigo=p.PEDIP_Codigo');
        $this->db->where('cp.CPC_TipoOperacion ','S');       
        //$this->db->where('cp.PEDIP_Codigo',$codi);
        $query = $this->db->get('cji_presupuesto p');
        if($query->num_rows() > 0){
         foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
        } 
  }

  function listar_pedidos($number_items='',$offset=''){
            $compania = $this->somevar['compania'];
        $this->db->select('*');
        $this->db->from('cji_pedido',$number_items,$offset);
        $this->db->join('cji_centrocosto','cji_pedido.CENCOST_Codigo = cji_centrocosto.CENCOSP_Codigo','left');
        $this->db->where('cji_pedido.COMPP_Codigo',$compania);
        $this->db->where('PEDIC_FlagEstado','2');

        $this->db->or_where('PEDIC_FlagEstado','1');//COMETAR
        $this->db->where_not_in('PEDIC_FlagCotizado','1');
        $this->db->where_not_in('PEDI_Estado','3');
        $query = $this->db->get();
        if($query->num_rows() > 0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
        }     

  } 
  function listar_pedidos_todos($filter = '',$number_items = '', $offset = '', $tipo_oper = 'ALL'){
    $compania = $this->somevar['compania'];

    $allCompanias = ($compania == 4) ? " pe.COMPP_Codigo < 5 " : " pe.COMPP_Codigo = $compania";

    $data_confi = $this->companiaconfiguracion_model->obtener($compania);
    $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
    
    $where = '';
    if (isset($filter->fechai) && $filter->fechai != '' && isset($filter->fechaf) && $filter->fechaf != '')
      $where = ' and pe.PEDIC_FechaRegistro BETWEEN "' . human_to_mysql($filter->fechai) . '" AND "' . human_to_mysql($filter->fechaf) . '"';
      
    switch ($data_confi_docu[0]->COMPCONFIDOCP_Tipo) {
      case '1': if (isset($filter->numero) && $filter->numero != '')
        $where .=' and pe.PEDIC_Numero=' . $filter->numero; break;
    }

    if (isset($filter->cliente) && $filter->cliente != '')
      $where.=' and pe.CLIP_Codigo=' . $filter->cliente;
    
    $limit = "";
        
    if ((string) $offset != '' && $number_items != '')
      $limit = 'LIMIT ' . $offset . ',' . $number_items;

    switch ($tipo_oper) {
      case 1:
        $tipo = " AND pe.CLIP_Codigo IS NOT NULL";
        break;
      case 2:
        $tipo = " AND pe.PERSP_Codigo IS NOT NULL";
        break;
      case 'ALL':
        $tipo = "";
        break;
      
      default:
        $tipo = "";
        break;
    }

      $sql = "SELECT DISTINCT pe.*, pr.PRESUC_Serie, pr.PRESUC_Numero, oc.OCOMC_Serie, oc.OCOMC_Numero
                FROM cji_pedido pe
                INNER JOIN cji_pedidodetalle pedidetalle ON pe.PEDIP_Codigo = pedidetalle.PEDIP_Codigo
                LEFT JOIN cji_ordencompra oc ON oc.PEDIP_Codigo = pe.PEDIP_Codigo
                LEFT JOIN cji_presupuesto pr ON pr.PRESUP_Codigo = pe.PRESUP_Codigo
                WHERE $allCompanias  $where $tipo
                ORDER BY pe.PEDIP_Codigo DESC
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

    public function nameEstablecimiento($id){
      $sql = "SELECT e.*, emp.EMPRC_RazonSocial, emp.EMPRC_Ruc FROM cji_emprestablecimiento e
                INNER JOIN cji_compania c ON c.EESTABP_Codigo = e.EESTABP_Codigo
                INNER JOIN cji_empresa emp ON emp.EMPRP_Codigo = e.EMPRP_Codigo
                WHERE c.COMPP_Codigo = $id
              ";

      $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $fila) {
                $data[] = $fila;
            }
            return $data;
        }
        else
          return NULL;
    }
  
  function listar_pedidos_finalizados($number_items='',$offset='')
        {
          
    $compania = $this->somevar['compania'];
      
    $this->db->select('*');
        $this->db->from('cji_pedido',$number_items,$offset);
        $this->db->where('cji_pedido.COMPP_Codigo',$compania);
        $this->db->where('PEDIC_FlagEstado',0);
        $query = $this->db->get();
        if($query->num_rows() > 0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
        }     

  }
  
  function listar_proveedores_pedido($pedido){
    $this->db->select('*');
        $this->db->from('cji_presupuesto');
        $this->db->join('cji_proveedor','cji_presupuesto.PROVP_Codigo = cji_proveedor.PROVP_Codigo','left');
        $this->db->join('cji_formapago','cji_formapago.FORPAP_Codigo = cji_presupuesto.FORPAP_Codigo','left');
        $this->db->join('cji_empresa','cji_proveedor.EMPRP_Codigo = cji_empresa.EMPRP_Codigo','left');
        $this->db->where('CPC_TipoOperacion','C');
        $this->db->where('PEDIP_Codigo  ',$pedido);
        $this->db->order_by('PEDIP_Codigo','RANDOM');
    $this->db->limit('3');
        $query = $this->db->get();
        if($query->num_rows() > 0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
        } 
  }
public function listar_proveedores_pedido2($pedido){
  //en esta ocacion solo traera las empresas no las persosnas asi que a esperar
 $sql= 'SELECT * FROM cji_presupuesto p
JOIN cji_proveedor pr on pr.PROVP_Codigo=p.PROVP_Codigo
JOIN cji_empresa e on pr.EMPRP_Codigo = e.EMPRP_Codigo
WHERE CPC_TipoOperacion="C" AND PEDIP_Codigo ='.$pedido.' ORDER BY PRESUP_Codigo DESC';
 $query = $this->db->query($sql);
 if($query->num_rows()>0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
        }
}
  function buscar_producto_proveedor_pedido($producto,$unidad,$proveedor,$pedido){
    if($producto != '' AND $unidad != '' AND $proveedor != '' AND $pedido!='')
    {
      $sql = 'SELECT * FROM cji_presupuestodetalle 
      JOIN cji_presupuesto USING(PRESUP_Codigo) 
      JOIN cji_unidadmedida USING (UNDMED_Codigo) 
      WHERE PEDIP_Codigo = '.$pedido.' AND PROD_Codigo = '.$producto.' AND UNDMED_Codigo = '.$unidad.' AND PROVP_Codigo='.$proveedor;
      $query = $this->db->query($sql);
      if($query->num_rows() > 0){
        foreach($query->result() as $fila){
          $data[] = $fila;
        }
        return $data;
      }
    }else{
      return array();
    }
  }
  
  function listar_total_productos_pedido($pedido){
    $sql = 'SELECT * FROM cji_presupuestodetalle 
        LEFT JOIN cji_producto ON cji_presupuestodetalle.PROD_Codigo = cji_producto.PROD_Codigo 
        JOIN cji_unidadmedida USING (UNDMED_Codigo) 
        WHERE PRESUP_Codigo IN 
        (SELECT PRESUP_Codigo FROM cji_presupuesto WHERE CPC_TipoOperacion="C" AND PEDIP_Codigo='.$pedido.')
        GROUP BY cji_presupuestodetalle.PROD_Codigo';

    $query = $this->db->query($sql);
    if($query->num_rows() > 0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
    }
  }
  
    public function obtener_pedido($pedido){
      $sql = "SELECT p.*,
                (SELECT CONCAT_WS('-', oc.OCOMC_Serie, oc.OCOMC_Numero) FROM cji_ordencompra oc WHERE oc.OCOMP_Codigo = p.OCOMP_Codigo) as serieNumero
                FROM cji_pedido p
                WHERE PEDIP_Codigo = $pedido;
              ";
      $query = $this->db->query($sql);

      if($query->num_rows() > 0){
        foreach($query->result() as $fila){
          $data[] = $fila;
        }
        return $data;
      }
    }
  function cliente_codigo_pedido($pedido){
    $this->db->select('CLIP_Codigo,PEDIP_Codigo');
        $query = $this->db->where('PEDIP_Codigo',$pedido)->get('cji_pedido');
    if($query->num_rows() > 0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
    }
    }
  function cerrar_pedido($pedido){
      $data = array(
        'PEDIC_FlagEstado' => 0
    );
      $this->db->where("PEDIP_Codigo",$pedido);
      $this->db->update("cji_pedido",$data);
  }
  
  
function obtener_detalle_pedido($pedido){
  $sql = "SELECT pd.*, p.*, fb.FABRIC_Descripcion
            FROM cji_pedidodetalle pd
            INNER JOIN cji_producto p ON p.PROD_Codigo = pd.PROD_Codigo
            LEFT JOIN cji_fabricante fb ON fb.FABRIP_Codigo = p.FABRIP_Codigo
            WHERE PEDIP_Codigo = $pedido AND PEDIDETC_FlagEstado = '1'
          ";
  $query = $this->db->query($sql);
   #$where = array("PEDIP_Codigo"  => $pedido,"PEDIDETC_FlagEstado" => "1");
   #$query = $this->db->where($where)->get('cji_pedidodetalle');
    if($query->num_rows()>0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
    }
    else
      return NULL;
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



    
  public function getPedigoCodigo($pedido){
    $this->db->select('PEDIP_Codigo');
    $where = array("PEDIP_Codigo"  => $pedido,"PEDIC_FlagEstado" => "1");
    $query = $this->db->where($where)->get('cji_pedido');
    if($query->num_rows()>0){
      foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
    }
   } 
  function insertar_pedido($serie, $numero, $fechasistema, $moneda, $obra, $cliente, $contacto, $ocompra = NULL, $igvpp, $importebruto, $descuentotal, $vventa, $igvtotal, $preciototal, $descuento100, $estado, $observacion){
      $compania = $this->somevar['compania'];
      $usuario =  $this->somevar['usuario'];

      if ($cliente == ""){
        $cliente = NULL;
        $personal = $_SESSION['persona'];
      }
      else
        $personal == NULL;
  
   $fecha = date('Y-m-d h:i:s');
      $data = array(
          'PEDIC_TipoDocume' =>"V",
          'PEDIC_Numero' =>$numero,
          'PEDIC_Serie' =>$serie,
          'PEDIC_FechaSistema' =>$fechasistema,
          'MONED_Codigo' =>$moneda,
          'PROYP_Codigo' =>$obra,
          'CLIP_Codigo' =>$cliente,
          'PERSP_Codigo' =>$personal,
          'ECONP_Contacto' =>$contacto,
          'OCOMP_Codigo' =>$ocompra,
          'PEDIC_IGV' =>$igvpp,
          'COMPP_Codigo' =>$compania,
          'PEDIC_ImporteBruto' =>$importebruto,
          'PEDIC_DescuentoTotal' =>$descuentotal,
          'PEDIC_Descuento100' =>$descuento100,
          'PEDIC_ValorVenta' =>$vventa,
          'PEDIC_IGVTotal' =>$igvtotal,
          'PEDIC_PrecioTotal' =>$preciototal,
          'PEDIC_FechaRegistro' =>$fecha,
          'PEDIC_FlagEstado' => $estado,
          'PEDIC_EstadoPresupuesto' =>"1",
          'PEDIC_Observacion' => $observacion
      );
      $this->db->insert("cji_pedido",$data);
    return $this->db->insert_id();
    }
    function update_pedido_presupuesto($pedido,$presupuesto){
      $data = array(
          'PEDIC_EstadoPresupuesto' =>"0",
          'PRESUP_Codigo' =>$presupuesto
      );
      
      $this->db->where("PEDIP_Codigo",$pedido);
      $this->db->update("cji_pedido",$data);
    }
    
    
    function modificar_pedido($pedido,$filter=null){
      
      $compania = $this->somevar['compania'];
      $usuario =  $this->somevar['usuario'];
      $filter->PEDIC_FechaModificacion= date('Y-m-d h:i:s');
      
      $where = array("PEDIP_Codigo" => $pedido);
      $this->db->where($where);
      $this->db->update('cji_pedido', (array) $filter);
     
    }
   
  function eliminar_pedido($pedido){
    $data     = array("PEDIC_FlagEstado"=>'0');
    $where = array("PEDIP_Codigo"=>$pedido);
    $this->db->where($where);
    $this->db->update('cji_pedido',$data);
    
  
    }
    function eliminar_producto_pedido2($pedido){
      $data      = array("PEDIDETC_FlagEstado"=>'0');
      $where = array("PEDIP_Codigo"=>$pedido);
      $this->db->where($where);
      $this->db->update('cji_pedidodetalle',$data);
    }
    function eliminar_producto_pedido($detalle_pedido){
    $data      = array("PEDIDETC_FlagEstado"=>'0');
    $where = array("PEDIDETP_Codigo"=>$detalle_pedido);
    $this->db->where($where);
    $this->db->update('cji_pedidodetalle',$data);
    }
    public function traerNumeroDoc(){

         $this->db->select_max('PEDIC_Numero');
        $query = $this->db->get('cji_pedido');   

         foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
    }
  public function traerSerieDoc(){

         $this->db->select_max('PEDIC_Serie');
        $query = $this->db->get('cji_pedido');   

         foreach($query->result() as $fila){
        $data[] = $fila;
      }
      return $data;
    }
    
    public function buscar_pedido_asoc($tipo_oper , $docu_orig, $filter = NULL, $number_items = '', $offset = '', $fecha_registro = '') {
      $compania = $this->somevar['compania'];
    
      $where = '';
      
            if (isset($filter->cliente) && $filter->cliente != '')
              $where.=' and p.CLIP_Codigo=' . $filter->cliente;
            
                $limit = "";
    
                if ((string) $offset != '' && $number_items != '')
                  $limit = 'LIMIT ' . $offset . ',' . $number_items;
                
//                
    
                  $sql = "
    SELECT p.PEDIC_FechaRegistro,
                         p.PEDIP_Codigo,
                         p.PEDIC_Serie,
                         p.PEDIC_Numero,
                         p.CLIP_Codigo,
                       (CASE c.CLIC_TipoPersona  WHEN '1'
                       THEN e.EMPRC_RazonSocial
                       ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre,
                       m.MONED_Simbolo,
                       p.PEDIC_PrecioTotal,
                       p.PEDIC_FlagEstado
                FROM cji_pedido p
                LEFT JOIN cji_moneda m ON m.MONED_Codigo=p.MONED_Codigo
                LEFT JOIN cji_pedidodetalle pd ON pd.PEDIP_Codigo=p.PEDIP_Codigo
                INNER JOIN cji_cliente c ON c.CLIP_Codigo=p.CLIP_Codigo
                LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND c.CLIC_TipoPersona ='0'
                LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND c.CLIC_TipoPersona='1'
                WHERE p.PEDIC_TipoDocume ='V' and p.PEDIC_FlagEstado='1' AND p.PEDIC_EstadoPresupuesto='1' "  . $where . " 
                GROUP BY p.PEDIP_Codigo
                ORDER BY p.PEDIC_FechaRegistro DESC" . $limit . "
    
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
   
    
    public function obtener_pedido_filtrado($pedido) {
      
      $where = array('PEDIP_Codigo' => $pedido);
      $query = $this->db->where($where)->get('cji_pedido');
      if ($query->num_rows() > 0) {
        foreach ($query->result() as $fila) {
          $data[] = $fila;
        }
        return $data;
      }
      
    }
    public function listar_pedido_pdf($fechain, $fechafin, $numero, $cliente)
    {
      $compania = $this->somevar['compania'];
      $data_confi = $this->companiaconfiguracion_model->obtener($compania);
      $data_confi_docu = $this->companiaconfidocumento_model->obtener($data_confi[0]->COMPCONFIP_Codigo, 13);
      
      $where = '';
      if ($fechain != '--' && $fechafin!= '--')
        $where = ' and pe.PEDIC_FechaRegistro BETWEEN "' . human_to_mysql($fechain) . '" AND "' . human_to_mysql($fechafin) . '"';
        switch ($data_confi_docu[0]->COMPCONFIDOCP_Tipo) {
          case '1': if ( $numero != '--')
            $where.=' and pe.PEDIC_Numero=' .  $numero; break;
        }
        if ($cliente != '--')
          $where.=' and pe.CLIP_Codigo=' . $cliente;
      
      
      
            $sql = "select DISTINCT pe.PEDIP_Codigo,pe.PEDIC_Serie,pe.PEDIC_Numero,pe.CLIP_Codigo,pe.PEDIC_PrecioTotal,substring(pe.PEDIC_FechaRegistro,1,10) as FECHA,pe.PROYP_Codigo,pe.PEDIC_EstadoPresupuesto, m.MONED_Simbolo, pr.PRESUC_Serie ,pr.PRESUC_Numero from cji_pedido pe
              inner join cji_pedidodetalle pedidetalle on pe.PEDIP_Codigo = pedidetalle.PEDIP_Codigo
              left join cji_presupuesto pr on pr.PRESUP_Codigo = pe.PRESUP_Codigo
              LEFT JOIN cji_moneda m ON m.MONED_Codigo=pe.MONED_Codigo
              WHERE pe.PEDIC_FlagEstado = 1 " . $where . "";
      
      
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
              foreach ($query->result() as $fila) {
                $data[] = $fila;
              }
              return $data;
            }
            return array();
      
    }

    public function listar_pedido_asoc(){
        $sql = "SELECT p.*, pd.*,
                (SELECT EESTABC_Descripcion FROM cji_compania c INNER JOIN cji_emprestablecimiento e ON e.EESTABP_Codigo = c.EESTABP_Codigo WHERE c.COMPP_Codigo = p.COMPP_Codigo LIMIT 1) as establecimiento
                  FROM cji_pedido p
                  INNER JOIN cji_pedidodetalle pd ON pd.PEDIP_Codigo = p.PEDIP_Codigo
                  WHERE p.PEDIC_FlagEstado = 3 AND pd.PEDIDETC_FlagEstado = '1' AND NOT EXISTS(SELECT pd.PEDIP_Codigo FROM cji_produccion pd WHERE pd.PEDIP_Codigo = p.PEDIP_Codigo)
                  GROUP BY p.PEDIP_Codigo
                  ORDER BY p.PEDIP_Codigo ASC
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