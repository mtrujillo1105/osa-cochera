<div class="sidebar">
	<!-- Sidebar user panel (optional) -->
	<div class="user-panel mt-3 pb-3 mb-3 d-flex">
		<div class="image">
			<img src="<?=$base_url;?>public/images/icons/persona.jpg" class="img-circle elevation-2" alt="User Image">
		</div>
		<div class="info">
			<a href="#" class="d-block"><?=$nombre_persona;?></a>
		</div>
	</div>

	<!-- Sidebar Menu -->
	<nav class="mt-2">
		<ul class="nav nav-pills nav-sidebar flex-column text-sm nav-flat nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
			<!-- Agrega la clase "menu-open" en el menu activo -->
			<li class="nav-item">
				<a class="nav-link" href="<?=site_url('seguridad/inicio');?>">
					<i class="nav-icon fas fa-home"></i>
					<p>Inicio</p>
				</a>
			</li> <?php
			foreach ($menus_base as $menu_base) { ?>
				<li class="nav-item has-treeview" id="menu_<?=$menu_base->MENU_Url;?>">
					<a class="nav-link" id="menubg_<?=$menu_base->MENU_Url;?>" href="<?=($menu_base->submenus != NULL) ? site_url($menu_base->MENU_Url) : "#";?>">
						<i class="nav-icon <?=$menu_base->MENU_Icon;?>"></i>
						<p>
							<?=$menu_base->MENU_Titulo;?>
							<i class="fas fa-angle-left right"></i>
							<span class="badge badge-info right"><?=($menu_base->submenus != NULL) ? count($menu_base->submenus) : 0;?></span>
						</p>
					</a> <?php
					if ($menu_base->submenus != NULL){ ?>
						<ul class="nav nav-treeview"> <?php
							foreach ($menu_base->submenus as $submenu) { ?>
								<li class="nav-item">
									<a class="nav-link" id="submenu_<?=str_replace('/','_',$submenu->MENU_Url);?>" href="<?=site_url($submenu->MENU_Url);?>">
										<i class="nav-icon <?=$submenu->MENU_Icon;?>"></i>
										<p><?=$submenu->MENU_Titulo;?></p>
									</a>
								</li> <?php
							} ?>
						</ul> <?php
					} ?>
				</li> <?php
			} ?>

			<li class="nav-item">
				<a class="nav-link" href="<?=site_url('index/salir_sistema');?>">
					<i class="nav-icon fas fa-sign-out-alt"></i>
					<p>Cerrar Sesión</p>
				</a>
			</li>
		</ul>
	</nav>
	<!-- /.sidebar-menu -->
</div>