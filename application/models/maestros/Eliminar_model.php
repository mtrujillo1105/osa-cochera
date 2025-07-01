<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow
/* ******************************************************************************** */
class Eliminar_model extends CI_Model{
	##  -> Begin
	private $compania;
	private $usuario;
  ##  -> End

	##  -> Begin
	public function __construct(){
		parent::__construct();
		$this->compania = $this->session->userdata('compania');
		$this->usuario  = $this->session->userdata('usuario');
	}
  ##  -> End
	
	public function Agregar_Tabla($ordAdj){
		$sql="$ordAdj";
		$this->db->query($sql);
	}
	
	public function Eliminar_Tabla($ordAdj){
		$sql="$ordAdj";
		$this->db->query($sql);
	}
	
	public function EliminarTransaccionales(){
		$this->db->truncate('cji_cotizacion');
		$this->db->truncate('cji_cotizaciondetalle');
		$this->db->truncate('cji_comprobante');
		$this->db->truncate('cji_comprobantedetalle');
		$this->db->truncate('cji_guiarem');
		$this->db->truncate('cji_guiaremdetalle');
		$this->db->truncate('cji_guiasa');
		$this->db->truncate('cji_guiasadetalle');
		$this->db->truncate('cji_guiain');
		$this->db->truncate('cji_guiaindetalle');
		$this->db->truncate('cji_guiatrans');
		$this->db->truncate('cji_guiatransdetalle');
		$this->db->truncate('cji_ordencompra');
		$this->db->truncate('cji_ocompradetalle');
		$this->db->truncate('cji_presupuesto');
		$this->db->truncate('cji_presupuestodetalle');
		$this->db->truncate('cji_nota');
		$this->db->truncate('cji_notadetalle');
		$this->db->truncate('cji_cuentas');
		$this->db->truncate('cji_cuentasempresas');
		$this->db->truncate('cji_cuentaspago');
		$this->db->truncate('cji_pago');
		$this->db->truncate('cji_kardex');
		$this->db->truncate('cji_inventario');
		$this->db->truncate('cji_inventariodetalle');
		$this->db->truncate('cji_letra');
	}

    #############################################
    ######## TRUNCATES - 
    #############################################
	
	public function truncate_comprobantes(){
		
        #############################################
        ######## VENTAS Y ENVIOS AL FACTURADOR
        #############################################

		$this->db->truncate("cji_comprobante");
		$this->db->truncate("cji_comprobantedetalle");

		$this->db->truncate("cji_comprobante_guiarem");
		$this->db->truncate("comprobantes_cuotas");
		$this->db->truncate("cji_letra");
		$this->db->truncate("cji_comprobante_letra");

		$this->db->truncate("cji_guiarem");
		$this->db->truncate("cji_guiaremdetalle");

		$this->db->truncate("cji_nota");
		$this->db->truncate("cji_notadetalle");
		
		$this->db->truncate("cji_respuestasunat");
		
        #############################################
        ######### CUENTAS Y PAGOS
        #############################################
		
		$this->db->truncate("cji_cuentas");
		$this->db->truncate("cji_cuentasempresas");
		$this->db->truncate("cji_cuentaspago");
		$this->db->truncate("cji_pago");

		$this->db->truncate("cji_caja");
		$this->db->truncate("cji_cheque");
		$this->db->truncate("cji_cajamovimiento");
		$this->db->truncate("cji_tipocaja");
		$this->db->truncate("cji_flujocaja");
		$this->db->truncate("cji_reponsblmoviminto");
		
		
		$this->db->truncate("cji_kardex");
		$this->db->truncate("temporal_detalle");

		$sql = "UPDATE cji_configuracion SET CONFIC_Numero = 0 WHERE DOCUP_Codigo IN(8,9,10,11,12,14,16)";
		$this->db->query($sql);
	}
	
