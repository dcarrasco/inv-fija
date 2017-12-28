<?php

namespace test;

use \Collection;

/**
 * Clase de testeo
 *
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
trait has_rake {

	// --------------------------------------------------------------------

	public function rake($detalle = '', $sort = '')
	{
		$file_list = $this->scan_dir(APPPATH);
		$stats     = $this->file_stats($file_list, $detalle);

		echo $this->print_rake_stats($stats, $sort);
	}

	// --------------------------------------------------------------------

	public function scan_dir($path)
	{
		$excluded_folders = ['.', '..', 'cache', 'config', 'language', 'logs', 'vendor', 'views'];

		return collect(scandir($path))
			->filter(function($file) use ($excluded_folders) {
				return ! in_array($file, $excluded_folders);
			})
			->map(function($file) use ($path) {
				return [
					'file'           => $path.$file,
					'extension'      => pathinfo($path.$file, PATHINFO_EXTENSION),
					'file_type'      => filetype($path.$file),
					static::STATS_KEY_CONTENT => '',
				];
			})
			->filter(function($file_data) {
				return array_get($file_data, 'file_type') === 'dir' OR
					(array_get($file_data, 'file_type') === 'file' AND array_get($file_data, 'extension') === 'php');
			})
			->map(function($file_data) {
				$file_data[static::STATS_KEY_CONTENT] = array_get($file_data, 'file_type') === 'dir'
						? $this->scan_dir(array_get($file_data, 'file').'/')
						: '';

				return $file_data;
			})
			->map_with_keys(function($file_data) {
				return [
					$file_data['file'] =>
						array_get($file_data, static::STATS_KEY_CONTENT) instanceof Collection
							? array_get($file_data, static::STATS_KEY_CONTENT)->all()
							: $file_data['file']
				];
			})
			->flatten();
	}

	// --------------------------------------------------------------------

	public function file_stats($file_list, $detalle)
	{
		$file_stats = $file_list
			->map([$this, 'recupera_file_contents'])
			->map([$this, 'cuenta_lineas'])
			->map([$this, 'cuenta_loc'])
			->map([$this, 'cuenta_clases'])
			->map([$this, 'cuenta_metodos'])
			->map([$this, 'achica_nombre_archivo']);

		return $this->resumen_file_stats($file_stats, $detalle);
	}

	// --------------------------------------------------------------------

	public function recupera_file_contents($file)
	{
		return [
			'file'	=> $file,
			static::STATS_KEY_CONTENT => collect(explode("\n", file_get_contents($file))),
		];
	}

	// --------------------------------------------------------------------

	public function cuenta_stats($file, $stat_key, $filter)
	{
		return array_merge($file, [
			$stat_key => array_get($file, static::STATS_KEY_CONTENT, collect())
				->filter($filter)
				->count()
		]);
	}

	// --------------------------------------------------------------------

	public function cuenta_lineas($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_LINES, function($line) {return TRUE;});
	}

	// --------------------------------------------------------------------

	public function cuenta_loc($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_LOC, function($line) {
			return ! preg_match('/^\s*$|^\s*\/\*\*|\s*\*[ ]*|^\s*\/\/|<?php/', $line);
		});
	}

	// --------------------------------------------------------------------

	public function cuenta_clases($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_CLASSES, function($line) {
			return preg_match('/^\s*class/', $line);
		});
	}

	// --------------------------------------------------------------------

	public function cuenta_metodos($file)
	{
		return $this->cuenta_stats($file, static::STATS_KEY_METHODS, function($line) {
			return preg_match('/^\s*(public|protected|private) (static )?function/', $line);
		});
	}

	// --------------------------------------------------------------------

	public function achica_nombre_archivo($file)
	{
		$file['file']   = substr($file['file'], strlen(APPPATH), strlen($file['file']));
		$file['folder'] = array_get(explode('/', $file['file']), 0);
		$file['file']   = substr($file['file'], strlen($file['folder'])+1, strlen($file['file']));

		unset($file[static::STATS_KEY_CONTENT]);

		return $file;
	}

	// --------------------------------------------------------------------

	public function resumen_file_stats($stats, $detalle)
	{
		return $stats
			->pluck($detalle === 'detalle' ? 'file' : 'folder')
			->sort()
			->unique()
			->map_with_keys(function($folder) {
				return [$folder => $folder];
			})
			->map(function($folder) use ($stats, $detalle) {
				return $stats->filter(function($file) use ($folder, $detalle) {
					return $file[$detalle ? 'file' : 'folder'] === $folder;
				});
			})
			->map([$this, 'calcula_stats']);
	}

	// --------------------------------------------------------------------

	public function calcula_stats($item)
	{
		return [
			static::STATS_KEY_LINES   => $item->pluck(static::STATS_KEY_LINES)->sum(),
			static::STATS_KEY_LOC     => $item->pluck(static::STATS_KEY_LOC)->sum(),
			static::STATS_KEY_CLASSES => $item->pluck(static::STATS_KEY_CLASSES)->sum(),
			static::STATS_KEY_METHODS => $item->pluck(static::STATS_KEY_METHODS)->sum(),
			static::STATS_KEY_MC      => $item->pluck(static::STATS_KEY_CLASSES)->sum() === 0 ? 0 : (int) ($item->pluck(static::STATS_KEY_METHODS)->sum() / $item->pluck(static::STATS_KEY_CLASSES)->sum()),
			static::STATS_KEY_LOCM    => $item->pluck(static::STATS_KEY_METHODS)->sum() === 0 ? 0 : (int) ($item->pluck(static::STATS_KEY_LOC)->sum() / $item->pluck(static::STATS_KEY_METHODS)->sum()),
		];
	}

	// --------------------------------------------------------------------

	public function print_rake_stats($stats, $sort)
	{
		$stats = $this->sort_stats($stats, $sort);

		$padding = collect([
			static::STATS_KEY_NAME    => 20,
			static::STATS_KEY_LINES   => 7,
			static::STATS_KEY_LOC     => 7,
			static::STATS_KEY_CLASSES => 7,
			static::STATS_KEY_METHODS => 7,
			static::STATS_KEY_MC      => 7,
			static::STATS_KEY_LOCM    => 7,
		]);

		$padding->add_item(
			max([
				$padding->get(static::STATS_KEY_NAME),
				$stats->map(function($stat, $stat_key) {return strlen($stat_key);})->max()
			]), static::STATS_KEY_NAME);

		$totals = collect()
			->add_item(collect([collect($stats->first())
				->map(function($value, $stat_key) use ($stats) {
					return $stats->pluck($stat_key)->sum();
				})->all()
			]), 'Totals')
			->map([$this, 'calcula_stats']);

		echo "\n".$this->rake_print_title($padding);
		echo $this->rake_print_stats($stats, $padding);
		echo $this->rake_print_line($padding);
		echo $this->rake_print_stats($totals, $padding);
		echo $this->rake_print_line($padding);
	}

	// --------------------------------------------------------------------

	protected function sort_stats($stats, $sort)
	{
		$sort = strtolower($sort);

		$columns = [
			'lines'   => 'Lines',
			'loc'     => 'LOC',
			'classes' => 'Classes',
			'methods' => 'Methods',
			'mc'      => 'M/C',
			'locm'    => 'LOC/M',
		];

		if ($sort === '' OR ! in_array($sort, array_keys($columns)))
		{
			return $stats;
		}

		return $stats
			->sort(function($stat1, $stat2) use ($sort, $columns) {
				return $stat1[$columns[$sort]] < $stat2[$columns[$sort]];
			});
	}

	// --------------------------------------------------------------------

	public function rake_print_line($padding)
	{
		return '+-'
			.$padding
				->map(function($padding) {return str_pad('', $padding, '-');})
				->implode('-+-')
			.'-+'."\n";
	}

	// --------------------------------------------------------------------

	public function rake_print_title($padding)
	{
		return $this->rake_print_line($padding)
			.'| '.$padding
				->map(function($padding, $name) {
					return str_pad($name, $padding, ' ', $name === static::STATS_KEY_NAME ? STR_PAD_RIGHT : STR_PAD_LEFT);
				})->implode(' | ')
			.' |'."\n"
			.$this->rake_print_line($padding);
	}

	// --------------------------------------------------------------------

	public function rake_print_stats($stats, $padding)
	{
		return $stats->map(function($folder, $folder_name) use ($padding) {
			return '| '
				.str_pad(ucfirst($folder_name), $padding->get(static::STATS_KEY_NAME), ' ')
				.' | '
				.collect($folder)->map(function($stat, $stat_key) use ($padding) {
					return str_pad($stat, $padding->get($stat_key), ' ', STR_PAD_LEFT);
				})->implode(' | ')
				.' |'."\n";
		})->implode('');
	}

}
