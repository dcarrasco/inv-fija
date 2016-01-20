<?php echo form_open(); ?>
<div class="accordion hidden-print">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						<?php echo $this->lang->line('adminbd_exportar_params'); ?>
					</a>
				</div>
			</div>
		</div>

		<div class="panel-collapse collapse in" id="form_param">
			<div class="panel-body">

				<?php echo print_validation_errors(); ?>

				<div class="col-md-4">
					<div class="form-group">
						<label>
							<?php echo $this->lang->line('adminbd_exportar_label_tables'); ?>
						</label>
						<?php echo form_dropdown('tabla', $combo_tablas, $this->input->post('tabla'),'id="select_tabla" size="10" class="form-control"'); ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>
							<?php echo $this->lang->line('adminbd_exportar_label_fields'); ?>
						</label>
						<?php echo form_dropdown('campo', $combo_campos, $this->input->post('campo'),'id="select_campo" size="7" class="form-control"'); ?>

						<label>
							<?php echo $this->lang->line('adminbd_exportar_label_fields_filter'); ?>
						</label>
						<?php echo form_input('filtro', $this->input->post('filtro'),'id="filtro_campo" class="form-control"'); ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="pull-right">
						<button type="submit" name="submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-cloud-download"></span>
							<?php echo $this->lang->line('adminbd_exportar_button_submit'); ?>
						</button>
					</div>

				</div>
			</div>
		</div>

	</div>
</div>
<?php echo form_close(); ?>

<pre>
{result_string}
</pre>

<script language="javascript">

$(document).ready(function() {
	$('#select_tabla').click(function (event) {
		tabla = $('#select_tabla').val();

		$('#select_campo').html('');
		var url_datos = js_base_url + 'adminbd_exportartablas/ajax_campos/' + tabla;
		$.get(url_datos, function (data) {$('#select_campo').html(data); });
	});
});

</script>