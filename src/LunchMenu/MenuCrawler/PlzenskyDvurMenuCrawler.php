<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;


class PlzenskyDvurMenuCrawler implements IMenuCrawler
{
	private const MENU_URL = 'http://www.plzenskydvur.cz/tydennimenu/';

	private static $weekdays = [
		'Neděle',
		'Pondělí',
		'Úterý',
		'Středa',
		'Čtvrtek',
		'Pátek',
		'Sobota',
	];


	public function getMenu(\DateTimeInterface $date): array
	{
		$day = (int) $date->format('j');
		$month = (int) $date->format('n');
		$weekday = self::$weekdays[$date->format('w')];
		$datePattern = sprintf('/\s*%s\s*%s\s*\.\s*%s\s*\.\s*$/u', $weekday, $day, $month);

		$html = file_get_contents(self::MENU_URL);

		$crawler = new Crawler($html);
		$crawler = $crawler->filter('.listek > .tyden > p.title')
			->reduce(function (Crawler $node, int $i) use ($datePattern): bool {
				return (bool) Strings::match($node->text(), $datePattern);
			});
		$list = $crawler->nextAll()->filter('.text')->first()->filter('p.menu_title');


		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			$matches = Strings::split($item->nextAll()->first()->text(), '/^\s*([0-9]+(?:g|ks))\s*/u', PREG_SPLIT_NO_EMPTY);
			$options[] = [
				'id' => $item->text(),
				'text' => $matches[1],
				'price' => $i === 0 ? 139 : ($i === 1 ? 89 : 99),
				'alergens' => null,
				'quantity' => $matches[0],
			];
		});

		//$soup = $crawler->nextAll()->filter('.text')->first()->filter('p')->first()->text();

		return [
			'url' => self::MENU_URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
