<div class="content-module">

	<div class="content-module-heading cf">

		<div class="content-header formulario">
			<h3>Consulta tráfico por IMEI</h3>
			<span class="mostrar-ocultar">click para cerrar</span>
			<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
			<div style="clear: both;"></div>
		</div> <!-- fin content-header -->

		<div id="area_formulario" class="cuerpo-formulario mostrar-ocultar">
			<?php echo form_open(''); ?>
			<table>
				<tr>
					<th>
						Series: <br />
						<?php echo form_textarea(array(
								'id' => 'series',
								'name' => 'series',
								'rows' => '10',
								'cols' => '30',
								'value' => set_value('series'),
								'class' => 'form-control',
							)); ?>
					</th>
					<th>
						Meses: <br />
						<?php echo form_multiselect('meses[]', $combo_mes, $this->input->post('meses'),'size="12" class="form-control"'); ?> <br />

					</th>
				</tr>
			</table>
			<br />
			<button type="submit" name="btn_submit" class="btn btn-primary" id="boton-submit">
				<span class="glyphicon glyphicon-list-alt"></span>
				Consultar
			</button>
			<?php echo anchor('analisis_series','Volver...', 'id="boton-reset" class="btn"'); ?>
			<?php echo form_close(); ?>
		</div>

	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">
		<div class="content-header movimientos">
			<h3>Detalle trafico</h3>
			<span class="mostrar-ocultar">click para cerrar</span>
			<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
			<div style="clear: both;"></div>
		</div> <!-- fin content-header -->

		<div id="res_movimientos" class="cuerpo-movimientos mostrar-ocultar">
		<table class="table table-hover table-condensed">
			<tr>
				<th>Serie IMEI</th>
				<th>celular</th>
				<th>Tipo</th>
				<th>RUT</th>
				<th>Nombre</th>
				<th>cod situacion</th>
				<th>Fecha Alta</th>
				<th>Fecha Baja</th>
			</tr>
		</table>
		</div>
	</div>  <!-- fin content-module-main -->
</div>  <!-- fin content-module -->

<br />

<!-- <pre><?php print_r($datos_trafico);?></pre> -->


<script type="text/javascript">
	$(document).ready(function() {
		if ($("#param").val() != "")
		{
			$("div.cuerpo-formulario").hide();
			$("div.formulario span").toggle();
		}

		$("#btn_limpiar").click(function() {
			event.preventDefault;
			$("#param").val("");
			$("#param").focus();
		})

		$("#boton-submit").click(function(event) {
			event.preventDefault();
			var str_series = $('form textarea').val();
			var arr_meses  = $('form select').val();
			if ((str_series != '') && (arr_meses != null))
			{
				var str_meses  = ''
				for (var i=0; i<arr_meses.length; i++)
				{
					if (i>0)
					{
						str_meses += '-';
					}
					str_meses += arr_meses[i];
				}
				$('#res_movimientos').empty();
				str_tabla = '<table class="table table-hover table-condensed"><tr>';
				str_tabla += '<th>Serie IMEI</th><th>celular</th><th>Tipo</th><th>RUT</th><th>Nombre</th><th>cod situacion</th>';
				str_tabla += '<th>Fecha Alta</th><th>Fecha Baja</th>';
				for (var j=0; j<arr_meses.length; j++)
					{
						str_tabla += '<th>' + arr_meses[j] + '</th>';
					}
				str_tabla += '</tr>';
				$('#res_movimientos').append(str_tabla);

				var arr_series = str_series.split('\n');
				for (var i = 0; i<arr_series.length; i++)
				{
					var serie = arr_series[i];
					if(serie != '')
					{
						$.getJSON('<?php echo base_url(); ?>analisis_series/ajax_trafico_mes/' + serie + '/' + str_meses, function(data) {
							for (var i=0; i<data.length; i++)
							{
								var str_append = '';
								str_append += '<tr>';
								str_append += '<td>' + data[i]['imei'] + '</td>';
								str_append += '<td>' + data[i]['celular'] + '</td>';
								str_append += '<td>' + data[i]['tipo'] + '</td>';
								str_append += '<td>' + data[i]['rut'] + '</td>';
								str_append += '<td>' + data[i]['nombre'] + '</td>';
								str_append += '<td>' + data[i]['cod_situacion'] + '</td>';
								str_append += '<td>' + data[i]['fecha_alta'] + '</td>';
								str_append += '<td>' + data[i]['fecha_baja'] + '</td>';

								for (var j=0; j<arr_meses.length; j++)
								{
									var valor_trafico = data[i][arr_meses[j]];
									if (typeof valor_trafico === 'undefined')
									{
										valor_trafico = '';
									}
									str_append += '<td>' + valor_trafico + '</td>';
								}

								str_append += '</tr>';
								$('#res_movimientos table').append(str_append);
							}
						});
					}
				}
			}
			//$("form").submit();
		})

		$("table tr").hover(function() {
			$(this).addClass("highlight");
		}, function() {
			$(this).removeClass("highlight")
		})

		$("div.content-header").click(function() {
			$(this).next("div.mostrar-ocultar").slideToggle("fast");
			$(this).children("span.mostrar-ocultar").toggle();
		})

	});
</script>