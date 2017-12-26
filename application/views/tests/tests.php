<!doctype html>
<html lang="es">
<head>
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

	<script type="text/javascript">
		var js_base_url = "{js_base_url}";
	</script>

</head>


<body>
<div class="container-fluid" id="container">

	<table class="table table-hover table-condensed">
		<thead>
			<tr>
				<th>n</th>
				<th>Test Name</th>
				<th>Test Datatype</th>
				<th>Expected Datatype</th>
				<th>Result</th>
				<th>Filename</th>
				<th>Line Number</th>
				<!-- <th>Notes</th> -->
			</tr>
		</thead>
		{test_report}
	</table>

</div>

<footer class="footer">
	<div class="text-center text-muted">
		<small><i class="fa fa-creative-commons"></i> 2013 &ndash; <?= date('Y'); ?></small>
	</div>
</footer>

</body>
</html>
