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

<form id="frm_aux">
	<input type="hidden" name="id" id="id_id" value="">
	<input type="hidden" name="id_inv" id="id_id_inv" value="">
	<input type="hidden" name="hoja" id="id_hoja" value="">
	<input type="hidden" name="aud" id="id_aud" value="">
	<input type="hidden" name="dig" id="id_dig" value="">
	<input type="hidden" name="ubic" id="id_ubic" value="">
	<!-- <input type="hidden" name="hu" id="id_hu" value=""> -->
	<input type="hidden" name="cat" id="id_cat" value="">
	<input type="hidden" name="desc" id="id_desc" value="">
	<input type="hidden" name="lote" id="id_lote" value="">
	<input type="hidden" name="cen" id="id_cen" value="">
	<input type="hidden" name="alm" id="id_alm" value="">
	<input type="hidden" name="um" id="id_um" value="">
	<input type="hidden" name="ssap" id="id_ssap" value="">
	<input type="hidden" name="sfis" id="id_sfis" value="">
	<input type="hidden" name="obs" id="id_obs" value="">
	<input type="hidden" name="fec" id="id_fec" value="">
	<input type="hidden" name="nvo" id="id_nvo" value="">
</form>

<script type="text/javascript">
	var arrDatos = [],
		arrErrores = [],
		curr = 0,
		cant = 0,
		cantErrores = 0,
		cantProc = 0;

	function proc_linea_carga(objLinea) {
		$('#id_id').val(objLinea.id);
		$('#id_id_inv').val(objLinea.id_inv);
		$('#id_hoja').val(objLinea.hoja);
		$('#id_aud').val(objLinea.aud);
		$('#id_dig').val(objLinea.dig);
		$('#id_ubic').val(objLinea.ubic);
		//$('#id_hu').val(objLinea.hu);
		$('#id_cat').val(objLinea.cat);
		$('#id_desc').val(objLinea.desc);
		$('#id_lote').val(objLinea.lote);
		$('#id_cen').val(objLinea.cen);
		$('#id_alm').val(objLinea.alm);
		$('#id_um').val(objLinea.um);
		$('#id_ssap').val(objLinea.ssap);
		$('#id_sfis').val(objLinea.sfis);
		$('#id_obs').val(objLinea.obs);
		$('#id_fec').val(objLinea.fec);
		$('#id_nvo').val(objLinea.nvo);

		arrDatos.push($('#frm_aux').serialize());
	}

	function procesa_carga() {
		var sdata,
			cantErrores = 0;
		$('#reg_error').text(cantErrores);

		while (arrDatos.length > 0) {
			sdata = arrDatos.shift();
			cantProc += 1;
			if (cantProc == 1) {
				$('#ejecuta_carga').addClass('disabled');
			}
			procesa_carga_linea(sdata);
		}
		arrDatos = arrErrores;
	}

	function procesa_carga_linea(datos_linea) {
		$.ajax({
			type:  "POST",
			url:   js_base_url + "inventario_analisis/inserta_linea_archivo",
			async: true,
			data:  datos_linea,
			success: function(datos) {
				curr += 1;
				var progreso = parseInt(100 * curr / cant) + '%';
				$('div.progress-bar').css('width',progreso);
				$('div.progress-bar').text(progreso);
				$('#reg_actual').text(curr);

				if (curr >= cant) {
					$('#status_progreso1').html('Carga finalizada (' + curr + ' registros cargados)');
				}
			},
			error: function() {
				cantErrores += 1;
				arrErrores.push(datos_linea);
				$('#reg_error').text(cantErrores);
			},
			complete: function() {
				cantProc -= 1;
				if (cantProc == 0) {
					$('#ejecuta_carga').removeClass('disabled');
				}
			},
		});
	}
</script>


<script type="text/javascript">
$(document).ready(function() {

	$('#ejecuta_carga').click(function (event) {
		event.preventDefault();
		//$('div.progress-bar').css('width', '0%');
		while (arrDatos.length > 0) {
			procesa_carga();
		}
		if (arrDatos.length == 0)
		{
			$('#ejecuta_carga').addClass('disabled');
		}
	})

	<?php echo $script_carga; ?>
	cant = arrDatos.length;

//$('#barra_progreso').text('Proceso finalizado.');
});
</script>
