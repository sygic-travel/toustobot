<?php

namespace Toustobot\LunchMenu;

use Nette\Utils\Strings;


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
				$listNumber = count($menu['options']) <= 1 ? '' : ($i + 1) . '. ';
				$price = (Strings::contains($item['text'], "\n") ? "\n" : ' ')  . "[{$item['price']}\u{2009}Kč]";
				$text .= $listNumber . $item['text'] . "$price\n";
			}
			$text .= "\n";
		}

		return $text;
	}
}
