<?php

namespace Demos\Methods;

use Exception;
use DOMDocument;

class MyarenaHLTV extends Parser
{
	public function parse($url)
	{
		$source = file_get_contents_curl($url);

		if(
			empty($source)
			|| (
				stripos($source, 'Демки игры') === false
				&& stripos($source, 'class="demo"') === false
			)
		) {
			throw new Exception('Пустая страница для парсинга');
		}

		$demos = [];
		$demosItems = explode('<tr>', $source);
		unset($demosItems[0]);
		unset($demosItems[1]);

		if(!$demosItems) {
			return [];
		}

		foreach($demosItems as $item) {
			$parts = explode('href=\'', $item);

			if(empty($parts[0]) || empty($parts[1])) {
				continue;
			}

			$parts = explode('\'>', $parts[1]);

			$file = empty($parts[0])
				? 'undefined'
				: $parts[0];

			$parts = explode('</a>', $parts[1]);
			$demoInfo = explode('-', $parts[0]);

			if(empty($demoInfo[1])) {
				$createdAt = time();
			} else {
				$time = str_split($demoInfo[1], 2);
				$createdAt = mktime($time[3], $time[4], 0, $time[1], $time[2], $time[0] + 2000);
			}

			$map = empty($demoInfo[2])
				? 'undefined'
				: explode('.', $demoInfo[2])[0];

			$parts = explode('width=5%><nobr>', $parts[1]);
			$size = explode('</nobr>', $parts[1])[0];
			$size = explode(' ', $size);

			if($size[1] == 'Kb') {
				$size = $size[0] * 1024;
			}

			if($size[1] == 'Mb') {
				$size = $size[0] * 1024 * 1024;
			}

			if(!is_integer($size)) {
				$size = 0;
			}

			$demos[] = [
				'id'        => uuid4(),
				'file'      => $file,
				'size'      => $size,
				'map'       => $map,
				'createdAt' => $createdAt,
			];
		}

		return $demos;
	}
}