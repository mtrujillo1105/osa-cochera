<?php
/* *********************************************************************************
Autor: Martín Trujillo
Fecha: 09/12/2020
/* ******************************************************************************** */
class RptAlmacen extends CI_Controller
{
    private $compania;
    private $url;
    private $view_js = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('maestros/almacen_model');
        $this->load->model('almacen/almacenproductoserie_model');
        $this->load->model('almacen/almacenproducto_model');
        $this->load->model('almacen/familia_model');
        $this->load->model('almacen/producto_model');
        $this->load->model('maestros/unidadmedida_model');
        $this->load->model('maestros/fabricante_model');
        $this->load->model('maestros/marca_model');
        $this->load->library('pdfgenerator');
        $this->load->library('excelspout');
        $this->compania = $this->session->userdata('compania');
        $this->url = base_url();
        $this->view_js = array(0 => "reportes/rptalmacen.js");		
    }

    public function index(){
        /** Reportes disponibles */
        $filter = new stdClass();
        $filter->modulo  = 9;
        $filter->menuurl = 'reportes/almacen/';
        $data['modulos'] = $this->menu_model->getMenus($filter);	
        /** Report Stock de productos */
        $filter = new stdClass();
        $filter->order = 'FAMI_Descripcion';
        $filter->dir = 'ASC';
        $filter->flagBS = 'B';
        $data['familias'] = $this->familia_model->getFamilias($filter);
        $data['modelos'] = $this->producto_model->getModelos();		
        /** Report Stock almacen productos */
        $filter = new stdClass();
        $filter->dir = "ASC";
        $filter->order = "ALMAC_Descripcion";
        $data['almacenes'] = $this->almacen_model->getAlmacens($filter);
        $data['scripts'] = $this->view_js;		
        $this->layout->view('reportes/rptalmacen_index', $data);
    }

    public function dtStockProducto($onlyJSON = true){
        $posDT = -1;
        $columnas = array(
                ++$posDT => "PROD_CodigoUsuario",
                ++$posDT => "PROD_Nombre",
                ++$posDT => "FAMI_Descripcion",
                ++$posDT => "MARCC_Descripcion",
                ++$posDT => "PROD_Modelo",
                ++$posDT => "UNDMED_Simbolo",
                ++$posDT => "ALMPROD_Stock"
        );
        $filter = new stdClass();
        if($onlyJSON){
                $filter->start = $this->input->post("start");
                $filter->length = $this->input->post("length");
                $filter->search = $this->input->post("search")["value"];
                $ordenar = $this->input->post("order")[0]["column"];
                if ($ordenar != "") {
                        $filter->order = $columnas[$ordenar];
                        $filter->dir = $this->input->post("order")[0]["dir"];
                }
                $item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
        }
        $filter->searchAlmacen = trim($this->input->post("txtAlmacen"));
        $filter->searchCodigoUsuario = trim($this->input->post("txtCodigo"));
        $filter->searchProducto = trim($this->input->post("txtNombre"));
        $filter->searchFamilia = $this->input->post("txtFamilia");
        $filter->searchModelo = trim($this->input->post("txtModelo"));
        $filter->searchMarca = trim($this->input->post("txtMarca"));
        $filter->searchFlagBS = 'B';
        $stockInfo = $this->almacenproducto_model->getStockAlmacen($filter);
        $records = array();
        if ($stockInfo["records"] != NULL) {
                foreach ($stockInfo["records"] as $i => $valor) {
                        $posDT = -1;
                        $records[] = array(
                                ++$posDT => $valor->PROD_CodigoUsuario,
                                ++$posDT => $valor->PROD_Nombre,
                                ++$posDT => $valor->FAMI_Descripcion,
                                ++$posDT => $valor->MARCC_Descripcion,
                                ++$posDT => $valor->PROD_Modelo,
                                ++$posDT => $valor->UNDMED_Simbolo,
                                ++$posDT => $valor->ALMPROD_Stock
                        );
                }
        }
        $recordsTotal = ($stockInfo["recordsTotal"] != NULL) ? $stockInfo["recordsTotal"] : 0;
        $recordsFilter = $stockInfo["recordsFilter"];	
        $json = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFilter,
                "data"            => $records
        );
        if($onlyJSON){
                echo json_encode($json);
                die();
        }
        else{
                return $records;
        }
    }	

	public function dtStockAlmacen($onlyJSON = true){
		$posDT = -1;
		$columnas = array(
			++$posDT => "ALMAC_Descripcion",
			++$posDT => "PROD_CodigoUsuario",
			++$posDT => "PROD_Nombre",
			++$posDT => "FAMI_Descripcion",
			++$posDT => "MARCC_Descripcion",
			++$posDT => "PROD_Modelo",
			++$posDT => "UNDMED_Simbolo",
			++$posDT => "ALMPROD_Stock"
		);
		$filter = new stdClass();
		if($onlyJSON){
			$filter->start = $this->input->post("start");
			$filter->length = $this->input->post("length");
			$filter->search = $this->input->post("search")["value"];
			$ordenar = $this->input->post("order")[0]["column"];
			if ($ordenar != "") {
				$filter->order = $columnas[$ordenar];
				$filter->dir = $this->input->post("order")[0]["dir"];
			}
			$item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;
		}
		$filter->searchAlmacen = $this->input->post("txtAlmacenAP");
		$filter->searchCodigoUsuario = trim($this->input->post("txtCodigoAP"));
		$filter->searchProducto = trim($this->input->post("txtNombreAP"));
		$filter->searchFamilia = $this->input->post("txtFamiliaAP");
		$filter->searchModelo = trim($this->input->post("txtModeloAP"));
		$filter->searchMarca = trim($this->input->post("txtMarcaAP"));
		$filter->searchFlagBS = 'B';
		$stockInfo = $this->almacenproducto_model->getStockAlmacen($filter);
		$records = array();
		if ($stockInfo["records"] != NULL) {
			foreach ($stockInfo["records"] as $i => $valor) {
				$posDT = -1;
				$records[] = array(
					++$posDT => $valor->ALMAC_Descripcion,
					++$posDT => $valor->PROD_CodigoUsuario,
					++$posDT => $valor->PROD_Nombre,
					++$posDT => $valor->FAMI_Descripcion,
					++$posDT => $valor->MARCC_Descripcion,
					++$posDT => $valor->PROD_Modelo,
					++$posDT => $valor->UNDMED_Simbolo,
					++$posDT => $valor->ALMPROD_Stock
				);
			}
		}
		$recordsTotal = ($stockInfo["recordsTotal"] != NULL) ? $stockInfo["recordsTotal"] : 0;
		$recordsFilter = $stockInfo["recordsFilter"];
		$json = array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFilter,
			"data"            => $records
		);
		if($onlyJSON){
			echo json_encode($json);
			die();
		}
		else{
			return $records;
		}
	}

	/** Imprimimos en PDF*/ 
	public function imprimirP(){
		$filter = new stdClass();
		$filter->searchAlmacen = $this->input->post("txtAlmacen");
		$filter->searchCodigoUsuario = trim($this->input->post("txtCodigo"));
		$filter->searchProducto = trim($this->input->post("txtNombre"));
		$filter->searchFamilia = $this->input->post("txtFamilia");
		$filter->searchModelo = trim($this->input->post("txtModelo"));
		$filter->searchMarca = trim($this->input->post("txtMarca"));
		$filter->searchFlagBS = 'B';
		$data = $this->almacenproducto_model->getStockAlmacen($filter);
		$html = $this->layout->view('reportes/stockproducto_pdf',$data,true);
		$this->pdfgenerator->generate($html,'stockproductos');
	}

	/** Imprimimos en Excel */
	public function imprimirE($reporte=null){
		if(!is_null($reporte)){
			switch($reporte){
				case 'rpt_StockProductos':
					$fileName        = "StockProductos";
					$data["header"]  = ['CODIGO','NOMBRE','FAMILIA','MARCA','MODELO','UNIDAD','STOCK'];
					$data["records"] = $this->dtStockProducto(false);	
					break;
				case 'rpt_StockAlmacenes':
					$fileName        = "StockAlmacenes";
					$data["header"]  = ['ALMACEN','CODIGO','NOMBRE','FAMILIA','MARCA','MODELO','UNIDAD','STOCK'];
					$data["records"] = $this->dtStockAlmacen(false);	
 					break;
			}
			$this->excelspout->generate($data,$fileName);
		}
	}	

}
?>