	public function truncate_docs(){
		
        #############################################
        ######## OTROS DOCUMENTOS
        #############################################
		
		$this->db->truncate("cji_ordencompra");
		$this->db->truncate("cji_ocompradetalle");
		$this->db->truncate("cji_presupuesto");
		$this->db->truncate("cji_presupuestodetalle");

		$this->db->truncate("cji_cotizacion");
		$this->db->truncate("cji_cotizaciondetalle");

		$this->db->truncate("cji_pedido");
		$this->db->truncate("cji_pedidodetalle");

		$this->db->truncate("cji_produccion");
		$this->db->truncate("cji_producciondetalle");

		$this->db->truncate("cji_despacho");
		$this->db->truncate("cji_despachodetalle");
	}

	public function truncate_inventarios(){
        #############################################
        ############# STOCK
        #############################################
		$this->db->truncate("cji_almacen");
		$this->db->truncate("cji_almacenproducto");
		$this->db->truncate("cji_almacenproductoserie");
		$this->db->truncate("cji_almaprolote");

		$this->db->truncate("cji_lote");
		$this->db->truncate("cji_loteprorrateo");

		$this->db->truncate("cji_serie");
		$this->db->truncate("cji_seriedocumento");
		$this->db->truncate("cji_seriemov");

		$this->db->truncate('cji_inventario');
		$this->db->truncate('cji_inventariodetalle');
		
        #############################################
        ######## GUIAS INTERNAS
        #############################################

		$this->db->truncate("cji_guiasa");
		$this->db->truncate("cji_guiasadetalle");
		$this->db->truncate("cji_guiain");
		$this->db->truncate("cji_guiaindetalle");
		$this->db->truncate("cji_guiatrans");
		$this->db->truncate("cji_guiatransdetalle");

		$this->db->truncate("cji_kardex");
	}

	public function truncate_stock(){
        #############################################
        ############# STOCK
        #############################################
		$this->db->truncate("cji_almacenproducto");
		$this->db->truncate("cji_almacenproductoserie");
		$this->db->truncate("cji_almaprolote");

		$this->db->truncate("cji_lote");
		$this->db->truncate("cji_loteprorrateo");

		$this->db->truncate("cji_serie");
		$this->db->truncate("cji_seriedocumento");
		$this->db->truncate("cji_seriemov");

		$this->db->truncate('cji_inventariodetalle');
		
        #############################################
        ######## GUIAS INTERNAS
        #############################################

		$this->db->truncate("cji_guiasa");
		$this->db->truncate("cji_guiasadetalle");
		$this->db->truncate("cji_guiain");
		$this->db->truncate("cji_guiaindetalle");
		$this->db->truncate("cji_guiatrans");
		$this->db->truncate("cji_guiatransdetalle");

		$this->db->truncate("cji_kardex");
	}

	public function truncate_productos(){
        #############################################
        ############# PRODUCTOS
        #############################################
		
		$this->db->truncate("cji_producto");
		$this->db->truncate("cji_productocompania");
		$this->db->truncate("cji_productounidad");

		$this->db->truncate("cji_productoprecio");
		$this->db->truncate("cji_productoproveedor");

		$this->db->truncate("cji_familia");
		$this->db->truncate("cji_familiacompania");
		$this->db->truncate("cji_marca");
		$this->db->truncate("cji_proveedormarca");

		$this->db->truncate("cji_receta");
		$this->db->truncate("cji_recetadetalle");
	}

