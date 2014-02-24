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
						<div class="checkbox">
							<?php echo form_checkbox('ult_mov', 'show', set_value('ult_mov'))?>
							Filtrar ultimo movimiento
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
				</tr>
			<?php foreach($log_serie as $reg_log): ?>
				<tr>
					<td><small><?php echo $reg_log['id_log_deco_tarjeta'] ?></small></td>
					<td><small><?php echo $reg_log['fecha_log'] ?></small></td>
					<td><small><?php echo $reg_log['serie_deco'] . $this->log_gestor_model->dv_serie_deco($reg_log['serie_deco']) ?></small></td>
					<td><small><?php echo $reg_log['serie_tarjeta'] ?></small></td>
					<td><small><?php echo $reg_log['peticion'] ?></small></td>
					<td><small><?php echo $reg_log['estado'] ?></small></td>
					<td><small><?php echo $reg_log['tipo_operacion_cas'] ?></small></td>
					<td><small><?php echo $reg_log['area'] . '-' . $reg_log['telefono']?></small></td>
					<td><small><?php echo $reg_log['rut'] . '-' . $reg_log['rut_dv'] ?></small></td>
				</tr>
			<?php endforeach; ?>
			<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>



<script type="text/javascript">
	$(document).ready(function() {
		if ($("#series").val() != "")
		{
			$("div.cuerpo-formulario").hide();
			$("div.formulario span").toggle();
		}

		$("#boton-reset").click(function(event) {
			//event.preventDefault();
			$("#series").val("");
			$("#series").focus();
		})

		$("table tr").hover(function() {
			$(this).addClass("highlight");
		}, function() {
			$(this).removeClass("highlight");
		})

		$("div.content-header").click(function() {
			$(this).next("div.mostrar-ocultar").slideToggle("fast");
			$(this).children("span.mostrar-ocultar").toggle();
		})

	});
</script>
