<ul class="nav nav-tabs hidden-print">
	<?php foreach($menu_modulo['menu'] as $modulo => $val): ?>
		<li class="<?php echo ($modulo == $menu_modulo['mod_selected']) ? 'active' : ''; ?>">
			<?php echo anchor($val['url'], $val['texto']); ?>
		</li>
	<?php endforeach; ?>
</ul>
