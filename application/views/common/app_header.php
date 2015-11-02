<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>inventario fija</title>
	<link rel="icon" href="<?php echo base_url(); ?>favicon.png" type="image/png" />

	<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/fix_bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/jquery.jqplot.min.css" />

	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.jqplot.min.js"></script>

	<script type="text/javascript">
		js_base_url = '<?php echo ($this->config->item('index_page') == '') ? base_url() : base_url() . $this->config->item('index_page') . '/'; ?>';
	</script>
</head>

<body>

<!-- DIV principal de la aplicacion -->

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

<div class="container">

<?php if (isset($menu_modulo)): ?>
<ul class="nav nav-tabs hidden-print">
	<?php foreach($menu_modulo['menu'] as $modulo => $val): ?>
		<li class="<?php echo ($modulo == $menu_modulo['mod_selected']) ? 'active' : ''; ?>">
			<?php echo anchor($val['url'], $val['texto']); ?>
		</li>
	<?php endforeach; ?>
</ul>

<div class="tab-content">
<div class="tab-pane active">
<?php endif; ?>

<?php echo (isset($msg_alerta) and $msg_alerta !== '') ? $msg_alerta : ''; ?>
