<div class="row">
	<?php //echo $menu_configuracion; ?>
</div>

<?php echo form_open('','id="frm_ppal"'); ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
			Parametros consulta
		</a>
	</div>

	<div class="panel-body collapse in" id="form_param">
		<div class="accordion-inner">
			<div class="row">
				<div class="col-md-4">
					<div>
						<div class="radio-inline">
							<?php echo form_radio('set_serie', 'serie_deco', set_radio('set_serie','serie_deco', TRUE));?>
							Serie Deco
						</div>
						<div class="radio-inline">
							<?php echo form_radio('set_serie', 'rut', set_radio('set_serie','rut'))?>
							RUT Cliente
						</div>
					</div>
					<div>
						<?php echo form_textarea(array(
								'id' => 'series',
								'name' => 'series',
								'rows' => '10',
								'cols' => '30',
								'value' => set_value('series'),
								'class' => 'form-control',
							)); ?>
					</div>
				</div>

				<div class="col-md-4">
					<div>
						<strong>Reportes</strong>
					</div>
					<div>
						<div class="radio-inline">
							<?php echo form_radio('tipo_reporte', 'log', set_radio('tipo_reporte', 'log', TRUE)); ?>
							Log completo
						</div>
						<div class="radio-inline">
							<?php echo form_radio('tipo_reporte', 'ultimo', set_radio('tipo_reporte', 'ultimo')); ?>
							Ultimo deco
						</div>
					</div>
					<div>
						<div class="checkbox">
							<?php echo form_checkbox('ult_mov', 'show', set_value('ult_mov'))?>
							Filtrar ultimo movimiento
						</div>
					</div>
					<div>
						<div>
							Tipo Operacion CAS
						</div>
						<div class="checkbox-inline">
							<?php echo form_checkbox('tipo_op_alta', 'alta', set_value('tipo_op_alta', 'alta')); ?>
							Alta
						</div>
						<div class="checkbox-inline">
							<?php echo form_checkbox('tipo_op_baja', 'baja', set_value('tipo_op_baja', 'baja')); ?>
							Baja
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="pull-right">
						<button type="submit" name="submit" class="btn btn-primary" id="boton-submit">
							<span class="glyphicon glyphicon-list-alt"></span>
							Consultar
						</button>
						<button name="excel" class="btn btn-default" id="boton-reset">
							<span class="glyphicon glyphicon-refresh"></span>
							Limpiar
						</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>


<div class="panel panel-default">
	<div class="panel-heading">
		<a href="#tabla_movimientos" class="accordion-toggle" data-toggle="collapse">
			Log Gestor DTH
		</a>
	</div>

	<div class="panel-body collapse in" id="tabla_movimientos">
		<div class="accordion-inner" style="overflow: auto">
			<table class="table table-bordered table-striped table-hover table-condensed" style="white-space:nowrap;">
			<?php foreach($log as $log_serie): ?>
				<tr>
					<th>id</th>
					<th>fecha</th>
					<th>serie deco</th>
					<th>serie tarjeta</th>
					<th>peticion</th>
					<th>estado</th>
					<th>tipo operacion cas</th>
					<th>telefono</th>
					<th>RUT</th>
					<th>Nombre cliente</th>
				</tr>
			<?php foreach($log_serie as $reg_log): ?>
				<tr>
					<td><small><?php echo $reg_log['id_log_deco_tarjeta'] ?></small></td>
					<td><small><?php echo $reg_log['fecha_log'] ?></small></td>
					<td><small><span class="serie"><?php echo $reg_log['serie_deco'] ?></span></small></td>
					<td><small><?php echo $reg_log['serie_tarjeta'] ?></small></td>
					<td><small><?php echo $reg_log['peticion'] ?></small></td>
					<td><small><?php echo $reg_log['estado'] ?></small></td>
					<td><small><?php echo $reg_log['tipo_operacion_cas'] ?></small></td>
					<td><small><?php echo $reg_log['telefono']?></small></td>
					<td><small><?php echo $reg_log['rut'] ?></small></td>
					<td><small><?php echo $reg_log['nombre'] ?></small></td>
				</tr>
			<?php endforeach; ?>
			<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>

<?php echo form_open('analisis_series/historia', array('id' => 'frmHistoria')); ?>
<?php echo form_hidden('series'); ?>
<?php echo form_hidden('show_mov', 'show'); ?>
<?php echo form_close(); ?>

<script type="text/javascript">
	$(document).ready(function () {

	    $('span.serie').css('cursor', 'pointer');

		$('span.serie').click(function (event) {
			var serie = $(this).text();
			$('input[name="series"]').val(serie);
			$('#frmHistoria').submit();
		});

		if ($("#series").val() != "")
		{
			$("div.cuerpo-formulario").hide();
			$("div.formulario span").toggle();
		}

		$("#boton-reset").click(function(event) {
			//event.preventDefault();
			$("#series").val("");
			$("#series").focus();
		});

		$("table tr").hover(function() {
			$(this).addClass("highlight");
		}, function() {
			$(this).removeClass("highlight");
		});

		$("div.content-header").click(function() {
			$(this).next("div.mostrar-ocultar").slideToggle("fast");
			$(this).children("span.mostrar-ocultar").toggle();
		});

	});

</script>
