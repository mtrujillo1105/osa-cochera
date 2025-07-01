<?php
/* *********************************************************************************
Autor: Unknow
Fecha: Unknow

/* ******************************************************************************** */
class Almacenproducto extends CI_Controller
{
	##  -> Begin
	private $compania;
	private $url;
	private $view_js = NULL;
	##  -> End

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

		$this->compania = $this->session->userdata('compania');
		$this->url = base_url();
		$this->view_js = array(0 => "almacen/almacenproducto.js");
	}

	##  -> Begin
	public function index()
	{
		$this->listar();
	}
	##  -> End

	##  -> Begin
	public function listar()
	{
		$filter = new stdClass();
		$filter->order = 'FAMI_Descripcion';
		$filter->dir = 'ASC';
		$filter->flagBS = 'B';
		$data['familias'] = $this->familia_model->getFamilias($filter);
		$data['modelos'] = $this->producto_model->getModelos();

		$filterOrden = new stdClass();
		$filterOrden->dir = "ASC";

		$filterOrden->order = "ALMAC_Descripcion";
		$data['almacenes'] = $this->almacen_model->getAlmacens($filterOrden);

		$filterOrden->order = "FABRIC_Descripcion";
		$data['fabricantes'] = $this->fabricante_model->getFabricantes($filterOrden);

		$filterOrden->order = "MARCC_Descripcion";
		$data['marcas'] = $this->marca_model->getMarcas($filterOrden);

		$filterOrden->order = "UNDMED_Descripcion";
		$data['unidades'] = $this->unidadmedida_model->getUmedidas($filterOrden);
		$data['scripts'] = $this->view_js;
		$this->layout->view('almacen/almacenproducto_index', $data);
	}
	##  -> End

	##  -> Begin
	public function dtStockAlmacen()
	{
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
		$filter->start = $this->input->post("start");
		$filter->length = $this->input->post("length");
		$filter->search = $this->input->post("search")["value"];

		$ordenar = $this->input->post("order")[0]["column"];
		if ($ordenar != "") {
			$filter->order = $columnas[$ordenar];
			$filter->dir = $this->input->post("order")[0]["dir"];
		}

		$item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;

		$filter->searchAlmacen = $this->input->post("txtAlmacen");
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

		die(json_encode($json));
	}
	##  -> End

	public function listar_general($j = '0')
	{
		$data['codigo'] = "";
		$data['nombre'] = "";
		$data['familia'] = "";
		$data['marca'] = "";
		$data['registros'] = count($this->producto_model->listar_productos_general('B'));
		$data['action'] = base_url() . "index.php/almacen/almacenproducto/buscar_general";
		$data['action2'] = base_url() . "index.php/almacen/kardex/listar";
		$conf['base_url'] = site_url('almacen/almacenproducto/listar_general');
		$conf['per_page'] = 50;
		$conf['num_links'] = 10;
		$conf['first_link'] = "&lt;&lt;";
		$conf['last_link'] = "&gt;&gt;";
		$conf['total_rows'] = $data['registros'];
		$conf['uri_segment'] = 4;
		$offset = (int) $this->uri->segment(4);
		$lista_producto = $this->producto_model->listar_productos_general('B', $conf['per_page'], $offset);
		$lista_establec = $this->emprestablecimiento_model->listar($this->session->userdata('empresa'));
		$item = $j + 1;
		$lista = array();
		$montoTotal = 0;
                             
		if (count($lista_producto) > 0) {
			foreach ($lista_producto as $producto) {
				$stock = array();
				$total = 0;
				$precios = array();
				foreach ($lista_establec as $establec) {
					$lista_almacen = $this->almacen_model->buscar_x_establec($establec->EESTABP_Codigo);
                                 
					$cantidad = 0;
					foreach ($lista_almacen as $almacen) {
                                            
						$cantidad += $this->producto_model->obtener_stock($producto->PROD_Codigo, '', $almacen->ALMAP_Codigo);
         
						foreach ($this->producto_model->obtener_precios_promedios($producto->PROD_Codigo, '', $almacen->ALMAP_Codigo) as $precio) {
							$precios[] = $precio;
						}
					}
					$total += $cantidad;
					$stock[] = $cantidad;

					$precioProm = count($precios) > 0 ? $precios[0] : 0;
					$montoProm = $precioProm * $total;
				}
				$stock[] = $total;
				$lista[] = array($item++, $producto->PROD_Codigo, $producto->PROD_GenericoIndividual, $producto->PROD_CodigoUsuario, $producto->PROD_Nombre, $stock, $precioProm, $montoProm);

				$montoTotal += $montoProm;
			}
		}
		$data['lista_establec'] = $lista_establec;
		$data['lista'] = $lista;
		$data['montoTotal'] = $montoTotal;

		$data['totalesCat'] = $this->almacenproducto_model->obtenerSumaStock();

		$data['totalesFami'] = $this->almacenproducto_model->obtenerSumaStockFamilia();

		$data['titulo_tabla'] = "STOCK DE GENERAL DE PRODUCTOS";
		$data['oculto'] = form_hidden(array('accion' => "", 'codigo' => "", 'modo' => "insertar", 'base_url' => base_url()));
		$this->pagination->initialize($conf);
		$data['paginacion'] = $this->pagination->create_links();
		$this->layout->view('almacen/almacenproducto_general', $data);
	}

	public function datatable_almacen_producto_general()
	{

		$columnas = array(
			0 => "PROD_CodigoUsuario",
			1 => "PROD_Nombre"
		);

		$filter = new stdClass();
		$filter->start = $this->input->post("start");
		$filter->length = $this->input->post("length");
		$filter->search = $this->input->post("search")["value"];

		$ordenar = $this->input->post("order")[0]["column"];
		if ($ordenar != "") {
			$filter->order = $columnas[$ordenar];
			$filter->dir = $this->input->post("order")[0]["dir"];
		}

		$item = ($this->input->post("start") != "") ? $this->input->post("start") : 0;

		$filter->searchCodigo = $this->input->post("txtCodigo");
		$filter->searchNombre = $this->input->post("txtNombre");
		$filter->searchModelo = $this->input->post("txtModelo");
		$filter->searchFlagBS = "B";

		$listado = $this->almacenproducto_model->listar_general($filter);
		$lista_establec = $this->emprestablecimiento_model->listar($this->session->userdata('empresa'));

		$lista = array();
		$item++;

		if (count($listado) > 0) {
			foreach ($listado as $indice => $valor) {
				$total = 0;
				$lista[$indice] = array(
					0 => $valor->PROD_CodigoUsuario,
					1 => $valor->PROD_Nombre
				);
				$j = 2;
				foreach ($lista_establec as $establec) {
					$lista_almacen = $this->almacen_model->buscar_x_establec($establec->EESTABP_Codigo);
					$cantidad = 0;
					foreach ($lista_almacen as $almacen) {
						$cantidad += $this->producto_model->obtener_stock($valor->PROD_Codigo, '', $almacen->ALMAP_Codigo);
					}

					$lista[$indice][$j] = $cantidad;
					$total += $cantidad;
					$j++;
				}
				$lista[$indice][$j] = $total;
			}
		}

		unset($filter->start);
		unset($filter->length);

		$json = array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => intval(count($this->almacenproducto_model->listar_general())),
			"recordsFiltered" => intval(count($this->almacenproducto_model->listar_general($filter))),
			"data"            => $lista
		);

		echo json_encode($json);
	}

	public function buscar_general($j = '0')
	{
		$codigo = $this->input->post('txtCodigo');
		$nombre = $this->input->post('txtNombre');
		$familia = $this->input->post('txtFamilia');
		$marca = $this->input->post('txtMarca');

		if (count($_POST) > 0) {
			$this->session->set_userdata(array('codigo' => $codigo, 'nombre' => $nombre, 'familia' => $familia, 'marca' => $marca));
		} else {
			$codigo = $this->session->userdata('codigo');
			$nombre = $this->session->userdata('nombre');
			$familia = $this->session->userdata('famlia');
			$marca = $this->session->userdata('marca');
		}

		$filter = new stdClass();
		$filter->flagBS = 'B';
		$filter->codigo = $codigo;
		$filter->nombre = $nombre;
		$filter->familia = $familia;
		$filter->marca = $marca;

		$data['codigo'] = $codigo;
		$data['nombre'] = $nombre;
		$data['familia'] = $familia;
		$data['marca'] = $marca;


		$data['registros'] = count($this->producto_model->buscar_productos_general($filter));
		$data['action'] = base_url() . "index.php/almacen/almacenproducto/buscar_general";
		$data['action2'] = base_url() . "index.php/almacen/kardex/listar";
		$conf['base_url'] = site_url('almacen/almacenproducto/buscar_general');
		$conf['per_page'] = 50;
		$conf['num_links'] = 10;
		$conf['first_link'] = "&lt;&lt;";
		$conf['last_link'] = "&gt;&gt;";
		$conf['total_rows'] = $data['registros'];
		$conf['uri_segment'] = 4;
		$offset = (int) $this->uri->segment(4);


		$lista_producto = $this->producto_model->buscar_productos_general($filter, $conf['per_page'], $offset);
		$lista_establec = $this->emprestablecimiento_model->listar($this->session->userdata('empresa'));
		$item = $j + 1;
		$lista = array();
		$montoTotal = 0;
		if (count($lista_producto) > 0) {
			foreach ($lista_producto as $producto) {
				$stock = array();
				$precios = array();
				$total = 0;
				foreach ($lista_establec as $establec) {
					$lista_almacen = $this->almacen_model->buscar_x_establec($establec->EESTABP_Codigo);
					$cantidad = 0;
					foreach ($lista_almacen as $almacen) {
						$cantidad += $this->producto_model->obtener_stock($producto->PROD_Codigo, '', $almacen->ALMAP_Codigo);
						foreach ($this->producto_model->obtener_precios_promedios($producto->PROD_Codigo, '', $almacen->ALMAP_Codigo) as $precio) {
							$precios[] = $precio;
						}
					}
					$total += $cantidad;
					$stock[] = $cantidad;
				}
				$precioProm = count($precios) > 0 ? $precios[0] : 0;
				$totalProm = $precioProm * $total;
				$stock[] = $total;
				$lista[] = array($item++, $producto->PROD_Codigo, $producto->PROD_GenericoIndividual, $producto->PROD_CodigoUsuario, $producto->PROD_Nombre, $stock, $precioProm, $totalProm);

				$montoTotal += $totalProm;
			}
		}
		$data['lista_establec'] = $lista_establec;
		$data['lista'] = $lista;
		$data['montoTotal'] = $montoTotal;
		$data['titulo_tabla'] = "STOCK DE GENERAL DE PRODUCTOS";
		$data['oculto'] = form_hidden(array('accion' => "", 'codigo' => "", 'modo' => "insertar", 'base_url' => base_url()));
		$this->pagination->initialize($conf);
		$data['paginacion'] = $this->pagination->create_links();
		$this->layout->view('almacen/almacenproducto_general', $data);
	}

	public function ver($codigo)
	{

		$datos_almacen = $this->almacen_model->obtener($codigo);
		$nombre_almacen = $datos_almacen[0]->ALMAC_Descripcion;
		$tipo_almacen = $datos_almacen[0]->TIPALM_Codigo;
		$datos_tipoalmacen = $this->tipoalmacen_model->obtener($tipo_almacen);
		$nombre_tipoalmacen = $datos_tipoalmacen[0]->TIPALM_Descripcion;
		$data['nombre_almacen'] = $nombre_almacen;
		$data['nombre_tipoalmacen'] = $nombre_tipoalmacen;
		$data['titulo'] = "VER ALMACEN";
		$data['oculto'] = form_hidden(array('base_url' => base_url()));
		$this->layout->view('almacen/almacen_ver', $data);
	}

	public function buscar($j = 0)
	{

		$nombre_almacen = $this->input->post('nombre_almacen');
		$tipo_almacen = $this->input->post('tipo_almacen');
		$filter = new stdClass();
		$filter->ALMAC_Descripcion = $nombre_almacen;
		$filter->TIPALM_Codigo = $tipo_almacen;
		$data['registros'] = count($this->almacen_model->buscar($filter));
		$conf['base_url'] = site_url('almacen/almacen/buscar/');
		$conf['per_page'] = 10;
		$conf['num_links'] = 3;
		$conf['first_link'] = "&lt;&lt;";
		$conf['last_link'] = "&gt;&gt;";
		$conf['total_rows'] = $data['registros'];
		$offset = (int) $this->uri->segment(4);
		$listado = $this->almacen_model->buscar($filter, $conf['per_page'], $offset);
		$item = $j + 1;
		$lista = array();
		if (count($listado) > 0) {
			foreach ($listado as $indice => $valor) {
				$codigo = $valor->ALMAP_Codigo;
				$editar = "<a href='#' onclick='editar_almacen(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/modificar.png' width='16' height='16' border='0' title='Modificar'></a>";
				$ver = "<a href='#' onclick='ver_almacen(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/ver.png' width='16' height='16' border='0' title='Modificar'></a>";
				$eliminar = "<a href='#' onclick='eliminar_almacen(" . $codigo . ")' target='_parent'><img src='" . base_url() . "images/eliminar.png' width='16' height='16' border='0' title='Modificar'></a>";
				$lista[] = array($item++, $valor->ALMAC_Descripcion, $valor->TIPALM_Descripcion, $editar, $ver, $eliminar);
			}
		}
		$data['titulo_tabla'] = "RESULTADO DE BUSQUEDA de ALMACENES";
		$data['titulo_busqueda'] = "BUSCAR ALMACEN";
		$data['nombre_almacen'] = form_input(array('name' => 'nombre_almacen', 'id' => 'nombre_almacen', 'value' => $nombre_almacen, 'maxlength' => '100', 'class' => 'cajaMedia'));
		$data['tipo_almacen'] = form_dropdown('tipo_almacen', $this->tipoalmacen_model->seleccionar(), $tipo_almacen, "id='tipo_almacen' class='comboMedio'");
		$data['form_open'] = form_open(base_url() . 'index.php/almacen/almacen/buscar', array("name" => "form_busquedaAlmacen", "id" => "form_busquedaAlmacen"));
		$data['form_close'] = form_close();
		$data['lista'] = $lista;
		$data['modelos'] = $this->producto_model->getModelos();
		$data['oculto'] = form_hidden(array('base_url' => base_url()));
		$this->pagination->initialize($conf);
		$data['paginacion'] = $this->pagination->create_links();
		$this->layout->view('almacen/almacen_index', $data);
	}

	public function verReporteExcel($modelo = "")
	{
		$this->load->library('Excel');
		$listadoAlmacen = $this->almacenproducto_model->listar("", $modelo);

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Reporte');

		###########################################
		######### ESTILOS
		###########################################
		$estiloTitulo = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 11
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasTitulo = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 11
			),
			'fill'  => array(
				'type'      => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'ECF0F1')
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasPar = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => false,
				'color'     => array(
					'rgb' => '000000'
				)
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasImpar = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => false,
				'color'     => array(
					'rgb' => '000000'
				)
			),
			'fill'  => array(
				'type'      => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'DCDCDCDC')
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
		$this->excel->getActiveSheet()->getStyle("A1:I2")->applyFromArray($estiloTitulo);
		$this->excel->getActiveSheet()->getStyle("A3:I3")->applyFromArray($estiloColumnasTitulo);
		$this->excel->setActiveSheetIndex(0)->mergeCells('A1:I2')->setCellValue('A1', $_SESSION['nombre_empresa']);

		$lugar = 3;
		$this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", 'N');
		$this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", 'Almacen');
		$this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", 'Codigo');
		$this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", 'Descripcion');
		$this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", 'Familia');
		$this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", 'Modelo');
		$this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", 'Marca');
		$this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", 'Stock General');
		$this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", 'Stock Minimo');

		$numeroS = 0;
		$lugar = 4;

		$producto = "";

		foreach ($listadoAlmacen as $indice => $valor) {
			$numeroS += 1;
			$Almacen = $valor->ALMAC_Descripcion;
			$this->excel->setActiveSheetIndex(0)
				->setCellValue("A$lugar", $numeroS)
				->setCellValue("B$lugar", $valor->ALMAC_Descripcion)
				->setCellValue("C$lugar", $valor->PROD_CodigoUsuario)
				->setCellValue("D$lugar", $valor->PROD_Nombre)
				->setCellValue("E$lugar", $valor->FAMI_Descripcion)
				->setCellValue("F$lugar", $valor->PROD_Modelo)
				->setCellValue("G$lugar", $valor->MARCC_Descripcion)
				->setCellValue("H$lugar", $valor->ALMPROD_Stock)
				->setCellValue("I$lugar", $valor->PROD_StockMinimo);

			if ($indice > 1 && $producto == $valor->PROD_Codigo)
				$this->excel->getActiveSheet()->getStyle("A$lugar:I$lugar")->applyFromArray($estiloColumnasPar);
			else
				$this->excel->getActiveSheet()->getStyle("A$lugar:I$lugar")->applyFromArray($estiloColumnasImpar);

			$lugar += 1;
			$producto = $valor->PROD_Codigo;
		}

		for ($i = 'A'; $i <= 'I'; $i++) {
			$this->excel->setActiveSheetIndex(0)
				->getColumnDimension($i)->setAutoSize(true);
		}

		$filename = "Stock almacen general " . date('Y-m-d') . ".xls"; //save our workbook as this file name

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=$filename");
		header("Cache-Control: max-age=0"); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		// Forzamos a la descarga
		$objWriter->save('php://output');
	}

	public function verCotizados($inicio = 0, $fin = 0)
	{
		$this->load->library('Excel');
		$listadoAlmacen = $this->almacenproducto_model->listarCotizados("", $inicio, $fin);

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Reporte');

		###########################################
		######### ESTILOS
		###########################################
		$estiloTitulo = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 11
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasTitulo = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 9
			),
			'fill'  => array(
				'type'      => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'ECF0F1')
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasPar = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => false,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 9
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasImpar = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => false,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 9
			),
			'fill'  => array(
				'type'      => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'DCDCDCDC')
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('25');
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth('10');
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth('11');
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth('10');
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth('11');
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth('10');
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth('10');

		$rango = ($inicio > 0 && $fin > 0) ? " COTIZADO DESDE EL NÚMERO $inicio HASTA $fin." : "";
		$this->excel->getActiveSheet()->getStyle("A1:G3")->applyFromArray($estiloTitulo);
		$this->excel->setActiveSheetIndex(0)->mergeCells('A1:G2')->setCellValue('A1', $_SESSION['nombre_empresa']);
		$this->excel->setActiveSheetIndex(0)->mergeCells('A3:G3')->setCellValue('A3', "STOCK ACTUAL DE ARTICULOS Y CANTIDAD COTIZADA. $rango EMITIDO EL : " . date("d / m / Y") . " a las: " . date("h:i:s"));

		$lugar = 4;
		$this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasTitulo);
		$this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", 'Descripcion');
		$this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", 'Marca');
		$this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", 'Stock Minimo');
		$this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", 'Stock Actual');
		$this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", 'Stock Cotizado');
		$this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", 'Precio Costo');
		$this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", 'Precio Venta');

		$numeroS = 0;
		$lugar++;

		$producto = "";

		$tcosto = 0;
		$tpv = 0;

		foreach ($listadoAlmacen as $indice => $valor) {
			$numeroS += 1;
			$Almacen = $valor->ALMAC_Descripcion;
			$this->excel->setActiveSheetIndex(0)
				->setCellValue("A$lugar", $valor->PROD_Nombre)
				->setCellValue("B$lugar", $valor->MARCC_CodigoUsuario)
				->setCellValue("C$lugar", $valor->PROD_StockMinimo)
				->setCellValue("D$lugar", $valor->ALMPROD_Stock)
				->setCellValue("E$lugar", $valor->cantidad)
				->setCellValue("F$lugar", number_format($valor->costo, 2))
				->setCellValue("G$lugar", number_format($valor->pv, 2));

			if ($indice > 1 && $producto == $valor->PROD_Codigo)
				$this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasPar);
			else
				$this->excel->getActiveSheet()->getStyle("A$lugar:G$lugar")->applyFromArray($estiloColumnasImpar);

			$lugar += 1;
			$producto = $valor->PROD_Codigo;

			$tcosto += $valor->costo;
			$tpv += $valor->pv;
		}

		$this->excel->setActiveSheetIndex(0)
			->setCellValue("E$lugar", "TOTAL")
			->setCellValue("F$lugar", number_format($tcosto, 2))
			->setCellValue("G$lugar",  number_format($tpv, 2));

		#for($i = 'C'; $i <= 'G'; $i++){
		#    $this->excel->setActiveSheetIndex(0)            
		#        ->getColumnDimension($i)->setAutoSize(true);
		#}

		$filename = "Stock general y cantidad cotizada " . date('Y-m-d') . ".xls"; //save our workbook as this file name

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=$filename");
		header("Cache-Control: max-age=0"); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		// Forzamos a la descarga
		$objWriter->save('php://output');
	}

	public function verReporteExcelDetalle()
	{
		$this->load->library('Excel');
		$listadoAlmacen = $this->almacenproducto_model->detalles();

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Reporte');

		###########################################
		######### ESTILOS
		###########################################
		$estiloTitulo = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 11
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasTitulo = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => true,
				'color'     => array(
					'rgb' => '000000'
				),
				'size' => 11
			),
			'fill'  => array(
				'type'      => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'ECF0F1')
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasPar = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => false,
				'color'     => array(
					'rgb' => '000000'
				)
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$estiloColumnasImpar = array(
			'font' => array(
				'name'      => 'Calibri',
				'bold'      => false,
				'color'     => array(
					'rgb' => '000000'
				)
			),
			'fill'  => array(
				'type'      => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('argb' => 'DCDCDCDC')
			),
			'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'          => TRUE
			)
		);

		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth('5');
		$this->excel->getActiveSheet()->getStyle("A1:K2")->applyFromArray($estiloTitulo);
		$this->excel->getActiveSheet()->getStyle("A3:K3")->applyFromArray($estiloColumnasTitulo);
		$this->excel->setActiveSheetIndex(0)->mergeCells('A1:K2')->setCellValue('A1', $_SESSION['nombre_empresa']);

		$lugar = 3;
		$this->excel->setActiveSheetIndex(0)->setCellValue("A$lugar", 'N');
		$this->excel->setActiveSheetIndex(0)->setCellValue("B$lugar", 'Almacen');
		$this->excel->setActiveSheetIndex(0)->setCellValue("C$lugar", 'Codigo');
		$this->excel->setActiveSheetIndex(0)->setCellValue("D$lugar", 'Descripcion');
		$this->excel->setActiveSheetIndex(0)->setCellValue("E$lugar", 'Familia');
		$this->excel->setActiveSheetIndex(0)->setCellValue("F$lugar", 'Modelo');
		$this->excel->setActiveSheetIndex(0)->setCellValue("G$lugar", 'Marca');
		$this->excel->setActiveSheetIndex(0)->setCellValue("H$lugar", 'Numero de lote');
		$this->excel->setActiveSheetIndex(0)->setCellValue("I$lugar", 'Vencimiento de lote');
		$this->excel->setActiveSheetIndex(0)->setCellValue("J$lugar", 'Precio Costo');
		$this->excel->setActiveSheetIndex(0)->setCellValue("K$lugar", 'Stock Lote');

		$numeroS = 0;
		$lugar = 4;

		$producto = "";

		foreach ($listadoAlmacen as $indice => $valor) {
			$numeroS += 1;
			$Almacen = $valor->ALMAC_Descripcion;
			$this->excel->setActiveSheetIndex(0)
				->setCellValue("A$lugar", $numeroS)
				->setCellValue("B$lugar", $valor->ALMAC_Descripcion)
				->setCellValue("C$lugar", $valor->PROD_CodigoUsuario)
				->setCellValue("D$lugar", $valor->PROD_Nombre)
				->setCellValue("E$lugar", $valor->FAMI_Descripcion)
				->setCellValue("F$lugar", $valor->PROD_Modelo)
				->setCellValue("G$lugar", $valor->MARCC_Descripcion)
				->setCellValue("H$lugar", $valor->LOTC_Numero)
				->setCellValue("I$lugar", $valor->LOTC_FechaVencimiento)
				->setCellValue("J$lugar", number_format($valor->ALMALOTC_Costo, 2))
				->setCellValue("K$lugar", $valor->ALMALOTC_Cantidad);

			if ($indice > 1 && $producto == $valor->PROD_Codigo)
				$this->excel->getActiveSheet()->getStyle("A$lugar:K$lugar")->applyFromArray($estiloColumnasPar);
			else
				$this->excel->getActiveSheet()->getStyle("A$lugar:K$lugar")->applyFromArray($estiloColumnasImpar);

			$lugar += 1;
			$producto = $valor->PROD_Codigo;
		}

		for ($i = 'A'; $i <= 'K'; $i++) {
			$this->excel->setActiveSheetIndex(0)
				->getColumnDimension($i)->setAutoSize(true);
		}

		$filename = "Stock almacen detallado " . date('Y-m-d') . ".xls"; //save our workbook as this file name

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=$filename");
		header("Cache-Control: max-age=0"); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		// Forzamos a la descarga
		$objWriter->save('php://output');
	}
}