	public function truncate_usuarios( $all = false){
		
        # BORRAMOS A LOS USUARIOS QUE NO SON ADMINISTRADOR Y CCAPA
		$sql = "DELETE FROM cji_usuario WHERE USUA_Codigo NOT IN (1,2)";
		$this->db->query($sql);

        # INICIAMOS EL INDICE "AUTOINCREMENT" DE LA TABLA EN 3
		$sql = "ALTER TABLE cji_usuario AUTO_INCREMENT = 3";
		$this->db->query($sql);

		if ($all == false){
            # BORRAMOS A LOS USUARIOS QUE NO SON ADMINISTRADOR Y CCAPA
			$sql = "DELETE FROM cji_usuario_compania WHERE USUA_Codigo NOT IN (1,2)";
			$this->db->query($sql);

            # OBTENEMOS EL ULTIMO ID INGRESADO EN LA TABLA
			$sql = "SELECT MAX(USUCOMP_Codigo) as id FROM cji_usuario_compania";
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0){
				foreach ($query->result() as $val) {
					$id = $val->id + 1;
				}

                # INICIAMOS EL INDICE "AUTOINCREMENT" DE LA TABLA EN $id
				$sql = "ALTER TABLE cji_usuario_compania AUTO_INCREMENT = $id";
				$this->db->query($sql);
			}
		}
		else
			$this->db->truncate("cji_usuario_compania");
	}

	public function truncate_personal(){
		
        # BORRAMOS AL PERSONAL QUE NO SON ADMINISTRADOR Y CCAPA
		$sql = "DELETE FROM cji_persona WHERE EXISTS( SELECT d.PERSP_Codigo FROM cji_directivo d WHERE d.PERSP_Codigo = cji_persona.PERSP_Codigo) AND PERSP_Codigo NOT IN (1,2)";
		$this->db->query($sql);
		
        # BORRAMOS A LOS DIRECTIVOS QUE NO SON ADMINISTRADOR
		$sql = "DELETE FROM cji_directivo WHERE DIREP_Codigo <> 1";
		$this->db->query($sql);

        # INICIAMOS EL INDICE "AUTOINCREMENT" DE LA TABLA EN 2
		$sql = "ALTER TABLE cji_directivo AUTO_INCREMENT = 2";
		$this->db->query($sql);

        # INICIAMOS EL INDICE "AUTOINCREMENT" DE LA TABLA EN 3
        # $sql = "ALTER TABLE cji_persona AUTO_INCREMENT = 3";
        # $this->db->query($sql);
	}

	public function truncate_clientes_proveedores(){

        # BORRAMOS LAS PERSONAS QUE SON CLIENTES
		$sql = "DELETE FROM cji_persona WHERE EXISTS( SELECT c.PERSP_Codigo FROM cji_cliente c WHERE c.PERSP_Codigo = cji_persona.PERSP_Codigo) AND PERSP_Codigo NOT IN (1,2)";
		$this->db->query($sql);
		
        # BORRAMOS LAS PERSONAS QUE SON PROVEEDORES
		$sql = "DELETE FROM cji_persona WHERE EXISTS( SELECT pr.PERSP_Codigo FROM cji_proveedor pr WHERE pr.PERSP_Codigo = cji_persona.PERSP_Codigo) AND PERSP_Codigo NOT IN (1,2)";
		$this->db->query($sql);

        # BORRAMOS LAS TABLAS DE CLIENTES 
		$this->db->truncate("cji_cliente");
		$this->db->truncate("cji_clientecompania");
		$this->db->truncate("cji_proveedorcompania");
		$this->db->truncate("cji_emprcontacto");

        # BORRAMOS TODAS LAS EMPRESAS MENOS LAS QUE TIENEN COMPAÑiA
		$sql = "DELETE FROM cji_empresa WHERE NOT EXISTS (SELECT c.EMPRP_Codigo FROM cji_compania c WHERE c.EMPRP_Codigo = cji_empresa.EMPRP_Codigo)";
		$this->db->query($sql);

        # OBTENGO EL ID DEL ULTIMO REGISTRO + 1 PARA AJUSTAR EL AUTOINCREMENT DE LA TABLA
		$sql = "SELECT (MAX(EMPRP_Codigo) + 1) as id FROM cji_empresa";
		$query = $this->db->query($sql);

		if ( $query->num_rows() > 0 ){
			foreach ($query->result() as $key => $value) {
				$id = $value->id;
			}
		}
		else
			$id = 1;

            # INICIAMOS EL INDICE "AUTOINCREMENT" DE LA TABLA EN EL ULTIMO ID REGISTRADO + 1
		if ($id != NULL && $id != ""){
			$sql = "ALTER TABLE cji_empresa AUTO_INCREMENT = $id";
			$this->db->query($sql);
		}

		
		$id = NULL;

        # BORRAMOS TODOS LOS ESTABLECIMIENTOS MENOS LOS QUE TIENEN REGISTRO EN LA TABLA COMPAÑIA
		$sql = "DELETE FROM cji_emprestablecimiento WHERE NOT EXISTS (SELECT c.EESTABP_Codigo FROM cji_compania c WHERE c.EESTABP_Codigo = cji_emprestablecimiento.EESTABP_Codigo)";
		$this->db->query($sql);

        # OBTENGO EL ID DEL ULTIMO REGISTRO + 1 PARA AJUSTAR EL AUTOINCREMENT DE LA TABLA
		$sql = "SELECT (MAX(EESTABP_Codigo) + 1) as id FROM cji_emprestablecimiento";
		$query = $this->db->query($sql);

		if ( $query->num_rows() > 0 ){
			foreach ($query->result() as $key => $value) {
				$id = $value->id;
			}
		}
		else
			$id = 1;

            # INICIAMOS EL INDICE "AUTOINCREMENT" DE LA TABLA EN EL ULTIMO ID REGISTRADO + 1
		if ($id != NULL && $id != ""){
			$sql = "ALTER TABLE cji_emprestablecimiento AUTO_INCREMENT = $id";
			$this->db->query($sql);
		}

		$id = NULL;

        # BORRAMOS A LOS PROVEEDORES QUE NO TENGAN REGISTRO EN LA TABLA EMPRESA
		$sql = "DELETE FROM cji_proveedor WHERE NOT EXISTS (SELECT e.EMPRP_Codigo FROM cji_empresa e WHERE e.EMPRP_Codigo = cji_proveedor.EMPRP_Codigo)";
		$this->db->query($sql);

        # OBTENGO EL ID DEL ULTIMO REGISTRO + 1 PARA AJUSTAR EL AUTOINCREMENT DE LA TABLA
		$sql = "SELECT (MAX(PROVP_Codigo) + 1) as id FROM cji_proveedor";
		$query = $this->db->query($sql);

		if ( $query->num_rows() > 0 ){
			foreach ($query->result() as $key => $value) {
				$id = $value->id;
			}
		}
		else
			$id = 1;

            # INICIAMOS EL INDICE "AUTOINCREMENT" DE LA TABLA EN EL ULTIMO ID REGISTRADO + 1
		if ($id != NULL && $id != ""){
			$sql = "ALTER TABLE cji_proveedor AUTO_INCREMENT = $id";
			$this->db->query($sql);
		}
	}

	public function truncate_empresas(){

		$this->truncate_clientes_proveedores();

        # CLIENTES
		$this->db->truncate("cji_cliente");
		$this->db->truncate("cji_clientecompania");
		
        # PROVEEDORES
		$this->db->truncate("cji_proveedor");
		$this->db->truncate("cji_proveedorcompania");
		
        # EMPRESAS
		$this->db->truncate("cji_empresa");
		$this->db->truncate("cji_emprestablecimiento");

        # COMPAÑIAS
		$this->db->truncate("cji_compania");
		$this->db->truncate("cji_companiaconfidocumento");
		$this->db->truncate("cji_companiaconfiguracion");
		$this->db->truncate("cji_compadocumenitem");
		$this->db->truncate("cji_configuracion");
	}

	public function truncate_all(){
		$this->truncate_comprobantes();
		$this->truncate_docs();
		$this->truncate_inventarios();
		$this->truncate_stock();
		$this->truncate_productos();
        $this->truncate_usuarios( true ); # true VACIAS LA TABLA usuario_compania
        $this->truncate_personal(); # INCLUYE DIRECTIVOS
        $this->truncate_empresas(); # INCLUYE CLIENTES Y PROVEEDORES
      }
    }
    ?>