

<div id="lineaResultado" style="text-align: left">

    N° de registros encontrados:&nbsp;<?php echo $registros; ?> 

</div>



<div id="frmResultado">

    <table class="fuente8 tb_listado tb_busqueda" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">

        <thead>

            <tr class="cabeceraTabla">

                <td style="width: 50px">ITEM</td>

                <td style="width: 90px">NRO. DOCUMENTO</td>

                <td style="width: 60px">FECHA</td>

                <td style="width: 500px">TITULO</td>



                <td style="width: 20px"></td>

                <td style="width: 20px"></td>

                <td style="width: 20px"></td>

            </tr>

        </thead>

        <tbody>

            <?php

            if (count($lista) > 0) {

                foreach ($lista as $indice => $valor) {

                    $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';

                    ?>

                    <tr class="<?php echo $class; ?>">

                        <td><div align="center"><?php echo $t_indice + 1 ?></div></td>

                        <td><div align="center"><?php

            echo

            str_pad($valor->INVE_Serie, 3, "0", STR_PAD_LEFT) . ' - ' .

            str_pad($valor->INVE_Numero, 6, "0", STR_PAD_LEFT);

                    ?></div></td>

                        <td><div align="center"><?php echo date('d/m/Y', strtotime($valor->INVE_FechaInicio)) ?></div></td>

                        <td><div align="left"><?php echo $valor->INVE_Titulo; ?></div></td>

                        <td>

                            <div align="center">

                                <a onclick="agregar_detalle(<?php echo $valor->INVE_Codigo ?>)">

                                    <img src="<?php echo base_url() ?>public/images/icons/file.png?=<?=IMG;?>" title="Ver Files">

                                </a>

                            </div>

                        </td>

                        <td>

                            <div align="center">

                                <a onclick="modificar_historia(<?php echo $valor->INVE_Codigo ?>)" >

                                    <img src="<?php echo base_url() ?>public/images/icons/modificar.png?=<?=IMG;?>" title="Modificar Registro">

                                </a>

                            </div>

                        </td>   <td>

                            <div align="center">

                                <a onclick="eliminar_historia(<?php echo $valor->INVE_Codigo ?>)" >

                                    <img src="<?php echo base_url() ?>public/images/icons/eliminar.png?=<?=IMG;?>" title="Eliminar Registro">

                                </a>

                            </div>

                        </td>

                    </tr>



                    <?php

                    $t_indice++;

                }

            } else {

                ?>

                <tr>

                    <td colspan="8" width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>

                </tr>

                <?php

            }

            ?>

        </tbody>

    </table>

</div>

<div id="pagination_container" class="busqueda_container" style="margin-top: 15px;"><?php echo $paginacion; ?></div>

<script>

    $(document).ready(function(){



        $(".busqueda_container a").each(function(){



            var url = $(this).attr("href");

            $(this).removeAttr("href");

            $(this).attr("pag", url);

            $(this).bind("click", function()

            {

                paginacion_jquery(url);

            });



        })



    })

</script>

