<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;
use Toustobot\Utils\Matcher;


class SonoMenuCrawler implements IMenuCrawler
{
	private const MENU_URL = 'https://www.sonocentrum.cz/the-restaurant/denni-menu/';

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
		$html = file_get_contents(self::MENU_URL);

		$crawler = new Crawler($html);
		$crawler = $crawler
			->filter('article > p > strong')
			->reduce(function (Crawler $node, int $i) use ($date): bool {
				return Matcher::matchesDate($date, $node->text());
			});
		$list = $crawler->parents()->first()->nextAll()->filter('table')->first()->filter('tbody > tr');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			if ($i === 0) {
				//$soup = $item->filter('td')->first()->text();die;
				return;
			}

			$matches = Strings::split($item->filter('td')->first()->text(), '/\s+([0-9,]+)\s*$/u');
			$options[] = [
				'id' => $i - 1,
				'text' => $matches[0],
				'price' => (int) $item->filter('td')->eq(1)->text(),
				'alergens' => $matches[1] ?? null,
				'quantity' => null,
			];
		});

		return [
			'url' => self::MENU_URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
