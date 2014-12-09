<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

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
				<?php echo $this->app_common->titulo_modulo(); ?>
			</a>
		</div>

		<ul class="nav navbar-nav pull-right">
			<?php foreach($this->app_common->menu_app() as $app_item): ?>
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
</nav>

<div class="container">

<?php if (isset($msg_alerta) and $msg_alerta != ''): ?>
<div class="alert alert-warning">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<?php echo $msg_alerta; ?>
</div>
<?php endif; ?>

<?php if (isset($menu_modulo)): ?>
<ul class="nav nav-tabs">
	<?php foreach($menu_modulo['menu'] as $modulo => $val): ?>
		<li class="<?php echo ($modulo == $menu_modulo['mod_selected']) ? 'active' : ''; ?>">
			<?php echo anchor($val['url'], $val['texto']); ?>
		</li>
	<?php endforeach; ?>
</ul>
<br />
<?php endif; ?>
