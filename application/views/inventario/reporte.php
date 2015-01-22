<div class="row">
	<?php echo form_open('','id="frm_param" class="form-search"'); ?>
	<div class="col-md-3">
		<div class="input-group input-group-sm">
			<?php echo form_input('filtrar_material', set_value('filtrar_material'), 'class="form-control" id="filtrar_material" placeholder="'. $this->lang->line('inventario_report_filter') .'" onKeyPress="return event.keyCode!=13"'); ?>
			<span class="input-group-btn">
				<button type="submit" class="btn btn-default" id="btn_filtrar"><span class="glyphicon glyphicon-search"></span></button>
			</span>
		</div>
	</div>

	<div class="col-md-4 col-md-offset-1">
		<div>
			<div class="checkbox-inline">
				<?php echo form_checkbox('elim_sin_dif', '1',set_checkbox('elim_sin_dif','1', FALSE), 'id="elim_sin_dif"'); ?>
				<?php echo $this->lang->line('inventario_report_check_ocultar_regs'); ?>
			</div>
		</div>
		<div>
			<div class="checkbox-inline">
				<?php echo form_checkbox('incl_ajustes', '1',set_checkbox('incl_ajustes','1', FALSE), 'id="incl_ajustes"'); ?>
				<?php echo $this->lang->line('inventario_report_check_incluir_ajustes'); ?>
			</div>
		</div>
		<div>
			<div class="checkbox-inline">
				<?php echo form_checkbox('incl_familias', '1',set_checkbox('incl_familias','1', FALSE), 'id="incl_familias"'); ?>
				<?php echo $this->lang->line('inventario_report_check_incluir_familias'); ?>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<?php echo $this->lang->line('inventario_report_label_inventario'); ?>
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

	$('[data-toggle="tooltip"]').tooltip();

	$('#filtrar_material').keyup(function (event) {
		var a_buscar = $('#filtrar_material').val().toUpperCase();

		if (a_buscar.length > 2) {
			$('tr.not_found').show();
			$('tr.not_found').removeClass('not_found');
			$('table.reporte tr').each(function() {
				var nodo_texto1 = $(this).children('td:eq(1)'),
					nodo_texto2 = $(this).children('td:eq(2)'),
					nodo_texto;

				if (nodo_texto1.size() > 0 || nodo_texto2.size() > 0) {
					nodo_texto = nodo_texto1.html() + nodo_texto2.html();
					if (nodo_texto.toUpperCase().indexOf(a_buscar) == -1) {
						$(this).addClass('not_found');
					}
				}
			});
			$('tr.not_found').hide();
			$('#filtrar_material').addClass('search_found');
			$('#btn_filtrar').removeClass('btn-default').addClass('btn-primary');
		} else {
			$('#filtrar_material').removeClass('search_found');
			$('#btn_filtrar').removeClass('btn-primary').addClass('btn-default');
			$('tr.not_found').show();
			$('tr.not_found').removeClass('not_found');
		}
	});

});
</script>
