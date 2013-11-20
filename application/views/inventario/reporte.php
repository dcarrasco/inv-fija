<div class="row">
	<?php echo form_open('','id="frm_param" class="form-search"'); ?>
	<div class="col-md-3">
		<div class="input-group input-group-sm">
			<?php echo form_input('filtrar_material', set_value('filtrar_material'), 'class="form-control" id="filtrar_material" placeholder="Texto a filtrar..." onKeyPress="return event.keyCode!=13"'); ?>
			<span class="input-group-btn">
				<button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i></button>
			</span>
		</div>
	</div>

	<div class="col-md-4 col-md-offset-1">
		<div>
			<div class="checkbox-inline">
				<?php echo form_checkbox('elim_sin_dif', '1',set_checkbox('elim_sin_dif','1', FALSE), 'id="elim_sin_dif"'); ?>
				Ocultar registros sin diferencias
			</div>
		</div>
		<div>
			<div class="checkbox-inline">
				<?php echo form_checkbox('incl_ajustes', '1',set_checkbox('incl_ajustes','1', FALSE), 'id="incl_ajustes"'); ?>
				Incluir ajustes de inventario
			</div>
		</div>
		<div>
			<div class="checkbox-inline">
				<?php echo form_checkbox('incl_familias', '1',set_checkbox('incl_familias','1', FALSE), 'id="incl_familias"'); ?>
				Incluir familias de productos
			</div>
		</div>
	</div>

	<div class="col-md-4">
		Inventario:
		<?php echo form_dropdown('inv_activo', $combo_inventarios, $id_inventario, 'id="sel_inv_activo" class="form-control input-sm"'); ?>

	</div>
	<?php echo form_hidden('order_by', set_value('order_by','')); ?>
	<?php echo form_hidden('order_sort', set_value('order_sort','')); ?>
	<?php echo form_close(); ?>
</div>

<div>
	<?php echo $reporte; ?>
</div> <!-- fin content-module-main -->


</div> <!-- fin content-module -->

<script type="text/javascript">
$(document).ready(function() {
	$('#sel_inv_activo').change(function () {
		$('form').submit();
	});

	$('#incl_ajustes').change(function () {
		$('form').submit();
	});

	$('#incl_familias').change(function () {
		$('form').submit();
	});

	$('#elim_sin_dif').change(function () {
		$('form').submit();
	});

	$('#filtrar_material').keyup(function (event) {
		$('tr.not_found').show();
		$('tr.not_found').removeClass('not_found');

		var a_buscar = $('#filtrar_material').val().toUpperCase();
		if (a_buscar != '') {
			$('table tr').each(function() {
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
		$('form input[name="order_by"]').val($(this).attr('order_by'));
		$('form input[name="order_sort"]').val($(this).attr('order_sort'));
		$('form').submit();
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
			if ($(this).children(':eq(1)').html() != '&nbsp;') {
				$(this).children(':eq(1)').html('<span>[-]</span>' + $(this).children(':eq(1)').html());
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