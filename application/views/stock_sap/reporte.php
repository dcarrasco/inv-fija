<div class="accordion">
	<?php echo form_open('','id="frm_param"'); ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-4">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						Parametros consulta
					</a>

				</div>
				<div class="col-md-4">
					Fecha reporte: <?php echo $fecha_reporte; ?>
				</div>
			</div>
		</div>

		<div class="panel-body collapse in" id="form_param">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-md-3">
						<div>
							<strong>Seleccionar Almacenes</strong>
						</div>
						<div class="radio-inline">
							<?php echo form_radio('tipo_op', 'MOVIL', set_radio('tipo_op','MOVIL', TRUE), 'id="tipo_op_movil"') ?>
							Movil
						</div>
						<div class="radio-inline">
							<?php echo form_radio('tipo_op', 'FIJA', set_radio('tipo_op','FIJA'), 'id="tipo_op_fija"') ?>
							Fija
						</div>
						<div>
							<?php echo form_multiselect('tipo_alm[]', $combo_tipo_alm, $this->input->post('tipo_alm'), 'size="10" class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div>
							<strong>Seleccionar Estado Stock</strong>
						</div>
						<div>
							<?php echo form_multiselect('estado_sap[]', $combo_estado_sap, $this->input->post('estado_sap'), 'size="10" class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div>
							<strong>Seleccionar Tipos Material</strong>
						</div>
						<div>
							<?php echo form_multiselect('tipo_mat[]', $combo_tipo_mat, $this->input->post('tipo_mat'), 'size="10" class="form-control"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div>
							<strong>Detalle</strong>
						</div>
						<div>
							<div class="checkbox">
								<?php echo form_checkbox('incl_almacen', '1', set_checkbox('incl_almacen','1', FALSE), 'id="incl_almacen"') ?>
								Mostrar almacenes
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('incl_lote', '1', set_checkbox('incl_lote','1', FALSE), 'id="incl_lote"') ?>
								Mostrar lotes
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('incl_modelos', '1', set_checkbox('incl_modelos','1', FALSE), 'id="incl_modelos"') ?>
								Mostrar modelos equipos
							</div>
						</div>

						<hr/>

						<div class="pull-right">
							<button type="submit" class="btn btn-primary">
								<i class="glyphicon glyphicon-list-alt"></i>
								Reporte
							</button>
						</div>
					</div>

				</div>
			</div>
		</div>

		<?php echo form_hidden('order_by', set_value('order_by','')); ?>
		<?php echo form_hidden('order_sort', set_value('order_sort','')); ?>
		<?php echo form_close(); ?>
	</div>
</div>

<div class="content-module-main">
	<?php echo $reporte; ?>
</div> <!-- fin content-module-main -->

<script type="text/javascript">
$(document).ready(function() {
	$('#filtrar_material').keyup(function (event) {
		$('tr.not_found').show();
		$('tr.not_found').removeClass('not_found');

		var a_buscar = $('#filtrar_material').val().toUpperCase();
		if (a_buscar != '') {
			$('div.content-module-main tr').each(function() {
				var nodo_texto = $(this).children('td:eq(1)');
				if (nodo_texto.size() > 0) {
					if (nodo_texto.html().toUpperCase().indexOf(a_buscar) == -1) {
						$(this).addClass('not_found');
					}
				}
			});
			$('tr.not_found').hide();
			$('#filtrar_material').addClass('search_found');
		} else {
			$('#filtrar_material').removeClass('search_found');
		}
	});

	$('table th a').click(function (event) {
		event.preventDefault();
		$('form#frm_param input[name="order_by"]').val($(this).attr('order_by'));
		$('form#frm_param input[name="order_sort"]').val($(this).attr('order_sort'));
		$('#frm_param').submit();
	});

	$('div.content-module-main td a').click(function (event) {
		event.preventDefault();
		$('form').attr('action', ($(this).attr('href')));
		$('form input[name="order_by"]').val('');
		$('form input[name="order_sort"]').val('');
		$('form').submit();
	});


	$('td.subtotal[colspan]').parent().each(function(i) {
		if ($(this).size() == 1) {
			if ($(this).children().html() != '&nbsp;') {
				$(this).children(':first').html('<span>[-]</span>' + $(this).children(':first').html());
				$(this).addClass('tr_subtotal_' + i);
				$(this).children().addClass('tr_subtotal');
			}
		}
	});

	$('tr[class^="tr_subtotal_"]').each(function() {
		var clase = $(this).attr('class');
		$(this).nextUntil('tr[class^="tr_subtotal_"]').addClass(clase + '_data');
	})

	$('td.tr_subtotal').click(function() {
		var clase = $(this).parent().attr('class');
		var txt_expand = ($(this).parent().next().is(':visible')) ? '[+]' : '[-]';
		$(this).children('span').html(txt_expand);
		$('tr.' + clase + '_data').toggle();
	});



});
</script>