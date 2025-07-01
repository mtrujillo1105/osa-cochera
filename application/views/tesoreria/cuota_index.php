<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>
    <div class="container-fluid">
        <div class="row header">
            <div class="col-md-12 col-lg-12">
                <div><?=$titulo_busqueda;?></div>
            </div>
        </div>
        <div class="row fuente8 py-1">
            <form id="form_busqueda" method="post" action="<?=$action;?>">
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <label for="nombre_cliente"><?=($tipo_oper=='V') ? 'Cliente: ' : 'Proveedor: ';?></label>
                    
                    <input type="text" name="ruc_cliente" id="ruc_cliente" value="" class="cajaPequena" <?=($tipo_oper=='C') ? 'style="display:none;"' : '';?>/>
                    <input type="text" name="nombre_cliente" id="nombre_cliente" value="" class="cajaGeneral" <?=($tipo_oper=='C') ? 'style="display:none;"' : '';?>/>
                    <input type="text" name="ruc_proveedor" id="ruc_proveedor" value="" class="cajaPequena" <?=($tipo_oper=='V') ? 'style="display:none;"' : '';?>/>
                    <input type="text" name="nombre_proveedor" id="nombre_proveedor" value="" class="cajaGeneral" <?=($tipo_oper=='V') ? 'style="display:none;"' : '';?>/>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <label for="serie">Serie</label> - <label for="numero">número: </label>
                    <input type="text" name="serie" id="serie" value="<?=$numero;?>" class="cajaMinima" maxlength="4"/>
                    <input type="text" name="numero" id="numero" value="<?=$numero;?>" class="cajaGeneral" maxlength="10"/>
                </div>
                <div class="col-sm-2 col-md-2 col-lg-2">
                    <label for="fechai">Fecha Inicial: </label>
                    <input type="date" name="fechai" id="fechai" value="<?=$fechai;?>" class="cajaGeneral"/>
                </div>
                <div class="col-sm-2 col-md-2 col-lg-2">
                    <label for="fechaf">Fecha final: </label>
                    <input type="date" name="fechaf" id="fechaf" value="<?=$fechaf;?>" class="cajaGeneral"/>
                </div>
                <?=$oculto;?>
            </form>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                        <div class="acciones">
                            <div id="botonBusqueda">
                                <ul id="limpiarC" class="lista_botones">
                                    <li id="limpiar">Limpiar</li>
                                </ul>
                                <ul id="buscarC" class="lista_botones">
                                    <li id="buscar">Buscar</li>
                                </ul>
                            </div>
                            <div id="lineaResultado">Documentos encontrados:&nbsp;<?=$registros;?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                        <div class="header text-align-center"><?=$titulo_tabla;?></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 pall-0">
                        <table class="fuente8 display" id="table-cuotas">
                            <div id="cargando_datos" class="loading-table">
                                <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                            </div>
                            <thead>
                                <tr class="cabeceraTabla">
                                    <td style="width:08%" data-orderable="true">FECHA</td>
                                    <td style="width:08%" data-orderable="true">SERIE</td>
                                    <td style="width:08%" data-orderable="true">NÚMERO</td>
                                    <td style="width:06%" data-orderable="true">ID</td>
                                    <td style="width:10%" data-orderable="false">DOC. CLIENTE</td>
                                    <td style="width:34%" data-orderable="false">RAZÓN SOCIAL</td>
                                    <td style="width:10%" data-orderable="false">TOTAL</td>
                                    <td style="width:06%" data-orderable="false">&nbsp;</td>
                                    <td style="width:10%" data-orderable="false">&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="ver_cuotas" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div style="text-align: center;">
                    <h3 class="bold">DETALLE DE CUOTAS</h3>
                    <span id="detaComp" class="bold"></span>
                    <input type="hidden" id="detaCompTotal" name="detaCompTotal" value=""/>
                    <input type="hidden" id="comprobante" name="comprobante" value=""/>
                </div>
                <div class="modal-body panel panel-default">
                    <table id="tbl_coutas" style="width: 100%; margin: auto;" class="table table-fixed">
                        <thead>
                            <th style="width: 08%">NÚMERO</th>
                            <th style="width: 10%">FECHA INICIO</th>
                            <th style="width: 10%">FECHA FIN</th>
                            <th style="width: 10%">MONTO</th>
                            <th style="width: 12%">ESTADO</th>
                            <th style="width: 20%">CAJA</th>
                            <th style="width: 25%">OBSERVACIÓN</th>
                            <th colspan="3" class="cuotas_pdf"></th>
                        </thead>
                        <tbody style="overflow: auto;"></tbody>
                        <tfoot>
                            <th colspan="3"></th>
                            <th style="text-align: right">TOTAL EN CUOTAS:</th>
                            <th><span class="total_cuotas_span"></span></th>
                            <th colspan="6"></th>
                        </tfoot>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default view-cuota" onclick="ver_cuotas_comprobante( $('#comprobante').val() )">Restablecer</button>
                    <button type="button" class="btn btn-primary" id="nvacuotaC" name="nvacuotaC" value="" onclick="addNvacuota()">Nuevo</button>
                    <button type="button" class="btn btn-danger" onclick="montoFactLet(this)">Salir</button>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    base_url = "<?=base_url();?>";
    $(document).ready(function(){
        $('#table-cuotas').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : '<?=base_url();?>index.php/tesoreria/cuota/datatable_comprobantes/<?="$tipo_oper";?>',
                    type: "POST",
                    data: { dataString: "" },
                    beforeSend: function(){
                        $(".loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $(".loading-table").hide();
                    }
            },
            language: spanish,
            order: [[ 2, "desc" ]]
        });

        $("#buscarC").click(function(){
            search();
        });

        $("#limpiarC").click(function(){
            search(false);
        });
    });

    function search( search = true ){
        if (search == true){
            fechai          = $("#fechai").val();
            fechaf          = $("#fechaf").val();

            serie           = $("#serie").val();
            numero          = $("#numero").val();

            ruc_cliente     = $("#ruc_cliente").val();
            nombre_cliente  = $("#nombre_cliente").val();

            ruc_proveedor     = $("#ruc_proveedor").val();
            nombre_proveedor  = $("#nombre_proveedor").val();
        }
        else{
            $("#fechai").val("");
            $("#fechaf").val("");
            $("#serie").val("");
            $("#numero").val("");
            $("#ruc_cliente").val("");
            $("#nombre_cliente").val("");
            $("#ruc_proveedor").val("");
            $("#nombre_proveedor").val("");

            fechai = "";
            fechaf = "";
            seriei = "";
            numero = "";
            ruc_cliente = "";
            nombre_cliente = "";
            ruc_proveedor = "";
            nombre_proveedor = "";
        }

        $('#table-cuotas').DataTable({ responsive: true,
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            ajax:{
                    url : '<?=base_url();?>index.php/tesoreria/cuota/datatable_comprobantes/<?="$tipo_oper";?>',
                    type: "POST",
                    data: {
                            fechai: fechai, 
                            fechaf: fechaf,
                            seriei: serie,
                            numero: numero,
                            ruc_cliente: ruc_cliente,
                            nombre_cliente: nombre_cliente,
                            ruc_proveedor: ruc_proveedor,
                            nombre_proveedor: nombre_proveedor
                    },
                    beforeSend: function(){
                        $(".loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $(".loading-table").hide();
                    }
            },
            language: spanish,
            order: [[ 2, "desc" ]]
        });
    }

    function total_coutas(){
        size = $(".total_cuotas_val").length;
        total = 0;
        for (i = 1; i <= size; i++){
            total = total + parseFloat( $("#cuotap_"+i).val() );
        }
        
        $(".total_cuotas_span").html(total.toFixed(2));
    }

    function ver_cuotas_comprobante(comprobante = ""){
        if (comprobante != ""){
            url = base_url+"index.php/tesoreria/cuota/obtener_cuotas_comprobante";
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: { comprobante: comprobante },
                beforeSend:function(data){
                    $('#tbl_coutas tbody').html('');
                },
                error: function (XRH, error){
                    Swal.fire({
                                icon: "info",
                                title: "No pudimos consultar la información, intentalo nuevamente.",
                                showConfirmButton: false,
                                showCancelButton: false,
                                timer: 2000
                            });
                },
                success: function (data){
                    var fila = '';
                    if (data.match == false) {
                        return false;
                    }
                    
                    var v = data.comprobante.operacion;

                    $("#nvacuotaC").val(comprobante);

                    if (data.comprobante.documento == "F")
                        documento = "FACTURA";
                    else
                        if (data.comprobante.documento == "B")
                            documento = "BOLETA";
                    else
                        documento = "COMPROBANTE";
                    
                    $('#detaComp').text(documento + " NÚMERO " + data.comprobante.serie + "-" + data.comprobante.numero + " TOTAL " + data.comprobante.total);
                    $('#detaCompTotal').val(data.comprobante.total);
                    $('#comprobante').val(comprobante);
                    total = 0;
                    $(".cuotas_pdf").html("<a href='"+base_url+"index.php/tesoreria/cuota/comprobante_cuotas/"+data.comprobante.comprobante_id+"/1' data-fancybox data-type='iframe' class='btn btn-info color-white'>PDF - CUOTAS</a>");

                    $.each(data.info, function(i,item){
                        indice = i + 1;
                         
                        fila ="<tr>"
                            fila += "<td id='idescripcion"+indice+"'>"+item.ncuota+"</td>";
                            fila += "<td><input readOnly class='form-control pall-0' type='date' id='fechai_"+indice+"' value='"+item.fechaiv+"'></td>";
                            fila += "<td><input readOnly class='form-control pall-0' type='date' id='fechaf_"+indice+"' value='"+item.fechafv+"'></td>";
                            fila += "<td><input readOnly class='form-control pall-0 total_cuotas_val' onchange='total_coutas()' type='number' id='cuotap_"+indice+"' value='"+item.cuota+"'></td>";

                            fila += "<td><select disabled class='form-control' id='estado_pago_"+indice+"'>";
                                if (item.estado_pago == "0") {
                                    fila += "<option selected value='0'>Pendiente</option>";
                                    fila += "<option value='1'>Pagado</option>";
                                }
                                else{
                                    fila += "<option value='0'>Pendiente</option>";
                                    fila += "<option selected value='1'>Pagado</option>"
                                }
                            fila += "</select></td>";

                            fila += "<td><select disabled class='form-control' id='caja_"+indice+"'>";
                                fila += "<option value=''> :: SELECCIONE UNA CAJA :: </option>";
                                $.each(item.cajas,function(j,list){
                                    fila += "<option value='"+list.CAJA_Codigo+"'";
                                        if (list.CAJA_Codigo == item.caja)
                                            fila += "selected";
                                    fila += ">"+list.CAJA_Nombre+"</option>";
                                });
                            fila += "</select></td>";

                            fila +="<td><textarea readOnly class='form-control pall-0 font-8 h-5' id='observacion_"+indice+"' maxlength='580'>" + item.observacion + "</textarea></td>";

                            if (item.estado_pago == 0){
                                fila += "<td>";
                                    fila += "<a href='javascript:;' class='read_row_" + indice + "' onclick='editar("+indice+")'><img src='"+base_url+"images/icono_codigo.png?=<?=IMG;?>' width='24' height='16' border='0' title='Modificar'></a>";
                                
                                    fila += "<a class='update_row_" + indice + " display-none' href='javascript:;' onclick='actualizar_cuota("+indice+","+item.codigo_cuota+","+comprobante+")'><img src='"+base_url+"images/save.gif?=<?=IMG;?>' width='16' height='16' border='0' title='Modificar'></a>";
                                fila += "</td>";
                            }

                            fila += "<td>";
                            //    fila += "<a href='javascript:;' class='read_row_" + indice + "' onclick='ver_pdf_cuota("+item.codigo_cuota+",8,0,"+'"'+v+'"'+")'><img src='"+base_url+"images/icono_imprimir.png?=<?=IMG;?>' width='16' height='16' border='0' title='Modificar'></a>";
                                fila += "<a href='"+base_url+"index.php/tesoreria/cuota/cuota_pdf/"+item.codigo_cuota+"/0' class='read_row_" + indice + "' data-fancybox data-type='iframe'><img src='"+base_url+"images/icono_imprimir.png?=<?=IMG;?>' width='16' height='16' border='0'></a>";
                                fila += "<a class='update_row_" + indice + " display-none' href='javascript:;' onclick='cancelar_edicion("+indice+")'><img src='"+base_url+"images/sprite.png?=<?=IMG;?>' width='16' height='16' border='0' title='Cancelar Modificación'></a>";
                            fila += "</td>";

                            fila += "<td>";
                            //    fila += "<a href='javascript:;' class='read_row_" + indice + "' onclick='ver_pdf_cuota("+item.codigo_cuota+",8,1,"+'"'+v+'"'+")'><img src='"+base_url+"images/pdf.png?=<?=IMG;?>' width='16' height='16' border='0' title='Modificar'></a>";
                                fila += "<a href='"+base_url+"index.php/tesoreria/cuota/cuota_pdf/"+item.codigo_cuota+"/1' class='read_row_" + indice + "' data-fancybox data-type='iframe'><img src='"+base_url+"images/pdf.png?=<?=IMG;?>' width='16' height='16' border='0'></a>";
                                fila += "<a class='update_row_" + indice + " display-none' href='javascript:;' onclick='borrar_cuota("+indice+","+item.codigo_cuota+","+comprobante+")'><img src='"+base_url+"images/eliminar.png?=<?=IMG;?>' width='16' height='16' border='0' title='Eliminar cuota'></a>";
                            fila += "</td>";
                        fila += "</tr>";

                        $('#tbl_coutas tbody').append(fila);
                        total = total + parseFloat(item.cuota);
                    });

                    $(".total_cuotas_span").html(total.toFixed(2));
                }
            });
        }
    }

    function addNvacuota(){
        var indice = 0;

        for (x = 1; x < 100; x++){
            if ( $("#cuotap_"+x).length == 0 ){
                indice = x;
                break;
            }
        }

        var comprobante = $("#nvacuotaC").val();

        var fechai = "";
        if (indice > 1){
            posA = indice - 1;
            fechai = $("#fechaf_" + posA).val();
        }

        fila = "<tr id='itemNvacuota"+indice+"'>"
            fila += "<td id='idescripcion"+indice+"'>------------</td>";
            fila += "<td><input readOnly class='form-control pall-0' type='date' id='fechai_"+indice+"' value='" + fechai + "'></td>";
            fila += "<td><input readOnly class='form-control pall-0' type='date' id='fechaf_"+indice+"' value=''></td>";
            fila += "<td><input readOnly class='form-control pall-0 total_cuotas_val' onchange='total_coutas()' type='number' id='cuotap_"+indice+"' value=''></td>";
                       
            fila += "<td><select disabled class='form-control pall-0' id='estado_pago_"+indice+"'>";
                fila += "<option selected value='0'>Pendiente</option>";
                fila += "<option value='1'>Pagado</option>";
            fila += "</select>";

            fila += "<td><textarea readOnly class='form-control pall-0 font-8 h-5' id='observacion_"+indice+"' maxlength='580'></textarea></td>";


            fila += "<td>";
                fila +="<a class='read_row_" + indice + "' href='javascript:;' onclick='editar("+indice+")'><img src='"+base_url+"images/icono_codigo.png?=<?=IMG;?>' width='24' height='16' border='0' title='Modificar'></a>";
                fila += "<a class='update_row_" + indice + " display-none' href='javascript:;' onclick='guardarNvaCuota("+indice+","+comprobante+")'><img src='"+base_url+"images/save.gif?=<?=IMG;?>' width='16' height='16' border='0' title='Modificar'></a>";
            fila += "</td>";
                            
            fila += "<td><a class='update_row_" + indice + " display-none' href='javascript:;' onclick='cancelar_edicion("+indice+")'><img src='"+base_url+"images/sprite.png?=<?=IMG;?>' width='16' height='16' border='0' title='Cancelar Modificación'></a></td>";

            fila +="<td><a class='update_row_" + indice + " display-none' href='javascript:;' onclick='borrarNvaCuota("+indice+")'><img src='"+base_url+"images/eliminar.png?=<?=IMG;?>' width='16' height='16' border='0' title='Eliminar cuota'></a></td>";
        fila += "</tr>";
                        
        $('#tbl_coutas tbody').append(fila);
    }

    function editar(fila){
        $(".read_row_"+fila).hide();
        $(".update_row_"+fila).show();
        
        $("#estado_pago_"+fila).removeAttr('disabled');
        $("#caja_"+fila).removeAttr('disabled');
        $("#fechai_"+fila).removeAttr('readOnly');
        $("#fechaf_"+fila).removeAttr('readOnly');
        $("#observacion_"+fila).removeAttr('readOnly');
        $("#cuotap_"+fila).removeAttr('readOnly');
    }

    function cancelar_edicion(fila){
        $(".read_row_"+fila).show();
        $(".update_row_"+fila).hide();

        $("#estado_pago_"+fila).attr('disabled', true);
        $("#caja_"+fila).attr('disabled', true);
        $("#fechai_"+fila).attr('readOnly', true);
        $("#fechaf_"+fila).attr('readOnly', true);
        $("#observacion_"+fila).attr('readOnly', true);
        $("#cuotap_"+fila).attr('readOnly', true);
    }

    function actualizar_cuota(fila, cuota, comprobante){
        Swal.fire({
                    icon: "info",
                    title: "¿Estas seguro de guardar esta cuota?",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.value){
                        var estado_pago = $("#estado_pago_"+fila).val();
                        var caja = $("#caja_"+fila).val();
                        var fechai = $("#fechai_"+fila).val();
                        var fechaf = $("#fechaf_"+fila).val();
                        var observacion = $("#observacion_"+fila).val();
                        var cuotap = $("#cuotap_"+fila).val();
                        var url = base_url+"index.php/tesoreria/cuota/guardar_couta";
                        cantPagosCuotas = 0;
                        for (x = 1; x < 100; x++){
                            if ( $("#cuotap_"+x).length == 0 )
                                break;
                            else{
                                var mont = parseFloat( $("#cuotap_"+x).val() );
                                cantPagosCuotas = cantPagosCuotas + mont;
                            }
                        }

                        if  (cantPagosCuotas > $("#detaCompTotal").val() ){
                            Swal.fire({
                                        icon: "warning",
                                        title: "La cantidad ingresada supera el monto total del comprobante.",
                                        html: "<b class='color-red'>Verifique el monto total de las cuotas y el total del comprobante.</b>",
                                        showConfirmButton: true,
                                        timer: 7000
                                    });
                        }
                        else{
                            $.ajax({
                                type: 'POST',
                                url: url,
                                dataType: 'json',
                                data: {
                                        idcuota: cuota,
                                        comprobante: "",
                                        observacion: observacion,
                                        estado_pago: estado_pago,
                                        caja: caja,
                                        fechai: fechai,
                                        fechaf: fechaf,
                                        cuota: cuotap
                                },
                                success: function(data){
                                    if (data.result == "success") {
                                        Swal.fire({
                                            icon: "success",
                                            title: "Registro actualizado.",
                                            showConfirmButton: true,
                                            timer: 2000
                                        });
                                    }
                                    else{
                                        Swal.fire({
                                            icon: "error",
                                            title: "Sin cambios.",
                                            html: "<b class='color-red'>El registro no fue actualizado, intentelo nuevamente.</b>",
                                            showConfirmButton: true,
                                            timer: 2000
                                        });
                                    }
                                },
                                complete: function(){
                                    ver_cuotas_comprobante(comprobante);
                                }
                            });
                        }
                    }
                });
    }

    function guardarNvaCuota(fila, comprobante){
        Swal.fire({
                    icon: "info",
                    title: "¿Estas seguro de guardar esta cuota?",
                    html: "<b class='color-red'></b>",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.value){
                        var estado_pago = $("#estado_pago_"+fila).val();
                        var caja = $("#caja_"+fila).val();
                        var fechai = $("#fechai_"+fila).val();
                        var fechaf = $("#fechaf_"+fila).val();
                        var observacion = $("#observacion_"+fila).val();
                        var cuotap = $("#cuotap_"+fila).val();
                        cantPagosCuotas = 0;
                        for (x = 1; x < 100; x++){
                            if ( $("#cuotap_"+x).length == 0 )
                                break;
                            else{
                                var mont = parseFloat( $("#cuotap_"+x).val() );
                                cantPagosCuotas = cantPagosCuotas + mont;
                            }
                        }
                        
                        if  (cantPagosCuotas > $("#detaCompTotal").val() ){
                            Swal.fire({
                                icon: "warning",
                                title: "La cantidad ingresada supera el monto total del comprobante.",
                                html: "<b class='color-red'>Verifique el monto total de las cuotas y el total del comprobante.</b>",
                                showConfirmButton: true,
                                timer: 7000
                            });
                        }
                        else{
                            var url = base_url+"index.php/tesoreria/cuota/guardar_couta";
                            $.ajax({
                                type: 'POST',
                                url: url,
                                dataType: 'json',
                                data: {
                                        idcuota: "",
                                        comprobante: comprobante,
                                        observacion: observacion,
                                        estado_pago: estado_pago,
                                        caja: caja,
                                        fechai: fechai,
                                        fechaf: fechaf,
                                        cuota: cuotap
                                },
                                success: function(data){
                                    if (data.result == "success") {
                                        Swal.fire({
                                            icon: "success",
                                            title: "información de cuota registrada.",
                                            showConfirmButton: true,
                                            timer: 2000
                                        });
                                    }
                                    else{
                                        Swal.fire({
                                            icon: "error",
                                            title: "Sin cambios.",
                                            html: "<b class='color-red'>La información no fue registrada, intentelo nuevamente.</b>",
                                            showConfirmButton: true,
                                            timer: 2000
                                        });
                                    }
                                },
                                complete: function(){
                                    ver_cuotas_comprobante(comprobante);
                                }
                            });
                        }
                    }
                });
    }

    function borrar_cuota(fila,cuota,comprobante){
        Swal.fire({
                    icon: "info",
                    title: "¿Estas seguro de eliminar esta cuota?",
                    html: "<b class='color-red'>Esta Acción no se puede deshacer.</b>",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.value){
                        var estado_pago = $("#estado_pago_"+fila).val();
                        var caja = $("#caja_"+fila).val();
                        var fechai = $("#fechai_"+fila).val();
                        var fechaf = $("#fechaf_"+fila).val();
                        var observacion = $("#observacion_"+fila).val();
                        var cuotap = $("#cuotap_"+fila).val();
                        var url = base_url+"index.php/tesoreria/cuota/borrar_cuota";
                        $.ajax({
                            type: 'POST',
                            data:{
                                idcuota: cuota
                            },
                            url: url,
                            dataType: 'json',
                            success:function(data){
                                if (data.result == "success"){
                                    Swal.fire({
                                                icon: "success",
                                                title: "Cuota eliminada correctamente.",
                                                showConfirmButton: true,
                                                timer: 2000
                                            });
                                }
                                else{
                                    Swal.fire({
                                                icon: "error",
                                                title: "Sin cambios.",
                                                html: "<b class='color-red'>El registro no fue eliminado. Intentelo nuevamente.</b>",
                                                showConfirmButton: true,
                                                timer: 7000
                                            });
                                }
                            },
                            complete: function(){
                                ver_cuotas_comprobante(comprobante);
                            }
                        });
                    }
                });
    }

    function borrarNvaCuota(fila){
        Swal.fire({
                    icon: "warning",
                    title: "¡Los campos habilitados para el nuevo registro seran eliminados!<br>¿Desea continuar?",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.value){
                        $("#itemNvacuota"+fila).remove();
                    }
                });
    }

    function montoFactLet(id){

        importe_total = parseFloat( $("#detaCompTotal").val() );
        importe_cuotas = parseFloat( $(".total_cuotas_span").text() );
        
        diferencia = importe_total - importe_cuotas;

        if ( diferencia != 0 ){
            Swal.fire({
                        icon: "warning",
                        title: "Los importes no coinciden, ¿Desea volver a editar o salir?",
                        html: "<b class='color-red'>El importe ingresado en la cantidad de cuotas no coincide con el importe total del documento de origen.</b>",
                        showConfirmButton: true,
                        showCancelButton: true,
                        confirmButtonText: "Volver",
                        cancelButtonText: "Salir"
                    }).then(result => {
                        if (!result.value){
                            $("#ver_cuotas").modal("hide");
                        }
                    });
        }
        else
            $("#ver_cuotas").modal("hide");
    }

    function ver_pdf_cuota(cuota,documento,imagen,tipo) {
        var url = base_url + "index.php/maestros/configuracionimpresion/impresionDocumentoCuota/"+cuota+"/"+documento+"/"+imagen+"/"+tipo+"/";
        window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
    }

    function verReporte(){
        var url = base_url + "index.php/tesoreria/cuota/impresionDocumentocuota";
        window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
    }
</script>