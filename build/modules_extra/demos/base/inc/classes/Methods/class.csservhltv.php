<?php

namespace Demos\Methods;

use Exception;

class CsservHLTV extends Parser
{
	public function parse($url)
	{
		$source = file_get_contents_curl($url);

		if(
			empty($source)
			|| (
				stripos($source, '---') === false
				&& stripos($source, '.-.') === false
			)
		) {
			throw new Exception('Пустая страница для парсинга');
		}

		$link = str_replace('demos_list.php', '', $url);

		$demos = [];
		$demosItems = explode('.-.', $source);

		if(!$demosItems) {
			return [];
		}

		foreach($demosItems as $item) {
			$demoParts = explode('---', $item);

			if(empty($demoParts[0]) || empty($demoParts[1])) {
				continue;
			}

			$file = $demoParts[0];
			$size = $demoParts[1];

			$demoInfo = explode('-', $file);

			$protocol = empty($demoInfo[0]) ? '48p' : $demoInfo[0];

			if(empty($demoInfo[1])) {
				$createdAt = time();
			} else {
				$time = str_split($demoInfo[1], 2);
				$createdAt = mktime($time[3], $time[4], 0, $time[1], $time[2], $time[0] + 2000);
			}

			$map = empty($demoInfo[2])
				? 'undefined'
				: explode('.', $demoInfo[2])[0];

			$demos[] = [
				'id'        => uuid4(),
				'file'      => $link . 'hltv_' . $protocol . '/cstrike/' . $file,
				'size'      => $size,
				'map'       => $map,
				'createdAt' => $createdAt,
			];
		}

		return $demos;
	}
}