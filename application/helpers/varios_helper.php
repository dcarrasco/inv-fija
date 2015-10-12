<?php

if ( ! function_exists('dbg'))
{
	/**
	 * Debug de varianbles
	 *
	 * @param  mixed $item Elemento a revisar
	 * @return void
	 */
	function dbg($item)
	{
		//echo '<pre style="font-family: courier, font-size: 8px">';
		//$dump = print_r($item, TRUE);
		//echo gettype($item) . ' : ' . $dump;
		//echo '</pre><hr>';
		ini_set('xdebug.var_display_max_depth', '6');
		var_dump($item);
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('dbg_die'))
{
	/**
	 * Debug de variables y para la ejecución del programa
	 *
	 * @param  mixed $item Elemento a revisar
	 * @return void
	 */
	function dd($item)
	{
		dbg($item);
		die();
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('app_render_view'))
{
	/**
	 * Render vista
	 *
	 * @param  string $vista    Nombre de la vista a dibujar
	 * @param  array  $datos    Arreglo con parámetros de datos a dibujar
	 * @param  array  $arr_menu Arreglo con submenu (en caso que el módulo tenga submenu)
	 * @return void
	 */
	function app_render_view($vista = null, $datos = array(), $arr_menu = array())
	{
		if (! $vista)
		{
			return;
		}

		// carga objeto global CI
		$CI =& get_instance();

		if (count($arr_menu) > 0)
		{
			$datos['menu_modulo'] = array('menu' => $arr_menu, 'mod_selected' => basename($vista));
		}

		$datos['msg_alerta']  = $CI->session->flashdata('msg_alerta');

		$CI->load->view('app_header', $datos);
		$CI->load->view($vista, $datos);
		$CI->load->view('app_footer', $datos);
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('form_array_format'))
{
	/**
	 * Formatea un arreglo para que sea usado en un formuario select
	 * Espera que el arreglo tenga a lo menos las llaves "llave" y "valor"
	 *
	 * @param  array  $arr     Arreglo a transformar
	 * @param  string $msg_ini Elemento inicial a desplegar en select
	 * @return array           Arreglo con formato a utilizar
	 */
	function form_array_format($arr = array(), $msg_ini = '')
	{
		$arr_combo = array();

		if ($msg_ini != '')
		{
			$arr_combo[''] = $msg_ini;
		}

		foreach($arr as $reg)
		{
			$arr_combo[$reg['llave']] = $reg['valor'];
		}

		return $arr_combo;
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('form_has_error'))
{
	/**
	 * Indica si el elemento del formulario tiene un error de validación
	 *
	 * @param  string $form_field Nombre del elemento del formulario
	 * @return bool               Indicador de error del elemento
	 */
	function form_has_error($form_field = '')
	{
		return (bool) (form_error($form_field) != '');
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('fmt_cantidad'))
{
	/**
	 * Formatea cantidades numéricas con separador decimal y de miles
	 *
	 * @param  integer $valor        Valor a formatear
	 * @param  integer $decimales    Cantidad de decimales a mostrar
	 * @param  boolean $mostrar_cero Indica si muestra o no valores ceros
	 * @return string                Valor formateado
	 */
	function fmt_cantidad($valor = 0, $decimales = 0, $mostrar_cero = false)
	{
		$cero = $mostrar_cero ? '0' : ' ';

		return ($valor == 0) ? $cero : number_format($valor, $decimales, ',', '.');
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('fmt_monto'))
{
	/**
	 * Formatea cantidades numéricas como un monto
	 *
	 * @param  integer $monto        Valor a formatear
	 * @param  string  $unidad       Unidad a desplegar
	 * @param  string  $signo_moneda Simbolo monetario
	 * @param  integer $decimales    Cantidad de decimales a mostrar
	 * @return string                Monto formateado
	 */
	function fmt_monto($monto = 0, $unidad = 'UN', $signo_moneda = '$', $decimales = 0)
	{
		if ($monto == 0)
		{
			return ' ';
		}
		else
		{
			if (strtoupper($unidad) == 'UN')
			{
				return $signo_moneda . '&nbsp;' . number_format($monto, $decimales, ',', '.');
			}
			elseif (strtoupper($unidad) == 'MM')
			{
				return 'MM' . $signo_moneda . '&nbsp;' . number_format($monto/1000000, ($monto > 10000000) ? 0 : 1, ',', '.');
			}
		}
	}

// --------------------------------------------------------------------

if ( ! function_exists('fmt_hora'))
{
	/**
	 * Devuelve una cantidad de segundos como una hora
	 *
	 * @param  integer $seg Cantidad de segundos a formatear
	 * @return string       Segundos formateados como hora
	 */
	function fmt_hora($seg = 0)
	{
		$sep = ":";

		$hh = (int) ($seg/3600);
		$hh = (strlen($hh) == 1) ? '0' . $hh : $hh;

		$mm = (int) (($seg - ((int) $hh) *3600)/60);
		$mm = (strlen($mm) == 1) ? '0' . $mm : $mm;

		$ss = (int) ($seg - ($hh*3600 + $mm*60));
		$ss = (strlen($ss) == 1) ? '0' . $ss : $ss;

		return $hh.$sep.$mm.$sep.$ss;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('genera_captcha_word'))
{
	/**
	 * Devuelve una palabra aleatoria para ser usada en el captcha
	 *
	 * @return string Palabra aleatoria
	 */
	function genera_captcha_word()
	{
		$diccionario = array(
			'abiertas','abiertos','aborrece','abrasada','abrazado','abrazara','abreviar','abriendo','abririan','abrumado',
			'acabadas','acaballa','acabando','acabaras','acabaron','acabasen','acciones','aceitera','acertada','acertado',
			'acertare','achaques','acogerse','acometer','acometia','acomodar','aconsejo','acontece','acordaba','acordado',
			'acordara','acosados','acostado','acuerdan','acurruco','adelante','adelanto','adivinar','admirada','admirado',
			'admitido','adversas','advertid','advertir','advierta','advierte','afanando','afirmaba','afligido','aforrado',
			'agravios','aguardar','aguardes','agudezas','agujeros','alabanza','alagones','alargaba','albanega','albañil',
			'alboroto','alborozo','alcaidia','alcanzar','alcarria','alcurnia','alevosia','alevosos','alforjas','alimenta',
			'aliviado','almendra','almohada','alojaban','alojemos','alquiler','amabades','amanecer','ambrosio','amenazas',
			'amorosas','amorosos','anchuras','andantes','angelica','angustia','aniquile','anteojos','anterior','antiguas',
			'antiguos','antojase','apacible','apaleado','aparejos','apartado','apartate','aparteme','apellido','aporrear',
			'aposento','apostare','apretaba','apuntado','aquellas','aquellos','arabigos','araucana','arcalaus','archivos',
			'ardiendo','ardiente','arenques','argamasa','armadura','arrieros','arrimada','arrimado','arrojaba','arrojado',
			'arrullar','asaduras','asentaba','asimesmo','asimismo','asomaron','astillas','atencion','atenuado','aterrada',
			'atrancar','atrevido','atribuia','aturdido','ausencia','ausentar','aventura','avisarte','ayudaran','ayudarme',
			'ayudaron','azoguejo','azpeitia','azumbres','balcones','baldones','ballesta','banderas','baronias','barrabas',
			'bastante','bastardo','batallas','bañadas','bbaladro','bebieron','belianis','bellezas','bellotas','bendecia',
			'bernardo','berrocal','bizmalle','bodoques','borrasca','bretaña','brevedad','brumadas','bucefalo','burlaron',
			'buscando','buscaros','buscasen','caballos','cabellos','cabestro','cabreros','callando','calzones','caminaba',
			'caminase','campaña','cansados','cansarse','cantando','cantidad','capitulo','capparum','cardenal','cargaban',
			'cargaron','carneros','carreras','cartones','cartujos','castigar','castilla','castillo','catolico','ceguedad',
			'celillos','cerrados','cerraron','ceñirle','chicoria','chimenea','ciencias','cierrese','cipiones','ciudades',
			'claridad','clarines','claustro','clerigos','cobardes','cobraria','cocinero','cofradia','cogiendo','colerico',
			'colgados','colocado','coloquio','columbro','comenzar','cometera','cometido','comiendo','comienza','comienzo',
			'comieron','comiesen','compadre','comparar','competir','componer','comunico','concebia','concedia','concerto',
			'concluya','concluye','concluyo','condenar','confesar','confiado','confiesa','confiese','confieso','confirmo',
			'conforme','confusas','conocera','conocian','conocida','conocido','consejas','consejos','conserve','conservo',
			'consolar','contadas','contando','contarla','contarle','contarlo','contarse','conteian','contenia','contenta',
			'contento','contiene','continua','contorno','contrato','convenia','conventa','conviene','corbetas','corcovos',
			'corellas','coronado','corredor','correrse','corridos','corrillo','cortaria','cortesia','cortezas','cosechas',
			'coselete','costilla','coyundas','crecidos','creditto','creyendo','criatura','crueldad','cuarenta','cuartana',
			'cubierta','cubierto','cubrirse','cuentalo','cuentase','cumplais','cumplido','cumplira','curiosos','dadselos',
			'darasela','debiendo','debieron','decirles','defender','defienda','dejarles','deleitar','delicado','demasias',
			'denantes','denostar','deparase','deposito','derechas','derechos','derribar','derrumba','desafios','desatino',
			'descalza','descanso','descargo','describe','descubra','descubre','descuido','desdenes','desdicha','deseaban',
			'deseamos','deseosos','desfacer','desfecho','desgajar','deshacer','deshecho','deshoras','designio','desigual',
			'desistir','desmayos','desnudas','desoluto','despacio','despaico','despecho','despedir','despensa','desperto',
			'despeña','despidio','despojar','despojos','destreza','desvario','desviado','desviase','deteneos','devaneos','
			devengar','devocion','diamante','dichosas','dichosos','diciendo','dieronle','dieronse','dilatelo','discreta',
			'discreto','disculpo','discurso','disponga','diversas','diversos','doliendo','doliente','doloroso','domeñar',
			'domingos','donacion','doncella','dormirla','dosiapor','dulcinea','durables','ejemplos','ejercito','eleccion',
			'embajada','embarazo','embestir','embistio','embustes','empapado','empinaba','empleada','empresas','enamoran',
			'enarbolo','encajada','encamino','encantan','encanten','encender','encendia','encendio','encierra','encubria',
			'encubrio','endechas','enemigos','enfadosa','enfrasco','enfriase','engañan','engañar','engendro','enjalmas',
			'enmendar','enmienda','enojadas','enojados','enristro','ensalada','ensillar','entender','entendio','enterado',
			'enterrar','entiende','entiendo','entierro','entonces','entraran','entraron','entregar','enviarle','envidiar',
			'envilece','epitafio','erizados','erizaron','escalera','escamosa','escogido','escopeta','escriben','escribia',
			'escribio','escribir','escritas','escritos','escuchar','escudero','escusado','esfuerzo','esgrimir','espaldar',
			'espaldas','español','especial','esperaba','esperame','espesura','espinazo','espiritu','espolear','espuelas',
			'esquivas','estabase','estancia','estimada','estomago','estorbar','estraña','estraño','estrecha','estrecho',
			'estrella','estribos','estudios','estuvose','excusado','extender','extendio','extiende','extraña','extraño',
			'faltaran','faltarle','faltaron','faltoles','familias','fantasia','fantasma','fatigaba','fatigado','favorece',
			'fazañas','fermosas','fiadores','ficieron','finisima','finisimo','flamante','flaqueza','follones','formaban',
			'fortunas','forzadas','frontero','fuesemos','gallarda','gallardo','ganadero','ganancia','garabato','generoso',
			'gentiles','gigantes','gobernar','gobierno','graciosa','gracioso','graduado','grandeza','gravedad','groseras',
			'guadiana','guardaba','guardado','guardare','guardese','guijarro','gustando','guzmanes','haberles','habiales',
			'habiendo','hablando','hablarle','habremos','hacednos','hacernos','hacienda','haciendo','haldudos','hallaban',
			'hallaren','hallaria','hallarla','hallarle','hallarme','hallaron','hallaros','hallarse','hallasen','hallazgo',
			'hazañas','hercules','heredado','herirles','hermanas','hermanos','hermosas','hermosos','heroicas','herrados',
			'hicieran','hicieron','hiciesen','hidalgos','hipolito','hircania','historia','holandas','holgaron','holgarse',
			'hombruna','homicida','honestos','honrados','honrarle','horadara','horrendo','hubieran','hubieras','hubieron',
			'hubiesen','humedece','humildad','ignorada','ilustres','imagenes','imaginar','imitando','imprimio','incendio',
			'incitado','indicios','infantes','infernal','infierno','infinita','infundio','infundir','ingenios','injurias',
			'injustos','insignia','instante','instinto','intactas','intitula','inutiles','invierno','italiano','justicia',
			'labrador','ladearme','ladrones','lagrimas','lamentos','lampazos','legitima','legitimo','lenguaje','lentejas',
			'levadizo','levantar','libertad','libraria','librarle','libreria','licencia','ligadura','ligereza','limitada',
			'limpiado','limpieza','lindezas','livianas','llamamos','llamando','llamaran','llamaria','llamarla','llamarle',
			'llamarme','llamaron','llamarse','llegaban','llegando','llegaran','llegaren','llegaron','llegasen','llevaban',
			'llevadas','llevadle','llevados','llevando','llevaran','llevaria','llevarla','llevarle','llevenme','llorando',
			'llorosos','lloviese','luciente','ludovico','machacan','maestria','maestros','majadero','maldecia','malditos',
			'maleante','mambrino','mameluco','mancebos','manchase','manchega','manchego','mandarme','manjares','manteado',
			'manteles','mantiene','mantuano','maravedi','marchito','marmoles','martinez','mascaras','medicina','medrosos',
			'mejillas','mejorado','meliflua','memorias','mendozas','meneallo','menearse','menester','menguada','menudear',
			'mercader','mercedes','merecian','merecido','merezcan','merrezco','miaulina','miembros','mientras','milagros',
			'militais','ministro','mirabale','mirallos','miserias','misterio','modernos','molieron','molinera','molinero',
			'momentos','monarcas','moncadas','monstruo','montaban','montaña','morgante','mortales','mostraba','mostrado',
			'mostrais','mostrara','mostrase','mostruos','muchacha','muchacho','mudables','muestras','muestren','muestres',
			'multitud','muñaton','muñecas','muñidor','naciones','neguijon','ningunas','ningunos','nombraba','nombrado',
			'nosotros','notorias','nublados','nuestras','nuestros','obedezca','objecion','obligada','obligado','ofendera',
			'ofrecere','ofrecian','olicante','olivante','olvidado','olvidase','opresion','ordenado','otorgaba','otorgado',
			'paciendo','pacifica','pacifico','pagareis','palabras','palmerin','palomino','paradero','pareceme','pareceos',
			'parecian','parecido','parezcan','pariente','partidas','partidos','partirse','pasajera','pasajero','pasarlas',
			'pasearse','pastores','pegadiza','pegarles','peligros','pellicos','pensaban','pensados','pensando','pensarlo',
			'pensaron','pequeña','pequeño','perailes','perdidos','perdiera','perdiese','perdonad','perdonar','perecian',
			'perezcan','pergenio','permiten','permitio','perpetua','perpetuo','persigue','personas','pertinaz','pesaroso',
			'pescador','peñasco','piadosas','piadosos','pideselo','pidieran','pintaban','pintados','pintarse','pisuerga',
			'platicas','platires','podadera','poderosa','poderoso','podremos','pomposas','ponerlos','poniendo','ponzoña',
			'porfiaba','porquero','porrazos','portales','portugal','porvenir','posesion','posesivo','postizos','potencia',
			'pradillo','precepto','preciosa','precioso','predicar','pregunte','pregunto','presente','prestaba','prestada',
			'presteza','presumia','pretendo','prevenir','primeras','primeros','princesa','principe','proceder','procurar',
			'profesan','profunda','profundo','progreso','projimos','promesas','prometer','prometio','proponia','prosapia',
			'prosigue','provecho','proveere','pudiendo','pudieran','pudieron','puedeslo','purisimo','pusieron','pusiesen',
			'pusosela','puñadas','quebrada','quebrado','quedaban','quedamos','quedaran','quedaron','quedarte','quedense',
			'quejarse','quemados','quemaran','quemasen','querella','queremos','quererla','quererlo','querrias','quiebras',
			'quieroos','quierote','quijadas','quimeras','quirocia','quisiera','quisiere','quisiese','quitadas','quitando',
			'quitaran','quitarle','quitarme','quitaron','quitarse','quitarte','raciones','rebaños','rebellas','recibian',
			'recibida','recibido','recogida','recogido','recorred','redondez','referida','refriega','regalado','regocijo',
			'relacion','relieves','relumbra','remanece','rematado','remediar','remedios','remendon','remision','remojado',
			'renuncio','reparaba','replicar','replique','repondio','reportes','reposada','repuesto','requiere','resintio',
			'resolvio','respetar','respetos','responde','retirate','retirose','reventar','reviente','revolvia','rigurosa',
			'riguroso','rindiese','riquezas','risueño','robustas','rodillas','rogarale','rondilla','ruibarbo','rusticas',
			'saavedra','sabidora','sabiendo','sabremos','sabrosas','salarios','salgamos','saliendo','salieron','salpicon',
			'saludado','sangrias','sanlucar','santidad','sardinas','sazonado','secretos','secundar','sediento','seguiale',
			'seguidme','seguirle','sensible','sentidos','sentiran','sequedad','servicio','serviria','servirla','servirle',
			'serviros','señales','señoras','señores','señoria','señuelo','shaberes','siendole','siguelos','siguiese',
			'siguiole','silencio','simiente','simpleza','singular','sinrazon','sintiose','siquiera','sirviese','soberbia',
			'soberbio','socarron','socorred','socorrer','socorria','soldados','solicito','soltando','sombrero','sombrios',
			'sosegada','sosegado','sospecha','soñadas','suadente','suavidad','subiendo','subieron','sucedera','sucedido',
			'supieron','suspenso','suspiros','sustento','sutileza','tablante','tamaños','tapiasen','tardaban','tardanza',
			'tarquino','tañendo','temerosa','temeroso','temiades','temiendo','temprano','tendidos','tenedlos','tenganse',
			'teniendo','terminos','terrible','tiemblan','tirantes','titubear','tocantes','tomarian','tomillas','tormenta',
			'tornando','tornaron','torralva','tortuoso','trabadas','trabajan','trabajos','trabando','tragedia','traigame',
			'trajeron','traslaen','trasluce','trataban','tratasen','trayendo','tristeza','triunfar','trocalle','trocaria',
			'trompeta','tropiezo','trotillo','trueques','trujeron','tuvieron','tuviesen','ufanarte','undecimo','universo',
			'valedera','valencia','valentia','valeroso','valiente','vencedor','venenosa','venganza','vengarme','vengaros',
			'veniades','ventanas','venteril','verdades','veriades','vestidos','victoria','viendola','viendole','viendose',
			'vigesimo','viniendo','vinieron','viniesen','vinosele','virtudes','visitaba','visitado','vizcaina','vizcaino',
			'vocablos','voluntad','volvamos','volverle','volverse','volverte','volviera','volviere','volviese','volviose',
			'vomitase','vosotros','vuelvase','vuestras','vuestros','yantaria'
		);

		return $diccionario[array_rand($diccionario)];
	}
}

// --------------------------------------------------------------------





}
/* helpers varios_helper.php */
/* Location: ./application/helpers/varios_helper.php */