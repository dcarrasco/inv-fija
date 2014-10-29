<div>
	<div class="form-group">
		<?php echo form_open('', array('id' => 'form_centro')); ?>
		<strong>Centro</strong>
		<label class="radio-inline">
			<?php echo form_radio('sel_centro', 'CL03', set_radio('sel_centro','CL03', TRUE)); ?>
			CL03
		</label>
		<label class="radio-inline">
			<?php echo form_radio('sel_centro', 'CL15', set_radio('sel_centro','CL15')); ?>
			CL15
		</label>
		<?php echo form_close(); ?>
	</div>
	<div class="form-group">
		<strong>Tipo Mapa</strong>
		<label class="radio-inline">
			<?php echo form_radio('sel_treemap_type', 'cantidad', set_radio('sel_treemap_type','cantidad', TRUE)); ?>
			Cantidad de equipos
		</label>
		<label class="radio-inline">
			<?php echo form_radio('sel_treemap_type', 'valor', set_radio('sel_treemap_type','valor')); ?>
			Valor de equipos
		</label>
	</div>
</div>

<div>
    <div id="chart_div" style="width: 100%; height: 500px;"></div>
</div>


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["treemap"]});
	google.setOnLoadCallback(drawChart2);

	var data = {
		cantidad: <?php echo $treemap_data_cantidad ?>,
		valor: <?php echo $treemap_data_valor ?>
	};

	$('input[name="sel_centro"]').click(function() {
		$('#form_centro').submit();
	});

	$('input[name="sel_treemap_type"]').click(function() {
		var type_selected = $('input[name="sel_treemap_type"]:checked').val();
		drawChart(data[type_selected]);
	});

	function drawChart2()
	{
		drawChart(data.cantidad);
	}

	function drawChart(treedata)
	{
		var data = google.visualization.arrayToDataTable(treedata);

		tree = new google.visualization.TreeMap(document.getElementById('chart_div'));

		var options = {
			minColor: '#0d0',
			midColor: '#7e8',
			maxColor: '#f00',
			headerHeight: 20,
			fontColor: 'black',
			showScale: true,
			generateTooltip: showFullTooltip,
			title: 'Mapa Stock',
		};

		tree.draw(data, options);

		function showFullTooltip(row, size, value)
		{
			return '<div style="background:#fd9; padding:10px; border-style:solid">' +
				'<span><b>' + data.getValue(row, 0) +
				'</b>, ' + data.getValue(row, 1) + ', ' + data.getValue(row, 2) +
				', ' + data.getValue(row, 3) + '</span><br>' +
				data.getColumnLabel(2) +
				' (total value of this cell and its children): ' + size + '<br>' +
				data.getColumnLabel(3) + ': ' + value + ' </div>';
		}
	}
</script>