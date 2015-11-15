<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">
				<!-- <img src="<?php echo base_url(); ?>img/TEL-logo_neg1.jpg" height="35"> -->
				<?php echo titulo_modulo(); ?>
			</a>

			<button class="navbar-toggle" data-toggle="collapse" data-target=".navMenuCollapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>


		<div class="collapse navbar-collapse navMenuCollapse">
			<ul class="nav navbar-nav navbar-right">
				<?php foreach(menu_app() as $app_item): ?>
				<li class="dropdown <?php echo $app_item['selected']; ?>">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<span class="<?php echo $app_item['icono']; ?>"></span>
						<?php echo $app_item['app']; ?>
						<b class="caret"></b>
					</a>

					<ul class="dropdown-menu">
						<?php foreach($app_item['modulos'] as $modulo_item): ?>
							<li <?php echo ($modulo_item['url'] == $this->uri->segment(1)) ? 'class="active"' : ''; ?>>
								<a href="<?php echo site_url($modulo_item['url']); ?>">
									<span class="<?php echo $modulo_item['icono']; ?>"></span>
									<?php echo $modulo_item['modulo']; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
				<?php endforeach; ?>

				<li>
					<a href="<?php echo site_url('login/logout'); ?>">
						<span class="glyphicon glyphicon-off"></span>
						Logout <?php echo $this->acl_model->get_user_firstname(); ?>
					</a>
				</li>

			</ul>
		</div>
	</div>
</nav>
