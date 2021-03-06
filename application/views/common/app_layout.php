<!doctype html>
<html lang="es">
<head>
	<!-- ************* APP-DINAMICS *************  -->
	<script>
	window['adrum-start-time']= new Date().getTime();
	window['adrum-app-key'] = 'AD-AAB-AAB-VPZ';
	</script>
	<script src="{base_url}js/adrum.js"></script>
	<!-- ************* APP-DINAMICS *************  -->

	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{app_title}</title>

	<link rel="icon" href="{base_url}img/favicon.png" type="image/png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="{base_url}img/favicon-152.png">

	<link rel="stylesheet" href="{base_url}css/bootstrap.min.css" />
	<link rel="stylesheet" href="{base_url}css/bootstrap-datepicker3.min.css" />
	<link rel="stylesheet" href="{base_url}css/font-awesome.min.css" />
	<link rel="stylesheet" href="{base_url}css/fix_bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="{base_url}css/jquery.jqplot.min.css" />

	<script type="text/javascript" src="{base_url}js/jquery.js"></script>
	<script type="text/javascript" src="{base_url}js/moment.min.js"></script>
	<script type="text/javascript" src="{base_url}js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{base_url}js/bootstrap-datepicker.min.js"></script>
	<script type="text/javascript" src="{base_url}js/locales/bootstrap-datepicker.es.js"></script>
	<script type="text/javascript" src="{base_url}js/jquery.jqplot.min.js"></script>

	<script type="text/javascript">
		var js_base_url = "{js_base_url}";
	</script>

	{extra_styles}

</head>


<body>

<?php if ( ! $is_vista_login): ?>
	{app_navbar}
<?php endif; ?>

<div class="container-fluid" id="container">

<?php if (isset($app_menu_modulo)): ?>
	{app_menu_modulo}
	<div class="col-md-10">
<?php endif; ?>

<?php if ( ! $is_vista_login): ?>
	{msg_alerta}
<?php endif; ?>

<!-- ============================== MODULOS APP ============================== -->
{arr_vistas}
	{vista}
{/arr_vistas}
<!-- ============================== /MODULOS APP ============================== -->

<?php if (isset($app_menu_modulo)): ?>
	</div> <!-- DIV   class="col-md-10" -->
	</div> <!-- DIV   class="row"    -->
<?php endif; ?>

</div> <!-- DIV principal de la aplicacion   class="container"-->

<footer class="footer">
	<div class="text-center text-muted">
		<small><i class="fa fa-creative-commons"></i> 2013 &ndash; <?= date('Y'); ?></small>
	</div>
</footer>

</body>
</html>
