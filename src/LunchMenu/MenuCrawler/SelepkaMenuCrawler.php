<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;


class SelepkaMenuCrawler implements IMenuCrawler
{
	private const MENU_URL = 'http://www.selepova.cz/denni-menu/';


	public function getMenu(\DateTimeInterface $date): array
	{
		$day = (int) $date->format('j');
		$datePattern = sprintf('/^\s*%s\s*$/u', $day);

		$html = file_get_contents(self::MENU_URL);

		$crawler = new Crawler($html);
		$crawler = $crawler->filter(".den > .datum > .cislo")
			->reduce(function (Crawler $node, int $i) use ($datePattern): bool {
				return (bool) Strings::match($node->text(), $datePattern);
			});
		$list = $crawler->parents()->eq(1)->filter('.seznam > ol > li');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			[$cs, $en] = explode('/', $item->filter('h6')->text());
			$price = $item->filter('.cena')->text();

			$matches = Strings::split($cs, '/\s+([0-9,]+)\s*$/u');
			$options[] = [
				'id' => $i,
				'text' => $matches[0],
				'price' => (int) $price,
				'alergens' => $matches[1] ?? null,
				'quantity' => null,
			];
		});

		//$soup = trim($crawler->parents()->eq(1)->filter('.seznam > .polevka')->filterXPath('//text()[last()]')->text());

		return [
			'url' => self::MENU_URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
