<div class="msg-alerta cf ac">
	<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2 well">
		<?php echo form_open_multipart('analisis/sube_stock', 'class="form-horizontal"'); ?>
		<?php echo form_hidden('formulario','upload'); ?>

		<div class="control-group">
			<label class="control-label">
				Inventario
			</label>
			<div class="controls">
				<span class="input-xlarge uneditable-input"><?php echo $inventario_id . ' - ' . $inventario_nombre; ?></span>
			</div>
		</div>

		<?php if (!$show_script_carga): ?>
		<div class="control-group">
			<label class="control-label">
				Archivo
			</label>
			<div class="controls">
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<p><strong>ADVERTENCIA</strong></p>
					<p>Al subir un archivo se eliminar <strong>TODOS</strong> los registros asociados al inventario
					"<?php echo $inventario_nombre?>"</p>
				</div>
				<?php echo form_upload('upload_file','','class="input-large"'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				Clave Administrador
			</label>
			<div class="controls">
				<?php echo form_password('password'); ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ($show_script_carga): ?>
		<div class="control-group">
			<label class="control-label">
				Progreso
			</label>
			<div class="controls">
				<?php echo $upload_error; ?>
				<?php echo $msj_error; ?>
				<div id="progreso_carga">
					<div class="progress">
						<div class="bar" style="width: 0%;"></div>
					</div>
					<div id="status_progreso1">Registros cargados OK: <span id="reg_actual">0</span> de <?php echo ($regs_OK); ?></div>
					<div id="status_progreso2">Registros con error: <span id="reg_error">0</span></div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div class="control-group">
			<div class="controls">
				<?php if ($show_script_carga): ?>
					<button class="btn btn-primary" id="ejecuta_carga">
						<i class="glyphicon glyphicon-play"></i>
						Ejecutar carga
					</button>
				<?php else: ?>
				<button type="submit" name="submit" class="btn btn-primary" id="btn_guardar">
					<i class="glyphicon glyphicon-upload"></i>
					Subir archivo
				</button>
				<?php endif; ?>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
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
	var arr_datos = [];
	var arr_ok    = [];
	var arr_error = [];
	var curr = 0;
	var cerr = 0;
	var cant = 0;
	var cproc = 0;

	// hu entre ubic y cat
	function proc_linea_carga(count, id, id_inv, hoja, aud, dig, ubic, cat, desc, lote, cen, alm, um, ssap, sfis, obs, fec, nvo) {
		$('#id_id').val(id);
		$('#id_id_inv').val(id_inv);
		$('#id_hoja').val(hoja);
		$('#id_aud').val(aud);
		$('#id_dig').val(dig);
		$('#id_ubic').val(ubic);
		//$('#id_hu').val(hu);
		$('#id_cat').val(cat);
		$('#id_desc').val(desc);
		$('#id_lote').val(lote);
		$('#id_cen').val(cen);
		$('#id_alm').val(alm);
		$('#id_um').val(um);
		$('#id_ssap').val(ssap);
		$('#id_sfis').val(sfis);
		$('#id_obs').val(obs);
		$('#id_fec').val(fec);
		$('#id_nvo').val(nvo);

		arr_datos.push($('#frm_aux').serialize());
	}

	function procesa_carga() {
		var sdata;
		cerr = 0;
		$('#reg_error').text(cerr);

		while (arr_datos.length > 0) {
			sdata = arr_datos.shift();
			cproc += 1;
			if (cproc == 1) {
				$('#ejecuta_carga').hide();
			}
			$.ajax({
				type:  "POST",
				url:   js_base_url + "analisis/inserta_linea_archivo",
				async: true,
				data:  sdata,
				success: function(datos) {
					curr += 1;
					var progreso = parseInt(100 * curr / cant) + '%';
					$('div.bar').css('width',progreso);
					$('#reg_actual').text(curr);

					if (curr >= cant) {
						$('#status_progreso1').html('Carga finalizada (' + curr + ' registros cargados)');
					}
				},
				error: function() {
					cerr += 1;
					arr_error.push(data);
					$('#reg_error').text(cerr);
				},
				complete: function() {
					cproc -= 1;
					if (cproc == 0) {
						$('#ejecuta_carga').show();
					}
				},
			});
		}
		arr_datos = arr_error;
	}
</script>


<script type="text/javascript">
$(document).ready(function() {

	$('#ejecuta_carga').click(function (event) {
		event.preventDefault();
		$('div.bar').css('width', '0%');
		ejecuta_carga();
	})

	function ejecuta_carga() {
		while (arr_datos.length > 0) {
			procesa_carga();
		}
	};

	<?php echo $script_carga; ?>
	cant = arr_datos.length

//$('#barra_progreso').text('Proceso finalizado.');
});
</script>
