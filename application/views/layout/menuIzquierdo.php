<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/estiloMenuIzquierdo.css?=<?=CSS;?>" type="text/css"/>
<div id="wrapperMenuIzquierdo">
    <ul class="menu">
        <li class="item1"><a href="<?php echo site_url('index/inicio'); ?>">Inicio</a> </li> 
        <?php
            /**verificamos si existe menu seleccionado en session **/
            $isExisteMenuSeleccionado = false;
            $menuCodigo = 0;
            $codigoMenuSub = 0;
            if(isset($_SESSION['idMenuSeleccionado'])){
            	$menuCodigo=$_SESSION['idMenuSeleccionado'];
            	$isExisteMenuSeleccionado=true;
            	if(isset($_SESSION['idMenuSub']))
            		$codigoMenuSub=$_SESSION['idMenuSub'];        	
            }
            /**fin de verificacion**/

            if ( !$isExisteMenuSeleccionado ){ ?>
                <li class="item1" id="idLiMenuDinamico" style="display:none;"> </li> <?php
            }
        
            foreach ($menus_base as $valor) {
        	   /**7:mantenimiento-9:Reportes**/
            	$idDefaultd = $valor->MENU_Codigo;
                $menuCodigo = ($menuCodigo == 0) ? 2 : $menuCodigo; // SI NO EXISTE SESION DE MENU TOMA EL MODULO PRINCIPAL ID 2
                
                if ($idDefaultd == $menuCodigo){
            		$descripcionMP=$valor->MENU_Descripcion;
            		$subMenus = $valor->submenus; ?>

                    <li class="item1" <?=($idDefaultd == $menuCodigo) ? "id='idLiMenuDinamico'" : "";?> >
                    <a href="javascript:;" <?=($idDefaultd == $menuCodigo) ? "id='idAMenuPrincipal'" : "";?> ><?=utf8_decode($descripcionMP);?><span><?=count($subMenus);?></span></a> <?php
                    if(count($subMenus)>0){ ?>
                        <ul <?=($idDefaultd == $menuCodigo) ? "id='idUlMenuSeleccionado'" : " ";?> > <?php
                        foreach ($subMenus as $valorSub){
                        	$codigoSubMenu=$valorSub->MENU_Codigo;
                        	$descripcionSub=$valorSub->MENU_Descripcion;
                        	$urlSub=$valorSub->MENU_Url;
                        	$estadoSub=$valorSub->MENU_FlagEstado;
                        	if($estadoSub==1){ ?>
                                <li class="subitem1"><a id="idAMenuSub_<?php echo $codigoSubMenu; ?>" href="<?php echo (trim($urlSub)!='')?site_url($urlSub):"#";?>" onclick="ingresarMenuSession(<?php echo $idDefaultd ?>,<?php echo $codigoSubMenu ?>)" > <?php echo ($descripcionSub); ?></a></li> <?php
                            }
                        } ?>
                        </ul> <?php
                    } ?>
                    </li> <?php
                }
            } ?>
    </ul>
</div>


<script type="text/javascript">
$(function() {
    var menu_ul = $('.menu > li > ul'),
        menu_a  = $('.menu > li > a');
    <?php if(!$isExisteMenuSeleccionado){ ?>
    menu_ul.hide();
    <?php } ?>
    //$("#idAMenuPrincipal").show();
    menu_a.click(function(e) {
        e.preventDefault();
        logicaMenuDinamico(this);
    });

});


function logicaMenuDinamico(idA){
	 menu_ul = $('.menu > li > ul'),
	 menu_a  = $('.menu > li > a');
	if(!$(idA).hasClass('active')) {
        menu_a.removeClass('active');
        menu_ul.filter(':visible').slideUp('normal');
        $(idA).addClass('active').next().stop(true,true).slideDown('normal');
    } else {
        $(idA).removeClass('active');
        $(idA).next().stop(true,true).slideUp('normal');
    }
	
}
<?php if($isExisteMenuSeleccionado){ ?>
	logicaMenuDinamico("#idAMenuPrincipal");
	$("#idAMenuSub_<?php echo $codigoMenuSub; ?>").addClass('active');
	$("#idAMenuSub_<?=$codigoMenuSub;?>").css("color:black");

	/***menu superior principal seleccionado**/
	$("#idAMenuSuperiorP_<?php echo $menuCodigo; ?>").addClass('active');
	document.getElementById("idAMenuSuperiorP_<?php echo $menuCodigo; ?>").style.background = '#003b65';
	
	
<?php } ?>
</script>

