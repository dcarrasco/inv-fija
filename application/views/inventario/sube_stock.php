<div class="msg-alerta cf ac">
	<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1 well">
		<?php echo form_open_multipart($this->uri->segment(1) . '/sube_stock', 'class="form-horizontal" role="form"'); ?>
		<?php echo form_hidden('formulario','upload'); ?>

		<div class="form-group">
			<label class="control-label col-sm-3">
				Inventario
			</label>
			<div class="col-sm-9">
				<p class="form-control-static"><?php echo $inventario_id . ' - ' . $inventario_nombre; ?></p>
			</div>
		</div>

		<?php if (!$show_script_carga): ?>
		<div class="form-group">
			<label class="control-label col-sm-3">
				Archivo
			</label>
			<div class="col-sm-9">
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<p><strong>ADVERTENCIA</strong></p>
					<p>Al subir un archivo se eliminar <strong>TODOS</strong> los registros asociados al inventario
					"<?php echo $inventario_nombre?>"</p>
				</div>
				<?php echo form_upload('upload_file','','class="form-control"'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				Clave Administrador
			</label>
			<div class="col-sm-4">
				<?php echo form_password('password', '', 'class="form-control"'); ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ($show_script_carga): ?>
		<div class="form-group">
			<label class="control-label col-sm-3">
				Progreso
			</label>
			<div class="col-sm-9">
				<?php echo $upload_error; ?>
				<?php echo $msj_error; ?>
				<div id="progreso_carga">
					<div class="progress">
						<div class="progress-bar" role="progressbar" style="width: 0%;"></div>
					</div>
					<div id="status_progreso1">Registros cargados OK: <span id="reg_actual">0</span> de <?php echo ($regs_OK); ?></div>
					<div id="status_progreso2">Registros con error: <span id="reg_error">0</span></div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div class="form-group">
			<label class="control-label col-sm-3">
			</label>
			<div class="col-sm-9">
				<?php if ($show_script_carga): ?>
					<button class="btn btn-primary pull-right" id="ejecuta_carga">
						<span class="glyphicon glyphicon-play"></span>
						Ejecutar carga
					</button>
				<?php else: ?>
				<button type="submit" name="submit" class="btn btn-primary pull-right" id="btn_guardar">
					<span class="glyphicon glyphicon-upload"></span>
					Subir archivo
				</button>
				<?php endif; ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
			</label>
			<div class="col-sm-9">
				<pre>
Formato del archivo:
	Archivo de texto
	Extension .txt
	Campos separados por tabulación

	Campos
			Ubicacion
			[HU - eliminada]
			Catalogo
			Descripcion catalogo
			Lote
			Centro
			Almacen
			Unidad de medida
			Stock SAP
			Hoja
				</pre>
			</div>
		</div>
		<?php echo form_close(); ?>
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

	<?php echo $script_carga; ?>

});
</script>
