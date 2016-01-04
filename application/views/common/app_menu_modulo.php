<ul class="nav nav-tabs hidden-print">
	{% for menu in menu_modulo %}
	<li class="{{menu.menu_selected}}"><a href="{{menu.menu_url}}">{{menu.menu_nombre}}</a></li>
	{% endfor %}
</ul>