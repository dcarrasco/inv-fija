<div class="row">
	<div class="col-md-10 col-md-offset-1 well">
		<fieldset>

			<legend>{_inventario_upload_label_fieldset_}</legend>

			{validation_errors}

			<div class="form-group">
				<label class="control-label col-sm-4">{_inventario_upload_label_inventario_}</label>
				<div class="col-sm-8">
					<p class="form-control-static">{inventario_id} - {inventario_nombre}</p>
					<?= print_message('{_inventario_upload_warning_line2_} "{inventario_nombre}".'	, 'warning') ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">{_inventario_upload_label_progress_}</label>
				<div class="col-sm-8">
					{msj_error}
					<div id="progreso_carga">
						<div class="progress">
							<div class="progress-bar" role="progressbar" style="width: 0%;"></div>
						</div>
						<div id="status_progreso1">
							{_inventario_upload_status_OK_}
							<span id="reg_actual">0</span> / {regs_ok}
						</div>
						<div id="status_progreso2">
							{_inventario_upload_status_error_}
							<span id="reg_error">0</span>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">
				</label>
				<div class="col-sm-8">
					<button class="btn btn-primary pull-right" id="ejecuta_carga">
						<span class="fa fa-play"></span>
						{_inventario_upload_button_load_}
					</button>
				</div>
			</div>

		</fieldset>
	</div>
</div>

<script type="text/javascript">
	var subeStock = {
		arrDatos    : [],
		arrErrores  : [],
		curr        : 0,
		cant        : 0,
		cantErrores : 0,
		cantProc    : 0,

		proc_linea_carga: function(objLinea) {
			this.arrDatos.push(objLinea);
			this.cant = this.arrDatos.length;
		},

		procesa_carga: function() {
			var sData;
			this.cantErrores = 0;
			$('#reg_error').text(this.cantErrores);

			while (this.arrDatos.length > 0) {
				sData = this.arrDatos.shift();
				this.cantProc += 1;
				if (this.cantProc == 1) {
					$('#ejecuta_carga').addClass('disabled');
				}
				this.procesa_carga_linea(sData);
			}
			this.arrDatos = this.arrErrores;
		},

		procesa_carga_linea: function(datosLinea) {
			var _this = this;
			$.ajax({
				type:  "POST",
				url:   js_base_url + "inventario_analisis/inserta_linea_archivo",
				async: true,
				data:  datosLinea,
				success: function(datos) {
					_this.curr += 1;
					var progreso = parseInt(100 * _this.curr / _this.cant) + '%';
					$('div.progress-bar').css('width',progreso);
					$('div.progress-bar').text(progreso);
					$('#reg_actual').text(_this.curr);

					if (_this.curr >= _this.cant) {
						$('#status_progreso1').html('Carga finalizada (' + _this.curr + ' registros cargados)');
					}
				},
				error: function() {
					_this.cantErrores += 1;
					_this.arrErrores.push(datosLinea);
					$('#reg_error').text(_this.cantErrores);
				},
				complete: function() {
					_this.cantProc -= 1;
					if (_this.cantProc == 0) {
						$('#ejecuta_carga').removeClass('disabled');
					}
				},
			});
		},

		setCant: function() {
			this.cant = this.arrDatos.length;
		},

		getCantDatos: function() {
			return this.arrDatos.length;
		},
	}



</script>


<script type="text/javascript">
$(document).ready(function() {

	$('#ejecuta_carga').click(function (event) {
		event.preventDefault();
		while (subeStock.getCantDatos() > 0) {
			subeStock.procesa_carga();
		}
		if (subeStock.getCantDatos() == 0) {
			$('#ejecuta_carga').addClass('disabled');
		}
	})

	{script_carga}

});
</script>
