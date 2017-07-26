<?php

namespace Toustobot\LunchMenu;


class MenuFormatter
{
	private static $weekdays = [
		'neděle',
		'pondělí',
		'úterý',
		'středa',
		'čtvrtek',
		'pátek',
		'sobota',
	];


	public function format(\DateTime $dateTime, array $menus): string
	{
		$text = '*Těpéro sombréro! Jak jde život? Dnes je '
			. self::$weekdays[$dateTime->format('w')]
			. ' '
			. $dateTime->format('j.n.')
			. ' Nedá si někdo toast?*'
			. "\n\n";

		foreach ($menus as $name => $menu) {
			$text .= "*$name* (<{$menu['url']}|link>)\n";
			foreach ($menu['options'] as $i => $item) {
				$text .= ($i + 1) . '. ' . $item['text'] . "\n";
			}
			$text .= "\n";
		}

		return $text;
	}
}
