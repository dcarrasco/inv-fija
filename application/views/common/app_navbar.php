<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">
				{{ titulo_modulo }}
			</a>

			<button class="navbar-toggle" data-toggle="collapse" data-target=".navMenuCollapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>


		<div class="collapse navbar-collapse navMenuCollapse">
			<ul class="nav navbar-nav navbar-right">
				{% for menu in navbar_menu %}
				<li class="dropdown {{menu.selected}}">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<span class="{{menu.icono}}"></span> {{menu.app}} <b class="caret"></b>
					</a>

					<ul class="dropdown-menu">
						{% for modulo in menu.modulos %}
						<li class="{{modulo.modulo_selected}}">
							<a href="{{modulo.modulo_url}}">
								<span class="{{modulo.modulo_icono}}"></span> {{modulo.modulo_nombre}}
							</a>
						</li>
						{% endfor %}
					</ul>
				</li>
				{% endfor %}

				<li>
					<a href="{{logout_url}}">
						<span class="glyphicon glyphicon-off"></span> Logout {{user_firstname}}
					</a>
				</li>

			</ul>
		</div>
	</div>
</nav> <!-- NAVBAR-->
